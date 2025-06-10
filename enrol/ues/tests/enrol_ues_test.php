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

require_once dirname(dirname(__FILE__)).'/lib.php';
require_once('generator.php');

class enrol_ues_testcase extends advanced_testcase {

    public $ues;
    public $sectionCount;

    public function setup() {
        global $CFG;
        $this->resetAfterTest();

        set_config('enrollment_provider', 'fake', 'enrol_ues');
        $this->assertEquals('fake', get_config('enrol_ues', 'enrollment_provider'));
        $this->ues = new enrol_ues_plugin();
    }

    public function test_provider_constructor() {
        $provider = $this->ues->provider();
        $this->assertInstanceOf('enrollment_provider', $provider);
    }

    /**
     *
     * @return ues_semester[]
     */
    public function test_get_semesters() {
        $semesters = $this->ues->get_semesters(time());

        $this->assertNotEmpty($semesters);
        $this->assertTrue(is_array($semesters));

        $unit = array_pop($semesters);
        $this->assertInstanceOf('ues_semester', $unit);

        return $semesters;
    }

    /**
     * @depends test_get_semesters
     * @param ues_semester[] $semesters
     */
    public function test_get_courses($semesters) {
        $process_courses = array();
        foreach ($semesters as $s) {
            $process_courses[] = $this->ues->get_courses($s);
            $this->assertContainsOnlyInstancesOf('ues_section', $process_courses);
        }
        return $process_courses;
    }
}
