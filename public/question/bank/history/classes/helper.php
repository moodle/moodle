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

namespace qbank_history;

/**
 * Helper class for question history.
 *
 * @package    qbank_history
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get the question history url.
     *
     * @param int $entryid id of the question entry
     * @param string $returnrul url of the page to return to
     * @param int $courseid id of the course
     * @param ?string $filter filter param to pass to the History view
     * @return \moodle_url
     * @deprecated since Moodle 5.0.
     * @todo MDL-82413 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(replacement: 'qbank_history\helper::get_question_history_url', since: '5.0', mdl: 'MDL-71378')]
    public static function question_history_url(int $entryid, string $returnrul, int $courseid, ?string $filter): \moodle_url {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
        $params = [
            'entryid' => $entryid,
            'returnurl' => $returnrul,
            'courseid' => $courseid
        ];
        if (!is_null($filter)) {
            $params['filter'] = $filter;
        }

        return new \moodle_url('/question/bank/history/history.php', $params);
    }

    /**
     * Get the question history url.
     *
     * @param int $entryid id of the question entry
     * @param string $returnrul url of the page to return to
     * @param int $cmid id of the coursemodule holding the question bank.
     * @param ?string $filter filter param to pass to the History view
     * @return \moodle_url
     */
    public static function get_question_history_url(int $entryid, string $returnrul, int $cmid, ?string $filter) {

        $params = [
            'entryid' => $entryid,
            'returnurl' => $returnrul,
            'cmid' => $cmid,
        ];
        if (!is_null($filter)) {
            $params['filter'] = $filter;
        }

        return new \moodle_url('/question/bank/history/history.php', $params);
    }

}
