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
		$(".config").hide();		
		$("#ConfigRow_"+id).fadeOut();
	}
	else{
		$.post("./Installer/configure", { mod_id:id},
			function(data) {
				   $(".config").hide();
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

function SaveMe(frmId,mod_id){
  var SelAccIndTab = $('#configaccord').accordion('getSelected');
	if(SelAccIndTab)
	var Acctitle = SelAccIndTab.panel('options').title;
	var SelTab = $('#tab').tabs('getSelected');
  if(SelTab)
  var Tabtitle = SelTab.panel('options').title;
	if(frmId=='aclform'){
	$.ajax({
		type: 'POST',
		url: "./Installer/SaveConfigurations",
		data: $('#'+frmId).serialize(),   
		success: function(data){
				$.each(data, function(jsonIndex, jsonValue){
					if (jsonValue['return'] == 1) {
						$("#ConfigRow_"+mod_id).hide();
						configure(mod_id,'');
						alert(jsonValue['msg']);
						//alert("MSG: "+tit);
						$(document).ready(function(){
				    if(Acctitle)
						$('#configaccord').accordion('select',Acctitle);
						});
					}
				});
		}
	});
	}
	else if(frmId=='hooksform'){
			$.ajax({
				type: 'POST',
				url: "./Installer/SaveHooks",
				data: $('#'+frmId).serialize(),   
				success: function(data){
						$.each(data, function(jsonIndex, jsonValue){
							if (jsonValue['return'] == 1) {
								$("#ConfigRow_"+mod_id).hide();
								configure(mod_id,'');
								alert(jsonValue['msg']);
								$(document).ready(function(){
								if(Tabtitle)
								$('#tab').tabs('select',Tabtitle);
								});
							}
						});
				}
			});	
	}
}

function DeleteACL(aclID,user,mod_id,msg){
  var SelAccIndTab = $('#configaccord').accordion('getSelected');
	if(SelAccIndTab)
	var Acctitle = SelAccIndTab.panel('options').title;
  if(confirm(msg)){
  $.ajax({
		type: 'POST',
		url: "./Installer/DeleteAcl",
		data:{
		aclID: aclID,
		user: user
		},
		success: function(data){
				$.each(data, function(jsonIndex, jsonValue){
					if (jsonValue['return'] == 1) {
						$("#ConfigRow_"+mod_id).hide();
						configure(mod_id,'');
						//$("#tabs_acl").attr("selected","selected");
						//$('#aa').accordion('select','Title1');
						alert(jsonValue['msg']);
						//alert("DEL: "+tit);
						$(document).ready(function(){
				    if(Acctitle)
						$('#configaccord').accordion('select',Acctitle);
						});
					}
				});
		}
	});
	}
}

function DeleteHooks(hooksID,mod_id,msg){
     var SelTab = $('#tab').tabs('getSelected');
  	 if(SelTab)
     var Tabtitle = SelTab.panel('options').title;
			if(confirm(msg)){
			$.ajax({
				type: 'POST',
				url: "./Installer/DeleteHooks",
				data:{
				hooksID: hooksID
				},
				success: function(data){
						$.each(data, function(jsonIndex, jsonValue){
							if (jsonValue['return'] == 1) {
								$("#ConfigRow_"+mod_id).hide();
								configure(mod_id,'');
								//$("#tabs_hooks").attr("selected","selected");
								alert(jsonValue['msg']);
								$(document).ready(function(){
								if(Tabtitle)
								$('#tab').tabs('select',Tabtitle);
								});
							}
						});
				}
			});	
			}
}