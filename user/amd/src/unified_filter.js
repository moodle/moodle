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
 * @module     core_user/unified_filter
 * @package    core_user
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
        var stringkeys = [
            {
                key: 'userfilterplaceholder',
                component: 'moodle'
            },
            {
                key: 'nofiltersapplied',
                component: 'moodle'
            }
        ];

        Str.get_strings(stringkeys).done(function(langstrings) {
            var placeholder = langstrings[0];
            var noSelectionString = langstrings[1];
            Autocomplete.enhance(SELECTORS.UNIFIED_FILTERS, true, 'core_user/unified_filter_datasource', placeholder,
                false, true, noSelectionString, true);
        }).fail(Notification.exception);

        var last = $(SELECTORS.UNIFIED_FILTERS).val();
        $(SELECTORS.UNIFIED_FILTERS).on('change', function() {
            var current = $(this).val();

            if (last.join(',') != current.join(',')) {
                this.form.submit();
            }
            last = current;
        });
    };

    return /** @alias module:core/form-autocomplete */ {
        // Public variables and functions.
        /**
         * Initialise the unified user filter.
         *
         * @method init
         */
        'init': function() {
            init();
        }
    };
});
