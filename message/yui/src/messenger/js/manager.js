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
 * Messenger manager.
 *
 * @module     moodle-core_message-messenger
 * @package    core_message
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

SELECTORS.MANAGER = {
    SENDMESSAGE: '[data-trigger="core_message-messenger::sendmessage"]'
};

/**
 * Messenger manager.
 *
 * @namespace M.core_message.messenger
 * @class MANAGER
 * @constructor
 */
var MANAGER = function() {
    MANAGER.superclass.constructor.apply(this, arguments);
};
Y.namespace('M.core_message.messenger').Manager = Y.extend(MANAGER, Y.Base, {

    _sendMessageDialog: null,
    _events: [],

    /**
     * Initializer.
     *
     * @method initializer
     */
    initializer: function() {
        this._setEvents();
    },

    /**
     * Sending a message.
     *
     * @method sendMessage
     * @param  {Number} userid   The user ID to send a message to.
     * @param  {String} fullname The fullname of the user.
     * @param  {EventFacade} e   The event triggered, when any it should be passed to the dialog.
     */
    sendMessage: function(userid, fullname, e) {
        var Klass;
        if (!this._sendMessageDialog) {
            Klass = Y.namespace('M.core_message.messenger.sendMessage');
            this._sendMessageDialog = new Klass({
                url: this.get('url')
            });
        }

        this._sendMessageDialog.prepareForUser(userid, fullname);
        this._sendMessageDialog.show(e);
    },

    /**
     * Pop up an alert dialogue to notify the logged in user that they are blocked from
     * messaging the target user.
     *
     * @method alertBlocked
     * @param  {String} blockedString The identifier to retrieve the blocked user message.
     * @param  {String} fullName The target user's full name.
     */
    alertBlocked: function(blockedString, fullName) {
        new M.core.alert({
            title: M.util.get_string('error', 'core'),
            message: M.util.get_string(blockedString, 'message', fullName)
        });
    },

    /**
     * Register the events.
     *
     * @method _setEvents.
     */
    _setEvents: function() {
        var captureEvent = function(e) {
            var target = e.currentTarget,
                userid = parseInt(target.getData('userid'), 10),
                fullname = target.getData('fullname'),
                blockedString = target.getData('blocked-string');

            if (!userid || !fullname) {
                return;
            }

            // Pass the validation before preventing defaults.
            e.preventDefault();
            if (blockedString) {
                this.alertBlocked(blockedString, fullname);
            } else {
                this.sendMessage(userid, fullname, e);
            }
        };

        this._events.push(Y.delegate('click', captureEvent, 'body', SELECTORS.MANAGER.SENDMESSAGE, this));
        this._events.push(Y.one(Y.config.doc).delegate('key', captureEvent, 'space', SELECTORS.MANAGER.SENDMESSAGE, this));
    }

}, {
    NAME: 'core_message-messenger-manager',
    ATTRS: {

        /**
         * URL to the message Ajax actions.
         *
         * @attribute url
         * @default M.cfg.wwwroot + '/message/ajax.php'
         * @type String
         */
        url: {
            readonly: true,
            value: M.cfg.wwwroot + '/message/ajax.php'
        }
    }
});

var MANAGERINST;
Y.namespace('M.core_message.messenger').init = function(config) {
    if (!MANAGERINST) {
        // Prevent duplicates of the manager if this function is called more than once.
        MANAGERINST = new MANAGER(config);
    }
    return MANAGERINST;
};
