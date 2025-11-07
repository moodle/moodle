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

declare(strict_types=1);

namespace core_courseformat\external;

use core_courseformat\stateactions;
use core_courseformat\stateupdates;

/**
 * Tests for the delete section test class.
 *
 * @package    core_courseformat
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @category   test
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(stateactions::class)]
final class delete_section_test extends \core_external\tests\externallib_testcase {
    /**
     * Test the webservice can execute the section_delete action.
     *
     * @param int $sectionum
     * @param string $format
     * @param int $expectedsectionum
     *
     * @throws \moodle_exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('section_delete_provider')]
    public function test_delete_section(int $sectionum, string $format, int $expectedsectionum): void {
        $this->resetAfterTest();

        $course =
            $this->getDataGenerator()->create_course(['numsections' => $sectionum, 'format' => $format]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        // Execute the method.
        $courseformat = course_get_format($course->id);
        $updates = new stateupdates($courseformat);
        $modinfo = get_fast_modinfo($course);
        $sections  = $modinfo->get_section_info_all();
        $sectionsid = array_map(function ($section) {
            return $section->id;
        }, $sections);
        $actions = new stateactions();
        $this->setUser($teacher);
        $actions->section_delete(
            $updates,
            $course,
            $sectionsid
        );
        // Check result.
        $modinfo = get_fast_modinfo($course);
        $sections  = $modinfo->get_section_info_all();
        $this->assertCount($expectedsectionum, $sections);
    }

    /**
     * Data provider for the test_delete_section method.
     *
     * @return \Generator
     */
    public static function section_delete_provider(): \Generator {
        yield 'format topic' => [
            'sectionum' => 4,
            'format' => 'topics',
            'expectedsectionum' => 1,
        ];
        yield 'format weeks' => [
            'sectionum' => 4,
            'format' => 'weeks',
            'expectedsectionum' => 1,
        ];
        yield 'format social' => [
            'sectionum' => 4,
            'format' => 'social',
            'expectedsectionum' => 5,
        ];
    }
}
