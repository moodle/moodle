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
 * JavaScript required by the add question pop-up.
 *
 * @module moodle-qbank_editquestion-chooser
 */

var SELECTORS = {
    CREATENEWQUESTION: 'div.createnewquestion',
    CREATENEWQUESTIONFORM: 'div.createnewquestion form',
    CHOOSERDIALOGUE: 'div.chooserdialoguebody',
    CHOOSERHEADER: 'div.choosertitle'
};

function Chooser() {
    Chooser.superclass.constructor.apply(this, arguments);
}

Y.extend(Chooser, M.core.chooserdialogue, {
    initializer: function() {
        Y.all(SELECTORS.CREATENEWQUESTIONFORM).each(function(createForm) {
            if (createForm.get('id') === 'chooserform') {
                // Not the singlebutton form. Ignore.
                return;
            }
            createForm.on('submit', this.displayQuestionChooser, this);
            createForm.one('button').set('disabled', false);
        }, this);
    },

    displayQuestionChooser: function(e) {
        var dialogue = Y.one(SELECTORS.CREATENEWQUESTION + ' ' + SELECTORS.CHOOSERDIALOGUE),
            header = Y.one(SELECTORS.CREATENEWQUESTION + ' ' + SELECTORS.CHOOSERHEADER);

        if (this.container === null) {
            // Setup the dialogue, and then prepare the chooser if it's not already been set up.
            this.setup_chooser_dialogue(dialogue, header, {});
            this.prepare_chooser();
        }

        // Update all of the hidden fields within the questionbank form.
        var originForm = e.target.ancestor('form', true),
            targetForm = this.container.one('form'),
            hiddenElements = originForm.all('input[type="hidden"]');

        targetForm.all('input.customfield').remove();
        hiddenElements.each(function(field) {
            targetForm.appendChild(field.cloneNode())
                .removeAttribute('id')
                .addClass('customfield');
        });

        // Display the chooser dialogue.
        this.display_chooser(e);
    }
}, {
    NAME: 'questionChooser'
});

M.question = M.question || {};
M.question.init_chooser = function(config) {
    return new Chooser(config);
};
