// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin tiny_cloze for TinyMCE v6 in Moodle.
 *
 * @module      tiny_cloze/ui
 * @copyright   2023 MoodleDACH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalEvents from 'core/modal_events';
import Modal from 'core/modal';
import ModalFactory from 'core/modal_factory';
import Mustache from 'core/mustache';
import {get_strings as getStrings} from 'core/str';
import {component} from './common';
import {hasQtypeMultianswerrgx} from './options';
import {
  CSS, TEMPLATE,
  markerClass, markerSpan,
  isNull, strdecode, strencode, indexOfNode,
  getUuid, getFractionOptions, getQuestionTypes,
  hasInvalidChars, hasOddBracketCount, isCustomGrade, setStr,
  selectCustomPercent
} from './cloze';

// Language strings used in the modal dialogue.
const STR = {};

/**
 * The editor instance that is injected via the onInit() function.
 *
 * @type {tinymce.Editor}
 * @private
 */
let _editor = null;

/**
 * A reference to the currently open form.
 *
 * @param _form
 * @type {Node}
 * @private
 */
let _form = null;

/**
 * An array containing the current answers options
 *
 * @param _answerdata
 * @type {Array}
 * @private
 */
let _answerdata = [];

/**
 * The sub question type to be edited
 *
 * @param _qtype
 * @type {string|null}
 * @private
 */
let _qtype = null;

/**
 * Remember the pos of the selected node.
 * @type {number}
 * @private
 */
let _selectedOffset = -1;

/**
 * The maximum marks for the sub question
 *
 * @param _marks
 * @type {Integer}
 * @private
 */
let _marks = 1;

/**
 * The modal dialogue to be displayed when designing the cloze question types.
 * @type {Modal|null}
 */
let _modal = null;

/**
 * If its a normal selection of text, use it for the first answer field.
 * @type {string|null}
 */
let _firstAnswer = null;

/**
 * When selecting a text portion that is used for the first answer field, remember
 * any whitespace before and after the selection.
 * 0 => no whitespace, 1 => whitespace before, 2 => whitespace after, 3 => whitespace before and after.
 * @type {int}
 */
let _selectedPrefixAndSuffix = 0;

/**
 * Inject the editor instance and add markers to the cloze question texts.
 * @param {tinymce.Editor} ed
 */
const onInit = function(ed) {
  _editor = ed; // The current editor instance.
  // Add the marker spans.
  _addMarkers();
  // And get the language strings.
  _getStr();
};

/**
 * Regex to recognize the question string in the text e.g. {1:NUMERICAL:...} or {:MULTICHOICE:...}
 * @param {tinymce.Editor} editor
 * @return {RegExp}
 * @private
 */
const _getRegexQtype = (editor) => {
  // eslint-disable-next-line max-len
  const baseQtypes = 'MULTICHOICE(_H|_V|_S|_HS|_VS)?|MULTIRESPONSE(_H|_S|_HS)?|NUMERICAL|SHORTANSWER(_C)?|SAC?|NM|MWC?|M[CR](V|H|VS|HS)?';
  const extQtypes = hasQtypeMultianswerrgx(editor) ? '|REGEXP(_C)?|RXC?' : '';
  return new RegExp('\\{([0-9]*):(' + baseQtypes + extQtypes + '):(.*?)(?<!\\\\)\\}', 'g');
};

/**
 * Load strings for the modal dialogue from the language packs.
 * @private
 */
const _getStr = async() => {
  let strToFetch = [
    {key: 'answer', component: 'question'},
    {key: 'chooseqtypetoadd', component: 'question'},
    {key: 'defaultmark', component: 'question'},
    {key: 'feedback', component: 'question'},
    {key: 'correct', component: 'question'},
    {key: 'incorrect', component: 'question'},
    {key: 'addmoreanswerblanks', component: 'qtype_calculated'},
    {key: 'delete', component: 'core'},
    {key: 'up', component: 'core'},
    {key: 'down', component: 'core'},
    {key: 'tolerance', component: 'qtype_calculated'},
    {key: 'gradenoun', component: 'core'},
    {key: 'caseno', component: 'mod_quiz'},
    {key: 'caseyes', component: 'mod_quiz'},
    {key: 'answersingleno', component: 'qtype_multichoice'},
    {key: 'answersingleyes', component: 'qtype_multichoice'},
    {key: 'layoutselectinline', component: 'qtype_multianswer'},
    {key: 'layouthorizontal', component: 'qtype_multianswer'},
    {key: 'layoutvertical', component: 'qtype_multianswer'},
    {key: 'shufflewithin', component: 'mod_quiz'},
    {key: 'layoutmultiple_horizontal', component: 'qtype_multianswer'},
    {key: 'layoutmultiple_vertical', component: 'qtype_multianswer'},
    {key: 'pluginnamesummary', component: 'qtype_multichoice'},
    {key: 'pluginnamesummary', component: 'qtype_shortanswer'},
    {key: 'pluginnamesummary', component: 'qtype_numerical'},
    {key: 'multichoice', component},
    {key: 'multiresponse', component},
    {key: 'numerical', component: 'mod_quiz'},
    {key: 'shortanswer', component: 'mod_quiz'},
    {key: 'cancel', component: 'core'},
    {key: 'select', component},
    {key: 'insert', component},
    {key: 'pluginname', component},
    {key: 'customgrade', component},
    {key: 'err_custom_rate', component},
    {key: 'err_empty_answer', component},
    {key: 'err_none_correct', component},
    {key: 'err_not_numeric', component},
    {key: 'err_invalid_chars', component},
    {key: 'err_invalid_brackets', component},
  ];
  let langKeys = [
    'answer',
    'chooseqtypetoadd',
    'defaultmark',
    'feedback',
    'correct',
    'incorrect',
    'addmoreanswerblanks',
    'delete',
    'up',
    'down',
    'tolerance',
    'grade',
    'caseno',
    'caseyes',
    'singleno',
    'singleyes',
    'selectinline',
    'horizontal',
    'vertical',
    'shuffle',
    'multi_horizontal',
    'multi_vertical',
    'summary_multichoice',
    'summary_shortanswer',
    'summary_numerical',
    'multichoice',
    'multiresponse',
    'numerical',
    'shortanswer',
    'btn_cancel',
    'btn_select',
    'btn_insert',
    'title',
    'custom_grade',
    'err_custom_rate',
    'err_empty_answer',
    'err_none_correct',
    'err_not_numeric',
    'err_invalid_chars',
    'err_invalid_brackets',
  ];
  if (hasQtypeMultianswerrgx(_editor)) {
    strToFetch.push({key: 'regexp', component: 'qtype_regexp'});
    strToFetch.push({key: 'pluginnamesummary', component: 'qtype_regexp'});
    langKeys.push('regexp');
    langKeys.push('summary_regexp');
  }
  getStrings(strToFetch).then(function() {
    const args = Array.from(arguments);
    langKeys.map((l, i) => {
      setStr(l, args[0][i]);
      STR[l] = args[0][i];
      return ''; // Make the linter happy.
    });
    return ''; // Make the linter happy.
  }).catch(() => {
    return '';
  });
};

/**
 * Create the modal.
 * @return {Promise<void>}
 * @private
 */
const _createModal = async function() {
  // Create the modal dialogue. Depending on whether we have a selected node or not, the content is different.
  const cfg = {
    title: STR.title,
    templateContext: {
      elementid: _editor.id
    },
    removeOnClose: true,
    large: true,
  };
  if (typeof Modal.create === 'function') {
    _modal = await Modal.create(cfg);
  } else {
    _modal = await ModalFactory.create(cfg);
  }
};

/**
 * Display modal dialogue to edit a cloze question. Either a form is displayed to edit subquestion or a list
 * of possible questions is show.
 *
 * @method displayDialogue
 * @public
 */
const displayDialogue = async function() {
  await _createModal();

  // Resolve whether cursor is in a subquestion.
  const subquestion = resolveSubquestion();
  if (subquestion) {
    _firstAnswer = null;
    // Subquestion found, remember which node of the marker nodes is selected.
    _selectedOffset = indexOfNode(_editor.dom.select('.' + markerClass), subquestion);
    _parseSubquestion(subquestion.innerHTML);
    _setDialogueContent(_qtype);
  } else {
    // No subquestion found, no offset to remember.
    _selectedOffset = -1;
    _firstAnswer = _editor.selection.getContent();
    _selectedPrefixAndSuffix = 0;
    if (_firstAnswer[0] === ' ') {
      _selectedPrefixAndSuffix = 1;
    }
    if (_firstAnswer[_firstAnswer.length - 1] === ' ') {
      _selectedPrefixAndSuffix += 2;
    }
    _firstAnswer = _firstAnswer.trim();
    _setDialogueContent();
  }
};

/**
 * On double click, check that we are on a question and display the dialogue with the question to edit.
 * @method displayDialogueForEdit
 * @param {Node} target
 * @public
 */
const displayDialogueForEdit = async function(target) {

  const subquestion = resolveSubquestion(target);
  if (!subquestion) {
    return;
  }
  await _createModal();
  _selectedOffset = indexOfNode(_editor.dom.select('.' + markerClass), subquestion);
  _parseSubquestion(subquestion.innerHTML);
  _setDialogueContent(_qtype);
};

/**
 * Search for cloze questions based on a regular expression. All the matching snippets at least contain the cloze
 * question definition. Although Moodle does not support encapsulated other functions within curly brackets, we
 * still try to find the correct closing bracket. The so extracted cloze question is surrounded by a marker span
 * element, that contains attributes so that the content inside the span cannot be modified by the editor (in the
 * textarea). Also, this makes it a lot easier to select the question, edit it in the dialogue and replace the result
 * in the existing text area.
 *
 * @method _addMarkers
 * @private
 */
const _addMarkers = function() {

  let content = _editor.getContent();
  let newContent = '';

  // Check if there is already a marker span. In this case we do not have to do anything.
  if (content.indexOf(markerClass) !== -1) {
    return;
  }

  let m;
  do {
    m = content.match((_getRegexQtype(_editor)));
    if (!m) { // No match of a cloze question, then we are done.
      newContent += content;
      break;
    }
    // Copy the current match to the new string preceded with the <span>.
    const pos = content.indexOf(m[0]);
    newContent += content.substring(0, pos) + markerSpan + content.substring(pos, pos + m[0].length);
    content = content.substring(pos + m[0].length);

    // Count the { in the string, should be just one (the very first one at position 0).
    let level = (m[0].match(/\{/g) || []).length;
    if (level === 1) {
      // If that's the case, we close the span and the cloze question text is the innerHTML of that marker span.
      newContent += '</span>';
      continue; // Look for the next matching cloze question.
    }
    // If there are more { than } in the string, then we did not find the corresponding } that belongs to the cloze string.
    while (level > 1) {
      const a = content.indexOf('{');
      const b = content.indexOf('}');
      if (a > -1 && b > -1 && a < b) { // The { is before another } so remember to find as many } until we back at level 1.
        level++;
        newContent = content.substring(0, a);
        content = content.substring(a + 1);
      } else if (b > -1) { // We found a closing } to a previously {.
        newContent = content.substring(0, b);
        content = content.substring(b + 1);
        level--;
      } else {
        level = 1; // Should not happen, just to stop the endless loop.
      }
    }
    newContent += '</span>';
  } while (m);
  _editor.setContent(newContent);
};

/**
 * Look for the marker span elements around a cloze question and remove that span. Also, the marker for a new
 * node to be inserted would be removed here as well.
 */
const _removeMarkers = function() {
  for (const span of _editor.dom.select('span.' + markerClass)) {
    _editor.dom.setOuterHTML(span, span.classList.contains('new') ? '' : span.innerHTML);
  }
};

/**
 * When the source code view dialogue is show, we must remove the spans around the cloze question strings
 * from the editor content and add them again when the dialogue is closed.
 * Since this event is also triggered when the editor data is saved, we use this function to remove the
 * highlighting content at that time.
 *
 * @method onBeforeGetContent
 * @param {object} content
 * @public
 */
const onBeforeGetContent = function(content) {
  if (!isNull(content.source_view) && content.source_view === true) {
    // If the user clicks on 'Cancel' or the close button on the html
    // source code dialog view, make sure we re-add the visual styling.
    var onClose = function() {
      _editor.off('close', onClose);
      _addMarkers();
    };
    _editor.on('CloseWindow', () => {
      onClose();
    });
    // Remove markers only if modal is not called, otherwise we will lose our new question marker.
    if (!_modal) {
      _removeMarkers();
    }
  }
};

/**
 * Fires when the form containing the editor is submitted.
 *
 * @method onSubmit
 * @public
 */
const onSubmit = function() {
  _removeMarkers();
};

/**
 * Set the dialogue content for the tool, attaching any required events. Either the modal dialogue displays
 * a list of the question types for the form for a particular question to edit. The set content is also
 * called when the form has changed (up or down move, deletion and adding a response). We must be aware of that
 * an event to the dialogue buttons must be attached once only. Therefore, when the form content is modified, only
 * the form events for the answers are set again, the general events are nor (nomodalevents is true then).
 *
 * @method _setDialogueContent
 * @param {String} qtype The question type to be used
 * @param {boolean} nomodalevents Optional do not attach events.
 * @private
 */
const _setDialogueContent = function(qtype, nomodalevents) {
  const footer = Mustache.render(TEMPLATE.FOOTER, {
    cancel: STR.btn_cancel,
    submit: !qtype ? STR.btn_select : STR.btn_insert,
  });
  let contentText;
  if (!qtype) {
    contentText = Mustache.render(TEMPLATE.TYPE, {
      CSS: CSS,
      STR: STR,
      qtype: _qtype,
      types: getQuestionTypes(hasQtypeMultianswerrgx(_editor))
    });
  } else {
    contentText = Mustache.render(TEMPLATE.FORM, {
      CSS: CSS,
      STR: STR,
      SRC: {
        ADD: M.util.image_url('t/add', 'core'),
        DEL: M.util.image_url('t/delete', 'core'),
        UP: M.util.image_url('t/up', 'core'),
        DOWN: M.util.image_url('t/down', 'core'),
      },
      answerdata: _answerdata,
      elementid: getUuid(),
      qtype: _qtype,
      name: getQuestionTypes(hasQtypeMultianswerrgx(_editor)).filter(q => _qtype === q.type)[0].name,
      marks: _marks,
      numerical: (_qtype === 'NUMERICAL' || _qtype === 'NM')
    });
  }
  _modal.setBody(contentText);
  _modal.setFooter(footer);
  _modal.show();
  const $root = _modal.getRoot();
  _form = $root.get(0).querySelector('form');
  _toggleDeleteIcon();

  if (!nomodalevents) {
    _modal.registerEventListeners();
    _modal.registerCloseOnSave();
    _modal.registerCloseOnCancel();
    $root.on(ModalEvents.cancel, _cancel);

    if (!qtype) { // For the question list we need the choice handler only, and we are done.
      $root.on(ModalEvents.save, _choiceHandler);
      return;
    } // Handler to add the question string to the editor content.
    $root.on(ModalEvents.save, _setSubquestion);
  }
  // The form needs events for the icons to move up/down, add or delete a response.
  const getTarget = e => {
    let p = e.target;
    while (!isNull(p) && p.nodeType === 1 && p.tagName !== 'A') {
      p = p.parentNode;
    }
    if (isNull(p.classList)) {
      return null;
    }
    return p;
  };

  _form.addEventListener('click', e => {
    const p = getTarget(e);
    if (isNull(p)) {
      return;
    }
    if (p.classList.contains(CSS.DELETE)) {
      e.preventDefault();
      _deleteAnswer(p);
      return;
    }
    if (p.classList.contains(CSS.ADD)) {
      e.preventDefault();
      _addAnswer(p);
      return;
    }
    if (p.classList.contains(CSS.LOWER)) {
      e.preventDefault();
      _lowerAnswer(p);
      return;
    }
    if (p.classList.contains(CSS.RAISE)) {
      e.preventDefault();
      _raiseAnswer(p);
    }
  });
  _form.addEventListener('keyup', e => {
    const p = getTarget(e);
    if (isNull(p)) {
      return;
    }
    if (p.classList.contains(CSS.ANSWER) || p.classList.contains(CSS.FEEDBACK)) {
      e.preventDefault();
      _addAnswer(p);
    }
  });
  _form.querySelectorAll('.' + CSS.FRACTION).forEach((sel) => {
    sel.addEventListener('change', e => {
      const id = e.target.getAttribute('id');
      if (e.target.value === selectCustomPercent) {
        document.getElementById(id + '_custom').parentNode.classList.remove('hidden');
      } else {
        document.getElementById(id + '_custom').parentNode.classList.add('hidden');
      }
    });
  });
};

/**
 * If there is one answer field, hide the delete icon. Otherwise show them
 * all to allow deletion of any answer.
 *
 * @private
 */
const _toggleDeleteIcon = function() {
  const deleteIcons = _form.querySelectorAll('.' + CSS.DELETE);
  if (deleteIcons.length === 1) {
    deleteIcons[0].classList.add('hidden');
    return;
  }
  for (let i = 0; i < deleteIcons.length; i++) {
    deleteIcons[i].classList.remove('hidden');
  }
};

/**
 * Handle question choice.
 *
 * @method _choiceHandler
 * @private
 * @param {Event} e Event from button click in chooser
 */
const _choiceHandler = function(e) {
  e.preventDefault();
  let qtype = _form.querySelector('input[name=qtype]:checked');
  if (qtype) {
    _qtype = qtype.value;
  }
  // For numerical and short answer questions (and when installed regexp) we offer one response field only.
  // All other question types have three empty response fields.
  const max = (_qtype.indexOf('SHORTANSWER') !== -1 || _qtype === 'NUMERICAL' || _qtype.indexOf('REGEXP') !== -1) ? 1 : 3;
  const blankAnswer = {
    id: getUuid(),
    answer: '',
    feedback: '',
    fraction: 100,
    fractionOptions: getFractionOptions(''),
    tolerance: 0,
    isCustomGrade: false,
  };
  _answerdata = [];
  for (let x = 0; x < max; x++) {
    _answerdata.push({...blankAnswer, id: getUuid()});
  }
  // The first response field gets the default grade correct.
  _answerdata[0].fractionOptions = getFractionOptions('=');
  // In case the user seleced some text, this is used as the first answer.
  if (_firstAnswer) {
    _answerdata[0].answer = _firstAnswer;
  }
  _modal.destroy();
  // Our choice is stored in _qtype. We need to create the modal dialogue with the form now.
  _createModal().then(() => {
    _setDialogueContent(_qtype);
    _form.querySelector('.' + CSS.ANSWER).focus();
    return ''; // Make the linter happy.
  }).catch(() => {
      return '';
  });
};

/**
 * Parse question and set properties found.
 *
 * @method _parseSubquestion
 * @private
 * @param {String} question The question string
 */
const _parseSubquestion = function(question) {
  _answerdata = []; // Flush answers to have an empty dialogue if something goes wrong parsing the question string.
  const regexQtype = _getRegexQtype(_editor);
  const parts = regexQtype.exec(question);
  regexQtype.lastIndex = 0; // Reset lastIndex so that the next match starts from the beginning of the question string.
  if (!parts) {
    return;
  }
  _marks = parts[1];
  _qtype = parts[2];
  // Convert the short notation to the long form e.g. SA to SHORTANSWER.
  if (_qtype.length < 5) {
    getQuestionTypes(hasQtypeMultianswerrgx(_editor)).forEach(l => {
      for (const a of l.abbr) {
        if (a === _qtype) {
          _qtype = l.type;
          return;
        }
      }
    });
  }
  // Depending on the regex the position of the answers is different.
  const answers = parts[hasQtypeMultianswerrgx(_editor) ? 8 : 7].match(/(\\.|[^~])*/g);
  if (!answers) {
    return;
  }
  answers.forEach(function(answer) {
    const options = /^(%(-?[.0-9]+)%|(=?))((\\.|[^#])*)#?(.*)/.exec(answer);
    if (options && options[4]) {
      let frac = '';
      if (options[3]) {
        frac = options[3] === '=' ? '=' : 100;
      } else if (options[2]) {
        frac = options[2];
      }
      if (_qtype === 'NUMERICAL' || _qtype === 'NM') {
        const tolerance = /^([^:]*):?(.*)/.exec(options[4])[2] || 0;
        _answerdata.push({
          id: getUuid(),
          answer: strdecode(options[4].replace(/:.*/, '')),
          feedback: strdecode(options[6]),
          tolerance: tolerance,
          fraction: frac,
          fractionOptions: getFractionOptions(frac),
          isCustomGrade: isCustomGrade(frac),
        });
        return;
      }
      _answerdata.push({
        answer: strdecode(options[4]),
        id: getUuid(),
        feedback: strdecode(options[6]),
        fraction: frac,
        fractionOptions: getFractionOptions(frac),
        isCustomGrade: isCustomGrade(frac),
      });
    }
  });
};

/**
 * Insert a new set of answer blanks below the button.
 *
 * @method _addAnswer
 * @param {Node} a Node that is the referred element
 * @private
 */
const _addAnswer = function(a) {
  let index = indexOfNode(_form.querySelectorAll('.' + CSS.ADD), a);
  if (index === -1) {
    index = 0;
  }
  let fraction = '';
  let answer = '';
  let feedback = '';
  let tolerance = 0;
  if (a.closest('li')) {
    fraction = a.closest('li').querySelector('.' + CSS.FRACTION).value;
    if (fraction === selectCustomPercent) {
      fraction = a.closest('li').querySelector('.' + CSS.FRAC_CUSTOM).value;
    }
    answer = a.closest('li').querySelector('.' + CSS.ANSWER).value;
    feedback = a.closest('li').querySelector('.' + CSS.FEEDBACK).value;
    if (a.closest('li').querySelector('.' + CSS.TOLERANCE)) {
      tolerance = a.closest('li').querySelector('.' + CSS.TOLERANCE).value;
    }
  }
  _processFormData();
  _answerdata.splice(index, 0, {
    id: getUuid(),
    answer: answer,
    feedback: feedback,
    fraction: fraction,
    fractionOptions: getFractionOptions(fraction),
    tolerance: tolerance,
    isCustomGrade: isCustomGrade(fraction)
  });
  _setDialogueContent(_qtype, true);
  _toggleDeleteIcon();
  _form.querySelectorAll('.' + CSS.ANSWER).item(index).focus();
};

/**
 * Delete set of answer next to the button.
 *
 * @method _deleteAnswer
 * @param {Node} a Node that is the referred element
 * @private
 */
const _deleteAnswer = function(a) {
  let index = indexOfNode(_form.querySelectorAll('.' + CSS.DELETE), a);
  if (index === -1) {
    index = indexOfNode(_form.querySelectorAll('li'), a.closest('li'));
  }
  _processFormData();
  _answerdata.splice(index, 1);
  _setDialogueContent(_qtype, true);
  const answers = _form.querySelectorAll('.' + CSS.ANSWER);
  index = Math.min(index, answers.length - 1);
  answers.item(index).focus();
  _toggleDeleteIcon();
};

/**
 * Lower answer option
 *
 * @method _lowerAnswer
 * @param {Node} a Node that is the referred element
 * @private
 */
const _lowerAnswer = function(a) {
  const li = a.closest('li');
  li.before(li.nextSibling);
  li.querySelector('.' + CSS.ANSWER).focus();
};

/**
 * Raise answer option
 *
 * @method _raiseAnswer
 * @param {Node} a Node that is the referred element
 * @private
 */
const _raiseAnswer = function(a) {
  const li = a.closest('li');
  li.after(li.previousSibling);
  li.querySelector('.' + CSS.ANSWER).focus();
};

/**
 * Reset and hide form.
 *
 * @method _cancel
 * @param {Event} e Event from button click
 * @private
 */
const _cancel = function(e) {
  e.preventDefault();
  // In case there is a marker where the new question should be inserted in the text it needs to be removed.
  for (const span of _editor.dom.select('.' + markerClass + '.new')) {
    span.remove();
  }
  _modal.destroy();
  _editor.focus();
  _modal = null;
};

/**
 * Insert question string into editor content and reset and hide form. If the form contains an error
 * nothing happens.
 *
 * @method _setSubquestion
 * @param {Event} e Event from button click
 * @private
 */
const _setSubquestion = function(e) {
  e.preventDefault();
  // Check if there are any errors and if so, fill the error container with the
  // messages and return without going any further and closing the dialogue.
  const errMsg = _form.querySelector('.msg-error');
  const formErrors = _processFormData(true);
  if (formErrors.length > 0) {
    errMsg.innerHTML = '<ul><li>' + formErrors.join('</li><li>') + '</li></ul>';
    errMsg.classList.remove('hidden');
    return;
  } else {
    errMsg.classList.add('hidden');
  }
  // Build the parser function from the data, that is going to be placed into the editor content.
  let question = '{' + _marks + ':' + _qtype + ':';

  // Filter all empty responses
  for (let i = 0; i < _answerdata.length; i++) {
    if (_answerdata[i].raw === '') {
      continue;
    }
    question += _answerdata[i].fraction && !isNaN(_answerdata[i].fraction)
      ? '%' + _answerdata[i].fraction + '%' : _answerdata[i].fraction;
    question += strencode(_answerdata[i].answer);
    if (_qtype === 'NM' || _qtype === 'NUMERICAL') {
      question += ':' + _answerdata[i].tolerance;
    }
    if (_answerdata[i].feedback) {
      question += '#' + strencode(_answerdata[i].feedback);
    }
    if (i < _answerdata.length - 1) {
      question += '~';
    }
  }
  if (question.slice(-1) === '~') {
    question = question.substring(0, question.length - 1);
  }
  question += '}';
  // eslint-disable-next-line no-bitwise
  if (_selectedPrefixAndSuffix & 1) {
    question = ' ' + question;
  }
  // eslint-disable-next-line no-bitwise
  if (_selectedPrefixAndSuffix & 2) {
    question += ' ';
  }

  _modal.destroy();
  _modal = null;
  _editor.focus();
  if (_selectedOffset > -1) { // We have to replace one of the marker spans (the innerHTML contains the question string).
    _editor.dom.select('.' + markerClass)[_selectedOffset].innerHTML = question;
  } else {
    // Just add the question text with markup.
    _editor.insertContent(markerSpan + question + '</span>');
  }
};

/**
 * Read the form data, process it and store the result in the internal _answerdata array.
 * Also, if validation is enabled, the fields are checked for invalid values e.g.
 * - answer field is empty (if a correct answer is contained, empty fields are eliminated).
 * - custom_grade field whenin use and does not contain a number.
 * - no field is marked as a correct answer.
 * - tolerance field must be in percentage of min -100 and max 100.
 * Any field with an error is maked and the first field containing an error gets the focus.
 *
 * @method _processFormData
 * @param {boolean} validate
 * @return {Array}
 * @private
 */
const _processFormData = function(validate) {
  _answerdata = [];
  let globalErrors = [];
  const answers = _form.querySelectorAll('.' + CSS.ANSWER);
  const feedbacks = _form.querySelectorAll('.' + CSS.FEEDBACK);
  const fractions = _form.querySelectorAll('.' + CSS.FRACTION);
  const customGrades = _form.querySelectorAll('.' + CSS.FRAC_CUSTOM);
  const tolerances = _form.querySelectorAll('.' + CSS.TOLERANCE);
  // Remove any error classes.
  for (let i = 0; i < answers.length; i++) {
    answers.item(i).classList.remove('error');
    customGrades.item(i).classList.remove('error');
    const currentAnswer = {
      raw: answers.item(i).value.trim(),
      answer: answers.item(i).value.trim(),
      id: getUuid(),
      feedback: feedbacks.item(i).value,
      fraction: fractions.item(i).value === selectCustomPercent ? customGrades.item(i).value : fractions.item(i).value,
      fractionOptions: getFractionOptions(fractions.item(i).value),
      tolerance: tolerances.length > 0 ? tolerances.item(i).value : 0,
      isCustomGrade: fractions.item(i).value === selectCustomPercent
    };
    if (_qtype === 'NM' || _qtype === 'NUMERICAL') {
      tolerances.item(i).classList.remove('error');
      // In numeric questions convert answer and tolerance to numeric values (this filters non numeric values).
      currentAnswer.answer = Number(currentAnswer.answer);
      currentAnswer.tolerance = Number(currentAnswer.tolerance);
    }
    _answerdata.push(currentAnswer);
  }
  _marks = _form.querySelector('.' + CSS.MARKS).value;

  if (validate) {
    const {hasCorrectAnswer, errors} = _validateAnswers();
    for (let i = 0; i < _answerdata.length; i++) {
      for (const err of _answerdata[i].hasErrors) {
        // Automatically remove empty answer fields for convenience if there is at least one correct answer.
        if (hasCorrectAnswer && (err === 'empty_answer' || err === 'correct_but_empty')) {
          break;
        }
        if (err === 'answer_not_numeric' || err === 'empty_answer'
          || err === 'correct_but_empty' || err === 'answer_invalid_chars'
          || err === 'answer_odd_bracket_count'
        ) {
          answers.item(i).classList.add('error');
        } else if (err === 'tolerance_not_numeric') {
          tolerances.item(i).classList.add('error');
        } else if (err === 'error_custom_rate') {
          customGrades.item(i).classList.add('error');
        }
      }
    }
    globalErrors = _translateGlobalErrors(hasCorrectAnswer, errors);
    // If we have errors, we focus the first field that contains an error.
    if (globalErrors.length > 0) {
      _form.querySelector('input.error').focus();
    }
  }
  return globalErrors;
};

/**
 * Validates the answer array. Checks for each question if the data from the form is
 * incomplete or has other errors. These are flagged accordingly in the array element.
 * The retruned object contains the properties:
 * - hasCorrectAnswer {boolean} is true if there is at least one correct answer.
 * - errors {Array} list of strings that contain an error code that is globaly used for error messages.
 *
 * @return {Array}
 * @private
 */
const _validateAnswers = function() {
  let errors = [];
  let hasCorrect = false;
  for (let i = 0; i < _answerdata.length; i++) {
    _answerdata[i].hasErrors = [];
    // Check if we have an empty answer string.
    if (_answerdata[i].raw === '') {
      _answerdata[i].hasErrors.push('empty_answer');
    }
    // We found a correct answer, when grade is marked as 100 or "=" and the answer is not empty.
    if (_answerdata[i].fraction === '100' || _answerdata[i].fraction === '=') {
      if (_answerdata[i].raw !== '') {
        _answerdata[i].isCorrect = true;
        hasCorrect = true;
      } else {
        _answerdata[i].hasErrors.push('correct_but_empty');
      }
    }
    // Check the custom grade, that must be a percentage number between -100 and 100.
    if (_answerdata[i].isCustomGrade &&
      (isNaN(_answerdata[i].fraction) || _answerdata[i].fraction < -100 || _answerdata[i].fraction > 100
        || _answerdata[i].fraction.trim() === '')
    ) {
      _answerdata[i].hasErrors.push('error_custom_rate');
    }
    // Special checks for a certain type.
    if (_qtype === 'NM' || _qtype === 'NUMERICAL') {
      _validateAnswersNumeric(i);
    }
    if (_qtype === 'REGEXP' || _qtype === 'REGEXP_C') {
      _validateAnswersRegexp(i);
    }

    errors = errors.concat(_answerdata[i].hasErrors);
  }

  return {
    hasCorrectAnswer: hasCorrect,
    errors: _combineGlobalErrors(hasCorrect, errors),
  };
};

/**
 * Validate numeric answers.
 * This type has an additional field tolerance that must be a number. Also
 * the answer itself must be numeric.
 * @param {int} i
 */
const _validateAnswersNumeric = function(i) {
  if (isNaN(_answerdata[i].answer) && _answerdata[i].raw !== '') {
    _answerdata[i].hasErrors.push('answer_not_numeric');
  }
  if (isNaN(_answerdata[i].tolerance)) {
    _answerdata[i].hasErrors.push('tolerance_not_numeric');
  }
};

/**
 * Validate regex answers.
 * Full documentation: https://docs.moodle.org/500/en/Regular_Expression_Short-Answer_question_type
 * @param {int} i
 */
const _validateAnswersRegexp = function(i) {
  // If the answer is somehat correct (positive grade), then the regex can use
  // the . ^ $ * + { } \ / as literals only (preceeded by a backslash).
  if ((_answerdata[i].isCorrect || _answerdata[i].isCustomGrade && _answerdata[i].fraction > 0) &&
    hasInvalidChars(_answerdata[i].raw)
  ) {
    _answerdata[i].hasErrors.push('answer_invalid_chars');
  }
  // Check that any used braket that is not used as a literal, has
  // as many opening as well as closing brakets.
  if (hasOddBracketCount(_answerdata[i].raw)) {
    _answerdata[i].hasErrors.push('answer_odd_bracket_count');
  }
};

/**
 * Translate the errors into a readable string for a list that is used on top of the
 * input fields, to indicate what part of the data is incorrect.
 *
 * @param {Boolean} hasCorrectAnswer
 * @param {Array} errors
 * @return {Array}
 * @private
 */
const _translateGlobalErrors = function(hasCorrectAnswer, errors) {
  const errTranslated = [];
  // Translate the error strings into a string that can be displayed in the form.
  const trMsg = {
    emptyanswer: STR.err_empty_answer,
    answernotnumeric: STR.err_not_numeric,
    tolerancenotnumeric: STR.err_not_numeric,
    errorcustomrate: STR.err_custom_rate,
    nonecorrect: STR.err_none_correct,
    answerinvalidchars: STR.err_invalid_chars,
    answeroddbracketcount: STR.err_invalid_brackets,
  };
  for (const err of errors) {
    // If there's at least one correct answer, we filter out all empty answers and therefore do not
    // show the error message.
    if (hasCorrectAnswer && err === 'empty_answer' || err === 'correct_but_empty') {
      continue;
    }
    // Remove underscore (we do this only because of the js linter).
    const key = err.replace(/_/g, '');
    errTranslated.push(trMsg[key]);
  }
  return errTranslated;
};

/**
 * Combine the error list from the answers to a global list.
 *
 * @param {Boolean} hasCorrectAnswer
 * @param {Array} errors
 * @return {Array}
 * @private
 */
const _combineGlobalErrors = function(hasCorrectAnswer, errors) {
  // Unique errors for the global error list.
  const errUnique = errors.filter((value, index, array) => array.indexOf(value) === index);
  // If we have a correct answer, do not show the empty answer error, because empty responses are filtered.
  if (hasCorrectAnswer) {
    const i = errUnique.indexOf('empty_answer');
    if (i > -1) {
      errUnique.splice(i, 1);
    }
  } else if (!errUnique.includes('correct_but_empty')) {
    errUnique.push('none_correct');
  }
  return errUnique;
};

/**
 * Check whether cursor is in a subquestion and return subquestion text if
 * true.
 *
 * @method resolveSubquestion
 * @param {Node|null} element The element to check if it is a subquestion.
 * @return {Mixed} The selected node of with the subquestion if found, false otherwise.
 */
const resolveSubquestion = function(element) {
  let span = element || _editor.selection.getStart();
  if (!isNull(span.classList) && span.classList.contains(markerClass)) {
    return span;
  }
  _editor.dom.getParents(span, elm => {
    // Are we in a span that encapsulates the cloze question?
    if (!isNull(elm.classList) && elm.classList.contains(markerClass)) {
      return elm;
    }
    return false;
  });
  return false;
};

export {
  displayDialogue,
  displayDialogueForEdit,
  resolveSubquestion,
  onInit,
  onBeforeGetContent,
  onSubmit,
};
