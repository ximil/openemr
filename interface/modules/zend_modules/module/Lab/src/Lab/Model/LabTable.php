<?php
namespace Lab\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway; 
use Zend\Db\Adapter\Adapter; 
use Zend\Db\ResultSet\ResultSet; 
use Zend\Db\Sql\Select;

use Zend\Config;
use Zend\Config\Writer;
use Zend\Soap\Client;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class LabTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
     /**
     * Lab Order Row wise
     */
    
    public function listPatientLabOrders()
    {	global $pid;
	$sql = "SELECT po.date_ordered, po.procedure_order_id, po.ord_group  
			FROM procedure_order po 
			WHERE po.patient_id='$pid' ORDER BY po.ord_group DESC, po.procedure_order_id DESC" ;

	$result = sqlStatement($sql);
	$arr 	= array();
	$i 	= 0;
	while ($row = sqlFetchArray($result)) {
	    $arr[$i]['date_ordered'] 		= htmlspecialchars($row['date_ordered'],ENT_QUOTES);
	    $arr[$i]['procedure_order_id'] 	= htmlspecialchars($row['procedure_order_id'],ENT_QUOTES);
	    $arr[$i]['ord_group'] 		= htmlspecialchars($row['ord_group'],ENT_QUOTES);
	    $i++;
	}
	return $arr;
    }
    
    public function listLabOrders($data)
    {
	$sql = "SELECT po.*,
			poc.procedure_code,
			poc.procedure_name,
			poc.procedure_suffix,
			poc.diagnoses,
			poc.procedure_order_seq,
			poc.patient_instructions, 
			CONCAT(pd.lname, ',', pd.fname) AS patient_name,
			pp.name AS provider_name 
		    FROM procedure_order po 
		    LEFT JOIN patient_data pd ON po.patient_id=pd.id 
		    LEFT JOIN procedure_providers pp
			ON po.lab_id=pp.ppid
		    LEFT JOIN procedure_order_code poc
			ON poc.procedure_order_id=po.procedure_order_id  
		    WHERE po.ord_group=(SELECT ord_group FROM procedure_order WHERE procedure_order_id='" . $data['ordId'] . "')
		    ORDER BY po.procedure_order_id, poc.procedure_order_seq";
	
	$result = sqlStatement($sql);
	$arr 	= array();
	$i 	= 0;
	while ($row = sqlFetchArray($result)) {
	    $arr[$i]['procedure_order_id'] 	=  htmlspecialchars($row['procedure_order_id'],ENT_QUOTES);
	    $arr[$i]['provider_id'] 		=  htmlspecialchars($row['provider_id'],ENT_QUOTES);
	    $arr[$i]['lab_id'] 			=  htmlspecialchars($row['lab_id'],ENT_QUOTES);
	    $arr[$i]['date_ordered'] 		=  htmlspecialchars($row['date_ordered'],ENT_QUOTES);
	    $arr[$i]['date_collected'] 		=  htmlspecialchars($row['date_collected'],ENT_QUOTES);
	    $arr[$i]['internal_comments'] 	=  htmlspecialchars($row['internal_comments'],ENT_QUOTES);
	    $arr[$i]['order_priority']		=  htmlspecialchars($row['order_priority'],ENT_QUOTES);
	    $arr[$i]['order_status'] 		=  htmlspecialchars($row['order_status'],ENT_QUOTES);
	    $arr[$i]['patient_instructions'] 	=  htmlspecialchars($row['patient_instructions'],ENT_QUOTES);
	    $arr[$i]['diagnoses'] 		=  htmlspecialchars($row['diagnoses'],ENT_QUOTES);
	    $arr[$i]['procedure_name'] 		=  htmlspecialchars($row['procedure_name'],ENT_QUOTES);
	    $arr[$i]['procedure_code'] 		=  htmlspecialchars($row['procedure_code'],ENT_QUOTES);
	    $arr[$i]['procedure_suffix'] 	=  htmlspecialchars($row['procedure_suffix'],ENT_QUOTES);
	    $arr[$i]['procedure_order_seq'] 	=  htmlspecialchars($row['procedure_order_seq'],ENT_QUOTES);
	    $arr[$i]['psc_hold'] 		=  htmlspecialchars($row['psc_hold'],ENT_QUOTES);
	    $arr[$i]['order_status'] 		=  htmlspecialchars($row['order_status'],ENT_QUOTES);
	    $i++;
	}
	return $arr;
    }
    
    public function listLabOrderAOE($data)
    {
	$ordId 	= $data['ordId'];
	$seq 	= $data['seq'];
	$sql = "SELECT * FROM procedure_answers pa
	 		LEFT JOIN procedure_order_code poc
			    ON pa.procedure_order_id = poc.procedure_order_id
			    AND pa.procedure_order_seq = poc.procedure_order_seq
			LEFT JOIN procedure_questions pq
			    ON pa.question_code=pq.question_code
			    AND pq.procedure_code = poc.procedure_code 
			WHERE pa.procedure_order_id='$ordId' 
			AND pa.procedure_order_seq='$seq'";
	$result = sqlStatement($sql);
	$arr = array();
	$i = 0;
	while($tmp = sqlFetchArray($result)) {
	    $arr[] = htmlspecialchars($tmp['question_text'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['required'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['question_code'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['tips'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['answer'],ENT_QUOTES);
	}
	return $arr;
    }
    
    public function removeLabOrders($data)
    {	$ordId = $data['ordId'];
	$result = sqlStatement("SELECT procedure_order_id FROM procedure_order WHERE procedure_order_id=?", array($ordId));
	if (sqlNumRows($result) > 0) {
	    sqlStatement("DELETE FROM procedure_order WHERE procedure_order_id=?", array($ordId));
	}
	$result = sqlStatement("SELECT procedure_order_id FROM procedure_order_code WHERE procedure_order_id=?", array($ordId));
	if (sqlNumRows($result) > 0) {
	    sqlStatement("DELETE FROM procedure_order_code WHERE procedure_order_id=?", array($ordId));
	}
	$result = sqlStatement("SELECT procedure_order_id FROM procedure_answers WHERE procedure_order_id=?", array($ordId));
	if (sqlNumRows($result) > 0) {
	    sqlStatement("DELETE FROM procedure_answers WHERE procedure_order_id=?", array($ordId));
	}
    }
    
    // Start Save Lab Data
    public function updateProcedureMaster($post,$ordnum,$orderGroup){
	$labvalArr = explode("|",$post['lab_id'][$ordnum][0]);
	$labval = $labvalArr[0];
	sqlStatement("UPDATE procedure_order SET provider_id=?,patient_id=?,encounter_id=?,date_collected=?,date_ordered=?,order_priority=?,order_status=?,lab_id=?,psc_hold=?,billto=?,internal_comments=?,ord_group=? WHERE procedure_order_id=?",
		    array($post['provider'][$ordnum][0],$post['patient_id'],$post['encounter_id'],$post['timecollected'][$ordnum][0],$post['orderdate'][$ordnum][0],$post['priority'][$ordnum][0],
		    'pending',$labval,$post['specimencollected'][$ordnum][0],$post['billto'][$ordnum][0],$post['internal_comments'][$ordnum][0],$orderGroup,$post['procedure_order_id'][$ordnum][0]));
    }
    
    public function saveLab($post,$aoe)
    {	
	$papArray = array();
	$specimenState = array();
	$procedure_type_id_arr = array();
	$j=0;
	$prevState = '';
	if ($post['procedure_order_id'][0][0] != '') {
	    $max = sqlQuery("SELECT ord_group FROM procedure_order WHERE procedure_order_id=?", array($post['procedure_order_id'][0][0]));
	    $orderGroup = $max['ord_group'];
	} else {
	    $max = sqlQuery("SELECT (MAX( ord_group ) + 1) AS ord_group FROM procedure_order");
	    $orderGroup = $max['ord_group']; 
	}
	for($ordnum=0;$ordnum<$post['total_panel'];$ordnum++){
	    if ($post['procedure_order_id'][$ordnum][0] != ''){
		sqlStatement("DELETE FROM procedure_order_code WHERE procedure_order_id=?",array($post['procedure_order_id'][$ordnum][0]));
		sqlStatement("DELETE FROM procedure_answers WHERE procedure_order_id=?",array($post['procedure_order_id'][$ordnum][0]));
	    }
	    for($i=0;$i<sizeof($post['procedures'][$ordnum]);$i++){
		$PRow = sqlQuery("SELECT * FROM procedure_type WHERE procedure_code=? AND suffix=? ORDER BY pap_indicator,specimen_state",
				 array($post['procedure_code'][$ordnum][$i],$post['procedure_suffix'][$ordnum][$i]));
		if(!isset(${$PRow['specimen_state']."_".$ordnum."_j"}))
		${$PRow['specimen_state']."_".$ordnum."_j"} = 0;
		if($PRow['pap_indicator']=="P"){
		    $papArray[$ordnum][$post['procedure_code'][$ordnum][$i]."|-|".$post['procedure_suffix'][$ordnum][$i]]['procedure'] = $PRow['name'];
		    $papArray[$ordnum][$post['procedure_code'][$ordnum][$i]."|-|".$post['procedure_suffix'][$ordnum][$i]]['diagnoses'] = $post['diagnoses'][$ordnum][$i];
		    $papArray[$ordnum][$post['procedure_code'][$ordnum][$i]."|-|".$post['procedure_suffix'][$ordnum][$i]]['patient_instructions'] = $post['patient_instructions'][$ordnum][$i];
		}
		else{
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['procedure_code'] = $PRow['procedure_code'];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['procedure'] = $PRow['name'];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['procedure_suffix'] = $PRow['suffix'];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['diagnoses'] = $post['diagnoses'][$ordnum][$i];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['patient_instructions'] = $post['patient_instructions'][$ordnum][$i];
		    ${$PRow['specimen_state']."_".$ordnum."_j"}++;
		}
	    }
	    if(sizeof($papArray[$ordnum])>0){
		foreach($papArray[$ordnum] as $procode_suffix=>$pronameArr ){
		    $PSArray = explode("|-|",$procode_suffix);
		    $procode = $PSArray[0];
		    $prosuffix = $PSArray[1];
		    $proname = $pronameArr['procedure'];
		    $diagnoses = $pronameArr['diagnoses'];
		    $patient_instructions = $pronameArr['patient_instructions'];
		    if ($post['procedure_order_id'][$ordnum][0] != '') {
			$this->updateProcedureMaster($post,$ordnum,$orderGroup);
			$PAPprocedure_type_id = $post['procedure_order_id'][$ordnum][0];
		    } else {
			$PAPprocedure_type_id = $this->insertProcedureMaster($post,$ordnum,$orderGroup);
		    }
			$procedure_type_id_arr[] = $PAPprocedure_type_id;
			$PAPseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses,patient_instructions)
			    VALUES (?,?,?,?,?,?)",array($PAPprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses,$patient_instructions));
			$this->insertAoe($PAPprocedure_type_id,$PAPseq,$aoe,$procode,$ordnum);
		}
	    }
	    if($post['specimencollected'][$ordnum][0]=="onsite"){
		if(sizeof($specimenState[$ordnum])>0){
		    foreach($specimenState[$ordnum] as $k=>$vArray){
			if ($post['procedure_order_id'][$ordnum][0] != ''){
			    $this->updateProcedureMaster($post,$ordnum,$orderGroup);
			    $SPEprocedure_type_id = $post['procedure_order_id'][$ordnum][0];
			} else {
			    $SPEprocedure_type_id = $this->insertProcedureMaster($post,$ordnum,$orderGroup);
			}
			$procedure_type_id_arr[] = $SPEprocedure_type_id;
			for($i=0;$i<sizeof($vArray);$i++){
			    $procode = $vArray[$i]['procedure_code'];
			    $proname = $vArray[$i]['procedure'];
			    $prosuffix = $vArray[$i]['procedure_suffix'];
			    $diagnoses = $vArray[$i]['diagnoses'];
			    $patient_instructions = $vArray[$i]['patient_instructions'];
			    
			    $SPEseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses,patient_instructions)
					    VALUES (?,?,?,?,?,?)",array($SPEprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses,$patient_instructions));
			    $this->insertAoe($SPEprocedure_type_id,$SPEseq,$aoe,$procode,$ordnum);
			}
		    }
		}
	    }
	    else{
		for($i=0;$i<sizeof($post['procedures'][$ordnum]);$i++){
		    $procedure_code = $post['procedure_code'][$ordnum][$i];
		    $procedure_suffix = $post['procedure_suffix'][$ordnum][$i];
		    if(array_key_exists($procedure_code."|-|".$procedure_suffix,$papArray)) continue;
		    if($i==0){
			if ($post['procedure_order_id'][$ordnum][$i] != '') {
			    $this->updateProcedureMaster($post,$ordnum,$orderGroup);
			    $procedure_type_id = $post['procedure_order_id'][$ordnum][$i];
			} else {
			    $procedure_type_id = $this->insertProcedureMaster($post,$ordnum,$orderGroup);
			}
			$procedure_type_id_arr[] = $procedure_type_id;
		    }
		    $seq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses,patient_instructions)
			VALUES (?,?,?,?,?,?)",array($procedure_type_id,$post['procedure_code'][$ordnum][$i],$post['procedures'][$ordnum][$i],$post['procedure_suffix'][$ordnum][$i],$post['diagnoses'][$ordnum][$i],$post['patient_instructions'][$ordnum][$i]));
		    $this->insertAoe($procedure_type_id,$seq,$aoe,$post['procedure_code'][$ordnum][$i],$post['diagnoses'][$ordnum][$i],$ordnum);
		}
	    }
	}
	return $procedure_type_id_arr;
    }
    
     
    public function insertAoe($procedure_type_id,$seq,$aoe,$procedure_code_i,$ordnum){
	$fh = fopen(dirname(__FILE__)."/yyyy.txt","a");
	fwrite($fh,print_r($procedure_type_id,1)."\r\n".print_r($seq,1)."\r\n".print_r($aoe,1)."\r\n".print_r($procedure_code_i,1)."\r\n".print_r($ordnum,1));
	foreach($aoe[$ordnum] as $ProcedureOrder=>$QuestionArr){
	    if($ProcedureOrder==$procedure_code_i){
		foreach($QuestionArr as $Question=>$Answer){
		    sqlStatement("INSERT INTO procedure_answers (procedure_order_id,procedure_order_seq,question_code,answer) VALUES (?,?,?,?)",
		    array($procedure_type_id,$seq,$Question,$Answer));
		}
	    }
	}
    }
    
    public function insertProcedureMaster($post,$ordnum,$orderGroup){
	global $pid,$encounter;
	$labvalArr = explode("|",$post['lab_id'][$ordnum][0]);
	$labval = $labvalArr[0];
	
	$procedure_type_id = sqlInsert("INSERT INTO procedure_order (provider_id,patient_id,encounter_id,date_collected,date_ordered,order_priority,
				       order_status,lab_id,psc_hold,billto,internal_comments, ord_group) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)",
		    array($post['provider'][$ordnum][0],$post['patient_id'],$post['encounter_id'],$post['timecollected'][$ordnum][0],$post['orderdate'][$ordnum][0],$post['priority'][$ordnum][0],
		    'pending',$labval,$post['specimencollected'][$ordnum][0],
		    $post['billto'][$ordnum][0],$post['internal_comments'][$ordnum][0],$orderGroup));
	addForm($encounter, "Procedure Order", $procedure_type_id, "procedure_order", $pid, $userauthorized);
	return $procedure_type_id;
    }
    // End Save Lab Data
    
    public function saveLabOLD($post,$aoe)
    {
	$papArray = array();
	$specimenState = array();
	$procedure_type_id_arr = array();
	$j=0;
	$prevState = '';
	$max = sqlQuery("SELECT (MAX( ord_group ) + 1) AS ord_group FROM procedure_order");
	$orderGroup = $max['ord_group'];
	//$fh = fopen(dirname(__FILE__)."/teeeewwwt.txt","a");
	//fwrite($fh,"rrr:".print_r($post,1));
	for($ordnum=0;$ordnum<$post['total_panel'];$ordnum++){
	    for($i=0;$i<sizeof($post['procedures'][$ordnum]);$i++){
		$PRow = sqlQuery("SELECT * FROM procedure_type WHERE procedure_code=? AND suffix=? ORDER BY pap_indicator,specimen_state",
				 array($post['procedure_code'][$ordnum][$i],$post['procedure_suffix'][$ordnum][$i]));
		if(!isset(${$PRow['specimen_state']."_".$ordnum."_j"}))
		${$PRow['specimen_state']."_".$ordnum."_j"} = 0;
		if($PRow['pap_indicator']=="P"){
		    $papArray[$ordnum][$post['procedure_code'][$ordnum][$i]."|-|".$post['procedure_suffix'][$ordnum][$i]]['procedure'] = $PRow['name'];
		    $papArray[$ordnum][$post['procedure_code'][$ordnum][$i]."|-|".$post['procedure_suffix'][$ordnum][$i]]['diagnoses'] = $post['diagnoses'][$ordnum][$i];
		    $papArray[$ordnum][$post['procedure_code'][$ordnum][$i]."|-|".$post['procedure_suffix'][$ordnum][$i]]['patient_instructions'] = $post['patient_instructions'][$ordnum][$i];
		}
		else{
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['procedure_code'] = $PRow['procedure_code'];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['procedure'] = $PRow['name'];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['procedure_suffix'] = $PRow['suffix'];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['diagnoses'] = $post['diagnoses'][$ordnum][$i];
		    $specimenState[$ordnum][$PRow['specimen_state']][${$PRow['specimen_state']."_".$ordnum."_j"}]['patient_instructions'] = $post['patient_instructions'][$ordnum][$i];
		    ${$PRow['specimen_state']."_".$ordnum."_j"}++;
		}
	    }
	    //$fh = fopen(dirname(__FILE__)."/teeeet.txt","a");
	    //fwrite($fh,"eee:".${$PRow['specimen_state']."_".$ordnum."_j"});
	    //fwrite($fh,"\r\nPRow:".print_r($PRow,1));
	    //fwrite($fh,"papArray:".print_r($papArray,1));
	    //fwrite($fh,"specimenState:".print_r($specimenState,1));
	    if(sizeof($papArray[$ordnum])>0){
		foreach($papArray[$ordnum] as $procode_suffix=>$pronameArr ){
		    $PSArray = explode("|-|",$procode_suffix);
		    $procode = $PSArray[0];
		    $prosuffix = $PSArray[1];
		    $proname = $pronameArr['procedure'];
		    $diagnoses = $pronameArr['diagnoses'];
		    $patient_instructions = $pronameArr['patient_instructions'];
		    $PAPprocedure_type_id = $this->insertProcedureMaster($post,$ordnum,$orderGroup);
		    $procedure_type_id_arr[] = $PAPprocedure_type_id;
		    $PAPseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses,patient_instructions)
			 VALUES (?,?,?,?,?,?)",array($PAPprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses,$patient_instructions));
		    $this->insertAoe($PAPprocedure_type_id,$PAPseq,$aoe,$procode,$ordnum);
		}
	    }
	    if($post['specimencollected'][$ordnum][0]=="onsite"){
		//$fh = fopen(dirname(__FILE__)."/tessst.txt","a");
		//fwrite($fh,print_r($specimenState[$ordnum],1));
		if(sizeof($specimenState[$ordnum])>0){
		    foreach($specimenState[$ordnum] as $k=>$vArray){
			$SPEprocedure_type_id = $this->insertProcedureMaster($post,$ordnum,$orderGroup);
			$procedure_type_id_arr[] = $SPEprocedure_type_id;
			for($i=0;$i<sizeof($vArray);$i++){
			    $procode = $vArray[$i]['procedure_code'];
			    $proname = $vArray[$i]['procedure'];
			    $prosuffix = $vArray[$i]['procedure_suffix'];
			    $diagnoses = $vArray[$i]['diagnoses'];
			    $patient_instructions = $vArray[$i]['patient_instructions'];
			    $sss ="INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses,patient_instructions)
					    VALUES ($SPEprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses,$patient_instructions)";
			    $SPEseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses,patient_instructions)
					    VALUES (?,?,?,?,?,?)",array($SPEprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses,$patient_instructions));
			    $this->insertAoe($SPEprocedure_type_id,$SPEseq,$aoe,$procode,$ordnum);
			}
		    }
		}
	    }
	    else{
		for($i=0;$i<sizeof($post['procedures'][$ordnum]);$i++){
		    $procedure_code = $post['procedure_code'][$ordnum][$i];
		    $procedure_suffix = $post['procedure_suffix'][$ordnum][$i];
		    if(array_key_exists($procedure_code."|-|".$procedure_suffix,$papArray)) continue;
		    if($i==0){
			$procedure_type_id = $this->insertProcedureMaster($post,$ordnum,$orderGroup);
			$procedure_type_id_arr[] = $procedure_type_id;
		    }
		    $seq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses,patient_instructions)
			VALUES (?,?,?,?,?,?)",array($procedure_type_id,$post['procedure_code'][$ordnum][$i],$post['procedures'][$ordnum][$i],$post['procedure_suffix'][$ordnum][$i]
						    ,$post['patient_instructions'][$ordnum][$i]));
		    $this->insertAoe($procedure_type_id,$seq,$aoe,$post['procedure_code'][$ordnum][$i],$post['diagnoses'][$ordnum][$i],$ordnum);
		}
	    }
	}
	return $procedure_type_id_arr;
    }

    public function listProcedures($inputString,$labId)
    { 
	$sql = "SELECT * FROM procedure_type AS pt WHERE pt.lab_id=? AND pt.name LIKE ? OR pt.procedure_code LIKE ? AND pt.activity=1 AND pt.procedure_type='ord'";
	$result = sqlStatement($sql,array($labId,$inputString."%",$inputString."%"));
	$arr = array();
	$i = 0;
	while($tmp = sqlFetchArray($result)) {
	    $arr[] =  htmlspecialchars($tmp['procedure_type_id'],ENT_QUOTES). '|-|' . htmlspecialchars($tmp['procedure_code'],ENT_QUOTES). '|-|' . htmlspecialchars($tmp['suffix'],ENT_QUOTES).'|-|'.htmlspecialchars($tmp['name'],ENT_QUOTES);
	}
	
	return $arr;
    }
    
    public function listAOE($procedureCode,$labId){
	$sql = "SELECT * FROM procedure_questions WHERE lab_id=? AND procedure_code=? AND activity=1 ORDER BY seq ASC";
	$result = sqlStatement($sql,array($labId,$procedureCode));
	$arr = array();
	$i = 0;
	//$inputFilter = new InputFilter();
	//$factory     = new InputFactory();

    //$form->setInputFilter($inputFilter);
	while($tmp = sqlFetchArray($result)) {
	//    $inputFilter->add($factory->createInput(array(
	//	'name'     => 'AOE_'.$procedureCode."_".$tmp['question_code']
	//    )));
	    $arr[] = htmlspecialchars($tmp['question_text'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['required'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['question_code'],ENT_QUOTES)."|-|".htmlspecialchars($tmp['tips'],ENT_QUOTES);
	}
	return $arr;
    }
    
    public function listDiagnoses($inputString)
    {
	
	$codeTypeValue 	=  sqlQuery("SELECT ct_id FROM code_types WHERE ct_key='ICD9'");

	// Search code type ICD9
	
	    $sql = "SELECT ref.formatted_dx_code as code, 
			    ref.long_desc as code_text 
			FROM `icd9_dx_code` as ref 
			LEFT OUTER JOIN `codes` as c 
				ON ref.formatted_dx_code = c.code 
				AND c.code_type = ? 
				WHERE (ref.long_desc LIKE ? OR ref.formatted_dx_code LIKE ?) 
				AND ref.active = '1'
				AND (c.active = 1 || c.active IS NULL) 
				ORDER BY ref.formatted_dx_code+0, ref.formatted_dx_code";
	    $result = sqlStatement($sql,array($codeTypeValue['ct_id'], "%" . $inputString . "%", "%" . $inputString . "%"));
	
	//$sql = "SELECT * FROM codes
	//		    WHERE code_type='2' 
	//			AND (code LIKE ? 
	//			    OR   code_text LIKE ?) ORDER BY code ";
	//$result = sqlStatement($sql,array($inputString . "%", $inputString . "%"));
	$arr = array();
	$i = 0;
	while($tmp = sqlFetchArray($result)) {
	    $arr[] =  htmlspecialchars($tmp['code'],ENT_QUOTES). '|-|' . htmlspecialchars($tmp['code_text'],ENT_QUOTES);
	}
	return $arr;
    }
	
    /**
    * Vipin
    **/
    
    public function getColumns($result)
    {
	$result_columns	= array();
	foreach($result as $res)
	{			
	    foreach($res as $key => $val)
	    {
		if(is_numeric($key))
		    continue;
		$result_columns[] = $key;							
	    }
	    break;
	}
	return $result_columns;
    }

    public function columnMapping($column_map,$result_col)
    {
	$table_sql	= array();
	
	foreach($result_col as $col)
	{
	    if(($column_map[$col]['colconfig']['table'] <> "")&&($column_map[$col]['colconfig']['column'] <> ""))
	    {
		$table 		= $column_map[$col]['colconfig']['table'];
		$column 	= $column_map[$col]['colconfig']['column'];
		
		$table_sql[$table][$col]	= $column;				
	    }	    
	}
	return $table_sql;		
    }
   
    public function importDataCheck($result,$column_map)//CHECK DATA IF ALREADY EXISTS
    {
	
	$result_col	= $this->getColumns($result);
	
	$mapped_tables	= $this->columnMapping($column_map,$result_col);
	
	foreach($result as $res)
	{
	    foreach($result_col as $col)
	    {
                ${$col}	= $res[$col];//GETTING IMPORTED VALUES
	    }
	   
	    foreach($mapped_tables as $table => $columns)
	    {
		$value_arr	= array();
		foreach($columns as $servercol => $column)
		{
		    if($column_map[$servercol]['colconfig']['insert_id'] == "1")
		    {
			$$servercol = $insert_id;
		    }
		    if($column_map[$servercol]['colconfig']['value_map'] == "1")
		    {
			$$servercol = $column_map[$servercol]['valconfig'][$$servercol];
		    }
		    $value_arr[$column] =  ${$servercol};                    
		}
	
		$fields	        = implode(",",$columns);
		$col_count	= count($columns);
		$field_vars	= "$".implode(",$",$columns);
		$params	        = rtrim(str_repeat("?,",$col_count),",");
		
		$primary_key_arr = $column_map['contraints'][$table]['primary_key'];
		if(count($primary_key_arr) > 0)
		{
		    $index      	= 0;
		    $condition  	= "";
		    $check_value_arr    = array();
		    
		    foreach($primary_key_arr as $pkey)
		    {
			if($index > 0)
			{
			    $condition.=" AND ";
			}
			$condition.=" ".$pkey." = ? ";
			$index++;
			$check_value_arr[$pkey] = $value_arr[$pkey];
		    }
		    
		    $update_arr = array();
		    foreach($value_arr as $key => $val)
		    {
			if(! in_array($key,$primary_key_arr))
			{                            
			    $update_arr[$key] = $val;
			}
		    }
		    
		    $update_combined_arr    = array_merge($update_arr,$check_value_arr);
		    
		    $index      	= 0;
		    $update_key_arr    	= array();
		    
		    foreach($update_arr as $upkey => $upval)
		    {
			$update_key_arr[]   = $upkey;
		    }
		    
		    $update_expr    = implode(" = ? ,",$update_key_arr);
		    $update_expr   .= " = ? ";
		    
		    $sql_check      = "SELECT COUNT(*) as data_exists FROM ".$table." WHERE ".$condition;
		    $pat_data_check = sqlQuery($sql_check,$check_value_arr);
		    
		    if($pat_data_check['data_exists'])
		    {
			$sqlup	        = "UPDATE ".$table." SET ".$update_expr." WHERE ".$condition;
			$pat_data_check = sqlQuery($sqlup,$update_combined_arr);			
		    }
		    else
		    {
			$sql	    = "INSERT INTO ".$table."(".$fields.") VALUES (".$params.")";
			$insert_id  = sqlInsert($sql,$value_arr);                        
		    }
		}		
	    }
	    
	    $count++;	    
	}
	sqlStatement("UPDATE procedure_type SET parent=procedure_type_id");
	sqlStatement("UPDATE procedure_type SET name=description");
    }

    public function getWebserviceOptions()
    {
	$options    = array('location' => "http://192.168.1.139/webserver/lab_server.php",
			    'uri'      => "urn://zhhealthcare/lab"
			    );
	return $options;
    }
    
    public function pullcompendiumTestConfig()
    {
	$column_map['test_lab_id']	                = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "lab_id",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_lab_entity'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "seccol",
									    'value_map' => "0",
									    'insert_id' => "1"));
	$column_map['test_code'] 		        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "procedure_code",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_specimen_state'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "specimen_state",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_unit_code'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_status_indicator'] 		= array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "activity",
									    'value_map' => "1",
									    'insert_id' => "0"),
								'valconfig' => array(
									    'A'         => "1",
									    'I'         => "0"));
	
	$column_map['test_insert_datetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_description'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "description",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_specimen_type'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_service_code'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_lab_site'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_update_datetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_update_user'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_code_suffix'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "suffix",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_profile'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_select'] 			= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_performing_site'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_flag'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_not_billed'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_is_billed_only'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_reflex_count'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_conforming_indicator']    	= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_alternate_temp'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_pap_indicator'] 	        = array('colconfig' => array(
									    'table'     => "procedure_type",
									    'column'    => "pap_indicator",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['test_last_updatetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	
	
	$column_map['contraints']   = array('procedure_type' => array(
									'primary_key' => array(
												'0'     => "lab_id",
												'1'     => "procedure_code",
												'2'	=> "suffix"))); 
	
	return $column_map;
    }
    
    public function pullcompendiumAoeConfig()
    {
	$column_map['aoe_lab_id']	                = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "lab_id",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_lab_entity'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_performing_site'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_unit_code'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_test_code'] 		        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "procedure_code",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_analyte_code'] 	        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "question_code",
									    'value_map' => "0",
									    'insert_id' => "0"));            
	
	$column_map['aoe_question'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	$column_map['aoe_status_indicator'] 	        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "activity",
									    'value_map' => "1",
									    'insert_id' => "0"),
                                                                'valconfig' => array(
									    'A'         => "1",
									    'I'         => "0"));
        
	$column_map['aoe_profile_component'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_insert_datetime'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_question_description'] 	= array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "question_text",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_suffix'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_result_filter'] 	        = array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "tips",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_test_code_mneumonic'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_test_flag'] 		        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	
	$column_map['aoe_upadate_datetime'] 		= array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_update_user'] 	                = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_component_name'] 		= array('colconfig' => array(
									    'table'     => "procedure_questions",
									    'column'    => "question_component",
									    'value_map' => "0",
									    'insert_id' => "0"));
        
	$column_map['aoe_last_updatetime'] 	        = array('colconfig' => array(
									    'table'     => "",
									    'column'    => "",
									    'value_map' => "0",
									    'insert_id' => "0"));
	
	
	$column_map['contraints']   = array('procedure_questions' => array(
									'primary_key' => array(
												'0'     => "lab_id",
												'1'     => "procedure_code",
												'2'     => "question_code"))); 
	
	return $column_map;
    }
    
    public function mapcolumntoxml()
    {
	$xmlconfig['patient_data']          = array(                      
						'column_map'    => array(
									'pid'           => 'pid',
									'fname'         => 'patient_fname',
									'DOB'           => 'patient_dob',
									'sex'           => 'patient_sex',
									'lname'         => 'patient_lname',
									'street'        => 'patient_street_address',
									'city'          => 'patient_city',
									'state'         => 'patient_state',
									'postal_code'   => 'patient_postal_code',
									'country_code'  => 'patient_country',
									'phone_contact' => 'patient_phone_no',
									'ss'            => 'patient_ss_no' 
									),
						'primary_key'   => array('pid'),
						'match_value'   => array('pid'));
	
	
	$xmlconfig['insurance_data']        = array(
						'column_map'    => array(
									'type'                      => '#type',
									'provider'                  => '#provider',
									'subscriber_street'         => 'guarantor_address',
									'subscriber_city'           => 'guarantor_city',
									'subscriber_state'          => 'guarantor_state',
									'subscriber_postal_code'    => 'guarantor_postal_code',
									'subscriber_lname'          => '$type_insurance_person_lname,guarantor_lname',
									'subscriber_fname'          => '$type_insurance_person_fname,guarantor_fname',
									'subscriber_relationship'   => '$type_insurance_person_relationship',
									'policy_number'             => '$type_insurance_policy_no',
														       
									'group_number'              => '$type_insurance_group_no',
									'subscriber_mname'          => '$type_insurance_person_mname,guarantor_mname',
									
									),
						'primary_key'           => array('pid'),
						'match_value'           => array('pid'),
						'child_table'           => 'insurance_companies,addresses',
                                                'tag_value_condition'   => array(
                                                                                'guarantor_fname' => array(
                                                                                                           'variable'   => "type",
                                                                                                           'value'      => "primary"),
                                                                                'guarantor_mname' => array(
                                                                                                           'variable'   => "type",
                                                                                                           'value'      => "primary"),
                                                                                'guarantor_lname' => array(
                                                                                                           'variable'   => "type",
                                                                                                           'value'      => "primary"),
                                                                                'guarantor_address' => array(
                                                                                                           'variable'   => "type",
                                                                                                           'value'      => "primary"),
                                                                                'guarantor_city' => array(
                                                                                                           'variable'   => "type",
                                                                                                           'value'      => "primary"),
                                                                                'guarantor_state' => array(
                                                                                                           'variable'   => "type",
                                                                                                           'value'      => "primary"),
                                                                                'guarantor_postal_code' => array(
                                                                                                           'variable'   => "type",
                                                                                                           'value'      => "primary")
                                                                                ));
	
	$xmlconfig['insurance_companies']   = array(
						'column_map'    => array(
									'name'  => '$type_insurance_name'                                                
									),
						'primary_key'   => array('id'),
						'match_value'   => array('provider'),
						'parent_table'  => 'insurance_data'
						);
	
	/*-------- NEW CONFIGURATION FOR ADDRESSES ---------*/
	//line1,    city,  state,  zip
	
	$xmlconfig['addresses']   	    = array(
						'column_map'    => array(
									'line1'  	=> '$type_insurance_address',
									'city'  	=> '$type_insurance_city',
									'state'  	=> '$type_insurance_state',
									'zip'  		=> '$type_insurance_postal_code',   
									),
						'primary_key'   => array('foreign_id'),
						'match_value'   => array('provider'),
						'parent_table'  => 'insurance_data'
						);
	/*--------------------------------------------------*/
       
        $xmlconfig['procedure_providers']   = array(                      
						'column_map'    => array(
									'send_app_id'       => 'send_app_id',
                                                                        'recv_app_id'       => 'recv_app_id',    
									'send_fac_id'       => 'send_fac_id',
									'recv_fac_id'       => 'recv_fac_id',
									'DorP'              => 'DorP'
									),
						'primary_key'   => array('ppid'),
						'match_value'   => array('lab_id'));
        
        
        $xmlconfig['procedure_order']       = array(                      
						'column_map'    => array(
                                                                        'provider_id'           => '#provider_id',
                                                                        'psc_hold'         	=> 'recv_app_id',
									'billto'	    	=> 'bill_to',
									'internal_comments'	=> 'patient_internal_comments'
									),
                                                'value_map'     => array(
                                                                        'psc_hold'              => array(
                                                                                                        'onsite'    => '',
                                                                                                        'labsite'   => 'PSC'
                                                                                                    )
                                                                        ),
						'primary_key'   => array('procedure_order_id'),
						'match_value'   => array('order_id'));
        
        $xmlconfig['users']                 = array(                      
						'column_map'    => array(
                                                                        'fname'                 => 'ordering_provider_fname',
                                                                        'lname'         	=> 'ordering_provider_lname',
									'npi'			=> 'ordering_provider_id',
									),
                                                'primary_key'   => array('id'),
						'match_value'   => array('provider_id'));
        
        
	return $xmlconfig;
    }

    public function mapResultXmlToColumn()
    {			
	$xmlconfig['procedure_report'] 	    = array(                      
						'xmltag_map'    => array(
									'$procedure_order_id'   => 'procedure_order_id',
									'$procedure_order_seq'	=> 'procedure_order_seq',
									'date_report'           => 'patient_dob',
									'date_collected'       	=> 'patient_sex',
									'specimen_num'        	=> 'patient_lname',
									'report_status'        	=> 'report_status',
									'$review_status'      	=> 'review_status'
									 ),
						'primary_key'   => array('procedure_report_id'),
						'match_value'   => array('procedure_report_id'),
						'child_table'   => 'procedure_result');
	
	
	$xmlconfig['procedure_result']	    = array(
						'xmltag_map'    => array(
									'$procedure_report_id'  => 'procedure_report_id',
									'abnormal'              => 'abnormal',
									'result'         	=> 'result',
									'range'           	=> 'range',
									'units'        		=> 'units',
									'facility'    		=> 'facility',
									'comments'          	=> 'comments',
									'$result_status'         => 'result_status'							
									),
						'primary_key'   => array('procedure_result_id'),
						'match_value'   => array('procedure_result_id'),
						'parent_table'   => 'procedure_report');
               
	return $xmlconfig;
    }
    
    public function generateSQLSelect($pid,$lab_id,$order_id,$cofig_arr,$table)
    {
	global $type;
	global $provider;
        global $provider_id;
        
     
	$table_name         = $table;
	    
	$col_map_arr        = $cofig_arr[$table]['column_map'];       
	$primary_key_arr    = $cofig_arr[$table]['primary_key'];        
	$match_value_arr    = $cofig_arr[$table]['match_value'];
	
	$index      = 0;
	$condition  = "";
	foreach($primary_key_arr as $pkey)
	{
	    if($index > 0)
	    {
		$condition .= " AND ";
	    }
	    $condition .= " ".$pkey." = ? ";
	    $index++;
	}
	
	$index  = 0;
	foreach($match_value_arr as $param)
	{
	    $match_value_arr[$index] = ${$match_value_arr[$index]};
	    $index++;
	}
	
	$col_arr        = array();
	foreach($col_map_arr as $col => $tag)
	{
	    $col_arr[]  = $col;
	}
	$cols   = implode(",",$col_arr);
	
	$sql    = "SELECT ".$cols." FROM ".$table_name." WHERE ".$condition;
	$res    = sqlStatement($sql,$match_value_arr);	
	return $res;    
    }
    
    public function generateOrderXml($pid,$lab_id,$xmlfile)
    {
	global $type;
	global $provider;
        global $provider_id;
	
	//XML TAGS NOT CONFIGURED YET
	$primary_insurance_coverage_type        = "";
	$secondary_insurance_coverage_type      = "";       
	$primary_insurance_person_address       = "";    
	$primary_insurance_person_city          = "";    
	$primary_insurance_person_state         = "";    
	$primary_insurance_person_postal_code   = "";    
	
	$guarantor_phone_no                     = "";    
	
        $observation_request_comments           = "";
	
	$xmltag_arr = array("pid","patient_fname","patient_dob","patient_sex","patient_lname","patient_street_address","patient_city",
			    "patient_state","patient_postal_code","patient_country","patient_phone_no","patient_ss_no","patient_internal_comments",
			    "primary_insurance_name","primary_insurance_address","primary_insurance_city","primary_insurance_state",
			    "primary_insurance_postal_code","primary_insurance_person_lname","primary_insurance_person_fname",
			    "primary_insurance_person_relationship","primary_insurance_policy_no","primary_insurance_coverage_type",
			    "secondary_insurance_name","secondary_insurance_address","secondary_insurance_city","secondary_insurance_state",
			    "secondary_insurance_postal_code","secondary_insurance_group_no","secondary_insurance_person_lname",
			    "secondary_insurance_person_fname","secondary_insurance_person_relationship","secondary_insurance_policy_no",
			    "secondary_insurance_coverage_type", "primary_insurance_person_address","primary_insurance_person_city",
			    "primary_insurance_person_state","primary_insurance_person_postal_code","guarantor_lname","guarantor_fname",
			    "guarantor_address","guarantor_city","guarantor_state","guarantor_postal_code","guarantor_phone_no",
			    "secondary_insurance_person_mname","guarantor_mname","ordering_provider_id","ordering_provider_lname",
			    "ordering_provider_fname","send_app_id","recv_app_id","send_fac_id","recv_fac_id","DorP","bill_to");
     
	
	$cofig_arr  = $this->mapcolumntoxml();
       
	
	//GETTING VALUES OF ADDITIONAL XML TAGS
	$sql_order   	= "SELECT procedure_order_id FROM procedure_order WHERE patient_id = ? AND order_status = ? AND lab_id = ? ";
	    
	$misc_value_arr = array();
	
	$order_value_arr['patient_id']   = $pid;
	$order_value_arr['order_status'] = "pending";//hard coded//
	$order_value_arr['lab_id']       = $lab_id;
	
	$res_order   = sqlStatement($sql_order,$order_value_arr);
	
	$test_count = 0;
	$diag_count = 0;       
	
	$return_arr = array();
	$i=0;
	
	
	
	while($data_order = sqlFetchArray($res_order))
	{
	    $diagnosis  = "";
	    $test_id    = "";           
            $test_aoe   = "";
	    
	    $result_xml = "";
	    
            /* ------------------------------------GENERATING XML FROM CONFIGURATION FOR EACH ORDER----------------------------------------*/
            $sl = 0;
            foreach($cofig_arr as $table => $config)
            {
              
                //SKIP THE DATA FETCHING OF CHILD TABLE ROWS , WHICH WILL AUTOMATICALLY FETCH IF IT IS CONFIGURED, IT HAS PARENT TABLE
                if($config['parent_table'] <> "")
                {
                    continue;
                }            
                $col_map_arr    = $cofig_arr[$table]['column_map'];
		$res            = $this->generateSQLSelect($pid,$lab_id,$data_order['procedure_order_id'],$cofig_arr,$table);
                
                while($data = sqlFetchArray($res))
                {
                    $global_arr  = array();                
                    $check_arr   = array();

                    foreach($col_map_arr as $col => $tagstr)
                    {                        
                        //CHECKING FOR MAULTIPLE TAG MAPPING
                        $tag_arr   = explode(",",$tagstr);
                        
                        foreach($tag_arr as $tag)
                        {
                            if(trim($tag) <> "")
                            {                        
                                if(substr($tag,0,1)== "#")
                                {
                                    $tag            = substr($tag,1,strlen($tag));
                                    $check_arr[]    = "$".$tag;
                                }
                                foreach($check_arr as $check)
                                {
                                    if(strstr($tag,$check))
                                    {
                                        $tag = str_replace($check,${ltrim($check,"$")},$tag);                               
                                    }
                                }
                                if($cofig_arr[$table]['value_map'][$col] <> "")
                                {
                                    $$tag   = $cofig_arr[$table]['value_map'][$col][$data[$col]];
                                }
                                else
                                {
                                    if($cofig_arr[$table]['tag_value_condition'][$tag]['variable'] <> "")
                                    {
                                        if(${$cofig_arr[$table]['tag_value_condition'][$tag]['variable']} == $cofig_arr[$table]['tag_value_condition'][$tag]['value'])
                                        {
                                            $$tag   = $data[$col];
                                        }
                                    }
                                    else
                                    {
                                        $$tag   = $data[$col];
                                    }
                                }
                            }
                        }
                    }
		        
                    if($config['child_table'] <> "")
                    {
			$child_table_arr	= explode(",",$config['child_table']);
			
			foreach($child_table_arr as $child_table)
			{
			    if(trim($child_table) <> "")
                            {		
				$res2           = $this->generateSQLSelect($pid,$lab_id,$data_order['procedure_order_id'],$cofig_arr,$child_table);
				$fetch2_count   = 0; 
				while($data1 = sqlFetchArray($res2))
				{
				    $col_map_arr2   = $cofig_arr[$child_table]['column_map'];
				    
				    foreach($col_map_arr2 as $col => $tagstr)
				    {
					//CHECKING FOR MAULTIPLE TAG MAPPING
					$tag_arr   = explode(",",$tagstr);
					
					foreach($tag_arr as $tag)
					{
					    if(trim($tag) <> "")
					    {
						foreach($check_arr as $check)
						{
							if(strstr($tag,$check))
							{
								$tag = str_replace($check,${ltrim($check,"$")},$tag);                                       
							}
						}
						
						if(substr($tag,0,1)== "#")
						{
							$tag = substr($tag,1,strlen($tag));                        
						}
						if($cofig_arr[$table]['value_map'][$col] <> "")
						{
						    $$tag   = $cofig_arr[$table]['value_map'][$col][$data1[$col]];
						}
						else
						{
						    $$tag   = $data1[$col];
						}
					    }
					}
				    }
				}
			    }
			}
                    }
                }
            }
	    /*-----------------------------------------------------------------------------------------------------------------------------*/
            
	    $xmlfile = ($xmlfile <> "") ? $xmlfile : "order_new_".gmdate('YmdHis')."_".$data_order['procedure_order_id'].".xml";
	    $result_xml	= '<?xml version="1.0" encoding="UTF-8"?><Order>';
	    
	    foreach($xmltag_arr as $tag)
	    {
		$tag_val = (trim(${$tag}) <> "") ? ${$tag} : "";
		$config->Order->$tag    = $tag_val;
		$result_xml.= '<'.$tag.'>'.$tag_val.'</'.$tag.'>';
		
	    }   
	    
	    /* ------------------ GETTING TEST DETAILS ------------------------*/
	    
	    $sql_test   = "SELECT procedure_code, procedure_suffix, procedure_order_seq, diagnoses, patient_instructions FROM procedure_order_code
                                WHERE procedure_order_id = ? ";
	    
	    $test_value_arr = array();	    
	    $test_value_arr['procedure_order_id']   = $data_order['procedure_order_id'];
	   
	    $res_test  	= sqlStatement($sql_test,$test_value_arr);
	    while($data_test = sqlFetchArray($res_test))
            {		
		/*-------------------- 	GETTING PATIENT INSTRUCTIONS ---------------*/
		$patient_instructions	= $data_test['patient_instructions'];
		
		if(($data_test['procedure_code'] <> "") && ($data_test['procedure_suffix'] <> ""))
		{
		    $test_id   .= $data_test['procedure_code']."#!#".$data_test['procedure_suffix']."#--#";
		}
		
		/*------------------- GETTING DIAGNOSES DETAILS -------------------*/
		if($data_test['diagnoses'] <> "")
		{
		    $diag_arr    =  explode(";",$data_test['diagnoses']);
		    
		    foreach($diag_arr as $diag)
		    {
			if(strpos($diag,":"))
			{
			    $diag_array     =  explode(":",$diag,2);
			    $diag_str	= $diag_array[1];
			}
			else
			{
			    $diag_str	= $diag;
			}
			
			$diagnosis     .= $diag_str;
			$diagnosis     .= "#@#";
		    }
		    $diagnosis  = rtrim($diagnosis,"#@#");
		    $diag_count++;		    
		}
		$diagnosis.="#~@~#";
	    
		/*------------------- GETTING AOE DETAILS -----------------*/
		$sql_aoe        = "SELECT question_code,answer_seq,answer FROM procedure_answers
				    WHERE procedure_order_id = ? AND procedure_order_seq = ? ";
		$aoe_value_arr  = array();
	    
		$aoe_value_arr['procedure_order_id']    = $data_order['procedure_order_id'];
		$aoe_value_arr['procedure_order_seq']   = $data_test['procedure_order_seq'];
			   
		$res_aoe        = sqlStatement($sql_aoe,$aoe_value_arr);
		$aoe_count	= 0; 
		while($data_aoe = sqlFetchArray($res_aoe))
		{		    
		    if($data_aoe['question_code'] <> "")
		    {
			$aoe_count++;
			if($aoe_count > 1)
			{
			    $test_aoe   .= "!#@#!";	
			}
			$test_aoe   .= $data_aoe['question_code']."!@!".$data_aoe['answer'];					  
		    }		    
		}
		$test_aoe   .= "!-#@#-!";
            }	 
	    
	    
	    
	    /*----------------------------  ASSIGNING ADDITIONAL TAG ELEMENTS FOR ORDER----------------------------------*/
	    $result_xml     .= '<observation_request_comments>'.$patient_instructions.'</observation_request_comments>';
	    $result_xml     .= '<test_id>'.$test_id.'</test_id>';
	    $result_xml     .= '<test_diagnosis>'.$diagnosis.'</test_diagnosis>';
	    $result_xml     .= '<test_aoe>'.$test_aoe.'</test_aoe>';
	    
	    $result_xml     .= '</Order>';
	    
	    $return_arr[]   = array (
				     'order_id'     => $data_order['procedure_order_id'],
				     'xmlstring'    => $result_xml
				    );
	}
	return $return_arr;        
    }
    
    public function getClientCredentials($proc_order_id)
    {
	$sql_proc       = "SELECT lab_id FROM procedure_order WHERE procedure_order_id = ? ";
	$proc_value_arr = array();
	$proc_value_arr['procedure_order_id']   = $proc_order_id;
	$res_proc   = sqlQuery($sql_proc,$proc_value_arr);
	$sql_cred   = "SELECT  login, password, remote_host FROM procedure_providers WHERE ppid = ? ";
	$res_cred   = sqlQuery($sql_cred,$res_proc);
	return $res_cred;        
    }
    
    public function setOrderStatus($proc_order_id,$status)
    {
	$sql_status         = "UPDATE procedure_order SET order_status = ? WHERE procedure_order_id = ? ";        
	$status_value_arr   = array();
        
        $status_value_arr['status']             = $status;
        $status_value_arr['procedure_order_id'] = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status;        
    }
}
?>

