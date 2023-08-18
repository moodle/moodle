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
 * @deprecated since Moodle 4.3
 * @todo       Final deprecation in Moodle 4.7/5.2. See MDL-79128/
 */

import $ from 'jquery';
import ModalEvents from 'core/modal_events';
import * as ModalRegistry from 'core/modal_registry';
import Modal from 'core/modal';
import ModalSaveCancel from 'core/modal_save_cancel';
import ModalDeleteCancel from 'core/modal_delete_cancel';
import ModalCancel from 'core/modal_cancel';
import ModalAlert from 'core/local/modal/alert';
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
 * Create a Modal instance.
 *
 * @method create
 * @param {object} modalConfig The configuration to create the modal instance
 * @param {object} triggerElement The trigger HTML jQuery object
 * @return {promise} Resolved with a Modal instance
 */
export const create = (modalConfig, triggerElement) => {
    window.console.warn(
        'The modal_factory has been deprecated since Moodle 4.3. Please use the create method on your target modal type instead.',
    );
    // Use of the triggerElement has been deprecated.
    const type = modalConfig.type || types.DEFAULT;

    const registryConf = ModalRegistry.get(type);
    if (!registryConf) {
        Notification.exception({message: `Unable to find modal of type: ${type}`});
    }

    const modal = registryConf.module.create(modalConfig);

    if (triggerElement) {
        window.console.warn(
            'The triggerElement feature of the modal_factory has been deprecated. Please use event listeners instead.',
        );
        setUpTrigger(modal, triggerElement, modalConfig);
    }

    return $.when(new Promise((resolve, reject) => {
        modal
            .then(resolve)
            .catch(reject);
    }));
};

export default {
    create,
    types,
};
