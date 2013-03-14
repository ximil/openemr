/**
* Result View Screen
*/

$(function(){
	$('#dg').edatagrid({
		url: './result/resultShow', // show all the pending results
		saveUrl: './result/resultUpdate', // save the result 
		updateUrl: './result/resultUpdate',
		//destroyUrl: 'destroy_user.php'
	});
});

// Search options, Status and Ordered Date
function doSearch(){
	$('#dg').edatagrid('load',{  
		statusOrder: $("input[name=searchStatusOrder]").val(),
		statusReport: $("input[name=searchStatusReport]").val(),
		statusResult: $("input[name=searchStatusResult]").val(),
		dtFrom: $('#acpro_inp1').val(),
		dtTo: $('#acpro_inp4').val(),
	}); 
}

// Result request with ordser id
function getResult(target) {
	var tr = $(target).closest('tr.datagrid-row');
	var rowIndex = parseInt(tr.attr('datagrid-row-index'));
	$('#dg').datagrid('selectRow', rowIndex);
	var row = $('#dg').datagrid('getSelected'); 
	if (row){  
		var order_id = row.procedure_order_id;
		window.location.assign("./getlabresult?order_id=" + order_id);
	}
}

// Requisition request with ordser id
function getRequisition(target) {
	var tr = $(target).closest('tr.datagrid-row');
	var rowIndex = parseInt(tr.attr('datagrid-row-index'));
	$('#dg').datagrid('selectRow', rowIndex);
	var row = $('#dg').datagrid('getSelected'); 
	if (row){  
		var order_id = row.procedure_order_id;
		window.location.assign("./getlabrequisition?order_id=" + order_id);
	}
}

function getLabelDownload(target) {
	var tr = $(target).closest('tr.datagrid-row');
	var rowIndex = parseInt(tr.attr('datagrid-row-index'));
	$('#dg').datagrid('selectRow', rowIndex);
	var row = $('#dg').datagrid('getSelected'); 
	if (row){  
		var order_id = row.procedure_order_id;
		alert('Order Id is ..' + order_id);
		//window.location.assign("./getLabelDownload?order_id=" + order_id);
	}
}

/**
* Show Popup and edit Result Status, Fecility, Comments and Notes
*/
var url;  
function editComments(target){
	var tr = $(target).closest('tr.datagrid-row');
	var rowIndex = parseInt(tr.attr('datagrid-row-index'));
	$('#dg').datagrid('selectRow', rowIndex);
	var row = $('#dg').datagrid('getSelected');
	if (row.editor == 0) {
		if (row){
			$('#dlg').dialog('open').dialog('setTitle','Result');
			$('#titleShow').html(row.result_text);
			$.ajax({
				type: "POST",
				cache: false,
				dataType: "json",
				url: "./result/getResultComments",
				data: {
					prid: row.procedure_result_id,
					},
				success: function(data) {
					$.each(data, function(entryIndex, entry){
						$("input[name=formResultStatus]").val(entry['result_status']);
						$("input[name=formResultFacility]").val(entry['facility']);
						$('#formResultComments').val(entry['comments']);
						$('#formResultNotes').val(entry['notes']);
						$('#cc').combobox('setValue',$.trim(entry['result_status']));
						$('#acpro_inp19').val(entry['title']);
						/* $("#cc option").filter(function() {
							return $(this).val() == entry['result_status'];
						}).get(0).selected = true; */
					});
				},
				error: function(data){
					//alert("Ajax Fail");
				}
			});		
			$('#fm').form('load',row);
			url	= '';		
		}
	}
} 

// Save the Popup form details to field comments
function saveComments() {
	var row = $('#dg').datagrid('getSelected');
	if (row){
		row.comments = $("input[name=formResultStatus]").val() + '|' + $("input[name=formResultFacility]").val() + '|' + $("#formResultComments").val() + '|' + $("#formResultNotes").val();
	}
	$('#dlg').dialog('close');
}

// Freezing cells 
$(function(){ 
	$('#dg').edatagrid({
		rowStyler: function(index,row){
			if (row.editor == 1) {
				return 'background-color:#E0ECFF;color:#0E2D5F;';
				//$('#dg').edatagrid('cancelRow');
				//$('#dg').edatagrid('cancelEdit')
				//$('#dg').datagrid('unselectAll');
			}
		},
		onLoadSuccess: function() {
			var lastIndex = $('#dg').datagrid('getRows').length-1;
			if (lastIndex > 0) {
				//$('#dg').datagrid('deleteRow', lastIndex);
				$("#datagrid-row-r2-1-" + lastIndex).hide();
				$("#datagrid-row-r2-2-" + lastIndex).hide();
			}
			
		},
		 onSelect:function(rowIndex, rowData){
			//$('#dg').edatagrid('cancelRow');
			//var curRow = $('#dg').datagrid('selectRow', rowIndex);
			//$('#dg').datagrid('unselectAll');
			var row = $('#dg').datagrid('getSelected');
			var fieldValue = $.trim(rowData.procedure_name);
			var editor = rowData.editor;
			if (editor == 1) {
				//alert('Disabled');
				$('#dg').edatagrid('cancelRow');
				$('#dg').edatagrid('cancelEdit');
				$('#dg').focus();
			}
			if (rowIndex == 0) {
				$('#datagrid-row-r2-2-0').focus();
			}
			
			if (fieldValue.length == 0 || editor == 1) {
				var opts = $('#dg').datagrid('getColumnOption', 'date_report');
				opts.editor = {
					type:'',
				
				};
				var opts = $('#dg').datagrid('getColumnOption', 'date_collected');
				opts.editor = {
					type:'',
				
				};
				var opts = $('#dg').datagrid('getColumnOption', 'specimen_num');
				opts.editor = {
					type:'',
				
				};
				var opts = $('#dg').datagrid('getColumnOption', 'report_status');
				opts.editor = {
					type:'',
				
				};
				if (editor == 1) {
					var opts = $('#dg').datagrid('getColumnOption', 'abnormal');
						opts.editor = {
						type:'',
				
					};
					var opts = $('#dg').datagrid('getColumnOption', 'result');
						opts.editor = {
						type:'',
				
					};
					var opts = $('#dg').datagrid('getColumnOption', 'pt2_units');
						opts.editor = {
						type:'',
				
					};
					var opts = $('#dg').datagrid('getColumnOption', 'pt2_range');
						opts.editor = {
						type:'',
				
					};
				}
			} else {
				var opts = $('#dg').datagrid('getColumnOption', 'date_report');
				opts.editor = {
					type:'datebox',
					options:{
						required:true
					}
				};
				var opts = $('#dg').datagrid('getColumnOption', 'date_collected');
				opts.editor = {
					type:'datebox',
				
				};
				var opts = $('#dg').datagrid('getColumnOption', 'specimen_num');
				opts.editor = {
					type:'text',
				
				};
				var opts = $('#dg').datagrid('getColumnOption', 'report_status');
				opts.editor = {
					type:'combobox',
					options:{  
						valueField:'option_id',  
						textField:'title',  
						url:'./result/getLabOptions?opt=status&optId=report',  
						required:false  
					} 
				};
				//if (editor == 0) {
					var opts = $('#dg').datagrid('getColumnOption', 'abnormal');
						opts.editor = {
						type:'combobox',
						options:{  
								valueField:'option_id',  
								textField:'title',  
								url:'./result/getLabOptions?opt=abnormal&optId=abnormal',  
								required:false  
							} 
					};
					var opts = $('#dg').datagrid('getColumnOption', 'result');
						opts.editor = {
						type:'text',
				
					};
					var opts = $('#dg').datagrid('getColumnOption', 'pt2_units');
						opts.editor = {
						type:'text',
				
					};
					var opts = $('#dg').datagrid('getColumnOption', 'pt2_range');
						opts.editor = {
						type:'text',
				
					};
				//}
			}
			fieldValue = '';
			//$('#dg').datagrid('unselectRow',rowIndex);
			return;
		}, 
	});
});