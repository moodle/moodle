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
 * JS used when deleting messages.
 *
 * @module     moodle-core_message-toolbox
 * @package    core_message
 * @copyright  2015 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var CSS = {
    ACCESSHIDE: 'accesshide',
    MESSAGEACTIVE: 'messageactive'
};
var SELECTORS = {
    DELETEICON: '.deleteicon',
    MESSAGEACTIVE: '.messageactive',
    MESSAGEHISTORY: '.messagehistory',
    MESSAGES: '.messagecontent'
};

M.core_message = M.core_message || {};
M.core_message.toolbox = M.core_message.toolbox || {};

/**
 * This class contains the JS related to deleting messages.
 *
 * @class M.core_message.toolbox.deletemsg
 * @constructor
 */
M.core_message.toolbox.deletemsg = {

    /**
     * The area the messages are contained.
     *
     * @property messagearea
     */
    messagearea: null,

    /**
     * Initializer.
     *
     * Sets up event listeners which 'activate' and 'deactivate' a message.
     *
     * @method init
     */
    init: function() {
        this.messagearea = Y.one(SELECTORS.MESSAGEHISTORY);

        // Set the events.
        this.messagearea.delegate('hover', this.over, this.out, SELECTORS.MESSAGES);
        this.messagearea.delegate('click', this.click, SELECTORS.MESSAGES, this);
    },

    /**
     * Handles when a mouse hovers over a message.
     *
     * @private
     * @params {EventFacade} e
     * @method over
     */
    over: function(e) {
        // 'Activate' the message area we hovered on.
        e.currentTarget.addClass(CSS.MESSAGEACTIVE);
        e.currentTarget.one(SELECTORS.DELETEICON).removeClass(CSS.ACCESSHIDE);
    },

    /**
     * Handles when a mouse hovers off a message.
     *
     * @private
     * @params {EventFacade} e
     * @method out
     */
    out: function(e) {
        // 'Deactivate' the message area we hovered off.
        e.currentTarget.removeClass(CSS.MESSAGEACTIVE);
        e.currentTarget.one(SELECTORS.DELETEICON).addClass(CSS.ACCESSHIDE);
    },

    /**
     * Handles when a mouse clicks on a message.
     *
     * @private
     * @params {EventFacade} e
     * @method click
     */
    click: function(e) {
        // 'Deactivate' the currently active message (if there is one).
        var activemessage = this.messagearea.one(SELECTORS.MESSAGEACTIVE);
        if (activemessage) {
            activemessage.removeClass(CSS.MESSAGEACTIVE);
            activemessage.one(SELECTORS.DELETEICON).addClass(CSS.ACCESSHIDE);
        }
        // 'Activate' the message area we clicked on.
        e.currentTarget.addClass(CSS.MESSAGEACTIVE);
        e.currentTarget.one(SELECTORS.DELETEICON).removeClass(CSS.ACCESSHIDE);
    }
};
