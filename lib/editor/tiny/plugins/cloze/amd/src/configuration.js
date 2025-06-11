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
 * Plugin tiny_cloze for TinyMCE v6 in Moodle.
 *
 * @module      tiny_cloze/configuration
 * @copyright   2023 MoodleDACH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    clozeeditButtonName,
} from './common';

import {
    addMenubarItem,
    addToolbarButtons,
} from 'editor_tiny/utils';

const getToolbarConfiguration = (instanceConfig) => {
    let toolbar = instanceConfig.toolbar;
    toolbar = addToolbarButtons(toolbar, 'content', [
        clozeeditButtonName,
    ]);

    return toolbar;
};

const getMenuConfiguration = (instanceConfig) => {
    let menu = instanceConfig.menu;
    menu = addMenubarItem(menu, 'insert', [
        clozeeditButtonName,
    ].join(' '));

    return menu;
};

export const configure = (instanceConfig) => {
    return {
        toolbar: getToolbarConfiguration(instanceConfig),
        menu: getMenuConfiguration(instanceConfig),
    };
};
