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
 * A javascript module to retrieve courses from the server.
 *
 * @module     block_myoverview/courses_view_repository
 * @package    block_myoverview
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    var STATUS = {
        PAST: 0,
        IN_PROGRESS: 1,
        FUTURE: 2
    },

    dataCache = {
        past: [
            {
                courseid: 1,
                shortname: 'Course 1',
                summary: 'This is a brief summary of course 1',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 2,
                shortname: 'Course 2',
                summary: 'This is a brief summary of course 2',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 3,
                shortname: 'Course 3',
                summary: 'This is a brief summary of course 3',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 4,
                shortname: 'Course 4',
                summary: 'This is a brief summary of course 4',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 5,
                shortname: 'Course 5',
                summary: 'This is a brief summary of course 5',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 6,
                shortname: 'Course 6',
                summary: 'This is a brief summary of course 6',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 7,
                shortname: 'Course 7',
                summary: 'This is a brief summary of course 7',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 8,
                shortname: 'Course 8',
                summary: 'This is a brief summary of course 8',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 9,
                shortname: 'Course 9',
                summary: 'This is a brief summary of course 9',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 10,
                shortname: 'Course 10',
                summary: 'This is a brief summary of course 10',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 11,
                shortname: 'Course 11',
                summary: 'This is a brief summary of course 11',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            },
            {
                courseid: 12,
                shortname: 'Course 12',
                summary: 'This is a brief summary of course 12',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.PAST
            }
        ],
        inprogress: [
            {
                courseid: 13,
                shortname: 'Course 13',
                summary: 'This is a brief summary of course 13',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 14,
                shortname: 'Course 14',
                summary: 'This is a brief summary of course 14',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 15,
                shortname: 'Course 15',
                summary: 'This is a brief summary of course 15',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 16,
                shortname: 'Course 16',
                summary: 'This is a brief summary of course 16',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 17,
                shortname: 'Course 17',
                summary: 'This is a brief summary of course 17',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 18,
                shortname: 'Course 18',
                summary: 'This is a brief summary of course 18',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 19,
                shortname: 'Course 19',
                summary: 'This is a brief summary of course 19',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 20,
                shortname: 'Course 20',
                summary: 'This is a brief summary of course 20',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 21,
                shortname: 'Course 21',
                summary: 'This is a brief summary of course 21',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 22,
                shortname: 'Course 22',
                summary: 'This is a brief summary of course 22',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 23,
                shortname: 'Course 23',
                summary: 'This is a brief summary of course 23',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 24,
                shortname: 'Course 24',
                summary: 'This is a brief summary of course 24',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            },
            {
                courseid: 25,
                shortname: 'Course 25',
                summary: 'This is a brief summary of course 25',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.IN_PROGRESS
            }
        ],
        future: [
            {
                courseid: 26,
                shortname: 'Course 26',
                summary: 'This is a brief summary of course 26',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 27,
                shortname: 'Course 27',
                summary: 'This is a brief summary of course 27',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 28,
                shortname: 'Course 28',
                summary: 'This is a brief summary of course 28',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 29,
                shortname: 'Course 29',
                summary: 'This is a brief summary of course 29',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 30,
                shortname: 'Course 30',
                summary: 'This is a brief summary of course 30',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 31,
                shortname: 'Course 31',
                summary: 'This is a brief summary of course 31',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 32,
                shortname: 'Course 32',
                summary: 'This is a brief summary of course 32',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 33,
                shortname: 'Course 33',
                summary: 'This is a brief summary of course 33',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 34,
                shortname: 'Course 34',
                summary: 'This is a brief summary of course 34',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 35,
                shortname: 'Course 35',
                summary: 'This is a brief summary of course 35',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 36,
                shortname: 'Course 36',
                summary: 'This is a brief summary of course 36',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            },
            {
                courseid: 37,
                shortname: 'Course 37',
                summary: 'This is a brief summary of course 37',
                startdate: 'Oct 1, 2016',
                enddate: 'Oct 6, 2016',
                status: STATUS.FUTURE
            }
        ]
    };

    /**
     * Retrieve a list of courses by status.
     *
     * @method queryFromStatus
     * @param {int}         status      The status of the course.
     * @param {int}         limit       Limit the number of results returned
     * @param {int}         offset      Offset the result set by the given amount
     * @return {promise}    Resolved with an array of courses
     */
    var queryFromStatus = function(status, limit, offset) {
        var deferred = $.Deferred();

        setTimeout(function() {
            deferred.resolve(dataCache[status].slice(offset, offset + limit));
        }, 1000);

        return deferred.promise();
    };

    /**
     * Get the number of courses by status (past, inprogress and future).
     *
     * @param {String} status Status of the course to be fetched.
     * @returns {int} Total of course by status.
     */
    var getTotalByStatus = function(status) {
        return dataCache[status].length;
    };

    return {
        queryFromStatus: queryFromStatus,
        getTotalByStatus: getTotalByStatus
    };
});
