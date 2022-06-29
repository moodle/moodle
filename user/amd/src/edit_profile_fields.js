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

import ModalForm from 'core_form/modalform';
import {get_string as getString} from 'core/str';

/**
 * User profile fields editor
 *
 * @module     core_user/edit_profile_fields
 * @copyright  2021 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const Selectors = {
    actions: {
        editCategory: '[data-action="editcategory"]',
        editField: '[data-action="editfield"]',
        createField: '[data-action="createfield"]',
    },
};

export const init = () => {
    document.addEventListener('click', function(e) {
        let element = e.target.closest(Selectors.actions.editCategory);
        if (element) {
            e.preventDefault();
            const title = element.getAttribute('data-id') ?
                getString('profileeditcategory', 'admin', element.getAttribute('data-name')) :
                getString('profilecreatenewcategory', 'admin');
            const form = new ModalForm({
                formClass: 'core_user\\form\\profile_category_form',
                args: {id: element.getAttribute('data-id')},
                modalConfig: {title},
                returnFocus: element,
            });
            form.addEventListener(form.events.FORM_SUBMITTED, () => window.location.reload());
            form.show();
        }

        element = e.target.closest(Selectors.actions.editField);
        if (element) {
            e.preventDefault();
            const form = new ModalForm({
                formClass: 'core_user\\form\\profile_field_form',
                args: {id: element.getAttribute('data-id')},
                modalConfig: {title: getString('profileeditfield', 'admin', element.getAttribute('data-name'))},
                returnFocus: element,
            });
            form.addEventListener(form.events.FORM_SUBMITTED, () => window.location.reload());
            form.show();
        }

        element = e.target.closest(Selectors.actions.createField);
        if (element) {
            e.preventDefault();
            const form = new ModalForm({
                formClass: 'core_user\\form\\profile_field_form',
                args: {datatype: element.getAttribute('data-datatype'), categoryid: element.getAttribute('data-categoryid')},
                modalConfig: {title: getString('profilecreatenewfield', 'admin', element.getAttribute('data-datatypename'))},
                returnFocus: element,
            });
            form.addEventListener(form.events.FORM_SUBMITTED, () => window.location.reload());
            form.show();
        }
    });
};
