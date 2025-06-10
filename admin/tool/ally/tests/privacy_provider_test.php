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
 * Test case for privacy implementation.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types\external_location;
use core_privacy\tests\provider_testcase;
use tool_ally\privacy\provider;

/**
 * Test case for privacy implementation.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class privacy_provider_test extends provider_testcase {
    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        if (!method_exists(collection::class, 'add_external_location_link')) {
            $this->markTestSkipped('This version of moodle does not fully support privacy');
        }
        $collection     = provider::get_metadata(new collection('tool_ally'));
        $itemcollection = $collection->get_collection();
        $this->assertCount(1, $itemcollection);

        /** @var external_location $item */
        $item = reset($itemcollection);
        $this->assertEquals('files', $item->get_name());

        $privacyfields = $item->get_privacy_fields();
        $this->assertCount(7, $privacyfields);
        $this->assertArrayHasKey('action', $privacyfields);
        $this->assertArrayHasKey('courseid', $privacyfields);
        $this->assertArrayHasKey('pathnamehash', $privacyfields);
        $this->assertArrayHasKey('timemodified', $privacyfields);
        $this->assertArrayHasKey('mimetype', $privacyfields);
        $this->assertArrayHasKey('contenthash', $privacyfields);
        $this->assertArrayHasKey('filecontents', $privacyfields);

        $this->assertEquals('privacy:metadata:files:externalpurpose', $item->get_summary());
    }
}
