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
 * Test auto configuration class.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\auto_config;

class auto_config_test extends \advanced_testcase {
    public function test_auto_config() {
        global $DB;

        $this->resetAfterTest();

        $field = $this->create_profile_field();
        $ac = new auto_config();
        $ac->configure();

        $this->assertNotEmpty($ac->token);
        $this->assertNotEmpty($ac->user);
        $this->assertNotEmpty($ac->role);

        $dataprofile = $DB->get_records('user_info_data', array('fieldid' => $field->id));
        $this->assertCount(1, $dataprofile);
    }

    public function test_auto_config_update_user() {
        global $DB;

        $this->resetAfterTest();
        $this->getDataGenerator()->create_user(['username' => 'ally_webuser']);

        $field = $this->create_profile_field();
        $ac = new auto_config();
        $ac->configure();

        $this->assertDebuggingNotCalled();
        $dataprofile = $DB->get_records('user_info_data', array('fieldid' => $field->id));
        $this->assertCount(1, $dataprofile);
    }

    private function create_profile_field() {
        global $CFG, $DB;

        $datatype = 'text';
        require_once($CFG->dirroot.'/user/profile/definelib.php');
        require_once($CFG->dirroot.'/user/profile/field/'.$datatype.'/define.class.php');
        $newfield = 'profile_define_'.$datatype;
        $formfield = new $newfield();

        $data = [
            'datatype' => 'text',
            'shortname' => 'text_field',
            'name' => 'Text Field',
            'description' => 'Description text field',
            'required' => 1,
            'locked' => 0,
            'forceunique' => 0,
            'signup' => 0,
            'visible' => 2,
            'categoryid' => 1,
            'defaultdata' => '',
            'param1' => 30,
            'param2' => 2048,
            'param3' => 0,
            'descriptionformat' => 1
        ];
        $formfield->define_save((object)$data);

        return $DB->get_record('user_info_field', array('shortname' => $data['shortname']), '*', MUST_EXIST);
    }
}
