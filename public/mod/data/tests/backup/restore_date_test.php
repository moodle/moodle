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

namespace mod_data\backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");
require_once($CFG->dirroot . '/rating/lib.php');

/**
 * Restore date tests.
 *
 * @package    mod_data
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class restore_date_test extends \restore_date_testcase {

    /**
     * Test restore dates.
     */
    public function test_restore_dates(): void {
        global $DB, $USER;

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $record = ['assesstimefinish' => 100, 'assesstimestart' => 100, 'ratingtime' => 1, 'assessed' => 2, 'scale' => 1,
                   'timeavailablefrom' => 100, 'timeavailableto' => 100, 'timeviewfrom' => 100, 'timeviewto' => 100];
        list($course, $data) = $this->create_course_and_module('data', $record);

        // Data field/record.
        $timestamp = 996699;
        $diff = $this->get_diff();
        $record = new \stdClass();
        $record->name = 'field-1';
        $record->type = 'text';
        $field = $gg->create_field($record, $data);
        $datarecordid = $gg->create_entry($data, [$field->field->id => 'NERDS NERDS EVERYWHERE, NO BRAIN TO THINK']);
        $datarecord = $DB->get_record('data_records', ['id' => $datarecordid]);

        // Ratings.
        $ratingoptions = new \stdClass;
        $ratingoptions->context = \context_module::instance($data->cmid);
        $ratingoptions->ratingarea = 'entry';
        $ratingoptions->component = 'mod_data';
        $ratingoptions->itemid  = $datarecord->id;
        $ratingoptions->scaleid = 2;
        $ratingoptions->userid  = $USER->id;
        $rating = new \rating($ratingoptions);
        $rating->update_rating(2);
        $rating = $DB->get_record('rating', ['itemid' => $datarecord->id]);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newdata = $DB->get_record('data', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($data, $newdata, ['timemodified']);
        $props = ['assesstimefinish', 'assesstimestart', 'timeavailablefrom', 'timeavailableto', 'timeviewfrom', 'timeviewto'];
        $this->assertFieldsRolledForward($data, $newdata, $props);

        $newdatarecord = $DB->get_record('data_records', ['dataid' => $newdata->id]);
        $newcm = $DB->get_record('course_modules', ['course' => $newcourseid, 'instance' => $newdata->id]);

        // Data record time checks.
        $this->assertEquals($datarecord->timecreated, $newdatarecord->timecreated);
        $this->assertEquals($datarecord->timemodified, $newdatarecord->timemodified);

        // Rating test.
        $newrating = $DB->get_record('rating', ['contextid' => \context_module::instance($newcm->id)->id]);
        $this->assertEquals($rating->timecreated, $newrating->timecreated);
        $this->assertEquals($rating->timemodified, $newrating->timemodified);
    }
}
