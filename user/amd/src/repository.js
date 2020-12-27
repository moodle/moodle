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
 * Module to handle AJAX interactions.
 *
 * @module     core_user/repository
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {call as fetchMany} from 'core/ajax';

/**
 * Unenrol the user with the specified user enrolmentid ID.
 *
 * @param {Number} userEnrolmentId
 * @return {Promise}
 */
export const unenrolUser = userEnrolmentId => {
    return fetchMany([{
        methodname: 'core_enrol_unenrol_user_enrolment',
        args: {
            ueid: userEnrolmentId,
        },
    }])[0];
};

/**
 * Submit the user enrolment form with the specified form data.
 *
 * @param {String} formdata
 * @return {Promise}
 */
export const submitUserEnrolmentForm = formdata => {
    return fetchMany([{
        methodname: 'core_enrol_submit_user_enrolment_form',
        args: {
            formdata,
        },
    }])[0];
};

export const createNotesForUsers = notes => {
    return fetchMany([{
        methodname: 'core_notes_create_notes',
        args: {
            notes
        }
    }])[0];
};

export const sendMessagesToUsers = messages => {
    return fetchMany([{
        methodname: 'core_message_send_instant_messages',
        args: {messages}
    }])[0];
};
