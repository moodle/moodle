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
 * The module handles any actions we perform on the message area.
 *
 * @module     core_message/actions
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {

    /**
     * Actions class.
     *
     * @param {Messagearea} messageArea The messaging area object.
     */
    function Actions(messageArea) {
        this.messageArea = messageArea;
        this._init();
    }

    /** @type {Messagearea} The messaging area object. */
    Actions.prototype.messageArea = null;

    /**
     * Initialise the event listeners.
     *
     * @private
     */
    Actions.prototype._init = function() {
        this.messageArea.onDelegateEvent('click', "[data-action='choose-messages-to-delete']",
            this._chooseMessagesToDelete.bind(this));
    };

    /**
     * Handles when we have selected to delete messages.
     *
     * @private
     */
    Actions.prototype._chooseMessagesToDelete = function() {
        this.messageArea.trigger('choose-messages-to-delete');
    };

    return Actions;
});