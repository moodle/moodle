YUI.add('moodle-mod_quiz-randomquestion', function (Y, NAME) {

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
 * Add a random question functionality for a popup in quiz editing page.
 *
 * @package   mod_quiz
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var CSS = {
    RANDOMQUESTIONFORM: 'div.randomquestionformforpopup',
    PAGEHIDDENINPUT: 'input#rform_qpage',
    RANDOMQUESTIONLINKS: '.menu [data-action="addarandomquestion"]'
};

var PARAMS = {
    PAGE: 'addonpage',
    HEADER: 'header',
    FORM: 'form'
};

var POPUP = function() {
    POPUP.superclass.constructor.apply(this, arguments);
};

Y.extend(POPUP, Y.Base, {

    dialogue: function(header) {
        // Create a dialogue on the page and hide it.
        var config = {
            headerContent: header,
            bodyContent: Y.one(CSS.RANDOMQUESTIONFORM),
            draggable: true,
            modal: true,
            zIndex: 1000,
            centered: false,
            width: 'auto',
            visible: false,
            postmethod: 'form',
            footerContent: null
        };
        var popup = {dialog: null};
        popup.dialog = new M.core.dialogue(config);
        popup.dialog.show();
    },

    initializer: function() {
        Y.one('body').delegate('click', this.display_dialogue, CSS.RANDOMQUESTIONLINKS, this);
    },

    display_dialogue: function(e) {
        e.preventDefault();

        Y.one(CSS.RANDOMQUESTIONFORM + ' ' + CSS.PAGEHIDDENINPUT).set('value',
                e.currentTarget.getData(PARAMS.PAGE));

        this.dialogue(e.currentTarget.getData(PARAMS.HEADER));
    }
});

M.mod_quiz = M.mod_quiz || {};
M.mod_quiz.randomquestion = M.mod_quiz.randomquestion || {};
M.mod_quiz.randomquestion.init = function() {
    return new POPUP();
};


}, '@VERSION@', {"requires": ["base", "event", "node", "io", "moodle-core-notification-dialogue"]});
