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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
], function($, Ajax) {
    'use strict';

    return {
        /**
         * A Javascript Promise Wrapper to make AJAX calls.
         *
         * Valid args are:
         * int example 2     Only get events after this time
         *
         * @method fetchSWE
         * @param {object} data_chunk The request arguments
         * @return {promise} Resolved with an array of the calendar events
         */
        qmAjax: function(data_chunk) {
            var promiseObj = new Promise(function(resolve, reject) {

                var send_this = [{
                    methodname: 'block_quickmail_qm_ajax',
                    args: {
                        datachunk: data_chunk,
                    }
                }];
                Ajax.call(send_this)[0].then(function(results) {
                    resolve(JSON.parse(results.data));
                }).catch(function(ev) {
                    reject(ev);
                });
            });
            return promiseObj;
        },
    };
});
