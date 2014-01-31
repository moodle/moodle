YUI.add('moodle-atto_emoticon-button', function (Y, NAME) {

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
 * Atto text editor emoticon plugin.
 *
 * @package    atto_emoticon
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
* CSS classes and IDs.
*
* @type {Object}
*/
var CSS = {
        EMOTE: 'atto_emoticon_emote',
        MAP: 'atto_emoticon_map'
    },
    /**
    * Selectors.
    *
    * @type {Object}
    */
    SELECTORS = {
        EMOTE: '.atto_emoticon_emote'
    };

M.atto_emoticon = M.atto_emoticon || {

    /**
     * The ID of the current editor.
     *
     * @type {String}
     */
    currentElementId: null,

    /**
    * The dialogue to select a character.
    *
    * @type {M.core.dialogue}
    */
    dialogue: null,

    /**
     * List of emoticons.
    *
    * This must be populated from the result of the PHP function emoticon_manager::get_emoticons().
     *
     * @type {Array}
     */
    emoticons: null,

    /**
    * Keeps track of the selection made by the user.
    *
    * @type {Mixed}
    */
    selection: null,

    /**
    * Init.
    *
    * @param {Object} params
    *
    * @return {Void}
    */
    init: function(params) {

        M.atto_emoticon.emoticons = params.emoticons;

        var displayChooser = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }

            // Stores the selection.
            M.atto_emoticon.selection = M.editor_atto.get_selection();
            if (M.atto_emoticon.selection === false) {
                return;
            }

            // Stores what editor we are working on.
            M.atto_emoticon.currentElementId = elementid;

            // Initialising the dialogue.
            var dialogue;
            if (!M.atto_emoticon.dialogue) {
                dialogue = new M.core.dialogue({
                    visible: false,
                    modal: true,
                    close: true,
                    draggable: true
                });

                // Setting up the content of the dialogue.
                dialogue.set('bodyContent', M.atto_emoticon.getDialogueContent());
                dialogue.set('headerContent', M.util.get_string('insertemoticon', 'atto_emoticon'));
                dialogue.render();
                dialogue.centerDialogue();
                M.atto_emoticon.dialogue = dialogue;
            } else {
                dialogue = M.atto_emoticon.dialogue;
            }

            dialogue.show();
        };

        var iconurl = M.util.image_url('e/emoticons', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'emoticon', iconurl, params.group, displayChooser);
    },

    /**
     * Generates the content of the dialogue.
     *
     * @return {Node} Node containing the dialogue content
     */
    getDialogueContent: function() {
        var content,
            emote,
            emotealt,
            html = '',
            i;

        html += '<div class="' + CSS.MAP + '">';
        html += '<ul>';
        for (i = 0; i < M.atto_emoticon.emoticons.length; i++ ) {
            emote = M.atto_emoticon.emoticons[i];
            emotealt = Y.Escape.html(M.util.get_string(emote.altidentifier, emote.altcomponent));
            html += '<li>';
            html += '<div><a href="#" class="' + CSS.EMOTE + '" data-index="' + i + '">';
            html += '<img src="' + M.util.image_url(emote.imagename, emote.imagecomponent) + '" alt="' + emotealt + '" />';
            html += '</a></div>';
            html += '<div>' + Y.Escape.html(emote.text) + '</div>';
            html += '<div>' + emotealt + '</div>';
            html += '</li>';
        }
        html += '</ul>';
        html += '</div>';

        content = Y.Node.create(html);
        content.delegate('click', M.atto_emoticon.insertEmote, SELECTORS.EMOTE, this);
        content.delegate('key', M.atto_emoticon.insertEmote, '32', SELECTORS.EMOTE, this);

        return content;
    },

    /**
     * Insert the picked emote in Atto.
     *
     * @param {Event} e The event
     * @return {Void}
     */
    insertEmote: function(e) {
        var target = e.target.ancestor(SELECTORS.EMOTE, true),
            emote = M.atto_emoticon.emoticons[target.getData('index')],
            html = '';

        e.preventDefault();
        e.stopPropagation();
        M.atto_emoticon.dialogue.hide();

        html = ' ' + emote.text + ' ';
        M.editor_atto.set_selection(M.atto_emoticon.selection);
        if (document.selection && document.selection.createRange().pasteHTML) {
            document.selection.createRange().pasteHTML(html);
        } else {
            document.execCommand('insertHTML', false, html);
        }

        // Clean the YUI ids from the HTML.
        M.editor_atto.text_updated(M.atto_emoticon.currentElementId);
    }
};


}, '@VERSION@', {"requires": ["node"]});
