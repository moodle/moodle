YUI.add('moodle-editor_atto-editor-shortcut', function (Y, NAME) {

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

var SHORTCUT,
    SHORTCUTNAME = 'AttoButtonShortcut',
    EVENTS = {
        press: 'press'
    },
    ATTRS = {
        action: 'action',
        eventtype: 'eventtype',
        keys: 'keys'
    },
    CSS = {
        editorid: 'data-editor',
        contenteditable: '.editor_atto_content'
    },
    NS = 'moodle-editor_atto-editor-shortcut';

/**
 * Atto editor shortcut class
 *
 * @namespace M.editor_atto
 * @class Shortcut
 * @constructor
 * @extends Base
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
SHORTCUT = function() {
    SHORTCUT.superclass.constructor.apply(this, arguments);
};
SHORTCUT.prototype = {
    /**
     * Initialises a new shortcut.
     * @method initializer
     */
    initializer: function() {
        /**
         * Shortcut Event: press.
         *
         * This event is fired when the user triggers the shortcut object by pressing the required keys.
         * The event has a default function {@see execActionDefault()} that calls the browser to execute the action used
         * when the shortcut is created.
         * If you wish to provide your own functionality you need to add a listener to the shortcut you create for this
         * event, and then when it is fired call e.preventDefault() on the event facade it provides.
         *
         * The event facade contains two custom properties:
         * * elementid: The ID of the editor that is being acted upon.
         * * origevent: The original event facade, should you need it for any reason (I hope not)
         *
         * @event press
         */
        this.publish(EVENTS.press, {
            emitFacade: true,
            defaultFn: this.execActionDefault
        });
    },
    /**
     * Gets called when the user has triggered this shortcut.
     * @method trigger
     * @param {EventFacade} e
     */
    trigger: function(e) {
        e.preventDefault();
        var elementid = e.target.getAttribute(CSS.editorid);
        this.fire(EVENTS.press, {
            elementid: elementid,
            origevent: e
        });
    },
    /**
     * Binds this shortcut to the editors being shown on the page.
     * @method bind
     * @chainable
     * @param {Node} node
     * @param {String} container CSS to select the container element to bind to. Usually the contenteditable element.
     * @return {SHORTCUT}
     */
    bind: function(node, container) {
        var eventtype = this.get(ATTRS.eventtype),
            keys = this.get(ATTRS.keys);
        Y.one('body').delegate(
            eventtype,                      // Event.
            this.trigger,  // Callback.
            keys,                           // Keys.
            container,                      // Delegated container.
            this                            // Context.
        );
        return this;
    },
    /**
     * The default action performed when this shortcut (or any) is triggered.
     *
     * This can be cancelled by attaching your own event listener to the press event published
     * by this shortcut and then calling e.preventDefault() on the EventFacade it triggers.
     *
     * @method execActionDefault
     * @param {EventFacade} e
     */
    execActionDefault: function(e) {
        var elementid = e.elementid;
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        document.execCommand(this.get(ATTRS.action), false, null);
        // Clean the YUI ids from the HTML.
        M.editor_atto.text_updated(elementid);
    },

    /**
     * Returns the default meta key to use with a shortcut.
     * @method getDefaultMeta
     * @returns {string}
     */
    getDefaultMeta: function() {
        return (Y.UA.os === 'macintosh') ? '+meta' : '+ctrl';
    },

    /**
     * Returns the key event to use for this shortcut.
     * @returns {string}
     */
    getKeyEvent: function() {
        return 'down:';
    }
};
Y.extend(SHORTCUT, Y.Base, SHORTCUT.prototype, {
    NAME: SHORTCUTNAME,
    ATTRS: {
        /**
         * The action this shortcut is performing.
         * If using the default functionality this should be the browser command to execute.
         * @attribute action
         * @type String
         * @writeOnce
         */
        action: {
            writeOnce: 'init',
            validator: function(val) {
                return Y.Lang.isString(val);
            }
        },
        /**
         * The key code(s) used to trigger the shortcut, should be something like `85` for u (underline).
         *
         * For a single char all you need to do is set the keys property to the char you want to map to a shortcut.
         * If you need to do something more advanced (special combinations etc) you can specify a complete key set and
         * then set the simplekeys property to false.
         *
         * Please note that if you do provide an complete char set the browser defaults can only be overridden on the key down
         * event. A keypress is unfortunately good enough.
         *
         * @attribute keys
         * @default false
         * @type String|Bool
         * @writeOnce
         */
        keys: {
            writeOnce: 'init',
            value: false,
            validator: function(val) {
                return Y.Lang.isString(val) || Y.Lang.isNumber(val) || Y.Lang.isBoolean(val);
            },
            getter: function(val) {
                if (this.get('simplekeys')) {
                    return this.getKeyEvent() + val + this.getDefaultMeta();
                }
            }
        },
        /**
         * The event type to trigger on.
         * I can't imagine any good reason to override this, if you find one please let me know.
         * @attribute eventtype
         * @type String
         * @writeOnce
         * @default key
         */
        eventtype: {
            writeOnce: 'init',
            value: 'key',
            validator: function(val) {
                return Y.Lang.isString(val);
            }
        },
        /**
         * When set to true a simple key combination is being used and we'll have to append the correct type and control for it.
         *
         * Set this too off if you want to define the complete key combination for the shortcut yourself (advanced).
         */
        simplekeys: {
            value: true,
            validator: function(val) {
                return Y.Lang.isBoolean(val);
            }
        }
    }
});

M.editor_atto = M.editor_atto || {};
Y.mix(M.editor_atto, {
    /**
     * An associative collection of shortcut objects that have been bound to the editors on the page.
     * @protected
     * @namespace M.editor_atto
     * @property shortcutdelegations
     * @type Object
     */
    shortcutdelegations: {},

    /**
     * Adds a button shortcut given a configuration object containing properties for it.
     *
     * The config object must contain at least action and keys.
     * For more details see {@link SHORTCUT()}
     *
     * @static
     * @namespace M.editor_atto
     * @method add_button_shortcut
     * @param {Object} config A configuration object containing at least action and keys.
     * @return SHORTCUT
     */
    add_button_shortcut: function(config) {
        var shortcut = new SHORTCUT(config);
        return this.register_button_shortcut(shortcut);
    },

    /**
     * Registers a shortcut object and binds it to the editors being displayed on the current page.
     *
     * @static
     * @namespace M.editor_atto
     * @method register_button_shortcut
     * @param {SHORTCUT} shortcut The shortcut object to add.
     * @return SHORTCUT
     */
    register_button_shortcut: function(shortcut) {
        var action = shortcut.get(ATTRS.action),
            keys = shortcut.get(ATTRS.keys);
        if (!M.editor_atto.shortcutdelegations[action] && keys) {
            M.editor_atto.shortcutdelegations[action] = shortcut.bind(Y.one('body'), CSS.contenteditable);
        }
        return M.editor_atto.shortcutdelegations[action];
    }
});


}, '@VERSION@', {"requires": ["node", "event", "event-custom", "moodle-editor_atto-editor"]});
