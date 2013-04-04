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

    error_reporting(E_ALL);
    date_default_timezone_set("Europe/London");
    $panoptoClientDir = dirname(__FILE__)."/panoptoPHP";
    require_once($panoptoClientDir."/includes/commons/PanoptoCommons.php");
    require_once($panoptoClientDir."/includes/helpers/PanoptoBookingsHelper.php");
    require_once($panoptoClientDir."/includes/commons/PanoptoBookingEngineCommons.php");

    $helper = null;
    $error = null;
    if($_POST)
    {
        try
        {
            $helper = new PanoptoBookingsHelper(
                                    /** DEBUG, uncomment below if you want to specify on a get query what the current user is **/
                                    /*isset($_GET["user"])?$_GET["user"]:*/PanoptoBookingEngineCommons::extractUsernameFromShibPrincipalName(getenv("YOUR_SHIB_ATTRIBUTE_FOR_LOGGED_IN_USER"))
                                    ,isset($_POST["unitID"])?$_POST["unitID"]:null
                                    ,"Q1213-"
                                    ,"panoptoserver.example.com"
                            );
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
        }

        if(isset($_POST["activity"]))
        {
            $helper->deleteSessions($_POST["activity"]);
        }
    }
    require 'https://www.ncl.ac.uk/fragments/ncltemplate/proxy/helpdesk_header.php';
?>
<style type="text/css" media="screen">
@import "https://www.ncl.ac.uk/includes/css/forms.css";
@import "bookingEngine.css";
</style>
<script type="text/javascript" src="bookingEngine.js"></script>

<h3>ReCap Bookings</h3>

<p>Please use the form below to delete individual recordings for the module code:
<strong><?= $helper->getModuleCode() ?></strong></p>

<p>Fields marked with an asterisk are mandatory.</p>

<!-- Uncomment two lines below to disable debug -->
<!--<form action="index.php<?/* ?user=<?= $helper->getUser() ?> */?>" method="post">
    <?/* <input type="hidden" name="user" value="<?= $helper->getUser() ?>"/> */?>-->
<!-- Comment two lines below to enable debug -->
<form action="index.php?user=<?= $helper->getUser() ?>" method="post">
    <input type="hidden" name="user" value="<?= $helper->getUser() ?>"/>
    <input type="hidden" name="module" value="<?= $helper->getSelectedModule() ?>"/>
    <input type="submit" name="back" value="Back To Booking Request" />
</form>
<form name="recordingForm" id="recordingForm" action="#" method="POST">
    <input type="hidden" name="recordingID" id="recordingID" value="" />
    <input type="hidden" name="speaker" id="speaker" value="" />
    <input type="hidden" name="notifyName" id="notifyName" value="" />
    <input type="hidden" name="notifyEmail" id="notifyEmail" value="" />
    <!-- Comment line below to disable debug -->
    <input type="hidden" name="user" value="<?= $helper->getUser() ?>"/>
    <input type="hidden" name="unitID" value="<?= $helper->getSelectedModule() ?>"/>
</form>
<form method="POST" action="#">

<?
    if($_POST)
    {
        try
        {
            $sessions = $helper->getSessions();
            if(count($sessions)>0)
            {
?>
    <fieldset>
        <legend>Module Booking Information</legend>
        <div id="wait"></div>
        <input id="delete" name="delete" value="Delete Selected" type="Submit" onclick="removeButtonOnSubmit(['delete','deleteBottom'],['wait','waitBottom'],'Please wait...')" /><br/>
        <input id="selectAllTop" type="button" name="selectall" value="Select All"/>
        <input id="deSelectAllTop" type="button" name="deselectall" value="Deselect All"/>
<?
                $todaysTimeStamp = time();
                foreach($sessions as $session)
                {
                    $startTime = PanoptoCommons::getUTCDateTimeAsLocalDateTime($session->getStartTime());
                    $dateArray = PanoptoCommons::getDateArrayFromPanoptoDateTime($startTime);
                    $timeArray = PanoptoCommons::getTimeArrayFromPanoptoDateTime($startTime);
                    $recordingTimeStamp = mktime($timeArray[0],$timeArray[1],$timeArray[2],$dateArray[1],$dateArray[2],$dateArray[0]);
                    $creator = $helper->getCreator($session->getCreatorId());
?>
        <div class="activity" onclick="toggleCheckBox('<?= $session->getId() ?>')">
            <h3 class="booked"><? if($recordingTimeStamp>=$todaysTimeStamp){ ?><input id="cbox-<?= $session->getId() ?>" type="checkbox" name="activity[]" value="<?= $session->getId() ?>" onclick="toggleCheckBox('<?= $session->getId() ?>')" /><? } ?><?= $session->getName()?> - <?= date('jS M Y',$recordingTimeStamp) ?></h3>
            <label>Week: <b><?= $helper->getWeekNumberForDate($dateArray) ?></b></label><br/>
            <label>Day: <b><?= date("l",$recordingTimeStamp) ?></b> Time: <b><?= $timeArray[0].":".$timeArray[1] ?></b> Duration: <b><?= $session->getDuration()/60/* <- divided by seconds*/ ?> minutes</b></label><br/>
            Recording Status: <b><?= $session->getState() ?></b> Streamed only: <b><?= $session->getIsDownloadable()?"No":"Yes"; ?></b>
        </div>
<?
                }
?>
            <input id="selectAllBottom" type="button" name="selectall" value="Select All"/>
            <input id="deSelectAllBottom" type="button" name="deselectall" value="Deselect All"/><br/>
            <div id="waitBottom"></div>
            <input id="deleteBottom" name="delete" value="Delete Selected" type="Submit" onclick="removeButtonOnSubmit(['delete','deleteBottom'],['wait','waitBottom'],'Please wait...')" />
    </fieldset>
<?
            }
        }
        catch(Exception $e)
        {
            //echo $e->__toString();
        }
    }
?>
    <!-- Comment line below to disable debug -->
    <input type="hidden" name="user" value="<?= $helper->getUser() ?>"/>
    <input type="hidden" name="unitID" value="<?= $helper->getSelectedModule() ?>"/>
</form>
<?
    require("https://www.ncl.ac.uk/fragments/footer.phtml");
?>