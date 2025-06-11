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
 * Setup functionality for the Wordlimit plugin for the Moodle TinyMCE 6 editor.
 * If a word limit exists, a hook is registered in the editor to check if the word limit
 * has been exceeded frequently if the editor content gets updated.
 *
 * @module    tiny_wordlimit/setup
 * @copyright 2023 University of Graz
 * @author    André Menrath <andre.menrath@uni-graz.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {addWordLimitElement, updateWordCountAlert} from 'tiny_wordlimit/ui';
import {getWordLimit} from './options';

const UPDATE_WORDCOUNT_STATUS_DELAY = 500;

export const setupWordLimit = (editor) => {
    // The editor options are available at the moment the construction of the editor begins.
    const wordLimit = getWordLimit(editor);
    if (wordLimit) {
        registerWordLimitEvents(editor, wordLimit);
    }
};

const registerWordLimitEvents = (editor, wordLimit) => {
    // Register a throttled hook that checks if the word limit has been exceeded and visually indicates if it has.
    const updateWordCountAlertWithDelay = throttleWithCancel(
        () => updateWordCountAlert(editor, wordLimit),
        UPDATE_WORDCOUNT_STATUS_DELAY
    );

    const tinymceUtilDelay = window.tinymce.util.Tools.resolve('tinymce.util.Delay');

    editor.on('init', () => {
        addWordLimitElement(editor, wordLimit);
        tinymceUtilDelay.setEditorTimeout(
            editor, () => {
                editor.on('SetContent BeforeAddUndo Undo Redo ViewUpdate keyup', updateWordCountAlertWithDelay.throttle);
            }, 0);
        editor.on('remove', updateWordCountAlertWithDelay.cancel);
        updateWordCountAlert(editor, wordLimit);
    });
};

const throttleWithCancel = (callback, rate) => {
    let timer = null;

    const cancel = () => {
      if (timer) {
        clearTimeout(timer);
        timer = null;
      }
    };

    const throttle = (...args) => {
      if (!timer) {
        timer = setTimeout(() => {
          timer = null;
          callback(...args);
        }, rate);
      }
    };

    return {
      cancel,
      throttle
    };
};
