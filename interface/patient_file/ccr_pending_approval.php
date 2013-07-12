<?php
/**
 * interface/patient_file/ccr_pending_approval.php Approval screen for uploaded CCR XML.
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
require_once("../../library/options.inc.php");
require_once("../../library/patient.inc");

function insert_patient($audit_master_id){
	$prow = sqlQuery("SELECT IFNULL(MAX(pid)+1,1) AS pid FROM patient_data");
	$pid = $prow['pid'];
	$res = sqlStatement("SELECT DISTINCT ad.table_name,entry_identification FROM audit_master as am,audit_details as ad WHERE am.id=ad.audit_master_id AND am.approval_status = '1' AND am.id=? AND am.type=11 ORDER BY ad.id",array($audit_master_id));
	$tablecnt = sqlNumRows($res);
	while($row = sqlFetchArray($res)){
		$resfield = sqlStatement("SELECT * FROM audit_details WHERE audit_master_id=? AND table_name=? AND entry_identification=?",array($audit_master_id,$row['table_name'],$row['entry_identification']));
		$table = $row['table_name'];
		$newdata = array();
		while($rowfield = sqlFetchArray($resfield)){
			if($table == 'patient_data'){
				if($rowfield['field_name'] == 'DOB'){
					$newdata['patient_data'][$rowfield['field_name']] = substr($rowfield['field_value'],0,10);
				}else{
					$newdata['patient_data'][$rowfield['field_name']] = $rowfield['field_value'];
				}
			}elseif($table == 'lists1'){
				$newdata['lists1'][$rowfield['field_name']] = $rowfield['field_value'];
			}elseif($table == 'lists2'){
				$newdata['lists2'][$rowfield['field_name']] = $rowfield['field_value'];
			}elseif($table == 'prescriptions'){
				$newdata['prescriptions'][$rowfield['field_name']] = $rowfield['field_value'];
			}elseif($table == 'immunizations'){
				$newdata['immunizations'][$rowfield['field_name']] = $rowfield['field_value'];
			}elseif($table == 'procedure_result'){
				$newdata['procedure_result'][$rowfield['field_name']] = $rowfield['field_value'];
			}elseif($table == 'procedure_type'){
				$newdata['procedure_type'][$rowfield['field_name']] = $rowfield['field_value'];
			}elseif($table == 'misc_address_book'){
				$newdata['misc_address_book'][$rowfield['field_name']] = $rowfield['field_value'];
			}
		}
		if($table == 'patient_data'){
			updatePatientData($pid,$newdata['patient_data'],true);
		}elseif($table == 'lists1'){
			sqlInsert("INSERT INTO lists(".
				"pid,diagnosis,activity".
				") VALUES (".
				"'".add_escape_custom($pid)."',".
				"'".add_escape_custom($newdata['lists1']['diagnosis'])."',".
				"'".add_escape_custom($newdata['lists1']['activity'])."')"
			);
		}elseif($table == 'lists2'){
			sqlInsert("INSERT INTO lists(".
				"pid,date,type,title,reaction".
				") VALUES (".
				"'".add_escape_custom($pid)."',".
				"'".add_escape_custom($newdata['lists2']['date'])."',".
				"'".add_escape_custom($newdata['lists2']['type'])."',".
				"'".add_escape_custom($newdata['lists2']['title'])."',".
				"'".add_escape_custom($newdata['lists2']['reaction'])."')"
			);
		}elseif($table == 'prescriptions'){
			sqlInsert("INSERT INTO prescriptions(".
				"patient_id,date_added,active,drug,size,form,quantity".
				") VALUES (".
				"'".add_escape_custom($pid)."',".
				"'".add_escape_custom($newdata['prescriptions']['date_added'])."',".
				"'".add_escape_custom($newdata['prescriptions']['active'])."',".
				"'".add_escape_custom($newdata['prescriptions']['drug'])."',".
				"'".add_escape_custom($newdata['prescriptions']['size'])."',".
				"'".add_escape_custom($newdata['prescriptions']['form'])."',".
				"'".add_escape_custom($newdata['prescriptions']['quantity'])."')"
			);
		}elseif($table == 'immunizations'){
			sqlInsert("INSERT INTO immunizations(".
				"patient_id,administered_date,note".
				") VALUES (".
				"'".add_escape_custom($pid)."',".
				"'".add_escape_custom($newdata['immunizations']['administered_date'])."',".
				"'".add_escape_custom($newdata['immunizations']['note'])."')"
			);
		}elseif($table == 'procedure_result'){
			/*sqlInsert("INSERT INTO procedure_result(".
				"date,result,abnormal".
				") VALUES (".
				"'".add_escape_custom($newdata['procedure_result']['date'])."',".
				"'".add_escape_custom($newdata['procedure_result']['result'])."',".
				"'".add_escape_custom($newdata['procedure_result']['abnormal'])."')"
			);*/
		}elseif($table == 'procedure_type'){
			/*sqlInsert("INSERT INTO procedure_type(".
				"name".
				") VALUES (".
				"'".add_escape_custom($newdata['procedure_type']['name'])."')"
			);*/
		}elseif($table == 'misc_address_book'){
			sqlInsert("INSERT INTO misc_address_book(".
				"lname,fname,street,city,state,zip,phone".
				") VALUES (".
				"'".add_escape_custom($newdata['misc_address_book']['lname'])."',".
				"'".add_escape_custom($newdata['misc_address_book']['fname'])."',".
				"'".add_escape_custom($newdata['misc_address_book']['street'])."',".
				"'".add_escape_custom($newdata['misc_address_book']['city'])."',".
				"'".add_escape_custom($newdata['misc_address_book']['state'])."',".
				"'".add_escape_custom($newdata['misc_address_book']['zip'])."',".
				"'".add_escape_custom($newdata['misc_address_book']['phone'])."')"
			);
		}
	}
	sqlQuery("UPDATE audit_master SET approval_status=2 WHERE id=?",array($audit_master_id));
}

if($_REQUEST['approve'] == 1){
	insert_patient($_REQUEST['am_id']);
?>
  <html>
		<head>
			<title><?php echo xlt('CCR Approve');?></title>
			<link rel="stylesheet" href="<?php echo xl($css_header,'e');?>" type="text/css" >
		</head>
		<body class="body_top" >
			<center><?php echo xlt('Approved Successfully'); ?></center>
		</body>
	</html>
	<?php
	exit;
}

?>
<html>
<head>
<title><?php echo xlt('Pending Approval');?></title>
<link rel="stylesheet" href="<?php echo xl($css_header,'e');?>" type="text/css">
<style>

table {
	color: #000;
	font: .8em/1.6em "Trebuchet MS",Verdana,sans-serif;
	border-collapse: collapse;
	margin: 0 auto;
	border: 1px solid #CCC;
}

tbody th,td {
	border-left: 0;
	padding: 8px
}

tbody{
	background: rgb(255,255,255); /* Old browsers */
	background: -moz-linear-gradient(top, rgba(255,255,255,1) 0%, rgba(229,229,229,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,1)), color-stop(100%,rgba(229,229,229,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, rgba(255,255,255,1) 0%,rgba(229,229,229,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, rgba(255,255,255,1) 0%,rgba(229,229,229,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top, rgba(255,255,255,1) 0%,rgba(229,229,229,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom, rgba(255,255,255,1) 0%,rgba(229,229,229,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
}

tbody th {
	color: #3e3e3e;
	padding: 5px 10px;
	background: #f5f6f6; /* Old browsers */
	background: -moz-linear-gradient(top, #f5f6f6 0%, #dbdce2 21%, #b8bac6 49%, #dddfe3 80%, #f5f6f6 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f5f6f6), color-stop(21%,#dbdce2), color-stop(49%,#b8bac6), color-stop(80%,#dddfe3), color-stop(100%,#f5f6f6)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #f5f6f6 0%,#dbdce2 21%,#b8bac6 49%,#dddfe3 80%,#f5f6f6 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #f5f6f6 0%,#dbdce2 21%,#b8bac6 49%,#dddfe3 80%,#f5f6f6 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top, #f5f6f6 0%,#dbdce2 21%,#b8bac6 49%,#dddfe3 80%,#f5f6f6 100%); /* IE10+ */
	background: linear-gradient(to bottom, #f5f6f6 0%,#dbdce2 21%,#b8bac6 49%,#dddfe3 80%,#f5f6f6 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f5f6f6', endColorstr='#f5f6f6',GradientType=0 ); /* IE6-9 */
	border-bottom: 1px solid;
}

tbody tr.odd {
	background-color: #F7F7F7;
	color: #666
}

.button-link {
	padding: 3px 10px;
	background: #c0c0c0;
	color: #000 !important;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	border: solid 1px #000000;
	-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4), 0 1px 1px rgba(0, 0, 0, 0.2);
	-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4), 0 1px 1px rgba(0, 0, 0, 0.2);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4), 0 1px 1px rgba(0, 0, 0, 0.2);
	-webkit-transition-duration: 0.2s;
	-moz-transition-duration: 0.2s;
	transition-duration: 0.2s;
	-webkit-user-select:none;
	-moz-user-select:none;
	-ms-user-select:none;
	user-select:none;
}

.button-link:hover {
	background: #808080;
	border: solid 1px #000000;
	text-decoration: none;
	color: #FFF !important;
}

</style>
<script type="text/javascript" >
  
</script>
</head>
<body class="body_top" >
<center>
<p><b><?php echo xlt('Pending Approval');?></b></p>
</center>
<form method="post" name="approve" >
<center>
<table style="width:80%;" border="0" >
	<tr>
		<th>
			<?php echo xlt('Patient Name'); ?>
		</th>
		<th>
			<?php echo xlt('Match Found'); ?>
		</th>
		<th>
			<?php echo xlt('Action'); ?>
		</th>
	</tr>
	<?php
	$query = sqlStatement("SELECT *,am.id amid,CONCAT(ad.field_value,' ',ad1.field_value) as pat_name FROM audit_master am JOIN audit_details ad ON
		ad.audit_master_id = am.id AND ad.table_name = 'patient_data' AND ad.field_name = 'lname' JOIN audit_details ad1 ON
		ad1.audit_master_id = am.id AND ad1.table_name = 'patient_data' AND ad1.field_name = 'fname' WHERE type='11' AND approval_status='1'");
	if(sqlNumRows($query) > 0){
		while($res = sqlFetchArray($query)){
		$dup_query = sqlStatement("SELECT * FROM audit_master am JOIN audit_details ad ON ad.audit_master_id = am.id AND ad.table_name = 'patient_data'
			AND ad.field_name = 'lname' JOIN audit_details ad1 ON ad1.audit_master_id = am.id AND ad1.table_name = 'patient_data' AND
			ad1.field_name = 'fname' JOIN audit_details ad2 ON ad2.audit_master_id = am.id AND ad2.table_name = 'patient_data' AND ad2.field_name = 'DOB'
			JOIN patient_data pd ON pd.lname = ad.field_value AND pd.fname = ad1.field_value AND pd.DOB = DATE(ad2.field_value) WHERE am.id = ?",
		array($res['amid']));
	?>
	<tr>
		<td class="bold" >
			<?php echo $res['pat_name']; ?>
		</td>
			<?php
			if(sqlNumRows($dup_query)>0){
				$dup_res = sqlFetchArray($dup_query);
			?>
		<td align="center" class="bold" >
			Yes
		</td>
		<td align="center" >
			<a href="ccr_review_approve.php?revandapprove=1&amid=<?php echo $res['amid']; ?>&pid=<?php echo $dup_res['pid']; ?>" class="button-link" ><?php echo xlt('Review')." & ".xlt('Approve'); ?></a>
		</td>
		<?php
			}else{
		?>
		<td align="center" class="bold" >
			No
		</td>
		<td align="center" >
			<a href="<?php echo $_SERVER['PHP_SELF'] ?>?approve=1&am_id=<?php echo $res['amid']; ?>" class="button-link" ><?php echo xlt('Approve'); ?></a>
		</td>
		<?php
			}
		?>
	</tr>
	<?php
		}
	}else{
	?>
		<tr>
			<td colspan="3" >
				<?php echo xlt('Nothing Pending for Approval')."."; ?>
			</td>
		</tr>
	<?php
	}
?>
</table>
</center>
</form>
</body>
</html>
