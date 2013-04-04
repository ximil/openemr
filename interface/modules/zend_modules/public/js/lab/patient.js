function getPatient(inputString,thisID,url){
  $.post(url,{
		type: "getPatient",
	  inputValue : inputString
	},
	function(data){
		if(data.response == true){
			//alert(data.patientArray);
			if(data.patientArray.length>0){
				patientArray = data.patientArray;
				j = '<ul class="suggestion">';
				for(var patient in patientArray){
					splitArr = patientArray[patient].split("|-|");
					//alert('"'+splitArr[3]+'"');
					j +="<li onclick=loadPatient('"+splitArr[0].replace(/\s+/gi,"&#160;")+"','"+splitArr[1]+"')><a href='#'>"+splitArr[0]+" - "+splitArr[1]+"</a></li>";
				}
				j += "</ul>";
				//alert(j);
				$("#patdiv").css('display','block');
				$("#patdiv").html(j);
			}else{
				$("#patdiv").html("");
				$("#patdiv").css('display','none');
			}
			// print success message
		}else{
			alert("Failed");
			// print error message
			console.log('could not add');
	  }
	}, 'json');
}

function loadPatient(patname,pid){
	$('#search_patient').val(patname);
	$('#patient_id').val(pid);
	$("#patdiv").css('display','none');
}