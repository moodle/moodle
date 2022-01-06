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
 * Contain the logic for the question bank modal.
 *
 * @module     mod_quiz/modal_quiz_question_bank
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/yui',
    'core/notification',
    'core/modal',
    'core/modal_events',
    'core/modal_registry',
    'core/fragment'
],
function(
    $,
    Y,
    Notification,
    Modal,
    ModalEvents,
    ModalRegistry,
    Fragment
) {

    var registered = false;
    var SELECTORS = {
        ADD_TO_QUIZ_CONTAINER: 'td.addtoquizaction',
        ANCHOR: 'a[href]',
        PREVIEW_CONTAINER: 'td.previewaction',
        SEARCH_OPTIONS: '#advancedsearch',
        DISPLAY_OPTIONS: '#displayoptions',
        ADD_QUESTIONS_FORM: 'form[action="edit.php"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalQuizQuestionBank = function(root) {
        Modal.call(this, root);

        this.contextId = null;
        this.addOnPageId = null;
    };

    ModalQuizQuestionBank.TYPE = 'mod_quiz-quiz-question-bank';
    ModalQuizQuestionBank.prototype = Object.create(Modal.prototype);
    ModalQuizQuestionBank.prototype.constructor = ModalQuizQuestionBank;

    /**
     * Save the Moodle context id that the question bank is being
     * rendered in.
     *
     * @method setContextId
     * @param {int} id
     */
    ModalQuizQuestionBank.prototype.setContextId = function(id) {
        this.contextId = id;
    };

    /**
     * Retrieve the saved Moodle context id.
     *
     * @method getContextId
     * @return {int}
     */
    ModalQuizQuestionBank.prototype.getContextId = function() {
        return this.contextId;
    };

    /**
     * Set the id of the page that the question should be added to
     * when the user clicks the add to quiz link.
     *
     * @method setAddOnPageId
     * @param {int} id
     */
    ModalQuizQuestionBank.prototype.setAddOnPageId = function(id) {
        this.addOnPageId = id;
    };

    /**
     * Returns the saved page id for the question to be added it.
     *
     * @method getAddOnPageId
     * @return {int}
     */
    ModalQuizQuestionBank.prototype.getAddOnPageId = function() {
        return this.addOnPageId;
    };

    /**
     * Override the parent show function.
     *
     * Reload the body contents when the modal is shown. The current
     * window URL is used to inform the new content that should be
     * displayed.
     *
     * @method show
     * @return {void}
     */
    ModalQuizQuestionBank.prototype.show = function() {
        this.reloadBodyContent(window.location.search);
        return Modal.prototype.show.call(this);
    };

    /**
     * Replaces the current body contents with a new version of the question
     * bank.
     *
     * The contents of the question bank are generated using the provided
     * query string.
     *
     * @method reloadBodyContent
     * @param {string} queryString URL encoded string.
     */
    ModalQuizQuestionBank.prototype.reloadBodyContent = function(queryString) {
        // Load the question bank fragment to be displayed in the modal.
        var promise = Fragment.loadFragment(
            'mod_quiz',
            'quiz_question_bank',
            this.getContextId(),
            {
                querystring: queryString
            }
        ).fail(Notification.exception);

        this.setBody(promise);
    };

    /**
     * Update the URL of the anchor element that the user clicked on to make
     * sure that the question is added to the correct page.
     *
     * @method handleAddToQuizEvent
     * @param {event} e A JavaScript event
     * @param {object} anchorElement The anchor element that was triggered
     */
    ModalQuizQuestionBank.prototype.handleAddToQuizEvent = function(e, anchorElement) {
        // If the user clicks the plus icon to add the question to the page
        // directly then we need to intercept the click in order to adjust the
        // href and include the correct add on page id before the page is
        // redirected.
        var href = anchorElement.attr('href') + '&addonpage=' + this.getAddOnPageId();
        anchorElement.attr('href', href);
    };

    /**
     * Open a popup window to show the preview of the question.
     *
     * @method handlePreviewContainerEvent
     * @param {event} e A JavaScript event
     * @param {object} anchorElement The anchor element that was triggered
     */
    ModalQuizQuestionBank.prototype.handlePreviewContainerEvent = function(e, anchorElement) {
        var popupOptions = [
            'height=600',
            'width=800',
            'top=0',
            'left=0',
            'menubar=0',
            'location=0',
            'scrollbars',
            'resizable',
            'toolbar',
            'status',
            'directories=0',
            'fullscreen=0',
            'dependent'
        ];
        window.openpopup(e, {
            url: anchorElement.attr('href'),
            name: 'questionpreview',
            options: popupOptions.join(',')
        });
    };

    /**
     * Reload the modal body with the new display options the user has selected.
     *
     * A query string is built using the form elements to be used to generate the
     * new body content.
     *
     * @method handleDisplayOptionFormEvent
     * @param {event} e A JavaScript event
     */
    ModalQuizQuestionBank.prototype.handleDisplayOptionFormEvent = function(e) {
        // Stop propagation to prevent other wild event handlers
        // from submitting the form on change.
        e.stopPropagation();
        e.preventDefault();

        var form = $(e.target).closest(SELECTORS.DISPLAY_OPTIONS);
        var queryString = '?' + form.serialize();
        this.reloadBodyContent(queryString);
    };

    /**
     * Listen for changes to the display options form.
     *
     * This handles the user changing:
     *      - The quiz category select box
     *      - The tags to filter by
     *      - Show/hide questions from sub categories
     *      - Show/hide old questions
     *
     * @method registerDisplayOptionListeners
     */
    ModalQuizQuestionBank.prototype.registerDisplayOptionListeners = function() {
        // Listen for changes to the display options form.
        this.getModal().on('change', SELECTORS.DISPLAY_OPTIONS, function(e) {
            // Get the element that was changed.
            var modifiedElement = $(e.target);
            if (modifiedElement.attr('aria-autocomplete')) {
                // If the element that was change is the autocomplete
                // input then we should ignore it because that is for
                // display purposes only.
                return;
            }

            this.handleDisplayOptionFormEvent(e);
        }.bind(this));

        // Listen for the display options form submission because the tags
        // filter will submit the form when it is changed.
        this.getModal().on('submit', SELECTORS.DISPLAY_OPTIONS, function(e) {
            this.handleDisplayOptionFormEvent(e);
        }.bind(this));
    };

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    ModalQuizQuestionBank.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        // Set up the event handlers for all of the display options.
        this.registerDisplayOptionListeners();

        this.getModal().on('submit', SELECTORS.ADD_QUESTIONS_FORM, function(e) {
            // If the user clicks on the "Add selected questions to the quiz" button to add some questions to the page
            // then we need to intercept the submit in order to include the correct "add on page id" before the form is
            // submitted.
            var formElement = $(e.currentTarget);

            $('<input />').attr('type', 'hidden')
                .attr('name', "addonpage")
                .attr('value', this.getAddOnPageId())
                .appendTo(formElement);
        }.bind(this));

        this.getModal().on('click', SELECTORS.ANCHOR, function(e) {
            var anchorElement = $(e.currentTarget);

            // If the anchor element was the add to quiz link.
            if (anchorElement.closest(SELECTORS.ADD_TO_QUIZ_CONTAINER).length) {
                this.handleAddToQuizEvent(e, anchorElement);
                return;
            }

            // If the anchor element was a preview question link.
            if (anchorElement.closest(SELECTORS.PREVIEW_CONTAINER).length) {
                this.handlePreviewContainerEvent(e, anchorElement);
                return;
            }

            // Click on expand/collaspse search-options. Has its own handler.
            // We should not interfere.
            if (anchorElement.closest(SELECTORS.SEARCH_OPTIONS).length) {
                return;
            }

            // Anything else means reload the pop-up contents.
            e.preventDefault();
            this.reloadBodyContent(anchorElement.prop('search'));
        }.bind(this));

        // Disable the form change checker when the body is rendered.
        this.getRoot().on(ModalEvents.bodyRendered, function() {
            // Make sure the form change checker is disabled otherwise it'll
            // stop the user from navigating away from the page once the modal
            // is hidden.
            Y.use('moodle-core-formchangechecker', function() {
                M.core_formchangechecker.reset_form_dirty_state();
            });
        });
    };

    // Automatically register with the modal registry the first time this module is
    // imported so that you can create modals of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(
            ModalQuizQuestionBank.TYPE,
            ModalQuizQuestionBank,
            'core/modal'
        );

        registered = true;
    }

    return ModalQuizQuestionBank;
});
