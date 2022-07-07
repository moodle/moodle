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
 * Chart axis.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_axis
 */
define([], function() {

    /**
     * Chart axis class.
     *
     * This is used to represent an axis, whether X or Y.
     *
     * @alias module:core/chart_axis
     * @class
     */
    function Axis() {
        // Please eslint no-empty-function.
    }

    /**
     * Default axis position.
     * @const {Null}
     */
    Axis.prototype.POS_DEFAULT = null;

    /**
     * Bottom axis position.
     * @const {String}
     */
    Axis.prototype.POS_BOTTOM = 'bottom';

    /**
     * Left axis position.
     * @const {String}
     */
    Axis.prototype.POS_LEFT = 'left';

    /**
     * Right axis position.
     * @const {String}
     */
    Axis.prototype.POS_RIGHT = 'right';

    /**
     * Top axis position.
     * @const {String}
     */
    Axis.prototype.POS_TOP = 'top';

    /**
     * Label of the axis.
     * @type {String}
     * @protected
     */
    Axis.prototype._label = null;

    /**
     * Labels of the ticks.
     * @type {String[]}
     * @protected
     */
    Axis.prototype._labels = null;

    /**
     * Maximum value of the axis.
     * @type {Number}
     * @protected
     */
    Axis.prototype._max = null;

    /**
     * Minimum value of the axis.
     * @type {Number}
     * @protected
     */
    Axis.prototype._min = null;

    /**
     * Position of the axis.
     * @type {String}
     * @protected
     */
    Axis.prototype._position = null;

    /**
     * Steps on the axis.
     * @type {Number}
     * @protected
     */
    Axis.prototype._stepSize = null;

    /**
     * Create a new instance of an axis from serialised data.
     *
     * @static
     * @method create
     * @param {Object} obj The data of the axis.
     * @return {module:core/chart_axis}
     */
    Axis.prototype.create = function(obj) {
        var s = new Axis();
        s.setPosition(obj.position);
        s.setLabel(obj.label);
        s.setStepSize(obj.stepSize);
        s.setMax(obj.max);
        s.setMin(obj.min);
        s.setLabels(obj.labels);
        return s;
    };

    /**
     * Get the label of the axis.
     *
     * @method getLabel
     * @return {String}
     */
    Axis.prototype.getLabel = function() {
        return this._label;
    };

    /**
     * Get the labels of the ticks of the axis.
     *
     * @method getLabels
     * @return {String[]}
     */
    Axis.prototype.getLabels = function() {
        return this._labels;
    };

    /**
     * Get the maximum value of the axis.
     *
     * @method getMax
     * @return {Number}
     */
    Axis.prototype.getMax = function() {
        return this._max;
    };

    /**
     * Get the minimum value of the axis.
     *
     * @method getMin
     * @return {Number}
     */
    Axis.prototype.getMin = function() {
        return this._min;
    };

    /**
     * Get the position of the axis.
     *
     * @method getPosition
     * @return {String}
     */
    Axis.prototype.getPosition = function() {
        return this._position;
    };

    /**
     * Get the step size of the axis.
     *
     * @method getStepSize
     * @return {Number}
     */
    Axis.prototype.getStepSize = function() {
        return this._stepSize;
    };

    /**
     * Set the label of the axis.
     *
     * @method setLabel
     * @param {String} label The label.
     */
    Axis.prototype.setLabel = function(label) {
        this._label = label || null;
    };

    /**
     * Set the labels of the values on the axis.
     *
     * This automatically sets the [_stepSize]{@link module:core/chart_axis#_stepSize},
     * [_min]{@link module:core/chart_axis#_min} and [_max]{@link module:core/chart_axis#_max}
     * to define a scale from 0 to the number of labels when none of the previously
     * mentioned values have been modified.
     *
     * You can use other values so long that your values in a series are mapped
     * to the values represented by your _min, _max and _stepSize.
     *
     * @method setLabels
     * @param {String[]} labels The labels.
     */
    Axis.prototype.setLabels = function(labels) {
        this._labels = labels || null;

        // By default we set the grid according to the labels.
        if (this._labels !== null
                && this._stepSize === null
                && (this._min === null || this._min === 0)
                && this._max === null) {
            this.setStepSize(1);
            this.setMin(0);
            this.setMax(labels.length - 1);
        }
    };

    /**
     * Set the maximum value on the axis.
     *
     * When this is not set (or set to null) it is left for the output
     * library to best guess what should be used.
     *
     * @method setMax
     * @param {Number} max The value.
     */
    Axis.prototype.setMax = function(max) {
        this._max = typeof max !== 'undefined' ? max : null;
    };

    /**
     * Set the minimum value on the axis.
     *
     * When this is not set (or set to null) it is left for the output
     * library to best guess what should be used.
     *
     * @method setMin
     * @param {Number} min The value.
     */
    Axis.prototype.setMin = function(min) {
        this._min = typeof min !== 'undefined' ? min : null;
    };

    /**
     * Set the position of the axis.
     *
     * This does not validate whether or not the constant used is valid
     * as the axis itself is not aware whether it represents the X or Y axis.
     *
     * The output library has to have a fallback in case the values are incorrect.
     * When this is not set to {@link module:core/chart_axis#POS_DEFAULT} it is up
     * to the output library to choose what position fits best.
     *
     * @method setPosition
     * @param {String} position The value.
     */
    Axis.prototype.setPosition = function(position) {
        if (position != this.POS_DEFAULT
                && position != this.POS_BOTTOM
                && position != this.POS_LEFT
                && position != this.POS_RIGHT
                && position != this.POS_TOP) {
            throw new Error('Invalid axis position.');
        }
        this._position = position;
    };

    /**
     * Set the stepSize on the axis.
     *
     * This is used to determine where ticks are displayed on the axis between min and max.
     *
     * @method setStepSize
     * @param {Number} stepSize The value.
     */
    Axis.prototype.setStepSize = function(stepSize) {
        if (typeof stepSize === 'undefined' || stepSize === null) {
            stepSize = null;
        } else if (isNaN(Number(stepSize))) {
            throw new Error('Value for stepSize is not a number.');
        } else {
            stepSize = Number(stepSize);
        }

        this._stepSize = stepSize;
    };

    return Axis;

});
