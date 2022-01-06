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

/*
 * @package    atto_emoticon
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_emoticon-button
 */

var COMPONENTNAME = 'atto_emoticon',
    CSS = {
        EMOTE: 'atto_emoticon_emote',
        MAP: 'atto_emoticon_map'
    },
    SELECTORS = {
        EMOTE: '.atto_emoticon_emote'
    },
    TEMPLATE = '' +
            '<div class="{{CSS.MAP}}">' +
                '<ul>' +
                    '{{#each emoticons}}' +
                        '<li><div>' +
                            '<a href="#" class="{{../CSS.EMOTE}}" data-text="{{text}}">' +
                                '<img ' +
                                    'src="{{image_url imagename imagecomponent}}" ' +
                                    'alt="{{get_string altidentifier altcomponent}}"' +
                                '/>' +
                            '</a>' +
                        '</div>' +
                        '<div>{{text}}</div>' +
                        '<div>{{get_string altidentifier altcomponent}}</div>' +
                        '</li>' +
                    '{{/each}}' +
                '</ul>' +
            '</div>';

/**
 * Atto text editor emoticon plugin.
 *
 * @namespace M.atto_emoticon
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_emoticon').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

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
        this.addButton({
            icon: 'e/emoticons',
            callback: this._displayDialogue
        });
    },

    /**
     * Display the Emoticon chooser.
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
            headerContent: M.util.get_string('insertemoticon', COMPONENTNAME),
            focusAfterHide: true
        }, true);

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent())
                .show();
    },

    /**
     * Insert the emoticon.
     *
     * @method _insertEmote
     * @param {EventFacade} e
     * @private
     */
    _insertEmote: function(e) {
        var target = e.target.ancestor(SELECTORS.EMOTE, true),
            host = this.get('host');

        e.preventDefault();

        // Hide the dialogue.
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        // Build the Emoticon text.
        var html = ' ' + target.getData('text') + ' ';

        // Focus on the previous selection.
        host.setSelection(this._currentSelection);

        // And add the character.
        host.insertContentAtFocusPoint(html);

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
        var template = Y.Handlebars.compile(TEMPLATE),
            content = Y.Node.create(template({
                emoticons: this.get('emoticons'),
                CSS: CSS
            }));
        content.delegate('click', this._insertEmote, SELECTORS.EMOTE, this);
        content.delegate('key', this._insertEmote, '32', SELECTORS.EMOTE, this);

        return content;
    }
}, {
    ATTRS: {
        /**
         * The list of emoticons to display.
         *
         * @attribute emoticons
         * @type array
         * @default {}
         */
        emoticons: {
            value: {}
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
