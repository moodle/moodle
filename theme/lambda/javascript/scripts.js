(function($) {
	$(document).ready(function(){

	function refreshlogs() {
		$('.reportbuilder-table .dropdown .dropdown-menu a.dropdown-item[data-action="report-action-popup"]').each(function(){
			var action = $(this).attr("data-popup-action");
			var replace = action.indexOf("\\\/");
			if (replace != -1) {
				var newattrval = action.replaceAll("\\","");
				$(this).attr("data-popup-action",newattrval);
			}
		});
	}

	$(".path-grade.ver-m41 .tertiary-navigation-selector .select-menu .dropdown-menu li:first-child li.dropdown-item:nth-child(5)").remove();
	$(".path-grade.ver-m41 .tertiary-navigation-selector .select-menu .dropdown-menu li:first-child li.dropdown-item:nth-child(6)").remove();
	$(".path-grade.ver-m41 .block.block_settings .block_tree.list li.contains_branch:first-child li.item_with_icon:nth-child(4)").remove();
	$(".path-grade.ver-m41 .block.block_settings .block_tree.list li.contains_branch:first-child li.item_with_icon:nth-child(5)").remove();
	
	$(".message.clickable.d-flex").last().focus({focusVisible: true});

	$(".format-tiles.editing li.course-section .course-content-item-content").removeClass("show");
	$(".path-course.format-topics .course-content-item-content.collapse.show").addClass("in");
	$(".path-course.format-weeks .course-content-item-content.collapse.show").addClass("in");
	$(".editing .cm_action_menu .dropdown-item.editing_moveleft").click(function(event) {
		event.preventDefault();
		var listItem = $(this).closest("li");
		listItem.removeClass("indented");
	});
	$(".initials-selector .initialswidget").on("click", function(){
		$(".initials-selector .initialsdropdown.dropdown-menu").toggleClass("show");
	});
	$(".initials-selector .initialsdropdown .btn-outline-secondary").on("click", function(){
		$(".initials-selector .initialsdropdown.dropdown-menu").removeClass("show");
	});		

	$('.select-menu ul.dropdown-menu li.dropdown-item').each(function(){
		const url = $(this).attr("data-value");
		this.innerHTML = '<a href="'+ url +'">' + this.innerHTML +'</a>';
	});

	var pageURL = $(location).attr("href");
	if (pageURL.indexOf("message/index.php?view=contactrequests") >= 0) {
		$(".nav-pills .nav-item:first-child:not(.checked)").removeClass("active");
		$(".nav-pills .nav-item:nth-child(2):not(.checked)").addClass("active");
		$(".nav-pills .nav-item").addClass("checked");

		$(".body-container .tab-content .tab-pane:nth-child(2):not(.checked)").addClass("in");
		$(".body-container .tab-content .tab-pane:first-child:not(.checked)").removeClass("in");
		$(".body-container .tab-content .tab-pane").addClass("checked");

		$(".nav-pills .nav-item .nav-link[data-action='show-contacts-section']").on("click", function(){
			$(".body-container .tab-content .tab-pane").removeClass("show");
		});
		$(".nav-pills .nav-item .nav-link[data-action='show-requests-section']").on("click", function(){
			$(".body-container .tab-content .tab-pane").removeClass("show");
		});
	}

	if (pageURL.indexOf("admin/tasklogs.php") >= 0) {
		refreshlogs();
		$(document).on('click', '.pagination .page-link, a[data-action="showcount"]', function(event) {
			setTimeout(function() {
				refreshlogs();
			}, 500);
		});
	}

	var offset = 1;
    var duration = 1;
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

	var pathname = window.location.href;
	var index = pathname.indexOf("&section=");
	var backlink = pathname.substring(0,index);
	$("#back-to-main-link").attr('href', backlink);
	
	$(document).on('click', 'button[data-toggle="dropdown"], .action-menu a[data-toggle="dropdown"], .lambda-custom-menu .nav-collapse.in a[data-toggle="dropdown"], .ml-auto.dropdown a[data-toggle="dropdown"], .tertiary-navigation-selector .dropdown.select-menu .btn[data-toggle="dropdown"]', function(event) {
		event.preventDefault();
  		$(this).next('.dropdown-menu').slideToggle("fast");
	});
	$(document).on('click', function (e) {
    	if(!$('button[data-toggle="dropdown"]').is(e.target) && !$('button[data-toggle="dropdown"]').has(e.target).length && !$('a[data-toggle="dropdown"]').is(e.target) && !$('a[data-toggle="dropdown"]').has(e.target).length && !$('.btn[data-toggle="dropdown"]').is(e.target) && !$('.btn[data-toggle="dropdown"]').has(e.target).length && !$(".atto_hasmenu").is(e.target) && !$(".reportbuilder-filters-sidebar *").is(e.target)){
        	$('.dropdown .dropdown-menu:not(.lambda-login)').slideUp("fast");
    	}                       
	});
	$(document).on('click', '.modchooser button[data-action="show-option-summary"], .modchooser button.closeoptionsummary', function(event) {
		$('.carousel-item[data-region="modules"]').toggleClass("active");
		$('.carousel-item[data-region="help"]').toggleClass("active");
	});
	$(document).on('click', '#dynamictabs-tabs .nav-item', function(event) {
		$('#editor-tab').removeClass("active");
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