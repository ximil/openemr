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
//
// +------------------------------------------------------------------------------+

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("server_mail.php");
class UserAudit extends UserMail{
public function delete_if_new_patient($var)
       {
	      $data_credentials=$var[0];
	if(UserService::valid($data_credentials)){
		$audit_master_id = $var['audit_master_id'];
		 $qry = "select * from audit_master WHERE id=? and approval_status=1 and type=1";
		 $result=sqlStatement($qry,array($audit_master_id));
		 $rowfield = sqlFetchArray($result);
		 if($rowfield['pid']>0)
		  {
			$pid=$rowfield['pid'];
			$qry = "DELETE from  patient_data WHERE pid=?";
		        sqlStatement($qry,array($pid));
			$qry = "DELETE  from employer_data WHERE pid=?";
		        sqlStatement($qry,array($pid));
			$qry = "DELETE  from history_data WHERE pid=?";
		        sqlStatement($qry,array($pid));
			$qry = "DELETE  from insurance_data WHERE pid=?";
		        sqlStatement($qry,array($pid));
			$qry = "DELETE from patient_access_offsite WHERE  pid=? ";
		        sqlStatement($qry,array($pid));
			$qry = "DELETE from openemr_postcalendar_events WHERE  pc_pid=? ";// appointments approved, but patient denied case.
		        sqlStatement($qry,array($pid));
			 $qry = "select * from documents_legal_master,documents_legal_detail   where dld_pid=? 
				and dlm_document_id=dld_master_docid and  dlm_subcategory   not in (SELECT dlc_id FROM `documents_legal_categories` 
				where dlc_category_name='Layout Signed' and dlc_category_type=2)";
			 $result=sqlStatement($qry,array($pid));
			 while($row_sql=sqlFetchArray($result))
			  {
			   @unlink('../documents/'.$row_sql['dld_filepath'].$row_sql['dld_filename']);
			  }
			$qry = "DELETE  from documents_legal_detail WHERE dld_pid=?";
		        sqlStatement($qry,array($pid));
		        $qry = "DELETE from audit_details WHERE audit_master_id in 
								(select id from audit_master WHERE pid=? )";//type and approval_status=1 is not called purposefully,so as to delete the appointments also
		        sqlStatement($qry,array($pid));
			$qry = "DELETE from audit_master WHERE pid=?";//type and approval_status=1 is not called purposefully,so as to delete the appointments also
		        sqlStatement($qry,array($pid));
		  }
	}
	else{
		throw new SoapFault("Server", "credentials failed in delete_if_new_patient error message");
	}
       }
public function update_audit_master($var)
       {
	      $data_credentials=$var[0];
	      if(UserService::valid($data_credentials)){
	       $audit_master_id=$var['audit_master_id'];
	       $approval_status=$var['approval_status'];
	       $comments=$var['comments'];
	       $user_id=$var['user_id'];
		sqlStatement("UPDATE audit_master SET approval_status=?, comments=?,modified_time=NOW(),user_id=? WHERE id=? ",array($approval_status,$comments,$user_id,$audit_master_id));
	      }
	      else{
	      throw new SoapFault("Server", "credentials failed in update_audit_master error message");
	      }
       }
    public function update_audited_data($var)
       {
	      $data_credentials=$var[0];
	$validtables = array("patient_data","employer_data","insurance_data","history_data","openemr_postcalendar_events");
        if(UserService::valid($data_credentials)){
	      $audit_master_id = $var['audit_master_id'];
	      $res = sqlStatement("SELECT * FROM  audit_master  where id=? and  approval_status='1' and  type='3' ",array($audit_master_id));
	      if(sqlNumRows($res)>0)//skip this function if type=3(only documents saved.)
		   {
		    return;
		   }
	      $res = sqlStatement("SELECT DISTINCT ad.table_name,am.id,am.pid FROM audit_master as am,audit_details as ad WHERE am.id=ad.audit_master_id and am.approval_status in ('1','4') and am.id=?",array($audit_master_id));
	      $tablecnt = sqlNumRows($res);
	      while($row = sqlFetchArray($res)){
	        $pid=$row['pid'];
		     $resfield = sqlStatement("SELECT * FROM audit_details WHERE audit_master_id=? AND table_name=?",array($audit_master_id,$row['table_name']));
		     $table = $row['table_name'];
		     $cnt = 0;
		     foreach($validtables as $value){//Update will execute if and only if all tables are validtables
			    if($value==$table)
			    $cnt++;
		     }
		     if($cnt>0){
			    while($rowfield = sqlFetchArray($resfield)){

				  if($table=='patient_data'){
					$newdata['patient_data'][$rowfield['field_name']]=$rowfield['field_value'];
				  }
				
				  if($table=='employer_data'){
					$newdata['employer_data'][$rowfield['field_name']]=$rowfield['field_value'];
				  }

				  if($table=='insurance_data'){
					$ins1_type="primary";
					$ins2_type="secondary";
					$ins3_type="tertiary";
					for($i=1;$i<=3;$i++) 
					{
						$newdata[$rowfield['entry_identification']][$rowfield['field_name']]=$rowfield['field_value'];
					}
				  }
				  
				  if($table=='openemr_postcalendar_events'){
				    $newdata['openemr_postcalendar_events'][$rowfield['field_name']]=$rowfield['field_value'];
				  }

			    }
			    require_once("../../library/invoice_summary.inc.php");
			    require_once("../../library/options.inc.php");
			    require_once("../../library/acl.inc");
			    require_once("../../library/patient.inc");
			    if($table=='patient_data'){
			       $pdrow = sqlQuery("SELECT id from patient_data WHERE pid=?",array($pid));
			       $newdata['patient_data']['id']=$pdrow['id'];
			       updatePatientData($pid,$newdata['patient_data']);
			    }
			    elseif($table=='employer_data'){
			       updateEmployerData($pid,$newdata['employer_data']);
			    }
			    elseif($table=='insurance_data'){
				    for($i=1;$i<=3;$i++){
					    newInsuranceData(
					      $pid,
					      $newdata[${ins.$i._type}]['type'],
					      $newdata[${ins.$i._type}]['provider'],
					      $newdata[${ins.$i._type}]['policy_number'],
					      $newdata[${ins.$i._type}]['group_number'],
					      $newdata[${ins.$i._type}]['plan_name'],
					      $newdata[${ins.$i._type}]['subscriber_lname'],
					      $newdata[${ins.$i._type}]['subscriber_mname'],
					      $newdata[${ins.$i._type}]['subscriber_fname'],
					      $newdata[${ins.$i._type}]['subscriber_relationship'],
					      $newdata[${ins.$i._type}]['subscriber_ss'],
					      $newdata[${ins.$i._type}]['subscriber_DOB'],
					      $newdata[${ins.$i._type}]['subscriber_street'],
					      $newdata[${ins.$i._type}]['subscriber_postal_code'],
					      $newdata[${ins.$i._type}]['subscriber_city'],
					      $newdata[${ins.$i._type}]['subscriber_state'],
					      $newdata[${ins.$i._type}]['subscriber_country'],
					      $newdata[${ins.$i._type}]['subscriber_phone'],
					      $newdata[${ins.$i._type}]['subscriber_employer'],
					      $newdata[${ins.$i._type}]['subscriber_employer_street'],
					      $newdata[${ins.$i._type}]['subscriber_employer_city'],
					      $newdata[${ins.$i._type}]['subscriber_employer_postal_code'],
					      $newdata[${ins.$i._type}]['subscriber_employer_state'],
					      $newdata[${ins.$i._type}]['subscriber_employer_country'],
					      $newdata[${ins.$i._type}]['copay'],
					      $newdata[${ins.$i._type}]['subscriber_sex'],
					      $newdata[${ins.$i._type}]['date'],
					      $newdata[${ins.$i._type}]['accept_assignment']);
				    }
			    }
			    elseif($table=='openemr_postcalendar_events'){
			      sqlInsert("INSERT INTO openemr_postcalendar_events ( " .
				    "pc_pid,pc_title,pc_time,pc_hometext,pc_eventDate,pc_endDate,pc_startTime,pc_endTime,pc_duration,pc_catid,pc_eventstatus,pc_aid,pc_facility" .
				    ") VALUES ( " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_pid']             . "', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_title']           . "', " .
				    "NOW(), "                                         .
				    "'" . $newdata['openemr_postcalendar_events']['pc_hometext'] . "', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_eventDate']          . "', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_endDate']                        . "', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_startTime']     . "', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_endTime']                   . "', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_duration'] . "', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_catid']              . "', " .
				    "1, " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_aid']."', " .
				    "'" . $newdata['openemr_postcalendar_events']['pc_facility']               . "')"
				);
			    }
			 }
		     else{
			    throw new SoapFault("Server", "Table Not Supported error message");
		     }
	      }
	}
	else{
		throw new SoapFault("Server", "credentials failed in updated_audited_data error message");
	}
    }
    public function insert_to_be_audit_data($var)
       {
	     $data_credentials = $var[0];
	 if(UserService::valid($data_credentials))
	      {
		     $audit_master_id_to_delete=$var['audit_master_id_to_delete'];
		     $pid=$var['pid'];
		     $approval_status=$var['approval_status'];
		     $type=$var['type'];
		     $ip_address=$var['ip_address'];
		     $table_name_array=$var['table_name_array'];
		     $field_name_value_array=$var['field_name_value_array'];
		     $entry_identification_array=$var['entry_identification_array'];
		     
		     if($audit_master_id_to_delete){
		     $qry = "DELETE from audit_master WHERE id=?";
		     sqlStatement($qry,array($audit_master_id_to_delete));
		     $qry = "DELETE from audit_details WHERE audit_master_id=?";
		     sqlStatement($qry,array($audit_master_id_to_delete));
		     }
	      
		     $master_query="INSERT INTO audit_master SET
		       pid = '$pid',
		       approval_status = '$approval_status',
		       ip_address = '$ip_address',
		       type = '$type'";
		     $audit_master_id= sqlInsert($master_query);
		     $detail_query="INSERT INTO `audit_details` (`table_name`, `field_name`, `field_value`, `audit_master_id`, `entry_identification`) VALUES ";
		     
		     foreach($table_name_array as $key=>$table_name)
		      {
			     foreach($field_name_value_array[$key] as $field_name=>$field_value)
			      {
				     $detail_query.="('$table_name' ,'".trim($field_name)."' ,'".trim($field_value)."','$audit_master_id' ,'".trim($entry_identification_array[$key])."'),";
			      }
		      }
		     $detail_query = substr($detail_query, 0, -1);
		     $detail_query=$detail_query.';';
		     sqlInsert($detail_query);
		     if($var['auto_update']==1)
		     {
			    $var['audit_master_id'] = $audit_master_id;
			    UserAudit::update_audited_data($var);
		     }
	      }
	     else
	      {
		     throw new SoapFault("Server", "credentials failed in insert_to_be_audit_data error message");
	      }
    }
    public function insert_audit_master($var)
       {
	      $data_credentials=$var[0];
	if(UserService::valid($data_credentials))
		 {
		     $pid=$var['pid'];
		     $approval_status=$var['approval_status'];
		     $type=$var['type'];
		     $ip_address=$var['ip_address'];

		     $master_query="INSERT INTO audit_master SET
		       pid = '$pid',
		       approval_status = '$approval_status',
		       ip_address = '$ip_address',
		       type = '$type'";
		     $audit_master_id= sqlInsert($master_query);
		 }
		else
		 {
			throw new SoapFault("Server", "credentials failed in insert_audit_master error message");
		 }
       }
}
?>