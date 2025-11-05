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
 * Provides an in-memory assign submissions finder, useful for testing.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger\finder;

/**
 * Provides an in-memory assign submissions finder, useful for testing.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class in_memory_assign_submission_finder implements assign_submission_finder {
    /** @var array list of assign submissions for a given user. */
    private array $memory;

    /**
     * Creates the finder with the given assign submissions for a given user.
     *
     * @param array $entitieswithkey
     */
    public function __construct(array $entitieswithkey) {
        $this->memory = $entitieswithkey;
    }

    /**
     * According to its memory, informs the latest record for the user and assign.
     *
     * @param int $assignid
     * @param int $userid
     * @return bool|object
     */
    public function latest_from_assign_and_user(int $assignid, int $userid): bool|object {
        return $this->memory[$assignid] ?? false;
    }

    /**
     * According to its memory, informs all the records for the user and assign.
     *
     * @param int $assignid
     * @param int $userid
     * @return array
     */
    public function all_from_assign_and_user(int $assignid, int $userid): array {
        return $this->memory[$assignid] ?? [];
    }
}
