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
 * Provides the {@link core_form\external_testcase} class.
 *
 * @package     core_form
 * @category    test
 * @copyright   2017 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_form;

use advanced_testcase;
use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Test cases for the {@link core_form\external} class.
 *
 * @copyright 2017 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_test extends advanced_testcase {

    /**
     * Test the core_form_get_filetypes_browser_data external function
     */
    public function test_get_filetypes_browser_data() {

        $data = external::get_filetypes_browser_data('', true, '');
        $data = external_api::clean_returnvalue(external::get_filetypes_browser_data_returns(), $data);
        $data = json_decode(json_encode($data));

        // The actual data are tested in filetypes_util_test.php, here we just
        // make sure that the external function wrapper seems to work.
        $this->assertIsObject($data);
        $this->assertIsArray($data->groups);
    }
}
