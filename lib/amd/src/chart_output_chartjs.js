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
    'core/chart_bar',
    'core/chart_output_base',
    'core/chart_line',
    'core/chart_pie',
    'core/chart_series'
], function($, Chartjs, Axis, Bar, Base, Line, Pie, Series) {

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
     * Clean data.
     *
     * @param {(String|String[])} data A single string or an array of strings.
     * @returns {(String|String[])}
     * @protected
     */
    Output.prototype._cleanData = function(data) {
        if (data instanceof Array) {
            return data.map(function(value) {
                return $('<span>').html(value).text();
            });
        } else {
            return $('<span>').html(data).text();
        }
    };

    /**
     * Get the chart type and handles the Chart.js specific chart types.
     *
     * By default returns the current chart TYPE value. Also does the handling of specific chart types, for example
     * check if the bar chart should be horizontal and the pie chart should be displayed as a doughnut.
     *
     * @method getChartType
     * @returns {String} the chart type.
     * @protected
     */
    Output.prototype._getChartType = function() {
        var type = this._chart.getType();

        // Bars can be displayed vertically and horizontally, defining horizontalBar type.
        if (this._chart.getType() === Bar.prototype.TYPE && this._chart.getHorizontal() === true) {
            type = 'horizontalBar';
        } else if (this._chart.getType() === Pie.prototype.TYPE && this._chart.getDoughnut() === true) {
            // Pie chart can be displayed as doughnut.
            type = 'doughnut';
        }

        return type;
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
                labelString: this._cleanData(axis.getLabel())
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
            type: this._getChartType(),
            data: {
                labels: this._cleanData(this._chart.getLabels()),
                datasets: this._makeDatasetsConfig()
            },
            options: {
                title: {
                    display: this._chart.getTitle() !== null,
                    text: this._cleanData(this._chart.getTitle())
                }
            }
        };

        this._chart.getXAxes().forEach(function(axis, i) {
            var axisLabels = axis.getLabels();

            config.options.scales = config.options.scales || {};
            config.options.scales.xAxes = config.options.scales.xAxes || [];
            config.options.scales.xAxes[i] = this._makeAxisConfig(axis, 'x', i);

            if (axisLabels !== null) {
                config.options.scales.xAxes[i].ticks.callback = function(value, index) {
                    return axisLabels[index] || '';
                };
            }
            config.options.scales.xAxes[i].stacked = this._isStacked();
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
            config.options.scales.yAxes[i].stacked = this._isStacked();
        }.bind(this));

        config.options.tooltips = {
            callbacks: {
                label: this._makeTooltip.bind(this)
            }
        };

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
                label: this._cleanData(series.getLabel()),
                data: series.getValues(),
                type: series.getType(),
                fill: false,
                backgroundColor: colors,
                // Pie charts look better without borders.
                borderColor: this._chart.getType() == Pie.prototype.TYPE ? null : colors,
                lineTension: this._isSmooth(series) ? 0.3 : 0
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

    /**
     * Get the chart data, add labels and rebuild the tooltip.
     *
     * @param {Object[]} tooltipItem The tooltip item data.
     * @param {Object[]} data The chart data.
     * @returns {String}
     * @protected
     */
    Output.prototype._makeTooltip = function(tooltipItem, data) {

        // Get series and chart data to rebuild the tooltip and add labels.
        var series = this._chart.getSeries()[tooltipItem.datasetIndex];
        var serieLabel = series.getLabel();
        var serieLabels = series.getLabels();
        var chartData = data.datasets[tooltipItem.datasetIndex].data;
        var tooltipData = chartData[tooltipItem.index];

        // Build default tooltip.
        var tooltip = [];

        // Pie and doughnut charts does not have axis.
        if (tooltipItem.xLabel == '' && tooltipItem.yLabel == '') {
            var chartLabels = this._cleanData(this._chart.getLabels());
            tooltip.push(chartLabels[tooltipItem.index]);
        }

        // Add series labels to the tooltip if any.
        if (serieLabels !== null) {
            tooltip.push(this._cleanData(serieLabels[tooltipItem.index]));
        } else {
            tooltip.push(this._cleanData(serieLabel) + ': ' + tooltipData);
        }

        return tooltip;
    };

    /**
     * Verify if the chart line is smooth or not.
     *
     * @protected
     * @param {module:core/chart_series} series The series.
     * @returns {Bool}
     */
    Output.prototype._isSmooth = function(series) {
        var smooth = false;
        if (this._chart.getType() === Line.prototype.TYPE) {
            smooth = series.getSmooth();
            if (smooth === null) {
                smooth = this._chart.getSmooth();
            }
        } else if (series.getType() === Series.prototype.TYPE_LINE) {
            smooth = series.getSmooth();
        }

        return smooth;
    };

    /**
     * Verify if the bar chart is stacked or not.
     *
     * @protected
     * @returns {Bool}
     */
    Output.prototype._isStacked = function() {
        var stacked = false;

        // Stacking is (currently) only supported for bar charts.
        if (this._chart.getType() === Bar.prototype.TYPE) {
            stacked = this._chart.getStacked();
        }

        return stacked;
    };

    /** @override */
    Output.prototype.update = function() {
        $.extend(true, this._config, this._makeConfig());
        this._chartjs.update();
    };

    return Output;

});
