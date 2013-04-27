/**
 *  Encounter js
 */

function addIssues(){

	//$('#dlg').dialog('refresh', '../../../../patient_file/summary/add_edit_issue.php'); 
	$('#dlg').dialog({  
		title: 'Issues (Injuries/Medical/Allergy)',  
		width:  850,  
		height: 600,  
		closed: false,  
		cache: false,  
		href: '../../../../patient_file/summary/add_edit_issue.php',  
		modal: true  
	}); 
 
	//$('#dlg').dialog('open').dialog('setTitle','Issues (Injuries/Medical/Allergy)');
	//$('#titleShow').html('Issues');

}
 

function selectIssue(thisValue) {alert('thisValue');
	$('#title').val(thisValue);
}

/*$('#issue').combobox({
	onSelect: function(param){alert('test');
			
	}
});*/

function checkAMC(param) {
    $.ajax({
        type: "POST",
        cache: false,
        dataType: "json",
        url: "./checkAMC",
        data: {
                id: param
            },
        success: function(data) {
			if (data.AMCTYPE == 'PER') {
				if (data.empty != 'Y') {
					$("#provEduRes").attr('checked', true);
				}
			}
			if (data.AMCTYPE == 'PCS') {
				if (data.empty != 'Y') {
					$("#provCliSum").attr('checked', true);
				}
			}
			if (data.AMCTYPE == 'TTC') {
				if (data.empty != 'Y') {
		        		$("#transTrandCare").attr('checked', true);
				}
				if (data.date_completed != 'empty') {
					$("#medReconcPerf").attr('checked', true);
				}
			}
        },
        error: function(data){
                alert("Ajax Fail");
        }
    });
}

// Save New Encounter
function save(){
	if ($('#visitCategory').val() == '') {
		alert('Please select a visit category ');
	} else {
	    $('#encounter').form('submit',{  
	        url: './saveData',  
	        onSubmit: function(){  
	            return $(this).form('validate');  
	        },  
	        success: function(result){
	        	window.location.assign("./show");
	        }  
	    });
	}
}

// Charting Panell Collapse
$(function(){
	$('#p').panel('collapse',true);
});

// Show the Notes at top while entering notes
function rewirte() {
	var height 	= $('#h').val();
	var weight 	= $('#w').val();
	var bmi  	= $('#bmi').val();
	var bp 		= $('#bp').val();
	var bp2		= $('#bp2').val();
	var tmp 	= $('#tmp').val();
	var pl		= $('#pl').val();
	var rrate 	= $('#rrate').val();

	if (height != '') {
		height = 'Ht: ' + height + ' in';
	}
	if (weight != '') {
		weight = 'wt: ' + weight + ' lb';
	}
	if (bmi != '') {
		bmi = 'BMI: ' + bmi ;
	}
	if (bp != '') {
		bp = 'BP: ' + bp;
		if (bp2 != '') {
			bp = bp + ' / ' + bp2;
		}
		bp = bp + ' mmHg' 
	}
	if (tmp != '') {
		tmp = 'Temp: ' + tmp + ' F';
	}
	if (pl != '') {
		pl = 'Plsue: ' + pl + ' bpm';
	}
	if (rrate != '') {
		rrate = 'RR: ' + rrate + ' rpm';
	}
	
	var txt 	= height + ' ' + weight + ' ' + bmi + ' ' + bp + ' ' + tmp + ' ' + pl + ' ' + rrate;
	$('#target').html(txt);

	$('#targetCC').html($('#ccomplaint').val());
}

// Charting Menu
function getChartingMenu() {
	// First Menu
	$.ajax({
        type: "POST",
        cache: false,
        dataType: "json",
        url: "./getChartingMenu",
        data: {
        	type: 1,
            },
        success: function(data) {
        	var category;
        	var url = '';
        	var html = '<ul>';
        	for (var i = 0; i < data.length; i++) {
        		if (data[i].category != category) {
        			html = html + '<li style="color:#0E2D5F; font-weight: bold">' + data[i].category + "</li>";
        		}
        		html = html + "<li><ul>";
        		//html = html + "<li>" + '<a href="#" onClick="openNewForm(\'/patient_file/encounter/load_form.php?formname=' + data[i].directory + '\');">';
        		url = '/forms/' + data[i].directory + '/new.php';
        		html = html + "<li>" + '<a href="#" onClick="openNewForm(\'' + url + '\', \'' + data[i].directory + '\');">';
        		html = html + data[i].name;
        		html = html + "</a></li>";
        		html = html + "</ul></li>";
        		category = data[i].category;
        	}
        	html = html + "</ul>";
        	$('#menu').html(html);
        },
        error: function(data){
                alert("Ajax Fail");
        }
    });
	
	// Second Menu
	$.ajax({
        type: "POST",
        cache: false,
        dataType: "json",
        url: "./getChartingMenu",
        data: {
        	type: 2,
            },
        success: function(data) {
        	var category;
        	var html = "<ul>";
        	for (var i = 0; i < data.length; i++) {
        		if (data[i].category != category) {
        			html = html + '<li style="color:#0E2D5F; font-weight: bold">' + data[i].category + "</li>";
        		}
        		html = html + "<li><ul>";
        		html = html + "<li>" + '<a href="#" onClick="alert(\'' + data[i].name + '\');">';
        		html = html + data[i].name;
        		html = html + "</a></li>";
        		html = html + "</ul></li>";
        		category = data[i].category;
        	}
        	html = html + "</ul>";
        	$('#menu2').html(html);
        },
        error: function(data){
                alert("Ajax Fail");
        }
    });
	
	// Third Menu
	$.ajax({
        type: "POST",
        cache: false,
        dataType: "json",
        url: "./getChartingMenu",
        data: {
        	type: 3,
            },
        success: function(data) {
        	var category 	= 'Modules';
        	var url 		= '';
        	var title 		= '';
        	var html = "<ul>";
        	html = html + '<li style="color:#0E2D5F; font-weight: bold">' + category + "</li>";
        	for (var i = 0; i < data.length; i++) {
        		url = data[i].relative_link;
        		title =  data[i].mod_nick_name ? data[i].mod_nick_name : data[i].mod_name;
        		html = html + "<li><ul>";
        		html = html + "<li>" + '<a href="JavaScript:void(0);" onClick="openNewForm(\'' + url + '\', \'' + data[i].mod_nick_name + '\');">';
        		html = html + data[i].mod_nick_name;
        		html = html + "</a></li>";
        		html = html + "</ul></li>";
        	}
        	html = html + "</ul>";
        	$('#menu3').html(html);
        },
        error: function(data){
                alert("Ajax Fail");
        }
    });
}



