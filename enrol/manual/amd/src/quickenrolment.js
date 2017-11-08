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
define(['core/templates',
         'jquery',
         'core/str',
         'core/config',
         'core/notification',
         'core/modal_factory',
         'core/modal_events',
         'core/fragment',
       ],
       function(Template, $, Str, Config, Notification, ModalFactory, ModalEvents, Fragment) {

    /** @type {Object} The list of selectors for the quick enrolment modal. */
    var SELECTORS = {
        COHORTSELECT: "#id_cohortlist",
        TRIGGERBUTTONS: ".enrolusersbutton.enrol_manual_plugin [type='submit']",
        UNWANTEDHIDDENFIELDS: ":input[value='_qf__force_multiselect_submission']"
    };

    /**
     * Constructor
     *
     * @param {Object} options Object containing options. The only valid option at this time is contextid.
     * Each call to templates.render gets it's own instance of this class.
     */
    var QuickEnrolment = function(options) {
        this.contextid = options.contextid;

        this.initModal();
    };
    // Class variables and functions.

    /** @var {number} courseid - */
    QuickEnrolment.prototype.courseid = 0;

    /** @var {Modal} modal */
    QuickEnrolment.prototype.modal = null;

    /**
     * Private method
     *
     * @method initModal
     * @private
     */
    QuickEnrolment.prototype.initModal = function() {
        var triggerButtons = $(SELECTORS.TRIGGERBUTTONS);

        $.when(
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
                var bodyPromise = this.getBody();
                bodyPromise.then(function(html) {
                    var stringIndex = $(html).find(SELECTORS.COHORTSELECT).length ? 0 : 1;
                    modal.setSaveButtonText(strings[stringIndex]);

                    return;
                })
                .fail(Notification.exception);

                modal.setBody(bodyPromise);
            }.bind(this));

            return;
        }.bind(this))
        .fail(Notification.exception);
    };

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    QuickEnrolment.prototype.submitForm = function(e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    };

    /**
     * Private method
     *
     * @method submitForm
     * @private
     * @param {Event} e Form submission event.
     */
    QuickEnrolment.prototype.submitFormAjax = function(e) {
        // We don't want to do a real form submission.
        e.preventDefault();

        var form = this.modal.getRoot().find('form');

        // Before send the data through AJAX, we need to parse and remove some unwanted hidden fields.
        // This hidden fields are added automatically by mforms and when it reaches the AJAX we get an error.
        var hidden = form.find(SELECTORS.UNWANTEDHIDDENFIELDS);
        hidden.each(function() {
            this.remove();
        });

        var formData = form.serialize();

        this.modal.hide();

        var settings = {
            type: 'GET',
            processData: false,
            contentType: "application/json"
        };

        var script = Config.wwwroot + '/enrol/manual/ajax.php?' + formData;
        $.ajax(script, settings)
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
    };

    /**
     * Private method
     *
     * @method getBody
     * @private
     * @return {Promise}
     */
    QuickEnrolment.prototype.getBody = function() {
        return Fragment.loadFragment('enrol_manual', 'enrol_users_form', this.contextid, {}).fail(Notification.exception);
    };

    /**
     * Private method
     *
     * @method getFooter
     * @private
     * @return {Promise}
     */
    QuickEnrolment.prototype.getFooter = function() {
        return Template.render('enrol_manual/enrol_modal_footer', {});
    };

    return /** @alias module:enrol_manual/quickenrolment */ {
        // Public variables and functions.
        /**
         * Every call to init creates a new instance of the class with it's own event listeners etc.
         *
         * @method init
         * @public
         * @param {object} config - config variables for the module.
         */
        init: function(config) {
            (new QuickEnrolment(config));
        }
    };
});
