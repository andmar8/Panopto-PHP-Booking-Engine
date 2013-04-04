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
    require_once(dirname(__FILE__)."/includes/helpers/PanoptoBookingEngineHelper.php");
    require_once(dirname(__FILE__)."/includes/clients/scientia/commons/ScientiaCommons.php");
    require_once(dirname(__FILE__)."/includes/commons/PanoptoBookingEngineCommons.php");

    $helper = null;
    $error = null;
    try
    {
        $helper = new PanoptoBookingEngineHelper(
                                /** DEBUG, uncomment below if you want to specify on a get query what the current user is **/
                                /*isset($_GET["user"])?$_GET["user"]:*/PanoptoBookingEngineCommons::extractUsernameFromShibPrincipalName(getenv("YOUR_SHIB_ATTRIBUTE_FOR_LOGGED_IN_USER"))
                                ,isset($_POST["module"])?$_POST["module"]:null
                                ,"Q1213-"
                                ,"panoptoserver.example.com"
                        );
    }
    catch(Exception $e)
    {
        $error = $e->getMessage();
    }
    //If user has pressed html button to Apply changes
    if(isset($_POST["schedule"]))
    {
        try
        {
            //if any activities were selected
            $activities = isset($_POST["activity"])?$_POST["activity"]:array();
            //book any valid sessions that don't clash
            $helper->bookSessions($activities);
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
        }
    }
    require 'https://www.ncl.ac.uk/fragments/ncltemplate/proxy/helpdesk_header.php';
?>
<style type="text/css" media="screen">
@import "https://www.ncl.ac.uk/includes/css/forms.css";
@import "bookingEngine.css";
</style>
<script type="text/javascript" src="bookingEngine.js" ></script>
<h3>ReCap Booking Request</h3>
<?
    if(!isset($error))
    {
?>
<p>Please use the form below to request a recording in a ReCap enabled
venue.</p>
<p>This request will be logged under your ISS username:
<strong><?= $helper->getUser() ?></strong></p>
<form method="POST" action="#">
  <fieldset class="leftLabels">
    <legend>Recording Venue &amp; Information</legend>
    <label>
      <span>
        Module Code
      </span>
<?
    try
    {
        $modules = $helper->getModulesForAStaffId();
        if(count($modules->getModules())>0)
        {
?>
      <select name="module">
<?
            foreach($modules->getModules() as $module)
            {
?>
          <option value="<?= $module->getHostKey() ?>"<? if($helper->getSelectedModule()==$module->getHostKey()){?>selected<?} ?>><?= $module->getName() ?>:&nbsp;<?= $module->getDescription() ?></option>
<?
            }
?>
      </select>
    </label>
    <label>
        <input value="Find ReCap Enabled Activities" type="Submit"/>
<?
        }
        else
        {
?>
      <select>
        <option disabled>No modules are associated with the user '<?= $helper->getUser() ?>'</option>
      </select>
<?
        }
    }
    catch(Exception $e)
    {
?>
      <select>
          <option disabled><?= $e->getMessage() ?></option>
      </select>
<?
    }
?>
    </label>
  </fieldset>
</form>
<?
    if($helper->getSelectedModule()!=null && $helper->getSelectedModule()!="")
    {
        try
        {
            if($helper->getLocationActivitySchedules()->count()>0)
            {
?>
    <form method="POST" action="bookings.php">
        <!-- Comment line below to disable debug -->
        <input type="hidden" name="user" value="<?= $helper->getUser() ?>"/>
        <input type="hidden" name="unitID" value="<?= $helper->getSelectedModule() ?>"/>
        <input type="submit" name="back" value="View All Bookings" />
    </form>
    <form id="bookingForm" name="bookingForm" method="POST" action="#">
        <fieldset id="bookingsFS">
            <legend>Booked or book-able activities for the current week onwards</legend>
            <div id="wait"></div>
            <input id="bookSelected" name="schedule" value="Book Selected" type="submit" onclick="removeButtonOnSubmit(['bookSelected','bookSelectedBottom'],['wait','waitBottom'],'Please wait...')" /><br/>
            <input id="selectAllTop" type="button" name="selectall" value="Select All"/>
            <input id="deSelectAllTop" type="button" name="deselectall" value="Deselect All"/>
<?
                //Add up number of $booked==true is more reliable than
                //number of clashes, count($scientiaXML->Activities->Activity)
                //is reliable too.
                $bookedCount = 0;
                $notInThePast = 0;
                foreach($helper->getLocationActivitySchedules()->getLocationActivitySchedules() as $activity)
                {
                    //Test week pattern as an int NOT a string, if there are no 1's in the string the int is !>0
                    if(ScientiaCommons::excludePreviousWeeksFromWeekPattern($activity->getWeekPattern(),$helper->getCurrentSciWeekNum())>0)
                    {
                        /**********************************
                         * We need to account for the
                         * current week being valid
                         * but the current "day" has passed,
                         * could be tricky on multi days?
                         */
                        
                        $notInThePast += 1;
                        $booked = $helper->detectActivitySessionClash($activity->getHostKey());
                        if($booked)
                        {
                            $bookedCount += 1;
                        }

                        $bgCol = "booked ";
?>
            <div class="<?= $booked?$bgCol:"" ?>activity" onclick="toggleCheckBox('<?= $activity->getHostKey() ?>')">
                <h2><? if(!$booked){?><input id="cbox-<?= $activity->getHostKey() ?>" type="checkbox" name="activity[]" value="<?= $activity->getHostKey() ?>" onclick="toggleCheckBox('<?= $activity->getHostKey() ?>')" /><?}?><?= $activity->getName() ?></h2>
<?
                                $dArr = explode('-',substr($activity->getStartDate(),0,10));
                                $date = date('jS M Y',mktime(0,0,0,$dArr[1],$dArr[2],$dArr[0]));
?>
                <label>From: <b><?= $date ?></b></label><br/>
                <label>Every: <b><? if($activity->getSuggestedDays()=="N/A"){echo ScientiaCommons::getWeekDayNumberAsName($activity->getScheduledDay());}else{echo $activity->getSuggestedDays();} ?></b></label><br/>
                <label>At: <b><?= ScientiaCommons::getTimeStringFromScientiaDateTime($activity->getScheduledStartTime()) ?></b> 'til <b><?= ScientiaCommons::getTimeStringFromScientiaDateTime($activity->getScheduledEndTime()) ?></b></label><br/>
                <label>During weeks: <b><?= $activity->getWeekLabels() ?></b></label><br/>
                <label>In: <b><?= $activity->getLocationDescription() ?>&nbsp;(<?= $activity->getLocationName() ?>)</b>, Holds <b><?= $activity->getLocationCapacity() ?></b></label>
            </div>
<?
                    }
                }

                if($bookedCount<$notInThePast)
                {
?>
            <input id="selectAllBottom" type="button" name="selectall" value="Select All"/>
            <input id="deSelectAllBottom" type="button" name="deselectall" value="Deselect All"/>
            <br/><div id="waitBottom"></div><input id="bookSelectedBottom" name="schedule" value="Book Selected" type="Submit" onclick="removeButtonOnSubmit(['bookSelected','bookSelectedBottom'],['wait','waitBottom'],'Please wait...')" />
<?
                }
                else
                {
                    if($notInThePast==0)
                    {
?>
        <p>No scheduled activities in ReCap enabled venues, occurring from this week onwards, were found</p>
<?
                    }
?>
        <script type="text/javascript">
            hideTopButtons();
        </script>
<?
                }
?>
    </fieldset>
<?
            }
            else
            {
?>
    <fieldset>
        <p>No scheduled activities in ReCap enabled venues were found</p>
    </fieldset>
<?
            }
        }
        catch(Exception $e)
        {
            //$e->getMessage();
        }
?>
    <input type="hidden" name="module" value="<?= $helper->getSelectedModule() ?>" />
<?
    }
?>
    <input name="mandatory[recording_type]" value="" type="hidden"/>
</form>
<?
    }
    else
    {
?>
        <?= $error ?>
        <form method="post" action="#">
            <input type="submit" name="back" value="Back" />
        </form>
<?
    }

    require("https://www.ncl.ac.uk/fragments/footer.phtml");
?>