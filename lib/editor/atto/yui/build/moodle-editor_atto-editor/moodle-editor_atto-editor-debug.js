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
            overlay.show();
            overlay.bodyNode.one('a').focus();
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
     */
    is_enabled : function(elementid, plugin) {
        var element = M.editor_atto.get_toolbar_node(elementid).one('.atto_' + plugin + '_button');

        return !element.hasAttribute('disabled');
    },

    /**
     * Enable a single widget in the toolbar.
     * @param string elementid, the element id of this editor.
     * @param string plugin, the name of the plugin that created the widget.
     */
    enable_widget : function(elementid, plugin) {
        var element = M.editor_atto.get_toolbar_node(elementid).one('.atto_' + plugin + '_button');

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
            element = M.editor_atto.get_toolbar_node(elementid).one('.atto_' + plugin + '_button');

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
     */
    add_toolbar_menu : function(elementid, plugin, iconurl, groupname, entries) {
        var toolbar = M.editor_atto.get_toolbar_node(elementid),
            group = toolbar.one('.atto_group.' + groupname + '_group'),
            currentfocus,
            button,
            expimgurl;

        if (!group) {
            group = Y.Node.create('<div class="atto_group ' + groupname + '_group"></div>');
            toolbar.append(group);
        }
        expimgurl = M.util.image_url('t/expanded', 'moodle');
        button = Y.Node.create('<button class="atto_' + plugin + '_button atto_hasmenu" ' +
                                    'data-editor="' + Y.Escape.html(elementid) + '" ' +
                                    'tabindex="-1" ' +
                                    'data-menu="' + plugin + '_' + elementid + '" ' +
                                    'title="' + Y.Escape.html(M.util.get_string('pluginname', 'atto_' + plugin)) + '">' +
                                    '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" src="' + iconurl + '"/>' +
                                    '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" src="' + expimgurl + '"/>' +
                                    '</button>');

        group.append(button);

        currentfocus = toolbar.getAttribute('aria-activedescendant');
        if (!currentfocus) {
            button.setAttribute('tabindex', '0');
            toolbar.setAttribute('aria-activedescendant', button.generateID());
        }

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
                Y.one('body').delegate('key', M.editor_atto.buttonclicked_handler, 'space,enter', '.atto_' + plugin + '_action_' + i);
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
            center : false
        });

        M.editor_atto.menus[plugin + '_' + elementid] = overlay;
        overlay.render();
        overlay.align(button, [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);
        overlay.hide();
        overlay.headerNode.hide();
    },

    /**
     * Add a button to the toolbar belonging to the editor for element with id "elementid".
     * @param string elementid - the id of the textarea we created this editor from.
     * @param string plugin - the plugin defining the button.
     * @param string icon - the url to the image for the icon
     * @param string groupname - the group the button should be appended to.
     * @handler function handler- A function to call when the button is clicked.
     */
    add_toolbar_button : function(elementid, plugin, iconurl, groupname, handler) {
        var toolbar = M.editor_atto.get_toolbar_node(elementid),
            group = toolbar.one('.atto_group.' + groupname + '_group'),
            button,
            currentfocus;

        if (!group) {
            group = Y.Node.create('<div class="atto_group ' + groupname +'_group"></div>');
            toolbar.append(group);
        }
        button = Y.Node.create('<button class="atto_' + plugin + '_button" ' +
                               'data-editor="' + Y.Escape.html(elementid) + '" ' +
                               'data-plugin="' + Y.Escape.html(plugin) + '" ' +
                               'tabindex="-1" ' +
                               'data-handler="' + Y.Escape.html(plugin) + '" ' +
                               'title="' + Y.Escape.html(M.util.get_string('pluginname', 'atto_' + plugin)) + '">' +
                                    '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" src="' + iconurl + '"/>' +
                               '</button>');

        group.append(button);

        currentfocus = toolbar.getAttribute('aria-activedescendant');
        if (!currentfocus) {
            button.setAttribute('tabindex', '0');
            toolbar.setAttribute('aria-activedescendant', button.generateID());
        }

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
                                            'spellcheck="true" ' +
                                            'class="' + CSS.CONTENT + '" />');

        var cssfont = '';
        var toolbar = Y.Node.create('<div class="' + CSS.TOOLBAR + '" id="' + params.elementid + '_toolbar" role="toolbar"/>');

        // Editable content wrapper.
        var content = Y.Node.create('<div class="' + CSS.CONTENTWRAPPER + '" />');
        var textarea = M.editor_atto.get_textarea_node(params.elementid);

        content.appendChild(atto);

        // Add everything to the wrapper.
        wrapper.appendChild(toolbar);
        wrapper.appendChild(content);

        // Bleh - why are we sent a url and not the css to apply directly?
        var css = Y.io(params.content_css, { sync: true });
        var pos = css.responseText.indexOf('font:');
        if (pos) {
            cssfont = css.responseText.substring(pos + 'font:'.length, css.responseText.length - 1);
            atto.setStyle('font', cssfont);
        }
        atto.setStyle('minHeight', (1.2 * (textarea.getAttribute('rows'))) + 'em');

        // Copy text to editable div.
        atto.append(textarea.get('value'));

        // Clean it.
        atto.cleanHTML();

        // Add the toolbar and editable zone to the page.
        textarea.get('parentNode').insert(wrapper, textarea);
        atto.setStyle('color', textarea.getStyle('color'));
        atto.setStyle('lineHeight', textarea.getStyle('lineHeight'));
        atto.setStyle('fontSize', textarea.getStyle('fontSize'));
        // Hide the old textarea.
        textarea.hide();

        // Copy the current value back to the textarea when focus leaves us.
        atto.on('blur', function() {
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
        var i, j;
        for (i = 0; i < params.plugins.length; i++) {
            var group = params.plugins[i].group;
            for (j = 0; j < params.plugins[i].plugins.length; j++) {
                var plugin = params.plugins[i].plugins[j];
                plugin.params.elementid = params.elementid;
                plugin.params.group = group;

                M['atto_' + plugin.name].init(plugin.params);
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

        buttons = toolbar.all('button');
        currentid = toolbar.getAttribute('aria-activedescendant');
        if (!currentid) {
            return;
        }
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
     * @param Range selection
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


}, '@VERSION@', {"requires": ["node", "io", "overlay", "escape", "event", "moodle-core-notification"]});
