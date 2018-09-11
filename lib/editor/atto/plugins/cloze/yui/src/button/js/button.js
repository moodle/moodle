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
 * @package    atto_cloze
 * @copyright  2016 onward Daniel Thies <dthies@ccal.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_cloze-button
 */

/**
 * Atto text editor cloze plugin.
 *
 * @namespace M.atto_cloze
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_cloze';

var CSS = {
    ANSWER: 'atto_cloze_answer',
    ANSWERS: 'atto_cloze_answers',
    ADD: 'atto_cloze_add',
    CANCEL: 'atto_cloze_cancel',
    DELETE: 'atto_cloze_delete',
    FEEDBACK: 'atto_cloze_feedback',
    FRACTION: 'atto_cloze_fraction',
    LEFT: 'atto_cloze_col0',
    LOWER: 'atto_cloze_down',
    RIGHT: 'atto_cloze_col1',
    MARKS: 'atto_cloze_marks',
    DUPLICATE: 'atto_cloze_duplicate',
    RAISE: 'atto_cloze_up',
    SUBMIT: 'atto_cloze_submit',
    SUMMARY: 'atto_cloze_summary',
    TOLERANCE: 'atto_cloze_tolerance',
    TYPE: 'atto_cloze_qtype'
};
var TEMPLATE = {
    FORM: '<div class="atto_cloze">' +
             '<form class="atto_form">' +
             '<p>{{qtype}}' +
                 '<label for="{{elementid}}_mark">{{get_string "defaultmark" "question"}}</label>' +
                 '<input id="{{elementid}}_mark" type="text" class="{{CSS.MARKS}}" value="{{marks}}" />' +
                 '<img class="{{CSS.ADD}}" title="{{get_string "addmoreanswerblanks" "qtype_calculated"}}" src="' +
                     M.util.image_url('t/add', 'core') + '">' +
             '<div class="{{CSS.ANSWERS}}">' +
             '<ol>{{#answerdata}}' +
             '<li><div><div class="{{../CSS.LEFT}}">' +
                 '<a class="{{../CSS.ADD}}" title="{{get_string "addmoreanswerblanks" "qtype_calculated"}}">' +
                 '<img class="icon_smallicon" src="' +
                     M.util.image_url('t/add', 'core') + '"></a>' +
                 '<a class="{{../CSS.DELETE}}" title="{{get_string "delete" "core"}}">' +
                 '<img class="icon_smallicon" src="' +
                     M.util.image_url('t/delete', 'core') + '"></a>' +
                 '<a class="{{../CSS.RAISE}}" title="{{get_string "up" "core"}}">' +
                 '<img class="icon_smallicon" src="' +
                     M.util.image_url('t/up', 'core') + '"></a>' +
                 '<a class="{{../CSS.LOWER}}" title="{{get_string "down" "core"}}">' +
                 '<img class="icon_smallicon" src="' +
                     M.util.image_url('t/down', 'core') + '"></a>' +
                 '<br /><label id="{{id}}_grade">{{get_string "grade" "core"}}</label>' +
                 '<select id="{{id}}_grade" value="{{fraction}}" class="{{../CSS.FRACTION}}" selected>' +
                     '{{#if fraction}}' +
                         '<option value="{{../fraction}}">{{../fraction}}%</option>' +
                     '{{/if}}' +
                     '<option value="">{{get_string "incorrect" "question"}}</option>' +
                     '{{#../fractions}}' +
                     '<option value="{{fraction}}">{{fraction}}%</option>' +
                     '{{/../fractions}}' +
                 '</select></div>' +
                 '<div class="{{../CSS.RIGHT}}">' +
                 '<label for="{{id}}_answer">{{get_string "answer" "question"}}</label>' +
                 '<input id="{{id}}_answer" type="text" class="{{../CSS.ANSWER}}" value="{{answer}}" />' +
                 '{{#if ../numerical}}' +
                 '<label for="{{id}}_tolerance">{{{get_string "tolerance" "qtype_calculated"}}}</label>' +
                 '<input id="{{id}}_tolerance" type="text" class="{{../../CSS.TOLERANCE}}" value="{{tolerance}}" />' +
                 '{{/if}}' +
                 '<label for="{{id}}_feedback">{{get_string "feedback" "question"}}</label>' +
                 '<input id="{{id}}_feedback" type="text" class="{{../CSS.FEEDBACK}}" value="{{feedback}}" />' +
             '</div></div></li>' +
             '{{/answerdata}}</ol></div>' +
                 '<p><button type="submit" class="{{CSS.SUBMIT}}" ' +
                     'title="{{get_string "common:insert" "editor_tinymce"}}">' +
                     '{{get_string "common:insert" "editor_tinymce"}}</button>' +
                 '<button type="submit" class="{{CSS.CANCEL}}">{{get_string "cancel" "core"}}</button></p>' +
             '</form>' +
          '</div>',
    OUTPUT: '&#123;{{marks}}:{{qtype}}:{{#answerdata}}~{{#if fraction}}%{{../fraction}}%{{/if}}{{answer}}' +
          '{{#if tolerance}}:{{tolerance}}{{/if}}' +
          '{{#if feedback}}#{{feedback}}{{/if}}{{/answerdata}}&#125;',
    TYPE: '<div class="atto_cloze">{{get_string "chooseqtypetoadd" "question"}}' +
             '<form ="atto_form">' +
             '<div class="{{CSS.TYPE}}">' +
             '{{#types}}' +
             '<div class="option">' +
                 '<input name="qtype" id="qtype_qtype_{{type}}" value="{{type}}" type="radio">' +
                 '<label for="qtype_qtype_{{type}}">' +
                 '<span class="typename">{{type}}</span>' +
                 '<span class="{{../CSS.SUMMARY}}"><h6>{{name}}</h6><p>{{summary}}</p>' +
                 '<ul>{{#options}}' +
                 '<li>{{option}}</li>' +
                 '{{/options}}</ul>' +
                 '</span>' +
                 '</label></div>' +
             '{{/types}}</div>' +
                 '<p><button type="submit" class="{{CSS.SUBMIT}}" ' +
                     'title="{{get_string "add" "core"}}">{{get_string "add" "core"}}</button>' +
                 '{{#qtype}}<button type="submit" class="{{../CSS.DUPLICATE}}">' +
                     '{{get_string "duplicate" "core"}}</button>{{/qtype}}' +
                 '<button type="submit" class="{{CSS.CANCEL}}">{{get_string "cancel" "core"}}</button></p>' +
          '</form></div>'
    },
    FRACTIONS = [{fraction: 100},
        {fraction: 50},
        {fraction: 33.33333},
        {fraction: 25},
        {fraction: 20},
        {fraction: 16.66667},
        {fraction: 14.28571},
        {fraction: 12.5},
        {fraction: 11.11111},
        {fraction: 10},
        {fraction: 5},
        {fraction: 0},
        {fraction: -5},
        {fraction: -10},
        {fraction: -11.11111},
        {fraction: -12.5},
        {fraction: -14.28571},
        {fraction: -16.66667},
        {fraction: -20},
        {fraction: -25},
        {fraction: -33.333},
        {fraction: -50},
        {fraction: -100}];

Y.namespace('M.atto_cloze').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * A reference to the currently open form.
     *
     * @param _form
     * @type Node
     * @private
     */
    _form: null,

    /**
     * An array containing the current answers options
     *
     * @param _answerdata
     * @type Array
     * @private
     */
    _answerdata: null,

    /**
     * The sub question type to be edited
     *
     * @param _qtype
     * @type String
     * @private
     */
    _qtype: null,

    /**
     * The text initial selected to use as answer default
     *
     * @param _selectedText
     * @type String
     * @private
     */
    _selectedText: null,


    /**
     * The maximum marks for the sub question
     *
     * @param _marks
     * @type Integer
     * @private
     */
    _marks: null,

    /**
     * The selection object returned by the browser.
     *
     * @property _currentSelection
     * @type Range
     * @default null
     * @private
     */
    _currentSelection: null,

    initializer: function() {
        this._groupFocus = {};
        // Check whether we are editing a question.
        var form = this.get('host').editor.ancestor('body#page-question-type-multianswer form');
        // Only add plugin if this is the first editor on a multianswer question form.
        if (!form ||
                !this.get('host').editor.compareTo(form.one('.editor_atto_content')) ||
                !form.test('[action="question.php"]')) {
            return;
        }

        this.addButton({
            icon: 'icon',
            iconComponent: 'qtype_multianswer',
            callback: this._displayDialogue
        });
        this._marks = 1;
        this._answerDefault = '';

        // We need custom highlight logic for this button.
        this.get('host').on('atto:selectionchanged', function() {
            if (this._resolveSubquestion()) {
                this.highlightButtons();
            } else {
                this.unHighlightButtons();
            }
        }, this);

    },

    /**
     * Display form to edit subquestions.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {

        var host = this.get('host');

        host.editor.focus();

        // Store the current selection.
        this._currentSelection = host.getSelection();
        if (this._currentSelection === false) {
            return;
        }

        // Save selected string to set answer default answer.
        this._selectedText = this._currentSelection.toString();

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENTNAME),
            bodyContent: '<div style="height:500px"></div>',
            width: 500
        }, true);

        // Resolve whether cursor is in a subquestion.
        var subquestion = this._resolveSubquestion();
        if (subquestion) {
            this._parseSubquestion(subquestion);
            dialogue.set('bodyContent', this._getDialogueContent(null, this._qtype));
        } else {
            dialogue.set('bodyContent', this._getDialogueContent());
        }
        dialogue.show();

        this._dialogue = dialogue;
    },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @param {Event} e The event causing content to change
     * @param {String} qtype The question type to be used
     * @return {Node} The content to place in the dialogue.
     * @private
     */
    _getDialogueContent: function(e, qtype) {
        var template, content;

        if (this._form) {
            this._form.remove()
                .destroy(true);
        }

        if (!qtype) {
            template = Y.Handlebars.compile(TEMPLATE.TYPE);
            content = Y.Node.create(template({CSS: CSS,
                qtype: this._qtype,
                types: this.get('questiontypes')
                }));
            this._form = content;

            content.delegate('click', this._choiceHandler,
                '.' + CSS.SUBMIT + ', .' + CSS.DUPLICATE, this);
            content.one('.' + CSS.CANCEL).on('click', this._cancel, this);
            return content;
        }

        template = Y.Handlebars.compile(TEMPLATE.FORM);

        content = Y.Node.create(template({CSS: CSS,
            answerdata: this._answerdata,
            elementid: Y.guid(),
            fractions: FRACTIONS,
            qtype: this._qtype,
            marks: this._marks,
            numerical: (this._qtype === 'NUMERICAL' || this._qtype === 'NM')
        }));

        this._form = content;

        content.one('.' + CSS.SUBMIT).on('click', this._setSubquestion, this);
        content.one('.' + CSS.CANCEL).on('click', this._cancel, this);
        content.delegate('click', this._deleteAnswer, '.' + CSS.DELETE, this);
        content.delegate('click', this._addAnswer, '.' + CSS.ADD, this);
        content.delegate('key', this._addAnswer, 'enter', '.' + CSS.ANSWER + ', .' + CSS.FEEDBACK, this);
        content.delegate('click', this._lowerAnswer, '.' + CSS.LOWER, this);
        content.delegate('click', this._raiseAnswer, '.' + CSS.RAISE, this);

        return content;
    },

    /**
     * Find the correct answer default for the current question type
     *
     * @method _getAnswerDefault
     * @private
     * @return {String} Default answer
     */
    _getAnswerDefault: function() {
        switch (this._qtype) {
            case 'SHORTANSWER':
            case 'SA':
            case 'NUMERICAL':
            case 'NM':
                this._answerDefault = 100;
                break;
            default:
                this._answerDefault = '';
        }
        return this._answerDefault;
    },

    /**
     * Handle question choice
     *
     * @method _choiceHandler
     * @private
     * @param {Event} e Event from button click in chooser
     */
    _choiceHandler: function(e) {
        e.preventDefault();
        var qtype = this._form.one('input[name=qtype]:checked');
        if (qtype) {
            this._qtype = qtype.get('value');
            this._getAnswerDefault();
        }
        if (e && e.currentTarget && e.currentTarget.hasClass(CSS.SUBMIT)) {
            this._answerdata = [
                {
                    id: Y.guid(),
                    answer: this._selectedText,
                    feedback: '',
                    fraction: 100,
                    tolerance: 0
                }
            ];
        }
        this._dialogue.set('bodyContent', this._getDialogueContent(e, this._qtype));
        this._form.one('.' + CSS.ANSWER).focus();
    },

    /**
     * Parse question and set properties found
     *
     * @method _parseSubquestion
     * @private
     * @param {String} question The question string
     */
    _parseSubquestion: function(question) {
        var re = /\{([0-9]*):([_A-Z]+):(.*?)\}$/g,
            parts = re.exec(question);
        if (!parts) {
            return;
        }
        this._marks = parts[1];
        this._qtype = parts[2];
        this._getAnswerDefault();
        this._answerdata = [];
        var answers = parts[3].match(/(\\.|[^~])*/g);
        if (!answers) {
            return;
        }
        answers.forEach(function(answer) {
            var options = /^(%(-?[.0-9]+)%|(=?))((\\.|[^#])*)#?(.*)/.exec(answer);
            if (options && options[4]) {
                if (this._qtype === 'NUMERICAL' || this._qtype === 'NM') {
                    var tolerance = /^([^:]*):?(.*)/.exec(options[4])[2] || 0;
                    this._answerdata.push({
                        answer: this._decode(options[4].replace(/:.*/, '')),
                        id: Y.guid(),
                        feedback: this._decode(options[6]),
                        tolerance: tolerance,
                        fraction: options[3] ? 100 : options[2] || 0});
                    return;
                }
                this._answerdata.push({answer: this._decode(options[4]),
                    id: Y.guid(),
                    feedback: this._decode(options[6]),
                    fraction: options[3] ? 100 : options[2] || 0});
            }
        }, this);
    },

    /**
     * Insert a new set of answer blanks before the button.
     *
     * @method _addAnswer
     * @param {Event} e Event from button click or return
     * @private
     */
    _addAnswer: function(e) {
        e.preventDefault();
        var index = this._form.all('.' + CSS.ADD).indexOf(e.target);
        if (index === -1) {
            index = this._form.all('.' + CSS.ANSWER + ', .' + CSS.FEEDBACK).indexOf(e.target);
            if (index !== -1) {
                index = Math.floor(index / 2) + 1;
            }
        }
        if (e.target.ancestor('li')) {
            this._answerDefault = e.target.ancestor('li').one('.' + CSS.FRACTION).getDOMNode().value;
            index = this._form.all('li').indexOf(e.target.ancestor('li')) + 1;
        }
        var tolerance = 0;
        if (e.target.ancestor('li') && e.target.ancestor('li').one('.' + CSS.TOLERANCE)) {
            tolerance = e.target.ancestor('li').one('.' + CSS.TOLERANCE).getDOMNode().value;
        }
        this._getFormData()
            ._answerdata.splice(index, 0, {answer: '', id: Y.guid(), feedback: '',
                fraction: this._answerDefault, tolerance: tolerance});
        this._dialogue.set('bodyContent', this._getDialogueContent(e, this._qtype));
        this._form.all('.' + CSS.ANSWER).item(index).focus();
    },

    /**
     * Delete set of answer blanks before the button.
     *
     * @method _deleteAnswer
     * @param {Event} e Event from button click
     * @private
     */
    _deleteAnswer: function(e) {
        e.preventDefault();
        var index = this._form.all('.' + CSS.DELETE).indexOf(e.target);
        if (index === -1) {
            index = this._form.all('li').indexOf(e.target.ancestor('li'));
        }
        this._getFormData()
            ._answerdata.splice(index, 1);
        this._dialogue.set('bodyContent', this._getDialogueContent(e, this._qtype));
        var answers = this._form.all('.' + CSS.ANSWER);
        index = Math.min(index, answers.size() - 1);
        answers.item(index).focus();
    },

    /**
     * Lower answer option
     *
     * @method _lowerAnswer
     * @param {Event} e Event from button click
     * @private
     */
    _lowerAnswer: function(e) {
        e.preventDefault();
        var li = e.target.ancestor('li');
        li.insertBefore(li.next(), li);
        li.one('.' + CSS.ANSWER).focus();
    },

    /**
     * Raise answer option
     *
     * @method _raiseAnswer
     * @param {Event} e Event from button click
     * @private
     */
    _raiseAnswer: function(e) {
        e.preventDefault();
        var li = e.target.ancestor('li');
        li.insertBefore(li, li.previous());
        li.one('.' + CSS.ANSWER).focus();
    },

    /**
     * Reset and hide form.
     *
     * @method _cancel
     * @param {Event} e Event from button click
     * @private
     */
    _cancel: function(e) {
        e.preventDefault();
        this._dialogue.hide();
    },

    /**
     * Insert content into editor and reset and hide form.
     *
     * @method _setSubquestion
     * @param {Event} e Event from button click
     * @private
     */
    _setSubquestion: function(e) {
        e.preventDefault();
        var template = Y.Handlebars.compile(TEMPLATE.OUTPUT);
        this._getFormData();

        this._answerdata.forEach(function(option) {
            option.answer = this._encode(option.answer);
            option.feedback = this._encode(option.feedback);
        }, this);

        var question = template(
            {CSS: CSS,
                answerdata: this._answerdata,
                qtype: this._qtype,
                marks: this._marks
            }),
            host = this.get('host');

        this._dialogue.hide();
        host.focus();
        host.setSelection(this._currentSelection);

        // Save the selection before inserting the new question.
        var selection = window.rangy.saveSelection();
        host.insertContentAtFocusPoint(question);

        // Select the inserted text.
        window.rangy.restoreSelection(selection);
    },

    /**
     * Read and process the current data in the form.
     *
     * @method _setSubquestion
     * @chainable
     * @return {Object} self
     * @private
     */
    _getFormData: function() {
        this._answerdata = [];
        var answer,
            answers = this._form.all('.' + CSS.ANSWER),
            feedbacks = this._form.all('.' + CSS.FEEDBACK),
            fractions = this._form.all('.' + CSS.FRACTION),
            tolerances = this._form.all('.' + CSS.TOLERANCE);
        for (var i = 0; i < answers.size(); i++) {
            answer = answers.item(i).getDOMNode().value;
            if (this._qtype === 'NM' || this._qtype === 'NUMERICAL') {
                answer = Number(answer);
            }
            this._answerdata.push({answer: answer,
                id: Y.guid(), feedback: feedbacks.item(i).getDOMNode().value,
                fraction: fractions.item(i).getDOMNode().value,
                tolerance: tolerances.item(i) ? tolerances.item(i).getDOMNode().value : 0});
            this._marks = this._form.one('.' + CSS.MARKS).getDOMNode().value;
        }
        return this;
    },

    /**
     * Locate a node and offset to be used as a end of a range representing an
     * offset in the text value of a node.
     * true.
     *
     * @method _getAnchor
     * @param {DOMNode} node Parent node with text value
     * @param {Integer} offset Position of character with in text of parent node
     * @return {Object} An object with anchor and offset for the character
     * with offset in string.
     * @private
     */
    _getAnchor: function(node, offset) {
        if (!node.hasChildNodes()) {
            return {anchor: node, offset: offset};
        }
        var child = node.firstChild;
        while (offset > child.textContent.length) {
            offset -= child.textContent.length;
            child = child.nextSibling;
        }
        return this._getAnchor(child, offset);
    },

    /**
     * Find the offset for the text of a child with within the text of parent
     *
     * @method _getOffset
     * @param {DOMNode} container Parent node with text value
     * @param {DOMNode} node The node at returned offset
     * @return {Integer} The offset of the child's text
     * @private
     */
    _getOffset: function(container, node) {
        if (container === node) {
            return 0;
        }
        if (!container.contains(node)) {
            return 0;
        }
        var offset = 0,
            child = container.firstChild;
        while (!child.contains(node)) {
            offset += child.textContent.length;
            child = child.nextSibling;
        }
        return offset + this._getOffset(child, node);
    },

    /**
     * Encode answer or feedback text.
     *
     * @method _encode
     * @param {String} text Text to encode
     * @return {String} The encoded text
     * @private
     */
    _encode: function(text) {
        return String(text).replace(/(#|\}|~)/g, '\\$1');
    },

    /**
     * Decode answer or feedback text.
     *
     * @method _decode
     * @param {String} text Text to decoded
     * @return {String} The decoded text
     * @private
     */
    _decode: function(text) {
        return String(text).replace(/\\(#|\}|~)/g, '$1');
    },

    /**
     * Check whether cursor is in a subquestion and return subquestion text if
     * true.
     *
     * @method _resolveSubquestion
     * @return {Mixed} The substring describing subquestion if found
     * @private
     */
    _resolveSubquestion: function() {
        var host = this.get('host'),
            selectedNode = host.getSelectionParentNode(),
            re = /\{[0-9]*:(\\.|[^}])*?\}/g;

        if (!selectedNode) {
            return false;
        }
        var subquestions = selectedNode.textContent.match(re);
        if (!subquestions) {
            return false;
        }

        var index,
            selection = this.get('host').getSelection(),
            result = '',
            questionEnd = 0;

        if (!selection || selection.length === 0) {
            return false;
        }

        var startIndex = this._getIndex(selectedNode, selection[0].startContainer, selection[0].startOffset),
            endIndex = this._getIndex(selectedNode, selection[0].endContainer, selection[0].endOffset);

        subquestions.forEach(function(subquestion) {
            index = selectedNode.textContent.indexOf(subquestion, questionEnd);
            questionEnd = index + subquestion.length;
            if (index <= startIndex && endIndex <= questionEnd) {
                result = subquestion;
                var startRange = this._getAnchor(selectedNode, index);
                var endRange = this._getAnchor(selectedNode, questionEnd);
                selection[0].setStart(startRange.anchor, startRange.offset);
                selection[0].setEnd(endRange.anchor, endRange.offset);
                this._currentSelection = selection;
            }
        }, this);

        return result;
    },

    /**
     * Calculate the postition in text of parent node an selection end point
     *
     * @method _getIndex
     * @param {Node} selectedNode parent node
     * @param {Node} container selection end point container node
     * @param {Integer} offset selection end point offset
     * @return {String} The substring describing subquestion
     * @private
     */
    _getIndex: function(selectedNode, container, offset) {
        var index;
        if (!container.firstChild) {
            index = this._getOffset(selectedNode, container) + offset;
        } else if (container.childNodes[offset]) {
            index = this._getOffset(selectedNode, container.childNodes[offset]);
        } else {
            index = this._getOffset(selectedNode, container.lastChild) + container.lastChild.textContent.length;
        }
        return index;
    }
}, {
    ATTRS: {
        /**
         * The list of subquestion types available in this version of Moodle.
         *
         * @attribute questiontypes
         * @type array
         */
        questiontypes: []
    }
});
