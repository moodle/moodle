YUI.add('moodle-atto_emojipicker-button', function (Y, NAME) {

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
 * @package    atto_emojipicker
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_emojipicker-button
 */
var COMPONENTNAME = 'atto_emojipicker';

/**
 * Atto text editor emoji picker plugin.
 *
 * @namespace M.atto_emojipicker
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_emojipicker').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    /**
     * A reference to the current selection at the time that the dialogue
     * was opened.
     *
     * @property _currentSelection
     * @type Range
     * @private
     */
    _currentSelection: null,

    initializer: function() {
        if (this.get('disabled')) {
            return;
        }

        this.addButton({
            icon: 'e/emoticons',
            callback: this._displayDialogue
        });
    },

    /**
     * Display the emoji picker.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        // Store the current selection.
        this._currentSelection = this.get('host').getSelection();
        if (this._currentSelection === false) {
            return;
        }

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('emojipicker', COMPONENTNAME),
            width: 'auto',
            focusAfterHide: true,
            additionalBaseClass: 'emoji-picker-dialogue'
        }, true);

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
                .show();
    },

    /**
     * Insert the emoticon.
     *
     * @method _insertEmote
     * @param {String} emoji
     * @private
     */
    _insertEmoji: function(emoji) {
        var host = this.get('host');

        // Hide the dialogue.
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        // Focus on the previous selection.
        host.setSelection(this._currentSelection);

        // And add the character.
        host.insertContentAtFocusPoint(emoji);

        this.markUpdated();
    },

    /**
     * Generates the content of the dialogue, attaching event listeners to
     * the content.
     *
     * @method _getDialogueContent
     * @return {Node} Node containing the dialogue content
     * @private
     */
    _getDialogueContent: function() {
        var wrapper = Y.Node.create('<div></div>');

        require(['core/templates', 'core/emoji/picker'], function(Templates, initialiseEmojiPicker) {
                Templates.render('core/emoji/picker', {}).then(function(html) {
                    var domNode = wrapper.getDOMNode();
                    domNode.innerHTML = html;
                    initialiseEmojiPicker(domNode, this._insertEmoji.bind(this));
                    this.getDialogue().centerDialogue();
                }.bind(this));
        }.bind(this));

        return wrapper;
    }
}, {
    ATTRS: {
        disabled: {
            value: true
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
