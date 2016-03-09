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
 * Course selector adaptor for auto-complete form element.
 *
 * @module     core/form-course-selector
 * @class      form-course-selector
 * @package    core
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['core/ajax', 'jquery'], function(ajax, $) {

    return /** @alias module:core/form-course-selector */ {
        // Public variables and functions.
        processResults: function(selector, data) {
            // Mangle the results into an array of objects.
            var results = [], i = 0;
            var excludelist = String($(selector).data('exclude')).split(',');

            for (i = 0; i < data.courses.length; i++) {
                if (excludelist.indexOf(String(data.courses[i].id)) === -1) {
                    results.push({ value: data.courses[i].id, label: data.courses[i].displayname });
                }
            }
            return results;
        },

        transport: function(selector, query, success, failure) {
            // Parse some data-attributes from the form element.
            var requiredcapabilities = $(selector).data('requiredcapabilities');
            if (requiredcapabilities.trim() !== "") {
                requiredcapabilities = requiredcapabilities.split(',');
            } else {
                requiredcapabilities = [];
            }
            // Build the query.
            var promise = null;

            if (typeof query === "undefined") {
                query = '';
            }

            var searchargs = {
                criterianame: 'search',
                criteriavalue: query,
                page: 0,
                perpage: 100,
                requiredcapabilities: requiredcapabilities
            };
            // Go go go!
            promise = ajax.call([{
                methodname: 'core_course_search_courses', args: searchargs
            }]);

            promise[0].done(success);
            promise[0].fail(failure);

            return promise;
        }
    };
});
