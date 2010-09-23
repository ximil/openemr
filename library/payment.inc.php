<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//===============================================================================
//This section handles the common functins of payment screens.
//===============================================================================
function DistributionInsert($CountRow,$created_time,$user_id)
 {//Function inserts the distribution.Payment,Adjustment,Deductable,Takeback & Follow up reasons are inserted as seperate rows.
 //It automatically pushes to next insurance for billing.
 //In the screen a drop down of Ins1,Ins2,Ins3,Pat are given.The posting can be done for any level.
  if (isset($_POST["Payment$CountRow"]) && $_POST["Payment$CountRow"]*1>0)
   {
		if(trim(formData('type_name'   ))=='Insurance')
		 {
		  if(trim(formData("HiddenIns$CountRow"   ))==1)
		   {
			  $AccountCode="IPP";
		   }
		  if(trim(formData("HiddenIns$CountRow"   ))==2)
		   {
			  $AccountCode="ISP";
		   }
		  if(trim(formData("HiddenIns$CountRow"   ))==3)
		   {
			  $AccountCode="ITP";
		   }
		 }
		elseif(trim(formData('type_name'   ))=='Patient')
		 {
		  $AccountCode="PP";
		 }
	  sqlStatement("insert into ar_activity set "    .
		"pid = '"       . trim(formData('hidden_patient_code' )) .
		"', encounter = '"     . trim(formData("HiddenEncounter$CountRow"   ))  .
		"', code = '"      . trim(formData("HiddenCode$CountRow"   ))  .
		"', modifier = '"      . trim(formData("HiddenModifier$CountRow"   ))  .
		"', payer_type = '"   . trim(formData("HiddenIns$CountRow"   )) .
		"', post_time = '"  . trim($created_time					) .
		"', post_user = '" . trim($user_id            )  .
		"', session_id = '"    . trim(formData('payment_id')) .
		"', modified_time = '"  . trim($created_time					) .
		"', pay_amount = '" . trim(formData("Payment$CountRow"   ))  .
		"', adj_amount = '"    . 0 .
		"', account_code = '" . "$AccountCode"  .
		"'");
		if(trim(formData('type_name'   ))!='Patient')
		 {
			$ferow = sqlQuery("select last_level_closed from form_encounter  where 
			pid ='".trim(formData('hidden_patient_code' ))."' and encounter='".trim(formData("HiddenEncounter$CountRow" ))."'");
			//multiple charges can come.
			if($ferow['last_level_closed']<trim(formData("HiddenIns$CountRow"   )))
			 {
				sqlStatement("update form_encounter set last_level_closed='".trim(formData("HiddenIns$CountRow"   ))."' where 
				pid ='".trim(formData('hidden_patient_code' ))."' and encounter='".trim(formData("HiddenEncounter$CountRow" ))."'");
				//last_level_closed gets increased.
				//-----------------------------------
				// Determine the next insurance level to be billed.
				$ferow = sqlQuery("SELECT date, last_level_closed " .
				  "FROM form_encounter WHERE " .
				  "pid = '".trim(formData('hidden_patient_code' ))."' AND encounter = '".trim(formData("HiddenEncounter$CountRow" ))."'");
				$date_of_service = substr($ferow['date'], 0, 10);
				$new_payer_type = 0 + $ferow['last_level_closed'];
				if ($new_payer_type <= 3 && !empty($ferow['last_level_closed']) || $new_payer_type == 0)
				  ++$new_payer_type;
				$new_payer_id = arGetPayerID(trim(formData('hidden_patient_code' )), $date_of_service, $new_payer_type);
				if($new_payer_id>0)
				 {
				arSetupSecondary(trim(formData('hidden_patient_code' )), trim(formData("HiddenEncounter$CountRow" )),0);
				 }
				//-----------------------------------
			 }
		 }
   }
  if (isset($_POST["AdjAmount$CountRow"]) && $_POST["AdjAmount$CountRow"]*1>0)
   {
		if(trim(formData('type_name'   ))=='Insurance')
		 {
		  $AdjustString="Ins adjust Ins".trim(formData("HiddenIns$CountRow"   ));
		  $AccountCode="IA";
		 }
		elseif(trim(formData('type_name'   ))=='Patient')
		 {
		  $AdjustString="Pt adjust";
		  $AccountCode="PA";
		 }


	  idSqlStatement("insert into ar_activity set "    .
		"pid = '"       . trim(formData('hidden_patient_code' )) .
		"', encounter = '"     . trim(formData("HiddenEncounter$CountRow"   ))  .
		"', code = '"      . trim(formData("HiddenCode$CountRow"   ))  .
		"', modifier = '"      . trim(formData("HiddenModifier$CountRow"   ))  .
		"', payer_type = '"   . trim(formData("HiddenIns$CountRow"   )) .
		"', post_time = '"  . trim($created_time					) .
		"', post_user = '" . trim($user_id            )  .
		"', session_id = '"    . trim(formData('payment_id')) .
		"', modified_time = '"  . trim($created_time					) .
		"', pay_amount = '" . 0  .
		"', adj_amount = '"    . trim(formData("AdjAmount$CountRow"   )) .
		"', memo = '" . "$AdjustString"  .
		"', account_code = '" . "$AccountCode"  .
		"'");
   }
  if (isset($_POST["Deductible$CountRow"]) && $_POST["Deductible$CountRow"]*1>0)
   {
	  idSqlStatement("insert into ar_activity set "    .
		"pid = '"       . trim(formData('hidden_patient_code' )) .
		"', encounter = '"     . trim(formData("HiddenEncounter$CountRow"   ))  .
		"', code = '"      . trim(formData("HiddenCode$CountRow"   ))  .
		"', modifier = '"      . trim(formData("HiddenModifier$CountRow"   ))  .
		"', payer_type = '"   . trim(formData("HiddenIns$CountRow"   )) .
		"', post_time = '"  . trim($created_time					) .
		"', post_user = '" . trim($user_id            )  .
		"', session_id = '"    . trim(formData('payment_id')) .
		"', modified_time = '"  . trim($created_time					) .
		"', pay_amount = '" . 0  .
		"', adj_amount = '"    . 0 .
		"', memo = '"    . "Deductable $".trim(formData("Deductible$CountRow"   )) .
		"', account_code = '" . "Deduct"  .
		"'");
   }
  if (isset($_POST["Takeback$CountRow"]) && $_POST["Takeback$CountRow"]*1>0)
   {
	  idSqlStatement("insert into ar_activity set "    .
		"pid = '"       . trim(formData('hidden_patient_code' )) .
		"', encounter = '"     . trim(formData("HiddenEncounter$CountRow"   ))  .
		"', code = '"      . trim(formData("HiddenCode$CountRow"   ))  .
		"', modifier = '"      . trim(formData("HiddenModifier$CountRow"   ))  .
		"', payer_type = '"   . trim(formData("HiddenIns$CountRow"   )) .
		"', post_time = '"  . trim($created_time					) .
		"', post_user = '" . trim($user_id            )  .
		"', session_id = '"    . trim(formData('payment_id')) .
		"', modified_time = '"  . trim($created_time					) .
		"', pay_amount = '" . trim(formData("Takeback$CountRow"   ))*-1  .
		"', adj_amount = '"    . 0 .
		"', account_code = '" . "Takeback"  .
		"'");
   }
  if (isset($_POST["FollowUp$CountRow"]) && $_POST["FollowUp$CountRow"]=='y')
   {
	  idSqlStatement("insert into ar_activity set "    .
		"pid = '"       . trim(formData('hidden_patient_code' )) .
		"', encounter = '"     . trim(formData("HiddenEncounter$CountRow"   ))  .
		"', code = '"      . trim(formData("HiddenCode$CountRow"   ))  .
		"', modifier = '"      . trim(formData("HiddenModifier$CountRow"   ))  .
		"', payer_type = '"   . trim(formData("HiddenIns$CountRow"   )) .
		"', post_time = '"  . trim($created_time					) .
		"', post_user = '" . trim($user_id            )  .
		"', session_id = '"    . trim(formData('payment_id')) .
		"', modified_time = '"  . trim($created_time					) .
		"', pay_amount = '" . 0  .
		"', adj_amount = '"    . 0 .
		"', follow_up = '"    . "y" .
		"', follow_up_note = '"    . trim(formData("FollowUpReason$CountRow"   )) .
		"'");
   }
}
//===============================================================================
  // Delete rows, with logging, for the specified table using the
  // specified WHERE clause.  Borrowed from deleter.php.
  //
  function row_delete($table, $where) {
    $tres = sqlStatement("SELECT * FROM $table WHERE $where");
    $count = 0;
    while ($trow = sqlFetchArray($tres)) {
      $logstring = "";
      foreach ($trow as $key => $value) {
        if (! $value || $value == '0000-00-00 00:00:00') continue;
        if ($logstring) $logstring .= " ";
        $logstring .= $key . "='" . addslashes($value) . "'";
      }
      newEvent("delete", $_SESSION['authUser'], $_SESSION['authProvider'], 1, "$table: $logstring");
      ++$count;
    }
    if ($count) {
      $query = "DELETE FROM $table WHERE $where";
      sqlStatement($query);
    }
  }
//===============================================================================
?>