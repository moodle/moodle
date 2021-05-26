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
 * Potential contexts selector module.
 *
 * @module     tool_analytics/potential-contexts
 * @copyright  2019 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax'], function($, Ajax) {

    return /** @alias module:tool_analytics/potential-contexts */ {

        processResults: function(selector, results) {
            var contexts = [];
            if ($.isArray(results)) {
                $.each(results, function(index, context) {
                    contexts.push({
                        value: context.id,
                        label: context.name
                    });
                });
                return contexts;

            } else {
                return results;
            }
        },

        transport: function(selector, query, success, failure) {
            var promise;

            let modelid = $(selector).attr('modelid') || null;
            promise = Ajax.call([{
                methodname: 'tool_analytics_potential_contexts',
                args: {
                    query: query,
                    modelid: modelid
                }
            }]);

            promise[0].then(success).fail(failure);
        }

    };

});
