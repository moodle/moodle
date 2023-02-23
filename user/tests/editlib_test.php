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

namespace core_user;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/user/editlib.php');

/**
 * Unit tests for user editlib api.
 *
 * @package    core_user
 * @category   test
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editlib_test extends \advanced_testcase {

    /**
     * Test that the required fields are returned in the correct order.
     */
    function test_useredit_get_required_name_fields() {
        global $CFG;
        // Back up config settings for restore later.
        $originalcfg = new \stdClass();
        $originalcfg->fullnamedisplay = $CFG->fullnamedisplay;

        $CFG->fullnamedisplay = 'language';
        $expectedresult = array(5 => 'firstname', 21 => 'lastname');
        $this->assertEquals(useredit_get_required_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstname';
        $expectedresult = array(5 => 'firstname', 21 => 'lastname');
        $this->assertEquals(useredit_get_required_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'lastname firstname';
        $expectedresult = array('lastname', 9 => 'firstname');
        $this->assertEquals(useredit_get_required_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstnamephonetic lastnamephonetic';
        $expectedresult = array(5 => 'firstname', 21 => 'lastname');
        $this->assertEquals(useredit_get_required_name_fields(), $expectedresult);

        // Tidy up after we finish testing.
        $CFG->fullnamedisplay = $originalcfg->fullnamedisplay;
    }

    /**
     * Test that the enabled fields are returned in the correct order.
     */
    function test_useredit_get_enabled_name_fields() {
        global $CFG;
        // Back up config settings for restore later.
        $originalcfg = new \stdClass();
        $originalcfg->fullnamedisplay = $CFG->fullnamedisplay;

        $CFG->fullnamedisplay = 'language';
        $expectedresult = array();
        $this->assertEquals(useredit_get_enabled_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstname lastname firstnamephonetic';
        $expectedresult = array(19 => 'firstnamephonetic');
        $this->assertEquals(useredit_get_enabled_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstnamephonetic, lastname lastnamephonetic (alternatename)';
        $expectedresult = array('firstnamephonetic', 28 => 'lastnamephonetic', 46 => 'alternatename');
        $this->assertEquals(useredit_get_enabled_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstnamephonetic lastnamephonetic alternatename middlename';
        $expectedresult = array('firstnamephonetic', 18 => 'lastnamephonetic', 35 => 'alternatename', 49 => 'middlename');
        $this->assertEquals(useredit_get_enabled_name_fields(), $expectedresult);

        // Tidy up after we finish testing.
        $CFG->fullnamedisplay = $originalcfg->fullnamedisplay;
    }

    /**
     * Test that the disabled fields are returned.
     */
    function test_useredit_get_disabled_name_fields() {
        global $CFG;
        // Back up config settings for restore later.
        $originalcfg = new \stdClass();
        $originalcfg->fullnamedisplay = $CFG->fullnamedisplay;

        $CFG->fullnamedisplay = 'language';
        $expectedresult = array('firstnamephonetic' => 'firstnamephonetic', 'lastnamephonetic' => 'lastnamephonetic',
                'middlename' => 'middlename', 'alternatename' => 'alternatename');
        $this->assertEquals(useredit_get_disabled_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstname lastname firstnamephonetic';
        $expectedresult = array('lastnamephonetic' => 'lastnamephonetic', 'middlename' => 'middlename', 'alternatename' => 'alternatename');
        $this->assertEquals(useredit_get_disabled_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstnamephonetic, lastname lastnamephonetic (alternatename)';
        $expectedresult = array('middlename' => 'middlename');
        $this->assertEquals(useredit_get_disabled_name_fields(), $expectedresult);
        $CFG->fullnamedisplay = 'firstnamephonetic lastnamephonetic alternatename middlename';
        $expectedresult = array();
        $this->assertEquals(useredit_get_disabled_name_fields(), $expectedresult);

        // Tidy up after we finish testing.
        $CFG->fullnamedisplay = $originalcfg->fullnamedisplay;
    }
}
