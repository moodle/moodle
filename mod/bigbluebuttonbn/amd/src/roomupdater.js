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
 * JS room updater.
 *
 * @module      mod_bigbluebuttonbn/roomupdater
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from "core/templates";
import {exception as displayException} from 'core/notification';
import {getMeetingInfo} from './repository';

const timeout = 5000;
const maxFactor = 10;

let updateCount = 0;
let updateFactor = 1;
let timerReference = null;
let timerRunning = false;

const resetValues = () => {
    updateCount = 0;
    updateFactor = 1;
};

/**
 * Start the information poller.
 */
export const start = () => {
    timerRunning = true;
    timerReference = setTimeout(() => poll(), timeout);
};

/**
 * Stop the room updater.
 */
export const stop = () => {
    timerRunning = false;
    if (timerReference) {
        clearInterval(timerReference);
        timerReference = null;
    }

    resetValues();
};

const poll = () => {
    if (!timerRunning) {
        // The poller has been stopped.
        return;
    }
    if ((updateCount % updateFactor) === 0) {
        updateRoom()
        .then(() => {
            if (updateFactor >= maxFactor) {
                updateFactor = 1;
            } else {
                updateFactor++;
            }

            return;

        })
        .catch()
        .then(() => {
            timerReference = setTimeout(() => poll(), timeout);
            return;
        })
        .catch();
    }
};

/**
 * Update the room information.
 *
 * @param {boolean} [updatecache=false]
 * @returns {Promise}
 */
export const updateRoom = (updatecache = false) => {
    const bbbRoomViewElement = document.getElementById('bbb-room-view');
    const bbbId = bbbRoomViewElement.dataset.bbbId;
    const groupId = bbbRoomViewElement.dataset.groupId;
    return getMeetingInfo(bbbId, groupId, updatecache)
        .then(data => {
            // Just make sure we have the right information for the template.
            data.haspresentations = false;
            if (data.presentations && data.presentations.length) {
                data.haspresentations = true;
            }
            return Templates.renderForPromise('mod_bigbluebuttonbn/room_view', data);
        })
        .then(({html, js}) => Templates.replaceNodeContents(bbbRoomViewElement, html, js))
        .catch(displayException);
};
