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
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Tests for customfields in courses
 *
 * @package    core_course
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class customfield_test extends \advanced_testcase {

    /**
     * Set up
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();

        $dg = self::getDataGenerator();
        $catid = $dg->create_custom_field_category([])->get('id');
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'text', 'shortname' => 'f1']);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'checkbox', 'shortname' => 'f2']);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'date', 'shortname' => 'f3',
            'configdata' => ['startyear' => 2000, 'endyear' => 3000, 'includetime' => 1]]);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'select', 'shortname' => 'f4',
            'configdata' => ['options' => "a\nb\nc"]]);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'textarea', 'shortname' => 'f5']);
    }

    /**
     * Test creating course with customfields and retrieving them
     */
    public function test_create_course(): void {
        global $DB;
        $dg = $this->getDataGenerator();

        $now = time();
        $c1 = $dg->create_course(['shortname' => 'SN', 'fullname' => 'FN',
            'summary' => 'DESC', 'summaryformat' => FORMAT_MOODLE,
            'customfield_f1' => 'some text', 'customfield_f2' => 1,
            'customfield_f3' => $now, 'customfield_f4' => 2,
            'customfield_f5_editor' => ['text' => 'test', 'format' => FORMAT_HTML]]);

        $data = \core_course\customfield\course_handler::create()->export_instance_data_object($c1->id);

        $this->assertEquals('some text', $data->f1);
        $this->assertEquals('Yes', $data->f2);
        $this->assertEquals(userdate($now, get_string('strftimedaydatetime')), $data->f3);
        $this->assertEquals('b', $data->f4);
        $this->assertEquals('test', $data->f5);

        $this->assertEquals(5, count($DB->get_records('customfield_data')));

        delete_course($c1->id, false);

        $this->assertEquals(0, count($DB->get_records('customfield_data')));
    }

    /**
     * Backup a course and return its backup ID.
     *
     * @param int $courseid The course ID.
     * @param int $userid The user doing the backup.
     * @return string
     */
    protected function backup_course($courseid, $userid = 2) {
        $backuptempdir = make_backup_temp_directory('');
        $packer = get_file_packer('application/vnd.moodle.backup');

        $bc = new \backup_controller(\backup::TYPE_1COURSE, $courseid, \backup::FORMAT_MOODLE, \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL, $userid);
        $bc->execute_plan();

        $results = $bc->get_results();
        $results['backup_destination']->extract_to_pathname($packer, "$backuptempdir/core_course_testcase");

        $bc->destroy();
        unset($bc);
        return 'core_course_testcase';
    }

    /**
     * Restore a course.
     *
     * @param int $backupid The backup ID.
     * @param int $courseid The course ID to restore in, or 0.
     * @param int $userid The ID of the user performing the restore.
     * @return stdClass The updated course object.
     */
    protected function restore_course($backupid, $courseid, $userid) {
        global $DB;

        $target = \backup::TARGET_CURRENT_ADDING;
        if (!$courseid) {
            $target = \backup::TARGET_NEW_COURSE;
            $categoryid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
            $courseid = \restore_dbops::create_new_course('Tmp', 'tmp', $categoryid);
        }

        $rc = new \restore_controller($backupid, $courseid, \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $userid, $target);
        $target == \backup::TARGET_NEW_COURSE ?: $rc->get_plan()->get_setting('overwrite_conf')->set_value(true);
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();

        $course = $DB->get_record('course', array('id' => $rc->get_courseid()));

        $rc->destroy();
        unset($rc);
        return $course;
    }

    /**
     * Test backup and restore of custom fields
     */
    public function test_restore_customfields(): void {
        global $USER;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course(['shortname' => 'SN', 'fullname' => 'FN',
            'summary' => 'DESC', 'summaryformat' => FORMAT_MOODLE,
            'customfield_f1' => 'some text', 'customfield_f2' => 1]);
        $backupid = $this->backup_course($c1->id);

        // The information is restored but adapted because names are already taken.
        $c2 = $this->restore_course($backupid, 0, $USER->id);

        $data = \core_course\customfield\course_handler::create()->export_instance_data_object($c1->id);
        $this->assertEquals('some text', $data->f1);
        $this->assertEquals('Yes', $data->f2);
    }

    /**
     * Delete a category that has fields and the fields have data.
     */
    public function test_delete_category(): void {
        global $DB;
        $dg = $this->getDataGenerator();

        $now = time();
        $c1 = $dg->create_course(['shortname' => 'SN', 'fullname' => 'FN',
            'summary' => 'DESC', 'summaryformat' => FORMAT_MOODLE,
            'customfield_f1' => 'some text', 'customfield_f2' => 1,
            'customfield_f3' => $now, 'customfield_f4' => 2,
            'customfield_f5_editor' => ['text' => 'test', 'format' => FORMAT_HTML]]);

        // Find the category and delete it.
        $cats = \core_course\customfield\course_handler::create()->get_categories_with_fields();
        $cat = reset($cats);
        $cat->get_handler()->delete_category($cat);

        // Course no longer has the customfield properties.
        $course = course_get_format($c1->id)->get_course();
        $keys = array_keys((array)$course);
        $this->assertEmpty(array_intersect($keys, ['customfield_f1', 'customfield_f2',
            'customfield_f3', 'customfield_f4', 'customfield_f5']));

        // Nothing in customfield tables either.
        $this->assertEquals(0, count($DB->get_records('customfield_field')));
        $this->assertEquals(0, count($DB->get_records('customfield_data')));
    }

}
