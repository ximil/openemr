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

class PullTable extends AbstractTableGateway
{
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
	
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
	$retstr		= "";	
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
			    $condition	.= " AND ";
			}
			$condition	.=" ".$pkey." = ? ";
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
		    
		    $update_expr    	= implode(" = ? ,",$update_key_arr);
		    $update_expr	.=" = ? ";
		    
		    $sql_check  = "SELECT COUNT(*) as data_exists FROM ".$table." WHERE ".$condition;
		    $pat_data_check    	= sqlQuery($sql_check,$check_value_arr);
		    
		    if($pat_data_check['data_exists'])
		    {
			$sqlup		= "UPDATE ".$table." SET ".$update_expr." WHERE ".$condition;
			$pat_data_check	= sqlQuery($sqlup,$update_combined_arr);
		    }
		    else
		    {
			$sql		= "INSERT INTO ".$table."(".$fields.") VALUES (".$params.")";
			$insert_id 	= sqlInsert($sql,$value_arr);			
		    }
		}		
	    }
	}
	sqlStatement("UPDATE procedure_type SET parent=procedure_type_id");
	sqlStatement("UPDATE procedure_type SET name=description");
	return $retstr;
    }
    
    public function getWebserviceOptions()
    {
	$options    = array('location' => "http://192.168.1.133/webserver/lab_server.php",
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
    
    public function getLabCredentials($lab_id)
    {
	$sql_cred   = "SELECT  login, password, remote_host FROM procedure_providers WHERE ppid = ? ";
	$res_cred   = sqlQuery($sql_cred,array('ppid' => $lab_id));
	return $res_cred;        
    }    
}
?>

