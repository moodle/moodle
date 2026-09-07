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
 * Pluginfile integration tests for core_h5p.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2026 Raquel Ortega <raquel.ortega@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/h5p/lib.php');

/**
 * Tests covering core_h5p_pluginfile().
 *
 * @covers ::core_h5p_pluginfile
 */
final class pluginfile_test extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        \core_h5p\local\library\autoloader::register();
    }

    /**
     * Deploy a minimal H5P package into a forum's context so access checks have something real to evaluate.
     *
     * @param array $configoverrides Overrides merged into the default H5P display config, e.g. ['export' => 0].
     * @return array
     */
    private function create_h5p_fixture(array $configoverrides = []): array {
        $factory = new factory();
        $config = (object) array_merge([
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ], $configoverrides);

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $modcontext = \context_module::instance($forum->cmid);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $otheruser = $this->getDataGenerator()->create_user();

        /** @var \core_h5p_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $generator->create_library_record('H5P.GreetingCard', 'GreetingCard', 1, 0);

        $path = self::get_fixture_path(__NAMESPACE__, 'greeting-card.h5p');
        $originalfile = helper::create_fake_stored_file_from_path($path, (int) $student->id, $modcontext);
        $h5pid = helper::save_h5p($factory, $originalfile, $config);
        $this->assertNotFalse($h5pid, 'save_h5p() failed to validate/store the fixture package.');

        return [
            'course' => $course,
            'forum' => $forum,
            'student' => $student,
            'otheruser' => $otheruser,
            'h5pid' => (int) $h5pid,
            'syscontext' => \context_system::instance(),
        ];
    }

    /**
     * CONTENT_FILEAREA must deny access via api::can_access_content() for a user not enrolled in the course.
     */
    public function test_content_filearea_pluginfile_access(): void {
        $this->resetAfterTest();

        $fixture = $this->create_h5p_fixture();

        $this->setUser($fixture['otheruser']);
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('redirecterrordetected', 'error'));
        \core_h5p_pluginfile(
            $fixture['course'],
            $fixture['forum'],
            $fixture['syscontext'],
            file_storage::CONTENT_FILEAREA,
            [$fixture['h5pid'], 'image-fixture.png'],
            false,
            ['dontdie' => true]
        );
    }

    /**
     * EXPORT_FILEAREA must reject invalid filenames, deny via api::can_access_content(), and honor the
     * "Allow download" display option.
     */
    public function test_export_filearea_pluginfile_access(): void {
        $this->resetAfterTest();

        $fixture = $this->create_h5p_fixture(['export' => 0]);
        $filename = 'my-export-' . $fixture['h5pid'] . '.h5p';

        // Invalid filenames are rejected before any DB lookup or access check.
        $this->setUser($fixture['student']);
        $this->assertFalse(\core_h5p_pluginfile(
            $fixture['course'],
            $fixture['forum'],
            $fixture['syscontext'],
            file_storage::EXPORT_FILEAREA,
            ['not-a-valid-export.h5p'],
            false,
            ['dontdie' => true]
        ));

        // The enrolled student passes the access check but downloads are disabled for this content.
        $this->assertFalse(\core_h5p_pluginfile(
            $fixture['course'],
            $fixture['forum'],
            $fixture['syscontext'],
            file_storage::EXPORT_FILEAREA,
            [$filename],
            false,
            ['dontdie' => true]
        ));

        // A user not enrolled in the course must be denied by api::can_access_content(), same as CONTENT_FILEAREA.
        $this->setUser($fixture['otheruser']);
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('redirecterrordetected', 'error'));
        \core_h5p_pluginfile(
            $fixture['course'],
            $fixture['forum'],
            $fixture['syscontext'],
            file_storage::EXPORT_FILEAREA,
            [$filename],
            false,
            ['dontdie' => true]
        );
    }
}
