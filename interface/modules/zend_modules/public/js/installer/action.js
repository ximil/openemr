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
	$.post("./Installer/manage", { modId: id, modAction: action},
	   function(data) {
			if(data=="Success")
				window.location.reload();
			else
				$('#err').html(data).fadeIn().delay(1000).fadeOut();	
	   }
	);
}