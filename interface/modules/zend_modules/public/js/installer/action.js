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