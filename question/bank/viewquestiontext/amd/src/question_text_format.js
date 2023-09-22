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
 * Javascript for question_text_format question bank control.
 *
 * @module     qbank_viewquestiontext/question_text_format
 * @copyright  2023 Catalyst IT Europe Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as repository from 'qbank_viewquestiontext/repository';
import RefreshUi from 'core_question/refresh_ui';
import Notification from 'core/notification';

const SELECTORS = {
    formatSelectId: 'question-text-format',
    returnUrl: '[name=returnurl]',
};

let uiRoot;

/**
 * Save the selected format via a web service call, and refresh the UI.
 *
 * @param {Event} e Select field change event.
 * @return {Promise<void>}
 */
const handleFormatChange = async(e) => {
    const value = e.target.value;
    try {
        await repository.setQuestionTextFormat(value);
        const returnUrlInput = e.target.closest('form').querySelector(SELECTORS.returnUrl);
        const returnUrl = new URL(returnUrlInput.value);
        await RefreshUi.refresh(uiRoot, returnUrl);
    } catch (ex) {
        Notification.exception(ex);
    }
};

/**
 * Initialise question text format widget.
 *
 * Find the uiRoot element and attach a change listener to the question text format selector.
 *
 * @param {String} uiRootId
 */
export const init = (uiRootId) => {
    uiRoot = document.getElementById(uiRootId);
    const select = document.getElementById(SELECTORS.formatSelectId);
    select.addEventListener('change', handleFormatChange);
};
