$(document).ready(function(){
    $( "#tabs" ).tabs();
		$( "#subtabs" ).tabs();
		var index = $('#tabs ul').index($('#tabId'));
		alert(index);
		//$('#tabs ul').tabs('select', index);
		//$(".pat-det").hide();
		$('.faded').addClass("idleField");
		<?php
		if($saved ==1){
		?>
		setTimeout(function(){
			$('.messageBox').fadeOut('fast');
		}, 3000);
		<?php
		}else{
		?>
			$(".messageBox").hide();
		<?php
		}
		?>
		$('.faded').focus(function(){
			$(this).removeClass("idleField").addClass("focusField");
			if (this.value == this.defaultValue){
				this.value = '';
			}
			if(this.value != this.defaultValue){
				this.select();
			}
		});
		$('.faded').blur(function(){
			if($.trim(this.value) == ''){
				$(this).removeClass("focusField completedField").addClass("idleField");
				this.value = (this.defaultValue ? this.defaultValue : '');
			}else{
				$(this).removeClass("focusField").addClass("completedField");
			}
		});
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}
		$("#patient-name")
		// don't navigate away from the field on tab when selecting an item
		.bind("keydown", function(event){
			if (event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active){
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function(request, response){
				$.ajax({
					url: "get_list.php",
					data: {
						term: request.term,
						type: 'patients',
					},
					success: function(data){
						data = JSON.parse(data);
						response(
							$.map(data, function(item){
								return {
									value : item.value,
									label : item.label,
									id: item.id,
								}
							})
						)
					},
					error: function(){
						alert('Error');
					}
				});
			},
			select: function(event, ui){
				$("#pid").val(ui.item.id);
				$(this).val(ui.item.value);
				return false;
			}
		});
		$("#test-name")
		// don't navigate away from the field on tab when selecting an item
		.bind("keydown", function(event){
			if(event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active){
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function(request, response){
				$.ajax({
					url: "get_list.php",
					data: {
						term: extractLast( request.term ),
						type: 'tests',
						lab: document.getElementById('lab-id').value,
						lab_type: document.getElementById('lab-type').value,
						location: document.getElementById('lab-location-code').value,
					},
					success: function(data){
						data = JSON.parse(data);
						response(
							$.map(data, function(item){
								return {
									value : item.test_description,
									label : item.test_description ,
									code : item.test_code,
									suffix: item.test_code_suffix,
									grp: item.grp,
								}
							})
						)
					},
					error: function(){
						alert('Error');
					}
				});
			},
			minLength: 3,
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function(event, ui){
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				if(ui.item.grp == 'group'){
					$.ajax({
						url: "get_list.php",
						data: {
							term: ui.item.code,
							type: "list_group",
						},
						success: function(thedata){
							//alert(thedata);
							$("#show-test-grp").html(thedata);
						},
						error:function(err){
							alert("ERR"+err);
						}
					});
					$("#show-test-grp").toggle('fast');
					return false;
				}
				if($("#test-id").val()){
					var testid = $("#test-id").val().split("##");
				}else{
					var testid = new Array();
				}
				// add the selected item
				testid.push( ui.item.code );
				$("#test-id").val(testid.join( "##" ));
				if($("#test-code-suffix").val()){
					var suffix = $("#test-code-suffix").val().split("##");
				}else{
					var suffix = new Array();
				}
				// add the selected item
				suffix.push( ui.item.suffix );
				$("#test-code-suffix").val(suffix.join( "##" ));
				return false;
			}
		});
		$(function(){
      $( ".datepicker" ).datepicker({
				changeMonth: true,
        changeYear: true,
				dateFormat: 'yy-mm-dd',
				defaultDate: $('.datepicker').val()
			});
			$( ".datetimepicker" ).datetimepicker({
				changeMonth: true,
        changeYear: true,
				dateFormat: 'yy-mm-dd',
				defaultDate: $('.datepicker').val()
			});
		});
	});
	/*function toggle_result(id){
		$("#pat-det-"+id).toggle('slow');
	}*/
	function list_locations(event){
		var ele = event.target.id;
		var lab_info = document.getElementById(ele).value;
		var lab_details = lab_info.split('-');
		if(lab_details[0] == 'ZHLAB'){
			if(document.getElementById('locationdiv').style.display == 'none'){
				document.getElementById('locationdiv').style.display = '';
			}
			if(document.getElementById('billtodiv').style.display == 'none'){
				document.getElementById('billtodiv').style.display = '';
			}
			$.ajax({
				url: "get_list.php",
				type: "GET",
				datatype: "html",
				data: {
					lab_name: lab_details[2],
				},
				success: function(data){
					//alert(data);
					$("#lab-location").html(data);
				},
				error: function(){
					alert('ajax error');
				}
			});
		}else if(lab_details[0] == 'LOCAL'){
			if(document.getElementById('locationdiv').style.display == ''){
				document.getElementById('locationdiv').style.display = 'none';
			}
			if(document.getElementById('billtodiv').style.display == ''){
				document.getElementById('billtodiv').style.display = 'none';
			}
		}else{
			if(document.getElementById('locationdiv').style.display == ''){
				document.getElementById('locationdiv').style.display = 'none';
			}
			if(document.getElementById('billtodiv').style.display == ''){
				document.getElementById('billtodiv').style.display = 'none';
			}
		}
		document.getElementById('lab-id').value = lab_details[1];
		document.getElementById('lab-type').value = lab_details[0];
	}
	function update_selection(){
		var values = new Array();
		$.each($("input[name='grp_tests']:checked"), function(){
			if($("#test-id").val()){
				var testid = $("#test-id").val().split("##");
			}else{
				var testid = new Array();
			}
			// add the selected item
			testid.push($(this).val());
			$("#test-id").val(testid.join( "##" ));
		});
		$("#show-test-grp").toggle('fast');
	}
