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
 * Privacy provider tests.
 *
 * @package    qtype_wq
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_wq\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;
// This package is manteined from Moodle 2.4 but the privacy API only applies
// from Moodle 3.3 version.
if ($CFG->version < 2017051500) {
    exit;
}

use core_privacy\local\metadata\collection;
use core_privacy\local\request\deletion_criteria;
use qtype_wq\privacy\provider;

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * @covers \qtype_wq\privacy\provider
 */
class provider_test extends \core_privacy\tests\provider_testcase {
    /** @var \stdClass The teacher object. */
    protected $teacher;

    /** @var \stdClass The course object. */
    protected $course;


    /**
     * {@inheritdoc}
     */
    protected function setUp(): void {
        $this->resetAfterTest();

        global $DB;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create a teacher which make a question.
        $teacher = $generator->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $generator->enrol_user($teacher->id,  $course->id, $teacherrole->id);
        $this->teacher = $teacher;

        // Add one question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('essay', 'plain', ['category' => $cat->id]);

        // Here we need to transform this question into a Wiris Quizzes question type.
        // Fetch the original record.
        $qrecord = $DB->get_record('question', ['id' => $q->id]);
        $qrecord->qtype = 'essaywiris';
        $qrecord->createdby = $teacher->id;

        // Update the original record.
        $DB->update_record('question', $qrecord);
        // Update the question object.
        $q->qtype = 'essaywiris';
        $q->createdby = $teacher->id;
        // Creating Wiris Question object.
        $wq = new \stdClass();
        $wq->question = $q->id;
        $wq->xml = 'xml';
        $DB->insert_record('qtype_wq', $wq);
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('qtype_wq');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $table = reset($itemcollection);
        $this->assertEquals('qtype_wq', $table->get_name());

        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('question', $privacyfields);
        $this->assertArrayHasKey('xml', $privacyfields);

        $this->assertEquals('privacy:metadata:qtype_wq', $table->get_summary());
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        $contextlist = provider::get_contexts_for_userid($this->teacher->id);

        $this->assertCount(1, $contextlist);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        global $DB;
         // A new question is created here associated to the original teacher.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('essay', 'plain', ['category' => $cat->id]);
        // Here we need to transform this question into a Wiris Quizzes question type.
        // Fetch the original record.
        $qrecord = $DB->get_record('question', ['id' => $q->id]);
        $qrecord->qtype = 'essaywiris';
        $qrecord->createdby = $this->teacher->id;

        // Update the original record.
        $DB->update_record('question', $qrecord);
        // Update the question object.
        $q->qtype = 'essaywiris';
        $q->createdby = $this->teacher->id;
        // Creating Wiris Question object.
        $wq = new \stdClass();
        $wq->question = $q->id;
        $wq->xml = 'xml';
        $DB->insert_record('qtype_wq', $wq);
        // Question is at system context level.
        $systemcontext = \context_system::instance();

        // Export all the data for the system context.
        $this->export_context_data_for_user($this->teacher->id, $systemcontext, 'qtype_wq');
        $writer = \core_privacy\local\request\writer::with_context($systemcontext);

        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // A new question is created here for another teacher
        // The context is the same (context_system) for both questions.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $teacher = $generator->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $generator->enrol_user($teacher->id,  $course->id, $teacherrole->id);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('essay', 'plain', ['category' => $cat->id]);
        // Here we need to transform this question into a Wiris Quizzes question type.

        // Fetch the original record.
        $qrecord = $DB->get_record('question', ['id' => $q->id]);
        $qrecord->qtype = 'essaywiris';
        $qrecord->createdby = $teacher->id;

        // Update the original record.
        $DB->update_record('question', $qrecord);
        // Update the question object.
        $q->qtype = 'essaywiris';
        $q->createdby = $teacher->id;
        // Creating Wiris Question object.
        $wq = new \stdClass();
        $wq->question = $q->id;
        $wq->xml = 'xml';
        $DB->insert_record('qtype_wq', $wq);

        // Before deletion, we should have 2 responses.
        $count = $DB->count_records('qtype_wq', []);
        $this->assertEquals(2, $count);

        // Delete data based on context.
        $syscontext = \context_system::instance();
        provider::delete_data_for_all_users_in_context($syscontext);

        // After deletion, the Wiris Quizzes questiosn should have been deleted.
        $count = $DB->count_records('qtype_wq', []);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user_() {
        global $DB;

        // Create a new question associated to a new teacher.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $anotherteacher = $generator->create_user();
        $anotherteacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $generator->enrol_user($anotherteacher->id,  $course->id, $anotherteacherrole->id);

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('essay', 'plain', ['category' => $cat->id]);
        // Here we need to transform this question into a Wiris Quizzes question type.
        // Fetching the original record.
        $qrecord = $DB->get_record('question', ['id' => $q->id]);
        $qrecord->qtype = 'essaywiris';
        $qrecord->createdby = $anotherteacher->id;

        // Update the original record.
        $DB->update_record('question', $qrecord);
        // Update the question object.
        $q->qtype = 'essaywiris';
        $q->createdby = $anotherteacher->id;
        // Creating Wiris Question object.
        $wq = new \stdClass();
        $wq->question = $q->id;
        $wq->xml = 'xml';
        $DB->insert_record('qtype_wq', $wq);

        // A new question is created here associated to the original teacher.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('essay', 'plain', ['category' => $cat->id]);
        // Here we need to transform this question into a Wiris Quizzes question type.
        // Fetch the original record.
        $qrecord = $DB->get_record('question', ['id' => $q->id]);
        $qrecord->qtype = 'essaywiris';
        $qrecord->createdby = $this->teacher->id;

        // Update the original record.
        $DB->update_record('question', $qrecord);
        // Update the question object.
        $q->qtype = 'essaywiris';
        $q->createdby = $this->teacher->id;
        // Creating Wiris Question object.
        $wq = new \stdClass();
        $wq->question = $q->id;
        $wq->xml = 'xml';
        $DB->insert_record('qtype_wq', $wq);

        // Before deletion, we should have 3 Wiris Quizzes questions.
        $count = $DB->count_records('qtype_wq', []);
        $this->assertEquals(3, $count);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->teacher, 'qtype_wq',
                                                                            [\context_system::instance()->id]);

        provider::delete_data_for_user($contextlist);

        // After deletion, only the question for the new teacher must exist.
        $count = $DB->count_records('qtype_wq', []);
        $this->assertEquals(1, $count);

        // Confirm that the existing question belongs to the new teacher.

        // The existence of a single record is verified before so at this point we can
        // call get_record() instead of get_records() method.
        $record = $DB->get_record('qtype_wq', []);
        $question = $DB->get_record('question', ['id' => $record->question]);
        // The remaining question belongs to the new teacher.
        $this->assertEquals($question->createdby, $anotherteacher->id);
    }
}
