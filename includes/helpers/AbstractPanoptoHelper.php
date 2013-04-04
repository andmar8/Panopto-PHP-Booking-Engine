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

abstract class AbstractPanoptoHelper
{
    protected $auth;
    protected $folder;
    protected $logger;
    protected $scientiaClient;
    protected $selectedModule;
    protected $server;
    protected $SMClient;
    protected $user;
    protected $yearPrefix;

    public function getSelectedModule()
    {
        return $this->selectedModule;
    }
    
    protected function getFolder($folderExternalId)
    {
        $folders = $this->SMClient->getFoldersByExternalId($folderExternalId)->getFolders();
        return count($folders)>0?$folders[0]:null;
    }

    public function getModuleCode()
    {
        return $this->yearPrefix.$this->selectedModule;
    }

    public function getUser()
    {
        return $this->user;
    }
}

?>
