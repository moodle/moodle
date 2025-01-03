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
 * Controller for handling the show/hide prompt button and the associated textarea.
 *
 * @module      tiny_ai/controllers/promtedit_controller
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getStrings} from 'core/str';
import {getDatamanager, getCurrentModalUniqId} from 'tiny_ai/utils';

export default class {

    constructor(baseSelector) {
        this.baseElement = document.querySelector(baseSelector);
    }

    async init() {
        const showPromptButton = this.baseElement.querySelector('[data-action="showprompt"]');
        const textarea = this.baseElement.querySelector('textarea[data-type="prompt"]');

        const datamanager = getDatamanager(getCurrentModalUniqId(this.baseElement));
        textarea.innerHTML = datamanager.getCurrentPrompt();
        datamanager.getEventEmitterElement().addEventListener('promptUpdated', (event) => {
            textarea.value = event.detail.newPrompt;
        });
        textarea.addEventListener('input', () => {
            datamanager.setCurrentPrompt(textarea.value);
        });

        if (showPromptButton) {
            const [showPromptString, hidePromptString] = await getStrings(
                [
                    {key: 'showprompt', component: 'tiny_ai'},
                    {key: 'hideprompt', component: 'tiny_ai'}
                ]
            );
            showPromptButton.addEventListener('click', () => {
                const currentText = showPromptButton.querySelector('[data-text]').innerText;
                showPromptButton.querySelector('[data-text]').innerText =
                    currentText === showPromptString ? hidePromptString : showPromptString;
                const buttonIcon = showPromptButton.querySelector('i');
                if (buttonIcon.classList.contains('fa-eye')) {
                    buttonIcon.classList.remove('fa-eye');
                    buttonIcon.classList.add('fa-eye-slash');
                } else {
                    buttonIcon.classList.remove('fa-eye-slash');
                    buttonIcon.classList.add('fa-eye');
                }
                textarea.classList.toggle('d-none');
            });
        }
    }
}
