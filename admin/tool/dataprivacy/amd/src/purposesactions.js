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
 * AMD module for purposes actions.
 *
 * @module     tool_dataprivacy/purposesactions
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
 * Module for purpose actions.
 *
 * @module     tool_dataprivacy/purposeactions
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import * as Ajax from 'core/ajax';
import * as Notification from 'core/notification';
import * as Str from 'core/str';
import ModalEvents from 'core/modal_events';
import ModalSaveCancel from 'core/modal_save_cancel';

/**
 * List of action selectors.
 *
 * @type {{DELETE: string}}
 */
const ACTIONS = {
    DELETE: '[data-action="deletepurpose"]',
};

export default class PurposeActions {

    static init() {
        return new this();
    }

    constructor() {
        this.registerEvents();
    }

    deletePurpose(id) {
        return Ajax.call([{
            methodname: 'tool_dataprivacy_delete_purpose',
            args: {id}
        }])[0];
    }

    handleRemoval(id) {
        this.deletePurpose(id)
            .then((data) => {
                if (data.result) {
                    document.querySelector(`tr[data-purposeid="${id}"]`)?.remove();
                } else {
                    Notification.addNotification({
                        message: data.warnings[0].message,
                        type: 'error'
                    });
                }

                return;
            })
            .catch(Notification.exception);

    }

    /**
     * Register event listeners.
     */
    registerEvents() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest(ACTIONS.DELETE);
            if (!target) {
                return;
            }

            e.preventDefault();

            this.confirmRemoval(target);
        });
    }

    confirmRemoval(target) {
        const id = target.dataset.id;
        var purposename = target.dataset.name;
        var stringkeys = [
            {
                key: 'deletepurpose',
                component: 'tool_dataprivacy'
            },
            {
                key: 'deletepurposetext',
                component: 'tool_dataprivacy',
                param: purposename
            },
            {
                key: 'delete'
            }
        ];

        Str.get_strings(stringkeys).then(([
            title,
            body,
            save,
        ]) => ModalSaveCancel.create({
            title,
            body,
            buttons: {
                save,
            },
            show: true,
            removeOnClose: true,
        }))
            .then((modal) => {
                // Handle save event.
                modal.getRoot().on(ModalEvents.save, () => this.handleRemoval(id));

                return modal;
            })
            .catch(Notification.exception);
    }
}
