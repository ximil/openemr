<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

 require_once("../../globals.php");
 require_once("$srcdir/sql.inc");
 
 $row = sqlQuery("SELECT * FROM patient_data WHERE pid=?",array($pid));
 
function generatePassword($length=6, $strength=1) {
	$consonants = 'bdghjmnpqrstvzacefiklowxy';
	$numbers = '0234561789';
	$specials = '@#$%';
	
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length/3; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))].$numbers[(rand() % strlen($numbers))].$specials[(rand() % strlen($specials))];
			$alt = 0;
		} else {
			$password .= $numbers[(rand() % strlen($numbers))].$specials[(rand() % strlen($specials))].$consonants[(rand() % strlen($consonants))];
			$alt = 1;
		}
	}
	return $password;
}
function validEmail($email){
    if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
    return true;
    }
    return false;
}
if(isset($_REQUEST['form_save']) && $_REQUEST['form_save']=='SUBMIT'){
    sqlStatement("UPDATE patient_data SET portal_username=?,portal_pwd=?,portal_pwd_status=? WHERE pid=?",array($_REQUEST['uname'],$_REQUEST['authpwd'],0,$pid));
}
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<?php
include_once("$srcdir/sha1.js");
?>
<script type="text/javascript">
function convertPass(){
    document.getElementById('authpwd').value=SHA1(document.getElementById('pwd').value);
    document.getElementById('form_save').value='SUBMIT';
    document.forms[0].submit();
}
<?php
if(isset($_REQUEST['form_save']) && $_REQUEST['form_save']=='SUBMIT'){
    echo "parent.$.fn.fancybox.close();";
}
?>
</script>
</head>
<body class="body_top">
    <form name="portallogin" action="" method="POST">
    <table align="center" style="margin-top:10px">
        <tr class="text">
            <?php
            $detail='';
            if($row['portal_username'])
            $detail = " Password ";
            else
            $detail = " Username And Password ";
            ?>
            <th colspan="5" align="center"><?php echo htmlspecialchars(xl("Generate".$detail."For ".$row['fname']),ENT_QUOTES);?></th>
        </tr>
        <tr class="text">
            <td><?php echo htmlspecialchars(xl('User Name').':',ENT_QUOTES);?></td>
            <td><input type="text" name="uname" value="<?php if($row['portal_username']) echo $row['portal_username']; else echo htmlspecialchars(strtolower($row['fname']).$row['id']);?>" size="10" readonly></td>
        </tr>
        <tr class="text">
            <td><?php echo htmlspecialchars(xl('Password').':',ENT_QUOTES);?></td>
            <?php
            $pwd = generatePassword();
            ?>
            <input type="hidden" name="authpwd" id="authpwd">
            <td><input type="text" name="pwd" id="pwd" value="<?php echo htmlspecialchars($pwd);?>" size="10" readonly></td>
            <td><a href="#" class="css_button" onclick="javascript:document.location.reload()"><span><?php echo htmlspecialchars(xl('Change'),ENT_QUOTES);?></span></a></td>
        </tr>
        <tr class="text">
            <td><input type="hidden" name="form_save" id="form_save"></td>
            <td colspan="5" align="center"><a href="#" class="css_button" onclick="return convertPass()"><span><?php echo htmlspecialchars(xl('Save'),ENT_QUOTES);?></span></a></td>
        </tr>
    </table>
    </form>
</body>