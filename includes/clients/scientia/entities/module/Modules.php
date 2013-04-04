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
    require_once('Module.php');

class Modules
{
    private $modules = array();

    public function __construct($xml)
    {
        foreach($xml->Modules->Module as $module)
        {
            $this->modules[] = new Module($module);
        }
    }

    public function count()
    {
        return count($this->modules);
    }

    public function getModule()
    {
        return $this->modules[0];
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function setModules($modules)
    {
        $this->modules = $modules;
    }
}
?>
