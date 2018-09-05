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
 * @module     core_message/message_area_actions
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core_message/message_area_events'], function(Events) {

    /** @type {Object} The list of selectors for the message area. */
    var SELECTORS = {
        MESSAGES: "[data-region='messages']"
    };

    /**
     * Actions class.
     *
     * @param {Messagearea} messageArea The messaging area object.
     */
    function Actions(messageArea) {
        this.messageArea = messageArea;
    }

    /** @type {Messagearea} The messaging area object. */
    Actions.prototype.messageArea = null;

    /**
     * Handles when we have selected to delete messages.
     */
    Actions.prototype.chooseMessagesToDelete = function() {
        // Only fire the event if we are viewing messages.
        if (this.messageArea.find(SELECTORS.MESSAGES).length !== 0) {
            this.messageArea.trigger(Events.CHOOSEMESSAGESTODELETE);
        }
    };

    return Actions;
});