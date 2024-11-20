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

import yaml from 'js-yaml';
import path from 'path';
import { writeFile, mkdir, readdir, readFile, unlink } from 'fs/promises';
import { isValidNoteName, sortNoteTypes } from './noteTypes.mjs';
import { sortComponents } from './components.mjs';

const unreleasedPath = path.resolve('.upgradenotes');

/**
 * Get the filename for the note.
 *
 * @param {string} issueNumnber The issue number
 * @returns {string}
 */
const getFilename = (issueNumber) => {
    const dateTimeFormat = new Intl.DateTimeFormat(undefined, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        fractionalSecondDigits: 2,
        timeZone: 'UTC',
    });

    const date = Object.fromEntries(
        dateTimeFormat.formatToParts(new Date())
            .filter((p) => p.type !== 'literal')
            .map((p) => ([p.type, p.value]))
    );

    const dateString = [
        date.year,
        date.month,
        date.day,
        date.hour,
        date.minute,
        date.second,
        date.fractionalSecond,
    ].join('');

    return `${issueNumber}-${dateString}.yml`;
};

/**
 * Create a new note.
 *
 * @param {string} issueNumber
 * @param {[type: string]: {message: string}} messages
 * @returns {string} The path to the note on disk
 */
export const createNote = async (
    issueNumber,
    messages,
    notePath,
) => {
    const note = {
        issueNumber,
        notes: {},
    };

    messages.forEach(({components, type, message}) => {
        note.notes[components] = note.notes[components] || [];
        note.notes[components].push({message, type});
    });

    if (!notePath) {
        notePath = path.resolve(unreleasedPath, getFilename(issueNumber));
    }
    const noteContent = yaml.dump(note);

    await mkdir(unreleasedPath, {recursive: true});
    await writeFile(notePath, noteContent);

    return notePath;
};

/**
 * Get all unreleased notes.
 *
 * @returns {Promise<{issueNumber: string, components: string[], types: {[type: string]: {message: string}[]}}[]>
 */
export const getAllNotes = async () => {
    const files = await readdir(unreleasedPath);
    const notes = files
        .filter((file) => file.endsWith('.yml'))
        .map(async (file) => {
            const filePath = path.resolve(unreleasedPath, file);
            const fileContent = await readFile(filePath, 'utf8');

            return {
                ...yaml.load(fileContent),
                filePath,
            };
        });

    return Promise.all(notes);
};

/**
 * Get the list of notes, grouped by note type, then component.
 *
 * @returns {Promise<{[type: string]: {[components: string]: {message: string, issueNumber: string}[]}}>}
 */
export const getCombinedNotes = async () => {
    const notes = await getAllNotes();
    const combinedNotes = {};

    notes.forEach((note) => {
        Object.entries(note.notes).forEach(([components, data]) => {
            data.forEach((entry) => {
                if (!isValidNoteName(entry.type)) {
                    throw new Error(`Invalid note type: "${entry.type}" in file ${note.filePath}`);
                }
                combinedNotes[entry.type] = combinedNotes[entry.type] || {};
                combinedNotes[entry.type][components] = combinedNotes[entry.type][components] || [];
                combinedNotes[entry.type][components].push({message: entry.message, issueNumber: note.issueNumber});
            });
        });
    });

    return combinedNotes;
};

/**
 * Get the list of notes, grouped by component, then by note type.
 *
 * @returns {Promise<{[component: string]: {[type: string]: {message: string, issueNumber: string}[]}}>}
 */
export const getCombinedNotesByComponent = async () => {
    const notes = await getAllNotes();
    const combinedNotes = {};

    notes.forEach((note) => {
        Object.entries(note.notes).forEach(([component, data]) => {
            combinedNotes[component] = combinedNotes[component] || {};
            data.forEach((entry) => {
                if (!isValidNoteName(entry.type)) {
                    throw new Error(`Invalid note type: "${entry.type}" in file ${note.filePath}`);
                }
                combinedNotes[component][entry.type] = combinedNotes[component][entry.type] || [];
                combinedNotes[component][entry.type].push({
                    component,
                    message: entry.message,
                    issueNumber: note.issueNumber,
                    type: entry.type,
                });
            });
        });
    });

    // Sort notes by note type.
    Object.entries(combinedNotes).forEach(([component, types]) => {
        combinedNotes[component] = Object.fromEntries(
            Object.entries(types).sort(([a], [b]) => sortNoteTypes(a, b))
        );
    });

    // Sort components.
    return Object.fromEntries(
        Object.entries(combinedNotes).sort(([a], [b]) => sortComponents(a, b))
    );
};

/**
 * Delete all unreleased notes.
 *
 * @returns {Promise<void>}
 */
export const deleteAllNotes = async () => {
    const files = await readdir(unreleasedPath);
    return Promise.all(
        files
            .filter((item, index) => files.indexOf(item) === index)
            .filter((file) => file.endsWith('.yml'))
            .map((file) => unlink(`${unreleasedPath}/${file}`))
    );
};
