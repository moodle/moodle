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
 * Test markdown text format.
 *
 * This is not a complete markdown test, it just validates
 * Moodle integration works.
 *
 * See http://daringfireball.net/projects/markdown/basics
 * for more format information.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class markdown_test extends \basic_testcase {

    public function test_paragraphs() {
        $text = "one\n\ntwo";
        $result = "<p>one</p>\n\n<p>two</p>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_headings() {
        $text = "Header 1\n====================\n\n## Header 2";
        $result = "<h1>Header 1</h1>\n\n<h2>Header 2</h2>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_lists() {
        $text = "* one\n* two\n* three\n";
        $result = "<ul>\n<li>one</li>\n<li>two</li>\n<li>three</li>\n</ul>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_links() {
        $text = "some [example link](http://example.com/)";
        $result = "<p>some <a href=\"http://example.com/\">example link</a></p>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_tabs() {
        $text = "a\tbb\tccc\tя\tюэ\t水\tabcd\tabcde\tabcdef";
        $result = "<p>a   bb  ccc я   юэ  水   abcd    abcde   abcdef</p>\n";
        $this->assertSame($result, markdown_to_html($text));
    }
}
