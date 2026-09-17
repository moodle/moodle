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

use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the markdown_extra class.
 *
 * This only covers changes made by the markdown_extra subclass.
 *
 * @package    core
 * @category   test
 * @copyright  2026 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversMethod(markdown_extra::class, '_doFencedCodeBlocks_callback')]
final class markdown_extra_test extends \advanced_testcase {
    /**
     * Test that fenced code blocks without a language token are normalised to the "none" language.
     *
     * @param string $input The markdown source.
     * @param string $expected The expected HTML output.
     */
    #[DataProvider('transform_provider')]
    public function test_transform(string $input, string $expected): void {
        $parser = \core\di::get(markdown_extra::class);
        $this->assertSame($expected, $parser->transform($input));
    }

    /**
     * Data provider for {@see test_transform()}.
     *
     * @return array[]
     */
    public static function transform_provider(): array {
        $backticks = "\u{0060}\u{0060}\u{0060}";

        return [
            'fenced_block_with_language' => [
                'input' => "{$backticks}php\necho 1;\n{$backticks}",
                'expected' => "<pre><code class=\"php\">echo 1;\n</code></pre>\n",
            ],
            'fenced_block_without_language' => [
                'input' => "{$backticks}\nplain code\n{$backticks}",
                'expected' => "<pre><code class=\"none\">plain code\n</code></pre>\n",
            ],
            'fenced_block_with_tilde_fence_and_without_language' => [
                'input' => "~~~\nplain code\n~~~",
                'expected' => "<pre><code class=\"none\">plain code\n</code></pre>\n",
            ],
            'empty_fenced_block_without_language' => [
                'input' => "{$backticks}\n\n{$backticks}",
                'expected' => "<pre><code class=\"none\"><br /></code></pre>\n",
            ],
        ];
    }
}
