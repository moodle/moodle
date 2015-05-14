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
 * Wrapper for the YUI M.core.notification class. Allows us to
 * use the YUI version in AMD code until it is replaced.
 *
 * @module     tool_lp/dialogue
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/yui'], function(Y) {

    // Private variables and functions.
    /**
     * Constructor
     *
     * @param {String} title Title for the window.
     * @param {String} content The content for the window.
     * @param {function} afterShow Callback executed after the window is opened.
     */
    var dialogue = function(title, content, afterShow) {
        this.yuiDialogue = null;
        var parent = this;

        Y.use('moodle-core-notification', function () {

            parent.yuiDialogue = new M.core.dialogue({
                headerContent: title,
                bodyContent: content,
                draggable: true,
                visible: false,
                center: true,
                modal: true
            });

            parent.yuiDialogue.after('visibleChange', function(e) {
                if (e.newVal) {
                    afterShow(parent);
                }
            });

            parent.yuiDialogue.show();
        });
    };

    /**
     * Close this window.
     */
    dialogue.prototype.close = function() {
        this.yuiDialogue.hide();
        this.yuiDialogue.destroy();
    };

    /**
     * Get content.
     */
    dialogue.prototype.getContent = function() {
        return this.yuiDialogue.bodyNode.getDOMNode();
    };

    return /** @alias module:tool_lp/dialogue */ dialogue;
});
