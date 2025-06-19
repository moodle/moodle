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

use ltiservice_gradebookservices\local\resources\lineitem;
use ltiservice_gradebookservices\local\resources\results;
use ltiservice_gradebookservices\local\resources\scores;
use ltiservice_gradebookservices\local\service\gradebookservices;

/**
 * Unit tests for lti lineitem.
 *
 * @package    ltiservice_gradebookservices
 * @category   test
 * @copyright  2022 Cengage Group <claude.vervoort@cengage.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_lti\service\gradebookservices\local\resources\lineitem
 */
final class lineitem_test extends \advanced_testcase {

    /**
     * @covers ::execute
     *
     * Test updating the line item with submission review.
     */
    public function test_execute_put_nosubreview(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $resourceid = 'test-resource-id';
        $tag = 'tag';
        $course = $this->getDataGenerator()->create_course();
        $typeid = $this->create_type();

        // The 1st item in the array is the items count.

        $gbservice = new gradebookservices();
        $gbservice->set_type(lti_get_type($typeid));
        $this->create_graded_lti($typeid, $course, $resourceid, $tag);
        $gradeitems = $gbservice->get_lineitems($course->id, null, null, null, null, null, $typeid);
        $this->assertEquals(1, $gradeitems[0]);
        $lineitem = gradebookservices::item_for_json($gradeitems[1][0], '', $typeid);
        $this->assertFalse(isset($lineitem->submissionReview));

        $lineitemresource = new lineitem($gbservice);

        $this->set_server_for_put($course, $typeid, $lineitem);

        $response = new \mod_lti\local\ltiservice\response();
        $lineitem->resourceId = $resourceid.'modified';
        $lineitem->tag = $tag.'modified';
        $response->set_request_data(json_encode($lineitem));

        $lineitemresource->execute($response);

        $lineitem = gradebookservices::item_for_json($gradeitems[1][0], '', $typeid);
        $this->assertFalse(isset($lineitem->submissionReview));
        $this->assertEquals($resourceid.'modified', $lineitem->resourceId);
        $this->assertEquals($tag.'modified', $lineitem->tag);
        $responseitem = json_decode($response->get_body());
        $this->assertEquals($resourceid.'modified', $responseitem->resourceId);
    }

    /**
     * @covers ::execute
     *
     * Test updating the line item with submission review.
     */
    public function test_execute_put_withsubreview(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $resourceid = 'test-resource-id';
        $tag = 'tag';
        $subreviewurl = 'https://subreview.example.com';
        $subreviewparams = 'a=2';
        $course = $this->getDataGenerator()->create_course();
        $typeid = $this->create_type();

        // The 1st item in the array is the items count.

        $gbservice = new gradebookservices();
        $gbservice->set_type(lti_get_type($typeid));
        $this->create_graded_lti($typeid, $course, $resourceid, $tag, $subreviewurl, $subreviewparams);
        $gradeitems = $gbservice->get_lineitems($course->id, null, null, null, null, null, $typeid);
        $this->assertEquals(1, $gradeitems[0]);
        $lineitem = gradebookservices::item_for_json($gradeitems[1][0], '', $typeid);
        $this->assertTrue(isset($lineitem->submissionReview));

        $lineitemresource = new lineitem($gbservice);

        $this->set_server_for_put($course, $typeid, $lineitem);

        $response = new \mod_lti\local\ltiservice\response();
        $lineitem->resourceId = $resourceid.'modified';
        $lineitem->tag = $tag.'modified';
        $lineitem->submissionReview->url = $subreviewurl.'modified';
        $lineitem->submissionReview->custom = ['a' => '3'];
        $response->set_request_data(json_encode($lineitem));

        $lineitemresource->execute($response);

        $lineitem = gradebookservices::item_for_json($gradeitems[1][0], '', $typeid);
        $this->assertEquals($resourceid.'modified', $lineitem->resourceId);
        $this->assertEquals($subreviewurl.'modified', $lineitem->submissionReview->url);
        $custom = $lineitem->submissionReview->custom;
        $this->assertEquals('a=3', join("\n", array_map(fn($k) => $k.'='.$custom[$k], array_keys($custom))));

        $responseitem = json_decode($response->get_body());
        $this->assertEquals($resourceid.'modified', $responseitem->resourceId);
        $this->assertEquals($subreviewurl.'modified', $responseitem->submissionReview->url);
    }

    /**
     * @covers ::execute
     *
     * Test updating the line item with submission review.
     */
    public function test_execute_put_addsubreview(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $resourceid = 'test-resource-id';
        $tag = 'tag';
        $subreviewurl = 'https://subreview.example.com';
        $course = $this->getDataGenerator()->create_course();
        $typeid = $this->create_type();

        // The 1st item in the array is the items count.

        $gbservice = new gradebookservices();
        $gbservice->set_type(lti_get_type($typeid));
        $this->create_graded_lti($typeid, $course, $resourceid, $tag);
        $gradeitems = $gbservice->get_lineitems($course->id, null, null, null, null, null, $typeid);
        $this->assertEquals(1, $gradeitems[0]);
        $lineitem = gradebookservices::item_for_json($gradeitems[1][0], '', $typeid);
        $this->assertFalse(isset($lineitem->submissionReview));

        $lineitemresource = new lineitem($gbservice);

        $this->set_server_for_put($course, $typeid, $lineitem);

        $response = new \mod_lti\local\ltiservice\response();
        $lineitem->resourceId = $resourceid.'modified';
        $lineitem->tag = $tag.'modified';
        $lineitem->submissionReview = ['url' => $subreviewurl];
        $response->set_request_data(json_encode($lineitem));

        $lineitemresource->execute($response);

        $lineitem = gradebookservices::item_for_json($gradeitems[1][0], '', $typeid);
        $this->assertEquals($resourceid.'modified', $lineitem->resourceId);
        $this->assertEquals($subreviewurl, $lineitem->submissionReview->url);
        $this->assertFalse(isset($lineitem->submissionReview->custom));

        $responseitem = json_decode($response->get_body());
        $this->assertEquals($resourceid.'modified', $responseitem->resourceId);
        $this->assertEquals($subreviewurl, $responseitem->submissionReview->url);
        $this->assertFalse(isset($responseitem->submissionReview->custom));
    }

    /**
     * Test running a series of score updates, highlighting problems with the score posting logic.
     *
     * @covers ::execute
     *
     * @return void
     */
    public function test_sequential_score_posts(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $this->resetAfterTest();
        $resourceid = 'test-resource-id';
        $tag = 'tag';
        $course = $this->getDataGenerator()->create_course();
        $typeid = $this->create_type();
        $user = $this->getDataGenerator()->create_and_enrol($course);

        // Create mod instance with line item - nothing pushed via services yet.
        $gbservice = new gradebookservices();
        $gbservice->set_type(lti_get_type($typeid));
        $modinstance = $this->create_graded_lti($typeid, $course, $resourceid, $tag);
        $gradeitems = $gbservice->get_lineitems($course->id, null, null, null, null, null, $typeid);
        $this->assertEquals(1, $gradeitems[0]); // The 1st item in the array is the items count.

        // Post a score so that there's at least one grade present at the time of lineitem update.
        $score = new scores($gbservice);
        $_SERVER['REQUEST_METHOD'] = \mod_lti\local\ltiservice\resource_base::HTTP_POST;
        $_SERVER['PATH_INFO'] = "/$course->id/lineitems/{$gradeitems[1][0]->id}/lineitem/scores?type_id=$typeid";
        $token = lti_new_access_token($typeid, ['https://purl.imsglobal.org/spec/lti-ags/scope/score']);
        $_SERVER['HTTP_Authorization'] = 'Bearer '.$token->token;
        $_GET['type_id'] = (string)$typeid;
        $requestdata = [
            'scoreGiven' => "8.0",
            'scoreMaximum' => "10.0",
            'activityProgress' => "Completed",
            'timestamp' => "2024-08-07T18:54:36.736+00:00",
            'gradingProgress' => "FullyGraded",
            'userId' => $user->id,
        ];
        $response = new \mod_lti\local\ltiservice\response();
        $response->set_content_type('application/vnd.ims.lis.v1.score+json');
        $response->set_request_data(json_encode($requestdata));
        $score->execute($response);

        // The grade in Moodle should reflect the score->timestamp, not the time of the score post.
        $grades = grade_get_grades($course->id, 'mod', 'lti', $modinstance->id, $user->id);
        $studentgrade = array_shift($grades->items[0]->grades);
        $this->assertEquals(strtotime($requestdata['timestamp']), $studentgrade->dategraded);

        // Read the results via the service. This should also return the correct dategraded time (timemodified in LTI terms).
        $_SERVER['REQUEST_METHOD'] = \mod_lti\local\ltiservice\resource_base::HTTP_GET;
        $_SERVER['PATH_INFO'] = "/$course->id/lineitems/{$gradeitems[1][0]->id}/lineitem/results?type_id=$typeid";
        $token = lti_new_access_token($typeid, ['https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly']);
        $_SERVER['HTTP_Authorization'] = 'Bearer '.$token->token;
        $_GET['type_id'] = (string)$typeid;
        $result = new results($gbservice);
        $response = new \mod_lti\local\ltiservice\response();
        $response->set_content_type('application/vnd.ims.lis.v2.resultcontainer+json');
        $result->execute($response);
        $body = json_decode($response->get_body());
        $result = array_shift($body);
        $this->assertEquals(strtotime($requestdata['timestamp']), strtotime($result->timestamp));

        // Now, try to post a newer score using a timestamp that is greater than the one originally sent, but less than the time at
        // which the score was last posted. This should be valid since the timestamp is greater than the original posted score.
        $_SERVER['REQUEST_METHOD'] = \mod_lti\local\ltiservice\resource_base::HTTP_POST;
        $_SERVER['PATH_INFO'] = "/$course->id/lineitems/{$gradeitems[1][0]->id}/lineitem/scores?type_id=$typeid";
        $token = lti_new_access_token($typeid, ['https://purl.imsglobal.org/spec/lti-ags/scope/score']);
        $_SERVER['HTTP_Authorization'] = 'Bearer '.$token->token;
        $_GET['type_id'] = (string)$typeid;
        $requestdata['scoreGiven'] = "14";
        $requestdata['timestamp'] = "2024-08-08T18:54:36.736+00:00";
        $response = new \mod_lti\local\ltiservice\response();
        $response->set_content_type('application/vnd.ims.lis.v1.score+json');
        $response->set_request_data(json_encode($requestdata));
        $score->execute($response);
        $this->assertEquals(200, json_decode($response->get_code()));

        // Finally, post a score that's just been updated (i.e. score->timestamp = now).
        $_SERVER['REQUEST_METHOD'] = \mod_lti\local\ltiservice\resource_base::HTTP_POST;
        $_SERVER['PATH_INFO'] = "/$course->id/lineitems/{$gradeitems[1][0]->id}/lineitem/scores?type_id=$typeid";
        $token = lti_new_access_token($typeid, ['https://purl.imsglobal.org/spec/lti-ags/scope/score']);
        $_SERVER['HTTP_Authorization'] = 'Bearer '.$token->token;
        $_GET['type_id'] = (string)$typeid;
        $requestdata['scoreGiven'] = "15";
        $requestdata['timestamp'] = date('c', time());
        $response = new \mod_lti\local\ltiservice\response();
        $response->set_content_type('application/vnd.ims.lis.v1.score+json');
        $response->set_request_data(json_encode($requestdata));
        $score->execute($response);
        $this->assertEquals(200, json_decode($response->get_code()));
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
            ?string $subreviewurl = null, ?string $subreviewparams = null): object {

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

    /**
     * Sets the server info and get to be configured for a PUT operation,
     * including having a proper auth token attached.
     *
     * @param object $course course where to add the lti instance.
     * @param int $typeid
     * @param object $lineitem
     */
    private function set_server_for_put(object $course, int $typeid, object $lineitem) {
        $_SERVER['REQUEST_METHOD'] = \mod_lti\local\ltiservice\resource_base::HTTP_PUT;
        $_SERVER['PATH_INFO'] = "/$course->id/lineitems$lineitem->id";

        $token = lti_new_access_token($typeid, ['https://purl.imsglobal.org/spec/lti-ags/scope/lineitem']);
        $_SERVER['HTTP_Authorization'] = 'Bearer '.$token->token;
        $_GET['type_id'] = (string)$typeid;
    }
}
