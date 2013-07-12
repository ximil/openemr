<?php
/**
 * interface/patient_file/ccr_import.php Upload screen and parser for the CCR XML.
 *
 * Functions to upload the CCR XML and to parse and insert it into audit tables.
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

function parseXmlFile($file,$field_mapping){
	$res = array();
	$content = file_get_contents($file);
	$xml = new DOMDocument;
	$xml->loadXML($content);
	$xpath = new DOMXpath($xml);
	$rootNamespace = $xml->lookupNamespaceUri($xml->namespaceURI);
	$xpath->registerNamespace('x',$rootNamespace);
	foreach($field_mapping as $skey=>$sval){
		$path = preg_replace("/\/([a-zA-Z])/","/x:$1",$skey);
		$elements = $xpath->query($path);
		if(!is_null($elements)){
			$ele_cnt = 1;
			foreach($elements as $element){
				foreach($sval as $field => $innerpath){
					$ipath = preg_replace(array("/^([a-zA-Z])/","/\/([a-zA-Z])/"),array("x:$1","/x:$1"),$innerpath);
					$val = $xpath->query($ipath, $element)->item(0)->textContent;
					if($val){
            $field_details = explode(':',$field);
						$res[$field_details[0]][$ele_cnt][$field_details[1]] = $val;
					}
				}
				$ele_cnt++;
			}
		}
	}
	return $res;
}

function insert_ccr_into_audit_data($var){
  $audit_master_id_to_delete = $var['audit_master_id_to_delete'];
  $approval_status = $var['approval_status'];
  $type = $var['type'];
  $ip_address = $var['ip_address'];
  $field_name_value_array = $var['field_name_value_array'];
  $entry_identification_array = $var['entry_identification_array'];
  if($audit_master_id_to_delete){
    $qry = "DELETE from audit_details WHERE audit_master_id=?";
    sqlStatement($qry,array($audit_master_id_to_delete));
    $qry = "DELETE from audit_master WHERE id=?";
    sqlStatement($qry,array($audit_master_id_to_delete));
  }
  $master_query = "INSERT INTO audit_master SET pid = ?,approval_status = ?,ip_address = ?,type = ?";
  $audit_master_id = sqlInsert($master_query,array(0,$approval_status,$ip_address,$type));
  $detail_query = "INSERT INTO `audit_details` (`table_name`, `field_name`, `field_value`, `audit_master_id`, `entry_identification`) VALUES ";
  $detail_query_array = '';
  foreach($field_name_value_array as $key=>$val){
    foreach($field_name_value_array[$key] as $cnt=>$field_details){
      foreach($field_details as $field_name=>$field_value){
        $detail_query .= "(? ,? ,? ,? ,?),";
        $detail_query_array[] = $key;
        $detail_query_array[] = trim($field_name);
        $detail_query_array[] = trim($field_value);
        $detail_query_array[] = $audit_master_id;
        $detail_query_array[] = trim($entry_identification_array[$key][$cnt]);
      }
    }
  }
  $detail_query = substr($detail_query, 0, -1);
  $detail_query = $detail_query.';';
  sqlInsert($detail_query,$detail_query_array);
}

?>
<html>
<head>
<title>Import CCR XML</title>
<link rel="stylesheet" href="<?php echo xl($css_header,'e');?>" type="text/css">
<script type="text/javascript" >
  function check_ext(ele){
    var ext = ele.value.match(/\.(.+)$/)[1];
    if(ext != 'xml'){
      alert("<?php echo xlt('Please select an XML file'); ?>");
      ele.value = '';
      return false;
    }
  }
</script>
</head>
<body class="body_top" >
<?php
  $errmsg = '';
  if($_POST["form_submit"] || $_POST["form_delete"]){
    $tempdir = $GLOBALS['OE_SITE_DIR']."/temp";
    if($_FILES['xmlfile']['type'] != 'text/xml'){
      die("Please upload an XML file");
    }
    if(!file_exists($tempdir))
      mkdir($tempdir);
      // Check if the upload worked.
    if(!$errmsg){
      if(!is_uploaded_file($_FILES['xmlfile']['tmp_name']))
        $errmsg = "Upload failed! Make sure the path/filename is valid and the file is less than 4,000,000 bytes.";
    }
    // Copy the image to its destination.
    //
    if(!$errmsg){
      $filename = "$tempdir"."/".$_SESSION['authUser'].time().rand(1000,100000).".xml";
      if(!move_uploaded_file($_FILES['xmlfile']['tmp_name'], $filename)){
        $errmsg = "Internal error accessing uploaded file!";
      }
    }
    echo "<script LANGUAGE=\"JavaScript\">\n";
    if($errmsg){
      $errmsg = strtr($errmsg, "\r\n'", "   ");
      echo "window.alert('$errmsg')\n";
      echo "window.back()\n";
    }
    echo "</script>\n</body>\n</html>\n";
    $file = $filename;
    //fields to which the corresponding elements are to be inserted
    //format - level 1 key is the main tag in the XML eg:- //Problems or //Problems/Problem according to the content in the XML.
    //level 2 key is 'table name:field name' and level 2 value is the sub tag under the main tag given in level 1 key
    //eg:- 'Type/Text' if the XML format is '//Problems/Problem/Type/Text' or 'id/@extension' if it is an attribute
    //level 2 key can be 'table name:#some value' for checking whether a particular tag exits in the XML section
    $field_mapping = array(
      '//Problems/Problem' => array(
        'lists1:diagnosis' => 'Description/Code/Value',
        'lists1:comments' => 'CommentID',
        'lists1:activity' => 'Status/Text',
      ),
      '//Alerts/Alert' => array(
        'lists2:type' => 'Type/Text',
        'lists2:diagnosis' => 'Description/Code/Value',
        'lists2:date' => 'Agent/EnvironmentalAgents/EnvironmentalAgent/DateTime/ExactDateTime',
        'lists2:title' => 'Agent/EnvironmentalAgents/EnvironmentalAgent/Description/Text',
        'lists2:reaction' => 'Reaction/Description/Text',
      ),
      '//Medications/Medication' => array(
        'prescriptions:date_added' => 'DateTime/ExactDateTime',
        'prescriptions:active' => 'Status/Text',
        'prescriptions:drug' => 'Product/ProductName/Text',
        'prescriptions:size' => 'Product/Strength/Value',
        'prescriptions:unit' => 'Product/Strength/Units/Unit',
        'prescriptions:form' => 'Product/Form/Text',
        'prescriptions:quantity' => 'Quantity/Value',
        'prescriptions:note' => 'PatientInstructions/Instruction/Text',
        'prescriptions:refills' => 'Refills/Refill/Number',
      ),
      '//Immunizations/Immunization' => array(
        'immunizations:administered_date' => 'DateTime/ExactDateTime',
        'immunizations:note' => 'Directions/Direction/Description/Text',
      ),
      '//Results/Result' => array(
        'procedure_result:date' => 'DateTime/ExactDateTime',
        'procedure_type:name' => 'Test/Description/Text',
        'procedure_result:result' => 'Test/TestResult/Value',
        'procedure_result:range' => 'Test/NormalResult/Normal/Value',
        'procedure_result:abnormal' => 'Test/Flag/Text',
      ),
      '//Actors/Actor' => array(
        'patient_data:fname' => 'Person/Name/CurrentName/Given',
        'patient_data:lname' => 'Person/Name/CurrentName/Family',
        'patient_data:DOB' => 'Person/DateOfBirth/ExactDateTime',
        'patient_data:sex' => 'Person/Gender/Text',
        'patient_data:abname' => 'InformationSystem/Name',
        'patient_data:#Type' => 'InformationSystem/Type',
        'patient_data:pubpid' => 'IDs/ID',
        'patient_data:street' => 'Address/Line1',
        'patient_data:city' => 'Address/City',
        'patient_data:state' => 'Address/State',
        'patient_data:postal_code' => 'Address/PostalCode',
        'patient_data:phone_contact' => 'Telephone/Value',
      ),
    );
    if(file_exists($file)){
      $var = array();
      $res = parseXmlFile($file,$field_mapping);
      $var = array(
        'approval_status' => 1,
        'type' => 11,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
      );
      foreach($res as $sections=>$details){
        foreach($details as $cnt=>$vals){
          foreach($vals as $key=>$val){
            if(array_key_exists('#Type',$res[$sections][$cnt])){
              if($key == 'postal_code'){
                $var['field_name_value_array']['misc_address_book'][$cnt]['zip'] = $val;
              }elseif($key == 'phone_contact'){
                $var['field_name_value_array']['misc_address_book'][$cnt]['phone'] = $val;
              }elseif($key == 'abname'){
                $values = explode(' ',$val);
                if($values[0]){
                  $var['field_name_value_array']['misc_address_book'][$cnt]['lname'] = $values[0];
                }
                if($values[1]){
                  $var['field_name_value_array']['misc_address_book'][$cnt]['fname'] = $values[1];
                }
              }else{
                $var['field_name_value_array']['misc_address_book'][$cnt][$key] = $val;
              }
              $var['entry_identification_array']['misc_address_book'][$cnt] = $cnt;
            }else{
              if($sections == 'lists1' && $key == 'activity'){
                if($val == 'Active'){
                  $val = 1;
                }else{
                  $val = 0;
                }
              }
              if($sections == 'lists2' && $key == 'type'){
                if(strpos($val,"-")){
                  $vals = explode("-",$val);
                  $val = $vals[0];
                }else{
                  $val = "";
                }
              }
              if($sections == 'prescriptions' && $key == 'active'){
                if($val == 'Active'){
                  $val = 1;
                }else{
                  $val = 0;
                }
              }
              $var['field_name_value_array'][$sections][$cnt][$key] = $val;
              $var['entry_identification_array'][$sections][$cnt] = $cnt;
            }
          }
          if(array_key_exists('#Type',$var['field_name_value_array']['misc_address_book'][$cnt])){
            unset($var['field_name_value_array']['misc_address_book'][$cnt]['#Type']);
          }
        }
      }
      insert_ccr_into_audit_data($var);
      echo "<span style='font-size:14px;'><center>".xlt('Successfully Uploaded the details').". ".xlt('Please appove the patient from the Approval Screen')."."."</center></span>";
    }else{
      exit(xlt('Failed to open')." ".$file);
    }
    exit;
  }
?>
<center>
<p><b>Upload CCR XML</b></p>
</center>
<form method="post" name="main" enctype="multipart/form-data" >
<input type="hidden" name="MAX_FILE_SIZE" value="4000000" >
<center>
<table border="0" >
 <tr>
  <td style="font-size:11pt" >
    <?php echo xlt('Upload this file'); ?>:
  </td>
  <td>
    <input type="file" name="xmlfile" id="xmlfile" onchange="return check_ext(this);" />
  </td>
 </tr>
</table>
<p>
<input type="submit" name="form_submit" value="Upload" />
</center>
</form>
</body>
</html>
