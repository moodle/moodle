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
import Templates from 'core/templates';
import {
    getContextId,
    getUserId
} from 'tiny_aiplacement/options';
import {getPolicyStatus, setPolicyStatus} from 'core/ai/policy';

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
    displayContentModal = async() => {
        const templateContext = this.getTemplateContext();
        this.modalObject = await this.setupModal();
        await this.modalObject.show(); // Will briefly show the modal with the loading spinner, while the content is being fetched.

        // Check if we need to display and handle the AI acceptance policy.
        const checkPolicy = await getPolicyStatus(this.userid, this.contextid);
        if (!checkPolicy.status) {
            await this.setupPolicyModal(templateContext);
        } else {
            await this.setupContentModal(templateContext);
        }
    };

    /**
     * Add event listeners for the text modal.
     *
     * @returns {void}
     */
    addContentEventListeners = async() => {
        const modalRoot = await this.modalObject.getRoot();
        const root = modalRoot[0];

        root.addEventListener('click', (e) => {
            this.handleContentModalClick(e, root);
        });

        this.setupPromptArea(root);
        this.hideLoadingSpinner(root);
    };

    /**
     * Set up the policy modal with loading spinner and policy content.
     *
     * @param {object} templateContext The template context.
     * @returns {Promise<void>} A promise that resolves when the modal is set up.
     */
    setupPolicyModal = async(templateContext) => {
        const loadingContext = {...templateContext, ishidden: true};
        const [loadingBody, policyBody, policyFooter] = await Promise.all([
            Templates.render('tiny_aiplacement/loading', loadingContext),
            Templates.render('tiny_aiplacement/modalbodypolicy', templateContext),
            Templates.render('tiny_aiplacement/modalfooterpolicy', templateContext)
        ]);

        this.modalObject.setBody(loadingBody + policyBody);
        this.modalObject.setFooter(policyFooter);
        this.modalObject.setTitle(getString('aiusagepolicy', 'core_ai'));
        this.addPolicyEventListeners();
    };

    /**
     * Hide the loading spinner.
     *
     * @param {Object} root The root element of the modal.
     */
    hideLoadingSpinner = (root) => {
        const loadingSpinnerDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_spinner`);
        loadingSpinnerDiv.classList.add('hidden');
        loadingSpinnerDiv.classList.remove('tiny-aiplacement-loading-spinner-container');
    };

    /**
     * Add event listeners for the policy modal.
     *
     * @returns {void}
     */
    addPolicyEventListeners = async() => {
        const modalRoot = await this.modalObject.getRoot();
        const root = modalRoot[0];

        root.addEventListener('click', (e) => {
            this.handlePolicyModalClick(e);
        });
    };


    /**
     * Handle click events within the policy modal.
     *
     * @param {Event} e - The click event object.
     */
    handlePolicyModalClick = (e) => {
        const actions = {
            accept: () => this.handlePolicyAccept(e.target),
            cancel: () => this.modalObject.destroy()
        };

        const actionKey = Object.keys(actions).find(key => e.target.closest(`[data-action="${key}"]`));
        if (actionKey) {
            e.preventDefault();
            actions[actionKey]();
        }
    };

    /**
     * Handle the policy accept action.
     *
     * @param {Object} acceptBtn The submit button element.
     * @returns {void}
     */
    handlePolicyAccept = async(acceptBtn) => {
        await setPolicyStatus(this.userid, this.contextid);

        const modalRoot = await this.modalObject.getRoot();
        const root = modalRoot[0];

        await this.displayLoading(root, acceptBtn, 'tiny-aiplacement-loading-spinner-container');

        const templateContext = this.getTemplateContext();
        await this.setupContentModal(templateContext);
        await this.hideLoading(root, acceptBtn);
    };

    /**
     * Get the context to use in the modal template.
     *
     * @returns {Object}
     */
    getTemplateContext = () => {
        return {elementid: this.editor.id};
    };

    /**
     * Display the loading state in the modal.
     *
     * @param {HTMLElement} root - The root element of the modal.
     * @param {HTMLElement} submitBtn - The submit button element.
     * @param {String|null} removeClass - The class to be removed from the loading spinner div, if any.
     */
    displayLoading = async(root, submitBtn, removeClass = null) => {
        const loadingSpinnerDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_spinner`);
        const overlayDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_overlay`);
        const blurDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_blur`);
        const loadingTextDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_loading_text`);
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
    };

    /**
     * Hide the loading action in the modal.
     *
     * @param {Object} root The root element of the modal.
     * @param {Object} submitBtn The submit button element.
     */
    hideLoading = async(root, submitBtn) => {
        const loadingSpinnerDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_spinner`);
        const overlayDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_overlay`);
        const blurDiv = root.querySelector(`#${this.editor.id}_tiny_aiplacement_blur`);
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
    };

}
