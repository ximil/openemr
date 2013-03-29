/**
* Result View Screen
*/

$(function(){
	$('#dg').edatagrid({
		url: './result/resultShow', // show all the pending results
		saveUrl: './result/resultUpdate', // save the result 
		updateUrl: './result/resultUpdate'
	})
});

// Search options, Status and Ordered Date
function doSearch(){
	$('#dg').edatagrid('load',{  
		statusOrder: $("input[name=searchStatusOrder]").val(),
		statusReport: $("input[name=searchStatusReport]").val(),
		statusResult: $("input[name=searchStatusResult]").val(),
		dtFrom: $("input[name=dtFrom]").val(),
		dtTo: $("input[name=dtTo]").val()
		//dtFrom: $('#acpro_inp1').val(),
		//dtTo: $('#acpro_inp4').val(),
	}) 
}

// Result request with ordser id
function getResult(target) {
	   	   
		var order_id = target;
		$.ajax({
			type: "POST",
			cache: false,
			dataType: "json",
			url: "./result/getLabResultPDF",
			data: {
				order_id: order_id
			},
			success: function(data) {
				$.each(data, function(jsonIndex, jsonValue){
					if (jsonValue['return'] == 1) {
						alert(jsonValue['msg']);
					} else if (jsonValue['return'] == 0) {
						var order_id = jsonValue['order_id'];
						window.location.assign("./result/getLabResultPDF?order_id=" + order_id);
					}
				});
				
			},
			error: function(data){
				alert('Ajax Fail');
			}
		});
	
}

// Requisition request with ordser id
function getRequisition(target) {

	/*var tr = $(target).closest('tr.datagrid-row');
	var rowIndex = parseInt(tr.attr('datagrid-row-index'));
	$('#dg').datagrid('selectRow', rowIndex);
	var row = $('#dg').datagrid('getSelected'); 
	if (row){  */
	
		var order_id = target;
		
		$.ajax({
			type: "POST",
			cache: false,
			dataType: "json",
			url: "./result/getLabRequisitionPDF",
			data: {
				order_id: order_id
			},
			success: function(data) {
				$.each(data, function(jsonIndex, jsonValue){
					if (jsonValue['return'] == 1) {
						alert(jsonValue['msg']);
					} else if (jsonValue['return'] == 0) {
						var order_id = jsonValue['order_id'];
						window.location.assign("./result/getLabRequisitionPDF?order_id=" + order_id);
					}
				});
				
			},
			error: function(data){
				alert('Ajax Fail');
			}
		});
	/*}*/
}

function getLabelDownload(target) {
	
	/*var tr = $(target).closest('tr.datagrid-row');
	var rowIndex = parseInt(tr.attr('datagrid-row-index'));
	$('#dg').datagrid('selectRow', rowIndex);
	var row = $('#dg').datagrid('getSelected'); 
	if (row){  */
		var order_id = target;
		//alert('Order Id is ..' + order_id);
		window.location.assign("./result/getLabelDownload?order_id=" + order_id);
	/*}*/
}

/**
* Show Popup and edit Result Status, Fecility, Comments and Notes
*/
var url;  
function editComments(target){
	
	/*var tr = $(target).closest('tr.datagrid-row');
	var rowIndex = parseInt(tr.attr('datagrid-row-index'));
	$('#dg').datagrid('selectRow', rowIndex);
	var row = $('#dg').datagrid('getSelected');
	if (row.editor == 0) {
		if (row){*/
			$('#dlg').dialog('open').dialog('setTitle','Result');
			$('#titleShow').html(row.result_text);
			$.ajax({
				type: "POST",
				cache: false,
				dataType: "json",
				url: "./result/getResultComments",
				data: {
					prid: row.procedure_result_id
					},
				success: function(data) {
					$.each(data, function(entryIndex, entry){
						$("input[name=formResultStatus]").val(entry['result_status']);
						$("input[name=formResultFacility]").val(entry['facility']);
						$('#formResultComments').val(entry['comments']);
						$('#formResultNotes').val(entry['notes']);
						$('#cc').combobox('setValue',$.trim(entry['result_status']));
						$('#acpro_inp19').val(entry['title']);
					});
				},
				error: function(data){
					//alert("Ajax Fail");
				}
			});		
			$('#fm').form('load',row);
			url	= '';		
		/*}*/
	/*}*/
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
			}
		},
		onLoadSuccess: function() {
			var lastIndex = $('#dg').datagrid('getRows').length-1;
			if (lastIndex > 0) {
				$("#datagrid-row-r2-1-" + lastIndex).hide();
				$("#datagrid-row-r2-2-" + lastIndex).hide();
			}
		},
		 onSelect:function(rowIndex, rowData){
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
						valueField:'value',  
						textField:'label',  
						url:'./result/getLabOptions?opt=status&optId=report',  
						required:false  
					} 
				};
				//if (editor == 0) {
					var opts = $('#dg').datagrid('getColumnOption', 'abnormal');
						opts.editor = {
						type:'combobox',
						options:{  
								valueField:'value',  
								textField:'label',  
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