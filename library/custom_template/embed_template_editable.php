<?php
// +-----------------------------------------------------------------------------+ 
// Copyright (C) 2011 Z&H Consultancy Services Private Limited <sam@zhservices.com>
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
//           Jacob T Paul <jacob@zhservices.com>
//
// +------------------------------------------------------------------------------+

//SANITIZE ALL ESCAPES
$sanitize_all_escapes=true;
//

//STOP FAKE REGISTER GLOBALS
$fake_register_globals=false;
//

require_once(dirname(__FILE__)."/../../interface/globals.php");
$contextName = $_REQUEST['contextName'];
$type = $_REQUEST['type'];
$PatientDetails = $_REQUEST['patient'];
$rowContext = sqlQuery("SELECT * FROM customlists WHERE cl_list_type=2 AND cl_list_item_long=?",array($contextName));
$Category = $GLOBALS['docwriter_category'] ? $GLOBALS['docwriter_category'] : 'Categories';
$Components = $GLOBALS['docwriter_components'] ? $GLOBALS['docwriter_components'] : 'Components';

?>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.css" media="screen" />
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/custom_template/ckeditor/ckeditor.js"></script>
    <script src="<?php echo $GLOBALS['webroot']?>/library/custom_template/ckeditor/_samples/sample.js" type="text/javascript"></script>
    <link href="<?php echo $GLOBALS['webroot']?>/library/custom_template/ckeditor/_samples/sample.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.1.3.2.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui-1.7.1.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/fancybox/jquery.fancybox-1.2.6.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.easydrag.handler.beta2.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/ajax_functions_writer.js"></script>
    <script language="JavaScript" type="text/javascript">
    
function updateBeforeSave(){
  $.ajax({
    type: "POST",
    url: "ajax_code.php",
    dataType: "html",
    data: {
      source:'prescription'
    },
    async: false,
    success: function(thedata){
      var arr = thedata.split('!####!');
      alert(arr[0]);
      document.getElementById('optionsPrescribe').innerHTML=arr[1];
    },
    error:function(){
      alert('ajax error');
    }
  });
}
    
    $(document).ready(function(){

    // fancy box
    enable_modals();

    tabbify();

    // special size for
	$(".iframe_small").fancybox( {
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 120,
		'frameWidth' : 330
	});
	$(".iframe_medium").fancybox( {
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 430,
		'frameWidth' : 680
	});
        $(".iframe_abvmedium").fancybox( {
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 500,
		'frameWidth' : 700
	});
  $(".iframe_large").fancybox( {
		'overlayOpacity' : 0.0,
		'showCloseButton' : true,
		'frameHeight' : 500,
		'frameWidth' : 800,
    callbackOnStart : function() {
      $("#fancy_overlay").bind('click',function() {
        updateBeforeSave();
      });
      $('#fancy_close').bind('click', function() {
        updateBeforeSave();
      });
    }
	});
	$(function(){
		// add drag and drop functionality to fancybox
		$("#fancy_outer").easydrag();
	});

        $("#menu5 > li > a.expanded + ul").slideToggle("medium");
	$("#menu5 > li > a").click(function() {
		$("#menu5 > li > a.expanded").not(this).toggleClass("expanded").toggleClass("collapsed").parent().find('> ul').slideToggle("medium");
		$(this).toggleClass("expanded").toggleClass("collapsed").parent().find('> ul').slideToggle("medium");
	});
    });
    </script>
     <script type="text/javascript">
$(document).ready(function(){ 
						   
	$(function() {
		$("#menu5 div").sortable({ opacity: 0.3, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
			$.post("updateDB.php", order);									 
		}								  
		});
	});
	load_data();
});
function load_data(){
    mainform = window.parent.document;
    if(mainform.getElementById('<?php echo $contextName;?>_optionTD')){
        if(mainform.getElementById('<?php echo $contextName;?>_optionTD').innerHTML){
            document.getElementById('options').innerHTML=mainform.getElementById('<?php echo $contextName;?>_optionTD').innerHTML;
        }
    }
    if(mainform.getElementById('<?php echo $type;?>_optionTD')){
        if(mainform.getElementById('<?php echo $type;?>_optionTD').innerHTML){
            document.getElementById('options').innerHTML=mainform.getElementById('<?php echo $type;?>_optionTD').innerHTML;
        }
    }
}
function checkShortcut(key)
{
 var type=(key.target || key.srcElement).type;
 if(type!='text' && type!='textarea')
 {      
        if(key.keyCode==8)
         {
                return false;
         }
 }
 
}
function CancelNotes(ObjField){
    Fields = ObjField.split('form_');
    Field = Fields[1];
    $('#nationdiv_'+Field, window.parent.document).slideUp();
    $('#nationtextarea_'+Field, window.parent.document).slideDown();
    $('.hide_other_divs', window.parent.document).show();
}
function INSTEXT(CONTENT){
    alert(CONTENT);
    //AppendStringToContent(CONTENT);
}
<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>
$(function() {
    $( "#draggable" ).draggable({ handle: "p" });
    $( "#draggable2" ).draggable({ cancel: "p.ui-widget-header" });
    $( "div, p" ).disableSelection();
    $('#toggle_nation_notes_plus_nation_notes').click(function(){
        $('.donotdisplay').css("display","");
        $('#toggle_nation_notes_plus_nation_notes').hide();
        $('#toggle_nation_notes_minus_nation_notes').show();
    });
    $('#toggle_nation_notes_minus_nation_notes').click(function(){
        $('.donotdisplay').css("display","none");
        $('#toggle_nation_notes_minus_nation_notes').hide();
        $('#toggle_nation_notes_plus_nation_notes').show();
    });
    $('.donotdisplay').css("display","none");
    $('.donotdisplayatall').css("display","none");
});
</script>
<style type="text/css">
    .dragDiv{
	width:156px;
	/*height:200px;
	overflow-x:hidden; 
	overflow-y:auto;*/
	display:none;
	border:1px solid black;
	background:rgb(223, 235, 254);
	/*--CSS3 Rounded Corners--*/
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
    }
    .p_header{
	-webkit-border-radius: 5px 5px 0px 0px;
	-moz-border-radius: 5px 5px 0px 0px;
	border-radius: 5px 5px 0px 0px;
    }
    .cke_contents {
	height: 700px !important;
    }
</style>
</head>
<body class="body_top" onkeydown="return checkShortcut(event)">
    <div id="draggable" class="ui-widget-content dragDiv" style="z-index:999999;position:fixed;">
	<p id="header_of_list" class="ui-widget-header text p_header" style="background:#9a9a9a;margin-top:0;margin-bottom:0;text-align:right;cursor:move"><span style="cursor:pointer" title="Close Window" onclick="displayDiv()">[X]</span></p>
	<table class="text">
	    <tr>
		<td>
		    <?php require "component_selection.php";?>
		</td>
	    </tr>
	</table>
    </div>
    <input type="hidden" name="list_id" id="list_id" value="<?php echo $rowContext['cl_list_id'];?>">    
    <table width=100% align=left cellpadding=0 cellspacing=0 margin-left=0px>
        <?php
        if($rowContext['cl_list_item_long']){
        ?>
        <tr class="text"><th colspan="2" align="center"><?php //echo strtoupper(htmlspecialchars(xl($rowContext['cl_list_item_long']),ENT_QUOTES));?></th></tr>
        <tr>
            <td>
                <div id="tab1" class="tabset_content tabset_content_active">
                    <form>
                    <table width=100% cellpadding=0 cellspacing=0>
                        <tr>
                            <td>
                                <?php
                                if(isset($_REQUEST['prescribe'])){
                                  if($GLOBALS['erx_enable']){
                                  ?>
                                      <a href='../../interface/eRx.php?page=compose' title='eRx Prescriptions' class='iframe_large css_button'><span><?php echo htmlspecialchars(xl('Prescribe-eRx'),ENT_QUOTES);?></span></a>
                                  <?php                                    
                                  }else{
                                  ?>
                                      <a href='../../interface/patient_file/summary/rx_frameset.php?fieldid=<?php echo $type;?>' title='Prescriptions' class='iframe_medium css_button'><span><?php echo htmlspecialchars(xl('Prescribe'),ENT_QUOTES);?></span></a>
                                  <?php
                                  }
                                }
                                if(isset($_REQUEST['diagnosis'])){
                                ?>
                                    <a href='../../interface/forms/fee_sheet/diag_select.php?fieldid=<?php echo $type;?>' title='Diagnosis' class='iframe_medium css_button'><span><?php echo htmlspecialchars(xl('Diagnosis'),ENT_QUOTES);?></span></a>
                                <?php
                                }
                                if(isset($_REQUEST['cpt'])){
                                ?>
                                    <a href='../../interface/forms/fee_sheet/cpt_select.php?fieldid=<?php echo $type;?>' title='CPT' class='iframe_medium css_button'><span><?php echo htmlspecialchars(xl('CPT'),ENT_QUOTES);?></span></a>
                                <?php
                                }
                                if(isset($_REQUEST['issue'])){
                                ?>
                                    <a href='../../interface/patient_file/summary/stats_full.php?encform=true' title='Issues' class='iframe_abvmedium css_button'><span><?php echo htmlspecialchars(xl('Issues'),ENT_QUOTES);?></span></a>
                                <?php
                                }
				$save_button_name=$_REQUEST['save_button_name'];
				if(!$save_button_name)
				    $save_button_name='SAVE';
                                ?>
                                <a href="#" onclick="return SaveInEmbed('<?php echo $type;?>')" class="css_button donotdisplayatall" id="nationnoteok" ><span><?php echo htmlspecialchars(xl($save_button_name),ENT_QUOTES);?></span></a>
				<a href="#" onclick="return CancelNotes('<?php echo $type;?>')" class="css_button donotdisplayatall" ><span><?php echo htmlspecialchars(xl('CANCEL'),ENT_QUOTES);?></span></a>
                            </td>
                        </tr>
                        <tr class="text">
                            <td id="templateDD" class="donotdisplay">
                                <select name="template" id="template" onchange="TemplateSentence(this.value)" style="width:100%">
                                    <option value=""><?php echo htmlspecialchars(xl('Select '.$Category),ENT_QUOTES);?></option>
                                    <?php
                                    $resTemplates = sqlStatement("SELECT * FROM template_users AS tu LEFT OUTER JOIN customlists AS c ON tu.tu_template_id=c.cl_list_slno WHERE tu.tu_user_id=? AND c.cl_list_type=3 AND cl_list_id=? AND cl_deleted=0 ORDER BY c.cl_list_item_long",array($_SESSION['authId'],$rowContext['cl_list_id']));
                                    while($rowTemplates = sqlFetchArray($resTemplates)){
                                    echo "<option value='".htmlspecialchars($rowTemplates['cl_list_slno'],ENT_QUOTES)."'>".htmlspecialchars(xl($rowTemplates['cl_list_item_long']),ENT_QUOTES)."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td style="padding:5px;">
                                <div id="share" style="display:none"></div>
				<div style="width:30px;float:left;clear:right;">
				    <div id="toggle_nation_notes_plus_nation_notes" title="Expand Components">
					<span>
					    <img height="25" width="25" src="<?php echo $GLOBALS['webroot']?>/images/show_menu.png">
					</span>
				    </div>
				    <div id="toggle_nation_notes_minus_nation_notes" title="Hide Components" style="display:none;">
					<span>
					    <img height="25" width="25" src="<?php echo $GLOBALS['webroot']?>/images/hide_menu.png">
					</span>
				    </div>
				</div>
				<a href="#" id="enter" onclick="top.restoreSession();ascii_write('13','textarea1');" title="<?php echo htmlspecialchars(xl('Enter Key'),ENT_QUOTES);?>"><img border=0 src="<?php echo $GLOBALS['webroot']?>/images/enter.gif"></a>&nbsp;
                                <a href="#" id="quest" onclick="top.restoreSession();AppendStringToContent('? ');" title="<?php echo htmlspecialchars(xl('Question Mark'),ENT_QUOTES);?>"><img border=0 src="<?php echo $GLOBALS['webroot']?>/images/question.png"></a>&nbsp;
                                <a href="#" id="para" onclick="top.restoreSession();ascii_write('para','textarea1');"  title="<?php echo htmlspecialchars(xl('New Paragraph'),ENT_QUOTES);?>"><img border=0 src="<?php echo $GLOBALS['webroot']?>/images/paragraph.png"></a>&nbsp;
                                <a href="#" id="space" onclick="top.restoreSession();ascii_write('32','textarea1');" class="css_button" title="<?php echo htmlspecialchars(xl('Space'),ENT_QUOTES);?>"><span><?php echo htmlspecialchars(xl('SPACE'),ENT_QUOTES);?></span></a>
                                <?php
                                $res=sqlStatement("SELECT * FROM template_users AS tu LEFT OUTER JOIN customlists AS cl ON cl.cl_list_slno=tu.tu_template_id
                                                    WHERE tu.tu_user_id=? AND cl.cl_list_type=6 AND cl.cl_deleted=0 ORDER BY cl.cl_order",array($_SESSION['authId']));
                                while($row=sqlFetchArray($res)){
                                ?>
                                    <a href="#" onclick="top.restoreSession();AppendStringToContent('<?php echo $row['cl_list_item_short'];?>');" class="css_button" title="<?php echo htmlspecialchars(xl($row['cl_list_item_long']),ENT_QUOTES);?>"><span><?php echo ucfirst(htmlspecialchars(xl($row['cl_list_item_long']),ENT_QUOTES));?></span></a>
                                <?php                   
                                }
                                ?>
                            </td>
			    <td style="width:60px">
				<a href="#" class="css_button" onclick="mergeValues()" onmouseover="showFocus()"><span><?php echo htmlspecialchars(xl('Merge'),ENT_QUOTES);?></span></a>
				<a href="#" class="css_button" onclick="displayDiv()"><span><?php echo htmlspecialchars(xl('List'),ENT_QUOTES);?></span></a>				
                            </td>
                        </tr>
                        <tr>
                            <td valign=top style="width:25%;" class="donotdisplay">
                                <div style="background-color:#DFEBFE">
                                <div style="overflow-y:scroll;overflow-x:hidden;height:400px">
                                <ul id="menu5" class="example_menu" style="width:100%;">
                                    <li><a class="expanded" style="width:100%"><?php echo htmlspecialchars(xl($Components),ENT_QUOTES);?></a>
                                        <ul>
                                        <div id="template_sentence">
                                        </div>
                                        </ul>
                                    </li>
                                    <?php
                                    if($PatientDetails=='patient' && $pid!=''){
                                        $row = sqlQuery("SELECT * FROM patient_data WHERE pid=?",array($pid));
                                    ?>
                                    <li><a class="collapsed"><?php echo htmlspecialchars(xl('Patient Details'),ENT_QUOTES);?></a>
                                        <ul>
                                            <li><span><a href="#" onclick="top.restoreSession();AppendStringToContent('<?php echo $row['fname'];?>');"><?php echo htmlspecialchars(xl('First name',ENT_QUOTES));?></a></span></li>
                                            <li><span><a href="#" onclick="top.restoreSession();AppendStringToContent('<?php echo $row['lname'];?>');"><?php echo htmlspecialchars(xl('Last name',ENT_QUOTES));?></a></span></li>
                                            <?php
                                            if($row['phone_home']){
                                            ?>
                                            <li><span><a href="#" onclick="top.restoreSession();AppendStringToContent('<?php echo $row['phone_home'];?>');"><?php echo htmlspecialchars(xl('Phone',ENT_QUOTES));?></a></span></li>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if($row['ss']){
                                            ?>
                                            <li><span><a href="#" onclick="top.restoreSession();AppendStringToContent('<?php echo $row['ss'];?>');"><?php echo htmlspecialchars(xl('SSN',ENT_QUOTES));?></a></span></li>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if($row['DOB']){
                                            ?>
                                            <li><span><a href="#" onclick="top.restoreSession();AppendStringToContent('<?php echo $row['DOB'];?>');"><?php echo htmlspecialchars(xl('Date Of Birth',ENT_QUOTES));?></a></span></li>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if($row['providerID']){
                                                $val=sqlQuery("SELECT CONCAT(lname,',',fname) AS name FROM users WHERE id='".$row['providerID']."'");
                                            ?>
                                            <li><span><a href="#" onclick="top.restoreSession();AppendStringToContent('<?php echo $val['name'];?>');"><?php echo htmlspecialchars(xl('PCP',ENT_QUOTES));?></a></span></li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                                </div>
                                </div>
                                <a href="personalize.php?list_id=<?php echo $rowContext['cl_list_id'];?>" id="personalize_link" class="iframe_medium css_button"><span><?php echo htmlspecialchars(xl('Personalize'),ENT_QUOTES);?></span></a>
                                <a href="add_custombutton.php" id="custombutton" class="iframe_medium css_button" title="<?php echo htmlspecialchars(xl('Add Buttons for Special Chars,Texts to be Displayed on Top of the Editor for inclusion to the text on a Click'),ENT_QUOTES);?>"><span><?php echo htmlspecialchars(xl('Add Buttons'),ENT_QUOTES);?></span></a>
                            </td>
                            <td valign=top style="width:75%;" colspan="2">
				<?php
				$class = "";
				$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
				if(!$isiPad){
				$class = "ckeditor";
				}
				?>
                                <textarea class="<?php echo $class;?>" id="textarea1" name="textarea1" rows="20" cols="53"><?php echo $_REQUEST['textarea_value'];?></textarea>
                            </td>                            
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td id="options" colspan="2">
                                <table width="100%">
                                    <tr class="text">
                                        <td></td>
                                        <td id="optionsPrescribe"></td>
                                    </tr>
                                    <tr class="text">
                                        <td></td>
                                        <td id="optionsDiagnosis"></td>
                                    </tr>
                                    <tr class="text">
                                        <td></td>
                                        <td id="optionsCpt"></td>
                                    </tr>
                                    <tr class="text">
                                        <td></td>
                                        <td id="optionsIssue"></td>
                                    </tr>
                                </table>
                    </table>
                    </form>
                </div>     
                
            </td>
        </tr>
    <?php
        }
    else{
        echo htmlspecialchars(xl('NO SUCH CONTEXT NAME').$contextName,ENT_QUOTES);
    }
    ?>
    </table>
    <table>
    </table>
</body>
</html>
