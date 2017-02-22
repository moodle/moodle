/* global DIALOGUE_PREFIX */

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
    initializer: function() {
        this.show();
    }
}, {
    NAME: 'Moodle information dialogue',
    CSS_PREFIX: DIALOGUE_PREFIX
});

Y.Base.modifyAttrs(INFO, {
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
