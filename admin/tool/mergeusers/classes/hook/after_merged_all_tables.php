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
 * Hook to address operations after all tables have been merged.
 *
 * This hook addresses the need to proceed with certain operations
 * to update user-related data, but transversal to several tables
 * or proceeding with aggregated data, like regrading.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\hook;

// phpcs:disable moodle.Commenting.MissingDocblock
use core\attribute\label;
use core\attribute\tags;

/**
 * Hook to address operations after all tables have been merged.
 *
 * This hook addresses the need to proceed with certain operations
 * to update user-related data, but transversal to several tables
 * or proceeding with aggregated data, like regrading.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[label('Actions to do after all database tables are merged, and before events are triggered')]
#[tags('tool_mergeusers', 'concluding_merge')]
class after_merged_all_tables {
    /** @var array $logs list of logs where to add logs, if necessary. */
    private array $logs;
    /** @var array $errormessages list of error messages where to add any, if necessary. */
    private array $errormessages;
    /**
     * Builds the hook with just the user.ids.
     *
     * @param int $toid user.id from the user to keep.
     * @param int $fromid user.id from the user to remove.
     * @param array $logs list of logs where to add logs, if necessary.
     * @param array $errormessages list of error messages where to add any, if necessary.
     */
    public function __construct(
        /** @var int $toid  user.id from the user to keep. */
        public readonly int $toid,
        /** @var int $fromid user.id from the user to remove. */
        public readonly int $fromid,
        array &$logs,
        array &$errormessages,
    ) {
        $this->logs = &$logs;
        $this->errormessages = &$errormessages;
    }

    /**
     * Adds a new line into the log list.
     *
     * @param string $logline
     * @return void
     */
    public function add_log(string $logline): void {
        $this->logs[] = $logline;
    }

    /**
     * Adds a new line into the error messages list.
     *
     * Adding an error message will make fail the merge.
     *
     * @param string $errorline
     * @return void
     */
    public function add_error_message(string $errorline): void {
        $this->errormessages[] = $errorline;
    }
}
