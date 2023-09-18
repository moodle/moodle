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

namespace core\moodlenet;

use core\moodlenet\share_recorder;

/**
 * Test coverage for moodlenet share recorder.
 *
 * @package   core
 * @copyright 2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\moodlenet\share_recorder
 */
class share_recorder_test extends \advanced_testcase {

    /**
     * Test inserting and updating an activity share progress to MoodleNet.
     *
     * @covers ::insert_share_progress
     * @covers ::update_share_progress
     */
    public function test_activity_share_progress(): void {
        global $DB, $USER;
        $this->resetAfterTest();

        $courseid = 10;
        $cmid = 20;
        $resourceurl = 'https://moodlenet.test/files/testresource.mbz';

        // Insert the activity share progress and test the returned id.
        $shareid = share_recorder::insert_share_progress(share_recorder::TYPE_ACTIVITY, $USER->id, $courseid, $cmid);
        $this->assertNotNull($shareid);

        // Test we have set the fields correctly.
        $record = $DB->get_record('moodlenet_share_progress', ['id' => $shareid]);
        $this->assertEquals(share_recorder::TYPE_ACTIVITY, $record->type);
        $this->assertEquals(share_recorder::STATUS_IN_PROGRESS, $record->status);
        $this->assertEquals($courseid, $record->courseid);
        $this->assertEquals($cmid, $record->cmid);
        $this->assertTimeCurrent($record->timecreated);
        $this->assertEquals($USER->id, $record->userid);

        // Update the record with the returned data from MoodleNet.
        share_recorder::update_share_progress($shareid, share_recorder::STATUS_SENT, $resourceurl);

        // Test we have set the fields correctly.
        $record = $DB->get_record('moodlenet_share_progress', ['id' => $shareid]);
        $this->assertEquals($resourceurl, $record->resourceurl);
        $this->assertEquals(share_recorder::STATUS_SENT, $record->status);
    }

    /**
     * Test inserting and updating a course share progress to MoodleNet.
     * We will also force an error status and test that too.
     *
     * @covers ::insert_share_progress
     * @covers ::update_share_progress
     */
    public function test_course_share_progress(): void {
        global $DB, $USER;
        $this->resetAfterTest();

        $courseid = 10;

        // Insert the course share progress and test the returned id.
        $shareid = share_recorder::insert_share_progress(share_recorder::TYPE_COURSE, $USER->id, $courseid);
        $this->assertNotNull($shareid);

        // Test we have set the fields correctly (we expect cmid to be null for course shares).
        $record = $DB->get_record('moodlenet_share_progress', ['id' => $shareid]);
        $this->assertEquals(share_recorder::TYPE_COURSE, $record->type);
        $this->assertEquals(share_recorder::STATUS_IN_PROGRESS, $record->status);
        $this->assertEquals($courseid, $record->courseid);
        $this->assertNull($record->cmid);
        $this->assertTimeCurrent($record->timecreated);
        $this->assertEquals($USER->id, $record->userid);

        // Update the record, but let's test with an error status.
        share_recorder::update_share_progress($shareid, share_recorder::STATUS_ERROR);

        // Test we have set the field correctly.
        $record = $DB->get_record('moodlenet_share_progress', ['id' => $shareid]);
        $this->assertEquals(share_recorder::STATUS_ERROR, $record->status);
    }

    /**
     * Tests the share type is one of the allowed values.
     *
     * @covers ::get_allowed_share_types
     */
    public function test_invalid_share_type(): void {
        global $USER;
        $this->resetAfterTest();

        $courseid = 10;
        $invalidsharetype = 99;

        $this->expectException(\moodle_exception::class);
        share_recorder::insert_share_progress($invalidsharetype, $USER->id, $courseid);
    }

    /**
     * Tests the share status is one of the allowed values.
     *
     * @covers ::get_allowed_share_statuses
     */
    public function test_invalid_share_status(): void {
        global $USER;
        $this->resetAfterTest();

        $courseid = 10;
        $invalidsharestatus = 66;

        $recordid = share_recorder::insert_share_progress(share_recorder::TYPE_COURSE, $USER->id, $courseid);

        $this->expectException(\moodle_exception::class);
        share_recorder::update_share_progress($recordid, $invalidsharestatus);
    }
}
