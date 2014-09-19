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
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once($GLOBALS['srcdir'] . '/csv_like_join.php');
require_once($GLOBALS['fileroot'] . '/custom/code_types.inc.php');

formHeader("Form:Cancer Diagnosis Form");
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');

function getOptionList($list_id) {
    $lres = sqlStatement("SELECT * FROM list_options WHERE list_id = ? ORDER BY seq, title", array($list_id));
    while ($lrow = sqlFetchArray($lres)) {
        $all[$lrow['option_id']] = $lrow['title'];
    }
    return $all;
}

$laterality = getOptionList('Cancer_Diagnosis_Laterality');
$behaviour = getOptionList('Cancer_Diagnosis_Behavior');
$confirmation = getOptionList('Cancer_Diagnosis_Confirmation');

$obj = $formid ? sqlQuery("SELECT * FROM `form_cancer_diagnosis` WHERE id=? AND pid = ? ORDER BY DATE DESC LIMIT 0,1", array($formid, $GLOBALS['pid'])) : array();
?>
<html>
    <head>
        <?php html_header_show(); ?>
        <!-- pop up calendar -->
        <style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
        <?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
        <link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
    </head>

    <body class="body_top">
        <script type="text/javascript">

            function sel_code(id)
            {
                document.getElementById('clickId').value = id;
                if (id == 'primary_site')
                {
                    var code = 'SNOMED-CT';
                }
                else if (id == 'procedure_code')
                {
                    var code = 'CPT4';
                }
                else {
                    var code = 'ICD9';
                }
                dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/patient_file/encounter/" ?>find_code_popup.php?codetype=' + code, '_blank', 700, 400);
            }

            function set_related(codetype, code, selector, codedesc) {
                var checkId = document.getElementById('clickId').value;
                if (checkId == 'primary_site')
                {
                    document.getElementById(checkId).value = code;
                    document.getElementById("primary_description").value = codedesc;
                    document.getElementById("displaytext1").innerHTML = codedesc;
                }
                else if (checkId == 'procedure_code')
                {
                    document.getElementById(checkId).value = code;
                    document.getElementById("procedure_description").value = codedesc;
                    document.getElementById("displaytext3").innerHTML = codedesc;
                }
                else {
                    document.getElementById(checkId).value = code;
                    document.getElementById("histology_description").value = codedesc;
                    document.getElementById("displaytext2").innerHTML = codedesc;
                }
            }

            function checkVal()
            {
                if (document.getElementById('status').checked)
                {
                    document.getElementById('status').value = 1;
                }
                else
                {
                    document.getElementById('status').value = 0;
                }
            }
        </script>
        <p><span class="forms-title" style="margin-left: 5px;"><?php echo xlt('Cancer Diagnosis Form'); ?></span></p>
        </br>
        <?php echo "<form method='post' name='my_form' " . "action='$rootdir/forms/cancer_diagnosis/save.php?id=" . attr($formid) . "'>\n"; ?>
        <table id="cancer_diagnosis" border="0"> 
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Procedure Code (CPT)') ?>:</td>
                <td class="forms">
                    <input type="text" id="procedure_code" name="procedure_code" class="procedure_code" value="<?php echo text($obj{"procedure_code"}); ?>" onclick='sel_code(this.id);'>
                    <span id="displaytext3" style="font-size:13px;color: blue;" class="displaytext3"><?php echo text($obj{"procedure_description"}); ?></span>
                    <input type="hidden" id="procedure_description" name="procedure_description" class="procedure_description" value="<?php echo text($obj{"procedure_description"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Status'); ?>:</td>
                <td class="forms">
                    <input type="checkbox" onclick="checkVal();" onload="checkVal();" id="status" name="status" class="status" <?php if ($obj{"status"} == 1) echo "checked='checked'"; ?> value="<?php echo text($obj{"status"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Primary Site'); ?>:</td>
                <td class="forms">
                    <input type="text" id="primary_site" name="primary_site" class="primary_site" value="<?php echo text($obj{"primary_site"}); ?>" onclick='sel_code(this.id);'>
                    <span id="displaytext1" style="font-size:13px;color: blue;" class="displaytext1"><?php echo text($obj{"primary_description"}); ?></span>
                    <input type="hidden" id="primary_description" name="primary_description" class="primary_description" value="<?php echo text($obj{"primary_description"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Laterality'); ?>:</td>
                <td class="forms">
                    <select id="laterality" name="laterality" class="laterality" style="padding:1px 0px;">
                        <?php foreach ($laterality as $key => $val): ?>
                            <option <?php if ($key == $obj{"laterality"}) echo "selected='selected'" ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Histology'); ?>:</td>
                <td class="forms">
                    <input type="text" id="histology" name="histology" class="histology" value="<?php echo text($obj{"histology"}); ?>" onclick='sel_code(this.id);'>
                    <span id="displaytext2" style="font-size:13px;color: blue;" class="displaytext2"><?php echo text($obj{"histology_description"}); ?></span>
                    <input type="hidden" id="histology_description" name="histology_description" class="histology_description" value="<?php echo text($obj{"histology_description"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Behavior'); ?>:</td>
                <td class="forms">
                    <select id="behavior" name="behavior" class="behavior" style="padding:1px 0px;">
                        <?php foreach ($behaviour as $key => $val): ?>
                            <option <?php if ($key == $obj{"behavior"}) echo "selected='selected'" ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Diagnostic Confirmation'); ?>:</td>
                <td class="forms">
                    <select id="diagnostic_confirmation" name="diagnostic_confirmation" class="diagnostic_confirmation" style="padding:1px 0px;">
                        <?php foreach ($confirmation as $key => $val): ?>
                            <option <?php if ($key == $obj{"diagnostic_confirmation"}) echo "selected='selected'" ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Stage'); ?>:</td>
                <td class="forms">
                    <input type="text" id="stage" name="stage" class="stage" value="<?php echo text($obj{"stage"}); ?>">
                </td>
            </tr>            
            <tr>
                <td align="left" class="forms"><?php echo xlt('Date'); ?>:</td>
                <td class="forms">
                    <input type='text' id="code_date" size='10' name='code_date' class="code_date" <?php echo attr($disabled) ?> value='<?php echo attr($obj{"date"}); ?>' title='<?php echo xla('yyyy-mm-dd Date of service'); ?>' onkeyup='datekeyup(this, mypcc)' onblur='dateblur(this, mypcc)' />
                    <img src='../../pic/show_calendar.gif' align='absbottom' id="img_code_date" width='24' height='22' class="img_code_date" border='0' alt='[?]' style='cursor:pointer;cursor:hand' title='<?php echo xla('Click here to choose a date'); ?>'>
                </td>
            <script language="javascript">
                /* required for popup calendar */
                Calendar.setup({inputField: "code_date", ifFormat: "%Y-%m-%d", button: "img_code_date"});
            </script>
        </tr>
        <tr>
            <td align="left" colspan="5" style="padding-bottom:7px;"></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="hidden" id="clickId" value="">
                <input type='submit'  value='<?php echo xlt('Save'); ?>' class="button-css">
            </td>
        </tr>
    </table>
</form>    
<?php
formFooter();
?>
