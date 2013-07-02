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
 * core_text unit tests
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
class core_text_testcase extends advanced_testcase {

    /**
     * Tests the static parse charset method
     * @return void
     */
    public function test_parse_charset() {
        $this->assertSame(core_text::parse_charset('Cp1250'), 'windows-1250');
        // does typo3 work? some encoding moodle does not use
        $this->assertSame(core_text::parse_charset('ms-ansi'), 'windows-1252');
    }

    /**
     * Tests the static convert method
     * @return void
     */
    public function test_convert() {
        $utf8 = "Žluťoučký koníček";
        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'iso-8859-2'), $iso2);
        $this->assertSame(core_text::convert($iso2, 'iso-8859-2', 'utf-8'), $utf8);
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'win-1250'), $win);
        $this->assertSame(core_text::convert($win, 'win-1250', 'utf-8'), $utf8);
        $this->assertSame(core_text::convert($win, 'win-1250', 'iso-8859-2'), $iso2);
        $this->assertSame(core_text::convert($iso2, 'iso-8859-2', 'win-1250'), $win);
        $this->assertSame(core_text::convert($iso2, 'iso-8859-2', 'iso-8859-2'), $iso2);
        $this->assertSame(core_text::convert($win, 'win-1250', 'cp1250'), $win);
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'utf-8'), $utf8);


        $utf8 = '言語設定';
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'EUC-JP'), $str);
        $this->assertSame(core_text::convert($str, 'EUC-JP', 'utf-8'), $utf8);
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'ISO-2022-JP'), $str);
        $this->assertSame(core_text::convert($str, 'ISO-2022-JP', 'utf-8'), $utf8);
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'SHIFT-JIS'), $str);
        $this->assertSame(core_text::convert($str, 'SHIFT-JIS', 'utf-8'), $utf8);
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $utf8 = '简体中文';
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'GB2312'), $str);
        $this->assertSame(core_text::convert($str, 'GB2312', 'utf-8'), $utf8);
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'utf-8'), $utf8);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'GB18030'), $str);
        $this->assertSame(core_text::convert($str, 'GB18030', 'utf-8'), $utf8);
        $this->assertSame(core_text::convert($utf8, 'utf-8', 'utf-8'), $utf8);
    }

    /**
     * Tests the static sub string method
     * @return void
     */
    public function test_substr() {
        $str = "Žluťoučký koníček";
        $this->assertSame(core_text::substr($str, 0), $str);
        $this->assertSame(core_text::substr($str, 1), 'luťoučký koníček');
        $this->assertSame(core_text::substr($str, 1, 3), 'luť');
        $this->assertSame(core_text::substr($str, 0, 100), $str);
        $this->assertSame(core_text::substr($str, -3, 2), 'če');

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::substr($iso2, 1, 3, 'iso-8859-2'), core_text::convert('luť', 'utf-8', 'iso-8859-2'));
        $this->assertSame(core_text::substr($iso2, 0, 100, 'iso-8859-2'), core_text::convert($str, 'utf-8', 'iso-8859-2'));
        $this->assertSame(core_text::substr($iso2, -3, 2, 'iso-8859-2'), core_text::convert('če', 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::substr($win, 1, 3, 'cp1250'), core_text::convert('luť', 'utf-8', 'cp1250'));
        $this->assertSame(core_text::substr($win, 0, 100, 'cp1250'), core_text::convert($str, 'utf-8', 'cp1250'));
        $this->assertSame(core_text::substr($win, -3, 2, 'cp1250'), core_text::convert('če', 'utf-8', 'cp1250'));


        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $s = pack("H*", "b8ec"); //EUC-JP
        $this->assertSame(core_text::substr($str, 1, 1, 'EUC-JP'), $s);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $s = pack("H*", "1b2442386c1b2842"); //ISO-2022-JP
        $this->assertSame(core_text::substr($str, 1, 1, 'ISO-2022-JP'), $s);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $s = pack("H*", "8cea"); //SHIFT-JIS
        $this->assertSame(core_text::substr($str, 1, 1, 'SHIFT-JIS'), $s);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $s = pack("H*", "cce5"); //GB2312
        $this->assertSame(core_text::substr($str, 1, 1, 'GB2312'), $s);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $s = pack("H*", "cce5"); //GB18030
        $this->assertSame(core_text::substr($str, 1, 1, 'GB18030'), $s);
    }

    /**
     * Tests the static string length method
     * @return void
     */
    public function test_strlen() {
        $str = "Žluťoučký koníček";
        $this->assertSame(core_text::strlen($str), 17);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::strlen($iso2, 'iso-8859-2'), 17);

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::strlen($win, 'cp1250'), 17);


        $str = pack("H*", "b8ec"); //EUC-JP
        $this->assertSame(core_text::strlen($str, 'EUC-JP'), 1);
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); //EUC-JP
        $this->assertSame(core_text::strlen($str, 'EUC-JP'), 4);

        $str = pack("H*", "1b2442386c1b2842"); //ISO-2022-JP
        $this->assertSame(core_text::strlen($str, 'ISO-2022-JP'), 1);
        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(core_text::strlen($str, 'ISO-2022-JP'), 4);

        $str = pack("H*", "8cea"); //SHIFT-JIS
        $this->assertSame(core_text::strlen($str, 'SHIFT-JIS'), 1);
        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(core_text::strlen($str, 'SHIFT-JIS'), 4);

        $str = pack("H*", "cce5"); //GB2312
        $this->assertSame(core_text::strlen($str, 'GB2312'), 1);
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(core_text::strlen($str, 'GB2312'), 4);

        $str = pack("H*", "cce5"); //GB18030
        $this->assertSame(core_text::strlen($str, 'GB18030'), 1);
        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(core_text::strlen($str, 'GB18030'), 4);
    }

    /**
     * Tests the static strtolower method
     * @return void
     */
    public function test_strtolower() {
        $str = "Žluťoučký koníček";
        $low = 'žluťoučký koníček';
        $this->assertSame(core_text::strtolower($str), $low);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::strtolower($iso2, 'iso-8859-2'), core_text::convert($low, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::strtolower($win, 'cp1250'), core_text::convert($low, 'utf-8', 'cp1250'));


        $str = '言語設定';
        $this->assertSame(core_text::strtolower($str), $str);

        $str = '简体中文';
        $this->assertSame(core_text::strtolower($str), $str);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(core_text::strtolower($str, 'ISO-2022-JP'), $str);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(core_text::strtolower($str, 'SHIFT-JIS'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(core_text::strtolower($str, 'GB2312'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(core_text::strtolower($str, 'GB18030'), $str);

        // typo3 has problems with integers
        $str = 1309528800;
        $this->assertSame((string)$str, core_text::strtolower($str));
    }

    /**
     * Tests the static strtoupper
     * @return void
     */
    public function test_strtoupper() {
        $str = "Žluťoučký koníček";
        $up  = 'ŽLUŤOUČKÝ KONÍČEK';
        $this->assertSame(core_text::strtoupper($str), $up);

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::strtoupper($iso2, 'iso-8859-2'), core_text::convert($up, 'utf-8', 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::strtoupper($win, 'cp1250'), core_text::convert($up, 'utf-8', 'cp1250'));


        $str = '言語設定';
        $this->assertSame(core_text::strtoupper($str), $str);

        $str = '简体中文';
        $this->assertSame(core_text::strtoupper($str), $str);

        $str = pack("H*", "1b24423840386c405f446a1b2842"); //ISO-2022-JP
        $this->assertSame(core_text::strtoupper($str, 'ISO-2022-JP'), $str);

        $str = pack("H*", "8cbe8cea90dd92e8"); //SHIFT-JIS
        $this->assertSame(core_text::strtoupper($str, 'SHIFT-JIS'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB2312
        $this->assertSame(core_text::strtoupper($str, 'GB2312'), $str);

        $str = pack("H*", "bcf2cce5d6d0cec4"); //GB18030
        $this->assertSame(core_text::strtoupper($str, 'GB18030'), $str);
    }

    /**
     * Tests the static strpos method
     * @return void
     */
    public function test_strpos() {
        $str = "Žluťoučký koníček";
        $this->assertSame(core_text::strpos($str, 'koníč'), 10);
    }

    /**
     * Tests the static strrpos
     * @return void
     */
    public function test_strrpos() {
        $str = "Žluťoučký koníček";
        $this->assertSame(core_text::strrpos($str, 'o'), 11);
    }

    /**
     * Tests the static specialtoascii method
     * @return void
     */
    public function test_specialtoascii() {
        $str = "Žluťoučký koníček";
        $this->assertSame(core_text::specialtoascii($str), 'Zlutoucky konicek');
    }

    /**
     * Tests the static encode_mimeheader method
     * @return void
     */
    public function test_encode_mimeheader() {
        $str = "Žluťoučký koníček";
        $this->assertSame(core_text::encode_mimeheader($str), '=?utf-8?B?xb1sdcWlb3XEjWvDvSBrb27DrcSNZWs=?=');
    }

    /**
     * Tests the static entities_to_utf8 method
     * @return void
     */
    public function test_entities_to_utf8() {
        $str = "&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&iacute;&#269;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("Žluťoučký koníček©\"&<>§«", core_text::entities_to_utf8($str));
    }

    /**
     * Tests the static utf8_to_entities method
     * @return void
     */
    public function test_utf8_to_entities() {
        $str = "&#x17d;luťoučký kon&iacute;ček&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&iacute;&#x10d;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;", core_text::utf8_to_entities($str));
        $this->assertSame("&#381;lu&#357;ou&#269;k&#253; kon&iacute;&#269;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;", core_text::utf8_to_entities($str, true));

        $str = "&#381;luťoučký kon&iacute;ček&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&#xed;&#x10d;ek&#xa9;\"&<>&#xa7;&#xab;", core_text::utf8_to_entities($str, false, true));
        $this->assertSame("&#381;lu&#357;ou&#269;k&#253; kon&#237;&#269;ek&#169;\"&<>&#167;&#171;", core_text::utf8_to_entities($str, true, true));
    }

    /**
     * Tests the static trim_utf8_bom method
     * @return void
     */
    public function test_trim_utf8_bom() {
        $bom = "\xef\xbb\xbf";
        $str = "Žluťoučký koníček";
        $this->assertSame(core_text::trim_utf8_bom($bom.$str.$bom), $str.$bom);
    }

    /**
     * Tests the static get_encodings method
     * @return void
     */
    public function test_get_encodings() {
        $encodings = core_text::get_encodings();
        $this->assertTrue(is_array($encodings));
        $this->assertTrue(count($encodings) > 1);
        $this->assertTrue(isset($encodings['UTF-8']));
    }

    /**
     * Tests the static code2utf8 method
     * @return void
     */
    public function test_code2utf8() {
        $this->assertSame(core_text::code2utf8(381), 'Ž');
    }

    /**
     * Tests the static utf8ord method
     * @return void
     */
    public function test_utf8ord() {
        $this->assertSame(core_text::utf8ord(''), ord(''));
        $this->assertSame(core_text::utf8ord('f'), ord('f'));
        $this->assertSame(core_text::utf8ord('α'), 0x03B1);
        $this->assertSame(core_text::utf8ord('й'), 0x0439);
        $this->assertSame(core_text::utf8ord('𯨟'), 0x2FA1F);
        $this->assertSame(core_text::utf8ord('Ž'), 381);
    }

    /**
     * Tests the static strtotitle method
     * @return void
     */
    public function test_strtotitle() {
        $str = "žluťoučký koníček";
        $this->assertSame(core_text::strtotitle($str), "Žluťoučký Koníček");
    }

    public function test_deprecated_textlib() {
        $this->assertSame(core_text::strtolower('HUH'), textlib::strtolower('HUH'));
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

