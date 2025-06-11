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
use tool_mergeusers\event\user_merged_success;
use tool_mergeusers\local\profile_fields;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../db/upgradelib.php');
/**
 * @package tool
 * @subpackage mergeusers
 * @author Sam Møller <smo@moxis.dk>
 * @copyright 2019 Servei de Recursos Educatius (http://www.sre.urv.cat)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class user_profile_field_info_test extends advanced_testcase {

    protected function setUp(): void {
        global $CFG;

        require_once $CFG->dirroot . '/admin/tool/mergeusers/lib.php';

        $this->resetAfterTest();
    }

    /**
     * Forces recreation of user profile fields to ensure they are generated as expected.
     * @group tool_mergeusers
     * @group user_profile_fields
     */
    public function test_create_user_profile_category(): void {
        global $DB;

        $category = $this->get_merge_users_profile_category();
        $old_id = $category->id;

        if (!empty($category->id)) {
            $DB->delete_records('user_info_category', ['id' => $category->id]);
        }

        tool_mergeusers_define_user_profile_fields();

        $category = $DB->get_record('user_info_category', ['name' => $category->name]);

        self::assertNotEmpty($category->id);
        self::assertNotEquals($old_id, $category->id);
    }

    /**
     * Invoke the function on upgrading and installing to be sure that custom profile fields
     * are present after its execution.
     *
     * @throws dml_exception
     * @group tool_mergeusers
     * @group user_profile_fields
     */
    public function test_user_profile_fields_are_created(): void {
        global $DB;

        $category = $this->get_merge_users_profile_category();

        // Remove all fields in the category.
        $DB->delete_records('user_info_field', ['categoryid' => $category->id]);

        tool_mergeusers_define_user_profile_fields();

        $records = $DB->get_records('user_info_field', ['categoryid' => $category->id]);

        $this->assert_profile_fields_are_generated($records);
    }

    /**
     * Emulate two users are successfully merged and check that all profile fields are updated.
     *
     * @throws dml_exception
     * @throws coding_exception
     * @group tool_mergeusers
     * @group user_profile_fields
     */
    public function test_profile_fields_are_updated_on_merge_success(): void {
        $generator = self::getDataGenerator();

        $olduser = $generator->create_user();
        $newuser = $generator->create_user();
        $logid = 1;
        $mergedate = time();
        $log = (object)[
            'id' => $logid,
            'touserid' => $newuser->id,
            'fromuserid' => $olduser->id,
            'mergedbyuserid' => 2,
            'timemodified' => $mergedate,
            'log' => '',
        ];

        $this->trigger_user_merged_success_event($olduser, $newuser, $log);

        $this->assert_profile_fields_are_set_on_user($olduser->id, $newuser->id, $logid, $mergedate, true);
        $this->assert_profile_fields_are_set_on_user($newuser->id, $olduser->id, $logid, $mergedate, false);

    }

    /**
     * Gets the profile field category related to merge users.
     *
     * @return stdClass
     * @throws dml_exception
     */
    private function get_merge_users_profile_category(): object {
        global $DB;

        $record = ['name' => profile_fields::MERGE_CATEGORY_FOR_FIELDS];
        $category = $DB->get_record('user_info_category', $record);

        if (empty($category)) {
            $category = self::getDataGenerator()->create_custom_profile_field_category($record);
        }

        return $category;
    }

    /**
     * Triggers a successful merge event.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    private function trigger_user_merged_success_event(
        object $old_user,
        object $new_user,
        object $log
    ): void {
        user_merged_success::create([
            'context' => \context_system::instance(),
            'other' => [
                'usersinvolved' => [
                    'toid' => $new_user->id,
                    'fromid' => $old_user->id,
                ],
                'logid' => $log->id,
                'log' => $log,
            ],
        ])->trigger();
    }

    /**
     * Evaluates if the custom profile fields are all the necessary for this plugin.
     *
     * @param array $records list of records related to the merge users category fields.
     * @return void
     */
    private function assert_profile_fields_are_generated(array $records): void {
        $fields = [];

        foreach ($records as $record) {
            $fields[$record->shortname] = $record;
        }

        self::assertCount(4, $fields);

        foreach (profile_fields::MERGE_FIELD_SHORTNAMES as $shortname) {
            self::assertArrayHasKey($shortname, $fields);
        }
    }

    /**
     * Checks whether the user profile fields are properly updated.
     *
     * @param int $userid merged user id to check.
     * @param int $otheruserid user id of the other user being merged.
     * @param int $logid log id of the merge.
     * @param int $mergedate date when the merge was done.
     * @param bool $isolduser true when the $userid is the old user; false when it is the new one.
     * @return void
     * @throws dml_exception
     */
    private function assert_profile_fields_are_set_on_user(int $userid, int $otheruserid, int $logid, int $mergedate, bool $isolduser) {
        $allfieldsbycategory = profile_get_user_fields_with_data_by_category($userid);
        $mergeuserscategoryid = $this->get_merge_users_profile_category()->id;
        $mergeuserfields = [];

        if (isset($allfieldsbycategory[$mergeuserscategoryid])) {
            foreach ($allfieldsbycategory[$mergeuserscategoryid] as $field) {
                $mergeuserfields[$field->get_shortname()] = $field->data;
            }
        }

        // Testing common profile fields are present.
        self::assertArrayHasKey(profile_fields::MERGE_DATE, $mergeuserfields);
        self::assertEquals($mergedate, $mergeuserfields[profile_fields::MERGE_DATE]);
        self::assertArrayHasKey(profile_fields::MERGE_LOG_ID, $mergeuserfields);
        self::assertEquals($logid, $mergeuserfields[profile_fields::MERGE_LOG_ID]);

        // Testing profile fields depending on old or new user.
        if ($isolduser) {
            self::assertArrayHasKey(profile_fields::MERGE_OLD_USER_ID, $mergeuserfields);
            self::assertEquals('', $mergeuserfields[profile_fields::MERGE_OLD_USER_ID]);
            self::assertArrayHasKey(profile_fields::MERGE_NEW_USER_ID, $mergeuserfields);
            self::assertEquals($otheruserid, $mergeuserfields[profile_fields::MERGE_NEW_USER_ID]);
        } else {
            self::assertArrayHasKey(profile_fields::MERGE_OLD_USER_ID, $mergeuserfields);
            self::assertEquals($otheruserid, $mergeuserfields[profile_fields::MERGE_OLD_USER_ID]);
            self::assertArrayHasKey(profile_fields::MERGE_NEW_USER_ID, $mergeuserfields);
            self::assertEquals('', $mergeuserfields[profile_fields::MERGE_NEW_USER_ID]);
        }
    }
}
