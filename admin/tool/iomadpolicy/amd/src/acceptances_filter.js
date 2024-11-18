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
 * Unified filter page JS module for the course participants page.
 *
 * @module     tool_iomadpolicy/acceptances_filter
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/form-autocomplete', 'core/str', 'core/notification'],
    function($, Autocomplete, Str, Notification) {

        /**
         * Selectors.
         *
         * @access private
         * @type {{UNIFIED_FILTERS: string}}
         */
        var SELECTORS = {
            UNIFIED_FILTERS: '#unified-filters'
        };

        /**
         * Init function.
         *
         * @method init
         * @private
         */
        var init = function() {
            var stringkeys = [{
                key: 'filterplaceholder',
                component: 'tool_iomadpolicy'
            }, {
                key: 'nofiltersapplied',
                component: 'tool_iomadpolicy'
            }];

            M.util.js_pending('acceptances_filter_datasource');
            Str.get_strings(stringkeys).done(function(langstrings) {
                var placeholder = langstrings[0];
                var noSelectionString = langstrings[1];
                Autocomplete.enhance(SELECTORS.UNIFIED_FILTERS, true, 'tool_iomadpolicy/acceptances_filter_datasource', placeholder,
                    false, true, noSelectionString, true)
                    .then(function() {
                        M.util.js_complete('acceptances_filter_datasource');

                        return;
                    })
                    .fail(Notification.exception);
            }).fail(Notification.exception);

            var last = $(SELECTORS.UNIFIED_FILTERS).val();
            $(SELECTORS.UNIFIED_FILTERS).on('change', function() {
                var current = $(this).val();
                var listoffilters = [];
                var textfilters = [];
                var updatedselectedfilters = false;

                $.each(current, function(index, catoption) {
                    var catandoption = catoption.split(':', 2);
                    if (catandoption.length !== 2) {
                        textfilters.push(catoption);
                        return true; // Text search filter.
                    }

                    var category = catandoption[0];
                    var option = catandoption[1];

                    // The last option (eg. 'Teacher') out of a category (eg. 'Role') in this loop is the one that was last
                    // selected, so we want to use that if there are multiple options from the same category. Eg. The user
                    // may have chosen to filter by the 'Student' role, then wanted to filter by the 'Teacher' role - the
                    // last option in the category to be selected (in this case 'Teacher') will come last, so will overwrite
                    // 'Student' (after this if). We want to let the JS know that the filters have been updated.
                    if (typeof listoffilters[category] !== 'undefined') {
                        updatedselectedfilters = true;
                    }

                    listoffilters[category] = option;
                    return true;
                });

                // Check if we have something to remove from the list of filters.
                if (updatedselectedfilters) {
                    // Go through and put the list into something we can use to update the list of filters.
                    var updatefilters = [];
                    for (var category in listoffilters) {
                        updatefilters.push(category + ":" + listoffilters[category]);
                    }
                    updatefilters = updatefilters.concat(textfilters);
                    $(this).val(updatefilters);
                }

                // Prevent form from submitting unnecessarily, eg. on blur when no filter is selected.
                if (last.join(',') != current.join(',')) {
                    this.form.submit();
                }
            });
        };

        /**
         * Return the unified user filter form.
         *
         * @method getForm
         * @return {DOMElement}
         */
        var getForm = function() {
            return $(SELECTORS.UNIFIED_FILTERS).closest('form');
        };

        return /** @alias module:core/form-autocomplete */ {
            /**
             * Initialise the unified user filter.
             *
             * @method init
             */
            init: function() {
                init();
            },

            /**
             * Return the unified user filter form.
             *
             * @method getForm
             * @return {DOMElement}
             */
            getForm: function() {
                return getForm();
            }
        };
    });
