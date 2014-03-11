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

var doc = document,
    BackColor = 'BackColor',
    colors = [
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
            items.push({
                text: '<div style="width: 20px; height: 20px; border: 1px solid #CCC; background-color: ' +
                        color.color +
                        '"></div>',
                callbackArgs: color.color
            });
        });

        this.addToolbarMenu({
            icon: 'e/text_highlight',
            overlayWidth: '4',
            globalItemConfig: {
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
        if (window.getSelection) {
            // Test for IE9 and non-IE browsers.
            try {
                if (!doc.execCommand(BackColor, false, color)) {
                    this._fallbackChangeStyle(color);
                }
            } catch (ex) {
                this._fallbackChangeStyle(color);
            }
        } else if (doc.selection && doc.selection.createRange) {
            // Test for IE8 or less.
            range = doc.selection.createRange();
            range.execCommand(BackColor, false, color);
        }

        // Mark as updated
        this.markUpdated();
    },

    /**
     * Change the background color.
     *
     * This function is an alternative use for IE browsers.
     *
     * @method _fallbackChangeStyle
     * @param {string} color The color for the background.
     * @chainable
     * @private
     */
    _fallbackChangeStyle: function (color) {
        var selection = window.getSelection(),
            range;

        if (selection.rangeCount && selection.getRangeAt) {
            range = selection.getRangeAt(0);
        }
        doc.designMode = "on";
        if (range) {
            selection.removeAllRanges();
            selection.addRange(range);
        }

        if (!doc.execCommand("HiliteColor", false, color)) {
            doc.execCommand(BackColor, false, color);
        }
        doc.designMode = "off";

        return this;
    }
});
