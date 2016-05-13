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
 * Send message dialog.
 *
 * @module     moodle-core_message-messenger
 * @package    core_message
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

CSSR.SENDMSGDIALOG = {
    ACCESSHIDE: 'accesshide',
    ACTIONS: 'message-actions',
    FOOTER: 'message-footer',
    HIDDEN: 'hidden',
    HISTORYLINK: 'message-history',
    INPUT: 'message-input',
    INPUTAREA: 'message-area',
    NOTICE: 'message-notice',
    NOTICEAREA: 'message-notice-area',
    PREFIX: 'core_message-messenger-sendmessage',
    SENDBTN: 'message-send',
    WRAPPER: 'message-wrapper'
};

SELECTORS.SENDMSGDIALOG = {
    FORM: 'form',
    HISTORYLINK: '.message-history',
    INPUT: '.message-input',
    NOTICE: '.message-notice div',
    NOTICEAREA: '.message-notice-area',
    SENDBTN: '.message-send'
};

/**
 * Send message dialog.
 *
 * @namespace M.core_message
 * @class SENDMSGDIALOG
 * @constructor
 */
var SENDMSGDIALOG = function() {
    SENDMSGDIALOG.superclass.constructor.apply(this, arguments);
};
Y.namespace('M.core_message.messenger').sendMessage = Y.extend(SENDMSGDIALOG, M.core.dialogue, {

    _bb: null,
    _sendLock: false,
    _hide: null,
    /**
     * Initializer.
     *
     * @method initializer
     */
    initializer: function() {
        var tpl,
            content;

        this._bb = this.get('boundingBox');
        this._hide = this.hide;

        // Prepare the content area.
        tpl = Y.Handlebars.compile(
            '<form action="#" id="messageform">' +
                '<div class="{{CSSR.INPUTAREA}}">' +
                    '<label class="{{CSSR.ACCESSHIDE}}" for="{{id}}">{{labelStr}}</label>' +
                    '<textarea class="{{CSSR.INPUT}}" id="{{id}}"></textarea>' +
                    '<div class="{{CSSR.NOTICEAREA}}" style="display: none;" aria-live="assertive">' +
                        '<div class="{{CSSR.NOTICE}}"><div></div></div>' +
                    '</div>' +
                '</div>' +
                '<div class="{{CSSR.ACTIONS}}">' +
                    '<input type="submit" value="{{sendStr}}" class="{{CSSR.SENDBTN}}">' +
                    '<a href="#" class="{{CSSR.HISTORYLINK}}">{{viewHistoryStr}}</a>' +
                    '<div style="clear: both;"></div>' +
                '</div>' +
            '</form>'
        );
        content = Y.Node.create(
            tpl({
                CSSR: CSSR.SENDMSGDIALOG,
                id: Y.guid(),
                labelStr: M.util.get_string('messagetosend', 'core_message'),
                loadingIcon: M.util.image_url('i/loading', 'moodle'),
                sendStr: M.util.get_string('sendmessage', 'core_message'),
                viewHistoryStr: M.util.get_string('viewconversation', 'core_message')
            })
        );
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        // Use standard dialogue class name. This removes the default styling of the footer.
        this._bb.one('.moodle-dialogue-wrap').addClass('moodle-dialogue-content');

        // Set the events listeners.
        this._setEvents();
    },

    /**
     * Prepare the dialog for a user.
     *
     * @method prepareForUser
     * @param  {Number} userid   The user ID.
     * @param  {String} fullname The user full name.
     */
    prepareForUser: function(userid, fullname) {
        var title;

        this.set('userid', userid);
        this.set('fullname', fullname);

        // Prepare the title.
        title = Y.Node.create('<h1>' + Y.Escape.html(fullname) + '</h1>');
        this.setStdModContent(Y.WidgetStdMod.HEADER, title, Y.WidgetStdMod.REPLACE);

        // Update the link to the conversation.
        this._bb.one(SELECTORS.SENDMSGDIALOG.HISTORYLINK)
            .set('href', M.cfg.wwwroot + '/message/index.php?id=' + this.get('userid'));

        // Set the content as empty and lock send.
        this._bb.one(SELECTORS.SENDMSGDIALOG.INPUT).set('value', '');

        // Register form with formchangechecker
        Y.use('moodle-core-formchangechecker', function() {
            M.core_formchangechecker.init({formid: "messageform"});
        });
    },

    /**
     * Send the message to the user.
     *
     * @method sendMessage
     * @param  {String} message The message to be sent.
     */
    sendMessage: function(message) {
        if (this._sendLock) {
            // Do not proceed if the lock is active.
            return;
        }

        if (!message || !this._validateMessage(message)) {
            // Do not send falsy messages.
            return;
        }

        // Actually send the message.
        this._ioSend = Y.io(this.get('url'), {
            method: 'POST',
            data: {
                sesskey: M.cfg.sesskey,
                action: 'sendmessage',
                userid: this.get('userid'),
                message: message
            },
            on: {
                start: function() {
                    var img = '<img alt="" role="presentation" src="' + M.util.image_url('i/loading_small', 'moodle') + '">';
                    this.setSendLock(true);
                    this.showNotice(img + ' ' + M.util.get_string('sendingmessage', 'core_message'));
                },
                success: function(id, response) {
                    var data = null;

                    try {
                        data = Y.JSON.parse(response.responseText);
                        if (data.error) {
                            this.hideNotice();
                            new M.core.ajaxException(data);
                            return;
                        }
                    } catch (e) {
                        this.hideNotice();
                        new M.core.exception(e);
                        return;
                    }

                    // Show a success message.
                    this.showNotice(M.util.get_string('messagesent', 'core_message'));

                    // Hide the dialog.
                    Y.later(1300, this, function() {
                        this.setSendLock(false);
                        this.hideNotice();
                        this.hide();
                    });
                },
                failure: function() {
                    this.setSendLock(false);
                    this.hideNotice();
                    new M.core.alert({
                        title: M.util.get_string('error', 'core'),
                        message: M.util.get_string('errorwhilesendingmessage', 'core_message')
                    });
                }
            },
            context: this
        });
    },

    /**
     * Override the default hide function.
     * @method hide
     */
    hide: function() {
        var self = this;

        if (!M.core_formchangechecker.get_form_dirty_state()) {
            return SENDMSGDIALOG.superclass.hide.call(this, arguments);
        }

        Y.use('moodle-core-notification-confirm', function() {
            var confirm = new M.core.confirm({
                title : M.util.get_string('confirm', 'moodle'),
                question : M.util.get_string('changesmadereallygoaway', 'moodle'),
                yesLabel : M.util.get_string('confirm', 'moodle'),
                noLabel : M.util.get_string('cancel', 'moodle')
            });
            confirm.on('complete-yes', function() {
                M.core_formchangechecker.reset_form_dirty_state();
                confirm.hide();
                confirm.destroy();
                return SENDMSGDIALOG.superclass.hide.call(this, arguments);
            }, self);
        });
    },

    /**
     * Show a notice.
     *
     * @method hideNotice.
     */
    hideNotice: function() {
        this._bb.one(SELECTORS.SENDMSGDIALOG.NOTICEAREA).hide();
    },

    /**
     * Show a notice.
     *
     * @param {String} html String to show.
     * @method showNotice.
     */
    showNotice: function(html) {
        this._bb.one(SELECTORS.SENDMSGDIALOG.NOTICE).setHTML(html);
        this._bb.one(SELECTORS.SENDMSGDIALOG.NOTICEAREA).show();
    },

    /**
     * Set the send lock.
     *
     * We do not lock the send button because that would cause a focus change on screenreaders
     * which then conflicts with the aria-live region reading out that we are sending a message.
     *
     * @method setSendLock
     * @param  {Boolean} lock When true, enables the lock.
     */
    setSendLock: function(lock) {
        if (lock) {
            this._sendLock = true;
        } else {
            this._sendLock = false;
        }
    },

    /**
     * Register the events.
     *
     * @method _setEvents.
     */
    _setEvents: function() {
        // Form submit.
        this._bb.one(SELECTORS.SENDMSGDIALOG.FORM).on('submit', function(e) {
            var message = this._bb.one(SELECTORS.SENDMSGDIALOG.INPUT).get('value');
            e.preventDefault();
            this.sendMessage(message);
        }, this);
    },

    /**
     * Validates a message.
     *
     * @method _validateMessage
     * @param  {String} message A message to be validated.
     */
    _validateMessage: function(message) {
        var trimmed;
        if (!message) {
            return false;
        }

        // Basic validation.
        trimmed = message.replace(' ', '')
                         .replace('&nbsp;', '')
                         .replace(/(<br\s*\/?>(<\/br\s*\/?>)?)+/, '')
                         .trim();

        return trimmed.length > 1;
    }

}, {
    NAME: 'core_message-messenger-sendmessage',
    CSS_PREFIX: CSSR.SENDMSGDIALOG.PREFIX,
    ATTRS: {

        /**
         * Fullname of the user.
         *
         * @attribute fullname
         * @default ''
         * @type String
         */
        fullname: {
            validator: Y.Lang.isString,
            value: ''
        },

        /**
         * URL to the message Ajax actions.
         *
         * @attribute url
         * @default null
         * @type String
         */
        url: {
            validator: Y.Lang.isString,
            value: null
        },

        /**
         * User ID this dialog interacts with.
         *
         * @attribute userid
         * @default 0
         * @type Number
         */
        userid: {
            validator: Y.Lang.isNumber,
            value: 0
        }
    }
});

Y.Base.modifyAttrs(Y.namespace('M.core_message.messenger.sendMessage'), {

    /**
     * List of extra classes.
     *
     * @attribute extraClasses
     * @default ['core_message-messenger-sendmessage']
     * @type Array
     */
    extraClasses: {
        value: ['core_message-messenger-sendmessage']
    },

    /**
     * Whether to focus on the target that caused the Widget to be shown.
     *
     * @attribute focusOnPreviousTargetAfterHide
     * @default true
     * @type Node
     */
    focusOnPreviousTargetAfterHide: {
        value: true
    },

    /**
     *
     * Width.
     *
     * @attribute width
     * @default '260px'
     * @type String|Number
     */
    width: {
        value: '360px'
    },

    /**
     * Boolean indicating whether or not the Widget is visible.
     *
     * @attribute visible
     * @default false
     * @type Boolean
     */
    visible: {
        value: false
    },

   /**
    * Whether the widget should be modal or not.
    *
    * @attribute modal
    * @type Boolean
    * @default true
    */
    modal: {
        value: true
    },

   /**
    * Whether the widget should be draggable or not.
    *
    * @attribute draggable
    * @type Boolean
    * @default false
    */
    draggable: {
        value: false
    },

    /**
     * Whether to display the dialogue centrally on the screen.
     *
     * @attribute center
     * @type Boolean
     * @default false
     */
    center: {
        value : true
    }

});
