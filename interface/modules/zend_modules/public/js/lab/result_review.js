/**
* Result Review Screen
*/

function updateStatus(order_id,status) {
	$.ajax({
		type: "POST",
		cache: false,
		dataType: "html",
		url: "../resultnew/reviewupdate",
		data: {
			order_id: order_id,
      status: status,
			comment :$("#review_comment").val(),
		},
		success: function(data) {
      alert("Saved Successfully");
      document.location.reload();
		},
		error: function(data){
			alert('Ajax Fail');
		}
	});	
}