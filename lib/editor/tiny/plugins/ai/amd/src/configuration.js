// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tiny tiny_ai for Moodle.
 *
 * @module      tiny_ai/configuration
 * @copyright   2024, ISB Bayern
 * @author      Dr. Peter Mayer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    toolbarButtonName,
    selectionbarButtonName
} from 'tiny_ai/common';

import {
    addMenubarItem,
    addToolbarButtons,
    addQuickbarsToolbarItem
} from 'editor_tiny/utils';

const getToolbarConfiguration = (instanceConfig) => {
    let toolbar = instanceConfig.toolbar;

    toolbar = addToolbarButtons(toolbar, 'formatting', [
        toolbarButtonName,
    ]);

    return toolbar;
};

const getMenuConfiguration = (instanceConfig) => {
    let menu = instanceConfig.menu;
    menu = addMenubarItem(menu, 'tools', [
        toolbarButtonName,
    ].join(' '));
    return menu;
};

const getSelectionToolbarConfiguration = (instanceConfig) => {
    let toolbar = instanceConfig.quickbars_selection_toolbar;
    // The following is a dirty workaround until MDL-82724 has been integrated.
    if (toolbar === false) {
        toolbar = undefined;
    }
    toolbar = addQuickbarsToolbarItem(toolbar, '|', selectionbarButtonName);
    return toolbar;
};

export const configure = (instanceConfig) => {
    return {
        toolbar: getToolbarConfiguration(instanceConfig),
        menu: getMenuConfiguration(instanceConfig),
        // eslint-disable-next-line camelcase
        quickbars_selection_toolbar: getSelectionToolbarConfiguration(instanceConfig)
    };
};
