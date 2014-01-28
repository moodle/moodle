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
        var click_h3 = function(e, elementid) {
            M.atto_title.change_title(e, elementid, '<h3>');
        };
        var click_h4 = function(e, elementid) {
            M.atto_title.change_title(e, elementid, '<h4>');
        };
        var click_h5 = function(e, elementid) {
            M.atto_title.change_title(e, elementid, '<h5>');
        };
        var click_pre = function(e, elementid) {
            M.atto_title.change_title(e, elementid, '<pre>');
        };
        var click_blockquote = function(e, elementid) {
            M.atto_title.change_title(e, elementid, '<blockquote>');
        };
        var click_p = function(e, elementid) {
            M.atto_title.change_title(e, elementid, '<p>');
        };

        var h3 = M.util.get_string('h3', 'atto_title');
        var h4 = M.util.get_string('h4', 'atto_title');
        var h5 = M.util.get_string('h5', 'atto_title');
        var pre = M.util.get_string('pre', 'atto_title');
        var blockquote = M.util.get_string('blockquote', 'atto_title');
        var p = M.util.get_string('p', 'atto_title');

        M.editor_atto.add_toolbar_menu(params.elementid,
                                                  'title',
                                                  params.icon,
                                                  params.group,
                                                  [
                                                      {'text' : h3, 'handler' : click_h3},
                                                      {'text' : h4, 'handler' : click_h4},
                                                      {'text' : h5, 'handler' : click_h5},
                                                      {'text' : pre, 'handler' : click_pre},
                                                      {'text' : blockquote, 'handler' : click_blockquote},
                                                      {'text' : p, 'handler' : click_p}
                                                  ]);
    },

    /**
     * Handle a choice from the menu (insert the node in the text editor matching elementid).
     * @param event e - The event that triggered this.
     * @param string elementid - The id of the editor
     * @param string node - The html to insert
     */
    change_title : function(e, elementid, node) {
        e.preventDefault();
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        document.execCommand('formatBlock', false, node);
        // Clean the YUI ids from the HTML.
        M.editor_atto.text_updated(elementid);
    }
};
