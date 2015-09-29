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
 * Provides core_update_checker_testcase class.
 *
 * @package     core
 * @category    test
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__.'/fixtures/testable_update_api.php');

/**
 * Tests for \core\update\api client.
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_update_api_testcase extends advanced_testcase {

    /**
     * Make sure the $CFG->branch is mapped correctly to the format used by the API.
     */
    public function test_convert_branch_numbering_format() {

        $client = \core\update\testable_api::client();

        $this->assertSame('2.9', $client->convert_branch_numbering_format(29));
        $this->assertSame('3.0', $client->convert_branch_numbering_format('30'));
        $this->assertSame('3.1', $client->convert_branch_numbering_format(3.1));
        $this->assertSame('3.1', $client->convert_branch_numbering_format('3.1'));
        $this->assertSame('10.1', $client->convert_branch_numbering_format(101));
        $this->assertSame('10.2', $client->convert_branch_numbering_format('102'));
    }

    /**
     * Getting info about particular plugin version.
     */
    public function test_get_plugin_info() {

        $client = \core\update\testable_api::client();

        // The plugin is not found in the plugins directory.
        $this->assertFalse($client->get_plugin_info('non_existing', 2015093000));

        // The plugin is known but there is no such version.
        $info = $client->get_plugin_info('foo_bar', 2014010100);
        $this->assertFalse($info->version);

        // Both plugin and the version are available.
        $info = $client->get_plugin_info('foo_bar', 2015093000);
        $this->assertNotNull($info->version->downloadurl);
    }

    /**
     * Getting info about the most suitable plugin version for us.
     */
    public function test_find_plugin() {

        $client = \core\update\testable_api::client();

        // The plugin is not found in the plugins directory.
        $this->assertFalse($client->find_plugin('non_existing'));

        // The plugin is known but there is no sufficient version.
        $info = $client->find_plugin('foo_bar', 2015093001);
        $this->assertFalse($info->version);

        // Both plugin and the version are available.
        $info = $client->find_plugin('foo_bar', 2015093000);
        $this->assertNotNull($info->version->downloadurl);

        $info = $client->find_plugin('foo_bar', ANY_VERSION);
        $this->assertNotNull($info->version->downloadurl);
    }
}
