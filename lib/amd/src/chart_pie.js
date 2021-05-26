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
 * Chart pie.
 *
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_pie
 */
define(['core/chart_base'], function(Base) {

    /**
     * Pie chart.
     *
     * @class
     * @extends {module:core/chart_base}
     */
    function Pie() {
        Base.prototype.constructor.apply(this, arguments);
    }
    Pie.prototype = Object.create(Base.prototype);

    /** @override */
    Pie.prototype.TYPE = 'pie';

    /**
     * Whether the chart should be displayed as doughnut or not.
     *
     * @type {Bool}
     * @protected
     */
    Pie.prototype._doughnut = null;

    /** @override */
    Pie.prototype.create = function(Klass, data) {
        var chart = Base.prototype.create.apply(this, arguments);
        chart.setDoughnut(data.doughnut);
        return chart;
    };

    /**
     * Overridden to add appropriate colors to the series.
     *
     * @override
     */
    Pie.prototype.addSeries = function(series) {
        if (series.getColor() === null) {
            var colors = [];
            var configColorSet = this.getConfigColorSet() || Base.prototype.COLORSET;
            for (var i = 0; i < series.getCount(); i++) {
                colors.push(configColorSet[i % configColorSet.length]);
            }
            series.setColors(colors);
        }
        return Base.prototype.addSeries.apply(this, arguments);
    };

    /**
     * Get whether the chart should be displayed as doughnut or not.
     *
     * @method getDoughnut
     * @returns {Bool}
     */
    Pie.prototype.getDoughnut = function() {
        return this._doughnut;
    };

    /**
     * Set whether the chart should be displayed as doughnut or not.
     *
     * @method setDoughnut
     * @param {Bool} doughnut True for doughnut type, false for pie.
     */
    Pie.prototype.setDoughnut = function(doughnut) {
        this._doughnut = Boolean(doughnut);
    };

    /**
     * Validate a series.
     *
     * Overrides parent implementation to validate that there is only
     * one series per chart instance.
     *
     * @override
     */
    Pie.prototype._validateSeries = function() {
        if (this._series.length >= 1) {
            throw new Error('Pie charts only support one serie.');
        }
        return Base.prototype._validateSeries.apply(this, arguments);
    };

    return Pie;

});
