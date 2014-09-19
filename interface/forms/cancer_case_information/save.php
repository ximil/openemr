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
$sanitize_all_escapes = $_POST['true'];

//STOP FAKE REGISTER GLOBALS
$fake_register_globals = $_POST['false'];

include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");
require_once("$srcdir/formdata.inc.php");

if (!$encounter) { // comes from globals.php
    die(xl("Internal error: we do not seem to be in an encounter!"));
}

$id = 0 + (isset($_GET['id']) ? $_GET['id'] : '');

$sets = "pid                    = {$_SESSION["pid"]},
        encounter               = '" . $_SESSION["encounter"] . "',
        user                    = '" . $_SESSION["authUser"] . "',
        authorized              = $userauthorized,
        digital_rectal_exam     = '" . add_escape_custom($_POST["digital_rectal_exam"]) . "',
        sextant_biopsy          = '" . add_escape_custom($_POST["sextant_biopsy"]) . "',
        diagnostic_tests        = '" . add_escape_custom($_POST["diagnostic_tests"]) . "',
        diagnosis_code          = '" . add_escape_custom($_POST["diagnosis_code"]) . "',            
        diagnosis_description   = '" . add_escape_custom($_POST["diagnosis_description"]) . "',
        chest_xray              = '" . add_escape_custom($_POST["chest_xray"]) . "',
        reported_symptoms       = '" . add_escape_custom($_POST["reported_symptoms"]) . "',
        plan                    = '" . add_escape_custom($_POST["plan"]) . "',            
        history                 = '" . add_escape_custom($_POST["history"]) . "',            
        history_reported_symptoms = '" . add_escape_custom($_POST["history_reported_symptoms"]) . "',
        findings                  = '" . add_escape_custom($_POST["findings"]) . "',            
        procedure_performed       = '" . add_escape_custom($_POST["procedure_performed"]) . "',            
        treatement_provided       = '" . add_escape_custom($_POST["treatement_provided"]) . "',
        date                      = '" . add_escape_custom($_POST["code_date"]) . "'";


if (empty($id)) {
    $newid = sqlInsert("INSERT INTO form_cancer_case_information SET $sets");
    addForm($encounter, "Cancer Case Information", $newid, "cancer_case_information", $_SESSION["pid"], $userauthorized);
} else {
    sqlStatement("UPDATE form_cancer_case_information SET $sets WHERE id = '" . add_escape_custom("$id") . "'");
}

$_SESSION["encounter"] = $encounter;
formHeader("Redirecting....");
formJump();
formFooter();
?>