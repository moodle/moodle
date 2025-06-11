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
 * tiny_fontfamily for Moodle.
 *
 * @module      tiny_fontfamily
 * @copyright   2024 Mikko Haiku <mikko.haiku@mediamaisteri.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    fontfamilyMenuItemName,
} from './common';

import {
    addMenubarItem,
} from 'editor_tiny/utils';

const getToolbarConfiguration = (instanceConfig) => {
    let toolbar = instanceConfig.toolbar;
    return toolbar;
};

const getMenuConfiguration = (instanceConfig) => {
    let menu = instanceConfig.menu;
    menu = addMenubarItem(menu, 'format', [
        fontfamilyMenuItemName,
    ].join(' '));

    return menu;
};

export const configure = (instanceConfig) => {
    return {
        toolbar: getToolbarConfiguration(instanceConfig),
        menu: getMenuConfiguration(instanceConfig),
    };
};
