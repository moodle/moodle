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

/*
 * JavaScript to expand/collapse sections.
 *
 * @package report_customsql
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module report_customsql/reportcategories
 */
define(['jquery'], function($) {
    /**
     * @alias module:report_customsql/reportcategories
     */
    var t = {

        /**
         * Initialise the tabs.
         */
        init: function () {
            $('body').on('click', '.csql_category h2', t.expandCollapse);
            $('.csql_expandcollapseall').on('click', t.expandCollapseAll);
            t.updateExpandCollapseAll();
        },

        /**
         * Event handler for expanding or collapsing one section.
         *
         * @param e DOM event.
         */
        expandCollapse: function (e) {
            var catwrapper = $(e.target).closest('.csql_category');
            if (catwrapper.length) {
                if (catwrapper.hasClass('csql_categoryhidden')) {
                    catwrapper.removeClass('csql_categoryhidden').addClass('csql_categoryshown');
                } else {
                    catwrapper.removeClass('csql_categoryshown').addClass('csql_categoryhidden');
                }
                e.preventDefault();
                t.updateExpandCollapseAll();
            }
        },

        /**
         * Event handler for expanding or collapsing one section.
         *
         * @param e DOM event.
         */
        expandCollapseAll: function (e) {
            if ($('.csql_categoryshown').length === 0) {
                // All categories collapsed, do expand all.
                $('.csql_category').removeClass('csql_categoryhidden');
                $('.csql_category').addClass('csql_categoryshown');
            } else {
                // All Some categories open, do collapse all.
                $('.csql_category').removeClass('csql_categoryshown');
                $('.csql_category').addClass('csql_categoryhidden');
            }
            e.preventDefault();
            t.updateExpandCollapseAll();
        },

        /**
         * Update the text of the expand/collpase all link, based
         * on whether any sections are open.
         */
        updateExpandCollapseAll: function () {
            var link = $('.csql_expandcollapseall');
            if ($('.csql_categoryshown').length === 0) {
                // All categories collapsed, link should expand all.
                link.text(link.data('expandalltext'));
            } else {
                // All Some categories open, link should collapse all.
                link.text(link.data('collapsealltext'));
            }
        }
    };

    return t;
});
