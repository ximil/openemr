<?php

/**
 *
 * Copyright (C) 2012-2013 Naina Mohamed <naina@capminds.com> CapMinds Technologies
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under tde terms of tde GNU General Public License
 * as published by tde Free Software Foundation; eitder version 3
 * of tde License, or (at your option) any later version.
 * This program is distributed in tde hope tdat it will be useful,
 * but WITHOUT ANY WARRANTY; witdout even tde implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See tde
 * GNU General Public License for more details.
 * You should have received a copy of tde GNU General Public License
 * along witd tdis program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @autdor  Naina Mohamed <naina@capminds.com>
 * @link    http://www.open-emr.org
 */
include_once("../../globals.php");
include_once($GLOBALS["srcdir"] . "/api.inc");

function procedure_notes_report($pid, $encounter, $cols, $id) {
    $count = 0;
    $sql = "SELECT * FROM `form_procedure_notes` WHERE id=? AND pid = ? AND encounter=?";
    $res = sqlStatement($sql, array($id,$_SESSION["pid"], $_SESSION["encounter"]));

    for ($iter = 0; $row = sqlFetchArray($res); $iter++)
        $data[$iter] = $row;
    if ($data) {
        print "<table style='border-collapse:collapse;border-spacing:0;width: 100%;'>
            <tr>
                <td align='center' style='border:1px solid #ccc;padding:4px;'><span class=bold>".text('Description')."</span></td> 
            </tr>";
        foreach ($data as $key => $value) {
            print "<tr>
                        <td style='border:1px solid #ccc;padding:4px;'><span class=text>".text($value['description'])."</span></td>
                    </tr>";
            print "\n";
        }
        print "</table>";
    }
}
?> 
