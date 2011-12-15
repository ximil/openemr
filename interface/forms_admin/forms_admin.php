<?php 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

//INCLUDES, DO ANY ACTIONS, THEN GET OUR DATA
include_once("../globals.php");
include_once("$srcdir/registry.inc");
include_once("$srcdir/sql.inc");
require_once("$srcdir/options.inc.php");
if ($_GET['method'] == "enable"){
	updateRegistered ( $_GET['id'], "state=1" );
}
elseif ($_GET['method'] == "disable"){
	updateRegistered ( $_GET['id'], "state=0" );
}
elseif ($_GET['method'] == "install_db"){
	$dir = getRegistryEntry ( $_GET['id'], "directory,speciality" );
	$_GET['spec']=$dir['speciality'];
	if (installSQL ("$srcdir/../interface/forms/{$dir['speciality']}/{$dir['directory']}"))
		updateRegistered ( $_GET['id'], "sql_run=1" );
	else
		$err = xl('ERROR: could not open table.sql, broken form?');
}
elseif ($_GET['method'] == "register"){
     
	registerFormspec ( $_GET['name'],$_GET['spec'] ) or $err=xl('error while registering form!');
}
elseif($_POST['register']==1)
{
    $selected_forms=$_POST['unregchk'];
    while($current_form=array_shift($selected_forms))
  {
    $current_form=explode(':',$current_form);
	$speciality_name=$current_form[0];
	$_GET['spec']=$speciality_name;
	$form_name=trim($current_form[1]);
	registerFormspec ($form_name,$speciality_name) or $err=xl('error while registering form!');
  }
}
//complete install
elseif($_POST['fullinstall']==1)
{
 $selected_forms=$_POST['unregchk'];
    while($current_form=array_shift($selected_forms))
  {
    $current_form=explode(':',$current_form);
	$speciality_name=$current_form[0];
	$_GET['spec']=$speciality_name;
	$form_name=trim($current_form[1]);
	$current_form_id=registerFormspec ($form_name,$speciality_name) or $err=xl('error while registering form!');
	
	//install db
	$dir = getRegistryEntry ($current_form_id, "directory,speciality,sql_run" );
	$_GET['spec']=$dir['speciality'];
	if(!$dir['sql_run']) // block mutiple installation
	{
	if (installSQL ("$srcdir/../interface/forms/{$dir['speciality']}/{$dir['directory']}"))
		updateRegistered ($current_form_id, "sql_run=1" );
	else
		$err = xl('ERROR: could not open table.sql, broken form?');
	}
	//end istall db
	 updateRegistered ($current_form_id, "state=1" );
  }
} 
//end complte install
elseif($_POST['installdbflag']==1)
{
 $selected_forms=$_POST['regchk'];
  while($current_form_id=array_shift($selected_forms))
  {
    $dir = getRegistryEntry ($current_form_id, "directory,speciality,sql_run" );
	$_GET['spec']=$dir['speciality'];
	if(!$dir['sql_run']) // block mutiple installation
	{
	if (installSQL ("$srcdir/../interface/forms/{$dir['speciality']}/{$dir['directory']}"))
		updateRegistered ($current_form_id, "sql_run=1" );
	else
		$err = xl('ERROR: could not open table.sql, broken form?');
	}	
  }	
}
elseif($_POST['enableflag']==1)
{
  $selected_forms=$_POST['regchk'];
   while($current_form_id=array_shift($selected_forms))
  {
   updateRegistered ($current_form_id, "state=1" );
  }
}
elseif($_POST['disableflag']==1)
{
  $selected_forms=$_POST['regchk'];
  while($current_form_id=array_shift($selected_forms))
  {
   updateRegistered ($current_form_id, "state=0" );
  }
}
$speciality=$_GET['spec'];
$bigdata = getRegisteredSpeciality("%",$speciality) or $bigdata = false;

$formtarget = $GLOBALS['concurrent_layout'] ? "" : " target='Main'";

//START OUT OUR PAGE....
?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<style type="text/css">
.css_button_small span{color:#000000;}
.css_button_small span:hover{color:#D40000;}
</style>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui-1.7.1.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.easydrag.handler.beta2.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/ajax_functions_writer.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.js"></script>
	 <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.easing-1.3.pack.js"></script>
	<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" type="text/css">
<script type="text/javascript" language="javascript">
function installdb()
{
  document.frm1.installdbflag.value=1;
  document.frm1.submit();
}
function enable()
{ 
  document.frm1.enableflag.value=1;
  document.frm1.submit();
}
function disable()
{
  document.frm1.disableflag.value=1;
  document.frm1.submit();
}
function register()
{
 document.unregfrm.register.value=1;
 document.unregfrm.submit();
}
function completeinstall()
{
 document.unregfrm.fullinstall.value=1;
 document.unregfrm.submit();
}
function toggle(source,ckeck) {
  field = document.getElementsByName(ckeck);
  for (i = 0; i < field.length; i++)
	field[i].checked = source.checked ;
}
function updateform()
{
 document.frm1.update1.value=1;
 document.frm1.submit();
}
$(document).ready(function(){ 
						   
	$(function() {
		$("#menu5").sortable({ opacity: 0.3, cursor: 'move', update: function() {
			//var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
			$.post("updatedb.php", $('#pn').serialize());									 
		}								  
		});
	});

});
function specpopup()
{
 var width  = 300;
 var height = 300;
 var left   = (screen.width  - width)/2;
 var top    = (screen.height - height)/2;
 var params = 'width='+width+', height='+height;
 params += ', top='+top+', left='+left;
 url='addlistPopup.php';
 window.open(url,'',params);
}

		$(document).ready(function() {
		
			
			$(".newfancyboxsmall").fancybox({
				'width'				: '50%',
				'height'			: '50%',
				'autoScale'			: false,
				'transitionIn'	                : 'elastic',
				'transitionOut'	                : 'elastic',
				'type'				: 'iframe'
			});
		});
	
</script>
</head>
<body class="body_top">
<span class="title" style="padding-left:5px;color:#D40000;font-size:20px;"><?php xl('Forms Administration','e');?></span>

<table style="margin:10px 0 10px 0;"><tr><td><b><?php echo htmlspecialchars(xl('Select specialty'),ENT_QUOTES);?></b></td><td>
<select name="department" onChange="location.href='?spec='+this.value">
<option value=""><?php echo htmlspecialchars(xl('Select'),ENT_QUOTES);?></option>
<?php
 $specArray=array(); // speciality option ids in list options
 $specDir=array();   //form directory list
 
  $path='../forms/';if ($handle = opendir($path)) {			  
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != ".."&& substr($file,0,11) == 'Speciality_') {
						   array_push($specDir,$file);
						   							
						}
					}
				closedir($handle);
				}
$lres = sqlStatement("SELECT * FROM list_options " .
    "WHERE list_id ='Speciality' ORDER BY seq, title");
	while ($lrow = sqlFetchArray($lres)) {
	array_push($specArray,$lrow['option_id']);

	?>
	  <option value="<?php echo $lrow['option_id'];?>" <?php if($speciality==$lrow['option_id']){echo 'selected="selected"';}?>><?php echo $lrow['title'];?></option>
	<?php
	}//end while
	$specDiff=array_diff($specDir,$specArray);
?>
<option value="common_forms">Common Forms</option></select>
</td><td><?php if(sizeof($specDiff)>0){ echo '<font color=green>Forms for '.sizeof($specDiff).' other specialties are available.  If you want to add them please click here: <a href="addlistPopup.php" class=" newfancyboxsmall css_button_small" style="float:right;" title="Missing Speciality List" ><span>'.htmlspecialchars(xl('View'),ENT_QUOTES).'</span></a></font>';}?> </td></tr></table>
<?php
 if(isset($speciality)) {
 if(isset($speciality)) $speciality1=$speciality;else $speciality1='';
       if($_POST['update1']==1){
           sqlQuery("update registry set allow_duplication='0' where speciality='".$speciality1."'");  
		                    }
	foreach($_POST as $key=>$val) {
	       if (preg_match('/nickname_(\d+)/', $key, $matches)) {
               		$nickname_id = $matches[1];
			sqlQuery("update registry set nickname='".$val."' where id=".$nickname_id);
		}
	       if (preg_match('/category_(\d+)/', $key, $matches)) {
               		$category_id = $matches[1];
			sqlQuery("update registry set category='".$val."' where id=".$category_id);
		}
	       if (preg_match('/priority_(\d+)/', $key, $matches)) {
               		$priority_id = $matches[1];
			sqlQuery("update registry set priority='".$val."' where id=".$priority_id);
		}
	       if (preg_match('/priority_list_(\d+)/', $key, $matches)) {
               		$priority_list_id = $matches[1];
			sqlQuery("update registry set priority_list='".$val."' where id=".$priority_list_id);
		}
		if (preg_match('/allow_duplication_(\d+)/', $key, $matches)) {
               		$allow_duplication_id = $matches[1];
					if($val) $val=1;
					sqlQuery("update registry set allow_duplication='".$val."' where id=".$allow_duplication_id);
		}
        }   
?>


<?php //ERROR REPORTING
if ($err)
	echo "<span class=bold>$err</span><br><br>\n";
?>
<hr>

<?php //REGISTERED SECTION ?>

<?php if ($bigdata != false){?>

<div style="background-color:#FFFFFF;width:910px;padding:5px;">
<span class=bold><?php xl('Registered','e');?></span><br>
<form name="frm1" method=POST action ='./forms_admin.php?spec=<?php echo $speciality;?>'<?php echo $formtarget; ?> id="pn">
<input type="hidden" name="update1">
<i style="float:left;"><?php xl('click here to update priority, category and nickname settings','e'); ?>&nbsp;&nbsp;</i>
<a href="#" class="css_button_small" onClick="javascript:updateform();" style="float:left;"><span><?php echo htmlspecialchars(xl('update'),ENT_QUOTES);?></span></a>
<span style="font-size:12px;color:#D41F00;margin:10px 0 0 10px;"><?php echo htmlspecialchars(xl('For changing menu order drag and drop the curresponding forms.'),ENT_QUOTES);?></span>
<br> 

<table border=0 cellpadding=1 cellspacing=2 width="900">
	<tr>
	  <td width="20px"><input type="checkbox" onClick="toggle(this,'regchk[]')" ></td>
		<td width="390px">
		<div style="margin:0 0 0 28px;"><a href="#" class="css_button_small" onClick="javascript:installdb();"><span><?php echo htmlspecialchars(xl('Install DB'),ENT_QUOTES);?></span></a> &nbsp;<a href="#" class="css_button_small" onClick="javascript:enable();"><span><?php echo htmlspecialchars(xl('Enable'),ENT_QUOTES);?></span></a>&nbsp;<a href="#" class="css_button_small" onClick="javascript:disable();"><span><?php echo htmlspecialchars(xl('Disable'),ENT_QUOTES);?></span></a></div>
		</td>
		<td width='90px'> </td>
		<?php /*?><td width="7%" align="center" class=bold><?php xl('Menu Order','e'); ?></td><?php */?>
		<td width="90px" class=bold><?php xl('List Order','e'); ?></td>
		<td width="90px" class=bold><?php xl('Category ','e'); ?></td>
		<td width="90px" class=bold><?php xl('Nickname','e'); ?></td>
		<td width="90px" class=bold><?php xl('Allow Duplication','e'); ?></td>
	</tr></table>
<?php }?>
<div id="menu5" style="width:900px;">
<?php
$color="#cccccc";
if ($bigdata != false)
foreach($bigdata as $registry)
{
	$priority_category = sqlQuery("select priority, category, nickname,priority_list,allow_duplication from registry where id=".$registry['id']); 
	?>
	<div>
	<input type="hidden" value="<?php echo $registry['id'];?>" name="clorder[]">
	<table width="900px" title="Drag and drop for changing menu order">
	<tr bgcolor="<?php echo $color?>" >
	  
		<td width="20">
			<input type="checkbox" value="<?php echo $registry['id'];?>" name="regchk[]">
		</td>
		<td width="200px">
			<span class=bold><?php echo xl_form_title($registry['name']); ?></span> 
		</td>
		<?php
			if ($registry['sql_run'] == 0)
				echo "<td bgcolor='$color' width='90px'><span class='text'>".xl('registered')."</span></td>";
			elseif ($registry['state'] == "0")
				echo "<td  width='90px'><a class=link_submit href='./forms_admin.php?id={$registry['id']}&method=enable&spec=".$speciality."'$formtarget>".xl('disabled')."</a></td>";
			else
				echo "<td width='90px'><a class=link_submit href='./forms_admin.php?id={$registry['id']}&method=disable&spec=".$speciality."'$formtarget>".xl('enabled')."</a></td>";
		?>
		<td width='90px' >
			<span class=text><?php
			
			if ($registry['unpackaged'])
				echo xl('PHP extracted','e');
			else
				echo xl('PHP compressed','e');
			
			?></span> 
		</td>
		<td width='90px'>
			<?php
			if ($registry['sql_run'])
				echo "<span class=text>".xl('DB installed')."</span>";
			else
				echo "<a class=link_submit href='./forms_admin.php?id={$registry['id']}&method=install_db&spec=".$speciality."'$formtarget>".xl('install DB')."</a>";
			?> 
		</td>
		<?php
			//echo "<td><input type=text size=4 name=priority_".$registry['id']." value='".$priority_category['priority']."'></td>";
			echo "<td width='90px'><input style='border:none;' type=text size=11 name=priority_list_".$registry['id']." value='".$priority_category['priority_list']."'></td>";
			echo "<td width='90px'><input style='border:none;' type=text size=11 name=category_".$registry['id']." value='".$priority_category['category']."'></td>";
			echo "<td width='90px'><input style='border:none;' type=text size=11 name=nickname_".$registry['id']." value='".$priority_category['nickname']."'></td>";
			$chk1="checked='checked'";
			if($priority_category['allow_duplication']==0){
			 $chk1='';
			}
			echo "<td width='90px'><input type=checkbox ".$chk1." name=allow_duplication_".$registry['id']."></td>";
		?>
	</tr></table></div>
	<?php
	if ($color=="#e9e9e9")
	        $color="#cccccc";
	else
	        $color="#e9e9e9";
} //end of foreach
	?></div>
	<input type="hidden" name="installdbflag">
	<input type="hidden" name="enableflag">
	<input type="hidden" name="disableflag">
	<?php if ($bigdata != false){?>
	<div style="clear:both; margin:3px 0 0 54px;">
	<a href="#" class="css_button_small" onClick="javascript:installdb();"><span><?php echo htmlspecialchars(xl('Install DB'),ENT_QUOTES);?></span></a> &nbsp;<a href="#" class="css_button_small" onClick="javascript:enable();"><span><?php echo htmlspecialchars(xl('Enable'),ENT_QUOTES);?></span></a>&nbsp;<a href="#" class="css_button_small" onClick="javascript:disable();"><span><?php echo htmlspecialchars(xl('Disable'),ENT_QUOTES);?></span></a></div></form>
</form></div>
<hr><?php }?> 

<?php  //UNREGISTERED SECTION ?>


<?php
//$specialitydir=$speciality!=''?'specialty_'.$speciality:'';
$dpath = "$srcdir/../interface/forms/".$speciality."/";
$dp = opendir($dpath);
$color="#cccccc";
for ($i=0; false != ($fname = readdir($dp)); $i++)
	if ($fname != "." && substr($fname,0,11)!='Speciality_' && $fname != ".." && $fname != "CVS" && $fname != "LBF" &&
    (is_dir($dpath.$fname) || stristr($fname, ".tar.gz") ||
    stristr($fname, ".tar") || stristr($fname, ".zip") ||
    stristr($fname, ".gz")))
		$inDir[$i] = $fname;

// ballards 11/05/2005 fixed bug in removing registered form from the list
if ($bigdata != false)
{
	foreach ( $bigdata as $registry )
	{
		$key = array_search($registry['directory'], $inDir) ;  /* returns integer or FALSE */
		unset($inDir[$key]);
	}
}
if(sizeof($inDir)>0)
{
?>
<div style="background-color:#FFFFFF;width:710px;padding:5px;">
<span class=bold><?php xl('Unregistered','e');?></span><br>
<form name="unregfrm" action="" method="post">
<input type="hidden" name="fullinstall">
<input type="hidden" name="register">
<table border=0 cellpadding=1 cellspacing=2 width="700">
<tr><td><input type="checkbox" onClick="toggle(this,'unregchk[]')" ></td><td></td><td colspan="2"><a href="#" class="css_button_small" onClick="javascript:register();"><span><?php echo htmlspecialchars(xl('Register'),ENT_QUOTES);?></span></a><a href="#" class="css_button_small" onClick="completeinstall();"><span><?php echo htmlspecialchars(xl('One Step Install'),ENT_QUOTES);?></span></a></td></tr>
<?php
}
foreach ( $inDir as $fname )
{
        // added 8-2009 by BM - do not show the metric vitals form as option since deprecated
	//  also added a toggle in globals.php in case user wants the option to add this deprecated form
        if (($fname == "vitalsM") && ($GLOBALS['disable_deprecated_metrics_form'])) continue;   
    
	if (stristr($fname, ".tar.gz") || stristr($fname, ".tar") || stristr($fname, ".zip") || stristr($fname, ".gz"))
		$phpState = "PHP compressed";
	else
		$phpState =  "PHP extracted";
	?>
	<tr>
		<td bgcolor="<?php echo $color?>" width="5%">
			<input type="checkbox" name="unregchk[]" value="<?php echo $speciality.':'.$fname;?>"> 
		</td>
		<td bgcolor="<?php echo $color?>" width="20%">
	        <?php
                $form_title_file = @file($GLOBALS['srcdir']."/../interface/forms/$fname/info.txt");
                        if ($form_title_file)
                                $form_title = $form_title_file[0];
                        else
                                $form_title = $fname;
                ?>
			<span class=bold><?php echo xl_form_title($form_title); ?></span> 
		</td>
		<td bgcolor="<?php echo $color?>" width="10%"><?php
			if ($phpState == "PHP extracted")
				echo '<a class=link_submit href="./forms_admin.php?name=' . urlencode($fname) . '&method=register&spec='.$speciality.'"'. $formtarget . '>' . xl('register') . '</a>';
			else
				echo '<span class=text>' . xl('n/a') . '</span>';
		?></td>
		<td bgcolor="<?php echo $color?>" width="20%">
			<span class=text><?php echo xl($phpState); ?></span> 
		</td>
		<td bgcolor="<?php echo $color?>" width="10%">
			<span class=text><?php xl('n/a','e'); ?></span> 
		</td>
	</tr>
	<?php
	if ($color=="#e9e9e9")
	        $color="#cccccc";
	else
	        $color="#e9e9e9";
	flush();
}//end of foreach
if(sizeof($inDir)>0)
{
?>
</table></form> <?php } }?>
</div>
</body>
</html>
