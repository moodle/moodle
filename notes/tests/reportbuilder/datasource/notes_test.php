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

namespace core_notes\reportbuilder\datasource;

use core_collator;
use core_notes_generator;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\filters\{date, select, text};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for notes datasource
 *
 * @package     core_notes
 * @covers      \core_notes\reportbuilder\datasource\notes
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notes_test extends core_reportbuilder_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/notes/lib.php");
    }

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        /** @var core_notes_generator $notesgenerator */
        $notesgenerator = $this->getDataGenerator()->get_plugin_generator('core_notes');

        $course = $this->getDataGenerator()->create_course();
        $usercoursenote = $this->getDataGenerator()->create_and_enrol($course);
        $notesgenerator->create_instance(['courseid' => $course->id, 'userid' => $usercoursenote->id, 'content' => 'Course',
            'publishstate' => NOTES_STATE_PUBLIC]);

        $userpersonalnote = $this->getDataGenerator()->create_user();
        $notesgenerator->create_instance(['courseid' => $course->id, 'userid' => $userpersonalnote->id, 'content' => 'Personal',
            'publishstate' => NOTES_STATE_DRAFT]);

        $usersitenote = $this->getDataGenerator()->create_user();
        $notesgenerator->create_instance(['courseid' => $course->id, 'userid' => $usersitenote->id, 'content' => 'Site',
            'publishstate' => NOTES_STATE_SITE]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Notes', 'source' => notes::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(3, $content);

        // Consistent order (course, personal, site), just in case.
        core_collator::asort_array_of_arrays_by_key($content, 'c1_publishstate');
        $content = array_values($content);

        // Default columns are recipient, publishstate, course, note.
        $this->assertEquals([
            [fullname($usercoursenote), 'Course notes', $course->fullname, 'Course'],
            [fullname($userpersonalnote), 'Personal notes', '', 'Personal'],
            [fullname($usersitenote), 'Site notes', '', 'Site'],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        global $DB;

        $this->resetAfterTest();

        $recipient = $this->getDataGenerator()->create_user();
        $author = $this->getDataGenerator()->create_user();
        $this->setUser($author);

        /** @var core_notes_generator $notesgenerator */
        $notesgenerator = $this->getDataGenerator()->get_plugin_generator('core_notes');
        $note = $notesgenerator->create_instance(['courseid' => SITEID, 'publishstate' => NOTES_STATE_SITE, 'content' => 'Cool',
            'userid' => $recipient->id,
        ]);

        // Manually update the created/modified date of the note.
        $note->created = 1654038000;
        $note->lastmodified = $note->created + HOURSECS;
        $DB->update_record('post', $note);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Notes', 'source' => notes::class, 'default' => 0]);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'note:content']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'note:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'note:timemodified']);

        // Ensure we can add data from both user entities.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'recipient:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'author:fullname']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $this->assertEquals([
            'Cool',
            userdate($note->created),
            userdate($note->lastmodified),
            fullname($recipient),
            fullname($author),
        ], array_values($content[0]));
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public function datasource_filters_provider(): array {
        return [
            'Filter content' => ['content', 'Cool', 'note:content', [
                'note:content_operator' => text::IS_EQUAL_TO,
                'note:content_value' => 'Cool',
            ], true],
            'Filter content (no match)' => ['content', 'Cool', 'note:content', [
                'note:content_operator' => text::DOES_NOT_CONTAIN,
                'note:content_value' => 'Cool',
            ], false],
            'Filter publish state' => ['publishstate', 'site', 'note:publishstate', [
                'note:publishstate_operator' => select::EQUAL_TO,
                'note:publishstate_value' => 'site',
            ], true],
            'Filter publish state (no match)' => ['publishstate', 'site', 'note:publishstate', [
                'note:publishstate_operator' => select::EQUAL_TO,
                'note:publishstate_value' => 'public',
            ], false],
            'Filter time created' => ['created', 1654038000, 'note:timecreated', [
                'note:timecreated_operator' => date::DATE_RANGE,
                'note:timecreated_from' => 1622502000,
            ], true],
            'Filter time created (no match)' => ['created', 1654038000, 'note:timecreated', [
                'note:timecreated_operator' => date::DATE_RANGE,
                'note:timecreated_to' => 1622502000,
            ], false],
            'Filter time modified' => ['lastmodified', 1654038000, 'note:timemodified', [
                'note:timemodified_operator' => date::DATE_RANGE,
                'note:timemodified_from' => 1622502000,
            ], true],
            'Filter time modified (no match)' => ['lastmodified', 1654038000, 'note:timemodified', [
                'note:timemodified_operator' => date::DATE_RANGE,
                'note:timemodified_to' => 1622502000,
            ], false],
        ];
    }

    /**
     * Test datasource filters
     *
     * @param string $field
     * @param mixed $value
     * @param string $filtername
     * @param array $filtervalues
     * @param bool $expectmatch
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(
        string $field,
        $value,
        string $filtername,
        array $filtervalues,
        bool $expectmatch
    ): void {
        global $DB;

        $this->resetAfterTest();

        $recipient = $this->getDataGenerator()->create_user();

        /** @var core_notes_generator $notesgenerator */
        $notesgenerator = $this->getDataGenerator()->get_plugin_generator('core_notes');

        // Create default note, then manually override one of it's properties to use for filtering.
        $note = $notesgenerator->create_instance(['courseid' => SITEID, 'userid' => $recipient->id]);
        $DB->set_field('post', $field, $value, ['id' => $note->id]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single recipient column, and given filter.
        $report = $generator->create_report(['name' => 'Notes', 'source' => notes::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'recipient:fullname']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals(fullname($recipient), reset($content[0]));
        } else {
            $this->assertEmpty($content);
        }
    }

    /**
     * Stress test datasource
     *
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function test_stress_datasource(): void {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $recipient = $this->getDataGenerator()->create_user();

        /** @var core_notes_generator $notesgenerator */
        $notesgenerator = $this->getDataGenerator()->get_plugin_generator('core_notes');
        $notesgenerator->create_instance(['courseid' => SITEID, 'userid' => $recipient->id]);

        $this->datasource_stress_test_columns(notes::class);
        $this->datasource_stress_test_columns_aggregation(notes::class);
        $this->datasource_stress_test_conditions(notes::class, 'note:content');
    }
}
