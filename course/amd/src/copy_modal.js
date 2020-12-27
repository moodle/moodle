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
 * This module provides the course copy modal from the course and
 * category management screen.
 *
 * @module     course
 * @package    core
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.9
 */

define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events',
        'core/ajax', 'core/fragment', 'core/notification', 'core/config'],
        function($, Str, ModalFactory, ModalEvents, ajax, Fragment, Notification, Config) {

    /**
     * Module level variables.
     */
    var CopyModal = {};
    var contextid;
    var course;
    var modalObj;
    var spinner = '<p class="text-center">'
        + '<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>'
        + '</p>';

    /**
     * Creates the modal for the course copy form
     *
     * @private
     */
    function createModal() {
        // Get the Title String.
        Str.get_string('loading').then(function(title) {
            // Create the Modal.
            ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: title,
                body: spinner,
                large: true
            })
            .done(function(modal) {
                modalObj = modal;
                // Explicitly handle form click events.
                modalObj.getRoot().on('click', '#id_submitreturn', processModalForm);
                modalObj.getRoot().on('click', '#id_submitdisplay', function(e) {
                    e.formredirect = true;
                    processModalForm(e);

                });
                modalObj.getRoot().on('click', '#id_cancel', function(e) {
                    e.preventDefault();
                    modalObj.setBody(spinner);
                    modalObj.hide();
                });
            });
            return;
        }).catch(function() {
            Notification.exception(new Error('Failed to load string: loading'));
        });
    }

    /**
     * Updates the body of the modal window.
     *
     * @param {Object} formdata
     * @private
     */
    function updateModalBody(formdata) {
        if (typeof formdata === "undefined") {
            formdata = {};
        }

        var params = {
                'jsonformdata': JSON.stringify(formdata),
                'courseid': course.id
        };

        modalObj.setBody(spinner);
        Str.get_string('copycoursetitle', 'backup', course.shortname).then(function(title) {
            modalObj.setTitle(title);
            modalObj.setBody(Fragment.loadFragment('course', 'new_base_form', contextid, params));
            return;
        }).catch(function() {
            Notification.exception(new Error('Failed to load string: copycoursetitle'));
        });
    }

    /**
     * Updates Moodle form with selected information.
     *
     * @param {Object} e
     * @private
     */
    function processModalForm(e) {
        e.preventDefault(); // Stop modal from closing.

        // Form data.
        var copyform = modalObj.getRoot().find('form').serialize();
        var formjson = JSON.stringify(copyform);

        // Handle invalid form fields for better UX.
        var invalid = $.merge(
                modalObj.getRoot().find('[aria-invalid="true"]'),
                modalObj.getRoot().find('.error')
        );

        if (invalid.length) {
            invalid.first().focus();
            return;
        }

        // Submit form via ajax.
        ajax.call([{
            methodname: 'core_backup_submit_copy_form',
            args: {
                jsonformdata: formjson
            },
        }])[0].done(function() {
            // For submission succeeded.
            modalObj.setBody(spinner);
            modalObj.hide();

            if (e.formredirect == true) {
                // We are redirecting to copy progress display.
                let redirect = Config.wwwroot + "/backup/copyprogress.php?id=" + course.id;
                window.location.assign(redirect);
            }

        }).fail(function() {
            // Form submission failed server side, redisplay with errors.
            updateModalBody(copyform);
        });
    }

    /**
     * Initialise the class.
     *
     * @param {Object} context
     * @public
     */
    CopyModal.init = function(context) {
        contextid = context;
        // Setup the initial Modal.
        createModal();

        // Setup the click handlers on the copy buttons.
        $('.action-copy').on('click', function(e) {
            e.preventDefault(); // Stop. Hammer time.
            let url = new URL(this.getAttribute('href'));
            let params = new URLSearchParams(url.search);
            let courseid = params.get('id');

            ajax.call([{ // Get the course information.
                methodname: 'core_course_get_courses',
                args: {
                    'options': {'ids': [courseid]},
                },
            }])[0].done(function(response) {
                // We have the course info get the modal content.
                course = response[0];
                updateModalBody();

            }).fail(function() {
                Notification.exception(new Error('Failed to load course'));
            });

            modalObj.show();
        });

    };

    return CopyModal;
});
