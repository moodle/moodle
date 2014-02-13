YUI.add('moodle-atto_fontcolor-button', function (Y, NAME) {

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
 * Atto text editor font color plugin.
 *
 * @package editor-atto
 * @copyright  2014 Rossiani Wijaya  <rwijaya@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_fontcolor = M.atto_fontcolor || {
    dialogue : null,
    init : function(params) {
        var plugin = 'fontcolor';

        var rgb_white  = '#FFFFFF',
            rgb_red    = '#EF4540',
            rgb_yellow = '#FFCF35',
            rgb_green  = '#98CA3E',
            rgb_blue   = '#7D9FD3',
            rgb_black  = '#333333';

        var click_white = function(e, elementid) {
            M.atto_fontcolor.change_color(e, elementid, 'transparent');
        };
        var click_red = function(e, elementid) {
            M.atto_fontcolor.change_color(e, elementid, rgb_red);
        };
        var click_yellow = function(e, elementid) {
            M.atto_fontcolor.change_color(e, elementid, rgb_yellow);
        };
        var click_green = function(e, elementid) {
            M.atto_fontcolor.change_color(e, elementid, rgb_green);
        };
        var click_blue = function(e, elementid) {
            M.atto_fontcolor.change_color(e, elementid, rgb_blue);
        };
        var click_black = function(e, elementid) {
            M.atto_fontcolor.change_color(e, elementid, rgb_black);
        };

        var buttoncss = 'width: 20px; height: 20px; border: 1px solid #CCC; background-color: ';
        var white  = '<div style="' + buttoncss + rgb_white + '"></div>';
        var red    = '<div style="' + buttoncss + rgb_red + '"></div>';
        var yellow = '<div style="' + buttoncss + rgb_yellow + '"></div>';
        var green  = '<div style="' + buttoncss + rgb_green + '"></div>';
        var blue   = '<div style="' + buttoncss + rgb_blue + '"></div>';
        var black  = '<div style="' + buttoncss + rgb_black + '"></div>';

        var iconurl = M.util.image_url('e/text_color', 'core');

        M.editor_atto.add_toolbar_menu(params.elementid,
            plugin,
            iconurl,
            params.group,
            [
                {'text' : white, 'handler' : click_white},
                {'text' : red, 'handler' : click_red},
                {'text' : yellow, 'handler' : click_yellow},
                {'text' : green, 'handler' : click_green},
                {'text' : blue, 'handler' : click_blue},
                {'text' : black, 'handler' : click_black}
            ],
            false,
            false,
            '4',
            '#333333');
    },

    /**
     * Handle to change the editor font color.
     * @param event e - The event that triggered this.
     * @param string elementid - the elemen id of menu icon.
     * @param string color - The color for the background.
     */
    change_color : function(e, elementid, color) {
        e.preventDefault();
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        document.execCommand('foreColor', 0, color);
        // Clean the YUI ids from the HTML.
        M.editor_atto.text_updated(elementid);
    }
};


}, '@VERSION@');
