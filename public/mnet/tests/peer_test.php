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

namespace core_mnet;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mnet/peer.php');

/**
 * Unit tests for mnet_peer hostname validation.
 *
 * @package    core_mnet
 * @copyright  2026 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mnet_peer
 */
final class peer_test extends \advanced_testcase {
    /**
     * Set up method.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test that bootstrap throws a moodle_exception when the peer URL is blocked by cURL security settings.
     */
    public function test_bootstrap_blocked_url(): void {
        set_config('curlsecurityblockedhosts', '10.255.255.1');

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('curlsecurityurlblocked', 'admin'));

        $peer = new \mnet_peer();
        $peer->bootstrap('http://10.255.255.1/', null, 'moodle');
    }
}
