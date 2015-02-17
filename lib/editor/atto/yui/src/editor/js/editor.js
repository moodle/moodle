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
    },
    rangy = window.rangy;

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

    /**
     * Event Handles to clear on editor destruction.
     *
     * @property _eventHandles
     * @private
     */
    _eventHandles: null,

    initializer: function() {
        var template;

        // Note - it is not safe to use a CSS selector like '#' + elementid because the id
        // may have colons in it - e.g.  quiz.
        this.textarea = Y.one(document.getElementById(this.get('elementid')));

        if (!this.textarea) {
            // No text area found.
            Y.log('Text area not found - unable to setup editor for ' + this.get('elementid'),
                    'error', LOGNAME);
            return;
        }

        this._eventHandles = [];

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

        // Use paragraphs not divs.
        if (document.queryCommandSupported('DefaultParagraphSeparator')) {
            document.execCommand('DefaultParagraphSeparator', false, 'p');
        }

        // Add the toolbar and editable zone to the page.
        this.textarea.get('parentNode').insert(this._wrapper, this.textarea).
                setAttribute('class', 'editor_atto_wrap');

        // Hide the old textarea.
        this.textarea.hide();

        // Copy the text to the contenteditable div.
        this.updateFromTextArea();

        // Publish the events that are defined by this editor.
        this.publishEvents();

        // Add handling for saving and restoring selections on cursor/focus changes.
        this.setupSelectionWatchers();

        // Add polling to update the textarea periodically when typing long content.
        this.setupAutomaticPolling();

        // Setup plugins.
        this.setupPlugins();

        // Initialize the auto-save timer.
        this.setupAutosave();
        // Preload the icons for the notifications.
        this.setupNotifications();
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

        return this;
    },

    /**
     * Set up automated polling of the text area to update the textarea.
     *
     * @method setupAutomaticPolling
     * @chainable
     */
    setupAutomaticPolling: function() {
        this._registerEventHandle(this.editor.on(['keyup', 'paste', 'cut'], this.updateOriginal, this));

        // Call this.updateOriginal after dropped content has been processed.
        this._registerEventHandle(this.editor.on('drop', this.updateOriginalDelayed, this));

        return this;
    },

    /**
     * Calls updateOriginal on a short timer to allow native event handlers to run first.
     *
     * @method updateOriginalDelayed
     * @chainable
     */
    updateOriginalDelayed: function() {
        Y.soon(Y.bind(this.updateOriginal, this));

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
                    Y.log("Plugin '" + plugin.name + "' could not be found - skipping initialisation", "warn", LOGNAME);
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
    },

    /**
     * Register an event handle for disposal in the destructor.
     *
     * @method _registerEventHandle
     * @param {EventHandle} The Event Handle as returned by Y.on, and Y.delegate.
     * @private
     */
    _registerEventHandle: function(handle) {
        this._eventHandles.push(handle);
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
         * The contextid of the form.
         *
         * @attribute contextid
         * @type Integer
         * @writeOnce
         */
        contextid: {
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
