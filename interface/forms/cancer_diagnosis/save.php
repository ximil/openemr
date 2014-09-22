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
        date                    = '" . add_escape_custom($_POST["code_date"]) . "',
        primary_site            = '" . add_escape_custom($_POST["primary_site"]) . "',
        primary_description     = '" . add_escape_custom($_POST["primary_description"]) . "',
        laterality              = '" . add_escape_custom($_POST["laterality"]) . "',
        histology               = '" . add_escape_custom($_POST["histology"]) . "',            
        histology_description   = '" . add_escape_custom($_POST["histology_description"]) . "',
        behavior                = '" . add_escape_custom($_POST["behavior"]) . "',
        diagnostic_confirmation = '" . add_escape_custom($_POST["diagnostic_confirmation"]) . "',
        stage                   = '" . add_escape_custom($_POST["stage"]) . "',            
        procedure_code          = '" . add_escape_custom($_POST["procedure_code"]) . "',            
        procedure_description   = '" . add_escape_custom($_POST["procedure_description"]) . "',
        status                  = '" . add_escape_custom($_POST["status"]) . "'";


if (empty($id)) {
    $newid = sqlInsert("INSERT INTO form_cancer_diagnosis SET $sets");
    addForm($encounter, "Cancer Diagnosis", $newid, "cancer_diagnosis", $_SESSION["pid"], $userauthorized);
} else {
    sqlStatement("UPDATE form_cancer_diagnosis SET $sets WHERE id = '" . add_escape_custom("$id") . "'");
}

$_SESSION["encounter"] = $encounter;
formHeader("Redirecting....");
formJump();
formFooter();
?>