<?php
// Cloned from patient_encounter.php.

include_once("../../globals.php");
include_once("$srcdir/pid.inc");
include_once("$srcdir/encounter.inc");

if (isset($_GET["set_encounter"])) {
 // The billing page might also be setting a new pid.
 if(isset($_GET["set_pid"]))
 {
     $set_pid=$_GET["set_pid"];
 }
 else if(isset($_GET["pid"]))
 {
     $set_pid=$_GET["pid"];
 }
 else
 {
     $set_pid=false;
 }
 if ($set_pid && $set_pid != $_SESSION["pid"]) {
  setpid($set_pid);
 }
 setencounter($_GET["set_encounter"]);
}
?>
<html>
<head>
<?php html_header_show();?>
</head>
<frameset cols="*">
    <?php
		$sql 	= "SELECT mod_name, type FROM modules WHERE mod_active=1";
		$result = sqlStatement($sql);
		$zendEnc = '';
		while ($tmp = sqlFetchArray($result)) {
			if ($tmp['mod_name'] == 'Encounter' && $tmp['type'] == 1) {
				$zendEnc 	= htmlspecialchars($tmp['mod_name'],ENT_QUOTES);
			}
		}
	?>
  <?php if ($zendEnc == 'Encounter') { ?>
     <!--<frame src="forms.php" name="Forms" scrolling="auto">-->
     <frame src="../../modules/zend_modules/public/encounter/show" name="Forms" scrolling="auto">
		<?php } else { ?>
			<frame src="forms.php" name="Forms" scrolling="auto">
		<?php } ?>
</frameset>
</html>
