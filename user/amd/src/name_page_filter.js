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
 * Name and page filter JS module for the course participants page.
 *
 * @module     core_user/name_page_filter
 * @package    core_user
 * @copyright  2017 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core_user/unified_filter'],
        function($, UnifiedFilter) {

    /**
     * Selectors.
     *
     * @access private
     * @type {{NAME_FILTERS: string, PAGE_FILTERS: string}}
     */
    var SELECTORS = {
        NAME_FILTERS: 'a.letter',
        PAGE_FILTERS: 'a.page-link'
    };

    /**
     * Init function.
     *
     * @method init
     * @private
     */
    var init = function() {
        $(SELECTORS.NAME_FILTERS + ', ' + SELECTORS.PAGE_FILTERS).on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            UnifiedFilter.getForm().attr('action', href);
            UnifiedFilter.getForm().submit();
        });
    };

    return /** @alias module:core/form-autocomplete */ {
        // Public variables and functions.
        /**
         * Initialise the name and page user filter.
         *
         * @method init
         */
        'init': function() {
            init();
        }
    };
});
