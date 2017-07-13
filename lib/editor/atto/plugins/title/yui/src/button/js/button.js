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
 * @package    atto_title
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_title-button
 */

/**
 * Atto text editor title plugin.
 *
 * @namespace M.atto_title
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var component = 'atto_title',
    styles = [
        {
            text: 'h3',
            callbackArgs: '<h3>'
        },
        {
            text: 'h4',
            callbackArgs: '<h4>'
        },
        {
            text: 'h5',
            callbackArgs: '<h5>'
        },
        {
            text: 'pre',
            callbackArgs: '<pre>'
        },
        {
            text: 'p',
            callbackArgs: '<p>'
        }
    ];

Y.namespace('M.atto_title').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var items = [];
        Y.Array.each(styles, function(style) {
            items.push({
                text: M.util.get_string(style.text, component),
                callbackArgs: style.callbackArgs
            });
        });
        this.addToolbarMenu({
            icon: 'e/styleprops',
            globalItemConfig: {
                callback: this._changeStyle
            },
            items: items
        });
    },

    /**
     * Change the title to the specified style.
     *
     * @method _changeStyle
     * @param {EventFacade} e
     * @param {string} color The new style
     * @private
     */
    _changeStyle: function(e, style) {
        document.execCommand('formatBlock', false, style);

        // Mark as updated
        this.markUpdated();
    }
});
