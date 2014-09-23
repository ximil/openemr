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

formHeader("Form:Cancer Case Information Form");
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');

$obj = $formid ? sqlQuery("SELECT * FROM `form_cancer_case_information` WHERE id=? AND pid = ? ORDER BY DATE DESC LIMIT 0,1", array($formid, $GLOBALS['pid'])) : array();
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
                dlgopen('<?php echo $GLOBALS['webroot'] . "/interface/patient_file/encounter/" ?>find_code_popup.php?codetype=ICD9', '_blank', 700, 400);
            }

            function set_related(codetype, code, selector, codedesc) {
                document.getElementById('diagnosis_code').value = code;
                document.getElementById("diagnosis_description").value = codedesc;
                document.getElementById("displaytext").innerHTML = codedesc;
            }
        </script>
        <p><span class="forms-title" style="margin-left: 5px;"><?php echo xlt('Cancer Case Information Form'); ?></span></p>
        </br>
        <?php echo "<form method='post' name='my_form' " . "action='$rootdir/forms/cancer_case_information/save.php?id=" . attr($formid) . "'>\n"; ?>
        <table id="cancer_case_information" border="0"> 
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Digital rectal exam') ?>:</td>
                <td class="forms">
                    <input type="text" id="digital_rectal_exam" style="width:300px;" name="digital_rectal_exam" class="digital_rectal_exam" value="<?php echo text($obj{"digital_rectal_exam"}); ?>">
                </td>
            </tr> 
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Sextant biopsy') ?>:</td>
                <td class="forms">
                    <input type="text" id="sextant_biopsy" style="width:300px;" name="sextant_biopsy" class="sextant_biopsy" value="<?php echo text($obj{"sextant_biopsy"}); ?>">
                </td>
            </tr> 
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Diagnostic tests') ?>:</td>
                <td class="forms">
                    <input type="text" id="diagnostic_tests" style="width:300px;" name="diagnostic_tests" class="diagnostic_tests" value="<?php echo text($obj{"diagnostic_tests"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Diagnosis') ?>:</td>
                <td class="forms">
                    <input type="text" id="diagnosis_code" style="width:300px;" name="diagnosis_code" class="diagnosis_code" value="<?php echo text($obj{"diagnosis_code"}); ?>" onclick='sel_code(this.id);'>
                    <span id="displaytext" style="font-size:13px;color: blue;" class="displaytext"><?php echo text($obj{"diagnosis_description"}); ?></span>
                    <input type="hidden" id="diagnosis_description" name="diagnosis_description" class="diagnosis_description" value="<?php echo text($obj{"diagnosis_description"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Chest x-ray') ?>:</td>
                <td class="forms">
                    <input type="text" id="chest_xray" style="width:300px;" name="chest_xray" class="chest_xray" value="<?php echo text($obj{"chest_xray"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Reported symptoms') ?>:</td>
                <td class="forms">
                    <input type="text" id="reported_symptoms" style="width:300px;" name="reported_symptoms" class="reported_symptoms" value="<?php echo text($obj{"reported_symptoms"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Plan') ?>:</td>
                <td class="forms">
                    <input type="text" id="plan" name="plan" style="width:300px;" class="plan" value="<?php echo text($obj{"plan"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('History') ?>:</td>
                <td class="forms">
                    <input type="text" id="history" name="history" style="width:300px;" class="history" value="<?php echo text($obj{"history"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Reported symptoms') ?>:</td>
                <td class="forms">
                    <input type="text" id="history_reported_symptoms" style="width:300px;" name="history_reported_symptoms" class="history_reported_symptoms" value="<?php echo text($obj{"history_reported_symptoms"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Findings') ?>:</td>
                <td class="forms">
                    <input type="text" id="findings" name="findings" style="width:300px;" class="findings" value="<?php echo text($obj{"findings"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Procedure performed') ?>:</td>
                <td class="forms">
                    <input type="text" id="procedure_performed" style="width:300px;" name="procedure_performed" class="procedure_performed" value="<?php echo text($obj{"procedure_performed"}); ?>">
                </td>
            </tr>
            <tr class="tb_row" id="tb_row">
                <td align="left" class="forms"><?php echo xlt('Treatment provided') ?>:</td>
                <td class="forms">
                    <input type="text" id="treatement_provided" style="width:300px;" name="treatement_provided" class="treatement_provided" value="<?php echo text($obj{"treatement_provided"}); ?>">
                </td>
            </tr> 
            <tr>
                <td align="left" class="forms"><?php echo xlt('Date'); ?>:</td>
                <td class="forms">
                    <input type='text' id="code_date" size='10' name='code_date' class="code_date" <?php echo attr($disabled) ?> value='<?php echo attr($obj{"date"}); ?>' title='<?php echo xla('yyyy-mm-dd Date of service'); ?>' onkeyup='datekeyup(this, mypcc)' onblur='dateblur(this, mypcc)' />
                    <img src='../../pic/show_calendar.gif' align='absbottom' id="img_code_date" width='24' height='22' class="img_code_date" border='0' alt='[?]' style='cursor:pointer;cursor:hand' title='<?php echo xla('Click here to choose a date'); ?>'>
                </td>
                <script language="javascript">
                    Calendar.setup({inputField: "code_date", ifFormat: "%Y-%m-%d", button: "img_code_date"});
                </script>
            </tr>
            <tr>
                <td align="left" colspan="5" style="padding-bottom:7px;"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type='submit'  value='<?php echo xlt('Save'); ?>' class="button-css">
                </td>
            </tr>
    </table>
</form>    
<?php
formFooter();
?>
