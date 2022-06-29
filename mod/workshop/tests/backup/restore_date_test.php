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

namespace mod_workshop\backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/workshop/locallib.php');
require_once($CFG->dirroot . '/mod/workshop/lib.php');
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");
require_once($CFG->dirroot . "/mod/workshop/tests/fixtures/testable.php");

/**
 * Restore date tests.
 *
 * @package    mod_workshop
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_date_test extends \restore_date_testcase {

    /**
     * Test restore dates.
     */
    public function test_restore_dates() {
        global $DB, $USER;

        // Create workshop data.
        $record = ['submissionstart' => 100, 'submissionend' => 100, 'assessmentend' => 100, 'assessmentstart' => 100];
        list($course, $workshop) = $this->create_course_and_module('workshop', $record);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid = $workshopgenerator->create_submission($workshop->id, $USER->id);
        $exsubid = $workshopgenerator->create_submission($workshop->id, $USER->id, ['example' => 1]);
        $workshopgenerator->create_assessment($subid, $USER->id);
        $workshopgenerator->create_assessment($exsubid, $USER->id, ['weight' => 0]);
        $workshopgenerator->create_assessment($exsubid, $USER->id);

        // Set time fields to a constant for easy validation.
        $timestamp = 100;
        $DB->set_field('workshop_submissions', 'timecreated', $timestamp);
        $DB->set_field('workshop_submissions', 'timemodified', $timestamp);
        $DB->set_field('workshop_assessments', 'timecreated', $timestamp);
        $DB->set_field('workshop_assessments', 'timemodified', $timestamp);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newworkshop = $DB->get_record('workshop', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($workshop, $newworkshop, ['timemodified']);
        $props = ['submissionstart', 'submissionend', 'assessmentend', 'assessmentstart'];
        $this->assertFieldsRolledForward($workshop, $newworkshop, $props);

        $submissions = $DB->get_records('workshop_submissions', ['workshopid' => $newworkshop->id]);
        // Workshop submission time checks.
        foreach ($submissions as $submission) {
            $this->assertEquals($timestamp, $submission->timecreated);
            $this->assertEquals($timestamp, $submission->timemodified);
            $assessments = $DB->get_records('workshop_assessments', ['submissionid' => $submission->id]);
            // Workshop assessment time checks.
            foreach ($assessments as $assessment) {
                $this->assertEquals($timestamp, $assessment->timecreated);
                $this->assertEquals($timestamp, $assessment->timemodified);
            }
        }
    }
}
