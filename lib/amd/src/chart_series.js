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
 */
define([], function() {

    /**
     * Chart data series.
     *
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

        this._label = label;
        this._values = values;
    }

    Series.prototype.TYPE_DEFAULT = null;
    Series.prototype.TYPE_LINE = 'line';

    Series.prototype._color = null;
    Series.prototype._label = null;
    Series.prototype._type = Series.prototype.TYPE_DEFAULT;
    Series.prototype._values = null;

    Series.prototype.create = function(obj) {
        var s = new Series(obj.label, obj.values);
        s.setColor(obj.color);
        s.setType(obj.type);
        return s;
    };

    Series.prototype.getColor = function() {
        return this._color;
    };

    Series.prototype.getCount = function() {
        return this._values.length;
    };

    Series.prototype.getLabel = function() {
        return this._label;
    };

    Series.prototype.getType = function() {
        return this._type;
    };

    Series.prototype.getValues = function() {
        return this._values;
    };

    Series.prototype.setColor = function(color) {
        this._color = color || null;
    };

    Series.prototype.setType = function(type) {
        if (type != this.TYPE_DEFAULT && type != this.TYPE_LINE) {
            throw new Error('Invalid serie type.');
        }
        this._type = type || null;
    };

    return Series;

});
