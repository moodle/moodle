YUI.add('moodle-atto_fontfamily-button', function (Y, NAME) {

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
 * @package    atto_fontfamily
 * @copyright  2015 Pau Ferrer Ocaña
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * Based on atto_fontsize - 2014 Andrew Nicols 2015 Adam Jenkins
 */

/**
 * @module moodle-atto_fontfamily-button
 */

/**
 * Atto text editor fontfamily plugin.
 *
 * @namespace M.atto_fontfamily
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_fontfamily').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    initializer: function() {
        var fonts = this.get('avalaiblefonts');

        if (fonts === undefined || fonts.length === 0) {
            return;
        }

        var items = [];
        Y.Array.each(fonts, function(font) {
            var fonttype = font.split('=');

            items.push({
                text: '<span style="font-family:' + fonttype[1] + ';">' + fonttype[0] + '</span>',
                callbackArgs: fonttype[1],
                callback: this._changeStyle
            });
        });

        this.addToolbarMenu({
            globalItemConfig: {
                callback: this._changeStyle
            },
            icon: 'icon',
            iconComponent: 'atto_fontfamily',
            items: items
        });
    },

    /**
     * Change the font family to the specified family.
     *
     * @method _changeStyle
     * @param {EventFacade} e
     * @param {string} family The new font family
     * @private
     */

    _changeStyle: function(e, size) {
        this.get('host').formatSelectionInlineStyle({
            fontFamily: size
        });
      }
}, {
    ATTRS: {
        avalaiblefonts: {
            value: {}
        }
    }
});


}, '@VERSION@');
