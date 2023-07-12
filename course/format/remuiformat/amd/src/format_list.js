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
 * Enhancements to Lists components for easy course accessibility.
 *
 * @module     format/remuiformat
 * @copyright  WisdmLabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', './common', './format_card_ordering'], function($, common, ordering) {

    function init() {

        $('.general-single-card').css({opacity: 0.0, visibility: "visible",}).animate({opacity: 1.0,}, 200, "swing");
        $('.single-card').css({opacity: 0.0, visibility: "visible",}).animate({opacity: 1.0,}, 400, "swing");

        $('.sections .section .toggle-icon, body:not(.editing) .sectionname').click(function(event) {
            let container = $(this).closest('li.section');
            if (container.find('.card-footer').length) {
                event.preventDefault();
                container.toggleClass('collapsed');
                container.find('.card-footer').slideToggle('fast');
                return false;
            }
        });

        common.init();
        ordering.init();
    }

    // Must return the init function.

    return {
        init: init
        };
 });
