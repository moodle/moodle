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

const isNull = a => typeof a === 'undefined' || a === null;
const strdecode = t => String(t).replace(/\\(#|\}|~)/g, '$1');
const strencode = t => String(t).replace(/(#|\}|~)/g, '\\$1');

/**
 * Check at which position a given node is in the list.
 *
 * @param {Array} list
 * @param {Node} node
 * @returns {Number}
 */
const indexOfNode = (list, node) => {
  for (let i = 0; i < list.length; i++) {
    if (list[i] === node) {
      return i;
    }
  }
  return -1;
};

/**
 * Get a unique identifier, used for every response field.
 *
 * @returns {string}
 */
const getUuid = function() {
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID();
  }
  return 'ed-cloze-' + Math.floor(Math.random() * 100000).toString().padStart(5, '0');
};

// Grade Selector value when custom percentage is selected.
const selectCustomPercent = '__custom__';

/**
 * Helper function to return the options html for the fraction select element.
 * The options are incorrect, correct, 100%, 50%, 0% and custom. The submitted
 * value will be selected.
 *
 * @param {string} s
 * @returns {string}
 */
const getFractionOptions = s => {
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
const isCustomGrade = s => {
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

// CSS classes that are used in the modal dialogue.
const CSS = {
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
const getQuestionTypes = function(withMultianswerrgx) {
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
const getCss = function() {
  return CSS;
};

/**
 * Check if the node has the given class derived from the CSS object.
 *
 * @param {Node} node
 * @param {string} className
 * @returns {boolean}
 */
const hasClass = function(node, className) {
  return node.classList.contains(CSS[className]);
};

/**
 * Get query selector for the given class name from the CSS object.
 *
 * @param {string} className
 * @returns {string}
 */
const queryClass = function(className) {
  return `.${CSS[className]}`;
};

/**
 * Set string value in STR object.
 *
 * @param {string} key
 * @param {string} val
 */
const setStr = function(key, val) {
  STR[key] = val;
};

/**
 * Get value from STR object or return the key, if not found.
 * If key is '*', return the whole STR object.
 *
 * @param {string} key
 * @returns {string|object}
 */
const getStr = function(key) {
  if (key === '*') {
    return STR;
  }
  return STR[key] ?? key;
};

export {
  isNull,
  isCustomGrade,
  indexOfNode,
  getCss,
  hasClass,
  queryClass,
  getUuid,
  getFractionOptions,
  selectCustomPercent,
  strdecode,
  strencode,
  getQuestionTypes,
  getStr,
  setStr
};