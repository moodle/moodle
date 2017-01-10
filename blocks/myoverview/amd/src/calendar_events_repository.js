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

    var date = new Date(),
        currentTime = Math.floor(date.setHours(0, 0, 0, 0) / 1000),
        todayTime = currentTime + (60 * 60),
        tomorrowTime = currentTime + (60 * 60 * 26),
        twoWeeksTime = currentTime + (60 * 60 * 24 * 14),
        twoMonthsTime = currentTime + (60 * 60 * 24 * 56),
        twoYearsTime = currentTime + (60 * 60 * 24 * 365 * 2),
        dataCache = [
        {
            actionname: 'Submit assignment',
            actionurl: 'https://www.google.com',
            enddate: 'Nov 4th, 10am',
            contextname: 'Assignment due 1',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: todayTime,
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
            orderTime: todayTime,
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
            orderTime: todayTime,
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
            orderTime: todayTime,
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
            orderTime: todayTime,
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
            orderTime: todayTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            orderTime: tomorrowTime,
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
            contextname: 'Assignment due 17',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 18',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 19',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 20',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 21',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 22',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 23',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 24',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 25',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 26',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 27',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 28',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 29',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 30',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 31',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 32',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoWeeksTime,
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
            contextname: 'Assignment due 33',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoMonthsTime,
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
            contextname: 'Assignment due 34',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoMonthsTime,
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
            contextname: 'Assignment due 35',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoMonthsTime,
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
            contextname: 'Assignment due 36',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoYearsTime,
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
            contextname: 'Assignment due 37',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoYearsTime,
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
            contextname: 'Assignment due 38',
            contexturl: 'https://www.google.com',
            coursename: 'Course 1',
            itemcount: 1,
            orderTime: twoYearsTime,
            icon: {
                key: 'icon',
                component: 'mod_assign',
                alttext: 'Assignment icon',
            },
        },
    ];

    /**
     * Retrieve a list of calendar events for the logged in user after the given
     * time.
     *
     * @method queryFromTime
     * @param {int}         startTime   Only get events after this time
     * @param {int}         limit       Limit the number of results returned
     * @param {int}         offset      Offset the result set by the given amount
     * @return {promise}    Resolved with an array of the calendar events
     */
    var queryFromTime = function(startTime, limit, offset) {
        var deferred = $.Deferred();

        setTimeout(function() {
            deferred.resolve(dataCache.slice(offset, offset + limit));
        }, 1000);

        return deferred.promise();
    };

    return {
        queryFromTime: queryFromTime,
    };
});
