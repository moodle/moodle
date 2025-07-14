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
 * Validation for configtext_maxlength.
 *
 * @module     core_form/configtext-maxlength
 * @copyright  2021 The Open University
 */
import {get_string as getString} from 'core/str';
import Templates from 'core/templates';
import Notification from 'core/notification';
import {prefetchStrings, prefetchTemplates} from 'core/prefetch';

let registered = false;

/**
 * Initialisation function.
 */
export const init = () => {
    if (registered) {
        return;
    }
    prefetchStrings('core', [
        'maximumchars',
    ]);

    prefetchTemplates([
        'core_form/setting_validation_failure',
    ]);

    registered = true;

    document.addEventListener('input', e => {
        const maxLengthField = e.target.closest('[data-validation-max-length]');
        if (!maxLengthField) {
            return;
        }

        if (maxLengthField.value.length > maxLengthField.dataset.validationMaxLength) {
            // Disable the form for this field.
            maxLengthField.form.addEventListener('submit', submissionCheck);
            // Display an error.
            getString('maximumchars', 'core', maxLengthField.dataset.validationMaxLength)
                .then(errorMessage => {
                    return Templates.renderForPromise('core_form/setting_validation_failure', {
                        fieldid: maxLengthField.id,
                        message: errorMessage,
                    });
                })
                .then(errorTemplate => {
                    if (!maxLengthField.dataset.validationFailureId) {
                        const formWrapper = maxLengthField.closest('.form-text');
                        Templates.prependNodeContents(formWrapper, errorTemplate.html, errorTemplate.js);
                        maxLengthField.dataset.validationFailureId = `maxlength_error_${maxLengthField.id}`;
                        // Disable submit button when the message is displayed.
                        updateSubmitButton();
                    }
                    return;
                })
                .then(() => {
                    maxLengthField.setAttribute('aria-invalid', true);
                    const errorField = document.getElementById(maxLengthField.dataset.validationFailureId);
                    if (errorField) {
                        errorField.setAttribute('aria-describedby', maxLengthField.id);
                    }
                    return;
                })
                .catch(Notification.exception);
        } else {
            // Remove the old message.
            const validationMessage = document.getElementById(maxLengthField.dataset.validationFailureId);
            if (validationMessage) {
                validationMessage.parentElement.remove();
                delete maxLengthField.dataset.validationFailureId;
                maxLengthField.removeAttribute('aria-invalid');
                // Enable submit button when the message was removed.
                updateSubmitButton();
            }
        }
    });
};

/**
 * Handle form submission.
 *
 * @param {Event} e The event.
 */
const submissionCheck = e => {
    const maxLengthFields = e.target.querySelectorAll('[data-validation-max-length]');
    const maxLengthFieldsArray = Array.from(maxLengthFields);
    maxLengthFieldsArray.some(maxLengthField => {
        // Focus on the first validation failure.
        if (maxLengthField.value.length > maxLengthField.dataset.validationMaxLength) {
            e.preventDefault();
            maxLengthField.focus();
            return true;
        }
        return false;
    });
};

/**
 * Update submit button.
 */
const updateSubmitButton = () => {
    const shouldDisable = document.querySelector('form#adminsettings .error');
    document.querySelector('form#adminsettings button[type="submit"]').disabled = !!shouldDisable;
};
