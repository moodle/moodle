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
 * External airnotifier functions unit tests
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External airnotifier functions unit tests
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_airnotifier_external_testcase extends externallib_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/lib/externallib.php');
        require_once($CFG->dirroot . '/webservice/externallib.php');
    }

    /**
     * Test add_user_device
     */
    public function test_add_user_device() {

        global $DB, $USER;

        $this->resetAfterTest(true);

        $user  = self::getDataGenerator()->create_user();
        self::setUser($user);

        $device = array();
        $device['appname'] = 'mymoodle';
        $device['devicename'] = 'Jerome\'s Android';
        $device['devicetype'] = 'galaxy nexus';
        $device['deviceos'] = 'android';
        $device['deviceosversion'] = '4.0.3';
        $device['devicebrand'] = 'samsung';
        $device['devicenotificationtoken'] = 'jhg576576sesgy98sd7g87sdg697sfg576df';
        $device['deviceuid'] = 'is87fs64g2vuf84g378gbh378ehg98h875';

        $deviceid = message_airnotifier_external::add_user_device($device);

        $devicedb = $DB->get_record('user_devices', array('id' => $deviceid));
        $this->assertEquals($devicedb->devicename, $device['devicename']);
        $this->assertEquals($devicedb->devicetype, $device['devicetype']);
        $this->assertEquals($devicedb->deviceos, $device['deviceos']);
        $this->assertEquals($devicedb->deviceosversion, $device['deviceosversion']);
        $this->assertEquals($devicedb->devicebrand, $device['devicebrand']);
        $this->assertEquals($devicedb->devicenotificationtoken, $device['devicenotificationtoken']);
        $this->assertEquals($devicedb->deviceuid, $device['deviceuid']);
        $this->assertEquals($devicedb->userid, $USER->id);
    }

}
