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

namespace mod_bigbluebuttonbn\local\proxy;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Recording proxy tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @covers  \mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy
 * @coversDefaultClass \mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy
 */
class bigbluebutton_proxy_test extends \advanced_testcase {
    /**
     * Test poll interval value
     *
     * @covers  \mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy::get_poll_interval
     * @return void
     */
    public function test_get_poll_interval(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->bigbluebuttonbn['poll_interval'] = 15;
        $this->assertEquals(15, bigbluebutton_proxy::get_poll_interval());
        $CFG->bigbluebuttonbn['poll_interval'] = 0;
        $this->assertEquals(bigbluebutton_proxy::MIN_POLL_INTERVAL, bigbluebutton_proxy::get_poll_interval());
    }
}
