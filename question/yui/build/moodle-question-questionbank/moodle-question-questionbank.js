YUI.add('moodle-question-questionbank', function (Y, NAME) {

/**
 * The question bank popup to show question bank ccontents for mod_quiz.
 *
 *
 * @moodle-mod_quiz-questionbank
 */

var CSS = {
    PAGECONTENT : 'div#page-content',
    SELECTALL : '', // TODO:
    DESELECTALL : '', // TODO:
    HEADERCHECKBOX : '', // TODO:
    FIRSTCHECKBOX : '' // TODO:
};

var QUESTIONBANK = 'question-questionbank';

question_bank = {
    strselectall: '',
    strdeselectall: '',
    headercheckbox: null,
    firstcheckbox: null,

    // Button variables for question bank.
    var createanewquestionbutton = Y.one('#buttonid'); // TODO: Findout the real id
    var addtoquizbutton = Y.one('#addtoquiziid'); // TODO: find the new id
    var deletbutton = Y.one('#deletebuttonid'); // TODO: find the real id
    var movetobutton = Y.one('#movetiid'); // TODO: find the new id
    var addrandomquestionstoquizbutton = Y.one('#movetiid'); // TODO: find the new id

    addtoquizbutton.on('click', function(e)) {
        // Add the selected questions to the quiz.

    }

    createanewquestionbutton.on('click', function(e)) {
        // Call Create a new question popup window.

    }

    deletbutton.on('click', function(e)) {
        // Delete selected questions.

    }

    movetobutton.on('click', function(e)) {
        // Move selected question to the chosen category..

    }

    
    
    init_checkbox_column: function(Y, strselectall, strdeselectall, firstcbid) {
        question_bank.strselectall = strselectall;
        question_bank.strdeselectall = strdeselectall;

        // Find the header checkbox, and initialise it.
        question_bank.headercheckbox = document.getElementById('qbheadercheckbox');
        question_bank.headercheckbox.disabled = false;
        question_bank.headercheckbox.title = strselectall;

        // Find the first real checkbox.
        question_bank.firstcheckbox = document.getElementById(firstcbid);

        // Add the event handler.
        Y.YUI2.util.Event.addListener(question_bank.headercheckbox, 'click', question_bank.header_checkbox_click);
    },

    header_checkbox_click: function() {
        if (question_bank.firstcheckbox.checked) {
            select_all_in_element_with_id('categoryquestions', '');
            question_bank.headercheckbox.title = question_bank.strselectall;
        } else {
            select_all_in_element_with_id('categoryquestions', 'checked');
            question_bank.headercheckbox.title = question_bank.strdeselectall;
        }
        question_bank.headercheckbox.checked = false;
    }
};


/**
 * The activity chooser dialogue for courses.
 *
 * @constructor
 * @class M.mod_quiz.questionbank
 * @extends M.core.chooserdialogue
 */
var QUESTIONBANK = function() {
    QUESTIONBANK.superclass.constructor.apply(this, arguments);
};

{
    NAME : QUESTIONBANK,
    ATTRS : {
        maxheight : {
            value : 800
        }
    }
});

M.question = M.question || {};
M.question.init_qbank = function(config) {
    return new QUESTIONBANK(config);
};


}, '@VERSION@', {"requires": ["", ""]});
