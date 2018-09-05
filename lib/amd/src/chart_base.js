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
 * Chart base.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_base
 */
define(['core/chart_series', 'core/chart_axis'], function(Series, Axis) {

    /**
     * Chart base.
     *
     * The constructor of a chart must never take any argument.
     *
     * {@link module:core/chart_base#_setDefault} to set the defaults on instantiation.
     *
     * @alias module:core/chart_base
     * @class
     */
    function Base() {
        this._series = [];
        this._labels = [];
        this._xaxes = [];
        this._yaxes = [];

        this._setDefaults();
    }

    /**
     * The series constituting this chart.
     *
     * @protected
     * @type {module:core/chart_series[]}
     */
    Base.prototype._series = null;

    /**
     * The labels of the X axis when categorised.
     *
     * @protected
     * @type {String[]}
     */
    Base.prototype._labels = null;

    /**
     * The title of the chart.
     *
     * @protected
     * @type {String}
     */
    Base.prototype._title = null;

    /**
     * The X axes.
     *
     * @protected
     * @type {module:core/chart_axis[]}
     */
    Base.prototype._xaxes = null;

    /**
     * The Y axes.
     *
     * @protected
     * @type {module:core/chart_axis[]}
     */
    Base.prototype._yaxes = null;

    /**
     * Colours to pick from when automatically assigning them.
     *
     * @const
     * @type {String[]}
     */
    Base.prototype.COLORSET = ['#f3c300', '#875692', '#f38400', '#a1caf1', '#be0032', '#c2b280', '#7f180d', '#008856',
            '#e68fac', '#0067a5'];

    /**
     * Set of colours defined by setting $CFG->chart_colorset to be picked when automatically assigning them.
     *
     * @type {String[]}
     * @protected
     */
    Base.prototype._configColorSet = null;

    /**
     * The type of chart.
     *
     * @abstract
     * @type {String}
     * @const
     */
    Base.prototype.TYPE = null;

    /**
     * Add a series to the chart.
     *
     * This will automatically assign a color to the series if it does not have one.
     *
     * @param {module:core/chart_series} series The series to add.
     */
    Base.prototype.addSeries = function(series) {
        this._validateSeries(series);
        this._series.push(series);

        // Give a default color from the set.
        if (series.getColor() === null) {
            var configColorSet = this.getConfigColorSet() || Base.prototype.COLORSET;
            series.setColor(configColorSet[this._series.length % configColorSet.length]);
        }
    };

    /**
     * Create a new instance of a chart from serialised data.
     *
     * the serialised attributes they offer and support.
     *
     * @static
     * @method create
     * @param {module:core/chart_base} Klass The class oject representing the type of chart to instantiate.
     * @param {Object} data The data of the chart.
     * @return {module:core/chart_base}
     */
    Base.prototype.create = function(Klass, data) {
        // TODO Not convinced about the usage of Klass here but I can't figure out a way
        // to have a reference to the class in the sub classes, in PHP I'd do new self().
        var Chart = new Klass();
        Chart.setConfigColorSet(data.config_colorset);
        Chart.setLabels(data.labels);
        Chart.setTitle(data.title);
        data.series.forEach(function(seriesData) {
            Chart.addSeries(Series.prototype.create(seriesData));
        });
        data.axes.x.forEach(function(axisData, i) {
            Chart.setXAxis(Axis.prototype.create(axisData), i);
        });
        data.axes.y.forEach(function(axisData, i) {
            Chart.setYAxis(Axis.prototype.create(axisData), i);
        });
        return Chart;
    };

    /**
     * Get an axis.
     *
     * @private
     * @param {String} xy Accepts the values 'x' or 'y'.
     * @param {Number} [index=0] The index of the axis of its type.
     * @param {Bool} [createIfNotExists=false] When true, create an instance if it does not exist.
     * @return {module:core/chart_axis}
     */
    Base.prototype.__getAxis = function(xy, index, createIfNotExists) {
        var axes = xy === 'x' ? this._xaxes : this._yaxes,
            setAxis = (xy === 'x' ? this.setXAxis : this.setYAxis).bind(this),
            axis;

        index = typeof index === 'undefined' ? 0 : index;
        createIfNotExists = typeof createIfNotExists === 'undefined' ? false : createIfNotExists;
        axis = axes[index];

        if (typeof axis === 'undefined') {
            if (!createIfNotExists) {
                throw new Error('Unknown axis.');
            }
            axis = new Axis();
            setAxis(axis, index);
        }

        return axis;
    };

    /**
     * Get colours defined by setting.
     *
     * @return {String[]}
     */
    Base.prototype.getConfigColorSet = function() {
        return this._configColorSet;
    };

    /**
     * Get the labels of the X axis.
     *
     * @return {String[]}
     */
    Base.prototype.getLabels = function() {
        return this._labels;
    };

    /**
     * Get the series.
     *
     * @return {module:core/chart_series[]}
     */
    Base.prototype.getSeries = function() {
        return this._series;
    };

    /**
     * Get the title of the chart.
     *
     * @return {String}
     */
    Base.prototype.getTitle = function() {
        return this._title;
    };

    /**
     * Get the type of chart.
     *
     * @see module:core/chart_base#TYPE
     * @return {String}
     */
    Base.prototype.getType = function() {
        if (!this.TYPE) {
            throw new Error('The TYPE property has not been set.');
        }
        return this.TYPE;
    };

    /**
     * Get the X axes.
     *
     * @return {module:core/chart_axis[]}
     */
    Base.prototype.getXAxes = function() {
        return this._xaxes;
    };

    /**
     * Get an X axis.
     *
     * @param {Number} [index=0] The index of the axis.
     * @param {Bool} [createIfNotExists=false] Create the instance of it does not exist at index.
     * @return {module:core/chart_axis}
     */
    Base.prototype.getXAxis = function(index, createIfNotExists) {
        return this.__getAxis('x', index, createIfNotExists);
    };

    /**
     * Get the Y axes.
     *
     * @return {module:core/chart_axis[]}
     */
    Base.prototype.getYAxes = function() {
        return this._yaxes;
    };

    /**
     * Get an Y axis.
     *
     * @param {Number} [index=0] The index of the axis.
     * @param {Bool} [createIfNotExists=false] Create the instance of it does not exist at index.
     * @return {module:core/chart_axis}
     */
    Base.prototype.getYAxis = function(index, createIfNotExists) {
        return this.__getAxis('y', index, createIfNotExists);
    };

    /**
     * Set colours defined by setting.
     *
     * @param {String[]} colorset An array of css colours.
     * @protected
     */
    Base.prototype.setConfigColorSet = function(colorset) {
        this._configColorSet = colorset;
    };

    /**
     * Set the defaults for this chart type.
     *
     * Child classes can extend this to set defaults values on instantiation.
     *
     * emphasize and self-document the defaults values set by the chart type.
     *
     * @protected
     */
    Base.prototype._setDefaults = function() {
        // For the children to extend.
    };

    /**
     * Set the labels of the X axis.
     *
     * This requires for each series to contain strictly as many values as there
     * are labels.
     *
     * @param {String[]} labels The labels.
     */
    Base.prototype.setLabels = function(labels) {
        if (labels.length && this._series.length && this._series[0].length != labels.length) {
            throw new Error('Series must match label values.');
        }
        this._labels = labels;
    };

    /**
     * Set the title of the chart.
     *
     * @param {String} title The title.
     */
    Base.prototype.setTitle = function(title) {
        this._title = title;
    };

    /**
     * Set an X axis.
     *
     * Note that this will override any predefined axis without warning.
     *
     * @param {module:core/chart_axis} axis The axis.
     * @param {Number} [index=0] The index of the axis.
     */
    Base.prototype.setXAxis = function(axis, index) {
        index = typeof index === 'undefined' ? 0 : index;
        this._validateAxis('x', axis, index);
        this._xaxes[index] = axis;
    };

    /**
     * Set a Y axis.
     *
     * Note that this will override any predefined axis without warning.
     *
     * @param {module:core/chart_axis} axis The axis.
     * @param {Number} [index=0] The index of the axis.
     */
    Base.prototype.setYAxis = function(axis, index) {
        index = typeof index === 'undefined' ? 0 : index;
        this._validateAxis('y', axis, index);
        this._yaxes[index] = axis;
    };

    /**
     * Validate an axis.
     *
     * @protected
     * @param {String} xy X or Y axis.
     * @param {module:core/chart_axis} axis The axis to validate.
     * @param {Number} [index=0] The index of the axis.
     */
    Base.prototype._validateAxis = function(xy, axis, index) {
        index = typeof index === 'undefined' ? 0 : index;
        if (index > 0) {
            var axes = xy == 'x' ? this._xaxes : this._yaxes;
            if (typeof axes[index - 1] === 'undefined') {
                throw new Error('Missing ' + xy + ' axis at index lower than ' + index);
            }
        }
    };

    /**
     * Validate a series.
     *
     * @protected
     * @param {module:core/chart_series} series The series to validate.
     */
    Base.prototype._validateSeries = function(series) {
        if (this._series.length && this._series[0].getCount() != series.getCount()) {
            throw new Error('Series do not have an equal number of values.');

        } else if (this._labels.length && this._labels.length != series.getCount()) {
            throw new Error('Series must match label values.');
        }
    };

    return Base;

});
