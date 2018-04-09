/**
 * Dock JS.
 *
 * This file contains the action key event definition that is used for accessibility handling within the Dock.
 *
 * @module moodle-core-dock
 */

/**
 * A 'dock:actionkey' Event.
 * The event consists of the left arrow, right arrow, enter and space keys.
 * More keys can be mapped to action meanings.
 * actions: collapse , expand, toggle, enter.
 *
 * This event is subscribed to by dockitems.
 * The on() method to subscribe allows specifying the desired trigger actions as JSON.
 *
 * This event can also be delegated if needed.
 *
 * @namespace M.core.dock
 * @class ActionKey
 */
Y.Event.define("dock:actionkey", {
    // Webkit and IE repeat keydown when you hold down arrow keys.
    // Opera links keypress to page scroll; others keydown.
    // Firefox prevents page scroll via preventDefault() on either
    // keydown or keypress.
    _event: (Y.UA.webkit || Y.UA.ie) ? 'keydown' : 'keypress',

    /**
     * The keys to trigger on.
     * @property _keys
     */
    _keys: {
        // arrows
        '37': 'collapse',
        '39': 'expand',
        // (@todo: lrt/rtl/M.core_dock.cfg.orientation decision to assign arrow to meanings)
        '32': 'toggle',
        '13': 'enter'
    },

    /**
     * Handles key events
     * @method _keyHandler
     * @param {EventFacade} e
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     * @param {Object} args
     */
    _keyHandler: function(e, notifier, args) {
        var actObj;
        if (!args.actions) {
            actObj = {collapse: true, expand: true, toggle: true, enter: true};
        } else {
            actObj = args.actions;
        }
        if (this._keys[e.keyCode] && actObj[this._keys[e.keyCode]]) {
            e.action = this._keys[e.keyCode];
            notifier.fire(e);
        }
    },

    /**
     * Subscribes to events.
     * @method on
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     */
    on: function(node, sub, notifier) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        if (sub.args === null) {
            // no actions given
            sub._detacher = node.on(this._event, this._keyHandler, this, notifier, {actions: false});
        } else {
            sub._detacher = node.on(this._event, this._keyHandler, this, notifier, sub.args[0]);
        }
    },

    /**
     * Detaches an event listener
     * @method detach
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     */
    detach: function(node, sub) {
        // detach our _detacher handle of the subscription made in on()
        sub._detacher.detach();
    },

    /**
     * Creates a delegated event listener.
     * @method delegate
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     * @param {String|function} filter Selector string or function that accpets an event object and returns null.
     */
    delegate: function(node, sub, notifier, filter) {
        // subscribe to _event and ask keyHandler to handle with given args[0] (the desired actions).
        if (sub.args === null) {
            // no actions given
            sub._delegateDetacher = node.delegate(this._event, this._keyHandler, filter, this, notifier, {actions: false});
        } else {
            sub._delegateDetacher = node.delegate(this._event, this._keyHandler, filter, this, notifier, sub.args[0]);
        }
    },

    /**
     * Detaches a delegated event listener.
     * @method detachDelegate
     * @param {Node} node The node this subscription was applied to.
     * @param {Subscription} sub The object tracking this subscription.
     * @param {SyntheticEvent.Notifier} notifier The notifier used to trigger the execution of subscribers
     * @param {String|function} filter Selector string or function that accpets an event object and returns null.
     */
    detachDelegate: function(node, sub) {
        sub._delegateDetacher.detach();
    }
});
