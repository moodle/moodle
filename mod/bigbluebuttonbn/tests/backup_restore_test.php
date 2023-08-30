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
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */

namespace mod_bigbluebuttonbn;

use mod_bigbluebuttonbn\test\testcase_helper_trait;
use restore_date_testcase;
use stdClass;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

/**
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \backup_bigbluebuttonbn_activity_task
 * @covers \restore_bigbluebuttonbn_activity_task
 */
class backup_restore_test extends restore_date_testcase {
    use testcase_helper_trait;

    /**
     * Setup
     */
    public function setUp(): void {
        parent::setUp();
    }

    /**
     * All instance types
     */
    const ALL_TYPES = [
        'Instance Type ALL' => instance::TYPE_ALL,
        'Instance Type Recording Only' => instance::TYPE_RECORDING_ONLY,
        'Instance Room Only' => instance::TYPE_ROOM_ONLY
    ];

    /**
     * Test backup restore (basic)
     */
    public function test_backup_restore(): void {
        global $DB;
        $this->resetAfterTest();
        $bbactivity = [];
        foreach (self::ALL_TYPES as $key => $type) {
            list($bbactivitycontext, $bbactivitycm, $bbactivity[$type])
                = $this->create_instance($this->get_course(), ['type' => $type]);
        }

        $newcourseid = $this->backup_and_restore($this->get_course());

        foreach (self::ALL_TYPES as $key => $type) {
            $newbbb =
                $DB->get_record('bigbluebuttonbn', ['course' => $newcourseid, 'type' => $type], '*', MUST_EXIST);
            // One record.
            $this->assert_bbb_activities_same($bbactivity[$type], $newbbb);
            $this->assertEquals($bbactivity[$type]->meetingid, $newbbb->meetingid);
        }
    }

    /**
     * @var $RECORDINGS_DATA array fake recording data.
     */
    const RECORDINGS_DATA = [
        ['name' => 'Recording 1'],
        ['name' => 'Recording 2'],
    ];

    /**
     * Check backup restore with recordings
     *
     */
    public function test_backup_restore_with_recordings(): void {
        global $DB;
        $this->resetAfterTest();
        $this->initialise_mock_server();
        set_config('bigbluebuttonbn_importrecordings_enabled', 1);
        // This is for imported recording.
        $generator = $this->getDataGenerator();
        $othercourse = $generator->create_course();
        $otherbbbactivities = [];
        $recordingstoimport = [];

        list('activity' => $otherbbbactivities[], 'recordings' => $recordings) =
            $this->create_activity_with_recordings($othercourse,
                instance::TYPE_ALL, self::RECORDINGS_DATA
            );
        $recordingstoimport = array_merge($recordingstoimport, $recordings);
        list('activity' => $otherbbbactivities[], 'recordings' => $recordings) =
            $this->create_activity_with_recordings($this->get_course(),
                instance::TYPE_ALL, self::RECORDINGS_DATA
            );
        $recordingstoimport = array_merge($recordingstoimport, $recordings);
        // Create a set of recordings and imported recordings.
        // We have nbrecording per bb activity, except for roomonly recordings which have the imported recordings.
        $bbactivity = [];
        foreach (self::ALL_TYPES as $key => $type) {
            $bbactivity[$type] = $this->getDataGenerator()->create_module(
                'bigbluebuttonbn',
                ['course' => $this->get_course()->id, 'type' => $type, 'name' => 'BBB Activity:' . $key],
                ['visible' => true]
            );
            $instance = instance::get_from_instanceid($bbactivity[$type]->id);
            // Create recording except for TYPE_RECORDING_ONLY Only.
            if ($instance->is_feature_enabled('showroom')) {
                $this->create_recordings_for_instance(instance::get_from_instanceid($bbactivity[$type]->id),
                    self::RECORDINGS_DATA);
            }
            // Then import the recordings into the instance.
            if ($instance->is_feature_enabled('importrecordings')) {
                foreach ($recordingstoimport as $rec) {
                    $rentity = recording::get_record(['id' => $rec->id]);
                    if ($rentity->get('bigbluebuttonbnid') != $instance->get_instance_id()) {
                        $rentity->create_imported_recording($instance);
                    }
                }
            }
        }

        // Backup and restore steps.
        $nbrecordings = count(self::RECORDINGS_DATA);
        $newcourseid = $this->backup_and_restore($this->get_course());

        // Now checks.
        foreach (self::ALL_TYPES as $key => $type) {
            $newbbb =
                $DB->get_record('bigbluebuttonbn',
                    ['course' => $newcourseid, 'type' => $type, 'name' => 'BBB Activity:' . $key],
                    '*',
                    MUST_EXIST); // One record.
            $this->assert_bbb_activities_same($bbactivity[$type], $newbbb);
            $newinstance = instance::get_from_instanceid($newbbb->id);

            $instancerecordings = $newinstance->get_recordings();
            // Type ROOM_ONLY & TYPE_ALL : all assigned recordings (NB_RECORDINGS).
            // Type TYPE_RECORDING_ONLY: all recordings from this course (i.e.
            // existing recording (NB_RECORDING) + ROOM_ONLY(NB_RECORDING)  + TYPE_ALL (NB_RECORDING)).
            $expectedcount = $type == instance::TYPE_RECORDING_ONLY ? $nbrecordings * 3 : $nbrecordings;
            // Type ROOM_ONLY & TYPE_ALL : The imported recording (NB_RECORDING*2 here)
            // Type TYPE_RECORDING_ONLY: imported recordings we add the imported recording from the other activity (TYPE_ALL).
            $expectedcount += $type == instance::TYPE_RECORDING_ONLY ? count($recordingstoimport) : 0;
            // We managed to import recording in this activity, so let's add them.
            $expectedcount += $newinstance->is_feature_enabled('importrecordings') ? count($recordingstoimport) : 0;
            $this->assertCount($expectedcount,
                $instancerecordings, 'Wrong count for instance Type:' . $key);
            // Then check imported recordings.
            foreach ($instancerecordings as $rec) {
                if ($rec->get('imported')) {
                    $importeddata = json_decode($rec->get('importeddata'));
                    $this->assertNotEmpty($importeddata);
                }
            }
        }
    }

    /**
     * Check duplicating activity does not duplicate meeting id
     *
     * @dataProvider bbb_type_provider
     */
    public function test_duplicate_module_no_meetingid(int $type) {
        list($bbactivitycontext, $bbactivitycm, $bbactivity)
            = $this->create_instance($this->get_course(), ['type' => $type]);
        $newcm = duplicate_module($this->get_course(), $bbactivitycm);
        $oldinstance = instance::get_from_cmid($bbactivitycm->id);
        $newinstance = instance::get_from_cmid($newcm->id);

        $this->assertNotEquals($oldinstance->get_instance_var('meetingid'), $newinstance->get_instance_var('meetingid'));
    }

    /**
     * Check that using the recycle bin keeps the meeting id
     *
     * @dataProvider bbb_type_provider
     */
    public function test_recycle_module_keep_meetingid(int $type) {
        list($bbactivitycontext, $bbactivitycm, $bbactivity)
            = $this->create_instance($this->get_course(), ['type' => $type]);
        // Delete the course module.
        course_delete_module($bbactivitycm->id);
        // Now, run the course module deletion adhoc task.
        \phpunit_util::run_all_adhoc_tasks();
        $currentinstances = instance::get_all_instances_in_course($this->course->id);
        $this->assertEmpty($currentinstances);
        // Try restoring.
        $recyclebin = new \tool_recyclebin\course_bin($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->restore_item($item);
        }
        $restoredinstance = instance::get_all_instances_in_course($this->course->id);
        $restoredinstance = end($restoredinstance);
        $this->assertEquals($restoredinstance->get_instance_var('meetingid'), $bbactivity->meetingid);
    }

    /**
     * Return an array of BigBlueButton types
     * @return array[]
     */
    public function bbb_type_provider() {
        return [
            'All' => [instance::TYPE_ALL],
            'Recording Only' => [instance::TYPE_RECORDING_ONLY],
            'Room Only' => [instance::TYPE_ROOM_ONLY]
        ];
    }

    /**
     * Check two bbb activities are the same
     *
     * @param stdClass $bbboriginal
     * @param stdClass $bbbdest
     */
    protected function assert_bbb_activities_same(stdClass $bbboriginal, stdClass $bbbdest) {
        $this->assertNotFalse($bbbdest);
        $filterfunction = function($key) {
            return !in_array($key, ['course', 'cmid', 'id', 'guestlinkuid', 'guestpassword']);
        };
        $this->assertEquals(
            array_filter((array) $bbboriginal, $filterfunction, ARRAY_FILTER_USE_KEY),
            array_filter((array) $bbbdest, $filterfunction, ARRAY_FILTER_USE_KEY)
        );
    }
}
