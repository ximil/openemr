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
include_once("../../globals.php");
include_once($GLOBALS["srcdir"] . "/api.inc");

function cancer_diagnosis_report($pid, $encounter, $cols, $id) {
    $count = 0;
    $data = sqlQuery("SELECT * FROM `form_cancer_diagnosis` WHERE id=? AND pid = ? ORDER BY DATE DESC LIMIT 0,1", array($id,$GLOBALS['pid']));
    if ($data) {
        print "<table style='border-collapse:collapse;border-spacing:0;width: 100%;'>";
        foreach ($data as $key => $value) {
            if ($key == "id" || $key == "pid" || $key == 'encounter' || $key == "user" || $key == 'authorized') {
                continue;
            }
            $key = ucwords(str_replace("_", " ", $key));
            if($key=='Status') $value = ($value==1)?'Active':'Inactive';
            print "<tr><td style='border:1px solid #ccc;padding:4px;'><span class=bold>" . xlt($key) . ": </span></td><td style='border:1px solid #ccc;padding:4px;'><span class=text>" . text($value) . "</span></td></tr>";;
            $count++;
            if ($count == $cols) {
                $count = 0;
                print "\n";
            }
        }
        print "</table>";
    }
}
?> 
