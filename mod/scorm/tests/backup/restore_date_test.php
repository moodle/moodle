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

namespace mod_scorm\backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

/**
 * Restore date tests.
 *
 * @package    mod_scorm
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_date_test extends \restore_date_testcase {

    public function test_restore_dates(): void {
        global $DB, $USER;

        $time = 10000;

        list($course, $scorm) = $this->create_course_and_module('scorm', ['timeopen' => $time, 'timeclose' => $time]);
        $scoes = scorm_get_scoes($scorm->id);
        $sco = array_shift($scoes);
        scorm_insert_track($USER->id, $scorm->id, $sco->id, 4, 'cmi.core.score.raw', 10);

        // We do not want second differences to fail our test because of execution delays.
        $DB->set_field('scorm_scoes_value', 'timemodified', $time);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newscorm = $DB->get_record('scorm', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($scorm, $newscorm, ['timemodified']);
        $props = ['timeopen', 'timeclose'];
        $this->assertFieldsRolledForward($scorm, $newscorm, $props);

        $sql = "SELECT *
                  FROM {scorm_scoes_value} v
                  JOIN {scorm_attempt} a ON a.id = v.attemptid
                 WHERE a.scormid = ?";
        $tracks = $DB->get_records_sql($sql, [$newscorm->id]);
        foreach ($tracks as $track) {
            $this->assertEquals($time, $track->timemodified);
        }
    }
}
