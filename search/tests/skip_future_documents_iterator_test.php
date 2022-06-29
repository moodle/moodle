<?php
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
 * Test iterator that skips future documents
 *
 * @package core_search
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Test iterator that skips future documents
 *
 * @package core_search
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class skip_future_documents_iterator_test extends \basic_testcase {

    /**
     * Test normal case with all documents in the past.
     */
    public function test_iterator_all_in_past() {
        $past = strtotime('2017-11-01');
        $documents = [
            self::make_doc($past, 1),
            self::make_doc($past + 1, 2),
            self::make_doc($past + 2, 3)
        ];
        $this->assertEquals('mod_x-frog-1.mod_x-frog-2.mod_x-frog-3.',
                self::do_iterator($documents));
    }

    /**
     * Confirm that the iterator does not call its parent iterator current() function too many
     * times.
     */
    public function test_iterator_performance() {
        $counter = new test_counting_iterator();
        $iterator = new skip_future_documents_iterator($counter);
        $items = 0;
        foreach ($iterator as $value) {
            $this->assertEquals(false, $value);
            $items++;
        }
        $this->assertEquals(3, $items);
        $this->assertEquals(3, $counter->get_count());
    }

    /**
     * Test with no documents at all.
     */
    public function test_iterator_empty() {
        $this->assertEquals('', self::do_iterator([]));
    }

    /**
     * Test if some documents are in the future.
     */
    public function test_iterator_some_in_future() {
        $past = strtotime('2017-11-01');
        $future = time() + 1000;
        $documents = [
            self::make_doc($past, 1),
            self::make_doc($past + 1, 2),
            self::make_doc($future, 3)
        ];
        $this->assertEquals('mod_x-frog-1.mod_x-frog-2.',
                self::do_iterator($documents));
    }

    /**
     * Test if all documents are in the future.
     */
    public function test_iterator_all_in_future() {
        $future = time() + 1000;
        $documents = [
            self::make_doc($future, 1),
            self::make_doc($future + 1, 2),
            self::make_doc($future + 2, 3)
        ];
        $this->assertEquals('', self::do_iterator($documents));
    }

    /**
     * Test when some documents return error.
     */
    public function test_iterator_some_false() {
        $past = strtotime('2017-11-01');
        $documents = [
            self::make_doc($past, 1),
            false,
            self::make_doc($past + 2, 3)
        ];
        $this->assertEquals('mod_x-frog-1.false.mod_x-frog-3.',
                self::do_iterator($documents));
    }

    /**
     * Test when all documents return error.
     */
    public function test_iterator_all_false() {
        $documents = [
            false,
            false,
            false
        ];
        $this->assertEquals('false.false.false.',
                self::do_iterator($documents));
    }

    /**
     * Test iterator with all cases.
     */
    public function test_iterator_past_false_and_future() {
        $past = strtotime('2017-11-01');
        $future = time() + 1000;
        $documents = [
            false,
            self::make_doc($past, 1),
            false,
            self::make_doc($past + 1, 2),
            false,
            self::make_doc($future, 3),
            false
        ];
        $this->assertEquals('false.mod_x-frog-1.false.mod_x-frog-2.false.',
                self::do_iterator($documents));
    }

    /**
     * Helper function to create a search document.
     *
     * @param int $time Modified time
     * @param int $index Item id
     * @return document Search document
     */
    protected static function make_doc($time, $index) {
        $doc = new document($index, 'mod_x', 'frog');
        $doc->set('modified', $time);
        return $doc;
    }

    /**
     * Puts documents through the iterator and returns result as a string for easy testing.
     *
     * @param document[] $documents Array of documents
     * @return string Documents converted to string
     */
    protected static function do_iterator(array $documents) {
        $parent = new \ArrayIterator($documents);
        $iterator = new skip_future_documents_iterator($parent);
        $result = '';
        foreach ($iterator as $rec) {
            if (!$rec) {
                $result .= 'false.';
            } else {
                $result .= $rec->get('id') . '.';
            }
        }
        return $result;
    }
}

/**
 * Fake iterator just for counting how many times current() is called. It returns 'false' 3 times.
 *
 * @package core_search
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_counting_iterator implements \Iterator {

    /** @var int Current position in iterator */
    protected $pos = 0;
    /** @var int Number of calls to current() function */
    protected $count = 0;

    /**
     * Returns the current element.
     *
     * @return mixed Can return any type.
     */
    public function current() {
        $this->count++;
        return false;
    }

    /**
     * Counts iterator usage.
     *
     * @return int Number of times current() was called
     */
    public function get_count() {
        return $this->count;
    }

    /**
     * Goes on to the next element.
     */
    public function next() {
        $this->pos++;
    }

    /**
     * Gets the key (not supported)
     *
     * @throws \coding_exception Always
     */
    public function key() {
        throw new \coding_exception('Unsupported');
    }

    /**
     * Checks if iterato is valid (still has entries).
     *
     * @return bool True if still valid
     */
    public function valid() {
        return $this->pos < 3;
    }

    /**
     * Rewinds the iterator.
     */
    public function rewind() {
        $this->pos = 0;
    }
}
