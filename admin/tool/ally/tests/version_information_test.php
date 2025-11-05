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
 * Test for version_information class.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\version_information;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * @group     tool_ally
 * @group     ally
 */
class version_information_test extends abstract_testcase {

    public function test_plugin_not_intsalled() {
        $versioninfo = new version_information();

        // Test out a module that we know will definitely be installed because it's core.
        $info = \phpunit_util::call_internal_method(
            $versioninfo, 'get_component_version', ['label'],
            version_information::class);

        $this->assertTrue($info->installed);
        $this->assertNotEmpty($info->version);
        $this->assertNotEmpty($info->requires);

        // Test out a fake module that definitely won't be installed.
        $info = \phpunit_util::call_internal_method(
            $versioninfo, 'get_component_version', ['some_fake_module'],
            version_information::class);

        $this->assertFalse($info->installed);
        $this->assertTrue(!isset($info->version));
        $this->assertTrue(!isset($info->requires));

    }

}
