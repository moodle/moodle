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
 * Tests for Moodle's String Formatter.
 *
 * @package   core
 * @copyright 2023 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\formatting
 * @coversDefaultClass \core\formatting
 */
class formatting_test extends \advanced_testcase {
    /**
     * @covers ::format_string
     */
    public function test_format_string_striptags(): void {
        global $CFG;

        $this->resetAfterTest();

        $formatting = new formatting();

        // < and > signs.
        $CFG->formatstringstriptags = false;
        $this->assertSame('x &lt; 1', $formatting->format_string('x < 1'));
        $this->assertSame('x &gt; 1', $formatting->format_string('x > 1'));
        $this->assertSame('x &lt; 1 and x &gt; 0', $formatting->format_string('x < 1 and x > 0'));

        $CFG->formatstringstriptags = true;
        $this->assertSame('x &lt; 1', $formatting->format_string('x < 1'));
        $this->assertSame('x &gt; 1', $formatting->format_string('x > 1'));
        $this->assertSame('x &lt; 1 and x &gt; 0', $formatting->format_string('x < 1 and x > 0'));
    }

    /**
     * @covers ::format_string
     * @dataProvider format_string_provider
     * @param string $expected
     * @param mixed $input
     * @param array $options
     */
    public function test_format_string_values(
        string $expected,
        mixed $input,
        array $options = [],
    ): void {
        $formatting = new formatting();
        $this->assertSame(
            $expected,
            $formatting->format_string($input, ...$options),
        );
    }

    /**
     * Data provider for format_string tests.
     *
     * @return array
     */
    public static function format_string_provider(): array {
        return [
            // Ampersands.
            ["&amp; &amp;&amp;&amp;&amp;&amp; &amp;&amp;", "& &&&&& &&"],
            ["ANother &amp; &amp;&amp;&amp;&amp;&amp; Category", "ANother & &&&&& Category"],
            ["ANother &amp; &amp;&amp;&amp;&amp;&amp; Category", "ANother & &&&&& Category", [true]],
            ["Nick's Test Site &amp; Other things", "Nick's Test Site & Other things", [true]],
            ["& < > \" '", "& < > \" '", [true, ['escape' => false]]],

            // String entities.
            ["&quot;", "&quot;"],

            // Digital entities.
            ["&11234;", "&11234;"],

            // Unicode entities.
            ["&#4475;", "&#4475;"],

            // Nulls.
            ['', null],
            ['', null, [true, ['escape' => false]]],
        ];
    }

    /**
     * The format string static caching should include the filters option to make
     * sure filters are correctly applied when requested.
     */
    public function test_format_string_static_caching_with_filters(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();

        $rawstring = '<span lang="en" class="multilang">English</span><span lang="ca" class="multilang">Catalan</span>';
        $expectednofilter = strip_tags($rawstring);
        $expectedfilter = 'English';
        $striplinks = true;
        $context = \core\context\course::instance($course->id);
        $options = [
            'context' => $context,
            'escape' => true,
            'filter' => false,
        ];

        $this->setUser($user);

        $formatting = new formatting();

        // Format the string without filters. It should just strip the
        // links.
        $nofilterresult = $formatting->format_string($rawstring, $striplinks, $options);
        $this->assertEquals($expectednofilter, $nofilterresult);

        // Add the multilang filter. Make sure it's enabled globally.
        $CFG->filterall = true;
        $CFG->stringfilters = 'multilang';
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_local_state('multilang', $context->id, TEXTFILTER_ON);
        // This time we want to apply the filters.
        $options['filter'] = true;
        $filterresult = $formatting->format_string($rawstring, $striplinks, $options);
        $this->assertMatchesRegularExpression("/$expectedfilter/", $filterresult);

        filter_set_local_state('multilang', $context->id, TEXTFILTER_OFF);

        // Confirm that we get back the cached string. The result should be
        // the same as the filtered text above even though we've disabled the
        // multilang filter in between.
        $cachedresult = $formatting->format_string($rawstring, $striplinks, $options);
        $this->assertMatchesRegularExpression("/$expectedfilter/", $cachedresult);
    }
}
