define(['jquery'], function ($) {
    return {
        init: function() {
            if (window.location == window.parent.location) {
                // not in iframe, show page elements
                $('nav.navbar').show();
                $('nav.navbar').css('display', 'flex');
                $('div#nav-drawer').show();
                $('section[data-region="blocks-column"]').show();
                $('footer#page-footer').show();
                $('div#course_page_title').css('display', 'none');
                $('.popupicon').css('display', 'none');
            } else {
                // in iframe, hide page elements
                $('body.drawer-open-left').css('margin-left', '0');
                $('div#page').css('margin-top', '0');
                $('section#region-main.has-blocks').css('width', '100%');
                $('div#page-wrapper').css('margin-bottom', '0');
                $('div.context-header-settings-menu').remove();
                $('div.region-main-settings-menu').remove();
                $('div.region_main_settings_menu_proxy').remove();
                $('div.action-menu-trigger').remove();
                $('div.ml-auto').remove();
                $('a.printicon').remove();
                $('header#page-header').css('display', 'none');
                $('.activityinstance a').click(function() {
                    $(this).attr('target', '_blank');
                });
                $('.modtype_assign .activityinstance a').click(function() {
                    $(this).attr('target', '_self');
                });
                $('.modtype_quiz .activityinstance a').click(function() {
                    $(this).attr('target', '_self');
                });
                $('#page-mod-assign-view .submissionlinks a').click(function() {
                    $(this).attr('target', '_blank');
                });
                $('.quizattempt .singlebutton form').click(function() {
                    $(this).attr('target', '_blank');
                    $(this).attr('method', 'get');
                });
            }
            $("body").fadeIn(150);
        }
    };
});
