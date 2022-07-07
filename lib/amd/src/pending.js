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
 * A helper to manage pendingJS checks.
 *
 * @module     core/pending
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.6
 */
define(['jquery'], function($) {

   /**
    * Request a new pendingPromise to be resolved.
    *
    * When the action you are performing is complete, simply call resolve on the returned Promise.
    *
    * @param    {Object}    pendingKey An optional key value to use
    * @return   {Promise}
    */
    var request = function(pendingKey) {
        var pendingPromise = $.Deferred();

        pendingKey = pendingKey || {};
        M.util.js_pending(pendingKey);

        pendingPromise.then(function() {
            return M.util.js_complete(pendingKey);
        })
        .catch();

        return pendingPromise;
    };

    request.prototype.constructor = request;

    return request;
});
