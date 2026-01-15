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

namespace core_customfield\external;

use core_customfield\category;
use core_external\external_api;

/**
 * Unit tests for custom field convert_category external method
 *
 * @package     core_customfield
 * @covers      \core_customfield\external\convert_category
 * @copyright   2026 Yerai Rodríguez <yerai.rodriguez@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class convert_category_test extends \core_external\tests\externallib_testcase {
    /**
     * Test execute
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = $this->getDataGenerator()->create_custom_field_category([
            'component' => 'core_course',
            'area' => 'course',
        ]);
        $this->getDataGenerator()->create_custom_field([
            'shortname' => 'customfield1',
            'type' => 'text',
            'categoryid' => $category->get('id'),
        ]);

        $result = convert_category::execute(
            categoryid: $category->get('id'),
            component: 'core_course',
            area: 'course',
            itemid: 0,
        );

        $result = external_api::clean_returnvalue(convert_category::execute_returns(), $result);
        $this->assertTrue($result);

        // Assert category is now shared.
        $categoryrecord = category::get_record(['id' => $category->get('id')]);
        $this->assertEquals($category->get('id'), $categoryrecord->get('id'));
        $this->assertEquals('core_customfield', $categoryrecord->get('component'));
        $this->assertEquals('shared', $categoryrecord->get('area'));
        $this->assertEquals(0, $categoryrecord->get('itemid'));
        $this->assertEquals(1, $categoryrecord->get('shared'));
    }

    /**
     * Test execute with category not found
     */
    public function test_execute_category_not_found(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->expectException(\core\exception\invalid_parameter_exception::class);
        $this->expectExceptionMessage(
            'Custom field category with ID 999 does not exist for the specified component, area and itemid.'
        );
        convert_category::execute(
            categoryid: 999,
            component: 'core_course',
            area: 'course',
            itemid: 0,
        );
    }

    /**
     * Test execute with no permission
     */
    public function test_execute_no_permission(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $category = $this->getDataGenerator()->create_custom_field_category([
            'component' => 'core_course',
            'area' => 'course',
        ]);
        $this->getDataGenerator()->create_custom_field([
            'shortname' => 'customfield1',
            'type' => 'text',
            'categoryid' => $category->get('id'),
        ]);

        $this->setUser($user);

        $this->expectException(\core\exception\access_denied_exception::class);
        $this->expectExceptionMessage('You do not have permission to convert this category to shared.');
        convert_category::execute(
            categoryid: $category->get('id'),
            component: 'core_course',
            area: 'course',
            itemid: 0,
        );
    }

    /**
     * Test execute with existing custom field in shared category
     */
    public function test_execute_existing_customfield(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = $this->getDataGenerator()->create_custom_field_category([
            'component' => 'core_course',
            'area' => 'course',
        ]);
        $this->getDataGenerator()->create_custom_field([
            'shortname' => 'customfield1',
            'type' => 'text',
            'categoryid' => $category->get('id'),
        ]);

        $sharedcategory = $this->getDataGenerator()->create_custom_field_category([
            'component' => 'core_customfield',
            'area' => 'shared',
        ]);
        $this->getDataGenerator()->create_custom_field([
            'shortname' => 'customfield1',
            'type' => 'text',
            'categoryid' => $sharedcategory->get('id'),
        ]);

        $this->expectException(\core\exception\moodle_exception::class);
        $this->expectExceptionMessage(get_string('sharedcustomfieldalreadyexists', 'core_customfield'));
        convert_category::execute(
            categoryid: $category->get('id'),
            component: 'core_course',
            area: 'course',
            itemid: 0,
        );
    }
}
