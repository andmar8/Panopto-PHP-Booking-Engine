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
    require_once($panoptoClientDir."/includes/impl/4.2/client/AccessManagementClient.php");
    require_once($panoptoClientDir."/includes/impl/4.2/client/RemoteRecorderManagementClient.php");
    require_once($panoptoClientDir."/includes/impl/4.2/client/SessionManagementClient.php");
    require_once($panoptoClientDir."/includes/impl/4.2/client/UserManagementClient.php");
    /*** ------- ***/
    require_once($panoptoClientDir."/logger/Logger.php");
    require_once(dirname(__FILE__)."/../clients/scientia/client/ScientiaClient.php");
    require_once(dirname(__FILE__)."/../clients/scientia/commons/ScientiaCommons.php");

class PanoptoBookingsHelper extends AbstractPanoptoHelper
{
    private $UMClient;

    public function __construct($user, $selectedModule = null, $yearPrefix = "Q1213-",$server = "panoptoserver.example.com")
    {
        $this->auth = new AuthenticationInfo("webservice user's username...","...and password here",null);
        $this->logger = new Logger("/tmp/PanoptoBookings.log",$user);
        $this->selectedModule = $selectedModule;
        $this->server = $server;
        $this->yearPrefix = $yearPrefix;
        
        $this->user = $user;
        if(isset($this->selectedModule))
        {
            $this->logger->log($yearPrefix.$this->selectedModule);
            $this->scientiaClient = new ScientiaClient("https://","timetableserver.example.com","/Scientia/TimetableXmlReportEngine");
            $this->SMClient = new SessionManagementClient($this->server, $this->auth);
            $this->UMClient = new UserManagementClient($this->server, $this->auth);
            $this->folder = $this->getFolder($this->getModuleCode());
        }
    }

    public function deleteSessions($selectedSessions)
    {
        $this->logger->var_dump($selectedSessions);
        try
        {
            $this->SMClient->deleteSessions($selectedSessions);
        }
        catch(Exception $e)
        {
            //echo $e->getMessage();
        }
    }

    public function getCreator($creatorId)
    {
        $users = $this->UMClient->GetUsers($creatorId)->getUsers();
        return count($users)>0?$users[0]:null;
    }

    public function getSessions()
    {
        $sessions = array();
        if(isset($this->folder))
        {
            /** All sessions by folder, sessions that are scheduled will not show vvv **/
            //$sessions = $SMClient->getSessionsList(new ListSessionsRequest(null, $folder->getId(), new Pagination(10000,null), null, null, null, null, null))->getSessions();
            /** All sessions **/
            //$sessions = $SMClient->getSessionsList(new ListSessionsRequest(null, null, new Pagination(10000,null), null, null, null, null, null))->getSessions();
            /** All created/scheduled sessions for given folder, ordered by date **/
            $states = array(SessionState::Created,SessionState::Scheduled);
            $sessions = $this->SMClient->getSessionsList(new ListSessionsRequest(null, $this->folder->getId(), new Pagination(10000,null), null, SessionSortField::Date, true, PanoptoCommons::getPanoptoDateTimeFromDateStringAndTimeString(date("Y-m-d"),date("H:i:s")), $states))->getSessions();
        }
        return $sessions;
    }

    public function getWeekNumberForDate($dateArray)
    {
        return $this->scientiaClient->getScientiaWeekNumberFromDate($dateArray[0]."-".$dateArray[1]."-".$dateArray[2]);
    }
}

?>
