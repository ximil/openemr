<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

$special_timeout = 3600;
include_once("../../globals.php");
$status = sqlQuery("SELECT * FROM registry WHERE directory='".$_GET['formname']."'");
    if($status['specialty'] != '')
    $specialtyPath = $status['specialty']."/";

if (substr($_GET["formname"], 0, 3) === 'LBF') {
  // Use the List Based Forms engine for all LBFxxxxx forms.
  include_once("$incdir/forms/LBF/new.php");
}
else {
	if( (!empty($_GET['pid'])) && ($_GET['pid'] > 0) )
	 {
		$pid = $_GET['pid'];
		$encounter = $_GET['encounter'];
	 }
         if($_GET["formname"] != "newpatient" ){
            include_once("$incdir/patient_file/encounter/new_form.php");
         }

  // ensure the path variable has no illegal characters
	if($status['specialty'] != ''){
		check_file_dir_name(rtrim($specialtyPath,"/"));
	}
	check_file_dir_name($_GET["formname"]);
	
	$row = sqlQuery("SELECT * FROM forms WHERE pid=? AND encounter=? AND deleted=? AND formdir=?",array($pid,$encounter,0,$_REQUEST["formname"]));
	if($row['id'] && !$status['allow_duplication']){	
	$_GET["id"] = $row['form_id'];
	  include_once("$incdir/forms/".$specialtyPath.$_GET["formname"]."/view.php");
	}
	else{
	  include_once("$incdir/forms/".$specialtyPath.$_GET["formname"]."/new.php");
	}

}
?>
