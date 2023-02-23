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

import {renderForPromise} from 'core/templates';
import {getFilePicker} from './options';
import {get_string as getString} from 'core/str';

/**
 * Get the image path for the specified image.
 *
 * @param {string} identifier The name of the image
 * @param {string} component The component name
 * @return {string} The image URL path
 */
export const getImagePath = (identifier, component = 'editor_tiny') => Promise.resolve(M.util.image_url(identifier, component));

export const getButtonImage = async(identifier, component = 'editor_tiny') => renderForPromise('editor_tiny/toolbar_button', {
    image: await getImagePath(identifier, component),
});

/**
 * Helper to display a filepicker and return a Promise.
 *
 * The Promise will resolve when a file is selected, or reject if the file type is not found.
 *
 * @param {TinyMCE} editor
 * @param {string} filetype
 * @returns {Promise<object>} The file object returned by the filepicker
 */
export const displayFilepicker = (editor, filetype) => new Promise((resolve, reject) => {
    const configuration = getFilePicker(editor, filetype);
    if (configuration) {
        const options = {
            ...configuration,
            formcallback: resolve,
        };
        M.core_filepicker.show(Y, options);
        return;
    }
    reject(`Unknown filetype ${filetype}`);
});

/**
 * Given a TinyMCE Toolbar configuration, add the specified button to the named section.
 *
 * @param {object} toolbar
 * @param {string} section
 * @param {string} button
 * @returns {object} The toolbar configuration
 */
export const addToolbarButton = (toolbar, section, button) => {
    if (!toolbar) {
        return [{
            name: section,
            items: [button],
        }];
    }

    const mutatedToolbar = JSON.parse(JSON.stringify(toolbar));
    return mutatedToolbar.map((item) => {
        if (item.name === section) {
            item.items.push(button);
        }

        return item;
    });
};

/**
 * Given a TinyMCE Toolbar configuration, add the specified buttons to the named section.
 *
 * @param {object} toolbar
 * @param {string} section
 * @param {Array} buttons
 * @returns {object} The toolbar configuration
 */
export const addToolbarButtons = (toolbar, section, buttons) => {
    if (!toolbar) {
        return [{
            name: section,
            items: buttons,
        }];
    }

    const mutatedToolbar = JSON.parse(JSON.stringify(toolbar));
    return mutatedToolbar.map((item) => {
        if (item.name === section) {
            buttons.forEach(button => item.items.push(button));
        }

        return item;
    });
};

/**
 * Insert a new section into the toolbar.
 *
 * @param {array} toolbar The TinyMCE.editor.settings.toolbar configuration
 * @param {string} name The new section name to add
 * @param {string} relativeTo Insert relative to this section name
 * @param {boolean} append Append or Prepend
 * @returns {array}
 */
export const addToolbarSection = (toolbar, name, relativeTo, append = true) => {
    const newSection = {
        name,
        items: [],
    };
    const sectionInserted = toolbar.some((section, index) => {
        if (section.name === relativeTo) {
            if (append) {
                toolbar.splice(index + 1, 0, newSection);
            } else {
                toolbar.splice(index, 0, newSection);
            }
            return true;
        }
        return false;
    });

    if (!sectionInserted) {
        // Relative section not found.
        if (append) {
            toolbar.push(newSection);
        } else {
            toolbar.unshift(newSection);
        }
    }

    return toolbar;
};

/**
 * Given a TinyMCE Menubar configuration, add the specified button to the named section.
 *
 * @param {object} menubar
 * @param {string} section
 * @param {string} menuitem
 * @returns {object}
 */
export const addMenubarItem = (menubar, section, menuitem) => {
    if (!menubar) {
        const emptyMenubar = {};
        emptyMenubar[section] = {
            title: section,
            items: menuitem,
        };
    }

    const mutatedMenubar = JSON.parse(JSON.stringify(menubar));
    Array.from(Object.entries(mutatedMenubar)).forEach(([name, menu]) => {
        if (name === section) {
            menu.items = `${menu.items} ${menuitem}`;
        }
    });

    return mutatedMenubar;
};

/**
 * Given a TinyMCE contextmenu configuration, add the specified button to the end.
 *
 * @param {string} contextmenu
 * @param {string[]} menuitems
 * @returns {string}
 */
export const addContextmenuItem = (contextmenu, ...menuitems) => {
    const contextmenuItems = (contextmenu ?? '').split(' ');

    return contextmenuItems
        .concat(menuitems)
        .filter((item) => item !== '')
        .join(' ');
};

/**
 * Given a TinyMCE quickbars configuration, add items to the meun.
 *
 * @param {string} toolbar
 * @param {string[]} menuitems
 * @returns {string}
 */
// eslint-disable-next-line no-unused-vars
export const addQuickbarsToolbarItem = (toolbar, ...menuitems) => {
    // For the moment we have disabled use of this menu.
    // The configuration is left in place to allow plugins to declare that they would like to support it in the future.
    return toolbar;
};

/**
 * Get the link to the user documentation for the named plugin.
 *
 * @param {string} pluginName
 * @returns {string}
 */
export const getDocumentationLink = (pluginName) => `https://docs.moodle.org/en/editor_tiny/${pluginName}`;

/**
 * Get the default plugin metadata for the named plugin.
 * If no URL is provided, then a URL is generated pointing to the standard Moodle Documentation.
 *
 * @param {string} component The component name
 * @param {string} pluginName The plugin name
 * @param {string|null} [url=null] An optional URL to the plugin documentation
 * @returns {object}
 */
export const getPluginMetadata = async(component, pluginName, url = null) => {
    const name = await getString('helplinktext', component);
    return {
        getMetadata: () => ({
            name,
            url: url ?? getDocumentationLink(pluginName),
        }),
    };
};

/**
 * Ensure that the editor is still in the DOM, removing it if it is not.
 *
 * @param {TinyMCE} editor
 * @returns {TinyMCE|null}
 */
export const ensureEditorIsValid = (editor) => {
    // TinyMCE uses the element ID as a map key internally, even if the target has changed.
    // In cases such as where an editor is in a modal form which has been detached from the DOM, but the editor not removed,
    // we need to manually destroy the editor.
    // We could theoretically do this with a Mutation Observer, but in some cases the Node may be moved,
    // or added back elsewhere in the DOM.
    if (!editor?.targetElm?.closest('body')) {
        editor.destroy();
        return null;
    }

    return editor;
};

/**
 * Given a TinyMCE Toolbar configuration, remove the specified button from the named section.
 *
 * @param {object} toolbar
 * @param {string} section
 * @param {string} button
 * @returns {object} The toolbar configuration
 */
 export const removeToolbarButton = (toolbar, section, button) => {
    if (!toolbar) {
        return [{
            name: section,
            items: [button],
        }];
    }

    const mutatedToolbar = JSON.parse(JSON.stringify(toolbar));
    return mutatedToolbar.map((item) => {
        if (item.name === section) {
            item.items.splice(item.items.indexOf(button), 1);
        }

        return item;
    });
};

/**
 * Given a TinyMCE Toolbar configuration, remove the specified buttons from the named section.
 *
 * @param {object} toolbar
 * @param {string} section
 * @param {Array} buttons
 * @returns {object} The toolbar configuration
 */
 export const removeToolbarButtons = (toolbar, section, buttons) => {
    if (!toolbar) {
        return [{
            name: section,
            items: buttons,
        }];
    }

    const mutatedToolbar = JSON.parse(JSON.stringify(toolbar));
    return mutatedToolbar.map((item) => {
        if (item.name === section) {
            buttons.forEach(button => item.items.splice(item.items.indexOf(button), 1));
        }

        return item;
    });
};

/**
 * Remove the specified sub-menu item from the named section.
 * Recreate a menu with the same sub-menu items but remove the specified item.
 *
 * @param {TinyMCE} editor
 * @param {string} section
 * @param {string} submenuitem The text of sub-menu that we want to removed
 */
export const removeSubmenuItem = async(editor, section, submenuitem) => {
    // Get menu items.
    const menuItems = editor.ui.registry.getAll().menuItems[section];

    // Because we will match between title strings,
    // we make sure no problems arise while applying multi-language.
    const submenuitemtitle = await getString(submenuitem, 'editor_tiny');

    // Overriding the menu items,
    // by recreating them but excluding the specified sub-menu.
    if (menuItems) {
        editor.ui.registry.addNestedMenuItem(
            section,
            {
                text: menuItems.text,
                getSubmenuItems: () => {
                    let newSubmenu = [];
                    menuItems.getSubmenuItems().forEach((item) => {
                        // Need to trim the text because some of the sub-menus use space to replace an icon.
                        if (item.text.trim() != submenuitemtitle) {
                            newSubmenu.push(item);
                        }
                    });
                    return newSubmenu;
                }
            }
        );
    }
};

/**
 * Given a TinyMCE Menubar configuration, remove the specified menu from the named section.
 *
 * @param {string} menubar
 * @param {string} section
 * @param {string} menuitem
 * @returns {object}
 */
export const removeMenubarItem = (menubar, section, menuitem) => {
    menubar[section].items = menubar[section].items
        .replace(menuitem, '');

    return menubar;
};

/**
 * Given a TinyMCE Menubar configuration, remove the specified menu from the named section.
 *
 * @param {string} menubar
 * @param {string} section
 * @param {Array} menuitems
 * @returns {object}
 */
export const removeMenubarItems = (menubar, section, menuitems) => {
    // Create RegExp pattern.
    const regexPattern = new RegExp(menuitems.join('|'), "ig");

    // Remove menuitems.
    menubar[section].items = menubar[section].items.replace(regexPattern, '');

    return menubar;
};
