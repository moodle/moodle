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

use core_calendar\type_factory;
use Mustache\LambdaHelper;

/**
 * Mustache helper that produces a value for an HTML datetime attribute.
 *
 * The WHATWG HTML standard requires datetime values to match one of its defined formats.
 * We use the global date and time format by default to represent the timestamp with a
 * Gregorian date, time and the user's timezone offset, e.g., 1933-01-02T00:00+0000.
 *
 * The userdate helper is not suitable for this because it removes the leading zero requested
 * by %d and uses the user's calendar. It exposes no options to change these behaviours.
 *
 * The JavaScript equivalent is htmlDateTimeHelper in core/local/templates/renderer. Keep them in sync.
 *
 * This helper is an implementation detail of core/time_element and is not intended for third-party use.
 *
 * @see https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#valid-global-date-and-time-string
 * @package core
 * @copyright 2026 Cameron Ball <cameron@cameron1729.xyz>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class mustache_html_datetime_helper {
    /**
     * Use an existing datetime value, or format a timestamp for an HTML datetime attribute.
     *
     * {{#htmldatetime}} datetime, timestamp, format {{/htmldatetime}}
     *
     * All three arguments are required, but the datetime argument may be empty.
     * When it is not empty, it is returned unchanged.
     *
     * @param string $text The text to parse for arguments.
     * @param LambdaHelper $helper Used to render nested mustache variables.
     * @return string
     */
    public function transform(string $text, LambdaHelper $helper): string {
        $arguments = array_map(trim(...), explode(',', $text, 3));
        $renderedarguments = array_map($helper, $arguments);
        [$datetime, $timestamp, $format] = array_map(strval(...), $renderedarguments);

        $calendar = type_factory::get_calendar_instance('gregorian');
        return $datetime !== '' ? $datetime : $calendar->timestamp_to_date_string($timestamp, $format, 99, false, false);
    }
}
