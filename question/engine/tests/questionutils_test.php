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
 * This file contains tests for the {@link question_utils} class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');


/**
 * Unit tests for the {@link question_utils} class.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_utils_test extends advanced_testcase {
    public function test_arrays_have_same_keys_and_values() {
        $this->assertTrue(question_utils::arrays_have_same_keys_and_values(
                array(),
                array()));
        $this->assertTrue(question_utils::arrays_have_same_keys_and_values(
                array('key' => 1),
                array('key' => '1')));
        $this->assertFalse(question_utils::arrays_have_same_keys_and_values(
                array(),
                array('key' => 1)));
        $this->assertFalse(question_utils::arrays_have_same_keys_and_values(
                array('key' => 2),
                array('key' => 1)));
        $this->assertFalse(question_utils::arrays_have_same_keys_and_values(
                array('key' => 1),
                array('otherkey' => 1)));
        $this->assertFalse(question_utils::arrays_have_same_keys_and_values(
                array('sub0' => '2', 'sub1' => '2', 'sub2' => '3', 'sub3' => '1'),
                array('sub0' => '1', 'sub1' => '2', 'sub2' => '3', 'sub3' => '1')));
    }

    public function test_arrays_same_at_key() {
        $this->assertTrue(question_utils::arrays_same_at_key(
                array(),
                array(),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key(
                array(),
                array('key' => 1),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key(
                array('key' => 1),
                array(),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key(
                array('key' => 1),
                array('key' => 1),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key(
                array('key' => 1),
                array('key' => 2),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key(
                array('key' => 1),
                array('key' => '1'),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key(
                array('key' => 0),
                array('key' => ''),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key(
                array(),
                array('key' => ''),
                'key'));
    }

    public function test_arrays_same_at_key_missing_is_blank() {
        $this->assertTrue(question_utils::arrays_same_at_key_missing_is_blank(
                array(),
                array(),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key_missing_is_blank(
                array(),
                array('key' => 1),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key_missing_is_blank(
                array('key' => 1),
                array(),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key_missing_is_blank(
                array('key' => 1),
                array('key' => 1),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key_missing_is_blank(
                array('key' => 1),
                array('key' => 2),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key_missing_is_blank(
                array('key' => 1),
                array('key' => '1'),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key_missing_is_blank(
                array('key' => '0'),
                array('key' => ''),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key_missing_is_blank(
                array(),
                array('key' => ''),
                'key'));
    }

    public function test_arrays_same_at_key_integer() {
        $this->assertTrue(question_utils::arrays_same_at_key_integer(
                array(),
                array(),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key_integer(
                array(),
                array('key' => 1),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key_integer(
                array('key' => 1),
                array(),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key_integer(
                array('key' => 1),
                array('key' => 1),
                'key'));
        $this->assertFalse(question_utils::arrays_same_at_key_integer(
                array('key' => 1),
                array('key' => 2),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key_integer(
                array('key' => 1),
                array('key' => '1'),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key_integer(
                array('key' => '0'),
                array('key' => ''),
                'key'));
        $this->assertTrue(question_utils::arrays_same_at_key_integer(
                array(),
                array('key' => 0),
                'key'));
    }

    public function test_int_to_roman() {
        $this->assertSame('i', question_utils::int_to_roman(1));
        $this->assertSame('iv', question_utils::int_to_roman(4));
        $this->assertSame('v', question_utils::int_to_roman(5));
        $this->assertSame('vi', question_utils::int_to_roman(6));
        $this->assertSame('ix', question_utils::int_to_roman(9));
        $this->assertSame('xi', question_utils::int_to_roman(11));
        $this->assertSame('xlviii', question_utils::int_to_roman(48));
        $this->assertSame('lxxxvii', question_utils::int_to_roman(87));
        $this->assertSame('c', question_utils::int_to_roman(100));
        $this->assertSame('mccxxxiv', question_utils::int_to_roman(1234));
        $this->assertSame('mmmcmxcix', question_utils::int_to_roman(3999));
    }

    public function test_int_to_letter() {
        $this->assertEquals('A', question_utils::int_to_letter(1));
        $this->assertEquals('B', question_utils::int_to_letter(2));
        $this->assertEquals('C', question_utils::int_to_letter(3));
        $this->assertEquals('D', question_utils::int_to_letter(4));
        $this->assertEquals('E', question_utils::int_to_letter(5));
        $this->assertEquals('F', question_utils::int_to_letter(6));
        $this->assertEquals('G', question_utils::int_to_letter(7));
        $this->assertEquals('H', question_utils::int_to_letter(8));
        $this->assertEquals('I', question_utils::int_to_letter(9));
        $this->assertEquals('J', question_utils::int_to_letter(10));
        $this->assertEquals('K', question_utils::int_to_letter(11));
        $this->assertEquals('L', question_utils::int_to_letter(12));
        $this->assertEquals('M', question_utils::int_to_letter(13));
        $this->assertEquals('N', question_utils::int_to_letter(14));
        $this->assertEquals('O', question_utils::int_to_letter(15));
        $this->assertEquals('P', question_utils::int_to_letter(16));
        $this->assertEquals('Q', question_utils::int_to_letter(17));
        $this->assertEquals('R', question_utils::int_to_letter(18));
        $this->assertEquals('S', question_utils::int_to_letter(19));
        $this->assertEquals('T', question_utils::int_to_letter(20));
        $this->assertEquals('U', question_utils::int_to_letter(21));
        $this->assertEquals('V', question_utils::int_to_letter(22));
        $this->assertEquals('W', question_utils::int_to_letter(23));
        $this->assertEquals('X', question_utils::int_to_letter(24));
        $this->assertEquals('Y', question_utils::int_to_letter(25));
        $this->assertEquals('Z', question_utils::int_to_letter(26));
    }

    public function test_int_to_roman_too_small() {
        $this->expectException(moodle_exception::class);
        question_utils::int_to_roman(0);
    }

    public function test_int_to_roman_too_big() {
        $this->expectException(moodle_exception::class);
        question_utils::int_to_roman(4000);
    }

    public function test_int_to_roman_not_int() {
        $this->expectException(moodle_exception::class);
        question_utils::int_to_roman(1.5);
    }

    public function test_clean_param_mark() {
        $this->assertNull(question_utils::clean_param_mark(null));
        $this->assertNull(question_utils::clean_param_mark('frog'));
        $this->assertSame('', question_utils::clean_param_mark(''));
        $this->assertSame(0.0, question_utils::clean_param_mark('0'));
        $this->assertSame(1.5, question_utils::clean_param_mark('1.5'));
        $this->assertSame(1.5, question_utils::clean_param_mark('1,5'));
        $this->assertSame(-1.5, question_utils::clean_param_mark('-1.5'));
        $this->assertSame(-1.5, question_utils::clean_param_mark('-1,5'));
    }
}
