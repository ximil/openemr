<?php
global $ISSUE_TYPES;
$ignoreAuth=true;
ob_start();
$pid = $_GET['pid'];
require_once("../../interface/globals.php");
if($GLOBALS['portal_activity_enable']!=1 || $GLOBALS['portal_activity_username']!=$_REQUEST['username'] || sha1($GLOBALS['portal_activity_password'])!=$_REQUEST['password']){
       return false;
       die;
}
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
require_once("../../ccr/createCCR.php");
require_once("../../custom/code_types.inc.php");   
$GLOBALS['pid']=$_GET['pid'];
ob_clean();
function text_to_xml($text)
 {//Converts a text to xml format.Format is as follows
 
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
function query_to_xml_result($sql_query)
 {//Accepts a select query string.It queries the database and returns the result in xml format.Format is as follows
 
  $doc = new DOMDocument();
  $doc->formatOutput = true;
  
  $root = $doc->createElement( "root" );
  $doc->appendChild( $root );
 
  $sql_result_set = mysql_query($sql_query);
  while($row = mysql_fetch_array($sql_result_set,MYSQL_ASSOC))
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
function delete_file($file_name_with_path)
 {
  @unlink($file_name_with_path);
 }
function store_to_file($file_name_with_path,$data)
 {
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
function file_to_xml($file_name_with_path)
 {//Accepts a file path.Fetches the file in xml format.Format is as follows
 
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
function execute_query($sql_query)
 {
  $sql_result_set = mysql_query($sql_query);
 }
function issue_type(){
      global $ISSUE_TYPES;
      return $ISSUE_TYPES;
}
function print_report($repArr,$pid,$type){
       foreach($repArr as $value){
	      ob_start();
	      if($type=="profile"){
	      getIncudes($value,$pid);
	      $out .= ob_get_clean()."|";
	      }
	      else{
	      if($type=='issue')
	      getIid($value,$pid);
	      if($type=='forms')
	      getforms($value,$pid);
	      $out .= ob_get_clean();
	      }
	      
       }
       return $out;
}
function print_ccr_report($ccraction,$raw){
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
		$html = htmlentities($html);
		}
		else{
		$pos1 = strpos($html,'*{');
		$pos2 = strpos($html,'h1');
		$tes = substr("$html",$pos1,($pos2-$pos1));
		$html = str_replace($tes,'',$html);
		}
        return $html;
}
function getforms($fId,$pid){
       $GLOBALS['pid'] = $pid;
       $inclookupres = sqlStatement("SELECT DISTINCT formdir FROM forms WHERE pid = '$pid' AND deleted=0");
       while($result = sqlFetchArray($inclookupres)) {
	   $formdir = $result['formdir'];
	   if (substr($formdir,0,3) == 'LBF')
	     include_once($GLOBALS['incdir'] . "/forms/LBF/report.php");
	   else
	     include_once($GLOBALS['incdir'] . "/forms/$formdir/report.php");
       }
       $N = 6;
       $inclookupres = sqlStatement("SELECT encounter,form_id,formdir,id FROM forms WHERE pid = '$pid' AND deleted=0
				    AND id IN (".$fId.")");
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
		   <td></td>
		   <th><?php echo $formdir;?></th>
	       </tr>
	       <tr id="<?php echo $form_id?>" class=text>
		   <td></td>
		   <td><?php echo $out;?></td>
	       </tr>
	       </table>
	   <?php
       }
}
function getIid($val,$pid)
{
    global $ISSUE_TYPES;
    $inclookupres = sqlStatement("SELECT DISTINCT formdir FROM forms WHERE pid = '$pid' AND deleted=0");
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
	$irow = sqlQuery("SELECT type, title, comments, diagnosis FROM lists WHERE id IN ('$val')");
        $diagnosis = $irow['diagnosis'];
        
        if ($prevIssueType != $irow['type'])
        {
            $disptype = $ISSUE_TYPES[$irow['type']][0];
            echo "<div class='issue_type' style='font-weight: bold;'>" . $disptype . ":</div>\n";
            $prevIssueType = $irow['type'];
        }
        echo "<div class='text issue'>";
        echo "<span class='issue_title'>" . $irow['title'] . ":</span>";
        echo "<span class='issue_comments'> " . $irow['comments'] . "</span>\n";
        if ($diagnosis)
        {
            echo "<div class='text issue_diag'>";
            echo "<span class='bold'>[".xl('Diagnosis')."]</span><br>";
            $dcodes = explode(";", $diagnosis);
            foreach ($dcodes as $dcode)
            {
                echo "<span class='italic'>".$dcode."</span>: ";
                echo lookup_code_descriptions($dcode)."<br>\n";
            }
            echo "</div>";
        }
        if ($irow['type'] == 'ippf_gcac')
        {
            echo "   <table>\n";
            display_layout_rows('GCA', sqlQuery("SELECT * FROM lists_ippf_gcac WHERE id = '$rowid'"));
            echo "   </table>\n";
        }
        else if ($irow['type'] == 'contraceptive')
        {
            echo "   <table>\n";
            display_layout_rows('CON', sqlQuery("SELECT * FROM lists_ippf_con WHERE id = '$rowid'"));
            echo "   </table>\n";
        }                    
        echo "</div>\n";                
        ?>                            
            </td>
        <?php                        

}
function getIncudes($val,$pid)
{
    if ($val == "demographics")
    {
        echo "<hr />";
        echo "<div class='text demographics' id='DEM'>\n";
        
        // printRecDataOne($patient_data_array, getRecPatientData ($pid), $N);
        $result1 = getPatientData($pid);
        $result2 = getEmployerData($pid);
        echo "   <table>\n";
	echo "<tr><td><h6>".xl('Patient Data').":</h6></td></tr>";
        display_layout_rows('DEM', $result1, $result2);
        echo "   </table>\n";
        echo "</div>\n";            
    }
    elseif ($val == "history")
    {
        echo "<hr />";
        echo "<div class='text history' id='HIS'>\n";
        
            $result1 = getHistoryData($pid);
            echo "   <table>\n";
	    echo "<tr><td><h6>".xl('History Data').":</h6></td></tr>";
            display_layout_rows('HIS', $result1);
            echo "   </table>\n";
        
        echo "</div>";
    }
    elseif ($val == "insurance")
    {
        echo "<hr />";
        echo "<div class='text insurance'>";
        echo "<h6>".xl('Insurance Data').":</h6>";
        echo "<br><span class=bold>".xl('Primary Insurance Data').":</span><br>";
        printRecDataOne($insurance_data_array, getRecInsuranceData ($pid,"primary"), $N);		
        echo "<span class=bold>".xl('Secondary Insurance Data').":</span><br>";	
        printRecDataOne($insurance_data_array, getRecInsuranceData ($pid,"secondary"), $N);
        echo "<span class=bold>".xl('Tertiary Insurance Data').":</span><br>";
        printRecDataOne($insurance_data_array, getRecInsuranceData ($pid,"tertiary"), $N);
        echo "</div>";
    }
    elseif ($val == "billing")
    {
        echo "<hr />";
        echo "<div class='text billing'>";
        echo "<h6>".xl('Billing Information').":</h6>";
        if (count($ar['newpatient']) > 0) {
            $billings = array();
            echo "<table>";
            echo "<tr><td width='400' class='bold'>Code</td><td class='bold'>".xl('Fee')."</td></tr>\n";
            $total = 0.00;
            $copays = 0.00;
            foreach ($ar['newpatient'] as $be) {
                $ta = split(":",$be);
                $billing = getPatientBillingEncounter($pid,$ta[1]);
                $billings[] = $billing;
                foreach ($billing as $b) {
                    echo "<tr>\n";
                    echo "<td class=text>";
                    echo $b['code_type'] . ":\t" . $b['code'] . "&nbsp;". $b['modifier'] . "&nbsp;&nbsp;&nbsp;" . $b['code_text'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo "</td>\n";
                    echo "<td class=text>";
                    echo oeFormatMoney($b['fee']);
                    echo "</td>\n";
                    echo "</tr>\n";
                    $total += $b['fee'];
                    if ($b['code_type'] == "COPAY") {
                        $copays += $b['fee'];
                    }
                }
            }
            echo "<tr><td>&nbsp;</td></tr>";
            echo "<tr><td class=bold>".xl('Sub-Total')."</td><td class=text>" . oeFormatMoney($total + abs($copays)) . "</td></tr>";
            echo "<tr><td class=bold>".xl('Paid')."</td><td class=text>" . oeFormatMoney(abs($copays)) . "</td></tr>";
            echo "<tr><td class=bold>".xl('Total')."</td><td class=text>" . oeFormatMoney($total) . "</td></tr>";
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
       
            echo "<hr />";
            echo "<div class='text immunizations'>\n";
            echo "<h6>".xl('Patient Immunization').":</h6>";
            $sql = "select i1.immunization_id as immunization_id, if(i1.administered_date,concat(i1.administered_date,' - ') ,substring(i1.note,1,20) ) as immunization_data from immunizations i1 where i1.patient_id = '$pid' order by administered_date desc";
            $result = sqlStatement($sql);
            while ($row=sqlFetchArray($result)) {
                echo $row{'immunization_data'} .
                  generate_display_field(array('data_type'=>'1','list_id'=>'immunizations'), $row['immunization_id']) .
                  "<br>\n";
            }
            echo "</div>\n";
       
    }
    elseif ($val == "batchcom")
    {
        echo "<hr />";
        echo "<div class='text transactions'>\n";
        echo "<h6>".xl('Patient Communication sent').":</h6>";
        $sql="SELECT concat( 'Messsage Type: ', batchcom.msg_type, ', Message Subject: ', batchcom.msg_subject, ', Sent on:', batchcom.msg_date_sent ) AS batchcom_data, batchcom.msg_text, concat( users.fname, users.lname ) AS user_name FROM `batchcom` JOIN `users` ON users.id = batchcom.sent_by WHERE batchcom.patient_id='$pid'";
        // echo $sql;
        $result = sqlStatement($sql);
        while ($row=sqlFetchArray($result)) {
            echo $row{'batchcom_data'}.", By: ".$row{'user_name'}."<br>Text:<br> ".$row{'msg_txt'}."<br>\n";
        }
        echo "</div>\n";
    }
    elseif ($val == "notes")
    {
        echo "<hr />";
        echo "<div class='text notes'>\n";
        echo "<h6>".xl('Patient Notes').":</h6>";
        printPatientNotes($pid);
        echo "</div>";
    }
    elseif ($val == "transactions")
    {
        echo "<hr />";
        echo "<div class='text transactions'>\n";
        echo "<h6>".xl('Patient Transactions').":</h6>";
        printPatientTransactions($pid);
        echo "</div>";
    }
}
//================================================================================================================
//================================================================================================================
	      //Below portion is SOAP WEB SERVICE SECTION//
//================================================================================================================
//================================================================================================================
$server = new SoapServer(null,array('uri' => "urn://tyler/res"));
$server->addFunction(array('text_to_xml','query_to_xml_result','file_to_xml','execute_query','issue_type','getIncudes','print_report','print_ccr_report','store_to_file'));
$server->addFunction(SOAP_FUNCTIONS_ALL);
$server->handle();
?>