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

namespace core_courseformat;

/**
 * Course format actions class tests.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(formatactions::class)]
final class formatactions_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_courseactions.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_sectionactions.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_cmactions.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test for get_instance static method.
     *
     * @param string $format
     * @param array $classnames
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_classname_action')]
    public function test_instance(string $format, array $classnames): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => $format]);

        $instance1 = formatactions::instance($course);
        $this->assertInstanceOf('\core_courseformat\formatactions', $instance1);

        $instance2 = formatactions::instance($course->id);
        $this->assertInstanceOf('\core_courseformat\formatactions', $instance2);

        // Validate the method is caching the result.
        $this->assertEquals($instance1, $instance2);

        // Validate public attribute classes.
        $this->assertInstanceOf($classnames['course'], $instance1->course);
        $this->assertInstanceOf($classnames['section'], $instance1->section);
        $this->assertInstanceOf($classnames['cm'], $instance1->cm);
    }

    /**
     * Test that the course action instance is created correctly.
     *
     * @param string $format
     * @param array $classnames
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_classname_action')]
    public function test_course_action_instance(string $format, array $classnames): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => $format]);

        $instance1 = formatactions::course($course);
        $this->assertInstanceOf($classnames['course'], $instance1);

        $instance2 = formatactions::course($course->id);
        $this->assertInstanceOf($classnames['course'], $instance2);
    }

    /**
     * Test that the section action instance is created correctly.
     *
     * @param string $format
     * @param array $classnames
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_classname_action')]
    public function test_static_sectionactions_instance(string $format, array $classnames): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => $format]);

        $instance1 = formatactions::section($course);
        $this->assertInstanceOf($classnames['section'], $instance1);

        $instance2 = formatactions::section($course->id);
        $this->assertInstanceOf($classnames['section'], $instance2);
    }

    /**
     * Test that the cm action instance is created correctly.
     *
     * @param string $format
     * @param array $classnames
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_classname_action')]
    public function test_static_cmactions_instance(string $format, array $classnames): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => $format]);

        $instance1 = formatactions::cm($course);
        $this->assertInstanceOf($classnames['cm'], $instance1);

        $instance2 = formatactions::cm($course->id);
        $this->assertInstanceOf($classnames['cm'], $instance2);
    }

    /**
     * Data provider for format class names scenarios.
     * @return \Generator
     */
    public static function provider_classname_action(): \Generator {
        yield 'Topics format' => [
            'format' => 'topics',
            'classnames' => [
                'course' => '\core_courseformat\local\courseactions',
                'section' => '\core_courseformat\local\sectionactions',
                'cm' => '\core_courseformat\local\cmactions',
            ],
        ];
        yield 'The unit test fixture format' => [
            'format' => 'theunittest',
            'classnames' => [
                'course' => '\format_theunittest\courseformat\courseactions',
                'section' => '\format_theunittest\courseformat\sectionactions',
                'cm' => '\format_theunittest\courseformat\cmactions',
            ],
        ];
    }
}
