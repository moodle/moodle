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
