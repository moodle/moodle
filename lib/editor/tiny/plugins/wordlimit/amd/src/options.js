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
 * Tiny Wordlimit option.
 *
 * @module    tiny_wordlimit/common
 * @copyright 2023 University of Graz
 * @author    André Menrath <andre.menrath@uni-graz.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getPluginOptionName} from 'editor_tiny/options';
import {pluginName} from './common';

const EDITOR_NAME_PATTERN_QUIZ = /q([0-9]+).([0-9]+)_answer/;
const MOD_QUIZ_ATTEMPT_PATH = '/mod/quiz/';

// Helper variable for the word limits option.
const wordLimitsName = getPluginOptionName(pluginName, 'wordLimits');

/**
 * Options registration function.
 *
 * @param {tinyMCE} editor
 */
export const register = (editor) => {
    const registerOption = editor.options.register;

    registerOption(wordLimitsName, {
        processor: 'object',
    });
};

/**
 * Fetch the Wordlimits for this page.
 *
 * @param   {tinyMCE} editor The editor instance.
 * @returns {object}  The values of the wordlimits.
 */
const getWordLimits = (editor) => editor.options.get(wordLimitsName);

/**
 * Fetch the maximum word limit for this editor instance.
 *
 * In a quiz there might be multiple slots/questions: in the backend during the
 * editor setup the current slot is not available in the scope, so we have to
 * extract the current questions wordlimit here.
 *
 *
 * @param   {tinyMCE} editor The editor instance to fetch the value for.
 * @returns {int}     The value of the maximum word limit.
 */
export const getWordLimit = (editor) => {
    const wordLimits = getWordLimits(editor);
    if (wordLimits) {
        if (isQuizQuestionEditor(editor)) {
            // In a quiz, we have all set wordlimits for (multiple) questions/slots
            // passed as an editor option.
            const [, , slot] = EDITOR_NAME_PATTERN_QUIZ.exec(editor.targetElm.name);
            const wordlimit = wordLimits[slot];
            return wordlimit;
        }
        // Only one wordlimit per page (online submission within assignment).
        return wordLimits[1];
    }
    // No wordlimit present for this editor instance.
    return null;
};

/**
 * Checks if the current editor is a essay question of a quiz.
 *
 * @param   {tinyMCE} editor The editor instance to fetch the value for.
 * @returns {boolean}
 */
const isQuizQuestionEditor = (editor) => {
    if (editor.documentBaseURI.path === MOD_QUIZ_ATTEMPT_PATH &&
        EDITOR_NAME_PATTERN_QUIZ.test(editor.targetElm.name)) {
        return true;
    }
    return false;
};
