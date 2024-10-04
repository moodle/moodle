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

namespace tool_usertours;

use context_course;
use context_coursecat;
use context_system;
use tool_usertours\local\filter\category;
use context;

/**
 * Tests for category filter.
 *
 * @package tool_usertours
 * @copyright 2025 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \tool_usertours\local\filter\category
 */
final class category_filter_test extends \advanced_testcase {
    /** @var \core_course_category */
    private \core_course_category $category1;
    /** @var \core_course_category */
    private \core_course_category $category2;
    /** @var \core_course_category */
    private \core_course_category $childcategory1;
    /** @var \core_course_category */
    private \core_course_category $childcategory2;
    /** @var \stdClass */
    private \stdClass $course1;
    /** @var \stdClass */
    private \stdClass $course2;
    /** @var \stdClass */
    private \stdClass $coursechild1;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        // Create parent categories.
        $this->category1 = $this->getDataGenerator()->create_category();
        $this->category2 = $this->getDataGenerator()->create_category();

        // Create child categories.
        $this->childcategory1 = $this->getDataGenerator()->create_category(['parent' => $this->category1->id]);
        $this->childcategory2 = $this->getDataGenerator()->create_category(['parent' => $this->category2->id]);

        // Create courses.
        $this->course1 = $this->getDataGenerator()->create_course(['category' => $this->category1->id]);
        $this->course2 = $this->getDataGenerator()->create_course(['category' => $this->category2->id]);
        $this->coursechild1 = $this->getDataGenerator()->create_course(['category' => $this->childcategory1->id]);
    }

    /**
     * Data provider for test_filter_matches.
     *
     * @return array
     */
    public static function filter_matches_provider(): array {
        return [
            'Parent category excluded, child category context' => [
                ['exclude' => ['{{CATEGORY1_ID}}']],
                'category:{{CHILD_CATEGORY1_ID}}',
                false,
            ],
        ];
    }

    /**
     * Test the filter_matches method.
     *
     * @dataProvider filter_matches_provider
     * @param array $tourconfig Tour configuration
     * @param string $contextinfo Context information
     * @param bool $expected Expected result
     */
    public function test_filter_matches(array $tourconfig, string $contextinfo, bool $expected): void {
        $this->resetAfterTest();

        // Replace placeholder IDs with actual IDs.
        $tourconfig = $this->replace_ids($tourconfig);
        $contextinfo = $this->replace_ids($contextinfo);

        $context = $this->create_context_from_string($contextinfo);

        $tour = new tour();
        $tour->set_filter_values('category', $tourconfig['include'] ?? []);
        $tour->set_filter_values('exclude_category', $tourconfig['exclude'] ?? []);

        $result = category::filter_matches($tour, $context);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test the get_filter_name method.
     */
    public function test_get_filter_name(): void {
        $this->assertEquals('category', category::get_filter_name());
    }

    /**
     * Test the get_filter_options method.
     */
    public function test_get_filter_options(): void {
        $options = category::get_filter_options();

        $this->assertIsArray($options);
        $this->assertArrayHasKey($this->category1->id, $options);
        $this->assertArrayHasKey($this->category2->id, $options);
        $this->assertArrayHasKey($this->childcategory1->id, $options);
        $this->assertArrayHasKey($this->childcategory2->id, $options);
    }

    /**
     * Create a context object from a string.
     *
     * @param string $contextinfo The context information.
     * @return context The context object.
     */
    private function create_context_from_string(string $contextinfo): context {
        $parts = explode(':', $contextinfo);
        $contextlevel = $parts[0];
        $instanceid = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : 0;

        return match ($contextlevel) {
            'system' => context_system::instance(),
            'category' => context_coursecat::instance($instanceid),
            'course' => context_course::instance($instanceid)
        };
    }

    /**
     * Replace placeholder IDs with actual IDs.
     *
     * @param mixed $data The data to process.
     * @return mixed The processed data.
     */
    private function replace_ids($data) {
        if (is_array($data)) {
            return array_map([$this, 'replace_ids'], $data);
        } else if (is_string($data)) {
            $replacements = [
                '{{CATEGORY1_ID}}' => $this->category1->id,
                '{{CATEGORY2_ID}}' => $this->category2->id,
                '{{CHILD_CATEGORY1_ID}}' => $this->childcategory1->id,
                '{{CHILD_CATEGORY2_ID}}' => $this->childcategory2->id,
                '{{COURSE1_ID}}' => $this->course1->id,
                '{{COURSE2_ID}}' => $this->course2->id,
                '{{COURSE_CHILD1_ID}}' => $this->coursechild1->id,
            ];
            return strtr($data, $replacements);
        }
        return $data;
    }
}
