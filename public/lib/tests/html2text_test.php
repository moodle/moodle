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

namespace core;

/**
 * Tests our html2text hacks
 *
 * Note: includes original tests from testweblib.php
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::html_to_text
 */
final class html2text_test extends \basic_testcase {
    /**
     * Data provider for general tests.
     *
     * @return array
     */
    public static function examples_provider(): array {
        // Used in the line wrapping tests.
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $long = "Here is a long string, more than 75 characters long, since by default html_to_text wraps text at 75 chars.";
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $wrapped = "Here is a long string, more than 75 characters long, since by default\nhtml_to_text wraps text at 75 chars.";

        // These two are used in the PRE parsing tests.
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $strorig = 'Consider the following function:<br /><pre><span style="color: rgb(153, 51, 102);">void FillMeUp(char* in_string) {'.
            '<br />  int i = 0;<br />  while (in_string[i] != \'\0\') {<br />    in_string[i] = \'X\';<br />    i++;<br />  }<br />'.
            '}</span></pre>What would happen if a non-terminated string were input to this function?<br /><br />';

        // Note, the spaces in the <pre> section are Unicode NBSPs - they may not be displayed in your editor.
        $strconv = <<<EOF
        Consider the following function:

        void FillMeUp(char* in_string) {
          int i = 0;
          while (in_string[i] != '\\0') {
            in_string[i] = 'X';
            i++;
          }
        }
        What would happen if a non-terminated string were input to this function?


        EOF;

        return [
            // Image alt tag replacements.
            'Image alt tag' => [
                '[edit]',
                [],
                '<img src="edit.png" alt="edit" />',
            ],
            'Image alt tag between strings' => [
                'xx[some gif]xx',
                [
                    'dolinks' => false,
                ],
                'xx<img src="gif.gif" alt="some gif" />xx',
            ],
            'core_text integration' => [
                'ŽLUŤOUČKÝ KONÍČEK',
                ['dolinks' => false],
                '<strong>Žluťoučký koníček</strong>',
            ],
            'No strip slashes in a tag' => [
                '[\edit]',
                [],
                '<img src="edit.png" alt="\edit" />',
            ],
            'No strip slashes in a string' => [
                '\\magic\\quotes\\are\\\\horrible',
                [],
                '\\magic\\quotes\\are\\\\horrible',
            ],
            'Protect "0"' => [
                '0',
                ['dolinks' => false],
                '0',
            ],
            'Invalid HTML 1' => [
                'Gin & Tonic',
                [],
                'Gin & Tonic',
            ],
            'Invalid HTML 2' => [
                'Gin > Tonic',
                [],
                'Gin > Tonic',
            ],
            'Invalid HTML 3' => [
                'Gin < Tonic',
                [],
                'Gin < Tonic',
            ],
            'Simple test 1' => [
                "_Hello_ WORLD!\n",
                [],
                '<p><i>Hello</i> <b>world</b>!</p>',
            ],
            'Simple test 2' => [
                "All the WORLD’S a stage.\n\n-- William Shakespeare\n",
                [],
                '<p>All the <strong>world’s</strong> a stage.</p><p>-- William Shakespeare</p>',
            ],
            'Simple test 3' => [
                "HELLO WORLD!\n\n",
                [],
                '<h1>Hello world!</h1>',
            ],
            'Simple test 4' => [
                "Hello\nworld!",
                [],
                'Hello<br />world!',
            ],
            'No wrapping when width set to 0' => [
                $long,
                ['width' => 0],
                $long,
            ],
            'Wrapping when width set to default' => [
                $wrapped,
                [],
                $long,
            ],
            'Trailing whitespace removal' => [
                'With trailing whitespace and some more text',
                [],
                "With trailing whitespace   \nand some   more text",
            ],
            'PRE parsing' => [
                $strconv,
                [],
                $strorig,
            ],
            'Strip script tags' => [
                'Interesting text',
                [],
                'Interesting <script type="text/javascript">var what_a_mess = "Yuck!";</script> text',
            ],
            'Trailing spaces before newline or tab' => [
                "Some text with trailing space\n\nAnd some more text\n",
                [],
                '<p>Some text with trailing space </p> <p>And some more text</p>',
            ],
            'Trailing spaces before newline or tab (list)' => [
                "\t* Some text with trailing space\n\t* And some more text\n\n",
                [],
                '<ul><li>Some text with trailing space </li> <li> And some more text </li> </ul>',
            ],
        ];
    }

    /**
     * Test html2text with various examples.
     *
     * @dataProvider examples_provider
     * @param string $expected
     * @param array $options
     * @param string $html
     */
    public function test_runner(
        string $expected,
        array $options,
        string $html,
    ): void {
        $this->assertSame($expected, html_to_text($html, ...$options));
    }

    /**
     * Test the links list enumeration.
     */
    public function test_build_link_list(): void {

        // Note the trailing whitespace left intentionally in the text after first link.
        $text = 'Total of <a title="List of integrated issues"
            href="http://tr.mdl.org/sh.jspa?r=1&j=p+%3D+%22I+d%22+%3D">     ' . '
            <strong>27 issues</strong></a> and <a href="http://another.url/?f=a&amp;b=2">some</a> other
have been fixed <strong><a href="http://third.url/view.php">last week</a></strong>';

        // Do not collect links.
        $result = html_to_text($text, 5000, false);
        $this->assertSame('Total of 27 ISSUES and some other have been fixed LAST WEEK', $result);

        // Collect and enumerate links.
        $result = html_to_text($text, 5000, true);
        $this->assertSame(0, strpos($result, 'Total of 27 ISSUES [1] and some [2] other have been fixed LAST WEEK [3]'));
        $this->assertSame(false, strpos($result, '[0]'));
        $this->assertSame(1, preg_match('|^'.preg_quote('[1] http://tr.mdl.org/sh.jspa?r=1&j=p+%3D+%22I+d%22+%3D').'$|m', $result));
        $this->assertSame(1, preg_match('|^'.preg_quote('[2] http://another.url/?f=a&amp;b=2').'$|m', $result));
        $this->assertSame(1, preg_match('|^'.preg_quote('[3] http://third.url/view.php').'$|m', $result));
        $this->assertSame(false, strpos($result, '[4]'));

        // Test multiple occurrences of the same URL.
        $text = '<p>See <a href="http://moodle.org">moodle.org</a>,
            <a href="http://www.google.fr">google</a>, <a href="http://www.univ-lemans.fr">univ-lemans</a>
            and <a href="http://www.google.fr">google</a>.
            Also try <a href="https://www.google.fr">google via HTTPS</a>.';
        $result = html_to_text($text, 5000, true);
        $this->assertSame(0, strpos($result, 'See moodle.org [1], google [2], univ-lemans [3] and google [2]. Also try google via HTTPS [4].'));
        $this->assertSame(false, strpos($result, '[0]'));
        $this->assertSame(1, preg_match('|^'.preg_quote('[1] http://moodle.org').'$|m', $result));
        $this->assertSame(1, preg_match('|^'.preg_quote('[2] http://www.google.fr').'$|m', $result));
        $this->assertSame(1, preg_match('|^'.preg_quote('[3] http://www.univ-lemans.fr').'$|m', $result));
        $this->assertSame(1, preg_match('|^'.preg_quote('[4] https://www.google.fr').'$|m', $result));
        $this->assertSame(false, strpos($result, '[5]'));
    }
}
