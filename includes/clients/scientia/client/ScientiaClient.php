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
    require_once(dirname(__FILE__)."/../entities/LocationActivitySchedule/LocationActivitySchedules.php");
    require_once(dirname(__FILE__)."/../entities/module/Modules.php");

class ScientiaClient
{
    private $scientiaProtocol;
    private $scientiaServer;
    private $scientiaServiceRoot;
    private $scientiaServiceEndPoint;
    private $scientiaEndPoint_DateFromWeekNumber;
    private $scientiaEndPoint_ModulesFromAnActivityHK;
    private $scientiaEndPoint_ModulesFromAStaffId;
    private $scientiaEndPoint_LocationActivitySchedule;
    private $scientiaEndPoint_WeekNumberFromDate;
    private $timeToConnectTimeout;
    private $timeToRetrieveTimeout;

    public function __construct($scientiaProtocol = "https://",$scientiaServer = "timetableserver.example.com",$scientiaServiceRoot = "/Scientia/TimetablingXmlReportEngine",$scientiaServiceEndPoint = "/Default.aspx", $timeToConnectTimeout = 1, $timeToRetrieveTimeout = 20)
    {
        $this->scientiaProtocol = $scientiaProtocol;
        $this->scientiaServer = $scientiaServer;
        $this->scientiaServiceRoot = $scientiaServiceRoot;
        $this->scientiaServiceEndPoint = $scientiaServiceEndPoint;
        $this->scientiaEndPoint_ModulesFromAnActivityHK = $this->scientiaProtocol.$this->scientiaServer.$this->scientiaServiceRoot.$this->scientiaServiceEndPoint."?modulesForAnActivityEndpoint&";
        $this->scientiaEndPoint_ModulesFromAStaffId = $this->scientiaProtocol.$this->scientiaServer.$this->scientiaServiceRoot.$this->scientiaServiceEndPoint."?modulesForAStaffIdEndpoint&";
        $this->scientiaEndPoint_LocationActivitySchedule = $this->scientiaProtocol.$this->scientiaServer.$this->scientiaServiceRoot.$this->scientiaServiceEndPoint."?LocationsActivitiesAndSchedulesEndpoint&";
        $this->scientiaEndPoint_WeekNumberFromDate = $this->scientiaProtocol.$this->scientiaServer.$this->scientiaServiceRoot.$this->scientiaServiceEndPoint."?dataset=weekNumbers&";
        $this->scientiaEndPoint_DateFromWeekNumber = $this->scientiaProtocol.$this->scientiaServer.$this->scientiaServiceRoot.$this->scientiaServiceEndPoint."?dataset=weeks&";
        $this->timeToConnectTimeout = $timeToConnectTimeout;
        $this->timeToRetrieveTimeout = $timeToRetrieveTimeout;
    }

    public function getModulesForAnActivityHK($activityHostKey)
    {
        return new Modules($this->getXML($this->scientiaEndPoint_ModulesFromAnActivityHK."hk=".urlencode($activityHostKey)));
    }

    public function getModulesForAStaffId($staffId)
    {
        return new Modules($this->getXML($this->scientiaEndPoint_ModulesFromAStaffId."staffId=".$staffId));
    }

    public function getLocationActivityScheduleForAModuleId($moduleId)
    {
        return new LocationActivitySchedules($this->getXML($this->scientiaEndPoint_LocationActivitySchedule."module=".$moduleId));
    }

    public function getDateFromScientiaWeekNumber($weekNo)
    {
        return $this->getXML($this->scientiaEndPoint_DateFromWeekNumber."week=".$weekNo)->Activities->Activity->Date;
    }

    public function getScientiaWeekNumberFromDate($weekDate)
    {
        return $this->getXML($this->scientiaEndPoint_WeekNumberFromDate."week=".$weekDate)->Weeks->Week->WeekNum;
    }

    private function getXML($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeToConnectTimeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeToRetrieveTimeout);
        $response = "";
        try
        {
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpCode!=200)
            {
                throw new Exception("HTTP CODE ".$httpCode);
            }
            return new SimpleXMLElement($response);
        }
        catch(Exception $e)
        {
            $errorPrompt = "Unfortunately the system could not retrieve timetabling data at the moment, ".
                            "often this is due to a temporarily high volume of users accessing the system, ".
                            "please try again in a few minutes.";
            //echo $errorPrompt;
            curl_close($ch);
            throw new Exception($errorPrompt);
        }
        curl_close($ch);
        return new SimpleXMLElement("<?xml version=\"1.0\"?>".
                                    "<Data>".
                                    "</Data>");
    }
}
?>
