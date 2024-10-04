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
 * A javascript module to handle question tags editing.
 *
 * @module     qbank_tagquestion/edit_tags
 * @copyright  2018 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
            'jquery',
            'core/fragment',
            'core/str',
            'core/modal_events',
            'core/modal_save_cancel',
            'core/notification',
            'core/custom_interaction_events',
            'qbank_tagquestion/repository',
            'qbank_tagquestion/selectors',
        ],
        function(
            $,
            Fragment,
            Str,
            ModalEvents,
            ModalSaveCancel,
            Notification,
            CustomEvents,
            Repository,
            QuestionSelectors
        ) {

    /**
     * Enable the save button in the footer.
     *
     * @param {object} root The container element.
     * @method enableSaveButton
     */
    var enableSaveButton = function(root) {
        root.find(QuestionSelectors.actions.save).prop('disabled', false);
    };

    /**
     * Disable the save button in the footer.
     *
     * @param {object} root The container element.
     * @method disableSaveButton
     */
    var disableSaveButton = function(root) {
        root.find(QuestionSelectors.actions.save).prop('disabled', true);
    };

    /**
     * Get the serialised form data.
     *
     * @method getFormData
     * @param {object} modal The modal object.
     * @return {string} serialised form data
     */
    var getFormData = function(modal) {
        return modal.getBody().find('form').serialize();
    };

    /**
     * Set the element state to loading.
     *
     * @param {object} root The container element
     * @method startLoading
     */
    var startLoading = function(root) {
        var loadingIconContainer = root.find(QuestionSelectors.containers.loadingIcon);

        loadingIconContainer.removeClass('hidden');
    };

    /**
     * Remove the loading state from the element.
     *
     * @param {object} root The container element
     * @method stopLoading
     */
    var stopLoading = function(root) {
        var loadingIconContainer = root.find(QuestionSelectors.containers.loadingIcon);

        loadingIconContainer.addClass('hidden');
    };

    /**
     * Set the context Id data attribute on the modal.
     *
     * @param {Promise} modal The modal promise.
     * @param {int} contextId The context id.
     */
    var setContextId = function(modal, contextId) {
        modal.getBody().attr('data-contextid', contextId);
    };

    /**
     * Get the context Id data attribute value from the modal body.
     *
     * @param {Promise} modal The modal promise.
     * @return {int} The context id.
     */
    var getContextId = function(modal) {
        return modal.getBody().data('contextid');
    };

    /**
     * Set the question Id data attribute on the modal.
     *
     * @param {Promise} modal The modal promise.
     * @param {int} questionId The question Id.
     */
    var setQuestionId = function(modal, questionId) {
        modal.getBody().attr('data-questionid', questionId);
    };

    /**
     * Get the question Id data attribute value from the modal body.
     *
     * @param {Promise} modal The modal promise.
     * @return {int} The question Id.
     */
    var getQuestionId = function(modal) {
        return modal.getBody().data('questionid');
    };

    /**
     * Register event listeners for the module.
     *
     * @param {object} root The calendar root element
     */
    var registerEventListeners = function(root) {
        var modalPromise = ModalSaveCancel.create({
            large: false,
        }).then(function(modal) {
            // All of this code only executes once, when the modal is
            // first created. This allows us to add any code that should
            // only be run once, such as adding event handlers to the modal.
            Str.get_string('questiontags', 'question')
                .then(function(string) {
                    modal.setTitle(string);
                    return string;
                })
                .catch(Notification.exception);

            modal.getRoot().on(ModalEvents.save, function(e) {
                var form = modal.getBody().find('form');
                form.submit();
                e.preventDefault();
            });

            modal.getRoot().on('submit', 'form', function(e) {
                save(modal, root).then(function() {
                    modal.hide();
                    location.reload();
                    return;
                }).catch(Notification.exception);

                // Stop the form from actually submitting and prevent it's
                // propagation because we have already handled the event.
                e.preventDefault();
                e.stopPropagation();
            });

            return modal;
        });

        root.on('click', QuestionSelectors.actions.edittags, function(e) {
            e.preventDefault();
            // eslint-disable-next-line promise/catch-or-return
            modalPromise.then((modal) => modal.show());
        });

        // We need to add an event handler to the tags link because there are
        // multiple links on the page and without adding a listener we don't know
        // which one the user clicked on the show the modal.
        root.on(CustomEvents.events.activate, QuestionSelectors.actions.edittags, function(e) {
            var currentTarget = $(e.currentTarget);

            var questionId = currentTarget.data('questionid'),
                canTag = !!currentTarget.data('cantag'),
                contextId = currentTarget.data('contextid');

            // This code gets called each time the user clicks the tag link
            // so we can use it to reload the contents of the tag modal.
            modalPromise.then(function(modal) {
                // Display spinner and disable save button.
                disableSaveButton(root);
                startLoading(root);

                var args = {
                    id: questionId
                };

                var tagsFragment = Fragment.loadFragment('qbank_tagquestion', 'tags_form', contextId, args);
                modal.setBody(tagsFragment);

                tagsFragment.then(function() {
                        enableSaveButton(root);
                        return;
                    })
                    .always(function() {
                        // Always hide the loading spinner when the request
                        // has completed.
                        stopLoading(root);
                        return;
                    })
                .catch(Notification.exception);

                // Show or hide the save button depending on whether the user
                // has the capability to edit the tags.
                if (canTag) {
                    modal.getRoot().find(QuestionSelectors.actions.save).show();
                } else {
                    modal.getRoot().find(QuestionSelectors.actions.save).hide();
                }

                setQuestionId(modal, questionId);
                setContextId(modal, contextId);

                return modal;
            }).catch(Notification.exception);

            e.preventDefault();
        });
    };

    /**
     * Send the form data to the server to save question tags.
     *
     * @method save
     * @param {object} modal The modal object.
     * @param {object} root The container element.
     * @return {object} A promise
     */
    var save = function(modal, root) {
        // Display spinner and disable save button.
        disableSaveButton(root);
        startLoading(root);

        var formData = getFormData(modal);
        var questionId = getQuestionId(modal);
        var contextId = getContextId(modal);

        // Send the form data to the server for processing.
        return Repository.submitTagCreateUpdateForm(questionId, contextId, formData)
            .always(function() {
                // Regardless of success or error we should always stop
                // the loading icon and re-enable the buttons.
                stopLoading(root);
                enableSaveButton(root);
                return;
            })
            .catch(Notification.exception);
    };

    return {
        init: function(root) {
            root = $(root);
            registerEventListeners(root);
        }
    };
});
