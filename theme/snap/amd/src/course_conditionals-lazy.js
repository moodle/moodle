/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Course conditionals function.
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/templates', 'theme_snap/ajax_notification'],
    function($, ajax, notification, templates, ajaxNotify) {

        // Main function.
        var init = function(courseConfig) {
            var currentlyUnavailableSections = courseConfig.unavailablesections.map(Number),
            currentlyUnavailableMods = courseConfig.unavailablemods.map(Number);

            $(document).on("snapModuleCompletionChange",
                function() {
                    ajax.call([
                        {
                            methodname: 'theme_snap_course_completion',
                            args: {
                                courseshortname: courseConfig.shortname,
                                unavailablesections: currentlyUnavailableSections.join(','),
                                unavailablemods: currentlyUnavailableMods.join(',')
                            },
                            done: function(response) {
                                /**
                                 * Update elements with newly available / unavailable html.
                                 * Elements can either be sections or modules.
                                 *
                                 * @param {object} changedHTML - response json
                                 * @param {string} typeKey - string (either 'section' or 'module')
                                 */
                                var updateModOrSectionHTML = function(changedHTML, typeKey) {
                                    if (!Object.keys(changedHTML).length) {
                                        // There are no newly available elements which require updating.
                                        return;
                                    }
                                    for (var i in changedHTML) {
                                        var item = changedHTML[i];
                                        var id = item.id ? item.id : item.number;
                                        var html = item.html;
                                        var baseSelector = '#' + typeKey + '-' + id;
                                        $(baseSelector).replaceWith(html);
                                    }
                                };

                                // Update newly available sections with released html.
                                updateModOrSectionHTML(response.changedsectionhtml, 'section');

                                // Update newly available modules with released html.
                                updateModOrSectionHTML(response.changedmodhtml, 'module');

                                // Update TOC.
                                templates.render('theme_snap/course_toc', response.toc)
                                    .done(function(result) {
                                        $('#course-toc').html($(result).html());
                                        $(document).trigger('snapTOCReplaced');
                                    });

                                // Update current state.
                                currentlyUnavailableSections = response.unavailablesections.split(',').map(Number);
                                currentlyUnavailableMods = response.unavailablemods.split(',').map(Number);

                            },
                            fail: function(response) {
                                ajaxNotify.ifErrorShowBestMsg(response);
                            }
                        }
                    ], true, true);
                }
            );
        };
        return init;
    }
);
