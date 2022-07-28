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
 * @package    atto_bold
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_bold-button
 */

/**
 * Atto text editor bold plugin.
 *
 * @namespace M.atto_bold
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_bold').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var bold;

        this.addButton({
            callback: this._toggleBold,
            icon: 'e/bold',
            buttonName: bold,
            inlineFormat: true,

            // Key code for the keyboard shortcut which triggers this button:
            keys: '66',

            // Watch the following tags and add/remove highlighting as appropriate:
            tags: 'strong, b'
        });
    },
    /**
     * Toggle the bold setting.
     *
     * @method _toggleBold
     * @param {EventFacade} e
     */
    _toggleBold: function() {
        var host = this.get('host');

        // Use the "bold" command for simplicity. This will toggle <strong> tags off as well.
        document.execCommand('bold', false, null);

        // Then change all <b> tags to <strong> tags. This will change any existing <b> tags as well.
        host.changeToCSS('b', 'bf-editor-bold-strong');
        host.changeToTags('bf-editor-bold-strong', 'strong');
    }
});
