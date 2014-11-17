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
 * @package    atto_fontcolor
 * @copyright  2014 Rossiani Wijaya  <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_align-button
 */

/**
 * Atto text editor fontcolor plugin.
 *
 * @namespace M.atto_fontcolor
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var colors = [
        {
            name: 'white'
        }, {
            name: 'red'
        }, {
            name: 'yellow'
        }, {
            name: 'green'
        }, {
            name: 'blue'
        }, {
            name: 'black'
        }
    ];

Y.namespace('M.atto_fontcolor').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var items = [];
        Y.Array.each(colors, function(color) {
            items.push({
                text: '<div class="' + color.name + '"></div>',
                callbackArgs: color.className || color.name,
                callback: this._changeStyle
            });
        });

        this.addToolbarMenu({
            icon: 'e/text_color',
            overlayWidth: '4',
            menuColor: '#333333',
            globalItemConfig: {
                callback: this._changeStyle
            },
            items: items
        });
    },

    /**
     * Change the font color using the specified class
     *
     * @method _changeStyle
     * @param {EventFacade} e
     * @param {string} className The class for the new font
     * @private
     */
    _changeStyle: function(e, className) {
        var id = Y.stamp({});
        this.get('host').toggleInlineSelectionClass([id]);
        this.editor.one('.' + id).setAttribute('class', '');
        this.get('host').toggleInlineSelectionClass([className, 'fontcolor']);
    }
});
