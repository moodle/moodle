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
 * Tiny Premium configuration.
 *
 * @module      tiny_premium/configuration
 * @copyright   2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    addToolbarButton,
    addMenubarItem,
    addToolbarSection,
    addContextmenuItem
} from 'editor_tiny/utils';

const configureToolbar = (toolbar) => {
    // Add premium toolbar sections to house all the plugins with no natural home.
    toolbar = addToolbarSection(toolbar, 'premium_a', 'advanced', true);
    toolbar = addToolbarSection(toolbar, 'premium_b', 'formatting', true);
    return toolbar;
};

export const configure = (instanceConfig) => {
    // There is some manipulating of the plugin menu, toolbar, context and quickbar items.
    // This was necessary to enhance user experience and closer align to the Tiny demo site.
    let plugins = instanceConfig.plugins;
    let menu = instanceConfig.menu;
    let toolbar = configureToolbar(instanceConfig.toolbar);
    let contextmenu = instanceConfig.contextmenu;
    let pluginsettings = {};

    // Advanced Table.
    plugins += ` advtable`;
    menu = addMenubarItem(menu, 'table', '| advtablerownumbering', 'advtablesort');

    // Enhanced Image Editing.
    plugins += ` editimage`;
    toolbar = addToolbarButton(toolbar, 'content', 'editimage', 'tiny_media_image');
    // Remove the duplicate image button from the quickbar toolbar by redefining the values without 'imageoptions'.
    // eslint-disable-next-line camelcase
    instanceConfig.editimage_toolbar = 'rotateleft rotateright flipv fliph editimage';

    // Export.
    plugins += ` export`;
    menu = addMenubarItem(menu, 'tools', '| export');

    // Page Embed.
    plugins += ` pageembed`;
    toolbar = addToolbarButton(toolbar, 'content', 'pageembed', 'tiny_media_video');

    // Advanced Typography.
    plugins += ` typography`;
    toolbar = addToolbarButton(toolbar, 'premium_b', 'typography');

    // Case Change.
    plugins += ` casechange`;
    toolbar = addToolbarButton(toolbar, 'premium_a', 'casechange');

    // Checklist.
    plugins += ` checklist`;
    toolbar = addToolbarButton(toolbar, 'lists', 'checklist');

    // Spell Checker Pro.
    plugins += ` tinymcespellchecker`;
    menu = addMenubarItem(menu, 'tools', 'spellcheckdialog', 'spellcheckerlanguage');
    contextmenu = addContextmenuItem(contextmenu, 'spellchecker');
    toolbar = addToolbarButton(toolbar, 'premium_a', 'spellcheckdialog');

    // Spelling Autocorrect.
    plugins += ` autocorrect`;
    menu = addMenubarItem(menu, 'tools', '| autocorrect capitalization', 'spellcheckdialog');

    // Permanent Pen.
    plugins += ` permanentpen`;
    menu = addMenubarItem(menu, 'format', '| permanentpen configurepermanentpen');
    toolbar = addToolbarButton(toolbar, 'premium_a', 'permanentpen');
    contextmenu = addContextmenuItem(contextmenu, 'configurepermanentpen');

    // Format Painter.
    plugins += ` formatpainter`;
    toolbar = addToolbarButton(toolbar, 'premium_a', 'formatpainter');

    // Link Checker.
    plugins += ` linkchecker`;
    contextmenu = addContextmenuItem(contextmenu, 'linkchecker');

    // Table of Contents.
    plugins += ` tableofcontents`;
    toolbar = addToolbarButton(toolbar, 'premium_a', 'tableofcontents');

    // Footnotes.
    plugins += ` footnotes`;
    toolbar = addToolbarButton(toolbar, 'premium_a', 'footnotes');
    menu = addMenubarItem(menu, 'insert', 'footnotes', 'tableofcontents');

    // Powerpaste.
    plugins += ` powerpaste`;

    return {
        plugins,
        toolbar,
        menu,
        contextmenu,
        ...pluginsettings
    };
};
