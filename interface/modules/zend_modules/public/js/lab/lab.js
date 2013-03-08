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
			    j +='<tr><td>'+i+'</td><td>'+splitArr[0]+'</td><td><input title="'+tips+'" placeholder="'+tips+'" class="combo" type="text" name="AOE_'+ProcedureCode+"_"+splitArr[2]+'"></td></tr>';
		    }
		    j+="</table>";
		    //alert(j);
		    if(j==='<table></table>'){
			$("#AOEtemplate_"+count).css('display','none');
			$("#AOE_"+count).html("");
		    }
		    else{
			$("#AOEtemplate_"+count).css('display','');
			$("#AOE_"+count).html(j);
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
	var table = document.getElementById("ordertable"); // find table to append to
	var clone = row.cloneNode(true); // copy children too
	clone.id = "proceduretemplate"+rowcount;
	//$('#proceduretemplate'+rowcount+" > td input[type=text]").id="procedures_"+rowcount;
	//clone.id = newrowid+""+rowcount; // change id or other attributes/contents
	table.appendChild(clone); // add new row to end of table
	//alert($('#proceduretemplate'+rowcount+" > td:last input[type=text]").attr("id"));
	$('#proceduretemplate'+rowcount+" > td:last input[type=text]").attr("id","procedures_"+rowcount);
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