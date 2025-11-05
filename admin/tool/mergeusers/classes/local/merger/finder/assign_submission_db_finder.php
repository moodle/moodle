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

/**
 * Provides the assign submissions finder.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger\finder;

use dml_exception;

/**
 * Provides the assign submissions finder.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_db_finder implements assign_submission_finder {
    /**
     * Provides the latest submission record for the given user and assign.
     *
     * @param int $assignid
     * @param int $userid
     * @return bool|object
     * @throws dml_exception
     */
    public function latest_from_assign_and_user(int $assignid, int $userid): bool|object {
        global $DB;
        return $DB->get_record(
            'assign_submission',
            ['assignment' => $assignid, 'latest' => 1, 'userid' => $userid]
        );
    }

    /**
     * Provides the list of records for the given user and assign.
     *
     * @param int $assignid
     * @param int $userid
     * @return array
     * @throws dml_exception
     */
    public function all_from_assign_and_user(int $assignid, int $userid): array {
        global $DB;
        return $DB->get_records(
            'assign_submission',
            ['assignment' => $assignid, 'userid' => $userid]
        );
    }
}
