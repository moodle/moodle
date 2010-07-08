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
 * Test the different web service protocols.
 *
 * @author jerome@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package web service
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * How to configure this unit tests:
 * 0- Enable the web service you wish to test in the Moodle administration
 * 1- Create a service with all functions in the Moodle administration
 * 2- Create a token associate this service and to an admin (so you get all capabilities)
 * 3- Configure setUp() function:
 *      a- write the token
 *      b- activate the protocols you wish to test
 *      c- activate the functions you wish to test (readonlytests and writetests arrays)
 *      d- set the number of time the web services are run
 * Do not run WRITE test function on a production site as they impact the DB (even though every
 * test should clean the modified data)
 *
 * How to write a new function:
 * 1- Add the function name to the array readonlytests/writetests
 * 2- Set it as false when you commit!
 * 3- write the function  - Do not prefix the function name by 'test'
 */
class webservice_test extends UnitTestCase {

    public $testtoken;
    public $testrest;
    public $testxmlrpc;
    public $testsoap;
    public $timerrest;
    public $timerxmlrpc;
    public $timersoap;
    public $readonlytests;
    public $writetests;

    function setUp() {
        //token to test
        $this->testtoken = '793e26aeddea7f0a696795d14dfccb0f';

        //protocols to test
        $this->testrest = false; //Does not work till XML => PHP is implemented (MDL-22965)
        $this->testxmlrpc = false;
        $this->testsoap = false;

        ////// DB READ-ONLY tests ////
        $this->readonlytests = array(
            'moodle_group_get_groups' => false
        );

        ////// DB WRITE tests ////
        $this->writetests = array(
            'moodle_user_create_users' => false
        );

        //performance testing: number of time the web service are run
        $this->iteration = 1;

        //DO NOT CHANGE
        //reset the timers
        $this->timerrest = 0;
        $this->timerxmlrpc = 0;
        $this->timersoap = 0;
    }

    function testRun() {
        global $CFG;

        if (!empty($this->testtoken)) {

            //Does not work till XML => PHP is implemented (MDL-22965)
            if ($this->testrest) {

                $this->timerrest = time();

                require_once($CFG->dirroot . "/webservice/rest/lib.php");
                $restclient = new webservice_rest_client($CFG->wwwroot
                                . '/webservice/rest/server.php', $this->testtoken);

                for ($i = 1; $i <= $this->iteration; $i = $i + 1) {
                    foreach ($this->readonlytests as $functioname => $run) {
                        if ($run) {
                            //$this->{$functioname}($restclient);
                        }
                    }
                    foreach ($this->writetests as $functioname => $run) {
                        if ($run) {
                            //$this->{$functioname}($restclient);
                        }
                    }
                }

                $this->timerrest = time() - $this->timerrest;
                //here you could call a log function to display the timer
                //example:
                //error_log('REST time: ');
                //error_log(print_r($this->timerrest));
            }

            if ($this->testxmlrpc) {

                $this->timerxmlrpc = time();

                require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");
                $xmlrpcclient = new webservice_xmlrpc_client($CFG->wwwroot
                                . '/webservice/xmlrpc/server.php', $this->testtoken);

                for ($i = 1; $i <= $this->iteration; $i = $i + 1) {
                    foreach ($this->readonlytests as $functioname => $run) {
                        if ($run) {
                            $this->{$functioname}($xmlrpcclient);
                        }
                    }
                    foreach ($this->writetests as $functioname => $run) {
                        if ($run) {
                            $this->{$functioname}($xmlrpcclient);
                        }
                    }
                }

                $this->timerxmlrpc = time() - $this->timerxmlrpc;
                //here you could call a log function to display the timer
                //example:
                //error_log('XML-RPC time: ');
                //error_log(print_r($this->timerxmlrpc));
            }

            if ($this->testsoap) {

                $this->timersoap = time();

                require_once($CFG->dirroot . "/webservice/soap/lib.php");
                $soapclient = new webservice_soap_client($CFG->wwwroot
                                . '/webservice/soap/server.php', $this->testtoken);
                $soapclient->setWsdlCache(false);

                for ($i = 1; $i <= $this->iteration; $i = $i + 1) {
                    foreach ($this->readonlytests as $functioname => $run) {
                        if ($run) {
                            $this->{$functioname}($soapclient);
                        }
                    }
                    foreach ($this->writetests as $functioname => $run) {
                        if ($run) {
                            $this->{$functioname}($soapclient);
                        }
                    }
                }

                $this->timersoap = time() - $this->timersoap;
                //here you could call a log function to display the timer
                //example:
                //error_log('SOAP time: ');
                //error_log(print_r($this->timersoap));
            }
        }
    }

    ///// WEB SERVICE TEST FUNCTIONS

    function moodle_group_get_groups($client) {
        global $DB;
        $dbgroups = $DB->get_records('groups');
        $groupids = array();
        foreach ($dbgroups as $dbgroup) {
            $groupids[] = $dbgroup->id;
        }
        $function = 'moodle_group_get_groups';

        $params = array('groupids' => $groupids);
        $groups = $client->call($function, $params);
        $this->assertEqual(count($groups), count($groupids));
    }

    function moodle_user_create_users($client) {
        global $DB;
        //do not run the test if users already exists
        $users = $DB->get_records_list('user', 'username',
                        array('testusername1', 'testusername2'));
        if (!empty($users)) {
            throw new moodle_exception('testuseralreadyexist');
        } else {
            //a full user
            $user1 = new stdClass();
            $user1->username = 'testusername1';
            $user1->password = 'testpassword1';
            $user1->firstname = 'testfirstname1';
            $user1->lastname = 'testlastname1';
            $user1->email = 'testemail1@moodle.com';
            $user1->auth = 'manual';
            $user1->idnumber = 'testidnumber1';
            $user1->emailstop = 1;
            $user1->lang = 'en';
            $user1->theme = 'standard';
            $user1->timezone = 99;
            $user1->mailformat = 0;
            $user1->description = 'Hello World!';
            $user1->city = 'testcity1';
            $user1->country = 'au';
            $user1->preferences = array(
                array('type' => 'preference1', 'value' => 'value1'),
                array('type' => 'preference2', 'value' => 'value2'));
            $user1->customfields = array(
                array('type' => 'type', 'value' => 'value'),
                array('type' => 'type2', 'value' => 'value2'));

            //a minimum user
            $user2 = new stdClass();
            $user2->username = 'testusername2';
            $user2->password = 'testpassword2';
            $user2->firstname = 'testfirstname2';
            $user2->lastname = 'testlastname2';
            $user2->email = 'testemail1@moodle.com';

            $users = array($user1, $user2);

            $function = 'moodle_user_create_users';
            $params = array('users' => $users);
            $resultusers = $client->call($function, $params);
            $this->assertEqual(count($users), count($resultusers));

            //retrieve users from the DB and check values
            $dbuser1 = $DB->get_record('user', array('username' => 'testusername1'));
            $this->assertEqual($dbuser1->firstname, $user1->firstname);
            $this->assertEqual($dbuser1->password,
                    hash_internal_user_password($user1->password));
            $this->assertEqual($dbuser1->lastname, $user1->lastname);
            $this->assertEqual($dbuser1->email, $user1->email);
            $this->assertEqual($dbuser1->auth, $user1->auth);
            $this->assertEqual($dbuser1->idnumber, $user1->idnumber);
            $this->assertEqual($dbuser1->emailstop, $user1->emailstop);
            $this->assertEqual($dbuser1->lang, $user1->lang);
            $this->assertEqual($dbuser1->theme, $user1->theme);
            $this->assertEqual($dbuser1->timezone, $user1->timezone);
            $this->assertEqual($dbuser1->mailformat, $user1->mailformat);
            $this->assertEqual($dbuser1->description, $user1->description);
            $this->assertEqual($dbuser1->city, $user1->city);
            $this->assertEqual($dbuser1->country, $user1->country);
            $user1preference1 = get_user_preferences('preference1', null, $dbuser1->id);
            $this->assertEqual('value1', $user1preference1);
            $user1preference2 = get_user_preferences('preference2', null, $dbuser1->id);
            $this->assertEqual('value2', $user1preference2);

            //retrieve users from the DB and check values
            $dbuser2 = $DB->get_record('user', array('username' => 'testusername2'));
            $this->assertEqual($dbuser2->firstname, $user2->firstname);
            $this->assertEqual($dbuser2->password,
                    hash_internal_user_password($user2->password));
            $this->assertEqual($dbuser2->lastname, $user2->lastname);
            $this->assertEqual($dbuser2->email, $user2->email);

            //delete users from DB
            $DB->delete_records_list('user', 'id',
                    array($dbuser1->id, $dbuser2->id));
        }
    }

}
