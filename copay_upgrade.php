<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2012 Z&H Consultancy Services Private Limited <sam@zhservices.com>
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
//           Ajil P M <ajilpm@zhservices.com> 
//
// +------------------------------------------------------------------------------+
//===============================================================================
//This section handles the moving of copay from billing table to ar_session and
// ar_activity tables.
//===============================================================================
ini_set('max_execution_time', '0');

$ignoreAuth = true; // no login required

require_once('interface/globals.php');
require_once('library/sql.inc');

// Force logging off
$GLOBALS["enable_auditlog"]=0;

//moving copay from billing table and saves into ar_session and ar_activity tables
$billing_copay = sqlStatement("SELECT * FROM billing where code_type='COPAY' and billing.activity!=0");
while($billing_copay_res = sqlFetchArray($billing_copay)){
  $billing_copay_res['fee'] = $billing_copay_res['fee'] * -1;
  $billing_code = sqlStatement("SELECT * FROM billing LEFT JOIN code_types ON billing.code_type=code_types.ct_key WHERE ".
    "code_types.ct_fee=1 AND pid=? AND encounter=? AND billing.activity!=0 LIMIT 1",
    array($billing_copay_res['pid'],$billing_copay_res['encounter']));
  $billing_code_res = array();
  if(sqlNumRows($billing_code) > 0){
    $billing_code_res = sqlFetchArray($billing_code);
  }
  $session_id = idSqlStatement("INSERT INTO ar_session(payer_id,user_id,pay_total,payment_type,description,".
    "patient_id,payment_method,adjustment_code,post_to_date) VALUES ('0',?,?,'patient','COPAY',?,'cash','patient_payment',?)",
    array($billing_copay_res['user'],$billing_copay_res['fee'],$billing_copay_res['pid'],$billing_copay_res['date']));
  if($session_id){
    if(!$billing_code_res['code']){
      $billing_code_res['code'] = '';
    }
    if(!$billing_code_res['modifier']){
      $billing_code_res['modifier'] = '';
    }
    SqlStatement("INSERT INTO ar_activity (pid,encounter,code,modifier,payer_type,post_time,post_user,session_id,".
      "pay_amount,account_code) VALUES (?,?,?,?,0,?,?,?,?,'PCP')",
      array($billing_copay_res['pid'],$billing_copay_res['encounter'],$billing_code_res['code'],$billing_code_res['modifier'],
      $billing_copay_res['date'],$billing_copay_res['user'],$session_id,$billing_copay_res['fee']));
    $insert_check = sqlStatement("SELECT * FROM ar_activity WHERE pid=? AND encounter=? AND code=? AND modifier=? ".
      "AND payer_type='0' AND post_time=? AND post_user=? AND session_id=? AND pay_amount=? AND account_code='PCP'",
      array($billing_copay_res['pid'],$billing_copay_res['encounter'],$billing_code_res['code'],$billing_code_res['modifier'],
      $billing_copay_res['date'],$billing_copay_res['user'],$session_id,$billing_copay_res['fee']));
    if(sqlNumRows($insert_check) > 0){
      sqlStatement("UPDATE billing SET activity=0 WHERE id=?",
        array($billing_copay_res['id']));
    }else{
      $fh = fopen("./copay_upgrade_error.log","a");
      fwrite($fh,"Not updating the activity to 0 for the copay entry in billing table with id : ".$billing_copay_res['id']
        " because an entry in ar_activity table corresponding to ar_session_id :$session_id could not be found\n");
    }
  }
}

?>