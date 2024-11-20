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
 * Chart line.
 *
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_line
 */
define(['core/chart_base'], function(Base) {

    /**
     * Line chart.
     *
     * @extends {module:core/chart_base}
     * @class
     */
    function Line() {
        Base.prototype.constructor.apply(this, arguments);
    }
    Line.prototype = Object.create(Base.prototype);

    /** @override */
    Line.prototype.TYPE = 'line';

    /**
     * Whether the line should be smooth or not.
     *
     * By default the chart lines are not smooth.
     *
     * @type {Bool}
     * @protected
     */
    Line.prototype._smooth = false;

    /** @override */
    Line.prototype.create = function(Klass, data) {
        var chart = Base.prototype.create.apply(this, arguments);
        chart.setSmooth(data.smooth);
        return chart;
    };

    /**
     * Get whether the line should be smooth or not.
     *
     * @method getSmooth
     * @returns {Bool}
     */
    Line.prototype.getSmooth = function() {
        return this._smooth;
    };

    /**
     * Set whether the line should be smooth or not.
     *
     * @method setSmooth
     * @param {Bool} smooth True if the line chart should be smooth, false otherwise.
     */
    Line.prototype.setSmooth = function(smooth) {
        this._smooth = Boolean(smooth);
    };

    return Line;

});
