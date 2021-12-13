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
 * Provides the {@see workshopform_rubric\privacy\provider_test} class.
 *
 * @package     workshopform_rubric
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace workshopform_rubric\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;

use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

/**
 * Unit tests for the privacy API implementation.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /**
     * Test {@link workshopform_rubric\privacy\provider::export_assessment_form()} implementation.
     */
    public function test_export_assessment_form() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->generator = $this->getDataGenerator();
        $this->workshopgenerator = $this->generator->get_plugin_generator('mod_workshop');

        $this->course1 = $this->generator->create_course();

        $this->workshop11 = $this->generator->create_module('workshop', [
            'course' => $this->course1,
            'name' => 'Workshop11',
        ]);
        $DB->set_field('workshop', 'phase', 50, ['id' => $this->workshop11->id]);

        $this->dim1 = $DB->insert_record('workshopform_rubric', [
            'workshopid' => $this->workshop11->id,
            'sort' => 1,
            'description' => 'Criterion 1 description',
            'descriptionformat' => FORMAT_MARKDOWN,
        ]);

        $DB->insert_record('workshopform_rubric_levels', [
            'dimensionid' => $this->dim1,
            'grade' => 0,
            'definition' => 'Missing',
            'definitionformat' => FORMAT_PLAIN,
        ]);

        $DB->insert_record('workshopform_rubric_levels', [
            'dimensionid' => $this->dim1,
            'grade' => 1,
            'definition' => 'Poor',
            'definitionformat' => FORMAT_PLAIN,
        ]);

        $DB->insert_record('workshopform_rubric_levels', [
            'dimensionid' => $this->dim1,
            'grade' => 2,
            'definition' => 'Good',
            'definitionformat' => FORMAT_PLAIN,
        ]);

        $this->dim2 = $DB->insert_record('workshopform_rubric', [
            'workshopid' => $this->workshop11->id,
            'sort' => 2,
            'description' => 'Criterion 2 description',
            'descriptionformat' => FORMAT_MARKDOWN,
        ]);

        $DB->insert_record('workshopform_rubric_levels', [
            'dimensionid' => $this->dim2,
            'grade' => 0,
            'definition' => 'Missing',
            'definitionformat' => FORMAT_PLAIN,
        ]);

        $DB->insert_record('workshopform_rubric_levels', [
            'dimensionid' => $this->dim2,
            'grade' => 5,
            'definition' => 'Great',
            'definitionformat' => FORMAT_PLAIN,
        ]);

        $this->student1 = $this->generator->create_user();
        $this->student2 = $this->generator->create_user();

        $this->submission111 = $this->workshopgenerator->create_submission($this->workshop11->id, $this->student1->id);

        $this->assessment1112 = $this->workshopgenerator->create_assessment($this->submission111, $this->student2->id, [
            'grade' => 92,
        ]);

        $DB->insert_record('workshop_grades', [
            'assessmentid' => $this->assessment1112,
            'strategy' => 'rubric',
            'dimensionid' => $this->dim1,
            'grade' => 1,
            'peercomment' => '',
            'peercommentformat' => FORMAT_PLAIN,
        ]);

        $DB->insert_record('workshop_grades', [
            'assessmentid' => $this->assessment1112,
            'strategy' => 'rubric',
            'dimensionid' => $this->dim2,
            'grade' => 5,
            'peercomment' => '',
            'peercommentformat' => FORMAT_PLAIN,
        ]);

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student2, 'mod_workshop', [
            \context_module::instance($this->workshop11->cmid)->id,
        ]);

        \mod_workshop\privacy\provider::export_user_data($contextlist);

        $writer = writer::with_context(\context_module::instance($this->workshop11->cmid));

        $form = $writer->get_data([
            get_string('myassessments', 'mod_workshop'),
            $this->assessment1112,
            get_string('assessmentform', 'mod_workshop'),
            get_string('pluginname', 'workshopform_rubric'),
        ]);

        $this->assertEquals('Criterion 1 description', $form->criteria[0]->description);
        $this->assertEquals(3, count($form->criteria[0]->levels));
        $this->assertEquals(2, count($form->criteria[1]->levels));
        $this->assertEquals(5, $form->criteria[1]->grade);
    }
}
