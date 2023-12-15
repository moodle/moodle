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
 * Storage helper for the Moodle Tiny Autosave plugin.
 *
 * @module      tiny_autosave/autosaver
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Options from './options';
import * as Storage from './storage';
import Log from 'core/log';
import {eventTypes} from 'core_form/events';
import {getLogSource} from './common';

export const register = (editor) => {
    const undoHandler = () => {
        if (!editor.undoManager.hasUndo()) {
            Log.debug(`Ignoring undo event as there is no undo history`, getLogSource(editor));
            return;
        }
        Storage.saveDraft(editor);
    };

    const visibilityChangedHandler = () => {
        if (document.visibilityState === 'hidden') {
            if (Options.isInitialised(editor)) {
                Storage.saveDraft(editor);
            }
        }
    };

    // Javascript form submission handler.
    const handleFormSubmittedByJavascript = (e) => {
        if (Options.isInitialised(editor) && e.target.contains(editor.getElement())) {
            removeAutoSaveSession();
        }
    };

    // Remove the auto save session.
    const removeAutoSaveSession = () => {
        document.removeEventListener('visibilitychange', visibilityChangedHandler);
        document.removeEventListener(eventTypes.formSubmittedByJavascript, handleFormSubmittedByJavascript);
        Storage.removeAutosaveSession(editor);
    };

    // Attempt to store the draft one final time before the page unloads.
    // Note: This may need to be sent as a beacon instead.
    document.addEventListener('visibilitychange', visibilityChangedHandler);

    // When the page is submitted as a form, remove the draft.
    editor.on('submit', removeAutoSaveSession);
    document.addEventListener(eventTypes.formSubmittedByJavascript, handleFormSubmittedByJavascript);

    editor.on('init', () => {
        // Setup the Undo handler.
        editor.on('AddUndo', undoHandler);

        if (editor.dom.isEmpty(editor.getBody())) {
            Log.info(`Attempting to restore draft`, getLogSource(editor));
            Storage.restoreDraft(editor);
        } else {
            // There was nothing to restore, so we can mark the editor as initialised.
            Log.warn(`Skipping draft restoration. The editor is not empty.`, getLogSource(editor));
            Options.markInitialised(editor);
        }
    });
};
