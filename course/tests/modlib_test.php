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

use core_courseformat\formatactions;

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

    /**
     * Test for can_add_moduleinfo on a non-existing module.
     *
     * @covers \can_add_moduleinfo
     */
    public function test_can_add_moduleinfo_invalid_module(): void {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(
            ['numsections' => 2, 'enablecompletion' => 1],
            ['createsections' => true]
        );

        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(2);

        $this->setAdminUser();

        $this->expectException(\dml_missing_record_exception::class);

        can_add_moduleinfo($course, 'non-existent-module!', $section->section);
    }

    /**
     * Test for can_add_moduleinfo when the user does not have addinstance capability.
     *
     * @covers \can_add_moduleinfo
     */
    public function test_can_add_moduleinfo_deny_add_instance(): void {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(
            ['numsections' => 2, 'enablecompletion' => 1],
            ['createsections' => true]
        );

        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(2);

        // The can_add_moduleinfo uses course_allowed_module to check if the module is allowed in the course.
        // This method uses capabilities like the specific module addinstance capability.
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher'], '*', MUST_EXIST);
        role_change_permission(
            $teacherrole->id,
            \context_course::instance($course->id),
            'mod/label:addinstance',
            CAP_PROHIBIT
        );

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        $this->setUser($user);

        $this->expectException(\moodle_exception::class);

        can_add_moduleinfo($course, 'label', $section->section);
    }

    /**
     * Test for can_add_moduleinfo.
     *
     * @dataProvider provider_can_add_moduleinfo
     * @covers \can_add_moduleinfo
     * @param string $rolename
     * @param bool $hascapability
     */
    public function test_can_add_moduleinfo_capability(string $rolename, bool $hascapability): void {
        global $DB;
        $this->resetAfterTest(true);

        $module = $DB->get_record('modules', ['name' => 'label'], '*', MUST_EXIST);

        $course = $this->getDataGenerator()->create_course(
            ['numsections' => 2, 'enablecompletion' => 1],
            ['createsections' => true]
        );

        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(2);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $rolename);
        $this->setUser($user);

        if (!$hascapability) {
            $this->expectException(\required_capability_exception::class);
        }

        $result = can_add_moduleinfo($course, 'label', $section->section);

        $this->assertEquals($module, $result[0]);
        $this->assertEquals(
            \context_course::instance($course->id),
            $result[1]
        );
        $this->assertEquals($section->id, $result[2]->id);
    }

    /**
     * Test for can_add_moduleinfo returns true on a delegate section.
     *
     * @dataProvider provider_can_add_moduleinfo
     * @covers \can_add_moduleinfo
     * @param string $rolename
     * @param bool $hascapability
     */
    public function test_can_add_moduleinfo_delegate_section(string $rolename, bool $hascapability): void {
        global $DB;
        $this->resetAfterTest(true);

        $module = $DB->get_record('modules', ['name' => 'label'], '*', MUST_EXIST);

        $course = $this->getDataGenerator()->create_course(
            ['numsections' => 2, 'enablecompletion' => 1],
            ['createsections' => true]
        );

        $section = formatactions::section($course)->create_delegated('mod_label', 0);

        $modinfo = get_fast_modinfo($course);
        $this->assertCount(4, $modinfo->get_section_info_all());
        $this->assertCount(3, $modinfo->get_listed_section_info_all());

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $rolename);
        $this->setUser($user);

        if (!$hascapability) {
            $this->expectException(\required_capability_exception::class);
        }

        $result = can_add_moduleinfo($course, 'label', $section->section);

        $this->assertEquals($module, $result[0]);
        $this->assertEquals(
            \context_course::instance($course->id),
            $result[1]
        );
        $this->assertEquals($section->id, $result[2]->id);

        // Validate no section has been created.
        $modinfo = get_fast_modinfo($course);
        $this->assertCount(4, $modinfo->get_section_info_all());
        $this->assertCount(3, $modinfo->get_listed_section_info_all());
        $this->assertEquals(
            $section->section,
            $modinfo->get_section_info_by_id($section->id)->section
        );
    }

    /**
     * Data provider for test_can_add_moduleinfo.
     * @return array
     */
    public static function provider_can_add_moduleinfo(): array {
        return [
            'Editing teacher' => [
                'rolename' => 'editingteacher',
                'hascapability' => true,
            ],
            'Manager' => [
                'rolename' => 'manager',
                'hascapability' => true,
            ],
            'Course creator' => [
                'rolename' => 'coursecreator',
                'hascapability' => false,
            ],
            'Non-editing teacher' => [
                'rolename' => 'teacher',
                'hascapability' => false,
            ],
            'Student' => [
                'rolename' => 'student',
                'hascapability' => false,
            ],
            'Guest' => [
                'rolename' => 'guest',
                'hascapability' => false,
            ],
        ];
    }
}
