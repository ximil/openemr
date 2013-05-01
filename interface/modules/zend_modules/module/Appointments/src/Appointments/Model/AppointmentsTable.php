<?php
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
	    $where .= " AND e.pc_eventDate BETWEEN '$dtFrom' AND '$dtTo'";
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
			p.pid AS `PID` ,
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
	//$fh = fopen(dirname(__FILE__)."/text.txt","a");
	//fwrite($fh,"data is :". $sql.print_r($data,1));
	$result = sqlStatement($sql);
    	$arr = array();
	$i = 0;
	while ($row = sqlFetchArray($result)) {
    	    array_push($arr, $row);
	    $i++;
    	}
	$arr[$i]['total'] = $noRows;
	return $arr;
    }
}


