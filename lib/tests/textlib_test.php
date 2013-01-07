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
 * textlib unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for our utf-8 aware text processing
 *
 * @package    core
 * @category   phpunit
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_textlib_testcase extends advanced_testcase {

    /**
     * Tests the static parse charset method
     * @return void
     */
    public function test_parse_charset() {
        $this->assertSame(textlib::parse_charset('Cp1250'), 'windows-1250');
        // does typo3 work? some encoding moodle does not use
        $this->assertSame(textlib::parse_charset('ms-ansi'), 'windows-1252');
    }

    /**
     * Tests the static convert method
     * @return void
     */
    public function test_convert() {
        $utf8 = "Žluťoučký koníček";
        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'iso-8859-2'), $iso2);
        $this->assertSame(textlib::convert($iso2, 'iso-8859-2', 'utf-8'), $utf8);
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'win-1250'), $win);
        $this->assertSame(textlib::convert($win, 'win-1250', 'utf-8'), $utf8);
        $this->assertSame(textlib::convert($win, 'win-1250', 'iso-8859-2'), $iso2);
        $this->assertSame(textlib::convert($iso2, 'iso-8859-2', 'win-1250'), $win);
        $this->assertSame(textlib::convert($iso2, 'iso-8859-2', 'iso-8859-2'), $iso2);
        $this->assertSame(textlib::convert($win, 'win-1250', 'cp1250'), $win);
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'utf-8'), $utf8);


        $utf8 = '言語設定';
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'EUC-JP'), $str);
        $this->assertSame(textlib::convert($str, 'EUC-JP', 'utf-8'), $utf8);
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'ISO-2022-JP'), $str);
        $this->assertSame(textlib::convert($str, 'ISO-2022-JP', 'utf-8'), $utf8);
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'SHIFT-JIS'), $str);
        $this->assertSame(textlib::convert($str, 'SHIFT-JIS', 'utf-8'), $utf8);
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $utf8 = '简体中文';
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'GB2312'), $str);
        $this->assertSame(textlib::convert($str, 'GB2312', 'utf-8'), $utf8);
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'GB18030'), $str);
        $this->assertSame(textlib::convert($str, 'GB18030', 'utf-8'), $utf8);
        $this->assertSame(textlib::convert($utf8, 'utf-8', 'utf-8'), $utf8);
    }

    /**
     * Tests the static sub string method
     * @return void
     */
    public function test_substr() {
        $str = "Žluťoučký koníček";
        $this->assertSame(textlib::substr($str, 0), $str);
        $this->assertSame(textlib::substr($str, 1), 'luťoučký koníček');
        $this->assertSame(textlib::substr($str, 1, 3), 'luť');
        $this->assertSame(textlib::substr($str, 0, 100), $str);
        $this->assertSame(textlib::substr($str, -3, 2), 'če');

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::substr($iso2, 1, 3, 'iso-8859-2'), textlib::convert('luť', 'utf-8', 'iso-8859-2'));
        $this->assertSame(textlib::substr($iso2, 0, 100, 'iso-8859-2'), textlib::convert($str, 'utf-8', 'iso-8859-2'));
        $this->assertSame(textlib::substr($iso2, -3, 2, 'iso-8859-2'), textlib::convert('če', 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::substr($win, 1, 3, 'cp1250'), textlib::convert('luť', 'utf-8', 'cp1250'));
        $this->assertSame(textlib::substr($win, 0, 100, 'cp1250'), textlib::convert($str, 'utf-8', 'cp1250'));
        $this->assertSame(textlib::substr($win, -3, 2, 'cp1250'), textlib::convert('če', 'utf-8', 'cp1250'));


        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $s = pack("H*", "b8ec"); //EUC-JP
        $this->assertSame(textlib::substr($str, 1, 1, 'EUC-JP'), $s);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $s = pack("H*", "1b2442386c1b2842"); //ISO-2022-JP
        $this->assertSame(textlib::substr($str, 1, 1, 'ISO-2022-JP'), $s);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $s = pack("H*", "8cea"); //SHIFT-JIS
        $this->assertSame(textlib::substr($str, 1, 1, 'SHIFT-JIS'), $s);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $s = pack("H*", "cce5"); //GB2312
        $this->assertSame(textlib::substr($str, 1, 1, 'GB2312'), $s);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $s = pack("H*", "cce5"); //GB18030
        $this->assertSame(textlib::substr($str, 1, 1, 'GB18030'), $s);
    }

    /**
     * Tests the static string length method
     * @return void
     */
    public function test_strlen() {
        $str = "Žluťoučký koníček";
        $this->assertSame(textlib::strlen($str), 17);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::strlen($iso2, 'iso-8859-2'), 17);

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::strlen($win, 'cp1250'), 17);


        $str = pack("H*", "b8ec"); //EUC-JP
        $this->assertSame(textlib::strlen($str, 'EUC-JP'), 1);
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $this->assertSame(textlib::strlen($str, 'EUC-JP'), 4);

        $str = pack("H*", "1b2442386c1b2842"); //ISO-2022-JP
        $this->assertSame(textlib::strlen($str, 'ISO-2022-JP'), 1);
        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(textlib::strlen($str, 'ISO-2022-JP'), 4);

        $str = pack("H*", "8cea"); //SHIFT-JIS
        $this->assertSame(textlib::strlen($str, 'SHIFT-JIS'), 1);
        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(textlib::strlen($str, 'SHIFT-JIS'), 4);

        $str = pack("H*", "cce5"); //GB2312
        $this->assertSame(textlib::strlen($str, 'GB2312'), 1);
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(textlib::strlen($str, 'GB2312'), 4);

        $str = pack("H*", "cce5"); //GB18030
        $this->assertSame(textlib::strlen($str, 'GB18030'), 1);
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(textlib::strlen($str, 'GB18030'), 4);
    }

    /**
     * Tests the static strtolower method
     * @return void
     */
    public function test_strtolower() {
        $str = "Žluťoučký koníček";
        $low = 'žluťoučký koníček';
        $this->assertSame(textlib::strtolower($str), $low);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::strtolower($iso2, 'iso-8859-2'), textlib::convert($low, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::strtolower($win, 'cp1250'), textlib::convert($low, 'utf-8', 'cp1250'));


        $str = '言語設定';
        $this->assertSame(textlib::strtolower($str), $str);

        $str = '简体中文';
        $this->assertSame(textlib::strtolower($str), $str);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(textlib::strtolower($str, 'ISO-2022-JP'), $str);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(textlib::strtolower($str, 'SHIFT-JIS'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(textlib::strtolower($str, 'GB2312'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(textlib::strtolower($str, 'GB18030'), $str);

        // typo3 has problems with integers
        $str = 1309528800;
        $this->assertSame((string)$str, textlib::strtolower($str));
    }

    /**
     * Tests the static strtoupper
     * @return void
     */
    public function test_strtoupper() {
        $str = "Žluťoučký koníček";
        $up  = 'ŽLUŤOUČKÝ KONÍČEK';
        $this->assertSame(textlib::strtoupper($str), $up);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::strtoupper($iso2, 'iso-8859-2'), textlib::convert($up, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(textlib::strtoupper($win, 'cp1250'), textlib::convert($up, 'utf-8', 'cp1250'));


        $str = '言語設定';
        $this->assertSame(textlib::strtoupper($str), $str);

        $str = '简体中文';
        $this->assertSame(textlib::strtoupper($str), $str);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(textlib::strtoupper($str, 'ISO-2022-JP'), $str);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(textlib::strtoupper($str, 'SHIFT-JIS'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(textlib::strtoupper($str, 'GB2312'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(textlib::strtoupper($str, 'GB18030'), $str);
    }

    /**
     * Tests the static strpos method
     * @return void
     */
    public function test_strpos() {
        $str = "Žluťoučký koníček";
        $this->assertSame(textlib::strpos($str, 'koníč'), 10);
    }

    /**
     * Tests the static strrpos
     * @return void
     */
    public function test_strrpos() {
        $str = "Žluťoučký koníček";
        $this->assertSame(textlib::strrpos($str, 'o'), 11);
    }

    /**
     * Tests the static specialtoascii method
     * @return void
     */
    public function test_specialtoascii() {
        $str = "Žluťoučký koníček";
        $this->assertSame(textlib::specialtoascii($str), 'Zlutoucky konicek');
    }

    /**
     * Tests the static encode_mimeheader method
     * @return void
     */
    public function test_encode_mimeheader() {
        $str = "Žluťoučký koníček";
        $this->assertSame(textlib::encode_mimeheader($str), '=?utf-8?B?xb1sdcWlb3XEjWvDvSBrb27DrcSNZWs=?=');
    }

    /**
     * Tests the static entities_to_utf8 method
     * @return void
     */
    public function test_entities_to_utf8() {
        $str = "&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&iacute;&#269;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("Žluťoučký koníček©\"&<>§«", textlib::entities_to_utf8($str));
    }

    /**
     * Tests the static utf8_to_entities method
     * @return void
     */
    public function test_utf8_to_entities() {
        $str = "&#x17d;luťoučký kon&iacute;ček&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&iacute;&#x10d;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;", textlib::utf8_to_entities($str));
        $this->assertSame("&#381;lu&#357;ou&#269;k&#253; kon&iacute;&#269;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;", textlib::utf8_to_entities($str, true));

        $str = "&#381;luťoučký kon&iacute;ček&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&#xed;&#x10d;ek&#xa9;\"&<>&#xa7;&#xab;", textlib::utf8_to_entities($str, false, true));
        $this->assertSame("&#381;lu&#357;ou&#269;k&#253; kon&#237;&#269;ek&#169;\"&<>&#167;&#171;", textlib::utf8_to_entities($str, true, true));
    }

    /**
     * Tests the static trim_utf8_bom method
     * @return void
     */
    public function test_trim_utf8_bom() {
        $bom = "\xef\xbb\xbf";
        $str = "Žluťoučký koníček";
        $this->assertSame(textlib::trim_utf8_bom($bom.$str.$bom), $str.$bom);
    }

    /**
     * Tests the static get_encodings method
     * @return void
     */
    public function test_get_encodings() {
        $encodings = textlib::get_encodings();
        $this->assertTrue(is_array($encodings));
        $this->assertTrue(count($encodings) > 1);
        $this->assertTrue(isset($encodings['UTF-8']));
    }

    /**
     * Tests the static code2utf8 method
     * @return void
     */
    public function test_code2utf8() {
        $this->assertSame(textlib::code2utf8(381), 'Ž');
    }

    /**
     * Tests the static strtotitle method
     * @return void
     */
    public function test_strtotitle() {
        $str = "žluťoučký koníček";
        $this->assertSame(textlib::strtotitle($str), "Žluťoučký Koníček");
    }

    /**
     * Tests the deprecated method of textlib that still require an instance.
     * @return void
     */
    public function test_deprecated_textlib_get_instance() {
        $textlib = textlib_get_instance();
        $this->assertDebuggingCalled();
        $this->assertSame($textlib->substr('abc', 1, 1), 'b');
        $this->assertSame($textlib->strlen('abc'), 3);
        $this->assertSame($textlib->strtoupper('Abc'), 'ABC');
        $this->assertSame($textlib->strtolower('Abc'), 'abc');
        $this->assertSame($textlib->strpos('abc', 'a'), 0);
        $this->assertSame($textlib->strpos('abc', 'd'), false);
        $this->assertSame($textlib->strrpos('abcabc', 'a'), 3);
        $this->assertSame($textlib->specialtoascii('ábc'), 'abc');
        $this->assertSame($textlib->strtotitle('abc ABC'), 'Abc Abc');
    }
}


/**
 * Unit tests for our utf-8 aware collator.
 *
 * Used for sorting.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collatorlib_testcase extends basic_testcase {

    /**
     * @var string The initial lang, stored because we change it during testing
     */
    protected $initiallang = null;

    /**
     * @var string The last error that has occured
     */
    protected $error = null;

    /**
     * Prepares things for this test case
     * @return void
     */
    protected function setUp() {
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

    /**
     * Cleans things up after this test case has run
     * @return void
     */
    protected function tearDown() {
        global $SESSION;
        parent::tearDown();
        if ($this->initiallang !== null) {
            $SESSION->lang = $this->initiallang;
            $this->initiallang = null;
        } else {
            unset($SESSION->lang);
        }
    }

    /**
     * Tests the static asort method
     * @return void
     */
    public function test_asort() {
        $arr = array('b' => 'ab', 1 => 'aa', 0 => 'cc');
        $result = collatorlib::asort($arr);
        $this->assertSame(array_values($arr), array('aa', 'ab', 'cc'));
        $this->assertSame(array_keys($arr), array(1, 'b', 0));
        $this->assertTrue($result);

        $arr = array('b' => 'ab', 1 => 'aa', 0 => 'cc');
        $result = collatorlib::asort($arr, collatorlib::SORT_STRING);
        $this->assertSame(array_values($arr), array('aa', 'ab', 'cc'));
        $this->assertSame(array_keys($arr), array(1, 'b', 0));
        $this->assertTrue($result);

        $arr = array('b' => 'aac', 1 => 'Aac', 0 => 'cc');
        $result = collatorlib::asort($arr, (collatorlib::SORT_STRING | collatorlib::CASE_SENSITIVE));
        $this->assertSame(array_values($arr), array('Aac', 'aac', 'cc'));
        $this->assertSame(array_keys($arr), array(1, 'b', 0));
        $this->assertTrue($result);

        $arr = array('b' => 'a1', 1 => 'a10', 0 => 'a3b');
        $result = collatorlib::asort($arr);
        $this->assertSame(array_values($arr), array('a1', 'a10', 'a3b'));
        $this->assertSame(array_keys($arr), array('b', 1, 0));
        $this->assertTrue($result);

        $arr = array('b' => 'a1', 1 => 'a10', 0 => 'a3b');
        $result = collatorlib::asort($arr, collatorlib::SORT_NATURAL);
        $this->assertSame(array_values($arr), array('a1', 'a3b', 'a10'));
        $this->assertSame(array_keys($arr), array('b', 0, 1));
        $this->assertTrue($result);

        $arr = array('b' => '1.1.1', 1 => '1.2', 0 => '1.20.2');
        $result = collatorlib::asort($arr, collatorlib::SORT_NATURAL);
        $this->assertSame(array_values($arr), array('1.1.1', '1.2', '1.20.2'));
        $this->assertSame(array_keys($arr), array('b', 1, 0));
        $this->assertTrue($result);

        $arr = array('b' => '-1', 1 => 1000, 0 => -1.2, 3 => 1, 4 => false);
        $result = collatorlib::asort($arr, collatorlib::SORT_NUMERIC);
        $this->assertSame(array_values($arr), array(-1.2, '-1', false, 1, 1000));
        $this->assertSame(array_keys($arr), array(0, 'b', 4, 3, 1));
        $this->assertTrue($result);

        $arr = array('b' => array(1), 1 => array(2, 3), 0 => 1);
        $result = collatorlib::asort($arr, collatorlib::SORT_REGULAR);
        $this->assertSame(array_values($arr), array(1, array(1), array(2, 3)));
        $this->assertSame(array_keys($arr), array(0, 'b', 1));
        $this->assertTrue($result);

        // test sorting of array of arrays - first element should be used for actual comparison
        $arr = array(0=>array('bb', 'z'), 1=>array('ab', 'a'), 2=>array('zz', 'x'));
        $result = collatorlib::asort($arr, collatorlib::SORT_REGULAR);
        $this->assertSame(array_keys($arr), array(1, 0, 2));
        $this->assertTrue($result);

        $arr = array('a' => 'áb', 'b' => 'ab', 1 => 'aa', 0=>'cc', 'x' => 'Áb',);
        $result = collatorlib::asort($arr);
        $this->assertSame(array_values($arr), array('aa', 'ab', 'áb', 'Áb', 'cc'), $this->error);
        $this->assertSame(array_keys($arr), array(1, 'b', 'a', 'x', 0), $this->error);
        $this->assertTrue($result);

        $a = array(2=>'b', 1=>'c');
        $c =& $a;
        $b =& $a;
        collatorlib::asort($b);
        $this->assertSame($a, $b);
        $this->assertSame($c, $b);
    }

    /**
     * Tests the static asort_objects_by_method method
     * @return void
     */
    public function test_asort_objects_by_method() {
        $objects = array(
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        $result = collatorlib::asort_objects_by_method($objects, 'get_protected_name');
        $this->assertSame(array_keys($objects), array(1, 'b', 0));
        $this->assertSame($this->get_ordered_names($objects, 'get_protected_name'), array('aa', 'ab', 'cc'));
        $this->assertTrue($result);

        $objects = array(
            'b' => new string_test_class('a20'),
            1 => new string_test_class('a1'),
            0 => new string_test_class('a100')
        );
        $result = collatorlib::asort_objects_by_method($objects, 'get_protected_name', collatorlib::SORT_NATURAL);
        $this->assertSame(array_keys($objects), array(1, 'b', 0));
        $this->assertSame($this->get_ordered_names($objects, 'get_protected_name'), array('a1', 'a20', 'a100'));
        $this->assertTrue($result);
    }

    /**
     * Tests the static asort_objects_by_method method
     * @return void
     */
    public function test_asort_objects_by_property() {
        $objects = array(
            'b' => new string_test_class('ab'),
            1 => new string_test_class('aa'),
            0 => new string_test_class('cc')
        );
        $result = collatorlib::asort_objects_by_property($objects, 'publicname');
        $this->assertSame(array_keys($objects), array(1, 'b', 0));
        $this->assertSame($this->get_ordered_names($objects, 'publicname'), array('aa', 'ab', 'cc'));
        $this->assertTrue($result);

        $objects = array(
            'b' => new string_test_class('a20'),
            1 => new string_test_class('a1'),
            0 => new string_test_class('a100')
        );
        $result = collatorlib::asort_objects_by_property($objects, 'publicname', collatorlib::SORT_NATURAL);
        $this->assertSame(array_keys($objects), array(1, 'b', 0));
        $this->assertSame($this->get_ordered_names($objects, 'publicname'), array('a1', 'a20', 'a100'));
        $this->assertTrue($result);
    }

    /**
     * Returns an array of sorted names
     * @param array $objects
     * @param string $methodproperty
     * @return type
     */
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

    /**
     * Tests the static ksort method
     * @return void
     */
    public function test_ksort() {
        $arr = array('b' => 'ab', 1 => 'aa', 0 => 'cc');
        $result = collatorlib::ksort($arr);
        $this->assertSame(array_keys($arr), array(0, 1, 'b'));
        $this->assertSame(array_values($arr), array('cc', 'aa', 'ab'));
        $this->assertTrue($result);

        $obj = new stdClass();
        $arr = array('1.1.1'=>array(), '1.2'=>$obj, '1.20.2'=>null);
        $result = collatorlib::ksort($arr, collatorlib::SORT_NATURAL);
        $this->assertSame(array_keys($arr), array('1.1.1', '1.2', '1.20.2'));
        $this->assertSame(array_values($arr), array(array(), $obj, null));
        $this->assertTrue($result);

        $a = array(2=>'b', 1=>'c');
        $c =& $a;
        $b =& $a;
        collatorlib::ksort($b);
        $this->assertSame($a, $b);
        $this->assertSame($c, $b);
    }
}


/**
 * Simple class used to work with the unit test.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class string_test_class extends stdClass {
    /**
     * @var string A public property
     */
    public $publicname;
    /**
     * @var string A protected property
     */
    protected $protectedname;
    /**
     * @var string A private property
     */
    private $privatename;
    /**
     * Constructs the test instance
     * @param string $name
     */
    public function __construct($name) {
        $this->publicname = $name;
        $this->protectedname = $name;
        $this->privatename = $name;
    }
    /**
     * Returns the protected property
     * @return string
     */
    public function get_protected_name() {
        return $this->protectedname;
    }
    /**
     * Returns the protected property
     * @return string
     */
    public function get_private_name() {
        return $this->publicname;
    }
}