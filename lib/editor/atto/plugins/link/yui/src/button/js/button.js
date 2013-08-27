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
 * Atto text editor link plugin.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_link = M.atto_link || {
    dialogue : null,
    selection : null,
    init : function(params) {
        var display_chooser = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            M.atto_link.selection = M.editor_atto.get_selection();
            if (M.atto_link.selection !== false && (!M.atto_link.selection.collapsed)) {
                var dialogue;
                if (!M.atto_link.dialogue) {
                    dialogue = new M.core.dialogue({
                        visible: false,
                        modal: true,
                        close: true,
                        draggable: true
                    });
                } else {
                    dialogue = M.atto_link.dialogue;
                }

                dialogue.render();
                dialogue.set('bodyContent', M.atto_link.get_form_content(elementid));
                dialogue.set('headerContent', M.util.get_string('createlink', 'atto_link'));

                M.atto_link.resolve_anchors();

                dialogue.show();
                M.atto_link.dialogue = dialogue;
            }
        };

        M.editor_atto.add_toolbar_button(params.elementid, 'link', params.icon, display_chooser, this);
    },
    resolve_anchors : function() {
        // Find the first anchor tag in the selection.
        var selectednode = M.editor_atto.get_selection_parent_node(),
            anchornode,
            url;

        // Note this is a document fragment and YUI doesn't like them.
        if (!selectednode) {
            return;
        }

        anchornode = Y.one(selectednode).ancestor('a');

        if (anchornode) {
            url = anchornode.getAttribute('href');
            if (url !== '') {
                M.atto_link.selection = M.editor_atto.get_selection_from_node(anchornode);
                Y.one('#atto_link_urlentry').set('value', url);
            }
        }
    },
    open_filepicker : function(e) {
        var elementid = this.getAttribute('data-editor');
        e.preventDefault();

        M.editor_atto.show_filepicker(elementid, 'link', M.atto_link.filepicker_callback);
    },
    filepicker_callback : function(params) {
        M.atto_link.dialogue.hide();
        if (params.url !== '') {
            M.editor_atto.set_selection(M.atto_link.selection);
            document.execCommand('unlink', false, null);
            document.execCommand('createLink', false, params.url);
        }
    },
    set_link : function(e) {
        e.preventDefault();
        M.atto_link.dialogue.hide();

        var input = e.currentTarget.get('parentNode').one('input');

        var value = input.get('value');
        if (value !== '') {
            M.editor_atto.set_selection(M.atto_link.selection);
            document.execCommand('unlink', false, null);
            document.execCommand('createLink', false, value);
        }
    },
    get_form_content : function(elementid) {
        var content = Y.Node.create('<form>' +
                             '<label for="atto_link_urlentry">' + M.util.get_string('enterurl', 'atto_link') +
                             '</label><br/>' +
                             '<input type="url" value="" id="atto_link_urlentry" size="32"/>' +
                             '<br/>' +
                             '<button id="openlinkbrowser" data-editor="' + Y.Escape.html(elementid) + '">' +
                             M.util.get_string('browserepositories', 'atto_link') +
                             '</button>' +
                             '<hr/>' +
                             '<button id="atto_link_urlentrysubmit">' +
                             M.util.get_string('createlink', 'atto_link') +
                             '</button>' +
                             '</form>' +
                             '<hr/>' + M.util.get_string('accessibilityhint', 'atto_link'));

        content.one('#atto_link_urlentrysubmit').on('click', M.atto_link.set_link);
        content.one('#openlinkbrowser').on('click', M.atto_link.open_filepicker);
        return content;
    }
};
