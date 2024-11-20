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
 * Repository to perform WS calls for mod_bigbluebuttonbn.
 *
 * @module      mod_bigbluebuttonbn/repository
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';

/**
 * Fetch the list of recordings from the server.
 *
 * @param   {Number} bigbluebuttonbnid The instance ID
 * @param   {String} tools the set of tools to display
 * @param   {number} groupid
 * @returns {Promise}
 */
export const fetchRecordings = (bigbluebuttonbnid, tools, groupid) => {
    const args = {
        bigbluebuttonbnid,
        tools,
    };

    if (groupid) {
        args.groupid = groupid;
    }

    return fetchMany([{methodname: 'mod_bigbluebuttonbn_get_recordings', args}])[0];
};

/**
 * Fetch the list of recordings from the server that can be imported.
 *
 * @param   {Number} destinationinstanceid The destination instance ID
 * @param   {Number} sourcebigbluebuttonbnid The original instance ID
 * @param   {Number} sourcecourseid The destination instance ID
 * @param   {String} tools the set of tools to display
 * @param   {number} groupid
 * @returns {Promise}
 */
export const fetchRecordingsToImport = (
    destinationinstanceid,
    sourcebigbluebuttonbnid,
    sourcecourseid,
    tools,
    groupid
) => {
    const args = {
        destinationinstanceid,
        sourcebigbluebuttonbnid,
        sourcecourseid,
        tools,
    };

    if (groupid) {
        args.groupid = groupid;
    }

    return fetchMany([{methodname: 'mod_bigbluebuttonbn_get_recordings_to_import', args}])[0];
};

/**
 * Perform an update on a single recording.
 *
 * @param   {object} args The instance ID
 * @returns {Promise}
 */
export const updateRecording = args => fetchMany([
    {
        methodname: 'mod_bigbluebuttonbn_update_recording',
        args,
    }
])[0];

/**
 * End the Meeting
 *
 * @param {number} bigbluebuttonbnid
 * @param {number} groupid
 * @returns {Promise}
 */
export const endMeeting = (bigbluebuttonbnid, groupid) => fetchMany([
    {
        methodname: 'mod_bigbluebuttonbn_end_meeting',
        args: {
            bigbluebuttonbnid,
            groupid
        },
    }
])[0];

/**
 * Validate completion.
 *
 * @param {number} bigbluebuttonbnid
 * @returns {Promise}
 */
export const completionValidate = (bigbluebuttonbnid) => fetchMany([
    {
        methodname: 'mod_bigbluebuttonbn_completion_validate',
        args: {
            bigbluebuttonbnid
        },
    }
])[0];


/**
 * Fetch meeting info for the specified meeting.
 *
 * @param {number} bigbluebuttonbnid
 * @param {number} groupid
 * @param {boolean} [updatecache=false]
 * @returns {Promise}
 */
export const getMeetingInfo = (bigbluebuttonbnid, groupid, updatecache = false) => fetchMany([
    {
        methodname: 'mod_bigbluebuttonbn_meeting_info',
        args: {
            bigbluebuttonbnid,
            groupid,
            updatecache,
        },
    }
])[0];
