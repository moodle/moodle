$(function() {
	if($('#loginoverlay').length) {
		$(window).bind("load", function() {
			var pos = $("#page-content").offset();
			$('#loginoverlay').offset({'top': pos.top - 25, 'left': $("#page-content-wrapper > .navbar > .container-fluid").offset().left});
			$('#loginoverlay').height($("#page").height() + 16);
			$('#loginoverlay').width($("#page-footer > div.container-fluid").width());
			$('#loginoverlay').css("display", "block");
		});
		
		$(window).resize(function (event) {
			var pos = $("#page-content").offset();
			$('#loginoverlay').offset({'top': pos.top - 25, 'left': $("#page-content-wrapper > .navbar > .container-fluid").offset().left});
			$('#loginoverlay').height($("#page").height() + 16);
			$('#loginoverlay').width($("#page-footer > div.container-fluid").width());
			$('#loginoverlay').css("display", "block");
		}); 
	
	}

});