// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * JavaScript for password verification popup
 *
 * @module     local_quiz_password_verify/verify
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/modal_factory', 'core/modal_events'],
    function($, Notification, ModalFactory, ModalEvents) {

        var attemptId = null;
        var verified = false;

        /**
         * Show password verification modal
         *
         * @param {Object} verificationData Data for verification (attemptid or cmid)
         * @param {Function} successCallback Callback to run on success
         * @returns {Promise} Modal promise
         */
        var showPasswordModal = function(verificationData, successCallback) {
            return ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: M.util.get_string('verifyyouridentity', 'local_quiz_password_verify'),
                body: '<div class="form-group">' +
                    '<label for="verify-password">' +
                    M.util.get_string('enteryourpassword', 'local_quiz_password_verify') + '</label>' +
                    '<input type="password" class="form-control" id="verify-password" autocomplete="current-password" required>' +
                    '<small class="form-text text-muted">' +
                    M.util.get_string('passwordhelp', 'local_quiz_password_verify') + '</small>' +
                    '</div>',
            }).then(function(modal) {
                modal.setSaveButtonText(M.util.get_string('verify', 'local_quiz_password_verify'));

                // Add custom class for styling.
                modal.getRoot().addClass('quiz-password-verify-modal');

                modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();
                    var password = $('#verify-password').val();

                    if (!password) {
                        Notification.addNotification({
                            message: M.util.get_string('passwordrequired', 'local_quiz_password_verify'),
                            type: 'error'
                        });
                        return;
                    }

                    verifyPassword(password, modal, verificationData, successCallback);
                });

                modal.show();

                // Focus password field when modal opens.
                modal.getRoot().on(ModalEvents.shown, function() {
                    $('#verify-password').focus();
                });

                return modal;
            });
        };

        /**
         * Verify password via AJAX
         *
         * @param {string} password The password to verify
         * @param {Object} modal The modal instance
         * @param {Object} verificationData The verification data (attemptid or cmid)
         * @param {Function} successCallback The callback to execute on success
         */
        var verifyPassword = function(password, modal, verificationData, successCallback) {
            var data = {
                password: password,
                sesskey: M.cfg.sesskey
            };

            if (verificationData.attemptid) {
                data.attemptid = verificationData.attemptid;
            } else if (verificationData.cmid) {
                data.cmid = verificationData.cmid;
            }

            $.ajax({
                url: M.cfg.wwwroot + '/local/quiz_password_verify/verify.php',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        verified = true;
                        modal.hide();
                        modal.destroy();

                        Notification.addNotification({
                            message: response.message,
                            type: 'success'
                        });

                        if (successCallback) {
                            successCallback();
                        }
                    } else {
                        Notification.addNotification({
                            message: response.message,
                            type: 'error'
                        });
                        $('#verify-password').val('').focus();
                    }
                },
                error: function(xhr, status, error) {
                    Notification.addNotification({
                        message: 'Error verifying password: ' + error,
                        type: 'error'
                    });
                    $('#verify-password').val('').focus();
                }
            });
        };

        // Nuclear Proxy: Intercept HTMLFormElement.prototype.submit.
        var originalProtoSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function() {

            // Intercept if it's the finish attempt form (ID check is most reliable for Summary page)
            // OR if it's processattempt.php on the summary page (fallback).
            var isFinishForm = (this.id === 'frm-finishattempt');
            var isSummaryPage = document.body.classList.contains('path-mod-quiz-summary') ||
                document.body.id === 'page-mod-quiz-summary';
            var isProcessAttempt = this.action && this.action.indexOf('processattempt.php') > -1;

            if (isFinishForm || (isProcessAttempt && isSummaryPage)) {
                var form = this;

                if (verified) {
                    originalProtoSubmit.apply(this, arguments);
                    return;
                }

                var attemptIdField = $(form).find('input[name="attempt"]');
                var currentAttemptId = attemptIdField.val();

                if (!currentAttemptId && attemptId) {
                    currentAttemptId = attemptId;
                }

                if (currentAttemptId) {
                    showPasswordModal({
                        attemptid: currentAttemptId
                    }, function() {
                        originalProtoSubmit.apply(form, arguments);
                    });
                } else {
                    originalProtoSubmit.apply(this, arguments);
                }
            } else {
                originalProtoSubmit.apply(this, arguments);
            }
        };

        /**
         * Initialize the password verification
         *
         * @param {int} quizAttemptId The attempt ID
         */
        var init = function(quizAttemptId) {
            attemptId = quizAttemptId;

            // Capture phase listener for "Mark as done" ONLY.
            window.addEventListener('click', function(e) {
                var target = e.target;

                // Handle "Mark as done".
                var toggleButton = target.closest('[data-action="toggle-manual-completion"]');
                if (toggleButton) {
                    // Check for quiz context (including View page).
                    if (toggleButton.closest('.modtype_quiz') ||
                        toggleButton.closest('.activity.quiz') ||
                        document.body.classList.contains('path-mod-quiz')) {

                        if (verified) {
                            return;
                        }

                        e.preventDefault();
                        e.stopImmediatePropagation();
                        e.stopPropagation();

                        var cmid = toggleButton.dataset.cmid;
                        showPasswordModal({
                            cmid: cmid
                        }, function() {
                            toggleButton.click();
                        });
                        return;
                    }
                }
            }, true); // Capture phase on WINDOW.
        };

        /**
         * Verify action (exposed API)
         *
         * @param {Object} data Data for verification {attemptid: ..., cmid: ...}
         * @param {Function} callback Function to call on success
         */
        var verifyAction = function(data, callback) {
            if (verified) {
                callback();
                return;
            }
            showPasswordModal(data, function() {
                callback();
            });
        };

        return {
            init: init,
            verifyAction: verifyAction
        };
    });
