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
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for course category report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\category
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class category_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array
     */
    public static function get_sql_filter_provider(): array {
        return [
            // Equal to.
            ['One', category::EQUAL_TO, false, ['One']],
            ['One', category::EQUAL_TO, true, ['One', 'Two', 'Three']],
            ['Two', category::EQUAL_TO, true, ['Two', 'Three']],
            ['Three', category::EQUAL_TO, true, ['Three']],

            // Not equal to.
            ['One', category::NOT_EQUAL_TO, false, ['Category 1', 'Two', 'Three', 'Four', 'Five', 'Six']],
            ['One', category::NOT_EQUAL_TO, true, ['Category 1', 'Four', 'Five', 'Six']],
            ['Two', category::NOT_EQUAL_TO, true, ['Category 1', 'One', 'Four', 'Five', 'Six']],
            ['Three', category::NOT_EQUAL_TO, true, ['Category 1', 'One', 'Two', 'Four', 'Five', 'Six']],

            // Default/empty state.
            [null, category::EQUAL_TO, false, ['Category 1', 'One', 'Two', 'Three', 'Four', 'Five', 'Six']],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param string|null $categoryname
     * @param int $operator
     * @param bool $subcategories
     * @param string[] $expectedcategories
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(
        ?string $categoryname,
        int $operator,
        bool $subcategories,
        array $expectedcategories,
    ): void {

        global $DB;

        $this->resetAfterTest();

        // Create category tree "One -> Two -> Three".
        $category1 = $this->getDataGenerator()->create_category(['name' => 'One']);
        $category2 = $this->getDataGenerator()->create_category(['name' => 'Two', 'parent' => $category1->id]);
        $category3 = $this->getDataGenerator()->create_category(['name' => 'Three', 'parent' => $category2->id]);

        // Second category tree "Four -> Five -> Six".
        $category4 = $this->getDataGenerator()->create_category(['name' => 'Four']);
        $category5 = $this->getDataGenerator()->create_category(['name' => 'Five', 'parent' => $category4->id]);
        $category6 = $this->getDataGenerator()->create_category(['name' => 'Six', 'parent' => $category5->id]);

        if ($categoryname !== null) {
            $categoryid = $DB->get_field('course_categories', 'id', ['name' => $categoryname], MUST_EXIST);
        } else {
            $categoryid = null;
        }

        $filter = new filter(
            category::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'id'
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = category::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $categoryid,
            $filter->get_unique_identifier() . '_subcategories' => $subcategories,
        ]);

        $categories = $DB->get_fieldset_select('course_categories', 'name', $select, $params);
        $this->assertEqualsCanonicalizing($expectedcategories, $categories);
    }

    /**
     * Test getting filter SQL with parameters
     */
    public function test_get_sql_filter_parameters(): void {
        global $DB;

        $this->resetAfterTest();

        $category1 = $this->getDataGenerator()->create_category(['name' => 'One']);
        $category2 = $this->getDataGenerator()->create_category(['name' => 'Two', 'parent' => $category1->id]);
        $category3 = $this->getDataGenerator()->create_category(['name' => 'Three']);

        // Rather convoluted filter SQL, but enough to demonstrate usage of a parameter that gets used twice in the query.
        $paramzero = database::generate_param_name();
        $filter = new filter(
            category::class,
            'test',
            new lang_string('yes'),
            'testentity',
            "id + :{$paramzero}",
            [$paramzero => 0]
        );

        // When including sub-categories, the filter SQL is included twice (for the category itself, plus to find descendents).
        [$select, $params] = category::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_value' => $category1->id,
            $filter->get_unique_identifier() . '_subcategories' => true,
        ]);

        $categories = $DB->get_fieldset_select('course_categories', 'id', $select, $params);
        $this->assertEqualsCanonicalizing([$category1->id, $category2->id], $categories);
    }
}
