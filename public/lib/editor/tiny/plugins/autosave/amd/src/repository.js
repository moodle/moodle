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
 * Repository helper for the Moodle Tiny Autosave plugin.
 *
 * @module      tiny_autosave/repository
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call} from 'core/ajax';
import * as config from 'core/config';
import * as Options from './options';
import Pending from 'core/pending';
import {ensureEditorIsValid} from 'editor_tiny/utils';

const fetchOne = (methodname, args) => call([{
    methodname,
    args,
}])[0];

/**
 * Resume an Autosave session.
 *
 * @param {TinyMCE} editor The TinyMCE editor instance
 * @returns {Promise<AutosaveSession>} The Autosave session
 */
export const resumeAutosaveSession = (editor) => {
    if (!ensureEditorIsValid(editor)) {
        return Promise.reject('Invalid editor');
    }
    const pendingPromise = new Pending('tiny_autosave/repository:resumeAutosaveSession');
    return fetchOne('tiny_autosave_resume_session', {
        contextid: Options.getContextId(editor),
        pagehash: Options.getPageHash(editor),
        pageinstance: Options.getPageInstance(editor),
        elementid: editor.targetElm.id,
        draftid: Options.getDraftItemId(editor),
    })
    .then((result) => {
        pendingPromise.resolve();
        return result;
    });
};

/**
 * Update the content of the Autosave session.
 *
 * @param {TinyMCE} editor The TinyMCE editor instance
 * @returns {Promise<AutosaveSession>} The Autosave session
 */
export const updateAutosaveSession = (editor) => {
    if (!ensureEditorIsValid(editor)) {
        return Promise.reject('Invalid editor');
    }
    if (Options.hasAutosaveHasReset(editor)) {
        return Promise.reject('Skipping store of autosave content - content has been reset');
    }

    const pendingPromise = new Pending('tiny_autosave/repository:updateAutosaveSession');

    return fetchOne('tiny_autosave_update_session', {
        contextid: Options.getContextId(editor),
        pagehash: Options.getPageHash(editor),
        pageinstance: Options.getPageInstance(editor),
        elementid: editor.targetElm.id,
        drafttext: editor.getContent(),
    })
    .then((result) => {
        pendingPromise.resolve();
        return result;
    });
};

/**
 * Remove the Autosave session.
 *
 * @param {TinyMCE} editor The TinyMCE editor instance
 */
export const removeAutosaveSession = (editor) => {
    if (!ensureEditorIsValid(editor)) {
        throw new Error('Invalid editor');
    }
    Options.setAutosaveHasReset(editor);

    // Please note that we must use a Beacon send here.
    // The XHR is not guaranteed because it will be aborted on page transition.
    // https://developer.mozilla.org/en-US/docs/Web/API/Beacon_API
    // Note: Moodle does not currently have a sendBeacon API endpoint.
    const requestUrl = new URL(`${config.wwwroot}/lib/ajax/service.php`);
    requestUrl.searchParams.set('sesskey', config.sesskey);

    const args = {
        contextid: Options.getContextId(editor),
        pagehash: Options.getPageHash(editor),
        pageinstance: Options.getPageInstance(editor),
        elementid: editor.targetElm.id,
    };
    navigator.sendBeacon(requestUrl, JSON.stringify([{
        index: 0,
        methodname: 'tiny_autosave_reset_session',
        args,
    }]));
};
