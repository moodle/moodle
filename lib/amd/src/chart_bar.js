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
 * Chart bar.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_bar
 */
define(['core/chart_base'], function(Base) {

    /**
     * Bar chart.
     *
     * @alias module:core/chart_bar
     * @extends {module:core/chart_base}
     * @class
     */
    function Bar() {
        Base.prototype.constructor.apply(this, arguments);
    }
    Bar.prototype = Object.create(Base.prototype);

    /**
     * Whether the bars should be displayed horizontally or not.
     *
     * @type {Bool}
     * @protected
     */
    Bar.prototype._horizontal = false;

    /**
     * Whether the bars should be stacked or not.
     *
     * @type {Bool}
     * @protected
     */
    Bar.prototype._stacked = false;

    /** @override */
    Bar.prototype.TYPE = 'bar';

    /** @override */
    Bar.prototype.create = function(Klass, data) {
        var chart = Base.prototype.create.apply(this, arguments);
        chart.setHorizontal(data.horizontal);
        chart.setStacked(data.stacked);
        return chart;
    };

    /** @override */
    Bar.prototype._setDefaults = function() {
        Base.prototype._setDefaults.apply(this, arguments);
        var axis = this.getYAxis(0, true);
        axis.setMin(0);
    };

    /**
     * Get whether the bars should be displayed horizontally or not.
     *
     * @returns {Bool}
     */
    Bar.prototype.getHorizontal = function() {
        return this._horizontal;
    };

    /**
     * Get whether the bars should be stacked or not.
     *
     * @returns {Bool}
     */
    Bar.prototype.getStacked = function() {
        return this._stacked;
    };

    /**
     * Set whether the bars should be displayed horizontally or not.
     *
     * It sets the X Axis to zero if the min value is null.
     *
     * @param {Bool} horizontal True if the bars should be displayed horizontally, false otherwise.
     */
    Bar.prototype.setHorizontal = function(horizontal) {
        var axis = this.getXAxis(0, true);
        if (axis.getMin() === null) {
            axis.setMin(0);
        }
        this._horizontal = Boolean(horizontal);
    };

    /**
     * Set whether the bars should be stacked or not.
     *
     * @method setStacked
     * @param {Bool} stacked True if the chart should be stacked or false otherwise.
     */
    Bar.prototype.setStacked = function(stacked) {
        this._stacked = Boolean(stacked);
    };

    return Bar;

});
