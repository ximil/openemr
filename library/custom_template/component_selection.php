<?php
require_once("$srcdir/classes/ORDataObject.class.php");
?>
<html>
    <head>
        <!--<script src="http://cdn.jquerytools.org/1.2.6/full/jquery.tools.min.js"></script>-->
        <script type="text/javascript">
            focussedElementId='';
            function doGetCaretPosition (ctrl) {
                var CaretPos = 0;	// IE Support
                if (document.selection) {
                    ctrl.focus ();
                    var Sel = document.selection.createRange ();
                    Sel.moveStart ('character', -ctrl.value.length);
                    CaretPos = Sel.text.length;
                }
                // Firefox support
                else if (ctrl.selectionStart || ctrl.selectionStart == '0')
                    CaretPos = ctrl.selectionStart;
                return (CaretPos);
            }
            function showFocus(){
                focussedElementId = $(document.activeElement).attr('id');
                if(focussedElementId == 'text_editor_frame') //text editor frame id should not be considered as focussed element.
                    focussedElementId = '';
            }
            function setValues(val){
                if(focussedElementId){
                    pos=doGetCaretPosition(document.getElementById(focussedElementId));
                    textAreaValue=document.getElementById(focussedElementId).value;
                    before=textAreaValue.substring(0,pos);
                    after=textAreaValue.substring(pos,textAreaValue.length);
                    document.getElementById(focussedElementId).value=before+""+val+""+after;
                    document.getElementById(focussedElementId).focus();
                }
                else if(!focussedElementId){
                    focussedElementId='textarea1';
                    if(CKEDITOR.instances[focussedElementId]){
                        AppendStringToContent(" "+val);
                    }
                }
                else{                    
                    alert("No Focus");
                }
            }
            function displayDiv(){
                $('#draggable').toggle('slow');
            }
            function mergeValues(){
                if(!focussedElementId){
                    focussedElementId='textarea1';
                    if(CKEDITOR.instances[focussedElementId]){
                        str = CKEDITOR.instances[focussedElementId].getData();
                    }
                    else
                        return false;
                }
                else{
                    str = document.getElementById(focussedElementId).value;
                }
                $.ajax({
                    type: "POST",
                    url: "<?php echo $GLOBALS['webroot'];?>/library/custom_template/ajax_code.php",
                    dataType: "html",
                    data: {
                        source:'merge_content',
                        strToMerge:str,
                    },
                    async: false,
                    success: function(thedata){
                        //alert(thedata);
                        if(focussedElementId=='textarea1'){
                            CKEDITOR.instances[focussedElementId].setData(thedata);
                        }
                        else{
                            document.getElementById(focussedElementId).value=thedata;
                        }
                    },
                    error:function(){
                        alert('ajax error');
                    }
                });
            }
        </script>
        <style type="text/css">
            /* Example styles used for this demo */
            #tabContainer{
                position:relative;            
                width:150px;            
            }
            #tabMenu{            
                position:relative;            
                height:30px;            
            }
            #tabContent{                
                position:relative;                
                height:266px;                
                font:12px Verdana, Arial, Helvetica, sans-serif;                
                color:#444444;                
                border:4px solid #9fb2d6;                
                overflow:auto;
            }
            #tabContent .content{
                display:none;            
            }
            #tabContent .active{            
                /*padding:0px 10px;            */
                display:block;            
                white-space:pre;            
            }
            /* Tab menu styles generated via the horitontal menu builder @ www.cssmenubuilder.com */            
            .menu{margin:0 auto; padding:0; height:30px; width:100%; display:block; background:url('<?php echo $GLOBALS['webroot'] ?>/images/topMenuImages.png') repeat-x;}            
            .menu li{padding:0; margin:0; list-style:none; display:inline;}            
            .menu li a{float:left; padding-left:15px; display:block; color:rgb(255,255,255); text-decoration:none; font:12px Verdana, Arial, Helvetica, sans-serif; cursor:pointer; background:url('<?php echo $GLOBALS['webroot'] ?>/images/topMenuImages.png') 0px -30px no-repeat;}            
            .menu li a span{line-height:30px; float:left; display:block; padding-right:15px; background:url('<?php echo $GLOBALS['webroot'] ?>/images/topMenuImages.png') 100% -30px no-repeat;}            
            .menu li a:hover{background-position:0px -60px; color:rgb(255,255,255);}            
            .menu li a:hover span{background-position:100% -60px;}            
            .menu li a.active, .menu li a.active:hover{line-height:30px; font:12px Verdana, Arial, Helvetica, sans-serif; background:url('<?php echo $GLOBALS['webroot'] ?>/images/topMenuImages.png') 0px -90px no-repeat; color:rgb(82,82,82);}            
            .menu li a.active span, .menu li a.active:hover span{background:url('<?php echo $GLOBALS['webroot'] ?>/images/topMenuImages.png') 100% -90px no-repeat;}
        </style>
        <script type="text/javascript">        
            $(document).ready(function(){
               initTabs();
            });
            function initTabs() {
                $('#tabMenu a').bind('click',function(e) {
                    e.preventDefault();            
                    var thref = $(this).attr("href").replace(/#/, '');            
                    $('#tabMenu a').removeClass('active');            
                    $(this).addClass('active');            
                    $('#tabContent div.content').removeClass('active');            
                    $('#'+thref).addClass('active');            
                });
            }        
        </script>
    </head>
    <body class="body_top">
        <div id="tabContainer">
            <div id="tabMenu">
                <ul class="menu">
                    <li><a href="intro"><span>Database</span></a></li>    
                    <li><a href="css" class="active"><span>Alias</span></a></li>
                </ul>    
            </div>
            <div id="tabContent">            
                <div id="intro" class="content" style="line-height: 0;overflow:hidden">
                    <table class="text">
                        <?
                        $table = array('patient_data','history_data');
                        $prev_value = '';
                        $q = sqlStatement("SELECT field_id,title FROM layout_options  WHERE form_id IN ('HIS','DEM')");
                        $dem = array();
                        while($r = sqlFetchArray($q)){
                            $dem[$r['field_id']] = $r['title'];
                        }
                        foreach($table as $key=>$value){
                            $fields = sqlListFields($value);
                            if($prev_value != $value){
                                ?>
                                <tr>
                                    <td style="background:#9a9a9a;width:10px">
                                        <?php echo $value;?>
                                    </td>
                                </tr>
                                <?php
                            }
                            foreach($fields as $field_name){
                                if($dem[$field_name]){
                                ?>
                                <tr>
                                    <td onmouseover="showFocus()" style="cursor:pointer;width:10px" onclick="setValues('[[<?php echo $value.".".$field_name;?>]]')">
                                        <?php echo $dem[$field_name];?>
                                    </td>
                                </tr>
                                <?php
                                }
                            }
                            $prev_value = $value;
                        }
                        ?>
                    </table>
                </div>
                <div id="css" class="content active" style="line-height: 0;">
                    <table class="text">
                        <?
                        $result = sqlStatement("select * from nation_notes_mapping");
                        while($row = sqlFetchArray($result)){
                            ?>
                            <tr>
                                <td onmouseover="showFocus()" style="cursor:pointer" onclick="setValues('[[[<?php echo $row['map_table_name'].".".$row['map_field_name'];?>]]]')">
                                    <?php echo $row['map_user_defined_name'];?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>