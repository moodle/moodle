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
        require_once($CFG->libdir . '/tablelib.php');

        // These constants are defined by mod/scorm/report.php, which cannot be included here.
        if (!defined('SCORM_REPORT_DEFAULT_PAGE_SIZE')) {
            define('SCORM_REPORT_DEFAULT_PAGE_SIZE', 20);
            define('SCORM_REPORT_ATTEMPTS_ALL_STUDENTS', 0);
            define('SCORM_REPORT_ATTEMPTS_STUDENTS_WITH', 1);
            define('SCORM_REPORT_ATTEMPTS_STUDENTS_WITH_NO', 2);
        }

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

    /**
     * Test that objective identifiers are escaped in the report headers.
     */
    public function test_display_escapes_objective_identifiers(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $scorm = $generator->create_module('scorm', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);
        $student = $generator->create_and_enrol($course, 'student');

        $payload = '<img src=x onerror=alert(1)>';
        $generator->get_plugin_generator('mod_scorm')->create_attempt([
            'scormid' => $scorm->id,
            'userid' => $student->id,
            'element' => 'cmi.objectives.0.id',
            'value' => $payload,
        ]);

        $PAGE->set_url('/mod/scorm/report.php', ['id' => $cm->id]);
        $PAGE->set_cm($cm, $course);

        ob_start();
        (new report())->display($scorm, $cm, $course, '');
        $output = ob_get_clean();

        $this->assertStringNotContainsString($payload, $output);
        $this->assertStringContainsString(s($payload), $output);
    }
}
