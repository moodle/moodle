YUI.add('moodle-core-widget-focusafterclose', function (Y, NAME) {

/**
 * Provides support for focusing on different nodes after the Widget is
 * hidden.
 *
 * If the focusOnPreviousTargetAfterHide attribute is true, then the module hooks
 * into the show function for that Widget to try and determine which Node
 * caused the Widget to be shown.
 *
 * Alternatively, the focusAfterHide attribute can be passed a Node.
 *
 * @module moodle-core-widget-focusafterhide
 */

var CAN_RECEIVE_FOCUS_SELECTOR = 'input:not([type="hidden"]), ' +
                                 'a[href], button, textarea, select, ' +
                                '[tabindex], [contenteditable="true"]';

/**
 * Provides support for focusing on different nodes after the Widget is
 * hidden.
 *
 * @class M.core.WidgetFocusAfterHide
 */
function WidgetFocusAfterHide() {
    Y.after(this._bindUIFocusAfterHide, this, 'bindUI');
    if (this.get('rendered')) {
        this._bindUIFocusAfterHide();
    }
}

WidgetFocusAfterHide.ATTRS = {
    /**
     * Whether to focus on the target that caused the Widget to be shown.
     *
     * <em>If this is true, and a valid Node is found, any Node specified to focusAfterHide
     * will be ignored.</em>
     *
     * @attribute focusOnPreviousTargetAfterHide
     * @default false
     * @type boolean
     */
    focusOnPreviousTargetAfterHide: {
        value: false
    },

    /**
     * The Node to focus on after hiding the Widget.
     *
     * <em>Note: If focusOnPreviousTargetAfterHide is true, and a valid Node is found, then this
     * value will be ignored. If it is true and not found, then this value will be used as
     * a fallback.</em>
     *
     * @attribute focusAfterHide
     * @default null
     * @type Node
     */
    focusAfterHide: {
        value: null,
        type: Y.Node
    }
};

WidgetFocusAfterHide.prototype = {
    /**
     * The list of Event Handles which we should cancel when the dialogue is destroyed.
     *
     * @property uiHandleFocusAfterHide
     * @type array
     * @protected
     */
    _uiHandlesFocusAfterHide: [],

    /**
     * A reference to the real show method which is being overwritten.
     *
     * @property _showFocusAfterHide
     * @type function
     * @default null
     * @protected
     */
    _showFocusAfterHide: null,

    /**
     * A reference to the detected previous target.
     *
     * @property _previousTargetFocusAfterHide
     * @type function
     * @default null
     * @protected
     */
    _previousTargetFocusAfterHide: null,

    initializer: function() {

        if (this.get('focusOnPreviousTargetAfterHide') && this.show) {
            // Overwrite the parent method so that we can get the focused
            // target.
            this._showFocusAfterHide = this.show;
            this.show = function(e) {
                this._showFocusAfterHide.apply(this, arguments);

                // We use a property rather than overriding the focusAfterHide parameter in
                // case the target cannot be found at hide time.
                this._previousTargetFocusAfterHide = null;
                if (e && e.currentTarget) {
                    this._previousTargetFocusAfterHide = e.currentTarget;
                }
            };
        }
    },

    destructor: function() {
        new Y.EventHandle(this.uiHandleFocusAfterHide).detach();
    },

    /**
     * Set up the event handling required for this module to work.
     *
     * @method _bindUIFocusAfterHide
     * @private
     */
    _bindUIFocusAfterHide: function() {
        // Detach the old handles first.
        new Y.EventHandle(this.uiHandleFocusAfterHide).detach();
        this.uiHandleFocusAfterHide = [
            this.after('visibleChange', this._afterHostVisibleChangeFocusAfterHide)
        ];
    },

    /**
     * Handle the change in UI visibility.
     *
     * This method changes the focus after the hide has taken place.
     *
     * @method _afterHostVisibleChangeFocusAfterHide
     * @private
     */
    _afterHostVisibleChangeFocusAfterHide: function() {
        if (!this.get('visible')) {
            if (this._attemptFocus(this._previousTargetFocusAfterHide)) {

            } else if (this._attemptFocus(this.get('focusAfterHide'))) {
                // Fall back to the focusAfterHide value if one was specified.

            } else {

            }
        }
    },

    _attemptFocus: function(node) {
        var focusTarget = Y.one(node);
        if (focusTarget) {
            focusTarget = focusTarget.ancestor(CAN_RECEIVE_FOCUS_SELECTOR, true);
            if (focusTarget) {
                focusTarget.focus();
                return true;
            }
        }
        return false;
    }
};

var NS = Y.namespace('M.core');
NS.WidgetFocusAfterHide = WidgetFocusAfterHide;


}, '@VERSION@', {"requires": ["base-build", "widget"]});
