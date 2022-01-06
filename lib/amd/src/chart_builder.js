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
 * Chart builder.
 *
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    /**
     * Chart builder.
     *
     * @exports core/chart_builder
     */
    var module = {

        /**
         * Make a chart instance.
         *
         * This takes data, most likely generated in PHP, and creates a chart instance from it
         * deferring most of the logic to {@link module:core/chart_base.create}.
         *
         * @param {Object} data The data.
         * @return {Promise} A promise resolved with the chart instance.
         */
        make: function(data) {
            var deferred = $.Deferred();
            require(['core/chart_' + data.type], function(Klass) {
                var instance = Klass.prototype.create(Klass, data);
                deferred.resolve(instance);
            });
            return deferred.promise();
        }
    };

    return module;

});
