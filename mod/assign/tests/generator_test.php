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

namespace mod_assign;

/**
 * Test for mod assign generator.
 * @package mod_assign
 * @copyright Frederik Pytlick <fmp@moxis.dk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_assign_generator
 */
final class generator_test extends \advanced_testcase {
    /**
     * Test creating an assignment instance using the mod_assign generator.
     */
    public function test_create_instance(): void {
        $this->resetAfterTest();

        $datagenerator = self::getDataGenerator();
        $course = $datagenerator->create_course();

        $scale = $datagenerator->create_scale();

        $generator = $datagenerator->get_plugin_generator('mod_assign');
        $instance1 = $generator->create_instance([
            'course' => $course,
            'name' => 'Assign 1',
            'gradetype' => GRADE_TYPE_SCALE,
            'gradescale' => $scale->id,
        ]);
        $instance2 = $generator->create_instance([
            'course' => $course,
            'name' => 'Assign 2',
            'gradetype' => GRADE_TYPE_NONE,
        ]);
        $instance3 = $generator->create_instance([
            'course' => $course,
            'name' => 'Assign 3',
            'gradetype' => GRADE_TYPE_VALUE,
        ]);

        $this->assertEquals(-$scale->id, $instance1->grade);
        $this->assertEquals(0, $instance2->grade);
        $this->assertEquals(100, $instance3->grade);
    }
}
