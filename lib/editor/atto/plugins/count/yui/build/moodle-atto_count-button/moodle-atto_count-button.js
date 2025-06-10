YUI.add('moodle-atto_count-button', function (Y, NAME) {

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
 * @package    atto_count
 * @copyright  2014 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_count-button
 */

/**
 * Atto text editor count plugin.
 *
 * @namespace M.atto_count
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */
var COMPONENTNAME = 'atto_count',
    TEMPLATE = '' +
            '<form class="atto_form">' +
            '<p><label class="sameline">{{get_string "wordsinalltext" component}}</label> <span>{{allTextWords}}</span></p>' +
            '<p><label class="sameline">{{get_string "lettersinalltext" component}}</label> <span>{{allTextLetters}}</span></p>' +
            '</form>';

Y.namespace('M.atto_count').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        this.addButton({
            callback: this._displayDialogue,
            icon: 'icon',
            iconComponent: 'atto_count'
        });
    },

    /**
     * Display the word count popup.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('countwordsandletters', COMPONENTNAME)
        });

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', this._getDialogueContent());

        dialogue.show();
    },

    /**
     * Generates the content of the dialogue.
     *
     * @method _getDialogueContent
     * @return {Node} Node containing the dialogue content
     * @private
     */
    _getDialogueContent: function() {
        var template = Y.Handlebars.compile(TEMPLATE),
            url,
            params,
            response;

        url = M.cfg.wwwroot + '/lib/editor/atto/plugins/count/ajax.php';
        params = {
            sesskey: M.cfg.sesskey,
            alltext: this.get('host').getCleanHTML()
        };

        response = Y.io(url, {
            sync: true,
            data: params,
            method: 'POST'
        });

        if (response.status === 200) {
            response = Y.JSON.parse(response.responseText);
        } else {
            throw new M.core.ajaxException(response.responsetext);
        }

        this._content = Y.Node.create(template({
            component: COMPONENTNAME,
            allTextWords: response.allTextWords,
            allTextLetters: response.allTextLetters
        }));
        return this._content;
    }

});


}, '@VERSION@', {"requires": ["io", "json-parse", "moodle-editor_atto-plugin"]});
