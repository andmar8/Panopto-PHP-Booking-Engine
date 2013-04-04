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

class LocationActivitySchedule
{
    private $activityTypeHostkey;
    private $activityTypeID;
    private $activityTypeName;
    private $activityTypeUsertext5;
    private $actSize;
    private $departmentHostkey;
    private $departmentID;
    private $departmentName;
    private $description;
    private $duration;
    private $hostKey;
    private $id;
    private $lastChanged;
    private $locationCapacity;
    private $locationDescription;
    private $locationHostKey;
    private $locationName;
    private $moduleDescription;
    private $moduleHostkey;
    private $moduleID;
    private $moduleName;
    private $name;
    private $plannedSize;
    private $realSize;
    private $scheduledDay;
    private $scheduledEndTime;
    private $scheduledStartTime;
    private $scheduled;
    private $sectionId;
    private $size;
    private $sizeLink;
    private $startDate;
    private $suggestedDays;
    private $type;
    private $usertext1;
    private $usertext2;
    private $usertext3;
    private $usertext4;
    private $usertext5;
    private $webtag;
    private $weekLabels;
    private $weekPattern;


    public function __construct($locationActivitySchedule)
    {
        $this->actSize = $locationActivitySchedule->ActSize;
        $this->activityTypeHostkey = $locationActivitySchedule->ActivityType_Hostkey;
        $this->activityTypeID = $locationActivitySchedule->ActivityType_ID;
        $this->activityTypeName = $locationActivitySchedule->ActivityType_Name;
        $this->activityTypeUsertext5 = $locationActivitySchedule->ActivityType_Usertext5;
        $this->departmentHostkey = $locationActivitySchedule->Department_Hostkey;
        $this->departmentID = $locationActivitySchedule->Department_ID;
        $this->departmentName = $locationActivitySchedule->Department_Name;
        $this->description = $locationActivitySchedule->Description;
        $this->duration = $locationActivitySchedule->Duration;
        $this->hostKey = $locationActivitySchedule->HostKey;
        $this->id = $locationActivitySchedule->Id;
        $this->lastChanged = $locationActivitySchedule->lastChanged;
        $this->locationCapacity = $locationActivitySchedule->LocationCapacity;
        $this->locationDescription = $locationActivitySchedule->LocationDescription;
        $this->locationHostKey = $locationActivitySchedule->LocationHostKey;
        $this->locationName = $locationActivitySchedule->LocationName;
        $this->moduleDescription = $locationActivitySchedule->Module_Description;
        $this->moduleHostkey = $locationActivitySchedule->Module_Hostkey;
        $this->moduleID = $locationActivitySchedule->Module_ID;
        $this->moduleName = $locationActivitySchedule->Module_Name;
        $this->name = $locationActivitySchedule->Name;
        $this->plannedSize = $locationActivitySchedule->PlannedSize;
        $this->realSize = $locationActivitySchedule->RealSize;
        $this->scheduled = $locationActivitySchedule->Scheduled;
        $this->scheduledDay = $locationActivitySchedule->ScheduledDay;
        $this->scheduledEndTime = $locationActivitySchedule->ScheduledEndTime;
        $this->scheduledStartTime = $locationActivitySchedule->ScheduledStartTime;
        $this->sectionId = $locationActivitySchedule->section_id;
        $this->size = $locationActivitySchedule->Size;
        $this->sizeLink = $locationActivitySchedule->SizeLink;
        $this->startDate = $locationActivitySchedule->StartDate;
        $this->suggestedDays = $locationActivitySchedule->SuggestedDays;
        $this->type = $locationActivitySchedule->Type;
        $this->usertext1 = $locationActivitySchedule->Usertext1;
        $this->usertext2 = $locationActivitySchedule->Usertext2;
        $this->usertext3 = $locationActivitySchedule->Usertext3;
        $this->usertext4 = $locationActivitySchedule->Usertext4;
        $this->usertext5 = $locationActivitySchedule->Usertext5;
        $this->weekLabels = $locationActivitySchedule->WeekLabels;
        $this->weekPattern = $locationActivitySchedule->WeekPattern;
        $this->webtag = $locationActivitySchedule->webtag;
    }

    public function getActivityTypeHostkey() {
        return $this->activityTypeHostkey;
    }

    public function getActivityTypeID() {
        return $this->activityTypeID;
    }

    public function getActivityTypeName() {
        return $this->activityTypeName;
    }

    public function getActivityTypeUsertext5() {
        return $this->activityTypeUsertext5;
    }

    public function getActSize() {
        return $this->actSize;
    }

    public function getDepartmentHostkey() {
        return $this->departmentHostkey;
    }

    public function getDepartmentID() {
        return $this->departmentID;
    }

    public function getDepartmentName() {
        return $this->departmentName;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function getHostKey() {
        return $this->hostKey;
    }

    public function getId() {
        return $this->id;
    }

    public function getLastChanged() {
        return $this->lastChanged;
    }

    public function getLocationCapacity() {
        return $this->locationCapacity;
    }

    public function getLocationDescription() {
        return $this->locationDescription;
    }

    public function getLocationHostKey() {
        return $this->locationHostKey;
    }

    public function getLocationName() {
        return $this->locationName;
    }

    public function getModuleDescription() {
        return $this->moduleDescription;
    }

    public function getModuleHostkey() {
        return $this->moduleHostkey;
    }

    public function getModuleID() {
        return $this->moduleID;
    }

    public function getModuleName() {
        return $this->moduleName;
    }

    public function getName() {
        return $this->name;
    }

    public function getPlannedSize() {
        return $this->plannedSize;
    }

    public function getRealSize() {
        return $this->realSize;
    }

    public function getScheduledDay() {
        return $this->scheduledDay;
    }

    public function getScheduledEndTime() {
        return $this->scheduledEndTime;
    }

    public function getScheduledStartTime() {
        return $this->scheduledStartTime;
    }

    public function getScheduled() {
        return $this->scheduled;
    }

    public function getSectionId() {
        return $this->sectionId;
    }

    public function getSize() {
        return $this->size;
    }

    public function getSizeLink() {
        return $this->sizeLink;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function getSuggestedDays() {
        return $this->suggestedDays;
    }

    public function getType() {
        return $this->type;
    }

    public function getUsertext1() {
        return $this->usertext1;
    }

    public function getUsertext2() {
        return $this->usertext2;
    }

    public function getUsertext3() {
        return $this->usertext3;
    }

    public function getUsertext4() {
        return $this->usertext4;
    }

    public function getUsertext5() {
        return $this->usertext5;
    }

    public function getWebtag() {
        return $this->webtag;
    }

    public function getWeekLabels() {
        return $this->weekLabels;
    }

    public function getWeekPattern() {
        return $this->weekPattern;
    }
}

?>
