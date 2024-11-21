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
 * Compat.
 *
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DynamicForm from 'core_form/dynamicform';
import ModalForm from 'core_form/modalform';
import Templates from 'core/templates';

/**
 * Render a template asynchronously.
 *
 * @param {String} name
 * @param {Object} context
 */
export const asyncRender = (name, context) => {
    if ('renderForPromise' in Templates) {
        return Templates.renderForPromise(name, context);
    }
    return new Promise((resolve, reject) => {
        Templates.render(name, context).then((html, js) => {
            resolve({html, js});
            return;
        }).catch((err) => {
            reject(err);
            return;
        });
    });
};

/**
 * Get form node.
 *
 * @param {ModalForm|DynamicForm} form The form.
 * @returns {Node}
 */
export function getFormNode(form) {
    try {
        return form.getFormNode();
    } catch (e) {
        if (form instanceof ModalForm) {
            return form.modal.getRoot().find('form')[0];
        } else if (form instanceof DynamicForm) {
            return form.container.querySelector('form');
        }
        return document.createElement('form');
    }
}

/**
 * Mark the form as submitted.
 *
 * @param {Node} node A DOM node.
 */
export function markFormSubmitted(node) {
    try {
        require('core_form/changechecker', function(ChangeChecker) {
            ChangeChecker.markFormSubmitted(node);
        });
    } catch (e) {
        if (typeof M.core_formchangechecker !== 'undefined') {
            M.core_formchangechecker.set_form_submitted();
        }
    }
}
