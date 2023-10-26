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
 * Javascript module for bulk actions.
 *
 * @module      gradereport_singleview/bulkactions
 * @copyright   2022 Ilya Tregubov <ilya@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import CustomEvents from "core/custom_interaction_events";
import ModalFactory from 'core/modal_factory';
import Templates from 'core/templates';
import ModalEvents from 'core/modal_events';
import * as Str from 'core/str';
import Notification from 'core/notification';
import selectors from 'gradereport_singleview/selectors';

/**
 * Initialize module.
 */
export const init = () => {
    const pendingPromise = new Pending();
    registerListenerEvents();
    pendingPromise.resolve();
};

/**
 * Register bulk actions related event listeners.
 *
 * @method registerListenerEvents
 */
const registerListenerEvents = () => {
    const events = [
        'click',
        CustomEvents.events.activate,
        CustomEvents.events.keyboardActivate
    ];
    CustomEvents.define(document, events);

    // Register events.
    events.forEach((event) => {
        document.addEventListener(event, async(e) => {
            const trigger = e.target.closest(selectors.actions.bulkaction);

            if (trigger) {
                if ((trigger.dataset.action === 'overrideallgrades') || (trigger.dataset.action === 'overridenonegrades')) {
                    const override = document.querySelectorAll(selectors.elements.override);

                    if (trigger.dataset.action === 'overridenonegrades') {
                        // Alert for removing all grade overrides on page.
                        Str.get_strings([
                            {key: 'removeoverride', component: 'gradereport_singleview'},
                            {key: 'overridenoneconfirm', component: 'gradereport_singleview'},
                            {key: 'removeoverridesave', component: 'gradereport_singleview'},
                            {key: 'cancel', component: 'moodle'},
                        ]).done((strings) => {
                            Notification.confirm(
                                strings[0],
                                strings[1],
                                strings[2],
                                strings[3],
                                () => {
                                    // Uncheck each override checkbox - this will make grade and feedback input fields disabled.
                                    override.forEach((el) => {
                                        if (el.checked) {
                                            el.click();
                                        }
                                    });
                                });
                        }).fail(Notification.exception);

                    } else {
                        // Check each override checkbox - this will make grade and feedback input fields enabled.
                        override.forEach((el) => {
                            if (!el.checked) {
                                el.click();
                            }
                        });
                    }
                } else if ((trigger.dataset.action === 'excludeallgrades') || (trigger.dataset.action === 'excludenonegrades')) {
                    const exclude = document.querySelectorAll(selectors.elements.exclude);
                    const checked = (trigger.dataset.action === 'excludeallgrades');
                    // Uncheck or check each exclude checkbox.
                    exclude.forEach((el) => {
                        el.checked = checked;
                    });
                } else if (trigger.dataset.action === 'bulklegend') {
                    // Modal for bulk insert grades.
                    Str.get_strings([
                        {key: 'bulklegend', component: 'gradereport_singleview'},
                        {key: 'save', component: 'moodle'},
                    ]).done((strings) => {
                        ModalFactory.create({
                            type: ModalFactory.types.SAVE_CANCEL,
                            body: Templates.render('gradereport_singleview/bulkinsert', {
                                id: 'bulkinsertmodal',
                                name: 'bulkinsertmodal'
                            }),
                            title: strings[0],
                        }).then((modal) => {
                            modal.setSaveButtonText(strings[1]);
                            modal.getFooter().find(selectors.elements.modalsave).attr('disabled', true);

                            modal.getRoot().on(ModalEvents.hidden, () => {
                                modal.getRoot().remove();
                            });

                            // We need to acknowledge that we understand risks of loosing data.
                            // Only when acknowledge checkbox is checked we allow selecting insert options.
                            modal.getRoot().on('change', selectors.elements.warningcheckbox,
                                (e) => {
                                    e.preventDefault();
                                    if (e.target.checked) {
                                        modal.getRoot().find(selectors.elements.modalformdata).removeClass('dimmed_text');
                                        modal.getRoot().find(selectors.elements.modalradio).removeAttr('disabled');
                                        modal.getRoot().find(selectors.elements.modalinput).removeAttr('disabled');

                                        const formRadioData = modal.getRoot().find(selectors.elements.modalradiochecked).val();
                                        // We allow saving grades only when all needed data present on form.
                                        if (formRadioData) {
                                            modal.getFooter().find(selectors.elements.modalsave).removeAttr('disabled');
                                        }
                                    } else {
                                        modal.getRoot().find(selectors.elements.modalformdata).addClass('dimmed_text');
                                        modal.getRoot().find(selectors.elements.modalradio).attr('disabled', true);
                                        modal.getRoot().find(selectors.elements.modalinput).attr('disabled', true);
                                        modal.getFooter().find(selectors.elements.modalsave).attr('disabled', true);
                                    }
                                });

                            // We allow saving grades only when all needed data present on form.
                            modal.getRoot().on('change', selectors.elements.modalradio,
                                (e) => {
                                    e.preventDefault();
                                    modal.getFooter().find(selectors.elements.modalsave).removeAttr('disabled');
                                });

                            modal.getRoot().on(ModalEvents.save, () => {
                                // When save button is clicked in modal form we insert data from modal
                                // into preexisted hidden bulk insert form and Save button for table form.
                                document.querySelector(selectors.elements.enablebulkinsert).checked = true;
                                const formRadioData = modal.getRoot().find(selectors.elements.modalradiochecked).val();
                                const $select = document.querySelector(selectors.elements.formradio);
                                $select.value = formRadioData;

                                const formData = modal.getRoot().find(selectors.elements.modalgrade).val();
                                document.querySelector(selectors.elements.formgrade).value = formData;
                                document.querySelector(selectors.elements.formsave).click();
                            });

                            modal.show();

                            return modal;
                        }).fail(Notification.exception);
                    }).fail(Notification.exception);
                }
            }
        });
    });
};
