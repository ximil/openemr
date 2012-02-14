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
//           Muhammed Basheer   <basheer@zhservices.com>
//
// +------------------------------------------------------------------------------+
//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

include_once("../globals.php");
include_once("$srcdir/sql.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formdata.inc.php");

if($_POST['submit1']==1)
{
  while($specialty=array_shift($_POST['specchk']))
  {
    $specialty_name=substr($specialty,10);
	sqlInsert("INSERT INTO list_options ( " .
                "list_id, option_id, title, seq, is_default, option_value, mapping, notes " .
                ") VALUES ( " .
                "'Specialty', "                       .
                "?, " .
                "?, " .
                "'', " .
                "'', " .
                "'', " .
                "'', " .
                "'' "  .
                ")",array($specialty,formTrim($specialty_name)));
				echo '<script type="text/javascript" language="javascript">parent.location.reload();parent.$.fancybox.close();</script>';
				
  }
}
?><head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" language="javascript">
function popupclose()
{
 parent.$.fancybox.close();
 
}
function toggle(source) {
  field = document.getElementsByName('specchk[]');
  for (i = 0; i < field.length; i++)
	field[i].checked = source.checked ;
}
function addlist()
{
 top.restoreSession();
 document.frm.submit1.value=1;
 document.frm.submit();
}
</script>
</head>

<body class='body_top'">
<form name="frm" action="" method="post">
<input type="hidden" name="submit1" />
<table>
<tr><td align="left"><input type="checkbox" onClick="toggle(this)"></td><td><b><?php echo htmlspecialchars(xl('Specialty Name'),ENT_QUOTES);?></b></td></tr>

<?php

$specArray=array(); // specialty option ids in list options
 $specDir=array();   //form directory list
 
  $path='../forms/';if ($handle = opendir($path)) {			  
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != ".."&& substr($file,0,10) == 'Specialty_') {
						   array_push($specDir,$file);
						   							
						}
					}
				closedir($handle);
				}
$lres = sqlStatement("SELECT * FROM list_options " .
    "WHERE list_id ='Specialty' ORDER BY seq, title");
	while ($lrow = sqlFetchArray($lres)) {
	array_push($specArray,$lrow['option_id']);

	}//end while
	$specDiff=array_diff($specDir,$specArray);
	while($specialty=array_shift($specDiff))
	{
	  $specialty_id=$specialty;
	  $specialty_name=substr($specialty,10);
	  ?>
	  <tr><td width="27" ><input type="checkbox" name="specchk[]" value="<?php echo $specialty_id;?>"></td><td width="326" style="color:#D400AA; font-weight:bold;"><?php echo htmlspecialchars($specialty_name);?></td>
	  </tr>
	  <?php
	}
?>
<tr><td colspan="2"><a href="#" class="css_button_small" onClick="addlist();"><span><?php echo htmlspecialchars(xl('Add to list'),ENT_QUOTES);?></span></a>&nbsp;<a href="#" class="css_button_small" onClick="popupclose();"><span><?php echo htmlspecialchars(xl('Cancel'),ENT_QUOTES);?></span></a></td></tr>
</table>
</form>
</body>
