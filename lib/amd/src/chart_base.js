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
 */
define(['core/chart_series', 'core/chart_axis'], function(Series, Axis) {

    /**
     * Chart base.
     */
    function Base() {
        this._series = [];
        this._labels = [];
        this._xaxes = [];
        this._yaxes = [];

        this._setDefaults();
    }
    Base.prototype._series = null;
    Base.prototype._labels = null;
    Base.prototype._title = null;
    Base.prototype._xaxes = null;
    Base.prototype._yaxes = null;

    Base.prototype.COLORSET = ['red', 'green', 'blue', 'yellow', 'pink', 'orange'];
    Base.prototype.TYPE = null;

    Base.prototype.addSeries = function(serie) {
        this._validateSerie(serie);
        this._series.push(serie);

        // Give a default color from the set.
        if (serie.getColor() === null) {
            serie.setColor(Base.prototype.COLORSET[this._series.length % Base.prototype.COLORSET.length]);
        }
    };

    Base.prototype.create = function(Klass, data) {
        // TODO Not convinced about the usage of Klass here but I can't figure out a way
        // to have a reference to the class in the sub classes, in PHP I'd do new self().
        var Chart = new Klass();

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

    Base.prototype.getLabels = function() {
        return this._labels;
    };

    Base.prototype.getSeries = function() {
        return this._series;
    };

    Base.prototype.getTitle = function() {
        return this._title;
    };

    Base.prototype.getType = function() {
        if (!this.TYPE) {
            throw new Error('The TYPE property has not been set.');
        }
        return this.TYPE;
    };

    Base.prototype.getXAxes = function() {
        return this._xaxes;
    };

    Base.prototype.getXAxis = function(index, createIfNotExists) {
        return this.__getAxis('x', index, createIfNotExists);
    };

    Base.prototype.getYAxes = function() {
        return this._yaxes;
    };

    Base.prototype.getYAxis = function(index, createIfNotExists) {
        return this.__getAxis('y', index, createIfNotExists);
    };

    Base.prototype._setDefaults = function() {
        // For the children to extend.
    };

    Base.prototype.setLabels = function(labels) {
        if (labels.length && this._series.length && this._series[0].length != labels.length) {
            throw new Error('Series must match label values.');
        }
        this._labels = labels;
    };

    Base.prototype.setTitle = function(title) {
        this._title = title;
    };

    Base.prototype.setXAxis = function(axis, index) {
        index = typeof index === 'undefined' ? 0 : index;
        this._xaxes[index] = axis;
    };

    Base.prototype.setYAxis = function(axis, index) {
        index = typeof index === 'undefined' ? 0 : index;
        this._yaxes[index] = axis;
    };

    Base.prototype._validateSerie = function(serie) {
        if (this._series.length && this._series[0].getCount() != serie.getCount()) {
            throw new Error('Series do not have an equal number of values.');

        } else if (this._labels.length && this._labels.length != serie.getCount()) {
            throw new Error('Series must match label values.');
        }
    };

    return Base;

});
