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
 * @package    atto_noautolink
 * @copyright  2014 Andrew Davis  <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_noautolink-button
 */

/**
 * Atto text editor noautolink plugin.
 *
 * @namespace M.atto_noautolink
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_noautolink').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        this.addButton({
            icon: 'e/prevent_autolink',
            callback: this._preventAutoLink,
            tags: '.nolink'
        });
    },

    /**
     * Prevent autolinking of the selected region.
     *
     * @method _preventAutoLink
     * @param {EventFacade} e
     * @private
     */
    _preventAutoLink: function() {
        // Toggle inline selection class
        this.get('host').toggleInlineSelectionClass(['nolink']);
    }
});
