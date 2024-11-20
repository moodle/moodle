// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * theme.js
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery'], function($) {

    var ThemeAcademi = function() {
        this.initialize();
    };

    ThemeAcademi.prototype.initialize = function() {
        var img = $("nav#header").find('.avatar').find('img[src$="/u/f2"]');
        var src = img.attr('src');
        img.attr('src', src + "_white");
        /* ------- Check navbar button status -------- */
        if ($("#header .navbar-nav button").attr('aria-expanded') === "true") {
            $("#header .navbar-nav").find('button').addClass('is-active');
        }
        /* ------ Event for change the drawer navbar style  ------ */
        $("#header .navbar-nav button").click(function() {
        var This = $(this);
            setTimeout(function() {
                if (This.attr('aria-expanded') === "true") {
                    $("#header .navbar-nav").find('button').addClass('is-active');
                } else {
                    $("#header .navbar-nav").find('button').removeClass('is-active');
                }
            }, 200);
        });

        var foothtml = $('footer#page-footer').text();
        if ($.trim(foothtml).length == 0) {
            $('footer#page-footer').addClass('empty-footer');
        }
        var addhtml = $('.address-head').text();
        if ($.trim(addhtml).length == 0) {
            $('.address-head').addClass('empty-address');
        }

        var $val = $("#id_s_theme_academi_pagesize").val();
        if ($val == 'container' || $val == 'default') {
            $("#admin-pagesizecustomval").find('input[type=text]').attr('disabled', 'disabled');
        }

        $("#id_s_theme_academi_pagesize").on('change', function() {
            var $this = $(this);
            var val = $this.val();
            if (val == 'container' || val == 'default') {
                $("#admin-pagesizecustomval").find('input[type=text]').attr('disabled', 'disabled');
            } else {
                $("#admin-pagesizecustomval").find('input[type=text]').removeAttr('disabled');
            }
        });

        // Settings For Scroll to top button.
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 150) {
                $('#backToTop').fadeIn('slow');
                $('#custom_save').fadeIn('slow');
            } else {
                $('#backToTop').fadeOut('slow');
            }
            if ($(this).scrollTop() >= $(window).height) {
            $('#backToTop').fadOut('slow');

            }
        });

        // Hide The BacktoTop button in top.
        $('#backToTop').click(function() {
            $("html, body").animate({scrollTop: 0}, 'slow');
            return false;
        });

        if ($('body').hasClass('pagelayout-frontpage')) {
            var contentselector = $('#page-wrapper #page #page-content .course-content #coursecontentcollapse1').find('ul');
            if (contentselector.hasClass('d-block')) {
                 $('body').addClass('course-content-element');
            } else {
                 $('body').removeClass('course-content-element');
            }
        }

        const drawerClass = () => {
            var drawer = document.querySelector('#page');
            console.log(drawer);
            if (drawer.classList.contains('show-drawer-right')) {
                $('.header-main').addClass('show-drawer-right');
            } else {
                $('.header-main').removeClass('show-drawer-right');
            }
        }
        // Add a header class, whent the right drawer is open.
        $('#page .drawer-right-toggle [data-toggler="drawers"], .drawer.drawer-right [data-toggler="drawers"]').click(function() {
            setTimeout(drawerClass, 100);
        });

        drawerClass();

    };

    return {
        init: function() {
            new ThemeAcademi();
        }
    };

});