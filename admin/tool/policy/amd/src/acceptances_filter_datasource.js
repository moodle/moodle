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
 * Datasource for the tool_policy/acceptances_filter.
 *
 * This module is compatible with core/form-autocomplete.
 *
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {

    return /** @alias module:tool_policy/acceptances_filter_datasource */ {
        /**
         * List filter options.
         *
         * @param {String} selector The select element selector.
         * @param {String} query The query string.
         * @return {Promise}
         */
        list: function(selector, query) {
            var filteredOptions = [];

            var el = $(selector);
            var originalOptions = $(selector).data('originaloptionsjson');
            var selectedFilters = el.val();
            $.each(originalOptions, function(index, option) {
                // Skip option if it does not contain the query string.
                if (query.trim() !== '' && option.label.toLocaleLowerCase().indexOf(query.toLocaleLowerCase()) === -1) {
                    return true;
                }
                // Skip filters that have already been selected.
                if ($.inArray(option.value, selectedFilters) > -1) {
                    return true;
                }

                filteredOptions.push(option);
                return true;
            });

            var deferred = new $.Deferred();
            deferred.resolve(filteredOptions);

            return deferred.promise();
        },

        /**
         * Process the results for auto complete elements.
         *
         * @param {String} selector The selector of the auto complete element.
         * @param {Array} results An array or results.
         * @return {Array} New array of results.
         */
        processResults: function(selector, results) {
            var options = [];
            $.each(results, function(index, data) {
                options.push({
                    value: data.value,
                    label: data.label
                });
            });
            return options;
        },

        /**
         * Source of data for Ajax element.
         *
         * @param {String} selector The selector of the auto complete element.
         * @param {String} query The query string.
         * @param {Function} callback A callback function receiving an array of results.
         */
        /* eslint-disable promise/no-callback-in-promise */
        transport: function(selector, query, callback) {
            this.list(selector, query).then(callback).catch(Notification.exception);
        }
    };

});
