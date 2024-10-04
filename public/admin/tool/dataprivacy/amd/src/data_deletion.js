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
 * Request actions.
 *
 * @module     tool_dataprivacy/data_deletion
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/str',
    'core/modal_save_cancel',
    'core/modal_events'],
function($, Ajax, Notification, Str, ModalSaveCancel, ModalEvents) {

    /**
     * List of action selectors.
     *
     * @type {{MARK_FOR_DELETION: string}}
     * @type {{SELECT_ALL: string}}
     */
    var ACTIONS = {
        MARK_FOR_DELETION: '[data-action="markfordeletion"]',
        SELECT_ALL: '[data-action="selectall"]',
    };

    /**
     * List of selectors.
     *
     * @type {{SELECTCONTEXT: string}}
     */
    var SELECTORS = {
        SELECTCONTEXT: '.selectcontext',
    };

    /**
     * DataDeletionActions class.
     */
    var DataDeletionActions = function() {
        this.registerEvents();
    };

    /**
     * Register event listeners.
     */
    DataDeletionActions.prototype.registerEvents = function() {
        $(ACTIONS.MARK_FOR_DELETION).click(function(e) {
            e.preventDefault();

            var selectedIds = [];
            $(SELECTORS.SELECTCONTEXT).each(function() {
                var checkbox = $(this);
                if (checkbox.is(':checked')) {
                    selectedIds.push(checkbox.val());
                }
            });
            showConfirmation(selectedIds);
        });

        $(ACTIONS.SELECT_ALL).change(function(e) {
            e.preventDefault();

            var selectallnone = $(this);
            if (selectallnone.is(':checked')) {
                $(SELECTORS.SELECTCONTEXT).attr('checked', 'checked');
            } else {
                $(SELECTORS.SELECTCONTEXT).removeAttr('checked');
            }
        });
    };

    /**
     * Show the confirmation dialogue.
     *
     * @param {Array} ids The array of expired context record IDs.
     */
    function showConfirmation(ids) {
        var keys = [
            {
                key: 'confirm',
                component: 'moodle'
            },
            {
                key: 'confirmcontextdeletion',
                component: 'tool_dataprivacy'
            }
        ];
        var wsfunction = 'tool_dataprivacy_confirm_contexts_for_deletion';

        var modalTitle = '';
        Str.get_strings(keys).then(function(langStrings) {
            modalTitle = langStrings[0];
            var confirmMessage = langStrings[1];
            return ModalSaveCancel.create({
                title: modalTitle,
                body: confirmMessage,
            });
        }).then(function(modal) {
            modal.setSaveButtonText(modalTitle);

            // Handle save event.
            modal.getRoot().on(ModalEvents.save, function() {
                // Confirm the request.
                var params = {
                    'ids': ids
                };

                var request = {
                    methodname: wsfunction,
                    args: params
                };

                Ajax.call([request])[0].done(function(data) {
                    if (data.result) {
                        window.location.reload();
                    } else {
                        Notification.addNotification({
                            message: data.warnings[0].message,
                            type: 'error'
                        });
                    }
                }).fail(Notification.exception);
            });

            // Handle hidden event.
            modal.getRoot().on(ModalEvents.hidden, function() {
                // Destroy when hidden.
                modal.destroy();
            });

            return modal;
        }).done(function(modal) {
            modal.show();
        }).fail(Notification.exception);
    }

    return DataDeletionActions;
});
