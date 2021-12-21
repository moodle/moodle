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
 * Contains unit tests for mod_workshop\dates.
 *
 * @package   mod_workshop
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_workshop;

use advanced_testcase;
use cm_info;
use core\activity_dates;

/**
 * Class for unit testing mod_workshop\dates.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates_test extends advanced_testcase {

    /**
     * Data provider for get_dates_for_module().
     * @return array[]
     */
    public function get_dates_for_module_provider(): array {
        $now = time();
        $before = $now - DAYSECS;
        $earlier = $before - DAYSECS;
        $earliest = $earlier - DAYSECS;
        $after = $now + DAYSECS;
        $later = $after + DAYSECS;
        $latest = $later + DAYSECS;

        return [
            'without any dates' => [
                null, null, null, null, []
            ],
            'only with start time for submissions' => [
                $after, null, null, null, [
                    ['label' => 'Submissions open:', 'timestamp' => $after],
                ]
            ],
            'only with end time for submissions' => [
                null, $after, null, null, [
                    ['label' => 'Submissions close:', 'timestamp' => $after],
                ]
            ],
            'only with start time for assessments' => [
                null, null, $after, null, [
                    ['label' => 'Assessments open:', 'timestamp' => $after],
                ]
            ],
            'only with end time for assessments' => [
                null, null, null, $after, [
                    ['label' => 'Assessments close:', 'timestamp' => $after],
                ]
            ],
            'all times in future' => [
                $after, $later, $latest, $latest + DAYSECS, [
                    ['label' => 'Submissions open:', 'timestamp' => $after],
                    ['label' => 'Submissions close:', 'timestamp' => $later],
                    ['label' => 'Assessments open:', 'timestamp' => $latest],
                    ['label' => 'Assessments close:', 'timestamp' => $latest + DAYSECS],
                ]
            ],
            'all times in the past' => [
                $earliest - DAYSECS, $earliest, $earlier, $before, [
                    ['label' => 'Submissions opened:', 'timestamp' => $earliest - DAYSECS],
                    ['label' => 'Submissions closed:', 'timestamp' => $earliest],
                    ['label' => 'Assessments opened:', 'timestamp' => $earlier],
                    ['label' => 'Assessments closed:', 'timestamp' => $before],
                ]
            ],
            'between submission and assessment' => [
                $earlier, $before, $after, $later, [
                    ['label' => 'Submissions opened:', 'timestamp' => $earlier],
                    ['label' => 'Submissions closed:', 'timestamp' => $before],
                    ['label' => 'Assessments open:', 'timestamp' => $after],
                    ['label' => 'Assessments close:', 'timestamp' => $later],
                ]
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @dataProvider get_dates_for_module_provider
     * @param int|null $submissionstart The 'Open for submissions from' value of the workshop.
     * @param int|null $submissionend The 'Submissions deadline' value of the workshop.
     * @param int|null $assessmentstart The 'Open for assessment from' value of the workshop.
     * @param int|null $assessmentend The 'Deadline for assessment' value of the workshop.
     * @param array $expected The expected value of calling get_dates_for_module()
     */
    public function test_get_dates_for_module(?int $submissionstart, ?int $submissionend,
            ?int $assessmentstart, ?int $assessmentend,
            array $expected) {

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $data = ['course' => $course->id];
        if ($submissionstart) {
            $data['submissionstart'] = $submissionstart;
        }
        if ($submissionend) {
            $data['submissionend'] = $submissionend;
        }
        if ($assessmentstart) {
            $data['assessmentstart'] = $assessmentstart;
        }
        if ($assessmentend) {
            $data['assessmentend'] = $assessmentend;
        }
        $this->setAdminUser();
        $workshop = $this->getDataGenerator()->create_module('workshop', $data);

        $this->setUser($user);

        $cm = get_coursemodule_from_instance('workshop', $workshop->id);
        // Make sure we're using a cm_info object.
        $cm = cm_info::create($cm);

        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }
}
