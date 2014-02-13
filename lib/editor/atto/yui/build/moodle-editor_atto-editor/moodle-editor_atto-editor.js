YUI.add('moodle-editor_atto-editor', function (Y, NAME) {

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
 * Atto editor.
 *
 * @package    editor_atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Classes constants.
 */
CSS = {
    CONTENT: 'editor_atto_content',
    CONTENTWRAPPER: 'editor_atto_content_wrap',
    TOOLBAR: 'editor_atto_toolbar',
    WRAPPER: 'editor_atto'
};

/**
 * Atto editor main class.
 * Common functions required by editor plugins.
 *
 * @package    editor_atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.editor_atto = M.editor_atto || {

    /**
     * List of known block level tags.
     * Taken from "https://developer.mozilla.org/en-US/docs/HTML/Block-level_elements".
     *
     * @type {Array}
     */
    BLOCK_TAGS : [
        'address',
        'article',
        'aside',
        'audio',
        'blockquote',
        'canvas',
        'dd',
        'div',
        'dl',
        'fieldset',
        'figcaption',
        'figure',
        'footer',
        'form',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'header',
        'hgroup',
        'hr',
        'noscript',
        'ol',
        'output',
        'p',
        'pre',
        'section',
        'table',
        'tfoot',
        'ul',
        'video'],

    /**
     * List of attached button handlers to prevent duplicates.
     */
    buttonhandlers : {},

    /**
     * List of attached handlers to add inline editing controls to content.
     */
    textupdatedhandlers : {},

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
     * List of saved selections per editor instance.
     */
    selections : {},

    focusfromclick : false,

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
                if ((ev.target.ancestor() !== this) && (ev.target !== this)) {
                    if (overlay.get('visible')) {
                        menu.detach('clickoutside');
                        overlay.hide();
                    }
                }
            }, this);

            overlay.align(Y.one(Y.config.doc.body), [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);
            overlay.show();
            var icon = e.target.ancestor('button', true).one('img');
            overlay.align(icon, [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);
            overlay.get('boundingBox').one('a').focus();
        }
    },

    /**
     * Handle clicks on editor buttons.
     * @param event e
     */
    buttonclicked_handler : function(e) {
        var elementid = this.getAttribute('data-editor');
        var plugin = this.getAttribute('data-plugin');
        var button = this.getAttribute('data-button');
        var handler = this.getAttribute('data-handler');
        var overlay = M.editor_atto.menus[plugin + '_' + elementid];
        var toolbar = M.editor_atto.get_toolbar_node(elementid);
        var currentid = toolbar.getAttribute('aria-activedescendant');

        // Right now, currentid is the id of the previously selected button.
        if (currentid) {
            current = Y.one('#' + currentid);
            // We only ever want one button with a tabindex of 0 at any one time.
            current.setAttribute('tabindex', '-1');
        }
        this.setAttribute('tabindex', 0);
        // And update the activedescendant to point at the currently selected button.
        toolbar.setAttribute('aria-activedescendant', this.generateID());

        if (overlay) {
            overlay.hide();
        }

        if (M.editor_atto.is_enabled(elementid, plugin, button)) {
            // Pass it on.
            handler = M.editor_atto.buttonhandlers[handler];
            return handler(e, elementid);
        }
    },

    /**
     * Disable all buttons and menus in the toolbar.
     * @param string elementid, the element id of this editor.
     */
    disable_all_widgets : function(elementid) {
        var plugin, element, toolbar = M.editor_atto.get_toolbar_node(elementid);
        for (plugin in M.editor_atto.widgets) {
            element = toolbar.one('.atto_' + plugin + '_button');

            if (element) {
                element.setAttribute('disabled', 'true');
            }
        }
    },

    /**
     * Get the node of the original textarea element that this editor replaced.
     *
     * @param string elementid, the element id of this editor.
     * @return Y.Node
     */
    get_textarea_node : function(elementid) {
        // Note - it is not safe to use a CSS selector like '#' + elementid
        // because the id may have colons in it - e.g. quiz.
        return Y.one(document.getElementById(elementid));
    },

    /**
     * Get the node of the toolbar container for this editor.
     *
     * @param string elementid, the element id of this editor.
     * @return Y.Node
     */
    get_toolbar_node : function(elementid) {
        // Note - it is not safe to use a CSS selector like '#' + elementid
        // because the id may have colons in it - e.g. quiz.
        return Y.one(document.getElementById(elementid + '_toolbar'));
    },

    /**
     * Get the node of the contenteditable container for this editor.
     *
     * @param string elementid, the element id of this editor.
     * @return Y.Node
     */
    get_editable_node : function(elementid) {
        // Note - it is not safe to use a CSS selector like '#' + elementid
        // because the id may have colons in it - e.g. quiz.
        return Y.one(document.getElementById(elementid + 'editable'));
    },

    /**
     * Determine if the specified toolbar button/menu is enabled.
     * @param string elementid, the element id of this editor.
     * @param string plugin, the plugin that created the button/menu.
     * @param string buttonname, optional - used when a plugin has multiple buttons.
     */
    is_enabled : function(elementid, plugin, button) {
        var buttonpath = plugin;
        if (button) {
            buttonpath += '_' + button;
        }
        var element = M.editor_atto.get_toolbar_node(elementid).one('.atto_' + buttonpath + '_button');

        return !element.hasAttribute('disabled');
    },

    /**
     * Enable a single widget in the toolbar.
     * @param string elementid, the element id of this editor.
     * @param string plugin, the name of the plugin that created the widget.
     * @param string buttonname, optional - used when a plugin has multiple buttons.
     */
    enable_widget : function(elementid, plugin, button) {
        var buttonpath = plugin;
        if (button) {
            buttonpath += '_' + button;
        }
        var element = M.editor_atto.get_toolbar_node(elementid).one('.atto_' + buttonpath + '_button');

        if (element) {
            element.removeAttribute('disabled');
        }
    },

    /**
     * Enable all buttons and menus in the toolbar.
     * @param string elementid, the element id of this editor.
     */
    enable_all_widgets : function(elementid) {
        var path, element;
        for (path in M.editor_atto.widgets) {
            element = M.editor_atto.get_toolbar_node(elementid).one('.atto_' + path + '_button');

            if (element) {
                element.removeAttribute('disabled');
            }
        }
    },

    /**
     * Add a content update handler to be called whenever the content is updated.
     * This is used to add inline editing controls to the content that are cleaned on submission.
     *
     * @param string elementid - the id of the textarea we created this editor from.
     * @handler function callback - The function to do the cleaning.
     * @param object context - the context to set for the callback.
     * @handler function handler - A function to call when the button is clicked.
     */
    add_text_updated_handler : function(elementid, callback) {
        if (!(elementid in M.editor_atto.textupdatedhandlers)) {
            M.editor_atto.textupdatedhandlers[elementid] = [];
        }
        M.editor_atto.textupdatedhandlers[elementid].push(callback);
    },

    /**
     * Add a button to the toolbar belonging to the editor for element with id "elementid".
     * @param string elementid - the id of the textarea we created this editor from.
     * @param string plugin - the plugin defining the button
     * @param string icon - the html used for the content of the button
     * @param string groupname - the group the button should be appended to.
     * @param array entries - List of menu entries with the string (entry.text) and the handlers (entry.handler).
     * @param string buttonname - (optional) a name for the button. Required if a plugin creates more than one button.
     * @param string buttontitle - (optional) a title for the button. Required if a plugin creates more than one button.
     * @param int overlaywidth - the overlay width size in 'ems'.
     * @param string menucolor - menu icon background color
     */
    add_toolbar_menu : function(elementid, plugin, iconurl, groupname, entries, buttonname, buttontitle, overlaywidth, menucolor) {
        var toolbar = M.editor_atto.get_toolbar_node(elementid),
            group = toolbar.one('.atto_group.' + groupname + '_group'),
            currentfocus,
            button,
            buttonpath,
            expimgurl;

        if (buttonname) {
            buttonpath = plugin + '_' + buttonname;
        } else {
            buttonname = '';
            buttonpath = plugin;
        }

        if (!buttontitle) {
            buttontitle = M.util.get_string('pluginname', 'atto_' + plugin);
        }

        if ((typeof overlaywidth) === 'undefined') {
            overlaywidth = '14';
        }
        if ((typeof menucolor) === 'undefined') {
            menucolor = 'transparent';
        }

        if (!group) {
            group = Y.Node.create('<div class="atto_group ' + groupname + '_group"></div>');
            toolbar.append(group);
        }
        expimgurl = M.util.image_url('t/expanded', 'moodle');
        button = Y.Node.create('<button class="atto_' + buttonpath + '_button atto_hasmenu" ' +
                                    'data-editor="' + Y.Escape.html(elementid) + '" ' +
                                    'tabindex="-1" ' +
                                    'type="button" ' +
                                    'data-menu="' + buttonpath + '_' + elementid + '" ' +
                                    'title="' + Y.Escape.html(buttontitle) + '">' +
                                    '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" '+
                                    'style="background-color:' + menucolor + ';" src="' + iconurl + '"/>' +
                                    '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" src="' + expimgurl + '"/>' +
                                    '</button>');

        group.append(button);

        currentfocus = toolbar.getAttribute('aria-activedescendant');
        if (!currentfocus) {
            // Initially set the first button in the toolbar to be the default on keyboard focus.
            button.setAttribute('tabindex', '0');
            toolbar.setAttribute('aria-activedescendant', button.generateID());
        }

        // Save the name of the plugin.
        M.editor_atto.widgets[buttonpath] = buttonpath;

        var menu = Y.Node.create('<div class="atto_' + buttonpath + '_menu' +
                                 ' atto_menu" data-editor="' + Y.Escape.html(elementid) + '"' +
                                 ' style="min-width:' + (overlaywidth-2) + 'em"' +
                                 '"></div>');
        var i = 0, entry = {};

        for (i = 0; i < entries.length; i++) {
            entry = entries[i];

            menu.append(Y.Node.create('<div class="atto_menuentry">' +
                                       '<a href="#" class="atto_' + buttonpath + '_action_' + i + '" ' +
                                       'data-editor="' + Y.Escape.html(elementid) + '" ' +
                                       'data-plugin="' + Y.Escape.html(plugin) + '" ' +
                                       'data-button="' + Y.Escape.html(buttonname) + '" ' +
                                       'data-handler="' + Y.Escape.html(buttonpath + '_action_' + i) + '">' +
                                       entry.text +
                                       '</a>' +
                                       '</div>'));
            if (!M.editor_atto.buttonhandlers[plugin + '_action_' + i]) {
                Y.one('body').delegate('click', M.editor_atto.buttonclicked_handler, '.atto_' + buttonpath + '_action_' + i);
                // Activate the link on space or enter.
                Y.one('body').delegate('key', M.editor_atto.buttonclicked_handler, '32,enter', '.atto_' + buttonpath + '_action_' + i);
                M.editor_atto.buttonhandlers[buttonpath + '_action_' + i] = entry.handler;
            }
        }

        if (!M.editor_atto.buttonhandlers[buttonpath]) {
            Y.one('body').delegate('click', M.editor_atto.showhide_menu_handler, '.atto_' + buttonpath + '_button');
            M.editor_atto.buttonhandlers[buttonpath] = true;
        }

        var overlay = new M.core.dialogue({
            bodyContent : menu,
            visible : false,
            width: overlaywidth + 'em',
            lightbox: false,
            closeButton: false,
            center : false
        });

        M.editor_atto.menus[buttonpath + '_' + elementid] = overlay;
        overlay.align(button, [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);
        overlay.hide();
        overlay.headerNode.hide();
        overlay.render();
    },

    /**
     * Add a button to the toolbar belonging to the editor for element with id "elementid".
     * @param string elementid - the id of the textarea we created this editor from.
     * @param string plugin - the plugin defining the button.
     * @param string icon - the url to the image for the icon
     * @param string groupname - the group the button should be appended to.
     * @handler function handler- A function to call when the button is clicked.
     * @param string buttonname - (optional) a name for the button. Required if a plugin creates more than one button.
     * @param string buttontitle - (optional) a title for the button. Required if a plugin creates more than one button.
     */
    add_toolbar_button : function(elementid, plugin, iconurl, groupname, handler, buttonname, buttontitle) {
        var toolbar = M.editor_atto.get_toolbar_node(elementid),
            group = toolbar.one('.atto_group.' + groupname + '_group'),
            button,
            buttonpath,
            currentfocus;

        if (buttonname) {
            buttonpath = plugin + '_' + buttonname;
        } else {
            buttonname = '';
            buttonpath = plugin;
        }

        if (!buttontitle) {
            buttontitle = M.util.get_string('pluginname', 'atto_' + plugin);
        }

        if (!group) {
            group = Y.Node.create('<div class="atto_group ' + groupname +'_group"></div>');
            toolbar.append(group);
        }
        button = Y.Node.create('<button class="atto_' + buttonpath + '_button" ' +
                               'data-editor="' + Y.Escape.html(elementid) + '" ' +
                               'data-plugin="' + Y.Escape.html(plugin) + '" ' +
                               'data-button="' + Y.Escape.html(buttonname) + '" ' +
                               'tabindex="-1" ' +
                               'data-handler="' + Y.Escape.html(buttonpath) + '" ' +
                               'title="' + Y.Escape.html(buttontitle) + '">' +
                                    '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" src="' + iconurl + '"/>' +
                               '</button>');

        group.append(button);

        currentfocus = toolbar.getAttribute('aria-activedescendant');
        if (!currentfocus) {
            // Initially set the first button in the toolbar to be the default on keyboard focus.
            button.setAttribute('tabindex', '0');
            toolbar.setAttribute('aria-activedescendant', button.generateID());
        }

        // We only need to attach this once.
        if (!M.editor_atto.buttonhandlers[buttonpath]) {
            Y.one('body').delegate('click', M.editor_atto.buttonclicked_handler, '.atto_' + buttonpath + '_button');
            M.editor_atto.buttonhandlers[buttonpath] = handler;
        }

        // Save the name of the plugin.
        M.editor_atto.widgets[buttonpath] = buttonpath;

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

        var editable = M.editor_atto.get_editable_node(elementid);

        return node && editable.contains(node);
    },

    /**
     * Focus on the editable area for this editor.
     * @param string elementid of this editor
     */
    focus : function(elementid) {
        M.editor_atto.get_editable_node(elementid).focus();
    },

    /**
     * Initialise the editor
     * @param object params for this editor instance.
     */
    init : function(params) {
        var wrapper = Y.Node.create('<div class="' + CSS.WRAPPER + '" />');
        var atto = Y.Node.create('<div id="' + params.elementid + 'editable" ' +
                                            'contenteditable="true" ' +
                                            'role="textbox" ' +
                                            'spellcheck="true" ' +
                                            'aria-live="off" ' +
                                            'class="' + CSS.CONTENT + '" />');

        var toolbar = Y.Node.create('<div class="' + CSS.TOOLBAR + '" id="' + params.elementid + '_toolbar" role="toolbar" aria-live="off"/>');

        // Editable content wrapper.
        var content = Y.Node.create('<div class="' + CSS.CONTENTWRAPPER + '" />');
        var textarea = M.editor_atto.get_textarea_node(params.elementid);
        var label = Y.one('[for="' + params.elementid + '"]');

        // Add a labelled-by attribute to the contenteditable.
        if (label) {
            label.generateID();
            atto.setAttribute('aria-labelledby', label.get("id"));
            toolbar.setAttribute('aria-labelledby', label.get("id"));
        }

        content.appendChild(atto);

        // Add everything to the wrapper.
        wrapper.appendChild(toolbar);
        wrapper.appendChild(content);

        // Style the editor.
        atto.setStyle('minHeight', (1.2 * (textarea.getAttribute('rows'))) + 'em');

        // Copy text to editable div.
        atto.append(textarea.get('value'));

        // Clean it.
        atto.cleanHTML();

        // Add the toolbar and editable zone to the page.
        textarea.get('parentNode').insert(wrapper, textarea);

        // Disable odd inline CSS styles.
        try {
            document.execCommand("styleWithCSS", 0, false);
        } catch (e1) {
            try {
                document.execCommand("useCSS", 0, true);
            } catch (e2) {
                try {
                    document.execCommand('styleWithCSS', false, false);
                }
                catch (e3) {
                    // We did our best.
                }
            }
        }

        // Hide the old textarea.
        textarea.hide();
        atto.on('keydown', this.save_selection, this, params.elementid);
        atto.on('mouseup', this.save_selection, this, params.elementid);
        atto.on('focus', this.restore_selection, this, params.elementid);
        // Do not restore selection when focus is from a click event.
        atto.on('mousedown', function() { this.focusfromclick = true; }, this);

        // Copy the current value back to the textarea when focus leaves us and save the current selection.
        atto.on('blur', function() {
            this.focusfromclick = false;
            this.text_updated(params.elementid);
        }, this);

        // Listen for Arrow left and Arrow right keys.
        Y.one(Y.config.doc.body).delegate('key',
                                          this.keyboard_navigation,
                                          'down:37,39',
                                          '#' + params.elementid + '_toolbar',
                                          this,
                                          params.elementid);

        // Save the file picker options for later.
        M.editor_atto.filepickeroptions[params.elementid] = params.filepickeroptions;

        // Init each of the plugins
        var i, j, group, plugin;
        for (i = 0; i < params.plugins.length; i++) {
            group = params.plugins[i].group;
            for (j = 0; j < params.plugins[i].plugins.length; j++) {
                plugin = params.plugins[i].plugins[j];
                plugin.params.elementid = params.elementid;
                plugin.params.group = group;

                M['atto_' + plugin.name].init(plugin.params);
            }
        }

        // Let the plugins run some init code once all plugins are in the page.
        for (i = 0; i < params.plugins.length; i++) {
            group = params.plugins[i].group;
            for (j = 0; j < params.plugins[i].plugins.length; j++) {
                plugin = params.plugins[i].plugins[j];
                plugin.params.elementid = params.elementid;
                plugin.params.group = group;

                if (typeof M['atto_' + plugin.name].after_init !== 'undefined') {
                    M['atto_' + plugin.name].after_init(plugin.params);
                }
            }
        }
    },

    /**
     * The text in the contenteditable region has been updated,
     * clean and copy the buffer to the text area.
     * @param string elementid - the id of the textarea we created this editor from.
     */
    text_updated : function(elementid) {
        var textarea = M.editor_atto.get_textarea_node(elementid),
            cleancontent = this.get_clean_html(elementid);
        textarea.set('value', cleancontent);
        // Trigger the onchange callback on the textarea, essentially to notify moodle-core-formchangechecker.
        textarea.simulate('change');
        // Trigger handlers for this action.
        var i = 0;
        if (elementid in M.editor_atto.textupdatedhandlers) {
            for (i = 0; i < M.editor_atto.textupdatedhandlers[elementid].length; i++) {
                var callback = M.editor_atto.textupdatedhandlers[elementid][i];
                callback(elementid);
            }
        }
    },

    /**
     * Remove all YUI ids from the generated HTML.
     * @param string elementid - the id of the textarea we created this editor from.
     * @return string HTML stripped of YUI ids
     */
    get_clean_html : function(elementid) {
        var atto = M.editor_atto.get_editable_node(elementid).cloneNode(true);

        Y.each(atto.all('[id]'), function(node) {
            var id = node.get('id');
            if (id.indexOf('yui') === 0) {
                node.removeAttribute('id');
            }
        });

        Y.each(atto.all('.atto_control'), function(node) {
            node.remove(true);
        });

        // Remove any and all nasties from source.
        atto.cleanHTML();

        return atto.getHTML();
    },

    /**
     * Implement arrow key navigation for the buttons in the toolbar.
     * @param Event e - the keyboard event.
     * @param string elementid - the id of the textarea we created this editor from.
     */
    keyboard_navigation : function(e, elementid) {
        var buttons,
            current,
            currentid,
            currentindex,
            toolbar = M.editor_atto.get_toolbar_node(elementid);

        e.preventDefault();

        // This workaround is because we cannot do ".atto_group:not([hidden]) button" in ie8 (even with selector-css3).
        // Create an empty NodeList.
        buttons = toolbar.all('empty');
        toolbar.all('.atto_group').each(function(group) {
            if (!group.hasAttribute('hidden')) {
                // Append the visible buttons to the buttons list.
                buttons = buttons.concat(group.all('button'));
            }
        });
        // The currentid is the id of the previously selected button.
        currentid = toolbar.getAttribute('aria-activedescendant');
        if (!currentid) {
            return;
        }
        // We only ever want one button with a tabindex of 0.
        current = Y.one('#' + currentid);
        current.setAttribute('tabindex', '-1');

        currentindex = buttons.indexOf(current);

        if (e.keyCode === 37) {
            // Left
            currentindex--;
            if (currentindex < 0) {
                currentindex = buttons.size()-1;
            }
        } else {
            // Right
            currentindex++;
            if (currentindex >= buttons.size()) {
                currentindex = 0;
            }
        }
        // Currentindex has been updated to point to the new button.
        current = buttons.item(currentindex);
        current.setAttribute('tabindex', '0');
        current.focus();
        toolbar.setAttribute('aria-activedescendant', current.generateID());
    },

    /**
     * Should we show the filepicker for this filetype?
     *
     * @param string elementid for this editor instance.
     * @param string type The media type for the file picker
     * @return boolean
     */
    can_show_filepicker : function(elementid, type) {
        var options = M.editor_atto.filepickeroptions[elementid];
        return ((typeof options[type]) !== "undefined");
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
     * Save the current selection on blur, allows more reliable keyboard navigation.
     * @param Y.Event event
     * @param string elementid
     */
    save_selection : function(event, elementid) {
        if (this.is_active(elementid)) {
            var sel = this.get_selection();
            if (sel.length > 0) {
                this.selections[elementid] = sel;
            }
        }
    },

    /**
     * Restore any current selection when the editor gets focus again.
     * @param Y.Event event
     * @param string elementid
     */
    restore_selection : function(event, elementid) {
        event.preventDefault();
        if (!this.focusfromclick) {
            if (typeof this.selections[elementid] !== "undefined") {
                this.set_selection(this.selections[elementid]);
            }
        }
        this.focusfromclick = false;
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
     * Check that a YUI node it at least partly contained by the selection.
     * @param Y.Node node
     * @return boolean
     */
    selection_contains_node : function(node) {
        var range, sel;
        if (window.getSelection) {
            sel = window.getSelection();

            if (sel.containsNode) {
                return sel.containsNode(node.getDOMNode(), true);
            }
        }
        sel = document.selection.createRange();
        range = sel.duplicate();
        range.moveToElementText(node.getDOMNode());
        return sel.inRange(range);
    },

    /**
     * Get the dom node representing the common anscestor of the selection nodes.
     * @return DOMNode or false
     */
    get_selection_parent_node : function() {
        var selection = M.editor_atto.get_selection();
        if (selection.length) {
            selection = selection.pop();
        }

        if (selection.commonAncestorContainer) {
            return selection.commonAncestorContainer;
        } else if (selection.parentElement) {
            return selection.parentElement();
        }
        // No selection
        return false;
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
    },

    /**
     * Change the formatting for the current selection.
     * Also changes the selection to the newly formatted block (allows applying multiple styles to a block).
     *
     * @param {String} elementid - The editor elementid.
     * @param {String} blocktag - Change the block level tag to this. Empty string, means do not change the tag.
     * @param {Object} attributes - The keys and values for attributes to be added/changed in the block tag.
     * @return Y.Node - if there was a selection.
     */
    format_selection_block : function(elementid, blocktag, attributes) {
        // First find the nearest ancestor of the selection that is a block level element.
        var selectionparentnode = M.editor_atto.get_selection_parent_node(),
            boundary,
            cell,
            nearestblock,
            newcontent,
            match,
            replacement;

        if (!selectionparentnode) {
            // No selection, nothing to format.
            return;
        }

        boundary = M.editor_atto.get_editable_node(elementid);

        selectionparentnode = Y.one(selectionparentnode);

        // If there is a table cell in between the selectionparentnode and the boundary,
        // move the boundary to the table cell.
        // This is because we might have a table in a div, and we select some text in a cell,
        // want to limit the change in style to the table cell, not the entire table (via the outer div).
        cell = selectionparentnode.ancestor(function (node) {
            var tagname = node.get('tagName');
            if (tagname) {
                tagname = tagname.toLowerCase();
            }
            return (node === boundary) ||
                   (tagname === 'td') ||
                   (tagname === 'th');
        }, true);

        if (cell) {
            // Limit the scope to the table cell.
            boundary = cell;
        }

        nearestblock = selectionparentnode.ancestor(M.editor_atto.BLOCK_TAGS.join(', '), true);
        if (nearestblock) {
            // Check that the block is contained by the boundary.
            match = nearestblock.ancestor(function (node) {
                return node === boundary;
            }, false);

            if (!match) {
                nearestblock = false;
            }
        }

        // No valid block element - make one.
        if (!nearestblock) {
            // There is no block node in the content, wrap the content in a p and use that.
            newcontent = Y.Node.create('<p></p>');
            boundary.get('childNodes').each(function (child) {
                newcontent.append(child.remove());
            });
            boundary.append(newcontent);
            nearestblock = newcontent;
        }

        // Guaranteed to have a valid block level element contained in the contenteditable region.
        // Change the tag to the new block level tag.
        if (blocktag && blocktag !== '') {
            // Change the block level node for a new one.
            replacement = Y.Node.create('<' + blocktag + '></' + blocktag + '>');
            // Copy all attributes.
            replacement.setAttrs(nearestblock.getAttrs());
            // Copy all children.
            nearestblock.get('childNodes').each(function (child) {
                child.remove();
                replacement.append(child);
            });

            nearestblock.replace(replacement);
            nearestblock = replacement;
        }

        // Set the attributes on the block level tag.
        if (attributes) {
            nearestblock.setAttrs(attributes);
        }

        // Change the selection to the modified block. This makes sense when we might apply multiple styles
        // to the block.
        var selection = M.editor_atto.get_selection_from_node(nearestblock);
        M.editor_atto.set_selection(selection);

        return nearestblock;
    }

};
var CONTROLMENU_NAME = "Controlmenu",
    CONTROLMENU;

/**
 * CONTROLMENU
 * This is a drop down list of buttons triggered (and aligned to) a button.
 *
 * @namespace M.editor_atto.controlmenu
 * @class controlmenu
 * @constructor
 * @extends M.core.dialogue
 */
CONTROLMENU = function(config) {
    config.draggable = false;
    config.center = false;
    config.width = 'auto';
    config.lightbox = false;
    config.footerContent = '';
    CONTROLMENU.superclass.constructor.apply(this, [config]);
};

Y.extend(CONTROLMENU, M.core.dialogue, {

    /**
     * Initialise the menu.
     *
     * @method initializer
     * @return void
     */
    initializer : function(config) {
        var body, headertext, bb;
        CONTROLMENU.superclass.initializer.call(this, config);

        bb = this.get('boundingBox');
        bb.addClass('editor_atto_controlmenu');

        // Close the menu when clicked outside (excluding the button that opened the menu).
        body = this.bodyNode;

        headertext = Y.Node.create('<h3/>');
        headertext.addClass('accesshide');
        headertext.setHTML(this.get('headerText'));
        body.prepend(headertext);

        body.on('clickoutside', function(e) {
            if (this.get('visible')) {
                // Note: we need to compare ids because for some reason - sometimes button is an Object, not a Y.Node.
                if (!e.target.ancestor('.atto_control')) {
                    e.preventDefault();
                    this.hide();
                }
            }
        }, this);
    }

}, {
    NAME : CONTROLMENU_NAME,
    ATTRS : {
        /**
         * The header for the drop down (only accessible to screen readers).
         *
         * @attribute headerText
         * @type String
         * @default ''
         */
        headerText : {
            value : ''
        }

    }
});

M.editor_atto = M.editor_atto || {};
M.editor_atto.controlmenu = CONTROLMENU;
/**
 * Class for cleaning ugly HTML.
 * Rewritten JS from jquery-clean plugin.
 *
 * @module editor_atto
 * @chainable
 */
function cleanHTML() {
    var cleaned = this.getHTML();

    // What are we doing ?
    // We are cleaning random HTML from all over the shop into a set of useful html suitable for content.
    // We are allowing styles etc, but not e.g. font tags, class="MsoNormal" etc.

    var rules = [
        // Source: "http://stackoverflow.com/questions/2875027/clean-microsoft-word-pasted-text-using-javascript"
        // Source: "http://stackoverflow.com/questions/1068280/javascript-regex-multiline-flag-doesnt-work"

        // Remove all HTML comments.
        {regex: /<!--[\s\S]*?-->/gi, replace: ""},
        // Source: "http://www.1stclassmedia.co.uk/developers/clean-ms-word-formatting.php"
        // Remove <?xml>, <\?xml>.
        {regex: /<\\?\?xml[^>]*>/gi, replace: ""},
        // Remove <o:blah>, <\o:blah>.
        {regex: /<\/?\w+:[^>]*>/gi, replace: ""}, // e.g. <o:p...
        // Remove MSO-blah, MSO:blah (e.g. in style attributes)
        {regex: /\s*MSO[-:][^;"']*;?/gi, replace: ""},
        // Remove empty spans
        {regex: /<span[^>]*>(&nbsp;|\s)*<\/span>/gi, replace: ""},
        // Remove class="Msoblah"
        {regex: /class="Mso[^"]*"/gi, replace: ""},

        // Source: "http://www.codinghorror.com/blog/2006/01/cleaning-words-nasty-html.html"
        // Remove forbidden tags for content, title, meta, style, st0-9, head, font, html, body.
        {regex: /<(\/?title|\/?meta|\/?style|\/?st\d|\/?head|\/?font|\/?html|\/?body|!\[)[^>]*?>/gi, replace: ""},

        // Source: "http://www.tim-jarrett.com/labs_javascript_scrub_word.php"
        // Replace extended chars with simple text.
        {regex: new RegExp(String.fromCharCode(8220), 'gi'), replace: '"'},
        {regex: new RegExp(String.fromCharCode(8216), 'gi'), replace: "'"},
        {regex: new RegExp(String.fromCharCode(8217), 'gi'), replace: "'"},
        {regex: new RegExp(String.fromCharCode(8211), 'gi'), replace: '-'},
        {regex: new RegExp(String.fromCharCode(8212), 'gi'), replace: '--'},
        {regex: new RegExp(String.fromCharCode(189), 'gi'), replace: '1/2'},
        {regex: new RegExp(String.fromCharCode(188), 'gi'), replace: '1/4'},
        {regex: new RegExp(String.fromCharCode(190), 'gi'), replace: '3/4'},
        {regex: new RegExp(String.fromCharCode(169), 'gi'), replace: '(c)'},
        {regex: new RegExp(String.fromCharCode(174), 'gi'), replace: '(r)'},
        {regex: new RegExp(String.fromCharCode(8230), 'gi'), replace: '...'}
    ];

    var i = 0, rule;

    for (i = 0; i < rules.length; i++) {
        rule = rules[i];
        cleaned = cleaned.replace(rule.regex, rule.replace);
    }

    this.setHTML(cleaned);
    return this;
}

Y.Node.addMethod("cleanHTML", cleanHTML);
Y.NodeList.importMethod(Y.Node.prototype, "cleanHTML");


}, '@VERSION@', {"requires": ["node", "io", "overlay", "escape", "event", "event-simulate", "moodle-core-notification"]});
