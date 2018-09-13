// This file is part of Moodle - http://moodle.org/
//
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
 *
 * @package     theme_klass
 * @copyright   2015 LMSACE Dev Team,lmsace.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

(function($) {
    var img = $("header#header").find('.avatars').find('img[src$="/u/f2"]');
    var src = img.attr('src');
    img.attr('src', src + '_white');
    var msg = $("header#header").find('#nav-message-popover-container .nav-link').find("img[src$='t/message']");
    var msgsrc = msg.attr('src');
    msg.attr('src', msgsrc + "_white");
    var note = $("header#header").find('#nav-notification-popover-container .nav-link').find("img[src$='i/notifications']");
    var notesrc = note.attr('src');
    note.attr('src', notesrc + "_white");

    /* ------- Check navbar button status -------- */
    if ($("#header .navbar-nav button").attr('aria-expanded') === "true") {
        $("#header .navbar-nav").find('button').addClass('is-active');
    }
    /* ------ Event for change the drawer navbar style  ------ */
    $("#header .navbar-nav button").click(function() {
        var $this = $(this);
        setTimeout(function() {
            if ($this.attr('aria-expanded') === "true") {
                $("#header .navbar-nav").find('button').addClass('is-active');
            } else {
                $("#header .navbar-nav").find('button').removeClass('is-active');
            }
        }, 200);
    });

})(jQuery);