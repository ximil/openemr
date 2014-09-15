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
$code = $_POST["code"];
$code_text = $_POST["codetext"];
$code_date = $_POST["code_date"];
$code_des = $_POST["description"];
$code_activity = $_POST["activity1"];

if ($id && $id != 0) {
    sqlStatement("DELETE FROM `form_functional_cognitive_status` WHERE pid = ? AND encounter = ?", array($_SESSION["pid"], $_SESSION["encounter"]));
    $newid = $id;
} else {
    $res = sqlStatement("SELECT MAX(f1.id) AS largestId FROM `form_functional_cognitive_status` f1 LEFT JOIN `forms` f ON f1.`id`=f.`form_id` WHERE f1.pid = ? 
                         AND f1.encounter = ? AND f.`deleted` != ?", array($_SESSION["pid"], $_SESSION["encounter"], 1));
    $getMaxid = sqlFetchArray($res);
    if ($getMaxid['largestId']) {
        $newid = $getMaxid['largestId'];
    } else {
        $res1 = sqlStatement("SELECT MAX(f1.id) AS largestId FROM `form_functional_cognitive_status` f1 LEFT JOIN `forms` f ON f1.`id`=f.`form_id` WHERE f1.pid = ? 
                         AND f1.encounter = ? AND f.`deleted` = ?", array($_SESSION["pid"], $_SESSION["encounter"], 1));
        $getMaxid1 = sqlFetchArray($res1);
        if ($getMaxid1['largestId']) {
            sqlStatement("DELETE FROM `form_functional_cognitive_status` WHERE pid = ? AND encounter = ?", array($_SESSION["pid"], $_SESSION["encounter"]));
        }
        $res2 = sqlStatement("SELECT MAX(id) as largestId FROM `form_functional_cognitive_status`");
        $getMaxid = sqlFetchArray($res2);
        if ($getMaxid['largestId']) {
            $newid = $getMaxid['largestId'] + 1;
        } else {
            $newid = 1;
        }
    }
}
$code_text = array_filter($code_text);

if (!empty($code_text)) {
    foreach ($code_text as $key => $codeval):
        $sets = "id    = $newid,
            pid        = {$_SESSION["pid"]},
            groupname  = '" . $_SESSION["authProvider"] . "',
            user       = '" . $_SESSION["authUser"] . "',
            encounter  = '" . $_SESSION["encounter"] . "',
            authorized = $userauthorized, 
            activity   = '" . add_escape_custom($code_activity[$key]) . "',
            code       = '" . add_escape_custom($code[$key]) . "',
            codetext   = '" . add_escape_custom($code_text[$key]) . "',
            description= '" . add_escape_custom($code_des[$key]) . "',
            date       =  '" . add_escape_custom($code_date[$key]) . "'";
        sqlInsert("INSERT INTO form_functional_cognitive_status SET $sets");
    endforeach;
    sqlStatement("DELETE FROM forms WHERE pid = ? AND encounter = ? AND formdir=?", array($_SESSION["pid"], $_SESSION["encounter"], 'functional_cognitive_status'));
    addForm($encounter, "Functional and Cognitive Status Form", $newid, "functional_cognitive_status", $pid, $userauthorized);
}
$_SESSION["encounter"] = $encounter;
formHeader("Redirecting....");
formJump();
formFooter();
?>

