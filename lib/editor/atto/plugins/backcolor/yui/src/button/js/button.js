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
 * @package    atto_backcolor
 * @copyright  2014 Rossiani Wijaya  <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_backcolor-button
 */

/**
 * Atto text editor backcolor plugin.
 *
 * @namespace M.atto_backcolor
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var colors = [
        {
            name: 'white',
            color: '#FFFFFF'
        }, {
            name: 'red',
            color: '#EF4540'
        }, {
            name: 'yellow',
            color: '#FFCF35'
        }, {
            name: 'green',
            color: '#98CA3E'
        }, {
            name: 'blue',
            color: '#7D9FD3'
        }, {
            name: 'black',
            color: '#333333'
        }
    ];

Y.namespace('M.atto_backcolor').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var items = [];
        Y.Array.each(colors, function(color) {
            var colorLabel = M.util.get_string('color_' + color.name, 'atto_backcolor');
            items.push({
                text: '<div class="coloroption" style="background-color: '
                        + color.color + '" aria-label="' + colorLabel + '" title="' + colorLabel + '"></div>',
                callbackArgs: color.color
            });
        });

        this.addToolbarMenu({
            icon: 'e/text_highlight',
            overlayWidth: '4',
            globalItemConfig: {
                inlineFormat: true,
                callback: this._changeStyle
            },
            items: items
        });
    },

    /**
     * Change the background color to the specified color.
     *
     * @method _changeStyle
     * @param {EventFacade} e
     * @param {string} color The new background color
     * @private
     */
    _changeStyle: function(e, color) {
        this.get('host').formatSelectionInlineStyle({
            backgroundColor: color
        });
    }
});
