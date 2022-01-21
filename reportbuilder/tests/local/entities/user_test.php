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

declare(strict_types=1);

namespace core_reportbuilder\local\entities;

use advanced_testcase;
use moodle_url;
use core_reportbuilder\manager;
use core_reportbuilder\testable_system_report_table;
use core_reportbuilder\user_entity_report;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\user_filter_manager;

/**
 * Unit tests for user entity
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\entities\base
 * @covers      \core_reportbuilder\local\entities\user
 * @covers      \core_reportbuilder\local\helpers\user_profile_fields
 * @covers      \core_reportbuilder\local\report\base
 * @covers      \core_reportbuilder\system_report
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_test extends advanced_testcase {

    /**
     * Load required classes
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/testable_system_report_table.php");
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/user_entity_report.php");
        require_once("{$CFG->dirroot}/user/profile/lib.php");
    }

    /**
     * Test callbacks are correctly applied for those columns using them
     */
    public function test_columns_with_callbacks(): void {
        $this->resetAfterTest();

        // Add a couple of user profile fields to show on the report.
        $this->getDataGenerator()->create_custom_profile_field(['datatype' => 'text',
            'shortname' => 'favcolor', 'name' => 'Favorite color']);
        $this->getDataGenerator()->create_custom_profile_field(['datatype' => 'text',
            'shortname' => 'favsuperpower', 'name' => 'Favorite super power']);

        $user = $this->getDataGenerator()->create_user([
            'suspended' => 1,
            'confirmed' => 0,
            'country' => 'ES',
            'profile_field_favcolor' => 'Blue',
            'profile_field_favsuperpower' => 'Time travel',
        ]);

        $tablerows = $this->get_report_table_rows();
        $userrows = array_filter($tablerows, static function(array $row) use ($user): bool {
            return $row['username'] === $user->username;
        });
        $userrow = reset($userrows);

        $this->assertEquals('Yes', $userrow['suspended']);
        $this->assertEquals('No', $userrow['confirmed']);
        $this->assertEquals('Spain', $userrow['country']);
        $this->assertEquals('Blue', $userrow['profilefield_favcolor']);
        $this->assertEquals('Time travel', $userrow['profilefield_favsuperpower']);
    }

    /**
     * Test the formatted user fullname columns
     */
    public function test_fullname_columns(): void {
        global $OUTPUT;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([]);

        $tablerows = $this->get_report_table_rows();
        $userrows = array_filter($tablerows, static function(array $row) use ($user): bool {
            return $row['username'] === $user->username;
        });
        $userrow = reset($userrows);

        $userfullname = fullname($user);
        $userprofile = (new moodle_url('/user/profile.php', ['id' => $user->id]))->out();
        $userpicture = $OUTPUT->user_picture($user, ['link' => false, 'alttext' => false]);

        $this->assertEquals($userfullname, $userrow['fullname']);
        $this->assertEquals('<a href="' . $userprofile . '">' . $userfullname . '</a>', $userrow['fullnamewithlink']);
        $this->assertEquals($userpicture . $userfullname, $userrow['fullnamewithpicture']);
        $this->assertEquals('<a href="' . $userprofile . '">' . $userpicture . $userfullname . '</a>',
            $userrow['fullnamewithpicturelink']);
    }

    /**
     * Test picture column callback
     */
    public function test_picture_column(): void {
        global $OUTPUT;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([]);

        $tablerows = $this->get_report_table_rows();
        $userrows = array_filter($tablerows, static function(array $row) use ($user): bool {
            return $row['username'] === $user->username;
        });
        $userrow = reset($userrows);

        $userpicture = $OUTPUT->user_picture($user, ['link' => false, 'alttext' => false]);
        $this->assertEquals($userpicture, $userrow['picture']);
    }

    /**
     * Test filtering report by user fields
     */
    public function test_filters(): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_user(['firstname' => 'Daffy', 'lastname' => 'Duck', 'email' => 'daffy@test.com',
            'city' => 'LA', 'lastaccess' => time() - YEARSECS, 'suspended' => 1]);
        $this->getDataGenerator()->create_user(['firstname' => 'Donald', 'lastname' => 'Duck', 'email' => 'donald@test.com',
            'city' => 'Chicago', 'lastaccess' => time(), 'suspended' => 0]);

        // Filter by fullname field.
        $filtervalues = [
            'user:fullname_operator' => text::IS_EQUAL_TO,
            'user:fullname_value' => 'Daffy Duck',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Daffy Duck',
        ], array_column($tablerows, 'fullname'));

        // Filter by firstname field.
        $filtervalues = [
            'user:firstname_operator' => text::CONTAINS,
            'user:firstname_value' => 'Donald',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Donald Duck',
        ], array_column($tablerows, 'fullname'));

        // Filter by lastname field.
        $filtervalues = [
            'user:lastname_operator' => text::CONTAINS,
            'user:lastname_value' => 'Duck',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEqualsCanonicalizing([
            'Donald Duck',
            'Daffy Duck',
        ], array_column($tablerows, 'fullname'));

        // Filter by email field.
        $filtervalues = [
            'user:email_operator' => text::IS_EQUAL_TO,
            'user:email_value' => 'donald@test.com',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Donald Duck',
        ], array_column($tablerows, 'fullname'));

        // Filter by city field.
        $filtervalues = [
            'user:city_operator' => text::IS_EQUAL_TO,
            'user:city_value' => 'Chicago',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Donald Duck',
        ], array_column($tablerows, 'fullname'));

        // Filter by city field.
        $filtervalues = [
            'user:city_operator' => text::IS_EQUAL_TO,
            'user:city_value' => 'Chicago',
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Donald Duck',
        ], array_column($tablerows, 'fullname'));

        // Filter by lastaccess field.
        $filtervalues = [
            'user:lastaccess_operator' => date::DATE_RANGE,
            'user:lastaccess_from' => time() - YEARSECS - 100,
            'user:lastaccess_to' => time() - YEARSECS + 100,
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Daffy Duck',
        ], array_column($tablerows, 'fullname'));

        // Filter by suspened field.
        $filtervalues = [
            'user:suspended_operator' => boolean_select::CHECKED,
        ];
        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Daffy Duck',
        ], array_column($tablerows, 'fullname'));
    }

    /**
     * Test filtering report by a user profile field
     */
    public function test_userprofilefield_filter(): void {
        $this->resetAfterTest();

        // Add a user profile field to show on the report.
        $this->getDataGenerator()->create_custom_profile_field(['datatype' => 'text',
            'shortname' => 'favcolor', 'name' => 'Favorite color']);

        $this->getDataGenerator()->create_user(['firstname' => 'Daffy', 'lastname' => 'Duck', 'profile_field_favcolor' => 'Blue']);
        $this->getDataGenerator()->create_user(['firstname' => 'Donald', 'lastname' => 'Duck',
            'profile_field_favcolor' => 'Green']);

        $filtervalues = [
            'user:profilefield_favcolor_operator' => text::IS_EQUAL_TO,
            'user:profilefield_favcolor_value' => 'Green',
        ];

        $tablerows = $this->get_report_table_rows($filtervalues);
        $this->assertEquals([
            'Donald Duck',
        ], array_column($tablerows, 'fullname'));
    }

    /**
     * Data provider for {@see test_userprofilefield_filter_empty}
     *
     * @return array
     */
    public function userprofilefield_filter_empty_provider(): array {
        return [
            ['checkbox', 1, boolean_select::NOT_CHECKED],
            ['text', 'Hello', text::IS_EMPTY],
            ['text', 'Hello', text::IS_NOT_EQUAL_TO],
            ['text', 'Hello', text::DOES_NOT_CONTAIN],
            ['menu', 'one', select::NOT_EQUAL_TO, "one\ntwo"],
        ];
    }

    /**
     * Test filtering report by a user profile field with negated operators (contains the "empty" value appropriate to the field
     * type, or is not set/null)
     *
     * @param string $datatype
     * @param mixed $userfieldvalue
     * @param int $operator
     * @param string $datatypeparam1
     *
     * @dataProvider userprofilefield_filter_empty_provider
     */
    public function test_userprofilefield_filter_empty(string $datatype, $userfieldvalue, int $operator,
            string $datatypeparam1 = ''): void {

        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => $datatype,
            'shortname' => 'test',
            'name' => 'My test field',
            'param1' => $datatypeparam1,
        ]);

        // At this point, the custom profile field was created after the admin account, so the value will be not set/null.
        $filtervalues = [
            'user:profilefield_test_operator' => $operator,
            'user:profilefield_test_value' => $userfieldvalue,
        ];

        // Create a user who does have the field set.
        $user = $this->getDataGenerator()->create_user(['profile_field_test' => $userfieldvalue]);

        $rows = $this->get_report_table_rows($filtervalues);

        $usernames = array_column($rows, 'username');
        $this->assertContains('admin', $usernames);
        $this->assertNotContains($user->username, $usernames);
    }

    /**
     * Data provider for {@see test_get_name_fields_select}
     *
     * @return array
     */
    public function get_name_fields_select_provider(): array {
        return [
            ['firstname lastname', ['firstname', 'lastname']],
            ['firstname middlename lastname', ['firstname', 'middlename', 'lastname']],
            ['alternatename lastname firstname', ['alternatename', 'lastname', 'firstname']],
        ];
    }

    /**
     * Tests the helper method for selecting all of a users' name fields
     *
     * @param string $fullnamedisplay
     * @param string[] $expecteduserfields
     *
     * @dataProvider get_name_fields_select_provider
     */
    public function test_get_name_fields_select(string $fullnamedisplay, array $expecteduserfields): void {
        global $DB;

        $this->resetAfterTest(true);

        set_config('alternativefullnameformat', $fullnamedisplay);

        $fields = user::get_name_fields_select('u');
        $user = $DB->get_record_sql("SELECT {$fields} FROM {user} u WHERE username = :username", ['username' => 'admin']);

        // Ensure we received back all name fields.
        $this->assertEquals($expecteduserfields, array_keys((array) $user));
    }

    /**
     * Helper method to create the report, and return it's rows
     *
     * @param array $filtervalues
     * @return array
     */
    private function get_report_table_rows(array $filtervalues = []): array {
        $report = manager::create_report_persistent((object) [
            'type' => user_entity_report::TYPE_SYSTEM_REPORT,
            'source' => user_entity_report::class,
        ]);

        user_filter_manager::set($report->get('id'), $filtervalues);

        return testable_system_report_table::create($report->get('id'), [])->get_table_rows();
    }
}
