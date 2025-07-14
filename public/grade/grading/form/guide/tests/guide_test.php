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

namespace gradingform_guide;

use gradingform_controller;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/grading/lib.php');
require_once($CFG->dirroot . '/grade/grading/form/guide/lib.php');

/**
 * Test cases for the Marking Guide.
 *
 * @package    gradingform_guide
 * @category   test
 * @copyright  2015 Nikita Kalinin <nixorv@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class guide_test extends \advanced_testcase {
    /**
     * Unit test to get draft instance and create new instance.
     */
    public function test_get_or_create_instance(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create fake areas.
        $fakearea = (object)array(
            'contextid'    => 1,
            'component'    => 'mod_assign',
            'areaname'     => 'submissions',
            'activemethod' => 'guide'
        );
        $fakearea1id = $DB->insert_record('grading_areas', $fakearea);
        $fakearea->contextid = 2;
        $fakearea2id = $DB->insert_record('grading_areas', $fakearea);

        // Create fake definitions.
        $fakedefinition = (object)array(
            'areaid'       => $fakearea1id,
            'method'       => 'guide',
            'name'         => 'fakedef',
            'status'       => gradingform_controller::DEFINITION_STATUS_READY,
            'timecreated'  => 0,
            'usercreated'  => 1,
            'timemodified' => 0,
            'usermodified' => 1,
        );
        $fakedef1id = $DB->insert_record('grading_definitions', $fakedefinition);
        $fakedefinition->areaid = $fakearea2id;
        $fakedef2id = $DB->insert_record('grading_definitions', $fakedefinition);

        // Create fake guide instance in first area.
        $fakeinstance = (object)array(
            'definitionid'   => $fakedef1id,
            'raterid'        => 1,
            'itemid'         => 1,
            'rawgrade'       => null,
            'status'         => 0,
            'feedback'       => null,
            'feedbackformat' => 0,
            'timemodified'   => 0
        );
        $fakeinstanceid = $DB->insert_record('grading_instances', $fakeinstance);

        $manager1 = get_grading_manager($fakearea1id);
        $manager2 = get_grading_manager($fakearea2id);
        $controller1 = $manager1->get_controller('guide');
        $controller2 = $manager2->get_controller('guide');

        $instance1 = $controller1->get_or_create_instance(0, 1, 1);
        $instance2 = $controller2->get_or_create_instance(0, 1, 1);

        // Definitions should not be the same.
        $this->assertEquals(false, $instance1->get_data('definitionid') == $instance2->get_data('definitionid'));
    }
}
