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
final class formatting_test extends \advanced_testcase {
    /**
     * @covers ::format_string
     */
    public function test_format_string_striptags_cfg(): void {
        global $CFG;

        $this->resetAfterTest();

        $formatting = new formatting();

        // Check < and > signs.
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
     */
    public function test_format_string_striptags_prop(): void {
        $formatting = new formatting();

        // Check < and > signs.
        $formatting->set_striptags(false);
        $this->assertSame('x &lt; 1', $formatting->format_string('x < 1'));
        $this->assertSame('x &gt; 1', $formatting->format_string('x > 1'));
        $this->assertSame('x &lt; 1 and x &gt; 0', $formatting->format_string('x < 1 and x > 0'));

        $formatting->set_striptags(true);
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
        array $params,
    ): void {
        $formatting = new formatting();
        $this->assertSame(
            $expected,
            $formatting->format_string(...$params),
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
            [
                'expected' => "&amp; &amp;&amp;&amp;&amp;&amp; &amp;&amp;",
                'params' => ["& &&&&& &&"],
            ],
            [
                'expected' => "ANother &amp; &amp;&amp;&amp;&amp;&amp; Category",
                'params' => ["ANother & &&&&& Category"],
            ],
            [
                'expected' => "ANother &amp; &amp;&amp;&amp;&amp;&amp; Category",
                'params' => [
                    'string' => "ANother & &&&&& Category",
                    'striplinks' => true,
                ],
            ],
            [
                'expected' => "Nick's Test Site &amp; Other things",
                'params' => [
                    'string' => "Nick's Test Site & Other things",
                    'striplinks' => true,
                ],
            ],
            [
                'expected' => "& < > \" '",
                'params' => [
                    'string' => "& < > \" '",
                    'striplinks' => true,
                    'escape' => false,
                ],
            ],

            // String entities.
            [
                'expected' => "&quot;",
                'params' => ["&quot;"],
            ],

            // Digital entities.
            [
                'expected' => "&11234;",
                'params' => ["&11234;"],
            ],

            // Unicode entities.
            [
                'expected' => "&#4475;",
                'params' => ["&#4475;"],
            ],

            // Nulls.
            ['', [null]],
            [
                'expected' => '',
                'params' => [
                    'string' => null,
                    'striplinks' => true,
                    'escape' => false,
                ],
            ],
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
        $context = \core\context\course::instance($course->id);
        $options = [
            'striplinks' => true,
            'context' => $context,
            'escape' => true,
            'filter' => false,
        ];

        $this->setUser($user);

        $formatting = new formatting();

        // Format the string without filters. It should just strip the
        // links.
        $nofilterresult = $formatting->format_string($rawstring, ...$options);
        $this->assertEquals($expectednofilter, $nofilterresult);

        // Add the multilang filter. Make sure it's enabled globally.
        $CFG->stringfilters = 'multilang';
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_local_state('multilang', $context->id, TEXTFILTER_ON);

        // Even after setting the filters, no filters are applied yet.
        $nofilterresult = $formatting->format_string($rawstring,...$options);
        $this->assertEquals($expectednofilter, $nofilterresult);

        // Apply the filter as an option.
        $options['filter'] = true;
        $filterresult = $formatting->format_string($rawstring,  ...$options);
        $this->assertMatchesRegularExpression("/$expectedfilter/", $filterresult);

        // Apply it as a formatting setting.
        unset($options['filter']);
        $formatting->set_filterall(true);
        $filterresult = $formatting->format_string($rawstring,  ...$options);
        $this->assertMatchesRegularExpression("/$expectedfilter/", $filterresult);

        // Unset it and we do not filter.
        $formatting->set_filterall(false);
        $nofilterresult = $formatting->format_string($rawstring,  ...$options);
        $this->assertEquals($expectednofilter, $nofilterresult);

        // Set it again.
        $formatting->set_filterall(true);
        filter_set_local_state('multilang', $context->id, TEXTFILTER_OFF);

        // Confirm that we get back the cached string. The result should be
        // the same as the filtered text above even though we've disabled the
        // multilang filter in between.
        $cachedresult = $formatting->format_string($rawstring, ...$options);
        $this->assertMatchesRegularExpression("/$expectedfilter/", $cachedresult);
    }

    /**
     * Test trust option of format_text().
     *
     * @covers ::format_text
     * @dataProvider format_text_trusted_provider
     */
    public function test_format_text_trusted(
        $expected,
        int $enabletrusttext,
        mixed $input,
        // Yes... FORMAT_ constants are strings of ints.
        string $format,
        array $options = [],
    ): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->enabletrusttext = $enabletrusttext;

        $formatter = new formatting();
        $this->assertEquals(
            $expected,
            $formatter->format_text($input, $format, ...$options),
        );
    }

    public static function format_text_trusted_provider(): array {
        $text = "lala <object>xx</object>";
        return [
            [
                s($text),
                0,
                $text,
                FORMAT_PLAIN,
                ['trusted' => true],
            ],
            [
                "<p>lala xx</p>\n",
                0,
                $text,
                FORMAT_MARKDOWN,
                ['trusted' => true],
            ],
            [
                '<div class="text_to_html">lala xx</div>',
                0,
                $text,
                FORMAT_MOODLE,
                ['trusted' => true],
            ],
            [
                'lala xx',
                0,
                $text,
                FORMAT_HTML,
                ['trusted' => true],
            ],

            [
                s($text),
                0,
                $text,
                FORMAT_PLAIN,
                ['trusted' => false],
            ],
            [
                "<p>lala xx</p>\n",
                0,
                $text,
                FORMAT_MARKDOWN,
                ['trusted' => false],
            ],
            [
                '<div class="text_to_html">lala xx</div>',
                0,
                $text,
                FORMAT_MOODLE,
                ['trusted' => false],
            ],
            [
                'lala xx',
                0,
                $text,
                FORMAT_HTML,
                ['trusted' => false],
            ],

            [
                s($text),
                1,
                $text,
                FORMAT_PLAIN,
                ['trusted' => true],
            ],
            [
                "<p>lala xx</p>\n",
                1,
                $text,
                FORMAT_MARKDOWN,
                ['trusted' => true],
            ],
            [
                '<div class="text_to_html">lala <object>xx</object></div>',
                1,
                $text,
                FORMAT_MOODLE,
                ['trusted' => true],
            ],
            [
                'lala <object>xx</object>',
                1,
                $text,
                FORMAT_HTML,
                ['trusted' => true],
            ],

            [
                s($text),
                1,
                $text,
                FORMAT_PLAIN,
                ['trusted' => false],
            ],
            [
                "<p>lala xx</p>\n",
                1,
                $text,
                FORMAT_MARKDOWN,
                ['trusted' => false],
            ],
            [
                '<div class="text_to_html">lala xx</div>',
                1,
                $text,
                FORMAT_MOODLE,
                ['trusted' => false],
            ],
            [
                'lala xx',
                1,
                $text,
                FORMAT_HTML,
                ['trusted' => false],
            ],

            [
                "<p>lala <object>xx</object></p>\n",
                1,
                $text,
                FORMAT_MARKDOWN,
                ['trusted' => true, 'clean' => false],
            ],
            [
                "<p>lala <object>xx</object></p>\n",
                1,
                $text,
                FORMAT_MARKDOWN,
                ['trusted' => false, 'clean' => false],
            ],
        ];
    }

    public function test_format_text_format_html(): void {
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertMatchesRegularExpression(
            '~^<p><img class="icon emoticon" alt="smile" title="smile" ' .
                'src="https://www.example.com/moodle/theme/image.php/boost/core/1/s/smiley" /></p>$~',
            $formatter->format_text('<p>:-)</p>', FORMAT_HTML)
        );
    }

    public function test_format_text_format_html_no_filters(): void {
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals(
            '<p>:-)</p>',
            $formatter->format_text(
                '<p>:-)</p>',
                FORMAT_HTML,
                filter: false,
            )
        );
    }

    public function test_format_text_format_plain(): void {
        // Note FORMAT_PLAIN does not filter ever, no matter we ask for filtering.
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals(
            ':-)',
            $formatter->format_text(':-)', FORMAT_PLAIN)
        );
    }

    public function test_format_text_format_plain_no_filters(): void {
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals(
            ':-)',
            $formatter->format_text(
                ':-)',
                FORMAT_PLAIN,
                filter: false,
            )
        );
    }

    public function test_format_text_format_markdown(): void {
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertMatchesRegularExpression(
            '~^<p><em><img class="icon emoticon" alt="smile" title="smile" ' .
                'src="https://www.example.com/moodle/theme/image.php/boost/core/1/s/smiley" />' .
                '</em></p>\n$~',
            $formatter->format_text('*:-)*', FORMAT_MARKDOWN)
        );
    }

    public function test_format_text_format_markdown_nofilter(): void {
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals(
            "<p><em>:-)</em></p>\n",
            $formatter->format_text('*:-)*', FORMAT_MARKDOWN, filter: false)
        );
    }

    public function test_format_text_format_moodle(): void {
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertMatchesRegularExpression(
            '~^<div class="text_to_html"><p>' .
                '<img class="icon emoticon" alt="smile" title="smile" ' .
                'src="https://www.example.com/moodle/theme/image.php/boost/core/1/s/smiley" /></p></div>$~',
            $formatter->format_text('<p>:-)</p>', FORMAT_MOODLE)
        );
    }

    public function test_format_text_format_moodle_no_filters(): void {
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('emoticon', TEXTFILTER_ON);
        $this->assertEquals(
            '<div class="text_to_html"><p>:-)</p></div>',
            $formatter->format_text('<p>:-)</p>', FORMAT_MOODLE, filter: false)
        );
    }

    /**
     * Make sure that nolink tags and spans prevent linking in filters that support it.
     */
    public function test_format_text_nolink(): void {
        global $CFG;
        $this->resetAfterTest();
        $formatter = new formatting();

        filter_set_global_state('activitynames', TEXTFILTER_ON);

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $page = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'name' => 'Test 1'],
        );
        $cm = get_coursemodule_from_instance('page', $page->id, $page->course, false, MUST_EXIST);
        $pageurl = $CFG->wwwroot . '/mod/page/view.php?id=' . $cm->id;

        $this->assertSame(
            '<p>Read <a class="autolink" title="Test 1" href="' . $pageurl . '">Test 1</a>.</p>',
            $formatter->format_text('<p>Read Test 1.</p>', FORMAT_HTML, context: $context),
        );

        $this->assertSame(
            '<p>Read <a class="autolink" title="Test 1" href="' . $pageurl . '">Test 1</a>.</p>',
            $formatter->format_text(
                '<p>Read Test 1.</p>',
                FORMAT_HTML,
                context: $context,
                clean: false,
            ),
        );

        $this->assertSame(
            '<p>Read Test 1.</p>',
            $formatter->format_text(
                '<p><nolink>Read Test 1.</nolink></p>',
                FORMAT_HTML,
                context: $context,
                clean: true,
            ),
        );

        $this->assertSame(
            '<p>Read Test 1.</p>',
            $formatter->format_text(
                '<p><nolink>Read Test 1.</nolink></p>',
                FORMAT_HTML,
                context: $context,
                clean: false,
            ),
        );

        $this->assertSame(
            '<p><span class="nolink">Read Test 1.</span></p>',
            $formatter->format_text(
                '<p><span class="nolink">Read Test 1.</span></p>',
                FORMAT_HTML,
                context: $context,
            ),
        );
    }

    public function test_format_text_overflowdiv(): void {
        $formatter = new formatting();

        $this->assertEquals(
            '<div class="no-overflow"><p>Hello world</p></div>',
            $formatter->format_text(
                '<p>Hello world</p>',
                FORMAT_HTML,
                overflowdiv: true,
            ),
        );
    }

    /**
     * Test adding blank target attribute to links
     *
     * @dataProvider format_text_blanktarget_testcases
     * @param string $link The link to add target="_blank" to
     * @param string $expected The expected filter value
     */
    public function test_format_text_blanktarget($link, $expected): void {
        $formatter = new formatting();
        $actual = $formatter->format_text(
            $link,
            FORMAT_MOODLE,
            blanktarget: true,
            filter: false,
            clean: false,
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for the test_format_text_blanktarget testcase
     *
     * @return array of testcases
     */
    public static function format_text_blanktarget_testcases(): array {
        return [
            'Simple link' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4">Hey, that\'s pretty good!</a>',
                '<div class="text_to_html"><a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_blank"' .
                    ' rel="noreferrer">Hey, that\'s pretty good!</a></div>',
            ],
            'Link with rel' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="nofollow">Hey, that\'s pretty good!</a>',
                '<div class="text_to_html"><a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="nofollow noreferrer"' .
                    ' target="_blank">Hey, that\'s pretty good!</a></div>',
            ],
            'Link with rel noreferrer' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="noreferrer">Hey, that\'s pretty good!</a>',
                '<div class="text_to_html"><a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" rel="noreferrer"' .
                    ' target="_blank">Hey, that\'s pretty good!</a></div>',
            ],
            'Link with target' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_self">Hey, that\'s pretty good!</a>',
                '<div class="text_to_html"><a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_self">' .
                    'Hey, that\'s pretty good!</a></div>',
            ],
            'Link with target blank' => [
                '<a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_blank">Hey, that\'s pretty good!</a>',
                '<div class="text_to_html"><a href="https://www.youtube.com/watch?v=JeimE8Wz6e4" target="_blank"' .
                    ' rel="noreferrer">Hey, that\'s pretty good!</a></div>',
            ],
            'Link with Frank\'s casket inscription' => [
                // phpcs:ignore moodle.Files.LineLength
                '<a href="https://en.wikipedia.org/wiki/Franks_Casket">ᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻ' .
                    'ᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁ</a>',
                    '<div class="text_to_html"><a href="https://en.wikipedia.org/wiki/Franks_Casket" target="_blank" ' .
                    // phpcs:ignore moodle.Files.LineLength
                    'rel="noreferrer">ᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁᚠᛁᛋᚳ᛫ᚠᛚᚩᛞᚢ᛫ᚪᚻᚩᚠᚩᚾᚠᛖᚱᚷ ᛖᚾ' .
                    'ᛒᛖᚱᛁᚷ ᚹᚪᚱᚦᚷᚪ᛬ᛋᚱᛁᚳᚷᚱᚩᚱᚾᚦᚫᚱᚻᛖᚩᚾᚷᚱᛖᚢᛏᚷᛁᛋᚹᚩᛗ ᚻᚱᚩᚾᚫᛋᛒᚪᚾ ᛗᚫᚷᛁ</a></div>',
            ],
            'No link' => [
                'Some very boring text written with the Latin script',
                '<div class="text_to_html">Some very boring text written with the Latin script</div>',
            ],
            'No link with Thror\'s map runes' => [
                // phpcs:ignore moodle.Files.LineLength
                'ᛋᛏᚫᚾᛞ ᛒᚣ ᚦᛖ ᚷᚱᛖᚣ ᛋᛏᚩᚾᛖ ᚻᚹᛁᛚᛖ ᚦᛖ ᚦᚱᚢᛋᚻ ᚾᚩᚳᛋ ᚫᚾᛞ ᚦᛖ ᛋᛖᛏᛏᛁᚾᚷ ᛋᚢᚾ ᚹᛁᚦ ᚦᛖ ᛚᚫᛋᛏ ᛚᛁᚷᚻᛏ ᚩᚠ ᛞᚢᚱᛁᚾᛋ ᛞᚫᚣ ᚹᛁᛚᛚ ᛋᚻᛁᚾᛖ ᚢᛈᚩᚾ ᚦᛖ ᚳᛖᚣᚻᚩᛚᛖ',
                // phpcs:ignore moodle.Files.LineLength
                '<div class="text_to_html">ᛋᛏᚫᚾᛞ ᛒᚣ ᚦᛖ ᚷᚱᛖᚣ ᛋᛏᚩᚾᛖ ᚻᚹᛁᛚᛖ ᚦᛖ ᚦᚱᚢᛋᚻ ᚾᚩᚳᛋ ᚫᚾᛞ ᚦᛖ ᛋᛖᛏᛏᛁᚾᚷ ᛋᚢᚾ ᚹᛁᚦ ᚦᛖ ᛚᚫᛋᛏ ᛚᛁᚷᚻᛏ ᚩᚠ ᛞᚢᚱᛁᚾᛋ ᛞᚫᚣ ᚹ' .
                    'ᛁᛚᛚ ᛋᚻᛁᚾᛖ ᚢᛈᚩᚾ ᚦᛖ ᚳᛖᚣᚻᚩᛚᛖ</div>',
            ],
        ];
    }

    /**
     * Test ability to force cleaning of otherwise non-cleaned content.
     *
     * @dataProvider format_text_cleaning_testcases
     *
     * @param string $input Input text
     * @param string $nocleaned Expected output of format_text() with noclean=true
     * @param string $cleaned Expected output of format_text() with noclean=false
     */
    public function test_format_text_cleaning($input, $nocleaned, $cleaned): void {
        $formatter = new formatting();

        $formatter->set_forceclean(false);
        $actual = $formatter->format_text($input, FORMAT_HTML, filter: false, clean: true);
        $this->assertEquals($cleaned, $actual);

        $formatter->set_forceclean(true);
        $actual = $formatter->format_text($input, FORMAT_HTML, filter: false, clean: true);
        $this->assertEquals($cleaned, $actual);

        $formatter->set_forceclean(false);
        $actual = $formatter->format_text($input, FORMAT_HTML, filter: false, clean: false);
        $this->assertEquals($nocleaned, $actual);

        $formatter->set_forceclean(true);
        $actual = $formatter->format_text($input, FORMAT_HTML, filter: false, clean: false);
        $this->assertEquals($cleaned, $actual);
    }

    /**
     * Data provider for the test_format_text_cleaning testcase
     *
     * @return array of testcases (string)testcasename => [(string)input, (string)nocleaned, (string)cleaned]
     */
    public static function format_text_cleaning_testcases(): array {
        return [
            'JavaScript' => [
                'Hello <script type="text/javascript">alert("XSS");</script> world',
                'Hello <script type="text/javascript">alert("XSS");</script> world',
                'Hello  world',
            ],
            'Inline frames' => [
                'Let us go phishing! <iframe src="https://1.2.3.4/google.com"></iframe>',
                'Let us go phishing! <iframe src="https://1.2.3.4/google.com"></iframe>',
                'Let us go phishing! ',
            ],
            'Malformed A tags' => [
                '<a onmouseover="alert(document.cookie)">xxs link</a>',
                '<a onmouseover="alert(document.cookie)">xxs link</a>',
                '<a>xxs link</a>',
            ],
            'Malformed IMG tags' => [
                '<IMG """><SCRIPT>alert("XSS")</SCRIPT>">',
                '<IMG """><SCRIPT>alert("XSS")</SCRIPT>">',
                '"&gt;',
            ],
            'On error alert' => [
                '<IMG SRC=/ onerror="alert(String.fromCharCode(88,83,83))"></img>',
                '<IMG SRC=/ onerror="alert(String.fromCharCode(88,83,83))"></img>',
                '<img src="/" alt="" />',
            ],
            'IMG onerror and javascript alert encode' => [
                '<img src=x onerror="&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000083&#0000083&#0000039&#0000041">',
                '<img src=x onerror="&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000083&#0000083&#0000039&#0000041">',
                '<img src="x" alt="x" />',
            ],
            'DIV background-image' => [
                '<DIV STYLE="background-image: url(javascript:alert(\'XSS\'))">',
                '<DIV STYLE="background-image: url(javascript:alert(\'XSS\'))">',
                '<div></div>',
            ],
        ];
    }
}
