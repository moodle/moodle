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
 * @module      tiny_autosave/storage
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Repository from "./repository";
import Pending from 'core/pending';
import {
    markInitialised,
    getBackoffTime,
} from "./options";
import Log from 'core/log';
import {getLogSource} from './common';

/** @property {Map} A map of debounced draft saves */
const saveDebounceMap = new Map();

/**
 * Attempt to restore a draft into the editor
 *
 * @param {TinyMCE} editor The Editor to restore a draft for
 */
export const restoreDraft = async(editor) => {
    const pendingPromise = new Pending('tiny_autosave/restoreDraft');
    try {
        const session = await Repository.resumeAutosaveSession(editor);
        if (session && session.drafttext) {
            editor.undoManager.ignore(() => {
                editor.setContent(session.drafttext);
                editor.save();
            });
        }
    } catch (error) {
        // Ignore any errors as drafts are optional.
        Log.warn(`Failed to restore draft: ${error}`, getLogSource(editor));
    }
    markInitialised(editor);
    pendingPromise.resolve();
};

/**
 * Save the current content of the editor as a draft.
 *
 * @param {TinyMCE} editor
 */
export const saveDraft = (editor) => {
    const timerId = saveDebounceMap.get(editor);
    if (timerId) {
        clearTimeout(timerId);
    }
    saveDebounceMap.set(editor, setTimeout(() => {
        Log.debug(`Saving draft`, getLogSource(editor));
        Repository.updateAutosaveSession(editor)
        .catch((error) => window.console.warn(error));
    }, getBackoffTime(editor)));
};

/**
 * Delete the draft for the current editor.
 *
 * @param {TinyMCE} editor
 */
export const removeAutosaveSession = (editor) => {
    Log.debug(`Removing Autosave session`, getLogSource(editor));
    Repository.removeAutosaveSession(editor);
};
