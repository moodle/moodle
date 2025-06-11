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
 * LSU Custom Snap Changes
 *
 * @package
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/templates', 'core/notification'],
    function($, coretemp, noty) {
    'use strict';
    return {
        /**
         * This is the starting function for the Cross Enrollment Tool
         * @param {int} id extras is data coming from PHP
         */
        updateCard: function(id) {
            // Clear the session storage so it won't last outside of the form page.
            $.ajax({
                type: "GET",
                async: true,
                url: M.cfg.wwwroot + '/theme/snap/rest.php?action=get_course_card_quick_links',
                data: 'courseid='+id,
                success: function(data) {
                    // This will call the function to load and render our template.
                    coretemp.renderForPromise('theme_snap/course_card_links', data)

                    // It returns a promise that needs to be resoved.
                    .then(({html, js}) => {
                        // replace the templated html
                        var thislink = 'span.course_quick_link_swap_' + data.courseid;
                        coretemp.replaceNodeContents(thislink, html, js);
                    })

                    // Deal with this exception (Using core/notify exception function is recommended).
                    .catch(ex => noty.exception(ex));
                }
            });
        },

        updateList: function() {
            // Clear the session storage so it won't last outside of the form page.
            var elts = $('*[class*="course_quick_link_swap_list_"]');
            elts.each(function() {

                $.ajax({
                    type: "GET",
                    async: true,
                    url: M.cfg.wwwroot + '/theme/snap/rest.php?action=get_course_card_quick_links',
                    data: 'courseid=' + $(this).data('courseid'),
                    success: function(data) {
                        // This will call the function to load and render our template.
                        coretemp.renderForPromise('theme_snap/course_card_links', data)

                        // It returns a promise that needs to be resoved.
                        .then(({html, js}) => {
                            // replace the templated html
                            var thislink = 'span.course_quick_link_swap_list_' + data.courseid;
                            coretemp.replaceNodeContents(thislink, html, js);
                        })

                        // Deal with this exception (Using core/notify exception function is recommended).
                        .catch(ex => noty.exception(ex));
                    }
                });

            });
        }
    };
});