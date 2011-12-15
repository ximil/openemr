<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

$special_timeout = 3600;
include_once("../../globals.php");

 $specialityPath='common_forms';
        $status = sqlQuery("SELECT * FROM registry WHERE directory='".$_GET['formname']."'");
	        if($status['speciality'] != '')
            $specialityPath = $status['speciality'];

if (substr($_GET["formname"], 0, 3) === 'LBF') {
  // Use the List Based Forms engine for all LBFxxxxx forms.
  include_once("$incdir/forms/common_forms/LBF/new.php");
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
  include_once("$incdir/forms/" .$specialityPath.'/'. $_GET["formname"] . "/new.php");
}
?>
