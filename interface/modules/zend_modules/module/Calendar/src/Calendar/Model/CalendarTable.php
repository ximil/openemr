<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
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
// Author:   Remesh Babu  <remesh@zhservices.com>
//           Jacob T.Paul <jacob@zhservices.com>
//           Eldho Chacko <eldho@zhservices.com>
//
// +------------------------------------------------------------------------------+
namespace Calendar\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CalendarTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
		
		// List all data to the calendar
		public function listCalendar($day, $type, $providerID)
		{
				$phpTime = $this->js2PhpTime($day);
				//echo $phpTime . "+" . $type;	
				switch($type){
					case "month":
						$st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
						$et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
						break;
					case "week":
						//suppose first day of a week is monday 
						$monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
						//echo date('N', $phpTime);
						$st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
						$et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
						break;
					case "day":
						$st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
						$et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
						break;
				}
				return $this->listCalendarByRange($st, $et, $providerID);
		}
		
		//Fetching Data from Table
		public function listCalendarByRange($sd, $ed, $providerID){
				$ret = array();
				$ret['events'] = array();
				$ret["issort"] =true;
				$ret["start"] = $this->php2JsTime($sd);
				$ret["end"] = $this->php2JsTime($ed);
				$ret['error'] = null;
				try{
						$dtFrom = $this->php2MySqlTime($sd);
						$dtTo		= $this->php2MySqlTime($ed);
						$sql = "SELECT pd.title, 
										CONCAT(pd.lname, ', ', pd.fname) AS patientName, 
										pd.DOB,   
										pe.*, 
										CONCAT(pe.pc_eventDate,' ',pe.pc_startTime) AS  StartTime, 
										CONCAT(IF(pe.pc_endDate = '0000-00-00', pe.pc_eventDate, pe.pc_endDate),' ', pe.pc_endTime) AS  EndTime 
									FROM openemr_postcalendar_events pe 
									LEFT JOIN patient_data pd 
									ON pd.id=pe.pc_pid 
									WHERE  pe.pc_aid = ?
										AND pe.pc_time  
												BETWEEN ? 
												AND ? 
									ORDER BY pe.pc_time";
						
						$handle = sqlStatement($sql, array($providerID, $dtFrom, $dtTo));
						while ($row = sqlFetchArray($handle)) {
								$birthDate = $row['DOB'];
								$birthDate = explode("-", $birthDate);
								$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));
						
								$ret['events'][] = array(
										$row['pc_eid'],
										$row['patientName'],
										$this->php2JsTime($this->mySql2PhpTime($row['StartTime'])),
										$this->php2JsTime($this->mySql2PhpTime($row['EndTime'])),
										$row['IsAllDayEvent'],
										0, //more than one day event
										//$row->InstanceType,
										0,//Recurring event,
										$row['Color'],
										1,//editable
										$row['Location'],
										'',//$attends
										$age,
										$row['DOB'],
										$row['pc_pid']
								);
						}
				}catch(Exception $e){
					 $ret['error'] = $e->getMessage();
				}
				return $ret;
		}
		
		// Date Time js to php
		public function js2PhpTime($jsdate)
		{
				if(preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches)==1){
					$ret = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
				}else if(preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches)==1){
					$ret = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
				}
				return $ret;
		}

		public function php2JsTime($phpDate){
				return date("m/d/Y H:i", $phpDate);
		}

		public function php2MySqlTime($phpDate){
				return date("Y-m-d H:i:s", $phpDate);
		}

		public function mySql2PhpTime($sqlDate){
				$arr = date_parse($sqlDate);
				return mktime($arr["hour"],$arr["minute"],$arr["second"],$arr["month"],$arr["day"],$arr["year"]);
		
		}
		
		// Get Providers Data
		public function getProviderData($data)
		{
				$option = $data['option'];
				$arr 		= array();
				
				if ($option == 'search') {
						$sql = "SELECT id, fname, lname, specialty FROM users 
												WHERE active = 1 
												AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) 
												AND authorized = 1
												ORDER BY lname, fname";
						$result = sqlStatement($sql);
						$rows[0] = array (
								'value' => '',
								'label' => xlt('All Providers'),
								'selected' => TRUE,
								'disabled' => FALSE
							);
						$i = 1;
	
						while($row = sqlFetchArray($result)) {
							$rows[$i] = array (
								'value' => $row['id'],
								'label' => $row['fname']." ".$row['lname'],
							);
							$i++;
						}
						return $rows;
				} else if ($option == 'addedit') {
						$sql = "SELECT id, fname, lname, specialty FROM users 
												WHERE active = 1 
												AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) 
												AND authorized = 1
												ORDER BY lname, fname";
						$result = sqlStatement($sql);

						$i = 0;
	
						while($row = sqlFetchArray($result)) {
							$rows[$i] = array (
								'value' => $row['id'],
								'label' => $row['fname']." ".$row['lname'],
								'selected' => true,
							);
							$i++;
						}
						return $rows;
				} else {
						$sql = "SELECT id, fname, lname, specialty FROM users 
												WHERE active = 1 
												AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) 
												AND authorized = 1
												ORDER BY lname, fname";
						$result = sqlStatement($sql);
						while ($row = sqlFetchArray($result)) {
								array_push($arr, $row);
						}
						return $arr;
				}
		}
		
		// Get Categories Data
		public function getCategoriesData($data)
		{
				$option = $data['option'];
				$id 		= $data['id'];
				$arr 		= array();
				
				// Search options
				if ($option == 'search') {
						$sql = "SELECT pc_catid, pc_catname FROM openemr_postcalendar_categories
												ORDER BY pc_catname";
						$result = sqlStatement($sql);
						$rows[0] = array (
								'value' => '',
								'label' => xlt('All Categories'),
								'selected' => TRUE,
								'disabled' => FALSE
							);
						$i = 1;
	
						while($row = sqlFetchArray($result)) {
							$rows[$i] = array (
								'value' => $row['pc_catid'],
								'label' => $row['pc_catname'],
							);
							$i++;
						}
						return $rows;
				}
				
				// Add or Edit Patient Calendar
				if ($option == 'addeditpatient') {
						$catType = 0;
						if ($id == 0) $selected = 5; //Default value 
						$sql = "SELECT pc_catid,
														pc_cattype,
														pc_catname,
														pc_recurrtype,
														pc_duration,
														pc_end_all_day
												FROM openemr_postcalendar_categories
												ORDER BY pc_catname";
						$result = sqlStatement($sql);
						$i = 0;
						while($row = sqlFetchArray($result)) {
								if ($row['pc_cattype'] != $catType) continue;
								$select = ($row['pc_catid'] == $selected) ? TRUE : FALSE;
								$rows[$i] = array (
									'value' => $row['pc_catid'],
									'label' => $row['pc_catname'],
									'selected' => $select,
								);
								$i++;
						}
						return $rows;
				}
				
				// Add or Edit Provider Calendar
				if ($option == 'addeditprovider') {
						$catType = 1;
						if ($id == 0) $selected = 2; //Default value 
						$sql = "SELECT pc_catid,
														pc_cattype,
														pc_catname,
														pc_recurrtype,
														pc_duration,
														pc_end_all_day
												FROM openemr_postcalendar_categories
												ORDER BY pc_catname";
						$result = sqlStatement($sql);
						$i = 0;
						while($row = sqlFetchArray($result)) {
								if ($row['pc_cattype'] != $catType) continue;
								$select = ($row['pc_catid'] == $selected) ? TRUE : FALSE;
								$rows[$i] = array (
									'value' => $row['pc_catid'],
									'label' => $row['pc_catname'],
									'selected' => $select,
								);
								$i++;
						}
						return $rows;
				}
				
				// Add or Edit Provider Preferred Categories Calendar
				if ($option == 'addeditproviderprefcat') {
						$catType = 1;
						if ($id == 0) $selected = 2; //Default value 
						$sql = "SELECT pc_catid,
														pc_cattype,
														pc_catname,
														pc_recurrtype,
														pc_duration,
														pc_end_all_day
												FROM openemr_postcalendar_categories
												ORDER BY pc_catname";
						$result = sqlStatement($sql);
						$rows[0] = array (
								'value' => '0',
								'label' => xlt('None'),
								'selected' => TRUE,
								'disabled' => FALSE
							);
						$i = 1;
						while($row = sqlFetchArray($result)) {
								$duration = round($row['pc_duration'] / 60);
								if ($row['pc_end_all_day']) $duration = 1440;
								
								$select = ($row['pc_catid'] == $selected) ? TRUE : FALSE;
								if ($duration) {
										$rows[$i] = array (
											'value' => $row['pc_catid'],
											'label' => $row['pc_catname'],
											'selected' => $select,
										);
										$i++;
								}
								if ($row['pc_cattype'] != $catType) continue;
						}
						return $rows;
				}
		}
		
		// Get Facilities Data
		public function getFacilitiesData($option='')
		{
				$arr = array();
				if ($option == 'search') {
						$sql = "SELECT id, name FROM facility
												ORDER BY name";
						$result = sqlStatement($sql);
						$rows[0] = array (
								'value' => '',
								'label' => xlt('All Facilities'),
								'selected' => TRUE,
								'disabled' => FALSE
							);
						$i = 1;
	
						while($row = sqlFetchArray($result)) {
							$rows[$i] = array (
								'value' => $row['id'],
								'label' => $row['name'],
							);
							$i++;
						}
						return $rows;
				}
				
				// Add or Edit Calendar
				if ($option == 'addedit') {
						$sql = "SELECT id, name FROM facility
												WHERE service_location != 0
												ORDER BY name";
						$result = sqlStatement($sql);

						$i = 0;
						while($row = sqlFetchArray($result)) {
							$rows[$i] = array (
								'value' => $row['id'],
								'label' => $row['name'],
																'selected' => true,
							);
							$i++;
						}
						return $rows;
				}
		}
		
		// Get Billing Facility
		public function getBillingFacilityData()
		{
				$sql = "SELECT id, name
										FROM facility
										WHERE billing_location = 1";
				$result = sqlStatement($sql);

				$i = 0;
				while($row = sqlFetchArray($result)) {
					$rows[$i] = array (
						'value' => $row['id'],
						'label' => $row['name'],
														'selected' => true,
					);
					$i++;
				}
				return $rows;
		}
		
		// Get Status
		public function getStatusData()
		{
				$sql = "SELECT * FROM list_options
										WHERE list_id='apptstat'
										ORDER BY seq, title";
				$result = sqlStatement($sql);

				$i = 0;
				while($row = sqlFetchArray($result)) {
						$select = ($i == 0) ? TRUE : FALSE;
						$rows[$i] = array (
								'value' => $row['option_id'],
								'label' => $row['title'],
								'selected' => $select,
						);
						$i++;
				}
				return $rows;
		}
		
		// Get Patient Deatils
		public function getPatientDetails($data)
		{
				$arr = array();
				if (isset($data['keyword']) && $data['keyword'] != '') { 
						$option = $data['option'];
						$keyword = $data['keyword'];
						if ($option == 'Name') {
							$where 	= " WHERE lname LIKE ? OR fname LIKE ?";
							$values = array('%'.$keyword.'%', '%'.$keyword.'%');
						}
						if ($option == 'Phone') {
							$where = " WHERE phone_home LIKE ?";
							$values = array('%'.$keyword.'%');
						}
						if ($option == 'DOB') {
							$where = " WHERE DOB LIKE ?";
							$values = array('%'.$keyword.'%');
						}
						if ($option == 'ID') {
							$where = " WHERE id LIKE ?";
							$values = array('%'.$keyword.'%');
						}
						if ($option == 'SSN') {
							$where = " WHERE ss LIKE ?";
							$values = array('%'.$keyword.'%');
						}
				
						$sql = "SELECT *, CONCAT(lname, ',' , fname)as name FROM patient_data $where";
						$result = sqlStatement($sql, $values);
						$i = 0;
						while ($row = sqlFetchArray($result)) {
								//array_push($arr, $row);
								$arr[$i]['name'] = htmlspecialchars($row['name'],ENT_QUOTES);
								$arr[$i]['phone_home'] = htmlspecialchars($row['phone_home'],ENT_QUOTES);
								$arr[$i]['ss'] = htmlspecialchars($row['ss'],ENT_QUOTES);
								$arr[$i]['DOB'] = htmlspecialchars($row['DOB'],ENT_QUOTES);
								$arr[$i]['id'] = htmlspecialchars($row['id'],ENT_QUOTES);
								$i++;
						}
				}
				return $arr;
		}
    
}


