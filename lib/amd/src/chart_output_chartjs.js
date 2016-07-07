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
 * Chart output for chart.js.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_output_chartjs
 */
define([
    'jquery',
    'core/chartjs',
    'core/chart_axis',
    'core/chart_output_base',
    'core/chart_pie',
], function($, Chartjs, Axis, Base, Pie) {

    /**
     * Makes an axis ID.
     *
     * @param {String} xy Accepts 'x' and 'y'.
     * @param {Number} index The axis index.
     * @return {String}
     */
    var makeAxisId = function(xy, index) {
        return 'axis-' + xy + '-' + index;
    };

    /**
     * Chart output for Chart.js.
     *
     * @class
     * @alias module:core/chart_output_chartjs
     * @extends {module:core/chart_output_base}
     */
    function Output() {
        Base.prototype.constructor.apply(this, arguments);

        // Make sure that we've got a canvas tag.
        this._canvas = this._node;
        if (this._canvas.prop('tagName') != 'CANVAS') {
            this._canvas = $('<canvas>');
            this._node.append(this._canvas);
        }

        this._build();
    }
    Output.prototype = Object.create(Base.prototype);

    /**
     * Reference to the chart config object.
     *
     * @type {Object}
     * @protected
     */
    Output.prototype._config = null;

    /**
     * Reference to the instance of chart.js.
     *
     * @type {Object}
     * @protected
     */
    Output.prototype._chartjs = null;

    /**
     * Reference to the canvas node.
     *
     * @type {Jquery}
     * @protected
     */
    Output.prototype._canvas = null;

    /**
     * Builds the config and the chart.
     *
     * @protected
     */
    Output.prototype._build = function() {
        this._config = this._makeConfig();
        this._chartjs = new Chartjs(this._canvas[0], this._config);
    };

    /**
     * Make the axis config.
     *
     * @protected
     * @param {module:core/chart_axis} axis The axis.
     * @param {String} xy Accepts 'x' or 'y'.
     * @param {Number} index The axis index.
     * @return {Object} The axis config.
     */
    Output.prototype._makeAxisConfig = function(axis, xy, index) {
        var scaleData = {
            id: makeAxisId(xy, index)
        };

        if (axis.getPosition() !== Axis.prototype.POS_DEFAULT) {
            scaleData.position = axis.getPosition();
        }

        if (axis.getLabel() !== null) {
            scaleData.scaleLabel = {
                display: true,
                labelString: axis.getLabel()
            };
        }

        if (axis.getStepSize() !== null) {
            scaleData.ticks = scaleData.ticks || {};
            scaleData.ticks.stepSize = axis.getStepSize();
        }

        if (axis.getMax() !== null) {
            scaleData.ticks = scaleData.ticks || {};
            scaleData.ticks.max = axis.getMax();
        }

        if (axis.getMin() !== null) {
            scaleData.ticks = scaleData.ticks || {};
            scaleData.ticks.min = axis.getMin();
        }

        return scaleData;
    };

    /**
     * Make the config config.
     *
     * @protected
     * @param {module:core/chart_axis} axis The axis.
     * @return {Object} The axis config.
     */
    Output.prototype._makeConfig = function() {
        var config = {
            type: this._chart.getType(),
            data: {
                labels: this._chart.getLabels(),
                datasets: this._makeDatasetsConfig()
            },
            options: {
                title: {
                    display: this._chart.getTitle() !== null,
                    text: this._chart.getTitle()
                }
            }
        };

        this._chart.getXAxes().forEach(function(axis, i) {
            config.options.scales = config.options.scales || {};
            config.options.scales.xAxes = config.options.scales.xAxes || [];
            config.options.scales.xAxes[i] = this._makeAxisConfig(axis, 'x', i);
        }.bind(this));

        this._chart.getYAxes().forEach(function(axis, i) {
            var axisLabels = axis.getLabels();

            config.options.scales = config.options.scales || {};
            config.options.scales.yAxes = config.options.scales.yAxes || [];
            config.options.scales.yAxes[i] = this._makeAxisConfig(axis, 'y', i);

            if (axisLabels !== null) {
                config.options.scales.yAxes[i].ticks.callback = function(value) {
                    return axisLabels[parseInt(value, 10)] || '';
                };
            }
        }.bind(this));

        return config;
    };

    /**
     * Get the datasets configurations.
     *
     * @protected
     * @return {Object[]}
     */
    Output.prototype._makeDatasetsConfig = function() {
        var sets = this._chart.getSeries().map(function(series) {
            var colors = series.hasColoredValues() ? series.getColors() : series.getColor();
            var dataset = {
                label: series.getLabel(),
                data: series.getValues(),
                type: series.getType(),
                fill: false,
                backgroundColor: colors,
                // Pie charts look better without borders.
                borderColor: this._chart.getType() == Pie.prototype.TYPE ? null : colors,
            };

            if (series.getXAxis() !== null) {
                dataset.xAxisID = makeAxisId('x', series.getXAxis());
            }
            if (series.getYAxis() !== null) {
                dataset.yAxisID = makeAxisId('y', series.getYAxis());
            }

            return dataset;
        }.bind(this));
        return sets;
    };

    /** @override */
    Output.prototype.update = function() {
        $.extend(true, this._config, this._makeConfig());
        this._chartjs.update();
    };

    return Output;

});
