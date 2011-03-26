<?php
// Copyright (C) 2006-2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

include_once("../../globals.php");
include_once($GLOBALS["srcdir"] . "/api.inc");
function scanned_notes_report($pid, $useless_encounter, $cols, $id) {
 global $webserver_root, $web_root, $encounter;

 // In the case of a patient report, the passed encounter is vital.
 $thisenc = $useless_encounter ? $useless_encounter : $encounter;

 $count = 0;

 $data = sqlQuery("SELECT * FROM form_scanned_notes fs left join documents on fs.document_id=documents.id WHERE " .
  "fs.id = '$id' AND fs.activity = '1'");
 $imagepath = $data['url'];
 $mimetype=$data['mimetype'];

$imagename = basename(preg_replace("|^(.*)://|","",$imagepath));
$imagepath1=$web_root . "/sites/" . $_SESSION['site_id'] ."/documents/$pid/$thisenc/$imagename";


 if ($data) {
  echo "<table cellpadding='0' cellspacing='0'>\n";

  if ($data['notes']) {
   echo " <tr>\n";
   echo "  <td valign='top'><span class='bold'>Comments: </span><span class='text'>";
   echo nl2br($data['notes']) . "</span></td>\n";
   echo " </tr>\n";
  }

    if (is_file($imagepath)) 
	 {
      list($width, $height, $type, $attr) = getimagesize($imagepath);
	  if($mimetype=="application/pdf")
	   {
	    $width=1000;
		$height=1000;
	   }
	  else
	   {
	    $width+=25;
		$height+=25;
	   }
	  echo " <tr>\n";
      echo "  <td valign='top'><span class='bold'>Document: $imagename</span>\n";
		if($mimetype=="image/png" || $mimetype=="image/jpg" || $mimetype=="image/jpeg" || $mimetype=="image/gif" || $mimetype=="image/tiff" || $mimetype=="application/pdf")
		 {
			echo "<iframe frameborder='0' width='$width' height='$height' type='$mimetype' src='" . $GLOBALS['webroot'] . 
							"/controller.php?document&retrieve&patient_id=&document_id=" . $data['document_id'] . "&as_file=false'></iframe>";
		 }
		else
		 {
			echo "<iframe frameborder='0' type='application/octet-stream' width='75%' height='30%' src='" . $GLOBALS['webroot'] . 
							"/controller.php?document&retrieve&patient_id=&document_id=" . $data['document_id'] . "&as_file=true'></iframe>";
		 }
      echo "  </td>";
      echo " </tr>";
     }

  echo "</table>";
 }
}
?>
