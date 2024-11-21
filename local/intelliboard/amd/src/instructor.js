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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

define(['jquery', 'core/ajax', 'local_intelliboard/intb_multipleselect'], function($, ajax, multipleselect) {
    return {
        /**
         * Modal window for settings of instructor dashboard
         */
        dashboardSettings: function(selectCoursesString, allSelectedString, selectAllString) {
            var select = $('.instructor-courses-settings');
            var wrapper = $('.instructor-dashboard-settings-wrapper');

            select.multipleSelect({
                multiple: true,
                placeholder: selectCoursesString,
                allSelected: allSelectedString,
                selectAllText: selectAllString,
                multipleWidth: 350,
                filter: true
            });

            wrapper.find('.ms-drop.bottom').append(
                '<button class="btn btn-primary">OK</button>'
            );

            wrapper.find('.ms-drop.bottom button').on('click', function() {
                var selectedCourses = select.val();

                if(selectedCourses == null) {
                    selectedCourses = [];
                }

                var saveCoursesPromises = ajax.call([
                    {
                        methodname: 'local_intelliboard_save_instructor_courses', args: {
                            data: JSON.stringify({courses: selectedCourses})
                        }
                    },
                ]);

                saveCoursesPromises[0].done(function(response) {
                    window.location.reload();
                });
            });
        }
    };
});