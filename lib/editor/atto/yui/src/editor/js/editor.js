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
 * Atto editor main class.
 * Common functions required by editor plugins.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.editor_atto = M.editor_atto || {
    /**
     * List of attached button handlers to prevent duplicates.
     */
    buttonhandlers : {},

    /**
     * List of YUI overlays for custom menus.
     */
    menus : {},

    /**
     * List of attached menu handlers to prevent duplicates.
     */
    menuhandlers : {},

    /**
     * List of file picker options for specific editor instances.
     */
    filepickeroptions : {},

    /**
     * List of buttons and menus that have been added to the toolbar.
     */
    widgets : {},

    /**
     * Toggle a menu.
     * @param event e
     */
    showhide_menu_handler : function(e) {
        e.preventDefault();
        var disabled = this.getAttribute('disabled');
        var overlayid = this.getAttribute('data-menu');
        var overlay = M.editor_atto.menus[overlayid];
        var menu = overlay.get('bodyContent');
        if (overlay.get('visible') || disabled) {
            overlay.hide();
            menu.detach('clickoutside');
        } else {
            menu.on('clickoutside', function(ev) {
                if (ev.target.ancestor() !== this) {
                    if (overlay.get('visible')) {
                        menu.detach('clickoutside');
                        overlay.hide();
                    }
                }
            }, this);
            overlay.show();
        }
    },

    /**
     * Handle clicks on editor buttons.
     * @param event e
     */
    buttonclicked_handler : function(e) {
        var elementid = this.getAttribute('data-editor');
        var plugin = this.getAttribute('data-plugin');
        var handler = this.getAttribute('data-handler');
        var overlay = M.editor_atto.menus[plugin + '_' + elementid];

        if (overlay) {
            overlay.hide();
        }

        if (M.editor_atto.is_enabled(elementid, plugin)) {
            // Pass it on.
            handler = M.editor_atto.buttonhandlers[handler];
            return handler(e, elementid);
        }
    },

    /**
     * Determine if the specified toolbar button/menu is enabled.
     * @param string elementid, the element id of this editor.
     * @param string plugin, the plugin that created the button/menu.
     */
    is_enabled : function(elementid, plugin) {
        var element = Y.one('#' + elementid + '_toolbar .atto_' + plugin + '_button');

        return !element.hasAttribute('disabled');
    },
    /**
     * Disable all buttons and menus in the toolbar.
     * @param string elementid, the element id of this editor.
     */
    disable_all_widgets : function(elementid) {
        var plugin, element;
        for (plugin in M.editor_atto.widgets) {
            element = Y.one('#' + elementid + '_toolbar .atto_' + plugin + '_button');

            if (element) {
                element.setAttribute('disabled', 'true');
            }
        }
    },

    /**
     * Enable a single widget in the toolbar.
     * @param string elementid, the element id of this editor.
     * @param string plugin, the name of the plugin that created the widget.
     */
    enable_widget : function(elementid, plugin) {
        var element = Y.one('#' + elementid + '_toolbar .atto_' + plugin + '_button');

        if (element) {
            element.removeAttribute('disabled');
        }
    },

    /**
     * Enable all buttons and menus in the toolbar.
     * @param string elementid, the element id of this editor.
     */
    enable_all_widgets : function(elementid) {
        var plugin, element;
        for (plugin in M.editor_atto.widgets) {
            element = Y.one('#' + elementid + '_toolbar .atto_' + plugin + '_button');

            if (element) {
                element.removeAttribute('disabled');
            }
        }
    },

    /**
     * Add a button to the toolbar belonging to the editor for element with id "elementid".
     * @param string elementid - the id of the textarea we created this editor from.
     * @param string plugin - the plugin defining the button
     * @param string icon - the html used for the content of the button
     * @handler function handler- A function to call when the button is clicked.
     */
    add_toolbar_menu : function(elementid, plugin, icon, entries) {
        var toolbar = Y.one('#' + elementid + '_toolbar');
        var button = Y.Node.create('<button class="atto_' + plugin + '_button atto_hasmenu" ' +
                                    'data-editor="' + Y.Escape.html(elementid) + '" ' +
                                    'data-menu="' + plugin + '_' + elementid + '" >' +
                                    icon +
                                    '</button>');

        toolbar.append(button);

        // Save the name of the plugin.
        M.editor_atto.widgets[plugin] = plugin;

        var menu = Y.Node.create('<div class="atto_' + plugin + '_menu' +
                                 ' atto_menu" data-editor="' + Y.Escape.html(elementid) + '"></div>');
        var i = 0, entry = {};

        for (i = 0; i < entries.length; i++) {
            entry = entries[i];

            menu.append(Y.Node.create('<div class="atto_menuentry">' +
                                       '<a href="#" class="atto_' + plugin + '_action_' + i + '" ' +
                                       'data-editor="' + Y.Escape.html(elementid) + '" ' +
                                       'data-plugin="' + Y.Escape.html(plugin) + '" ' +
                                       'data-handler="' + Y.Escape.html(plugin + '_action_' + i) + '">' +
                                       entry.text +
                                       '</a>' +
                                       '</div>'));
            if (!M.editor_atto.buttonhandlers[plugin + '_action_' + i]) {
                Y.one('body').delegate('click', M.editor_atto.buttonclicked_handler, '.atto_' + plugin + '_action_' + i);
                M.editor_atto.buttonhandlers[plugin + '_action_' + i] = entry.handler;
            }
        }

        if (!M.editor_atto.buttonhandlers[plugin]) {
            Y.one('body').delegate('click', M.editor_atto.showhide_menu_handler, '.atto_' + plugin + '_button');
            M.editor_atto.buttonhandlers[plugin] = true;
        }

        var overlay = new M.core.dialogue({
            bodyContent : menu,
            visible : false,
            width: '14em',
            zindex: 100,
            lightbox: false,
            closeButton: false,
            centered : false,
            align: {node: button, points: [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]}
        });

        M.editor_atto.menus[plugin + '_' + elementid] = overlay;
        overlay.render();
        overlay.hide();
        overlay.headerNode.hide();
    },

    /**
     * Add a button to the toolbar belonging to the editor for element with id "elementid".
     * @param string elementid - the id of the textarea we created this editor from.
     * @param string plugin - the plugin defining the button
     * @param string icon - the html used for the content of the button
     * @handler function handler- A function to call when the button is clicked.
     */
    add_toolbar_button : function(elementid, plugin, icon, handler) {
        var toolbar = Y.one('#' + elementid + '_toolbar');
        var button = Y.Node.create('<button class="atto_' + plugin + '_button" ' +
                                   'data-editor="' + Y.Escape.html(elementid) + '" ' +
                                   'data-plugin="' + Y.Escape.html(plugin) + '" ' +
                                   'data-handler="' + Y.Escape.html(plugin) + '">' +
                                    icon +
                                    '</button>');

        toolbar.append(button);

        // We only need to attach this once.
        if (!M.editor_atto.buttonhandlers[plugin]) {
            Y.one('body').delegate('click', M.editor_atto.buttonclicked_handler, '.atto_' + plugin + '_button');
            M.editor_atto.buttonhandlers[plugin] = handler;
        }

        // Save the name of the plugin.
        M.editor_atto.widgets[plugin] = plugin;

    },

    /**
     * Work out if the cursor is in the editable area for this editor instance.
     * @param string elementid of this editor
     * @return bool
     */
    is_active : function(elementid) {
        var selection = M.editor_atto.get_selection();

        if (selection.length) {
            selection = selection.pop();
        }

        var node = null;
        if (selection.parentElement) {
            node = Y.one(selection.parentElement());
        } else {
            node = Y.one(selection.startContainer);
        }

        return node && node.ancestor('#' + elementid + 'editable') !== null;
    },

    /**
     * Focus on the editable area for this editor.
     * @param string elementid of this editor
     */
    focus : function(elementid) {
        Y.one('#' + elementid + 'editable').focus();
    },

    /**
     * Initialise the editor
     * @param object params for this editor instance.
     */
    init : function(params) {
        var textarea = Y.one('#' +params.elementid);
        var atto = Y.Node.create('<div id="' + params.elementid + 'editable" ' +
                                            'contenteditable="true" ' +
                                            'spellcheck="true" ' +
                                            'class="editor_atto"/>');
        var cssfont = '';
        var toolbar = Y.Node.create('<div class="editor_atto_toolbar" id="' + params.elementid + '_toolbar"/>');

        // Bleh - why are we sent a url and not the css to apply directly?
        var css = Y.io(params.content_css, { sync: true });
        var pos = css.responseText.indexOf('font:');
        if (pos) {
            cssfont = css.responseText.substring(pos + 'font:'.length, css.responseText.length - 1);
            atto.setStyle('font', cssfont);
        }
        atto.setStyle('minHeight', (1.2 * (textarea.getAttribute('rows') - 1)) + 'em');

        // Copy text to editable div.
        atto.append(textarea.get('value'));

        // Add the toolbar to the page.
        textarea.get('parentNode').insert(toolbar, textarea);
        // Add the editable div to the page.
        textarea.get('parentNode').insert(atto, textarea);
        atto.setStyle('color', textarea.getStyle('color'));
        atto.setStyle('lineHeight', textarea.getStyle('lineHeight'));
        atto.setStyle('fontSize', textarea.getStyle('fontSize'));
        // Hide the old textarea.
        textarea.hide();

        // Copy the current value back to the textarea when focus leaves us.
        atto.on('blur', function() {
            textarea.set('value', atto.getHTML());
        });

        // Save the file picker options for later.
        M.editor_atto.filepickeroptions[params.elementid] = params.filepickeroptions;
    },

    /**
     * Show the filepicker.
     * @param string elementid for this editor instance.
     * @param string type The media type for the file picker
     * @param function callback
     */
    show_filepicker : function(elementid, type, callback) {
        Y.use('core_filepicker', function (Y) {
            var options = M.editor_atto.filepickeroptions[elementid][type];

            options.formcallback = callback;
            options.editor_target = Y.one(elementid);

            M.core_filepicker.show(Y, options);
        });
    },

    /**
     * Create a cross browser selection object that represents a yui node.
     * @param Node yui node for the selection
     * @return range (browser dependent)
     */
    get_selection_from_node: function(node) {
        var range;

        if (window.getSelection) {
            range = document.createRange();

            range.setStartBefore(node.getDOMNode());
            range.setEndAfter(node.getDOMNode());
            return [range];
        } else if (document.selection) {
            range = document.body.createTextRange();
            range.moveToElementText(node.getDOMNode());
            return range;
        }
        return false;
    },

    /**
     * Get the selection object that can be passed back to set_selection.
     * @return range (browser dependent)
     */
    get_selection : function() {
        if (window.getSelection) {
            var sel = window.getSelection();
            var ranges = [], i = 0;
            for (i = 0; i < sel.rangeCount; i++) {
                ranges.push(sel.getRangeAt(i));
            }
            return ranges;
        } else if (document.selection) {
            // IE < 9
            if (document.selection.createRange) {
                return document.selection.createRange();
            }
        }
        return false;
    },

    /**
     * Get the dom node representing the common anscestor of the selection nodes.
     * @return DOMNode
     */
    get_selection_parent_node : function() {
        var selection = M.editor_atto.get_selection();
        if (selection.length > 0) {
            return selection[0].commonAncestorContainer;
        }
    },

    /**
     * Get the list of child nodes of the selection.
     * @return DOMNode[]
     */
    get_selection_text : function() {
        var selection = M.editor_atto.get_selection();
        if (selection.length > 0 && selection[0].cloneContents) {
            return selection[0].cloneContents();
        }
    },

    /**
     * Set the current selection. Used to restore a selection.
     */
    set_selection : function(selection) {
        var sel, i;

        if (window.getSelection) {
            sel = window.getSelection();
            sel.removeAllRanges();
            for (i = 0; i < selection.length; i++) {
                sel.addRange(selection[i]);
            }
        } else if (document.selection) {
            // IE < 9
            if (selection.select) {
                selection.select();
            }
        }
    }

};
