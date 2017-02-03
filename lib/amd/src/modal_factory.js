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
 * Create a modal.
 *
 * @module     core/modal_factory
 * @class      modal_factory
 * @package    core
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/modal_events', 'core/modal', 'core/modal_save_cancel', 'core/modal_confirm', 'core/modal_cancel',
        'core/templates', 'core/notification', 'core/custom_interaction_events'],
    function($, ModalEvents, Modal, ModalSaveCancel, ModalConfirm, ModalCancel, Templates, Notification, CustomEvents) {

    // The templates for each type of modal.
    var TEMPLATES = {
        DEFAULT: 'core/modal',
        SAVE_CANCEL: 'core/modal_save_cancel',
        CONFIRM: 'core/modal_confirm',
        CANCEL: 'core/modal_cancel',
    };

    // The JS classes for each type of modal.
    var CLASSES = {
        DEFAULT: Modal,
        SAVE_CANCEL: ModalSaveCancel,
        CONFIRM: ModalConfirm,
        CANCEL: ModalCancel,
    };

    // The available types of modals.
    var TYPES = {
        DEFAULT: 'DEFAULT',
        SAVE_CANCEL: 'SAVE_CANCEL',
        CONFIRM: 'CONFIRM',
        CANCEL: 'CANCEL',
    };

    /**
     * Set up the events required to show the modal and return focus when the modal
     * is closed.
     *
     * @method setUpTrigger
     * @param {object} modal The modal instance
     * @param {object} triggerElement The jQuery element to open the modal
     */
    var setUpTrigger = function(modal, triggerElement) {
        if (typeof triggerElement != 'undefined') {
            CustomEvents.define(triggerElement, [CustomEvents.events.activate]);
            triggerElement.on(CustomEvents.events.activate, function(e, data) {
                modal.show();
                data.originalEvent.preventDefault();
            });

            modal.getRoot().on(ModalEvents.hidden, function() {
                triggerElement.focus();
            });
        }
    };

    /**
     * Create the correct instance of a modal based on the givem type. Sets up
     * the trigger between the modal and the trigger element.
     *
     * @method createFromElement
     * @param {string} type A modal type (see TYPES)
     * @param {object} modalElement The modal HTML jQuery object
     * @param {object} triggerElement The trigger HTML jQuery object
     * @return {object} Modal instance
     */
    var createFromElement = function(type, modalElement, triggerElement) {
        modalElement = $(modalElement);
        var ClassName = CLASSES[type];
        var modal = new ClassName(modalElement);
        setUpTrigger(modal, triggerElement);

        return modal;
    };

    /**
     * Create the correct modal instance for the given type, including loading
     * the correct template and setting up the trigger relationship with the
     * trigger element.
     *
     * @method createFromType
     * @param {string} type A modal type (see TYPES)
     * @param {object} triggerElement The trigger HTML jQuery object
     * @return {promise} Resolved with a Modal instance
     */
    var createFromType = function(type, triggerElement) {
        var templateName = TEMPLATES[type];

        return Templates.render(templateName, {})
            .then(function(html) {
                var modalElement = $(html);
                return createFromElement(type, modalElement, triggerElement);
            })
            .fail(Notification.exception);
    };

    /**
     * Create a Modal instance.
     *
     * @method create
     * @param {object} modalConfig The configuration to create the modal instance
     * @param {object} triggerElement The trigger HTML jQuery object
     * @return {promise} Resolved with a Modal instance
     */
    var create = function(modalConfig, triggerElement) {
        var type = modalConfig.type || TYPES.DEFAULT;
        var isLarge = modalConfig.large ? true : false;

        if (!TYPES[type]) {
            type = TYPES.DEFAULT;
        }

        return createFromType(type, triggerElement)
            .then(function(modal) {
                if (typeof modalConfig.title != 'undefined') {
                    modal.setTitle(modalConfig.title);
                }

                if (typeof modalConfig.body != 'undefined') {
                    modal.setBody(modalConfig.body);
                }

                if (typeof modalConfig.footer != 'undefined') {
                    modal.setFooter(modalConfig.footer);
                }

                if (isLarge) {
                    modal.setLarge();
                }

                return modal;
            });
    };

    return {
        create: create,
        types: TYPES,
    };
});
