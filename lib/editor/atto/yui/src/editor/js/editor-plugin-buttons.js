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
 * @module moodle-editor_atto-plugin
 * @submodule buttons
 */

/**
 * Button functions for an Atto Plugin.
 *
 * See {{#crossLink "M.editor_atto.EditorPlugin"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorPluginButtons
 */

var MENUTEMPLATE = '' +
        '<button class="{{buttonClass}} atto_hasmenu" ' +
            'tabindex="-1" ' +
            'type="button" ' +
            'title="{{title}}">' +
            '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" '+
                'style="background-color:{{config.menuColor}};" src="{{config.iconurl}}" />' +
            '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" src="{{image_url "t/expanded" "moodle"}}"/>' +
        '</button>';

var DISABLED = 'disabled',
    HIGHLIGHT = 'highlight',
    LOGNAME = 'moodle-editor_atto-editor-plugin',
    CSS = {
        EDITORWRAPPER: '.editor_atto_content'
    };

function EditorPluginButtons() {}

EditorPluginButtons.ATTRS= {
};

EditorPluginButtons.prototype = {
    /**
     * All of the buttons that belong to this plugin instance.
     *
     * Buttons are stored by button name.
     *
     * @property buttons
     * @type object
     */
    buttons: null,

    /**
     * A list of each of the button names.
     *
     * @property buttonNames
     * @type array
     */
    buttonNames: null,

    /**
     * A read-only view of the current state for each button. Mappings are stored by name.
     *
     * Possible states are:
     * <ul>
     * <li>{{#crossLink "M.editor_atto.EditorPluginButtons/ENABLED:property"}}{{/crossLink}}; and</li>
     * <li>{{#crossLink "M.editor_atto.EditorPluginButtons/DISABLED:property"}}{{/crossLink}}.</li>
     * </ul>
     *
     * @property buttonStates
     * @type object
     */
    buttonStates: null,

    /**
     * The menus belonging to this plugin instance.
     *
     * @property menus
     * @type object
     */
    menus: null,

    /**
     * The state for a disabled button.
     *
     * @property DISABLED
     * @type Number
     * @static
     * @value 0
     */
    DISABLED: 0,

    /**
     * The state for an enabled button.
     *
     * @property ENABLED
     * @type Number
     * @static
     * @value 1
     */
    ENABLED: 1,

    /**
     * The list of Event Handlers for buttons.
     *
     * @property _buttonHandlers
     * @protected
     * @type array
     */
    _buttonHandlers: null,

    /**
     * Hide handlers which are cancelled when the menu is hidden.
     *
     * @property _menuHideHandlers
     * @protected
     * @type array
     */
    _menuHideHandlers: null,

    /**
     * A textual description of the primary keyboard shortcut for this
     * plugin.
     *
     * This will be null if no keyboard shortcut has been registered.
     *
     * @property _primaryKeyboardShortcut
     * @protected
     * @type String
     * @default null
     */
    _primaryKeyboardShortcut: null,

    /**
     * An list of objects returned by Y.soon().
     *
     * The keys will be the buttonName of the button, and the value the Y.soon() object.
     *
     * @property _highlightQueue
     * @protected
     * @type Object
     * @default null
     */
    _highlightQueue: null,

    /**
     * Add a button for this plugin to the toolbar.
     *
     * @method addButton
     * @param {object} config The configuration for this button
     * @param {string} [config.iconurl] The URL for the icon. If not specified, then the icon and component will be used instead.
     * @param {string} [config.icon] The icon identifier.
     * @param {string} [config.iconComponent='core'] The icon component.
     * @param {string} [config.keys] The shortcut key that can call this plugin from the keyboard.
     * @param {string} [config.keyDescription] An optional description for the keyboard shortcuts.
     * If not specified, this is automatically generated based on config.keys.
     * If multiple key bindings are supplied to config.keys, then only the first is used.
     * If set to false, then no description is added to the title.
     * @param {string} [config.tags] The tags that trigger this button to be highlighted.
     * @param {boolean} [config.tagMatchRequiresAll=true] Working in combination with the tags parameter, when true
     * every tag of the selection has to match. When false, only one match is needed. Only set this to false when
     * necessary as it is much less efficient.
     * See {{#crossLink "M.editor_atto.EditorSelection/selectionFilterMatches:method"}}{{/crossLink}} for more information.
     * @param {string} [config.title=this.name] The string identifier in the plugin's language file.
     * @param {string} [config.buttonName=this.name] The name of the button. This is used in the buttons object, and if
     * specified, in the class for the button.
     * @param {function} config.callback A callback function to call when the button is clicked.
     * @param {object} [config.callbackArgs] Any arguments to pass to the callback.
     * @return {Node} The Node representing the newly created button.
     */
    addButton: function(config) {
        var group = this.get('group'),
            pluginname = this.name,
            buttonClass = 'atto_' + pluginname + '_button',
            button,
            host = this.get('host');

        if (config.exec) {
            buttonClass = buttonClass + '_' + config.exec;
        }

        if (!config.buttonName) {
            // Set a default button name - this is used as an identifier in the button object.
            config.buttonName = config.exec || pluginname;
        } else {
            buttonClass = buttonClass + '_' + config.buttonName;
        }
        config.buttonClass = buttonClass;

        // Normalize icon configuration.
        config = this._normalizeIcon(config);

        if (!config.title) {
            config.title = 'pluginname';
        }
        var title = M.util.get_string(config.title, 'atto_' + pluginname);

        // Create the actual button.
        button = Y.Node.create('<button type="button" class="' + buttonClass + '"' +
                'tabindex="-1">' +
                    '<img class="icon" aria-hidden="true" role="presentation" width="16" height="16" src="' + config.iconurl + '"/>' +
                '</button>');
        button.setAttribute('title', title);

        // Append it to the group.
        group.append(button);

        var currentfocus = this.toolbar.getAttribute('aria-activedescendant');
        if (!currentfocus) {
            // Initially set the first button in the toolbar to be the default on keyboard focus.
            button.setAttribute('tabindex', '0');
            this.toolbar.setAttribute('aria-activedescendant', button.generateID());
            this.get('host')._tabFocus = button;
        }

        // Normalize the callback parameters.
        config = this._normalizeCallback(config);

        // Add the standard click handler to the button.
        this._buttonHandlers.push(
            this.toolbar.delegate('click', config.callback, '.' + buttonClass, this)
        );

        // Handle button click via shortcut key.
        if (config.keys) {
            if (typeof config.keyDescription !== 'undefined') {
                // A keyboard shortcut description was specified - use it.
                this._primaryKeyboardShortcut[buttonClass] = config.keyDescription;
            }
            this._addKeyboardListener(config.callback, config.keys, buttonClass);

            if (this._primaryKeyboardShortcut[buttonClass]) {
                // If we have a valid keyboard shortcut description, then set it with the title.
                button.setAttribute('title', M.util.get_string('plugin_title_shortcut', 'editor_atto', {
                        title: title,
                        shortcut: this._primaryKeyboardShortcut[buttonClass]
                    }));
            }
        }

        // Handle highlighting of the button.
        if (config.tags) {
            var tagMatchRequiresAll = true;
            if (typeof config.tagMatchRequiresAll === 'boolean') {
                tagMatchRequiresAll = config.tagMatchRequiresAll;
            }
            this._buttonHandlers.push(
                host.on(['atto:selectionchanged', 'change'], function(e) {
                    if (typeof this._highlightQueue[config.buttonName] !== 'undefined') {
                        this._highlightQueue[config.buttonName].cancel();
                    }
                    // Async the highlighting.
                    this._highlightQueue[config.buttonName] = Y.soon(Y.bind(function(e) {
                        if (host.selectionFilterMatches(config.tags, e.selectedNodes, tagMatchRequiresAll)) {
                            this.highlightButtons(config.buttonName);
                        } else {
                            this.unHighlightButtons(config.buttonName);
                        }
                    }, this, e));
                }, this)
            );
        }

        // Add the button reference to the buttons array for later reference.
        this.buttonNames.push(config.buttonName);
        this.buttons[config.buttonName] = button;
        this.buttonStates[config.buttonName] = this.ENABLED;
        return button;
    },

    /**
     * Add a basic button which ties into the execCommand.
     *
     * See {{#crossLink "M.editor_atto.EditorPluginButtons/addButton:method"}}addButton{{/crossLink}} for full details of the optional parameters.
     *
     * @method addBasicButton
     * @param {object} config The button configuration
     * @param {string} config.exec The execCommand to call on the document.
     * @param {string} [config.iconurl] The URL for the icon. If not specified, then the icon and component will be used instead.
     * @param {string} [config.icon] The icon identifier.
     * @param {string} [config.iconComponent='core'] The icon component.
     * @param {string} [config.keys] The shortcut key that can call this plugin from the keyboard.
     * @param {string} [config.tags] The tags that trigger this button to be highlighted.
     * @param {boolean} [config.tagMatchRequiresAll=false] Working in combination with the tags parameter, highlight
     * this button when any match is good enough.
     *
     * See {{#crossLink "M.editor_atto.EditorSelection/selectionFilterMatches:method"}}{{/crossLink}} for more information.
     * @param {string} [config.title=this.name] The string identifier in the plugin's language file.
     * @param {string} [config.buttonName=this.name] The name of the button. This is used in the buttons object, and if
     * specified, in the class for the button.
     * @return {Node} The Node representing the newly created button.
     */
    addBasicButton: function(config) {
        if (!config.exec) {
            Y.log('No exec command specified. Cannot proceed.',
                    'warn', 'moodle-editor_atto-plugin');
            return null;
        }

        // The default icon - true for most core plugins.
        if (!config.icon) {
            config.icon = 'e/' + config.exec;
        }

        // The default callback.
        config.callback = function() {
            document.execCommand(config.exec, false, null);

            // And mark the text area as updated.
            this.markUpdated();
        };

        // Return the newly created button.
        return this.addButton(config);
    },

    /**
     * Add a menu for this plugin to the editor toolbar.
     *
     * @method addToolbarMenu
     * @param {object} config The configuration for this button
     * @param {string} [config.iconurl] The URL for the icon. If not specified, then the icon and component will be used instead.
     * @param {string} [config.icon] The icon identifier.
     * @param {string} [config.iconComponent='core'] The icon component.
     * @param {string} [config.title=this.name] The string identifier in the plugin's language file.
     * @param {string} [config.buttonName=this.name] The name of the button. This is used in the buttons object, and if
     * specified, in the class for the button.
     * @param {function} config.callback A callback function to call when the button is clicked.
     * @param {object} [config.callbackArgs] Any arguments to pass to the callback.
     * @param {array} config.entries List of menu entries with the string (entry.text) and the handlers (entry.handler).
     * @param {number} [config.overlayWidth=14] The width of the menu. This will be suffixed with the 'em' unit.
     * @param {string} [config.menuColor] menu icon background color
     * @return {Node} The Node representing the newly created button.
     */
    addToolbarMenu: function(config) {
        var group = this.get('group'),
            pluginname = this.name,
            buttonClass = 'atto_' + pluginname + '_button',
            button,
            currentFocus;

        if (!config.buttonName) {
            // Set a default button name - this is used as an identifier in the button object.
            config.buttonName = pluginname;
        } else {
            buttonClass = buttonClass + '_' + config.buttonName;
        }
        config.buttonClass = buttonClass;

        // Normalize icon configuration.
        config = this._normalizeIcon(config);

        if (!config.title) {
            config.title = 'pluginname';
        }
        var title = M.util.get_string(config.title, 'atto_' + pluginname);

        if (!config.menuColor) {
            config.menuColor = 'transparent';
        }

        // Create the actual button.
        var template = Y.Handlebars.compile(MENUTEMPLATE);
        button = Y.Node.create(template({
            buttonClass: buttonClass,
            config: config,
            title: title
        }));

        // Append it to the group.
        group.append(button);

        currentFocus = this.toolbar.getAttribute('aria-activedescendant');
        if (!currentFocus) {
            // Initially set the first button in the toolbar to be the default on keyboard focus.
            button.setAttribute('tabindex', '0');
            this.toolbar.setAttribute('aria-activedescendant', button.generateID());
        }

        // Add the standard click handler to the menu.
        this._buttonHandlers.push(
            this.toolbar.delegate('click', this._showToolbarMenu, '.' + buttonClass, this, config),
            this.toolbar.delegate('key', this._showToolbarMenuAndFocus, '40, 32, enter', '.' + buttonClass, this, config)
        );

        // Add the button reference to the buttons array for later reference.
        this.buttonNames.push(config.buttonName);
        this.buttons[config.buttonName] = button;
        this.buttonStates[config.buttonName] = this.ENABLED;

        return button;
    },

    /**
     * Display a toolbar menu.
     *
     * @method _showToolbarMenu
     * @param {EventFacade} e
     * @param {object} config The configuration for the whole toolbar.
     * @param {Number} [config.overlayWidth=14] The width of the menu
     * @private
     */
    _showToolbarMenu: function(e, config) {
        // Prevent default primarily to prevent arrow press changes.
        e.preventDefault();

        if (!this.isEnabled()) {
            // Exit early if the plugin is disabled.
            return;
        }

        if (e.currentTarget.ancestor('button', true).hasAttribute(DISABLED)) {
            // Exit early if the clicked button was disabled.
            return;
        }

        var menuDialogue;

        if (!this.menus[config.buttonClass]) {
            if (!config.overlayWidth) {
                config.overlayWidth = '14';
            }

            if (!config.innerOverlayWidth) {
                config.innerOverlayWidth = parseInt(config.overlayWidth, 10) - 2 + 'em';
            }
            config.overlayWidth = parseInt(config.overlayWidth, 10) + 'em';

            this.menus[config.buttonClass] = new Y.M.editor_atto.Menu(config);

            this.menus[config.buttonClass].get('contentBox').delegate('click',
                    this._chooseMenuItem, '.atto_menuentry a', this, config);
        }

        // Clear the focusAfterHide for any other menus which may be open.
        Y.Array.each(this.get('host').openMenus, function(menu) {
            menu.set('focusAfterHide', null);
        });

        // Ensure that we focus on this button next time.
        var creatorButton = this.buttons[config.buttonName];
        creatorButton.focus();
        this.get('host')._setTabFocus(creatorButton);

        // Get a reference to the menu dialogue.
        menuDialogue = this.menus[config.buttonClass];

        // Focus on the button by default after hiding this menu.
        menuDialogue.set('focusAfterHide', creatorButton);

        // Display the menu.
        menuDialogue.show();

        // Position it next to the button which opened it.
        menuDialogue.align(this.buttons[config.buttonName], [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]);

        this.get('host').openMenus = [menuDialogue];
    },

    /**
     * Display a toolbar menu and focus upon the first item.
     *
     * @method _showToolbarMenuAndFocus
     * @param {EventFacade} e
     * @param {object} config The configuration for the whole toolbar.
     * @param {Number} [config.overlayWidth=14] The width of the menu
     * @private
     */
    _showToolbarMenuAndFocus: function(e, config) {
        this._showToolbarMenu(e, config);

        // Focus on the first element in the menu.
        this.menus[config.buttonClass].get('boundingBox').one('a').focus();
    },

    /**
     * Select a menu item and call the appropriate callbacks.
     *
     * @method _chooseMenuItem
     * @param {EventFacade} e
     * @param {object} config
     * @param {M.core.dialogue} menuDialogue The Dialogue to hide.
     * @private
     */
    _chooseMenuItem: function(e, config, menuDialogue) {
        // Get the index from the clicked anchor.
        var index = e.target.ancestor('a', true).getData('index'),

            // And the normalized callback configuration.
            buttonConfig = this._normalizeCallback(config.items[index], config.globalItemConfig);

            menuDialogue = this.menus[config.buttonClass];

        // Prevent the dialogue to be closed because of some browser weirdness.
        menuDialogue.set('preventHideMenu', true);

        // Call the callback for this button.
        buttonConfig.callback(e, buttonConfig._callback, buttonConfig.callbackArgs);

        // Cancel the hide menu prevention.
        menuDialogue.set('preventHideMenu', false);

        console.log('Menu item chosen');
        // Set the focus after hide so that focus is returned to the editor and changes are made correctly.
        menuDialogue.set('focusAfterHide', this.get('host').editor);
        menuDialogue.hide(e);
    },

    /**
     * Normalize and sanitize the configuration variables relating to callbacks.
     *
     * @method _normalizeCallback
     * @param {object} config
     * @param {function} config.callback A callback function to call when the button is clicked.
     * @param {object} [config.callbackArgs] Any arguments to pass to the callback.
     * @param {object} [inheritFrom] A parent configuration that this configuration may inherit from.
     * @return {object} The normalized configuration
     * @private
     */
    _normalizeCallback: function(config, inheritFrom) {
        if (config._callbackNormalized) {
            // Return early if the callback has already been normalized.
            return config;
        }

        if (!inheritFrom) {
            // Create an empty inheritFrom to make life easier below.
            inheritFrom = {};
        }

        // We wrap the callback in function to prevent the default action, check whether the editor is
        // active and focus it, and then mark the field as updated.
        config._callback = config.callback || inheritFrom.callback;
        config.callback = Y.rbind(this._callbackWrapper, this, config._callback, config.callbackArgs);

        config._callbackNormalized = true;

        return config;
    },

    /**
     * Normalize and sanitize the configuration variables relating to icons.
     *
     * @method _normalizeIcon
     * @param {object} config
     * @param {string} [config.iconurl] The URL for the icon. If not specified, then the icon and component will be used instead.
     * @param {string} [config.icon] The icon identifier.
     * @param {string} [config.iconComponent='core'] The icon component.
     * @return {object} The normalized configuration
     * @private
     */
    _normalizeIcon: function(config) {
        if (!config.iconurl) {
            // The default icon component.
            if (!config.iconComponent) {
                config.iconComponent = 'core';
            }
            config.iconurl = M.util.image_url(config.icon, config.iconComponent);
        }

        return config;
    },

    /**
     * A wrapper in which to run the callbacks.
     *
     * This handles common functionality such as:
     * <ul>
     *  <li>preventing the default action; and</li>
     *  <li>focusing the editor if relevant.</li>
     * </ul>
     *
     * @method _callbackWrapper
     * @param {EventFacade} e
     * @param {Function} callback The function to call which makes the relevant changes.
     * @param {Array} [callbackArgs] The arguments passed to this callback.
     * @return {Mixed} The value returned by the callback.
     * @private
     */
    _callbackWrapper: function(e, callback, callbackArgs) {
        e.preventDefault();

        if (!this.isEnabled()) {
            // Exit early if the plugin is disabled.
            return;
        }

        var creatorButton = e.currentTarget.ancestor('button', true);

        if (creatorButton && creatorButton.hasAttribute(DISABLED)) {
            // Exit early if the clicked button was disabled.
            return;
        }

        if (!(YUI.Env.UA.android || this.get('host').isActive())) {
            // We must not focus for Android here, even if the editor is not active because the keyboard auto-completion
            // changes the cursor position.
            // If we save that change, then when we restore the change later we get put in the wrong place.
            // Android is fine to save the selection without the editor being in focus.
            this.get('host').focus();
        }

        // Save the selection.
        this.get('host').saveSelection();

        // Ensure that we focus on this button next time.
        if (creatorButton) {
            this.get('host')._setTabFocus(creatorButton);
        }

        // Build the arguments list, but remove the callback we're calling.
        var args = [e, callbackArgs];

        // Restore selection before making changes.
        this.get('host').restoreSelection();

        // Actually call the callback now.
        return callback.apply(this, args);
    },

    /**
     * Add a keyboard listener to call the callback.
     *
     * The keyConfig will take either an array of keyConfigurations, in
     * which case _addKeyboardListener is called multiple times; an object
     * containing an optional eventtype, optional container, and a set of
     * keyCodes, or just a string containing the keyCodes. When keyConfig is
     * not an object, it is wrapped around a function that ensures that
     * only the expected key modifiers were used. For instance, it checks
     * that space+ctrl is not triggered when the user presses ctrl+shift+space.
     * When using an object, the developer should check that manually.
     *
     * @method _addKeyboardListener
     * @param {function} callback
     * @param {array|object|string} keyConfig
     * @param {string} [keyConfig.eventtype=key] The type of event
     * @param {string} [keyConfig.container=.editor_atto_content] The containing element.
     * @param {string} keyConfig.keyCodes The keycodes to user for the event.
     * @private
     *
     */
    _addKeyboardListener: function(callback, keyConfig, buttonName) {
        var eventtype = 'key',
            container = CSS.EDITORWRAPPER,
            keys,
            handler,
            modifier;

        if (Y.Lang.isArray(keyConfig)) {
            // If an Array was specified, call the add function for each element.
            Y.Array.each(keyConfig, function(config) {
                this._addKeyboardListener(callback, config);
            }, this);

            return this;

        } else if (typeof keyConfig === "object") {
            if (keyConfig.eventtype) {
                eventtype = keyConfig.eventtype;
            }

            if (keyConfig.container) {
                container = keyConfig.container;
            }

            // Must be specified.
            keys = keyConfig.keyCodes;
            handler = callback;

        } else {
            modifier = this._getDefaultMetaKey();
            keys = this._getKeyEvent() + keyConfig + '+' + modifier;
            if (typeof this._primaryKeyboardShortcut[buttonName] === 'undefined') {
                this._primaryKeyboardShortcut[buttonName] = this._getDefaultMetaKeyDescription(keyConfig);
            }

            // Wrap the callback into a handler to check if it uses the specified modifiers, not more.
            handler = Y.bind(function(modifiers, e) {
                if (this._eventUsesExactKeyModifiers(modifiers, e)) {
                    callback.apply(this, [e]);
                }
            }, this, [modifier]);
        }

        this._buttonHandlers.push(
            this.editor.delegate(
                eventtype,
                handler,
                keys,
                container,
                this
            )
        );

        Y.log('Atto shortcut registered: ' + keys + ' now triggers for ' + buttonName,
                'debug', LOGNAME);
    },

    /**
     * Checks if a key event was strictly defined for the modifiers passed.
     *
     * @method _eventUsesExactKeyModifiers
     * @param  {Array} modifiers List of key modifiers to check for (alt, ctrl, meta or shift).
     * @param  {EventFacade} e The event facade.
     * @return {Boolean} True if the event was stricly using the modifiers specified.
     */
    _eventUsesExactKeyModifiers: function(modifiers, e) {
        var exactMatch = true,
            hasKey;

        if (e.type !== 'key') {
            return false;
        }

        hasKey = Y.Array.indexOf(modifiers, 'alt') > -1;
        exactMatch = exactMatch && ((e.altKey && hasKey) || (!e.altKey && !hasKey));
        hasKey = Y.Array.indexOf(modifiers, 'ctrl') > -1;
        exactMatch = exactMatch && ((e.ctrlKey && hasKey) || (!e.ctrlKey && !hasKey));
        hasKey = Y.Array.indexOf(modifiers, 'meta') > -1;
        exactMatch = exactMatch && ((e.metaKey && hasKey) || (!e.metaKey && !hasKey));
        hasKey = Y.Array.indexOf(modifiers, 'shift') > -1;
        exactMatch = exactMatch && ((e.shiftKey && hasKey) || (!e.shiftKey && !hasKey));

        return exactMatch;
    },

    /**
     * Determine if this plugin is enabled, based upon the state of it's buttons.
     *
     * @method isEnabled
     * @return {boolean}
     */
    isEnabled: function() {
        // The first instance of an undisabled button will make this return true.
        var found = Y.Object.some(this.buttonStates, function(button) {
            return (button === this.ENABLED);
        }, this);

        return found;
    },

    /**
     * Enable one button, or all buttons relating to this Plugin.
     *
     * If no button is specified, all buttons are disabled.
     *
     * @method disableButtons
     * @param {String} [button] The name of a specific plugin to enable.
     * @chainable
     */
    disableButtons: function(button) {
        return this._setButtonState(false, button);
    },

    /**
     * Enable one button, or all buttons relating to this Plugin.
     *
     * If no button is specified, all buttons are enabled.
     *
     * @method enableButtons
     * @param {String} [button] The name of a specific plugin to enable.
     * @chainable
     */
    enableButtons: function(button) {
        return this._setButtonState(true, button);
    },

    /**
     * Set the button state for one button, or all buttons associated with this plugin.
     *
     * @method _setButtonState
     * @param {Boolean} enable Whether to enable this button.
     * @param {String} [button] The name of a specific plugin to set state for.
     * @chainable
     * @private
     */
    _setButtonState: function(enable, button) {
        var attributeChange = 'setAttribute';
        if (enable) {
            attributeChange = 'removeAttribute';
        }
        if (button) {
            if (this.buttons[button]) {
                this.buttons[button][attributeChange](DISABLED, DISABLED);
                this.buttonStates[button] = enable ? this.ENABLED : this.DISABLED;
            }
        } else {
            Y.Array.each(this.buttonNames, function(button) {
                this.buttons[button][attributeChange](DISABLED, DISABLED);
                this.buttonStates[button] = enable ? this.ENABLED : this.DISABLED;
            }, this);
        }

        this.get('host').checkTabFocus();
        return this;
    },

    /**
     * Highlight a button, or buttons in the toolbar.
     *
     * If no button is specified, all buttons are highlighted.
     *
     * @method highlightButtons
     * @param {string} [button] If a plugin has multiple buttons, the specific button to highlight.
     * @chainable
     */
    highlightButtons: function(button) {
        return this._changeButtonHighlight(true, button);
    },

    /**
     * Un-highlight a button, or buttons in the toolbar.
     *
     * If no button is specified, all buttons are un-highlighted.
     *
     * @method unHighlightButtons
     * @param {string} [button] If a plugin has multiple buttons, the specific button to highlight.
     * @chainable
     */
    unHighlightButtons: function(button) {
        return this._changeButtonHighlight(false, button);
    },

    /**
     * Highlight a button, or buttons in the toolbar.
     *
     * @method _changeButtonHighlight
     * @param {boolean} highlight true
     * @param {string} [button] If a plugin has multiple buttons, the specific button to highlight.
     * @protected
     * @chainable
     */
    _changeButtonHighlight: function(highlight, button) {
        var method = 'addClass';

        if (!highlight) {
            method = 'removeClass';
        }
        if (button) {
            if (this.buttons[button]) {
                this.buttons[button][method](HIGHLIGHT);
            }
        } else {
            Y.Object.each(this.buttons, function(button) {
                button[method](HIGHLIGHT);
            }, this);
        }

        return this;
    },

    /**
     * Get the default meta key to use with keyboard events.
     *
     * On a Mac, this will be the 'meta' key for Command; otherwise it will
     * be the Control key.
     *
     * @method _getDefaultMetaKey
     * @return {string}
     * @private
     */
    _getDefaultMetaKey: function() {
        if (Y.UA.os === 'macintosh') {
            return 'meta';
        } else {
            return 'ctrl';
        }
    },

    /**
     * Get the user-visible description of the meta key to use with keyboard events.
     *
     * On a Mac, this will be 'Command' ; otherwise it will be 'Control'.
     *
     * @method _getDefaultMetaKeyDescription
     * @return {string}
     * @private
     */
    _getDefaultMetaKeyDescription: function(keyCode) {
        if (Y.UA.os === 'macintosh') {
            return M.util.get_string('editor_command_keycode', 'editor_atto', String.fromCharCode(keyCode).toLowerCase());
        } else {
            return M.util.get_string('editor_control_keycode', 'editor_atto', String.fromCharCode(keyCode).toLowerCase());
        }
    },

    /**
     * Get the standard key event to use for keyboard events.
     *
     * @method _getKeyEvent
     * @return {string}
     * @private
     */
    _getKeyEvent: function() {
        return 'down:';
    }
};

Y.Base.mix(Y.M.editor_atto.EditorPlugin, [EditorPluginButtons]);
