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
 * A notify function for the Atto editor.
 *
 * @module     moodle-editor_atto-notify
 * @submodule  notify-base
 * @package    editor_atto
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function EditorNotify() {}

EditorNotify.ATTRS= {
};

EditorNotify.prototype = {

    /**
     * A single Y.Overlay for this editor. There is only ever one - it is replaced if a new message comes in.
     *
     * @property messageOverlay
     * @type {Y.Overlay}
     */
    messageOverlay: null,

    /**
     * A single timer object that can be used to cancel the hiding behaviour.
     *
     * @property hideTimer
     * @type {timer}
     */
    hideTimer: null,

    /**
     * Initialize the notifications.
     *
     * @method setupNotifications
     * @chainable
     */
    setupNotifications: function() {
        var preload1 = new Image(),
            preload2 = new Image();

        preload1.src = M.util.image_url('i/warning', 'moodle');
        preload2.src = M.util.image_url('i/info', 'moodle');

        return this;
    },

    /**
     * Show a notification in a floaty overlay somewhere in the atto editor text area.
     *
     * @method showMessage
     * @param {String} message - The translated message (use get_string)
     * @param {String} type - Must be either "info" or "warning"
     * @param {Integer} timeout - Time in milliseconds to show this message for.
     * @chainable
     */
    showMessage: function(message, type, timeout) {

        if (this.messageOverlay === null) {
            this.messageOverlay = Y.Node.create('<div class="editor_atto_notification"></div>');

            this.messageOverlay.hide();
            this._wrapper.append(this.messageOverlay);

            this.messageOverlay.on('click', function() {
                this.messageOverlay.hide();
            }, this);
        }

        if (this.hideTimer !== null) {
            this.hideTimer.cancel();
        }

        var messageTypeIcon = '';
        if (type === "warning") {
            messageTypeIcon = '<img width="16" height="16" src="' +
                              M.util.image_url('i/warning', 'moodle') +
                              '" alt="' + M.util.get_string('warning', 'moodle') + '"/>';
        } else if (type === "info") {
            messageTypeIcon = '<img width="16" height="16" src="' +
                              M.util.image_url('i/info', 'moodle') +
                              '" alt="' + M.util.get_string('info', 'moodle') + '"/>';
        } else {
            Y.log('Invalid message type specified: ' + type + '. Must be either "info" or "warning".', 'debug');
        }

        // Parse the timeout value.
        var inttimeout = parseInt(timeout, 10);
        if (inttimeout <= 0) {
            inttimeout = 60000;
        }

        // Convert class to atto_info (for example).
        type = 'atto_' + type;

        var bodyContent = Y.Node.create('<div class="' + type + '" role="alert" aria-live="assertive">' +
                                        messageTypeIcon + ' ' +
                                        Y.Escape.html(message) +
                                        '</div>');
        this.messageOverlay.empty();
        this.messageOverlay.append(bodyContent);
        this.messageOverlay.show();

        this.hideTimer = Y.later(inttimeout, this, function() {
            Y.log('Hide Atto notification.', 'debug');
            this.hideTimer = null;
            this.messageOverlay.hide();
        });

        return this;
    }

};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorNotify]);
