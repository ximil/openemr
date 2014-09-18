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

function care_plan_report($pid, $encounter, $cols, $id) {
    $count = 0;
    //$data = formFetch("form_care_plan", $id);
    $sql = "SELECT * FROM `form_care_plan` WHERE pid = ? AND encounter = ?";
    $res = sqlStatement($sql, array($_SESSION["pid"], $_SESSION["encounter"]));

    for ($iter = 0; $row = sqlFetchArray($res); $iter++)
        $data[$iter] = $row;
    if ($data) {
        print "<table class='ccda_listing' width='100%' border='1'>
            <tr>
                <th align='center' style='font-size: 14px;'>".text('Code')."</th>
                <th align='center' style='font-size: 14px;'>".text('Code Text')."</th>
                <th align='center' style='font-size: 14px;'>".text('Description')."</th> 
                <th align='center' style='font-size: 14px;'>".text('Date')."</th>
            </tr>";
        foreach ($data as $key => $value) {
            print "<tr>
                        <td><span class=text>".text($value['code'])."</span></td>
                        <td><span class=text>".text($value['codetext'])."</span></td>
                        <td><span class=text>".text($value['description'])."</span></td>
                        <td><span class=text>".text($value['date'])."</span></td>
                    </tr>";
            print "\n";
        }
        print "</table>";
    }
}
?> 
