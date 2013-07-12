<?php
/**
 * interface/patient_file/ccr_review_approve.php Approval screen for uploaded CCR XML.
 *
 * Approval screen for uploaded CCR XML.
 *
 * Copyright (C) 2013 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Eldho Chacko <eldho@zhservices.com>
 * @author  Ajil P M <ajilpm@zhservices.com>
 * @link    http://www.open-emr.org
 */

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once("../globals.php");

function createAuditArray($am_id,$table_name){
	if(strpos($table_name,',')){
		$tables = explode(',',$table_name);
		$arr = array($am_id);
		$table_qry = "";
		for($i=0;$i<count($tables);$i++){
			$table_qry .= "?,";
			array_unshift($arr,$tables[$i]);
		}
		$table_qry = substr($table_qry,0,-1);
		$query = sqlStatement("SELECT * FROM audit_master am LEFT JOIN audit_details ad ON ad.audit_master_id = am.id AND ad.table_name IN ($table_qry) 
		WHERE am.id = ? AND am.type = 11 AND am.approval_status = 1 ORDER BY ad.entry_identification,ad.field_name",$arr);
	}else{
		$query = sqlStatement("SELECT * FROM audit_master am LEFT JOIN audit_details ad ON ad.audit_master_id = am.id AND ad.table_name = ? 
			WHERE am.id = ? AND am.type = 11 AND am.approval_status = 1 ORDER BY ad.entry_identification,ad.field_name",array($table_name,$am_id));
	}
	$result = array();
	while($res = sqlFetchArray($query)){
		$result[$table_name][$res['entry_identification']][$res['field_name']] = $res['field_value'];
	}
	return $result;
}

function insert_to_table($table_name,$table_fields,array $bind_values){
	$bind_fields = "";
	for($i=0;$i<count($bind_values);$i++){
		$bind_fields .= "?,";
	}
	$bind_fields = substr($bind_fields,0,-1);
	$query = "INSERT INTO $table_name ($table_fields) VALUES ($bind_fields)";
	sqlQuery($query,$bind_values);
}

function update_table($table_name,array $bind_values,$where){
	$set_bind_fields = "";
	foreach($bind_values as $key=>$val){
		$set_bind_fields .= $key." = ?,";
	}
	$set_bind_fields = substr($set_bind_fields,0,-1);
	$query = "UPDATE $table_name SET $set_bind_fields $where";
	sqlQuery($query,$bind_values);
}

$patient_data = array(
	'sex'			 			=> 'Sex',
	'pubpid' 				=> 'External ID',
	'street' 				=> 'Street',
	'city' 					=> 'City',
	'state'					=> 'State',
	'postal_code'		=> 'Postal Code',
);

if($_POST["setval"] == 'Approve'){
	foreach($_REQUEST as $key=>$val){
		if(substr($key,-4) == '-sel'){
			if(is_array($val)){
				for($i=0;$i<count($val);$i++){
					if($val[$i] == 'insert'){
						if(substr($key,0,-4) == 'lists1'){
							if($_REQUEST['lists1-activity'][$i] == 'Active'){
								$activity = 1;
							}elseif($_REQUEST['lists1-activity'][$i] == 'Inactive'){
								$activity = 0;
							}
							insert_to_table('lists','pid,diagnosis,activity',array($_REQUEST['pid'],$_REQUEST['lists1-diagnosis'][$i],$activity));
						}elseif(substr($key,0,-4) == 'lists2'){
							insert_to_table('lists','pid,date,type,title,reaction',array($_REQUEST['pid'],$_REQUEST['lists2-date'][$i],$_REQUEST['lists2-type'][$i],$_REQUEST['lists2-title'][$i],$_REQUEST['lists2-reaction'][$i]));
						}elseif(substr($key,0,-4) == 'prescriptions'){
							if($_REQUEST['prescriptions-active'][$i] == 'Active'){
								$active = 1;
							}elseif($_REQUEST['prescriptions-active'][$i] == 'Inactive'){
								$active = 0;
							}
							insert_to_table('prescriptions','patient_id,date_added,active,drug,size,form,quantity',array($_REQUEST['pid'],$_REQUEST['prescriptions-date_added'][$i],$active,$_REQUEST['prescriptions-drug'][$i],$_REQUEST['prescriptions-size'][$i],$_REQUEST['prescriptions-form'][$i],$_REQUEST['prescriptions-quantity'][$i]));
						}elseif(substr($key,0,-4) == 'immunizations'){
							insert_to_table('immunizations','patient_id,administered_date,note',array($_REQUEST['pid'],$_REQUEST['immunizations-administered_date'][$i],$_REQUEST['immunizations-note'][$i]));
						}elseif(substr($key,0,-4) == 'procedure_result'){
							insert_to_table('procedure_type','name',array($_REQUEST['procedure_type-name'][$i]));
							insert_to_table('procedure_result','date,result,abnormal',array($_REQUEST['procedure_result-date'][$i],$active,$_REQUEST['procedure_result-abnormal'][$i]));
						}
					}elseif($val[$i] == 'update'){
						if(substr($key,0,-4) == 'lists1'){
							if($_REQUEST['lists1-activity'][$i] == 'Active'){
								$activity = 1;
							}elseif($_REQUEST['lists1-activity'][$i] == 'Inactive'){
								$activity = 0;
							}
							update_table('lists',array('diagnosis'=>$_REQUEST['lists1-diagnosis'][$i],'activity'=>$activity),"WHERE pid='".$_REQUEST['pid']."' AND diagnosis='".$_REQUEST['lists1-old-diagnosis'][$i]."'");
						}
					}
				}
			}else{
				if(substr($key,0,12) == 'patient_data'){
					if($val == 'update'){
						$var_name = substr($key,0,-4);
						$field_name = substr($var_name,13);
						$patient_data_array[$field_name] = $_REQUEST[$var_name];
					}
				}
			}
		}
	}
	if(count($patient_data_array) > 0){
		update_table('patient_data',$patient_data_array,"WHERE pid='".$_REQUEST['pid']."'");
	}
	update_table("audit_master",array('approval_status' => 2),"where id = ".$_REQUEST['amid']);
	?>
	<html>
		<head>
			<title>CCR Review and Approve</title>
			<link rel="stylesheet" href="<?php echo xl($css_header,'e');?>" type="text/css" >
		</head>
		<body class="body_top" >
			<center><?php echo xlt('Approved Successfully'); ?></center>
		</body>
	</html>
	<?php
	exit;
}elseif($_POST["setval"] == 'Discard'){
	update_table("audit_master",array('approval_status' => 3),"where id = ".$_REQUEST['amid']);
	?>
	<html>
		<head>
			<title>CCR Review and Approve</title>
			<link rel="stylesheet" href="<?php echo xl($css_header,'e');?>" type="text/css" >
		</head>
		<body class="body_top" >
			<center><?php echo xlt('Discarded'); ?></center>
		</body>
	</html>
	<?php
	exit;
}

?>
<html>
<head>
<title>CCR Review and Approve</title>
<link rel="stylesheet" href="<?php echo xl($css_header,'e');?>" type="text/css" >
<style>

table {
	color: #000;
	font: .85em/1.6em "Trebuchet MS",Verdana,sans-serif;
	border-collapse: collapse;
	margin: 0 auto;
	border: 1px solid #CCC;
}

tbody th,td {
	border-left: 0;
	padding: 8px;
}

tbody {
	background: #D4D4D4;
}

table table tbody tr {
	background: #EEEEEE;
}

</style>
<script type="text/javascript" >

function submit_form(val){
	//alert(val);
	document.getElementById('setval').value = val;
	document.forms['approveform'].submit();
}

</script>
</head>
<body class="body_top" >
<center>
<p><b>CCR Patient Review</b></p>
</center>
<form method="post" name="approveform" >
	<table border="0" width="90%;" >
		<tr>
			<td>
				<u><?php echo xlt('Demographics'); ?></u>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="95%" >
					<tr>
						<?php
							$query_pd = sqlStatement("SELECT ad.id as adid, table_name, field_name, field_value FROM audit_master am JOIN audit_details ad ON ad.audit_master_id = am.id
								WHERE am.id = ? AND ad.table_name = 'patient_data' ORDER BY ad.id",array($_REQUEST['amid']));
							$i = 0;
							while($res_pd = sqlFetchArray($query_pd)){
								if($res_pd['field_name'] != 'lname' && $res_pd['field_name'] != 'fname' && $res_pd['field_name'] != 'DOB'){
									$i++;
									$query_oldpd = sqlQuery("SELECT ".$res_pd['field_name']." AS val FROM patient_data WHERE pid = ?",array($_REQUEST['pid']));
									if($res_pd['field_name'] == 'sex'){
										echo "<td>".($patient_data[$res_pd['field_name']] ? $patient_data[$res_pd['field_name']]: $res_pd['field_name'])."</td>
											<td><select name='".$res_pd['table_name']."-".$res_pd['field_name']."' style='width:150px;' ><option value='Male' ".($res_pd['field_value'] == 'Male' ? 'selected' : '' )." >Male</option>
											<option value='Female' ".($res_pd['field_value'] == 'Female' ? 'selected' : '' )." >Female</option></select><span style='color:red;padding-left:25px;' >".$query_oldpd['val']."</span></td>
											<td><select name='".$res_pd['table_name']."-".$res_pd['field_name']."-sel'><option value='ignore' >Ignore</option><option value='update' >Update</option></select></td>";
									}else{
										echo "<td>".($patient_data[$res_pd['field_name']] ? $patient_data[$res_pd['field_name']]: $res_pd['field_name'])."</td>
											<td><input type='text' name='".$res_pd['table_name']."-".$res_pd['field_name']."' value='".$res_pd['field_value']."' ><span style='color:red;padding-left:25px;' >".$query_oldpd['val']."</span></td>
											<td><select name='".$res_pd['table_name']."-".$res_pd['field_name']."-sel'><option value='ignore' >Ignore</option><option value='update' >Update</option></select></td>";
									}
									if($i%2 == 0){
										echo "</tr><tr>";
									}else{
										echo "<td>&nbsp;&nbsp;&nbsp;</td>";
									}
								}
							}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<u><?php echo xlt('Problems'); ?></u>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="95%" >
					<tr>
						<?php
							$query_existing_prob = sqlStatement("SELECT * FROM lists WHERE pid = ? AND TYPE = 'medical_problem'",array($_REQUEST['pid']));
							$result = array();
							while($res_existing_prob = sqlFetchArray($query_existing_prob)){
								array_push($result,$res_existing_prob);
							}
							$aud_res = createAuditArray($_REQUEST['amid'],'lists1');
							while($res_existing_prob = array_shift($result)){
								if($res_existing_prob['activity'] == 1){
									$activity = 'Active';
								}else{
									$activity = 'Inactive';
								}
								$set = 0;
								foreach($aud_res['lists1'] as $k=>$v){
									if(in_array($res_existing_prob['diagnosis'],$aud_res['lists1'][$k])){
										$set = 1;
										echo "<tr><td>".xlt('Title')."</td><td><input type='text' name='lists1-title[]' value='' ></td>
										<td>".xlt('Code')."</td><td><input type='text' name='lists1-diagnosis[]' value='".$aud_res['lists1'][$k]['diagnosis']."' >
										<input type='hidden' name='lists1-old-diagnosis[]' value='".$res_existing_prob['diagnosis']."' ></td>
										<td>".xlt('Status')."</td><td><input type='text' name='lists1-activity[]' value='".$activity."' ></td><td rowspan='2' >
										<select name='lists1-sel[]'><option value='ignore' >".xlt('Ignore')."</option><option value='update' >".xlt('Update')."</option></select></td></tr>
										<tr style='color:red' ><td>&nbsp;</td><td>".$res_existing_prob['title']."</td><td>&nbsp;</td><td>".$res_existing_prob['diagnosis']."</td>
										<td>&nbsp;</td><td>$activity</td>";
										unset($aud_res['lists1'][$k]);
									}
								}
								if($set == 0){
									echo "<tr><td>".xlt('Title')."</td><td>".$res_existing_prob['title']."</td>
									<td>".xlt('Code')."</td><td>".$res_existing_prob['diagnosis']."</td>
									<td>".xlt('Status')."</td><td>$activity</td><td>&nbsp;</td>";
								}
								echo "</tr>";
							}
							foreach($aud_res['lists1'] as $key=>$val){
								if($val['activity'] == 1){
									$activity = 'Active';
								}else{
									$activity = 'Inactive';
								}
								echo "<tr><td>".xlt('Title')."</td><td><input type='text' name='lists1-title[]' value='' ></td>
									<td>".xlt('Code')."</td><td><input type='text' name='lists1-diagnosis[]' value='".$val['diagnosis']."' ></td>
									<td>".xlt('Status')."</td><td><input type='text' name='lists1-activity[]' value='".$activity."' ></td>
									<td><select name='lists1-sel[]'><option value='ignore' >".xlt('Ignore')."</option><option value='insert' >".xlt('Insert')."</option></select></td></tr>";
							}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<u><?php echo xlt('Allergy'); ?></u>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="95%" >
					<tr>
						<?php
							$query_existing_alerts = sqlStatement("SELECT * FROM lists WHERE pid = ? AND TYPE = 'allergy'",array($_REQUEST['pid']));
							$result = array();
							while($res_existing_alerts = sqlFetchArray($query_existing_alerts)){
								array_push($result,$res_existing_alerts);
							}
							$aud_res = createAuditArray($_REQUEST['amid'],'lists2');
							while($res_existing_alerts = array_shift($result)){
								if($res_existing_alerts['activity'] == 1){
									$activity = 'Active';
								}else{
									$activity = 'Inactive';
								}
								echo "<tr><td>".xlt('Title')."</td><td>".$res_existing_alerts['title']."</td>
								<td>".xlt('Date Time')."</td><td>".$res_existing_alerts['date']."</td>
								<td>".xlt('Reaction')."</td><td>".$res_existing_alerts['reaction']."</td><td>&nbsp;</td></tr>";
							}
							foreach($aud_res['lists2'] as $key=>$val){
								if($val['activity'] == 1){
									$activity = 'Active';
								}else{
									$activity = 'Inactive';
								}
								echo "<tr><td>".xlt('Title')."</td><td><input type='text' name='lists2-title[]' value='".$val['title']."' ></td>
									<td>".xlt('Date Time')."</td><td><input type='text' name='lists2-date[]' value='".$val['date']."' ></td>
									<td>".xlt('Reaction')."</td><td><input type='text' name='lists2-reaction[]' value='".$val['reaction']."' ></td>
									<td><select name='lists2-sel[]'><option value='ignore' >".xlt('Ignore')."</option><option value='insert' >".xlt('Insert')."</option></select></td>
									<input type='hidden' name='lists2-type[]' value='".$val['type']."' >
									</tr>";
							}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<u><?php echo xlt('Medications'); ?></u>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="95%" >
					<tr>
						<?php
							$query_existing_medications = sqlStatement("SELECT * FROM prescriptions WHERE patient_id = ?",array($_REQUEST['pid']));
							$result = array();
							while($res_existing_medications = sqlFetchArray($query_existing_medications)){
								array_push($result,$res_existing_medications);
							}
							$aud_res = createAuditArray($_REQUEST['amid'],'prescriptions');
							while($res_existing_medications = array_shift($result)){
								if($res_existing_medications['active'] == 1){
									$activity = 'Active';
								}else{
									$activity = 'Inactive';
								}
								echo "<tr><td>".xlt('Name')."</td><td>".$res_existing_medications['drug']."</td>
									<td>".xlt('Date')."</td><td>".$res_existing_medications['date_added']."</td>
									<td>".xlt('Status')."</td><td>".$activity."</td><td rowspan='2' >&nbsp;</td></tr><tr><td>".xlt('Form')."</td><td>
									".$res_existing_medications['form']."&nbsp;&nbsp;&nbsp;".xlt('Strength').
									"&nbsp;&nbsp;&nbsp;".$res_existing_medications['size']."</td>
									<td>".xlt('Quantity')."</td><td>".$res_existing_medications['quantity']."</td>
									<td>".xlt('Refills')."</td><td>".$res_existing_medications['refills']."</td></tr>";
							}
							foreach($aud_res['prescriptions'] as $key=>$val){
								if($val['active'] == 1){
									$activity = 'Active';
								}else{
									$activity = 'Inactive';
								}
								echo "<tr><td>".xlt('Name')."</td><td><input type='text' name='prescriptions-drug[]' value='".$val['drug']."' ></td>
									<td>".xlt('Date')."</td><td><input type='text' name='prescriptions-date_added[]' value='".$val['date_added']."' ></td>
									<td>".xlt('Status')."</td><td><input type='text' name='prescriptions-active[]' value='".$activity."' ></td><td rowspan='2' >
									<select name='prescriptions-sel[]'><option value='ignore' >".xlt('Ignore')."</option><option value='insert' >".xlt('Insert')."</option></select></td></tr><tr><td>".xlt('Form')."</td><td>
									<input type='text' size='8' name='prescriptions-form[]' value='".$val['form']."' >&nbsp;&nbsp;&nbsp;".xlt('Strength').
									"&nbsp;&nbsp;&nbsp;<input type='text' size='7' name='prescriptions-size[]' value='".$val['size']."' ></td>
									<td>".xlt('Quantity')."</td><td><input type='text' name='prescriptions-quantity[]' value='".$val['quantity']."' ></td>
									<td>".xlt('Refills')."</td><td><input type='text' name='prescriptions-refills[]' value='".$val['refills']."' ></td></tr>";
							}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<u><?php echo xlt('Immunizations'); ?></u>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="95%" >
					<tr>
						<?php
							$query_existing_immunizations = sqlStatement("SELECT * FROM immunizations WHERE patient_id = ?",array($_REQUEST['pid']));
							$result = array();
							while($res_existing_immunizations = sqlFetchArray($query_existing_immunizations)){
								array_push($result,$res_existing_immunizations);
							}
							$aud_res = createAuditArray($_REQUEST['amid'],'immunizations');
							while($res_existing_immunizations = array_shift($result)){
								echo "<tr><td>".xlt('Administered Date')."</td>
									<td>".$res_existing_immunizations['administered_date']."</td>
									<td>".xlt('Note')."</td><td>".$res_existing_immunizations['note']."</td>
									<td>&nbsp;</td></tr>";
							}
							foreach($aud_res['immunizations'] as $key=>$val){
								echo "<tr><td>".xlt('Administered Date')."</td>
									<td><input type='text' name='immunizations-administered_date[]' value='".$val['administered_date']."' ></td>
									<td>".xlt('Note')."</td><td><input type='text' name='immunizations-note[]' value='".$val['note']."' ></td>
									<td><select name='immunizations-sel[]'><option value='ignore' >".xlt('Ignore')."</option><option value='insert' >".xlt('Insert')."</option></select></td></tr>";
							}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<u><?php echo xlt('Lab Results'); ?></u>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="95%" >
					<tr>
						<?php
							$query_existing_lab_results = sqlStatement("SELECT * FROM procedure_order AS po LEFT JOIN procedure_order_code AS poc
								ON poc.procedure_order_id = po.procedure_order_id LEFT JOIN procedure_report AS pr ON pr.procedure_order_id = po.procedure_order_id
								LEFT JOIN procedure_result AS prs ON prs.procedure_report_id = pr.procedure_report_id WHERE patient_id = ?",array($_REQUEST['pid']));
							$result = array();
							while($res_existing_lab_results = sqlFetchArray($query_existing_lab_results)){
								array_push($result,$res_existing_lab_results);
							}
							$aud_res = createAuditArray($_REQUEST['amid'],'procedure_result,procedure_type');
							while($res_existing_lab_results = array_shift($result)){
								echo "<tr><td>".xlt('Name')."</td>
									<td>".$res_existing_lab_results['result_text']."</td>
									<td>".xlt('Date')."</td><td>".$res_existing_lab_results['date_ordered']."</td>
									<td>".xlt('Result')."</td><td>".$res_existing_lab_results['result']."</td>
									<td>".xlt('Abnormal')."</td><td>".$res_existing_lab_results['abnormal']."</td>
									<td>&nbsp;</td></tr>";
							}
							foreach($aud_res['procedure_result,procedure_type'] as $key=>$val){
								echo "<tr><td>".xlt('Name')."</td>
									<td><input type='text' name='procedure_type-name[]' value='".$val['name']."' ></td>
									<td>".xlt('Date')."</td><td><input type='text' name='procedure_result-date[]' value='".$val['date']."' ></td>
									<td>".xlt('Result')."</td><td><input type='text' name='procedure_result-result[]' value='".$val['result']."' ></td>
									<td>".xlt('Abnormal')."</td><td><input type='text' name='procedure_result-abnormal[]' value='".$val['abnormal']."' ></td>
									<td><select name='procedure_result-sel[]'><option value='ignore' >".xlt('Ignore')."</option><option value='insert' >".xlt('Insert')."</option></select></td></tr>";
							}
						?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" >
				<input type="button" name="approve" value="Approve" onclick="submit_form(this.value);" >
				<input type="button" name="discard" value="Discard" onclick="submit_form(this.value);" >
				<input type="hidden" name="setval" id="setval" value="" >
			</td>
		</tr>
	</table>
</form>
</body>
</html>
