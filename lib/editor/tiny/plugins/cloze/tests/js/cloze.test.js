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
 * Test suite for some of the js files of the tiny_cloze plugin for Moodle.
 *
 * @module      tiny_cloze
 * @copyright   2025 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as assert from 'assert';
import * as jsdom from 'jsdom';
import * as cloze from './src/cloze.mjs';


describe('Test function hasClass()', function () {
  describe('create <i class="tiny_cloze_add">+</i>', function () {
    const dom = new jsdom.JSDOM(`<!DOCTYPE html><i class="tiny_cloze_add">+</i>`);
    const n = dom.window.document.querySelector('i');
    it('Check for existing class property ADD of CSS object.', function () {
      assert.equal(cloze.hasClass(n, 'ADD'), true);
    });
    it('Check for missing class property DELETE of CSS object.', function () {
      assert.equal(cloze.hasClass(n, 'DELETE'), false);
    });
  });
});

describe('Test function indexOfNode()', function () {
  describe('create <i>1</i><i>2</i><i>3</i>', function () {
    const dom = new jsdom.JSDOM(`<!DOCTYPE html><i>1</i><i>2</i><i>3</i>`);
    const list = Array.from(dom.window.document.querySelectorAll('i'));
    it('Check for index of <i>2</i>.', function () {
      assert.equal(cloze.indexOfNode(list, list[1]), 1);
    });
    it('Check for index of newly created node.', function () {
      assert.equal(cloze.indexOfNode(list, dom.window.document.createElement('i')), -1);
    });
  });
});

describe('Test function getUuid()', function () {
  const len = (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function')
    ? 36 : 14;
  it(`Check for length ${len}.`, function () {
    assert.equal(cloze.getUuid().length, len);
  });
  it('Check for pattern.', function () {
    if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
      assert.match(cloze.getUuid(), /^[0-9a-f]{8}\-([0-9a-f]{4}\-){3}[0-9a-f]{12}$/);
    } else {
      assert.match(cloze.getUuid(), /^ed-cloze-\d{5}$/);
    }
  });
});

describe('Test function getQuestionTypes() without regex', function () {
  const questions = cloze.getQuestionTypes();
  it('Check for # of questions', function () {
    assert.equal(questions.length, 13);
  });
  if ('Check that sequence is correct', function () {
    assert.equal(questions[2].type, 'MULTICHOICE_V');
    assert.equal(questions[7].type, 'MULTIRESPONSE_H');
    assert.equal(questions[10].type, 'NUMERICAL');
    assert.equal(questions[12].type, 'SHORTANSWER_C');
  });
});

describe('Test function getQuestionTypes() with regex', function () {
  const questions = cloze.getQuestionTypes(true);
  it('Check for # of questions', function () {
    assert.equal(questions.length, 15);
  });
  if ('Check that sequence is correct', function () {
    assert.equal(questions[2].type, 'MULTICHOICE_V');
    assert.equal(questions[7].type, 'MULTIRESPONSE_H');
    assert.equal(questions[10].type, 'NUMERICAL');
    assert.equal(questions[12].type, 'REGEXP_C');
    assert.equal(questions[14].type, 'SHORTANSWER_C');
  });
});

describe('Test function isCustomGrade()', function () {
  it('Test not custom grades 100, =, 50, 0', function () {
    assert.equal(cloze.isCustomGrade('100'), false);
    assert.equal(cloze.isCustomGrade('='), false);
    assert.equal(cloze.isCustomGrade('50'), false);
    assert.equal(cloze.isCustomGrade('0'), false);
    assert.equal(cloze.isCustomGrade(''), false);
  });
  if ('Test custom grades 80, 60, 40, 33', function () {
    assert.equal(cloze.isCustomGrade('80'), true);
    assert.equal(cloze.isCustomGrade('60'), true);
    assert.equal(cloze.isCustomGrade('40'), true);
    assert.equal(cloze.isCustomGrade('33'), true);
  });
});

describe('Test function hasInvalidChars()', function () {
  it('Test valid regex answer strings.', function () {
    assert.equal(cloze.hasInvalidChars('100\\.FF\\.'), false);
    assert.equal(cloze.hasInvalidChars('\\{cf\\}'), false);
    assert.equal(cloze.hasInvalidChars('(1|2)?9\\$'), false);
    assert.equal(cloze.hasInvalidChars('a\\+b\\+c'), false);
    assert.equal(cloze.hasInvalidChars(''), false);
    assert.equal(cloze.hasInvalidChars('[bcr]at'), false);
    assert.equal(cloze.hasInvalidChars('__one__,__two__'), false);
    assert.equal(cloze.hasInvalidChars('path\\/to\\/file'), false);
  });
  it('Test invalid regex answers strings.', function () {
    assert.equal(cloze.hasInvalidChars('2.000,00'), true);
    assert.equal(cloze.hasInvalidChars('{cf}'), true);
    assert.equal(cloze.hasInvalidChars('a + b'), true);
    assert.equal(cloze.hasInvalidChars('a*b'), true);
    assert.equal(cloze.hasInvalidChars('a\\sb'), true);
    assert.equal(cloze.hasInvalidChars('bin/bash'), true);
  });
});

describe('Test function hasOddBracketCount()', function () {
  it('Test valid bracket count in answer strings.', function () {
    assert.equal(cloze.hasOddBracketCount('100\\(FF\\.'), false);
    assert.equal(cloze.hasOddBracketCount('\\{cf\\}'), false);
    assert.equal(cloze.hasOddBracketCount('(1|2){3,9}\\$'), false);
    assert.equal(cloze.hasOddBracketCount('[uvx]{2,}'), false);
    assert.equal(cloze.hasOddBracketCount(''), false);
    assert.equal(cloze.hasOddBracketCount('[bcr]at'), false);
    assert.equal(cloze.hasOddBracketCount('\\[text\\]'), false);
  });
  it('Test invalid bracket count in answers strings.', function () {
    assert.equal(cloze.hasOddBracketCount('2.{00(fc)?00'), true);
    assert.equal(cloze.hasOddBracketCount('cf}'), true);
    assert.equal(cloze.hasOddBracketCount('[abc]2)b'), true);
    assert.equal(cloze.hasOddBracketCount('[[[())]]]'), true);
    assert.equal(cloze.hasOddBracketCount('{([)]}'), true);
  });
});