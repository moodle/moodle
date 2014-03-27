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

/*
 * @package    atto_rtl
 * @copyright  2014 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_rtl-button
 */

/**
 * Atto text editor rtl plugin.
 *
 * @namespace M.atto_rtl
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_rtl').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var direction;

        direction = 'ltr';
        this.addButton({
            icon: 'e/left_to_right',
            title: direction,
            buttonName: direction,
            callback: this._toggleRTL,
            callbackArgs: direction
        });

        direction = 'rtl';
        this.addButton({
            icon: 'e/right_to_left',
            title: direction,
            buttonName: direction,
            callback: this._toggleRTL,
            callbackArgs: direction
        });
    },

    /**
     * Toggle the RTL/LTR values based on the supplied direction.
     *
     * @method _toggleRTL
     * @param {EventFacade} e
     * @param {String} direction
     */
    _toggleRTL: function(e, direction) {
        var host = this.get('host'),
            selection = host.getSelection();
        if (selection) {
            // Format the selection to be sure it has a tag parent (not the contenteditable).
            var parentNode = host.formatSelectionBlock(),
                parentDOMNode = parentNode.getDOMNode();

            var currentDirection = parentDOMNode.getAttribute('dir');
            if (currentDirection === direction) {
                parentDOMNode.removeAttribute("dir");
            } else {
                parentDOMNode.setAttribute("dir", direction);
            }

            // Mark the text as having been updated.
            this.markUpdated();
        }
    }
});
