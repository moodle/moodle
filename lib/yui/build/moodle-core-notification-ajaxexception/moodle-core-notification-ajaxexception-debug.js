YUI.add('moodle-core-notification-ajaxexception', function (Y, NAME) {

/* eslint-disable no-unused-vars, no-unused-expressions */
var DIALOGUE_PREFIX,
    BASE,
    CONFIRMYES,
    CONFIRMNO,
    TITLE,
    QUESTION,
    CSS;

DIALOGUE_PREFIX = 'moodle-dialogue',
BASE = 'notificationBase',
CONFIRMYES = 'yesLabel',
CONFIRMNO = 'noLabel',
TITLE = 'title',
QUESTION = 'question',
CSS = {
    BASE: 'moodle-dialogue-base',
    WRAP: 'moodle-dialogue-wrap',
    HEADER: 'moodle-dialogue-hd',
    BODY: 'moodle-dialogue-bd',
    CONTENT: 'moodle-dialogue-content',
    FOOTER: 'moodle-dialogue-ft',
    HIDDEN: 'hidden',
    LIGHTBOX: 'moodle-dialogue-lightbox'
};

// Set up the namespace once.
M.core = M.core || {};
/* global BASE, DIALOGUE_PREFIX */

/**
 * A dialogue type designed to display an appropriate error when an error
 * thrown in the Moodle codebase was reported during an AJAX request.
 *
 * @module moodle-core-notification
 * @submodule moodle-core-notification-ajaxexception
 */

var AJAXEXCEPTION_NAME = 'Moodle AJAX exception',
    AJAXEXCEPTION;

/**
 * Extends core Dialogue to show the exception dialogue.
 *
 * @param {Object} config Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.ajaxException
 * @extends M.core.dialogue
 */
AJAXEXCEPTION = function(config) {
    config.name = config.name || M.util.get_string('error', 'moodle');
    config.closeButton = true;
    AJAXEXCEPTION.superclass.constructor.apply(this, [config]);
};
Y.extend(AJAXEXCEPTION, M.core.notification.info, {
    _keypress: null,
    initializer: function(config) {
        var content,
            self = this,
            delay = this.get('hideTimeoutDelay'),
            labelsep = M.util.get_string('labelsep', 'langconfig');
        this.get(BASE).addClass('moodle-dialogue-exception');
        this.setStdModContent(Y.WidgetStdMod.HEADER,
                '<h1 id="moodle-dialogue-' + this.get('COUNT') + '-header-text">' + Y.Escape.html(config.name) + '</h1>',
                Y.WidgetStdMod.REPLACE);
        content = Y.Node.create('<div class="moodle-ajaxexception" data-rel="fatalerror"></div>')
                .append(Y.Node.create('<div class="moodle-exception-message">' + Y.Escape.html(this.get('error')) + '</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-debuginfo"><label>' +
                        M.util.get_string('url', 'moodle') + labelsep + '</label> ' +
                        this.get('reproductionlink') + '</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-debuginfo"><label>' +
                        M.util.get_string('debuginfo', 'debug') + labelsep + '</label> ' +
                        Y.Escape.html(this.get('debuginfo')) + '</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-stacktrace"><label>' +
                        M.util.get_string('stacktrace', 'debug') + labelsep + '</label> <pre>' +
                        Y.Escape.html(this.get('stacktrace')) + '</pre></div>'));
        if (M.cfg.developerdebug) {
            content.all('.moodle-exception-param').removeClass('hidden');
        }
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        if (delay) {
            this._hideTimeout = setTimeout(function() {
                self.hide();
            }, delay);
        }
        this.after('visibleChange', this.visibilityChanged, this);
        this._keypress = Y.on('key', this.hide, window, 'down:13, 27', this);
        this.centerDialogue();
    },
    visibilityChanged: function(e) {
        if (e.attrName === 'visible' && e.prevVal && !e.newVal) {
            var self = this;
            this._keypress.detach();
            setTimeout(function() {
                self.destroy();
            }, 1000);
        }
    }
}, {
    NAME: AJAXEXCEPTION_NAME,
    CSS_PREFIX: DIALOGUE_PREFIX,
    ATTRS: {

        /**
         * The error message given in the exception.
         *
         * @attribute error
         * @type String
         * @default 'Unknown error'
         * @optional
         */
        error: {
            validator: Y.Lang.isString,
            value: M.util.get_string('unknownerror', 'moodle')
        },

        /**
         * Any additional debug information given in the exception.
         *
         * @attribute stacktrace
         * @type String|null
         * @default null
         * @optional
         */
        debuginfo: {
            value: null
        },

        /**
         * The complete stack trace provided in the exception.
         *
         * @attribute stacktrace
         * @type String|null
         * @default null
         * @optional
         */
        stacktrace: {
            value: null
        },

        /**
         * A link which may be used by support staff to replicate the issue.
         *
         * @attribute reproductionlink
         * @type String
         * @default null
         * @optional
         */
        reproductionlink: {
            setter: function(link) {
                if (link !== null) {
                    link = Y.Escape.html(link);
                    link = '<a href="' + link + '">' + link.replace(M.cfg.wwwroot, '') + '</a>';
                }
                return link;
            },
            value: null
        },

        /**
         * If set, the dialogue is hidden after the specified timeout period.
         *
         * @attribute hideTimeoutDelay
         * @type Number
         * @default null
         * @optional
         */
        hideTimeoutDelay: {
            validator: Y.Lang.isNumber,
            value: null
        }
    }
});

M.core.ajaxException = AJAXEXCEPTION;


}, '@VERSION@', {"requires": ["moodle-core-notification-dialogue"]});
