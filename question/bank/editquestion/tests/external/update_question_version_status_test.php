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

use qbank_editquestion\external\update_question_version_status;

/**
 * Submit status external api test.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_question\local\bank\question_version_status
 * @coversDefaultClass \qbank_editquestion\form\question_status_form
 * @coversDefaultClass \qbank_editquestion\editquestion_helper
 */
class update_question_version_status_test extends \advanced_testcase {

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
     * @covers ::mock_generate_submit_keys
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
        $data = ['status' => 2];
        $mform = \qbank_editquestion\form\question_status_form::mock_generate_submit_keys($data);
        $this->expectException('moodle_exception');
        list($result, $statusname) = update_question_version_status::execute($numq->id, http_build_query($mform, '', '&'));
        // Test if the version actually changed.
        $currentstatus = $DB->get_record('question_versions', ['questionid' => $numq->id]);
        $this->assertEquals($data['status'], $currentstatus->status);
        $this->assertEquals(editquestion_helper::get_question_status_string($currentstatus->status), $statusname);
    }

    /**
     * Test that updating the status does not create a new version.
     *
     * @covers ::mock_generate_submit_keys
     * @covers ::execute
     */
    public function test_submit_status_does_not_create_a_new_version() {
        global $DB;
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $numq = $questiongenerator->create_question('essay', null,
            ['category' => $cat->id, 'name' => 'This is the first version']);
        $countcurrentrecords = $DB->count_records('question_versions');
        $this->assertEquals(1, $countcurrentrecords);
        $data = ['status' => 2];
        $mform = \qbank_editquestion\form\question_status_form::mock_generate_submit_keys($data);
        $this->expectException('moodle_exception');
        list($result, $statusname) = update_question_version_status::execute($numq->id, http_build_query($mform, '', '&'));
        $countafterupdate = $DB->count_records('question_versions');
        $this->assertEquals($countcurrentrecords, $countafterupdate);
    }
}
