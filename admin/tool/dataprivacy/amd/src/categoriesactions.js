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
 * AMD module for categories actions.
 *
 * @module     tool_dataprivacy/categoriesactions
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
    DELETE: '[data-action="deletecategory"]',
};

export default class CategoriesActions {

    static init() {
        return new this();
    }

    /**
     * CategoriesActions class.
     */
    constructor() {
        this.registerEvents();
    }

    deleteCategory(id) {
        return Ajax.call([{
            methodname: 'tool_dataprivacy_delete_category',
            args: {id}
        }])[0];
    }

    handleCategoryRemoval(id) {
        this.deleteCategory(id)
            .then((data) => {
                if (data.result) {
                    document.querySelector(`tr[data-categoryid="${id}"]`)?.remove();
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

            this.confirmCategoryRemoval(target);
        });
    }

    confirmCategoryRemoval(target) {
        const id = target.dataset.id;
        var categoryname = target.dataset.name;
        var stringkeys = [
            {
                key: 'deletecategory',
                component: 'tool_dataprivacy'
            },
            {
                key: 'deletecategorytext',
                component: 'tool_dataprivacy',
                param: categoryname
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
            modal.getRoot().on(ModalEvents.save, () => this.handleCategoryRemoval(id));

            return modal;
        })
        .catch(Notification.exception);
    }
}
