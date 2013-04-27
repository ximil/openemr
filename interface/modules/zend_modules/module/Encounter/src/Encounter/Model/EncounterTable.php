<?php
namespace Encounter\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class EncounterTable extends AbstractTableGateway
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
    
    public function listIssue($issue)
    {
    	return sqlQuery("SELECT * FROM lists WHERE id = ?",array($issue));
    }
    
    public function saveEncounter($post)
    {	
    	global $userauthorized;
    	global $phpgacl_location;
    	global $pid;
    	global $encounter;
    	require_once ($phpgacl_location.'/../library/forms.inc');
    	require_once ($phpgacl_location.'/../library/encounter.inc');
    	
    	$visitCategory 		= $post['visitCategory'];
    	$facility		 	= $post['facility'];
    	$billingFacility 	= $post['billingFacility'];
    	$sensitivity 		= $post['sensitivity'];
    	$description 		= $post['description'];
    	$dtService			= $post['dtService'];
    	$dtOnset			= $post['dtOnset'];
    	$provider_id		= $post['provider'];
    	
    	if ($post['mode'] != 'Edit') {
    		$conn = $GLOBALS['adodb']['db'];
    		$encounter = $conn->GenID("sequences");
    		
    		//$provider_id = $userauthorized ? $_SESSION['authUserID'] : 0;
    		//$dt = date('Y-m-d');

    		$facilityresult = sqlQuery("select name FROM facility WHERE id =", array($facility));
    		$facilityName = $facilityresult['name'];
    		 
    		$sql = "INSERT INTO form_encounter
    		SET date = ?,
    		onset_date = ?,
    		reason = ?,
    		facility = ?,
    		pc_catid = ?,
    		facility_id = ?,
    		billing_facility = ?,
    		sensitivity = ?,
    		pid = ?,
    		encounter = ?,
    		provider_id = ?";
    		$lastId = sqlInsert($sql, array(
    				$dtService,
    				$dtOnset,
    				$description,
    				$facilityName,
    				$visitCategory,
    				$facility,
    				$billingFacility,
    				$sensitivity,
    				$pid,
    				$encounter,
    				$provider_id
    		));
    		addForm($encounter, "New Patient Encounter", $lastId, "newpatient", $pid, $userauthorized, $dtService);
    	} else if ($post['mode'] == 'Edit') {
    		$id = $post['id'];
    		$datepart = acl_check('encounters', 'date_a') ? "date = '$dtService', " : "";
    		$sql = "UPDATE form_encounter SET $datepart
	    					onset_date = ?, 
	    					reason = ?, 
	    					facility = ?, 
	    					pc_catid = ?, 
	    					facility_id = ?, 
	    					billing_facility = ?, 
	    					sensitivity = ?,
	    					provider_id = ? 
    					WHERE id = ?";
    		sqlStatement($sql, array(
    				$dtOnset,
    				$description,
    				$facilityName,
    				$visitCategory,
    				$facility,
    				$billingFacility,
    				$sensitivity,
    				$provider_id,
    				$id
    		));
   		}
   		
	    // Update the list of issues associated with this encounter.
	    sqlStatement("DELETE FROM issue_encounter WHERE pid = ? AND encounter = ?", array($pid, $encounter));
	    if (is_array($_POST['issues'])) {
	    	foreach ($_POST['issues'] as $issue) {
	    		$sql = "INSERT INTO issue_encounter SET	pid=?,
	    		list_id=?,
	    		encounter=?";
	    		sqlInsert($sql, array($pid, $issue, $encounter));
	    	}
	    }
  	
    	//$fh = fopen(dirname(__FILE__)."/text.txt","a");
    	//fwrite($fh,"data is :". $sql.print_r($post,1));
    	setencounter($encounter);
    	
    }
    
    public function listEncounter($encounter)
    {
    	$arr = array();
    	$sql = "SELECT * FROM form_encounter WHERE encounter=?";
    	$result = sqlStatement($sql, array($encounter));
    	while ($row = sqlFetchArray($result)) {
    		array_push($arr, $row);
    	}
    	 
    	return $arr;
    }
  
}


