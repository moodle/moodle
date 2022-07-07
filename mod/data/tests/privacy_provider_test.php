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
 * @package    mod_data
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_userlist;
use mod_data\privacy\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    mod_data
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_data_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {
    /** @var stdClass The student object. */
    protected $student;
    /** @var stdClass The student object. */
    protected $student2;
    /** @var stdClass The student object. */
    protected $student3;

    /** @var stdClass The data object. */
    protected $datamodule;

    /** @var stdClass The course object. */
    protected $course;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void {
        $this->resetAfterTest();

        global $DB;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $params = [
            'course' => $course->id,
            'name' => 'Database module',
            'comments' => 1,
            'assessed' => 1,
        ];

        // The database activity.
        $datamodule = $this->get_generator()->create_instance($params);

        $fieldtypes = array('checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url',
            'latlong', 'file', 'picture');
        // Creating test Fields with default parameter values.
        foreach ($fieldtypes as $count => $fieldtype) {
            // Creating variables dynamically.
            $fieldname = 'field' . $count;
            $record = new \stdClass();
            $record->name = $fieldname;
            $record->description = $fieldname . ' descr';
            $record->type = $fieldtype;

            ${$fieldname} = $this->get_generator()->create_field($record, $datamodule);
        }

        $cm = get_coursemodule_from_instance('data', $datamodule->id);

        // Create a student.
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $student3 = $generator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($student1->id,  $course->id, $studentrole->id);
        $generator->enrol_user($student2->id,  $course->id, $studentrole->id);
        $generator->enrol_user($student3->id,  $course->id, $studentrole->id);

        // Add records.
        $this->setUser($student1);
        $record1id = $this->generate_data_record($datamodule);
        $this->generate_data_record($datamodule);

        $this->setUser($student2);
        $this->generate_data_record($datamodule);
        $this->generate_data_record($datamodule);
        $this->generate_data_record($datamodule);

        $this->setUser($student3);
        $this->generate_data_record($datamodule);

        $this->student = $student1;
        $this->student2 = $student2;
        $this->student3 = $student3;
        $this->datamodule = $datamodule;
        $this->course = $course;
    }

    /**
     * Get mod_data generator
     *
     * @return mod_data_generator
     */
    protected function get_generator() {
        return $this->getDataGenerator()->get_plugin_generator('mod_data');
    }

    /**
     * Generates one record in the database module as the current student
     *
     * @param stdClass $datamodule
     * @return mixed
     */
    protected function generate_data_record($datamodule) {
        global $DB;

        static $counter = 0;
        $counter++;

        $contents = array();
        $contents[] = array('opt1', 'opt2', 'opt3', 'opt4');
        $contents[] = sprintf("%02f", $counter) . '-01-2000';
        $contents[] = 'menu1';
        $contents[] = array('multimenu1', 'multimenu2', 'multimenu3', 'multimenu4');
        $contents[] = 5 * $counter;
        $contents[] = 'radioopt1';
        $contents[] = 'text for testing' . $counter;
        $contents[] = "<p>text area testing $counter<br /></p>";
        $contents[] = array('example.url', 'sampleurl' . $counter);
        $contents[] = [-31.9489873, 115.8382036]; // Latlong.
        $contents[] = "Filename{$counter}.pdf"; // File - filename.
        $contents[] = array("Cat{$counter}.jpg", 'Cat' . $counter); // Picture - filename with alt text.
        $count = 0;
        $fieldcontents = array();
        $fields = $DB->get_records('data_fields', array('dataid' => $datamodule->id), 'id');
        foreach ($fields as $fieldrecord) {
            $fieldcontents[$fieldrecord->id] = $contents[$count++];
        }
        $tags = ['Cats', 'mice' . $counter];
        return $this->get_generator()->create_entry($datamodule, $fieldcontents, 0, $tags);
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('mod_data');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(7, $itemcollection);

        $table = reset($itemcollection);
        $this->assertEquals('data_records', $table->get_name());

        $table = next($itemcollection);
        $this->assertEquals('data_content', $table->get_name());
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $cm = get_coursemodule_from_instance('data', $this->datamodule->id);

        $contextlist = provider::get_contexts_for_userid($this->student->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $cmcontext = context_module::instance($cm->id);
        $this->assertEquals($cmcontext->id, $contextforuser->id);
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {
        $component = 'mod_data';
        $cm = get_coursemodule_from_instance('data', $this->datamodule->id);
        $cmcontext = context_module::instance($cm->id);

        $userlist = new \core_privacy\local\request\userlist($cmcontext, $component);
        provider::get_users_in_context($userlist);

        $this->assertCount(3, $userlist);

        $expected = [$this->student->id, $this->student2->id, $this->student3->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Get test privacy writer
     *
     * @param context $context
     * @return \core_privacy\tests\request\content_writer
     */
    protected function get_writer($context) {
        return \core_privacy\local\request\writer::with_context($context);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        global $DB;
        $cm = get_coursemodule_from_instance('data', $this->datamodule->id);
        $cmcontext = context_module::instance($cm->id);
        $records = $DB->get_records_select('data_records', 'userid = :userid ORDER BY id', ['userid' => $this->student->id]);
        $record = reset($records);
        $contents = $DB->get_records('data_content', ['recordid' => $record->id]);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student->id, $cmcontext, 'mod_data');
        $writer = $this->get_writer($cmcontext);
        $data = $writer->get_data([$record->id]);
        $this->assertNotEmpty($data);
        foreach ($contents as $content) {
            $data = $writer->get_data([$record->id, $content->id]);
            $this->assertNotEmpty($data);
            $hasfile = in_array($data->field['type'], ['file', 'picture']);
            $this->assertEquals($hasfile, !empty($writer->get_files([$record->id, $content->id])));
        }
        $tags = $writer->get_related_data([$record->id], 'tags');
        $this->assertNotEmpty($tags);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        $cm = get_coursemodule_from_instance('data', $this->datamodule->id);
        $cmcontext = context_module::instance($cm->id);

        provider::delete_data_for_all_users_in_context($cmcontext);

        $appctxt = new \core_privacy\local\request\approved_contextlist($this->student, 'mod_data', [$cmcontext->id]);
        provider::export_user_data($appctxt);
        $this->assertFalse($this->get_writer($cmcontext)->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        $cm = get_coursemodule_from_instance('data', $this->datamodule->id);
        $cmcontext = context_module::instance($cm->id);

        $appctxt = new \core_privacy\local\request\approved_contextlist($this->student, 'mod_data', [$cmcontext->id]);
        provider::delete_data_for_user($appctxt);

        provider::export_user_data($appctxt);
        $this->assertFalse($this->get_writer($cmcontext)->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        $cm = get_coursemodule_from_instance('data', $this->datamodule->id);
        $cmcontext = context_module::instance($cm->id);
        $userstodelete = [$this->student->id, $this->student2->id];

        // Ensure student, student 2 and student 3 have data before being deleted.
        $appctxt = new \core_privacy\local\request\approved_contextlist($this->student, 'mod_data', [$cmcontext->id]);
        provider::export_user_data($appctxt);
        $this->assertTrue($this->get_writer($cmcontext)->has_any_data());

        $appctxt = new \core_privacy\local\request\approved_contextlist($this->student2, 'mod_data', [$cmcontext->id]);
        provider::export_user_data($appctxt);
        $this->assertTrue($this->get_writer($cmcontext)->has_any_data());

        // Delete data for student 1 and 2.
        $approvedlist = new approved_userlist($cmcontext, 'mod_data', $userstodelete);
        provider::delete_data_for_users($approvedlist);

        // Reset the writer so it doesn't contain the data from before deletion.
        \core_privacy\local\request\writer::reset();

        // Ensure data is now deleted for student and student 2.
        $appctxt = new \core_privacy\local\request\approved_contextlist($this->student, 'mod_data', [$cmcontext->id]);
        provider::export_user_data($appctxt);

        $this->assertFalse($this->get_writer($cmcontext)->has_any_data());

        $appctxt = new \core_privacy\local\request\approved_contextlist($this->student2, 'mod_data', [$cmcontext->id]);
        provider::export_user_data($appctxt);

        $this->assertFalse($this->get_writer($cmcontext)->has_any_data());

        // Ensure data still intact for student 3.
        $appctxt = new \core_privacy\local\request\approved_contextlist($this->student3, 'mod_data', [$cmcontext->id]);
        provider::export_user_data($appctxt);

        $this->assertTrue($this->get_writer($cmcontext)->has_any_data());
    }
}
