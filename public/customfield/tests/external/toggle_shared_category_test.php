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

use core_external\external_api;
use core_customfield\shared;
use core_customfield_generator;

/**
 * Unit tests for custom field toggle_shared_category external method
 *
 * @package     core_customfield
 * @covers      \core_customfield\external\toggle_shared_category
 * @copyright   2025 David Carrillo <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class toggle_shared_category_test extends \core_external\tests\externallib_testcase {
    /**
     * Test execute
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $sharedcategory = $generator->create_category(['component' => 'core_customfield', 'area' => 'shared']);
        $generator->create_field([
            'categoryid' => $sharedcategory->get('id'),
            'name' => 'My shared field',
            'shortname' => 'mysharedfield',
            'type' => 'text',
        ]);

        $this->assertEmpty(shared::get_records());

        $result = toggle_shared_category::execute(
            $sharedcategory->get('id'),
            'core_course',
            'course',
            0,
            true
        );
        $result = external_api::clean_returnvalue(toggle_shared_category::execute_returns(), $result);
        $this->assertTrue($result);
        $records = shared::get_records();
        $record = reset($records);
        $this->assertEquals($sharedcategory->get('id'), $record->get('categoryid'));
        $this->assertEquals('core_course', $record->get('component'));
        $this->assertEquals('course', $record->get('area'));
        $this->assertEquals(0, $record->get('itemid'));

        $result = toggle_shared_category::execute(
            $sharedcategory->get('id'),
            'core_course',
            'course',
            0,
            false
        );
        $result = external_api::clean_returnvalue(toggle_shared_category::execute_returns(), $result);
        $this->assertTrue($result);
        $this->assertEmpty(shared::get_records());
    }

    /**
     * Test execute with no permission
     */
    public function test_execute_no_permission(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $sharedcategory = $generator->create_category(['component' => 'core_customfield', 'area' => 'shared']);
        $generator->create_field([
            'categoryid' => $sharedcategory->get('id'),
            'name' => 'My shared field',
            'shortname' => 'mysharedfield',
            'type' => 'text',
        ]);

        $this->setUser($user);

        $this->expectException(\moodle_exception::class);
        $str = "Sorry, but you do not currently have permissions to do that (Configure shared custom fields).";
        $this->expectExceptionMessage($str);
        toggle_shared_category::execute(
            $sharedcategory->get('id'),
            'core_course',
            'course',
            0,
            true
        );
    }
}
