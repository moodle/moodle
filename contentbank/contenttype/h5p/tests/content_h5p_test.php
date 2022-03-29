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

namespace contenttype_h5p;

/**
 * Test for H5P content bank plugin.
 *
 * @package    contenttype_h5p
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \contenttype_h5p\content
 */
class content_h5p_test extends \advanced_testcase {

    /**
     * Tests for uploaded file.
     *
     * @covers ::get_file
     */
    public function test_upload_file() {
        $this->resetAfterTest();

        // Create content.
        $record = new \stdClass();
        $record->name = 'Test content';
        $record->configdata = '';
        $contenttype = new \contenttype_h5p\contenttype(\context_system::instance());
        $content = $contenttype->create_content($record);

        // Create a dummy file.
        $filename = 'content.h5p';
        $dummy = [
            'contextid' => \context_system::instance()->id,
            'component' => 'contentbank',
            'filearea' => 'public',
            'itemid' => $content->get_id(),
            'filepath' => '/',
            'filename' => $filename
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($dummy, 'dummy content');

        $file = $content->get_file();
        $this->assertInstanceOf(\stored_file::class, $file);
        $this->assertEquals($filename, $file->get_filename());
    }

    /**
     * Tests for is view allowed content.
     *
     * @covers ::is_view_allowed
     * @dataProvider is_view_allowed_provider
     *
     * @param string $role User role to use for create and view contents.
     * @param array $disabledlibraries Library names to disable.
     * @param array $expected Array with the expected values for the contents in the following order:
     *     ['H5P.Blanks deployed', 'H5P.Accordion deployed', 'H5P.Accordion undeployed', 'Invalid content'].
     */
    public function test_is_view_allowed(string $role, array $disabledlibraries, array $expected): void {
        global $CFG, $USER, $DB;

        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Set user.
        if ($role == 'admin') {
            $this->setAdminUser();
        } else {
            // Enrol user to the course.
            $user = $this->getDataGenerator()->create_and_enrol($course, $role);
            $this->setUser($user);
        }

        // Add contents to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 1, $USER->id, $coursecontext, true, $filepath);
        $filltheblanks = array_shift($contents);
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/ipsums.h5p';
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 2, $USER->id, $coursecontext, true, $filepath);
        $accordion1 = array_shift($contents);
        $accordion2 = array_shift($contents);
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/invalid.zip';
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 1, $USER->id, $coursecontext, true, $filepath);
        $invalid = array_shift($contents);

        // Load some of these H5P files though the player to create the H5P DB entries.
        $h5pplayer = new \core_h5p\player($filltheblanks->get_file_url(), new \stdClass(), true);
        $h5pplayer = new \core_h5p\player($accordion1->get_file_url(), new \stdClass(), true);

        // Check the expected H5P content has been created.
        $this->assertEquals(2, $DB->count_records('h5p'));
        $this->assertEquals(4, $DB->count_records('contentbank_content'));

        // Disable libraries.
        foreach ($disabledlibraries as $libraryname) {
            $libraryid = $DB->get_field('h5p_libraries', 'id', ['machinename' => $libraryname]);
            \core_h5p\api::set_library_enabled((int) $libraryid, false);
        }

        $this->assertEquals($expected[0], $filltheblanks->is_view_allowed());
        $this->assertEquals($expected[1], $accordion1->is_view_allowed());
        $this->assertEquals($expected[2], $accordion2->is_view_allowed());
        $this->assertEquals($expected[3], $invalid->is_view_allowed());

        // Check that after enabling libraries again, all the content return true (but the invalid package).
        foreach ($disabledlibraries as $libraryname) {
            $libraryid = $DB->get_field('h5p_libraries', 'id', ['machinename' => $libraryname]);
            \core_h5p\api::set_library_enabled((int) $libraryid, true);
        }

        $this->assertEquals(true, $filltheblanks->is_view_allowed());
        $this->assertEquals(true, $accordion1->is_view_allowed());
        $this->assertEquals(true, $accordion2->is_view_allowed()); // It will be deployed, so now it will always return true.
        $this->assertEquals($expected[3], $invalid->is_view_allowed());
    }

    /**
     * Data provider for test_is_view_allowed.
     *
     * @return array
     */
    public function is_view_allowed_provider(): array {
        return [
            'Editing teacher with all libraries enabled' => [
                'role' => 'editingteacher',
                'disabledlibraries' => [],
                'expected' => [true, true, true, false],
            ],
            'Manager with all libraries enabled' => [
                'role' => 'manager',
                'disabledlibraries' => [],
                'expected' => [true, true, true, true],
            ],
            'Admin with all libraries enabled' => [
                'role' => 'admin',
                'disabledlibraries' => [],
                'expected' => [true, true, true, true],
            ],
            'Editing teacher with H5P.Accordion disabled' => [
                'role' => 'editingteacher',
                'disabledlibraries' => ['H5P.Accordion'],
                'expected' => [true, false, false, false],
            ],
            'Manager with H5P.Accordion disabled' => [
                'role' => 'manager',
                'disabledlibraries' => ['H5P.Accordion'],
                'expected' => [true, false, true, true],
            ],
            'Admin with H5P.Accordion disabled' => [
                'role' => 'admin',
                'disabledlibraries' => ['H5P.Accordion'],
                'expected' => [true, false, true, true],
            ],
            'Editing teacher with all libraries disabled' => [
                'role' => 'editingteacher',
                'disabledlibraries' => ['H5P.Accordion', 'H5P.Blanks'],
                'expected' => [false, false, false, false],
            ],
            'Manager with all libraries disabled' => [
                'role' => 'manager',
                'disabledlibraries' => ['H5P.Accordion', 'H5P.Blanks'],
                'expected' => [false, false, true, true],
            ],
            'Admin with all libraries disabled' => [
                'role' => 'admin',
                'disabledlibraries' => ['H5P.Accordion', 'H5P.Blanks'],
                'expected' => [false, false, true, true],
            ],
        ];
    }
}
