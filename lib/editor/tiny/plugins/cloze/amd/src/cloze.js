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
 * Helper functions for the tiny_cloze plugin for Moodle.
 *
 * @module      tiny_cloze
 * @copyright   2025 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const isNull = a => typeof a === 'undefined' || a === null;
export const strdecode = t => String(t).replace(/\\(#|\}|~)/g, '$1');
export const strencode = t => String(t).replace(/(#|\}|~)/g, '\\$1');

/**
 * Check at which position a given node is in the list.
 *
 * @param {Array} list
 * @param {Node} node
 * @returns {Number}
 */
export const indexOfNode = (list, node) => {
  for (let i = 0; i < list.length; i++) {
    if (list[i] === node) {
      return i;
    }
  }
  return -1;
};

/**
 * Checks for some chars "[.^$*+{}\/]", that these are escaped by a backslash. If that's not
 * the case true is returned.
 * @param {string} text
 * @returns {boolean}
 */
export const hasInvalidChars = (text) => {
  // Remove pattern like \. or \$
  const regex = /\\[.^$*+{}\\/]/g;
  text = text.replace(regex, '');
  for (const c of '.^$*+{}\\/'.split('')) {
    // If the special char is not still in the string, it was not escaped by \
    if (text.indexOf(c) > -1) {
      return true;
    }
  }
  return false;
};

/**
 * Counts the ocurrences of opening and closing brackets and returns true when they mismatch.
 * The function also checks that there are no overlapping sequences such as ([)].
 * @param {string} text
 * @returns {boolean}
 */
export const hasOddBracketCount = (text) => {
  let stack = [];

  for (let i = 0; i < text.length; i++) {
    const char = text[i];
    const isEscaped = i > 0 && text[i - 1] === '\\';

    if (!isEscaped) {
      if (char === '(' || char === '[' || char === '{') {
        stack.push(char);
      } else if (char === ')' || char === ']' || char === '}') {
        if (stack.length === 0) {
          return true;
        }
        let open = stack.pop();
        if (
          open === '(' && char !== ')' ||
          open === '[' && char !== ']' ||
          open === '{' && char !== '}'
        ) {
          return true;
        }
      }
    }
  }
  return stack.length > 0;
};

/**
 * Get a unique identifier, used for every response field.
 *
 * @returns {string}
 */
export const getUuid = function() {
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID();
  }
  return 'ed-cloze-' + Math.floor(Math.random() * 100000).toString().padStart(5, '0');
};

// Grade Selector value when custom percentage is selected.
export const selectCustomPercent = '__custom__';

/**
 * Helper function to return the options html for the fraction select element.
 * The options are incorrect, correct, 100%, 50%, 0% and custom. The submitted
 * value will be selected.
 *
 * @param {string} s
 * @returns {string}
 */
export const getFractionOptions = s => {
  const attrSel = ' selected="selected"';
  let isSel = s === '=' ? attrSel : '';
  let html = `<option value="">${STR.incorrect}</option><option value="="${isSel}>${STR.correct}</option>`;
  FRACTIONS.forEach(item => {
    isSel = item.value.toString() === s ? attrSel : '';
    html += `<option value="${item.value}"${isSel}>${item.value}%</option>`;
  });
  isSel = s !== '' && html.indexOf(attrSel) === -1 ? attrSel : '';
  html += `<option value="${selectCustomPercent}"${isSel}>${STR.custom_grade}</option>`;
  return html;
};

/**
 * Check if the value is a custom grade value (in order to show the input field).
 *
 * @param {string} s
 * @returns {boolean}
 */
export const isCustomGrade = s => {
  if (s === '=' || s === '') {
    return false;
  }
  let found = false;
  FRACTIONS.forEach(item => {
    if (item.value.toString() === s) {
      found = true;
    }
  });
  return !found;
};

// Marker class and the whole span element that is used to encapsulate the cloze question text.
export const markerClass = 'cloze-question-marker';
export const markerSpan = '<span contenteditable="false" class="' + markerClass + '" data-mce-contenteditable="false">';

// CSS classes that are used in the modal dialogue.
export const CSS = {
  ANSWER: 'tiny_cloze_answer',
  ANSWERS: 'tiny_cloze_answers',
  ADD: 'tiny_cloze_add',
  CANCEL: 'tiny_cloze_cancel',
  DELETE: 'tiny_cloze_delete',
  FEEDBACK: 'tiny_cloze_feedback',
  FRACTION: 'tiny_cloze_fraction',
  FRAC_CUSTOM: 'tiny_cloze_frac_custom',
  LEFT: 'tiny_cloze_col0',
  LOWER: 'tiny_cloze_down',
  RIGHT: 'tiny_cloze_col1',
  MARKS: 'tiny_cloze_marks',
  DUPLICATE: 'tiny_cloze_duplicate',
  RAISE: 'tiny_cloze_up',
  SUBMIT: 'tiny_cloze_submit',
  SUMMARY: 'tiny_cloze_summary',
  TOLERANCE: 'tiny_cloze_tolerance',
  TYPE: 'tiny_cloze_qtype'
};

// Templates for the modal dialogue content.
export const TEMPLATE = {
  FORM: '<div class="tiny_cloze">' +
    '<p>{{name}} ({{qtype}})</p>' +
    '<form name="tiny_cloze_form">' +
    '<div class="ml-0 form-group">' +
    '<label for="{{elementid}}_mark">{{STR.defaultmark}}</label>' +
    '<input id="{{elementid}}_mark" type="text" value="{{marks}}" ' +
    'class="{{CSS.MARKS}} form-control d-inline mx-2" />' +
    '<a class="{{CSS.ADD}}" title="{{STR.addmoreanswerblanks}}">' +
    '<img class="icon_smallicon" src="{{SRC.ADD}}" alt="{{STR.addmoreanswerblanks}}"></a>' +
    '</div>' +
    '<div class="msg-error hidden"></div>' +
    '<div class="{{CSS.ANSWERS}} mb-3">' +
    '<ol class="pl-3">{{#answerdata}}' +
    '<li class="mt-3"><div class="row form-group">' +
    '<div class="col-2"><label for="{{id}}_answer">{{STR.answer}}</label></div>' +
    '<div class="col-8"><input id="{{id}}_answer" type="text" value="{{answer}}" ' +
    'class="{{CSS.ANSWER}} form-control d-inline mx-2" /></div>' +
    '<div class="col-2">' +
    '<a class="{{CSS.ADD}}" title="{{STR.addmoreanswerblanks}}">' +
    '<img class="icon_smallicon" src="{{SRC.ADD}}" alt="{{STR.addmoreanswerblanks}}"></a>' +
    '<a class="{{CSS.DELETE}}" title="{{STR.delete}}">' +
    '<img class="icon_smallicon" src="{{SRC.DEL}}" alt="{{STR.delete}}"></a>' +
    '<a class="{{CSS.RAISE}}" title="{{STR.up}}">' +
    '<img class="icon_smallicon" src="{{SRC.UP}}" alt="{{STR.up}}"></a>' +
    '<a class="{{CSS.LOWER}}" title="{{STR.down}}">' +
    '<img class="icon_smallicon" src="{{SRC.DOWN}}" alt="{{STR.down}}"></a>' +
    '</div>' +
    '</div>' +
    '{{#numerical}}' +
    '<div class="row form-group">' +
    '<div class="col-2">' +
    '<label for="{{id}}_tolerance">{{{STR.tolerance}}}</label>' +
    '</div><div class="col-8">' +
    '<input id="{{id}}_tolerance" type="text" value="{{tolerance}}" ' +
    'class="{{CSS.TOLERANCE}} form-control d-inline mx-2" />' +
    '</div>' +
    '</div>' +
    '{{/numerical}}' +
    '<div class="row form-group">' +
    '<div class="col-2">' +
    '<label for="{{id}}_feedback">{{STR.feedback}}</label>' +
    '</div><div class="col-8">' +
    '<input id="{{id}}_feedback" type="text" value="{{feedback}}" ' +
    'class="{{CSS.FEEDBACK}} form-control d-inline mx-2" />' +
    '</div></div>' +
    '<div class="row form-group">' +
    '<div class="col-2">' +
    '<label id="{{id}}_grade">{{STR.grade}}</label>' +
    '</div><div class="col-8">' +
    '<select id="{{id}}_grade" class="{{CSS.FRACTION}} custom-select mx-2">' +
    '{{{fractionOptions}}}' +
    '</select>' +
    '<span class="{{^isCustomGrade}} hidden{{/isCustomGrade}}">' +
    '<input id="{{id}}_grade_custom" type="text"{{#isCustomGrade}} value="{{fraction}}"{{/isCustomGrade}} ' +
    'class="{{CSS.FRAC_CUSTOM}} form-control d-inline mx-2" style="width: 4rem;" />%' +
    '</span></div>' +
    '</div></li>' +
    '{{/answerdata}}</ol></div>' +
    '</form>' +
    '</div>',
  TYPE: '<div class="tiny_cloze mt-0 mx-2 mb-2">' +
    '<p>{{STR.chooseqtypetoadd}}</p>' +
    '<form name="tiny_cloze_form">' +
    '<div class="{{CSS.TYPE}} form-check">' +
    '{{#types}}' +
    '<div class="option">' +
    '<input name="qtype" id="qtype_qtype_{{type}}" value="{{type}}" type="radio" class="form-check-input">' +
    '<label for="qtype_qtype_{{type}}">' +
    '<span class="typename">{{type}}</span>' +
    '<span class="{{CSS.SUMMARY}}"><h6>{{name}}</h6><p>{{summary}}</p>' +
    '<ul>{{#options}}' +
    '<li>{{.}}</li>' +
    '{{/options}}</ul>' +
    '</span>' +
    '</label></div>' +
    '{{/types}}</div>' +
    '</form></div>',
  FOOTER: '<button type="button" class="btn btn-secondary" data-action="cancel">{{cancel}}</button>' +
    '<button type="button" class="btn btn-primary" data-action="save">{{submit}}</button>',
};

// Values to indicate whether a response is correct or not. These appear in the selection.
const FRACTIONS = [
  {value: 100},
  {value: 50},
  {value: 0},
];

// Language strings used in the modal dialogue.
const STR = {};

/**
 * Return the question types that are available for the cloze question.
 *
 * @param {boolean} withMultianswerrgx Whether to include the regular expression question types.
 * @returns {Array}
 */
export const getQuestionTypes = function(withMultianswerrgx) {
  let qtypes = [
    {
      'type': 'MULTICHOICE',
      'abbr': ['MC'],
      'name': STR.multichoice,
      'summary': STR.summary_multichoice,
      'options': [STR.selectinline, STR.singleyes],
    },
    {
      'type': 'MULTICHOICE_H',
      'abbr': ['MCH'],
      'name': STR.multichoice,
      'summary': STR.summary_multichoice,
      'options': [STR.horizontal, STR.singleyes],
    },
    {
      'type': 'MULTICHOICE_V',
      'abbr': ['MCV'],
      'name': STR.multichoice,
      'summary': STR.summary_multichoice,
      'options': [STR.vertical, STR.singleyes],
    },
    {
      'type': 'MULTICHOICE_S',
      'abbr': ['MCS'],
      'name': STR.multichoice,
      'summary': STR.summary_multichoice,
      'options': [STR.selectinline, STR.shuffle, STR.singleyes],
    },
    {
      'type': 'MULTICHOICE_HS',
      'abbr': ['MCHS'],
      'name': STR.multichoice,
      'summary': STR.summary_multichoice,
      'options': [STR.horizontal, STR.shuffle, STR.singleyes],
    },
    {
      'type': 'MULTICHOICE_VS',
      'abbr': ['MCVS'],
      'name': STR.multichoice,
      'summary': STR.summary_multichoice,
      'options': [STR.vertical, STR.shuffle, STR.singleyes],
    },
    {
      'type': 'MULTIRESPONSE',
      'abbr': ['MR'],
      'name': STR.multiresponse,
      'summary': STR.summary_multichoice,
      'options': [STR.multi_vertical, STR.singleno],
    },
    {
      'type': 'MULTIRESPONSE_H',
      'abbr': ['MRH'],
      'name': STR.multiresponse,
      'summary': STR.summary_multichoice,
      'options': [STR.multi_horizontal, STR.singleno],
    },
    {
      'type': 'MULTIRESPONSE_S',
      'abbr': ['MRS'],
      'name': STR.multiresponse,
      'summary': STR.summary_multichoice,
      'options': [STR.multi_vertical, STR.shuffle, STR.singleno],
    },
    {
      'type': 'MULTIRESPONSE_HS',
      'abbr': ['MRHS'],
      'name': STR.multiresponse,
      'summary': STR.summary_multichoice,
      'options': [STR.multi_horizontal, STR.shuffle, STR.singleno],
    },
    {
      'type': 'NUMERICAL',
      'abbr': ['NM'],
      'name': STR.numerical,
      'summary': STR.summary_numerical,
    },
    {
      'type': 'SHORTANSWER',
      'abbr': ['SA', 'MW'],
      'name': STR.shortanswer,
      'summary': STR.summary_shortanswer,
      'options': [STR.caseno],
    },
    {
      'type': 'SHORTANSWER_C',
      'abbr': ['SAC', 'MWC'],
      'name': STR.shortanswer,
      'summary': STR.summary_shortanswer,
      'options': [STR.caseyes],
    },
  ];
  if (withMultianswerrgx) {
    qtypes.splice(11, 0, {
      'type': 'REGEXP',
      'abbr': ['RX'],
      'name': STR.regexp,
      'summary': STR.summary_regexp,
      'options': [STR.caseno],
    }, {
      'type': 'REGEXP_C',
      'abbr': ['RXC'],
      'name': STR.regexp,
      'summary': STR.summary_regexp,
      'options': [STR.caseyes],
    });
  }
  return qtypes;
};

/**
 * Return the CSS object (containing class names for the different icons and elements).
 *
 * @returns {object}
 */
export const getCss = function() {
  return CSS;
};

/**
 * Check if the node has the given class derived from the CSS object.
 *
 * @param {Node} node
 * @param {string} className
 * @returns {boolean}
 */
export const hasClass = function(node, className) {
  return node.classList.contains(CSS[className]);
};

/**
 * Get query selector for the given class name from the CSS object.
 *
 * @param {string} className
 * @returns {string}
 */
export const queryClass = function(className) {
  return `.${CSS[className]}`;
};

/**
 * Set string value in STR object.
 *
 * @param {string} key
 * @param {string} val
 */
export const setStr = function(key, val) {
  STR[key] = val;
};

/**
 * Get value from STR object or return the key, if not found.
 * If key is '*', return the whole STR object.
 *
 * @param {string} key
 * @returns {string|object}
 */
export const getStr = function(key) {
  if (key === '*') {
    return STR;
  }
  return STR[key] ?? key;
};
