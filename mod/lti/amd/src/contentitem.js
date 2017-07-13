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
 * Launches the modal dialogue that contains the iframe that sends the Content-Item selection request to an
 * LTI tool provider that supports Content-Item type message.
 *
 * See template: mod_lti/contentitem
 *
 * @module     mod_lti/contentitem
 * @class      contentitem
 * @package    mod_lti
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(
    [
        'jquery',
        'core/notification',
        'core/str',
        'core/templates',
        'mod_lti/form-field',
        'core/modal_factory',
        'core/modal_events'
    ],
    function($, notification, str, templates, FormField, ModalFactory, ModalEvents) {
        var dialogue;
        var contentItem = {
            /**
             * Init function.
             *
             * @param {string} url The URL for the content item selection.
             * @param {object} postData The data to be sent for the content item selection request.
             */
            init: function(url, postData) {
                var context = {
                    url: url,
                    postData: postData
                };
                var bodyPromise = templates.render('mod_lti/contentitem', context);

                if (dialogue) {
                    // Set dialogue body.
                    dialogue.setBody(bodyPromise);
                    // Display the dialogue.
                    dialogue.show();
                    return;
                }

                str.get_string('selectcontent', 'lti').then(function(title) {
                    return ModalFactory.create({
                        title: title,
                        body: bodyPromise,
                        large: true
                    });
                }).then(function(modal) {
                    dialogue = modal;
                    // On hide handler.
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        // Empty modal contents when it's hidden.
                        modal.setBody('');

                        // Fetch notifications.
                        notification.fetchNotifications();
                    });

                    // Display the dialogue.
                    modal.show();
                    return;
                }).catch(notification.exception);
            }
        };

        /**
         * Array of form fields for LTI tool configuration.
         *
         * @type {*[]}
         */
        var ltiFormFields = [
            new FormField('name', FormField.TYPES.TEXT, false, ''),
            new FormField('introeditor', FormField.TYPES.EDITOR, false, ''),
            new FormField('toolurl', FormField.TYPES.TEXT, true, ''),
            new FormField('securetoolurl', FormField.TYPES.TEXT, true, ''),
            new FormField('instructorchoiceacceptgrades', FormField.TYPES.CHECKBOX, true, true),
            new FormField('instructorchoicesendname', FormField.TYPES.CHECKBOX, true, true),
            new FormField('instructorchoicesendemailaddr', FormField.TYPES.CHECKBOX, true, true),
            new FormField('instructorcustomparameters', FormField.TYPES.TEXT, true, ''),
            new FormField('icon', FormField.TYPES.TEXT, true, ''),
            new FormField('secureicon', FormField.TYPES.TEXT, true, ''),
            new FormField('launchcontainer', FormField.TYPES.SELECT, true, 0)
        ];

        /**
         * Window function that can be called from mod_lti/contentitem_return to close the dialogue and process the return data.
         *
         * @param {object} returnData The fetched configuration data from the Content-Item selection dialogue.
         */
        window.processContentItemReturnData = function(returnData) {
            if (dialogue) {
                dialogue.hide();
            }

            // Populate LTI configuration fields from return data.
            var index;
            for (index in ltiFormFields) {
                var field = ltiFormFields[index];
                var value = null;
                if ($.type(returnData[field.name]) !== 'undefined') {
                    value = returnData[field.name];
                }
                field.setFieldValue(value);
            }
        };

        return contentItem;
    }
);
