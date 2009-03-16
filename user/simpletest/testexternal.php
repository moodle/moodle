<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   user
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */

/**
 * Unit tests for (some of) user/external.php.
 * WARNING: DO NOT RUN THIS TEST ON A PRODUCTION SITE
 * => DO NOT UNCOMMENT THESE TEST FUNCTIONS EXCEPT IF YOU ARE DEVELOPER
 * => NONE OF THESE TEST FUNCTIONS SHOULD BE UNCOMMENT BY DEFAULT
 * => THESE TEST FUNCTIONS ARE DEPENDENT BETWEEEN EACH OTHER
 * => THE FUNCTION ORDER MUST NOT BE CHANGED
 *
 *
 * THIS TEST NEEDS TO BE RUN AS ADMIN!!!
 * @author Jerome Mouneyrac
 */

require_once($CFG->dirroot . '/user/external.php');

class user_external_test extends UnitTestCase {
    var $realDB;

    function setUp() {

    }

    function tearDown() {

    }
/*
    function test_create_users() {
        /// test that we create multiple users
        $params = array();
        for ($i=0;$i<2;$i=$i+1) {
            $user = array();
            $user['username'] = 'mockuserfortesting'.$i;
            $user['firstname'] = 'mockuserfortesting'.$i.'_firstname';
            $user['lastname'] = 'mockuserfortesting'.$i.'_lastname';
            $user['email'] = 'mockuserfortesting'.$i.'@moodle.com';
            $user['password'] = 'mockuserfortesting'.$i.'_password';
            $params[] = $user;
        }
        $result = user_external::create_users($params);
        $this->assertEqual(sizeof($result), 2);
        //just test that value are integer and not null
        $this->assertIsA($result[key($result)], "integer");
        $this->assertNotNull($result[key($result)]);

        /// test that we create one user with all optional fields
        $params = array();
        $user = array();
        $user['username'] = 'mockuserfortestingXX';
        $user['firstname'] = 'mockuserfortesting_firstname';
        $user['lastname'] = 'mockuserfortesting_lastname';
        $user['email'] = 'mockuserfortesting@moodle.com';
        $user['password'] = 'mockuserfortesting_password';
        $user['city'] = 'mockuserfortesting_city';
        $user['description'] = 'mockuserfortesting description';
        $user['country'] = 'AU';
        $user['lang']='en_utf8';
        $user['auth']='manual';
        $params[] = $user;
        $result = user_external::create_users($params);
        $this->assertEqual($result, true);


        /// test we cannot create a user with some missing mandatory field
        $params = array();
        $user = array();
        $user['username'] = 'mockuserfortestingY';
        $params[] = $user;
        $this->expectException(new moodle_exception('missingrequiredfield'));
        $result = user_external::create_users($params);

    }

    function test_create_users_2() {
        /// test we cannot create a user because the username already exist
        $params = array();
        $user = array();
        $user['username'] = 'mockuserfortestingXX';
        $user['firstname'] = 'mockuserfortestingX_firstname';
        $user['lastname'] = 'mockuserfortestingX_lastname';
        $user['email'] = 'mockuserfortestingX@moodle.com';
        $user['password'] = 'mockuserfortestingX_password';
        $params[] = $user;

        $this->expectException(new moodle_exception('wscouldnotcreateeuserindb'));
        $result = user_external::create_users($params);
    }

    function test_get_users() {
        $params = array('search' => 'mockuserfortestingXX');

        $users = user_external::get_users($params);
       
        foreach ($users as $user) {
            $this->assertEqual($user->username, 'mockuserfortestingXX');
            $this->assertEqual($user->firstname, 'mockuserfortesting_firstname');
            $this->assertEqual($user->lastname, 'mockuserfortesting_lastname');
            $this->assertEqual($user->email, 'mockuserfortesting@moodle.com');
            //  $this->assertEqual($user->password, 'mockuserfortesting_password');
            $this->assertEqual($user->city, 'mockuserfortesting_c');
            $this->assertEqual($user->description, 'mockuserfortesting description');
            $this->assertEqual($user->country, 'AU');
            $this->assertEqual($user->lang, 'en_utf8');
        }

    }

    function test_update_users() {
        /// update several users with full information
        $params = array();
        $user = array();
        $user['username'] = 'mockuserfortestingXX';
        $user['newusername'] = 'mockuserfortestingXY';
        $user['firstname'] = 'mockuserfortestingY_firstname';
        $user['lastname'] = 'mockuserfortestingY_lastname';
        $user['email'] = 'mockuserfortestingY@moodle.com';
        $user['password'] = 'mockuserfortestingY_password';
        $user['city'] = 'mockuserfortestingY_city';
        $user['description'] = 'mockuserfortestingY description';
        $user['country'] = 'AU';
        $user['lang']='en_utf8';
        $user['auth']='manual';
        $params[] = $user;
        $user = array();
        $user['username'] = 'mockuserfortesting0';
        $user['newusername'] = 'mockuserfortesting0Y';
        $user['firstname'] = 'mockuserfortesting0Y_firstname';
        $user['lastname'] = 'mockuserfortesting0Y_lastname';
        $user['email'] = 'mockuserfortesting0Y@moodle.com';
        $user['password'] = 'mockuserfortesting0Y_password';
        $user['city'] = 'mockuserfortesting0Y_city';
        $user['description'] = 'mockuserfortesting0Y description';
        $user['country'] = 'AU';
        $user['lang']='en_utf8';
        $user['auth']='manual';
        $params[] = $user;
        $result = user_external::update_users($params);
        $this->assertEqual($result, true);

        /// Exception: update non existing user
        $params = array();
        $user = array();
        $user['username'] = 'mockuserfortesting000';
        $user['newusername'] = 'mockuserfortesting0Y';
        $params[] = $user;
        $this->expectException(new moodle_exception('wscouldnotupdatenoexistinguser')); //TODO catch the write exception
        $result = user_external::update_users($params);
    }

    function test_update_users_2() {
        /// update an existing user with an already existing username
        $params = array();
        $user = array();
        $user['username'] = 'mockuserfortesting0Y';
        $user['newusername'] = 'mockuserfortestingXY';
        $params[] = $user;
 
        $this->expectException(new moodle_exception('wscouldnotupdateuserindb')); 
        $result = user_external::update_users($params);
    }

    function test_delete_users() {
        /// we delete all previously created users
        $params = array();
        $user = array();
        $user['username'] = 'mockuserfortestingXY';
        $params[] = $user;
        $user = array();
        $user['username'] = 'mockuserfortesting0Y';
        $params[] = $user;
        $user = array();
        $user['username'] = 'mockuserfortesting1';
        $params[] = $user;
        $result = user_external::delete_users($params);
        $this->assertEqual($result, true);

        /// try to delete them a new time, should return exception
        $this->expectException(new moodle_exception('wscouldnotdeletenoexistinguser')); 
        $result = user_external::delete_users($params);
    }
*/
}
?>