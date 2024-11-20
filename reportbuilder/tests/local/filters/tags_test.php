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

namespace core_reportbuilder\local\filters;

use advanced_testcase;
use lang_string;
use core_reportbuilder_generator;
use core_reportbuilder\local\report\filter;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for tags report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\tags
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array[]
     */
    public static function get_sql_filter_provider(): array {
        return [
            'Any value' => [tags::ANY_VALUE, null, ['course01', 'course01', 'course02', 'course03']],
            'Not empty' => [tags::NOT_EMPTY, null, ['course01', 'course01', 'course02']],
            'Empty' => [tags::EMPTY, null, ['course03']],
            'Equal to unselected' => [tags::EQUAL_TO, null, ['course01', 'course01', 'course02', 'course03']],
            'Equal to selected tag' => [tags::EQUAL_TO, 'cat', ['course01']],
            'Not equal to unselected' => [tags::NOT_EQUAL_TO, null, ['course01', 'course01', 'course02', 'course03']],
            'Not equal to selected tag' => [tags::NOT_EQUAL_TO, 'fish', ['course01', 'course01', 'course03']],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param string|null $tagname
     * @param array $expectedcoursenames
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(int $operator, ?string $tagname, array $expectedcoursenames): void {
        global $DB;

        $this->resetAfterTest();

        $this->getDataGenerator()->create_course(['fullname' => 'course01', 'tags' => ['cat', 'dog']]);
        $this->getDataGenerator()->create_course(['fullname' => 'course02', 'tags' => ['fish']]);
        $this->getDataGenerator()->create_course(['fullname' => 'course03']);

        $filter = (new filter(
            tags::class,
            'tags',
            new lang_string('tags'),
            'testentity',
            't.id'
        ));

        // Create instance of our filter, passing ID of the tag if specified.
        if ($tagname !== null) {
            $tagid = $DB->get_field('tag', 'id', ['name' => $tagname], MUST_EXIST);
            $value = [$tagid];
        } else {
            $value = null;
        }

        [$select, $params] = tags::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $value,
        ]);

        $sql = 'SELECT c.fullname
                  FROM {course} c
             LEFT JOIN {tag_instance} ti ON ti.itemid = c.id
             LEFT JOIN {tag} t ON t.id = ti.tagid
                 WHERE c.id != ' . SITEID;

        if ($select) {
            $sql .= " AND {$select}";
        }

        $courses = $DB->get_fieldset_sql($sql, $params);
        $this->assertEqualsCanonicalizing($expectedcoursenames, $courses);
    }

    /**
     * Data provider for {@see test_get_sql_filter_component}
     *
     * @return array[]
     */
    public static function get_sql_filter_component_provider(): array {
        return [
            'Any value' => [tags::ANY_VALUE, null, ['report01', 'report02']],
            'Not empty' => [tags::NOT_EMPTY, null, ['report01']],
            'Empty' => [tags::EMPTY, null, ['report02']],
            'Equal to unselected' => [tags::EQUAL_TO, null, ['report01', 'report02']],
            'Equal to selected tag' => [tags::EQUAL_TO, 'fish', ['report01']],
            'Equal to selected tag (different component)' => [tags::EQUAL_TO, 'cat', []],
            'Not equal to unselected' => [tags::NOT_EQUAL_TO, null, ['report01', 'report02']],
            'Not equal to selected tag' => [tags::NOT_EQUAL_TO, 'fish', ['report02']],
            'Not Equal to selected tag (different component)' => [tags::NOT_EQUAL_TO, 'cat', ['report01', 'report02']],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param string|null $tagname
     * @param array $expectedreportnames
     *
     * @dataProvider get_sql_filter_component_provider
     */
    public function test_get_sql_filter_component(int $operator, ?string $tagname, array $expectedreportnames): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course with tags, we shouldn't ever get this data back when specifying another component.
        $this->getDataGenerator()->create_course(['tags' => ['cat', 'dog']]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $generator->create_report(['name' => 'report01', 'source' => users::class, 'tags' => ['fish']]);
        $generator->create_report(['name' => 'report02', 'source' => users::class]);

        $filter = (new filter(
            tags::class,
            'tags',
            new lang_string('tags'),
            'testentity',
            'r.id'
        ))->set_options([
            'component' => 'core_reportbuilder',
            'itemtype' => 'reportbuilder_report',
        ]);

        // Create instance of our filter, passing ID of the tag if specified.
        if ($tagname !== null) {
            $tagid = $DB->get_field('tag', 'id', ['name' => $tagname], MUST_EXIST);
            $value = [$tagid];
        } else {
            $value = null;
        }

        [$select, $params] = tags::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $value,
        ]);

        $sql = 'SELECT r.name FROM {reportbuilder_report} r';
        if ($select) {
            $sql .= " WHERE {$select}";
        }

        $reports = $DB->get_fieldset_sql($sql, $params);
        $this->assertEqualsCanonicalizing($expectedreportnames, $reports);
    }
}
