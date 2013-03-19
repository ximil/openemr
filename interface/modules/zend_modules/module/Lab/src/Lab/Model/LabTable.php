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
    
    public function listLabOrders()
    {
	$sql = "SELECT procedure_order_id,
			provider_id,
			patient_id,
			encounter_id,
			date_ordered
		    FROM procedure_order";
	$result = sqlStatement($sql);
	$arr = array();
	while ($row = sqlFetchArray($result)) {
	    $arr[] = $row;
	}
	//echo '<pre>'; print_r($arr); echo '</pre>';
	return $arr;
    }
    
  
	
//   public function saveLab(Lab $lab,$aoe)
//    {
//	$fh = fopen("D:/SAVELAB.txt","a");
//	fwrite($fh,print_r($lab->procedures,1));
//	fwrite($fh,print_r($lab->procedure_code,1));
//	fwrite($fh,print_r($aoe,1));
//	$procedure_type_id = sqlInsert("INSERT INTO procedure_order (provider_id,patient_id,encounter_id,date_collected,date_ordered,order_priority,order_status,
//		  diagnoses,patient_instructions,lab_id,psc_hold) VALUES(?,?,?,?,?,?,?,?,?,?,?)",
//		  array($lab->provider,$lab->pid,$lab->encounter,$lab->timecollected,$lab->orderdate,$lab->priority,$lab->status,
//			"ICD9:".$lab->diagnoses,$lab->patient_instructions,$lab->lab_id,$lab->specimencollected));
//	$seq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix)
//		     VALUES (?,?,?,?)",array($procedure_type_id,$lab->procedurecode,$lab->procedures,$lab->proceduresuffix));
//	$fh = fopen("D:/AAOOEE.txt","a");
//	fwrite($fh,print_r($aoe,1));
//	foreach($aoe as $ProcedureOrder=>$QuestionArr){
//	    if($ProcedureOrder==$lab->procedurecode){
//		foreach($QuestionArr as $Question=>$Answer){
//		    sqlStatement("INSERT INTO procedure_answers (procedure_order_id,procedure_order_seq,question_code,answer) VALUES (?,?,?,?)",
//		    array($procedure_type_id,$seq,$Question,$Answer));
//		}
//	    }
//	}
//	return $procedure_type_id;
//    }
    public function insertAoe($procedure_type_id,$seq,$aoe,$procedure_code_i){
	foreach($aoe as $ProcedureOrder=>$QuestionArr){
	    if($ProcedureOrder==$procedure_code_i){
		foreach($QuestionArr as $Question=>$Answer){
		    sqlStatement("INSERT INTO procedure_answers (procedure_order_id,procedure_order_seq,question_code,answer) VALUES (?,?,?,?)",
		    array($procedure_type_id,$seq,$Question,$Answer));
		}
	    }
	}
    }
    public function insertProcedureMaster($post){
	$procedure_type_id = sqlInsert("INSERT INTO procedure_order (provider_id,patient_id,encounter_id,date_collected,date_ordered,order_priority,
				       order_status,patient_instructions,lab_id,psc_hold,billto,internal_comments) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)",
		    array($post['provider'],$post['patient_id'],$post['encounter_id'],$post['timecollected'],$post['orderdate'],$post['priority'],
		    'pending',$post['patient_instructions'],$post['lab_id'],$post['specimencollected'],
		    $post['billto'],$post['internal_comments']));
	return $procedure_type_id;
    }
    public function saveLab($post,$aoe)
    {
	$papArray = array();
	$specimenState = array();
	$procedure_type_id_arr = array();
	$j=0;
	$prevState = '';
	for($i=0;$i<sizeof($post['procedures']);$i++){
	    $PRow = sqlQuery("SELECT * FROM procedure_type WHERE procedure_code=? AND suffix=? ORDER BY pap_indicator,specimen_state",
			     array($post['procedure_code'][$i],$post['procedure_suffix'][$i]));
	    if(!isset(${$PRow['specimen_state']."_j"}))
	    ${$PRow['specimen_state']."_j"} = 0;
	    if($PRow['pap_indicator']=="P"){
		$papArray[$post['procedure_code'][$i]."|-|".$post['procedure_suffix'][$i]]['procedure'] = $PRow['name'];
		$papArray[$post['procedure_code'][$i]."|-|".$post['procedure_suffix'][$i]]['diagnoses'] = $post['diagnoses'][$i];
	    }
	    else{
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['procedure_code'] = $PRow['procedure_code'];
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['procedure'] = $PRow['name'];
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['procedure_suffix'] = $PRow['suffix'];
		$specimenState[$PRow['specimen_state']][${$PRow['specimen_state']."_j"}]['diagnoses'] = $post['diagnoses'][$i];
		${$PRow['specimen_state']."_j"}++;
	    }
	}
	if(sizeof($papArray)>0){
	    foreach($papArray as $procode_suffix=>$pronameArr ){
	    	$PSArray = explode("|-|",$procode_suffix);
		$procode = $PSArray[0];
		$prosuffix = $PSArray[1];
		$proname = $pronameArr['procedure'];
		$diagnoses = $pronameArr['diagnoses'];
		$PAPprocedure_type_id = $this->insertProcedureMaster($post);
		$procedure_type_id_arr[] = $PAPprocedure_type_id;
		$PAPseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses)
		     VALUES (?,?,?,?,?)",array($PAPprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses));
		$this->insertAoe($PAPprocedure_type_id,$PAPseq,$aoe,$procode);
	    }
	}
	if($post['specimencollected']=="onsite"){
	    if(sizeof($specimenState)>0){
		foreach($specimenState as $k=>$vArray){
		    $SPEprocedure_type_id = $this->insertProcedureMaster($post);
		    $procedure_type_id_arr[] = $SPEprocedure_type_id;
		    for($i=0;$i<sizeof($vArray);$i++){
			$procode = $vArray[$i]['procedure_code'];
			$proname = $vArray[$i]['procedure'];
			$prosuffix = $vArray[$i]['procedure_suffix'];
			$diagnoses = $vArray[$i]['diagnoses'];
			$SPEseq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses)
					VALUES (?,?,?,?,?)",array($SPEprocedure_type_id,$procode,$proname,$prosuffix,$diagnoses));
			$this->insertAoe($SPEprocedure_type_id,$SPEseq,$aoe,$procode);
		    }
		}
	    }
	}
	else{
	    for($i=0;$i<sizeof($post['procedures']);$i++){
		$procedure_code = $post['procedure_code'][$i];
		$procedure_suffix = $post['procedure_suffix'][$i];
		if(array_key_exists($procedure_code."|-|".$procedure_suffix,$papArray)) continue;
		if($i==0){
		$procedure_type_id = $this->insertProcedureMaster($post);
		$procedure_type_id_arr[] = $procedure_type_id;
		}
		$seq = sqlInsert("INSERT INTO procedure_order_code (procedure_order_id,procedure_code,procedure_name,procedure_suffix,diagnoses)
		    VALUES (?,?,?,?,?)",array($procedure_type_id,$post['procedure_code'][$i],$post['procedures'][$i],$post['procedure_suffix'][$i]));
		$this->insertAoe($procedure_type_id,$seq,$aoe,$post['procedure_code'][$i],$post['diagnoses'][$i]);
	    }
	}
	return $procedure_type_id_arr;
    }
    
//    public function listLabLocation($inputString)
//    {
//	$sql = "SELECT * FROM labs WHERE lab_name=?,array($inputString)";
//	$result = sqlStatement($sql);
//	$i = 0;
//	
//	while($row=sqlFetchArray($res)) {
//		$rows[$i] = array (
//			'value' => $row['ppid'],
//			'label' => $row['name'],
//		);
//		$i++;
//	}
//	return $rows;
//
//    }
    public function listProcedures($inputString,$labId)
    {
	$sql = "SELECT * FROM procedure_type AS pt WHERE pt.lab_id=? AND (name LIKE ? OR procedure_code LIKE ?) AND pt.activity=1";
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
   
    function importDataCheck($result,$column_map)//CHECK DATA IF ALREADY EXISTS
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
	    //if($count > 5){ break;}
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
                                                                        'provider_id'           => '#provider_id,ordering_provider_id',
                                                                        'psc_hold'         	=> 'recv_app_id',
									'billto'	    	=> 'bill_to',
									'patient_instructions'	=> 'patient_internal_comments',
									'internal_comments'	=> 'observation_request_comments'
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
                                                                        'lname'         	=> 'ordering_provider_lname'
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
			    "ordering_provider_fname","observation_request_comments","send_app_id","recv_app_id","send_fac_id","recv_fac_id","DorP","bill_to");
     
	
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
			
                                //echo "<br>".$col." => $".$tag." = ".$$tag;
                                //echo "<br>$"."data[".$col."] = ".$data[$col];
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
	    $sql_test   = "SELECT procedure_code, procedure_suffix, procedure_order_seq, diagnoses FROM procedure_order_code
                                WHERE procedure_order_id = ? ";
	    
	    $test_value_arr = array();	    
	    $test_value_arr['procedure_order_id']   = $data_order['procedure_order_id'];
	   
	    $res_test  	= sqlStatement($sql_test,$test_value_arr);
	    while($data_test = sqlFetchArray($res_test))
            {
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
	    
	    
	    
	    /*--------------------------------------------------------------*/
	    
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
    /*
    public function changeOrderRequisitionStatus($proc_order_id,$status,$file_name)
    {
	$sql_status         = "UPDATE procedure_order SET order_status = ?, requisition_file_url = ? WHERE procedure_order_id = ? ";        
	$status_value_arr   = array();
        
        $status_value_arr['status']               = $status;
        $status_value_arr['requisition_file_url'] = $file_name;
	$status_value_arr['procedure_order_id']   = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status;        
    }
    
    public function changeOrderResultStatus($proc_order_id,$status,$file_name)
    {
	$sql_status         = "UPDATE procedure_order SET order_status = ?, result_file_url = ? WHERE procedure_order_id = ? ";        
	$status_value_arr   = array();
        
        $status_value_arr['status']             = $status;
        $status_value_arr['result_file_url']    = $file_name;
	$status_value_arr['procedure_order_id'] = $proc_order_id;
        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status;        
    }
   
    
    public function getOrderStatus($proc_order_id)
    {
	$sql_status         = "SELECT order_status FROM procedure_order WHERE procedure_order_id = ? ";        
	$status_value_arr   = array();
        
        $status_value_arr['procedure_order_id']   = $proc_order_id;        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status['order_status'];        
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
    
    public function getOrderRequisitionFile($proc_order_id)
    {
	$sql_status         = "SELECT requisition_file_url FROM procedure_order WHERE procedure_order_id = ? ";        
	$status_value_arr   = array();
        
        $status_value_arr['procedure_order_id']   = $proc_order_id;        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status['requisition_file_url'];        
    }
    
    public function getOrderResultFile($proc_order_id)
    {
	$sql_status         = "SELECT result_file_url FROM procedure_order WHERE procedure_order_id = ? ";        
	$status_value_arr   = array();
        
        $status_value_arr['procedure_order_id']   = $proc_order_id;        
	$res_status   = sqlQuery($sql_status,$status_value_arr);	
	return $res_status['result_file_url'];        
    }
    */
    
}
?>

