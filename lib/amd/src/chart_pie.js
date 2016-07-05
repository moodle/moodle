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
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_pie
 */
define(['core/chart_base'], function(Base) {

    /**
     * Pie chart.
     *
     * @class
     * @alias module:core/chart_pie
     * @extends {module:core/chart_base}
     */
    function Pie() {
        Base.prototype.constructor.apply(this, arguments);
    }
    Pie.prototype = Object.create(Base.prototype);

    /** @override */
    Pie.prototype.TYPE = 'pie';

    /**
     * Overridden to add appropriate colors to the series.
     *
     * @override
     */
    Pie.prototype.addSeries = function(series) {
        if (series.getColor() === null) {
            var colors = [];
            for (var i = 0; i < series.getCount(); i++) {
                colors.push(this.COLORSET[i % Base.prototype.COLORSET.length]);
            }
            series.setColors(colors);
        }
        return Base.prototype.addSeries.apply(this, arguments);
    };

    /**
     * Validate a series.
     *
     * Overrides parent implementation to validate that there is only
     * one series per chart instance.
     *
     * @override
     */
    Pie.prototype._validateSerie = function() {
        if (this._series.length >= 1) {
            throw new Error('Pie charts only support one serie.');
        }
        return Base.prototype._validateSerie.apply(this, arguments);
    };

    return Pie;

});
