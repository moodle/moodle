YUI.add('moodle-atto_media-button', function (Y, NAME) {

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
 * Atto text editor media plugin.
 *
 * @package editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_media = M.atto_media || {
    dialogue : null,
    selection : null,
    init : function(params) {
        var display_chooser = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }
            M.atto_media.selection = M.editor_atto.get_selection();
            if (M.atto_media.selection !== false) {
                var dialogue;
                if (!M.atto_media.dialogue) {
                    dialogue = new M.core.dialogue({
                        visible: false,
                        modal: true,
                        close: true,
                        draggable: true
                    });
                } else {
                    dialogue = M.atto_media.dialogue;
                }

                dialogue.render();
                dialogue.set('bodyContent', M.atto_media.get_form_content(elementid));
                dialogue.set('headerContent', M.util.get_string('createmedia', 'atto_media'));
                dialogue.centerDialogue();
                dialogue.show();
                M.atto_media.dialogue = dialogue;
            }
        };

        M.editor_atto.add_toolbar_button(params.elementid, 'media', params.icon, params.group, display_chooser, this);
    },
    open_browser : function(e) {
        var elementid = this.getAttribute('data-editor');
        e.preventDefault();

        M.editor_atto.show_filepicker(elementid, 'media', M.atto_media.filepicker_callback);
    },
    filepicker_callback : function(params) {
        if (params.url !== '') {
            var input = Y.one('#atto_media_urlentry');
            input.set('value', params.url);
            input = Y.one('#atto_media_nameentry');
            input.set('value', params.file);
        }
    },
    set_media : function(e, elementid) {
        e.preventDefault();
        M.atto_media.dialogue.hide();

        var input = e.currentTarget.ancestor('.atto_form').one('#atto_media_urlentry');
        var url = input.get('value');
        input = e.currentTarget.ancestor('.atto_form').one('#atto_media_nameentry');
        var name = input.get('value');

        if (url !== '' && name !== '') {
            M.editor_atto.set_selection(M.atto_media.selection);
            var mediahtml = '<a href="' + Y.Escape.html(url) + '">' + name + '</a>';

            if (document.selection && document.selection.createRange().pasteHTML) {
                document.selection.createRange().pasteHTML(mediahtml);
            } else {
                document.execCommand('insertHTML', false, mediahtml);
            }
            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        }
    },
    get_form_content : function(elementid) {
        var content = Y.Node.create('<form class="atto_form">' +
                             '<label for="atto_media_urlentry">' + M.util.get_string('enterurl', 'atto_media') +
                             '</label>' +
                             '<input class="fullwidth" type="url" value="" id="atto_media_urlentry" size="32"/><br/>' +
                             '<button id="openmediabrowser" data-editor="' + Y.Escape.html(elementid) + '">' +
                             M.util.get_string('browserepositories', 'atto_media') +
                             '</button>' +
                             '<label for="atto_media_nameentry">' + M.util.get_string('entername', 'atto_media') +
                             '</label>' +
                             '<input class="fullwidth" type="text" value="" id="atto_media_nameentry" size="32" required="true"/>' +
                             '<div class="mdl-align">' +
                             '<br/>' +
                             '<button id="atto_media_urlentrysubmit">' +
                             M.util.get_string('createmedia', 'atto_media') +
                             '</button>' +
                             '</div>' +
                             '</form>' +
                             '<hr/>' + M.util.get_string('accessibilityhint', 'atto_media'));

        content.one('#atto_media_urlentrysubmit').on('click', M.atto_media.set_media, this, elementid);
        content.one('#openmediabrowser').on('click', M.atto_media.open_browser);
        return content;
    }
};


}, '@VERSION@', {"requires": ["node", "escape"]});
