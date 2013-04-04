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

class Module
{
    private $departmentDescription;
    private $departmentHostkey;
    private $departmentId;
    private $departmentName;
    private $description;
    private $hostkey;
    private $id;
    private $name;
    private $userText1;
    private $userText2;

    public function __construct($module)
    {
        $this->departmentDescription = $module->Department_Description;
        $this->departmentHostkey = $module->Department_HostKey;
        $this->departmentId = $module->Department_ID;
        $this->departmentName = $module->Department_Name;
        $this->description = $module->Description;
        $this->hostkey = $module->HostKey;
        $this->id = $module->ID;
        $this->name = $module->Name;
        $this->userText1 = $module->Usertext1;
        $this->userText2 = $module->Usertext2;
    }

    public function getDepartmentDescription()
    {
        return $this->departmentDescription;
    }

    public function getDepartmentHostkey()
    {
        return $this->departmentHostkey;
    }

    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    public function getUserText1()
    {
        return $this->userText1;
    }

    public function getUserText2()
    {
        return $this->userText2;
    }

    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getHostkey()
    {
        return $this->hostkey;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
?>
