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
namespace Appointments\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AppointmentsTable extends AbstractTableGateway
{
  public $tableGateway;

  public function __construct(TableGateway $tableGateway)
  {
    $this->tableGateway = $tableGateway;
  }
  
  public function fetchAll()
  {
    $resultSet = $this->tableGateway->select();
    return $resultSet;
  }
  
  public function listAppointments($data)
  {
    $where 		= '';
    $criteria 	= (!empty($data['criteria'])) ? $data['criteria'] : '';
    if ($criteria == 'DOS') {
        $dos 	= $data['dos'];
        $dtFrom 	= $data['dtFrom'];
        $dtTo 	= $data['dtFrom'];
        if ($dtFrom != '' && $dtTo != '') {
          $where .= " AND e.pc_eventDate BETWEEN '$dtFrom' AND '$dtTo'";
        }
    }
    if ($criteria == 'patient') {
        $patient 	= $data['patient'];
        $where .= " AND p.pid='$patient'";
    }
    $start = isset($data['page']) ? $data['page'] :  0;
      $rows = isset($data['rows']) ? $data['rows'] : 20;
      if ($data['page'] == 1) {
          $start = $data['page'] - 1;
      } elseif ($data['page'] > 1) {
          $start = (($data['page'] - 1) * $rows);
      }
    
    $sql = "SELECT CONCAT(u.fname,' ',u.mname,' ',u.lname) AS `Encounter_Provider` ,
        DATE_FORMAT(e.pc_eventDate,'%m/%d/%y,%W') AS `Date` ,
        DATE_FORMAT(e.pc_startTime,'%h:%i %p') AS `Time` ,
        p.pubpid AS `ID` ,
        CONCAT(p.fname,' ', p.mname,' ',p.lname) AS `Patient` ,
        p.pid AS `PatientID` ,
        p.DOB AS `DOB` ,
        p.phone_home AS `Phone` ,
        e.pc_hometext AS `Comments` ,
        c.pc_catname AS `Type` ,
        lp.title AS `Status` ,
        '###3###' AS `Primary_Insurance` ,
        '###2###' AS `Insurance_Balance` ,
        '###1###' AS `Patient_Balance` ,
        pc_eventDate AS `Event_Date` ,
        pc_startTime AS `Start_Time` ,
        lower(u.lname) AS `Middle Name` , lower(u.fname) AS `Patient_Id`
          FROM openemr_postcalendar_events AS e
          LEFT OUTER JOIN patient_data AS p
        ON p.pid = e.pc_pid
          LEFT OUTER JOIN users AS u
        ON u.id = e.pc_aid
          LEFT OUTER JOIN openemr_postcalendar_categories AS c
        ON c.pc_catid = e.pc_catid
          LEFT OUTER JOIN facility as bf
        ON e.pc_billing_location=bf.id
          LEFT OUTER JOIN facility as sf
        ON e.pc_facility=sf.id
          LEFT OUTER JOIN list_options AS lp
        ON e.`pc_apptstatus`=lp.`option_id`
        AND lp.list_id='apptstat'
          WHERE 1 = '1' AND 2 = '2' 
        $where 
          ORDER BY CONCAT(u.fname,' ',u.mname,' ',u.lname),
        pc_eventDate,
        pc_startTime,
        lower(u.lname),
        lower(u.fname)  ";
    $result = sqlStatement($sql);
    $noRows = sqlNumRows($result);
    $sql .= " LIMIT $start, $rows";
    
    $result = sqlStatement($sql);
        $arr = array();
    $rows = array();
    $i = 0;
    $rows['total'] = $noRows;
    while ($row = sqlFetchArray($result)) {
      $arr[$i]['Encounter_Provider'] 	= htmlspecialchars($row['Encounter_Provider'],ENT_QUOTES);
      $arr[$i]['Date'] 			          = htmlspecialchars($row['Date'],ENT_QUOTES);
      $arr[$i]['Time'] 			          = htmlspecialchars($row['Time'],ENT_QUOTES);
      $arr[$i]['ID'] 			            = htmlspecialchars($row['ID'],ENT_QUOTES);
      $arr[$i]['Patient'] 		        = htmlspecialchars($row['Patient'],ENT_QUOTES);
      $arr[$i]['PatientID'] 		      = htmlspecialchars($row['PatientID'],ENT_QUOTES);
      $arr[$i]['DOB'] 			          = htmlspecialchars($row['DOB'],ENT_QUOTES);
      $arr[$i]['Phone'] 			        = htmlspecialchars($row['Phone'],ENT_QUOTES);
      $arr[$i]['Comments'] 		        = htmlspecialchars($row['Comments'],ENT_QUOTES);
      $arr[$i]['Type'] 			          = htmlspecialchars($row['Type'],ENT_QUOTES);
      $arr[$i]['Status'] 			        = htmlspecialchars($row['Status'],ENT_QUOTES);
      $arr[$i]['Event_Date'] 		      = htmlspecialchars($row['Event_Date'],ENT_QUOTES);
      $arr[$i]['Start_Time'] 		      = htmlspecialchars($row['Start_Time'],ENT_QUOTES);
      $arr[$i]['Patient_Id'] 		      = htmlspecialchars($row['Patient_Id'],ENT_QUOTES);      
      $i++;
    }
    $rows["rows"] = $arr;
    return $rows;
  }
}


