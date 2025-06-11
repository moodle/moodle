/* eslint-disable no-undef */
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
 * Tiny tiny_fontsize for Moodle.
 *
 * @module      plugintype_pluginname/plugin
 * @copyright   2023 Mikko Haiku <mikko.haiku@mediamaisteri.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    fontsizeButtonName,
    fontsizeMenuItemName,
} from './common';

import {
    addMenubarItem,
    addToolbarButtons,
} from 'editor_tiny/utils';

const getToolbarConfiguration = (instanceConfig) => {
    let toolbar = instanceConfig.toolbar;
    toolbar = addToolbarButtons(toolbar, 'formatting', [fontsizeButtonName]);
    return toolbar;
};

const getMenuConfiguration = (instanceConfig) => {
    let menu = instanceConfig.menu;
    menu = addMenubarItem(menu, 'format', [
        fontsizeMenuItemName,
    ].join(' '));

    return menu;
};

export const configure = (instanceConfig) => {
    return {
        toolbar: getToolbarConfiguration(instanceConfig),
        menu: getMenuConfiguration(instanceConfig),
    };
};
