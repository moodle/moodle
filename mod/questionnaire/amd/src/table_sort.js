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
 * JavaScript library for questionnaire response table sorting.
 *
 * @module     mod_questionnaire/table_sort
 * @copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    var t = {
        /**
         * Initialise the event listeners.
         *
         */
        init: function() {
            M.util.js_pending('mod_questionnaire_tablesort');
            $(".qn-handcursor").on('click', t.sortcolumn);
            M.util.js_complete('mod_questionnaire_tablesort');
        },

        /**
         * Javascript for sorting s'Text Box' Response.
         * @param {Event} e
         */
        sortcolumn: function(e) {
            e.preventDefault();
            var col = $(this).index();
            var id = $(this).closest('table').attr('id');
            var sortOrder = 1;
            $(this).siblings().find('span[class^="icon-container-"]').hide();
            $(this).siblings().removeClass('asc desc');
            $(this).find('span[class^="icon-container-"]').removeAttr('style');
            if ($(this).is('.asc')) {
                $(this).removeClass('asc').addClass('desc');
                sortOrder = -1;
            } else {
                $(this).addClass('asc').removeClass('desc');
            }
            var arrData = $(this).closest('table').find('tbody >tr:has(td.cell)').get();
            arrData.sort(function (a, b) {
                var val1 = $(a).children('td').eq(col).text();
                var val2 = $(b).children('td').eq(col).text();
                // Regex to check for date sorting.
                var dateregx = /^\d{2}.*\d{4},/;
                if (dateregx.test(val1) && dateregx.test(val2)) {
                    val1 = new Date(val1);
                    val2 = new Date(val2);
                    return (val1 < val2) ? -sortOrder : (val1 > val2) ? sortOrder : 0;
                } else if ($.isNumeric(val1) && $.isNumeric(val2)) {
                    return sortOrder == 1 ? val1 - val2 : val2 - val1;
                } else {
                    return (val1 < val2) ? -sortOrder : (val1 > val2) ? sortOrder : 0;
                }
            });
            /* Append the sorted rows to tbody*/
            $.each(arrData, function (index, row) {
                var tableid = $('#' + id + ' tbody');
                tableid.append(row);
            });
        },
    };
    return t;
});