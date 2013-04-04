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
    $panoptoClientDir = dirname(__FILE__)."/../../panoptoPHP";
    require_once(dirname(__FILE__)."/AbstractPanoptoHelper.php");
    require_once($panoptoClientDir."/includes/dataObjects/objects/AuthenticationInfo.php");
    require_once($panoptoClientDir."/includes/commons/PanoptoCommons.php");
    /*** 4.2 API ***/
    require_once($panoptoClientDir."/includes/impl/4.2/client/RemoteRecorderManagementClient.php");
    require_once($panoptoClientDir."/includes/impl/4.2/client/SessionManagementClient.php");
    /*** ------- ***/
    require_once($panoptoClientDir."/logger/Logger.php");
    require_once(dirname(__FILE__)."/../clients/scientia/client/ScientiaClient.php");
    require_once(dirname(__FILE__)."/../clients/scientia/commons/ScientiaCommons.php");

class PanoptoBookingEngineHelper extends AbstractPanoptoHelper
{
    private $clashes; //Array of host keys that are in panopto as sessions
    private $currentSciWeekNum;
    private $locationActivitySchedules;
    private $RRMClient;
    private $SECONDS_TO_ADD_AT_START_OF_SESSIONS = 300; //This puts a buffer on sessions, so you don't record 5 minutes of students coming in to the lecture!

    public function __construct($user, $selectedModule = null, $yearPrefix = "Q1213-",$server = "panoptoserver.example.com")
    {
        $this->logger = new Logger("/tmp/PanoptoBookingEngine.log",$user);
        if(isset($user))
        {
            $this->user = $user;
            $this->scientiaClient = new ScientiaClient("https://","timetableserver.example.com","/Scientia/TimetableXmlReportEngine");
            if(isset($selectedModule))
            {
                $this->auth = new AuthenticationInfo("webservice user's username...","...and password here",null);
                $this->clashes = array();
                $this->server = $server;
                $this->yearPrefix = $yearPrefix;
                $this->selectedModule = $selectedModule;

                $this->SMClient = new SessionManagementClient($this->server, $this->auth);
                $this->RRMClient = new RemoteRecorderManagementClient($this->server, $this->auth);

                $this->logger->log($yearPrefix.$this->selectedModule);
                $this->folder = $this->getFolder($this->getModuleCode());

                if(!isset($this->folder)){throw new Exception("This module does not exist (yet) on panopto, so you can not schedule recordings for it");}
                $this->currentSciWeekNum = $this->scientiaClient->getScientiaWeekNumberFromDate(date('Y-m-d'));

                $this->locationActivitySchedules = $this->scientiaClient->getLocationActivityScheduleForAModuleId($this->selectedModule);
                //Not a lot of point detecting clashes if no activities are returned to display!
                if($this->locationActivitySchedules->count()>0)
                {
                    foreach($this->locationActivitySchedules->getLocationActivitySchedules() as $activity)
                    {
                        $this->findActivitySessionClashes($activity->getHostKey());
                    }
                }
            }
        }
    }

    private function addActivityAsSession(LocationActivitySchedule $activity, $weekNo, $remoteRecorderSettings, $folderForJointActivities = null, $dayOfWeek = null)
    {
        $daysForward = isset($dayOfWeek)?ScientiaCommons::getWeekDayNameAsNumber($dayOfWeek):$activity->getScheduledDay();
        $this->addASession($activity, $weekNo, $daysForward, $remoteRecorderSettings, $folderForJointActivities);
    }

    private function addASession($activity,$weekNo,$daysForward,$recorderSettings,$folderForJointActivities = null)
    {
        $startDate = PanoptoCommons::getDateStringWithDaysAddedForDateString(ScientiaCommons::getDateStringFromScientiaDate($this->scientiaClient->getDateFromScientiaWeekNumber($weekNo)),$daysForward);
        $startTime = ScientiaCommons::getTimeStringFromScientiaDateTime($activity->getScheduledStartTime());
        $endTime = ScientiaCommons::getTimeStringFromScientiaDateTime($activity->getScheduledEndTime());
        $startDateTime = PanoptoCommons::getPanoptoDateTimeFromDateStringAndTimeString($startDate,PanoptoCommons::adjustSessionTime($startTime, $this->SECONDS_TO_ADD_AT_START_OF_SESSIONS));
        $endDateTime = PanoptoCommons::getPanoptoDateTimeFromDateStringAndTimeString($startDate,$endTime);
        $this->logger->log($activity->getName()." ".$startDateTime." ".$endDateTime);
        try
        {
            $srr = new ScheduleRecordingResponse($this->RRMClient->scheduleRecording(
                            $activity->getName()
                            ,isset($folderForJointActivities)?$folderForJointActivities->getId():$this->folder->getId()
                            ,PanoptoCommons::getLocalDateTimeAsUTCDateTime($startDateTime)
                            ,PanoptoCommons::getLocalDateTimeAsUTCDateTime($endDateTime)
                            ,$recorderSettings));
            $sessionId = $srr->getGuid();
            if(isset($sessionId))
            {
                $this->clashes[] = $activity->getHostKey();
                $this->SMClient->updateSessionExternalId($sessionId, $activity->getHostKey());
                $this->logger->log("Booked: Act(".$activity->getHostKey().", ".$activity->getName().", ".$startDateTime." ".$endDateTime.")->Sess(".$sessionId.")");
            }
            else
            {
                $this->logger->log("Not Booked: Act(".$activity->getHostKey().", ".$activity->getName().", ".$startDateTime." ".$endDateTime.")");
            }
        }
        catch(Exception $e)
        {
            /*
             * If the error is that you can't schedule something
             * 'cos it's in the past, then ignore.
             */
            if(strpos($e->getMessage(),"minutes in the past")===false)
            {
                $this->logger->log($e->getMessage());
//                echo $e->getMessage()."<br/>";
            }
            $this->logger->log($e->getMessage());
        }
    }

    public function bookSessions($activities)
    {
        foreach($this->getLocationActivitySchedules()->getLocationActivitySchedules() as $activity)
        {
            //If it's not in panopto && it has been asked to be booked
            if(!$this->detectActivitySessionClash($activity->getHostKey()) && in_array($activity->getHostKey(),$activities))
            {
                $this->logger->log("Activity: ".$activity->getHostKey());
                $this->logger->log("Type: ".$activity->getType());
                $folderForJointActivities = null;
                if(strpos($activity->getType(),"Joint")!==false)
                {
                    $folderForJointActivities = $this->getFolderForJointActivity($activity->getHostKey());
                    $this->logger->log(isset($folderForJointActivities)?"Folder found, this must be a fully joint module":"Folder not found, this must be a partial joint module, using selected folder: ".$this->folder->getName());
                }
                $weekNumbers = ScientiaCommons::getWeekNumberArrayFromWeekPattern($activity->getWeekPattern());
                $this->logger->var_dump($weekNumbers);
                $remoteRecorderSettings = $this->getRemoteRecorderSettingsForActivity($activity->getLocationHostKey());
                if(count($remoteRecorderSettings)>0)
                {
                    //For each week an activity covers
                    foreach($weekNumbers as $weekNo)
                    {
                        $this->logger->log("Booking for week ".$weekNo." ".$this->scientiaClient->getDateFromScientiaWeekNumber($weekNo));
                        /*
                         * SuggestedDays for non-repeating days of week can be either
                         * "N/A" or the day that it is held on, i.e. the human version
                         * of the ScheduledDay number. Either is valid.
                         */
                        $repeatingDaysOfWeek = PanoptoCommons::getPanoptoDayOfWeekArrayForWeekDayNameArray(ScientiaCommons::getRepeatingDaysInEachWeek($activity->getSuggestedDays()));
                        if(count($repeatingDaysOfWeek)<=1) // If repeating days == N/A or just 1 day name i.e "Friday"
                        {
                            $this->addActivityAsSession($activity, $weekNo, $remoteRecorderSettings, $folderForJointActivities);
                        }
                        else
                        {
                            foreach($repeatingDaysOfWeek as $dayOfWeek)
                            {
                                $this->addActivityAsSession($activity, $weekNo, $remoteRecorderSettings, $folderForJointActivities, $dayOfWeek);
                            }
                        }
                    }
                }
                else
                {
                    echo "<div class=\"errors\">There are currently no valid remote recorders in ".$activity->getLocationName()." for the activity ".$activity->getName()."</div>";
                }
            }
        }
    }

    public function detectActivitySessionClash($activityHostKey)
    {
        return in_array($activityHostKey,$this->clashes);
    }
    
    private function findActivitySessionClashes($activityHostKey)
    {
        $sessions = $this->SMClient->getSessionsByExternalId($activityHostKey)->getSessions();
        if(isset($sessions)&&count($sessions)>0)
        {
            $this->clashes[] = $activityHostKey;
        }
    }

    public function getLocationActivitySchedules()
    {
        return $this->locationActivitySchedules;
    }

    public function getCurrentSciWeekNum()
    {
        return $this->currentSciWeekNum;
    }
    
    public function getModulesForAStaffId()
    {
        if(isset($this->user) && $this->user!=null)
        {
            return $this->scientiaClient->getModulesForAStaffId($this->user);
        }
        else
        {
            throw new Exception("The user '".$this->user."' may not exist in the ReCap system");
        }
    }

    private function getFolderForJointActivity($activityHostKey)
    {
        $folderExternalId = $this->yearPrefix;
        $modules = $this->scientiaClient->getModulesForAnActivityHK($activityHostKey);
        foreach($modules->getModules() as $module)
        {
            $folderExternalId .= $module->getHostKey()."-";
        }
        $this->logger->log("Attempting to find joint activity folder: ".substr($folderExternalId,0,-1));
        return $this->getFolder(substr($folderExternalId,0,-1));
    }

    private function translateScientiaLocationHostKeyToPanoptoRemoteRecorderExId($locationHostKey)
    {
        return strtoupper(str_replace(array(" ",".","#","-","/","\\","(",")",","),"",$locationHostKey));
    }

    private function getRemoteRecorderSettingsForActivity($locationHostKey)
    {
        $this->logger->log("Location: ".$locationHostKey);
        $remoteRecorderExId = $this->translateScientiaLocationHostKeyToPanoptoRemoteRecorderExId($locationHostKey);
        
        /*** Get remote recorders from their external id ***/
        $aoRR = array();
        $aoRR[] = $remoteRecorderExId;
        $aos = new ArrayOfString($aoRR);

        $remoteRecorders = array();
        try{$remoteRecorders = array_merge($remoteRecorders,$this->getRemoteRecordersByExternalId($aos));}catch(Exception $e){}

        $recorderSettings = array();
        /**** Work out the recorder settings from the remote recorder name ****/
        if(count($remoteRecorders)>0)
        {
            foreach($remoteRecorders as $remoteRecorder)
            {
                $remoteRecorderName = $remoteRecorder->getName();
                $RRpostfix = substr($remoteRecorderName,-2);
                switch($RRpostfix)
                {
                    //if the recorder's name ends in "-P" it's a primary recorder
                    case "-P": $recorderSettings[] = new RecorderSettings($remoteRecorder->getId(), false, false); break;
                    //if the recorder's name ends in "-S" it's a secondary recorder
                    case "-S": $recorderSettings[] = new RecorderSettings($remoteRecorder->getId(), true, false); break;
                    default:
                }
            }
        }
        $this->logger->var_dump($recorderSettings);
        return $recorderSettings;
    }

    private function getRemoteRecordersByExternalId(ArrayOfString $hostkeys)
    {
        return $this->RRMClient->getRemoteRecordersByExternalId($hostkeys)->getRemoteRecorders();
    }
}

?>
