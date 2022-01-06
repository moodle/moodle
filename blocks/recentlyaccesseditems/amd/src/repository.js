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
 * A javascript module to handle user ajax actions.
 *
 * @module     block_recentlyaccesseditems/repository
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax'], function(Ajax) {

    /**
     * Get the list of items that the user has most recently accessed.
     *
     * @method getRecentItems
     * @param {int} limit Only return this many results
     * @return {promise} Resolved with an array of items
     */
    var getRecentItems = function(limit) {
        var args = {};
        if (typeof limit !== 'undefined') {
            args.limit = limit;
        }
        var request = {
            methodname: 'block_recentlyaccesseditems_get_recent_items',
            args: args
        };
        return Ajax.call([request])[0];
    };
    return {
        getRecentItems: getRecentItems
    };
});