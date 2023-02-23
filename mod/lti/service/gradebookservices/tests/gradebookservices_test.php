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

namespace ltiservice_gradebookservices;

use ltiservice_gradebookservices\local\service\gradebookservices;

/**
 * Unit tests for lti gradebookservices.
 *
 * @package    ltiservice_gradebookservices
 * @category   test
 * @copyright  2020 Claude Vervoort <claude.vervoort@cengage.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_lti\service\gradebookservices\local\gradebookservices
 */
class gradebookservices_test extends \advanced_testcase {

    /**
     * @covers ::instance_added
     *
     * Test saving a graded LTI with resource and tag info (as a result of
     * content item selection) creates a gradebookservices record
     * that can be retrieved using the gradebook service API.
     */
    public function test_lti_add_coupled_lineitem() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a tool type, associated with that proxy.

        $typeid = $this->create_type();
        $course = $this->getDataGenerator()->create_course();
        $resourceid = 'test-resource-id';
        $tag = 'tag';
        $subreviewurl = 'https://subreview.example.com';
        $subreviewparams = 'a=2';

        $ltiinstance = $this->create_graded_lti($typeid, $course, $resourceid, $tag, $subreviewurl, $subreviewparams);

        $this->assertNotNull($ltiinstance);

        $gbs = gradebookservices::find_ltiservice_gradebookservice_for_lti($ltiinstance->id);

        $this->assertNotNull($gbs);
        $this->assertEquals($resourceid, $gbs->resourceid);
        $this->assertEquals($tag, $gbs->tag);
        $this->assertEquals($subreviewurl, $gbs->subreviewurl);
        $this->assertEquals($subreviewparams, $gbs->subreviewparams);
        $this->assert_lineitems($course, $typeid, $ltiinstance->name,
            $ltiinstance, $resourceid, $tag, $subreviewurl, $subreviewparams);
    }

    /**
     * @covers ::instance_added
     *
     * Test saving a graded LTI with resource and tag info (as a result of
     * content item selection) creates a gradebookservices record
     * that can be retrieved using the gradebook service API.
     */
    public function test_lti_add_coupled_lineitem_default_subreview() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a tool type, associated with that proxy.

        $typeid = $this->create_type();
        $course = $this->getDataGenerator()->create_course();
        $resourceid = 'test-resource-id';
        $tag = 'tag';

        $ltiinstance = $this->create_graded_lti($typeid, $course, $resourceid, $tag, 'DEFAULT');

        $this->assertNotNull($ltiinstance);

        $gbs = gradebookservices::find_ltiservice_gradebookservice_for_lti($ltiinstance->id);

        $this->assertNotNull($gbs);
        $this->assertEquals('DEFAULT', $gbs->subreviewurl);
        $this->assert_lineitems($course, $typeid, $ltiinstance->name, $ltiinstance, $resourceid, $tag, 'DEFAULT');
    }

    /**
     * @covers ::add_standalone_lineitem
     *
     * Test saving a standalone LTI lineitem with resource and tag info
     * that can be retrieved using the gradebook service API.
     */
    public function test_lti_add_standalone_lineitem() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $resourceid = "test-resource-standalone";
        $tag = "test-tag-standalone";
        $typeid = $this->create_type();

        $this->create_standalone_lineitem($course->id, $typeid, $resourceid, $tag);

        $this->assert_lineitems($course, $typeid, "manualtest", null, $resourceid, $tag);
    }

    /**
     * @covers ::find_ltiservice_gradebookservice_for_lti
     *
     * Test line item URL is populated for coupled line item only
     * if there is not another line item bound to the lti instance,
     * since in that case there would be no rule to define which of
     * the line items should be actually passed.
     */
    public function test_get_launch_parameters_coupled() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a tool type, associated with that proxy.

        $typeid = $this->create_type();
        $course = $this->getDataGenerator()->create_course();

        $ltiinstance = $this->create_graded_lti($typeid, $course, 'resource-id', 'tag', 'https://subreview.url', 'sub=review');

        $this->assertNotNull($ltiinstance);

        $gbservice = new gradebookservices();
        $params = $gbservice->get_launch_parameters('basic-lti-launch-request', $course->id, 111, $typeid, $ltiinstance->id);
        $this->assertEquals('$LineItem.url', $params['lineitem_url']);
        $this->assertEquals('$LineItem.url', $params['lineitem_url']);

        $this->create_standalone_lineitem($course->id, $typeid, 'resource-id', 'tag', $ltiinstance->id);
        $params = $gbservice->get_launch_parameters('basic-lti-launch-request', $course->id, 111, $typeid, $ltiinstance->id);
        $this->assertEquals('$LineItems.url', $params['lineitems_url']);
        // 2 line items for a single link, we cannot return a single line item url.
        $this->assertFalse(array_key_exists('$LineItem.url', $params));
    }

    /**
     * @covers ::override_endpoint
     *
     * Test Submission Review URL and custom parameter is applied when the
     * launch is submission review.
     */
    public function test_get_launch_parameters_coupled_subreview_override() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a tool type, associated with that proxy.

        $typeid = $this->create_type();
        $course = $this->getDataGenerator()->create_course();

        $ltiinstance = $this->create_graded_lti($typeid, $course, 'resource-id', 'tag',
            'https://example.com/subreview', 'action=review');

        $this->assertNotNull($ltiinstance);

        $gbservice = new gradebookservices();
        $overrides = $gbservice->override_endpoint('LtiSubmissionReviewRequest', 'https://example.com/lti',
            "color=blue", $course->id, $ltiinstance);

        $this->assertEquals('https://example.com/subreview', $overrides[0]);
        $this->assertEquals("color=blue\naction=review", $overrides[1]);
    }

    /**
     * @covers ::override_endpoint
     *
     * Test Submission Review URL and custom parameter is applied when the
     * launch is submission review.
     */
    public function test_get_launch_parameters_coupled_subreview_override_default() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a tool type, associated with that proxy.

        $typeid = $this->create_type();
        $course = $this->getDataGenerator()->create_course();

        $ltiinstance = $this->create_graded_lti($typeid, $course, 'resource-id', 'tag',
            'DEFAULT', '');

        $this->assertNotNull($ltiinstance);

        $gbservice = new gradebookservices();
        $overrides = $gbservice->override_endpoint('LtiSubmissionReviewRequest', 'https://example.com/lti',
            "color=blue", $course->id, $ltiinstance);

        $this->assertEquals('https://example.com/lti', $overrides[0]);
        $this->assertEquals("color=blue", $overrides[1]);
    }

    /**
     * @covers ::get_launch_parameters
     *
     * Test line item URL is populated for not coupled line item only
     * if there is a single line item attached to that lti instance.
     */
    public function test_get_launch_parameters_decoupled() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a tool type, associated with that proxy.

        $typeid = $this->create_type();

        $course = $this->getDataGenerator()->create_course();

        $ltiinstance = $this->create_notgraded_lti($typeid, $course);

        $this->assertNotNull($ltiinstance);

        $gbservice = new gradebookservices();
        $params = $gbservice->get_launch_parameters('basic-lti-launch-request', $course->id, 111, $typeid, $ltiinstance->id);
        $this->assertEquals('$LineItems.url', $params['lineitems_url']);
        $this->assertFalse(array_key_exists('$LineItem.url', $params));

        $this->create_standalone_lineitem($course->id, $typeid, 'resource-id', 'tag', $ltiinstance->id);
        $params = $gbservice->get_launch_parameters('basic-lti-launch-request', $course->id, 111, $typeid, $ltiinstance->id);
        $this->assertEquals('$LineItems.url', $params['lineitems_url']);
        $this->assertEquals('$LineItem.url', $params['lineitem_url']);

        // 2 line items for a single link, we cannot return a single line item url.
        $this->create_standalone_lineitem($course->id, $typeid, 'resource-id', 'tag-2', $ltiinstance->id);
        $this->assertFalse(array_key_exists('$LineItem.url', $params));
    }

    /**
     * @covers ::is_user_gradable_in_course
     *
     * Test if a user can be graded in a course.
     */
    public function test_is_user_gradable_in_course() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'editingteacher');

        $this->assertTrue(gradebookservices::is_user_gradable_in_course($course->id, $user1->id));
        $this->assertFalse(gradebookservices::is_user_gradable_in_course($course->id, $user2->id));
    }

    /**
     * Asserts a matching gradebookservices record exist with the matching tag and resourceid.
     *
     * @param object $course current course
     * @param int $typeid Type id of the tool
     * @param string $label Label of the line item
     * @param object|null $ltiinstance lti instance related to that line item
     * @param string|null $resourceid resourceid the line item should have
     * @param string|null $tag tag the line item should have
     * @param string|null $subreviewurl submission review url
     * @param string|null $subreviewparams submission review custom params
     */
    private function assert_lineitems(object $course, int $typeid,
            string $label, ?object $ltiinstance, ?string $resourceid, ?string $tag,
            ?string $subreviewurl = null, ?string $subreviewparams = null) : void {
        $gbservice = new gradebookservices();
        $gradeitems = $gbservice->get_lineitems($course->id, null, null, null, null, null, $typeid);

        // The 1st item in the array is the items count.
        $this->assertEquals(1, $gradeitems[0]);
        $lineitem = gradebookservices::item_for_json($gradeitems[1][0], '', $typeid);
        $this->assertEquals(10, $lineitem->scoreMaximum);
        $this->assertEquals($resourceid, $lineitem->resourceId);
        $this->assertEquals($tag, $lineitem->tag);
        $this->assertEquals($label, $lineitem->label);
        $this->assertEquals(!empty($subreviewurl), isset($lineitem->submissionReview));
        if ($subreviewurl) {
            if ($subreviewurl == 'DEFAULT') {
                $this->assertFalse(isset($this->submissionReview->url));
            } else {
                $this->assertEquals($subreviewurl, $lineitem->submissionReview->url);
            }
            if ($subreviewparams) {
                $custom = $lineitem->submissionReview->custom;
                $this->assertEquals($subreviewparams, join("\n", array_map(fn($k) => $k.'='.$custom[$k], array_keys($custom))));
            } else {
                $this->assertFalse(isset($this->submissionReview->custom));
            }
        }

        $gradeitems = $gbservice->get_lineitems($course->id, $resourceid, null, null, null, null, $typeid);
        $this->assertEquals(1, $gradeitems[0]);

        if (isset($ltiinstance)) {
            $gradeitems = $gbservice->get_lineitems($course->id, null, $ltiinstance->id, null, null, null, $typeid);
            $this->assertEquals(1, $gradeitems[0]);
            $gradeitems = $gbservice->get_lineitems($course->id, null, $ltiinstance->id + 1, null, null, null, $typeid);
            $this->assertEquals(0, $gradeitems[0]);
        }

        $gradeitems = $gbservice->get_lineitems($course->id, null, null, $tag, null, null, $typeid);
        $this->assertEquals(1, $gradeitems[0]);

        $gradeitems = $gbservice->get_lineitems($course->id, 'an unknown resource id', null, null, null, null, $typeid);
        $this->assertEquals(0, $gradeitems[0]);

        $gradeitems = $gbservice->get_lineitems($course->id, null, null, 'an unknown tag', null, null, $typeid);
        $this->assertEquals(0, $gradeitems[0]);
    }

    /**
     * Inserts a graded lti instance, which should create a grade_item and gradebookservices record.
     *
     * @param int $typeid Type ID of the LTI Tool.
     * @param object $course course where to add the lti instance.
     * @param string|null $resourceid resource id
     * @param string|null $tag tag
     * @param string|null $subreviewurl submission review url
     * @param string|null $subreviewparams submission review custom params
     *
     * @return object lti instance created
     */
    private function create_graded_lti(int $typeid, object $course, ?string $resourceid, ?string $tag,
            ?string $subreviewurl = null, ?string $subreviewparams = null) : object {

        $lti = ['course' => $course->id,
            'typeid' => $typeid,
            'instructorchoiceacceptgrades' => LTI_SETTING_ALWAYS,
            'grade' => 10,
            'lineitemresourceid' => $resourceid,
            'lineitemtag' => $tag,
            'lineitemsubreviewurl' => $subreviewurl,
            'lineitemsubreviewparams' => $subreviewparams];

        return $this->getDataGenerator()->create_module('lti', $lti, array());
    }

     /**
      * Inserts an lti instance that is not graded.
      *
      * @param int $typeid Type Id of the LTI Tool.
      * @param object $course course where to add the lti instance.
      *
      * @return object lti instance created
      */
    private function create_notgraded_lti(int $typeid, object $course) : object {

        $lti = ['course' => $course->id,
            'typeid' => $typeid,
            'instructorchoiceacceptgrades' => LTI_SETTING_NEVER];

        return $this->getDataGenerator()->create_module('lti', $lti, array());
    }

    /**
     * Inserts a standalone lineitem (gradeitem, gradebookservices entries).
     *
     * @param int $courseid Id of the course where the standalone line item will be added.
     * @param int $typeid of the LTI Tool
     * @param string|null $resourceid resource id
     * @param string|null $tag tag
     * @param int|null $ltiinstanceid Id of the LTI instance the standalone line item will be related to.
     *
     */
    private function create_standalone_lineitem(int $courseid, int $typeid, ?string $resourceid,
            ?string $tag, int $ltiinstanceid = null) : void {
        $gbservice = new gradebookservices();
        $gbservice->add_standalone_lineitem($courseid,
            "manualtest",
            10,
            "https://test.phpunit",
            $ltiinstanceid,
            $resourceid,
            $tag,
            $typeid,
            null /*toolproxyid*/);
    }

    /**
     * Creates a new LTI Tool Type.
     */
    private function create_type() {
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = "Test client ID";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();
        $config->ltiservice_gradesynchronization = 2;
        return lti_add_type($type, $config);
    }
}
