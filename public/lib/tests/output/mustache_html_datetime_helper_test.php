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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the htmldatetime Mustache helper.
 *
 * @package core
 * @copyright 2026 Cameron Ball <cameron@cameron1729.xyz>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(mustache_html_datetime_helper::class)]
final class mustache_html_datetime_helper_test extends \advanced_testcase {
    /**
     * Existing datetime values take precedence over formatting a timestamp.
     *
     * @param string $text Helper arguments
     * @param string $timezone User timezone
     * @param string $expected Expected date
     */
    #[DataProvider('timestamp_provider')]
    public function test_htmldatetime(string $text, string $timezone, string $expected): void {
        global $USER;

        $this->resetAfterTest();
        $USER->timezone = $timezone;
        $engine = new mustache_engine([
            'helpers' => ['htmldatetime' => (new mustache_html_datetime_helper())->transform(...)],
        ]);

        $this->assertSame($expected, $engine->render(
            '{{#htmldatetime}} ' . $text . ' {{/htmldatetime}}',
            ['timestamp' => 946782306, 'format' => '%Y-%m-%d', 'datetime' => '2000-01-02T03:05:06Z'],
        ));
    }

    /**
     * Existing values, custom formats and timestamps in different timezones.
     *
     * @return array
     */
    public static function timestamp_provider(): array {
        return [
            'documentation example' => [', -1167523200, %Y-%m-%dT%H:%M%z', 'UTC', '1933-01-02T00:00+0000'],
            'time from the timestamp' => [', -1167460260, %Y-%m-%dT%H:%M%z', 'UTC', '1933-01-02T17:29+0000'],
            'nested variables' => [', {{timestamp}}, {{format}}', 'UTC', '2000-01-02'],
            'epoch' => [', 0, %Y-%m-%dT%H:%M%z', 'UTC', '1970-01-01T00:00+0000'],
            'fractional timezone' => [', {{timestamp}}, %Y-%m-%dT%H:%M%z', 'Asia/Kathmandu', '2000-01-02T08:50+0545'],
            'previous day' => [', 0, %Y-%m-%dT%H:%M%z', 'America/New_York', '1969-12-31T19:00-0500'],
            'seconds' => [', {{timestamp}}, %Y-%m-%dT%H:%M:%S%z', 'UTC', '2000-01-02T03:05:06+0000'],
            'time only' => [', {{timestamp}}, %H:%M:%S', 'UTC', '03:05:06'],
            'comma in format' => [', {{timestamp}}, %Y-%m-%d, %H:%M', 'UTC', '2000-01-02, 03:05'],
            'existing datetime' => ['{{datetime}}, {{timestamp}}, %Y', 'UTC', '2000-01-02T03:05:06Z'],
            'existing datetime without timestamp' => ['{{datetime}}, , %Y', 'UTC', '2000-01-02T03:05:06Z'],
        ];
    }
}
