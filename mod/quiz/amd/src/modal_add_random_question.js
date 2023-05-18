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
 * Contain the logic for the add random question modal.
 *
 * @module     mod_quiz/modal_add_random_question
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Modal from 'core/modal';
import * as Notification from 'core/notification';
import * as Fragment from 'core/fragment';
import * as Templates from 'core/templates';
import * as FormChangeChecker from 'core_form/changechecker';

const SELECTORS = {
    EXISTING_CATEGORY_CONTAINER: '[data-region="existing-category-container"]',
    EXISTING_CATEGORY_FORM_ELEMENT: '#id_existingcategoryheader',
    NEW_CATEGORY_CONTAINER: '[data-region="new-category-container"]',
    NEW_CATEGORY_FORM_ELEMENT: '#id_newcategoryheader',
    TAB_CONTENT: '[data-region="tab-content"]',
    ADD_ON_PAGE_FORM_ELEMENT: '[name="addonpage"]',
    SUBMIT_BUTTON_ELEMENT: 'input[type="submit"]',
    CANCEL_BUTTON_ELEMENT: 'input[type="submit"][name="cancel"]',
    FORM_HEADER: 'legend',
    BUTTON_CONTAINER: '.fitem',
};

export default class ModalAddRandomQuestion extends Modal {
    static TYPE = 'mod_quiz-quiz-add-random-question';
    static TEMPLATE = 'mod_quiz/modal_add_random_question';

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    constructor(root) {
        super(root);
        this.contextId = null;
        this.addOnPageId = null;
        this.category = null;
        this.returnUrl = null;
        this.cmid = null;
        this.loadedForm = false;
    }

    /**
     * Save the Moodle context id that the question bank is being
     * rendered in.
     *
     * @method setContextId
     * @param {int} id
     */
    setContextId(id) {
        this.contextId = id;
    }

    /**
     * Retrieve the saved Moodle context id.
     *
     * @method getContextId
     * @return {int}
     */
    getContextId() {
        return this.contextId;
    }

    /**
     * Set the id of the page that the question should be added to
     * when the user clicks the add to quiz link.
     *
     * @method setAddOnPageId
     * @param {int} id
     */
    setAddOnPageId(id) {
        this.addOnPageId = id;
        this.getBody().find(SELECTORS.ADD_ON_PAGE_FORM_ELEMENT).val(id);
    }

    /**
     * Returns the saved page id for the question to be added to.
     *
     * @method getAddOnPageId
     * @return {int}
     */
    getAddOnPageId() {
        return this.addOnPageId;
    }

    /**
     * Set the category for this form. The category is a comma separated
     * category id and category context id.
     *
     * @method setCategory
     * @param {string} category
     */
    setCategory(category) {
        this.category = category;
    }

    /**
     * Returns the saved category.
     *
     * @method getCategory
     * @return {string}
     */
    getCategory() {
        return this.category;
    }

    /**
     * Set the return URL for the form.
     *
     * @method setReturnUrl
     * @param {string} url
     */
    setReturnUrl(url) {
        this.returnUrl = url;
    }

    /**
     * Returns the return URL for the form.
     *
     * @method getReturnUrl
     * @return {string}
     */
    getReturnUrl() {
        return this.returnUrl;
    }

    /**
     * Set the course module id for the form.
     *
     * @method setCMID
     * @param {int} id
     */
    setCMID(id) {
        this.cmid = id;
    }

    /**
     * Returns the course module id for the form.
     *
     * @method getCMID
     * @return {int}
     */
    getCMID() {
        return this.cmid;
    }

    /**
     * Moves a given form element inside (a child of) a given tab element.
     *
     * Hides the 'legend' (e.g. header) element of the form element because the
     * tab has the name.
     *
     * Moves the submit button into a footer element at the bottom of the form
     * element for styling purposes.
     *
     * @method moveFormElementIntoTab
     * @param  {jquery} formElement The form element to move into the tab.
     * @param  {jquey} tabElement The tab element for the form element to move into.
     */
    moveFormElementIntoTab(formElement, tabElement) {
        const submitButtons = formElement.find(SELECTORS.SUBMIT_BUTTON_ELEMENT);
        const footer = $('<div class="modal-footer mt-1" data-region="footer"></div>');
        // Hide the header because the tabs show us which part of the form we're
        // looking at.
        formElement.find(SELECTORS.FORM_HEADER).addClass('hidden');
        // Move the element inside a tab.
        formElement.wrap(tabElement);
        // Remove the buttons container element.
        submitButtons.closest(SELECTORS.BUTTON_CONTAINER).remove();
        // Put the button inside a footer.
        submitButtons.appendTo(footer);
        // Add the footer to the end of the category form element.
        footer.appendTo(formElement);
    }

    /**
     * Empty the tab content container and move all tabs from the form into the
     * tab container element.
     *
     * @method moveTabsIntoTabContent
     * @param  {jquery} form The form element.
     */
    moveTabsIntoTabContent(form) {
        // Empty it to remove the loading icon.
        const tabContent = this.getBody().find(SELECTORS.TAB_CONTENT).empty();
        // Make sure all tabs are inside the tab content element.
        form.find('[role="tabpanel"]').wrapAll(tabContent);
    }

    /**
     * Make sure all of the tabs have a cancel button in their fotter to sit along
     * side the submit button.
     *
     * @method moveCancelButtonToTabs
     * @param  {jquey} form The form element.
     */
    moveCancelButtonToTabs(form) {
        const cancelButton = form.find(SELECTORS.CANCEL_BUTTON_ELEMENT).addClass('ml-1');
        const tabFooters = form.find('[data-region="footer"]');
        // Remove the buttons container element.
        cancelButton.closest(SELECTORS.BUTTON_CONTAINER).remove();
        cancelButton.clone().appendTo(tabFooters);
    }

    /**
     * Load the add random question form in a fragement and perform some transformation
     * on the HTML to convert it into tabs for rendering in the modal.
     *
     * @method loadForm
     * @return {promise} Resolved with form HTML and JS.
     */
    loadForm() {
        return Fragment.loadFragment(
            'mod_quiz',
            'add_random_question_form',
            this.getContextId(),
            {
                addonpage: this.getAddOnPageId(),
                cat: this.getCategory(),
                returnurl: this.getReturnUrl(),
                cmid: this.getCMID()
            }
        )
        .then((html, js) =>{
            const form = $(html);
            const existingCategoryFormElement = form.find(SELECTORS.EXISTING_CATEGORY_FORM_ELEMENT);
            const existingCategoryTab = this.getBody().find(SELECTORS.EXISTING_CATEGORY_CONTAINER);
            const newCategoryFormElement = form.find(SELECTORS.NEW_CATEGORY_FORM_ELEMENT);
            const newCategoryTab = this.getBody().find(SELECTORS.NEW_CATEGORY_CONTAINER);

            // Transform the form into tabs for better rendering in the modal.
            this.moveFormElementIntoTab(existingCategoryFormElement, existingCategoryTab);
            this.moveFormElementIntoTab(newCategoryFormElement, newCategoryTab);
            this.moveTabsIntoTabContent(form);
            this.moveCancelButtonToTabs(form);

            Templates.replaceNode(this.getBody().find(SELECTORS.TAB_CONTENT), form, js);
            return;
        })
        .then(() => {
            // Make sure the form change checker is disabled otherwise it'll stop the user from navigating away from the
            // page once the modal is hidden.
            FormChangeChecker.disableAllChecks();
            return;
        })
        .catch(Notification.exception);
    }

    /**
     * Override the modal show function to load the form when this modal is first
     * shown.
     *
     * @method show
     */
    show() {
        super.show(this);

        if (!this.loadedForm) {
            this.loadForm();
            this.loadedForm = true;
        }
    }
}

ModalAddRandomQuestion.registerModalType();
