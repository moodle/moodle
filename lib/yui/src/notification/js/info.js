/**
 * A dialogue type designed to display informative messages to users.
 *
 * @module moodle-core-notification
 */

/**
 * Extends core Dialogue to provide a type of dialogue which can be used
 * for informative message which are modal, and centered.
 *
 * @param {Object} config Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.notification.info
 * @extends M.core.dialogue
 */
var INFO = function() {
    INFO.superclass.constructor.apply(this, arguments);
};

Y.extend(INFO, M.core.dialogue, {
}, {
    NAME: 'Moodle information dialogue',
    CSS_PREFIX: DIALOGUE_PREFIX
});

Y.Base.modifyAttrs(INFO, {
    /**
     * Boolean indicating whether or not the Widget is visible.
     *
     * We override this from the default M.core.dialogue attribute value.
     *
     * @attribute visible
     * @default true
     * @type Boolean
     */
    visible: {
        value: true
    },

   /**
    * Whether the widget should be modal or not.
    *
    * We override this to change the default from false to true for a subset of dialogues.
    *
    * @attribute modal
    * @type Boolean
    * @default true
    */
    modal: {
        validator: Y.Lang.isBoolean,
        value: true
    }
});

M.core.notification = M.core.notification || {};
M.core.notification.info = INFO;
