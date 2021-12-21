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
/* eslint-disable no-unused-vars */

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
    BLOCK_TAGS: [
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
     * An indicator of the current input direction.
     *
     * @property coreDirection
     * @type string
     */
    coreDirection: null,

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

        var extraclasses = this.textarea.getAttribute('class');

        this._eventHandles = [];

        var description = Y.Node.create('<div class="sr-only">' + M.util.get_string('richtexteditor', 'editor_atto') + '</div>');
        this._wrapper = Y.Node.create('<div class="' + CSS.WRAPPER + '" role="application" />');
        this._wrapper.appendChild(description);
        this._wrapper.setAttribute('aria-describedby', description.generateID());
        template = Y.Handlebars.compile('<div id="{{elementid}}editable" ' +
                'contenteditable="true" ' +
                'role="textbox" ' +
                'spellcheck="true" ' +
                'aria-live="off" ' +
                'class="{{CSS.CONTENT}} ' + extraclasses + '" ' +
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

        // Set diretcion according to current page language.
        this.coreDirection = Y.one('body').hasClass('dir-rtl') ? 'rtl' : 'ltr';

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

        // Set up custom event for editor updated.
        Y.mix(Y.Node.DOM_EVENTS, {'form:editorUpdated': true});
        this.textarea.on('form:editorUpdated', function() {
            this.updateEditorState();
        }, this);

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
        this._registerEventHandle(this.editor.on(['keyup', 'cut'], this.updateOriginal, this));
        this._registerEventHandle(this.editor.on('paste', this.pasteCleanup, this));

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
     * Update the state of the editor.
     */
    updateEditorState: function() {
        var disabled = this.textarea.hasAttribute('readonly'),
            editorfield = Y.one('#' + this.get('elementid') + 'editable');
        // Enable/Disable all plugins.
        this._setPluginState(!disabled);
        // Enable/Disable content of editor.
        if (editorfield) {
            editorfield.setAttribute('contenteditable', !disabled);
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
 * A notify function for the Atto editor.
 *
 * @module     moodle-editor_atto-notify
 * @submodule  notify
 * @package    editor_atto
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var LOGNAME_NOTIFY = 'moodle-editor_atto-editor-notify',
    NOTIFY_INFO = 'info',
    NOTIFY_WARNING = 'warning';

function EditorNotify() {}

EditorNotify.ATTRS = {
};

EditorNotify.prototype = {

    /**
     * A single Y.Node for this editor. There is only ever one - it is replaced if a new message comes in.
     *
     * @property messageOverlay
     * @type {Node}
     */
    messageOverlay: null,

    /**
     * A single timer object that can be used to cancel the hiding behaviour.
     *
     * @property hideTimer
     * @type {timer}
     */
    hideTimer: null,

    /**
     * Initialize the notifications.
     *
     * @method setupNotifications
     * @chainable
     */
    setupNotifications: function() {
        var preload1 = new Image(),
            preload2 = new Image();

        preload1.src = M.util.image_url('i/warning', 'moodle');
        preload2.src = M.util.image_url('i/info', 'moodle');

        return this;
    },

    /**
     * Show a notification in a floaty overlay somewhere in the atto editor text area.
     *
     * @method showMessage
     * @param {String} message The translated message (use get_string)
     * @param {String} type Must be either "info" or "warning"
     * @param {Number} timeout Time in milliseconds to show this message for.
     * @chainable
     */
    showMessage: function(message, type, timeout) {
        var messageTypeIcon = '',
            intTimeout,
            bodyContent;

        if (this.messageOverlay === null) {
            this.messageOverlay = Y.Node.create('<div class="editor_atto_notification"></div>');

            this.messageOverlay.hide(true);
            this.textarea.get('parentNode').append(this.messageOverlay);

            this.messageOverlay.on('click', function() {
                this.messageOverlay.hide(true);
            }, this);
        }

        if (this.hideTimer !== null) {
            this.hideTimer.cancel();
        }

        if (type === NOTIFY_WARNING) {
            messageTypeIcon = '<img src="' +
                              M.util.image_url('i/warning', 'moodle') +
                              '" alt="' + M.util.get_string('warning', 'moodle') + '"/>';
        } else if (type === NOTIFY_INFO) {
            messageTypeIcon = '<img src="' +
                              M.util.image_url('i/info', 'moodle') +
                              '" alt="' + M.util.get_string('info', 'moodle') + '"/>';
        } else {
            Y.log('Invalid message type specified: ' + type + '. Must be either "info" or "warning".', 'debug', LOGNAME_NOTIFY);
        }

        // Parse the timeout value.
        intTimeout = parseInt(timeout, 10);
        if (intTimeout <= 0) {
            intTimeout = 60000;
        }

        // Convert class to atto_info (for example).
        type = 'atto_' + type;

        bodyContent = Y.Node.create('<div class="' + type + '" role="alert" aria-live="assertive">' +
                                        messageTypeIcon + ' ' +
                                        Y.Escape.html(message) +
                                        '</div>');
        this.messageOverlay.empty();
        this.messageOverlay.append(bodyContent);
        this.messageOverlay.show(true);

        this.hideTimer = Y.later(intTimeout, this, function() {
            Y.log('Hide Atto notification.', 'debug', LOGNAME_NOTIFY);
            this.hideTimer = null;
            if (this.messageOverlay.inDoc()) {
                this.messageOverlay.hide(true);
            }
        });

        return this;
    }

};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorNotify]);
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

EditorTextArea.ATTRS = {
};

EditorTextArea.prototype = {

    /**
     * Return the appropriate empty content value for the current browser.
     *
     * Different browsers use a different content when they are empty and
     * we must set this reliable across the board.
     *
     * @method _getEmptyContent
     * @return String The content to use representing no user-provided content
     * @private
     */
    _getEmptyContent: function() {
        var alignment;
        if (this.coreDirection === 'rtl') {
            alignment = 'style="text-align: right;"';
        } else {
            alignment = 'style="text-align: left;"';
        }
        if (Y.UA.ie && Y.UA.ie < 10) {
            return '<p dir="' + this.coreDirection + '" ' + alignment + '></p>';
        } else {
            return '<p dir="' + this.coreDirection + '" ' + alignment + '><br></p>';
        }
    },

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

        // Copy cleaned HTML to editable div.
        this.editor.append(this._cleanHTML(this.textarea.get('value'), true));

        // Insert a paragraph in the empty contenteditable div.
        if (this.editor.getHTML() === '') {
            this.editor.setHTML(this._getEmptyContent());
        }

        return this;
    },

    /**
     * Copy the text from the contenteditable to the textarea which it replaced.
     *
     * @method updateOriginal
     * @chainable
     */
    updateOriginal: function() {
        // Get the previous and current value to compare them.
        var oldValue = this.textarea.get('value'),
            newValue = this.getCleanHTML();

        if (newValue === "" && this.isActive()) {
            // The content was entirely empty so get the empty content placeholder.
            newValue = this._getEmptyContent();
        }

        // Only call this when there has been an actual change to reduce processing.
        if (oldValue !== newValue) {
            // Insert the cleaned content.
            this.textarea.set('value', newValue);

            // Trigger the onchange callback on the textarea, essentially to notify moodle-core-formchangechecker.
            this.textarea.simulate('change');

            // Trigger handlers for this action.
            this.fire('change');
        }

        return this;
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
/* eslint-disable no-unused-vars */

/**
 * A autosave function for the Atto editor.
 *
 * @module     moodle-editor_atto-autosave
 * @submodule  autosave-base
 * @package    editor_atto
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var SUCCESS_MESSAGE_TIMEOUT = 5000,
    RECOVER_MESSAGE_TIMEOUT = 60000,
    LOGNAME_AUTOSAVE = 'moodle-editor_atto-editor-autosave';

function EditorAutosave() {}

EditorAutosave.ATTRS = {
    /**
     * Enable/Disable auto save for this instance.
     *
     * @attribute autosaveEnabled
     * @type Boolean
     * @writeOnce
     */
    autosaveEnabled: {
        value: true,
        writeOnce: true
    },

    /**
     * The time between autosaves (in seconds).
     *
     * @attribute autosaveFrequency
     * @type Number
     * @default 60
     * @writeOnce
     */
    autosaveFrequency: {
        value: 60,
        writeOnce: true
    },

    /**
     * Unique hash for this page instance. Calculated from $PAGE->url in php.
     *
     * @attribute pageHash
     * @type String
     * @writeOnce
     */
    pageHash: {
        value: '',
        writeOnce: true
    }
};

EditorAutosave.prototype = {

    /**
     * The text that was auto saved in the last request.
     *
     * @property lastText
     * @type string
     */
    lastText: "",

    /**
     * Autosave instance.
     *
     * @property autosaveInstance
     * @type string
     */
    autosaveInstance: null,

    /**
     * Autosave Timer.
     *
     * @property autosaveTimer
     * @type object
     */
    autosaveTimer: null,

    /**
     * Initialize the autosave process
     *
     * @method setupAutosave
     * @chainable
     */
    setupAutosave: function() {
        var draftid = -1,
            form,
            optiontype = null,
            options = this.get('filepickeroptions'),
            params;

        if (!this.get('autosaveEnabled')) {
            // Autosave disabled for this instance.
            return;
        }

        this.autosaveInstance = Y.stamp(this);
        for (optiontype in options) {
            if (typeof options[optiontype].itemid !== "undefined") {
                draftid = options[optiontype].itemid;
            }
        }

        // First see if there are any saved drafts.
        // Make an ajax request.
        params = {
            contextid: this.get('contextid'),
            action: 'resume',
            draftid: draftid,
            elementid: this.get('elementid'),
            pageinstance: this.autosaveInstance,
            pagehash: this.get('pageHash')
        };

        this.autosaveIo(params, this, {
            success: function(response) {
                if (response === null) {
                    // This can happen when there is nothing to resume from.
                    return;
                } else if (!response) {
                    Y.log('Invalid response received.', 'debug', LOGNAME_AUTOSAVE);
                    return;
                }

                // Revert untouched editor contents to an empty string.
                var emptyContents = [
                    // For FF and Chrome.
                    '<p></p>',
                    '<p><br></p>',
                    '<br>',
                    '<p dir="rtl" style="text-align: right;"></p>',
                    '<p dir="rtl" style="text-align: right;"><br></p>',
                    '<p dir="ltr" style="text-align: left;"></p>',
                    '<p dir="ltr" style="text-align: left;"><br></p>',
                    // For IE 9 and 10.
                    '<p>&nbsp;</p>',
                    '<p><br>&nbsp;</p>',
                    '<p dir="rtl" style="text-align: right;">&nbsp;</p>',
                    '<p dir="rtl" style="text-align: right;"><br>&nbsp;</p>',
                    '<p dir="ltr" style="text-align: left;">&nbsp;</p>',
                    '<p dir="ltr" style="text-align: left;"><br>&nbsp;</p>'
                ];
                if (emptyContents.includes(response.result)) {
                    response.result = '';
                }

                if (response.error || typeof response.result === 'undefined') {
                    Y.log('Error occurred recovering draft text: ' + response.error, 'debug', LOGNAME_AUTOSAVE);
                    this.showMessage(M.util.get_string('errortextrecovery', 'editor_atto'),
                            NOTIFY_WARNING, RECOVER_MESSAGE_TIMEOUT);
                } else if (response.result !== this.textarea.get('value') &&
                        response.result !== '') {
                    Y.log('Autosave text found - recover it.', 'debug', LOGNAME_AUTOSAVE);
                    this.recoverText(response.result);
                }
                this._fireSelectionChanged();

            },
            failure: function() {
                this.showMessage(M.util.get_string('errortextrecovery', 'editor_atto'),
                        NOTIFY_WARNING, RECOVER_MESSAGE_TIMEOUT);
            }
        });

        // Now setup the timer for periodic saves.
        var delay = parseInt(this.get('autosaveFrequency'), 10) * 1000;
        this.autosaveTimer = Y.later(delay, this, this.saveDraft, false, true);

        // Now setup the listener for form submission.
        form = this.textarea.ancestor('form');
        if (form) {
            this.autosaveIoOnSubmit(form, {
                action: 'reset',
                contextid: this.get('contextid'),
                elementid: this.get('elementid'),
                pageinstance: this.autosaveInstance,
                pagehash: this.get('pageHash')
            });
        }
        return this;
    },

    /**
     * Recover a previous version of this text and show a message.
     *
     * @method recoverText
     * @param {String} text
     * @chainable
     */
    recoverText: function(text) {
        this.editor.setHTML(text);
        this.saveSelection();
        this.updateOriginal();
        this.lastText = text;

        this.showMessage(M.util.get_string('textrecovered', 'editor_atto'),
                NOTIFY_INFO, RECOVER_MESSAGE_TIMEOUT);

        // Fire an event that the editor content has changed.
        require(['core_editor/events'], function(editorEvents) {
            editorEvents.notifyEditorContentRestored(this.editor.getDOMNode());
        }.bind(this));

        return this;
    },

    /**
     * Save a single draft via ajax.
     *
     * @method saveDraft
     * @chainable
     */
    saveDraft: function() {
        var url, params;

        if (!this.editor.getDOMNode()) {
            // Stop autosaving if the editor was removed from the page.
            this.autosaveTimer.cancel();
            return;
        }
        // Only copy the text from the div to the textarea if the textarea is not currently visible.
        if (!this.editor.get('hidden')) {
            this.updateOriginal();
        }
        var newText = this.textarea.get('value');

        if (newText !== this.lastText) {
            Y.log('Autosave text', 'debug', LOGNAME_AUTOSAVE);

            // Make an ajax request.
            url = M.cfg.wwwroot + this.get('autosaveAjaxScript');
            params = {
                sesskey: M.cfg.sesskey,
                contextid: this.get('contextid'),
                action: 'save',
                drafttext: newText,
                elementid: this.get('elementid'),
                pagehash: this.get('pageHash'),
                pageinstance: this.autosaveInstance
            };

            // Reusable error handler - must be passed the correct context.
            var ajaxErrorFunction = function(response) {
                var errorDuration = parseInt(this.get('autosaveFrequency'), 10) * 1000;
                Y.log('Error while autosaving text', 'warn', LOGNAME_AUTOSAVE);
                Y.log(response, 'warn', LOGNAME_AUTOSAVE);
                this.showMessage(M.util.get_string('autosavefailed', 'editor_atto'), NOTIFY_WARNING, errorDuration);
            };

            this.autosaveIo(params, this, {
                failure: ajaxErrorFunction,
                success: function(response) {
                    if (response && response.error) {
                        Y.soon(Y.bind(ajaxErrorFunction, this, [response]));
                    } else {
                        // All working.
                        this.lastText = newText;
                        this.showMessage(M.util.get_string('autosavesucceeded', 'editor_atto'),
                                NOTIFY_INFO, SUCCESS_MESSAGE_TIMEOUT);
                    }
                }
            });
        }
        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorAutosave]);
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
 * A autosave function for the Atto editor.
 *
 * @module     moodle-editor_atto-autosave-io
 * @submodule  autosave-io
 * @package    editor_atto
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var EditorAutosaveIoDispatcherInstance = null;

function EditorAutosaveIoDispatcher() {
    EditorAutosaveIoDispatcher.superclass.constructor.apply(this, arguments);
    this._submitEvents = {};
    this._queue = [];
    this._throttle = null;
}
EditorAutosaveIoDispatcher.NAME = 'EditorAutosaveIoDispatcher';
EditorAutosaveIoDispatcher.ATTRS = {

    /**
     * The relative path to the ajax script.
     *
     * @attribute autosaveAjaxScript
     * @type String
     * @default '/lib/editor/atto/autosave-ajax.php'
     * @readOnly
     */
    autosaveAjaxScript: {
        value: '/lib/editor/atto/autosave-ajax.php',
        readOnly: true
    },

    /**
     * The time buffer for the throttled requested.
     *
     * @attribute delay
     * @type Number
     * @default 50
     * @readOnly
     */
    delay: {
        value: 50,
        readOnly: true
    }

};
Y.extend(EditorAutosaveIoDispatcher, Y.Base, {

    /**
     * Dispatch an IO request.
     *
     * This method will put the requests in a queue in order to attempt to bulk them.
     *
     * @param  {Object} params    The parameters of the request.
     * @param  {Object} context   The context in which the callbacks are called.
     * @param  {Object} callbacks Object with 'success', 'complete', 'end', 'failure' and 'start' as
     *                            optional keys defining the callbacks to call. Success and Complete
     *                            functions will receive the response as parameter. Success and Complete
     *                            may receive an object containing the error key, use this to confirm
     *                            that no errors occured.
     * @return {Void}
     */
    dispatch: function(params, context, callbacks) {
        if (this._throttle) {
            this._throttle.cancel();
        }

        this._throttle = Y.later(this.get('delay'), this, this._processDispatchQueue);
        this._queue.push([params, context, callbacks]);
    },

    /**
     * Dispatches the requests in the queue.
     *
     * @return {Void}
     */
    _processDispatchQueue: function() {
        var queue = this._queue,
            data = {};

        this._queue = [];
        if (queue.length < 1) {
            return;
        }

        Y.Array.each(queue, function(item, index) {
            data[index] = item[0];
        });

        Y.io(M.cfg.wwwroot + this.get('autosaveAjaxScript'), {
            method: 'POST',
            data: Y.QueryString.stringify({
                actions: data,
                sesskey: M.cfg.sesskey
            }),
            on: {
                start: this._makeIoEventCallback('start', queue),
                complete: this._makeIoEventCallback('complete', queue),
                failure: this._makeIoEventCallback('failure', queue),
                end: this._makeIoEventCallback('end', queue),
                success: this._makeIoEventCallback('success', queue)
            }
        });
    },

    /**
     * Creates a function that dispatches an IO response to callbacks.
     *
     * @param  {String} event The type of event.
     * @param  {Array} queue The queue.
     * @return {Function}
     */
    _makeIoEventCallback: function(event, queue) {
        var noop = function() {};
        return function() {
            var response = arguments[1],
                parsed = {};

            if ((event == 'complete' || event == 'success') && (typeof response !== 'undefined'
                    && typeof response.responseText !== 'undefined' && response.responseText !== '')) {

                // Success and complete events need to parse the response.
                parsed = JSON.parse(response.responseText) || {};
            }

            Y.Array.each(queue, function(item, index) {
                var context = item[1],
                    cb = (item[2] && item[2][event]) || noop,
                    arg;

                if (parsed && parsed.error) {
                    // The response is an error, we send it to everyone.
                    arg = parsed;
                } else if (parsed) {
                    // The response was parsed, we only communicate the relevant portion of the response.
                    arg = parsed[index];
                }

                cb.apply(context, [arg]);
            });
        };
    },

    /**
     * Form submit handler.
     *
     * @param  {EventFacade} e The event.
     * @return {Void}
     */
    _onSubmit: function(e) {
        var data = {},
            id = e.currentTarget.generateID(),
            params = this._submitEvents[id];

        if (!params || params.ios.length < 1) {
            return;
        }

        Y.Array.each(params.ios, function(param, index) {
            data[index] = param;
        });

        Y.io(M.cfg.wwwroot + this.get('autosaveAjaxScript'), {
            method: 'POST',
            data: Y.QueryString.stringify({
                actions: data,
                sesskey: M.cfg.sesskey
            }),
            sync: true
        });
    },

    /**
     * Registers a request to be made on form submission.
     *
     * @param  {Node} node The forum node we will listen to.
     * @param  {Object} params Parameters for the IO request.
     * @return {Void}
     */
    whenSubmit: function(node, params) {
        if (typeof this._submitEvents[node.generateID()] === 'undefined') {
            this._submitEvents[node.generateID()] = {
                event: node.on('submit', this._onSubmit, this),
                ajaxEvent: node.on(M.core.event.FORM_SUBMIT_AJAX, this._onSubmit, this),
                ios: []
            };
        }
        this._submitEvents[node.get('id')].ios.push([params]);
    }

});
EditorAutosaveIoDispatcherInstance = new EditorAutosaveIoDispatcher();


function EditorAutosaveIo() {}
EditorAutosaveIo.prototype = {

    /**
     * Dispatch an IO request.
     *
     * This method will put the requests in a queue in order to attempt to bulk them.
     *
     * @param  {Object} params    The parameters of the request.
     * @param  {Object} context   The context in which the callbacks are called.
     * @param  {Object} callbacks Object with 'success', 'complete', 'end', 'failure' and 'start' as
     *                            optional keys defining the callbacks to call. Success and Complete
     *                            functions will receive the response as parameter. Success and Complete
     *                            may receive an object containing the error key, use this to confirm
     *                            that no errors occured.
     * @return {Void}
     */
    autosaveIo: function(params, context, callbacks) {
        EditorAutosaveIoDispatcherInstance.dispatch(params, context, callbacks);
    },

    /**
     * Registers a request to be made on form submission.
     *
     * @param  {Node} form The forum node we will listen to.
     * @param  {Object} params Parameters for the IO request.
     * @return {Void}
     */
    autosaveIoOnSubmit: function(form, params) {
        EditorAutosaveIoDispatcherInstance.whenSubmit(form, params);
    }

};
Y.Base.mix(Y.M.editor_atto.Editor, [EditorAutosaveIo]);
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

EditorClean.ATTRS = {
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
        var editorClone = this.editor.cloneNode(true),
            html;

        // Remove all YUI IDs.
        Y.each(editorClone.all('[id^="yui"]'), function(node) {
            node.removeAttribute('id');
        });

        editorClone.all('.atto_control').remove(true);
        html = editorClone.get('innerHTML');

        // Revert untouched editor contents to an empty string.
        var emptyContents = [
            // For FF and Chrome.
            '<p></p>',
            '<p><br></p>',
            '<br>',
            '<p dir="rtl" style="text-align: right;"></p>',
            '<p dir="rtl" style="text-align: right;"><br></p>',
            '<p dir="ltr" style="text-align: left;"></p>',
            '<p dir="ltr" style="text-align: left;"><br></p>',
            // For IE 9 and 10.
            '<p>&nbsp;</p>',
            '<p><br>&nbsp;</p>',
            '<p dir="rtl" style="text-align: right;">&nbsp;</p>',
            '<p dir="rtl" style="text-align: right;"><br>&nbsp;</p>',
            '<p dir="ltr" style="text-align: left;">&nbsp;</p>',
            '<p dir="ltr" style="text-align: left;"><br>&nbsp;</p>'
        ];
        if (emptyContents.includes(html)) {
            return '';
        }

        // Remove any and all nasties from source.
        return this._cleanHTML(html);
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
     * @param {Boolean} deepClean If true, do a more in depth (and resource intensive) cleaning of the HTML.
     * @return {String} The cleaned HTML
     */
    _cleanHTML: function(content, deepClean) {
        // Removing limited things that can break the page or a disallowed, like unclosed comments, style blocks, etc.

        var rules = [
            // Remove any style blocks. Some browsers do not work well with them in a contenteditable.
            // Plus style blocks are not allowed in body html, except with "scoped", which most browsers don't support as of 2015.
            // Reference: "http://stackoverflow.com/questions/1068280/javascript-regex-multiline-flag-doesnt-work"
            {regex: /<style[^>]*>[\s\S]*?<\/style>/gi, replace: ""},

            // Remove any open HTML comment opens that are not followed by a close. This can completely break page layout.
            {regex: /<!--(?![\s\S]*?-->)/gi, replace: ""},

            // Source: "http://www.codinghorror.com/blog/2006/01/cleaning-words-nasty-html.html"
            // Remove forbidden tags for content, title, meta, style, st0-9, head, font, html, body, link.
            {regex: /<\/?(?:title|meta|style|st\d|head\b|font|html|body|link)[^>]*?>/gi, replace: ""}
        ];

        content = this._filterContentWithRules(content, rules);

        if (deepClean) {
            content = this._cleanHTMLLists(content);
        }

        return content;
    },

    /**
     * Take the supplied content and run on the supplied regex rules.
     *
     * @method _filterContentWithRules
     * @private
     * @param {String} content The content to clean
     * @param {Array} rules An array of structures: [ {regex: /something/, replace: "something"}, {...}, ...]
     * @return {String} The cleaned content
     */
    _filterContentWithRules: function(content, rules) {
        var i = 0;
        for (i = 0; i < rules.length; i++) {
            content = content.replace(rules[i].regex, rules[i].replace);
        }

        return content;
    },

    /**
     * Intercept and clean html paste events.
     *
     * @method pasteCleanup
     * @param {Object} sourceEvent The YUI EventFacade  object
     * @return {Boolean} True if the passed event should continue, false if not.
     */
    pasteCleanup: function(sourceEvent) {
        // We only expect paste events, but we will check anyways.
        if (sourceEvent.type === 'paste') {
            // Register the delayed paste cleanup. We will cancel it if we register the fallback cleanup.
            var delayedCleanup = this.postPasteCleanupDelayed();
            // The YUI event wrapper doesn't provide paste event info, so we need the underlying event.
            var event = sourceEvent._event;
            // Check if we have a valid clipboardData object in the event.
            // IE has a clipboard object at window.clipboardData, but as of IE 11, it does not provide HTML content access.
            if (event && event.clipboardData && event.clipboardData.getData && event.clipboardData.types) {
                // Check if there is HTML type to be pasted, if we can get it, we want to scrub before insert.
                var types = event.clipboardData.types;
                var isHTML = false;
                // Different browsers use different containers to hold the types, so test various functions.
                if (typeof types.contains === 'function') {
                    isHTML = types.contains('text/html');
                } else if (typeof types.indexOf === 'function') {
                    isHTML = (types.indexOf('text/html') > -1);
                }

                var content;
                if (isHTML) {
                    // Get the clipboard content.
                    try {
                        content = event.clipboardData.getData('text/html');
                    } catch (error) {
                        // Something went wrong. Fallback.
                        delayedCleanup.cancel();
                        this.fallbackPasteCleanupDelayed();
                        return true;
                    }

                    // Stop the original paste.
                    sourceEvent.preventDefault();

                    // Scrub the paste content.
                    content = this._cleanPasteHTML(content);

                    // Insert the content.
                    this.insertContentAtFocusPoint(content);

                    // Update the text area.
                    this.updateOriginal();
                    return false;
                } else {
                    try {
                        // Plaintext clipboard content can only be retrieved this way.
                        content = event.clipboardData.getData('text');
                    } catch (error) {
                        // Something went wrong. Fallback.
                        // Due to poor cross browser clipboard compatibility, the failure to find html doesn't mean it isn't there.
                        // Wait for the clipboard event to finish then fallback clean the entire editor.
                        delayedCleanup.cancel();
                        this.fallbackPasteCleanupDelayed();
                        return true;
                    }
                }
            } else {
                // If we reached a here, this probably means the browser has limited (or no) clipboard support.
                // Wait for the clipboard event to finish then fallback clean the entire editor.
                this.fallbackPasteCleanupDelayed();
                return true;
            }
        }

        // We should never get here - we must have received a non-paste event for some reason.
        // Um, just call updateOriginalDelayed() - it's safe.
        this.updateOriginalDelayed();
        return true;
    },

    /**
     * Calls postPasteCleanup on a short timer to allow the paste event handlers to complete, then deep clean the content.
     *
     * @method postPasteCleanupDelayed
     * @return {object}
     * @chainable
     */
    postPasteCleanupDelayed: function() {
        Y.soon(Y.bind(this.postPasteCleanup, this));

        return this;
    },

    /**
     * Do additional cleanup after the paste is complete.
     *
     * @method postPasteCleanup
     * @return {object}
     * @chainable
     */
    postPasteCleanup: function() {
        Y.log('Executing delayed post paste cleanup', 'debug', LOGNAME);

        // Save the current selection (cursor position).
        var selection = window.rangy.saveSelection();

        // Get, clean, and replace the content in the editable.
        var content = this.editor.get('innerHTML');
        this.editor.set('innerHTML', this._cleanHTML(content, true));

        // Update the textarea.
        this.updateOriginal();

        // Restore the selection (cursor position).
        window.rangy.restoreSelection(selection);

        return this;
    },

    /**
     * Cleanup code after a paste event if we couldn't intercept the paste content.
     *
     * @method fallbackPasteCleanup
     * @return {object}
     * @chainable
     */
    fallbackPasteCleanup: function() {
        Y.log('Using fallbackPasteCleanup for atto cleanup', 'debug', LOGNAME);

        // Save the current selection (cursor position).
        var selection = window.rangy.saveSelection();

        // Get, clean, and replace the content in the editable.
        var content = this.editor.get('innerHTML');
        this.editor.set('innerHTML', this._cleanHTML(this._cleanPasteHTML(content), true));

        // Update the textarea.
        this.updateOriginal();

        // Restore the selection (cursor position).
        window.rangy.restoreSelection(selection);

        return this;
    },

    /**
     * Calls fallbackPasteCleanup on a short timer to allow the paste event handlers to complete.
     *
     * @method fallbackPasteCleanupDelayed
     * @chainable
     */
    fallbackPasteCleanupDelayed: function() {
        Y.soon(Y.bind(this.fallbackPasteCleanup, this));

        return this;
    },

    /**
     * Cleanup html that comes from WYSIWYG paste events. These are more likely to contain messy code that we should strip.
     *
     * @method _cleanPasteHTML
     * @private
     * @param {String} content The html content to clean
     * @return {String} The cleaned HTML
     */
    _cleanPasteHTML: function(content) {
        // Return an empty string if passed an invalid or empty object.
        if (!content || content.length === 0) {
            return "";
        }

        // Rules that get rid of the real-nasties and don't care about normalize code (correct quotes, white spaces, etc).
        var rules = [
            // Stuff that is specifically from MS Word and similar office packages.
            // Remove all garbage after closing html tag.
            {regex: /<\s*\/html\s*>([\s\S]+)$/gi, replace: ""},
            // Remove if comment blocks.
            {regex: /<!--\[if[\s\S]*?endif\]-->/gi, replace: ""},
            // Remove start and end fragment comment blocks.
            {regex: /<!--(Start|End)Fragment-->/gi, replace: ""},
            // Remove any xml blocks.
            {regex: /<xml[^>]*>[\s\S]*?<\/xml>/gi, replace: ""},
            // Remove any <?xml><\?xml> blocks.
            {regex: /<\?xml[^>]*>[\s\S]*?<\\\?xml>/gi, replace: ""},
            // Remove <o:blah>, <\o:blah>.
            {regex: /<\/?\w+:[^>]*>/gi, replace: ""}
        ];

        // Apply the first set of harsher rules.
        content = this._filterContentWithRules(content, rules);

        // Apply the standard rules, which mainly cleans things like headers, links, and style blocks.
        content = this._cleanHTML(content);

        // Check if the string is empty or only contains whitespace.
        if (content.length === 0 || !content.match(/\S/)) {
            return content;
        }

        // Now we let the browser normalize the code by loading it into the DOM and then get the html back.
        // This gives us well quoted, well formatted code to continue our work on. Word may provide very poorly formatted code.
        var holder = document.createElement('div');
        holder.innerHTML = content;
        content = holder.innerHTML;
        // Free up the DOM memory.
        holder.innerHTML = "";

        // Run some more rules that care about quotes and whitespace.
        rules = [
            // Get all class attributes so we can work on them.
            {regex: /(<[^>]*?class\s*?=\s*?")([^>"]*)(")/gi, replace: function(match, group1, group2, group3) {
                    // Remove MSO classes.
                    group2 = group2.replace(/(?:^|[\s])[\s]*MSO[_a-zA-Z0-9\-]*/gi, "");
                    // Remove Apple- classes.
                    group2 = group2.replace(/(?:^|[\s])[\s]*Apple-[_a-zA-Z0-9\-]*/gi, "");
                    return group1 + group2 + group3;
                }},
            // Remove OLE_LINK# anchors that may litter the code.
            {regex: /<a [^>]*?name\s*?=\s*?"OLE_LINK\d*?"[^>]*?>\s*?<\/a>/gi, replace: ""}
        ];

        // Clean all style attributes from the text.
        content = this._cleanStyles(content);

        // Apply the rules.
        content = this._filterContentWithRules(content, rules);

        // Reapply the standard cleaner to the content.
        content = this._cleanHTML(content);

        // Clean unused spans out of the content.
        content = this._cleanSpans(content);

        return content;
    },

    /**
     * Clean all inline styles from pasted text.
     *
     * This code intentionally doesn't use YUI Nodes. YUI was quite a bit slower at this, so using raw DOM objects instead.
     *
     * @method _cleanStyles
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned HTML
     */
    _cleanStyles: function(content) {
        var holder = document.createElement('div');
        holder.innerHTML = content;
        var elementsWithStyle = holder.querySelectorAll('[style]');
        var i = 0;

        for (i = 0; i < elementsWithStyle.length; i++) {
            elementsWithStyle[i].removeAttribute('style');
        }

        var elementsWithClass = holder.querySelectorAll('[class]');
        for (i = 0; i < elementsWithClass.length; i++) {
            elementsWithClass[i].removeAttribute('class');
        }

        return holder.innerHTML;
    },
    /**
     * Clean empty or un-unused spans from passed HTML.
     *
     * This code intentionally doesn't use YUI Nodes. YUI was quite a bit slower at this, so using raw DOM objects instead.
     *
     * @method _cleanSpans
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned HTML
     */
    _cleanSpans: function(content) {
        // Return an empty string if passed an invalid or empty object.
        if (!content || content.length === 0) {
            return "";
        }
        // Check if the string is empty or only contains whitespace.
        if (content.length === 0 || !content.match(/\S/)) {
            return content;
        }

        var rules = [
            // Remove unused class, style, or id attributes. This will make empty tag detection easier later.
            {regex: /(<[^>]*?)(?:[\s]*(?:class|style|id)\s*?=\s*?"\s*?")+/gi, replace: "$1"}
        ];
        // Apply the rules.
        content = this._filterContentWithRules(content, rules);

        // Reference: "http://stackoverflow.com/questions/8131396/remove-nested-span-without-id"

        // This is better to run detached from the DOM, so the browser doesn't try to update on each change.
        var holder = document.createElement('div');
        holder.innerHTML = content;
        var spans = holder.getElementsByTagName('span');

        // Since we will be removing elements from the list, we should copy it to an array, making it static.
        var spansarr = Array.prototype.slice.call(spans, 0);

        spansarr.forEach(function(span) {
            if (!span.hasAttributes()) {
                // If no attributes (id, class, style, etc), this span is has no effect.
                // Move each child (if they exist) to the parent in place of this span.
                while (span.firstChild) {
                    span.parentNode.insertBefore(span.firstChild, span);
                }

                // Remove the now empty span.
                span.parentNode.removeChild(span);
            }
        });

        return holder.innerHTML;
    },

    /**
     * This is a function that searches for, and attempts to correct certain issues with ul/ol html lists.
     * This is needed because these lists are used heavily in page layout, and content with bad tags can
     * lead to broke course pages.
     *
     * The theory of operation here is to linearly process the incoming content, counting the opening and closing
     * of list tags, and determining when there is a mismatch.
     *
     * The specific issues this should be able to correct are:
     * - Orphaned li elements will be wrapped in a set of ul tags.
     * - li elements inside li elements.
     * - An extra closing ul, or ol tag will be discarded.
     * - An extra closing li tag will have an opening tag added if appropriate, or will be discarded.
     * - If there is an unmatched list open tag, a matching close tag will be inserted.
     *
     * It does it's best to match the case of corrected tags. Even though not required by html spec,
     * it seems like the safer route.
     *
     * A note on parent elements of li. This code assumes that li must have a ol or ul parent.
     * There are two other potential other parents of li. They are menu and dir. The dir tag was deprecated in
     * HTML4, and removed in HTML5. The menu tag is experimental as of this writing, and basically doesn't work
     * in any browsers, even Firefox, which theoretically has limited support for it. If other parents of li
     * become viable, they will need to be added to this code.
     *
     * @method _cleanHTMLLists
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned content
     */
    _cleanHTMLLists: function(content) {
        var output = '',
            toProcess = content,
            match = null,
            openTags = [],
            currentTag = null,
            previousTag = null;

        // Use a regular expression to find the next open or close li, ul, or ol tag.
        // Keep going until there are no more matching tags left.
        while ((match = toProcess.match(/<(\/?)(li|ul|ol)[^>]*>/i))) {
            currentTag = {
                tag: match[2],
                tagLowerCase: match[2].toLowerCase(),
                fullTag: match[0],
                isOpen: (match[1].length == 1) ? false : true
            };

            // Get the most recent open tag.
            previousTag = (openTags.length) ? openTags[openTags.length - 1] : null;

            // Slice up the content based on the match and add content before the match to output.
            output += toProcess.slice(0, match.index);
            toProcess = toProcess.slice(match.index + match[0].length);

            // Now the full content is in output + currentTag.fullTag + toProcess. When making fixes, it is best to push the fix and
            // fullTag back onto the front or toProcess, then restart the loop. This allows processing to follow the normal path
            // most often. But sometimes we will need to modify output to insert or remove tags in the already complete code.

            if (currentTag.isOpen) {
                // We are at the opening phase of a tag.
                // We have to do special processing for list items, as they can only be children of ul and ol tags.
                if (currentTag.tagLowerCase === 'li') {
                    if (!previousTag) {
                        // This means we have are opening a li, but aren't in a list. This is not allowed!

                        // We are going to check for the count of open and close ol tags ahead to decide what to do.
                        var closeCount = (toProcess.match(/<\/(ol)[ >]/ig) || []).length;
                        var openCount = (toProcess.match(/<(ol)[ >]/ig) || []).length;

                        if (closeCount > openCount) {
                            // There are more close ol's ahead than opens ahead. So open the ol and try again.
                            Y.log('Adding an opening ol for orphan li', 'debug', LOGNAME);
                            toProcess = '<ol>' + currentTag.fullTag + toProcess;
                            continue;
                        }

                        // For the other cases, just open a ul and try again. Later the closing ul will get matched if it exists,
                        // or if it doesn't one will automatically get inserted.
                        Y.log('Adding an opening ul for orphan li', 'debug', LOGNAME);
                        toProcess = '<ul>' + currentTag.fullTag + toProcess;
                        continue;
                    }

                    if (previousTag.tagLowerCase === 'li') {
                        // You aren't allowed to nest li tags. Close the current one before starting the new one.
                        Y.log('Adding a closing ' + previousTag.tag + ' before opening a new one.', 'debug', LOGNAME);
                        toProcess = '</' + previousTag.tag + '>' + currentTag.fullTag + toProcess;
                        continue;
                    }

                    // Previous tag must be a list at this point, so we can continue.
                }

                // If we made it this far, record the tag to the open tags list.
                openTags.push({
                    tag: currentTag.tag,
                    tagLowerCase: currentTag.tagLowerCase,
                    position: output.length,
                    length: currentTag.fullTag.length
                });
            } else {
                // We are processing a closing tag.

                if (openTags.length == 0) {
                    // We are closing a tag that isn't open. That's a problem. Just discarding should be safe.
                    Y.log('Discarding extra ' + currentTag.fullTag + ' tag.', 'debug', LOGNAME);
                    continue;
                }

                if (previousTag.tagLowerCase === currentTag.tagLowerCase) {
                    // Closing a tag that matches the open tag. This is the nominal case. Pop it off, and update previousTag.
                    if (currentTag.tag != previousTag.tag) {
                        // This would mean cases don't match between the opening and closing tag.
                        // We are going to swap them to match, even though not required.
                        currentTag.fullTag = currentTag.fullTag.replace(currentTag.tag, previousTag.tag);
                    }

                    openTags.pop();
                    previousTag = (openTags.length) ? openTags[openTags.length - 1] : null;
                } else {
                    // We are closing a tag that isn't the most recent open one open, so we have a mismatch.
                    if (currentTag.tagLowerCase === 'li' && previousTag.liEnd && (previousTag.liEnd < output.length)) {
                        // We are closing an unopened li, but the parent list has complete li tags more than 0 chars ago.
                        // Assume we are missing an open li at the end of the previous li, and insert there.
                        Y.log('Inserting opening ' + currentTag.tag + ' after previous li.', 'debug', LOGNAME);
                        output = this._insertString(output, '<' + currentTag.tag + '>', previousTag.liEnd);
                    } else if (currentTag.tagLowerCase === 'li' && !previousTag.liEnd &&
                            ((previousTag.position + previousTag.length) < output.length)) {
                        // We are closing an unopened li, and the parent has no previous lis in it, but opened more than 0
                        // chars ago. Assume we are missing a starting li, and insert it right after the list opened.
                        Y.log('Inserting opening ' + currentTag.tag + ' at start of parent.', 'debug', LOGNAME);
                        output = this._insertString(output, '<' + currentTag.tag + '>', previousTag.position + previousTag.length);
                    } else if (previousTag.tagLowerCase === 'li') {
                        // We must be trying to close a ul/ol while in a li. Just assume we are missing a closing li.
                        Y.log('Adding a closing ' + previousTag.tag + ' before closing ' + currentTag.tag + '.', 'debug', LOGNAME);
                        toProcess = '</' + previousTag.tag + '>' + currentTag.fullTag + toProcess;
                        continue;
                    } else {
                        // Here we must be trying to close a tag that isn't open, or is open higher up. Just discard.
                        // If there ends up being a missing close tag later on, that will get fixed separately.
                        Y.log('Discarding incorrect ' + currentTag.fullTag + '.', 'debug', LOGNAME);
                        continue;
                    }
                }

                // If we have a valid closing li tag, and a list, record where the li ended.
                if (currentTag.tagLowerCase === 'li' && previousTag) {
                    previousTag.liEnd = output.length + currentTag.fullTag.length;
                }

            }

            // Now we can add the tag to the output.
            output += currentTag.fullTag;
        }

        // Add anything left in toProcess to the output.
        output += toProcess;

        // Anything still in the openTags list are extra and need to be dealt with.
        if (openTags.length) {
            // Work on the list in reverse order so positions stay correct.
            while ((currentTag = openTags.pop())) {
                if (currentTag.liEnd) {
                    // We have a position for the last list item in this element. Insert the closing it after that.
                    output = this._insertString(output, '</' + currentTag.tag + '>', currentTag.liEnd);
                    Y.log('Adding closing ' + currentTag.tag + ' based on last li location.', 'debug', LOGNAME);
                } else {
                    // If there weren't any children list items, then we should just remove the tag where it started.
                    // This will also remote an open li tag that runs to the end of the content, since it has no children lis.
                    output = output.slice(0, currentTag.position) + output.slice(currentTag.position + currentTag.length);
                    Y.log('Removing opening ' + currentTag.fullTag + ' because it was missing closing.', 'debug', LOGNAME);
                }
            }
        }

        return output;
    },

    /**
     * Insert a string in the middle of an existing string at the specified location.
     *
     * @method _insertString
     * @param {String} content The subject of the insertion.
     * @param {String} insert The string that will be inserted.
     * @param {Number} position The location to make the insertion.
     * @return {String} The string with the new content inserted.
     */
    _insertString: function(content, insert, position) {
        return content.slice(0, position) + insert + content.slice(position);
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
 * @submodule commands
 */

/**
 * Selection functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorCommand
 */

function EditorCommand() {}

EditorCommand.ATTRS = {
};

EditorCommand.prototype = {
    /**
     * Applies a callback method to editor if selection is uncollapsed or waits for input to select first.
     * @method applyFormat
     * @param e EventTarget Event to be passed to callback if selection is uncollapsed
     * @param method callback A callback method which changes editor when text is selected.
     * @param object context Context to be used for callback method
     * @param array args Array of arguments to pass to callback
     */
    applyFormat: function(e, callback, context, args) {
        function handleInsert(e, callback, context, args, anchorNode, anchorOffset) {
            // After something is inputed, select it and apply the formating function.
            Y.soon(Y.bind(function(e, callback, context, args, anchorNode, anchorOffset) {
                var selection = window.rangy.getSelection();

                // Set the start of the selection to where it was when the method was first called.
                var range = selection.getRangeAt(0);
                range.setStart(anchorNode, anchorOffset);
                selection.setSingleRange(range);

                // Now apply callback to the new text that is selected.
                callback.apply(context, [e, args]);

                // Collapse selection so cursor is at end of inserted material.
                selection.collapseToEnd();

                // Save save selection and editor contents.
                this.saveSelection();
                this.updateOriginal();
            }, this, e, callback, context, args, anchorNode, anchorOffset));
        }

        // Set default context for the method.
        context = context || this;

        // Check whether range is collapsed.
        var selection = window.rangy.getSelection();

        if (selection.isCollapsed) {
            // Selection is collapsed so listen for input into editor.
            var handle = this.editor.once('input', handleInsert, this, callback, context, args,
                    selection.anchorNode, selection.anchorOffset);

            // Cancel if selection changes before input.
            this.editor.onceAfter(['click', 'selectstart'], handle.detach, handle);

            return;
        }

        // The range is not collapsed; so apply callback method immediately.
        callback.apply(context, [e, args]);

        // Save save selection and editor contents.
        this.saveSelection();
        this.updateOriginal();
    },

    /**
     * Replaces all the tags in a node list with new type.
     * @method replaceTags
     * @param NodeList nodelist
     * @param String tag
     */
    replaceTags: function(nodelist, tag) {
        // We mark elements in the node list for iterations.
        nodelist.setAttribute('data-iterate', true);
        var node = this.editor.one('[data-iterate="true"]');
        while (node) {
            var clone = Y.Node.create('<' + tag + ' />')
                .setAttrs(node.getAttrs())
                .removeAttribute('data-iterate');
            // Copy class and style if not blank.
            if (node.getAttribute('style')) {
                clone.setAttribute('style', node.getAttribute('style'));
            }
            if (node.getAttribute('class')) {
                clone.setAttribute('class', node.getAttribute('class'));
            }
            // We use childNodes here because we are interested in both type 1 and 3 child nodes.
            var children = node.getDOMNode().childNodes;
            var child;
            child = children[0];
            while (typeof child !== "undefined") {
                clone.append(child);
                child = children[0];
            }
            node.replace(clone);
            node = this.editor.one('[data-iterate="true"]');
        }
    },

    /**
     * Change all tags with given type to a span with CSS class attribute.
     * @method changeToCSS
     * @param String tag Tag type to be changed to span
     * @param String markerClass CSS class that corresponds to desired tag
     */
    changeToCSS: function(tag, markerClass) {
        // Save the selection.
        var selection = window.rangy.saveSelection();

        // Remove display:none from rangy markers so browser doesn't delete them.
        this.editor.all('.rangySelectionBoundary').setStyle('display', null);

        // Replace tags with CSS classes.
        this.editor.all(tag).addClass(markerClass);
        this.replaceTags(this.editor.all('.' + markerClass), 'span');

        // Restore selection and toggle class.
        window.rangy.restoreSelection(selection);
    },

    /**
     * Change spans with CSS classes in editor into elements with given tag.
     * @method changeToCSS
     * @param String markerClass CSS class that corresponds to desired tag
     * @param String tag New tag type to be created
     */
    changeToTags: function(markerClass, tag) {
        // Save the selection.
        var selection = window.rangy.saveSelection();

        // Remove display:none from rangy markers so browser doesn't delete them.
        this.editor.all('.rangySelectionBoundary').setStyle('display', null);

        // Replace spans with given tag.
        this.replaceTags(this.editor.all('span[class="' + markerClass + '"]'), tag);
        this.editor.all(tag + '[class="' + markerClass + '"]').removeAttribute('class');
        this.editor.all('.' + markerClass).each(function(n) {
            n.wrap('<' + tag + '/>');
            n.removeClass(markerClass);
        });

        // Remove CSS classes.
        this.editor.all('[class="' + markerClass + '"]').removeAttribute('class');
        this.editor.all(tag).removeClass(markerClass);

        // Restore selection.
        window.rangy.restoreSelection(selection);
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorCommand]);
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

EditorToolbar.ATTRS = {
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

EditorToolbarNav.ATTRS = {
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
            current = e.target.ancestor('button', true),
            innerButtons = e.target.all('button');

        // If we are not on a button and the element we are on contains some buttons, then move between the inner buttons.
        if (!current && innerButtons) {
            buttons = innerButtons;
        }

        if (e.keyCode === 37) {
            // Moving left so reverse the direction.
            direction = -1;
        }

        button = this._findFirstFocusable(buttons, current, direction);
        if (button) {
            button.focus();
            this._setTabFocus(button);
        } else {
            Y.log("Unable to find a button to focus on", 'debug', LOGNAME);
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
            candidate,
            button,
            index;

        // Determine which button to start the search from.
        index = buttons.indexOf(startAt);
        if (index < -1) {
            Y.log("Unable to find the button in the list of buttons", 'debug', LOGNAME);
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
            // * the button is inside a hidden wrapper element.
            if (candidate.hasAttribute('hidden') || candidate.hasAttribute('disabled') || candidate.ancestor('[hidden]')) {
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
                var button = this._findFirstFocusable(this.toolbar.all('button'), this._tabFocus, -1);
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

EditorSelection.ATTRS = {
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
     * Whether if the last gesturemovestart event target was contained in this editor or not.
     *
     * @property _gesturestartededitor
     * @type Boolean
     * @default false
     * @private
     */
    _gesturestartededitor: false,

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

        this.editor.on(['keyup', 'focus'], function(e) {
                Y.soon(Y.bind(this._hasSelectionChanged, this, e));
            }, this);

        Y.one(document.body).on('gesturemovestart', function(e) {
            if (this._wrapper.contains(e.target._node)) {
                this._gesturestartededitor = true;
            } else {
                this._gesturestartededitor = false;
            }
        }, null, this);

        Y.one(document.body).on('gesturemoveend', function(e) {
            if (!this._gesturestartededitor) {
                // Ignore the event if movestart target was not contained in the editor.
                return;
            }
            Y.soon(Y.bind(this._hasSelectionChanged, this, e));
        }, {
            // Standalone will make sure all editors receive the end event.
            standAlone: true
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

        // We can't be active if the editor doesn't have focus at the moment.
        if (!document.activeElement ||
                !(this.editor.compareTo(document.activeElement) || this.editor.contains(document.activeElement))) {
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
                // The function getSelectedNodes only returns nodes within the editor, so this test is safe.
                return node === editor;
            };

        // If we do not find at least one match in the editor, no point trying to find them in the selection.
        if (!editor.one(selector)) {
            return false;
        }

        selectednodes.each(function(node) {
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
            // We do not want to select all the nodes in the editor if we managed to
            // have a collapsed selection directly in the editor.
            // It's also possible for the commonAncestorContainer to be the document, which selectNode does not handle
            // so we must filter that out here too.
            if (range.commonAncestorContainer !== this.editor.getDOMNode()
                    && range.commonAncestorContainer !== Y.config.doc) {
                range = range.cloneRange();
                range.selectNode(range.commonAncestorContainer);
            }
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
     * @return {Node} The YUI Node object added to the DOM.
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
            range.collapse(false);
            var currentnode = node.getDOMNode(),
                last = currentnode.lastChild || currentnode;
            range.insertNode(currentnode);
            range.collapseAfter(last);
            selection.setSingleRange(range);
        }
        return node;
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

EditorStyling.ATTRS = {
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
        var cssApplier = rangy.createClassApplier(classname, {normalize: true});

        cssApplier.toggleSelection();
    },

    /**
     * Change the formatting for the current selection.
     *
     * This will set inline styles on the current selection.
     *
     * @method formatSelectionInlineStyle
     * @param {Array} styles - Style attributes to set on the nodes.
     */
    formatSelectionInlineStyle: function(styles) {
        var classname = this.PLACEHOLDER_CLASS;
        var cssApplier = rangy.createClassApplier(classname, {normalize: true});

        cssApplier.applyToSelection();

        this.editor.all('.' + classname).each(function(node) {
            node.removeClass(classname).setStyles(styles);
        }, this);

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
        cell = selectionparentnode.ancestor(function(node) {
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
            match = nearestblock.ancestor(function(node) {
                return node === boundary;
            }, false);

            if (!match) {
                nearestblock = false;
            }
        }

        // No valid block element - make one.
        if (!nearestblock) {
            var alignment;
            if (this.coreDirection === 'rtl') {
                alignment = 'style="text-align: right;"';
            } else {
                alignment = 'style="text-align: left;"';
            }
            // There is no block node in the content, wrap the content in a p and use that.
            newcontent = Y.Node.create('<p dir="' + this.coreDirection + '" ' + alignment + '></p>');
            boundary.get('childNodes').each(function(child) {
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
            nearestblock.get('childNodes').each(function(child) {
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

EditorFilepicker.ATTRS = {
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
        Y.use('core_filepicker', function(Y) {
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
        "transition",
        "io",
        "overlay",
        "escape",
        "event",
        "event-simulate",
        "event-custom",
        "node-event-html5",
        "node-event-simulate",
        "yui-throttle",
        "moodle-core-notification-dialogue",
        "moodle-core-notification-confirm",
        "moodle-editor_atto-rangy",
        "handlebars",
        "timers",
        "querystring-stringify"
    ]
});
