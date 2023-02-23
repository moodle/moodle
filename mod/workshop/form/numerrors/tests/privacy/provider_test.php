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
 * Provides the {@see workshopform_numerrors\privacy\provider_test} class.
 *
 * @package     workshopform_numerrors
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace workshopform_numerrors\privacy;

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
     * Test {@link workshopform_numerrors\privacy\provider::export_assessment_form()} implementation.
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

        $this->dim1 = $DB->insert_record('workshopform_numerrors', [
            'workshopid' => $this->workshop11->id,
            'sort' => 1,
            'description' => 'Assertion 1 description',
            'descriptionformat' => FORMAT_MARKDOWN,
            'descriptiontrust' => 0,
            'grade0' => 'No',
            'grade1' => 'Yes',
            'weight' => 1,
        ]);

        $this->dim2 = $DB->insert_record('workshopform_numerrors', [
            'workshopid' => $this->workshop11->id,
            'sort' => 2,
            'description' => 'Assertion 2 description',
            'descriptionformat' => FORMAT_MARKDOWN,
            'descriptiontrust' => 0,
            'grade0' => 'Missing',
            'grade1' => 'Present',
            'weight' => 1,
        ]);

        $this->student1 = $this->generator->create_user();
        $this->student2 = $this->generator->create_user();

        $this->submission111 = $this->workshopgenerator->create_submission($this->workshop11->id, $this->student1->id);

        $this->assessment1112 = $this->workshopgenerator->create_assessment($this->submission111, $this->student2->id, [
            'grade' => 92,
        ]);

        $DB->insert_record('workshop_grades', [
            'assessmentid' => $this->assessment1112,
            'strategy' => 'numerrors',
            'dimensionid' => $this->dim1,
            'grade' => 1,
            'peercomment' => 'Awesome',
            'peercommentformat' => FORMAT_PLAIN,
        ]);

        $DB->insert_record('workshop_grades', [
            'assessmentid' => $this->assessment1112,
            'strategy' => 'numerrors',
            'dimensionid' => $this->dim2,
            'grade' => 0,
            'peercomment' => 'Missing',
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
            get_string('pluginname', 'workshopform_numerrors'),
        ]);

        $this->assertEquals('Assertion 1 description', $form->assertions[0]->description);
        $this->assertEquals(0, $form->assertions[1]->grade);
        $this->assertEquals('Missing', $form->assertions[1]->peercomment);
    }
}
