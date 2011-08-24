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
 * Functions to support installation process
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for our utf-8 aware text processing
 *
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class textlib_test extends UnitTestCase {

    public static $includecoverage = array('lib/textlib.class.php');

    public function test_parse_charset() {
        $this->assertIdentical(textlib::parse_charset('Cp1250'), 'windows-1250');
        // does typo3 work? some encoding moodle does not use
        $this->assertIdentical(textlib::parse_charset('ms-ansi'), 'windows-1252');
    }

    public function test_convert() {
        $utf8 = "Žluťoučký koníček";
        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::convert($utf8, 'utf-8', 'iso-8859-2'), $iso2);
        $this->assertIdentical(textlib::convert($iso2, 'iso-8859-2', 'utf-8'), $utf8);
        $this->assertIdentical(textlib::convert($utf8, 'utf-8', 'win-1250'), $win);
        $this->assertIdentical(textlib::convert($win, 'win-1250', 'utf-8'), $utf8);
        $this->assertIdentical(textlib::convert($win, 'win-1250', 'iso-8859-2'), $iso2);
        $this->assertIdentical(textlib::convert($iso2, 'iso-8859-2', 'win-1250'), $win);
        $this->assertIdentical(textlib::convert($iso2, 'iso-8859-2', 'iso-8859-2'), $iso2);
        $this->assertIdentical(textlib::convert($win, 'win-1250', 'cp1250'), $win);
    }

    public function test_substr() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::substr($str, 1, 3), 'luť');
        $this->assertIdentical(textlib::substr($str, 0, 100), $str);
        $this->assertIdentical(textlib::substr($str, -3, 2), 'če');

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::substr($iso2, 1, 3, 'iso-8859-2'), textlib::convert('luť', 'utf-8', 'iso-8859-2'));
        $this->assertIdentical(textlib::substr($iso2, 0, 100, 'iso-8859-2'), textlib::convert($str, 'utf-8', 'iso-8859-2'));
        $this->assertIdentical(textlib::substr($iso2, -3, 2, 'iso-8859-2'), textlib::convert('če', 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::substr($win, 1, 3, 'cp1250'), textlib::convert('luť', 'utf-8', 'cp1250'));
        $this->assertIdentical(textlib::substr($win, 0, 100, 'cp1250'), textlib::convert($str, 'utf-8', 'cp1250'));
        $this->assertIdentical(textlib::substr($win, -3, 2, 'cp1250'), textlib::convert('če', 'utf-8', 'cp1250'));
    }

    public function test_strlen() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::strlen($str), 17);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strlen($iso2, 'iso-8859-2'), 17);

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strlen($win, 'cp1250'), 17);
    }

    public function test_strtolower() {
        $str = "Žluťoučký koníček";
        $low = 'žluťoučký koníček';
        $this->assertIdentical(textlib::strtolower($str), $low);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtolower($iso2, 'iso-8859-2'), textlib::convert($low, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtolower($win, 'cp1250'), textlib::convert($low, 'utf-8', 'cp1250'));
    }

    public function test_strtoupper() {
        $str = "Žluťoučký koníček";
        $up  = 'ŽLUŤOUČKÝ KONÍČEK';
        $this->assertIdentical(textlib::strtoupper($str), $up);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtoupper($iso2, 'iso-8859-2'), textlib::convert($up, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtoupper($win, 'cp1250'), textlib::convert($up, 'utf-8', 'cp1250'));
    }

    public function test_strpos() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::strpos($str, 'koníč'), 10);
    }

    public function test_strrpos() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::strrpos($str, 'o'), 11);
    }

    public function test_specialtoascii() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::specialtoascii($str), 'Zlutoucky konicek');
    }

    public function test_encode_mimeheader() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::encode_mimeheader($str), '=?utf-8?B?xb1sdcWlb3XEjWvDvSBrb27DrcSNZWs=?=');
    }

    public function test_entities_to_utf8() {
        $str = "&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&#237;&#269;ek";
        $this->assertIdentical(textlib::entities_to_utf8($str), "Žluťoučký koníček");

    }

    public function test_utf8_to_entities() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::utf8_to_entities($str), "&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&#xed;&#x10d;ek");
        $this->assertIdentical(textlib::utf8_to_entities($str, true), "&#381;lu&#357;ou&#269;k&#253; kon&#237;&#269;ek");

    }

    public function test_trim_utf8_bom() {
        $bom = "\xef\xbb\xbf";
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::trim_utf8_bom($bom.$str.$bom), $str.$bom);
    }

    public function test_get_encodings() {
        $encodings = textlib::get_encodings();
        $this->assertTrue(is_array($encodings));
        $this->assertTrue(count($encodings) > 1);
        $this->assertTrue(isset($encodings['UTF-8']));
    }

    public function test_code2utf8() {
        $this->assertIdentical(textlib::code2utf8(381), 'Ž');
    }

    public function test_strtotitle() {
        $str = "žluťoučký koníček";
        $this->assertIdentical(textlib::strtotitle($str), "Žluťoučký Koníček");
    }

    public function test_asort() {
        global $SESSION;
        $SESSION->lang = 'en'; // make sure we test en language to get consistent results, hopefully all systems have this locale

        $arr = array('b'=>'ab', 1=>'aa', 0=>'cc');
        textlib::asort($arr);
        $this->assertIdentical(array_keys($arr), array(1, 'b', 0));
        $this->assertIdentical(array_values($arr), array('aa', 'ab', 'cc'));

        if (extension_loaded('intl')) {
            $error = 'Collation aware sorting not supported';
        } else {
            $error = 'Collation aware sorting not supported, PHP extension "intl" is not available.';
        }

        $arr = array('a'=>'áb', 'b'=>'ab', 1=>'aa', 0=>'cc');
        textlib::asort($arr);
        $this->assertIdentical(array_keys($arr), array(1, 'b', 'a', 0), $error);

        unset($SESSION->lang);
    }

    public function test_deprecated_textlib_get_instance() {
        $textlib = textlib_get_instance();
        $this->assertIdentical($textlib->substr('abc', 1, 1), 'b');
        $this->assertIdentical($textlib->strlen('abc'), 3);
        $this->assertIdentical($textlib->strtoupper('Abc'), 'ABC');
        $this->assertIdentical($textlib->strtolower('Abc'), 'abc');
        $this->assertIdentical($textlib->strpos('abc', 'a'), 0);
        $this->assertIdentical($textlib->strpos('abc', 'd'), false);
        $this->assertIdentical($textlib->strrpos('abcabc', 'a'), 3);
        $this->assertIdentical($textlib->specialtoascii('ábc'), 'abc');
        $this->assertIdentical($textlib->strtotitle('abc ABC'), 'Abc Abc');
    }
}
