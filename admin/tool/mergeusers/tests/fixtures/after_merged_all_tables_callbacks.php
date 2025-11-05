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
 * Fixture callback for the after_merged_all_tables hook.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\fixtures;

use tool_mergeusers\hook\after_merged_all_tables;

/**
 * Callback implementation for testing the after_merged_all_tables hook.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class after_merged_all_tables_callbacks {
    /** @var int user.id from the user to keep. */
    public static int $toid;
    /** @var int user.id from the user to remove. */
    public static int $fromid;

    /**
     * Simulates an execution of the callback, recording the pair of user.ids involved in the merge.
     *
     * @param after_merged_all_tables $hook
     * @return void
     */
    public static function test_hook_after_merged_all_tables(
        after_merged_all_tables $hook,
    ): void {
        self::$toid = $hook->toid;
        self::$fromid = $hook->fromid;
        $hook->add_log(self::class);
    }

    /**
     * Simulates an execution of the callback recording an error message.
     *
     * @param after_merged_all_tables $hook
     * @return void
     */
    public static function test_hook_after_merged_all_tables_with_an_error_message(
        after_merged_all_tables $hook,
    ): void {
        $hook->add_error_message(self::class);
    }
}
