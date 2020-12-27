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
 * Chart output for HTML table.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_output_htmltable
 */
define([
    'jquery',
    'core/chart_output_base',
], function($, Base) {

    /**
     * Render a chart as an HTML table.
     *
     * @class
     * @extends {module:core/chart_output_base}
     * @alias module:core/chart_output_htmltable
     */
    function Output() {
        Base.prototype.constructor.apply(this, arguments);
        this._build();
    }
    Output.prototype = Object.create(Base.prototype);

    /**
     * Attach the table to the document.
     *
     * @protected
     */
    Output.prototype._build = function() {
        this._node.empty();
        this._node.append(this._makeTable());
    };

    /**
     * Builds the table node.
     *
     * @protected
     * @return {Jquery}
     */
    Output.prototype._makeTable = function() {
        var tbl = $('<table>'),
            c = this._chart,
            node,
            value,
            labels = c.getLabels(),
            hasLabel = labels.length > 0,
            series = c.getSeries(),
            seriesLabels,
            rowCount = series[0].getCount();

        // Identify the table.
        tbl.addClass('chart-output-htmltable generaltable');

        // Set the caption.
        if (c.getTitle() !== null) {
            tbl.append($('<caption>').text(c.getTitle()));
        }

        // Write the column headers.
        node = $('<tr>');
        if (hasLabel) {
            node.append($('<td>'));
        }
        series.forEach(function(serie) {
            node.append(
                $('<th>')
                .text(serie.getLabel())
                .attr('scope', 'col')
            );
        });
        tbl.append(node);

        // Write rows.
        for (var rowId = 0; rowId < rowCount; rowId++) {
            node = $('<tr>');
            if (labels.length > 0) {
                node.append(
                    $('<th>')
                    .text(labels[rowId])
                    .attr('scope', 'row')
                );
            }
            for (var serieId = 0; serieId < series.length; serieId++) {
                value = series[serieId].getValues()[rowId];
                seriesLabels = series[serieId].getLabels();
                if (seriesLabels !== null) {
                    value = series[serieId].getLabels()[rowId];
                }
                node.append($('<td>').text(value));
            }
            tbl.append(node);
        }

        return tbl;
    };

    /** @override */
    Output.prototype.update = function() {
        this._build();
    };

    return Output;

});
