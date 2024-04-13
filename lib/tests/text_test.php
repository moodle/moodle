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
 * core_text unit tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for our utf-8 aware text processing.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_text
 *
 */
class text_test extends advanced_testcase {

    /**
     * Tests the static parse charset method.
     *
     * @covers ::parse_charset()
     */
    public function test_parse_charset(): void {
        $this->assertSame('windows-1250', core_text::parse_charset('Cp1250'));
        // Some encoding moodle does not use.
        $this->assertSame('windows-1252', core_text::parse_charset('ms-ansi'));
    }

    /**
     * Tests the static convert method.
     *
     * @covers ::convert()
     */
    public function test_convert(): void {
        $this->assertSame('', core_text::convert('', 'utf-8', 'utf-8'));
        $utf8 = "Žluťoučký koníček";
        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame($iso2, core_text::convert($utf8, 'utf-8', 'iso-8859-2'));
        $this->assertSame($utf8, core_text::convert($iso2, 'iso-8859-2', 'utf-8'));
        $this->assertSame($win, core_text::convert($utf8, 'utf-8', 'win-1250'));
        $this->assertSame($utf8, core_text::convert($win, 'win-1250', 'utf-8'));
        $this->assertSame($iso2, core_text::convert($win, 'win-1250', 'iso-8859-2'));
        $this->assertSame($win, core_text::convert($iso2, 'iso-8859-2', 'win-1250'));
        $this->assertSame($iso2, core_text::convert($iso2, 'iso-8859-2', 'iso-8859-2'));
        $this->assertSame($win, core_text::convert($win, 'win-1250', 'cp1250'));
        $this->assertSame($utf8, core_text::convert($utf8, 'utf-8', 'utf-8'));

        $utf8 = '言語設定';
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); // EUC-JP
        $this->assertSame($str, core_text::convert($utf8, 'utf-8', 'EUC-JP'));
        $this->assertSame($utf8, core_text::convert($str, 'EUC-JP', 'utf-8'));
        $this->assertSame($utf8, core_text::convert($utf8, 'utf-8', 'utf-8'));

        $str = pack("H*", "1b24423840386c405f446a1b2842"); // ISO-2022-JP
        $this->assertSame($str, core_text::convert($utf8, 'utf-8', 'ISO-2022-JP'));
        $this->assertSame($utf8, core_text::convert($str, 'ISO-2022-JP', 'utf-8'));
        $this->assertSame($utf8, core_text::convert($utf8, 'utf-8', 'utf-8'));

        $str = pack("H*", "8cbe8cea90dd92e8"); // SHIFT-JIS
        $this->assertSame($str, core_text::convert($utf8, 'utf-8', 'SHIFT-JIS'));
        $this->assertSame($utf8, core_text::convert($str, 'SHIFT-JIS', 'utf-8'));
        $this->assertSame($utf8, core_text::convert($utf8, 'utf-8', 'utf-8'));

        $utf8 = '简体中文';
        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB2312
        $this->assertSame($str, core_text::convert($utf8, 'utf-8', 'GB2312'));
        $this->assertSame($utf8, core_text::convert($str, 'GB2312', 'utf-8'));
        $this->assertSame($utf8, core_text::convert($utf8, 'utf-8', 'utf-8'));

        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB18030
        $this->assertSame($str, core_text::convert($utf8, 'utf-8', 'GB18030'));
        $this->assertSame($utf8, core_text::convert($str, 'GB18030', 'utf-8'));
        $this->assertSame($utf8, core_text::convert($utf8, 'utf-8', 'utf-8'));

        $utf8 = "Žluťoučký koníček";
        $this->assertSame('Zlutoucky konicek', core_text::convert($utf8, 'utf-8', 'ascii'));
        $this->assertSame($utf8, core_text::convert($utf8.chr(130), 'utf-8', 'utf-8'));
        $utf8 = "Der eine stößt den Speer zum Mann";
        $this->assertSame('Der eine stosst den Speer zum Mann', core_text::convert($utf8, 'utf-8', 'ascii'));
        $iso1 = core_text::convert($utf8, 'utf-8', 'iso-8859-1');
        $this->assertSame('Der eine stosst den Speer zum Mann', core_text::convert($iso1, 'iso-8859-1', 'ascii'));
        $utf8 = "A æ Übérmensch på høyeste nivå! И я люблю PHP! есть. アクセシビリティ. ﬁ";
        $this->assertSame("A ae Ubermensch pa hoyeste niva! I a lublu PHP! est'. akuseshibiriti. fi",
            core_text::convert($utf8, 'utf-8', 'ascii'));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::convert(null, 'utf-8', 'ascii'));
    }

    /**
     * Tests the static sub string method.
     *
     * @covers ::substr()
     */
    public function test_substr(): void {
        $str = "Žluťoučký koníček";
        $this->assertSame($str, core_text::substr($str, 0));
        $this->assertSame('luťoučký koníček', core_text::substr($str, 1));
        $this->assertSame('luť', core_text::substr($str, 1, 3));
        $this->assertSame($str, core_text::substr($str, 0, 100));
        $this->assertSame('če', core_text::substr($str, -3, 2));

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::convert('luť', 'utf-8', 'iso-8859-2'), core_text::substr($iso2, 1, 3, 'iso-8859-2'));
        $this->assertSame(core_text::convert($str, 'utf-8', 'iso-8859-2'), core_text::substr($iso2, 0, 100, 'iso-8859-2'));
        $this->assertSame(core_text::convert('če', 'utf-8', 'iso-8859-2'), core_text::substr($iso2, -3, 2, 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::convert('luť', 'utf-8', 'cp1250'), core_text::substr($win, 1, 3, 'cp1250'));
        $this->assertSame(core_text::convert($str, 'utf-8', 'cp1250'), core_text::substr($win, 0, 100, 'cp1250'));
        $this->assertSame(core_text::convert('če', 'utf-8', 'cp1250'), core_text::substr($win, -3, 2, 'cp1250'));

        $str = pack("H*", "b8c0b8ecc0dfc4ea"); // EUC-JP
        $s = pack("H*", "b8ec"); // EUC-JP
        $this->assertSame($s, core_text::substr($str, 1, 1, 'EUC-JP'));

        $str = pack("H*", "1b24423840386c405f446a1b2842"); // ISO-2022-JP
        $s = pack("H*", "1b2442386c1b2842"); // ISO-2022-JP
        $this->assertSame($s, core_text::substr($str, 1, 1, 'ISO-2022-JP'));

        $str = pack("H*", "8cbe8cea90dd92e8"); // SHIFT-JIS
        $s = pack("H*", "8cea"); // SHIFT-JIS
        $this->assertSame($s, core_text::substr($str, 1, 1, 'SHIFT-JIS'));

        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB2312
        $s = pack("H*", "cce5"); // GB2312
        $this->assertSame($s, core_text::substr($str, 1, 1, 'GB2312'));

        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB18030
        $s = pack("H*", "cce5"); // GB18030
        $this->assertSame($s, core_text::substr($str, 1, 1, 'GB18030'));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::substr(null, 1, 1));
        $this->assertSame('', core_text::substr(null, 1));
        $this->assertSame('', core_text::substr(null, 1, 1, 'cp1250'));
        $this->assertSame('', core_text::substr(null, 1, null, 'cp1250'));
    }

    /**
     * Tests the static string length method.
     *
     * @covers ::strlen()
     */
    public function test_strlen(): void {
        $str = "Žluťoučký koníček";
        $this->assertSame(17, core_text::strlen($str));

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(17, core_text::strlen($iso2, 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(17, core_text::strlen($win, 'cp1250'));

        $str = pack("H*", "b8ec"); // EUC-JP
        $this->assertSame(1, core_text::strlen($str, 'EUC-JP'));
        $str = pack("H*", "b8c0b8ecc0dfc4ea"); // EUC-JP
        $this->assertSame(4, core_text::strlen($str, 'EUC-JP'));

        $str = pack("H*", "1b2442386c1b2842"); // ISO-2022-JP
        $this->assertSame(1, core_text::strlen($str, 'ISO-2022-JP'));
        $str = pack("H*", "1b24423840386c405f446a1b2842"); // ISO-2022-JP
        $this->assertSame(4, core_text::strlen($str, 'ISO-2022-JP'));

        $str = pack("H*", "8cea"); // SHIFT-JIS
        $this->assertSame(1, core_text::strlen($str, 'SHIFT-JIS'));
        $str = pack("H*", "8cbe8cea90dd92e8"); // SHIFT-JIS
        $this->assertSame(4, core_text::strlen($str, 'SHIFT-JIS'));

        $str = pack("H*", "cce5"); // GB2312
        $this->assertSame(1, core_text::strlen($str, 'GB2312'));
        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB2312
        $this->assertSame(4, core_text::strlen($str, 'GB2312'));

        $str = pack("H*", "cce5"); // GB18030
        $this->assertSame(1, core_text::strlen($str, 'GB18030'));
        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB18030
        $this->assertSame(4, core_text::strlen($str, 'GB18030'));

        // Check that null argument is allowed.
        $this->assertSame(0, core_text::strlen(null));
        $this->assertSame(0, core_text::strlen(null, 'cp1250'));
    }

    /**
     * Test unicode safe string truncation.
     *
     * @covers ::str_max_bytes()
     */
    public function test_str_max_bytes(): void {
        // These are all 3 byte characters, so this is a 12-byte string.
        $str = '言語設定';

        $this->assertEquals(12, strlen($str));

        // Step back, shortening the string 1 byte at a time. Should remove in 1 char chunks.
        $conv = core_text::str_max_bytes($str, 12);
        $this->assertEquals(12, strlen($conv));
        $this->assertSame('言語設定', $conv);
        $conv = core_text::str_max_bytes($str, 11);
        $this->assertEquals(9, strlen($conv));
        $this->assertSame('言語設', $conv);
        $conv = core_text::str_max_bytes($str, 10);
        $this->assertEquals(9, strlen($conv));
        $this->assertSame('言語設', $conv);
        $conv = core_text::str_max_bytes($str, 9);
        $this->assertEquals(9, strlen($conv));
        $this->assertSame('言語設', $conv);
        $conv = core_text::str_max_bytes($str, 8);
        $this->assertEquals(6, strlen($conv));
        $this->assertSame('言語', $conv);

        // Now try a mixed byte string.
        $str = '言語設a定';

        $this->assertEquals(13, strlen($str));

        $conv = core_text::str_max_bytes($str, 11);
        $this->assertEquals(10, strlen($conv));
        $this->assertSame('言語設a', $conv);
        $conv = core_text::str_max_bytes($str, 10);
        $this->assertEquals(10, strlen($conv));
        $this->assertSame('言語設a', $conv);
        $conv = core_text::str_max_bytes($str, 9);
        $this->assertEquals(9, strlen($conv));
        $this->assertSame('言語設', $conv);
        $conv = core_text::str_max_bytes($str, 8);
        $this->assertEquals(6, strlen($conv));
        $this->assertSame('言語', $conv);

        // Test 0 byte case.
        $conv = core_text::str_max_bytes($str, 0);
        $this->assertEquals(0, strlen($conv));
        $this->assertSame('', $conv);

        // Check that null argument is allowed.
        $this->assertSame('', core_text::str_max_bytes(null, 1));
    }

    /**
     * Tests the static strtolower method.
     *
     * @covers ::strtolower()
     */
    public function test_strtolower(): void {
        $str = "Žluťoučký koníček";
        $low = 'žluťoučký koníček';
        $this->assertSame($low, core_text::strtolower($str));

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::convert($low, 'utf-8', 'iso-8859-2'), core_text::strtolower($iso2, 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::convert($low, 'utf-8', 'cp1250'), core_text::strtolower($win, 'cp1250'));

        $str = '言語設定';
        $this->assertSame($str, core_text::strtolower($str));

        $str = '简体中文';
        $this->assertSame($str, core_text::strtolower($str));

        $str = pack("H*", "1b24423840386c405f446a1b2842"); // ISO-2022-JP
        $this->assertSame($str, core_text::strtolower($str, 'ISO-2022-JP'));

        $str = pack("H*", "8cbe8cea90dd92e8"); // SHIFT-JIS
        $this->assertSame($str, core_text::strtolower($str, 'SHIFT-JIS'));

        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB2312
        $this->assertSame($str, core_text::strtolower($str, 'GB2312'));

        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB18030
        $this->assertSame($str, core_text::strtolower($str, 'GB18030'));

        $str = 1309528800;
        $this->assertSame((string)$str, core_text::strtolower($str));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::strtolower(null));
        $this->assertSame('', core_text::strtolower(null, 'cp1250'));
    }

    /**
     * Tests the static strtoupper.
     *
     * @covers ::strtoupper()
     */
    public function test_strtoupper(): void {
        $str = "Žluťoučký koníček";
        $up  = 'ŽLUŤOUČKÝ KONÍČEK';
        $this->assertSame($up, core_text::strtoupper($str));

        $iso2 = pack("H*", "ae6c75bb6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::convert($up, 'utf-8', 'iso-8859-2'), core_text::strtoupper($iso2, 'iso-8859-2'));

        $win  = pack("H*", "8e6c759d6f75e86bfd206b6f6eede8656b");
        $this->assertSame(core_text::convert($up, 'utf-8', 'cp1250'), core_text::strtoupper($win, 'cp1250'));

        $str = '言語設定';
        $this->assertSame($str, core_text::strtoupper($str));

        $str = '简体中文';
        $this->assertSame($str, core_text::strtoupper($str));

        $str = pack("H*", "1b24423840386c405f446a1b2842"); // ISO-2022-JP
        $this->assertSame($str, core_text::strtoupper($str, 'ISO-2022-JP'));

        $str = pack("H*", "8cbe8cea90dd92e8"); // SHIFT-JIS
        $this->assertSame($str, core_text::strtoupper($str, 'SHIFT-JIS'));

        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB2312
        $this->assertSame($str, core_text::strtoupper($str, 'GB2312'));

        $str = pack("H*", "bcf2cce5d6d0cec4"); // GB18030
        $this->assertSame($str, core_text::strtoupper($str, 'GB18030'));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::strtoupper(null));
        $this->assertSame('', core_text::strtoupper(null, 'cp1250'));
    }

    /**
     * Test the strrev method.
     *
     * @covers ::strrev()
     */
    public function test_strrev(): void {
        $strings = array(
            "Žluťoučký koníček" => "kečínok ýkčuoťulŽ",
            'ŽLUŤOUČKÝ KONÍČEK' => "KEČÍNOK ÝKČUOŤULŽ",
            '言語設定' => '定設語言',
            '简体中文' => '文中体简',
            "Der eine stößt den Speer zum Mann" => "nnaM muz reepS ned tßöts enie reD"
        );
        foreach ($strings as $before => $after) {
            // Make sure we can reverse it both ways and that it comes out the same.
            $this->assertSame($after, core_text::strrev($before));
            $this->assertSame($before, core_text::strrev($after));
            // Reverse it twice to be doubly sure.
            $this->assertSame($after, core_text::strrev(core_text::strrev($after)));
        }

        // Check that null argument is allowed.
        $this->assertSame('', core_text::strrev(null));
    }

    /**
     * Tests the static strpos method.
     *
     * @covers ::strpos()
     */
    public function test_strpos(): void {
        $str = "Žluťoučký koníček";
        $this->assertSame(10, core_text::strpos($str, 'koníč'));

        // Check that null argument is allowed.
        $this->assertSame(false, core_text::strpos(null, 'a'));
    }

    /**
     * Tests the static strrpos.
     *
     * @covers ::strrpos()
     */
    public function test_strrpos(): void {
        $str = "Žluťoučký koníček";
        $this->assertSame(11, core_text::strrpos($str, 'o'));

        // Check that null argument is allowed.
        $this->assertSame(false, core_text::strrpos(null, 'o'));
    }

    /**
     * Tests the static specialtoascii method.
     *
     * @covers ::specialtoascii()
     */
    public function test_specialtoascii(): void {
        $str = "Žluťoučký koníček";
        $this->assertSame('Zlutoucky konicek', core_text::specialtoascii($str));

        $utf8 = "Der eine stößt den Speer zum Mann";
        $iso1 = core_text::convert($utf8, 'utf-8', 'iso-8859-1');
        $this->assertSame('Der eine stosst den Speer zum Mann', core_text::specialtoascii($utf8));
        $this->assertSame('Der eine stosst den Speer zum Mann', core_text::specialtoascii($iso1, 'iso-8859-1'));

        $str = 'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ';
        $this->assertSame('aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY', core_text::specialtoascii($str));

        $utf8 = 'A æ Übérmensch på høyeste nivå! И я люблю PHP! есть. ﬁ';
        $this->assertSame('A ae Ubermensch pa hoyeste niva! I a lublu PHP! est\'. fi', core_text::specialtoascii($utf8, 'utf8'));

        $utf8 = 'キャンパス Αλφαβητικός Κατάλογος Лорем ипсум долор сит амет';
        $this->assertSame('kyanpasu Alphabetikos Katalogos Lorem ipsum dolor sit amet', core_text::specialtoascii($utf8));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::specialtoascii(null));
        $this->assertSame('', core_text::specialtoascii(null, 'ascii'));
    }

    /**
     * Tests the static encode_mimeheader method.
     *
     * @covers ::encode_mimeheader()
     * @covers \moodle_phpmailer::encodeHeader()
     */
    public function test_encode_mimeheader(): void {
        global $CFG;
        require_once($CFG->libdir.'/phpmailer/moodle_phpmailer.php');
        $mailer = new moodle_phpmailer();

        // Encode short string with non-latin characters.
        $str = "Žluťoučký koníček";
        $encodedstr = '=?utf-8?B?xb1sdcWlb3XEjWvDvSBrb27DrcSNZWs=?=';
        $this->assertSame($encodedstr, core_text::encode_mimeheader($str));
        $this->assertSame($encodedstr, $mailer->encodeHeader($str));
        $this->assertSame('"' . $encodedstr . '"', $mailer->encodeHeader($str, 'phrase'));

        // Encode short string without non-latin characters. Make sure the quotes are escaped in quoted email headers.
        $latinstr = 'text"with quotes';
        $this->assertSame($latinstr, core_text::encode_mimeheader($latinstr));
        $this->assertSame($latinstr, $mailer->encodeHeader($latinstr));
        $this->assertSame('"text\\"with quotes"', $mailer->encodeHeader($latinstr, 'phrase'));

        // Encode long string without non-latin characters.
        $longlatinstr = 'This is a very long text that still should not be split into several lines in the email headers because '.
            'it does not have any non-latin characters. The "quotes" and \\backslashes should be escaped only if it\'s a part of email address';
        $this->assertSame($longlatinstr, core_text::encode_mimeheader($longlatinstr));
        $this->assertSame($longlatinstr, $mailer->encodeHeader($longlatinstr));
        $longlatinstrwithslash = preg_replace(['/\\\\/', "/\"/"], ['\\\\\\', '\\"'], $longlatinstr);
        $this->assertSame('"' . $longlatinstrwithslash . '"', $mailer->encodeHeader($longlatinstr, 'phrase'));

        // Encode long string with non-latin characters.
        $longstr = "Неопознанная ошибка в файле C:\\tmp\\: \"Не пользуйтесь виндоуз\"";
        $encodedlongstr = "=?utf-8?B?0J3QtdC+0L/QvtC30L3QsNC90L3QsNGPINC+0YjQuNCx0LrQsCDQsiDRhNCw?=
 =?utf-8?B?0LnQu9C1IEM6XHRtcFw6ICLQndC1INC/0L7Qu9GM0LfRg9C50YLQtdGB?=
 =?utf-8?B?0Ywg0LLQuNC90LTQvtGD0Lci?=";
        $this->assertSame($encodedlongstr, $mailer->encodeHeader($longstr));
        $this->assertSame('"' . $encodedlongstr . '"', $mailer->encodeHeader($longstr, 'phrase'));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::encode_mimeheader(null));
    }

    /**
     * Tests the static entities_to_utf8 method.
     *
     * @covers ::entities_to_utf8()
     */
    public function test_entities_to_utf8(): void {
        $str = "&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&iacute;&#269;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("Žluťoučký koníček©\"&<>§«", core_text::entities_to_utf8($str));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::entities_to_utf8(null));
    }

    /**
     * Tests the static utf8_to_entities method.
     *
     * @covers ::utf8_to_entities()
     */
    public function test_utf8_to_entities(): void {
        $str = "&#x17d;luťoučký kon&iacute;ček&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&iacute;&#x10d;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;", core_text::utf8_to_entities($str));
        $this->assertSame("&#381;lu&#357;ou&#269;k&#253; kon&iacute;&#269;ek&copy;&quot;&amp;&lt;&gt;&sect;&laquo;", core_text::utf8_to_entities($str, true));

        $str = "&#381;luťoučký kon&iacute;ček&copy;&quot;&amp;&lt;&gt;&sect;&laquo;";
        $this->assertSame("&#x17d;lu&#x165;ou&#x10d;k&#xfd; kon&#xed;&#x10d;ek&#xa9;\"&<>&#xa7;&#xab;", core_text::utf8_to_entities($str, false, true));
        $this->assertSame("&#381;lu&#357;ou&#269;k&#253; kon&#237;&#269;ek&#169;\"&<>&#167;&#171;", core_text::utf8_to_entities($str, true, true));

        // Check that null argument is allowed.
        $this->assertSame('', core_text::utf8_to_entities(null));
        $this->assertSame('', core_text::utf8_to_entities(null, true));
    }

    /**
     * Tests the static trim_utf8_bom method.
     *
     * @covers ::trim_utf8_bom()
     */
    public function test_trim_utf8_bom(): void {
        $bom = "\xef\xbb\xbf";
        $str = "Žluťoučký koníček";
        $this->assertSame($str.$bom, core_text::trim_utf8_bom($bom.$str.$bom));

        // Check that null argument is allowed.
        $this->assertSame(null, core_text::trim_utf8_bom(null));
    }

    /**
     * Tests the static remove_unicode_non_characters method.
     *
     * @covers ::remove_unicode_non_characters()
     */
    public function test_remove_unicode_non_characters(): void {
        // Confirm that texts which don't contain these characters are unchanged.
        $this->assertSame('Frogs!', core_text::remove_unicode_non_characters('Frogs!'));

        // Even if they contain some very scary characters.
        $example = html_entity_decode('A&#xfffd;&#x1d15f;B', ENT_COMPAT);
        $this->assertSame($example, core_text::remove_unicode_non_characters($example));

        // Non-characters are removed wherever they may be, with other characters left.
        $example = html_entity_decode('&#xfffe;A&#xffff;B&#x8fffe;C&#xfdd0;D&#xfffd;E&#xfdd5;', ENT_COMPAT);
        $expected = html_entity_decode('ABCD&#xfffd;E', ENT_COMPAT);
        $this->assertSame($expected, core_text::remove_unicode_non_characters($example));

        // If you only have a non-character, you get empty string.
        $example = html_entity_decode('&#xfffe;', ENT_COMPAT);
        $this->assertSame('', core_text::remove_unicode_non_characters($example));

        // Check that null argument is allowed.
        $this->assertSame(null, core_text::trim_utf8_bom(null));
    }

    /**
     * Tests the static get_encodings method.
     *
     * @covers ::get_encodings()
     */
    public function test_get_encodings(): void {
        $encodings = core_text::get_encodings();
        $this->assertTrue(is_array($encodings));
        $this->assertTrue(count($encodings) > 1);
        $this->assertTrue(isset($encodings['UTF-8']));
    }

    /**
     * Tests the static code2utf8 method.
     *
     * @covers ::code2utf8()
     */
    public function test_code2utf8(): void {
        $this->assertSame('Ž', core_text::code2utf8(381));
    }

    /**
     * Tests the static utf8ord method.
     *
     * @covers ::utf8ord()
     */
    public function test_utf8ord(): void {
        $this->assertSame(ord(''), core_text::utf8ord(''));
        $this->assertSame(ord('f'), core_text::utf8ord('f'));
        $this->assertSame(0x03B1, core_text::utf8ord('α'));
        $this->assertSame(0x0439, core_text::utf8ord('й'));
        $this->assertSame(0x2FA1F, core_text::utf8ord('𯨟'));
        $this->assertSame(381, core_text::utf8ord('Ž'));

        // Check that null argument is allowed.
        $this->assertSame(ord(''), core_text::utf8ord(null));
    }

    /**
     * Tests the static strtotitle method.
     *
     * @covers ::strtotitle()
     */
    public function test_strtotitle(): void {
        $str = "žluťoučký koníček";
        $this->assertSame("Žluťoučký Koníček", core_text::strtotitle($str));

        // Check that null argument is allowed.
        $this->assertSame(null, core_text::strtotitle(null));
    }

    /**
     * Test strrchr.
     *
     * @covers ::strrchr()
     */
    public function test_strrchr(): void {
        $str = "Žluťoučký koníček";
        $this->assertSame('koníček', core_text::strrchr($str, 'koní'));
        $this->assertSame('Žluťoučký ', core_text::strrchr($str, 'koní', true));
        $this->assertFalse(core_text::strrchr($str, 'A'));
        $this->assertFalse(core_text::strrchr($str, 'ç', true));

        // Check that null argument is allowed.
        $this->assertSame(false, core_text::strrchr(null, 'o'));
    }

    /**
     * Tests the static checker is_charset_supported
     *
     * @dataProvider is_charset_supported_provider
     * @param string $charset
     * @param bool $expected
     * @covers ::is_charset_supported()
     */
    public function test_is_charset_supported(string $charset, bool $expected): void {
        $charset = core_text::parse_charset($charset);
        $this->assertEquals($expected, core_text::is_charset_supported($charset));
    }

    /**
     * Provider for the test_is_charset_supported()
     * @return array[]
     */
    public static function is_charset_supported_provider(): array {
        return [
            "Check unsupported windows charset" => [
                "cp1250", false
            ],
            "Check supported windows charset" => [
                "cp1252", true
            ]
        ];
    }
}
