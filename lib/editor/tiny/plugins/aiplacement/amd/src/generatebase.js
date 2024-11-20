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
 * Tiny AI base generate class.
 *
 * @module      tiny_aiplacement/generatebase
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {loadingMessages} from 'tiny_aiplacement/loading';
import {getString} from 'core/str';
import {
    getContextId,
    getUserId,
} from 'tiny_aiplacement/options';
import Policy from 'core_ai/policy';
import PolicyModal from 'core_ai/policymodal';
import CustomEvents from 'core/custom_interaction_events';
import {isPolicyAgreed} from './options';

export default class GenerateBase {
    modalObject;

    /**
     * Class constructor.
     *
     * @param {TinyMCE.editor} editor The tinyMCE editor instance.
     */
    constructor(editor) {
        this.editor = editor;
        this.userid = getUserId(editor);
        this.contextid = getContextId(editor);
        this.responseObj = null;
    }

    /**
     * Display the modal when the AI button is clicked.
     *
     */
    async displayContentModal() {
        Policy.preconfigurePolicyState(this.userid, isPolicyAgreed(this.editor));
        if (!await Policy.getPolicyStatus(this.userid)) {
            const policyModal = await PolicyModal.create();
            policyModal.getModal().on(CustomEvents.events.activate, policyModal.getActionSelector('save'), () => {
                this.displayContentModal();
            });
            return;
        }

        this.modalObject = await this.setupModal();
    }

    getModalClass() {
        throw new Error("Method 'getModalClass' must be implemented.");
    }


    /**
     * Set up the base text generation modal with default body content.
     *
     * @returns {TextModal} The image modal object.
     */
    async setupModal() {
        const modal = this.getModalClass().create({
            templateContext: {
                elementid: this.editor.id,
            },
        });

        this.addContentEventListeners(modal);

        return modal;
    }

    /**
     * Add event listeners for the text modal.
     *
     * @param {Modal} modal
     */
    async addContentEventListeners(modal) {
        const modalRoot = (await modal).getRoot();
        const root = modalRoot[0];

        root.addEventListener('click', (e) => {
            this.handleContentModalClick(e, root);
        });

        this.setupPromptArea(root);
        this.hideLoadingSpinner(root);
    }

    handleContentModalClick() {
        throw new Error('Method handleContentModalClick must be implemented.');
    }

    /**
     * Hide the loading spinner.
     *
     * @param {Object} root The root element of the modal.
     */
    hideLoadingSpinner(root) {
        const loadingSpinnerDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_spinner"]`);
        loadingSpinnerDiv.classList.add('hidden');
        loadingSpinnerDiv.classList.remove('tiny-aiplacement-loading-spinner-container');
    }

    /**
     * Display the loading state in the modal.
     *
     * @param {HTMLElement} root - The root element of the modal.
     * @param {HTMLElement} submitBtn - The submit button element.
     * @param {String|null} removeClass - The class to be removed from the loading spinner div, if any.
     */
    async displayLoading(root, submitBtn, removeClass = null) {
        const loadingSpinnerDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_spinner"]`);
        const overlayDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_overlay"]`);
        const blurDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_blur"]`);
        const loadingTextDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_loading_text"]`);
        const actionButtons = root.querySelectorAll('.tiny-aiplacement-generate-footer button');

        loadingMessages(loadingTextDiv);

        if (removeClass) {
            loadingSpinnerDiv.classList.remove(removeClass);
        }

        loadingSpinnerDiv.classList.remove('hidden');
        overlayDiv.classList.remove('hidden');
        blurDiv.classList.add('tiny-aiplacement-blur');
        submitBtn.innerHTML = await getString('generating', 'tiny_aiplacement');

        if (actionButtons) {
            actionButtons.forEach((button) => {
                button.disabled = true;
            });
        }
    }

    /**
     * Hide the loading action in the modal.
     *
     * @param {Object} root The root element of the modal.
     * @param {Object} submitBtn The submit button element.
     */
    async hideLoading(root, submitBtn) {
        const loadingSpinnerDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_spinner"]`);
        const overlayDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_overlay"]`);
        const blurDiv = root.querySelector(`[id="${this.editor.id}_tiny_aiplacement_blur"]`);
        const actionButtons = root.querySelectorAll('.tiny-aiplacement-generate-footer button');
        if (loadingSpinnerDiv) {
            loadingSpinnerDiv.classList.add('hidden');
        }
        if (overlayDiv) {
            overlayDiv.classList.add('hidden');
        }
        if (blurDiv) {
            blurDiv.classList.remove('tiny-aiplacement-blur');
        }
        submitBtn.innerHTML = await getString('regenerate', 'tiny_aiplacement');

        if (actionButtons) {
            actionButtons.forEach((button) => {
                button.disabled = false;
            });
        }
    }
}
