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

namespace mod_subsection;

use availability_date\condition;
use core_availability\tree;
use core_courseformat\formatactions;

/**
 * Tests for Subsection manager class.
 *
 * @covers     \mod_subsection\manager
 * @package    mod_subsection
 * @category   test
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manager_test extends \advanced_testcase {
    /**
     * Test get_delegated_section_info.
     *
     * @covers ::get_delegated_section_info
     * @dataProvider provider_test_get_delegated_section_info
     * @param bool $hasavailability Whether the module has access restrictions.
     * @param bool $visible Whether the module is visible.
     * @return void
     */
    public function test_get_delegated_section_info(
        bool $hasavailability,
        bool $visible
    ): void {
        global $DB;

        $this->resetAfterTest();

        // Set up the availability settings.
        $availabilityjson = null;
        if ($hasavailability) {
            $operation = condition::DIRECTION_FROM;
            $availabilityjson = json_encode(tree::get_root_json(
                [
                    condition::get_json($operation, time() + 3600),
                ],
                '&',
                true
            ));
        }

        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 2]);
        $module = $this->getDataGenerator()->create_module(
            'subsection',
            (object)['course' => $course->id, 'section' => 2],
            ['visible' => $visible, 'availability' => $availabilityjson]
        );

        $cm = get_coursemodule_from_id('subsection', $module->cmid, 0, false, MUST_EXIST);
        $manager = manager::create_from_coursemodule($cm);
        $sectioninfo = $manager->get_delegated_section_info();
        $this->assertInstanceOf(\section_info::class, $sectioninfo);
        $this->assertEquals($cm->instance, $sectioninfo->itemid);
        $this->assertEquals($cm->name, $sectioninfo->name);
        $this->assertEquals($cm->visible, $sectioninfo->visible);
        $this->assertEquals($cm->availability, $sectioninfo->availability);
        $initialid = $sectioninfo->id;

        // When subsections are disabled, all subsections are considered orphaned
        // and can be removed without affecting the course_module. This should regenerate
        // the delegated section once the module is re-enabled.
        $pluginmanager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $pluginmanager::enable_plugin('subsection', 0);
        formatactions::section($course)->delete($sectioninfo);
        rebuild_course_cache($course->id, true);
        $pluginmanager::enable_plugin('subsection', 1);
        rebuild_course_cache($course->id, true);

        $cm = get_coursemodule_from_id('subsection', $module->cmid, 0, false, MUST_EXIST);
        $manager = manager::create_from_coursemodule($cm);
        $sectioninfo = $manager->get_delegated_section_info();
        $this->assertInstanceOf(\section_info::class, $sectioninfo);
        $this->assertEquals($cm->instance, $sectioninfo->itemid);
        $this->assertEquals($cm->name, $sectioninfo->name);
        $this->assertEquals($cm->visible, $sectioninfo->visible);
        $this->assertEquals($cm->availability, $sectioninfo->availability);

        // The section should be different from the previous one.
        $this->assertNotEquals($initialid, $sectioninfo->id);
    }

    /**
     * Data provider for test_get_delegated_section_info.
     *
     * @return array
     */
    public static function provider_test_get_delegated_section_info(): array {
        return [
            'Module is visible with no restrictions' => [
                'hasavailability' => false,
                'visible' => true,
            ],
            'Module is visible with restrictions' => [
                'hasavailability' => true,
                'visible' => true,
            ],
            'Module is hidden with no restrictions' => [
                'hasavailability' => false,
                'visible' => false,
            ],
            'Module is hidden with restrictions' => [
                'hasavailability' => true,
                'visible' => false,
            ],

        ];
    }

    /**
     * Test clear_description.
     */
    public function test_clear_description(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        // Add a couple of subsections with file in the description.
        $module1 = $this->getDataGenerator()->create_module('subsection', [
            'course' => $course->id,
            'section' => 1,
            'summary' => 'Subsection text with <a href="@@PLUGINFILE@@/intro1.txt">link</a>',
        ]);
        $subsection1 = $DB->get_record(
            'course_sections',
            ['course' => $course->id, 'itemid' => $module1->id],
        );
        $filerecord = [
            'component' => 'course',
            'filearea' => 'section',
            'contextid' => \context_course::instance($course->id)->id,
            'itemid' => $subsection1->id,
            'filename' => 'intro1.txt',
            'filepath' => '/',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'Test intro file');

        $module2 = $this->getDataGenerator()->create_module('subsection', [
            'course' => $course->id,
            'section' => 1,
            'summary' => 'Subsection text with <a href="@@PLUGINFILE@@/intro2.txt">link</a>',
        ]);
        $subsection2 = $DB->get_record(
            'course_sections',
            ['course' => $course->id, 'itemid' => $module2->id],
        );
        $filerecord = [
            'component' => 'course',
            'filearea' => 'section',
            'contextid' => \context_course::instance($course->id)->id,
            'itemid' => $subsection2->id,
            'filename' => 'intro2.txt',
            'filepath' => '/',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'Test intro file');
        // Add one more subsection with description but no files.
        $module3 = $this->getDataGenerator()->create_module('subsection', [
            'course' => $course->id,
            'section' => 1,
            'summary' => 'Subsection text with no files',
        ]);

        // Check subsections have descriptions.
        $this->assertEquals(
            3,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        $this->assertEquals(
            2,
            $DB->count_records_select(
                'files',
                'component = :component AND filearea = :filearea AND (filename != :filename)',
                [
                    'component' => 'course',
                    'filearea' => 'section',
                    'filename' => '.',
                ],
            ),
        );

        // Clear the description for subsection with files.
        $manager = manager::create_from_id($course->id, $module1->id);
        $manager->clear_description();

        // Check only subsection description and its files have been removed.
        $this->assertEquals(
            2,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        // Check the file has been removed too.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'files',
                'component = :component AND filearea = :filearea AND (filename != :filename)',
                [
                    'component' => 'course',
                    'filearea' => 'section',
                    'filename' => '.',
                ],
            ),
        );

        // Clear the description for subsection without files.
        $manager = manager::create_from_id($course->id, $module3->id);
        $manager->clear_description();

        // Check only subsection3 description has been removed.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'course_sections',
                'course = :courseid AND component = \'mod_subsection\' AND summary != \'\'',
                ['courseid' => $course->id],
            ),
        );
        // Check no files have been removed.
        $this->assertEquals(
            1,
            $DB->count_records_select(
                'files',
                'component = :component AND filearea = :filearea AND (filename != :filename)',
                [
                    'component' => 'course',
                    'filearea' => 'section',
                    'filename' => '.',
                ],
            ),
        );
    }
}
