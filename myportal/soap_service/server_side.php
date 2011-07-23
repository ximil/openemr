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

global $ISSUE_TYPES;
$ignoreAuth=true;
ob_start();

require_once("../../interface/globals.php");
$err = '';
if(!extension_loaded("soap")){
  dl("php_soap.dll");
}

class UserService
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
	       $savedpath="../documents/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	       $savedpath="../documents/unsigned/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	       $savedpath="../documents/signed/";
	       if(is_dir($savedpath));
	       else
	       {
		       mkdir($savedpath,0777);
		       chmod($savedpath, 0777);
	       }
	       $savedpath="../documents/upload/";
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
   public function issue_type($data){
	if($this->valid($data[0])){
	global $ISSUE_TYPES;
	require_once("../../library/lists.inc");
	return $ISSUE_TYPES;
	}
	else{
		throw new SoapFault("Server", "credentials failed in issue_type error message");
	}
    }
    public function print_report($data){
	if($this->valid($data[0])){
	$repArr = $data[1];
	$pid = $data[2];
	$type = $data[3];
	global $ISSUE_TYPES;
	require_once("../../library/forms.inc");
	require_once("../../library/billing.inc");
	require_once("../../library/pnotes.inc");
	require_once("../../library/patient.inc");
	require_once("../../library/options.inc.php");
	require_once("../../library/acl.inc");
	require_once("../../library/lists.inc");
	require_once("../../library/report.inc");
	require_once("../../library/classes/Document.class.php");
	require_once("../../library/classes/Note.class.php");
	require_once("../../library/formatting.inc.php");
	require_once("../../custom/code_types.inc.php");
	     foreach($repArr as $value){
		    ob_start();
		    if($type=="profile"){
		    $this->getIncudes($value,$pid);
		    $out .= ob_get_clean()."|";
		    }
		    else{
		    if($type=='issue')
		    $this->getIid($value,$pid);
		    if($type=='forms')
		    $this->getforms($value,$pid);
		    $out .= ob_get_clean();
		    }
		    
	     }
       return $out;
	}
	else{
		throw new SoapFault("Server", "credentials failed in print_report error message");
	}
    }
    public function print_ccr_report($data){
	if($this->valid($data[0])){
	$ccraction = $data[1];
	$raw = $data[2];
	require_once("../../ccr/createCCR.php");
	      ob_start();
	      createCCR($ccraction,$raw);
		      $html = ob_get_clean();
		      if($ccraction=='viewccd')
		      {
		      
		      $html = preg_replace('/<!DOCTYPE html PUBLIC "-\/\/W3C\/\/DTD HTML 4.01\/\/EN" "http:\/\/www.w3.org\/TR\/html4\/strict.dtd">/','',$html);
		      $pos1 = strpos($html,'body {');
		      $pos2 = strpos($html,'.h1center');
		      $tes = substr("$html",$pos1,($pos2-$pos1));
		      $html = str_replace($tes,'',$html);
		      $html = str_replace('h3>','h2>',$html);
		      $html = base64_encode($html);
		      }
		      else{
		      $pos1 = strpos($html,'*{');
		      $pos2 = strpos($html,'h1');
		      $tes = substr("$html",$pos1,($pos2-$pos1));
		      $html = str_replace($tes,'',$html);
		      }
	return $html;
	}
	else{
		throw new SoapFault("Server", "credentials failed in print_ccr_report error message");
	}
    }
    public function getforms($fId,$pid){
	$GLOBALS['pid'] = $pid;
	$inclookupres = sqlStatement("SELECT DISTINCT formdir FROM forms WHERE pid = ? AND deleted=0",array($pid));
	while($result = sqlFetchArray($inclookupres)) {
	    $formdir = $result['formdir'];
	    if (substr($formdir,0,3) == 'LBF')
	      include_once($GLOBALS['incdir'] . "/forms/LBF/report.php");
	    else
	      include_once($GLOBALS['incdir'] . "/forms/$formdir/report.php");
	}
	$N = 6;
	$inclookupres = sqlStatement("SELECT encounter,form_id,formdir,id FROM forms WHERE pid = ? AND deleted=0
				     AND id IN (".$fId.")",array($pid));
	while($result = sqlFetchArray($inclookupres)) {
	    $form_encounter=$result['encounter'];
	    $form_id=$result['form_id'];
	    $formdir = $result['formdir'];
	    $id=$result['id'];
	    ob_start();
	    if (substr($formdir,0,3) == 'LBF')
	      call_user_func("lbf_report", $pid, $form_encounter, $N, $form_id, $formdir);
	    else
	      call_user_func($formdir . "_report", $pid, $form_encounter, $N, $form_id);
	    $out=ob_get_clean();
	    ?>	<table>
		<tr class=text>
		    <th><?php echo htmlspecialchars($formdir,ENT_QUOTES);?></th>
		</tr>
		</table>
		    <?php echo $out;?>
	    <?php
	}
    }
    public function getIid($val,$pid){
	global $ISSUE_TYPES;
	$inclookupres = sqlStatement("SELECT DISTINCT formdir FROM forms WHERE pid = ? AND deleted=?",array($pid,0));
	while($result = sqlFetchArray($inclookupres)) {
	    $formdir = $result['formdir'];
	    if (substr($formdir,0,3) == 'LBF')
	      include_once($GLOBALS['incdir'] . "/forms/LBF/report.php");
	    else
	      include_once($GLOBALS['incdir'] . "/forms/$formdir/report.php");
	}
	    ?>
	    <tr class=text>
		<td></td>
		<td>
	    <?php
	    $irow = sqlQuery("SELECT type, title, comments, diagnosis FROM lists WHERE id IN (".$val.")");
	    $diagnosis = $irow['diagnosis'];
	    
	    if ($prevIssueType != $irow['type'])
	    {
		$disptype = $ISSUE_TYPES[$irow['type']][0];
		?>
		<div class='issue_type' style='font-weight: bold;'><?php echo htmlspecialchars($disptype,ENT_QUOTES);?>:</div>
		<?php
		$prevIssueType = $irow['type'];
	    }
	    ?>
	    <div class='text issue'>
	    <span class='issue_title'><?php echo htmlspecialchars($irow['title'],ENT_QUOTES);?>:</span>
	    <span class='issue_comments'><?php echo htmlspecialchars($irow['comments'],ENT_QUOTES);?></span>
	    <?php
	    if ($diagnosis)
	    {
		?>
		<div class='text issue_diag'>
		<span class='bold'>[<?php echo htmlspecialchars(xl('Diagnosis'),ENT_QUOTES);?>]</span><br>
		<?php
		$dcodes = explode(";", $diagnosis);
		foreach ($dcodes as $dcode)
		{
		    ?>
		    <span class='italic'><?php echo htmlspecialchars($dcode,ENT_QUOTES);?></span>:
		    <?php
		    echo htmlspecialchars(lookup_code_descriptions($dcode),ENT_QUOTES);
		    ?>
		    <br>
		    <?php
		}
		?>
		</div>
		<?php
	    }
	    if ($irow['type'] == 'ippf_gcac')
	    {
		?>
		<table>
		<?php
		display_layout_rows('GCA', sqlQuery("SELECT * FROM lists_ippf_gcac WHERE id = ?",array($rowid)));
		?>
    
		</table>
		<?php
	    }
	    else if ($irow['type'] == 'contraceptive')
	    {
		?>
		<table>
		    <?php
		display_layout_rows('CON', sqlQuery("SELECT * FROM lists_ippf_con WHERE id = ?",array($rowid)));
		?>
		</table>
		<?php
	    }                    
	   ?>
	    </div>
	    <?php
	    ?>                            
		</td>
	    <?php                        

    }
    public function getIncudes($val,$pid){
	if ($val == "demographics")
	{
	    ?>
	    <hr />
	    <div class='text demographics' id='DEM'>
	    <?php
	    // printRecDataOne($patient_data_array, getRecPatientData ($pid), $N);
	    $result1 = getPatientData($pid);
	    $result2 = getEmployerData($pid);
	    ?>
	    <table>
	    <tr><td><h6><?php echo htmlspecialchars(xl('Patient Data').":",ENT_QUOTES);?></h6></td></tr>
	    <?php
	    display_layout_rows('DEM', $result1, $result2);
	    ?>
	    </table>
	    </div>
	    <?php
	}
	elseif ($val == "history")
	{
	    ?>
	    <hr />
	    <div class='text history' id='HIS'>
		<?php
		$result1 = getHistoryData($pid);
		?>
		<table>
		<tr><td><h6><?php echo htmlspecialchars(xl('History Data').":",ENT_QUOTES);?></h6></td></tr>
		<?php
		display_layout_rows('HIS', $result1);
		?>
		</table>
		</div>
	<?php
	}
	elseif ($val == "insurance")
	{
	    ?>
	    <hr />
	    <div class='text insurance'>";
	    <h6><?php echo htmlspecialchars(xl('Insurance Data').":",ENT_QUOTES);?></h6>
	    <br><span class=bold><?php echo htmlspecialchars(xl('Primary Insurance Data').":",ENT_QUOTES);?></span><br>
	    <?php
	    printRecDataOne($insurance_data_array, getRecInsuranceData ($pid,"primary"), $N);
	    ?>
	    <span class=bold><?php echo htmlspecialchars(xl('Secondary Insurance Data').":",ENT_QUOTES);?></span><br>
	    <?php
	    printRecDataOne($insurance_data_array, getRecInsuranceData ($pid,"secondary"), $N);
	    ?>
	    <span class=bold><?php echo htmlspecialchars(xl('Tertiary Insurance Data').":",ENT_QUOTES);?></span><br>
	    <?php
	    printRecDataOne($insurance_data_array, getRecInsuranceData ($pid,"tertiary"), $N);
	    ?>
	    </div>
	    <?php
	}
	elseif ($val == "billing")
	{
	    ?>
	    <hr />
	    <div class='text billing'>
	    <h6><?php echo htmlspecialchars(xl('Billing Information').":",ENT_QUOTES);?></h6>
	    <?php
	    if (count($ar['newpatient']) > 0) {
		$billings = array();
		?>
		<table>
		<tr><td width='400' class='bold'><?php echo htmlspecialchars(xl('Code'),ENT_QUOTES);?></td><td class='bold'><?php echo htmlspecialchars(xl('Fee'),ENT_QUOTES);?></td></tr>
		<?php
		$total = 0.00;
		$copays = 0.00;
		foreach ($ar['newpatient'] as $be) {
		    $ta = split(":",$be);
		    $billing = getPatientBillingEncounter($pid,$ta[1]);
		    $billings[] = $billing;
		    foreach ($billing as $b) {
			?>
			<tr>
			<td class=text>
			<?php
			echo htmlspecialchars($b['code_type'],ENT_QUOTES) . ":\t" .htmlspecialchars( $b['code'],ENT_QUOTES) . "&nbsp;". htmlspecialchars($b['modifier'],ENT_QUOTES) . "&nbsp;&nbsp;&nbsp;" . htmlspecialchars($b['code_text'],ENT_QUOTES) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			?>
			</td>
			<td class=text>
			<?php
			echo htmlspecialchars(oeFormatMoney($b['fee']),ENT_QUOTES);
			?>
			</td>
			</tr>
			<?php
			$total += $b['fee'];
			if ($b['code_type'] == "COPAY") {
			    $copays += $b['fee'];
			}
		    }
		}
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td class=bold>".htmlspecialchars(xl('Sub-Total'),ENT_QUOTES)."</td><td class=text>" . htmlspecialchars(oeFormatMoney($total + abs($copays)),ENT_QUOTES) . "</td></tr>";
		echo "<tr><td class=bold>".htmlspecialchars(xl('Paid'),ENT_QUOTES)."</td><td class=text>" . htmlspecialchars(oeFormatMoney(abs($copays)),ENT_QUOTES) . "</td></tr>";
		echo "<tr><td class=bold>".htmlspecialchars(xl('Total'),ENT_QUOTES)."</td><td class=text>" .htmlspecialchars(oeFormatMoney($total),ENT_QUOTES) . "</td></tr>";
		echo "</table>";
		echo "<pre>";
		//print_r($billings);
		echo "</pre>";
	    } else {
		printPatientBilling($pid);
	    }
	    echo "</div>\n"; // end of billing DIV
	}
	elseif ($val == "immunizations")
	{
	   
		?>
		<hr />
		<div class='text immunizations'>
		<h6><?php echo htmlspecialchars(xl('Patient Immunization').":",ENT_QUOTES);?></h6>
		<?php
		$sql = "select i1.immunization_id as immunization_id, if(i1.administered_date,concat(i1.administered_date,' - ') ,substring(i1.note,1,20) ) as immunization_data from immunizations i1 where i1.patient_id = ? order by administered_date desc";
		$result = sqlStatement($sql,array($pid));
		while ($row=sqlFetchArray($result)) {
		    echo htmlspecialchars($row{'immunization_data'},ENT_QUOTES);
		    echo generate_display_field(array('data_type'=>'1','list_id'=>'immunizations'), $row['immunization_id']);
		    ?>
		      <br>
		    <?php
		}
		?>
		</div>
		<?php
	   
	}
	elseif ($val == "batchcom")
	{
	    ?>
	    <hr />
	    <div class='text transactions'>
	    <h6><?php htmlspecialchars(xl('Patient Communication sent').":",ENT_QUOTES);?></h6>
	    <?php
	    $sql="SELECT concat( 'Messsage Type: ', batchcom.msg_type, ', Message Subject: ', batchcom.msg_subject, ', Sent on:', batchcom.msg_date_sent ) AS batchcom_data, batchcom.msg_text, concat( users.fname, users.lname ) AS user_name FROM `batchcom` JOIN `users` ON users.id = batchcom.sent_by WHERE batchcom.patient_id=?";
	    $result = sqlStatement($sql,array($pid));
	    while ($row=sqlFetchArray($result)) {
		echo htmlspecialchars($row{'batchcom_data'}.", ".xl('By').": ".$row{'user_name'},ENT_QUOTES);
		?>
		<br><?php echo htmlspecialchars(xl('Text'),ENT_QUOTES);?>:<br><?php echo htmlspecialchars($row{'msg_txt'},ENT_QUOTES);?><br>
		<?php
	    }
	    ?>
	    </div>
	    <?php
	}
	elseif ($val == "notes")
	{
	    ?>
	    <hr />
	    <div class='text notes'>
	    <h6><?php echo htmlspecialchars(xl('Patient Notes').":",ENT_QUOTES);?></h6>
	    <?php
	    printPatientNotes($pid);
	    ?>
	    </div>
	    <?php
	}
	elseif ($val == "transactions")
	{
	    ?>
	    <hr />
	    <div class='text transactions'>
	    <h6><?php echo htmlspecialchars(xl('Patient Transactions').":",ENT_QUOTES);?></h6>
	    <?php
	    printPatientTransactions($pid);
	    ?>
	    </div>
	    <?php
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
	elseif($func=='insert_audited_data')
	 {
	        array_unshift($var,$data_credentials);
		UserService::insert_audited_data($var);
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
	}
	else{
		throw new SoapFault("Server", "credentials failed in batch_despatch error message");
	}
    }
    public function delete_if_new_patient($var)
       {
	      $data_credentials=$var[0];
	if(UserService::valid($data_credentials)){
		$pid = $var['pid'];
		 $qry = "select * from audit_master WHERE pid=? and approval_status=1 and type=1";
		 $result=sqlStatement($qry,array($pid));
		 $rowfield = sqlFetchArray($result);
		 if($rowfield['pid']>0)
		  {
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
		        $qry = "DELETE from audit_details WHERE audit_master_id in (select id from audit_master WHERE pid=? and approval_status=1)";
		        sqlStatement($qry,array($pid));
			$qry = "DELETE from audit_master WHERE pid=? and approval_status=1";
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
	 $pid=$var['pid'];
	 $approval_status=$var['approval_status'];
	 $comments=$var['comments'];
	 $user_id=$var['user_id'];
	  sqlStatement("UPDATE audit_master SET approval_status=?, comments=?,modified_time=NOW(),user_id=? WHERE pid=? and approval_status='1' ",array($approval_status,$comments,$user_id,$pid));
	}
	else{
	throw new SoapFault("Server", "credentials failed in update_audit_master error message");
	}
    }
    public function update_audited_data($var)
       {
	      $data_credentials=$var[0];
	$validtables = array("patient_data","employer_data","insurance_data","history_data");
        if(UserService::valid($data_credentials)){
	      $auditmasterid = $var['auditmasterid'];
	      $res = sqlStatement("SELECT DISTINCT ad.table_name,am.pid FROM audit_master as am,audit_details as ad WHERE am.id=ad.audit_master_id and ad.audit_master_id=?",array($auditmasterid));
	      $tablecnt = sqlNumRows($res);
	      while($row = sqlFetchArray($res)){
		     $resfield = sqlStatement("SELECT * FROM audit_details WHERE audit_master_id=? AND table_name=?",array($auditmasterid,$row['table_name']));
		     $table = $row['table_name'];
		     $cnt = 0;
		     foreach($validtables as $value){//Update will execute if and only if all tables are validtables
			    if($value==$table)
			    $cnt++;
		     }
		     if($cnt==$tablecnt){
			    while($rowfield = sqlFetchArray($resfield)){
				  if($fields)
				  $fields .= ",".trim($rowfield['field_name']) ."='".trim($rowfield['field_value'])."'";
				  else
				  $fields .= trim($rowfield['field_name']) ."='".trim($rowfield['field_value'])."'";
				
				  if($table=='insurance_data'){
					$arr[$rowfield['entry_identification']][$rowfield['field_name']]=$rowfield['field_value'];
				  }
				  else{
				    $arr[trim($rowfield['field_name'])]=trim($rowfield['field_value']);
				  }
			    }
			    require_once("../../library/invoice_summary.inc.php");
			    require_once("../../library/options.inc.php");
			    require_once("../../library/acl.inc");
			    require_once("../../library/patient.inc");
			    if($table=='patient_data'){
			       $pdrow = sqlQuery("SELECT id from patient_data WHERE pid=?",array($row['pid']));
			       $arr['id']=$pdrow['id'];
			       updatePatientData($row['pid'],$arr);
			    }
			    elseif($table=='employer_data'){
			       updateEmployerData($row['pid'],$arr);
			    }
			    elseif($table=='insurance_data'){
				    for($i=1;$i<=3;$i++){
					    newInsuranceData(
					      $row['pid'],
					      $arr[$i]['type'],
					      $arr[$i]['provider'],
					      $arr[$i]['policy_number'],
					      $arr[$i]['group_number'],
					      $arr[$i]['plan_name'],
					      $arr[$i]['subscriber_lname'],
					      $arr[$i]['subscriber_mname'],
					      $arr[$i]['subscriber_fname'],
					      $arr[$i]['subscriber_relationship'],
					      $arr[$i]['subscriber_ss'],
					      $arr[$i]['subscriber_DOB'],
					      $arr[$i]['subscriber_street'],
					      $arr[$i]['subscriber_postal_code'],
					      $arr[$i]['subscriber_city'],
					      $arr[$i]['subscriber_state'],
					      $arr[$i]['subscriber_country'],
					      $arr[$i]['subscriber_phone'],
					      $arr[$i]['subscriber_employer'],
					      $arr[$i]['subscriber_employer_street'],
					      $arr[$i]['subscriber_employer_city'],
					      $arr[$i]['subscriber_employer_postal_code'],
					      $arr[$i]['subscriber_employer_state'],
					      $arr[$i]['subscriber_employer_country'],
					      $arr[$i]['copay'],
					      $arr[$i]['subscriber_sex'],
					      $arr[$i]['date'],
					      $arr[$i]['accept_assignment']);
				    }
			    }
			    elseif($table=='history_data'){
			    //
			    }
			    else{
				   sqlStatement("UPDATE $table SET $fields WHERE pid=?",array($row['pid']));
			    }
		     UserService::update_audit_master(array('pid'=>$row['pid'],'approval_status'=>2),$data_credentials);
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
    public function insert_audited_data($var)
       {
	     $data_credentials = $var[0];
	 if(UserService::valid($data_credentials))
	      {
		     $pid=$var['pid'];
		     $approval_status=$var['approval_status'];
		     $type=$var['type'];
		     $ip_address=$var['ip_address'];
		     $table_name_array=$var['table_name_array'];
		     $field_name_value_array=$var['field_name_value_array'];
		     $entry_identification_array=$var['entry_identification_array'];
		     

		     $qry = "select * from audit_master WHERE pid=? and approval_status=1";
		     $result=sqlStatement($qry,array($pid));
		     $rowfield = sqlFetchArray($result);
		     $qry = "DELETE from audit_master WHERE id=?";
		     sqlStatement($qry,array($rowfield['id']));
		     $qry = "DELETE from audit_details WHERE audit_master_id=?";
		     sqlStatement($qry,array($rowfield['id']));

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
	      }
	     else
	      {
		     throw new SoapFault("Server", "credentials failed in insert_audited_data error message");
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
   public function update_password($data){
       if($this->valid($data[0])){
	       $set = $data[1];
	       $where = $data[2];
	       $qry = "UPDATE  patient_access_offsite SET $set WHERE $where";
	       sqlStatement($qry);
       }
       else{
	       throw new SoapFault("Server", "credentials failed in update_password error message");
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
session_start();
$server = new SoapServer(null,array('uri' => "urn://portal/res"));
$server->setClass('UserService');
$server->setPersistence(SOAP_PERSISTENCE_SESSION);
$server->handle();
?>