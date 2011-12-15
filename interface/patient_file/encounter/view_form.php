<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

include_once("../../globals.php");
$specialityPath='common_forms';
        $status = sqlQuery("SELECT * FROM registry WHERE directory='".$_GET['formname']."'");
	        if($status['speciality'] != '')
            $specialityPath = $status['speciality'];
if (substr($_GET["formname"], 0, 3) === 'LBF') {
  // Use the List Based Forms engine for all LBFxxxxx forms.
  include_once("$incdir/forms/common_forms/LBF/view.php");
}
else {
  include_once("$incdir/forms/" .$specialityPath.'/'. $_GET["formname"] . "/view.php");
}

$id = $_GET["id"];
?>
