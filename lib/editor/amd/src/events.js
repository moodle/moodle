// This file is part of Moodle - http://moodle.org/ //
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
 * Javascript events for the `core_editor` subsystem.
 *
 * @module     core_editor/events
 * @copyright  2021 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.0
 */

import {dispatchEvent} from 'core/event_dispatcher';

/**
 * Events for the `core_editor` subsystem.
 *
 * @constant
 * @property {String} editorContentRestored See {@link event:editorContentRestored}
 */
export const eventTypes = {
    /**
     * An event triggered when an editor restores auto-saved content.
     *
     * @event editorContentRestored
     */
    editorContentRestored: 'core_editor/contentRestored',
};

/**
 * Trigger an event to indicate that editor content was restored.
 *
 * @method  notifyEditorContentRestored
 * @param   {HTMLElement|null} editor The element that was modified
 * @returns {CustomEvent}
 * @fires   editorContentRestored
 */
export const notifyEditorContentRestored = editor => {
    if (!editor) {
        window.console.warn(
            `The HTMLElement representing the editor that was modified should be provided to notifyEditorContentRestored.`
        );
    }
    return dispatchEvent(
        eventTypes.editorContentRestored,
        {},
        editor || document
    );
};
