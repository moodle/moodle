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
 * A registry for the different types of modal.
 *
 * @module     core/modal_registry
 * @class      modal_registry
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import * as Notification from 'core/notification';
import * as Prefetch from 'core/prefetch';

// A singleton registry for all modules to access. Allows types to be
// added at runtime.
const registry = new Map();

/**
 * Get a registered type of modal.
 *
 * @method get
 * @param {string} type The type of modal to get
 * @return {object} The registered config for the modal
 */
export const get = (type) => registry.get(type);

/**
 * Register a modal with the registry.
 *
 * @method register
 * @param {string} type The type of modal (must be unique)
 * @param {function} module The modal module (must be a constructor function of type core/modal)
 * @param {string} template The template name of the modal
 */
export const register = (type, module, template) => {
    const existing = get(type);
    if (existing && existing.module !== module) {
        Notification.exception({
            message: `Modal of  type '${type}' is already registered`,
        });
    }

    if (!module || typeof module !== 'function') {
        Notification.exception({message: "You must provide a modal module"});
    }

    if (!template) {
        Notification.exception({message: "You must provide a modal template"});
    }

    registry.set(type, {module, template});

    // Prefetch the template.
    Prefetch.prefetchTemplate(template);
};

export default {
    register,
    get,
};
