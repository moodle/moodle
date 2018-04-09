/* global CONFIRMYES, CONFIRMNO, QUESTION, BASE, TITLE, DIALOGUE_PREFIX */

/**
 * A dialogue type designed to display a confirmation to the user.
 *
 * @module moodle-core-notification
 * @submodule moodle-core-notification-confirm
 */

var CONFIRM_NAME = 'Moodle confirmation dialogue',
    CONFIRM;

/**
 * Extends core Dialogue to show the confirmation dialogue.
 *
 * @param {Object} config Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.confirm
 * @extends M.core.dialogue
 */
CONFIRM = function(config) {
    CONFIRM.superclass.constructor.apply(this, [config]);
};
Y.extend(CONFIRM, M.core.notification.info, {
    /**
     * The list of events to detach when destroying this dialogue.
     *
     * @property _closeEvents
     * @type EventHandle[]
     * @private
     */
    _closeEvents: null,

    /**
     * A reference to the yes button.
     *
     * @property _yesButton
     * @type Node
     * @private
     */
    _yesButton: null,

    /**
     * A reference to the No button.
     *
     * @property _noButton
     * @type Node
     * @private
     */
    _noButton: null,

    /**
     * A reference to the Question.
     *
     * @property _question
     * @type Node
     * @private
     */
    _question: null,

    initializer: function() {
        this._closeEvents = [];
        this.publish('complete');
        this.publish('complete-yes');
        this.publish('complete-no');
        this._yesButton = Y.Node.create('<input type="button" class="btn btn-primary m-r-1" id="id_yuiconfirmyes-' +
                                        this.get('COUNT') + '" value="' + this.get(CONFIRMYES) + '" />');
        this._noButton = Y.Node.create('<input type="button" class="btn btn-secondary m-r-1" id="id_yuiconfirmno-' +
                                        this.get('COUNT') + '" value="' + this.get(CONFIRMNO) + '" />');
        this._question = Y.Node.create('<div class="confirmation-message">' + this.get(QUESTION) + '</div>');
        var content = Y.Node.create('<div class="confirmation-dialogue"></div>')
                        .append(this._question)
                        .append(Y.Node.create('<div class="confirmation-buttons form-inline"></div>')
                            .append(this._yesButton)
                            .append(this._noButton));
        this.get(BASE).addClass('moodle-dialogue-confirm');
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
        this.setStdModContent(Y.WidgetStdMod.HEADER,
                '<h1 id="moodle-dialogue-' + this.get('COUNT') + '-header-text">' + this.get(TITLE) + '</h1>',
                Y.WidgetStdMod.REPLACE);

        this._closeEvents.push(
            Y.on('key', this.submit, window, 'down:27', this, false),
            this._yesButton.on('click', this.submit, this, true),
            this._noButton.on('click', this.submit, this, false)
        );

        var closeButton = this.get('boundingBox').one('.closebutton');
        if (closeButton) {
            // The close button should act exactly like the 'No' button.
            this._closeEvents.push(
                closeButton.on('click', this.submit, this)
            );
        }
    },
    submit: function(e, outcome) {
        new Y.EventHandle(this._closeEvents).detach();
        this.fire('complete', outcome);
        if (outcome) {
            this.fire('complete-yes');
        } else {
            this.fire('complete-no');
        }
        this.hide();
        this.destroy();
    }
}, {
    NAME: CONFIRM_NAME,
    CSS_PREFIX: DIALOGUE_PREFIX,
    ATTRS: {

        /**
         * The button text to use to accept the confirmation.
         *
         * @attribute yesLabel
         * @type String
         * @default 'Yes'
         */
        yesLabel: {
            validator: Y.Lang.isString,
            valueFn: function() {
                return M.util.get_string('yes', 'moodle');
            },
            setter: function(value) {
                if (this._yesButton) {
                    this._yesButton.set('value', value);
                }
                return value;
            }
        },

        /**
         * The button text to use to reject the confirmation.
         *
         * @attribute noLabel
         * @type String
         * @default 'No'
         */
        noLabel: {
            validator: Y.Lang.isString,
            valueFn: function() {
                return M.util.get_string('no', 'moodle');
            },
            setter: function(value) {
                if (this._noButton) {
                    this._noButton.set('value', value);
                }
                return value;
            }
        },

        /**
         * The title of the dialogue.
         *
         * @attribute title
         * @type String
         * @default 'Confirm'
         */
        title: {
            validator: Y.Lang.isString,
            value: M.util.get_string('confirm', 'moodle')
        },

        /**
         * The question posed by the dialogue.
         *
         * @attribute question
         * @type String
         * @default 'Are you sure?'
         */
        question: {
            validator: Y.Lang.isString,
            valueFn: function() {
                return M.util.get_string('areyousure', 'moodle');
            },
            setter: function(value) {
                if (this._question) {
                    this._question.set('value', value);
                }
                return value;
            }
        }
    }
});
Y.augment(CONFIRM, Y.EventTarget);

M.core.confirm = CONFIRM;
