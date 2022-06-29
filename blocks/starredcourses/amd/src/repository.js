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
 * A javascript module to retrieve user's starred courses.
 *
 * @module block_starredcourses/repository
 * @copyright  2018 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {

    /**
     * Retrieve a list of starred courses.
     *
     * Valid args are:
     * int limit    number of records to retrieve
     * int offset   the offset of records to retrieve
     *
     * @method getStarredCourses
     * @param {object} args The request arguments
     * @return {promise} Resolved with an array of courses
     */
    var getStarredCourses = function(args) {

        var request = {
            methodname: 'block_starredcourses_get_starred_courses',
            args: args
        };

        var promise = Ajax.call([request])[0];

        promise.fail(Notification.exception);

        return promise;
    };

    return {
        getStarredCourses: getStarredCourses
    };
});
