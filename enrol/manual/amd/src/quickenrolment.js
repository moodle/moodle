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
 * Quick enrolment AMD module.
 *
 * @module     enrol_manual/quickenrolment
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import jQuery from 'jquery';
import * as Str from 'core/str';
import Fragment from 'core/fragment';
import ModalEvents from 'core/modal_events';
import ModalFactory from 'core/modal_factory';
import Notification from 'core/notification';
import Templates from 'core/templates';
import Config from 'core/config';
import Pending from 'core/pending';

const Selectors = {
    cohortSelector: "#id_cohortlist",
    triggerButtons: ".enrolusersbutton.enrol_manual_plugin [type='submit']",
    unwantedHiddenFields: ":input[value='_qf__force_multiselect_submission']"
};

const QuickEnrolment = class {
    constructor(contextId) {
        this.contextId = contextId;

        this.initModal();
    }

    /**
     * Private method
     *
     * @method initModal
     * @private
     */
    initModal() {
        var triggerButtons = jQuery(Selectors.triggerButtons);

        jQuery.when(
            Str.get_strings([
                {key: 'enroluserscohorts', component: 'enrol_manual'},
                {key: 'enrolusers', component: 'enrol_manual'},
            ]),
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                large: true,
            }, triggerButtons)
        )
        .then(function(strings, modal) {
            this.modal = modal;

            modal.setTitle(strings[1]);
            modal.setSaveButtonText(strings[1]);

            modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
            modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

            // We want the reset the form every time it is opened.
            modal.getRoot().on(ModalEvents.hidden, function() {
                modal.setBody('');
            });

            modal.getRoot().on(ModalEvents.shown, function() {
                var pendingPromise = new Pending('enrol_manual/quickenrolment:initModal:shown');
                var bodyPromise = this.getBody();
                bodyPromise.then(function(html) {
                    var stringIndex = jQuery(html).find(Selectors.cohortSelector).length ? 0 : 1;
                    modal.setSaveButtonText(strings[stringIndex]);

                    return;
                })
                .then(pendingPromise.resolve)
                .catch(Notification.exception);

                modal.setBody(bodyPromise);
            }.bind(this));

            return;
        }.bind(this))
        .fail(Notification.exception);

    }

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    submitForm(e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    }

    /**
     * Private method
     *
     * @method submitForm
     * @private
     * @param {Event} e Form submission event.
     */
    submitFormAjax(e) {
        // We don't want to do a real form submission.
        e.preventDefault();

        var form = this.modal.getRoot().find('form');

        // Before send the data through AJAX, we need to parse and remove some unwanted hidden fields.
        // This hidden fields are added automatically by mforms and when it reaches the AJAX we get an error.
        var hidden = form.find(Selectors.unwantedHiddenFields);
        hidden.each(function() {
            jQuery(this).remove();
        });

        var formData = form.serialize();

        this.modal.hide();

        var settings = {
            type: 'GET',
            processData: false,
            contentType: "application/json"
        };

        var script = Config.wwwroot + '/enrol/manual/ajax.php?' + formData;
        jQuery.ajax(script, settings)
            .then(function(response) {

                if (response.error) {
                    Notification.addNotification({
                        message: response.error,
                        type: "error"
                    });
                } else {
                    // Reload the page, don't show changed data warnings.
                    if (typeof window.M.core_formchangechecker !== "undefined") {
                        window.M.core_formchangechecker.reset_form_dirty_state();
                    }
                    window.location.reload();
                }
                return;
            })
            .fail(Notification.exception);
    }

    /**
     * Private method
     *
     * @method getBody
     * @private
     * @return {Promise}
     */
    getBody() {
        return Fragment.loadFragment('enrol_manual', 'enrol_users_form', this.contextId, {}).fail(Notification.exception);
    }

    /**
     * Private method
     *
     * @method getFooter
     * @private
     * @return {Promise}
     */
    getFooter() {
        return Templates.render('enrol_manual/enrol_modal_footer', {});
    }
};

export const init = ({contextid}) => {
    new QuickEnrolment(contextid);
};
