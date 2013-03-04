function getLocation(selectedLab){
	if(selectedLab != ''){
		if($('#locationrow').css('display') == 'none'){   
			$('#locationrow').css('display', 'block');
			//$('#locatonCaption').css('display', 'block');
		} 
		/*if($('#billtodiv').css('display') == 'none'){
			$('#billtodiv').css('display', 'block');
			$('#billtoCaption').css('display', 'block');
		}*/
		
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
		//$('#locatonCaption').css('display', 'none');
		//$('#billtodiv').css('display', 'none');
		//$('#billtoCaption').css('display', 'none');
		
	}
}
function getProcedures(inputString,thisID,labID) {
	//alert(document.getElementById(thisID).value);
	$(function () {
		'use strict';
		$.ajax({
			type: "POST",
			url: './search',
			dataType: 'json',
			data: {
				query: inputString,
				inputValue: inputString,
				dependentId: document.getElementById(labID).value,
				type: 'getProcedures'
			},
			async: false,
			cache: false
			//success:function(thedata){
			//	
			//}
		}).done(function (source) {
			var dataArray = $.map(source, function (value, key) { 
										return { 
											value: value, 
											data: key 
										}; 
									    }),
			items = $.map(source, function (value) { return value; });
			//$.mockjax({
			//	url: '*',
			//	responseTime:  200,
			//	response: function (settings) {
			//		var query = settings.data.query,
			//			queryLowerCase = query.toLowerCase(),
			//			suggestions = $.grep(items, function(items) {
			//				 return items.toLowerCase().indexOf(queryLowerCase) !== -1;
			//			}),
			//			response = {
			//				query: query,
			//				suggestions: suggestions
			//			};
			//
			//		this.responseText = JSON.stringify(response);
			//	}
			//});
		loadAOE11("123456",thisID,labID);
			// Initialize ajax autocomplete:
			//$('#' + thisID).autocomplete({
			//	serviceUrl: '/autosuggest/service/url',
			//	onSelect: function(suggestion) {
			//		var arr = suggestion.value.split("-");
			//		$('#' + thisID).val(arr[0]);
			//		$('#procedure_code').val(arr[1]);
			//		$('#procedure_suffix').val(arr[2]);
			//		
			//		//loadaoe();
			//	}
			//});
		});
	
	});
}

function loadAOE11(procedureCode,thisID,labID){
	//procedureCode = "123456";//document.getElementById('procedure_code').value;
	alert(procedureCode+","+thisID+","+labID);
	labVal = $("#"+labID).val();alert(labVal);
	$.ajax({
		type: "POST",
		url: "./search11",
		data: {
				query: procedureCode,
				inputValue: procedureCode,
				dependentId: labVal,
				type: 'loadAOE'
			},
		success: function(){
				alert("OK");
			},
		dataType: 'json'
	      });
	//$(function () {
		//'use strict';
		/*$.ajax({
			type: "POST",
			url: './search11',
			dataType: 'json',
			data: {
				query: procedureCode,
				inputValue: procedureCode,
				dependentId: labVal,
				type: 'loadAOE'
			},
			async: false,
			cache: false,
			success:function(){
				alert("OK");
			}
		});*//*.done(function (source) {
			var dataArray = $.map(source, function (value, key) { 
										return { 
											value: value, 
											data: key 
										}; 
									    }),
			items = $.map(source, function (value) { return value; });alert(items);
			$.mockjax({
				url: '*',
				responseTime:  200,
				response: function (settings) {
					var query = settings.data.query,
						queryLowerCase = query.toLowerCase(),
						suggestions = $.grep(items, function(items) {
							 return items.toLowerCase().indexOf(queryLowerCase) !== -1;
						}),
						response = {
							query: query,
							suggestions: suggestions
						};
			
					this.responseText = JSON.stringify(response);
				}
			});*/
			// Initialize ajax autocomplete:
			//$('#' + thisID).autocomplete({
			//	serviceUrl: '/autosuggest/service/url',
			//	onSelect: function(suggestion) {
			//		//var arr = suggestion.value.split("-");
			//		//$('#' + thisID).val(arr[0]);
			//		//$('#procedure_code').val(arr[1]);
			//		//loadAOE11(arr[1],thisID,labID);
			//		alert(suggestion);
			//	}
			//});
		//});
	
	//});
}
//load();
//function load(){
//setTimeout(loadAOE11,500);
//}
//function loadAOE11(){
//	//if(!document.getElementById('procedure_code').value){
//	//	//alert("jhf");
//	//	load();
//	//}
//	//else{
//	//	alert("dfhgh");
//	procedureCode = document.getElementById('procedure_code').value;
//	var labval = document.getElementById('lab_id').value;
//	//alert($("#lab_id").val());
//	$(function () {//alert("1eeeeeeeeeeeeeeeee");
//		$.ajax({
//			type: "GET",
//			url: './search11',
//			dataType: 'json',
//			
//			data: {
//				query: procedureCode,
//				inputValue1: procedureCode,
//				dependentId1: 2,
//				type1 : "loadAOE"
//			},
//			async: true,
//			cache: false,
//			success:function(thedata){
//				msg = JSON.stringify(thedata);
//				//alert("OKrrrrrrrrrrrrrrrrrrr"+msg);
//			},
//			error:function (xhr, options, error){
//				alert(xhr.status);
//				alert(error);
//			}
//		}).done(function (source) {alert("1");
//			var dataArray = $.map(source, function (value, key) { 
//										return { 
//											value: value, 
//											data: key 
//										}; 
//									    }),
//			items = $.map(source, function (value) { return value; });
//			alert("dfgdg"+items);
//			$.mockjax({
//				url: '*',
//				responseTime:  200,
//				response: function (settings) {
//					var query = settings.data.query,
//						queryLowerCase = query.toLowerCase(),
//						suggestions = $.grep(items, function(items) {
//							 return items.toLowerCase().indexOf(queryLowerCase) !== -1;
//						}),
//						response = {
//							query: query,
//							suggestions: suggestions
//						};
//			
//					this.responseText = JSON.stringify(response);
//				}
//			});
//			// Initialize ajax autocomplete:
//			//$('#' + thisID).autocomplete({
//			//	serviceUrl: '/autosuggest/service/url',
//			//	onSelect: function(suggestion) {
//			//		var arr = suggestion.value.split("-");
//			//		$('#' + thisID).val(arr[0]);
//			//		loadAOE(arr[0],thisID,labID);
//			//	}
//			});
//	});
//	//}
//}