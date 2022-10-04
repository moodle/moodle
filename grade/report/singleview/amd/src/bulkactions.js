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
            const trigger = e.target.closest('[data-role]');
            if (trigger) {
                if ((trigger.dataset.role === 'overrideallgrades') || (trigger.dataset.role === 'overridenonegrades')) {
                    const overrideAll = document.querySelectorAll('input[type=checkbox][name^=override]');

                    if (trigger.dataset.role === 'overridenonegrades') {
                        const confirm = new M.core.confirm({
                            title: M.util.get_string('removeoverride', 'gradereport_singleview'),
                            question: M.util.get_string('overridenoneconfirm', 'gradereport_singleview'),
                            noLabel: M.util.get_string('cancel', 'moodle'),
                            yesLabel: M.util.get_string('removeoverridesave', 'gradereport_singleview')
                        });

                        confirm.on('complete-yes', function () {
                            confirm.hide();
                            confirm.destroy();

                            overrideAll.forEach(function (el) {
                                if (el.checked) {
                                    el.click();
                                }
                            });
                        }, self);
                        confirm.show();
                    } else {
                        overrideAll.forEach(function (el) {
                            if (!el.checked) {
                                el.click();
                            }
                        });
                    }
                } else if ((trigger.dataset.role === 'excludeallgrades') || (trigger.dataset.role === 'excludenonegrades')) {
                    const excludeAll = document.querySelectorAll('input[type=checkbox][name^=exclude]');
                    const checked = (trigger.dataset.role === 'excludeallgrades');
                    excludeAll.forEach(function (el) {
                        el.checked = checked;
                    });
                } else if (trigger.dataset.role === 'bulklegend') {
                    ModalFactory.create({
                        type: ModalFactory.types.SAVE_CANCEL,
                        body: Templates.render('gradereport_singleview/bulkinsert', {
                            id: 'bulkinsertmodal',
                            name: 'bulkinsertmodal'
                        }),
                        title: 'Bulk insert',
                    })
                        .then(function (modal) {
                            modal.setSaveButtonText('Save');
                            modal.getFooter().find('[data-action="save"]').attr('disabled', true);

                            modal.getRoot().on(ModalEvents.hidden, function () {
                                modal.getRoot().remove();
                            });

                            modal.getRoot().on('change', 'input[type="checkbox"]',
                                (e) => {
                                    e.preventDefault();
                                    if (e.target.checked) {
                                        modal.getRoot().find('.formdata').removeClass('dimmed_text');
                                        modal.getRoot().find('input[type="radio"]').removeAttr('disabled');
                                        modal.getRoot().find('input[type="text"]').removeAttr('disabled');

                                        const formRadioData = modal.getRoot().find('input[type="radio"]:checked').val();
                                        if (formRadioData) {
                                            modal.getFooter().find('[data-action="save"]').removeAttr('disabled');
                                        }
                                    } else {
                                        modal.getRoot().find('.formdata').addClass('dimmed_text');
                                        modal.getRoot().find('input[type="radio"]').attr('disabled', true);
                                        modal.getRoot().find('input[type="text"]').attr('disabled', true);
                                        modal.getFooter().find('[data-action="save"]').attr('disabled', true);
                                    }
                                });

                            modal.getRoot().on('change', 'input[type="radio"]',
                                (e) => {
                                    e.preventDefault();
                                    modal.getFooter().find('[data-action="save"]').removeAttr('disabled');
                                });

                            modal.getRoot().on(ModalEvents.save, function () {
                                document.querySelector('input[type="checkbox"][name^=bulk]').checked = true;

                                const formRadioData = modal.getRoot().find('input[type="radio"]:checked').val();
                                const $select = document.querySelector('select[name^=bulk]');
                                $select.value = formRadioData;

                                const formData = modal.getRoot().find('.form-control').val();
                                document.querySelector('input[type="text"][name^=bulk]').value = formData;
                                document.querySelector('input[type="submit"]').click();
                            });

                            modal.show();

                            return modal;
                        });
                }
            }
        });
    });
};
