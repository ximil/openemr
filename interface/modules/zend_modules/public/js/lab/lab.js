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
			}/*,
			success: function(thedata){
					alert(thedata);
					//document.getElementById('template_sentence').innerHTML = thedata;
					},
			    error:function(){
				alert("fail");
			    }*/
		}).done(function (source) {
			var dataArray = $.map(source, function (value, key) { 
										return { 
											value: value, 
											data: key 
										}; 
									    }),
			items = $.map(source, function (value) { return value; });
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
			});
		
			// Initialize ajax autocomplete:
			$('#' + thisID).autocomplete({
				serviceUrl: '/autosuggest/service/url',
				onSelect: function(suggestion) {
					var arr = suggestion.value.split("-");
					$('#' + thisID).val(arr[0]);
				}
			});
		});
	});
}