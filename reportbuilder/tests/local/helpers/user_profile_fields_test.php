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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for user profile fields helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\user_profile_fields
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_profile_fields_test_testcase extends advanced_testcase {

    /**
     * Generate userprofilefields
     */
    private function generate_userprofilefields(): user_profile_fields {
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'upf1', 'name' => 'User profile field text 1', 'datatype' => 'text']);

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'upf2', 'name' => 'User profile field text 2', 'datatype' => 'text']);

        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');

        // Create an instance of the userprofilefield helper.
        return new user_profile_fields("$useralias.id", $userentity->get_entity_name());
    }

    /**
     * Test for get_columns
     */
    public function test_get_columns(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $columns = $userprofilefields->get_columns();
        $this->assertCount(2, $columns);
        [$column0, $column1] = $columns;
        $this->assertInstanceOf(column::class, $column0);
        $this->assertInstanceOf(column::class, $column1);
        $this->assertEqualsCanonicalizing(['User profile field text 1', 'User profile field text 2'],
            [$column0->get_title(), $column1->get_title()]);
        $this->assertEquals(column::TYPE_TEXT, $column0->get_type());
        $this->assertEquals('user', $column0->get_entity_name());
        $this->assertStringStartsWith('LEFT JOIN {user_info_data}', $column0->get_joins()[0]);
    }

    /**
     * Test for add_join
     */
    public function test_add_join(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $columns = $userprofilefields->get_columns();
        $this->assertCount(1, ($columns[0])->get_joins());

        $userprofilefields->add_join('JOIN {test} t ON t.id = id');
        $columns = $userprofilefields->get_columns();
        $this->assertCount(2, ($columns[0])->get_joins());
    }

    /**
     * Test for add_joins
     */
    public function test_add_joins(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $columns = $userprofilefields->get_columns();
        $this->assertCount(1, ($columns[0])->get_joins());

        $userprofilefields->add_joins(['JOIN {test} t ON t.id = id', 'JOIN {test2} t2 ON t2.id = id']);
        $columns = $userprofilefields->get_columns();
        $this->assertCount(3, ($columns[0])->get_joins());
    }

    /**
     * Test for get_filters
     */
    public function test_get_filters(): void {
        $this->resetAfterTest();

        $userprofilefields = $this->generate_userprofilefields();
        $filters = $userprofilefields->get_filters();
        $this->assertCount(2, $filters);
        [$filter0, $filter1] = $filters;
        $this->assertInstanceOf(filter::class, $filter0);
        $this->assertInstanceOf(filter::class, $filter1);
        $this->assertEqualsCanonicalizing(['User profile field text 1', 'User profile field text 2'],
            [$filter0->get_header(), $filter1->get_header()]);
    }
}
