YUI.add('moodle-atto_underline-button', function (Y, NAME) {

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
 * Selectors.
 *
 * @type {Object}
 */
var SELECTORS = {
    TAGS : 'u'
};

/**
 * Atto text editor underline plugin.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_underline = M.atto_underline || {
    init : function(params) {
        var click = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            document.execCommand('underline', false, null);
            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        };

        var iconurl = M.util.image_url('e/underline', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'underline', iconurl, params.group, click);
        M.editor_atto.add_button_shortcut({action: 'underline', keys: 85});

        // Attach an event listner to watch for "changes" in the contenteditable.
        // This includes cursor changes, we check if the button should be active or not, based
        // on the text selection.
        M.editor_atto.on('atto:selectionchanged', function(e) {
            if (M.editor_atto.selection_filter_matches(e.elementid, SELECTORS.TAGS, e.selectedNodes)) {
                M.editor_atto.add_widget_highlight(e.elementid, 'underline');
            } else {
                M.editor_atto.remove_widget_highlight(e.elementid, 'underline');
            }
        });
    }
};


}, '@VERSION@', {"requires": ["node", "moodle-editor_atto-editor-shortcut"]});
