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
 * Apply dataTable on HTML table for report.
 *
 * @module     report_lpmonitoring/report_datatable
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 */

define(['jquery',
        'report_lpmonitoring/paginated_datatable',
        'report_lpmonitoring/colorcontrast',
        'report_lpmonitoring/jquery.dataTables'],
        function ($, DataTable, colorcontrast) {

            /**
             * Constructor.
             *
             * @param {string} tableSelector The CSS selector used for the table.
             * @param {string} reportfilterName The name of the filter radio buttons.
             * @param {string} searchSelector The CSS selector used for the table search input.
             * @param {string} searchColumnsSelector The CSS selector used for the table search input in course/activity.
             * @param {string} reportScaleSelector The CSS selector used for the scale report input.
             * @param {string} coursesSelector The CSS selector used for courses columns and cells.
             * @param {string} activitiesSelector The CSS selector used for activities columns and cells.
             * @param {string} filterscalevalue The filter scale value
             */
            var ReportDataTable = function(tableSelector, reportfilterName, searchSelector, searchColumnsSelector,
            reportScaleSelector, coursesSelector, activitiesSelector, filterscalevalue) {
                this.tableSelector = tableSelector;
                this.reportfilterName = reportfilterName;
                this.searchSelector = searchSelector;
                this.searchColumnsSelector = searchColumnsSelector;
                this.reportScaleSelector = reportScaleSelector;
                this.coursesSelector = coursesSelector;
                this.activitiesSelector = activitiesSelector;
                this.columns = [];

                var self = this;
                // Perform the search and filters according to the actual values.
                $(document).ready(function() {
                    // Init the color contrast object.
                    var colorContrast = colorcontrast.init();
                    colorContrast.apply('td.evaluation a');

                    DataTable.apply(self.tableSelector, false, false);
                    // Filters.
                    var texts = [''];
                    $(self.tableSelector + " thead tr th").each(function( index ) {
                        if (index > 1) {
                            var indexdom = index + 1;
                            $(tableSelector + ' tbody tr td:nth-of-type(' + indexdom + ')').each(function() {
                                var text = $(this).text().trim();
                                if (texts.indexOf(text) == -1) {
                                    var selected = '';
                                    if (text === filterscalevalue) {
                                        selected = 'selected="true"';
                                    }
                                    $(self.reportScaleSelector).append('<option value="' + text + '"' + selected + '>'
                                            + text + '</option>');
                                    texts.push(text);
                                }
                            });
                            self.columns.push(index);
                        }
                    });

                    $(self.reportScaleSelector).on('change', function() {
                        self.performSearch();
                    });
                    $(self.searchSelector).on('input', function() {
                        self.performSearch();
                    });
                    $(self.searchColumnsSelector).on('input', function() {
                        self.filterCoulmns();
                        self.performSearch();
                    });
                    $('input[type=radio][name=' + self.reportfilterName + ']').change(function() {
                        self.courseActivityFilter();
                        self.performSearch();
                    });

                    $(tableSelector).show();
                    self.courseActivityFilter();
                    self.filterCoulmns();
                    self.performSearch();
                });
            };

            /** @var {String} The table CSS selector. */
            ReportDataTable.prototype.tableSelector = null;
            /** @var {String} The report filter name (for radio buttons). */
            ReportDataTable.prototype.reportfilterName = null;
            /** @var {String} The search input CSS selector. */
            ReportDataTable.prototype.searchSelector = null;
            /** @var {String} The search columns input CSS selector. */
            ReportDataTable.prototype.searchColumnsSelector = null;
            /** @var {String} The scale select CSS selector. */
            ReportDataTable.prototype.reportScaleSelector = null;
            /** @var {String} The courses cells CSS selector. */
            ReportDataTable.prototype.coursesSelector = null;
            /** @var {String} The activities (course modules) cells CSS selector. */
            ReportDataTable.prototype.activitiesSelector = null;
            /** @var {Array} The columns indexes. */
            ReportDataTable.prototype.columns = [];

            /**
             * Perform the search and make sure the correct radio button is applied to the table (hide cells accordingly).
             *
             * @name   performSearch
             * @return {Void}
             * @function
             */
            ReportDataTable.prototype.performSearch = function() {
                var table = $(this.tableSelector).DataTable();
                table
                 .search( '' )
                 .columns().search( '' )
                 .draw();
                $(this.tableSelector).DataTable().column(0).search($(this.searchSelector).val(), false, false).draw();
                var optionSelected = $(this.reportScaleSelector + ' option:selected').val();
                $(this.tableSelector).DataTable().search(optionSelected, false, false).draw();
            };

            /**
             * Switch display between course and activity
             *
             * @name   courseActivityFilter
             * @return {Void}
             * @function
             */
            ReportDataTable.prototype.courseActivityFilter = function() {
                var self = this;
                var checkedvalue = $('input[type=radio][name=' + self.reportfilterName + ']:checked').val();
                var classcolumn = '';
                var courseormodule = false;
                if (checkedvalue === 'course') {
                    classcolumn = 'course-cell';
                    courseormodule = true;
                }
                else if (checkedvalue === 'module') {
                    classcolumn = 'cm-cell';
                    courseormodule = true;
                }
                if (courseormodule) {
                    $(self.tableSelector + " thead tr th").each(function( index ) {
                        if (index > 1) {
                            var column = $(self.tableSelector).DataTable().column(index);
                            var columnheader = column.header();
                            var condition = $(this).hasClass(classcolumn);
                            $(columnheader).toggleClass('switchsearchhidden', !condition);
                            $(this).toggleClass('switchsearchhidden', !condition);
                            column.nodes().to$().toggleClass('switchsearchhidden', !condition);
                        }
                    });
                } else {
                    $.each(self.columns, function(index, value) {
                        var column = $(self.tableSelector).DataTable().column(value);
                        var columnheader = column.header();
                        $(columnheader).removeClass('switchsearchhidden');
                        column.nodes().to$().removeClass('switchsearchhidden');
                    });
                }
            };

            /**
             * Perform the search in th column.
             *
             * @name   filterCoulmn
             * @return {Void}
             * @function
             */
            ReportDataTable.prototype.filterCoulmns = function() {
                var value = $(this.searchColumnsSelector).val().toLowerCase();
                var self = this;
                if (value !== '') {
                    $(self.tableSelector + " thead tr th").each(function( index ) {
                        if (index <= 1) {
                            return true;
                        }
                        var column = $(self.tableSelector).DataTable().column(index);
                        var columnheader = column.header();
                        var condition = $(this).text().toLowerCase().indexOf(value) > -1;
                        $(columnheader).toggleClass('filtersearchhidden', !condition);
                        $(this).toggleClass('filtersearchhidden', !condition);
                        column.nodes().to$().toggleClass('filtersearchhidden', !condition);
                    });
                } else {
                    $.each(self.columns, function(index, value) {
                        var column = $(self.tableSelector).DataTable().column(value);
                        var columnheader = column.header();
                        $(columnheader).removeClass('filtersearchhidden');
                        column.nodes().to$().removeClass('filtersearchhidden');
                    });
                }
            };

            return {
                init: function (tableSelector, reportfilterName, searchSelector,
                    searchColumnsSelector, reportScaleSelector, coursesSelector, activitiesSelector, filterscalevalue) {
                    return new ReportDataTable(tableSelector, reportfilterName, searchSelector,
                        searchColumnsSelector, reportScaleSelector, coursesSelector, activitiesSelector, filterscalevalue);
                }
            };
        });
