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

namespace availability_profile;

/**
 * Unit tests for the user profile condition.
 *
 * @package availability_profile
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition_test extends \advanced_testcase {
    /** @var profile_define_text Profile field for testing */
    protected $profilefield;

    /** @var array Array of user IDs for whome we already set the profile field */
    protected $setusers = array();

    /** @var condition Current condition */
    private $cond;
    /** @var \core_availability\info Current info */
    private $info;

    public function setUp(): void {
        global $DB, $CFG;

        $this->resetAfterTest();

        // Add a custom profile field type.
        $this->profilefield = $this->getDataGenerator()->create_custom_profile_field(array(
                'shortname' => 'frogtype', 'name' => 'Type of frog',
                'datatype' => 'text'));

        // Clear static cache.
        \availability_profile\condition::wipe_static_cache();

        // Load the mock info class so that it can be used.
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
    }

    /**
     * Tests constructing and using date condition as part of tree.
     */
    public function test_in_tree() {
        global $USER;

        $this->setAdminUser();

        $info = new \core_availability\mock_info();

        $structure = (object)array('op' => '|', 'show' => true, 'c' => array(
                (object)array('type' => 'profile',
                        'op' => condition::OP_IS_EQUAL_TO,
                        'cf' => 'frogtype', 'v' => 'tree')));
        $tree = new \core_availability\tree($structure);

        // Initial check (user does not have custom field).
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertFalse($result->is_available());

        // Set field.
        $this->set_field($USER->id, 'tree');

        // Now it's true!
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertTrue($result->is_available());
    }

    /**
     * Tests the constructor including error conditions. Also tests the
     * string conversion feature (intended for debugging only).
     */
    public function test_constructor() {
        // No parameters.
        $structure = new \stdClass();
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->op', $e->getMessage());
        }

        // Invalid op.
        $structure->op = 'isklingonfor';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->op', $e->getMessage());
        }

        // Missing value.
        $structure->op = condition::OP_IS_EQUAL_TO;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->v', $e->getMessage());
        }

        // Invalid value (not string).
        $structure->v = false;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing or invalid ->v', $e->getMessage());
        }

        // Unexpected value.
        $structure->op = condition::OP_IS_EMPTY;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Unexpected ->v', $e->getMessage());
        }

        // Missing field.
        $structure->op = condition::OP_IS_EQUAL_TO;
        $structure->v = 'flying';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Missing ->sf or ->cf', $e->getMessage());
        }

        // Invalid field (not string).
        $structure->sf = 42;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Invalid ->sf', $e->getMessage());
        }

        // Both fields.
        $structure->sf = 'department';
        $structure->cf = 'frogtype';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Both ->sf and ->cf', $e->getMessage());
        }

        // Invalid ->cf field (not string).
        unset($structure->sf);
        $structure->cf = false;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (\coding_exception $e) {
            $this->assertStringContainsString('Invalid ->cf', $e->getMessage());
        }

        // Valid examples (checks values are correctly included).
        $structure->cf = 'frogtype';
        $cond = new condition($structure);
        $this->assertEquals('{profile:*frogtype isequalto flying}', (string)$cond);

        unset($structure->v);
        $structure->op = condition::OP_IS_EMPTY;
        $cond = new condition($structure);
        $this->assertEquals('{profile:*frogtype isempty}', (string)$cond);

        unset($structure->cf);
        $structure->sf = 'department';
        $cond = new condition($structure);
        $this->assertEquals('{profile:department isempty}', (string)$cond);
    }

    /**
     * Tests the save() function.
     */
    public function test_save() {
        $structure = (object)array('cf' => 'frogtype', 'op' => condition::OP_IS_EMPTY);
        $cond = new condition($structure);
        $structure->type = 'profile';
        $this->assertEquals($structure, $cond->save());

        $structure = (object)array('cf' => 'frogtype', 'op' => condition::OP_ENDS_WITH,
                'v' => 'bouncy');
        $cond = new condition($structure);
        $structure->type = 'profile';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Tests the is_available function. There is no separate test for
     * get_full_information because that function is called from is_available
     * and we test its values here.
     */
    public function test_is_available() {
        global $USER, $SITE, $DB;
        $this->setAdminUser();
        $info = new \core_availability\mock_info();

        // Prepare to test with all operators against custom field using all
        // combinations of NOT and true/false states..
        $information = 'x';
        $structure = (object)array('cf' => 'frogtype');

        $structure->op = condition::OP_IS_NOT_EMPTY;
        $cond = new condition($structure);
        $this->assert_is_available_result(false, '~Type of frog.*is not empty~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, 'poison dart');
        $this->assert_is_available_result(true, '~Type of frog.*is empty~',
                $cond, $info, $USER->id);

        $structure->op = condition::OP_IS_EMPTY;
        $cond = new condition($structure);
        $this->assert_is_available_result(false, '~.*Type of frog.*is empty~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, null);
        $this->assert_is_available_result(true, '~.*Type of frog.*is not empty~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, '');
        $this->assert_is_available_result(true, '~.*Type of frog.*is not empty~',
                $cond, $info, $USER->id);

        $structure->op = condition::OP_CONTAINS;
        $structure->v = 'llf';
        $cond = new condition($structure);
        $this->assert_is_available_result(false, '~Type of frog.*contains.*llf~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, 'bullfrog');
        $this->assert_is_available_result(true, '~Type of frog.*does not contain.*llf~',
                $cond, $info, $USER->id);

        $structure->op = condition::OP_DOES_NOT_CONTAIN;
        $cond = new condition($structure);
        $this->assert_is_available_result(false, '~Type of frog.*does not contain.*llf~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, 'goliath');
        $this->assert_is_available_result(true, '~Type of frog.*contains.*llf~',
                $cond, $info, $USER->id);

        $structure->op = condition::OP_IS_EQUAL_TO;
        $structure->v = 'Kermit';
        $cond = new condition($structure);
        $this->assert_is_available_result(false, '~Type of frog.*is <.*Kermit~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, 'Kermit');
        $this->assert_is_available_result(true, '~Type of frog.*is not.*Kermit~',
                $cond, $info, $USER->id);

        $structure->op = condition::OP_STARTS_WITH;
        $structure->v = 'Kerm';
        $cond = new condition($structure);
        $this->assert_is_available_result(true, '~Type of frog.*does not start.*Kerm~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, 'Keroppi');
        $this->assert_is_available_result(false, '~Type of frog.*starts.*Kerm~',
                $cond, $info, $USER->id);

        $structure->op = condition::OP_ENDS_WITH;
        $structure->v = 'ppi';
        $cond = new condition($structure);
        $this->assert_is_available_result(true, '~Type of frog.*does not end.*ppi~',
                $cond, $info, $USER->id);
        $this->set_field($USER->id, 'Kermit');
        $this->assert_is_available_result(false, '~Type of frog.*ends.*ppi~',
                $cond, $info, $USER->id);

        // Also test is_available for a different (not current) user.
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $structure->op = condition::OP_CONTAINS;
        $structure->v = 'rne';
        $cond = new condition($structure);
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $this->set_field($user->id, 'horned');
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));

        // Now check with a standard field (department).
        $structure = (object)array('op' => condition::OP_IS_EQUAL_TO,
                'sf' => 'department', 'v' => 'Cheese Studies');
        $cond = new condition($structure);
        $this->assertFalse($cond->is_available(false, $info, true, $USER->id));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));

        // Check the message (should be using lang string with capital, which
        // is evidence that it called the right function to get the name).
        $information = $cond->get_description(false, false, $info);
        $information = \core_availability\info::format_info($information, $info->get_course());
        $this->assertMatchesRegularExpression('~Department~', $information);

        // Set the field to true for both users and retry.
        $DB->set_field('user', 'department', 'Cheese Studies', array('id' => $user->id));
        $USER->department = 'Cheese Studies';
        $this->assertTrue($cond->is_available(false, $info, true, $USER->id));
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
    }

    /**
     * Tests what happens with custom fields that are text areas. These should
     * not be offered in the menu because their data is not included in user
     * object
     */
    public function test_custom_textarea_field() {
        global $USER, $SITE, $DB;
        $this->setAdminUser();
        $info = new \core_availability\mock_info();

        // Add custom textarea type.
        $customfield = $this->getDataGenerator()->create_custom_profile_field(array(
                'shortname' => 'longtext', 'name' => 'Long text',
                'datatype' => 'textarea'));

        // The list of fields should include the text field added in setUp(),
        // but should not include the textarea field added just now.
        $fields = condition::get_custom_profile_fields();
        $this->assertArrayHasKey('frogtype', $fields);
        $this->assertArrayNotHasKey('longtext', $fields);
    }

    /**
     * Sets the custom profile field used for testing.
     *
     * @param int $userid User id
     * @param string|null $value Field value or null to clear
     * @param int $fieldid Field id or 0 to use default one
     */
    protected function set_field($userid, $value, $fieldid = 0) {
        global $DB, $USER;

        if (!$fieldid) {
            $fieldid = $this->profilefield->id;
        }
        $alreadyset = array_key_exists($userid, $this->setusers);
        if (is_null($value)) {
            $DB->delete_records('user_info_data',
                    array('userid' => $userid, 'fieldid' => $fieldid));
            unset($this->setusers[$userid]);
        } else if ($alreadyset) {
            $DB->set_field('user_info_data', 'data', $value,
                    array('userid' => $userid, 'fieldid' => $fieldid));
        } else {
            $DB->insert_record('user_info_data', array('userid' => $userid,
                    'fieldid' => $fieldid, 'data' => $value));
            $this->setusers[$userid] = true;
        }
    }

    /**
     * Checks the result of is_available. This function is to save duplicated
     * code; it does two checks (the normal is_available with $not set to true
     * and set to false). Whichever result is expected to be true, it checks
     * $information ends up as empty string for that one, and as a regex match
     * for another one.
     *
     * @param bool $yes If the positive test is expected to return true
     * @param string $failpattern Regex pattern to match text when it returns false
     * @param condition $cond Condition
     * @param \core_availability\info $info Information about current context
     * @param int $userid User id
     */
    protected function assert_is_available_result($yes, $failpattern, condition $cond,
            \core_availability\info $info, $userid) {
        // Positive (normal) test.
        $this->assertEquals($yes, $cond->is_available(false, $info, true, $userid),
                'Failed checking normal (positive) result');
        if (!$yes) {
            $information = $cond->get_description(false, false, $info);
            $information = \core_availability\info::format_info($information, $info->get_course());
            $this->assertMatchesRegularExpression($failpattern, $information);
        }

        // Negative (NOT) test.
        $this->assertEquals(!$yes, $cond->is_available(true, $info, true, $userid),
                'Failed checking NOT (negative) result');
        if ($yes) {
            $information = $cond->get_description(false, true, $info);
            $information = \core_availability\info::format_info($information, $info->get_course());
            $this->assertMatchesRegularExpression($failpattern, $information);
        }
    }

    /**
     * Tests the filter_users (bulk checking) function.
     */
    public function test_filter_users() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Erase static cache before test.
        condition::wipe_static_cache();

        // Make a test course and some users.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $student1 = $generator->create_user(array('institution' => 'Unseen University'));
        $student2 = $generator->create_user(array('institution' => 'Hogwarts'));
        $student3 = $generator->create_user(array('institution' => 'Unseen University'));
        $allusers = array();
        foreach (array($student1, $student2, $student3) as $student) {
            $generator->enrol_user($student->id, $course->id);
            $allusers[$student->id] = $student;
        }
        $this->set_field($student1->id, 'poison dart');
        $this->set_field($student2->id, 'poison dart');
        $info = new \core_availability\mock_info($course);
        $checker = new \core_availability\capability_checker($info->get_context());

        // Test standard field condition (positive and negative).
        $cond = new condition((object)array('sf' => 'institution', 'op' => 'contains', 'v' => 'Unseen'));
        $result = array_keys($cond->filter_user_list($allusers, false, $info, $checker));
        ksort($result);
        $this->assertEquals(array($student1->id, $student3->id), $result);
        $result = array_keys($cond->filter_user_list($allusers, true, $info, $checker));
        ksort($result);
        $this->assertEquals(array($student2->id), $result);

        // Test custom field condition.
        $cond = new condition((object)array('cf' => 'frogtype', 'op' => 'contains', 'v' => 'poison'));
        $result = array_keys($cond->filter_user_list($allusers, false, $info, $checker));
        ksort($result);
        $this->assertEquals(array($student1->id, $student2->id), $result);
        $result = array_keys($cond->filter_user_list($allusers, true, $info, $checker));
        ksort($result);
        $this->assertEquals(array($student3->id), $result);
    }

    /**
     * Tests getting user list SQL. This is a different test from the above because
     * there is some additional code in this function so more variants need testing.
     */
    public function test_get_user_list_sql() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Erase static cache before test.
        condition::wipe_static_cache();

        // For testing, make another info field with default value.
        $otherprofilefield = $this->getDataGenerator()->create_custom_profile_field(array(
                'shortname' => 'tonguestyle', 'name' => 'Tongue style',
                'datatype' => 'text', 'defaultdata' => 'Slimy'));

        // Make a test course and some users.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $student1 = $generator->create_user(array('institution' => 'Unseen University'));
        $student2 = $generator->create_user(array('institution' => 'Hogwarts'));
        $student3 = $generator->create_user(array('institution' => 'Unseen University'));
        $student4 = $generator->create_user(array('institution' => '0'));
        $allusers = array();
        foreach (array($student1, $student2, $student3, $student4) as $student) {
            $generator->enrol_user($student->id, $course->id);
            $allusers[$student->id] = $student;
        }
        $this->set_field($student1->id, 'poison dart');
        $this->set_field($student2->id, 'poison dart');
        $this->set_field($student3->id, 'Rough', $otherprofilefield->id);
        $this->info = new \core_availability\mock_info($course);

        // Test standard field condition (positive).
        $this->cond = new condition((object)array('sf' => 'institution',
                'op' => condition::OP_CONTAINS, 'v' => 'Univ'));
        $this->assert_user_list_sql_results(array($student1->id, $student3->id));

        // Now try it negative.
        $this->assert_user_list_sql_results(array($student2->id, $student4->id), true);

        // Try all the other condition types.
        $this->cond = new condition((object)array('sf' => 'institution',
                'op' => condition::OP_DOES_NOT_CONTAIN, 'v' => 's'));
        $this->assert_user_list_sql_results(array($student4->id));
        $this->cond = new condition((object)array('sf' => 'institution',
                'op' => condition::OP_IS_EQUAL_TO, 'v' => 'Hogwarts'));
        $this->assert_user_list_sql_results(array($student2->id));
        $this->cond = new condition((object)array('sf' => 'institution',
                'op' => condition::OP_STARTS_WITH, 'v' => 'U'));
        $this->assert_user_list_sql_results(array($student1->id, $student3->id));
        $this->cond = new condition((object)array('sf' => 'institution',
                'op' => condition::OP_ENDS_WITH, 'v' => 'rts'));
        $this->assert_user_list_sql_results(array($student2->id));
        $this->cond = new condition((object)array('sf' => 'institution',
                'op' => condition::OP_IS_EMPTY));
        $this->assert_user_list_sql_results(array($student4->id));
        $this->cond = new condition((object)array('sf' => 'institution',
                'op' => condition::OP_IS_NOT_EMPTY));
        $this->assert_user_list_sql_results(array($student1->id, $student2->id, $student3->id));

        // Try with a custom field condition that doesn't have a default.
        $this->cond = new condition((object)array('cf' => 'frogtype',
                'op' => condition::OP_CONTAINS, 'v' => 'poison'));
        $this->assert_user_list_sql_results(array($student1->id, $student2->id));
        $this->cond = new condition((object)array('cf' => 'frogtype',
                'op' => condition::OP_IS_EMPTY));
        $this->assert_user_list_sql_results(array($student3->id, $student4->id));

        // Try with one that does have a default.
        $this->cond = new condition((object)array('cf' => 'tonguestyle',
                'op' => condition::OP_STARTS_WITH, 'v' => 'Sli'));
        $this->assert_user_list_sql_results(array($student1->id, $student2->id,
                $student4->id));
        $this->cond = new condition((object)array('cf' => 'tonguestyle',
                'op' => condition::OP_IS_EMPTY));
        $this->assert_user_list_sql_results(array());
    }

    /**
     * Convenience function. Gets the user list SQL and runs it, then checks
     * results.
     *
     * @param array $expected Array of expected user ids
     * @param bool $not True if using NOT condition
     */
    private function assert_user_list_sql_results(array $expected, $not = false) {
        global $DB;
        list ($sql, $params) = $this->cond->get_user_list_sql($not, $this->info, true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);
    }
}
