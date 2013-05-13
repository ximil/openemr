/**
 * providers.js
 */

$(function(){
  // Providers (Add or Edit) Dialog Box
  $('#dlgProvider').dialog({  
      title: 				'Providers',  
      width:  			850,  
      height: 			430,  
      closed: 			true,  
      cache: 				false,
      autoOpen: 		false,  
      iconCls:			'icon-providers',
      resizable:		true,
      minimizable:	true,
      maximizable:	true,
      modal: 				true
  });
  
  // Procedure Provider Data Grid
  $('#dgProvider').datagrid({
    // Double click on the data grid row and edit option
    onDblClickRow: function(rowIndex, rowData){
      editProvider();
    },
    onClickRow: function(rowIndex, rowData) {
      $("#editPProvider").css("display", "block");
      $("#destroyPProvider").css("display", "block");
    }
  });
});

var url;
// New Procedure Provider
function newProvider(){  
    $('#dlg').dialog('open').dialog('setTitle','New Procedure Provider');  
    $('#fmProcedureProvider').form('clear');  
    url = './saveProcedureProvider';  
}

// Edit Procedure Provider
function editProvider(){
    var row = $('#dgProvider').datagrid('getSelected'); 
    if (row){
        $('#DorP').combobox('setValue', row.DorP);
        $('#protocol').combobox('setValue', row.protocol);
        $('#ppid').val(row.ppid);
        $('#dlg').dialog('open').dialog('setTitle','Edit Procedure Provider');  
        $('#fmProcedureProvider').form('load',row);  
        url = './saveProcedureProvider?id='+row.ppid;  
    }  
}

// Save Procedure Provider (New Or Update)
function saveProviders(){
  $('#fmProcedureProvider').form('submit',{  
    url: url,  
    onSubmit: function(){  
      return $(this).form('validate');  
    },  
    success: function(response){//alert(response.toSource());
      var result = eval('('+response+')');
      if (result.errorMsg){  
        $.messager.show({  
          title: 'Error',  
          msg: result.errorMsg  
        });  
      } else {  
        $('#dlg').dialog('close');
        $('#dgProvider').datagrid('reload');
      }  
    }  
  });  
}

// Delete Procedure Provider
function destroyProvider(){  
    var row = $('#dgProvider').datagrid('getSelected');  
    if (row){  
        $.messager.confirm('Confirm','Are you sure you want to destroy this provider?',function(r){  
            if (r){  
                $.post('./deleteProcedureProvider',{ppid:row.ppid},function(result){  
                    if (result.success){  
                        $('#dgProvider').datagrid('reload');
                    } else {  
                        $.messager.show({
                            title: 'Error',  
                            msg: result.errorMsg  
                        });  
                    }  
                },'json');  
            }  
        });  
    }  
}