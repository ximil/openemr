<?php
/**
 *
 * Copyright (C) 2012-2013 Naina Mohamed <naina@capminds.com> CapMinds Technologies
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
 * @author  Naina Mohamed <naina@capminds.com>
 * @link    http://www.open-emr.org
 */
//SANITIZE ALL ESCAPES
$sanitize_all_escapes = true;

//STOP FAKE REGISTER GLOBALS
$fake_register_globals = false;

include_once("../../globals.php");
include_once("$srcdir/api.inc");

formHeader("Form:Progress Notes Form");
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
if ($formid) {
  $sql = "SELECT * FROM `form_progress_notes` WHERE id=? AND pid = ? AND encounter = ?";
  $res = sqlStatement($sql, array($formid,$_SESSION["pid"],$_SESSION["encounter"]));

  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  $check_res = $all;
}

$check_res = $formid ? $check_res : array();
?>
<html>
  <head>
    <?php html_header_show(); ?>
    <link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
  </head>

  <body class="body_top">
    <p><span class="forms-title"><?php echo xlt('Progress Notes Form'); ?></span></p>
    <br> 
    <form method="post" name="my_form" action="<?php echo $rootdir;?>/forms/progress_notes/save.php?id="<?php echo attr($formid);?> >
      <span class=text><?php echo xlt('Description: '); ?></span><br><textarea cols=80 rows=8 wrap=virtual name="description" ></textarea><br>
      <input type='submit'  value='<?php echo xlt('Save'); ?>' class="button-css">
    </form>
<?php
  formFooter();
?>
        