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

namespace core_course;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');

/**
 * Module lib related unit tests
 *
 * @package    core_course
 * @category   test
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modlib_test extends \advanced_testcase {

    /**
     * Test prepare_new_moduleinfo_data
     */
    public function test_prepare_new_moduleinfo_data() {
        global $DB;
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        // Test with a complex module, like assign.
        $assignmodule = $DB->get_record('modules', array('name' => 'assign'), '*', MUST_EXIST);
        $sectionnumber = 1;

        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $assignmodule->name, $sectionnumber);
        $this->assertEquals($assignmodule, $module);
        $this->assertEquals($coursecontext, $context);
        $this->assertNull($cm); // Not cm yet.

        $expecteddata = new \stdClass();
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
        $expecteddata->downloadcontent  = DOWNLOAD_COURSE_CONTENT_ENABLED;

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
     * Test prepare_new_moduleinfo_data with suffix (which is currently only used by the completion rules).
     * @covers ::prepare_new_moduleinfo_data
     */
    public function test_prepare_new_moduleinfo_data_with_suffix() {
        global $DB;
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        // Test with a complex module, like assign.
        $assignmodule = $DB->get_record('modules', ['name' => 'assign'], '*', MUST_EXIST);
        $sectionnumber = 1;

        $suffix = 'mysuffix';
        [$module, $context, $cw, $cm, $data] = prepare_new_moduleinfo_data($course, $assignmodule->name, $sectionnumber, $suffix);
        $this->assertEquals($assignmodule, $module);
        $this->assertEquals($coursecontext, $context);
        $this->assertNull($cm); // Not cm yet.

        $expecteddata = new \stdClass();
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
        $expecteddata->{'completion' . $suffix} = 0;
        $expecteddata->downloadcontent  = DOWNLOAD_COURSE_CONTENT_ENABLED;

        // Unset untestable.
        unset($data->introeditor);
        unset($data->_advancedgradingdata);

        $this->assertEquals($expecteddata, $data);
        $this->assertFalse(property_exists($data, 'completion'));
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
        $assigncontext = \context_module::instance($assign->cmid);

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
        $expecteddata->completionpassgrade = $assigncm->completionpassgrade;
        $expecteddata->completiongradeitemnumber = null;
        $expecteddata->showdescription    = $assigncm->showdescription;
        $expecteddata->downloadcontent    = $assigncm->downloadcontent;
        $expecteddata->tags               = \core_tag_tag::get_item_tags_array('core', 'course_modules', $assigncm->id);
        $expecteddata->lang               = null;
        $expecteddata->availabilityconditionsjson = null;
        $expecteddata->advancedgradingmethod_submissions = null;
        if ($items = \grade_item::fetch_all(array('itemtype' => 'mod', 'itemmodule' => 'assign',
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
        $expecteddata->completionpassgrade = $assigncm->completionpassgrade;

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

    /**
     * Test add_moduleinfo (only beforemod parameter for now).
     *
     * @covers \add_moduleinfo
     */
    public function test_add_moduleinfo() {
        global $DB;
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $course = self::getDataGenerator()->create_course();
        $labelmodule = $DB->get_record('modules', ['name' => 'label'], '*', MUST_EXIST);
        $sectionnumber = 1;
        $modules = [];
        $moduleinfo = [];

        for ($i = 0; $i < 4; $i++) {
            $modules[$i] = self::getDataGenerator()->create_module('label', ['course' => $course->id, 'section' => $sectionnumber]);
            $modulescm[$i] = get_coursemodule_from_id('label', $modules[$i]->cmid);
        }

        $modules[4] = self::getDataGenerator()->create_module('label', ['course' => $course->id, 'section' => $sectionnumber + 1]);
        $modulescm[4] = get_coursemodule_from_id('label', $modules[4]->cmid);

        // The beforemod attribute is not set, should be null afterwards.
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $labelmodule->name, $sectionnumber);
        $moduleinfo[0] = add_moduleinfo($data, $course);
        $this->assertEquals(null, $moduleinfo[0]->beforemod);

        // Insert before the first module.
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $labelmodule->name, $sectionnumber);
        $data->beforemod = $modulescm[0]->id;
        $moduleinfo[1] = add_moduleinfo($data, $course);
        $this->assertEquals($modulescm[0]->id, $moduleinfo[1]->beforemod);

        // Insert between the two last modules.
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $labelmodule->name, $sectionnumber);
        $data->beforemod = $modulescm[3]->id;
        $moduleinfo[2] = add_moduleinfo($data, $course);
        $this->assertEquals($modulescm[3]->id, $moduleinfo[2]->beforemod);

        // Insert before a not existing module.
        course_delete_module($modulescm[2]->id);

        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $labelmodule->name, $sectionnumber);
        $data->beforemod = $modulescm[2]->id;
        $moduleinfo[3] = add_moduleinfo($data, $course);
        $this->assertEquals($modulescm[2]->id, $moduleinfo[3]->beforemod);

        // Insert before a module that is in another section.
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $labelmodule->name, $sectionnumber);
        $data->beforemod = $modulescm[4]->id;
        $moduleinfo[4] = add_moduleinfo($data, $course);
        $this->assertEquals($modulescm[4]->id, $moduleinfo[4]->beforemod);

        $modinfo = get_fast_modinfo($course);

        $expectedorder = [
            $moduleinfo[1]->coursemodule,
            $modulescm[0]->id,
            $modulescm[1]->id,
            $moduleinfo[2]->coursemodule,
            $modulescm[3]->id,
            $moduleinfo[0]->coursemodule,
            $moduleinfo[3]->coursemodule,
            $moduleinfo[4]->coursemodule,
        ];

        $this->assertEquals($expectedorder, $modinfo->get_sections()[$sectionnumber]);
    }

}
