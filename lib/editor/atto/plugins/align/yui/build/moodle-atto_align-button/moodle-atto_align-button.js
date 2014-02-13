YUI.add('moodle-atto_align-button', function (Y, NAME) {

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
 * Atto text editor align plugin.
 *
 * @package    atto_align
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var LEFT = 'left',
    RIGHT = 'right',
    CENTER = 'center';

M.atto_align = M.atto_align || {

    /**
    * Init.
    *
    * @param {Object} params
    *
    * @return {Void}
    */
    init: function(params) {
        var iconurl, leftAlign, rightAlign, centerAlign;

        leftAlign = function(e, elementid) {
            e.preventDefault();
            M.atto_align.changeAlignment(elementid, LEFT);
        };

        centerAlign = function(e, elementid) {
            e.preventDefault();
            M.atto_align.changeAlignment(elementid, CENTER);
        };

        rightAlign = function(e, elementid) {
            e.preventDefault();
            M.atto_align.changeAlignment(elementid, RIGHT);
        };

        iconurl = M.util.image_url('e/align_left', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'align', iconurl, params.group, leftAlign,
            'left', M.util.get_string('leftalign', 'atto_align'));

        iconurl = M.util.image_url('e/align_center', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'align', iconurl, params.group, centerAlign,
            'center', M.util.get_string('center', 'atto_align'));

        iconurl = M.util.image_url('e/align_right', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'align', iconurl, params.group, rightAlign,
            'right', M.util.get_string('rightalign', 'atto_align'));
    },

    /**
     * Changes the text alignment.
     *
     * @param {String} elementid The editor ID.
     * @param {String} alignment The alignment to change to.
     * @return {Void}
     */
    changeAlignment: function(elementid, alignment) {
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }

        if (alignment === RIGHT) {
            document.execCommand('justifyRight', false, null);
        } else if (alignment === CENTER) {
            document.execCommand('justifyCenter', false, null);
        } else {
            document.execCommand('justifyLeft', false, null);
        }
    }

};


}, '@VERSION@', {"requires": []});
