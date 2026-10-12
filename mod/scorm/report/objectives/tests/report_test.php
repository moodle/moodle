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

namespace scormreport_objectives;

/**
 * Unit tests for the objectives SCORM report.
 *
 * @package    scormreport_objectives
 * @category   test
 * @copyright  2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \scormreport_objectives\report
 */
final class report_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/scorm/locallib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test that objectives are grouped by SCO, including identifiers shared between SCOs.
     */
    public function test_get_scorm_objectives(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scorm = $generator->create_module('scorm', [
            'course' => $course->id,
            'packagefilepath' => $CFG->dirroot . '/mod/scorm/tests/packages/complexscorm.zip',
        ]);
        $student = $generator->create_and_enrol($course, 'student');
        [$sco1, $sco2] = array_values(scorm_get_scoes($scorm->id));

        scorm_insert_track($student->id, $scorm->id, $sco1->id, 1, 'cmi.objectives.0.id', 'obj1');
        scorm_insert_track($student->id, $scorm->id, $sco1->id, 1, 'cmi.objectives.1.id', 'obj2');
        scorm_insert_track($student->id, $scorm->id, $sco2->id, 1, 'cmi.objectives.0.id', 'obj1');

        // Load the report class file, which also defines get_scorm_objectives().
        class_exists(report::class);
        $this->assertEqualsCanonicalizing([
            $sco1->id => ['obj1', 'obj2'],
            $sco2->id => ['obj1'],
        ], array_map('array_values', get_scorm_objectives($scorm->id)));
    }
}
