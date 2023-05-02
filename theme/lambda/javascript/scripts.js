(function($) {
 	$(document).ready(function(){

	var offset = 220;
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.back-to-top').fadeIn(duration);
        } else {
            jQuery('.back-to-top').fadeOut(duration);
        }
	});
	jQuery('.back-to-top').click(function(event) {
    	event.preventDefault();
    	jQuery('html, body').animate({scrollTop: 0}, duration);
    	return false;
	});
	
	$(document).on('click', 'button[data-toggle="dropdown"], .action-menu a[data-toggle="dropdown"], .lambda-custom-menu .nav-collapse.in a[data-toggle="dropdown"], .ml-auto.dropdown a[data-toggle="dropdown"]', function(event) {
		event.preventDefault();
  		$(this).next('.dropdown-menu').slideToggle("fast");
	});
	$(document).on('click', function (e) {
    	if(!$('button[data-toggle="dropdown"]').is(e.target) && !$('button[data-toggle="dropdown"]').has(e.target).length && !$('a[data-toggle="dropdown"]').is(e.target) && !$('a[data-toggle="dropdown"]').has(e.target).length && !$(".atto_hasmenu").is(e.target)){
        	$('.dropdown .dropdown-menu:not(.lambda-login)').slideUp("fast");
    	}                       
	});
	$(document).on('click', '.modchooser button[data-action="show-option-summary"], .modchooser button.closeoptionsummary', function(event) {
		$('.carousel-item[data-region="modules"]').toggleClass("active");
		$('.carousel-item[data-region="help"]').toggleClass("active");
	});

	});
}) (jQuery);


var togglesidebar = function() {
  var sidebar_open = Y.one('body').hasClass('sidebar-open');
  if (sidebar_open) {
    Y.one('body').removeClass('sidebar-open');
    M.util.set_user_preference('theme_lambda_sidebar', 'sidebar-closed');
  } else {
    Y.one('body').addClass('sidebar-open');
    M.util.set_user_preference('theme_lambda_sidebar', 'sidebar-open');
  }
};

M.theme_lambda = M.theme_lambda || {};
M.theme_lambda.sidebar =  {
  init: function() {
    Y.one('body').delegate('click', togglesidebar, '#sidebar-btn');
  }
};