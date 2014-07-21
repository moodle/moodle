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

// There is no error type here - because you should make errors more obvious.
var NOTIFY_WARNING = 'atto_warning',
    NOTIFY_INFO = 'atto_info';

EditorNotify.prototype = {

    /**
     * A single Y.Overlay for this editor. There is only ever one - it is replaced if a new message comes in.
     *
     * @property messageOverlay
     * @type {Y.Overlay}
     */
    messageOverlay: null,

    /**
     * Show a notification in a floaty overlay somewhere in the atto editor text area.
     *
     * @method showMessage
     * @chainable
     */
    showMessage: function(message, type) {

        if (this.messageOverlay === null) {
            this.messageOverlay = Y.Node.create('<div class="editor_atto_notification"></div>');

            this.messageOverlay.hide();
            this._wrapper.append(this.messageOverlay);

            this.messageOverlay.on('click', function() {
                this.messageOverlay.hide();
            }, this);

        }

        var messageTypeIcon = '';
        if (type === NOTIFY_WARNING) {
            messageTypeIcon = '<img width="16" height="16" src="' +
                              M.util.image_url('i/warning', 'moodle') +
                              '" alt="' + M.util.get_string('warning', 'moodle') + '"/>';
        } else if (type === NOTIFY_INFO) {
            messageTypeIcon = '<img width="16" height="16" src="' +
                              M.util.image_url('i/info', 'moodle') +
                              '" alt="' + M.util.get_string('info', 'moodle') + '"/>';
        }

        var bodyContent = Y.Node.create('<div class="' + type + '" role="alert" aria-live="assertive">' +
                                        messageTypeIcon + ' ' +
                                        Y.Escape.html(message) +
                                        '</div>');
        this.messageOverlay.empty();
        this.messageOverlay.append(bodyContent);
        this.messageOverlay.show();

        return this;
    }

};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorNotify]);
