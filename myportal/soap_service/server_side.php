<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2011 Z&H Consultancy Services Private Limited <sam@zhservices.com>
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
//           Jacob T Paul <jacob@zhservices.com>
//           Paul Simon   <paul@zhservices.com>
//           Vinish K     <vinish@zhservices.com>
//
// +------------------------------------------------------------------------------+

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

global $ISSUE_TYPES;
$ignoreAuth=true;
ob_start();

require_once("../../interface/globals.php");
$err = '';
if(!extension_loaded("soap")){
  dl("php_soap.dll");
}
require_once("server_med_rec.php");
class UserService extends Userforms
{
    public function text_to_xml($data){//Converts a text to xml format.Format is as follows
	if($this->valid($data[0])){
	 $text = $data[1];	
	 $doc = new DOMDocument();
	 $doc->formatOutput = true;
	 
	 $root = $doc->createElement( "root" );
	 $doc->appendChild( $root );
	
	 $level = $doc->createElement( "level" );
	 $root->appendChild( $level );
	 
	 $element = $doc->createElement( "text" );
	 $element->appendChild(
	   $doc->createTextNode( $text )
	   );
	 $level->appendChild( $element );
	 return $doc->saveXML();
	}
	else{
		throw new SoapFault("Server", "credentials failed in text_to_xml");
	}
    }
    public function query_to_xml_result($data){//Accepts a select query string.It queries the database and returns the result in xml format.Format is as follows
	if($this->valid($data[0])){
	 $fields=$data[1];
	 $from = $data[2];
	 if(strtolower($fields)=='all')
         {
          $fields=' * ';
         }
	 $sql_query = "SELECT $fields FROM $from";
	 $doc = new DOMDocument();
	 $doc->formatOutput = true;
	 
	 $root = $doc->createElement( "root" );
	 $doc->appendChild( $root );
	
	 $sql_result_set = sqlStatement($sql_query);
	 while($row = sqlFetchArray($sql_result_set))
	 {
	   $level = $doc->createElement( "level" );
	   $root->appendChild( $level );
	   foreach($row as $key=>$value){
	   $element = $doc->createElement( "$key" );
	   $element->appendChild(
	       $doc->createTextNode( $value )
	   );
	   $level->appendChild( $element );
	   }
	 }
	 return $doc->saveXML();
	}
	else{
		throw new SoapFault("Server", "credentials failed in query_to_xml_result error message");
	}
    }
    public function function_return_to_xml($var=array()){//Accepts a select query string.It queries the database and returns the result in xml format.Format is as follows
	
	  $doc = new DOMDocument();
	  $doc->formatOutput = true;
	 
	  $root = $doc->createElement( "root" );
	  $doc->appendChild( $root );
	
	 
	   $level = $doc->createElement( "level" );
	   $root->appendChild( $level );
	   foreach($var as $key=>$value){
	   $element = $doc->createElement( "$key" );
	   $element->appendChild(
	       $doc->createTextNode( $value )
	   );
	   $level->appendChild( $element );
	       }
	   
	 return $doc->saveXML();
	
    }
    public function delete_file($data){
	if($this->valid($data[0])){
	 $file_name_with_path=$data[1];
	 @unlink($file_name_with_path);
	}
	else{
		throw new SoapFault("Server", "credentials failed in delete_file error message");
	}
    }  
    public function file_to_xml($data){//Accepts a file path.Fetches the file in xml format.Format is as follows
	if($this->valid($data[0])){
	   $file_name_with_path=$data[1];
	   $path_parts = pathinfo($file_name_with_path);
	   $handler = fopen($file_name_with_path,"rb");
	   $returnData = fread($handler,filesize($file_name_with_path));
	   fclose($handler);
	   $doc = new DOMDocument();
	   $doc->formatOutput = true;
	   
	   $root = $doc->createElement( "root" );
	   $doc->appendChild( $root );
	
	       $level = $doc->createElement( "level" );
	       $root->appendChild( $level );
	 
	   $filename = $doc->createElement( "name" );
	   $filename->appendChild(
	   $doc->createTextNode( $path_parts['basename'] )
	   );
	   $level->appendChild( $filename );
	   
	   $type = $doc->createElement( "type" );
	   $type->appendChild(
	   $doc->createTextNode( $path_parts['extension'] )
	   );
	   $level->appendChild( $type );
	   $content = $doc->createElement( "file" );
	   $content->appendChild(
	   $doc->createTextNode( base64_encode($returnData) )
	   );
	   $level->appendChild( $content );
	   return $doc->saveXML();
	}
	else{
		throw new SoapFault("Server", "credentials failed in file_to_xml error message");
	}
    }
    public function store_to_file($data){
	if($this->valid($data[0])){
	       $file_name_with_path=$data[1];
	       $data=$data[2];
	       $savedpath=$GLOBALS['OE_SITE_DIR']."/myportal/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	       $savedpath=$GLOBALS['OE_SITE_DIR']."/myportal/documents/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	       $savedpath=$GLOBALS['OE_SITE_DIR']."/myportal/documents/unsigned/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	       $savedpath=$GLOBALS['OE_SITE_DIR']."/myportal/documents/signed/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	       $savedpath=$GLOBALS['OE_SITE_DIR']."/myportal/documents/upload/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	   $handler = fopen($file_name_with_path,"w");
	   fwrite($handler, base64_decode($data));
	   fclose($handler);
	       chmod($file_name_with_path,0777);
	}
	else{
		throw new SoapFault("Server", "credentials failed in store_to_file error message");
	}
	}
   
    static public function batch_despatch($var,$func,$data_credentials){
	if(UserService::valid($data_credentials)){
	require_once("../../library/invoice_summary.inc.php");
	require_once("../../library/options.inc.php");
	require_once("../../library/acl.inc");
	require_once("../../library/patient.inc");
	if($func=='ar_responsible_party')
	 {
		$patient_id=$var['pid'];
		$encounter_id=$var['encounter'];
		$x['ar_responsible_party']=ar_responsible_party($patient_id,$encounter_id);
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='getInsuranceData')
	 {
		$pid=$var['pid'];
		$type=$var['type'];
		$given=$var['given'];
		$x=getInsuranceData($pid,$type,$given);
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='generate_select_list')
	 {
		$tag_name=$var['tag_name'];
		$list_id=$var['list_id'];
		$currvalue=$var['currvalue'];
		$title=$var['title'];
		$empty_name=$var['empty_name'];
		$class=$var['class'];
		$onchange=$var['onchange'];
	        $x['generate_select_list']=generate_select_list($tag_name,$list_id,$currvalue,$title,$empty_name,$class,$onchange);
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='xl_layout_label')
	 {
		$constant=$var['constant'];
	        $x['xl_layout_label']=xl_layout_label($constant);
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='generate_form_field')
	 {
		$frow=$var['frow'];
		$currvalue=$var['currvalue'];
	        ob_start();
		generate_form_field($frow,$currvalue);
		$x['generate_form_field']=ob_get_contents();
		ob_end_clean();
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='getInsuranceProviders')
	 {
		$i=$var['i'];
		$provider=$var['provider'];
		$insurancei=getInsuranceProviders();
	        $x=$insurancei;
		return $x;
	 }
	elseif($func=='get_layout_form_value')
	 {
		$frow=$var['frow'];
		$_POST=$var['post_array'];
		$x['get_layout_form_value']=get_layout_form_value($frow);
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='updatePatientData')
	 {
		$pid=$var['pid'];
		$patient_data=$var['patient_data'];
		$create=$var['create'];
		updatePatientData($pid,$patient_data,$create);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='updateEmployerData')
	 {
		$pid=$var['pid'];
		$employer_data=$var['employer_data'];
		$create=$var['create'];
		updateEmployerData($pid,$employer_data,$create);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='newHistoryData')
	 {
		$pid=$var['pid'];
		newHistoryData($pid);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='newInsuranceData')
	 {
		$_POST=$var[0];
		foreach($var as $key=>$value)
		 {
			if($key>=3)//first 3 need to be skipped.
			 {
			  $var[$key]=formData($value);
			 }
			if($key>=1)
			 {
			  $parameters[$key]=$var[$key];
			 }
		 }
		$parameters[12]=fixDate($parameters[12]);
		$parameters[27]=fixDate($parameters[27]);
		call_user_func_array('newInsuranceData',$parameters);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='insert_to_be_audit_data')
	 {
	        array_unshift($var,$data_credentials);
		UserService::insert_to_be_audit_data($var);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='insert_audit_master')
	 {
	        array_unshift($var,$data_credentials);
		UserService::insert_audit_master($var);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='insert_login_details')
	 {
		array_unshift($var,$data_credentials);
		UserService::insert_login_details($var);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='update_audit_master')
	 {
		array_unshift($var,$data_credentials);
		UserService::update_audit_master($var);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='update_dlm_dld')
	 {
		array_unshift($var,$data_credentials);
		UserService::update_dlm_dld($var);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='update_password')
	 {
		array_unshift($var,$data_credentials);
		$x['update_password']=UserService::update_password($var);
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='delete_if_new_patient')
	 {
		array_unshift($var,$data_credentials);
		UserService::delete_if_new_patient($var);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	elseif($func=='update_audited_data')
	 {
		array_unshift($var,$data_credentials);
		UserService::update_audited_data($var);
		$x['ok']='ok';
		return UserService::function_return_to_xml($x);
	 }
	 elseif($func=='update_openemr_appointment')
	 {
	      array_unshift($var,$data_credentials);
	      UserService::update_openemr_appointment($var);
	      $x['ok']='ok';
	      return UserService::function_return_to_xml($x);
	 }
	}
	else{
		throw new SoapFault("Server", "credentials failed in batch_despatch error message");
	}
    }
    
   public function insert_login_details($var)
       {
	      $data_credentials=$var[0];
	if(UserService::valid($data_credentials))
		 {
			$pid=$var['pid'];
			$username=$var['username'];
			$authPass=$var['authPass'];
			$query="insert into patient_access_offsite(pid,portal_username,portal_pwd) values ('$pid','$username','$authPass')";
			sqlInsert($query);
		 }
		else
		 {
			throw new SoapFault("Server", "credentials failed in insert_login_details error message");
		 }
	}
   
   public function update_password($var){
	      $data_credentials=$var[0];
       if(UserService::valid($data_credentials)){
	       $set = $var['set'];
	       $where = $var['where'];
	       $qry = "select * from  patient_access_offsite  WHERE $where";
	       $res=sqlStatement($qry);
		   if(sqlNumRows($res)>0)
		    {
			   $qry = "UPDATE  patient_access_offsite SET $set WHERE $where";
			   sqlStatement($qry);
			   return 'ok';
			}
			else
			 {
			   return 'notok';
			 }
       }
       else{
	       throw new SoapFault("Server", "credentials failed in update_password error message");
       }
    }
       public function update_openemr_appointment($var)
       {
	      $data_credentials=$var[0];
	      $fh=fopen('this.txt','w');
	      if(UserService::valid($data_credentials)){
		     foreach($var[1] as $key=>$value)
		     {
			    fwrite($fh,$var[1][$key]."\r\n");
			    $eid=explode('_',$var[1][$key]);
			    if($eid[0]=='calendar')
			    {
				   fwrite($fh,"update openemr_postcalendar_events set pc_apptstatus='x' where pc_eid=$eid[1]"."\r\n");
				   sqlQuery("update openemr_postcalendar_events set pc_apptstatus='x' where pc_eid=?",array($eid[1]));
			    }
			    elseif($eid[0]=='audit')
			    {
				   fwrite($fh,"update audit_master set approval_status='5' where id=$eid[1]"."\r\n");
				   sqlQuery("update audit_master set approval_status='5' where id=?",array($eid[1]));
			    }
		     }
	      }
	      else{
		     throw new SoapFault("Server", "credentials failed in update_openemr_appointment error message");
	      }
       }
    public function update_dlm_dld($var)
       {
	      $data_credentials=$var[0];
       if(UserService::valid($data_credentials)){
	       $set = $var['set'];
	       $where = $var['where'];
	       $qry = "UPDATE  documents_legal_master,documents_legal_detail SET $set WHERE $where";
	       sqlStatement($qry);
       }
       else{
	       throw new SoapFault("Server", "credentials failed in update_dlm_dld error message");
       }
    }
    public function update_dld($data){
       if($this->valid($data[0])){
	       $set = $data[1];
	       $where = $data[2];
	       $qry = "UPDATE documents_legal_detail SET $set WHERE $where";
	       sqlStatement($qry);
       }
       else{
	       throw new SoapFault("Server", "credentials failed in update_dld error message");
       }
    }
    public function insert_dld($data){
       if($this->valid($data[0])){
	       sqlStatement("INSERT INTO documents_legal_detail (dld_pid,dld_signed,dld_filepath,dld_master_docid,dld_filename,dld_encounter,dld_file_for_pdf_generation) ".
	       " VALUES (?,?,?,?,?,?,?)",array($data[1],$data[2],$data[3],$data[4],$data[5],$data[6],$data[7]));
       }
       else{
	       throw new SoapFault("Server", "credentials failed in insert_dld error message");
       }
    }
    public function insert_dlm($data){
       if($this->valid($data[0])){
	       sqlStatement("INSERT INTO documents_legal_master(dlm_category, dlm_subcategory,dlm_document_name,dlm_facility,dlm_provider,
	       dlm_filename,dlm_filepath,dlm_effective_date,content) values (?,?,?,?,?,?,?,?,?)",array($data[1],$data[2],$data[3],$data[4],$data[5],$data[6],$data[7],$data[8],$data[9]));
       }
       else{
	       throw new SoapFault("Server", "credentials failed in insert_dlm error message");
       }
    }
    public function batch_select($data){
	if($this->valid($data[0])){
		$batch = $data[1];
		foreach($batch as $key=>$value)
		{
		     
		$batchkey=$value['batchkey'];
		$fields=$value['fields'];
		$from=$value['from'];
		$arrproc[] = $data[0];
		$arrproc[] = $fields;
		$arrproc[] = $from;
		$return_array[$batchkey]=$this->query_to_xml_result($arrproc);
		$arrproc=null;
		}
		return $return_array;
	}
	else{
		throw new SoapFault("Server", "credentials failed in batch_select error message");
	}
    }
    public function batch_function($data){
	if($this->valid($data[0])){
		$batch = $data[1];
		foreach($batch as $key=>$value)
		{
		
		$batchkey=$value['batchkey'];
		$function=$value['funcname'];
		$param=$value['param'];
		$param[]=$data[0];
		$res=call_user_func_array("UserService::$function",$param);
		$return_array[$batchkey]=$res;
		}
		return $return_array;
	}
	else{
		throw new SoapFault("Server", "credentials failed in batch_function error message");
	}
    }
    public function multiplecall($data){
       if($this->valid($data[0])){
	        $batch = $data[1];
		foreach($batch as $key=>$value)
		{
		$batchkey=$value['batchkey'];
		$function=$value['funcname'];
		$param=$value['param'];
		if(is_array($param))
		array_unshift($param,$data[0]);
		else
		$param[]=$data[0];
		$res= UserService::$function($param);
		$return_array[$batchkey]=$res;
		}
		return $return_array;
       }
       else{
		throw new SoapFault("Server", "credentials failed in multiplecall error message");
       }
    }
    public function getversion($data){
       if($this->valid($data[0])){
	      return 1;
       }
       else{
		throw new SoapFault("Server", "credentials failed in getversion error message");
       }
    }

   
  public function valid($credentials){
        $timminus = date("Y-m-d H:m",(strtotime(date("Y-m-d H:m"))-7200)).":00";
	sqlStatement("DELETE FROM audit_details WHERE audit_master_id IN(SELECT id FROM audit_master WHERE type=5 AND created_time<=?)",array($timminus));
        sqlStatement("DELETE FROM audit_master WHERE type=5 AND created_time<=?",array($timminus));
	
	global $pid;
	$ok=0;
	$tim = strtotime(gmdate("Y-m-d H:m"));
	$res = sqlStatement("SELECT * FROM audit_details WHERE field_value=?",array($credentials[4]));
	if(sqlNumRows($res)){
		if($GLOBALS['check']!=true){
		return false;
		}
	}
	else{
	      $grpID = sqlInsert("INSERT INTO audit_master SET type=5");
	      sqlStatement("INSERT INTO audit_details SET field_value='".$credentials[4]."' , audit_master_id='".$grpID."'");
	}
	if(sha1($GLOBALS['portal_offsite_password'].date("Y-m-d H",$tim).$credentials[4])==$credentials[3]){
	      $ok =1;
	}
	elseif(sha1($GLOBALS['portal_offsite_password'].date("Y-m-d H",($tim-3600)).$credentials[4])==$credentials[3]){
	      $ok =1;
	}
	elseif(sha1($GLOBALS['portal_offsite_password'].date("Y-m-d H",($tim+3600)).$credentials[4])==$credentials[3]){
	      $ok =1;
	}
	if(($credentials[2]==$GLOBALS['portal_offsite_username'] && $ok==1 && $GLOBALS['portal_offsite_enable']==1)||$GLOBALS['check']==true){
		$_GET['pid'] = $credentials[1];
		$_GET['site'] = $credentials[0];
		$pid = $_GET['pid'];
		require_once("../../interface/globals.php");
		$GLOBALS['pid']=$_GET['pid'];
		$GLOBALS['check']=true;
		
		return true;
	}
	else{
		return false;
	}
    }
    public function check_connection($data){
       if($this->valid($data[0])){
	   return 'ok';
       }
       else{
	   return 'notok';
       }
    }
}
$server = new SoapServer(null,array('uri' => "urn://portal/res"));
$server->setClass('UserService');
$server->setPersistence(SOAP_PERSISTENCE_SESSION);
$server->handle();
?>