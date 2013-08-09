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
 * Atto text editor title plugin.
 *
 * @package editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_title = M.atto_title || {
    init : function(params) {
        var click_h1 = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            document.execCommand('formatBlock', false, '<h1>');
        };
        var click_h2 = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            document.execCommand('formatBlock', false, '<h2>');
        };
        var click_blockquote = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            document.execCommand('formatBlock', false, '<blockquote>');
        };
        var click_p = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            document.execCommand('formatBlock', false, '<p>');
        };

        var h1 = '<h1>' +  M.util.get_string('h1', 'atto_title') + '</h1>';
        var h2 = '<h2>' +  M.util.get_string('h2', 'atto_title') + '</h2>';
        var blockquote = '<p>&nbsp;&nbsp;&nbsp;&nbsp;' +  M.util.get_string('blockquote', 'atto_title') + '</p>';
        var p = '<p>' +  M.util.get_string('p', 'atto_title') + '</p>';

        M.editor_atto.add_toolbar_menu(params.elementid,
                                                  'title',
                                                  params.icon,
                                                  [
                                                      {'text' : h1, 'handler' : click_h1},
                                                      {'text' : h2, 'handler' : click_h2},
                                                      {'text' : blockquote, 'handler' : click_blockquote},
                                                      {'text' : p, 'handler' : click_p}
                                                  ]);
    }
};
