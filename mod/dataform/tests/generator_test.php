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

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit dataform generator testcase
 *
 * @package    mod_dataform
 * @category   phpunit
 * @group      mod_dataform
 * @group      mod_dataform_generator
 * @copyright  2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_dataform_generator_testcase extends advanced_testcase {

    public function test_generator() {
        global $DB;

        $this->resetAfterTest();

        // Reset dataform local cache.
        \mod_dataform_instance_store::unregister();

        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        // There should be no instances at this point.
        $instances = array();
        $this->assertEquals(count($instances), $DB->count_records('dataform'));

        // Verify the datafrom generator.
        $dataformgenerator = $generator->get_plugin_generator('mod_dataform');
        $this->assertInstanceOf('mod_dataform_generator', $dataformgenerator);
        $this->assertEquals('dataform', $dataformgenerator->get_modulename());

        // Add course.
        $course = $generator->create_course();

        // Add instances.
        $dataset = $this->createCsvDataSet(array('cases' => __DIR__.'/fixtures/tc_generator.csv'));
        $cases = $dataset->getTable('cases');
        $columns = $dataset->getTableMetaData('cases')->getColumns();

        for ($r = 0; $r < $cases->getRowCount(); $r++) {
            $case = array_combine($columns, $cases->getRow($r));
            $case['course'] = $course->id;

            // Create the instance.
            $data = $dataformgenerator->create_instance($case);
            $instances[] = $data->id;
            $this->assertEquals(count($instances), $DB->count_records('dataform'));

            // Update the instance.
            $df = \mod_dataform_dataform::instance($data->id);
            $df->update($df->data);

            // Verify instances count.
            $this->assertEquals(count($instances), $DB->count_records('dataform'));

            // Verify course id.
            $this->assertEquals($df->course->id, $course->id);

            // Verify course module.
            $cm = get_coursemodule_from_instance('dataform', $df->id);
            $this->assertEquals($df->cm->id, $cm->id);
            $this->assertEquals($df->id, $cm->instance);
            $this->assertEquals('dataform', $cm->modname);
            $this->assertEquals($course->id, $cm->course);

            // Verify context.
            $context = context_module::instance($cm->id);
            $this->assertEquals($df->context->id, $context->id);
            $this->assertEquals($df->cm->id, $context->instanceid);

            // Test gradebook integration using low level DB access - DO NOT USE IN PLUGIN CODE!
            if ($data->grade) {
                $gitemparams = array(
                    'courseid' => $course->id,
                    'itemtype' => 'mod',
                    'itemmodule' => 'dataform',
                    'iteminstance' => $data->id
                );
                $gitem = $DB->get_record('grade_items', $gitemparams);
                $this->assertNotEmpty($gitem);
                $this->assertEquals(100, $gitem->grademax);
                $this->assertEquals(0, $gitem->grademin);
                $this->assertEquals(GRADE_TYPE_VALUE, $gitem->gradetype);
            }
        }

        $dataid = array_pop($instances);
        \mod_dataform_dataform::instance($dataid)->delete();
        $this->assertEquals(count($instances), $DB->count_records('dataform'));
    }
}
