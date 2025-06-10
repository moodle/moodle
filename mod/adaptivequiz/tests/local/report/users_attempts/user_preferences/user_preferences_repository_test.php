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
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\user_preferences;

use advanced_testcase;

/**
 * @covers \mod_adaptivequiz\local\report\users_attempts\user_preferences\user_preferences_repository
 */
class user_preferences_repository_test extends advanced_testcase {

    public function test_it_stores_and_fetches_preferences(): void {
        $this->resetAfterTest();

        $preferences = user_preferences::from_array(['perpage' => 20, 'showinitialsbar' => 0]);

        user_preferences_repository::save($preferences);
        $this->assertEquals($preferences, user_preferences_repository::get());
    }

    public function test_it_avoids_querying_database_when_not_needed(): void {
        global $DB;

        $this->resetAfterTest();

        $preferences = user_preferences::from_array(['perpage' => 20, 'showinitialsbar' => 0]);

        // Check it will not query database to fetch preference after saving them.
        $queriescountbefore = $DB->perf_get_reads();

        user_preferences_repository::save($preferences);
        user_preferences_repository::get();

        $this->assertEquals($queriescountbefore, $DB->perf_get_reads());

        // Check it will not query the database for subsequent fetches of preferences.
        user_preferences_repository::get();

        $this->assertEquals($queriescountbefore, $DB->perf_get_reads());
    }
}
