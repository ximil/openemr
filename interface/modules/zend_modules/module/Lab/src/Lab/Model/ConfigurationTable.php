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

use Zend\View\Model\JsonModel;

class ConfigurationTable extends AbstractTableGateway
{
    public $tableGateway;
    public $parent_result;
    public $child_result;
    
    public $initiating_typeid;
    
    public $result;
    
    public $child_ids;


    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway 		= $tableGateway;
	$this->parent_result 		= array();
	$this->child_result 		= array();
	$this->initiating_typeid	= 0;
	$this->result			= array();
	$this->child_ids		= array();
    }   
	
    public function getConfigDetails($type_id)
    {
	
	$comb_arr		= array();
	$resparent_arr[]	= $this->getConfigParentDetails($type_id);
	
	$is_group	= 0;
	
	foreach($resparent_arr as $resparent)
	{
	    foreach($resparent as $type_arr)
	    {
		foreach($type_arr as $type)
		{
		    $comb_arr[]	= $type;
		    if($type['group'] == 'grp')
		    {
			$is_group	= 1;			
		    }
		}
	    }
	}
	
	if($is_group == 0)
	{	    
	    $reschild_arr[]		= $this->getConfigChildDetails($type_id);
	}
	
	foreach($reschild_arr as $reschild)
	{
	    foreach($reschild as $type_arr)
	    {
		foreach($type_arr as $type)
		{
		    $comb_arr[]	= $type;
		}
	    }
	}	
	
	$ret_arr        	= new JsonModel($comb_arr);
	
	$fp	= fopen("D:/arr.txt","w");
	fwrite($fp,"\n comb arr \n ".print_r($ret_arr,1));
	
	return $ret_arr;
	
    }
    //NEW FUNCTION TO SHOW TEST DETAILS IN A TREE VIEW
    public function getConfigParentDetails($type_id)
    {
	$init_tag	= 1;
	$json_arr	= array();		
	$res_arr 	= array();
	
	$row 		= $this->getTypeDetails($type_id);	
	
	if($row['procedure_type_id'] <> "")
	{	   
	    $res_arr 	= $this->saveDetailsArray($row,$init_tag);
	    array_push($this->parent_result, $res_arr);
	}	
	return $this->parent_result;
    }
     
    public function getConfigChildDetails($type_id)
    {
	
	$child_res 	= $this->getChildDetails($type_id);
	while($child 	= sqlFetchArray($child_res))
	{
	    if(($child['procedure_type_id'] <> "")&&($child['procedure_type_id'] <> $type_id))
	    {
		$res_arr = $this->saveDetailsArray($child,$init_tag);
		array_push($this->child_result, $res_arr);
	    
		$this->getConfigChildDetails($child['procedure_type_id']);
	    }
	}
	return $this->child_result;	
    }
    
    public function getConfigChildIds($type_id)
    {
	
	$child_res 	= $this->getChildDetails($type_id);
	while($child 	= sqlFetchArray($child_res))
	{
	    if(($child['procedure_type_id'] <> "")&&($child['procedure_type_id'] <> $type_id))
	    {
		array_push($this->child_ids, $child['procedure_type_id']);
	    
		$this->getConfigChildIds($child['procedure_type_id']);
	    }
	}
	return $this->child_ids;	
    }
    
    
    
    public function getTypeDetails($type_id)
    {	
	$sel_col	= ""; 
	foreach($upcols_array as $col)
	{
	    if($data[$col] <> "")
	    {	    
		$sel_col	.= $col." = ? ,";
		$input_arr[]	= $data[$col];
	    }
	    
	}
	
	$sql		= "SELECT procedure_type_id, parent, name, lab_id, procedure_code, procedure_type, description, seq, units,`range`, related_code,
				route_admin, laterality, standard_code, body_site, specimen
			    FROM procedure_type 
				WHERE procedure_type_id = ? ";
	
	$value_arr  	= array($type_id);
	
	$res 		= sqlStatement($sql,$value_arr);
	$row 		= sqlFetchArray($res);
	return $row;
    }
    
    public function getChildDetails($type_id)
    {
	$sql		= "SELECT procedure_type_id, parent, name, lab_id, procedure_code, procedure_type, description, seq, units,`range`, related_code
				FROM procedure_type 
				    WHERE parent = ? ";
	$value_arr  	= array($type_id);
	
	$res        	= sqlStatement($sql,$value_arr);
	
	return $res;
    }
    
    
    public function saveDetailsArray($row,$open_flag=0)
    {
	$arr		= array();
	$ret_arr	= array();
	$result_array	= array();
	$orderfrom_arr	= array();
	
	//'group_name', 'group_description',
	//'order_name', 'order_description', 'order_sequence', 'order_from', 'order_procedurecode', 'order_standardcode', 'order_bodysite',
	//'order_specimentype', 'order_administervia', 'order_laterality'
	//'result_name', 'result_description', 'result_sequence', 'result_defaultunits', 'result_defaultrange',	'result_followupservices'
	//'reccomendation_name', 'reccomendation_description', 'reccomendation_sequence', 'reccomendation_defaultunits', 'reccomendation_defaultrange',	'reccomendation_followupservices'
	
	
	$grp_array	= array('name' 			=> 'group_name',
				'description' 		=> 'group_description');
				
	$ord_array	= array('name' 			=> 'order_name',
				'description' 		=> 'order_description',
				'seq' 			=> 'order_sequence',
				'lab_id' 		=> 'order_from',								
				'procedure_code' 	=> 'order_procedurecode',
				'standard_code' 	=> 'order_standardcode',
				'body_site' 		=> 'order_bodysite',
				'specimen' 		=> 'order_specimentype',
				'route_admin' 		=> 'order_administervia',								 
				'laterality' 		=> 'order_laterality');
	
	$res_array	= array('name' 			=> 'result_name',
				'description' 		=> 'result_description',
				'seq' 			=> 'result_sequence',
				'units' 		=> 'result_defaultunits',								
				'range' 		=> 'result_defaultrange',
				'related_code' 		=> 'result_followupservices');
	
	$rec_array	= array('name' 			=> 'reccomendation_name',
				'description' 		=> 'reccomendation_description',
				'seq' 			=> 'reccomendation_sequence',
				'units' 		=> 'reccomendation_defaultunits',								
				'range' 		=> 'reccomendation_defaultrange',
				'related_code' 		=> 'reccomendation_followupservices');	
	
	if ($row['procedure_type'] == 'grp')
	{
	    $arr['group_type_id'] 	= $row['procedure_type_id'];
	    $arr['group'] 	= $row['procedure_type'];     
	    foreach($grp_array as $column => $grp)
	    {		
		$arr[$grp] 	= $row[$column];	
	    }
	    array_push($result_array, $arr);
	}
	
	else if ($row['procedure_type'] == 'ord')
	{
	    $arr['order_type_id'] 	= $row['procedure_type_id'];
	    $arr['group'] 	= $row['procedure_type'];
	    foreach($ord_array as $column => $ord)
	    {
		$arr[$ord] 	= $row[$column];
		
	    }
	    array_push($result_array, $arr);
	}
	
	else if ($row['procedure_type'] == 'res')
	{
	    $arr['result_type_id'] 	= $row['procedure_type_id'];
	    $arr['group'] 	= $row['procedure_type'];
	    foreach($res_array as $column => $res)
	    {
		$arr[$res] 	= $row[$column];		
	    }
	    array_push($result_array, $arr);
	}
	
	else if($row['procedure_type'] == 'rec')
	{
	    $arr['reccomendation_type_id'] 	= $row['procedure_type_id'];
	    $arr['group'] 	= $row['procedure_type'];
	    foreach($rec_array as $column => $rec)
	    {
		$arr[$rec] 	= $row[$column];	
		
	    }
	    array_push($result_array, $arr);
	}
	
	return $result_array;
    }
    
    public function getAllConfigDetails()
    {	
	$reult_arr	= array();
	$json_arr	= array();	
	
	$start 	= isset($_POST['page']) ? intval($_POST['page']) : 1;  
	$rows 	= isset($_POST['rows']) ? intval($_POST['rows']) : 10;  
	
        if ($_POST['page'] == 1) {
            $start = $_POST['page'] - 1;
        } elseif ($_POST['page'] > 1) {
            $start = (($_POST['page'] - 1) * $rows);
        }	
	
	$sql	= "SELECT procedure_type_id, parent, name, procedure_code, procedure_type, `range`, description
			FROM procedure_type LIMIT $start, $rows ";
	$res    = sqlStatement($sql);	
	
	$sql_count	= "SELECT procedure_type_id FROM procedure_type ";
	$res_count    	= sqlStatement($sql_count);	
	$numrows	= sqlNumRows($res_count);
	
	$json_arr['total']	= $numrows;	
	$no_rows		= 0;
	
	while ($row = sqlFetchArray($res))
	{	    
	    $exists	= 0;
	    foreach($this->result as $result)
	    {
		if($result['id'] == $row['procedure_type_id'])
		{
		    $exists	= 1;
		    break;
		}
	    }
	    
	    if($exists == 1)
	    {
		continue;
	    }	    
	    
	    $no_rows++;
	    
	    $res_arr	= array();
	    
	    $res_arr['id']	= $row['procedure_type_id'];
	    $res_arr['pk']	= $row['procedure_type_id'];
	    $res_arr['name']	= $row['name'];	    
	    
	    $res_arr['procedure_code']	= $row['procedure_code'];
	    $res_arr['procedure_type']	= $row['procedure_type'];
	    $res_arr['range']		= $row['range'];
	    $res_arr['discription']	= $row['description'];
	    
	    $res_arr['action']		= "<div class=\"icon_add\" onclick=\"addExist(".$row['procedure_type_id'].")\">&nbsp;</div>";
	    $res_arr['action']		.= "<div class=\"icon_edit\" onclick=\"editItem(".$row['procedure_type_id'].")\">&nbsp;</div>";
	    $res_arr['action']		.= "<div class=\"icon_delete\" onclick=\"deleteItem(".$row['procedure_type_id'].")\">&nbsp;</div>";
	    
	    if($res_arr['procedure_type'] == "grp"){
		$res_arr['iconCls']	= "tree-folder";
		$res_arr['order']	= "Group";
	    }
	    else if($res_arr['procedure_type'] == "ord"){
		$res_arr['iconCls']	= "icon-lab-order";
		$res_arr['order']	= "Order";
	    }
	    else if($res_arr['procedure_type'] == "res"){
		$res_arr['iconCls']	= "icon-lab-result";
		$res_arr['order']	= "Result";
	    }
	    else if($res_arr['procedure_type'] == "rec"){
		$res_arr['iconCls']	= "tree-file";
		$res_arr['order']	= "Recommendation";
	    }	    
	    
	    $sql_child	= "SELECT procedure_type_id FROM procedure_type WHERE parent = '".$row['procedure_type_id']."' AND
				procedure_type_id <> '".$row['procedure_type_id']."' ";
	    $res_child 	= sqlStatement($sql_child);	
	    $numchilds	= sqlNumRows($res_child);	    
	    
	    if($row['procedure_type_id'] <> $row['parent'])
	    {
		
		$row['parent']		= ($row['parent'] == 0) ? "":$row['parent'];
		$res_arr['_parentId']	= $row['parent'];
	    
	    }
	    if($numchilds > 0)
	    {
		    $res_arr['state']	= "closed";
	    }
	    //array_push($this->result[$row['procedure_type_id']],$res_arr);
	    $this->result[]	= $res_arr;	    
	   
	    if($numchilds > 0)
	    {
		$this->saveAllChildConfigArray($row['procedure_type_id']);
	    }	    
	}	
	
	$json_arr['rows']	= $this->result;	
	$result_arr        	= new JsonModel($json_arr);	
	
	return $result_arr;
    }
    
    public function saveAllChildConfigArray($type_id)
    {
	$sql	= "SELECT `procedure_type_id`, parent, name, procedure_code, procedure_type, `range`, description
			FROM procedure_type WHERE parent = '".$type_id."' AND
				    procedure_type_id <> '".$type_id."' ";
	
	$res    = sqlStatement($sql);
	
	while($row = sqlFetchArray($res))
	{
	    $res_arr	= array();
    
	    $res_arr['id']		= $row['procedure_type_id'];
	    $res_arr['pk']		= $row['procedure_type_id'];
	    $res_arr['name']		= $row['name'];	
	    
	    $res_arr['procedure_code']	= $row['procedure_code'];
	    $res_arr['procedure_type']	= $row['procedure_type'];
	    $res_arr['range']		= $row['range'];
	    $res_arr['discription']	= $row['description'];
	    
	    $res_arr['action']		= "<div class=\"icon_add\" onclick=\"addExist(".$row['procedure_type_id'].")\">&nbsp;</div>";
	    $res_arr['action']		.= "<div class=\"icon_edit\" onclick=\"editItem(".$row['procedure_type_id'].")\">&nbsp;</div>";
	    $res_arr['action']		.= "<div class=\"icon_delete\" onclick=\"deleteItem(".$row['procedure_type_id'].")\">&nbsp;</div>";
	    
	    if($res_arr['procedure_type'] == "grp"){
		$res_arr['iconCls']	= "tree-folder";
		$res_arr['order']	= "Group";
	    }
	    else if($res_arr['procedure_type'] == "ord"){
		$res_arr['iconCls']	= "icon-lab-order";
		$res_arr['order']	= "Order";
	    }
	    else if($res_arr['procedure_type'] == "res"){
		$res_arr['iconCls']	= "icon-lab-result";
		$res_arr['order']	= "Result";
	    }
	    else if($res_arr['procedure_type'] == "rec"){
		$res_arr['iconCls']	= "tree-file";
		$res_arr['order']	= "Recommendation";
	    }	
	    
	    $sql_child	= "SELECT procedure_type_id FROM procedure_type WHERE parent = '".$row['procedure_type_id']."' AND
				    procedure_type_id <> '".$row['procedure_type_id']."' ";
	    $res_child 	= sqlStatement($sql_child);	
	    $numchilds	= sqlNumRows($res_child);
	    
	    if($row['procedure_type_id'] <> $row['parent'])
	    {
		if($numchilds <> 0)
		{
		    $res_arr['state']	= "closed";
		}
		$row['parent']		= ($row['parent'] == 0) ? "":$row['parent'];
		$res_arr['_parentId']	= $row['parent'];
	    
	    }
	    //array_push($this->result[$type_id], $res_arr);
	    $this->result[]	=  $res_arr;
	    if($numchilds > 0)
	    {
		$this->saveAllChildConfigArray($row['procedure_type_id']);
	    }
	}
    }
    
    public function updateConfigDetails($request)
    {		
	$upcols_array	= array('name' , 'procedure_code', 'lab_id', 'body_site',  'specimen',
				'route_admin',  'laterality',  'description',  'standard_code',  'related_code',  'units',  'range',  'seq');
	
	$data  	    	= array(
				'type_id'    		=> $request->getQuery('type_id'),
				'name'    		=> $request->getQuery('name'),
				'description'    	=> $request->getQuery('description'),
				'seq'    		=> $request->getQuery('seq'),
				'order_from'    	=> $request->getQuery('lab_id'),
				'procedure_code'    	=> $request->getQuery('procedure_code'),
				'standard_code'   	=> $request->getQuery('standard_code'),
				'body_site'    		=> $request->getQuery('body_site'),
				'specimen'    		=> $request->getQuery('specimen'),
				'route_admin'    	=> $request->getQuery('route_admin'),
				'laterality'    	=> $request->getQuery('laterality'),
				'units'    		=> $request->getQuery('units'),
				'range'    		=> $request->getQuery('range'),
				'related_code'   	=> $request->getQuery('related_code'));
	
	$sel_col	= "";
	$input_arr	= array();
	
	foreach($upcols_array as $col)
	{
	    if($data[$col] <> "")
	    {	    
		$sel_col	.= "`".$col."` = ? ,";
		$input_arr[]	= $data[$col];
	    }	    
	}
	$input_arr[]	= $data['type_id'];	
	$sel_col	= rtrim($sel_col,",");	
	
	$sql	= "UPDATE procedure_type SET $sel_col WHERE procedure_type_id = ? ";	
	$res    = sqlStatement($sql,$input_arr);
	
	$return	= array();
	
	$return[0]  = array('return' => 0, 'type_id' => $data['type_id']);
	$arr        = new JsonModel($return);
		
	return $arr;
    }
                    
    public function getAddConfigDetails($list_array)
    {
	$arr		= array();
	$ret_arr	= array();
	$result_array	= array();
	$orderfrom_arr	= array();
	
	$bodysite	= $list_array[0];
	$specimen	= $list_array[1];
	$administervia	= $list_array[2];
	$laterality	= $list_array[3];
	$defaultunits	= $list_array[4];	

	foreach($bodysite as $bodysite_array)
	{
	    $bodysite_arr[]		= array('id' 	=> $bodysite_array['value'],
						'value' => $bodysite_array['label'],
						'text'  => $bodysite_array['label']);
	}
	
	foreach($specimen as $specimen_array)
	{
	    $specimen_arr[]		= array('id' => $specimen_array['value'],
						'value' => $specimen_array['label'],
						'text'  => $specimen_array['label']);
	}
	
	foreach($administervia as $administervia_array)
	{
	    $administervia_arr[]	= array('id' => $administervia_array['value'],
						'value' => $administervia_array['label'],
						'text'  => $administervia_array['label']);
	}
	
	foreach($laterality as $laterality_array)
	{
	    $laterality_arr[]		= array('id' => $laterality_array['value'],
						'value' => $laterality_array['label'],
						'text'  => $laterality_array['label']);
	}
	
	foreach($defaultunits as $defaultunits_array)
	{
	    $defaultunits_arr[]		= array('id' => $defaultunits_array['value'],
						'value' => $defaultunits_array['label'],
						'text'  => $defaultunits_array['label']);
	}
		
	$ppres = sqlStatement("SELECT ppid, name FROM procedure_providers ORDER BY name, ppid");
	while($pprow = sqlFetchArray($ppres))
	{
	    $orderfrom_arr[] = array('id' => $pprow['ppid'],
				     'value' => $pprow['name'],
				     'text'  => $pprow['name']);
	}
	
	$grp_array	= array('name' 			=> array('title'  	=> "Name",
								 'editor' 	=> "text"),
				'description' 		=> array('title'  	=> "Description",
								 'editor' 	=> "text"));
				
	$ord_array	= array('name' 			=> array('title'  	=> "Name",
								 'editor' 	=> "text"),
				'description' 		=> array('title'  	=> "Description",
								 'editor' 	=> "text"),
				'seq' 			=> array('title'  	=> "Sequence",
								 'editor' 	=> "text"),
				'order_from' 		=> array('title'  	=> "Order From",
								 'editor' 	=> "combobox",
								 'options'	=> 'orderfrom_arr'),
				'procedure_code' 	=> array('title'  	=> "Procedure Code",
								 'editor' 	=> "text"),
				'standard_code' 	=> array('title'  	=> "Standard Code",
								 'editor' 	=> "text"),
				'body_site' 		=> array('title'  	=> "Body Site",
								 'editor' 	=> "combobox",
								 'options'	=> 'bodysite_arr'),
				'specimen' 		=> array('title'  	=> "Specimen Type",
								 'editor' 	=> "combobox",
								 'options'	=> 'specimen_arr'),
				'route_admin' 		=> array('title'  	=> "Administer Via",
								 'editor' 	=> "combobox",
								 'options'	=> 'administervia_arr'),
				'laterality' 		=> array('title'  	=> "Laterality",
								 'editor' 	=> "combobox",
								 'options'	=> 'laterality_arr'));
	
	$res_array	= array('name' 			=> array('title'  	=> "Name",
								 'editor' 	=> "text"),
				'description' 		=> array('title'  	=> "Description",
								 'editor' 	=> "text"),
				'seq' 			=> array('title'  	=> "Sequence",
								 'editor' 	=> "text"),
				'units' 		=> array('title'  	=> "Default Units",
								 'editor' 	=> "combobox",
								 'options'	=> 'defaultunits_arr'),
				'range' 		=> array('title'  	=> "Default Range",
								 'editor' 	=> "text"),
				'related_code' 		=> array('title'  	=> "Followup Services",
								 'editor' 	=> "text"));
	
	$rec_array	= array('name' 			=> array('title'  	=> "Name",
								 'editor' 	=> "text"),
				'description' 		=> array('title'  	=> "Description",
								 'editor' 	=> "text"),
				'seq' 			=> array('title'  	=> "Sequence",
								 'editor' 	=> "text"),
				'units' 		=> array('title'  	=> "Default Units",
								 'editor' 	=> "combobox",
								 'options'	=> 'defaultunits_arr'),
				'range' 		=> array('title'  	=> "Default Range",
								 'editor' 	=> "text"),
				'related_code' 		=> array('title'  	=> "Followup Services",
								 'editor' 	=> "text"));
	
	
	/*
	[
	    {"name":"Name","value":"Bill Smith","group":"ID Settings","editor":"text"},
	    {"name":"Address","value":"","group":"ID Settings","editor":"text"},
	    {"name":"Age","value":"40","group":"ID Settings","editor":"numberbox"},
	    {"name":"Birthday","value":"01/02/2012","group":"ID Settings","editor":"datebox"},
	    {"name":"SSN","value":"123-456-7890","group":"ID Settings","editor":"text"},
	    {"name":"Email","value":"bill@gmail.com","group":"Marketing Settings","editor":{
		"type":"validatebox",
		"options":{
		    "validType":"email"
		}
	    }},
	    {"name":"FrequentBuyer","value":"text3","group":"Marketing Settings","editor":{
		"type":"combobox",
		 "options": {
		    "data": [{ "value":"text1", "text":"text1"},{ "value":"text2", "text":"text2"},{"value":"text3","text":"text3"},{ "value":"text4", "text":"text4"},{ "value":"text5", "text":"text5"}]
		  }
	    }}
	]
	*/
		
	foreach($grp_array as $column => $grp)
	{
	    $arr['name'] 	= $grp['title'];
	    $arr['value'] 	= "";
	    $arr['group'] 	= 'Group'; 
	    
	    if($grp['editor'] == "text")
	    {
		$arr['editor'] 	= $grp['editor'];
	    }
	    else if($grp['editor'] == "combobox")
	    {
		$arr['editor'] 	= array('type' 		=> $grp['editor'],
					'options' 	=> array('data' => ${$grp['options']})
					);
	    }	    
	    array_push($result_array, $arr);
	}
	
	foreach($ord_array as $column => $ord)
	{
	    $arr['name'] 	= $ord['title'];
	    $arr['value'] 	= "";			
	    $arr['group'] 	= 'Order';
	    
	    if($ord['editor'] == "text")
	    {
		$arr['editor'] 	= $ord['editor'];
	    }
	    else if($ord['editor'] == "combobox")
	    {
		$arr['editor'] 	= array('type' 		=> $ord['editor'],
					'options' 	=> array('data' => ${$ord['options']})
					);
	    }
	    array_push($result_array, $arr);
	}
		
	foreach($res_array as $column => $res)
	{
	    $arr['name'] 	= $res['title'];
	    $arr['value'] 	= "";			
	    $arr['group'] 	= 'Result';
	   
	    if($res['editor'] == "text")
	    {
		$arr['editor'] 	= $res['editor'];
	    }
	    else if($res['editor'] == "combobox")
	    {
		$arr['editor'] 	= array('type' 		=> $res['editor'],
					'options' 	=> array('data' => ${$res['options']})
					);
	    }
	    array_push($result_array, $arr);
	}
	
	foreach($rec_array as $column => $rec)
	{
	    $arr['name'] 	= $rec['title'];
	    $arr['value'] 	= "";			
	    $arr['group'] 	= 'Recommendation';
	    if($rec['editor'] == "text")
	    {
		$arr['editor'] 	= $rec['editor'];
	    }
	    else if($rec['editor'] == "combobox")
	    {
		$arr['editor'] 	= array('type' 		=> $rec['editor'],
					'options' 	=> array('data' => ${$rec['options']}
								 )
					);
	    }
	    array_push($result_array, $arr);
	}
	
	$ret_arr        	= new JsonModel($result_array);	
	return $ret_arr;	
    }
    
    public function addConfigDetails($request)
    {
	$upcols_array	= array('procedure_type', 'parent', 'name' , 'lab_id', 'procedure_code',  'body_site',  'specimen',
				'route_admin',  'laterality',  'description',  'standard_code',  'related_code',  'units',  'range',  'seq');
	
	$data  	    	= array(
				'procedure_type'    	=> $request->getQuery('procedure_type'),
				'parent'    		=> $request->getQuery('parent'),
				'name'    		=> $request->getQuery('name'),
				'description'    	=> $request->getQuery('description'),
				'seq'    		=> $request->getQuery('seq'),
				'lab_id'    		=> $request->getQuery('lab_id'),
				'procedure_code'    	=> $request->getQuery('procedure_code'),
				'standard_code'   	=> $request->getQuery('standard_code'),
				'body_site'    		=> $request->getQuery('body_site'),
				'specimen'    		=> $request->getQuery('specimen'),
				'route_admin'    	=> $request->getQuery('route_admin'),
				'laterality'    	=> $request->getQuery('laterality'),
				'units'    		=> $request->getQuery('units'),
				'range'    		=> $request->getQuery('range'),
				'related_code'   	=> $request->getQuery('related_code'));	
	
	foreach($data as $key => $val)
	{
	    $data[$key]	= (($data[$key] <> null)||(isset($data[$key]))) ? $data[$key] : "";	  
	}
	
	/*	
	if($data['procedure_type'] == "Group")
	{
	    $data['procedure_type'] = "grp";
	}
	else if($data['procedure_type'] == "Order")
	{
	    $data['procedure_type'] = "ord";
	}
	else if($data['procedure_type'] == "Result")
	{
	    $data['procedure_type'] = "res";
	}
	else if($data['procedure_type'] == "Recommendation")
	{
	    $data['procedure_type'] = "rec";
	}*/	
	
	$sel_col	= "";
	$input_arr	= array();
	
	$param_count	= 0;
	foreach($upcols_array as $col)
	{	      
	    $sel_col		.= "`".$col."`,";
	    $input_arr[]	= $data[$col];
	    $param_count++;
	}
	$param_str	= str_repeat("?,",($param_count));
	$params		= rtrim($param_str,",");	
	$sel_col	= rtrim($sel_col,",");	
	
	$sql		= "INSERT INTO procedure_type ($sel_col) VALUES ($params)";		
	$res    	= sqlInsert($sql,$input_arr);	
	
	
	
	$return	= array();
	
	$return[0]  = array('return' => 0, 'type_id' => $res);
	$arr        = new JsonModel($return);
	
	return $arr;
    }
    
    /*public function getAddExistConfigDetails($type_id)
    {
	$arr		= array();
	$ret_arr	= array();
	$result_array	= array();
	
	$row 		= $this->getTypeDetails($type_id);
	
	
	$grp_array	= array('name' => "Name", 'description' => "Description");
				
	$ord_array	= array('name' => "Name", 'description' => "Description",'seq' => "Sequence",'order_from' => "Order From",
				'procedure_code' => "Procedure Code",'standard_code' => "Standard Code",'body_site' => "Body Site",
				'specimen' =>"Specimen Type", 'route_admin' => "Administer Via",'laterality' => "Laterality");
	
	$res_array	= array('name' => "Name", 'description' => "Description",'seq' => "Sequence", 'units' => "Default Units",
				'range' =>"Default Range", 'related_code' => "Followup Services");
	
	$rec_array	= array('name' => "Name", 'description' => "Description",'seq' => "Sequence", 'units' => "Default Units",
				'range' =>"Default Range", 'related_code' => "Followup Services");
		
	foreach($grp_array as $column => $grp)
	{
	    $arr['name'] 	= $grp;
	    $arr['value'] 	= "";
	    $arr['group'] 	= 'Group'; 
	    $arr['editor'] 	= 'text';
	    array_push($result_array, $arr);
	}
	
	//if($row['procedure_type'] == "grp")
	//{
	    foreach($ord_array as $column => $ord)
	    {
		$arr['name'] 	= $ord;
		$arr['value'] 	= "";			
		$arr['group'] 	= 'Order';
		$arr['editor'] 	= 'text';
		array_push($result_array, $arr);
	    }
	//}
	//if($row['procedure_type'] == "ord")
	//{
	    foreach($res_array as $column => $res)
	    {
		$arr['name'] 	= $res;
		$arr['value'] 	= "";			
		$arr['group'] 	= 'Result';
		$arr['editor'] 	= 'text';
		array_push($result_array, $arr);
	    }
	//}
	//if($row['procedure_type'] == "res")
	//{
	    foreach($rec_array as $column => $rec)
	    {
		$arr['name'] 	= $rec;
		$arr['value'] 	= "";			
		$arr['group'] 	= 'Recommendation';
		$arr['editor'] 	= 'text';
		array_push($result_array, $arr);
	    }
	//}
	$ret_arr        	= new JsonModel($result_array);	
	return $ret_arr;	
    }*/
    
    public function deleteConfigDetails($type_id)
    {
	array_push($this->child_ids, $type_id);
	$ret_arr	= $this->getConfigChildIds($type_id);
	$ret_arr	= array_reverse($ret_arr);
	
	$sql	= "DELETE FROM procedure_type WHERE procedure_type_id = ? ";
	
	foreach($ret_arr as $typeid)
	{
	    $in_arr	= array('type_id' => $typeid);	    
	    sqlStatement($sql,$in_arr);
	}	
	
	$return	= array();
	
	$return[0]  = array('return' => 0, 'type_id' => $type_id);
	$arr        = new JsonModel($return);
	
	return $arr;
    }
}
?>

