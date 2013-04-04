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

class ScientiaCommons
{
    public static function getWeekDayNumberAsName($dayNumber)
    {
        switch($dayNumber)
        {
            case 0:     return "Monday";
            case 1:     return "Tuesday";
            case 2:     return "Wednesday";
            case 3:     return "Thursday";
            case 4:     return "Friday";
            case 5:     return "Saturday";
            case 6:     return "Sunday";
            //default:  return null;
        }
    }
    
    public static function getDateStringFromScientiaDateTime($scientiaDateTime)
    {
        $tempDate = explode("T",$scientiaDateTime);
        return $tempDate[0];
    }

    public static function getTimeStringFromScientiaDateTime($scientiaDateTime)
    {
        $tempTime = explode("T",$scientiaDateTime);
        $tempTime = explode("+",$tempTime[1]);
        return $tempTime[0];
    }

    //"Scientia" week day number
    public static function getWeekDayNameAsNumber($weekDayName)
    {
        switch(strtolower($weekDayName))
        {
            case "monday":      return 0;
            case "tuesday":     return 1;
            case "wednesday":   return 2;
            case "thursday":    return 3;
            case "friday":      return 4;
            case "saturday":    return 5;
            case "sunday":      return 6;
            //default:          return null;
        }
    }

    public static function getDateStringFromScientiaDate($date)
    {
        $dArr = explode("/",$date);
        return $dArr[2]."-".$dArr[1]."-".$dArr[0];
    }

    public static function getScientiaTimeAsUnixTime($scientiaTime)
    {
        return strtotime(substr($scientiaTime,11,8));
    }

    public static function getUnixTime($date,$time)
    {
        $dArr = explode('-',$date);
        $tArr = explode(':',$time);
        return mktime($tArr[0],$tArr[1],$tArr[2],$dArr[1],$dArr[2],$dArr[0]);
    }

    public static function getRepeatingDaysInEachWeek($suggestedDays)
    {
        return explode(",",$suggestedDays);
    }

    public static function excludePreviousWeeksFromWeekPattern($weekPattern,$currentSciWeekNum)
    {
        $currentSciWeekNum = intval((string)$currentSciWeekNum); //translate from simplexmlelement to int
        $currentSciWeekNum = $currentSciWeekNum>=0?$currentSciWeekNum:0; //make sure the number is !< 0
        return substr("0000000000000000000000000000000000000000000000000000",0,$currentSciWeekNum).substr($weekPattern,$currentSciWeekNum);
    }

    public static function getActivityTypeDisplayableName($type)
    {
        switch($type)
        {
            case "W":   return "Workshop";       break;
            case "CL":  return "Computing Lab";  break;
            case "F":   return "Fieldwork";      break;
            case "S":   return "Seminar";        break;
            case "Tut": return "Tutorial";       break;
            case "P":   return "Practical";      break;
            case "L":   return "Lecture";        break;
            case "SC":  return "Study Clinic";   break;
            case "B":   return "Booking";        break;
            case "FS":  return "Film Showing";   break;
            default: return "Other";
        }
    }

    public static function getScientiaDateTimeAsTimeStamp($scientiaDateTime)
    {
        return strtotime(substr($scientiaDateTime,0,10));
    }

    public static function getWeekNumberArrayFromWeekPattern($weekPattern)
    {
        $weekValueArray = array();
        $weeks = strlen($weekPattern);
        $pos = strpos($weekPattern,"1");
        if($pos!==false)
        {
            $weekValueArray[] = $pos+1;
            for($pos=$pos+1;$pos<$weeks;$pos++)
            {
                $pos = strpos($weekPattern,"1",$pos);
                if($pos!==false)
                {
                    $weekValueArray[] = $pos+1;
                }
                else
                {
                    break;
                }
            }
        }
        return $weekValueArray;
    }
}
?>
