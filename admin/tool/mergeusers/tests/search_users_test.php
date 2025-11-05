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
 * Search users
 *
 * @package    tool_mergeusers
 * @copyright  2024 Leon Stringer <leon.stringer@ntlworld.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers;

use advanced_testcase;
use coding_exception;
use dml_exception;
use tool_mergeusers\local\user_searcher;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for searching for users.
 */
final class search_users_test extends advanced_testcase {
    /**
     * Test for searching for specific user fields.
     * Also, search must not return any matching deleted users.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_search_users
     * @dataProvider search_criteria
     * @throws dml_exception
     * @throws coding_exception
     */
    public function test_search_for_user_field_excluding_deleted_users(string $searchfield, string $input, int $count): void {
        $this->resetAfterTest(true);

        $deleteduser = $this->getDataGenerator()->create_user([
            'username' => 'student1', 'email' => 'student1@example.com',
            'firstname' => 'Student', 'lastname' => 'One',
            'idnumber' => 'ID001',
        ]);
        delete_user($deleteduser);
        $this->getDataGenerator()->create_user([
            'username' => 'student1', 'email' => 'student1@example.com',
            'firstname' => 'Student', 'lastname' => 'One',
            'idnumber' => 'ID001',
        ]);

        if (($searchfield === 'id') && ($input === 'id')) {
            $input = "{$deleteduser->id}";
        } else if ($searchfield === 'email') {
            $input = md5($deleteduser->username);
        }

        $mus = new user_searcher();
        $this->assertCount(
            $count,
            $mus->search_users($input, $searchfield)
        );
    }

    /**
     * Test various allowed values for MergeUserSearch->search_users()'s
     * $searchfield parameter.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_search_users
     */
    public static function search_criteria(): array {
        return [
            'id' => [
                'searchfield' => 'id',
                'input' => 'id', // Special case, to be swapped with real ID into the test.
                'count' => 0,
            ],
            // Special case for database engines: compare the "id" int field against a string value.
            'id_with_letters' => [
                'searchfield' => 'id',
                'input' => 'abc',
                'count' => 0,
            ],
            'id_empty' => [
                'searchfield' => 'id',
                'input' => '',
                'count' => 0,
            ],
            'id_existing' => [
                'searchfield' => 'id',
                'input' => '1',
                'count' => 1, // Guest.
            ],
            'username' => [
                'searchfield' => 'username',
                'input' => 'student1',
                'count' => 1,
            ],
            'firstname' => [
                'searchfield' => 'firstname',
                'input' => 'Student',
                'count' => 1,
            ],
            'firstname_partial' => [
                'searchfield' => 'firstname',
                'input' => 'Stu',
                'count' => 1,
            ],
            'firstname_case_insensitive' => [
                'searchfield' => 'firstname',
                'input' => 'STUDENT',
                'count' => 1,
            ],
            'lastname' => [
                'searchfield' => 'lastname',
                'input' => 'One',
                'count' => 1,
            ],
            'email' => [
                'searchfield' => 'email',
                'input' => 'student1',
                'count' => 0,
            ],
            'idnumber' => [
                'searchfield' => 'idnumber',
                'input' => '', // Equates to '%%' which matches all idnumbers.
                'count' => 3, // Users guest + admin + student1.
            ],
            'all' => [
                'searchfield' => 'all',
                'input' => 'student1',
                'count' => 1,
            ],
            'all_partial' => [
                'searchfield' => 'all',
                'input' => 'stu',
                'count' => 1,
            ],
            'all_case_insensitive' => [
                'searchfield' => 'all',
                'input' => 'STUDENT1',
                'count' => 1,
            ],
        ];
    }
}
