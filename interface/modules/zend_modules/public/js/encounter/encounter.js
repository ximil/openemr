/**
 *  Encounter js
 */
// Add issues
function addIssues(){
	var url = '../../../../patient_file/summary/add_edit_issue.php';
	$('#urlWindow').attr('src', url);
	$('#dlg').dialog({  
		title: 'Issues (Injuries/Medical/Allergy)',  
		width:  850,  
		height: 465,  
		closed: false,  
		cache: 	false,  
		modal: 	true  
	});
}

// Check check box options at right side of encounter edit form
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
				//alert("Ajax Fail");
			}
    });
}

// Save New Encounter
function save(){
	if ($('#visitCategory').val() == '') {
		alert('Please select a visit category ');
	} else if ($('#provider').val() == '') {
		alert('Please select a Provider ');
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

function getPDF(){
	var formID = [];
	//formID = $('#formID').val();
	$("input[name^='formID']").each(function(idx) {
    formID[idx] = $(this).val(); //alert($(this).val());
	});
	/*
	var n = $("input[name^='formID']").length;
	var array = $("input[name^='formID']");
	for(i=0; i < n; i++) {
		card_value = array.eq(i).attr('name');
		alert(card_value);
	}*/
	//$("#formID").each(function () {
		//alert($(this).val());
	//},

	$('#encounter').form('submit',{  
		url: './pdf?formID=' + formID,
		//url: './pdf',
		onSubmit: function(){  
			return $(this).form('validate');  
		},  
		success: function(result){
			//window.location.assign("./show");
			alert(result);
		},
		error: function(){
			alert('error');
		}
	});
}

// Preview Report Print
function getPrint(){
	/*$('#formReport').form('submit',{  
		url: './previewPrint',
		onSubmit: function(){  
			return $(this).form('validate');  
		},  
		success: function(result){
			//window.location.assign("./show");
			alert(result);
		},
		error: function(){
			alert('error');
		}
	});*/

	// Print and Preview
	/*var divContents = $("#printPreview").html();
	var printWindow = window.open('http://www.w3schools.com', '', 'height=400,width=800');
	printWindow.document.write('<html><head><title>DIV Contents</title>');
	printWindow.document.write('</head><body >');
	printWindow.document.write(divContents);
	printWindow.document.write('</body></html>');
	printWindow.document.close();*/
	var data = [];
	data[0] = 'Demographics'; // Default settings
	/*$('#checkvalues :checked').each(function() {
    data.push($(this).val());
  });*/
	var printWindow = window.open('./report?data=' + data, '', 'height=auto,width=800,scrollbars=1');
	printWindow.print();

	
}
// Get window size if Resizing 
window.onresize = function() {
    var size = getClientSize();
    //alert("Width: " + size.width + ", Height: " + size.height);
		$('#tabs').tabs('resize');
}
function getClientSize() {
  var width = 0, height = 0;
  if(typeof(window.innerWidth) == 'number') {
				width = window.innerWidth;
        height = window.innerHeight;
  } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
        width = document.documentElement.clientWidth;
        height = document.documentElement.clientHeight;
  } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
        width = document.body.clientWidth;
        height = document.body.clientHeight;
  }
  return {width: width, height: height};
}

// Preview Reort Option Dialog Box
function getReport() {
	windowTitle = 'Preview Report';
	$('#dlg-print').dialog({  
		title: 				windowTitle,  
		width:  			500,  
		height: 			300,  
		closed: 			false,  
		cache: 				false,
		autoOpen: 		false,  
		iconCls:			'icon-report',
		resizable:		true,
		//minimizable:	true,
		//maximizable:	true,
		modal: 				true
	}); 
}

//Save Encounter Notes
function saveNote(){
	$('#notes').form('submit',{
		url: './saveNoteData',  
		onSubmit: function(){  
			return $(this).form('validate');  
		},  
		success: function(result){
			//alert(result);
		}  
	});
}

//Delete Encounter Notes
function deleteNote(){
	$('#confirm').dialog('close');
	$('#notes').form('submit',{
		url: './deleteNoteData',  
		onSubmit: function(){  
			return $(this).form('validate');  
		},  
		success: function(result){
			
		}  
	});
	setTimeout('refresh()', 1000);
}

// Refresh after deleteing
function refresh() {
	window.location.reload();
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
	$("div.panel-tool").mouseover(function() {
			$('#p').panel('expand').panel('refresh');
	});
	$("#patienttable").mouseover(function() {
			$('#p').panel('collapse');
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
	// End Charting shortcut open and close
	
	// Value Set to fields
	setTimeout('valueSet()', 1000);
});

// Dilog window open fron charting menu
var dialogTitle = '';
var dialogUrl = '';
function openNewForm(url, title) {
	var arr = title.split('_');
	var windowTitle = '';
	dialogTitle = title;
	for (var i = 0; i < arr.length; i++) {
		windowTitle += arr[i].toUpperCase() + ' ';
	}
	title = '<span style="color:#fff">' + windowTitle + '</span>';
	$('#urlWindow').attr('src','../../../..' + url);
	dialogUrl = url;
	$('#dlg').dialog({  
		title: 				windowTitle,  
		width:  			1100,  
		height: 			630,  
		closed: 			false,  
		cache: 				false,
		autoOpen: 		false,  
		iconCls:			'icon-ok',
		resizable:		true,
		minimizable:	true,
		maximizable:	true,
		modal: 				true
	}); 
}

// Notations from Notes area
function togle_nation (id,Source) {
	expandedField = $("#nationnote_expand_field").val();
	if(expandedField) {
		var isiPad = navigator.userAgent.match(/iPad/i) != null;
		if(!isiPad){
			var textAreaContent = window.frames['iframe_'+expandedField][0].document.body.innerHTML;
		} else {
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
			} else {
				document.getElementById(textara1+'_optionTD').innerHTML =document.getElementById('iframe_'+expandedField).contentWindow.document.getElementById('options').innerHTML; 
			}
		}
		if(document.getElementById(textara1)){
			document.getElementById(textara1).value = textAreaContent;
			if(!isiPad){
				document.getElementById(textara1).value +="|*|*|*|"+window.frames['iframe_'+textara].document.getElementById('options').innerHTML;
			} else {
				document.getElementById(textara1).value +="|*|*|*|"+document.getElementById('iframe_'+expandedField).contentWindow.document.getElementById('options').innerHTML; 
			}
		}
		$("#nationtextarea_"+expandedField).slideDown();
		$("#nationdiv_"+expandedField).slideUp();
	}
		$("#nationtextarea_"+id).slideUp();
		document.getElementById("iframe_"+id).src=Source;
		$("#nationdiv_"+id).slideDown();
		$('.hide_other_divs').hide();
}

// Show the Vital Notes at top (entering notes)
function rewirte() {
	var height 	= $('#h').val();
	var weight 	= $('#w').val();
	var bmi  		= $('#bmi').val();
	var bp 			= $('#bp').val();
	var bp2			= $('#bp2').val();
	var tmp 		= $('#tmp').val();
	var pl			= $('#pl').val();
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
	txt = $.trim(txt);
	if (txt.length > 0) {
		$('#target').html(txt);
	}
	var cc = $('#ccomplaint').val();
	cc = $.trim(cc);
	if (cc.length > 0) {
		$('#targetCC').html(cc);
	} else {
		$('#targetCC').html('(No Chief Complaint Entered)');
	}
	$('#description').val($('#ccomplaint').val());
}

// Charting Menu
function getChartingMenu() {
	// First Menu (Administrative and Clinical)
	$.ajax({
		type: "POST",
		cache: false,
		dataType: "json",
		url: "./getChartingMenu",
		data: {
			type: 1
				},
		success: function(data) {
			var category;
			var url = '';
			var html = '<ul>';
			for (var i = 0; i < data.length; i++) {
				if (data[i].category == '') {
					data[i].category = 'Miscellaneous';
				}
				if (data[i].category != category) {
					html = html + '<li style="color:#0E2D5F; font-weight: bold">' + data[i].category + "</li>";
				}
				if (data[i].name != 'New Encounter Form') {
					html = html + "<li id='" + data[i].directory + "'><ul>";
					//html = html + "<li>" + '<a href="#" onClick="openNewForm(\'/patient_file/encounter/load_form.php?formname=' + data[i].directory + '\');">';
					if (data[i].form_id && data[i].form_id > 0) {
						url = '/forms/' + data[i].directory + '/view.php?id=' + data[i].form_id;
						if ($("#tabs:contains('" + data[i].name + "')").length == 0) {
							// Adding Tabs
							addTabs(data[i].name, url);
						}
					} else {
						url = '/forms/' + data[i].directory + '/new.php';
					}
					html = html + '<li>' + '<a class="selection" style="overflow-x: hidden; overflow-y: hidden; opacity: 1;" href="#" onClick="openNewForm(\'' + url + '\', \'' + data[i].directory + '\');">';
					html = html + data[i].name;
					html = html + "</a></li>";
					html = html + "</ul></li>";
				}
				category = data[i].category;
			}
			html = html + "</ul>";
			$('#menu').html(html);
		},
		error: function(data){
			//alert("Ajax Fail");
		}
  });
	
	// Second Menu (LBF)
	$.ajax({
		type: "POST",
		cache: false,
		dataType: "json",
		url: "./getChartingMenu",
		data: {
			type: 2
				},
		success: function(data) {
			var category = 'Layout Based';
			var url = '';
			var html = "<ul>";
			for (var i = 0; i < data.length; i++) {
				if (data[i].form_id && data[i].form_id > 0) {
					url = '/patient_file/encounter/load_form.php?formname=' + data[i].option_id + '&id=' + data[i].form_id;
					if ($("#tabs:contains('" + data[i].title + "')").length == 0) {
						// Adding Tabs
						addTabs(data[i].title, url);
					}
				} else {
					url = '/patient_file/encounter/load_form.php?formname=' + data[i].option_id;
				}
				title =  data[i].title;
				html = html + '<li style="color:#0E2D5F; font-weight: bold">' + category + "</li>";
				html = html + "<li><ul>";
				html = html + "<li>" + '<a href="#" onClick="openNewForm(\'' + url + '\', \'' + data[i].title + '\');">';
				html = html + data[i].title;
				html = html + "</a></li>";
				html = html + "</ul></li>";
				category = data[i].category;
			}
			html = html + "</ul>";
			$('#menu2').html(html);
		},
		error: function(data){
			//alert("Ajax Fail");
		}
	});
	
	// Third Menu (Modules)
	$.ajax({
		type: "POST",
		cache: false,
		dataType: "json",
		url: "./getChartingMenu",
		data: {
			type: 3
				},
		success: function(data) {
			var category 	= '';//'Modules';
			var url 		= '';
			var title 		= '';
			var html = "<ul>";
			html = html + '<li style="color:#0E2D5F; font-weight: bold">' + category + "</li>";
			for (var i = 0; i < data.length; i++) {
				if (data[i].mod_ui_name != category) {
					html = html + '<li style="color:#0E2D5F; font-weight: bold">' + data[i].mod_ui_name + "</li>";
				}
				url = data[i].relative_link;
				// Adding Tabs
				if ($("#tabs:contains('" + data[i].menu_name + "')").length == 0) {
					addTabs(data[i].menu_name, url);
				}
				title =  data[i].mod_ui_name ? data[i].mod_ui_name : data[i].menu_name;
				html = html + "<li><ul>";
				html = html + "<li>" + '<a href="JavaScript:void(0);" class="selection" style="overflow-x: hidden; overflow-y: hidden; opacity: 1;" onClick="openNewForm(\'' + url + '\', \'' + data[i].menu_name + '\');">';
				html = html + data[i].menu_name;
				html = html + "</a></li>";
				html = html + "</ul></li>";
				category = data[i].mod_ui_name;
			}
			html = html + "</ul>";
			$('#menu3').html(html);
		},
		error: function(data){
			//alert("Ajax Fail");
		}
	});
}

// Event handler in iFrame
$(document).ready(function () {
	var urlFrame = $("iframe[name='urlWindow']");
	urlFrame.load(function () {
		var frameBody = urlFrame.contents().find('body');
		// Type = button
		var frameEventBtn = frameBody.find('input[type=button]');
		frameEventBtn.click(function(e){
			//alert(e.toSource());
			//alert(frameEventBtn.val());
			if ($('input[type="button"][value="Cancel"]')) {
				//alert($('#urlWindow').attr('src')); 
				$('#dlg').dialog('close');
			}
		});
		// Type = link 
		frameEventBtn = frameBody.find('a');
		frameEventBtn.click(function(e) {//alert('appSearch');
			var url = $(this).attr('href');
			var ids = $(this).attr('id');
			var arr = url.split('/');
			var len = arr.length;
			if (ids != 'appSearch') {
				if (arr[len-1] == 'encounter_top.php') {
					$('#dlg').dialog('close');
				} else {
					setTimeout('closeDilog()', 1000);
				}
			}
		});
		// Type = Submit
		frameEventBtn = frameBody.find('input[type=submit]');
		frameEventBtn.click(function(e) {
			if ($('input[type="button"][value!="Refresh"]')) {
				// Set time delay for Save and close the box 
				setTimeout('closeDilog()', 1000);
			}
		});
	});
	
	// Dialog Box Minimize (Charting Forms)
	var idx = 0;
	$('#dlg').dialog({  
		onMinimize:function(){
			var dialogId = '#dlg_' + (idx++);
			var arr = dialogTitle.split('_');
			var dialogBoxTitle = '';
			for (var i = 0; i < arr.length; i++) {
				dialogBoxTitle += arr[i].toUpperCase() + ' ';
			}
			if ($("#diloagMin:contains('"+dialogBoxTitle+"')").length == 0) {
				$('#diloagMin').append(
				'<div style="float:left"><div class="panel-header panel-header-noborder window-header" style="width:200px;;background: #0068A4; color:#fff; font-size:10px;border:1px solid #95B8E7;"  id="' + 
				dialogId + '">' + dialogBoxTitle + 
				'<div class="panel-tool">'+
				'<a class="panel-tool-max" href="javascript:void(0)" onClick="dialogMax(\'' + dialogUrl + '\', \'' + dialogTitle + '\', \'' + dialogId + '\')"></a>'+
				'<a class="panel-tool-close" href="javascript:void(0)" onClick="dialogClose(\'' +dialogId+ '\')"></a>'+
				'</div></div></div>');
      }
		}
	});
	
	// Submit Event handling in Tab (Url load after submit)
	var iFrameUrl = '';
	$('#tabs').tabs({
    //border:false,  
    onSelect:function(title, index){  
      var iFrameName = 'urlWindowTab' + index;
			iFrameUrl = $('#tabs').find("#" + iFrameName).attr('src');
			var iFrameTab = $("iframe[name='" + iFrameName + "']");
			iFrameTab.load(function () {
				var frameBody = iFrameTab.contents().find('body');
				// Type = button
				var frameEventBtn = frameBody.find('input[type=button]');
				frameEventBtn.click(function(e){
					if ($('input[type="button"][value="Cancel"]')) {
						$('#' + iFrameName).attr('src', iFrameUrl);
					}
				});
				// Type = link 
				frameEventBtn = frameBody.find('a');
				frameEventBtn.click(function(e) {
					var url = $(this).attr('href');
					var ids = $(this).attr('id');
					var arr = url.split('/');
					var len = arr.length;
					if (ids != 'appSearch') {
						if (arr[len-1] == 'encounter_top.php') {
							setTimeout(function(){
								$('#' + iFrameName).attr('src', iFrameUrl);
							},800);
						} else {
							setTimeout(function(){
								$('#' + iFrameName).attr('src', iFrameUrl);
							},800);
						}
					}
				});
				// Type = Submit
				frameEventBtn = frameBody.find('input[type=submit]');
				frameEventBtn.click(function(e) {
					if ($('input[type="button"][value!="Refresh"]')) {
						setTimeout(function(){
							$('#' + iFrameName).attr('src', iFrameUrl);
						},800);
					}
				});
			});
    }  
	});
});

// Close Dialog Box after save
function closeDilog() {
	$('#dlg').dialog('close');
	// Refresh Charting menu (for url change) after saving 
	getChartingMenu();
}

// Dialog Box Maximize
function dialogMax(dialogUrl,dialogTitle, dialogId) {
	$("div[id="+dialogId+"]").remove();
	openNewForm(dialogUrl, dialogTitle);
}

// Close Minimized Dialog Box
function dialogClose(dialogId) {
	$("div[id="+dialogId+"]").remove();
}

// Dynamically Adding Tabs (Saved forms only)
var tabIndex = 0;
function addTabs(title, url){
	tabIndex++;
	$('#tabs').tabs('add',{  
		title: title,  
		content: '<div style="width:auto;height:850px; padding:5px;"><iframe id="urlWindowTab' + tabIndex + '" name="urlWindowTab' + tabIndex + '" src="" style="width:100%; height:100%; border: none;"></iframe></div>',  
		closable: true  
	});
	$('#urlWindowTab' + tabIndex).attr('src','../../../..' + url);
	// Default Select Tab (Note)
	$('#tabs').tabs('select',0);
}  
