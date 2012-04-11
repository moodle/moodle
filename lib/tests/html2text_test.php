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
 * Tests our html2text hacks
 *
 * Note: includes original tests from testweblib.php
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


class html2text_testcase extends basic_testcase {

    /**
     * ALT as image replacements
     */
    public function test_images() {
        $this->assertEquals('[edit]', html_to_text('<img src="edit.png" alt="edit" />'));

        $text = 'xx<img src="gif.gif" alt="some gif" />xx';
        $result = html_to_text($text, null, false, false);
        $this->assertSame($result, 'xx[some gif]xx');
    }

    /**
     * No magic quotes messing
     */
    public function test_no_strip_slashes() {
        $this->assertEquals('[\edit]', html_to_text('<img src="edit.png" alt="\edit" />'));

        $text = '\\magic\\quotes\\are\\\\horrible';
        $result = html_to_text($text, null, false, false);
        $this->assertSame($result, $text);
    }

    /**
     * Textlib integration
     */
    public function test_textlib() {
        $text = '<strong>Žluťoučký koníček</strong>';
        $result = html_to_text($text, null, false, false);
        $this->assertSame($result, 'ŽLUŤOUČKÝ KONÍČEK');
    }

    /**
     * Protect 0
     */
    public function test_zero() {
        $text = '0';
        $result = html_to_text($text, null, false, false);
        $this->assertSame($result, $text);

        $this->assertSame('0', html_to_text('0'));
    }

    // ======= Standard html2text conversion features =======

    /**
     * Various invalid HTML typed by users that ignore html strict
     **/
    public function test_invalid_html() {
        $text = 'Gin & Tonic';
        $result = html_to_text($text, null, false, false);
        $this->assertSame($result, $text);

        $text = 'Gin > Tonic';
        $result = html_to_text($text, null, false, false);
        $this->assertSame($result, $text);

        $text = 'Gin < Tonic';
        $result = html_to_text($text, null, false, false);
        $this->assertSame($result, $text);
    }

    /**
     * Basic text formatting.
     */
    public function test_simple() {
        $this->assertEquals("_Hello_ WORLD!", html_to_text('<p><i>Hello</i> <b>world</b>!</p>'));
        $this->assertEquals("All the WORLD’S a stage.\n\n-- William Shakespeare", html_to_text('<p>All the <strong>world’s</strong> a stage.</p><p>-- William Shakespeare</p>'));
        $this->assertEquals("HELLO WORLD!\n\n", html_to_text('<h1>Hello world!</h1>'));
        $this->assertEquals("Hello\nworld!", html_to_text('Hello<br />world!'));
    }

    /**
     * Test line wrapping
     */
    public function test_text_nowrap() {
        $long = "Here is a long string, more than 75 characters long, since by default html_to_text wraps text at 75 chars.";
        $wrapped = "Here is a long string, more than 75 characters long, since by default\nhtml_to_text wraps text at 75 chars.";
        $this->assertEquals($long, html_to_text($long, 0));
        $this->assertEquals($wrapped, html_to_text($long));
    }

    /**
     * Whitespace removal
     */
    public function test_trailing_whitespace() {
        $this->assertEquals('With trailing whitespace and some more text', html_to_text("With trailing whitespace   \nand some   more text", 0));
    }

    /**
     * PRE parsing
     */
    public function test_html_to_text_pre_parsing_problem() {
        $strorig = 'Consider the following function:<br /><pre><span style="color: rgb(153, 51, 102);">void FillMeUp(char* in_string) {'.
            '<br />  int i = 0;<br />  while (in_string[i] != \'\0\') {<br />    in_string[i] = \'X\';<br />    i++;<br />  }<br />'.
            '}</span></pre>What would happen if a non-terminated string were input to this function?<br /><br />';

        $strconv = 'Consider the following function:

void FillMeUp(char* in_string) {
 int i = 0;
 while (in_string[i] != \'\0\') {
 in_string[i] = \'X\';
 i++;
 }
}
What would happen if a non-terminated string were input to this function?

';

        $this->assertSame($strconv, html_to_text($strorig));
    }
}

