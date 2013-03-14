function getLocation(selectedLab){
	if(selectedLab != ''){
		if($('#locationrow').css('display') == 'none'){   
			$('#locationrow').css('display', 'block');
		}
		$.ajax({
			url: "./labLocation",
			type: "POST",
			dataType: 'json',
			data: {
				inputValue: selectedLab,
				type: 'lab_location'
			},
			success: function(data){
				$("#locationName").val(data);
			},
			error: function(){
				alert('ajax error');
			}
		});
		
	} else {
		$('#locationrow').css('display', 'none');
		
	}
}
document.onclick=HideTheAjaxDivs;
function HideTheAjaxDivs(){
	$(".autocomplete-suggestions").css('display','none');
}
function loadAoeQuest(labval,ProcedureCode,procedure,count,suffix){
	//alert(ProcedureCode+"-"+procedure+"-"+count);
	$('#procedures_' + count).val(procedure);
	$('#procedure_code_'+count).val(ProcedureCode);
	$('#procedure_suffix_'+count).val(suffix);
	//alert($('#procedure_' + count).val());
	$.post("./search",{
            type: "loadAOE",
	    inputValue : ProcedureCode,
	    dependentId : labval
        },
	function(data){
	    if(data.response == true){
		    //alert(data.procedureArray);
		    aoeArray = data.aoeArray;
		    j = '<table>';
		    i=0;
		    for(var questioncode in aoeArray){
			i++;
			    splitArr = aoeArray[questioncode].split("|-|");
			    tips = splitArr[3];
			    if(tips)
			    cls = "personPopupTrigger";
			    else
			    cls = "";
			    j +='<tr><td>'+i+'</td><td>'+splitArr[0]+'</td><td><input rel="'+tips+'" class="combo '+cls+'" type="text" name="AOE_'+ProcedureCode+"_"+splitArr[2]+'"></td></tr>';
		    }
		    j+="</table>";
		    //alert(j);
		    contents = "<fieldset><legend>"+procedure+"</legend>";
		    if(j==='<table></table>'){
			$("#AOEtemplate_"+count).css('display','none');
			$("#AOE_"+count).html("");
		    }
		    else{
			$("#AOEtemplate_"+count).css('display','');
			$("#AOE_"+count).html(contents+j+"</fieldset>");
		    }
	    // print success message
	    } else {
		    alert("Failed");
		// print error message
		console.log('could not add');
	    }
	}, 'json');
}
function checkLab(labval){
	if(labval>0){
		$("#internaltime").css('display','none');
	}
	else{
		$("#internaltime").css('display','block');
	}
}
function cloneRow()
{	
	var rowcount = document.getElementById('procedurecount').value;
	var row = document.getElementById("proceduretemplate_1"); // find row to copy
	var AOErow = document.getElementById("AOEtemplate_1"); // find row to copy
	var Diagrow = document.getElementById("diagnosestemplate_1"); // find row to copy
	var table = document.getElementById("ordertable"); // find table to append to
	var Diagclone = Diagrow.cloneNode(true); // copy children too
	//clone.id = newrowid+""+rowcount; // change id or other attributes/contents
	Diagclone.id = "diagnosestemplate_"+rowcount;//
	table.appendChild(Diagclone); // add new row to end of table
	$('#diagnosestemplate_'+rowcount+" > td:last input[type=text]").removeAttr("required");
	$('#diagnosestemplate_'+rowcount+" > td:last input[type=text]").removeAttr("class");
	$('#diagnosestemplate_'+rowcount+" > td:last input[type=text]").attr("class","combo");
	var clone = row.cloneNode(true); // copy children too
	clone.id = "proceduretemplate"+rowcount;
	//$('#proceduretemplate'+rowcount+" > td input[type=text]").id="procedures_"+rowcount;
	//clone.id = newrowid+""+rowcount; // change id or other attributes/contents
	table.appendChild(clone); // add new row to end of table
	//alert($('#proceduretemplate'+rowcount+" > td:last input[type=text]").attr("id"));
	$('#proceduretemplate'+rowcount+" > td:last input[type=text]").attr("id","procedures_"+rowcount);
	$('#proceduretemplate'+rowcount+" > td:last input[type=text]").removeAttr("required");
	$('#proceduretemplate'+rowcount+" > td:last input[type=text]").removeAttr("class");
	$('#proceduretemplate'+rowcount+" > td:last input[type=text]").attr("class","combo");
	$('#proceduretemplate'+rowcount+" > td:last input[type=text]").val("");
	$('#proceduretemplate'+rowcount+" > td:last div").attr("id","prodiv_"+rowcount);
	$('#proceduretemplate'+rowcount+" > td:last div").html("");
	$('#proceduretemplate'+rowcount+" > td:last input[type=hidden]").attr("id","procedure_code_"+rowcount);
	$('#proceduretemplate'+rowcount+" > td:last input[type=hidden]").val("");
	$('#proceduretemplate'+rowcount+" > td:last input[type=hidden]:last").attr("id","procedure_suffix_"+rowcount);
	$('#proceduretemplate'+rowcount+" > td:last input[type=hidden]:last").val("");
	//alert($('#proceduretemplate'+rowcount+" td >input[type=text]").id);
	//$("#"+tableid+" tr:last select").val("");
	var AOEclone = AOErow.cloneNode(true); // copy children too
	//clone.id = newrowid+""+rowcount; // change id or other attributes/contents
	AOEclone.id = "AOEtemplate_"+rowcount
	table.appendChild(AOEclone); // add new row to end of table
	$('#AOEtemplate_'+rowcount).css("display","none");
	$('#AOEtemplate_'+rowcount+" > td:last").attr("id","AOE_"+rowcount);
	$('#AOEtemplate_'+rowcount+" > td:last").html("");
	//$('#AOEtemplate_'+rowcount).css("display","none");
	//$('#AOEtemplate_'+rowcount+" > td:last").attr("id","AOE_"+rowcount);
	//$('#AOEtemplate_'+rowcount+" > td:last").html("");
	//$("#"+tableid+" tr:last select").val("");
	$('#proceduretemplate'+rowcount+" > td:last input[type=text]").focus();
	document.getElementById('procedurecount').value = parseInt(rowcount)+1;
}
function getProcedures(inputString,thisID,labID) {
	countArr = thisID.split("_");
	count = countArr[1];
	var labval = document.getElementById(labID).value;
        $.post("./search",{
            type: "getProcedures",
	    inputValue : inputString,
	    dependentId : labval
        },
	function(data){
	    if(data.response == true){
		    //alert(data.procedureArray);
		    if(data.procedureArray.length>0){
		    procedureArray = data.procedureArray;
		    j = '<ul class="suggestion">';
		    for(var procedure in procedureArray){
			    splitArr = procedureArray[procedure].split("|-|");
			    //alert('"'+splitArr[3]+'"');
			    j +="<li onclick=loadAoeQuest('"+labval+"','"+splitArr[1].replace(/\s+/gi,"&#160;")+"','"+splitArr[3].replace(/\s+/gi,"&#160;")+"','"+count+"','"+splitArr[2].replace(/\s+/gi,"&#160;")+"')><a href='#'>"+splitArr[1]+"-"+splitArr[3]+"</a></li>";
		    }
		    j+="</ul>";
		    //alert(j);
		    $("#prodiv_"+count).css('display','block');
		    $("#prodiv_"+count).html(j);
		    }
		    else{
			$("#prodiv_"+count).html("");
			$("#prodiv_"+count).css('display','none');
		    }
	    // print success message
	    } else {
		    alert("Failed");
		// print error message
		console.log('could not add');
	    }
	}, 'json');
}

/**
* Result View Screen
*/

$(function(){
	$('#dg').edatagrid({
		url: './resultShow', // show all the pending results
		saveUrl: './resultUpdate', // save the result 
		updateUrl: './resultUpdate',
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
				url: "./getResultComments",
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
						url:'./getLabStatus',  
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
								url:'./getLabAbnormal',  
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


function pulldata(lab_id,type)
{
	//alert("hi pull :"+lab_id.value+" type :"+type );
	var labVal = document.getElementById("lab_id").value;
	var actionvar='';
	if(type == 1)
	{
		actionvar = "pullcompendiumtest";
	}
	else if(type == 2)
	{
		actionvar = "pullcompendiumaoe";
	}
	else if(type == 3)
	{			
		actionvar = "pullcompendiumtestaoe";
	}
	
	document.getElementById("ajaxload").innerHTML="<img src = '../images/pulling.gif' >";
	
	
	$.post("pull/"+actionvar,{
           lab_id : labVal
        },
	function(data){
	    if(data.response == true){
		    //alert("hi resp :"+data.response);
		    //alert("hi resp :"+data.result);
		    document.getElementById("ajaxload").innerHTML=data.result;
//		    /*if(data.procedureArray.length>0){
//		    
//		    //alert(j);
//		    //$("#prodiv_"+count).css('display','block');
//		    //$("#prodiv_"+count).html(j);
//		    }
//		    else{
//			//$("#prodiv_"+count).html("");
//			//$("#prodiv_"+count).css('display','none');
//		    }*/
//	    // print success message
	    } else {
		document.getElementById("ajaxload").innerHTML="";
		    alert("Failed");
		// print error message
		console.log('could not add');
	    }
	}, 'json');
}
