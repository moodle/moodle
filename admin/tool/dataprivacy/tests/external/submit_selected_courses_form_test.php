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
 * This is the external method for submit selected courses.
 *
 * @package    tool_dataprivacy
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use tool_dataprivacy\api;

/**
 * External function submit_selected_courses_form_test.
 *
 * @package    tool_dataprivacy
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \tool_dataprivacy\api
 */
class submit_selected_courses_form_test extends \externallib_advanced_testcase {
    /**
     * Test for submit_selected_courses_form().
     */
    public function test_submit_selected_courses_form() {
        global $DB;
        $this->resetAfterTest();

        set_config('allowfiltering', 1, 'tool_dataprivacy');
        $generator = new \testing_data_generator();
        $s1 = $generator->create_user();
        $s1->ignoresesskey = true;
        $u1 = $generator->create_user();
        $u1->ignoresesskey = true;

        $context = \context_system::instance();
        $course = $this->getDataGenerator()->create_course([]);

        $coursecontext1 = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($s1->id, $course->id, 'student');

        // Manager role.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        // Give the manager role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);
        // Assign u1 as a manager.
        role_assign($managerroleid, $u1->id, $context->id);

        // Map the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');

        // Create the sample data request.
        $this->setUser($s1);
        $datarequest = api::create_data_request($s1->id, api::DATAREQUEST_TYPE_EXPORT);
        $requestid = $datarequest->get('id');

        // Make this ready for approval.
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        $this->setUser($u1);
        $jsonstring = "requestid=" . $requestid . "&sesskey=" . sesskey() .
                "&_qf__tool_dataprivacy_form_exportfilter_form=1&coursecontextids%5B%5D=" . $coursecontext1->id;
        $results = submit_selected_courses_form::execute($requestid, json_encode($jsonstring));
        $this->assertTrue($results["result"]);
    }
}
