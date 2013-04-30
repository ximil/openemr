/**
 *  Encounter js
 */

function addIssues(){
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
 

function selectIssue(thisValue) {

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

//Save New Encounter Notes
function saveNote(){
    $('#notes').form('submit',{
        url: './saveNoteData',  
        onSubmit: function(){  
            return $(this).form('validate');  
        },  
        success: function(result){
        	alert(result);
        }  
    });
}

$(function(){
	// Charting Panell Collapse
	$('#p').panel('collapse',true);
	
	// Popup window border background color chage
	$('.window').css({"background":"#0068A4"});

	// Charting shortcut open and close on mosue event 
	$("div.panel-title").mouseover(function() {
			$('#p').panel('expand').panel('refresh');
	});
	
	var m_out	= 0;
	var m_over	= 0;
  	
	$("div#p").mouseout(function(){
		m_out	= 1;
		m_over	= 0;
		showDialog(m_over,m_out);
		//$('#p').panel('collapse');
	}).mouseover(function(){
		m_out	= 0;
		m_over	= 1;
		showDialog(m_over,m_out);
	});

	function showDialog(m_over,m_out) {
		if(m_out == 1 )
			$('#p').panel('collapse');
		else if(m_over == 1 )
			$('#p').panel('expand');
	}
  	
  	$(document).click(function(e){
  		$('#p').panel('collapse');
	});
  	
	// Seen By combo box setting
	/*$.ajax({
        type: "POST",
        cache: false,
        dataType: "json",
        url: "./getProviders",
        data: {
        	type: 1,
            },
        success: function(data) {
			var options;
        	for (var i = 0; i < data.length; i++) {
            	options += '<option value="' + data[i].value+ '">' + data[i].label+ '</option>';
        	}
        	 $("#providerstest").html(options);

        },
        error: function(data){
                alert("Ajax Fail");
        }
    });*/
	
	// Value Settings
	setTimeout('valueSet()', 1000);
	
});

function openNewForm(url, title) {
	var arr = title.split('_');
	var title = '';
	for (var i = 0; i < arr.length; i++) {
		title += arr[i].toUpperCase() + ' ';
	}
	title = '<span style="color:#fff">' + title + '</span>';
	$('#urlWindow').attr('src','../../../..' + url);
	$('#dlg').dialog({  
		title: 			title,  
		width:  		850,  
		height: 		400,  
		closed: 		false,  
		cache: 			false,
		autoOpen: 		false,  
		//href: 			'../../../..' + url,
		iconCls:		'icon-ok',
		resizable:		true,
		minimizable:	true,
		maximizable:	true,
		modal: 			true,
	}); 
}

function togle_nation(id,Source) {
	expandedField = $("#nationnote_expand_field").val();
	if(expandedField){
		var isiPad = navigator.userAgent.match(/iPad/i) != null;
		if(!isiPad){
			var textAreaContent = window.frames['iframe_'+expandedField][0].document.body.innerHTML;
		}
		else{
			var textAreaContent = document.getElementById('iframe_'+expandedField).contentWindow.document.getElementById('textarea1').value;
		}
		textara = expandedField;
		textara1 = 'form_'+expandedField;
		mainform=window.parent.document;
		if(document.getElementById(textara1+'_div'))
			document.getElementById(textara1+'_div').innerHTML = textAreaContent;
		if(document.getElementById(textara1))
			document.getElementById(textara1).value = textAreaContent;
		if(document.getElementById(textara1+'_optionTD')){
			if(!isiPad){
				document.getElementById(textara1+'_optionTD').innerHTML =window.frames['iframe_'+textara].document.getElementById('options').innerHTML;
			}
			else{
				document.getElementById(textara1+'_optionTD').innerHTML =document.getElementById('iframe_'+expandedField).contentWindow.document.getElementById('options').innerHTML; 
			}
		}
		if(document.getElementById(textara1)){
			document.getElementById(textara1).value = textAreaContent;
			if(!isiPad){
				document.getElementById(textara1).value +="|*|*|*|"+window.frames['iframe_'+textara].document.getElementById('options').innerHTML;
			}
			else{
				document.getElementById(textara1).value +="|*|*|*|"+document.getElementById('iframe_'+expandedField).contentWindow.document.getElementById('options').innerHTML; 
			}
		}
		$("#nationtextarea_"+expandedField).slideDown();
		$("#nationdiv_"+expandedField).slideUp();
	}
	 //document.getElementById("nationnote_expand_field").value=id;
	 $("#nationtextarea_"+id).slideUp();
	 document.getElementById("iframe_"+id).src=Source;
	 $("#nationdiv_"+id).slideDown();
	 $('.hide_other_divs').hide();
}

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
        		if (data[i].form_id && data[i].form_id > 0) {
        			url = '/forms/' + data[i].directory + '/view.php?id=' + data[i].form_id;
        		} else {
        			url = '/forms/' + data[i].directory + '/new.php';
        		}
        		
        		//alert(data[i].form_id + ' | ' + url);

        		html = html + '<li>' + '<a class="selection" style="overflow-x: hidden; overflow-y: hidden; opacity: 1;" href="#" onClick="openNewForm(\'' + url + '\', \'' + data[i].directory + '\');">';
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
        		html = html + "<li>" + '<a href="JavaScript:void(0);" class="selection" style="overflow-x: hidden; overflow-y: hidden; opacity: 1;" onClick="openNewForm(\'' + url + '\', \'' + data[i].mod_nick_name + '\');">';
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



