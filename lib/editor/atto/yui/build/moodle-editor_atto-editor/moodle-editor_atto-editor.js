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
 * The Atto WYSIWG pluggable editor, written for Moodle.
 *
 * @module     moodle-editor_atto-editor
 * @package    editor_atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @main       moodle-editor_atto-editor
 */

/**
 * @module moodle-editor_atto-editor
 * @submodule editor-base
 */

var LOGNAME = 'moodle-editor_atto-editor';
var CSS = {
        CONTENT: 'editor_atto_content',
        CONTENTWRAPPER: 'editor_atto_content_wrap',
        TOOLBAR: 'editor_atto_toolbar',
        WRAPPER: 'editor_atto',
        HIGHLIGHT: 'highlight'
    };

/**
 * The Atto editor for Moodle.
 *
 * @namespace M.editor_atto
 * @class Editor
 * @constructor
 * @uses M.editor_atto.EditorClean
 * @uses M.editor_atto.EditorFilepicker
 * @uses M.editor_atto.EditorSelection
 * @uses M.editor_atto.EditorStyling
 * @uses M.editor_atto.EditorTextArea
 * @uses M.editor_atto.EditorToolbar
 * @uses M.editor_atto.EditorToolbarNav
 */

function Editor() {
    Editor.superclass.constructor.apply(this, arguments);
}

Y.extend(Editor, Y.Base, {

    /**
     * List of known block level tags.
     * Taken from "https://developer.mozilla.org/en-US/docs/HTML/Block-level_elements".
     *
     * @property BLOCK_TAGS
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
        'video'
    ],

    PLACEHOLDER_CLASS: 'atto-tmp-class',
    ALL_NODES_SELECTOR: '[style],font[face]',
    FONT_FAMILY: 'fontFamily',

    /**
     * The wrapper containing the editor.
     *
     * @property _wrapper
     * @type Node
     * @private
     */
    _wrapper: null,

    /**
     * A reference to the content editable Node.
     *
     * @property editor
     * @type Node
     */
    editor: null,

    /**
     * A reference to the original text area.
     *
     * @property textarea
     * @type Node
     */
    textarea: null,

    /**
     * A reference to the label associated with the original text area.
     *
     * @property textareaLabel
     * @type Node
     */
    textareaLabel: null,

    /**
     * A reference to the list of plugins.
     *
     * @property plugins
     * @type object
     */
    plugins: null,

    initializer: function() {
        var template;

        // Note - it is not safe to use a CSS selector like '#' + elementid because the id
        // may have colons in it - e.g.  quiz.
        this.textarea = Y.one(document.getElementById(this.get('elementid')));

        if (!this.textarea) {
            // No text area found.
            return;
        }

        this._wrapper = Y.Node.create('<div class="' + CSS.WRAPPER + '" />');
        template = Y.Handlebars.compile('<div id="{{elementid}}editable" ' +
                'contenteditable="true" ' +
                'role="textbox" ' +
                'spellcheck="true" ' +
                'aria-live="off" ' +
                'class="{{CSS.CONTENT}}" ' +
                '/>');
        this.editor = Y.Node.create(template({
            elementid: this.get('elementid'),
            CSS: CSS
        }));

        // Add a labelled-by attribute to the contenteditable.
        this.textareaLabel = Y.one('[for="' + this.get('elementid') + '"]');
        if (this.textareaLabel) {
            this.textareaLabel.generateID();
            this.editor.setAttribute('aria-labelledby', this.textareaLabel.get("id"));
        }

        // Add everything to the wrapper.
        this.setupToolbar();

        // Editable content wrapper.
        var content = Y.Node.create('<div class="' + CSS.CONTENTWRAPPER + '" />');
        content.appendChild(this.editor);
        this._wrapper.appendChild(content);

        // Style the editor. According to the styles.css: 20 is the line-height, 8 is padding-top + padding-bottom.
        this.editor.setStyle('minHeight', ((20 * this.textarea.getAttribute('rows')) + 8) + 'px');

        if (Y.UA.ie === 0) {
            // We set a height here to force the overflow because decent browsers allow the CSS property resize.
            this.editor.setStyle('height', ((20 * this.textarea.getAttribute('rows')) + 8) + 'px');
        }

        // Disable odd inline CSS styles.
        this.disableCssStyling();

        // Add the toolbar and editable zone to the page.
        this.textarea.get('parentNode').insert(this._wrapper, this.textarea);

        // Hide the old textarea.
        this.textarea.hide();

        // Copy the text to the contenteditable div.
        this.updateFromTextArea();

        // Publish the events that are defined by this editor.
        this.publishEvents();

        // Add handling for saving and restoring selections on cursor/focus changes.
        this.setupSelectionWatchers();

        // Setup plugins.
        this.setupPlugins();
    },

    /**
     * Focus on the editable area for this editor.
     *
     * @method focus
     * @chainable
     */
    focus: function() {
        this.editor.focus();

        return this;
    },

    /**
     * Publish events for this editor instance.
     *
     * @method publishEvents
     * @private
     * @chainable
     */
    publishEvents: function() {
        /**
         * Fired when changes are made within the editor.
         *
         * @event change
         */
        this.publish('change', {
            broadcast: true,
            preventable: true
        });

        /**
         * Fired when all plugins have completed loading.
         *
         * @event pluginsloaded
         */
        this.publish('pluginsloaded', {
            fireOnce: true
        });

        this.publish('atto:selectionchanged', {
            prefix: 'atto'
        });

        Y.delegate(['mouseup', 'keyup', 'focus'], this._hasSelectionChanged, document.body, '#' + this.editor.get('id'), this);

        return this;
    },

    setupPlugins: function() {
        // Clear the list of plugins.
        this.plugins = {};

        var plugins = this.get('plugins');

        var groupIndex,
            group,
            pluginIndex,
            plugin,
            pluginConfig;

        for (groupIndex in plugins) {
            group = plugins[groupIndex];
            if (!group.plugins) {
                // No plugins in this group - skip it.
                continue;
            }
            for (pluginIndex in group.plugins) {
                plugin = group.plugins[pluginIndex];

                pluginConfig = Y.mix({
                    name: plugin.name,
                    group: group.group,
                    editor: this.editor,
                    toolbar: this.toolbar,
                    host: this
                }, plugin);

                // Add a reference to the current editor.
                if (typeof Y.M['atto_' + plugin.name] === "undefined") {
                    continue;
                }
                this.plugins[plugin.name] = new Y.M['atto_' + plugin.name].Button(pluginConfig);
            }
        }

        // Some plugins need to perform actions once all plugins have loaded.
        this.fire('pluginsloaded');

        return this;
    },

    enablePlugins: function(plugin) {
        this._setPluginState(true, plugin);
    },

    disablePlugins: function(plugin) {
        this._setPluginState(false, plugin);
    },

    _setPluginState: function(enable, plugin) {
        var target = 'disableButtons';
        if (enable) {
            target = 'enableButtons';
        }

        if (plugin) {
            this.plugins[plugin][target]();
        } else {
            Y.Object.each(this.plugins, function(currentPlugin) {
                currentPlugin[target]();
            }, this);
        }
    }

}, {
    NS: 'editor_atto',
    ATTRS: {
        /**
         * The unique identifier for the form element representing the editor.
         *
         * @attribute elementid
         * @type String
         * @writeOnce
         */
        elementid: {
            value: null,
            writeOnce: true
        },

        /**
         * Plugins with their configuration.
         *
         * The plugins structure is:
         *
         *     [
         *         {
         *             "group": "groupName",
         *             "plugins": [
         *                 "pluginName": {
         *                     "configKey": "configValue"
         *                 },
         *                 "pluginName": {
         *                     "configKey": "configValue"
         *                 }
         *             ]
         *         },
         *         {
         *             "group": "groupName",
         *             "plugins": [
         *                 "pluginName": {
         *                     "configKey": "configValue"
         *                 }
         *             ]
         *         }
         *     ]
         *
         * @attribute plugins
         * @type Object
         * @writeOnce
         */
        plugins: {
            value: {},
            writeOnce: true
        }
    }
});

// The Editor publishes custom events that can be subscribed to.
Y.augment(Editor, Y.EventTarget);

Y.namespace('M.editor_atto').Editor = Editor;

// Function for Moodle's initialisation.
Y.namespace('M.editor_atto.Editor').init = function(config) {
    return new Y.M.editor_atto.Editor(config);
};
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
 * @module moodle-editor_atto-editor
 * @submodule textarea
 */

/**
 * Textarea functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorTextArea
 */

function EditorTextArea() {}

EditorTextArea.ATTRS= {
};

EditorTextArea.prototype = {
    /**
     * Copy and clean the text from the textarea into the contenteditable div.
     *
     * If the text is empty, provide a default paragraph tag to hold the content.
     *
     * @method updateFromTextArea
     * @chainable
     */
    updateFromTextArea: function() {
        // Clear it first.
        this.editor.setHTML('');

        // Copy text to editable div.
        this.editor.append(this.textarea.get('value'));

        // Clean it.
        this.cleanEditorHTML();

        // Insert a paragraph in the empty contenteditable div.
        if (this.editor.getHTML() === '') {
            if (Y.UA.ie && Y.UA.ie < 10) {
                this.editor.setHTML('<p></p>');
            } else {
                this.editor.setHTML('<p><br></p>');
            }
        }
    },

    /**
     * Copy the text from the contenteditable to the textarea which it replaced.
     *
     * @method updateOriginal
     * @chainable
     */
    updateOriginal : function() {
        // Insert the cleaned content.
        this.textarea.set('value', this.getCleanHTML());

        // Trigger the onchange callback on the textarea, essentially to notify moodle-core-formchangechecker.
        this.textarea.simulate('change');

        // Trigger handlers for this action.
        this.fire('change');
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorTextArea]);
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
 * @module moodle-editor_atto-editor
 * @submodule clean
 */

/**
 * Functions for the Atto editor to clean the generated content.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorClean
 */

function EditorClean() {}

EditorClean.ATTRS= {
};

EditorClean.prototype = {
    /**
     * Clean the generated HTML content without modifying the editor content.
     *
     * This includes removes all YUI ids from the generated content.
     *
     * @return {string} The cleaned HTML content.
     */
    getCleanHTML: function() {
        // Clone the editor so that we don't actually modify the real content.
        var editorClone = this.editor.cloneNode(true);

        // Remove all YUI IDs.
        Y.each(editorClone.all('[id^="yui"]'), function(node) {
            node.removeAttribute('id');
        });

        editorClone.all('.atto_control').remove(true);

        // Remove any and all nasties from source.
       return this._cleanHTML(editorClone.get('innerHTML'));
    },

    /**
     * Clean the HTML content of the editor.
     *
     * @method cleanEditorHTML
     * @chainable
     */
    cleanEditorHTML: function() {
        var startValue = this.editor.get('innerHTML');
        this.editor.set('innerHTML', this._cleanHTML(startValue));

        return this;
    },

    /**
     * Clean the specified HTML content and remove any content which could cause issues.
     *
     * @method _cleanHTML
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned HTML
     */
    _cleanHTML: function(content) {
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

        var i = 0;
        for (i = 0; i < rules.length; i++) {
            content = content.replace(rules[i].regex, rules[i].replace);
        }

        return content;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorClean]);
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
 * @module moodle-editor_atto-editor
 * @submodule toolbar
 */

/**
 * Toolbar functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorToolbar
 */

function EditorToolbar() {}

EditorToolbar.ATTRS= {
};

EditorToolbar.prototype = {
    /**
     * A reference to the toolbar Node.
     *
     * @property toolbar
     * @type Node
     */
    toolbar: null,

    /**
     * A reference to any currently open menus in the toolbar.
     *
     * @property openMenus
     * @type Array
     */
    openMenus: null,

    /**
     * Setup the toolbar on the editor.
     *
     * @method setupToolbar
     * @chainable
     */
    setupToolbar: function() {
        this.toolbar = Y.Node.create('<div class="' + CSS.TOOLBAR + '" role="toolbar" aria-live="off"/>');
        this.openMenus = [];
        this._wrapper.appendChild(this.toolbar);

        if (this.textareaLabel) {
            this.toolbar.setAttribute('aria-labelledby', this.textareaLabel.get("id"));
        }

        // Add keyboard navigation for the toolbar.
        this.setupToolbarNavigation();

        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorToolbar]);
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
 * @module moodle-editor_atto-editor
 * @submodule toolbarnav
 */

/**
 * Toolbar Navigation functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorToolbarNav
 */

function EditorToolbarNav() {}

EditorToolbarNav.ATTRS= {
};

EditorToolbarNav.prototype = {
    /**
     * The current focal point for tabbing.
     *
     * @property _tabFocus
     * @type Node
     * @default null
     * @private
     */
    _tabFocus: null,

    /**
     * Set up the watchers for toolbar navigation.
     *
     * @method setupToolbarNavigation
     * @chainable
     */
    setupToolbarNavigation: function() {
        // Listen for Arrow left and Arrow right keys.
        this._wrapper.delegate('key',
                this.toolbarKeyboardNavigation,
                'down:37,39',
                '.' + CSS.TOOLBAR,
                this);
        this._wrapper.delegate('focus',
                function(e) {
                    this._setTabFocus(e.currentTarget);
                }, '.' + CSS.TOOLBAR + ' button', this);

        return this;
    },

    /**
     * Implement arrow key navigation for the buttons in the toolbar.
     *
     * @method toolbarKeyboardNavigation
     * @param {EventFacade} e - the keyboard event.
     */
    toolbarKeyboardNavigation: function(e) {
        // Prevent the default browser behaviour.
        e.preventDefault();

        // On cursor moves we loops through the buttons.
        var buttons = this.toolbar.all('button'),
            direction = 1,
            button,
            current = e.target.ancestor('button', true);

        if (e.keyCode === 37) {
            // Moving left so reverse the direction.
            direction = -1;
        }

        button = this._findFirstFocusable(buttons, current, direction);
        if (button) {
            button.focus();
            this._setTabFocus(button);
        } else {
        }
    },

    /**
     * Find the first focusable button.
     *
     * @param {NodeList} buttons A list of nodes.
     * @param {Node} startAt The node in the list to start the search from.
     * @param {Number} direction The direction in which to search (1 or -1).
     * @return {Node | Undefined} The Node or undefined.
     * @method _findFirstFocusable
     * @private
     */
    _findFirstFocusable: function(buttons, startAt, direction) {
        var checkCount = 0,
            group,
            candidate,
            button,
            index;

        // Determine which button to start the search from.
        index = buttons.indexOf(startAt);
        if (index < -1) {
            index = 0;
        }

        // Try to find the next.
        while (checkCount < buttons.size()) {
            index += direction;
            if (index < 0) {
                index = buttons.size() - 1;
            } else if (index >= buttons.size()) {
                // Handle wrapping.
                index = 0;
            }

            candidate = buttons.item(index);

            // Add a counter to ensure we don't get stuck in a loop if there's only one visible menu item.
            checkCount++;

            // Loop while:
            // * we haven't checked every button;
            // * the button is hidden or disabled;
            // * the group is hidden.
            if (candidate.hasAttribute('hidden') || candidate.hasAttribute('disabled')) {
                continue;
            }
            group = candidate.ancestor('.atto_group');
            if (group.hasAttribute('hidden')) {
                continue;
            }

            button = candidate;
            break;
        }

        return button;
    },

    /**
     * Check the tab focus.
     *
     * When we disable or hide a button, we should call this method to ensure that the
     * focus is not currently set on an inaccessible button, otherwise tabbing to the toolbar
     * would be impossible.
     *
     * @method checkTabFocus
     * @chainable
     */
    checkTabFocus: function() {
        if (this._tabFocus) {
            if (this._tabFocus.hasAttribute('disabled') || this._tabFocus.hasAttribute('hidden')
                    || this._tabFocus.ancestor('.atto_group').hasAttribute('hidden')) {
                // Find first available button.
                button = this._findFirstFocusable(this.toolbar.all('button'), this._tabFocus, -1);
                if (button) {
                    if (this._tabFocus.compareTo(document.activeElement)) {
                        // We should also move the focus, because the inaccessible button also has the focus.
                        button.focus();
                    }
                    this._setTabFocus(button);
                }
            }
        }
        return this;
    },

    /**
     * Sets tab focus for the toolbar to the specified Node.
     *
     * @method _setTabFocus
     * @param {Node} button The node that focus should now be set to
     * @chainable
     * @private
     */
    _setTabFocus: function(button) {
        if (this._tabFocus) {
            // Unset the previous entry.
            this._tabFocus.setAttribute('tabindex', '-1');
        }

        // Set up the new entry.
        this._tabFocus = button;
        this._tabFocus.setAttribute('tabindex', 0);

        // And update the activedescendant to point at the currently selected button.
        this.toolbar.setAttribute('aria-activedescendant', this._tabFocus.generateID());

        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorToolbarNav]);
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
 * @module moodle-editor_atto-editor
 * @submodule selection
 */

/**
 * Selection functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorSelection
 */

function EditorSelection() {}

EditorSelection.ATTRS= {
};

EditorSelection.prototype = {

    /**
     * List of saved selections per editor instance.
     *
     * @property _selections
     * @private
     */
    _selections: null,

    /**
     * A unique identifier for the last selection recorded.
     *
     * @property _lastSelection
     * @param lastselection
     * @type string
     * @private
     */
    _lastSelection: null,

    /**
     * Whether focus came from a click event.
     *
     * This is used to determine whether to restore the selection or not.
     *
     * @property _focusFromClick
     * @type Boolean
     * @default false
     * @private
     */
    _focusFromClick: false,

    /**
     * Set up the watchers for selection save and restoration.
     *
     * @method setupSelectionWatchers
     * @chainable
     */
    setupSelectionWatchers: function() {
        // Save the selection when a change was made.
        this.on('atto:selectionchanged', this.saveSelection, this);

        this.editor.on('focus', this.restoreSelection, this);

        // Do not restore selection when focus is from a click event.
        this.editor.on('mousedown', function() {
            this._focusFromClick = true;
        }, this);

        // Copy the current value back to the textarea when focus leaves us and save the current selection.
        this.editor.on('blur', function() {
            // Clear the _focusFromClick value.
            this._focusFromClick = false;

            // Update the original text area.
            this.updateOriginal();
        }, this);

        return this;
    },

    /**
     * Work out if the cursor is in the editable area for this editor instance.
     *
     * @method isActive
     * @return {boolean}
     */
    isActive: function() {
        var range = rangy.createRange(),
            selection = rangy.getSelection();

        if (!selection.rangeCount) {
            // If there was no range count, then there is no selection.
            return false;
        }

        // Check whether the range intersects the editor selection.
        range.selectNode(this.editor.getDOMNode());
        return range.intersectsRange(selection.getRangeAt(0));
    },

    /**
     * Create a cross browser selection object that represents a YUI node.
     *
     * @method getSelectionFromNode
     * @param {Node} YUI Node to base the selection upon.
     * @return {[rangy.Range]}
     */
    getSelectionFromNode: function(node) {
        var range = rangy.createRange();
        range.selectNode(node.getDOMNode());
        return [range];
    },

    /**
     * Save the current selection to an internal property.
     *
     * This allows more reliable return focus, helping improve keyboard navigation.
     *
     * Should be used in combination with {{#crossLink "M.editor_atto.EditorSelection/restoreSelection"}}{{/crossLink}}.
     *
     * @method saveSelection
     */
    saveSelection: function() {
        if (this.isActive()) {
            this._selections = this.getSelection();
        }
    },

    /**
     * Restore any stored selection when the editor gets focus again.
     *
     * Should be used in combination with {{#crossLink "M.editor_atto.EditorSelection/saveSelection"}}{{/crossLink}}.
     *
     * @method restoreSelection
     */
    restoreSelection: function() {
        if (!this._focusFromClick) {
            if (this._selections) {
                this.setSelection(this._selections);
            }
        }
        this._focusFromClick = false;
    },

    /**
     * Get the selection object that can be passed back to setSelection.
     *
     * @method getSelection
     * @return {array} An array of rangy ranges.
     */
    getSelection: function() {
        return rangy.getSelection().getAllRanges();
    },

    /**
     * Check that a YUI node it at least partly contained by the current selection.
     *
     * @method selectionContainsNode
     * @param {Node} The node to check.
     * @return {boolean}
     */
    selectionContainsNode: function(node) {
        return rangy.getSelection().containsNode(node.getDOMNode(), true);
    },

    /**
     * Runs a filter on each node in the selection, and report whether the
     * supplied selector(s) were found in the supplied Nodes.
     *
     * By default, all specified nodes must match the selection, but this
     * can be controlled with the requireall property.
     *
     * @method selectionFilterMatches
     * @param {String} selector
     * @param {NodeList} [selectednodes] For performance this should be passed. If not passed, this will be looked up each time.
     * @param {Boolean} [requireall=true] Used to specify that "any" match is good enough.
     * @return {Boolean}
     */
    selectionFilterMatches: function(selector, selectednodes, requireall) {
        if (typeof requireall === 'undefined') {
            requireall = true;
        }
        if (!selectednodes) {
            // Find this because it was not passed as a param.
            selectednodes = this.getSelectedNodes();
        }
        var allmatch = selectednodes.size() > 0,
            anymatch = false;

        var editor = this.editor,
            stopFn = function(node) {
                return editor.contains(node);
            };

        selectednodes.each(function(node){
            // Check each node, if it doesn't match the tags AND is not within the specified tags then fail this thing.
            if (requireall) {
                // Check for at least one failure.
                if (!allmatch || !node.ancestor(selector, true, stopFn)) {
                    allmatch = false;
                }
            } else {
                // Check for at least one match.
                if (!anymatch && node.ancestor(selector, true, stopFn)) {
                    anymatch = true;
                }
            }
        }, this);
        if (requireall) {
            return allmatch;
        } else {
            return anymatch;
        }
    },

    /**
     * Get the deepest possible list of nodes in the current selection.
     *
     * @method getSelectedNodes
     * @return {NodeList}
     */
    getSelectedNodes: function() {
        var results = new Y.NodeList(),
            nodes,
            selection,
            range,
            node,
            i;

        selection = rangy.getSelection();

        if (selection.rangeCount) {
            range = selection.getRangeAt(0);
        } else {
            // Empty range.
            range = rangy.createRange();
        }

        if (range.collapsed) {
            range = range.cloneRange();
            range.selectNode(range.commonAncestorContainer);
        }

        nodes = range.getNodes();

        for (i = 0; i < nodes.length; i++) {
            node = Y.one(nodes[i]);
            if (this.editor.contains(node)) {
                results.push(node);
            }
        }
        return results;
    },

    /**
     * Check whether the current selection has changed since this method was last called.
     *
     * If the selection has changed, the atto:selectionchanged event is also fired.
     *
     * @method _hasSelectionChanged
     * @private
     * @param {EventFacade} e
     * @return {Boolean}
     */
    _hasSelectionChanged: function(e) {
        var selection = rangy.getSelection(),
            range,
            changed = false;

        if (selection.rangeCount) {
            range = selection.getRangeAt(0);
        } else {
            // Empty range.
            range = rangy.createRange();
        }

        if (this._lastSelection) {
            if (!this._lastSelection.equals(range)) {
                changed = true;
                return this._fireSelectionChanged(e);
            }
        }
        this._lastSelection = range;
        return changed;
    },

    /**
     * Fires the atto:selectionchanged event.
     *
     * When the selectionchanged event is fired, the following arguments are provided:
     *   - event : the original event that lead to this event being fired.
     *   - selectednodes :  an array containing nodes that are entirely selected of contain partially selected content.
     *
     * @method _fireSelectionChanged
     * @private
     * @param {EventFacade} e
     */
    _fireSelectionChanged: function(e) {
        this.fire('atto:selectionchanged', {
            event: e,
            selectedNodes: this.getSelectedNodes()
        });
    },

    /**
     * Get the DOM node representing the common anscestor of the selection nodes.
     *
     * @method getSelectionParentNode
     * @return {Element|boolean} The DOM Node for this parent, or false if no seletion was made.
     */
    getSelectionParentNode: function() {
        var selection = rangy.getSelection();
        if (selection.rangeCount) {
            return selection.getRangeAt(0).commonAncestorContainer;
        }
        return false;
    },

    /**
     * Set the current selection. Used to restore a selection.
     *
     * @method selection
     * @param {array} ranges A list of rangy.range objects in the selection.
     */
    setSelection: function(ranges) {
        var selection = rangy.getSelection();
        selection.setRanges(ranges);
    },

    /**
     * Inserts the given HTML into the editable content at the currently focused point.
     *
     * @method insertContentAtFocusPoint
     * @param {String} html
     */
    insertContentAtFocusPoint: function(html) {
        var selection = rangy.getSelection(),
            range,
            node = Y.Node.create(html);
        if (selection.rangeCount) {
            range = selection.getRangeAt(0);
        }
        if (range) {
            range.deleteContents();
            range.insertNode(node.getDOMNode());
        }
    }

};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorSelection]);
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
 * @module moodle-editor_atto-editor
 * @submodule styling
 */

/**
 * Editor styling functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorStyling
 */

function EditorStyling() {}

EditorStyling.ATTRS= {
};

EditorStyling.prototype = {
    /**
     * Disable CSS styling.
     *
     * @method disableCssStyling
     */
    disableCssStyling: function() {
        try {
            document.execCommand("styleWithCSS", 0, false);
        } catch (e1) {
            try {
                document.execCommand("useCSS", 0, true);
            } catch (e2) {
                try {
                    document.execCommand('styleWithCSS', false, false);
                } catch (e3) {
                    // We did our best.
                }
            }
        }
    },

    /**
     * Enable CSS styling.
     *
     * @method enableCssStyling
     */
    enableCssStyling: function() {
        try {
            document.execCommand("styleWithCSS", 0, true);
        } catch (e1) {
            try {
                document.execCommand("useCSS", 0, false);
            } catch (e2) {
                try {
                    document.execCommand('styleWithCSS', false, true);
                } catch (e3) {
                    // We did our best.
                }
            }
        }
    },

    /**
     * Change the formatting for the current selection.
     *
     * This will wrap the selection in span tags, adding the provided classes.
     *
     * If the selection covers multiple block elements, multiple spans will be inserted to preserve the original structure.
     *
     * @method toggleInlineSelectionClass
     * @param {Array} toggleclasses - Class names to be toggled on or off.
     */
    toggleInlineSelectionClass: function(toggleclasses) {
        var classname = toggleclasses.join(" ");
        var originalSelection = this.getSelection();
        var cssApplier = rangy.createCssClassApplier(classname, {normalize: true});

        cssApplier.toggleSelection();

        this.setSelection(originalSelection);
    },

    /**
     * Change the formatting for the current selection.
     *
     * This will set inline styles on the current selection.
     *
     * @method toggleInlineSelectionClass
     * @param {Array} styles - Style attributes to set on the nodes.
     */
    formatSelectionInlineStyle: function(styles) {
        var classname = this.PLACEHOLDER_CLASS;
        var originalSelection = this.getSelection();
        var cssApplier = rangy.createCssClassApplier(classname, {normalize: true});

        cssApplier.applyToSelection();

        this.editor.all('.' + classname).each(function (node) {
            node.removeClass(classname).setStyles(styles);
        }, this);

        this.setSelection(originalSelection);
    },

    /**
     * Change the formatting for the current selection.
     *
     * Also changes the selection to the newly formatted block (allows applying multiple styles to a block).
     *
     * @method formatSelectionBlock
     * @param {String} [blocktag] Change the block level tag to this. Empty string, means do not change the tag.
     * @param {Object} [attributes] The keys and values for attributes to be added/changed in the block tag.
     * @return {Node|boolean} The Node that was formatted if a change was made, otherwise false.
     */
    formatSelectionBlock: function(blocktag, attributes) {
        // First find the nearest ancestor of the selection that is a block level element.
        var selectionparentnode = this.getSelectionParentNode(),
            boundary,
            cell,
            nearestblock,
            newcontent,
            match,
            replacement;

        if (!selectionparentnode) {
            // No selection, nothing to format.
            return false;
        }

        boundary = this.editor;

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

        nearestblock = selectionparentnode.ancestor(this.BLOCK_TAGS.join(', '), true);
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
        var selection = this.getSelectionFromNode(nearestblock);
        this.setSelection(selection);

        return nearestblock;
    }

};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorStyling]);
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
 * @module moodle-editor_atto-editor
 * @submodule filepicker
 */

/**
 * Filepicker options for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorFilepicker
 */

function EditorFilepicker() {}

EditorFilepicker.ATTRS= {
    /**
     * The options for the filepicker.
     *
     * @attribute filepickeroptions
     * @type object
     * @default {}
     */
    filepickeroptions: {
        value: {}
    }
};

EditorFilepicker.prototype = {
    /**
     * Should we show the filepicker for this filetype?
     *
     * @method canShowFilepicker
     * @param string type The media type for the file picker.
     * @return {boolean}
     */
    canShowFilepicker: function(type) {
        return (typeof this.get('filepickeroptions')[type] !== 'undefined');
    },

    /**
     * Show the filepicker.
     *
     * This depends on core_filepicker, and then call that modules show function.
     *
     * @method showFilepicker
     * @param {string} type The media type for the file picker.
     * @param {function} callback The callback to use when selecting an item of media.
     * @param {object} [context] The context from which to call the callback.
     */
    showFilepicker: function(type, callback, context) {
        var self = this;
        Y.use('core_filepicker', function (Y) {
            var options = Y.clone(self.get('filepickeroptions')[type], true);
            options.formcallback = callback;
            if (context) {
                options.magicscope = context;
            }

            M.core_filepicker.show(Y, options);
        });
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorFilepicker]);


}, '@VERSION@', {
    "requires": [
        "node",
        "io",
        "overlay",
        "escape",
        "event",
        "event-simulate",
        "event-custom",
        "yui-throttle",
        "moodle-core-notification-dialogue",
        "moodle-editor_atto-rangy",
        "handlebars"
    ]
});
