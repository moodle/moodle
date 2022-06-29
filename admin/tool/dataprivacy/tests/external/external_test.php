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
 * External tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\external;

use externallib_advanced_testcase;
use tool_dataprivacy\api;
use tool_dataprivacy\context_instance;
use tool_dataprivacy\external;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External testcase.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_test extends externallib_advanced_testcase {

    /**
     * Test for external::approve_data_request() with the user not logged in.
     */
    public function test_approve_data_request_not_logged_in() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Log out the user and set force login to true.
        $this->setUser();

        $this->expectException(\require_login_exception::class);
        external::approve_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::approve_data_request() with the user not having a DPO role.
     */
    public function test_approve_data_request_not_dpo() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Login as the requester.
        $this->setUser($requester);
        $this->expectException(\required_capability_exception::class);
        external::approve_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::approve_data_request() for request that's not ready for approval
     */
    public function test_approve_data_request_not_waiting_for_approval() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);
        $datarequest->set('status', api::DATAREQUEST_STATUS_CANCELLED)->save();

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $this->expectException(\moodle_exception::class);
        external::approve_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::approve_data_request()
     */
    public function test_approve_data_request() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        api::update_request_status($datarequest->get('id'), api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::approve_data_request($datarequest->get('id'));
        $return = (object) \external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::approve_data_request() for a non-existent request ID.
     */
    public function test_approve_data_request_non_existent() {
        $this->resetAfterTest();

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();

        $result = external::approve_data_request(1);

        $return = (object) \external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
    }

    /**
     * Test for external::cancel_data_request() of another user.
     */
    public function test_cancel_data_request_other_user() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $otheruser = $generator->create_user();
        $comment = 'sample comment';

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Login as other user.
        $this->setUser($otheruser);

        $result = external::cancel_data_request($datarequest->get('id'));
        $return = (object) \external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
    }

    /**
     * Test cancellation of a request where you are the requester of another user's data.
     */
    public function test_cancel_data_request_other_user_as_requester() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $otheruser = $generator->create_user();
        $comment = 'sample comment';

        // Assign requester as otheruser'sparent.
        $systemcontext = \context_system::instance();
        $parentrole = $generator->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        role_assign($parentrole, $requester->id, \context_user::instance($otheruser->id));

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($otheruser->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        $result = external::cancel_data_request($datarequest->get('id'));
        $return = (object) \external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test cancellation of a request where you are the requester of another user's data.
     */
    public function test_cancel_data_request_requester_lost_permissions() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $otheruser = $generator->create_user();
        $comment = 'sample comment';

        // Assign requester as otheruser'sparent.
        $systemcontext = \context_system::instance();
        $parentrole = $generator->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        role_assign($parentrole, $requester->id, \context_user::instance($otheruser->id));

        $this->setUser($requester);
        $datarequest = api::create_data_request($otheruser->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Unassign the role.
        role_unassign($parentrole, $requester->id, \context_user::instance($otheruser->id)->id);

        // This user can no longer make the request.
        $this->expectException(\required_capability_exception::class);

        $result = external::cancel_data_request($datarequest->get('id'));
    }

    /**
     * Test cancellation of a request where you are the requester of another user's data.
     */
    public function test_cancel_data_request_other_user_as_child() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $otheruser = $generator->create_user();
        $comment = 'sample comment';

        // Assign requester as otheruser'sparent.
        $systemcontext = \context_system::instance();
        $parentrole = $generator->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        role_assign($parentrole, $requester->id, \context_user::instance($otheruser->id));

        // Test data request creation.
        $this->setUser($otheruser);
        $datarequest = api::create_data_request($otheruser->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        $result = external::cancel_data_request($datarequest->get('id'));
        $return = (object) \external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::cancel_data_request()
     */
    public function test_cancel_data_request() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Test cancellation.
        $this->setUser($requester);
        $result = external::cancel_data_request($datarequest->get('id'));

        $return = (object) \external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test contact DPO.
     */
    public function test_contact_dpo() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $user = $generator->create_user();

        $this->setUser($user);
        $message = 'Hello world!';
        $result = external::contact_dpo($message);
        $return = (object) \external_api::clean_returnvalue(external::contact_dpo_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test contact DPO with message containing invalid input.
     */
    public function test_contact_dpo_with_nasty_input() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $user = $generator->create_user();

        $this->setUser($user);
        $this->expectException('invalid_parameter_exception');
        external::contact_dpo('de<>\\..scription');
    }

    /**
     * Test for external::deny_data_request() with the user not logged in.
     */
    public function test_deny_data_request_not_logged_in() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        // Test data request creation.
        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Log out.
        $this->setUser();
        $this->expectException(\require_login_exception::class);
        external::deny_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::deny_data_request() with the user not having a DPO role.
     */
    public function test_deny_data_request_not_dpo() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Login as the requester.
        $this->setUser($requester);
        $this->expectException(\required_capability_exception::class);
        external::deny_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::deny_data_request() for request that's not ready for approval
     */
    public function test_deny_data_request_not_waiting_for_approval() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);
        $datarequest->set('status', api::DATAREQUEST_STATUS_CANCELLED)->save();

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $this->expectException(\moodle_exception::class);
        external::deny_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::deny_data_request()
     */
    public function test_deny_data_request() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        api::update_request_status($datarequest->get('id'), api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::approve_data_request($datarequest->get('id'));
        $return = (object) \external_api::clean_returnvalue(external::deny_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::deny_data_request() for a non-existent request ID.
     */
    public function test_deny_data_request_non_existent() {
        $this->resetAfterTest();

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $result = external::deny_data_request(1);

        $return = (object) \external_api::clean_returnvalue(external::deny_data_request_returns(), $result);
        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
    }

    /**
     * Test for external::get_data_request() with the user not logged in.
     */
    public function test_get_data_request_not_logged_in() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        $this->setUser();
        $this->expectException(\require_login_exception::class);
        external::get_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::get_data_request() with the user not having a DPO role.
     */
    public function test_get_data_request_not_dpo() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $otheruser = $generator->create_user();
        $comment = 'sample comment';

        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Login as the otheruser.
        $this->setUser($otheruser);
        $this->expectException(\required_capability_exception::class);
        external::get_data_request($datarequest->get('id'));
    }

    /**
     * Test for external::get_data_request()
     */
    public function test_get_data_request() {
        $this->resetAfterTest();

        $generator = new \testing_data_generator();
        $requester = $generator->create_user();
        $comment = 'sample comment';

        $this->setUser($requester);
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $result = external::get_data_request($datarequest->get('id'));

        $return = (object) \external_api::clean_returnvalue(external::get_data_request_returns(), $result);
        $this->assertEquals(api::DATAREQUEST_TYPE_EXPORT, $return->result['type']);
        $this->assertEquals('sample comment', $return->result['comments']);
        $this->assertEquals($requester->id, $return->result['userid']);
        $this->assertEquals($requester->id, $return->result['requestedby']);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::get_data_request() for a non-existent request ID.
     */
    public function test_get_data_request_non_existent() {
        $this->resetAfterTest();

        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $this->expectException(\dml_missing_record_exception::class);
        external::get_data_request(1);
    }

    /**
     * Test for \tool_dataprivacy\external::set_context_defaults()
     * when called by a user that doesn't have the manage registry capability.
     */
    public function test_set_context_defaults_no_capability() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);
        $this->expectException(\required_capability_exception::class);
        external::set_context_defaults(CONTEXT_COURSECAT, context_instance::INHERIT, context_instance::INHERIT, '', false);
    }

    /**
     * Test for \tool_dataprivacy\external::set_context_defaults().
     *
     * We're just checking the module context level here to test the WS function.
     * More testing is done in \tool_dataprivacy_api_testcase::test_set_context_defaults().
     *
     * @dataProvider get_options_provider
     * @param bool $modulelevel Whether defaults are to be applied on the module context level or for an activity only.
     * @param bool $override Whether to override instances.
     */
    public function test_set_context_defaults($modulelevel, $override) {
        $this->resetAfterTest();

        $this->setAdminUser();
        $generator = $this->getDataGenerator();

        // Generate course cat, course, block, assignment, forum instances.
        $coursecat = $generator->create_category();
        $course = $generator->create_course(['category' => $coursecat->id]);
        $assign = $generator->create_module('assign', ['course' => $course->id]);
        list($course, $assigncm) = get_course_and_cm_from_instance($assign->id, 'assign');
        $assigncontext = \context_module::instance($assigncm->id);

        // Generate purpose and category.
        $category1 = api::create_category((object)['name' => 'Test category 1']);
        $category2 = api::create_category((object)['name' => 'Test category 2']);
        $purpose1 = api::create_purpose((object)[
            'name' => 'Test purpose 1', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a'
        ]);
        $purpose2 = api::create_purpose((object)[
            'name' => 'Test purpose 2', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a'
        ]);

        // Set a custom purpose and ID for this assignment instance.
        $assignctxinstance = api::set_context_instance((object) [
            'contextid' => $assigncontext->id,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);

        $modulename = $modulelevel ? 'assign' : '';
        $categoryid = $category2->get('id');
        $purposeid = $purpose2->get('id');
        $result = external::set_context_defaults(CONTEXT_MODULE, $categoryid, $purposeid, $modulename, $override);

        // Extract the result.
        $return = \external_api::clean_returnvalue(external::set_context_defaults_returns(), $result);
        $this->assertTrue($return['result']);

        // Check the assignment context instance.
        $instanceexists = context_instance::record_exists($assignctxinstance->get('id'));
        if ($override) {
            // The custom assign instance should have been deleted.
            $this->assertFalse($instanceexists);
        } else {
            // The custom assign instance should still exist.
            $this->assertTrue($instanceexists);
        }

        // Check the saved defaults.
        list($savedpurpose, $savedcategory) = \tool_dataprivacy\data_registry::get_defaults(CONTEXT_MODULE, $modulename);
        $this->assertEquals($categoryid, $savedcategory);
        $this->assertEquals($purposeid, $savedpurpose);
    }

    /**
     * Test for \tool_dataprivacy\external::get_category_options()
     * when called by a user that doesn't have the manage registry capability.
     */
    public function test_get_category_options_no_capability() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(\required_capability_exception::class);
        external::get_category_options(true, true);
    }

    /**
     * Data provider for \tool_dataprivacy_external_testcase::test_XX_options().
     */
    public function get_options_provider() {
        return [
            [false, false],
            [false, true],
            [true, false],
            [true, true],
        ];
    }

    /**
     * Test for \tool_dataprivacy\external::get_category_options().
     *
     * @dataProvider get_options_provider
     * @param bool $includeinherit Whether "Inherit" would be included to the options.
     * @param bool $includenotset Whether "Not set" would be included to the options.
     */
    public function test_get_category_options($includeinherit, $includenotset) {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Prepare our expected options.
        $expectedoptions = [];
        if ($includeinherit) {
            $expectedoptions[] = [
                'id' => context_instance::INHERIT,
                'name' => get_string('inherit', 'tool_dataprivacy'),
            ];
        }

        if ($includenotset) {
            $expectedoptions[] = [
                'id' => context_instance::NOTSET,
                'name' => get_string('notset', 'tool_dataprivacy'),
            ];
        }

        for ($i = 1; $i <= 3; $i++) {
            $category = api::create_category((object)['name' => 'Category ' . $i]);
            $expectedoptions[] = [
                'id' => $category->get('id'),
                'name' => $category->get('name'),
            ];
        }

        // Call the WS function.
        $result = external::get_category_options($includeinherit, $includenotset);

        // Extract the options.
        $return = (object) \external_api::clean_returnvalue(external::get_category_options_returns(), $result);
        $options = $return->options;

        // Make sure everything checks out.
        $this->assertCount(count($expectedoptions), $options);
        foreach ($options as $option) {
            $this->assertContains($option, $expectedoptions);
        }
    }

    /**
     * Test for \tool_dataprivacy\external::get_purpose_options()
     * when called by a user that doesn't have the manage registry capability.
     */
    public function test_get_purpose_options_no_capability() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);
        $this->expectException(\required_capability_exception::class);
        external::get_category_options(true, true);
    }

    /**
     * Test for \tool_dataprivacy\external::get_purpose_options().
     *
     * @dataProvider get_options_provider
     * @param bool $includeinherit Whether "Inherit" would be included to the options.
     * @param bool $includenotset Whether "Not set" would be included to the options.
     */
    public function test_get_purpose_options($includeinherit, $includenotset) {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Prepare our expected options.
        $expectedoptions = [];
        if ($includeinherit) {
            $expectedoptions[] = [
                'id' => context_instance::INHERIT,
                'name' => get_string('inherit', 'tool_dataprivacy'),
            ];
        }

        if ($includenotset) {
            $expectedoptions[] = [
                'id' => context_instance::NOTSET,
                'name' => get_string('notset', 'tool_dataprivacy'),
            ];
        }

        for ($i = 1; $i <= 3; $i++) {
            $purpose = api::create_purpose((object)[
                'name' => 'Purpose ' . $i, 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a'
            ]);
            $expectedoptions[] = [
                'id' => $purpose->get('id'),
                'name' => $purpose->get('name'),
            ];
        }

        // Call the WS function.
        $result = external::get_purpose_options($includeinherit, $includenotset);

        // Extract the options.
        $return = (object) \external_api::clean_returnvalue(external::get_purpose_options_returns(), $result);
        $options = $return->options;

        // Make sure everything checks out.
        $this->assertCount(count($expectedoptions), $options);
        foreach ($options as $option) {
            $this->assertContains($option, $expectedoptions);
        }
    }

    /**
     * Data provider for \tool_dataprivacy_external_testcase::get_activity_options().
     */
    public function get_activity_options_provider() {
        return [
            [false, false, true],
            [false, true, true],
            [true, false, true],
            [true, true, true],
            [false, false, false],
            [false, true, false],
            [true, false, false],
            [true, true, false],
        ];
    }

    /**
     * Test for \tool_dataprivacy\external::get_activity_options().
     *
     * @dataProvider get_activity_options_provider
     * @param bool $inheritcategory Whether the category would be set to "Inherit".
     * @param bool $inheritpurpose Whether the purpose would be set to "Inherit".
     * @param bool $nodefaults Whether to fetch only activities that don't have defaults.
     */
    public function test_get_activity_options($inheritcategory, $inheritpurpose, $nodefaults) {
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = api::create_category((object)['name' => 'Test category']);
        $purpose = api::create_purpose((object)[
            'name' => 'Test purpose ', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a'
        ]);
        $categoryid = $category->get('id');
        $purposeid = $purpose->get('id');

        if ($inheritcategory) {
            $categoryid = context_instance::INHERIT;
        }
        if ($inheritpurpose) {
            $purposeid = context_instance::INHERIT;
        }

        // Set the context default for the assignment module.
        api::set_context_defaults(CONTEXT_MODULE, $categoryid, $purposeid, 'assign');

        // Call the WS function.
        $result = external::get_activity_options($nodefaults);

        // Extract the options.
        $return = (object) \external_api::clean_returnvalue(external::get_activity_options_returns(), $result);
        $options = $return->options;

        // Make sure the options list is not empty.
        $this->assertNotEmpty($options);

        $pluginwithdefaults = [
            'name' => 'assign',
            'displayname' => get_string('pluginname', 'assign')
        ];

        // If we don't want plugins with defaults to be listed or if both of the category and purpose are set to inherit,
        // the assign module should be listed.
        if (!$nodefaults || ($inheritcategory && $inheritpurpose)) {
            $this->assertContains($pluginwithdefaults, $options);
        } else {
            $this->assertNotContains($pluginwithdefaults, $options);
        }
    }

    /**
     * Test for external::bulk_approve_data_requests().
     */
    public function test_bulk_approve_data_requests() {
        $this->resetAfterTest();

        // Create delete data requests.
        $requester1 = $this->getDataGenerator()->create_user();
        $this->setUser($requester1->id);
        $datarequest1 = api::create_data_request($requester1->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid1 = $datarequest1->get('id');

        $requester2 = $this->getDataGenerator()->create_user();
        $this->setUser($requester2->id);
        $datarequest2 = api::create_data_request($requester2->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid2 = $datarequest2->get('id');

        // Approve the requests.
        $this->setAdminUser();
        api::update_request_status($requestid1, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        api::update_request_status($requestid2, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::bulk_approve_data_requests([$requestid1, $requestid2]);

        $return = (object) \external_api::clean_returnvalue(external::bulk_approve_data_requests_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::bulk_approve_data_requests() for a non-existent request ID.
     */
    public function test_bulk_approve_data_requests_non_existent() {
        $this->resetAfterTest();

        $this->setAdminUser();

        $result = external::bulk_approve_data_requests([42]);

        $return = (object) \external_api::clean_returnvalue(external::bulk_approve_data_requests_returns(), $result);
        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
        $this->assertEquals(42, $warning['item']);
    }

    /**
     * Test for external::bulk_deny_data_requests() for a user without permission to deny requests.
     */
    public function test_bulk_approve_data_requests_no_permission() {
        $this->resetAfterTest();

        // Create delete data requests.
        $requester1 = $this->getDataGenerator()->create_user();
        $this->setUser($requester1->id);
        $datarequest1 = api::create_data_request($requester1->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid1 = $datarequest1->get('id');

        $requester2 = $this->getDataGenerator()->create_user();
        $this->setUser($requester2->id);
        $datarequest2 = api::create_data_request($requester2->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid2 = $datarequest2->get('id');

        $this->setAdminUser();
        api::update_request_status($requestid1, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        api::update_request_status($requestid2, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        // Approve the requests.
        $uut = $this->getDataGenerator()->create_user();
        $this->setUser($uut);

        $this->expectException(\required_capability_exception::class);
        $result = external::bulk_approve_data_requests([$requestid1, $requestid2]);
    }

    /**
     * Test for external::bulk_deny_data_requests() for a user without permission to deny requests.
     */
    public function test_bulk_approve_data_requests_own_request() {
        $this->resetAfterTest();

        // Create delete data requests.
        $requester1 = $this->getDataGenerator()->create_user();
        $this->setUser($requester1->id);
        $datarequest1 = api::create_data_request($requester1->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid1 = $datarequest1->get('id');

        $requester2 = $this->getDataGenerator()->create_user();
        $this->setUser($requester2->id);
        $datarequest2 = api::create_data_request($requester2->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid2 = $datarequest2->get('id');

        $this->setAdminUser();
        api::update_request_status($requestid1, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        api::update_request_status($requestid2, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        // Deny the requests.
        $this->setUser($requester1);

        $this->expectException(\required_capability_exception::class);
        $result = external::bulk_approve_data_requests([$requestid1]);
    }

    /**
     * Test for external::bulk_deny_data_requests().
     */
    public function test_bulk_deny_data_requests() {
        $this->resetAfterTest();

        // Create delete data requests.
        $requester1 = $this->getDataGenerator()->create_user();
        $this->setUser($requester1->id);
        $datarequest1 = api::create_data_request($requester1->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid1 = $datarequest1->get('id');

        $requester2 = $this->getDataGenerator()->create_user();
        $this->setUser($requester2->id);
        $datarequest2 = api::create_data_request($requester2->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid2 = $datarequest2->get('id');

        // Deny the requests.
        $this->setAdminUser();
        api::update_request_status($requestid1, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        api::update_request_status($requestid2, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::bulk_deny_data_requests([$requestid1, $requestid2]);

        $return = (object) \external_api::clean_returnvalue(external::bulk_approve_data_requests_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::bulk_deny_data_requests() for a non-existent request ID.
     */
    public function test_bulk_deny_data_requests_non_existent() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $result = external::bulk_deny_data_requests([42]);
        $return = (object) \external_api::clean_returnvalue(external::bulk_approve_data_requests_returns(), $result);

        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
        $this->assertEquals(42, $warning['item']);
    }

    /**
     * Test for external::bulk_deny_data_requests() for a user without permission to deny requests.
     */
    public function test_bulk_deny_data_requests_no_permission() {
        $this->resetAfterTest();

        // Create delete data requests.
        $requester1 = $this->getDataGenerator()->create_user();
        $this->setUser($requester1->id);
        $datarequest1 = api::create_data_request($requester1->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid1 = $datarequest1->get('id');

        $requester2 = $this->getDataGenerator()->create_user();
        $this->setUser($requester2->id);
        $datarequest2 = api::create_data_request($requester2->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid2 = $datarequest2->get('id');

        $this->setAdminUser();
        api::update_request_status($requestid1, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        api::update_request_status($requestid2, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        // Deny the requests.
        $uut = $this->getDataGenerator()->create_user();
        $this->setUser($uut);

        $this->expectException(\required_capability_exception::class);
        $result = external::bulk_deny_data_requests([$requestid1, $requestid2]);
    }

    /**
     * Test for external::bulk_deny_data_requests() for a user cannot approve their own request.
     */
    public function test_bulk_deny_data_requests_own_request() {
        $this->resetAfterTest();

        // Create delete data requests.
        $requester1 = $this->getDataGenerator()->create_user();
        $this->setUser($requester1->id);
        $datarequest1 = api::create_data_request($requester1->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid1 = $datarequest1->get('id');

        $requester2 = $this->getDataGenerator()->create_user();
        $this->setUser($requester2->id);
        $datarequest2 = api::create_data_request($requester2->id, api::DATAREQUEST_TYPE_DELETE, 'Example comment');
        $requestid2 = $datarequest2->get('id');

        $this->setAdminUser();
        api::update_request_status($requestid1, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        api::update_request_status($requestid2, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        // Deny the requests.
        $this->setUser($requester1);

        $this->expectException(\required_capability_exception::class);
        $result = external::bulk_deny_data_requests([$requestid1]);
    }

    /**
     * Test for external::get_users(), case search using non-identity field without
     * facing any permission problem.
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public function test_get_users_using_using_non_identity() {
        $this->resetAfterTest();
        $context = \context_system::instance();
        $requester = $this->getDataGenerator()->create_user();
        $role = $this->getDataGenerator()->create_role();
        role_assign($role, $requester->id, $context);
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $role, $context);
        $this->setUser($requester);

        $this->getDataGenerator()->create_user([
            'firstname' => 'First Student'
        ]);
        $student2 = $this->getDataGenerator()->create_user([
            'firstname' => 'Second Student'
        ]);

        $results = external::get_users('Second');
        $this->assertCount(1, $results);
        $this->assertEquals((object)[
            'id' => $student2->id,
            'fullname' => fullname($student2),
            'extrafields' => []
        ], $results[$student2->id]);
    }

    /**
     * Test for external::get_users(), case search using identity field but
     * don't have "moodle/site:viewuseridentity" permission.
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public function test_get_users_using_identity_without_permission() {
        global $CFG;

        $this->resetAfterTest();
        $CFG->showuseridentity = 'institution';

        // Create requester user and assign correct capability.
        $context = \context_system::instance();
        $requester = $this->getDataGenerator()->create_user();
        $role = $this->getDataGenerator()->create_role();
        role_assign($role, $requester->id, $context);
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $role, $context);
        $this->setUser($requester);

        $this->getDataGenerator()->create_user([
            'institution' => 'University1'
        ]);

        $results = external::get_users('University1');
        $this->assertEmpty($results);
    }

    /**
     * Test for external::get_users(), case search using disabled identity field
     * even they have "moodle/site:viewuseridentity" permission.
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public function test_get_users_using_field_not_in_identity() {
        $this->resetAfterTest();

        $context = \context_system::instance();
        $requester = $this->getDataGenerator()->create_user();
        $role = $this->getDataGenerator()->create_role();
        role_assign($role, $requester->id, $context);
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $role, $context);
        assign_capability('moodle/site:viewuseridentity', CAP_ALLOW, $role, $context);
        $this->setUser($requester);

        $this->getDataGenerator()->create_user([
            'institution' => 'University1'
        ]);

        $results = external::get_users('University1');
        $this->assertEmpty($results);
    }

    /**
     * Test for external::get_users(), case search using enabled identity field
     * with "moodle/site:viewuseridentity" permission.
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public function test_get_users() {
        global $CFG;
        $this->resetAfterTest();
        $CFG->showuseridentity = 'institution';
        $context = \context_system::instance();
        $requester = $this->getDataGenerator()->create_user();
        $role = $this->getDataGenerator()->create_role();
        role_assign($role, $requester->id, $context);
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $role, $context);
        assign_capability('moodle/site:viewuseridentity', CAP_ALLOW, $role, $context);
        $this->setUser($requester);

        $student1 = $this->getDataGenerator()->create_user([
            'institution' => 'University1'
        ]);
        $this->getDataGenerator()->create_user([
            'institution' => 'University2'
        ]);

        $results = external::get_users('University1');
        $this->assertCount(1, $results);
        $this->assertEquals((object)[
            'id' => $student1->id,
            'fullname' => fullname($student1),
            'extrafields' => [
                0 => (object)[
                    'name' => 'institution',
                    'value' => 'University1'
                ]
            ]
        ], $results[$student1->id]);
    }
}
