<?php
require_once("../interface/globals.php");
 $emr_path = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 $emrpatharr = explode("/myportal",$emr_path);
 $emr_path = (!empty($_SERVER['HTTPS'])) ? "https://".$emrpatharr[0] : "http://".$emrpatharr[0];
 $row = sqlQuery("SELECT fname,lname FROM users WHERE id='".$_SESSION['authId']."'");
?>
<html>
<head>
    <?php include_once($GLOBALS['fileroot']."/library/sha1.js");?>
<script type="text/javascript">
 function getshansubmit(){
    var pass = SHA1("<?php echo $GLOBALS['portal_activity_password'];?>");
    document.forms[0].pwd.value = pass;
    document.forms[0].submit();
 }
</script>
</head>
<title>Redirection</title>
<body onload="getshansubmit()">
    <form name="portal" method="post" action="<?php echo $GLOBALS['portal_activity_endpoint'];?>">
    <input type="hidden" name="user" value="<?php echo $GLOBALS['portal_activity_username'];?>">
    <input type="hidden" name="emr_path" value="<?php echo $emr_path;?>">
    <input type="hidden" name="emr_site" value="<?php echo $_SESSION['site_id'];?>">
    <input type="hidden" name="uname" value="<?php echo $row['fname']." ".$row['lname'];?>">
    <input type="hidden" name="pwd" value="">
    </form>
</body>
</html>