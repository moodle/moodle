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
 * A javascript module to retrieve calendar events from the server.
 *
 * @module     block_myoverview/calendar_events_repository
 * @class      repository
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    var dataCache = [
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 1',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 2',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 3',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 4',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 5',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 6',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 7',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 8',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 9',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 10',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 11',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 12',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 13',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 14',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 15',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 16',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
    ];

    /**
     * Retrieve a list of calendar events for the logged in user for the
     * given day range (inclusive).
     *
     * @method queryForUserByDays
     * @param {int}         startDay    The first day in the range
     * @param {int}         endDay      The last day in the range
     * @param {int}         limit       Limit the number of results returned
     * @param {int}         offset      Offset the result set by the given amount
     * @return {promise}    Resolved with an array of the calendar events
     */
    var queryForUserByDays = function(startDay, endDay, limit, offset) {
        var deferred = $.Deferred();

        setTimeout(function() {
            deferred.resolve(dataCache.slice(offset, offset + limit));
        }, 1000);

        return deferred.promise();
    };

    return {
        query_for_user_by_days: queryForUserByDays,
    };
});
