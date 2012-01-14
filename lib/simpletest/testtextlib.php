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


        $utf8 = '言語設定';
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $this->assertIdentical(textlib::convert($utf8, 'utf-8', 'EUC-JP'), $str);
        $this->assertIdentical(textlib::convert($str, 'EUC-JP', 'utf-8'), $utf8);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertIdentical(textlib::convert($utf8, 'utf-8', 'ISO-2022-JP'), $str);
        $this->assertIdentical(textlib::convert($str, 'ISO-2022-JP', 'utf-8'), $utf8);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertIdentical(textlib::convert($utf8, 'utf-8', 'SHIFT-JIS'), $str);
        $this->assertIdentical(textlib::convert($str, 'SHIFT-JIS', 'utf-8'), $utf8);

        $utf8 = '简体中文';
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertIdentical(textlib::convert($utf8, 'utf-8', 'GB2312'), $str);
        $this->assertIdentical(textlib::convert($str, 'GB2312', 'utf-8'), $utf8);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertIdentical(textlib::convert($utf8, 'utf-8', 'GB18030'), $str);
        $this->assertIdentical(textlib::convert($str, 'GB18030', 'utf-8'), $utf8);
    }

    public function test_substr() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::substr($str, 0), $str);
        $this->assertIdentical(textlib::substr($str, 1), 'luťoučký koníček');
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


        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $s = pack("H*", "b8ec"); //EUC-JP
        $this->assertIdentical(textlib::substr($str, 1, 1, 'EUC-JP'), $s);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $s = pack("H*", "1b2442386c1b2842"); //ISO-2022-JP
        $this->assertIdentical(textlib::substr($str, 1, 1, 'ISO-2022-JP'), $s);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $s = pack("H*", "8cea"); //SHIFT-JIS
        $this->assertIdentical(textlib::substr($str, 1, 1, 'SHIFT-JIS'), $s);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $s = pack("H*", "cce5"); //GB2312
        $this->assertIdentical(textlib::substr($str, 1, 1, 'GB2312'), $s);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $s = pack("H*", "cce5"); //GB18030
        $this->assertIdentical(textlib::substr($str, 1, 1, 'GB18030'), $s);
    }

    public function test_strlen() {
        $str = "Žluťoučký koníček";
        $this->assertIdentical(textlib::strlen($str), 17);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strlen($iso2, 'iso-8859-2'), 17);

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strlen($win, 'cp1250'), 17);


        $str = pack("H*", "b8ec"); //EUC-JP
        $this->assertIdentical(textlib::strlen($str, 'EUC-JP'), 1);
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $this->assertIdentical(textlib::strlen($str, 'EUC-JP'), 4);

        $str = pack("H*", "1b2442386c1b2842"); //ISO-2022-JP
        $this->assertIdentical(textlib::strlen($str, 'ISO-2022-JP'), 1);
        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertIdentical(textlib::strlen($str, 'ISO-2022-JP'), 4);

        $str = pack("H*", "8cea"); //SHIFT-JIS
        $this->assertIdentical(textlib::strlen($str, 'SHIFT-JIS'), 1);
        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertIdentical(textlib::strlen($str, 'SHIFT-JIS'), 4);

        $str = pack("H*", "cce5"); //GB2312
        $this->assertIdentical(textlib::strlen($str, 'GB2312'), 1);
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertIdentical(textlib::strlen($str, 'GB2312'), 4);

        $str = pack("H*", "cce5"); //GB18030
        $this->assertIdentical(textlib::strlen($str, 'GB18030'), 1);
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertIdentical(textlib::strlen($str, 'GB18030'), 4);
    }

    public function test_strtolower() {
        $str = "Žluťoučký koníček";
        $low = 'žluťoučký koníček';
        $this->assertIdentical(textlib::strtolower($str), $low);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtolower($iso2, 'iso-8859-2'), textlib::convert($low, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtolower($win, 'cp1250'), textlib::convert($low, 'utf-8', 'cp1250'));


        $str = '言語設定';
        $this->assertIdentical(textlib::strtolower($str), $str);

        $str = '简体中文';
        $this->assertIdentical(textlib::strtolower($str), $str);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertIdentical(textlib::strtolower($str, 'ISO-2022-JP'), $str);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertIdentical(textlib::strtolower($str, 'SHIFT-JIS'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertIdentical(textlib::strtolower($str, 'GB2312'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertIdentical(textlib::strtolower($str, 'GB18030'), $str);
    }

    public function test_strtoupper() {
        $str = "Žluťoučký koníček";
        $up  = 'ŽLUŤOUČKÝ KONÍČEK';
        $this->assertIdentical(textlib::strtoupper($str), $up);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtoupper($iso2, 'iso-8859-2'), textlib::convert($up, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertIdentical(textlib::strtoupper($win, 'cp1250'), textlib::convert($up, 'utf-8', 'cp1250'));


        $str = '言語設定';
        $this->assertIdentical(textlib::strtoupper($str), $str);

        $str = '简体中文';
        $this->assertIdentical(textlib::strtoupper($str), $str);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertIdentical(textlib::strtoupper($str, 'ISO-2022-JP'), $str);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertIdentical(textlib::strtoupper($str, 'SHIFT-JIS'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertIdentical(textlib::strtoupper($str, 'GB2312'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertIdentical(textlib::strtoupper($str, 'GB18030'), $str);
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

/**
 * Unit tests for our utf-8 aware collator.
 *
 * Used for sorting.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collatorlib_test extends UnitTestCase {

    protected $initiallang = null;
    protected $error = null;

    public function setUp() {
        global $SESSION;
        if (isset($SESSION->lang)) {
            $this->initiallang = $SESSION->lang;
        }
        $SESSION->lang = 'en'; // make sure we test en language to get consistent results, hopefully all systems have this locale
        if (extension_loaded('intl')) {
            $this->error = 'Collation aware sorting not supported';
        } else {
            $this->error = 'Collation aware sorting not supported, PHP extension "intl" is not available.';
        }
        parent::setUp();
    }
    public function tearDown() {
        global $SESSION;
        parent::tearDown();
        if ($this->initiallang !== null) {
            $SESSION->lang = $this->initiallang;
            $this->initiallang = null;
        } else {
            unset($SESSION->lang);
        }
    }
    function test_asort() {
        $arr = array('b' => 'ab', 1 => 'aa', 0 => 'cc');
        collatorlib::asort($arr);
        $this->assertIdentical(array_keys($arr), array(1, 'b', 0));
        $this->assertIdentical(array_values($arr), array('aa', 'ab', 'cc'));

        $arr = array('a' => 'áb', 'b' => 'ab', 1 => 'aa', 0=>'cc');
        collatorlib::asort($arr);
        $this->assertIdentical(array_keys($arr), array(1, 'b', 'a', 0), $this->error);
        $this->assertIdentical(array_values($arr), array('aa', 'ab', 'áb', 'cc'), $this->error);
    }
    function test_asort_objects_by_method() {
        $objects = array(
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        collatorlib::asort_objects_by_method($objects, 'get_protected_name');
        $this->assertIdentical(array_keys($objects), array(1, 'b', 0));
        $this->assertIdentical($this->get_ordered_names($objects, 'get_protected_name'), array('aa', 'ab', 'cc'));

        $objects = array(
            'a' => new string_test_class('áb'),
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        collatorlib::asort_objects_by_method($objects, 'get_private_name');
        $this->assertIdentical(array_keys($objects), array(1, 'b', 'a', 0), $this->error);
        $this->assertIdentical($this->get_ordered_names($objects, 'get_private_name'), array('aa', 'ab', 'áb', 'cc'), $this->error);
    }
    function test_asort_objects_by_property() {
        $objects = array(
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        collatorlib::asort_objects_by_property($objects, 'publicname');
        $this->assertIdentical(array_keys($objects), array(1, 'b', 0));
        $this->assertIdentical($this->get_ordered_names($objects, 'publicname'), array('aa', 'ab', 'cc'));

        $objects = array(
            'a' => new string_test_class('áb'),
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        collatorlib::asort_objects_by_property($objects, 'publicname');
        $this->assertIdentical(array_keys($objects), array(1, 'b', 'a', 0), $this->error);
        $this->assertIdentical($this->get_ordered_names($objects, 'publicname'), array('aa', 'ab', 'áb', 'cc'), $this->error);
    }
    protected function get_ordered_names($objects, $methodproperty = 'get_protected_name') {
        $return = array();
        foreach ($objects as $object) {
            if ($methodproperty == 'publicname') {
                $return[] = $object->publicname;
            } else {
                $return[] = $object->$methodproperty();
            }
        }
        return $return;
    }
}
/**
 * Simple class used to work with the unit test.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class string_test_class extends stdClass {
    public $publicname;
    protected $protectedname;
    private $privatename;
    public function __construct($name) {
        $this->publicname = $name;
        $this->protectedname = $name;
        $this->privatename = $name;
    }
    public function get_protected_name() {
        return $this->protectedname;
    }
    public function get_private_name() {
        return $this->publicname;
    }
}