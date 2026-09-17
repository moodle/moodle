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
final class markdown_test extends \basic_testcase {

    public function test_paragraphs(): void {
        $text = "one\n\ntwo";
        $result = "<p>one</p>\n\n<p>two</p>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_headings(): void {
        $text = "Header 1\n====================\n\n## Header 2";
        $result = "<h1>Header 1</h1>\n\n<h2>Header 2</h2>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_lists(): void {
        $text = "* one\n* two\n* three\n";
        $result = "<ul>\n<li>one</li>\n<li>two</li>\n<li>three</li>\n</ul>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_links(): void {
        $text = "some [example link](http://example.com/)";
        $result = "<p>some <a href=\"http://example.com/\">example link</a></p>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    public function test_tabs(): void {
        $text = "a\tbb\tccc\tя\tюэ\t水\tabcd\tabcde\tabcdef";
        $result = "<p>a   bb  ccc я   юэ  水   abcd    abcde   abcdef</p>\n";
        $this->assertSame($result, markdown_to_html($text));
    }

    /**
     * Fenced code blocks must be rendered with the markup that
     * filter_codehighlighter (Prism.js) recognises: a "language-*" class on the
     * <pre> element followed by a bare <code>. The filter's trigger pattern is
     * /<pre.+?class=".*?language-.*?"><code>/i.
     *
     * @covers ::markdown_to_html
     */
    public function test_fenced_code_block_uses_language_class_on_pre(): void {
        $backticks = "\u{0060}\u{0060}\u{0060}";
        $text = "{$backticks}php\necho 'hi';\n{$backticks}";
        $output = markdown_to_html($text);

        $this->assertStringContainsString('<pre class="language-php"><code>', $output);
        $this->assertMatchesRegularExpression('/<pre.+?class=".*?language-.*?"><code>/i', $output);
    }

    /**
     * A fenced code block without a language token must be mapped to the generic
     * "language-none" class so filter_codehighlighter still styles it as a code
     * block. Prism has no grammar for "none", so it applies the generic
     * code-block styling without any language-specific highlighting.
     *
     * @covers ::markdown_to_html
     */
    public function test_fenced_code_block_without_language_maps_to_none(): void {
        $backticks = "\u{0060}\u{0060}\u{0060}";
        $text = "{$backticks}\nplain code\n{$backticks}";
        $output = markdown_to_html($text);

        $this->assertStringContainsString('<pre class="language-none"><code>', $output);
        $this->assertMatchesRegularExpression('/<pre.+?class=".*?language-.*?"><code>/i', $output);
        $this->assertStringNotContainsString('<pre><code>', $output);
    }

    /**
     * Existing raw HTML code blocks must not be rewritten. Only fenced blocks
     * without a language token should become "language-none".
     *
     * @covers ::markdown_to_html
     */
    public function test_existing_pre_code_markup_is_not_rewritten(): void {
        $backticks = "\u{0060}\u{0060}\u{0060}";
        $text = "<pre><code>raw html</code></pre>\n\n{$backticks}\nplain code\n{$backticks}";
        $output = markdown_to_html($text);

        $this->assertStringContainsString('<pre><code>raw html</code></pre>', $output);
        $this->assertStringContainsString('<pre class="language-none"><code>plain code', $output);
        $this->assertStringNotContainsString('<pre class="language-none"><code>raw html', $output);
    }
}
