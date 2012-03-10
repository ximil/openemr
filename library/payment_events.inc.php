<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2010 Z&H Consultancy Services Private Limited <sam@zhservices.com>
//
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
//
// A copy of the GNU General Public License is included along with this program:
// openemr/interface/login/GnuGPL.html
// For more information write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// 
// Author:   Eldho Chacko <eldho@zhservices.com>
//           Paul Simon K <paul@zhservices.com> 
//
// +------------------------------------------------------------------------------+
//===============================================================================
//This section handles the events of payment screen.
//===============================================================================
define('REPEAT_EVERY_DAY',     0);
define('REPEAT_EVERY_WEEK',    1);
define('REPEAT_EVERY_MONTH',   2);
define('REPEAT_EVERY_YEAR',    3);
define('REPEAT_EVERY_WORK_DAY',4);
//===============================================================================
//Create event in calender as arrived
function calendar_arrived($form_pid) {
	$Today=date('Y-m-d');
	//Take all recurring events relevent for today.
	$result_event=sqlStatement("SELECT * FROM openemr_postcalendar_events WHERE pc_recurrtype='1' and pc_pid =? and pc_endDate!='0000-00-00' 
		and pc_eventDate < ? and pc_endDate >= ? ",
		array($form_pid,$Today,$Today));
	if(sqlNumRows($result_event)==0)//no repeating appointment
	 {
		$result_event=sqlStatement("SELECT * FROM openemr_postcalendar_events WHERE pc_pid =?	and pc_eventDate = ?",
			array($form_pid,$Today));
		if(sqlNumRows($result_event)==0)//no appointment
		 {
			echo "<br><br><br>".htmlspecialchars( xl('Sorry No Appointment is Fixed'), ENT_QUOTES ).". ".htmlspecialchars( xl('No Encounter could be created'), ENT_QUOTES ).".";
			die;
		 }
		else//one appointment
		 {
			 $enc = todaysEncounter($form_pid);//create encounter
			 $zero_enc=0;
			 sqlStatement("UPDATE openemr_postcalendar_events SET pc_apptstatus ='@' WHERE pc_pid =? and pc_eventDate = ?",
				 array($form_pid,$Today));
		 }
	 }
	else//repeating appointment set
	 {
		while($row_event=sqlFetchArray($result_event))
		 {
			$pc_eid = $row_event['pc_eid'];
			$pc_eventDate = $row_event['pc_eventDate'];
			$pc_recurrspec_array = unserialize($row_event['pc_recurrspec']);
			while(1)
			 {
				if($pc_eventDate==$Today)//Matches so insert.
				 {
				 if(!$exist_eid=check_event_exist($pc_eid))
					{ 
					 update_event($pc_eid);
					}
				 else
					{
					 sqlStatement("UPDATE openemr_postcalendar_events SET pc_apptstatus = '@' WHERE pc_eid = ?",
						 array($exist_eid));
					}
					 $enc = todaysEncounter($form_pid);//create encounter
					 $zero_enc=0;
				 break;
				 }
				elseif($pc_eventDate>$Today)//the frequency does not match today,no need to increment furthur.
				 {
					echo "<br><br><br>".htmlspecialchars( xl('Sorry No Appointment is Fixed'), ENT_QUOTES ).". ".htmlspecialchars( xl('No Encounter could be created'), ENT_QUOTES ).".";
					die;
				 break;
				 }
				$pc_eventDate_array=split('-',$pc_eventDate);
				//Find the next day as per the frequency definition.
				$pc_eventDate=increment($pc_eventDate_array[2],$pc_eventDate_array[1],$pc_eventDate_array[0],
								$pc_recurrspec_array['event_repeat_freq'],$pc_recurrspec_array['event_repeat_freq_type']);
			 }
		 }
	 }
	return $enc;
}
//===============================================================================
// Get the patient's encounter ID for today, creating it if there is none.
//
function todaysEncounter($patient_id) {
  global $today;

  $encounter = todaysEncounterIf($patient_id);
  if ($encounter) return $encounter;

  $tmprow = sqlQuery("SELECT username, facility, facility_id FROM users " .
    "WHERE id = '" . $_SESSION["authUserID"] . "'");
  $username = $tmprow['username'];
  $facility = $tmprow['facility'];
  $facility_id = $tmprow['facility_id'];
  $conn = $GLOBALS['adodb']['db'];
  $encounter = $conn->GenID("sequences");
  addForm($encounter, "New Patient Encounter",
    sqlInsert("INSERT INTO form_encounter SET " .
      "date = '$today', " .
      "reason = 'Please indicate visit reason', " .
      "facility = '$facility', " .
      "facility_id = '$facility_id', " .
      "billing_facility = '$facility_id', " .
      "pid = '$patient_id', " .
      "encounter = '$encounter'"
    ),
    "newpatient", $patient_id, "1", "NOW()", $username
  );
  return $encounter;
}
//===============================================================================
// Get the patient's encounter ID for today, if it exists.
// In the case of more than one encounter today, pick the last one.
//
function todaysEncounterIf($patient_id) {
  global $today;
  $tmprow = sqlQuery("SELECT encounter FROM form_encounter WHERE " .
    "pid = '$patient_id' AND date = '$today 00:00:00' " .
    "ORDER BY encounter DESC LIMIT 1");
  return empty($tmprow['encounter']) ? 0 : $tmprow['encounter'];
}
//===============================================================================
// get the original event's repeat specs
function update_event($eid)
 {
	$origEventRes = sqlStatement("SELECT * FROM openemr_postcalendar_events WHERE pc_eid = ?",array($eid));
	$origEvent=sqlFetchArray($origEventRes);
	$oldRecurrspec = unserialize($origEvent['pc_recurrspec']);
	$duration=$origEvent['pc_duration'];
	$starttime=$origEvent['pc_startTime'];
	$endtime=$origEvent['pc_endTime'];
	$selected_date = date("Ymd");
	if ($oldRecurrspec['exdate'] != "") { $oldRecurrspec['exdate'] .= ",".$selected_date; }
	else { $oldRecurrspec['exdate'] .= $selected_date; }
	// mod original event recur specs to exclude this date
	sqlStatement("UPDATE openemr_postcalendar_events SET pc_recurrspec = ? WHERE pc_eid = ?",array(serialize($oldRecurrspec),$eid));
	// specify some special variables needed for the INSERT
  // no recurr specs, this is used for adding a new non-recurring event
    $noRecurrspec = array("event_repeat_freq" => "",
                        "event_repeat_freq_type" => "",
                        "event_repeat_on_num" => "1",
                        "event_repeat_on_day" => "0",
                        "event_repeat_on_freq" => "0",
                        "exdate" => ""
                    );
    // Useless garbage that we must save.
    $locationspecs = array("event_location" => "",
                            "event_street1" => "",
                            "event_street2" => "",
                            "event_city" => "",
                            "event_state" => "",
                            "event_postal" => ""
                        );
    $locationspec = serialize($locationspecs);
	$args['event_date'] = date('Y-m-d');
	$args['duration'] = $duration;
	// this event is forced to NOT REPEAT
	$args['form_repeat'] = "0";
	$args['recurrspec'] = $noRecurrspec;
	$args['form_enddate'] = "0000-00-00";
	$args['starttime'] = $starttime;
	$args['endtime'] = $endtime;
	$args['locationspec'] = $locationspec;
	$args['form_category']=$origEvent['pc_catid'];             
	$args['new_multiple_value']=$origEvent['pc_multiple'];             
	$args['form_provider']=$origEvent['pc_aid'];                           
	$args['form_pid']=$origEvent['pc_pid'];                  
	$args['form_title']=$origEvent['pc_title'];  
	$args['form_allday']=$origEvent['pc_alldayevent'];               
	$args['form_apptstatus']='@';           
	$args['form_prefcat']=$origEvent['pc_prefcatid'];              
	$args['facility']=$origEvent['pc_facility'];
	$args['billing_facility']=$origEvent['pc_billing_location'];
	InsertEvent($args,'payment');
 }
//===============================================================================
// check if event exists
function check_event_exist($eid)
 {
	$origEventRes = sqlStatement("SELECT * FROM openemr_postcalendar_events WHERE pc_eid = ?",array($eid));
	$origEvent=sqlFetchArray($origEventRes);
	$pc_catid=$origEvent['pc_catid'];
	$pc_aid=$origEvent['pc_aid'];
	$pc_pid=$origEvent['pc_pid'];
	$pc_eventDate=date('Y-m-d');
	$pc_startTime=$origEvent['pc_startTime'];
	$pc_endTime=$origEvent['pc_endTime'];
	$pc_facility=$origEvent['pc_facility'];
	$pc_billing_location=$origEvent['pc_billing_location'];
	$pc_recurrspec_array = unserialize($origEvent['pc_recurrspec']);
	$origEvent = sqlStatement("SELECT * FROM openemr_postcalendar_events WHERE pc_eid != ? and pc_catid=? and pc_aid=? ".
		"and pc_pid=? and pc_eventDate=? and pc_startTime=? and pc_endTime=? and pc_facility=? and pc_billing_location=?",
		array($eid,$pc_catid,$pc_aid,$pc_pid,$pc_eventDate,$pc_startTime,$pc_endTime,$pc_facility,$pc_billing_location));
	if(sqlNumRows($origEvent)>0)
	 {
	  $origEventRow=sqlFetchArray($origEvent);
	  return $origEventRow['pc_eid'];
	 }
	else
	 {
		if(strpos($pc_recurrspec_array['exdate'],date('Ymd')) === false)//;'20110228'
		 {
		  return false;
		 }
		else
		 {//this happens in delete case
		  return true;
		 }
	 }
 }
//===============================================================================
/**
 *	increment()
 *	returns the next valid date for an event based on the
 *	current day,month,year,freq and type
 *  @private
 *	@returns string YYYY-MM-DD
 */
function increment($d,$m,$y,$f,$t)
{
    if($t == REPEAT_EVERY_DAY) {
        return date('Y-m-d',mktime(0,0,0,$m,($d+$f),$y));
    } elseif($t == REPEAT_EVERY_WORK_DAY) {
        // a workday is defined as Mon,Tue,Wed,Thu,Fri
        // repeating on every or Nth work day means to not include
        // weekends (Sat/Sun) in the increment... tricky

        // ugh, a day-by-day loop seems necessary here, something where
        // we can check to see if the day is a Sat/Sun and increment
        // the frequency count so as to ignore the weekend. hmmmm....
        $orig_freq = $f;
        for ($daycount=1; $daycount<=$orig_freq; $daycount++) {
            $nextWorkDOW = date('D',mktime(0,0,0,$m,($d+$daycount),$y));
            if ($nextWorkDOW == "Sat") { $f++; }
            else if ($nextWorkDOW == "Sun") { $f++; }
        }
        // and finally make sure we haven't landed on a Sat/Sun
        // adjust as necessary
        $nextWorkDOW = date('D',mktime(0,0,0,$m,($d+$f),$y));
        if ($nextWorkDOW == "Sat") { $f+=2; }
        else if ($nextWorkDOW == "Sun") { $f++; }

        return date('Y-m-d',mktime(0,0,0,$m,($d+$f),$y));

    } elseif($t == REPEAT_EVERY_WEEK) {
        return date('Y-m-d',mktime(0,0,0,$m,($d+(7*$f)),$y));
    } elseif($t == REPEAT_EVERY_MONTH) {
        return date('Y-m-d',mktime(0,0,0,($m+$f),$d,$y));
    } elseif($t == REPEAT_EVERY_YEAR) {
        return date('Y-m-d',mktime(0,0,0,$m,$d,($y+$f)));
    }
}
//===============================================================================
?>