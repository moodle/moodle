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
 */
define([
    'jquery',
    'core/chartjs',
    'core/chart_axis',
    'core/chart_output_base',
], function($, Chartjs, Axis, Base) {

    /**
     * Chart output for Chart.js.
     */
    function Output() {
        Base.prototype.constructor.apply(this, arguments);
        this._build();
    }

    Output.prototype = Object.create(Base.prototype);

    Output.prototype._config = null;
    Output.prototype._chartjs = null;

    Output.prototype.getDatasets = function() {
        var sets = this._chart.getSeries().map(function(series) {
            return {
                label: series.getLabel(),
                data: series.getValues(),
                type: series.getType(),
                fill: false,
                borderColor: series.getColor(),
                backgroundColor: series.getColor()
            };
        });
        return sets;
    };

    Output.prototype._build = function() {
        this._config = this._makeConfig();
        this._chartjs = new Chartjs(this._node[0], this._config);
    };

    Output.prototype._makeAxisConfig = function(axis) {
        var scaleData = {};

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

    Output.prototype._makeConfig = function() {
        var config = {
            type: this._chart.getType(),
            data: {
                labels: this._chart.getLabels(),
                datasets: this.getDatasets()
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
            config.options.scales.xAxes[i] = this._makeAxisConfig(axis);
        }.bind(this));

        this._chart.getYAxes().forEach(function(axis, i) {
            config.options.scales = config.options.scales || {};
            config.options.scales.yAxes = config.options.scales.yAxes || [];
            config.options.scales.yAxes[i] = this._makeAxisConfig(axis);
        }.bind(this));

        return config;
    };

    Output.prototype.update = function() {
        $.extend(true, this._config, this._makeConfig());
        this._chartjs.update();
    };

    return Output;

});
