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
 * Module to load and render the tools for the AI assist plugin.
 *
 * @module     aiplacement_courseassist/placement
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Ajax from 'core/ajax';
import 'core/copy_to_clipboard';
import Notification from 'core/notification';
import Selectors from 'aiplacement_courseassist/selectors';
import Policy from 'core_ai/policy';
import AIHelper from 'core_ai/helper';
import DrawerEvents from 'core/drawer_events';
import {subscribe} from 'core/pubsub';
import * as MessageDrawerHelper from 'core_message/message_drawer_helper';

const AICourseAssist = class {

    /**
     * The user ID.
     * @type {Integer}
     */
    userId;
    /**
     * The context ID.
     * @type {Integer}
     */
    contextId;

    /**
     * Constructor.
     * @param {Integer} userId The user ID.
     * @param {Integer} contextId The context ID.
     */
    constructor(userId, contextId) {
        this.userId = userId;
        this.contextId = contextId;

        this.aiDrawerElement = document.querySelector(Selectors.ELEMENTS.AIDRAWER);
        this.aiDrawerBodyElement = document.querySelector(Selectors.ELEMENTS.AIDRAWER_BODY);
        this.pageElement = document.querySelector(Selectors.ELEMENTS.PAGE);

        this.registerEventListeners();
    }

    /**
     * Register event listeners.
     */
    registerEventListeners() {
        document.addEventListener('click', async(e) => {
            const summariseAction = e.target.closest(Selectors.ACTIONS.SUMMARY);
            if (summariseAction) {
                e.preventDefault();
                this.toggleAIDrawer();
                const isPolicyAccepted = await this.isPolicyAccepted();
                if (!isPolicyAccepted) {
                    // Display policy.
                    this.displayPolicy();
                    return;
                }
                // Display summary.
                this.displaySummary();
            }
        });

        // Close AI drawer if message drawer is shown.
        subscribe(DrawerEvents.DRAWER_SHOWN, () => {
            if (this.isAIDrawerOpen()) {
                this.closeAIDrawer();
            }
        });
    }

    /**
     * Register event listeners for the policy.
     */
    registerPolicyEventListeners() {
        const acceptAction = document.querySelector(Selectors.ACTIONS.ACCEPT);
        const declineAction = document.querySelector(Selectors.ACTIONS.DECLINE);
        if (acceptAction) {
            acceptAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.acceptPolicy().then(() => {
                    return this.displaySummary();
                }).catch(Notification.exception);
            });
        }
        if (declineAction) {
            declineAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeAIDrawer();
            });
        }
    }

    /**
     * Register event listeners for the error.
     */
    registerErrorEventListeners() {
        const retryAction = document.querySelector(Selectors.ACTIONS.RETRY);
        if (retryAction) {
            retryAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.aiDrawerBodyElement.dataset.hasdata = '0';
                this.displaySummary();
            });
        }
    }

    /**
     * Register event listeners for the response.
     */
    registerResponseEventListeners() {
        const regenerateAction = document.querySelector(Selectors.ACTIONS.REGENERATE);
        if (regenerateAction) {
            regenerateAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.aiDrawerBodyElement.dataset.hasdata = '0';
                this.displaySummary();
            });
        }
    }

    registerLoadingEventListeners() {
        const cancelAction = document.querySelector(Selectors.ACTIONS.CANCEL);
        if (cancelAction) {
            cancelAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.setRequestCancelled();
                this.toggleAIDrawer();
            });
        }
    }

    /**
     * Check if the AI drawer is open.
     * @return {boolean} True if the AI drawer is open, false otherwise.
     */
    isAIDrawerOpen() {
        return this.aiDrawerElement.classList.contains('show');
    }

    /**
     * Check if the request is cancelled.
     * @return {boolean} True if the request is cancelled, false otherwise.
     */
    isRequestCancelled() {
        return this.aiDrawerBodyElement.dataset.cancelled === '1';
    }

    setRequestCancelled() {
        this.aiDrawerBodyElement.dataset.cancelled = '1';
    }

    /**
     * Open the AI drawer.
     */
    openAIDrawer() {
        // Close message drawer if it is shown.
        MessageDrawerHelper.hide();
        this.aiDrawerElement.classList.add('show');
        this.aiDrawerBodyElement.setAttribute('aria-live', 'polite');
        if (!this.pageElement.classList.contains('show-drawer-right')) {
            this.addPadding();
        }
        // Disable the summary button.
        this.disableSummaryButton();
    }

    /**
     * Close the AI drawer.
     */
    closeAIDrawer() {
        this.aiDrawerElement.classList.remove('show');
        this.aiDrawerBodyElement.removeAttribute('aria-live');
        if (this.pageElement.classList.contains('show-drawer-right') && this.aiDrawerBodyElement.dataset.removepadding === '1') {
            this.removePadding();
        }
        // Enable the summary button.
        this.enableSummaryButton();
    }

    /**
     * Toggle the AI drawer.
     */
    toggleAIDrawer() {
        if (this.isAIDrawerOpen()) {
            this.closeAIDrawer();
        } else {
            this.openAIDrawer();
        }
    }

    /**
     * Add padding to the page to make space for the AI drawer.
     */
    addPadding() {
        this.pageElement.classList.add('show-drawer-right');
        this.aiDrawerBodyElement.dataset.removepadding = '1';
    }

    /**
     * Remove padding from the page.
     */
    removePadding() {
        this.pageElement.classList.remove('show-drawer-right');
        this.aiDrawerBodyElement.dataset.removepadding = '0';
    }

    /**
     * Disable the summary button.
     */
    disableSummaryButton() {
        const summaryButton = document.querySelector(Selectors.ACTIONS.SUMMARY);
        if (summaryButton) {
            summaryButton.setAttribute('disabled', 1);
        }
    }

    /**
     * Enable the summary button and focus on it.
     */
    enableSummaryButton() {
        const summaryButton = document.querySelector(Selectors.ACTIONS.SUMMARY);
        if (summaryButton) {
            summaryButton.removeAttribute('disabled');
            summaryButton.focus();
        }
    }

    /**
     * Check if the policy is accepted.
     * @return {bool} True if the policy is accepted, false otherwise.
     */
    async isPolicyAccepted() {
        return await Policy.getPolicyStatus(this.userId);
    }

    /**
     * Accept the policy.
     * @return {Promise<Object>}
     */
    acceptPolicy() {
        return Policy.acceptPolicy();
    }

    /**
     * Check if the AI drawer has generated content or not.
     * @return {boolean} True if the AI drawer has generated content, false otherwise.
     */
    hasGeneratedContent() {
        return this.aiDrawerBodyElement.dataset.hasdata === '1';
    }

    /**
     * Display the policy.
     */
    displayPolicy() {
        Templates.render('core_ai/policyblock', {}).then((html) => {
            this.aiDrawerBodyElement.innerHTML = html;
            this.registerPolicyEventListeners();
            return;
        }).catch(Notification.exception);
    }

    /**
     * Display the loading spinner.
     */
    displayLoading() {
        Templates.render('aiplacement_courseassist/loading', {}).then((html) => {
            this.aiDrawerBodyElement.innerHTML = html;
            this.registerLoadingEventListeners();
            return;
        }).catch(Notification.exception);
    }

    /**
     * Display the summary.
     */
    async displaySummary() {
        if (!this.hasGeneratedContent()) {
            // Display loading spinner.
            this.displayLoading();
            // Clear the drawer content to prevent sending some unnecessary content.
            this.aiDrawerBodyElement.innerHTML = '';
            const request = {
                methodname: 'aiplacement_courseassist_summarise_text',
                args: {
                    contextid: this.contextId,
                    prompttext: this.getTextContent(),
                }
            };
            try {
                const responseObj = await Ajax.call([request])[0];
                if (responseObj.error) {
                    this.displayError();
                    return;
                } else {
                    if (!this.isRequestCancelled()) {
                        // Replace double line breaks with <br> and with </p><p> for paragraphs.
                        const generatedContent = AIHelper.replaceLineBreaks(responseObj.generatedcontent);
                        this.displayResponse(generatedContent);
                        return;
                    } else {
                        this.aiDrawerBodyElement.dataset.cancelled = '0';
                    }
                }
            } catch (error) {
                window.console.log(error);
                this.displayError();
            }
        }
    }

    /**
     * Display the response.
     * @param {String} content The content to display.
     */
    displayResponse(content) {
        Templates.render('aiplacement_courseassist/response', {content: content}).then((html) => {
            this.aiDrawerBodyElement.innerHTML = html;
            this.aiDrawerBodyElement.dataset.hasdata = '1';
            this.registerResponseEventListeners();
            return;
        }).catch(Notification.exception);
    }

    /**
     * Display the error.
     */
    displayError() {
        Templates.render('aiplacement_courseassist/error', {}).then((html) => {
            this.aiDrawerBodyElement.innerHTML = html;
            this.registerErrorEventListeners();
            return;
        }).catch(Notification.exception);
    }

    /**
     * Get the text content of the main region.
     * @return {String} The text content.
     */
    getTextContent() {
        const mainRegion = document.querySelector(Selectors.ELEMENTS.MAIN_REGION);
        return mainRegion.innerText || mainRegion.textContent;
    }
};

export default AICourseAssist;
