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
    function ($, Notification, ModalFactory, ModalEvents) {

        var attemptId = null;
        var verified = false;
        var originalSubmitHandler = null;

        /**
         * Show password verification modal
         */
        var showPasswordModal = function () {
            return ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: M.util.get_string('verifyyouridentity', 'local_quiz_password_verify'),
                body: '<div class="form-group">' +
                    '<label for="verify-password">' + M.util.get_string('enteryourpassword', 'local_quiz_password_verify') + '</label>' +
                    '<input type="password" class="form-control" id="verify-password" autocomplete="current-password" required>' +
                    '<small class="form-text text-muted">' + M.util.get_string('passwordhelp', 'local_quiz_password_verify') + '</small>' +
                    '</div>',
            }).then(function (modal) {
                modal.setSaveButtonText(M.util.get_string('verify', 'local_quiz_password_verify'));

                modal.getRoot().on(ModalEvents.save, function (e) {
                    e.preventDefault();
                    var password = $('#verify-password').val();

                    if (!password) {
                        Notification.addNotification({
                            message: M.util.get_string('passwordrequired', 'local_quiz_password_verify'),
                            type: 'error'
                        });
                        return;
                    }

                    verifyPassword(password, modal);
                });

                modal.show();

                // Focus password field when modal opens
                modal.getRoot().on(ModalEvents.shown, function () {
                    $('#verify-password').focus();
                });

                return modal;
            });
        };

        /**
         * Verify password via AJAX
         */
        var verifyPassword = function (password, modal) {
            $.ajax({
                url: M.cfg.wwwroot + '/local/quiz_password_verify/verify.php',
                type: 'POST',
                data: {
                    attemptid: attemptId,
                    password: password,
                    sesskey: M.cfg.sesskey
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        verified = true;
                        modal.hide();
                        modal.destroy();

                        Notification.addNotification({
                            message: response.message,
                            type: 'success'
                        });

                        // Now submit the quiz
                        if (originalSubmitHandler) {
                            originalSubmitHandler();
                        }
                    } else {
                        Notification.addNotification({
                            message: response.message,
                            type: 'error'
                        });
                        $('#verify-password').val('').focus();
                    }
                },
                error: function (xhr, status, error) {
                    Notification.addNotification({
                        message: 'Error verifying password: ' + error,
                        type: 'error'
                    });
                    $('#verify-password').val('').focus();
                }
            });
        };

        /**
         * Initialize the password verification
         */
        var init = function (quizAttemptId) {
            attemptId = quizAttemptId;

            // Find the quiz submit button(s)
            var submitButtons = $('input[name="finishattempt"], button[name="finishattempt"]');

            if (submitButtons.length === 0) {
                return;
            }

            // Intercept the form submission
            var quizForm = submitButtons.closest('form');

            quizForm.on('submit', function (e) {
                if (!verified) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    // Store the original submit action
                    originalSubmitHandler = function () {
                        quizForm.off('submit');
                        quizForm.submit();
                    };

                    showPasswordModal();
                    return false;
                }
            });
        };

        return {
            init: init
        };
    });
