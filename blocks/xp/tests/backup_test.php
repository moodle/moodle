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
 * Test backup and retore.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once(__DIR__ . '/fixtures/events.php');

use backup_controller;
use backup;
use base_setting;
use block_manager;
use block_xp_filter;
use block_xp_rule_base;
use block_xp_rule_property;
use block_xp_ruleset;
use block_xp\local\reason\event_name_reason;
use block_xp\tests\base_testcase;
use context_course;
use moodle_page;
use moodle_url;
use restore_controller;
use restore_dbops;

/**
 * Test backup and retore.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \backup_xp_block_task
 * @covers     \backup_xp_block_structure_step
 * @covers     \restore_xp_block_task
 * @covers     \restore_xp_block_structure_step
 */
final class backup_test extends base_testcase {

    /**
     * Test restore in new course.
     *
     * @covers \backup_xp_block_structure_step
     */
    public function test_restore_in_new_course(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $u1 = $data['u1'];
        $u2 = $data['u2'];
        $u3 = $data['u3'];

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        $newid = restore_dbops::create_new_course($c1->fullname . 'new', $c1->shortname . 'new', $c1->category);
        $this->restore($backupid, $newid, backup::TARGET_NEW_COURSE);

        // Validate that it is same as c1.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $newid, 'maxactionspertime' => 8]));
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $newid]));
        $this->assertEquals(10, $DB->get_field('block_xp', 'xp', ['courseid' => $newid, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $newid, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $newid, 'userid' => $u3->id]));
        $this->assertEquals(4, $DB->count_records('block_xp_log', ['courseid' => $newid]));
        $this->assertEquals(6, $DB->count_records('block_xp_filters', ['courseid' => $newid]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['courseid' => $newid, 'points' => 666]));
    }

    /**
     * Test restore in new course without users.
     */
    public function test_restore_in_new_course_without_users(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        $newid = restore_dbops::create_new_course($c1->fullname . 'new', $c1->shortname . 'new', $c1->category);
        $this->restore($backupid, $newid, backup::TARGET_NEW_COURSE, ['users' => false]);

        // Validate that it is same as c1.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $newid, 'maxactionspertime' => 8]));
        $this->assertEquals(0, $DB->count_records('block_xp', ['courseid' => $newid]));
        $this->assertEquals(0, $DB->count_records('block_xp_log', ['courseid' => $newid]));
        $this->assertEquals(6, $DB->count_records('block_xp_filters', ['courseid' => $newid]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['courseid' => $newid, 'points' => 666]));
    }

    /**
     * Test restore merge in other.
     */
    public function test_restore_merge_in_other(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $c2 = $data['c2'];
        $u1 = $data['u1'];
        $u2 = $data['u2'];
        $u3 = $data['u3'];

        $this->setAdminUser();
        $backupid = $this->backup($c1);
        $this->restore($backupid, $c2->id, backup::TARGET_EXISTING_ADDING);

        // Config is not overwritten.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c2->id, 'maxactionspertime' => 18]));
        // U1 already exist and remains unchanged.
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(33, $DB->get_field('block_xp', 'xp', ['courseid' => $c2->id, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $c2->id, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $c2->id, 'userid' => $u3->id]));
        // All logs are imported in.
        $this->assertEquals(5, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));
        // All filters are imported in.
        $this->assertEquals(11, $DB->count_records('block_xp_filters', ['courseid' => $c2->id]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['courseid' => $c2->id, 'points' => 666]));
    }

    /**
     * Test restore delete and merge in other.
     */
    public function test_restore_delete_and_merge_in_other(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $c2 = $data['c2'];
        $u1 = $data['u1'];
        $u2 = $data['u2'];
        $u3 = $data['u3'];

        $this->setAdminUser();
        $backupid = $this->backup($c1);
        $this->restore($backupid, $c2->id, backup::TARGET_EXISTING_DELETING);

        // Everything matches c1 in c2.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c2->id, 'maxactionspertime' => 8]));
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(10, $DB->get_field('block_xp', 'xp', ['courseid' => $c2->id, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $c2->id, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $c2->id, 'userid' => $u3->id]));
        $this->assertEquals(4, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));
        $this->assertEquals(6, $DB->count_records('block_xp_filters', ['courseid' => $c2->id]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['courseid' => $c2->id, 'points' => 666]));

        // Validate nothing changed in other courses (c1).
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c1->id, 'maxactionspertime' => 8]));
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $c1->id]));
        $this->assertEquals(10, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u3->id]));
        $this->assertEquals(4, $DB->count_records('block_xp_log', ['courseid' => $c1->id]));
        $this->assertEquals(6, $DB->count_records('block_xp_filters', ['courseid' => $c1->id]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['courseid' => $c1->id, 'points' => 666]));
    }

    /**
     * Test restore merge in same without change.
     */
    public function test_restore_merge_in_same_without_change(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $u1 = $data['u1'];
        $u2 = $data['u2'];
        $u3 = $data['u3'];

        $this->setAdminUser();
        $backupid = $this->backup($c1);
        $this->restore($backupid, $c1->id, backup::TARGET_EXISTING_ADDING);

        // Config is not overwritten.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c1->id, 'maxactionspertime' => 8]));
        // All entries already exist and remains unchanged.
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $c1->id]));
        $this->assertEquals(10, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u3->id]));
        // All logs are imported in.
        $this->assertEquals(8, $DB->count_records('block_xp_log', ['courseid' => $c1->id]));
        // All filters are imported in.
        $this->assertEquals(12, $DB->count_records('block_xp_filters', ['courseid' => $c1->id]));
        $this->assertEquals(2, $DB->count_records('block_xp_filters', ['courseid' => $c1->id, 'points' => 666]));
    }

    /**
     * Test restore merge in same with changes.
     */
    public function test_restore_merge_in_same_with_changes(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $u1 = $data['u1'];
        $u2 = $data['u2'];
        $u3 = $data['u3'];
        $w1 = $data['w1'];

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        // Applying minor changes.
        $w1->get_config()->set('maxactionspertime', 999);
        $w1->get_store()->increase_with_reason($u1->id, 89, new event_name_reason('core\event\something'));
        $rule = new \block_xp_rule_property(\block_xp_rule_base::CT, 'something', 'eventname');
        block_xp_filter::load_from_data(['rule' => $rule, 'points' => 1337, 'courseid' => $c1->id, 'sortorder' => 0])->save();

        $this->restore($backupid, $c1->id, backup::TARGET_EXISTING_ADDING);

        // Config is not overwritten.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c1->id, 'maxactionspertime' => 999]));
        // All entries already exist and remains unchanged.
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $c1->id]));
        $this->assertEquals(99, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u3->id]));
        // All logs are imported in.
        $this->assertEquals(9, $DB->count_records('block_xp_log', ['courseid' => $c1->id]));
        // All filters are imported in.
        $this->assertEquals(13, $DB->count_records('block_xp_filters', ['courseid' => $c1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $c1->id, 'points' => 1337]));
        $this->assertEquals(2, $DB->count_records('block_xp_filters', ['courseid' => $c1->id, 'points' => 666]));
    }

    /**
     * Test restore delete and merge in same.
     */
    public function test_restore_delete_and_merge_in_same(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $u1 = $data['u1'];
        $u2 = $data['u2'];
        $u3 = $data['u3'];
        $w1 = $data['w1'];

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        // Applying minor changes.
        $w1->get_config()->set('maxactionspertime', 999);
        $w1->get_store()->increase_with_reason($u1->id, 89, new event_name_reason('core\event\something'));
        $rule = new \block_xp_rule_property(\block_xp_rule_base::CT, 'something', 'eventname');
        block_xp_filter::load_from_data(['rule' => $rule, 'points' => 1337, 'courseid' => $c1->id, 'sortorder' => 0])->save();

        $this->restore($backupid, $c1->id, backup::TARGET_EXISTING_DELETING);

        // Exactly as contained in the backup.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c1->id, 'maxactionspertime' => 8]));
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $c1->id]));
        $this->assertEquals(10, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u3->id]));
        $this->assertEquals(4, $DB->count_records('block_xp_log', ['courseid' => $c1->id]));
        $this->assertEquals(6, $DB->count_records('block_xp_filters', ['courseid' => $c1->id]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['courseid' => $c1->id, 'points' => 666]));
    }

    /**
     * Test restore grade filters with non existing.
     */
    public function test_restore_grade_filter_with_none_existing(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $c2 = $data['c2'];
        $w1 = $data['w1'];

        $fm = $w1->get_filter_manager();
        $fm->purge();

        $rule = new block_xp_ruleset([new block_xp_rule_property(block_xp_rule_base::CT, 'something', 'eventname')]);
        block_xp_filter::load_from_data(['rule' => $rule, 'points' => 1, 'courseid' => $c1->id, 'sortorder' => 0,
            'category' => block_xp_filter::CATEGORY_GRADES, ])->save();

        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        $newid = restore_dbops::create_new_course('xptest', 'xptest', $c1->category);
        $this->restore($backupid, $newid, backup::TARGET_NEW_COURSE);

        // The filter has been restored.
        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $newid,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));
    }

    /**
     * Test restore grade filters with one existing.
     */
    public function test_restore_grade_filter_with_one_existing(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $c2 = $data['c2'];
        $w1 = $data['w1'];

        $fm = $w1->get_filter_manager();
        $fm->purge();

        $rule = new block_xp_ruleset([new block_xp_rule_property(block_xp_rule_base::CT, 'something', 'eventname')]);
        $filter = block_xp_filter::load_from_data(['rule' => $rule, 'points' => 1, 'courseid' => $c1->id, 'sortorder' => 0,
            'category' => block_xp_filter::CATEGORY_GRADES, ]);
        $filter->save();

        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        $this->restore($backupid, $c1->id, backup::TARGET_CURRENT_ADDING);

        // Another filter has not been created.
        $this->assertTrue($DB->record_exists('block_xp_filters', ['id' => $filter->get_id()]));
        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));
    }

    /**
     * Test restore grade filters with one non ruleset existing.
     */
    public function test_restore_grade_filter_with_one_non_ruleset_existing(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $c2 = $data['c2'];
        $w1 = $data['w1'];

        $fm = $w1->get_filter_manager();
        $fm->purge();

        $rule = new block_xp_rule_property(block_xp_rule_base::CT, 'something', 'eventname');
        $filter = block_xp_filter::load_from_data(['rule' => $rule, 'points' => 1, 'courseid' => $c1->id, 'sortorder' => 0,
            'category' => block_xp_filter::CATEGORY_GRADES, ]);
        $filter->save();

        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        $this->restore($backupid, $c1->id, backup::TARGET_CURRENT_ADDING);

        // Another filter has not been created.
        $this->assertTrue($DB->record_exists('block_xp_filters', ['id' => $filter->get_id()]));
        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));
    }

    /**
     * Test restore grade filters with many existing.
     */
    public function test_restore_grade_filter_with_many_existing(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $c2 = $data['c2'];
        $w1 = $data['w1'];

        $fm = $w1->get_filter_manager();
        $fm->purge();

        $rule = new block_xp_ruleset([new block_xp_rule_property(block_xp_rule_base::CT, 'something', 'eventname')]);
        $filter1 = block_xp_filter::load_from_data(['rule' => $rule, 'points' => 1, 'courseid' => $c1->id, 'sortorder' => 0,
            'category' => block_xp_filter::CATEGORY_GRADES, ]);
        $filter1->save();
        $rule = new block_xp_ruleset([new block_xp_rule_property(block_xp_rule_base::CT, 'somethingelse', 'eventname')]);
        $filter2 = block_xp_filter::load_from_data(['rule' => $rule, 'points' => 2, 'courseid' => $c1->id, 'sortorder' => 0,
            'category' => block_xp_filter::CATEGORY_GRADES, ]);
        $filter2->save();

        $this->assertEquals(2, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));

        $this->setAdminUser();
        $backupid = $this->backup($c1);

        $this->restore($backupid, $c1->id, backup::TARGET_CURRENT_ADDING);

        // Another filter has not been created.
        $this->assertTrue($DB->record_exists('block_xp_filters', ['id' => $filter1->get_id()]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['id' => $filter2->get_id()]));
        $this->assertEquals(2, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));
    }

    /**
     * Test restore grade filters with one empty existing.
     */
    public function test_restore_grade_filter_with_one_empty_existing(): void {
        global $DB;

        $data = $this->setup_courses();
        $c1 = $data['c1'];
        $c2 = $data['c2'];
        $w1 = $data['w1'];

        $fm = $w1->get_filter_manager();
        $fm->purge();

        $rule = new block_xp_ruleset([new block_xp_rule_property(block_xp_rule_base::CT, 'something', 'eventname')]);
        $filter = block_xp_filter::load_from_data(['rule' => $rule, 'points' => 1, 'courseid' => $c1->id, 'sortorder' => 0,
            'category' => block_xp_filter::CATEGORY_GRADES, ]);
        $filter->save();
        $origfilterid = $filter->get_id();
        $this->assertNotEmpty($filter->get_rule()->get_rules());

        // Creating backup with a non-empty rule.
        $this->setAdminUser();
        $backupid = $this->backup($c1);

        // Changing existing rule to empty rule.
        $rule = new block_xp_ruleset([]);
        $filter->set_rule($rule);
        $filter->save();

        $record = $DB->get_record('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]);
        $dbfilter = block_xp_filter::load_from_data($record);
        $this->assertEquals($filter->get_id(), $dbfilter->get_id());
        $this->assertEmpty($filter->get_rule()->get_rules());
        $this->assertEmpty($dbfilter->get_rule()->get_rules());

        // Restoring the backup.
        $this->restore($backupid, $c1->id, backup::TARGET_CURRENT_ADDING);

        // The filter has been replaced.
        $this->assertEquals(1, $DB->count_records('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]));
        $record = $DB->get_record('block_xp_filters', ['courseid' => $c1->id,
            'category' => block_xp_filter::CATEGORY_GRADES, ]);
        $filter = block_xp_filter::load_from_data($record);
        $this->assertEquals($origfilterid, $filter->get_id());
        $this->assertNotEmpty($filter->get_rule()->get_rules());
    }

    /**
     * Backs a course up to temp directory.
     *
     * Inspired from tool_log.
     *
     * @param \stdClass $course Course object to backup.
     * @return string ID of backup.
     */
    protected function backup($course) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just create the directory and not zip it.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id,
            backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_IMPORT,
            $USER->id);

        $settings = [
            'users' => true,
            'groups' => true,
            'blocks' => true,
            'role_assignments' => false,
            'permissions' => false,
            'files' => false,
            'filters' => false,
            'comments' => false,
            'badges' => false,
            'calendarevents' => false,
            'userscompletion' => false,
            'logs' => false,
            'grade_histories' => false,
            'questionbank' => false,
            'competencies' => false,
            'customfield' => false,
            'contentbankcontent' => false,
            'legacyfiles' => false,
        ];

        $plan = $bc->get_plan();
        foreach ($settings as $name => $value) {
            if (!$plan->setting_exists($name)) {
                continue;
            }
            $setting = $plan->get_setting($name);
            if ($setting->get_status() != base_setting::NOT_LOCKED) {
                $setting->set_status(base_setting::NOT_LOCKED);
            }
            $setting->set_value($value);
        }

        $backupid = $bc->get_backupid();

        $bc->execute_plan();
        $bc->destroy();
        return $backupid;
    }

    /**
     * Restore a course.
     *
     * @param string $backupid Backup ID.
     * @param string $courseid Destination course ID.
     * @param int $target The backup::TARGET_* constant.
     * @param array $settings Backup settings.
     */
    protected function restore($backupid, $courseid, $target, $settings = []) {
        global $USER;

        $rc = new restore_controller($backupid, $courseid, backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id, $target);

        $settings += [
            'users' => true,
            'groups' => true,
            'blocks' => true,
            'role_assignments' => false,
            'permissions' => false,
            'files' => false,
            'filters' => false,
            'comments' => false,
            'badges' => false,
            'calendarevents' => false,
            'userscompletion' => false,
            'logs' => false,
            'grade_histories' => false,
            'questionbank' => false,
            'competencies' => false,
            'customfield' => false,
            'contentbankcontent' => false,
            'legacyfiles' => false,
        ];

        $plan = $rc->get_plan();
        foreach ($settings as $name => $value) {
            if (!$plan->setting_exists($name)) {
                continue;
            }
            $setting = $plan->get_setting($name);
            if ($setting->get_status() != base_setting::NOT_LOCKED) {
                $setting->set_status(base_setting::NOT_LOCKED);
            }
            $setting->set_value($value);
        }

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * Setup the courses.
     *
     * @return array
     */
    protected function setup_courses() {
        global $DB;

        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $w1 = $this->get_world($c1->id);
        $w2 = $this->get_world($c2->id);

        // Modify config.
        $w1->get_config()->set('maxactionspertime', 8);
        $w2->get_config()->set('maxactionspertime', 18);

        // Create points and logs.
        $w1->get_store()->increase_with_reason($u1->id, '5', new event_name_reason('core\event\couse_viewed'));
        $w1->get_store()->increase_with_reason($u1->id, '5', new event_name_reason('core\event\couse_viewed'));
        $w1->get_store()->increase_with_reason($u2->id, '11', new event_name_reason('core\event\couse_viewed'));
        $w1->get_store()->increase_with_reason($u3->id, '22', new event_name_reason('core\event\couse_viewed'));
        $w2->get_store()->increase_with_reason($u1->id, '33', new event_name_reason('core\event\couse_viewed'));

        // Create rules.
        $w1->get_filter_manager();
        $w2->get_filter_manager();
        $rule = new \block_xp_rule_property(\block_xp_rule_base::CT, 'course_viewed', 'eventname');
        block_xp_filter::load_from_data(['rule' => $rule, 'points' => 666, 'courseid' => $c1->id, 'sortorder' => 0])->save();

        // Validate and document setup.
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c1->id, 'maxactionspertime' => 8]));
        $this->assertTrue($DB->record_exists('block_xp_config', ['courseid' => $c2->id, 'maxactionspertime' => 18]));
        $this->assertEquals(3, $DB->count_records('block_xp', ['courseid' => $c1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(10, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(11, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(22, $DB->get_field('block_xp', 'xp', ['courseid' => $c1->id, 'userid' => $u3->id]));
        $this->assertEquals(33, $DB->get_field('block_xp', 'xp', ['courseid' => $c2->id, 'userid' => $u1->id]));
        $this->assertEquals(4, $DB->count_records('block_xp_log', ['courseid' => $c1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));
        $this->assertEquals(6, $DB->count_records('block_xp_filters', ['courseid' => $c1->id]));
        $this->assertEquals(5, $DB->count_records('block_xp_filters', ['courseid' => $c2->id]));
        $this->assertTrue($DB->record_exists('block_xp_filters', ['courseid' => $c1->id, 'points' => 666]));
        $this->assertFalse($DB->record_exists('block_xp_filters', ['courseid' => $c2->id, 'points' => 666]));

        // Add block to courses.
        $page = new moodle_page();
        $page->set_context(context_course::instance($c1->id));
        $page->set_pagetype('page-type');
        $page->set_url(new moodle_url('/'));
        $blockmanager = new block_manager($page);
        $blockmanager->add_regions(['a'], false);
        $blockmanager->set_default_region('a');
        $blockmanager->add_block('xp', 'a', 0, false);

        $page = new moodle_page();
        $page->set_context(context_course::instance($c2->id));
        $page->set_pagetype('page-type');
        $page->set_url(new moodle_url('/'));
        $blockmanager = new block_manager($page);
        $blockmanager->add_regions(['a'], false);
        $blockmanager->set_default_region('a');
        $blockmanager->add_block('xp', 'a', 0, false);

        return ['c1' => $c1, 'c2' => $c2, 'u1' => $u1, 'u2' => $u2, 'u3' => $u3, 'w1' => $w1];
    }

}
