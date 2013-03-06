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
function loadAoeQuest(labval,ProcedureCode,procedure,thisID){
	$('#' + thisID).val(procedure);
	$('#procedure_code').val(ProcedureCode)
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
			    j +='<tr><td>'+i+'</td><td>'+splitArr[0]+'</td><td><input class="easyui-validatebox combo" data-options="required:true" type="text" name="AOE_'+ProcedureCode+"_"+splitArr[2]+'"></td></tr>';
		    }
		    j+="</table>";
		    //alert(j);
		    $("#AOE").css('display','block');
		    $("#AOE").html(j);
	    // print success message
	    } else {
		    alert("Failed");
		// print error message
		console.log('could not add');
	    }
	}, 'json');
}
function checkLab(labval){
	if(labval){
		$("#internaltime").css('display','none');
	}
	else{
		$("#internaltime").css('display','block');
	}
}
function getProcedures(inputString,thisID,labID) {
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
		    j = '<ul style="list-style: none; padding: 0; margin: 0;">';
		    for(var procedure in procedureArray){
			    splitArr = procedureArray[procedure].split("|-|");
			    //alert('"'+splitArr[3]+'"');
			    j +="<li><a href='#' onclick=loadAoeQuest('"+labval+"','"+splitArr[1].replace(" ","&#160;")+"','"+splitArr[3].replace(" ","&#160;")+"','"+thisID+"')>"+splitArr[3]+"</a></li>";
		    }
		    j+="</ul>";
		    //alert(j);
		    $("#prodiv").css('display','block');
		    $("#prodiv").html(j);
		    }
		    else{
			$("#prodiv").html("");
			$("#prodiv").css('display','none');
		    }
	    // print success message
	    } else {
		    alert("Failed");
		// print error message
		console.log('could not add');
	    }
	}, 'json');
}