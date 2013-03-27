function register(status,title,name,method,type){
	$.post("./Installer/register", { st: status, mod_title: title, mod_name: name, mod_method:method,mtype:type},
	   function(data) {
			if(data=="Success")
				window.location.reload();
			else
				$('#err').html(data).fadeIn().delay(1000).fadeOut();	
	   }
	);
}

function manage(id,action){
	if(document.getElementById('mod_enc_menu'))
	modencmenu = document.getElementById('mod_enc_menu').value;
	else
	modencmenu = '';
	if(document.getElementById('mod_nick_name'))
	modnickname = document.getElementById('mod_nick_name').value;
	else
	modnickname = '';
	$.post("./Installer/manage", { modId: id, modAction: action,mod_enc_menu:modencmenu,mod_nick_name:modnickname},
	   function(data) {
			if(data=="Success")
				window.location.reload();
			else
				$('#err').html(data).fadeIn().delay(1000).fadeOut();	
	   }
	);
}

function configure(id,imgpath){
	//$("#ConfigRow_"+id).toggle();
	//$("#ConfigRow_"+id).html('<td colspan="10" align="center"><img src="'+imgpath+'/images/pulling.gif"></td>');
	//$("#ConfigRow_"+id).load("./Installer/configure");
	if($("#ConfigRow_"+id).css("display")!="none"){
		$("#ConfigRow_"+id).fadeOut();
	}
	else{
		$.post("./Installer/configure", { mod_id:id},
			function(data) {
			     $("#ConfigRow_"+id).hide();
			     $("#ConfigRow_"+id).html('<td colspan="10" align="center">'+data+'</td>').fadeIn();	
			}
		);
	}
}

function custom_toggle(obj){
	if($("#"+obj).css("display")!="none"){
		$("#"+obj).fadeOut();
	}
	else{
		$("#"+obj).fadeIn();
	}
}

function SaveMe(frmId){
	$.ajax({
		type: 'POST',
		url: "./Installer/SaveConfigurations",
		data: $('#'+frmId).serialize(),   
		success: function(data){
		   alert(data);
		}
	});
}