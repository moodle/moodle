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

namespace core_grades;

use grade_outcome;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Tests that outcomes can exist without an associated scale and that the grader
 * report shows no column for an outcome that has no scale, because no grade item
 * is created for such outcomes.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class outcome_scaleless_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        parent::setUpBeforeClass();
    }

    /**
     * An outcome can be created, saved and re-fetched without a scale.
     *
     * @covers \grade_outcome::insert
     * @covers \grade_outcome::fetch
     */
    public function test_outcome_without_scale_can_be_created(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $outcome = new grade_outcome();
        $outcome->courseid  = $course->id;
        $outcome->shortname = 'noscale';
        $outcome->fullname  = 'Outcome with no scale';

        $id = $outcome->insert();
        $this->assertNotFalse($id, 'Outcome without scale should insert successfully.');

        // Re-fetch from the database.
        $fetched = grade_outcome::fetch(['id' => $id]);
        $this->assertInstanceOf(grade_outcome::class, $fetched);
        $this->assertEmpty($fetched->scaleid, 'scaleid should be empty for a scale-less outcome.');
    }

    /**
     * load_scale() returns false when the outcome has no scale.
     *
     * @covers \grade_outcome::load_scale
     */
    public function test_load_scale_returns_false_for_scaleless_outcome(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $outcome = new grade_outcome();
        $outcome->courseid  = $course->id;
        $outcome->shortname = 'noscale2';
        $outcome->fullname  = 'Outcome load_scale test';
        $outcome->insert();

        $this->assertFalse($outcome->load_scale(), 'load_scale() must return false when scaleid is not set.');
    }

    /**
     * An outcome can be created with a scale and load_scale() returns the correct object.
     *
     * @covers \grade_outcome::load_scale
     */
    public function test_load_scale_returns_scale_for_scaled_outcome(): void {
        $this->resetAfterTest();

        $course  = $this->getDataGenerator()->create_course();
        $scalerecord = $this->getDataGenerator()->create_scale(['scale' => 'Yes,No', 'courseid' => $course->id]);

        $outcome = new grade_outcome();
        $outcome->courseid  = $course->id;
        $outcome->shortname = 'withscale';
        $outcome->fullname  = 'Outcome with scale';
        $outcome->scaleid   = $scalerecord->id;
        $outcome->insert();

        $scale = $outcome->load_scale();
        $this->assertNotNull($scale, 'load_scale() must return a scale object when scaleid is set.');
        $this->assertSame((int)$scalerecord->id, (int)$scale->id);
    }

    /**
     * Outcomes without scales do not create grade items when attached to activities.
     *
     * @covers ::edit_module_post_actions
     */
    public function test_scaleless_outcomes_do_not_create_grade_items(): void {
        global $CFG;

        $this->resetAfterTest();
        $CFG->enableoutcomes = 1;

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create a scale.
        $scale = $generator->create_scale([
            'courseid' => $course->id,
            'scale' => 'Poor,Good',
        ]);

        // Outcome with scale.
        $scaledoutcome = $generator->create_grade_outcome([
            'courseid' => $course->id,
            'shortname' => 'scaled',
            'fullname' => 'Scaled outcome',
            'scaleid' => $scale->id,
        ]);

        // Outcome without scale.
        $scalelessoutcome = $generator->create_grade_outcome([
            'courseid' => $course->id,
            'shortname' => 'noscale',
            'fullname' => 'Scale-less outcome',
        ]);

        // Create an assignment with both outcomes selected.
        $assign = $generator->create_module('assign', [
            'course' => $course->id,
            'outcome_' . $scaledoutcome->id => 1,
            'outcome_' . $scalelessoutcome->id => 1,
        ]);

        // Verify grade item exists for scaled outcome.
        $gradeitems = \grade_item::fetch_all([
            'courseid' => $course->id,
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
        ]);

        $outcomeids = array_map(
            static fn($item) => (int)$item->outcomeid,
            $gradeitems ?: []
        );

        $this->assertContains(
            (int)$scaledoutcome->id,
            $outcomeids,
            'Outcome with scale should create a grade item.'
        );

        $this->assertNotContains(
            (int)$scalelessoutcome->id,
            $outcomeids,
            'Outcome without scale should not create a grade item.'
        );
    }

    /**
     * Tests that add_outcome_to_module() creates a module outcome record.
     *
     * @covers \grade_outcome::add_outcome_to_module
     */
    public function test_add_outcome_to_module_creates_record(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        $outcome = $this->create_scaleless_outcome($course->id);

        $result = $outcome->add_outcome_to_module($course->id, $cm->id);

        $this->assertTrue($result, 'add_outcome_to_module() must return true on success.');
        $this->assertTrue(
            $this->outcome_module_exists($outcome, $course->id, $cm->id),
            'A row must exist in grade_outcomes_modules for the module.'
        );

        // Verify idempotency: a second call must not create a duplicate row.
        $outcome->add_outcome_to_module($course->id, $cm->id);
        $this->assertTrue(
            $this->outcome_module_exists($outcome, $course->id, $cm->id),
            'Duplicate calls to add_outcome_to_module() must not create duplicate rows.'
        );
    }

    /**
     * Verifies that add_outcome_to_module() returns false and creates no row for a scaled outcome.
     *
     * @covers \grade_outcome::add_outcome_to_module
     */
    public function test_scaled_outcome_not_added_to_outcome_module(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $scale  = $this->getDataGenerator()->create_scale(['scale' => 'Yes,No', 'courseid' => $course->id]);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        $outcome = new grade_outcome();
        $outcome->courseid  = $course->id;
        $outcome->shortname = 'scaled';
        $outcome->fullname  = 'Scaled outcome';
        $outcome->scaleid   = $scale->id;
        $outcome->insert();

        $result = $outcome->add_outcome_to_module((int)$course->id, $cm->id);

        $this->assertFalse($result);
        $this->assertFalse(
            $this->outcome_module_exists($outcome, $course->id, $cm->id),
            'No grade_outcomes_modules row must be created for a scaled outcome.'
        );
    }

    /**
     * Tests that remove_outcome_from_module() removes a module outcome record.
     *
     * @covers \grade_outcome::remove_outcome_from_module
     */
    public function test_remove_outcome_from_module_deletes_association(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        $outcome = $this->create_scaleless_outcome($course->id);
        $outcome->add_outcome_to_module((int)$course->id, (int)$cm->id);
        $outcome->remove_outcome_from_module((int)$course->id, (int)$cm->id);

        $this->assertFalse(
            $this->outcome_module_exists($outcome, $course->id, $cm->id),
            'remove_outcome_from_module() must delete the grade_outcomes_modules row.'
        );
    }

    /**
     * Deleting an outcome removes all its grade_outcomes_modules rows.
     *
     * @covers \grade_outcome::delete
     */
    public function test_delete_outcome_removes_all_module_records(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        $outcome = $this->create_scaleless_outcome($course->id);

        $outcome->add_outcome_to_module((int)$course->id, (int)$cm->id);
        $outcome->delete();

        $this->assertFalse(
            $this->outcome_module_exists($outcome, $course->id, $cm->id),
            'Deleting the outcome must remove all its grade_outcomes_modules rows.'
        );
    }

    /**
     * Scaled and scale-less outcomes with usage records are detected as used.
     *
     * @covers \grade_outcome::get_used_outcomes_in_course
     */
    public function test_used_outcomes_are_detected_as_used(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $scale  = $this->getDataGenerator()->create_scale(['scale' => 'Yes,No']);

        // Scaled outcome (via grade_items).
        $scaled = new grade_outcome();
        $scaled->shortname = 'scaled';
        $scaled->fullname  = 'Scaled outcome';
        $scaled->scaleid   = $scale->id;
        $scaled->insert();

        $this->insert_outcome_grade_item($course->id, $scaled->id, $scale->id);

        // Scale-less outcome (via module association).
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm     = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        $scaleless = $this->create_scaleless_outcome($course->id);
        $scaleless->add_outcome_to_module($course->id, $cm->id);

        $used = grade_outcome::get_used_outcomes_in_course($course->id);

        $this->assertEqualsCanonicalizing(
            [
                (int)$scaled->id,
                (int)$scaleless->id,
            ],
            $used,
            'All outcomes with either grade items or module associations must be detected as used.'
        );
    }

    /**
     * Outcomes without any usage record are not detected as used.
     *
     * @covers \grade_outcome::get_used_outcomes_in_course
     */
    public function test_unused_outcomes_are_not_detected_as_used(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course  = $this->getDataGenerator()->create_course();
        $outcome = $this->create_scaleless_outcome($course->id);

        // Outcome exists but has no grade item and no grade_outcomes_modules row.
        $used = grade_outcome::get_used_outcomes_in_course($course->id);

        $this->assertNotContains(
            (int)$outcome->id,
            $used,
            'An outcome with no grade item or module record must not be detected as used.'
        );
    }

    /**
     * Scale-less outcomes for a module are returned.
     *
     * @covers \grade_outcome::get_outcomes_in_module
     */
    public function test_scaleless_outcomes_are_returned_for_course_module(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm     = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        $outcome = $this->create_scaleless_outcome($course->id);
        $outcome->add_outcome_to_module($course->id, $cm->id);

        $oids = grade_outcome::get_outcomes_in_module($cm->id, $course->id);
        $this->assertContains(
            (int)$outcome->id,
            $oids,
            'Scale-less outcomes linked to a module must be returned for that module.'
        );
    }

    /**
     * Deleting an activity removes grade_outcomes_modules rows for scale-less
     * outcomes that were attached to it.
     *
     * @covers \core_courseformat\local\cmactions::delete
     */
    public function test_deleting_activity_removes_outcome_module_records(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enableoutcomes = 1;

        $course    = $this->getDataGenerator()->create_course();

        $scaleless = $this->create_scaleless_outcome($course->id);

        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        // Associate the activity with the scale-less outcome.
        $scaleless->add_outcome_to_module((int)$course->id, (int)$cm->id);

        // Verify pre-conditions.
        $this->assertTrue(
            $this->outcome_module_exists($scaleless, $course->id, $cm->id),
            'Pre-condition: the scale-less outcome must be associated with the activity before deletion.'
        );

        // Delete the assignment activity.
        $cmactions = new \core_courseformat\local\cmactions($course);
        $cmactions->delete((int)$cm->id);

        $this->assertFalse(
            $this->outcome_module_exists($scaleless, $course->id, $cm->id),
            'Deleting an activity should remove the associated grade_outcomes_modules record.'
        );
    }

    /**
     * Backing up and restoring a full course preserves scaleless outcomes and
     * their activity associations.
     *
     * @covers \restore_outcomes_structure_step
     * @covers \restore_activity_grades_structure_step
     */
    public function test_course_backup_restore_preserves_scaleless_outcome_association(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enableoutcomes = 1;
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        $course = $this->getDataGenerator()->create_course();
        $scaleless = $this->create_scaleless_outcome($course->id);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);
        $scaleless->add_outcome_to_module($course->id, $cm->id);

        $newcourseid = $this->backup_and_restore_course($course->id);

        // The scaleless outcome must exist in the new course with scaleid = null.
        $restoredoutcomes = \grade_outcome::fetch_all(['courseid' => $newcourseid]);
        $this->assertCount(1, $restoredoutcomes);
        $restored = reset($restoredoutcomes);
        $this->assertNull($restored->scaleid, 'scaleid must remain null after course restore.');

        // The restored assign module.
        $modinfo = get_fast_modinfo($newcourseid);
        $newassigns = array_values($modinfo->get_instances_of('assign'));
        $this->assertCount(1, $newassigns, 'Exactly one assign must exist in the restored course.');
        $newcmid = (int)$newassigns[0]->id;

        $this->assertTrue(
            $this->outcome_module_exists($restored, $newcourseid, $newcmid),
            'The restored module should retain its outcome association.'
        );
    }

    /**
     * A course containing scaled and scale-less outcomes, both used and unused,
     * preserves all outcomes across a full course backup/restore.
     *
     * @covers \backup_annotate_course_outcomes
     * @covers \restore_outcomes_structure_step
     */
    public function test_course_backup_restore_preserves_course_outcomes(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enableoutcomes = 1;
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        $course = $this->getDataGenerator()->create_course();
        $scale  = $this->getDataGenerator()->create_scale(['scale' => 'Yes,No', 'courseid' => $course->id]);

        // Used scaled outcome: attached to an assignment via a grade item.
        $usedscaled = $this->getDataGenerator()->create_grade_outcome([
            'courseid' => $course->id,
            'shortname' => 'usedscaled',
            'fullname' => 'Used scaled outcome',
            'scaleid' => $scale->id,
        ]);
        $assign = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id,
            'outcome_' . $usedscaled->id => 1,
        ]);

        // Used scale-less outcome: attached to the same activity via grade_outcomes_modules.
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);
        $usedscaleless = $this->create_scaleless_outcome($course->id, 'usedscaleless');
        $usedscaleless->add_outcome_to_module($course->id, $cm->id);

        // Unused scaled outcome: not referenced anywhere.
        $this->getDataGenerator()->create_grade_outcome([
            'courseid' => $course->id,
            'shortname' => 'unusedscaled',
            'fullname' => 'Unused scaled outcome',
            'scaleid' => $scale->id,
        ]);

        // Unused scale-less outcome: not referenced anywhere.
        $this->create_scaleless_outcome($course->id, 'unusedscaleless');

        $newcourseid = $this->backup_and_restore_course($course->id);

        $restoredoutcomes = \grade_outcome::fetch_all(['courseid' => $newcourseid]);
        $this->assertCount(4, $restoredoutcomes, 'All four outcomes must be preserved regardless of usage.');

        $outcomesbyshortname = [];
        foreach ($restoredoutcomes as $restored) {
            $outcomesbyshortname[$restored->shortname] = $restored;
        }

        $this->assertArrayHasKey('usedscaled', $outcomesbyshortname);
        $this->assertArrayHasKey('usedscaleless', $outcomesbyshortname);
        $this->assertArrayHasKey('unusedscaled', $outcomesbyshortname);
        $this->assertArrayHasKey('unusedscaleless', $outcomesbyshortname);

        $this->assertNotEmpty($outcomesbyshortname['usedscaled']->scaleid);
        $this->assertNotEmpty($outcomesbyshortname['unusedscaled']->scaleid);
        $this->assertNull($outcomesbyshortname['usedscaleless']->scaleid);
        $this->assertNull($outcomesbyshortname['unusedscaleless']->scaleid);

        // Verify that the restored scaled outcomes still have their scales.
        $restoredscale = \grade_scale::fetch(['id' => $outcomesbyshortname['usedscaled']->scaleid]);
        $this->assertNotFalse($restoredscale);
        $this->assertSame('Yes,No', $restoredscale->scale);
    }

    /**
     * Backing up and restoring an activity preserves referenced outcomes but does not
     * include unrelated course outcomes.
     *
     * @covers \restore_outcomes_structure_step
     * @covers \restore_activity_grades_structure_step
     */
    public function test_activity_backup_restore_only_restores_referenced_outcomes(): void {
        global $CFG, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enableoutcomes = 1;
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $scale   = $this->getDataGenerator()->create_scale(['scale' => 'Yes,No', 'courseid' => $course1->id]);

        // Create an unrelated scaled outcome that should not be included in the activity backup.
        $unusedscaled = $this->getDataGenerator()->create_grade_outcome([
            'courseid' => $course1->id,
            'shortname' => 'unusedscaled',
            'fullname' => 'Unused scaled outcome',
            'scaleid' => $scale->id,
        ]);

        // Create a scaleless outcome referenced by the activity.
        $scaleless = $this->create_scaleless_outcome($course1->id);
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $course1->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course1->id);
        $scaleless->add_outcome_to_module($course1->id, $cm->id);

        // Backup the activity.
        $bc = new \backup_controller(
            \backup::TYPE_1ACTIVITY,
            $cm->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore the activity into another course.
        $rc = new \restore_controller(
            $backupid,
            $course2->id,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id,
            \backup::TARGET_CURRENT_ADDING,
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        // Verify that only the referenced scaleless outcome was restored.
        $restoredoutcomes = \grade_outcome::fetch_all(['courseid' => $course2->id]);
        $this->assertCount(1, $restoredoutcomes);

        $restored = reset($restoredoutcomes);
        $this->assertNull($restored->scaleid);
        $this->assertSame($scaleless->shortname, $restored->shortname);
        $this->assertEmpty(
            \grade_outcome::fetch_all([
                'courseid' => $course2->id,
                'shortname' => $unusedscaled->shortname,
            ]),
            'Unused course outcomes should not be restored with an activity backup.'
        );

        // Verify that the restored activity retains its outcome association.
        $modinfo = get_fast_modinfo($course2->id);
        $newassigns = array_values($modinfo->get_instances_of('assign'));
        $this->assertCount(1, $newassigns, 'Exactly one assign must exist in the restored course.');
        $newcmid = (int) $newassigns[0]->id;

        $this->assertTrue(
            $this->outcome_module_exists($restored, $course2->id, $newcmid),
            'The restored module should retain its outcome association.'
        );
    }

    /**
     * Performs a full course backup and restores it into a new course, returning
     * the new course's id.
     *
     * @param int $courseid The id of the course to back up.
     * @return int The id of the newly restored course.
     */
    private function backup_and_restore_course(int $courseid): int {
        global $USER;

        // Backup the full course.
        $bc = new \backup_controller(
            \backup::TYPE_1COURSE,
            $courseid,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        // Restore into a new course.
        $course = get_course($courseid);
        $newcourseid = \restore_dbops::create_new_course(
            'Restored course',
            'restored_' . $backupid,
            $course->category
        );
        $rc = new \restore_controller(
            $backupid,
            $newcourseid,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id,
            \backup::TARGET_NEW_COURSE
        );
        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }

    /**
     * Checks whether a module outcome record exists for the given outcome, course and course module.
     *
     * @param grade_outcome $outcome The outcome.
     * @param int $courseid The course ID.
     * @param int $cmid The course module ID.
     * @return bool True if the record exists, otherwise false.
     */
    private function outcome_module_exists(grade_outcome $outcome, int $courseid, int $cmid): bool {
        global $DB;
        $sql = "SELECT 1
                  FROM {grade_outcomes_modules} gom
                  JOIN {grade_outcomes_courses} goc ON goc.id = gom.outcomecourseid
                 WHERE goc.outcomeid = :outcomeid
                       AND goc.courseid = :courseid
                       AND gom.cmid = :cmid";
        return $DB->record_exists_sql($sql, ['outcomeid' => $outcome->id, 'courseid' => $courseid, 'cmid' => $cmid]);
    }

    /**
     * Creates a scale-less outcome for the given course.
     *
     * @param int $courseid The course ID.
     * @param string $shortname The outcome shortname.
     * @return grade_outcome The created outcome.
     */
    private function create_scaleless_outcome(int $courseid, string $shortname = 'noscale'): grade_outcome {
        $outcome = new grade_outcome();
        $outcome->courseid = $courseid;
        $outcome->shortname = $shortname;
        $outcome->fullname = 'No scale outcome';
        $outcome->insert();
        return $outcome;
    }

    /**
     * Helper: inserts a bare grade_item row that references an outcome.
     *
     * @param int $courseid
     * @param int $outcomeid
     * @param int $scaleid
     */
    private function insert_outcome_grade_item(int $courseid, int $outcomeid, int $scaleid): void {
        $item = new \grade_item();
        $item->courseid     = $courseid;
        $item->itemtype     = 'manual';
        $item->itemname     = 'test item';
        $item->outcomeid    = $outcomeid;
        $item->gradetype    = GRADE_TYPE_SCALE;
        $item->scaleid      = $scaleid;
        $item->insert();
    }
}
