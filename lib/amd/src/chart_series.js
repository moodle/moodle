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
 * Chart series.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_series
 */
define([], function() {

    /**
     * Chart data series.
     *
     * @class
     * @alias module:core/chart_series
     * @param {String} label The series label.
     * @param {Number[]} values The values.
     */
    function Series(label, values) {
        if (typeof label !== 'string') {
            throw new Error('Invalid label for series.');

        } else if (typeof values !== 'object') {
            throw new Error('Values for a series must be an array.');

        } else if (values.length < 1) {
            throw new Error('Invalid values received for series.');
        }

        this._colors = [];
        this._label = label;
        this._values = values;
    }

    /**
     * The default type of series.
     *
     * @type {Null}
     * @const
     */
    Series.prototype.TYPE_DEFAULT = null;

    /**
     * Type of series 'line'.
     *
     * @type {String}
     * @const
     */
    Series.prototype.TYPE_LINE = 'line';

    /**
     * The colors of the series.
     *
     * @type {String[]}
     * @protected
     */
    Series.prototype._colors = null;

    /**
     * The label of the series.
     *
     * @type {String}
     * @protected
     */
    Series.prototype._label = null;

    /**
     * The type of the series.
     *
     * @type {String}
     * @protected
     */
    Series.prototype._type = Series.prototype.TYPE_DEFAULT;

    /**
     * The values in the series.
     *
     * @type {Number[]}
     * @protected
     */
    Series.prototype._values = null;

    /**
     * Create a new instance of a series from serialised data.
     *
     * @static
     * @method create
     * @param {Object} obj The data of the series.
     * @return {module:core/chart_series}
     */
    Series.prototype.create = function(obj) {
        var s = new Series(obj.label, obj.values);
        s.setType(obj.type);

        // Colors are exported as an array with 1, or n values.
        if (obj.colors && obj.colors.length > 1) {
            s.setColors(obj.colors);
        } else {
            s.setColor(obj.colors[0]);
        }

        return s;
    };

    /**
     * Get the color.
     *
     * @return {String}
     */
    Series.prototype.getColor = function() {
        return this._colors[0] || null;
    };

    /**
     * Get the colors for each value in the series.
     *
     * @return {String[]}
     */
    Series.prototype.getColors = function() {
        return this._colors;
    };

    /**
     * Get the number of values in the series.
     *
     * @return {Number}
     */
    Series.prototype.getCount = function() {
        return this._values.length;
    };

    /**
     * Get the series label.
     *
     * @return {String}
     */
    Series.prototype.getLabel = function() {
        return this._label;
    };

    /**
     * Get the series type.
     *
     * @return {String}
     */
    Series.prototype.getType = function() {
        return this._type;
    };

    /**
     * Get the series values.
     *
     * @return {Number[]}
     */
    Series.prototype.getValues = function() {
        return this._values;
    };

    /**
     * Whether there is a color per value.
     *
     * @return {Bool}
     */
    Series.prototype.hasColoredValues = function() {
        return this._colors.length == this.getCount();
    };

    /**
     * Set the series color.
     *
     * @param {String} color A CSS-compatible color.
     */
    Series.prototype.setColor = function(color) {
        this._colors = [color];
    };

    /**
     * Set a color for each value in the series.
     *
     * @param {String[]} colors CSS-compatible colors.
     */
    Series.prototype.setColors = function(colors) {
        if (colors && colors.length != this.getCount()) {
            throw new Error('When setting multiple colors there must be one per value.');
        }
        this._colors = colors || [];
    };

    /**
     * Set the type of the series.
     *
     * @param {String} type A type constant value.
     */
    Series.prototype.setType = function(type) {
        if (type != this.TYPE_DEFAULT && type != this.TYPE_LINE) {
            throw new Error('Invalid serie type.');
        }
        this._type = type || null;
    };

    return Series;

});
