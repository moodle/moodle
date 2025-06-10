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
 * PHPUnit MHUtil tests.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/blocks/mhaairs/block_mhaairs_util.php");

/**
 * PHPUnit mhaairs util test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_util
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_util_testcase extends advanced_testcase {

    /**
     * Tests token validation.
     *
     * @return void
     */
    public function test_is_token_valid() {
        $this->resetAfterTest();

        $secret = 'DF4#R66';
        $userid = 'mhaairs';

        $token = MHUtil::create_token($userid);
        $encodedtoken = MHUtil::encode_token2($token, $secret);
        $istokenvalid = MHUtil::is_token_valid($encodedtoken, $secret);
        $this->assertEquals(true, $istokenvalid);
    }

    /**
     * Tests environment info.
     *
     * @return void
     */
    public function test_environment_info() {
        $this->resetAfterTest();

        $envinfo = MHUtil::get_environment_info();
        $this->assertEquals(true, !empty($envinfo->phpversion));
    }
}
