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

namespace qbank_editquestion;

use core_question\local\bank\question_version_status;
use qbank_editquestion\external\update_question_version_status;

/**
 * Submit status external api test.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_question\local\bank\question_version_status
 * @coversDefaultClass \qbank_editquestion\external\update_question_version_status
 * @coversDefaultClass \qbank_editquestion\editquestion_helper
 */
class update_question_version_status_test extends \advanced_testcase {

    /** @var \stdClass course record. */
    protected $course;

    /** @var mixed. */
    protected $user;

    /**
     * Called before every test.
     */
    public function setUp(): void {
        global $USER;
        parent::setUp();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
        $this->user = $USER;
    }

    /**
     * Test if the submit status webservice changes the status of the question.
     *
     * @covers ::execute
     * @covers ::get_question_status_string
     */
    public function test_submit_status_updates_the_question_status() {
        global $DB;
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $numq = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is the first version']);
        $result = update_question_version_status::execute($numq->id, 'draft');
        // Test if the version actually changed.
        $currentstatus = $DB->get_record('question_versions', ['questionid' => $numq->id]);
        $this->assertEquals(editquestion_helper::get_question_status_string($currentstatus->status), $result['statusname']);
    }

    /**
     * Test submit status webservice only takes an existing parameter status.
     *
     * @covers ::execute
     */
    public function test_submit_status_error() {
        global $DB;
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $numq = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is the first version']);
        // Passing a wrong status to web service.
        $result = update_question_version_status::execute($numq->id, 'frog');
        // Tests web service returns error.
        $this->assertEquals(false, $result['status']);
        $this->assertEquals('', $result['statusname']);
        $this->assertEquals(get_string('unrecognizedstatus', 'qbank_editquestion'), $result['error']);
        // Test version did not change.
        $currentstatus = $DB->get_record('question_versions', ['questionid' => $numq->id]);
        $this->assertEquals(question_version_status::QUESTION_STATUS_READY, $currentstatus->status);
    }

    /**
     * Test that updating the status does not create a new version.
     *
     * @covers ::execute
     */
    public function test_submit_status_does_not_create_a_new_version() {
        global $DB;
        $this->resetAfterTest();

        // Find out the start count in 'question_versions' table.
        $versioncount = $DB->count_records('question_versions');

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $numq = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is the first version']);
        $countcurrentrecords = $DB->count_records('question_versions');
        // New version count should be equal to start + 1.
        $this->assertEquals($versioncount + 1, $countcurrentrecords);

        $result = update_question_version_status::execute($numq->id, 'draft');
        $countafterupdate = $DB->count_records('question_versions');
        $this->assertEquals($countcurrentrecords, $countafterupdate);
    }
}
