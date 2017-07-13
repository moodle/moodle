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
 * Module lib related unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');

class core_course_modlib_testcase extends advanced_testcase {

    /**
     * Test prepare_new_moduleinfo_data
     */
    public function test_prepare_new_moduleinfo_data() {
        global $DB;
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        // Test with a complex module, like assign.
        $assignmodule = $DB->get_record('modules', array('name' => 'assign'), '*', MUST_EXIST);
        $sectionnumber = 1;

        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $assignmodule->name, $sectionnumber);
        $this->assertEquals($assignmodule, $module);
        $this->assertEquals($coursecontext, $context);
        $this->assertNull($cm); // Not cm yet.

        $expecteddata = new stdClass();
        $expecteddata->section          = $sectionnumber;
        $expecteddata->visible          = 1;
        $expecteddata->course           = $course->id;
        $expecteddata->module           = $module->id;
        $expecteddata->modulename       = $module->name;
        $expecteddata->groupmode        = $course->groupmode;
        $expecteddata->groupingid       = $course->defaultgroupingid;
        $expecteddata->id               = '';
        $expecteddata->instance         = '';
        $expecteddata->coursemodule     = '';
        $expecteddata->advancedgradingmethod_submissions = ''; // Not grading methods enabled by default.
        $expecteddata->completion       = 0;
        // Unset untestable.
        unset($data->introeditor);
        unset($data->_advancedgradingdata);

        $this->assertEquals($expecteddata, $data);

        // Create a viewer user. Not able to edit.
        $viewer = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($viewer->id, $course->id);
        $this->setUser($viewer);
        $this->expectException('required_capability_exception');
        prepare_new_moduleinfo_data($course, $assignmodule->name, $sectionnumber);
    }

    /**
     * Test get_moduleinfo_data
     */
    public function test_get_moduleinfo_data() {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $assignmodule = $DB->get_record('modules', array('name' => 'assign'), '*', MUST_EXIST);
        $assign = self::getDataGenerator()->create_module('assign', array('course' => $course->id));
        $assigncm = get_coursemodule_from_id('assign', $assign->cmid);
        $assigncontext = context_module::instance($assign->cmid);

        list($cm, $context, $module, $data, $cw) = get_moduleinfo_data($assigncm, $course);
        $this->assertEquals($assigncm, $cm);
        $this->assertEquals($assigncontext, $context);
        $this->assertEquals($assignmodule, $module);

        // Prepare expected data.
        $expecteddata = clone $assign;
        $expecteddata->coursemodule       = $assigncm->id;
        $expecteddata->section            = $cw->section;
        $expecteddata->visible            = $assigncm->visible;
        $expecteddata->visibleoncoursepage = $assigncm->visibleoncoursepage;
        $expecteddata->cmidnumber         = $assigncm->idnumber;
        $expecteddata->groupmode          = groups_get_activity_groupmode($cm);
        $expecteddata->groupingid         = $assigncm->groupingid;
        $expecteddata->course             = $course->id;
        $expecteddata->module             = $module->id;
        $expecteddata->modulename         = $module->name;
        $expecteddata->instance           = $assigncm->instance;
        $expecteddata->completion         = $assigncm->completion;
        $expecteddata->completionview     = $assigncm->completionview;
        $expecteddata->completionexpected = $assigncm->completionexpected;
        $expecteddata->completionusegrade = is_null($assigncm->completiongradeitemnumber) ? 0 : 1;
        $expecteddata->showdescription    = $assigncm->showdescription;
        $expecteddata->tags               = core_tag_tag::get_item_tags_array('core', 'course_modules', $assigncm->id);
        $expecteddata->availabilityconditionsjson = null;
        $expecteddata->advancedgradingmethod_submissions = null;
        if ($items = grade_item::fetch_all(array('itemtype' => 'mod', 'itemmodule' => 'assign',
                                                    'iteminstance' => $assign->id, 'courseid' => $course->id))) {
            // set category if present
            $gradecat = false;
            foreach ($items as $item) {
                if ($gradecat === false) {
                    $gradecat = $item->categoryid;
                    continue;
                }
                if ($gradecat != $item->categoryid) {
                    //mixed categories
                    $gradecat = false;
                    break;
                }
            }
            if ($gradecat !== false) {
                // do not set if mixed categories present
                $expecteddata->gradecat = $gradecat;
            }
        }
        $expecteddata->gradepass = '0.00';

        // Unset untestable.
        unset($expecteddata->cmid);
        unset($data->introeditor);
        unset($data->_advancedgradingdata);

        $this->assertEquals($expecteddata, $data);

        // Create a viewer user. Not able to edit.
        $viewer = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($viewer->id, $course->id);
        $this->setUser($viewer);
        $this->expectException('required_capability_exception');
        get_moduleinfo_data($assigncm, $course);
    }
}
