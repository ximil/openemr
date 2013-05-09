/**
 * Appointments.js
 */
// Group View Data Grid 
$(function(){
        $('#dg').datagrid({
		url: './getAppointmentsData',
		groupField:'Encounter_Provider',  
		view: groupview,
		groupFormatter:function(value, rows){
			var totalPRIMARY_INSURANCE 	= 0;
			var totalINSURANCE_BALANCE 	= 0;
			var totalPATIENT_BALANCE 	= 0;
			for (var i = 0; i < rows.length; i++) {

				/*if (typeof(rows[i].PRIMARY_INSURANCE) == 'undefined') {
					totalPRIMARY_INSURANCE 	= totalPRIMARY_INSURANCE + 0;
				} else {
					totalPRIMARY_INSURANCE 	= parseFloat(totalPRIMARY_INSURANCE ) + parseFloat(rows[i].PRIMARY_INSURANCE);
				}
				if (typeof(rows[i].INSURANCE_BALANCE) == 'undefined') {
					totalINSURANCE_BALANCE 	= totalINSURANCE_BALANCE + 0;
				} else {
					totalINSURANCE_BALANCE 	= parseFloat(totalINSURANCE_BALANCE) + parseFloat(rows[i].INSURANCE_BALANCE);
				}
				if (typeof(rows[i].PATIENT_BALANCE) == 'undefined') {
					totalPATIENT_BALANCE 	= totalPATIENT_BALANCE + 0;
				} else {
					totalPATIENT_BALANCE 	= parseFloat(totalPATIENT_BALANCE) + parseFloat(rows[i].PATIENT_BALANCE);
				}*/
			}
			return '<div><div style="color:#0A58C4; float:left">' + value  + '</div><div style="padding-left:120px;"><div style="color:#0E2D5F; font-size:11px;">Total - </div><div style="padding-left:10px; color:#0E2D5F; font-size:10px">Records <span style="padding-left:66px"></span>: ' + rows.length + '</div></div></div>';
                        //return '<div><div style="color:#0A58C4; float:left">' + value  + '</div><div style="padding-left:120px;"><div style="color:#0E2D5F; font-size:11px;">Total - </div><div style="padding-left:10px; color:#0E2D5F; font-size:10px">Records <span style="padding-left:66px"></span>: ' + rows.length + '</div><div style="padding-left:10px;color:#0E2D5F; font-size:10px">Primary Insurance <span style="padding-left:17px"></span>: ' + totalPRIMARY_INSURANCE +  '</div><div style="padding-left:10px;color:#0E2D5F; font-size:10px">Insurance Balance <span style="padding-left:15px"></span>: ' + totalINSURANCE_BALANCE + '</div><div style="padding-left:10px;color:#0E2D5F; font-size:10px">Patient Balance <span style="padding-left:30px"></span>: ' + totalPATIENT_BALANCE + '</div></div></div>';  
		},
		rowStyler: function(index,row){
			return 'background-color:#E0ECFF;color:#0E2D5F;';
		},
	});
	
        // Search Box Show and Hide
	$('#criteria').combobox({  
		onSelect: function(rec){  
			if (rec.value == 'patient') {
				$("#criteriaDOS").hide("slow");
				$("#criteriaPID").show("slow");
			}
			if (rec.value == 'DOS') {
				$("#criteriaDOS").show("slow");
				$("#criteriaPID").hide("slow");
			}
		}
	});
});

// Search 
function doSearch(){
	var criteria 	= $('input[name="criteria"]').val();
	var patient 	= '';
	var dos 	= '';
	var dtFrom 	= '';
	var dtTo 	= ''
	if (criteria == 'patient') {
		patient = $('#patient').val();
	}
	if (criteria == 'DOS') {
		dos 	= $('#dos').val();
		dtFrom 	= $('input[name="dtFrom"]').val();
		dtTo 	= $('input[name="dtTo"]').val();
	}
	$('#dg').datagrid('load',{  
		criteria:criteria,
		patient:patient,
		dos:dos,
		dtFrom:dtFrom,
		dtTo:dtTo,
	}) 
}