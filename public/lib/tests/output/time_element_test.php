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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\output;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the initial HTML rendered by core/time_element.
 *
 * @package core
 * @copyright 2026 Cameron Ball <cameron@cameron1729.xyz>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversNothing]
final class time_element_test extends \advanced_testcase {
    /**
     * The datetime attribute must be usable before any JavaScript runs.
     *
     * @param array $context Template context
     * @param string $timezone User timezone
     * @param string $expected Expected datetime attribute
     */
    #[DataProvider('datetime_provider')]
    public function test_datetime(array $context, string $timezone, string $expected): void {
        global $USER;

        $this->resetAfterTest();
        $USER->timezone = $timezone;
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_url('/');
        $renderer = $page->get_renderer('core');
        $context += ['userdateformat' => '%d %b %Y'];

        $html = $renderer->render_from_template('core/time_element', $context);
        $document = new \DOMDocument();
        @$document->loadHTML($html);
        $time = $document->getElementsByTagName('time')->item(0);

        $this->assertSame($expected, $time->getAttribute('datetime'));
        $this->assertSame(userdate($context['timestamp'], $context['userdateformat']), trim($time->textContent));
        $this->assertSame((string) $context['timestamp'], $time->getAttribute('data-timestamp'));
        $this->assertSame($context['datetimeformat'] ?? '%Y-%m-%dT%H:%M%z', $time->getAttribute('data-datetimeformat'));
        $this->assertFalse($time->hasAttribute('data-injected'));
    }

    /**
     * Supplied datetime values and formats are respected in the initial markup.
     *
     * @return array
     */
    public static function datetime_provider(): array {
        return [
            'epoch' => [['timestamp' => 0], 'UTC', '1970-01-01T00:00+0000'],
            'leading zeroes' => [['timestamp' => 946782306], 'UTC', '2000-01-02T03:05+0000'],
            'fractional timezone' => [['timestamp' => 946782306], 'Asia/Kathmandu', '2000-01-02T08:50+0545'],
            'previous day' => [['timestamp' => 0], 'America/New_York', '1969-12-31T19:00-0500'],
            'date only' => [
                ['timestamp' => 946782306, 'datetimeformat' => '%Y-%m-%d'],
                'UTC',
                '2000-01-02',
            ],
            'seconds' => [
                ['timestamp' => 946782306, 'datetimeformat' => '%Y-%m-%dT%H:%M:%S%z'],
                'UTC',
                '2000-01-02T03:05:06+0000',
            ],
            'custom format in fractional timezone' => [
                ['timestamp' => 946782306, 'datetimeformat' => '%Y-%m-%dT%H:%M:%S%z'],
                'Asia/Kathmandu',
                '2000-01-02T08:50:06+0545',
            ],
            'supplied datetime' => [
                ['timestamp' => 946782306, 'datetime' => '2000-01-02T03:05:06Z', 'datetimeformat' => '%Y'],
                'UTC',
                '2000-01-02T03:05:06Z',
            ],
            'empty datetime' => [
                ['timestamp' => 946782306, 'datetime' => '', 'datetimeformat' => '%Y-%m-%d'],
                'UTC',
                '2000-01-02',
            ],
            'supplied value stays inside the attribute' => [
                ['timestamp' => 946782306, 'datetime' => '2000-01-02" data-injected="yes'],
                'UTC',
                '2000-01-02" data-injected="yes',
            ],
            'custom format stays inside the attribute' => [
                ['timestamp' => 946782306, 'datetimeformat' => '%Y-%m-%d" data-injected="yes'],
                'UTC',
                '2000-01-02" data-injected="yes',
            ],
            'visible date format is independent' => [
                ['timestamp' => 946782306, 'userdateformat' => '%Y'],
                'UTC',
                '2000-01-02T03:05+0000',
            ],
        ];
    }

    /**
     * Inherited templates retain value, format, timestamp and presentation overrides.
     */
    public function test_inherited_blocks(): void {
        global $CFG, $USER;

        $this->resetAfterTest();
        $USER->timezone = 'UTC';
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_url('/');
        $renderer = $page->get_renderer('core');
        $engine = (new \ReflectionMethod($renderer, 'get_mustache'))->invoke($renderer);
        $engine->setLoader(new \Mustache\Loader\ArrayLoader([
            'core/time_element' => file_get_contents($CFG->libdir . '/templates/time_element.mustache'),
            'core/test' => '{{< core/time_element }}'
                . '{{$elementid}}custom-id{{/elementid}}'
                . '{{$elementclass}}custom-class{{/elementclass}}'
                . '{{$timestampval}}{{post.created}}{{/timestampval}}'
                . '{{$datetimeformatval}}%Y-%m-%d{{/datetimeformatval}}'
                . '{{$userdateformatval}}%d %b %Y{{/userdateformatval}}'
                . '{{/core/time_element}}',
            'core/explicit' => '{{< core/time_element }}'
                . '{{$datetimeval}}2000-01-02{{/datetimeval}}'
                . '{{$datedisplay}}A custom date label{{/datedisplay}}'
                . '{{/core/time_element}}',
            'core/empty' => '{{< core/time_element }}'
                . '{{$datetimeval}}{{optionaldate}}{{/datetimeval}}'
                . '{{$datetimeformatval}}%Y-%m-%d{{/datetimeformatval}}'
                . '{{$datedisplay}}A custom date label{{/datedisplay}}'
                . '{{/core/time_element}}',
            'core/translated' => '{{< core/time_element }}'
                . '{{$datetimeformatval}}{{#str}}strftimetime24, langconfig{{/str}}{{/datetimeformatval}}'
                . '{{$datedisplay}}A custom date label{{/datedisplay}}'
                . '{{/core/time_element}}',
        ]));

        $html = $renderer->render_from_template('core/test', ['post' => ['created' => 946782306]]);
        $this->assertStringContainsString('datetime="2000-01-02"', $html);
        $this->assertStringContainsString('id="time-custom-id"', $html);
        $this->assertStringContainsString('class="custom-class"', $html);
        $this->assertStringContainsString('2 Jan 2000', $html);
        $this->assertStringContainsString('data-timestamp="946782306"', $html);
        $this->assertStringContainsString('data-datetimeformat="%Y-%m-%d"', $html);

        $html = $renderer->render_from_template('core/explicit', ['datetime' => '2000']);
        $this->assertStringContainsString('datetime="2000-01-02"', $html);
        $this->assertStringContainsString('A custom date label', $html);

        $html = $renderer->render_from_template('core/translated', ['timestamp' => 946782306]);
        $this->assertStringContainsString('datetime="03:05"', $html);
        $this->assertStringContainsString('data-datetimeformat="%H:%M"', $html);

        // An empty datetime block falls back to the timestamp during rendering.
        $html = $renderer->render_from_template('core/empty', ['timestamp' => 946782306]);
        $this->assertStringContainsString('datetime="2000-01-02"', $html);
        $this->assertStringContainsString('A custom date label', $html);
    }

    /**
     * Formatted datetime values use the Gregorian calendar.
     */
    public function test_calendar_type(): void {
        global $CFG, $USER;

        $this->resetAfterTest();
        require_once($CFG->dirroot . '/calendar/tests/calendartype_test_example.php');
        $USER->calendartype = 'test_example';
        $USER->timezone = 'UTC';
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_url('/');

        $html = $page->get_renderer('core')->render_from_template('core/time_element', [
            'timestamp' => 946782306,
            'userdateformat' => '%d %b %Y',
        ]);
        $this->assertStringContainsString('datetime="2000-01-02T03:05+0000"', $html);
    }
}
