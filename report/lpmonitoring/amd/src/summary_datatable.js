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
 * Apply dataTable on HTML table for summary.
 *
 * @module     report_lpmonitoring/summary_datatable
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 */

define(['jquery', 'report_lpmonitoring/paginated_datatable', 'report_lpmonitoring/colorcontrast'],
        function ($, DataTable, colorcontrast) {

            /**
             * Constructor.
             *
             * @param {string} tableSelector The CSS selector used for the table.
             * @param {string} summaryfilterName The name of the filter radio buttons.
             * @param {string} searchSelector The CSS selector used for the table search input.
             * @param {string} totalSelector The CSS selector used for total.
             * @param {string} coursesSelector The CSS selector used for courses columns and cells.
             * @param {string} activitiesSelector The CSS selector used for activities columns and cells.
             */
            var SummaryDataTable = function(tableSelector, summaryfilterName, searchSelector, totalSelector, coursesSelector,
            activitiesSelector) {
                this.tableSelector = tableSelector;
                this.summaryfilterName = summaryfilterName;
                this.searchSelector = searchSelector;
                this.totalSelector = totalSelector;
                this.coursesSelector = coursesSelector;
                this.activitiesSelector = activitiesSelector;

                var self = this;
                // Perform the search and filters according to the actual values.
                $(document).ready(function() {
                    // Init the color contrast object.
                    var colorContrast = colorcontrast.init();
                    colorContrast.apply('.summary-table .total-cell, .summary-table  .course-cell, .summary-table  .cm-cell');

                    DataTable.apply(self.tableSelector, false, false);

                    $(self.searchSelector).on('input', function() {
                        self.performSearch();
                    });

                    $('input[type=radio][name=' + self.summaryfilterName + ']').change(function() {
                        self.courseActivityFilter();
                        self.performSearch();
                    });

                    $('#scale-filter-summary').on('change', function() {
                        self.summarySelectScale();
                    });

                    $(tableSelector).show();
                    self.courseActivityFilter();
                    self.summarySelectScale();
                    self.performSearch();
                });
            };

            /** @var {String} The table CSS selector. */
            SummaryDataTable.prototype.tableSelector = null;
            /** @var {String} The report filter name (for radio buttons). */
            SummaryDataTable.prototype.summaryfilterName = null;
            /** @var {String} The search input CSS selector. */
            SummaryDataTable.prototype.searchSelector = null;
            /** @var {String} The total CSS selector. */
            SummaryDataTable.prototype.totalSelector = null;
            /** @var {String} The courses cells CSS selector. */
            SummaryDataTable.prototype.coursesSelector = null;
            /** @var {String} The activities (course modules) cells CSS selector. */
            SummaryDataTable.prototype.activitiesSelector = null;

            /**
             * Perform the search in competency names (hide rows accordingly).
             *
             * @name   performSearch
             * @return {Void}
             * @function
             */
            SummaryDataTable.prototype.performSearch = function() {
                $(this.tableSelector).DataTable().column(0).search($(this.searchSelector).val(), false, false).draw();
            };

            /**
             * Switch display between course and activity.
             *
             * @name   courseActivityFilter
             * @return {Void}
             * @function
             */
            SummaryDataTable.prototype.courseActivityFilter = function() {
                var self = this;
                var checkedvalue = $('input[type=radio][name=' + self.summaryfilterName + ']:checked').val();
                var classcolumn = '';
                if (checkedvalue === 'course') {
                    classcolumn = 'course-cell';
                } else if (checkedvalue === 'module') {
                    classcolumn = 'cm-cell';
                } else {
                    classcolumn = 'total-cell';
                }
                $(self.tableSelector + " thead tr th").each(function( index ) {
                    if (index > 0) {
                        var column = $(self.tableSelector).DataTable().column(index);
                        var columnheader = column.header();
                        var condition = $(this).hasClass(classcolumn);
                        $(columnheader).toggleClass('switchsearchhidden', !condition);
                        $(this).toggleClass('switchsearchhidden', !condition);
                        column.nodes().to$().toggleClass('switchsearchhidden', !condition);
                    }
                });
            };

            /**
             * Switch display between scales.
             *
             * @name   summarySelectScale
             * @return {Void}
             * @function
             */
            SummaryDataTable.prototype.summarySelectScale = function() {
                var optionSelected = $('#scale-filter-summary').val();
                if ($(this.tableSelector).data('scaleid') == optionSelected) {
                    $(this.tableSelector).parents(".table-scroll").show();
                    this.performSearch();
                } else {
                    $(this.tableSelector).parents(".table-scroll").hide();
                }
            };

            return {
                init: function (tableSelector, summaryfilterName, searchSelector, totalSelector, coursesSelector,
                    activitiesSelector) {
                        return new SummaryDataTable(tableSelector, summaryfilterName, searchSelector, totalSelector,
                            coursesSelector, activitiesSelector);
                }
            };
        });
