YUI.add('moodle-core-notification-exception', function (Y, NAME) {

/* eslint-disable no-unused-vars, no-unused-expressions */
var DIALOGUE_PREFIX,
    BASE,
    CONFIRMYES,
    CONFIRMNO,
    TITLE,
    QUESTION,
    CSS_CLASSES;

DIALOGUE_PREFIX = 'moodle-dialogue';
BASE = 'notificationBase';
CONFIRMYES = 'yesLabel';
CONFIRMNO = 'noLabel';
TITLE = 'title';
QUESTION = 'question';
CSS_CLASSES = {
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
/**
 * A dialogue type designed to display an appropriate error when a generic
 * javascript error was thrown and caught.
 *
 * @module moodle-core-notification
 * @submodule moodle-core-notification-exception
 */

var EXCEPTION_NAME = 'Moodle exception',
    EXCEPTION;

/**
 * Extends core Dialogue to show the exception dialogue.
 *
 * @param {Object} config Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.exception
 * @extends M.core.dialogue
 */
EXCEPTION = function(c) {
    var config = Y.mix({}, c);
    config.width = config.width || (M.cfg.developerdebug) ? Math.floor(Y.one(document.body).get('winWidth') / 3) + 'px' : null;
    config.closeButton = true;

    // We need to allow some properties which are part of the exception
    // prototype, otherwise AttributeCore filters them during value normalisation.
    var allowlist = [
        'message',
        'name',
        'fileName',
        'lineNumber',
        'stack'
    ];
    Y.Array.each(allowlist, function(k) {
        config[k] = c[k];
    });

    EXCEPTION.superclass.constructor.apply(this, [config]);
};
Y.extend(EXCEPTION, M.core.notification.info, {
    _hideTimeout: null,
    _keypress: null,
    initializer: function(config) {
        var content,
            self = this,
            delay = this.get('hideTimeoutDelay'),
            labelsep = M.util.get_string('labelsep', 'langconfig');
        this.get(BASE).addClass('moodle-dialogue-exception');
        this.setStdModContent(Y.WidgetStdMod.HEADER,
                '<h5 id="moodle-dialogue-' + this.get('COUNT') + '-wrap-header-text">' + Y.Escape.html(config.name) + '</h5>',
                Y.WidgetStdMod.REPLACE);
        content = Y.Node.create('<div class="moodle-exception" data-rel="fatalerror"></div>')
                .append(Y.Node.create('<div class="moodle-exception-message">' + Y.Escape.html(this.get('message')) + '</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-filename"><label>' +
                        M.util.get_string('file', 'moodle') + labelsep + '</label> ' +
                        Y.Escape.html(this.get('fileName')) + '</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-linenumber"><label>' +
                        M.util.get_string('line', 'debug') + labelsep + '</label> ' +
                        Y.Escape.html(this.get('lineNumber')) + '</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-stacktrace"><label>' +
                        M.util.get_string('stacktrace', 'debug') + labelsep + '</label> <pre>' +
                        this.get('stack') + '</pre></div>'));
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
        this._keypress = Y.on('key', this.hide, window, 'down:13,27', this);
        this.centerDialogue();
    },
    visibilityChanged: function(e) {
        if (e.attrName === 'visible' && e.prevVal && !e.newVal) {
            if (this._keypress) {
                this._keypress.detach();
            }
            var self = this;
            setTimeout(function() {
                self.destroy();
            }, 1000);
        }
    }
}, {
    NAME: EXCEPTION_NAME,
    CSS_PREFIX: DIALOGUE_PREFIX,
    ATTRS: {
        /**
         * The message of the alert.
         *
         * @attribute message
         * @type String
         * @default ''
         */
        message: {
            value: ''
        },

        /**
         * The name of the alert.
         *
         * @attribute title
         * @type String
         * @default ''
         */
        name: {
            value: ''
        },

        /**
         * The name of the file where the error was thrown.
         *
         * @attribute fileName
         * @type String
         * @default ''
         */
        fileName: {
            value: ''
        },

        /**
         * The line number where the error was thrown.
         *
         * @attribute lineNumber
         * @type String
         * @default ''
         */
        lineNumber: {
            value: ''
        },

        /**
         * The backtrace from the error
         *
         * @attribute lineNumber
         * @type String
         * @default ''
         */
        stack: {
            setter: function(str) {
                var lines = Y.Escape.html(str).split("\n"),
                    pattern = new RegExp('^(.+)@(' + M.cfg.wwwroot + ')?(.{0,75}).*:(\\d+)$'),
                    i;
                for (i in lines) {
                    lines[i] = lines[i].replace(pattern,
                            "<div class='stacktrace-line'>ln: $4</div>" +
                            "<div class='stacktrace-file'>$3</div>" +
                            "<div class='stacktrace-call'>$1</div>");
                }
                return lines.join("\n");
            },
            value: ''
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

M.core.exception = EXCEPTION;


}, '@VERSION@', {"requires": ["moodle-core-notification-dialogue"]});
