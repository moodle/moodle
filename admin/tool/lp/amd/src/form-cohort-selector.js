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
 * Cohort selector module.
 *
 * @module     tool_lp/form-cohort-selector
 * @class      form-cohort-selector
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax'], function($, Ajax) {

    return /** @alias module:tool_lp/form-cohort-selector */ {

        processResults: function(selector, results) {
            var cohorts = [];
            $.each(results, function(index, cohort) {
                var name = cohort.name;
                if (cohort.idnumber.length > 0) {
                    // Add idnumber, but it's not a safe string so we must encode it.
                    name += ' (' + $('<div/>').text(cohort.idnumber).html() + ')';
                }
                cohorts.push({
                    value: cohort.id,
                    label: name
                });
            });
            return cohorts;
        },

        transport: function(selector, query, success, failure) {
            var promise,
                contextid = parseInt($(selector).data('contextid'), 10);

            promise = Ajax.call([{
                methodname: 'tool_lp_search_cohorts',
                args: {
                    query: query,
                    context: { contextid: contextid }
                }
            }]);

            promise[0].then(function(results) {
                success(results.cohorts);
            }, failure);
        }

    };

});
