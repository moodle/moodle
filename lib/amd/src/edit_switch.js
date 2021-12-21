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
 * Controls the edit switch.
 *
 * @module     core/edit_switch
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import {dispatchEvent} from 'core/event_dispatcher';
import {exception as displayException} from 'core/notification';

/**
 * Change the Edit mode.
 *
 * @param {number} context The contextid that editing is being set for
 * @param {bool} setmode Whether editing is set or not
 * @return {Promise} Resolved with an array file the stored file url.
 */
const setEditMode = (context, setmode) => fetchMany([{
    methodname: 'core_change_editmode',
    args: {
        context,
        setmode,
    },
}])[0];

/**
 * Toggle the edit switch
 *
 * @method
 * @protected
 * @param {HTMLElement} editSwitch
 */
const toggleEditSwitch = editSwitch => {
    if (editSwitch.checked) {
        editSwitch.setAttribute('aria-checked', true);
    } else {
        editSwitch.setAttribute('aria-checked', false);
    }

    const event = notifyEditModeSet(editSwitch, editSwitch.checked);
    if (!event.defaultPrevented) {
        window.location = editSwitch.dataset.pageurl;
    }
};

/**
 * Names of events for core/edit_switch.
 *
 * @static
 * @property {String} editModeSet See {@link event:core/edit_switch/editModeSet}
 */
export const eventTypes = {
    /**
     * An event triggered when the edit mode toggled.
     *
     * @event core/edit_switch/editModeSet
     * @type {CustomEvent}
     * @property {HTMLElement} target The switch used to toggle the edit mode
     * @property {object} detail
     * @property {bool} detail.editMode
     */
    editModeSet: 'core/edit_switch/editModeSet',
};

/**
 * Dispatch the editModeSet event after changing the edit mode.
 *
 * This event is cancelable.
 *
 * The default action is to reload the page after toggling the edit mode.
 *
 * @method
 * @protected
 * @param {HTMLElement} container
 * @param {bool} editMode
 * @returns {CustomEvent}
 */
const notifyEditModeSet = (container, editMode) => dispatchEvent(
    eventTypes.editModeSet,
    {editMode},
    container,
    {cancelable: true}
);

/**
 * Add the eventlistener for the editswitch.
 *
 * @param {string} editingSwitchId The id of the editing switch to listen for
 */
export const init = editingSwitchId => {
    const editSwitch = document.getElementById(editingSwitchId);
    editSwitch.addEventListener('change', () => {
        setEditMode(editSwitch.dataset.context, editSwitch.checked)
        .then(result => {
            if (result.success) {
                toggleEditSwitch(editSwitch);
            } else {
                editSwitch.checked = false;
            }
            return;
        })
        .catch(displayException);
    });
};
