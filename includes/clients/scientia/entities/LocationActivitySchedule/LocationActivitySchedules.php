<?php
    /*
     * This file is part of Panopto-PHP-Booking-Engine.
     * 
     * Panopto-PHP-Booking-Engine is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     * 
     * Panopto-PHP-Booking-Engine is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     * 
     * You should have received a copy of the GNU General Public License
     * along with Panopto-PHP-Booking-Engine.  If not, see <http://www.gnu.org/licenses/>.
     * 
     * Copyright: Andrew Martin, Newcastle University
     * 
     */
    require_once("LocationActivitySchedule.php");

class LocationActivitySchedules
{
    private $locationActivitySchedules = array();

    public function __construct($xml,$includeDuplicateHostKeys = false)
    {
        foreach($xml->Activities->Activity as $locationActivitySchedule)
        {
            if(!$includeDuplicateHostKeys)
            {
                if(!isset($this->locationActivitySchedules[$locationActivitySchedule->HostKey.""]))
                {
                    $this->locationActivitySchedules[$locationActivitySchedule->HostKey.""] = new LocationActivitySchedule($locationActivitySchedule);
                }
            }
            else
            {
                $this->locationActivitySchedules[] = new LocationActivitySchedule($locationActivitySchedule);
            }
        }
    }

    public function getLocationActivitySchedules()
    {
        return $this->locationActivitySchedules;
    }

    public function getLocationActivitySchedule($hostKey = "")
    {
        if($hostKey=="")
        {
            foreach($this->locationActivitySchedules as $locationActivitySchedule)
            {
                return $locationActivitySchedule;
            }
        }
        else
        {
            return $this->locationActivitySchedules[$hostKey.""];
        }
    }

    public function count()
    {
        return count($this->locationActivitySchedules);
    }
}

?>
