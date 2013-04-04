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

window.onload = function(){
    var sat = document.getElementById('selectAllTop');
    var dsat = document.getElementById('deSelectAllTop');
    var sab = document.getElementById('selectAllBottom');
    var dsab = document.getElementById('deSelectAllBottom');
    sat.onclick = selectAll;
    dsat.onclick = deSelectAll;
    sab.onclick = selectAll;
    dsab.onclick = deSelectAll;
}

function selectAll(){
    var cbox = document.getElementsByName('activity[]');
    for (i=0;i<cbox.length;i++){
        cbox[i].checked = true;
    }
}

function deSelectAll(){
    var cbox = document.getElementsByName('activity[]');
    for (i=0;i<cbox.length;i++){
        cbox[i].checked = false;
    }
}

function toggleCheckBox(activityHK){
    var checkBox = document.getElementById('cbox-'+activityHK);
    checkBox.checked ? checkBox.checked=false : checkBox.checked=true;
}

function hideTopButtons(){
    document.getElementById('bookSelected').style.visibility = 'hidden';
    document.getElementById('selectAllTop').style.visibility = 'hidden';
    document.getElementById('deSelectAllTop').style.visibility = 'hidden';
}

function removeButtonOnSubmit(buttons,infoBoxes,msg){
    //Remove all submission buttons
    for(var i=0;i<buttons.length;i++){
        document.getElementById(buttons[i]).style.visibility='hidden';
    }
    //Replace submission buttons with divs containing a message
    for(var i=0;i<infoBoxes.length;i++){
        document.getElementById(infoBoxes[i]).innerHTML = msg;
    }
}