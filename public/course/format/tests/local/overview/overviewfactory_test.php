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

namespace core_courseformat\local\overview;

/**
 * Tests for course
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewfactory::class)]
final class overviewfactory_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/format/tests/fixtures/wrongcm_activityoverview.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test create method on resource activities.
     *
     * @param string $resourcetype
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('create_resource_provider')]
    public function test_create_resource(
        string $resourcetype,
        ?string $expected,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module($resourcetype, ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = overviewfactory::create($cm);

        $this->assertInstanceOf($expected, $overview);
    }

    /**
     * Data provider for test_create_resource.
     *
     * @return \Generator
     */
    public static function create_resource_provider(): \Generator {
        // Resource activities.
        yield 'book' => [
            'resourcetype' => 'book',
            'expected' => resourceoverview::class,
        ];
        yield 'folder' => [
            'resourcetype' => 'folder',
            'expected' => resourceoverview::class,
        ];
        yield 'page' => [
            'resourcetype' => 'page',
            'expected' => resourceoverview::class,
        ];
        yield 'resource' => [
            'resourcetype' => 'resource',
            'expected' => resourceoverview::class,
        ];
        yield 'url' => [
            'resourcetype' => 'url',
            'expected' => resourceoverview::class,
        ];
        yield  // Fallbacks and integrations.
        'assign' => [
            'resourcetype' => 'assign',
            'expected' => \mod_assign\courseformat\overview::class,
        ];
        yield 'bigbluebuttonbn' => [
            'resourcetype' => 'bigbluebuttonbn',
            'expected' => \mod_bigbluebuttonbn\courseformat\overview::class,
        ];
        yield 'choice' => [
            'resourcetype' => 'choice',
            'expected' => \mod_choice\courseformat\overview::class,
        ];
        yield 'data' => [
            'resourcetype' => 'data',
            'expected' => \mod_data\courseformat\overview::class,
        ];
        yield 'feedback' => [
            'resourcetype' => 'feedback',
            'expected' => \mod_feedback\courseformat\overview::class,
        ];
        yield 'forum' => [
            'resourcetype' => 'forum',
            'expected' => \mod_forum\courseformat\overview::class,
        ];
        yield 'glossary' => [
            'resourcetype' => 'glossary',
            'expected' => \mod_glossary\courseformat\overview::class,
        ];
        yield 'h5pactivity' => [
            'resourcetype' => 'h5pactivity',
            'expected' => \mod_h5pactivity\courseformat\overview::class,
        ];
        yield 'lesson' => [
            'resourcetype' => 'lesson',
            'expected' => \mod_lesson\courseformat\overview::class,
        ];
        yield 'lti' => [
            'resourcetype' => 'lti',
            'expected' => resourceoverview::class,
        ];
        yield 'qbank' => [
            'resourcetype' => 'qbank',
            'expected' => resourceoverview::class,
        ];
        yield 'quiz' => [
            'resourcetype' => 'quiz',
            'expected' => \mod_quiz\courseformat\overview::class,
        ];
        yield 'scorm' => [
            'resourcetype' => 'scorm',
            'expected' => \mod_scorm\courseformat\overview::class,
        ];
        yield 'wiki' => [
            'resourcetype' => 'wiki',
            'expected' => \mod_wiki\courseformat\overview::class,
        ];
        yield 'workshop' => [
            'resourcetype' => 'workshop',
            'expected' => \mod_workshop\courseformat\overview::class,
        ];
    }

    public function test_create_exception(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        // We know the factory will only use the modname to create the overview,
        // this is a small trick to make the factory to use a wrong class and
        // won't happen in a real code. However, this is the easiest way to test
        // the exception.
        $reflection = new \ReflectionClass($cm);
        $property = $reflection->getProperty('modname');
        $property->setAccessible(true);
        $property->setValue($cm, 'wrongcm');

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches("/.* must extend core_courseformat\\\\activityoverviewbase.*/");
        overviewfactory::create($cm);
    }

    /**
     * Test activity_has_overview_integration for existing modules.
     *
     * @param string $modname
     * @param bool $hasintegration
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('activity_has_overview_integration_provider')]
    public function test_activity_has_overview_integration(
        string $modname,
        bool $hasintegration,
    ): void {
        $result = overviewfactory::activity_has_overview_integration($modname);
        $this->assertEquals($hasintegration, $result);
    }

    /**
     * Data provider for test_overview_integrations.
     *
     * @return \Generator
     */
    public static function activity_has_overview_integration_provider(): \Generator {
        yield 'assign' => [
            'modname' => 'assign',
            'hasintegration' => true,
        ];
        yield 'bigbluebuttonbn' => [
            'modname' => 'bigbluebuttonbn',
            'hasintegration' => true,
        ];
        yield 'book' => [
            'modname' => 'book',
            'hasintegration' => false,
        ];
        yield 'choice' => [
            'modname' => 'choice',
            'hasintegration' => true,
        ];
        yield 'data' => [
            'modname' => 'data',
            'hasintegration' => true,
        ];
        yield 'feedback' => [
            'modname' => 'feedback',
            'hasintegration' => true,
        ];
        yield 'folder' => [
            'modname' => 'folder',
            'hasintegration' => false,
        ];
        yield 'forum' => [
            'modname' => 'forum',
            'hasintegration' => true,
        ];
        yield 'glossary' => [
            'modname' => 'glossary',
            'hasintegration' => true,
        ];
        yield 'h5pactivity' => [
            'modname' => 'h5pactivity',
            'hasintegration' => true,
        ];
        yield 'imscp' => [
            'modname' => 'imscp',
            'hasintegration' => false,
        ];
        yield 'label' => [
            'modname' => 'label',
            'hasintegration' => false,
        ];
        yield 'lesson' => [
            'modname' => 'lesson',
            'hasintegration' => true,
        ];
        yield 'lti' => [
            'modname' => 'lti',
            'hasintegration' => false,
        ];
        yield 'page' => [
            'modname' => 'page',
            'hasintegration' => false,
        ];
        yield 'qbank' => [
            'modname' => 'qbank',
            'hasintegration' => false,
        ];
        yield 'quiz' => [
            'modname' => 'quiz',
            'hasintegration' => true,
        ];
        yield 'resource' => [
            'modname' => 'resource',
            'hasintegration' => true,
        ];
        yield 'scorm' => [
            'modname' => 'scorm',
            'hasintegration' => true,
        ];
        yield 'url' => [
            'modname' => 'url',
            'hasintegration' => false,
        ];
        yield 'wiki' => [
            'modname' => 'wiki',
            'hasintegration' => true,
        ];
        yield 'workshop' => [
            'modname' => 'workshop',
            'hasintegration' => true,
        ];
    }

    /**
     * Test activity_has_overview_integration for non-existing integration.
     */
    public function test_activity_has_overview_integration_non_existing(): void {
        $result = overviewfactory::activity_has_overview_integration('fakemodulenonexisting');
        $this->assertFalse($result);
    }
}
