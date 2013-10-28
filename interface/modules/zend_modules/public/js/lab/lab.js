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
function loadAoeQuest(labval,ProcedureCode,procedure,count,suffix,ordercnt,remote_labval){
	//alert(ProcedureCode+"-"+procedure+"-"+count);
	
  var listprocode = $('#procedure_list').val();
  arrprocode = listprocode.split("|");
  cnt = 0;
  for(var i=0;i<arrprocode.length;i++){
  if(arrprocode[i]==ProcedureCode)
  cnt++;
  }
  if(cnt>0)
  {
    alert("Already used this procedure");
    $('#procedure_code_'+count).val("");
  }
  else{
    $('#procedures_' + count).focus();
	$('#procedures_' + count).val(procedure);
	$('#procedure_code_'+count).val(ProcedureCode);
  $('#procedure_list').val($('#procedure_list').val()+"|"+ProcedureCode);
	$('#procedure_suffix_'+count).val(suffix);
//	alert($('#procedures_' + count).val());
//  alert($('#procedure_code_' + count).val());
//  alert($('#procedure_suffix_' + count).val());
	$.post("./search",{
            type: "loadAOE",
	    inputValue : ProcedureCode,
	    dependentId : labval,
      remoteLabId : remote_labval
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
			    j +='<tr><td>'+i+'</td><td>'+splitArr[0]+'</td><td><input rel="'+tips+'" class="combo '+cls+'" type="text" name="AOE_'+ordercnt+"_"+ProcedureCode+"_"+splitArr[2]+'"></td></tr>';
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
}
function checkLab(labval, id){
	var arrId = id.split('lab_id_');
	var arr = labval.split("|");
	var labvalue = arr[0];
	var type = arr[1];

	if(labvalue > 0){
		$("#internaltimecaption_" + arrId[1]).css('display','none');
		$("#internaltime_" + arrId[1]).css('display','none');
	} else{
		$("#internaltimecation_" + arrId[1]).css('display','block');
		$("#internaltime_" + arrId[1]).css('display','none');
	}
	
	if (type == 1) {
		$("#specimencollectedcaption_" + arrId[1]).css('display','block');
		$("#specimencollectedtd_" + arrId[1]).css('display','block');
		$("#billtocaption_" + arrId[1]).css('display','block');
		$("#billtotd_" + arrId[1]).css('display','block');
		
	} else {
		$("#specimencollectedcaption_" + arrId[1]).css('display','none');
		$("#specimencollectedtd_" + arrId[1]).css('display','none');
		$("#billtocaption_" + arrId[1]).css('display','none');
		$("#billtotd_" + arrId[1]).css('display','none');
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
function getProcedures(inputString,thisID) {
	arr = thisID.split("procedures_");
	ordercntArr = arr[1].split("_");
	labID = "lab_id_"+ordercntArr[0]+"_1"; //alert(inputString + '|' + thisID + '|' + labID);
	count = arr[1];
	var labval1 = document.getElementById(labID).value;
	arrLab = labval1.split("|");
	labval = arrLab[0];
  remote_labval = arrLab[2];
        $.post("./search",{
            type: "getProcedures",
	    inputValue : inputString,
	    dependentId : labval,
      remoteLabId : remote_labval
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
			    j +="<li onclick=loadAoeQuest('"+labval+"','"+splitArr[1].replace(/\s+/gi,"&#160;")+"','"+splitArr[3].replace(/\s+/gi,"&#160;")+"','"+count+"','"+splitArr[2].replace(/\s+/gi,"&#160;")+"','"+ordercntArr[0]+"','"+remote_labval+"')><a href='#'>"+splitArr[1]+"-"+splitArr[3]+"</a></li>";
			}
			j+="</ul>";
			//alert(j);
			//$("#prodiv_"+count).css('display','block');
			//$("#"+thisID).focus();
			$("#prodiv_" + arr[1]).css('display','block');
			$("#prodiv_" + arr[1]).html(j);
		    }
		    else{
			//$("#"+thisID).val("");
			//$("#prodiv_"+count).html("");
			//$("#prodiv_"+count).css('display','none');
			//$('#' + thisID).val('');
			$('#procedure_code_' + count).val('');
			$("#prodiv_" + arr[1]).html("");
			$("#prodiv_" + arr[1]).css('display','none');
		    }
	    // print success message
	    } else {
		    alert("Failed");
		// print error message
		console.log('could not add');
	    }
	}, 'json');
}

function getDiagnoses(inputString,thisID) {
	arr = thisID.split("diagnoses_");
	inputString = $.trim(inputString.substring(inputString.lastIndexOf(';') + 1));
	if (inputString == '') return false;
        $.post("./search",{
            type: "getDiagnoses",
	    inputValue : inputString
        },
	function(data) {
	    if(data.response == true) {
		    if(data.diagnosesArray.length>0) {
			diagnosesArray = data.diagnosesArray;
			j = '<ul class="suggestion">';
			for(var diagnoses in diagnosesArray) {
			    splitArr = diagnosesArray[diagnoses].split("|-|");
			    j +="<li onclick=loadDiagnoses('" + splitArr[0] + "','" + arr[1] + "')><a href='#'>"+splitArr[0]+"-"+splitArr[1]+"</a></li>";
			}
			j += "</ul>";
			$("#diagnodiv_" + arr[1]).css('display','block');
			$("#diagnodiv_" + arr[1]).html(j);
		    }
		    else {
			$("#diagnodiv_" + arr[1]).html("");
			$("#diagnodiv_" + arr[1]).css('display','none');
		    }
	    } else {
		alert("Failed");
		console.log('could not add');
	    }
	}, 'json');

}

var keyWord = '';
function loadDiagnoses(data, id){
	keyWord += data + ';';
	$('#diagnoses_' + id).val(keyWord);
}

function readDiagnoses(thisValue, thisId) {
	keyWord = thisValue;
}

function pulldata(lab_id,type) {
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
