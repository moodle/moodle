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
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import ModalEvents from 'core/modal_events';
import * as ModalRegistry from 'core/modal_registry';
import Modal from 'core/modal';
import ModalSaveCancel from 'core/modal_save_cancel';
import ModalDeleteCancel from 'core/modal_delete_cancel';
import ModalCancel from 'core/modal_cancel';
import ModalAlert from 'core/local/modal/alert';
import * as Templates from 'core/templates';
import * as Notification from 'core/notification';
import * as CustomEvents from 'core/custom_interaction_events';
import Pending from 'core/pending';

/**
 * The available standard modals.
 *
 * @property {String} DEFAULT The default modal
 * @property {String} SAVE_CANCEL A modal which can be used to either save, or cancel.
 * @property {String} DELETE_CANCEL A modal which can be used to either delete, or cancel.
 * @property {String} CANCEL A modal which displayed a cancel button
 * @property {String} ALERT An information modal which only displays information.
 */
export const types = {
    DEFAULT: 'DEFAULT',
    SAVE_CANCEL: ModalSaveCancel.TYPE,
    DELETE_CANCEL: ModalDeleteCancel.TYPE,
    CANCEL: ModalCancel.TYPE,
    ALERT: ModalAlert.TYPE,
};

// Most modals are self-registering.
// We do not self-register the base Modal because we do not want to define a default TYPE
// on the class that every other modal extends.
ModalRegistry.register(types.DEFAULT, Modal, Modal.TEMPLATE);

/**
 * Set up the events required to show the modal and return focus when the modal
 * is closed.
 *
 * @method setUpTrigger
 * @private
 * @param {Promise} modalPromise The modal instance
 * @param {object} triggerElement The jQuery element to open the modal
 * @param {object} modalConfig The modal configuration given to the factory
 */
const setUpTrigger = (modalPromise, triggerElement, modalConfig) => {
    // The element that actually shows the modal.
    let actualTriggerElement = null;
    // Check if the client has provided a callback function to be called
    // before the modal is displayed.
    const hasPreShowCallback = (typeof modalConfig.preShowCallback == 'function');
    // Function to handle the trigger element being activated.
    const triggeredCallback = (e, data) => {
        const pendingPromise = new Pending('core/modal_factory:setUpTrigger:triggeredCallback');
        actualTriggerElement = $(e.currentTarget);

        // eslint-disable-next-line promise/catch-or-return
        modalPromise.then(function(modal) {
            if (hasPreShowCallback) {
                // If the client provided a pre-show callback then execute
                // it now before showing the modal.
                modalConfig.preShowCallback(actualTriggerElement, modal);
            }

            modal.show();

            return modal;
        })
        .then(pendingPromise.resolve);
        data.originalEvent.preventDefault();
    };

    // The trigger element can either be a single element or it can be an
    // element + selector pair to create a delegated event handler to trigger
    // the modal.
    if (Array.isArray(triggerElement)) {
        const selector = triggerElement[1];
        triggerElement = triggerElement[0];

        CustomEvents.define(triggerElement, [CustomEvents.events.activate]);
        triggerElement.on(CustomEvents.events.activate, selector, triggeredCallback);
    } else {
        CustomEvents.define(triggerElement, [CustomEvents.events.activate]);
        triggerElement.on(CustomEvents.events.activate, triggeredCallback);
    }

    // eslint-disable-next-line promise/catch-or-return
    modalPromise.then(function(modal) {
        modal.getRoot().on(ModalEvents.hidden, function() {
            // Focus on the trigger element that actually launched the modal.
            if (actualTriggerElement !== null) {
                actualTriggerElement.focus();
            }
        });

        return modal;
    });
};

/**
 * Create the correct instance of a modal based on the givem type. Sets up
 * the trigger between the modal and the trigger element.
 *
 * @method createFromElement
 * @private
 * @param {object} registryConf A config from the ModalRegistry
 * @param {object} modalElement The modal HTML jQuery object
 * @return {object} Modal instance
 */
const createFromElement = (registryConf, modalElement) => {
    modalElement = $(modalElement);
    const Module = registryConf.module;
    const modal = new Module(modalElement);

    return modal;
};

/**
 * Create the correct modal instance for the given type, including loading
 * the correct template.
 *
 * @method createFromType
 * @private
 * @param {object} registryConf A config from the ModalRegistry
 * @param {object} templateContext The context to render the template with
 * @returns {promise} Resolved with a Modal instance
 */
const createFromType = (registryConf, templateContext) => {
    const templateName = registryConf.template;
    return Templates.render(templateName, templateContext)
        .then((html) => createFromElement(registryConf, $(html)));
};

/**
 * Create a Modal instance.
 *
 * @method create
 * @param {object} modalConfig The configuration to create the modal instance
 * @param {object} triggerElement The trigger HTML jQuery object
 * @return {promise} Resolved with a Modal instance
 */
export const create = (modalConfig, triggerElement) => {
    const type = modalConfig.type || types.DEFAULT;
    const isLarge = modalConfig.large ? true : false;
    const isVerticallyCentered = modalConfig.verticallyCentered ? true : false;
    // If 'scrollable' is not configured, set the modal to be scrollable by default.
    const isScrollable = modalConfig.hasOwnProperty('scrollable') ? modalConfig.scrollable : true;

    const registryConf = ModalRegistry.get(type);
    if (!registryConf) {
        Notification.exception({message: 'Unable to find modal of type: ' + type});
    }

    const templateContext = modalConfig.templateContext || {};

    const modalPromise = createFromType(registryConf, templateContext)
        .then((modal) => {
            if (typeof modalConfig.title !== 'undefined') {
                modal.setTitle(modalConfig.title);
            }

            if (typeof modalConfig.body !== 'undefined') {
                modal.setBody(modalConfig.body);
            }

            if (typeof modalConfig.footer !== 'undefined') {
                modal.setFooter(modalConfig.footer);
            }

            if (modalConfig.buttons) {
                Object.entries(modalConfig.buttons).forEach(function([key, value]) {
                    modal.setButtonText(key, value);
                });
            }

            if (isLarge) {
                modal.setLarge();
            }

            if (isVerticallyCentered) {
                modal.setVerticallyCentered();
            }

            if (typeof modalConfig.removeOnClose !== 'undefined') {
                // If configured remove the modal when hiding it.
                modal.setRemoveOnClose(modalConfig.removeOnClose);
            }

            modal.setScrollable(isScrollable);

            return modal;
        });

    if (typeof triggerElement !== 'undefined') {
        setUpTrigger(modalPromise, triggerElement, modalConfig);
    }

    return modalPromise;
};

export default {
    create,
    types,
};
