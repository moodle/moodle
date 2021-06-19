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
 * Chart output base.
 *
 * This takes a chart object and draws it.
 *
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module     core/chart_output_base
 */
define(['jquery'], function($) {

    /**
     * Chart output base.
     *
     * The constructor of an output class must instantly generate and display the
     * chart. It is also the responsability of the output module to check that
     * the node received is of the appropriate type, if not a new node can be
     * added within.
     *
     * The output module has total control over the content of the node and can
     * clear it or output anything to it at will. A node should not be shared by
     * two simultaneous output modules.
     *
     * @class
     * @param {Node} node The node to output with/in.
     * @param {Chart} chart A chart object.
     */
    function Base(node, chart) {
        this._node = $(node);
        this._chart = chart;
    }

    /**
     * Update method.
     *
     * This is the public method through which an output instance in informed
     * that the chart instance has been updated and they need to update the
     * chart rendering.
     *
     * @abstract
     * @return {Void}
     */
    Base.prototype.update = function() {
        throw new Error('Not supported.');
    };

    return Base;

});
