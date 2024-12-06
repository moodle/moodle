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

namespace qbank_viewcreator;

use core\exception\moodle_exception;
use core\plugininfo\filter;
use core_question\local\bank\condition;

/**
 * Filter condition for date/time modified
 *
 * @package   qbank_viewcreator
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class timemodified_condition extends condition {
    /**
     * @var string Search for times before the specified date.
     */
    const MODE_BEFORE = 'before';

    /**
     * @var string Search for times after the specified date.
     */
    const MODE_AFTER = 'after';

    /**
     * @var string Search for times between the specified dates.
     */
    const MODE_BETWEEN = 'between';

    #[\Override]
    public function get_title() {
        return get_string('timemodified', 'qbank_viewcreator');
    }

    #[\Override]
    public static function get_condition_key() {
        return 'timemodified';
    }

    #[\Override]
    public function get_filter_class() {
        return 'core/datafilter/filtertypes/datetime';
    }

    /**
     * Set a single valid jointype, so we don't display the jointype selector.
     *
     * We have a separate filter option to control how this condition is applied, Any/All/None doesn't apply here.
     *
     * @return array
     */
    public function get_join_list(): array {
        return [
            self::JOINTYPE_DEFAULT,
        ];
    }

    /**
     * Build an SQL WHERE condition to filter questions based on q.timemodified.
     *
     * $filter['values'][0] contains the datetime to search after, $filter['values'][1] contains the datetime
     * to search before. Whether to use these dates to search after, before, or between these dates is determined
     * by the value of $filter['fileroptions']['mode'].
     *
     * The datetime values are in the format YYYY-MM-DDTHH:mm, as provided by the datetime-local input type.
     *
     * @param array $filter ['values' => [$before, $after], 'filteroptions' => ['mode' => $mode]]
     * @return array
     * @throws moodle_exception If an invalid mode or range is provided.
     */
    public static function build_query_from_filter(array $filter): array {
        if (!isset($filter['filteroptions']['mode']) || empty($filter['values'])) {
            return ['', []];
        }
        $mode = $filter['filteroptions']['mode'];
        if (!in_array($mode, [self::MODE_AFTER, self::MODE_BEFORE, self::MODE_BETWEEN])) {
            throw new moodle_exception('invaliddatetimemode', 'error', a: $filter['filteroptions']['mode']);
        }
        $tz = new \DateTimeZone(\core_date::get_user_timezone(99));
        $datetimeafter = new \DateTime($filter['values'][0], $tz);
        $datetimebefore = new \DateTime($filter['values'][1], $tz);
        if ($mode === self::MODE_AFTER) {
            $conditions = 'q.timemodified > :timemodifiedafter';
            $params['timemodifiedafter'] = $datetimeafter->getTimestamp();
        } else if ($mode === self::MODE_BEFORE) {
            $conditions = 'q.timemodified < :timemodifiedbefore';
            $params['timemodifiedbefore'] = $datetimebefore->getTimestamp();
        } else {
            if ($datetimeafter > $datetimebefore) {
                throw new moodle_exception(
                    'invaliddatetimebetween',
                    'error',
                    a: (object) [
                        'before' => $datetimebefore->format('Y-m-d H:i'),
                        'after' => $datetimeafter->format('Y-m-d H:i'),
                    ],
                );
            }
            $conditions = 'q.timemodified > :timemodifiedafter AND q.timemodified < :timemodifiedbefore';
            $params = [
                'timemodifiedafter' => $datetimeafter->getTimestamp(),
                'timemodifiedbefore' => $datetimebefore->getTimestamp(),
            ];
        }

        return [$conditions, $params];
    }

    /**
     * Return the default datetime values for the filter.
     *
     * This generates values formatted for datetime-local fields. The first value returned is the current time,
     * for use as the default "before" datetime. The second is midnight 1 week ago, for use as the default "after"
     * datetime.
     *
     * @return array[]
     */
    public function get_initial_values(): array {
        $tz = new \DateTimeZone(\core_date::get_user_timezone());
        // Datetime format used by the <input type="datetime-local"> field.
        $format = 'Y-m-d\TH:i';
        $now = (new \DateTime('now', $tz))->format($format);
        $oneweek = (new \DateTime('midnight 1 week ago', $tz))->format($format);
        return [
            [
                'value' => $now,
                'title' => $now,
            ],
            [
                'value' => $oneweek,
                'title' => $oneweek,
            ],
        ];
    }
}
