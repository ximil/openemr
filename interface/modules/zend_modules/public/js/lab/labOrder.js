/**
 * Lab Multiple Order Screen
 */

// Hidden Value Settings
    $(function(){
        //Hidden value setting for first panel and row count
        if ($('#accord_panel_0').length < 1) {
            $('#lab').append('<input type="hidden" id="accord_panel_0" name="accord_panel_0" value="1" />');
        }
        // Total panels
        if ($('#total_panel').length < 1) {
            $('#lab').append('<input type="hidden" id="total_panel" name="total_panel" value="1" />');
        }
        var $radios = $('#specimencollected_1_1');
        if($radios.is(':checked') === false) {
            $radios.filter('[value=onsite]').attr('checked', true);
        }
    });

    // Save the data
    var url = './savedata'
    function saveFrm() {
        url = 'savedata';
        $('#lab').form('submit',{
            url: url,  
            onSubmit: function(){  
                return; //$(this).form('validate');  
            },  
            success: function(result){
                var result = eval('('+result+')');  
                if (result.errorMsg){
                    $.messager.show({  
                        title: 'Error',  
                        msg: result.errorMsg  
                    });  
                }
            }  
        });  
    }
    
    // Remove the dynamically added rows
    function cancelItem(id) {
        var arr = id.split('_');
        $('#cloneID_' + arr[1] + '_' + arr[2]).remove();
    }
    
    //Remove the selected Panell (New Order)
    function removeAccord(){
        var pp = $('#accord').accordion('getSelected');
        if (pp){
            var index = $('#accord').accordion('getPanelIndex',pp);
            $('#accord').accordion('remove',index);
        }
    }
    
    // Dynamically add new rows in the Procedure order
    var inc = 1;
    function addRow() {
        inc++;
        var pp = $('#accord').accordion('getSelected');
        if (pp){
            var index = $('#accord').accordion('getPanelIndex',pp);
            var acc_cnt = index + 1;
            // Setting row count for each panell
            var key = index;
            var rowCount = 1;
            rowCount = parseInt($('#accord_panel_' + key).val());
            inc = rowCount + 1;
            
            $("#accord_panel_" + key).val(rowCount + 1);
            var clone = $("#cloneID_1").clone(false).appendTo("#insTempl_"+acc_cnt+"_1");
            $("#insTempl_"+ acc_cnt + "_1 table:last").attr('id', 'cloneID_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#diagnosestemplate').attr('id', 'diagnosestemplate_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#proceduretemplate').attr('id', 'proceduretemplate_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#diagnodiv').attr('id', 'diagnodiv_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#prodiv').attr('id', 'prodiv_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#patient_instructions_1_1').attr('id', 'patient_instructions_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#diagnoses_1_1').attr('id', 'diagnoses_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#procedures_1_1').attr('id', 'procedures_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#procedure_code_1_1').attr('id', 'procedure_code_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#procedure_suffix_1_1').attr('id', 'procedure_suffix_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find('#AOEtemplate').attr('id', 'AOEtemplate_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find("#procedures").attr('id', 'procedures_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find("#AOE").attr('id', 'AOE_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find("#deleteButton").attr('id', 'deleteButton_'+acc_cnt+'_' + inc);
            $('#cloneID_'+acc_cnt+'_' + inc).find("#addButton").attr('id', 'addButton_'+acc_cnt+'_' + inc);
            
            // Field Name Change
            $('#cloneID_'+acc_cnt+'_' + inc).find('#patient_instructions_' + acc_cnt + '_' + inc ).attr('name', 'patient_instructions[' + (acc_cnt - 1) + '][]');
            $('#cloneID_'+acc_cnt+'_' + inc).find('#diagnoses_' + acc_cnt + '_' + inc ).attr('name', 'diagnoses[' + (acc_cnt - 1) + '][]');
            $('#cloneID_'+acc_cnt+'_' + inc).find('#procedures_' + acc_cnt + '_' + inc ).attr('name', 'procedures[' + (acc_cnt - 1) + '][]');
            $('#cloneID_'+acc_cnt+'_' + inc).find('#procedure_code_' + acc_cnt + '_' + inc ).attr('name', 'procedure_code[' + (acc_cnt - 1) + '][]');
            $('#cloneID_'+acc_cnt+'_' + inc).find('#procedure_suffix_' + acc_cnt + '_' + inc ).attr('name', 'procedure_suffix[' + (acc_cnt - 1) + '][]');
        }
    }
    
    // Create a new Panell (New Order)
    var idx   = 1;
    var j     = 1;
    var nOrd  = 2;
    function newOrder(){
        // Create a hidden field for each panell for row count
        $('#lab').append('<input type="hidden" id="accord_panel_' + idx + '" name="accord_panel_' + idx + '" value="1" />');
        // Set total panels
        $("#total_panel").val(nOrd);
        
        var acc_cnt = nOrd;   
        var newAccord = $('#mainTemplate>*').clone(false).appendTo("#accord");
        newAccord.find('#main').closest("#panel_" + (nOrd - 1)).attr('id', 'main_' + nOrd);
        newAccord.find('#toolbar').attr('id', 'toolbar_' + nOrd);
        newAccord.find('#editor').attr('id', 'editor_' + nOrd);
        newAccord.find('#tt').attr('id', 'tt_' + nOrd);
        newAccord.find('#dgord').attr('id', 'dgord_' + nOrd);
        
        newAccord.find('#provider_1_1').attr('id', 'provider_' + acc_cnt + '_1');
        newAccord.find('#lab_id_1_1').attr('id', 'lab_id_' + acc_cnt + '_1');
        newAccord.find('#orderdate_1_1').attr('id', 'orderdate_' + acc_cnt + '_1');
        newAccord.find('#internaltimecaption').attr('id', 'internaltimecaption_' + acc_cnt + '_1');
        newAccord.find('#internaltime').attr('id', 'internaltime_' + acc_cnt + '_1');
        newAccord.find('#specimencollectedcaption').attr('id', 'specimencollectedcaption_' + acc_cnt + '_1');
        newAccord.find('#oderingdate').attr('id', 'oderingdate_' + acc_cnt + '_1');
        newAccord.find('#timecollected_1_1').attr('id', 'timecollected_' + acc_cnt + '_1');
        newAccord.find('#internal_comments_1_1').attr('id', 'internal_comments_' + acc_cnt + '_1');
        newAccord.find('#specimencollected_1_1').attr('id', 'specimencollected_' + acc_cnt + '_1');
        newAccord.find('#specimencollectedtd').attr('id', 'specimencollectedtd_' + acc_cnt + '_1');
        newAccord.find('#priority_1_1').attr('id', 'priority_' + acc_cnt + '_1');
        newAccord.find('#status_1_1').attr('id', 'status_' + acc_cnt + '_1');
        newAccord.find('#billtocaption').attr('id', 'billtocaption_' + acc_cnt + '_1');
        newAccord.find('#billtotd').attr('id', 'billtotd_' + acc_cnt + '_1');
        newAccord.find('#billto_1_1').attr('id', 'billto_' + acc_cnt + '_1');
        newAccord.find('#insTempl').attr('id', "insTempl_" + acc_cnt + "_1");
        
        // Remove duplicate Order Date and Internal Time Collected
        $('#oderingdate_' + acc_cnt + '_1 span').remove();
        $('#internaltime_' + acc_cnt + '_1 span').remove();
        
        var clone = $("#cloneID_1").clone(false).appendTo("#insTempl_" + acc_cnt + "_1");
        $("#insTempl_" + acc_cnt + "_1 table:last").attr('id', 'cloneID_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#diagnosestemplate').attr('id', 'diagnosestemplate_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#proceduretemplate').attr('id', 'proceduretemplate_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#diagnodiv').attr('id', 'diagnodiv_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#prodiv').attr('id', 'prodiv_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#patient_instructions_1_1').attr('id', 'patient_instructions_'+ acc_cnt +'_1');
        $('#cloneID_' + acc_cnt + '_1').find('#diagnoses_1_1').attr('id', 'diagnoses_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#procedures_1_1').attr('id', 'procedures_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#procedure_code_1_1').attr('id', 'procedure_code_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#procedure_suffix_1_1').attr('id', 'procedure_suffix_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#AOEtemplate').attr('id', 'AOEtemplate_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#procedures').attr('id', 'procedures_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#AOE').attr('id', 'AOE_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#deleteButton').attr('id', 'deleteButton_' + acc_cnt + '_1');
        $('#cloneID_' + acc_cnt + '_1').find('#addButton').attr('id', 'addButton_' + acc_cnt + '_1');
        
        // Field name settings
        newAccord.find('#provider_' + acc_cnt + '_1').attr('name', 'provider[' + (acc_cnt - 1) + '][]');
        newAccord.find('#lab_id_' + acc_cnt + '_1').attr('name', 'lab_id[' + (acc_cnt - 1) + '][]');
        newAccord.find('#orderdate_' + acc_cnt + '_1').attr('name', 'orderdate[' + (acc_cnt - 1) + '][]');
        newAccord.find('#timecollected_' + acc_cnt + '_1').attr('name', 'timecollected[' + (acc_cnt - 1) + '][]');
        newAccord.find('#internal_comments_' + acc_cnt + '_1').attr('name', 'internal_comments[' + (acc_cnt - 1) + '][]');
        newAccord.find('#specimencollected_' + acc_cnt + '_1').attr('name', 'specimencollected[' + (acc_cnt - 1) + '][]');
        newAccord.find('#priority_' + acc_cnt + '_1').attr('name', 'priority[' + (acc_cnt - 1) + '][]');
        newAccord.find('#status_' + acc_cnt + '_1').attr('name', 'status[' + (acc_cnt - 1) + '][]');
        newAccord.find('#billto_' + acc_cnt + '_1').attr('name', 'billto[' + (acc_cnt - 1) + '][]');
                
        $('#cloneID_' + acc_cnt + '_1').find('#patient_instructions_'+ acc_cnt + '_1').attr('name', 'patient_instructions[' + (acc_cnt - 1) + '][]');
        $('#cloneID_' + acc_cnt + '_1').find('#diagnoses_' + acc_cnt + '_1').attr('name', 'diagnoses[' + (acc_cnt - 1) + '][]');
        $('#cloneID_' + acc_cnt + '_1').find('#procedures_' + acc_cnt + '_1').attr('name', 'procedures[' + (acc_cnt - 1) + '][]');
        $('#cloneID_' + acc_cnt + '_1').find('#procedure_code_' + acc_cnt + '_1').attr('name', 'procedure_code[' + (acc_cnt - 1) + '][]');
        $('#cloneID_' + acc_cnt + '_1').find('#procedure_suffix_' + acc_cnt + '_1').attr('name', 'procedure_suffix[' + (acc_cnt - 1) + '][]');
        
        var $radios = $('#specimencollected_' + acc_cnt + '_1');
        if($radios.is(':checked') === false) {
            $radios.filter('[value=onsite]').attr('checked', true);
        }
        
        $('#accord').accordion('add',{
            title:'<img  class="easyui-linkbutton" iconCls="icon-save" src="../css/icons/multiple.png"  border="0" />  New Procedure Order ' + idx,
            content:newAccord
        });

        idx++;
        j++;
        nOrd++;
    }