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

import * as Components from '../../components.js';

const componentData = Components.fetchComponentData();

/**
 * The standard components shipped with core Moodle.
 *
 * @type {Object}
 */
export const standardComponents = componentData.standardComponents;

/**
 * All components of the current Moodle instance.
 *
 * @type {Object}
 */
export const allComponents = componentData.components;

/**
 * Get all components of the current Moodle instance.
 *
 * @returns {Array}
 */
export const getAllComponents = () => {
    let components = new Map(Object.entries(componentData.pluginTypes).map(([value, path]) => ([path,{
        path,
        value,
        name: `${value} (plugin type)`,
    }])));

    Object
        .entries(componentData.components)
        .filter(([path, value]) => Object.values(componentData.standardComponents).includes(value))
        .forEach(([path, value]) => {
            const entry = {
                path,
                value,
                name: value,
            };
            if (Object.values(componentData.subsystems).includes(value)) {
                if (components.has(path)) {
                    entry.name = `${value} (subsystem / plugintype)`;
                } else {
                    entry.name = `${value} (subsystem)`;
                }
            }

            components.set(path, entry);
        });

        return Array.from(components.values());
};

/**
 * Whether the specified component is a standard component shipped with core Moodle.
 *
 * @param {string} componentName
 * @returns {boolean}
 */
export const isStandardComponent = (componentName) => {
    if (Object.values(componentData.standardComponents).includes(componentName)) {
        return true;
    }

    if (Object.keys(componentData.pluginTypes).includes(componentName)) {
        return true;
    }

    return false;
};

export const rewritePlugintypeAsSubsystem = (componentName) => {
    if (Object.keys(componentData.pluginTypes).includes(componentName)) {
        const pluginTypePath = componentData.pluginTypes[componentName];
        if (Object.keys(componentData.subsystems).includes(pluginTypePath)) {
            return true;
        }
    }

    return false;
};

/**
 * Whether the specified component is a community component.
 *
 * @param {string} componentName
 * @returns {boolean}
 */
export const isCommunityComponent = (componentName) => {
    if (isStandardComponent(componentName)) {
        return false;
    }

    return Object.values(componentData.components).indexOf(componentName) !== -1;
};

/**
 * Sort method for components.
 *
 * This method sorts components putting `core` first, followed by core subsystems, then everything else.
 *
 * @param {String} a
 * @param {String} b
 * @returns {Number}
 */
export const sortComponents = (a, b) => {
    // Always put 'core' first.
    if (a === 'core') {
        return -1;
    } else if (b === 'core') {
        return 1;
    }

    // Put core subsystems next.
    if (a.startsWith('core_') && !b.startsWith('core_')) {
        return -1;
    } else if (b.startsWith('core_') && !a.startsWith('core_')) {
        return 1;
    }

    // Sort alphabetically for everything else.
    return a.localeCompare(b);
};
