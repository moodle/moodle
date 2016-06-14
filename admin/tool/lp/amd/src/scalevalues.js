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
 * Module to get the scale values.
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax'], function($, ajax) {
    var localCache = [];

    return /** @alias module:tool_lp/scalevalues */ {

        /**
         * Return a promise object that will be resolved into a string eventually (maybe immediately).
         *
         * @method get_values
         * @param {Number} scaleid The scale id
         * @return [] {Promise}
         */

        get_values: function(scaleid) {

            var deferred = $.Deferred();

            if (typeof localCache[scaleid] === 'undefined') {
                ajax.call([{
                    methodname: 'core_competency_get_scale_values',
                    args: {scaleid : scaleid},
                    done: function(scaleinfo) {
                        localCache[scaleid] = scaleinfo;
                        deferred.resolve(scaleinfo);
                    },
                    fail: (deferred.reject)
                }]);
            } else {
                deferred.resolve(localCache[scaleid]);
            }

            return deferred.promise();
        }
    };
});
