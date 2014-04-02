YUI.add('moodle-question-chooser', function (Y, NAME) {

var SELECTORS = {
    CREATENEWQUESTION: 'div.createnewquestion',
    CREATENEWQUESTIONFORM: 'div.createnewquestion form',
    CHOOSERDIALOGUE: 'div.chooserdialogue',
    CHOOSERHEADER: 'div.choosertitle'
};

function Chooser() {
    Chooser.superclass.constructor.apply(this, arguments);
}

Y.extend(Chooser, M.core.chooserdialogue, {
    initializer: function() {
        Y.all('form').each(function(node) {
            if (/question\/addquestion\.php/.test(node.getAttribute('action'))) {
                node.on('submit', this.displayQuestionChooser, this);
            }
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


}, '@VERSION@', {"requires": ["moodle-core-chooserdialogue"]});
