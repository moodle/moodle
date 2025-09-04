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
import {getString} from 'core/str';
import * as FocusLock from 'core/local/aria/focuslock';
import {isSmall} from "core/pagehelpers";

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
        this.jumpToElement = document.querySelector(Selectors.ELEMENTS.JUMPTO);
        this.actionElement = document.querySelector(Selectors.ELEMENTS.ACTION);
        this.aiDrawerCloseElement = this.aiDrawerElement.querySelector(Selectors.ELEMENTS.AIDRAWER_CLOSE);
        this.lastAction = '';
        this.responses = new Map();
        this.isDrawerFocusLocked = false;

        this.registerEventListeners();
    }

    /**
     * Register event listeners.
     */
    registerEventListeners() {
        document.addEventListener('click', async(e) => {
            // Display summarise.
            const summariseAction = e.target.closest(Selectors.ACTIONS.SUMMARY);
            if (summariseAction) {
                e.preventDefault();
                this.openAIDrawer();
                this.lastAction = 'summarise_text';
                this.actionElement.focus();
                const isPolicyAccepted = await this.isPolicyAccepted();
                if (!isPolicyAccepted) {
                    // Display policy.
                    this.displayPolicy();
                    return;
                }
                this.displayAction(this.lastAction);
            }
            // Display explain.
            const explainAction = e.target.closest(Selectors.ACTIONS.EXPLAIN);
            if (explainAction) {
                e.preventDefault();
                this.openAIDrawer();
                this.lastAction = 'explain_text';
                this.actionElement.focus();
                const isPolicyAccepted = await this.isPolicyAccepted();
                if (!isPolicyAccepted) {
                    // Display policy.
                    this.displayPolicy();
                    return;
                }
                this.displayAction(this.lastAction);
            }
            // Close AI drawer.
            const closeAiDrawer = e.target.closest(Selectors.ELEMENTS.AIDRAWER_CLOSE);
            if (closeAiDrawer) {
                e.preventDefault();
                this.closeAIDrawer();
            }
        });

        document.addEventListener('keydown', e => {
            if (this.isAIDrawerOpen() && e.key === 'Escape') {
                this.closeAIDrawer();
            }
        });

        // Close AI drawer if message drawer is shown.
        subscribe(DrawerEvents.DRAWER_SHOWN, () => {
            if (this.isAIDrawerOpen()) {
                this.closeAIDrawer();
            }
        });

        // Check if there is course assist control region in the page.
        if (this.jumpToElement) {
            // Focus on the AI drawer's close button when the jump-to element is focused.
            this.jumpToElement.addEventListener('focus', () => {
                this.aiDrawerCloseElement.focus();
            });
        }

        // Focus on the action element when the AI drawer container receives focus.
        this.aiDrawerElement.addEventListener('focus', () => {
            this.actionElement.focus();
        });

        // Check if the action element exists.
        if (this.actionElement) {
            // Remove active from the action element when it loses focus.
            this.actionElement.addEventListener('blur', () => {
                this.actionElement.classList.remove('active');
            });
        }
    }

    /**
     * Register event listeners for the policy.
     */
    registerPolicyEventListeners() {
        const acceptAction = document.querySelector(Selectors.ACTIONS.ACCEPT);
        const declineAction = document.querySelector(Selectors.ACTIONS.DECLINE);
        if (acceptAction && this.lastAction.length) {
            acceptAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.acceptPolicy().then(() => {
                    return this.displayAction(this.lastAction);
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
        if (retryAction && this.lastAction.length) {
            retryAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.displayAction(this.lastAction);
            });
        }
    }

    /**
     * Register event listeners for the responses.
     */
    registerResponseEventListeners() {
        // Get all regenerate action buttons (one per response in the AI drawer).
        const regenerateActions = document.querySelectorAll(Selectors.ACTIONS.REGENERATE);
        // Add event listeners for each regenerate action.
        regenerateActions.forEach(regenerateAction => {
            const responseElement = regenerateAction.closest(Selectors.ELEMENTS.RESPONSE);
            if (regenerateAction && responseElement) {
                // Get the action that this response is associated with.
                const actionPerformed = responseElement.getAttribute('data-action-performed');
                regenerateAction.addEventListener('click', (e) => {
                    e.preventDefault();
                    // Remove the old response before displaying the new one.
                    this.removeResponseFromStack(actionPerformed);
                    this.displayAction(actionPerformed);
                });
            }
        });
    }

    registerLoadingEventListeners() {
        const cancelAction = document.querySelector(Selectors.ACTIONS.CANCEL);
        if (cancelAction) {
            cancelAction.addEventListener('click', (e) => {
                e.preventDefault();
                this.setRequestCancelled();
                this.toggleAIDrawer();
                this.removeResponseFromStack('loading');
                // Refresh the response stack to avoid false indication of loading.
                const responses = this.getResponseStack();
                this.aiDrawerBodyElement.innerHTML = responses;
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
        this.aiDrawerElement.setAttribute('tabindex', 0);
        this.aiDrawerBodyElement.setAttribute('aria-live', 'polite');
        if (!this.pageElement.classList.contains('show-drawer-right')) {
            this.addPadding();
        }
        this.jumpToElement.setAttribute('tabindex', 0);
        this.jumpToElement.focus();

        // If the AI drawer is opened on a small screen, we need to trap the focus tab within the AI drawer.
        if (isSmall()) {
            FocusLock.trapFocus(this.aiDrawerElement);
            this.aiDrawerElement.setAttribute('aria-modal', 'true');
            this.aiDrawerElement.setAttribute('role', 'dialog');
            this.isDrawerFocusLocked = true;
        }
    }

    /**
     * Close the AI drawer.
     */
    closeAIDrawer() {
        // Untrap focus if it was locked.
        if (this.isDrawerFocusLocked) {
            FocusLock.untrapFocus();
            this.aiDrawerElement.removeAttribute('aria-modal');
            this.aiDrawerElement.setAttribute('role', 'region');
        }

        this.aiDrawerElement.classList.remove('show');
        this.aiDrawerElement.setAttribute('tabindex', -1);
        this.aiDrawerBodyElement.removeAttribute('aria-live');
        if (this.pageElement.classList.contains('show-drawer-right') && this.aiDrawerBodyElement.dataset.removepadding === '1') {
            this.removePadding();
        }
        this.jumpToElement.setAttribute('tabindex', -1);

        // We can enforce a focus-visible state on the focus element using element.focus({focusVisible: true}).
        // Unfortunately, this feature isn't supported in all browsers, only Firefox provides support for it.
        // Therefore, we will apply the active class to the action element and set focus on it.
        // This action will make the action element appear focused.
        // When the action element loses focus,
        // we will remove the active class at {@see registerEventListeners()}
        this.actionElement.classList.add('active');
        this.actionElement.focus();
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
     * Get important params related to the action.
     * @param {string} action The action to use.
     * @returns {object} The params to use for the action.
     */
    async getParamsForAction(action) {
        let params = {};

        switch (action) {
            case 'summarise_text':
                params.method = 'aiplacement_courseassist_summarise_text';
                params.heading = await getString('aisummary', 'aiplacement_courseassist');
                break;

            case 'explain_text':
                params.method = 'aiplacement_courseassist_explain_text';
                params.heading = await getString('aiexplain', 'aiplacement_courseassist');
                break;
        }

        return params;
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
     * Check if the AI drawer has already generated content for a particular action.
     * @param {string} action The action to check.
     * @return {boolean} True if the AI drawer has generated content, false otherwise.
     */
    hasGeneratedContent(action) {
        return this.responses.has(action);
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
            this.addResponseToStack('loading', html);
            const responses = this.getResponseStack();
            this.aiDrawerBodyElement.innerHTML = responses;
            this.registerLoadingEventListeners();
            return;
        }).then(() => {
            this.removeResponseFromStack('loading');
            return;
        }).catch(Notification.exception);
    }

    /**
     * Display the action result in the AI drawer.
     * @param {string} action The action to display.
     */
    async displayAction(action) {
        if (this.hasGeneratedContent(action)) {
            // Scroll to generated content.
            const existingReponse = document.querySelector('[data-action-performed="' + action + '"]');
            if (existingReponse) {
                this.aiDrawerBodyElement.scrollTop = existingReponse.offsetTop;
            }
        } else {
            // Display loading spinner.
            this.displayLoading();
            // Clear the drawer to prevent including the previously generated response in the new response prompt.
            this.aiDrawerBodyElement.innerHTML = '';
            const params = await this.getParamsForAction(action);
            const request = {
                methodname: params.method,
                args: {
                    contextid: this.contextId,
                    prompttext: this.getTextContent(),
                }
            };
            try {
                const responseObj = await Ajax.call([request])[0];
                if (responseObj.error) {
                    this.displayError(responseObj.error, responseObj.errormessage);
                    return;
                } else {
                    if (!this.isRequestCancelled()) {
                        // Perform replacements on the generated context to ensure it is formatted correctly.
                        const generatedContent = AIHelper.formatResponse(responseObj.generatedcontent);
                        this.displayResponse(generatedContent, action);
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
     * Add the HTML response to the response stack.
     * The stack will be used to display all responses in the AI drawer.
     * @param {String} action The action key.
     * @param {String} html The HTML to store.
     */
    addResponseToStack(action, html) {
        this.responses.set(action, html);
    }

    /**
     * Remove a stored response, allowing for a regenerated one.
     * @param {String} action The action key.
     */
    removeResponseFromStack(action) {
        if (this.responses.has(action)) {
            this.responses.delete(action);
        }
    }

    /**
     * Return a stack of HTML responses.
     * @return {String} HTML responses.
     */
    getResponseStack() {
        let stack = '';
        // Reverse to get newest first.
        const responses = [...this.responses.values()].reverse();
        for (const response of responses) {
            stack += response;
        }
        return stack;
    }

    /**
     * Display the responses.
     * @param {String} content The content to display.
     * @param {String} action The action used.
     */
    async displayResponse(content, action) {
        const params = await this.getParamsForAction(action);
        const args = {
            content: content,
            heading: params.heading,
            action: action,
        };
        Templates.render('aiplacement_courseassist/response', args).then((html) => {
            this.addResponseToStack(action, html);
            const responses = this.getResponseStack();
            this.aiDrawerBodyElement.innerHTML = responses;
            this.registerResponseEventListeners();
            return;
        }).catch(Notification.exception);
    }

    /**
     * Display the error.
     *
     * @param {String} error The error name to display.
     * @param {String} errorMessage The error message to display.
     */
    async displayError(error = '', errorMessage = '') {
        if (!error) {
            // Get the default error message.
            error = await getString('error:defaultname', 'core_ai');
            errorMessage = await getString('error:defaultmessage', 'core_ai');
        }
        Templates.render('aiplacement_courseassist/error', {'error': error, 'errorMessage': errorMessage}).then((html) => {
            this.addResponseToStack('error', html);
            const responses = this.getResponseStack();
            this.aiDrawerBodyElement.innerHTML = responses;
            this.registerErrorEventListeners();
            return;
        }).then(() => {
            this.removeResponseFromStack('error');
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
