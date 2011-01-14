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
 * Unit tests for /lib/filelib.php.
 *
 * @package   file
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/filelib.php');

class filelib_test extends UnitTestCase {
    public function test_format_postdata_for_curlcall() {

        //POST params with just simple types
        $postdatatoconvert =array( 'userid' => 1, 'roleid' => 22, 'name' => 'john');
        $expectedresult = "userid=1&roleid=22&name=john";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEqual($postdata, $expectedresult);

        //POST params with a string containing & character
        $postdatatoconvert =array( 'name' => 'john&emilie', 'roleid' => 22);
        $expectedresult = "name=john%26emilie&roleid=22"; //urlencode: '%26' => '&'
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEqual($postdata, $expectedresult);

        //POST params with an empty value
        $postdatatoconvert =array( 'name' => null, 'roleid' => 22);
        $expectedresult = "name=&roleid=22"; 
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEqual($postdata, $expectedresult);

        //POST params with complex types
        $postdatatoconvert =array( 'users' => array(
                        array(
                                'id' => 2,
                                'customfields' => array(
                                        array
                                        (
                                                'type' => 'Color',
                                                'value' => 'violet'
                                        )
                                )
                        )
                )
        );
        $expectedresult = "users[0][id]=2&users[0][customfields][0][type]=Color&users[0][customfields][0][value]=violet";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEqual($postdata, $expectedresult);

        //POST params with other complex types
        $postdatatoconvert = array ('members' =>
            array(
            array('groupid' => 1, 'userid' => 1)
            , array('groupid' => 1, 'userid' => 2)
                )
            );
        $expectedresult = "members[0][groupid]=1&members[0][userid]=1&members[1][groupid]=1&members[1][userid]=2";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEqual($postdata, $expectedresult);
    }

    public function test_download_file_content() {
        $testhtml = "http://download.moodle.org/unittest/test.html";
        $contents = download_file_content($testhtml);
        $this->assertEqual('47250a973d1b88d9445f94db4ef2c97a', md5($contents));
    }
}
