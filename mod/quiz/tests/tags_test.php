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

namespace mod_quiz;

use mod_quiz\question\bank\qbank_helper;
use mod_quiz\quiz_settings;

/**
 * Test the restore of random question tags.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags_test extends \advanced_testcase {
    public function test_restore_random_question_by_tag(): void {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        $backupid = 'abc';
        $backuppath = make_backup_temp_directory($backupid);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname(
                __DIR__ . "/fixtures/random_by_tag_quiz.mbz", $backuppath);

        // Do the restore to new course with default settings.
        $categoryid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $newcourseid = \restore_dbops::create_new_course('Test fullname', 'Test shortname', $categoryid);
        $rc = new \restore_controller($backupid, $newcourseid, \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id,
                \backup::TARGET_NEW_COURSE);

        $this->assertTrue($rc->execute_precheck());
        $rc->execute_plan();
        $rc->destroy();

        // Get the information about the resulting course and check that it is set up correctly.
        $modinfo = get_fast_modinfo($newcourseid);
        $quiz = array_values($modinfo->get_instances_of('quiz'))[0];
        $quizobj = quiz_settings::create($quiz->instance);
        $structure = structure::create_for_quiz($quizobj);

        // Are the correct slots returned?
        $slots = $structure->get_slots();
        $this->assertCount(1, $slots);

        $quizobj->preload_questions();
        $quizobj->load_questions();
        $questions = $quizobj->get_questions();

        $this->assertCount(1, $questions);

        $question = array_values($questions)[0];

        $tag1 = \core_tag_tag::get_by_name(0, 't1', 'id, name');
        $this->assertNotFalse($tag1);

        $tag2 = \core_tag_tag::get_by_name(0, 't2', 'id, name');
        $this->assertNotFalse($tag2);

        $tag3 = \core_tag_tag::get_by_name(0, 't3', 'id, name');
        $this->assertNotFalse($tag3);

        $slottags = $this->get_tags_for_slot($question->slotid);
        $slottags = reset($slottags);
        $slottags = explode(',', $slottags);
        $this->assertEquals("{$tag2->id},{$tag2->name}", "{$slottags[0]},{$slottags[1]}");

        $defaultcategory = question_get_default_category(\context_course::instance($newcourseid)->id);
        $this->assertEquals($defaultcategory->id, $question->category);
        $randomincludingsubcategories = $DB->get_record('question_set_references',
            ['itemid' => reset($slots)->id, 'component' => 'mod_quiz', 'questionarea' => 'slot']);
        $filtercondition = json_decode($randomincludingsubcategories->filtercondition);
        $this->assertEquals(0, $filtercondition->includingsubcategories);
    }

    /**
     * Helper to get the random tags, if any, for a slot.
     *
     * @param int $slotid the id of the slot to get tags for.
     * @return array the tags.
     */
    protected function get_tags_for_slot(int $slotid): array {
        global $DB;
        $referencedata = $DB->get_record('question_set_references',
                ['itemid' => $slotid, 'component' => 'mod_quiz', 'questionarea' => 'slot']);
        if (isset($referencedata->filtercondition)) {
            $filtercondition = json_decode($referencedata->filtercondition);
            if (isset($filtercondition->tags)) {
                return $filtercondition->tags;
            }
        }
        return [];
    }
}
