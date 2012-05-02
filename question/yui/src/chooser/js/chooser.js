var SELECTORS = {
    CREATENEWQUESTION: 'div.createnewquestion',
    CREATENEWQUESTIONFORM: 'div.createnewquestion form',
    CHOOSERDIALOGUE: 'div.chooserdialogue',
    CHOOSERHEADER: 'div.choosertitle',
    QBANKCATEGORY: '#qbankcategory'
};

function Chooser() {
    Chooser.superclass.constructor.apply(this, arguments);
}

Y.extend(Chooser, M.core.chooserdialogue, {
    initializer: function() {
        Y.one(SELECTORS.CREATENEWQUESTIONFORM).on('submit', this.displayQuestionChooser, this);
    },
    displayQuestionChooser: function(e) {
        var dialogue = Y.one(SELECTORS.CREATENEWQUESTION + ' ' + SELECTORS.CHOOSERDIALOGUE),
            header = Y.one(SELECTORS.CREATENEWQUESTION + ' ' + SELECTORS.CHOOSERHEADER);

        if (this.container === null) {
            // Setup the dialogue, and then prepare the chooser if it's not already been set up.
            this.setup_chooser_dialogue(dialogue, header, {});
            this.prepare_chooser();
        }

        // Set the category ID in the form - this may have been updated since the dialogue
        // was previously displayed so we must update it here.
        this.container.one(SELECTORS.QBANKCATEGORY).set('value',
            Y.one(SELECTORS.CREATENEWQUESTIONFORM).get('category').get('value'));

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
