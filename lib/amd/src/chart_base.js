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
define(['core/chart_series'], function(Series) {

    /**
     * Chart base.
     */
    function Base() {
        this._series = [];
        this._labels = [];
    }
    Base.prototype._series = null;
    Base.prototype._labels = null;
    Base.prototype._title = null;
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
        for (var i = 0; i < data.series.length; i++) {
            Chart.addSeries(Series.prototype.create(data.series[i]));
        }
        return Chart;
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

    Base.prototype.setLabels = function(labels) {
        if (labels.length && this._series.length && this._series[0].length != labels.length) {
            throw new Error('Series must match label values.');
        }
        this._labels = labels;
    };

    Base.prototype.setTitle = function(title) {
        this._title = title;
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
