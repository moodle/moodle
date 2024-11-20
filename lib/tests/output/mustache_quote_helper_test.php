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

declare(strict_types=1);

namespace core\output;

/**
 * Unit tests for the mustache_quote_helper class.
 *
 * @package   core
 * @category  test
 * @copyright 2022 TU Berlin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\output\mustache_quote_helper
 */
class mustache_quote_helper_test extends \basic_testcase {

    /**
     * Tests the quote helper
     *
     * @covers ::quote
     */
    public function test_quote(): void {
        $engine = new \Mustache_Engine();
        $context = new \Mustache_Context([
            'world' => '{{planet}}',
            'planet' => '<earth>'
        ]);
        $lambdahelper = new \Mustache_LambdaHelper($engine, $context);

        $quotehelper = new mustache_quote_helper();

        // Simple string.
        $this->assertEquals('"Hello world!"', $quotehelper->quote('Hello world!', $lambdahelper));

        // Special JSON characters in string.
        $this->assertEquals(
            '"Hello \\"world\\"! (\\b,\\f,\\n,\\r,\\t,\\\\)"',
            $quotehelper->quote('Hello "world"! (' . chr(8) . ",\f,\n,\r,\t,\\)", $lambdahelper)
        );

        // Double curly braces in string.
        $this->assertEquals(
            '"Hello {{=<% %>=}}{{<%={{ }}=%>world{{=<% %>=}}}}<%={{ }}=%>!"',
            $quotehelper->quote('{{=<% %>=}}Hello {{world}}!<%={{ }}=%>', $lambdahelper)
        );

        // Triple curly braces in string.
        $this->assertEquals(
            '"Hello {{=<% %>=}}{{{<%={{ }}=%>world{{=<% %>=}}}}}<%={{ }}=%>!"',
            $quotehelper->quote('{{=<% %>=}}Hello {{{world}}}!<%={{ }}=%>', $lambdahelper)
        );

        // Variable interpolation with double braces.
        $this->assertEquals(
            '"Hello &lt;earth&gt;!"',
            $quotehelper->quote('Hello {{planet}}!', $lambdahelper)
        );

        // Variable interpolation with triple braces.
        $this->assertEquals(
            '"Hello <earth>!"',
            $quotehelper->quote('Hello {{{planet}}}!', $lambdahelper)
        );

        // Variables interpolated only once.
        $this->assertEquals(
            '"Hello {{=<% %>=}}{{<%={{ }}=%>planet{{=<% %>=}}}}<%={{ }}=%>!"',
            $quotehelper->quote('Hello {{world}}!', $lambdahelper)
        );
    }
}
