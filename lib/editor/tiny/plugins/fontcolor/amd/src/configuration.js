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
 * Tiny tiny_fontcolor for Moodle.
 *
 * @module      tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {addMenubarItem, addContextmenuItem, addToolbarButtons} from 'editor_tiny/utils';
import {forecolor, backcolor} from './common';

const configureToolbar = (toolbar) => {
    toolbar = addToolbarButtons(toolbar, 'formatting', [forecolor, backcolor]);
    return toolbar;
};

const configureMenu = (menu) => {
    menu = addMenubarItem(menu, 'format', forecolor);
    menu = addMenubarItem(menu, 'format', backcolor);
    return menu;
};

const configureContextMenu = (menu) => {
    if (!menu) {
        menu = '';
    }
    return addContextmenuItem(menu, '|', forecolor, backcolor);
};

export const configure = (instanceConfig) => {
    return {
        toolbar: configureToolbar(instanceConfig.toolbar),
        menu: configureMenu(instanceConfig.menu),
        // eslint-disable-next-line camelcase
        quickbars_selection_toolbar: configureContextMenu(instanceConfig.quickbars_selection_toolbar),
    };
};
