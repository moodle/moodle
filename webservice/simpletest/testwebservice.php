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
    ///  It must be included from a Moodle page
    die('Direct access to this script is forbidden.');
}

/**
 * How to configure this unit tests:
 * 0- Enable the web service you wish to test in the Moodle administration
 * 1- Create a service with all functions in the Moodle administration
 * 2- Create a token associate this service and to an admin (or a user with all required capabilities)
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
        $this->testtoken = 'acabec9d20933913f14309785324f579';

        //protocols to test
        $this->testrest = false; //Does not work till XML => PHP is implemented (MDL-22965)
        $this->testxmlrpc = false;
        $this->testsoap = false;

        ////// READ-ONLY DB tests ////
        $this->readonlytests = array(
            'moodle_group_get_groups' => false,
            'moodle_course_get_courses' => false,
            'moodle_user_get_users_by_id' => false,
            'moodle_enrol_get_enrolled_users' => false,
            'moodle_group_get_course_groups' => false,
            'moodle_group_get_groupmembers' => false
        );

        ////// WRITE DB tests ////
        $this->writetests = array(
            'moodle_user_create_users' => false,
            'moodle_course_create_courses' => false,
            'moodle_user_delete_users' => false,
            'moodle_user_update_users' => false,
            'moodle_role_assign' => false,
            'moodle_role_unassign' => false,
            'moodle_group_add_groupmembers' => false,
            'moodle_group_delete_groupmembers' => false,
            'moodle_group_create_groups' => false,
            'moodle_group_delete_groups' => false,
            'moodle_enrol_manual_enrol_users' => false
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

        if (!$this->testrest and !$this->testxmlrpc and !$this->testsoap) {
            print_r("Web service unit tests are not run as not setup.
                (see /webservice/simpletest/testwebservice.php)");
        }

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
                                . '/webservice/soap/server.php', $this->testtoken,
                        array("features" => SOAP_WAIT_ONE_WAY_CALLS)); //force SOAP synchronous mode
                                                                     //when function return null
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

    function moodle_user_get_users_by_id($client) {
        global $DB;
        $dbusers = $DB->get_records('user', array('deleted' => 0));
        $userids = array();
        foreach ($dbusers as $dbuser) {
            $userids[] = $dbuser->id;
        }
        $function = 'moodle_user_get_users_by_id';

        $params = array('userids' => $userids);
        $users = $client->call($function, $params);

        $this->assertEqual(count($users), count($userids));
    }

    /**
     * This test will:
     * 1- create a user (core call)
     * 2- enrol this user in the courses supporting enrolment
     * 3- unenrol this user (core call)
     */
    function moodle_enrol_manual_enrol_users($client) {
        global $DB, $CFG;

        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->dirroot . "/user/profile/lib.php");
        require_once($CFG->dirroot . "/lib/enrollib.php");

        //Delete some previous test data
        if ($user = $DB->get_record('user', array('username' => 'veryimprobabletestusername2'))) {
            $DB->delete_records('user', array('id' => $user->id));
        }
        if ($role = $DB->get_record('role', array('shortname' => 'role1thatshouldnotexist'))) {
            set_role_contextlevels($role->id, array(CONTEXT_COURSE));
            delete_role($role->id);
        }

        //create a user
        $user = new stdClass();
        $user->username = 'veryimprobabletestusername2';
        $user->password = 'testpassword2';
        $user->firstname = 'testfirstname2';
        $user->lastname = 'testlastname2';
        $user->email = 'testemail1@moodle.com';
        $user->id = user_create_user($user);

        $roleid = create_role('role1thatshouldnotexist', 'role1thatshouldnotexist', '');
        set_role_contextlevels($roleid, array(CONTEXT_COURSE));

        $enrolments = array();
        $courses = $DB->get_records('course');

        foreach ($courses as $course) {
            if ($course->id > 1) {
                $enrolments[] = array('roleid' => $roleid,
                    'userid' => $user->id, 'courseid' => $course->id);
                $enrolledcourses[] = $course;
            }
        }

        //web service call
        $function = 'moodle_enrol_manual_enrol_users';
        $wsparams = array('enrolments' => $enrolments);
        $enrolmentsresult = $client->call($function, $wsparams);

        //get instance that can unenrol
        $enrols = enrol_get_plugins(true);
        $enrolinstances = enrol_get_instances($course->id, true);
        $unenrolled = false;
        foreach ($enrolinstances as $instance) {
            if (!$unenrolled and $enrols[$instance->enrol]->allow_unenrol($instance)) {
                $unenrolinstance = $instance;
                $unenrolled = true;
            }
        }

        //test and unenrol the user
        $enrolledusercourses = enrol_get_users_courses($user->id);
        foreach ($enrolledcourses as $course) {
            //test
            $this->assertEqual(true, isset($enrolledusercourses[$course->id]));

            //unenrol the user
            $enrols[$unenrolinstance->enrol]->unenrol_user($unenrolinstance, $user->id, $roleid);
        }

        //delete user
        $DB->delete_records('user', array('id' => $user->id));

        //delete the context level
        set_role_contextlevels($roleid, array(CONTEXT_COURSE));

        //delete role
        delete_role($roleid);
    }


    function moodle_enrol_get_enrolled_users($client) {
        global $DB;

        //function settings
        $withcapability = '';
        $groupid = null;
        $onlyactive = false;

        $dbcourses = $DB->get_records('course');
        $function = 'moodle_enrol_get_enrolled_users';

        foreach ($dbcourses as $dbcourse) {

            $params = array();

            $coursecontext = get_context_instance(CONTEXT_COURSE, $dbcourse->id);

            list($sql, $params) = get_enrolled_sql($coursecontext, $withcapability, $groupid, $onlyactive);
            $sql = "SELECT DISTINCT ue.userid, e.courseid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid)
                     WHERE e.courseid = :courseid AND ue.userid IN ($sql)";

            $params['courseid'] = $dbcourse->id;

            $enrolledusers = $DB->get_records_sql($sql, $params);

            $wsparams = array('courseid' => $dbcourse->id, 'withcapability' => $withcapability,
                'groupid' => $groupid, 'onlyactive' => $onlyactive);
            $resultusers = $client->call($function, $wsparams);

            $this->assertEqual(count($resultusers), count($enrolledusers));
        }
    }

    function moodle_course_get_courses($client) {
        global $DB;

        $function = 'moodle_course_get_courses';

        //retrieve all courses from db
        $dbcourses = $DB->get_records('course');
        $courseids = array();
        foreach ($dbcourses as $dbcourse) {
            $courseids[] = $dbcourse->id;
        }

        //retrieve all courses by id
        $params = array('options' => array('ids' => $courseids));
        $courses = $client->call($function, $params);

        //check it is the same course count
        $this->assertEqual(count($courses), count($courseids));

        //check all course values are identic
        foreach ($courses as $course) {
            $this->assertEqual($course['fullname'],
                    $dbcourses[$course['id']]->fullname);
            $this->assertEqual($course['shortname'],
                    $dbcourses[$course['id']]->shortname);
            $this->assertEqual($course['categoryid'],
                    $dbcourses[$course['id']]->category);
            $this->assertEqual($course['categorysortorder'],
                    $dbcourses[$course['id']]->sortorder);
            $this->assertEqual($course['idnumber'],
                    $dbcourses[$course['id']]->idnumber);
            $this->assertEqual($course['summary'],
                    $dbcourses[$course['id']]->summary);
            $this->assertEqual($course['summaryformat'],
                    $dbcourses[$course['id']]->summaryformat);
            $this->assertEqual($course['format'],
                    $dbcourses[$course['id']]->format);
            $this->assertEqual($course['showgrades'],
                    $dbcourses[$course['id']]->showgrades);
            $this->assertEqual($course['newsitems'],
                    $dbcourses[$course['id']]->newsitems);
            $this->assertEqual($course['startdate'],
                    $dbcourses[$course['id']]->startdate);
            $this->assertEqual($course['numsections'],
                    $dbcourses[$course['id']]->numsections);
            $this->assertEqual($course['maxbytes'],
                    $dbcourses[$course['id']]->maxbytes);
            $this->assertEqual($course['visible'],
                    $dbcourses[$course['id']]->visible);
            $this->assertEqual($course['hiddensections'],
                    $dbcourses[$course['id']]->hiddensections);
            $this->assertEqual($course['groupmode'],
                    $dbcourses[$course['id']]->groupmode);
            $this->assertEqual($course['groupmodeforce'],
                    $dbcourses[$course['id']]->groupmodeforce);
            $this->assertEqual($course['defaultgroupingid'],
                    $dbcourses[$course['id']]->defaultgroupingid);
            $this->assertEqual($course['lang'],
                    $dbcourses[$course['id']]->lang);
            $this->assertEqual($course['timecreated'],
                    $dbcourses[$course['id']]->timecreated);
            $this->assertEqual($course['timemodified'],
                    $dbcourses[$course['id']]->timemodified);
            if (key_exists('enablecompletion', $course)) {
                $this->assertEqual($course['enablecompletion'],
                        $dbcourses[$course['id']]->enablecompletion);
            }
            if (key_exists('completionstartonenrol', $course)) {
                $this->assertEqual($course['completionstartonenrol'],
                        $dbcourses[$course['id']]->completionstartonenrol);
            }
            if (key_exists('completionnotify', $course)) {
                $this->assertEqual($course['completionnotify'],
                        $dbcourses[$course['id']]->completionnotify);
            }
            $this->assertEqual($course['forcetheme'],
                    $dbcourses[$course['id']]->theme);
        }
    }

    function moodle_course_create_courses($client) {
        global $DB, $CFG;

        ///Test data
        $courseconfig = get_config('moodlecourse');

        $themeobjects = get_list_of_themes();
        $theme = key($themeobjects);
        $categoryid = $DB->get_record('course_categories', array(), '*', IGNORE_MULTIPLE)->id;
        $categoryid = empty($categoryid) ? 0 : $categoryid;

        $course1 = new stdClass();
        $course1->fullname = 'Test Data create course 1';
        $course1->shortname = 'testdatacourse1';
        $course1->categoryid = $categoryid;
        $course1->idnumber = '328327982372342343234';
        $course1->summary = 'This is a summary';
        $course1->summaryformat = FORMAT_HTML;
        $course1->format = $courseconfig->format;
        $course1->showgrades = $courseconfig->showgrades;
        $course1->showreports = $courseconfig->showreports;
        $course1->newsitems = $courseconfig->newsitems;
        $course1->startdate = time();
        $course1->numsections = $courseconfig->numsections;
        $course1->maxbytes = $courseconfig->maxbytes;
        $course1->visible = $courseconfig->visible;
        $course1->hiddensections = $courseconfig->hiddensections;
        $course1->groupmode = $courseconfig->groupmode;
        $course1->groupmodeforce = $courseconfig->groupmodeforce;
        $course1->defaultgroupingid = 0;
        if (!empty($courseconfig->lang)) {
            $course1->lang = $courseconfig->lang;
        }
        $course1->enablecompletion = $courseconfig->enablecompletion;
        $course1->completionstartonenrol = $courseconfig->completionstartonenrol;
        $course1->completionnotify = 0;
        $course1->forcetheme = $theme;

        $course2 = new stdClass();
        $course2->fullname = 'Test Data create course 2';
        $course2->shortname = 'testdatacourse2';
        $course2->categoryid = $categoryid;

        $courses = array($course1, $course2);

        //do not run the test if course1 or course2 already exists
        $existingcourses = $DB->get_records_list('course', 'fullname',
                        array($course1->fullname, $course2->fullname));
        if (!empty($existingcourses)) {
            throw new moodle_exception('testdatacoursesalreadyexist');
        }

        $function = 'moodle_course_create_courses';
        $params = array('courses' => $courses);
        $resultcourses = $client->call($function, $params);
        $this->assertEqual(count($courses), count($resultcourses));

        //retrieve user1 from the DB and check values
        $dbcourse1 = $DB->get_record('course', array('fullname' => $course1->fullname));
        $this->assertEqual($dbcourse1->fullname, $course1->fullname);
        $this->assertEqual($dbcourse1->shortname, $course1->shortname);
        $this->assertEqual($dbcourse1->category, $course1->categoryid);
        $this->assertEqual($dbcourse1->idnumber, $course1->idnumber);
        $this->assertEqual($dbcourse1->summary, $course1->summary);
        $this->assertEqual($dbcourse1->summaryformat, $course1->summaryformat);
        $this->assertEqual($dbcourse1->format, $course1->format);
        $this->assertEqual($dbcourse1->showgrades, $course1->showgrades);
        $this->assertEqual($dbcourse1->showreports, $course1->showreports);
        $this->assertEqual($dbcourse1->newsitems, $course1->newsitems);
        $this->assertEqual($dbcourse1->startdate, $course1->startdate);
        $this->assertEqual($dbcourse1->numsections, $course1->numsections);
        $this->assertEqual($dbcourse1->maxbytes, $course1->maxbytes);
        $this->assertEqual($dbcourse1->visible, $course1->visible);
        $this->assertEqual($dbcourse1->hiddensections, $course1->hiddensections);
        $this->assertEqual($dbcourse1->groupmode, $course1->groupmode);
        $this->assertEqual($dbcourse1->groupmodeforce, $course1->groupmodeforce);
        $this->assertEqual($dbcourse1->defaultgroupingid, $course1->defaultgroupingid);
        if (!empty($courseconfig->lang)) {
            $this->assertEqual($dbcourse1->lang, $course1->lang);
        }
        if (completion_info::is_enabled_for_site()) {
            $this->assertEqual($dbcourse1->enablecompletion, $course1->enablecompletion);
            $this->assertEqual($dbcourse1->completionstartonenrol, $course1->completionstartonenrol);
        }
        $this->assertEqual($dbcourse1->completionnotify, $course1->completionnotify);
        if (!empty($CFG->allowcoursethemes)) {
            $this->assertEqual($dbcourse1->theme, $course1->forcetheme);
        }

        //retrieve user2 from the DB and check values
        $dbcourse2 = $DB->get_record('course', array('fullname' => $course2->fullname));
        $this->assertEqual($dbcourse2->fullname, $course2->fullname);
        $this->assertEqual($dbcourse2->shortname, $course2->shortname);
        $this->assertEqual($dbcourse2->category, $course2->categoryid);
        $this->assertEqual($dbcourse2->summaryformat, FORMAT_MOODLE);
        $this->assertEqual($dbcourse2->format, $courseconfig->format);
        $this->assertEqual($dbcourse2->showgrades, $courseconfig->showgrades);
        $this->assertEqual($dbcourse2->showreports, $courseconfig->showreports);
        $this->assertEqual($dbcourse2->newsitems, $courseconfig->newsitems);
        $this->assertEqual($dbcourse2->numsections, $courseconfig->numsections);
        $this->assertEqual($dbcourse2->maxbytes, $courseconfig->maxbytes);
        $this->assertEqual($dbcourse2->visible, $courseconfig->visible);
        $this->assertEqual($dbcourse2->hiddensections, $courseconfig->hiddensections);
        $this->assertEqual($dbcourse2->groupmode, $courseconfig->groupmode);
        $this->assertEqual($dbcourse2->groupmodeforce, $courseconfig->groupmodeforce);
        $this->assertEqual($dbcourse2->defaultgroupingid, 0);

        //delete users from DB
        $DB->delete_records_list('course', 'id',
                array($dbcourse1->id, $dbcourse2->id));
    }

    function moodle_user_create_users($client) {
        global $DB, $CFG;

        //Test data
        //a full user: user1
        $user1 = new stdClass();
        $user1->username = 'testusername1';
        $user1->password = 'testpassword1';
        $user1->firstname = 'testfirstname1';
        $user1->lastname = 'testlastname1';
        $user1->email = 'testemail1@moodle.com';
        $user1->auth = 'manual';
        $user1->idnumber = 'testidnumber1';
        $user1->lang = 'en';
        $user1->theme = 'standard';
        $user1->timezone = 99;
        $user1->mailformat = 0;
        $user1->description = 'Hello World!';
        $user1->city = 'testcity1';
        $user1->country = 'au';
        $preferencename1 = 'preference1';
        $preferencename2 = 'preference2';
        $user1->preferences = array(
            array('type' => $preferencename1, 'value' => 'preferencevalue1'),
            array('type' => $preferencename2, 'value' => 'preferencevalue2'));
        $customfieldname1 = 'testdatacustom1';
        $customfieldname2 = 'testdatacustom2';
        $user1->customfields = array(
            array('type' => $customfieldname1, 'value' => 'customvalue'),
            array('type' => $customfieldname2, 'value' => 'customvalue2'));
        //a small user: user2
        $user2 = new stdClass();
        $user2->username = 'testusername2';
        $user2->password = 'testpassword2';
        $user2->firstname = 'testfirstname2';
        $user2->lastname = 'testlastname2';
        $user2->email = 'testemail1@moodle.com';

        $users = array($user1, $user2);

        //do not run the test if user1 or user2 already exists
        $existingusers = $DB->get_records_list('user', 'username',
                        array($user1->username, $user2->username));
        if (!empty($existingusers)) {
            throw new moodle_exception('testdatausersalreadyexist');
        }

        //do not run the test if data test custom fields already exists
        $existingcustomfields = $DB->get_records_list('user_info_field', 'shortname',
                        array($customfieldname1, $customfieldname2));
        if (!empty($existingcustomfields)) {
            throw new moodle_exception('testdatacustomfieldsalreadyexist');
        }

        //create the custom fields
        $customfield = new stdClass();
        $customfield->shortname = $customfieldname1;
        $customfield->name = $customfieldname1;
        $customfield->datatype = 'text';
        $DB->insert_record('user_info_field', $customfield);
        $customfield = new stdClass();
        $customfield->shortname = $customfieldname2;
        $customfield->name = $customfieldname2;
        $customfield->datatype = 'text';
        $DB->insert_record('user_info_field', $customfield);

        $function = 'moodle_user_create_users';
        $params = array('users' => $users);
        $resultusers = $client->call($function, $params);
        $this->assertEqual(count($users), count($resultusers));

        //retrieve user1 from the DB and check values
        $dbuser1 = $DB->get_record('user', array('username' => $user1->username));
        $this->assertEqual($dbuser1->firstname, $user1->firstname);
        $this->assertEqual($dbuser1->password,
                hash_internal_user_password($user1->password));
        $this->assertEqual($dbuser1->lastname, $user1->lastname);
        $this->assertEqual($dbuser1->email, $user1->email);
        $this->assertEqual($dbuser1->auth, $user1->auth);
        $this->assertEqual($dbuser1->idnumber, $user1->idnumber);
        $this->assertEqual($dbuser1->lang, $user1->lang);
        $this->assertEqual($dbuser1->theme, $user1->theme);
        $this->assertEqual($dbuser1->timezone, $user1->timezone);
        $this->assertEqual($dbuser1->mailformat, $user1->mailformat);
        $this->assertEqual($dbuser1->description, $user1->description);
        $this->assertEqual($dbuser1->city, $user1->city);
        $this->assertEqual($dbuser1->country, $user1->country);
        $user1preference1 = get_user_preferences($user1->preferences[0]['type'],
                        null, $dbuser1->id);
        $this->assertEqual($user1->preferences[0]['value'], $user1preference1);
        $user1preference2 = get_user_preferences($user1->preferences[1]['type'],
                        null, $dbuser1->id);
        $this->assertEqual($user1->preferences[1]['value'], $user1preference2);
        require_once($CFG->dirroot . "/user/profile/lib.php");
        $customfields = profile_user_record($dbuser1->id);

        $customfields = (array) $customfields;
        $customfieldname1 = $user1->customfields[0]['type'];
        $customfieldname2 = $user1->customfields[1]['type'];
        $this->assertEqual($customfields[$customfieldname1],
                $user1->customfields[0]['value']);
        $this->assertEqual($customfields[$customfieldname2],
                $user1->customfields[1]['value']);


        //retrieve user2 from the DB and check values
        $dbuser2 = $DB->get_record('user', array('username' => $user2->username));
        $this->assertEqual($dbuser2->firstname, $user2->firstname);
        $this->assertEqual($dbuser2->password,
                hash_internal_user_password($user2->password));
        $this->assertEqual($dbuser2->lastname, $user2->lastname);
        $this->assertEqual($dbuser2->email, $user2->email);

        //unset preferences
        $DB->delete_records('user_preferences', array('userid' => $dbuser1->id));

        //clear custom fields data
        $DB->delete_records('user_info_data', array('userid' => $dbuser1->id));

        //delete custom fields
        $DB->delete_records_list('user_info_field', 'shortname',
                array($customfieldname1, $customfieldname2));

        //delete users from DB
        $DB->delete_records_list('user', 'id',
                array($dbuser1->id, $dbuser2->id));
    }

    function moodle_user_delete_users($client) {
        global $DB, $CFG;

        //Set test data
        //a full user: user1
        $user1 = new stdClass();
        $user1->username = 'veryimprobabletestusername1';
        $user1->password = 'testpassword1';
        $user1->firstname = 'testfirstname1';
        $user1->lastname = 'testlastname1';
        $user1->email = 'testemail1@moodle.com';
        $user1->auth = 'manual';
        $user1->idnumber = 'testidnumber1';
        $user1->lang = 'en';
        $user1->theme = 'standard';
        $user1->timezone = 99;
        $user1->mailformat = 0;
        $user1->description = 'Hello World!';
        $user1->city = 'testcity1';
        $user1->country = 'au';
        $preferencename1 = 'preference1';
        $preferencename2 = 'preference2';
        $user1->preferences = array(
            array('type' => $preferencename1, 'value' => 'preferencevalue1'),
            array('type' => $preferencename2, 'value' => 'preferencevalue2'));
        $customfieldname1 = 'testdatacustom1';
        $customfieldname2 = 'testdatacustom2';
        $user1->customfields = array(
            array('type' => $customfieldname1, 'value' => 'customvalue'),
            array('type' => $customfieldname2, 'value' => 'customvalue2'));
        //a small user: user2
        $user2 = new stdClass();
        $user2->username = 'veryimprobabletestusername2';
        $user2->password = 'testpassword2';
        $user2->firstname = 'testfirstname2';
        $user2->lastname = 'testlastname2';
        $user2->email = 'testemail1@moodle.com';
        $users = array($user1, $user2);

        //can run this test only if test usernames don't exist
        $searchusers = $DB->get_records_list('user', 'username',
                array($user1->username, $user1->username));
        if (count($searchusers) == 0) {
            //create two users
            require_once($CFG->dirroot."/user/lib.php");
            require_once($CFG->dirroot."/user/profile/lib.php");
            $user1->id = user_create_user($user1);
            // custom fields
            if(!empty($user1->customfields)) {
                foreach($user1->customfields as $customfield) {
                    $user1->{"profile_field_".$customfield['type']} = $customfield['value'];
                }
                profile_save_data((object) $user1);
            }
            //preferences
            if (!empty($user1->preferences)) {
                foreach($user1->preferences as $preference) {
                    set_user_preference($preference['type'], $preference['value'],$user1->id);
                }
            }
            $user2->id = user_create_user($user2);

            //create the custom fields
            $customfield = new stdClass();
            $customfield->shortname = $customfieldname1;
            $customfield->name = $customfieldname1;
            $customfield->datatype = 'text';
            $DB->insert_record('user_info_field', $customfield);
            $customfield = new stdClass();
            $customfield->shortname = $customfieldname2;
            $customfield->name = $customfieldname2;
            $customfield->datatype = 'text';
            $DB->insert_record('user_info_field', $customfield);

            //search for them => TEST they exists
            $searchusers = $DB->get_records_list('user', 'username',
                    array($user1->username, $user2->username));
            $this->assertEqual(count($users), count($searchusers));

            //delete the users by webservice
            $function = 'moodle_user_delete_users';
            $params = array('users' => array($user1->id, $user2->id));
            $client->call($function, $params);

            //search for them => TESTS they don't exists
            $searchusers = $DB->get_records_list('user', 'username',
                    array($user1->username, $user2->username));
           
            $this->assertTrue(empty($searchusers));

            //unset preferences
            $DB->delete_records('user_preferences', array('userid' => $user1->id));

            //clear custom fields data
            $DB->delete_records('user_info_data', array('userid' => $user1->id));

            //delete custom fields
            $DB->delete_records_list('user_info_field', 'shortname',
                    array($customfieldname1, $customfieldname2));

            //delete users from DB
            $DB->delete_records_list('user', 'id',
                    array($user1->id, $user2->id));
        }
    }

    function moodle_user_update_users($client) {
        global $DB, $CFG;

        //Set test data
        //a full user: user1
        $user1 = new stdClass();
        $user1->username = 'veryimprobabletestusername1';
        $user1->password = 'testpassword1';
        $user1->firstname = 'testfirstname1';
        $user1->lastname = 'testlastname1';
        $user1->email = 'testemail1@moodle.com';
        $user1->auth = 'manual';
        $user1->idnumber = 'testidnumber1';
        $user1->lang = 'en';
        $user1->theme = 'standard';
        $user1->timezone = 99;
        $user1->mailformat = 0;
        $user1->description = 'Hello World!';
        $user1->city = 'testcity1';
        $user1->country = 'au';
        $preferencename1 = 'preference1';
        $preferencename2 = 'preference2';
        $user1->preferences = array(
            array('type' => $preferencename1, 'value' => 'preferencevalue1'),
            array('type' => $preferencename2, 'value' => 'preferencevalue2'));
        $customfieldname1 = 'testdatacustom1';
        $customfieldname2 = 'testdatacustom2';
        $user1->customfields = array(
            array('type' => $customfieldname1, 'value' => 'customvalue'),
            array('type' => $customfieldname2, 'value' => 'customvalue2'));
        //a small user: user2
        $user2 = new stdClass();
        $user2->username = 'veryimprobabletestusername2';
        $user2->password = 'testpassword2';
        $user2->firstname = 'testfirstname2';
        $user2->lastname = 'testlastname2';
        $user2->email = 'testemail1@moodle.com';
        $users = array($user1, $user2);

        //can run this test only if test usernames don't exist
        $searchusers = $DB->get_records_list('user', 'username',
                array($user1->username, $user1->username));
        if (count($searchusers) == 0) {
            //create two users
            require_once($CFG->dirroot."/user/lib.php");
            require_once($CFG->dirroot."/user/profile/lib.php");
            $user1->id = user_create_user($user1);
            //unset field created by user_create_user
            unset($user1->timemodified);
            unset($user1->timecreated);

            // custom fields
            if(!empty($user1->customfields)) {
                foreach($user1->customfields as $customfield) {
                    $customuser1->id = $user1->id;
                    $customuser1->{"profile_field_".$customfield['type']} = $customfield['value'];
                }
                profile_save_data((object) $customuser1);
            }
            //preferences
            if (!empty($user1->preferences)) {
                foreach($user1->preferences as $preference) {
                    set_user_preference($preference['type'], $preference['value'],$user1->id);
                }
            }
            $user2->id = user_create_user($user2);
            unset($user2->timemodified);
            unset($user2->timecreated);

             //create the custom fields
            $customfield = new stdClass();
            $customfield->shortname = $customfieldname1;
            $customfield->name = $customfieldname1;
            $customfield->datatype = 'text';
            $DB->insert_record('user_info_field', $customfield);
            $customfield = new stdClass();
            $customfield->shortname = $customfieldname2;
            $customfield->name = $customfieldname2;
            $customfield->datatype = 'text';
            $DB->insert_record('user_info_field', $customfield);
            
            //search for them => TEST they exists
            $searchusers = $DB->get_records_list('user', 'username',
                    array($user1->username, $user2->username));
            $this->assertEqual(count($users), count($searchusers));

            //update the test data
            $user1->username = 'veryimprobabletestusername1_updated';
            $user1->password = 'testpassword1_updated';
            $user1->firstname = 'testfirstname1_updated';
            $user1->lastname = 'testlastname1_updated';
            $user1->email = 'testemail1_updated@moodle.com';
            $user1->auth = 'manual';
            $user1->idnumber = 'testidnumber1_updated';
            $user1->lang = 'en';
            $user1->theme = 'standard';
            $user1->timezone = 98;
            $user1->mailformat = 1;
            $user1->description = 'Hello World!_updated';
            $user1->city = 'testcity1_updated';
            $user1->country = 'au';
            $preferencename1 = 'preference1';
            $preferencename2 = 'preference2';
            $user1->preferences = array(
            array('type' => $preferencename1, 'value' => 'preferencevalue1_updated'),
            array('type' => $preferencename2, 'value' => 'preferencevalue2_updated'));
            $customfieldname1 = 'testdatacustom1';
            $customfieldname2 = 'testdatacustom2';
            $user1->customfields = array(
            array('type' => $customfieldname1, 'value' => 'customvalue_updated'),
            array('type' => $customfieldname2, 'value' => 'customvalue2_updated'));
            $user2->username = 'veryimprobabletestusername2_updated';
            $user2->password = 'testpassword2_updated';
            $user2->firstname = 'testfirstname2_updated';
            $user2->lastname = 'testlastname2_updated';
            $user2->email = 'testemail1_updated@moodle.com';
            $users = array($user1, $user2);
            
            //update the users by web service
            $function = 'moodle_user_update_users';
            $params = array('users' => $users);
            $client->call($function, $params);

            //compare DB user with the test data
            $dbuser1 = $DB->get_record('user', array('username' => $user1->username));
            $this->assertEqual($dbuser1->firstname, $user1->firstname);
            $this->assertEqual($dbuser1->password,
                    hash_internal_user_password($user1->password));
            $this->assertEqual($dbuser1->lastname, $user1->lastname);
            $this->assertEqual($dbuser1->email, $user1->email);
            $this->assertEqual($dbuser1->auth, $user1->auth);
            $this->assertEqual($dbuser1->idnumber, $user1->idnumber);
            $this->assertEqual($dbuser1->lang, $user1->lang);
            $this->assertEqual($dbuser1->theme, $user1->theme);
            $this->assertEqual($dbuser1->timezone, $user1->timezone);
            $this->assertEqual($dbuser1->mailformat, $user1->mailformat);
            $this->assertEqual($dbuser1->description, $user1->description);
            $this->assertEqual($dbuser1->city, $user1->city);
            $this->assertEqual($dbuser1->country, $user1->country);
            $user1preference1 = get_user_preferences($user1->preferences[0]['type'],
                            null, $dbuser1->id);
            $this->assertEqual($user1->preferences[0]['value'], $user1preference1);
            $user1preference2 = get_user_preferences($user1->preferences[1]['type'],
                            null, $dbuser1->id);
            $this->assertEqual($user1->preferences[1]['value'], $user1preference2);
            require_once($CFG->dirroot . "/user/profile/lib.php");
            $customfields = profile_user_record($dbuser1->id);

            $customfields = (array) $customfields;
            $customfieldname1 = $user1->customfields[0]['type'];
            $customfieldname2 = $user1->customfields[1]['type'];
            $this->assertEqual($customfields[$customfieldname1],
                    $user1->customfields[0]['value']);
            $this->assertEqual($customfields[$customfieldname2],
                    $user1->customfields[1]['value']);

            $dbuser2 = $DB->get_record('user', array('username' => $user2->username));
            $this->assertEqual($dbuser2->firstname, $user2->firstname);
            $this->assertEqual($dbuser2->password,
                    hash_internal_user_password($user2->password));
            $this->assertEqual($dbuser2->lastname, $user2->lastname);
            $this->assertEqual($dbuser2->email, $user2->email);

            //unset preferences
            $DB->delete_records('user_preferences', array('userid' => $dbuser1->id));

            //clear custom fields data
            $DB->delete_records('user_info_data', array('userid' => $dbuser1->id));

            //delete custom fields
            $DB->delete_records_list('user_info_field', 'shortname',
                    array($customfieldname1, $customfieldname2));

            //delete users from DB
            $DB->delete_records_list('user', 'id',
                    array($dbuser1->id, $dbuser2->id));

        }
    }

    function moodle_role_assign($client) {
        global $DB, $CFG;

        $searchusers = $DB->get_records_list('user', 'username',
                array('veryimprobabletestusername2'));
        $searchroles = $DB->get_records_list('role', 'shortname',
                array('role1thatshouldnotexist', 'role2thatshouldnotexist'));

        if (empty($searchusers) and empty($searchroles)) {

            //create a temp user
            $user = new stdClass();
            $user->username = 'veryimprobabletestusername2';
            $user->password = 'testpassword2';
            $user->firstname = 'testfirstname2';
            $user->lastname = 'testlastname2';
            $user->email = 'testemail1@moodle.com';
            require_once($CFG->dirroot."/user/lib.php");
            $user->id = user_create_user($user);

            //create two roles
            $role1->id = create_role('role1thatshouldnotexist', 'role1thatshouldnotexist', '');
            $role2->id = create_role('role2thatshouldnotexist', 'role2thatshouldnotexist', '');

            //assign user to role by webservice
            $context = get_system_context();
            $assignments = array(
                array('roleid' => $role1->id, 'userid' => $user->id, 'contextid' => $context->id),
                array('roleid' => $role2->id, 'userid' => $user->id, 'contextid' => $context->id)
            );

            $function = 'moodle_role_assign';
            $params = array('assignments' => $assignments);
            $client->call($function, $params);

            //check that the assignment work
            $roles = get_user_roles($context, $user->id, false);
            foreach ($roles as $role) {
                $this->assertTrue(($role->roleid == $role1->id) or ($role->roleid == $role2->id) );
            }

            //unassign roles from user
            role_unassign($role1->id, $user->id, $context->id, '', NULL);
            role_unassign($role2->id, $user->id, $context->id, '', NULL);

            //delete user from DB
            $DB->delete_records('user', array('id' => $user->id));

            //delete the two role from DB
            delete_role($role1->id);
            delete_role($role2->id);
        }
    }

    function moodle_role_unassign($client) {
        global $DB, $CFG;

        $searchusers = $DB->get_records_list('user', 'username',
                array('veryimprobabletestusername2'));
        $searchroles = $DB->get_records_list('role', 'shortname',
                array('role1thatshouldnotexist', 'role2thatshouldnotexist'));

        if (empty($searchusers) and empty($searchroles)) {

            //create a temp user
            $user = new stdClass();
            $user->username = 'veryimprobabletestusername2';
            $user->password = 'testpassword2';
            $user->firstname = 'testfirstname2';
            $user->lastname = 'testlastname2';
            $user->email = 'testemail1@moodle.com';
            require_once($CFG->dirroot."/user/lib.php");
            $user->id = user_create_user($user);

            //create two roles
            $role1->id = create_role('role1thatshouldnotexist', 'role1thatshouldnotexist', '');
            $role2->id = create_role('role2thatshouldnotexist', 'role2thatshouldnotexist', '');
        
            //assign roles from user
            $context = get_system_context();
            role_assign($role1->id, $user->id, $context->id);
            role_assign($role2->id, $user->id, $context->id);

            //check that the local assignment work
            $roles = get_user_roles($context, $user->id, false);
            foreach ($roles as $role) {
                $this->assertTrue(($role->roleid == $role1->id) or ($role->roleid == $role2->id) );
            }

            //unassign user to role by webservice          
            $assignments = array(
                array('roleid' => $role1->id, 'userid' => $user->id, 'contextid' => $context->id),
                array('roleid' => $role2->id, 'userid' => $user->id, 'contextid' => $context->id)
            );
            $function = 'moodle_role_unassign';
            $params = array('assignments' => $assignments);
            $client->call($function, $params);

            //check that the web service unassignment work
            $roles = get_user_roles($context, $user->id, false);
            $this->assertTrue(empty($roles));

            //delete user from DB
            $DB->delete_records('user', array('id' => $user->id));

            //delete the two role from DB
            delete_role($role1->id);
            delete_role($role2->id);
        }

    }

    /**
     * READ ONLY test
     * TODO: find a better solution that running web service for each course
     * in the system
     * For each courses, test the number of groups
     * @param object $client
     */
    function moodle_group_get_course_groups($client) {
        global $DB;

        $courses = $DB->get_records('course');
        foreach($courses as $course) {
            $coursegroups = groups_get_all_groups($course->id);
            $function = 'moodle_group_get_course_groups';
            $params = array('courseid' => $course->id);
            $groups = $client->call($function, $params);
            $this->assertEqual(count($groups), count($coursegroups));
        }
    }


    /**
     * READ ONLY test
     * Test that the same number of members are returned
     * for each existing group in the system
     * @param object $client
     */
    function moodle_group_get_groupmembers($client) {
        global $DB;

        $groups = $DB->get_records('groups');
        $groupids = array();
        foreach ($groups as $group) {
            $groupids[] = $group->id;
        }
        $function = 'moodle_group_get_groupmembers';
        $params = array('groupids' => $groupids);
        $groupsmembers = $client->call($function, $params);

        foreach($groupsmembers as $groupmembers) {
            $dbgroupmembers = groups_get_members($groupmembers['groupid']);
            unset($groups[$groupmembers['groupid']]);
            $this->assertEqual(count($dbgroupmembers), count($groupmembers['userids']));
        }

        //check that all existing groups have been returned by the web service function
        $this->assertTrue(empty($groups));
       
        
    }

    function moodle_group_add_groupmembers($client) {
        global $DB, $CFG;

        //create category
        $category = new stdClass();
        $category->name = 'tmpcategoryfortest123';
        $category->id = $DB->insert_record('course_categories', $category);

        //create a course
        $course = new stdClass();
        $course->fullname = 'tmpcoursefortest123';
        $course->shortname = 'tmpcoursefortest123';
        $course->idnumber = 'tmpcoursefortest123';
        $course->category = $category->id;
        $course->id = $DB->insert_record('course', $course);

        //create a role
        $role1->id = create_role('role1thatshouldnotexist', 'role1thatshouldnotexist', '');

        //create a user
        $user = new stdClass();
        $user->username = 'veryimprobabletestusername2';
        $user->password = 'testpassword2';
        $user->firstname = 'testfirstname2';
        $user->lastname = 'testlastname2';
        $user->email = 'testemail1@moodle.com';
        $user->mnethostid = $CFG->mnet_localhost_id;
        require_once($CFG->dirroot."/user/lib.php");
        $user->id = user_create_user($user);

        //create course context
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

        //enrol the user in the course with the created role
        role_assign($role1->id, $user->id, $context->id);
        $enrol = new stdClass();
        $enrol->courseid = $course->id;
        $enrol->roleid = $role1->id;
        $enrol->id = $DB->insert_record('enrol', $enrol);
        $enrolment = new stdClass();
        $enrolment->userid = $user->id;
        $enrolment->enrolid = $enrol->id;
        $enrolment->id = $DB->insert_record('user_enrolments', $enrolment);

        //create a group in the course
        $group = new stdClass();
        $group->courseid = $course->id;
        $group->name = 'tmpgroufortest123';
        $group->id = $DB->insert_record('groups', $group);

        //WEBSERVICE CALL
        $function = 'moodle_group_add_groupmembers';
        $params = array('members' => array(array('groupid' => $group->id, 'userid' => $user->id)));
        $groupsmembers = $client->call($function, $params);

        //CHECK TEST RESULT
        require_once($CFG->libdir . '/grouplib.php');
        $groupmembers = groups_get_members($group->id);
        $this->assertEqual(count($groupmembers), 1);
        $this->assertEqual($groupmembers[$user->id]->id, $user->id);

        //remove the members from the group
        require_once($CFG->dirroot . "/group/lib.php");
        groups_remove_member($group->id, $user->id);

        //delete the group
        $DB->delete_records('groups', array('id' => $group->id));

        //unenrol the user
        $DB->delete_records('user_enrolments', array('id' => $enrolment->id));
        $DB->delete_records('enrol', array('id' => $enrol->id));
        role_unassign($role1->id, $user->id, $context->id);

        //delete course context
        delete_context(CONTEXT_COURSE, $course->id);

        //delete the user
        $DB->delete_records('user', array('id' => $user->id));

        //delete the role
        delete_role($role1->id);

        //delete the course
        $DB->delete_records('course', array('id' => $course->id));

        //delete the category
        $DB->delete_records('course_categories', array('id' => $category->id));
        
    }

    function moodle_group_delete_groupmembers($client) {
        global $DB, $CFG;

        //create category
        $category = new stdClass();
        $category->name = 'tmpcategoryfortest123';
        $category->id = $DB->insert_record('course_categories', $category);

        //create a course
        $course = new stdClass();
        $course->fullname = 'tmpcoursefortest123';
        $course->shortname = 'tmpcoursefortest123';
        $course->idnumber = 'tmpcoursefortest123';
        $course->category = $category->id;
        $course->id = $DB->insert_record('course', $course);

        //create a role
        $role1->id = create_role('role1thatshouldnotexist', 'role1thatshouldnotexist', '');

        //create a user
        $user = new stdClass();
        $user->username = 'veryimprobabletestusername2';
        $user->password = 'testpassword2';
        $user->firstname = 'testfirstname2';
        $user->lastname = 'testlastname2';
        $user->email = 'testemail1@moodle.com';
        $user->mnethostid = $CFG->mnet_localhost_id;
        require_once($CFG->dirroot."/user/lib.php");
        $user->id = user_create_user($user);

        //create course context
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

        //enrol the user in the course with the created role
        role_assign($role1->id, $user->id, $context->id);
        $enrol = new stdClass();
        $enrol->courseid = $course->id;
        $enrol->roleid = $role1->id;
        $enrol->id = $DB->insert_record('enrol', $enrol);
        $enrolment = new stdClass();
        $enrolment->userid = $user->id;
        $enrolment->enrolid = $enrol->id;
        $enrolment->id = $DB->insert_record('user_enrolments', $enrolment);

        //create a group in the course
        $group = new stdClass();
        $group->courseid = $course->id;
        $group->name = 'tmpgroufortest123';
        $group->id = $DB->insert_record('groups', $group);

        //add group member
        require_once($CFG->dirroot . "/group/lib.php");
        groups_add_member($group->id, $user->id);
        $groupmembers = groups_get_members($group->id);
        $this->assertEqual(count($groupmembers), 1);

        //WEB SERVICE CALL - remove the members from the group
        $function = 'moodle_group_delete_groupmembers';
        $params = array('members' => array(array('groupid' => $group->id, 'userid' => $user->id)));
        $client->call($function, $params);

        require_once($CFG->libdir . '/grouplib.php');
        $groupmembers = groups_get_members($group->id);
        $this->assertEqual(count($groupmembers), 0);

        //delete the group
        $DB->delete_records('groups', array('id' => $group->id));

        //unenrol the user
        $DB->delete_records('user_enrolments', array('id' => $enrolment->id));
        $DB->delete_records('enrol', array('id' => $enrol->id));
        role_unassign($role1->id, $user->id, $context->id);

        //delete course context
        delete_context(CONTEXT_COURSE, $course->id);

        //delete the user
        $DB->delete_records('user', array('id' => $user->id));

        //delete the role
        delete_role($role1->id);

        //delete the course
        $DB->delete_records('course', array('id' => $course->id));

        //delete the category
        $DB->delete_records('course_categories', array('id' => $category->id));

    }

    function moodle_group_create_groups($client) {
        global $DB, $CFG;

        //create category
        $category = new stdClass();
        $category->name = 'tmpcategoryfortest123';
        $category->id = $DB->insert_record('course_categories', $category);

        //create a course
        $course = new stdClass();
        $course->fullname = 'tmpcoursefortest123';
        $course->shortname = 'tmpcoursefortest123';
        $course->idnumber = 'tmpcoursefortest123';
        $course->category = $category->id;
        $course->id = $DB->insert_record('course', $course);

        //create a role
        $role1->id = create_role('role1thatshouldnotexist', 'role1thatshouldnotexist', '');

        //create a user
        $user = new stdClass();
        $user->username = 'veryimprobabletestusername2';
        $user->password = 'testpassword2';
        $user->firstname = 'testfirstname2';
        $user->lastname = 'testlastname2';
        $user->email = 'testemail1@moodle.com';
        $user->mnethostid = $CFG->mnet_localhost_id;
        require_once($CFG->dirroot."/user/lib.php");
        $user->id = user_create_user($user);

        //create course context
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

        //enrol the user in the course with the created role
        role_assign($role1->id, $user->id, $context->id);
        $enrol = new stdClass();
        $enrol->courseid = $course->id;
        $enrol->roleid = $role1->id;
        $enrol->id = $DB->insert_record('enrol', $enrol);
        $enrolment = new stdClass();
        $enrolment->userid = $user->id;
        $enrolment->enrolid = $enrol->id;
        $enrolment->id = $DB->insert_record('user_enrolments', $enrolment);

        require_once($CFG->dirroot . "/group/lib.php");
        $groups = groups_get_all_groups($course->id);
        $this->assertEqual(count($groups), 0);

        //WEBSERVICE CALL - create a group in the course
        $group = new stdClass();
        $group->courseid = $course->id;
        $group->name = 'tmpgroufortest123';
        $group->enrolmentkey = '';
        $group->description = '';
        $group2 = new stdClass();
        $group2->courseid = $course->id;
        $group2->name = 'tmpgroufortest1233';
        $group2->enrolmentkey = '';
        $group2->description = '';
        $paramgroups = array($group, $group2);
        $function = 'moodle_group_create_groups';
        $params = array('groups' => $paramgroups);
        $createdgroups = $client->call($function, $params);

        $groups = groups_get_all_groups($course->id);
        $this->assertEqual(count($groups), count($paramgroups));

        //delete the group
        foreach ($groups as $dbgroup) {
            $DB->delete_records('groups', array('id' => $dbgroup->id));
        }

        //unenrol the user
        $DB->delete_records('user_enrolments', array('id' => $enrolment->id));
        $DB->delete_records('enrol', array('id' => $enrol->id));
        role_unassign($role1->id, $user->id, $context->id);

        //delete course context
        delete_context(CONTEXT_COURSE, $course->id);

        //delete the user
        $DB->delete_records('user', array('id' => $user->id));

        //delete the role
        delete_role($role1->id);

        //delete the course
        $DB->delete_records('course', array('id' => $course->id));

        //delete the category
        $DB->delete_records('course_categories', array('id' => $category->id));

    }

    function moodle_group_delete_groups($client) {
        global $DB, $CFG;

        //create category
        $category = new stdClass();
        $category->name = 'tmpcategoryfortest123';
        $category->id = $DB->insert_record('course_categories', $category);

        //create a course
        $course = new stdClass();
        $course->fullname = 'tmpcoursefortest123';
        $course->shortname = 'tmpcoursefortest123';
        $course->idnumber = 'tmpcoursefortest123';
        $course->category = $category->id;
        $course->id = $DB->insert_record('course', $course);

        //create a role
        $role1->id = create_role('role1thatshouldnotexist', 'role1thatshouldnotexist', '');

        //create a user
        $user = new stdClass();
        $user->username = 'veryimprobabletestusername2';
        $user->password = 'testpassword2';
        $user->firstname = 'testfirstname2';
        $user->lastname = 'testlastname2';
        $user->email = 'testemail1@moodle.com';
        $user->mnethostid = $CFG->mnet_localhost_id;
        require_once($CFG->dirroot."/user/lib.php");
        $user->id = user_create_user($user);

        //create course context
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

        //enrol the user in the course with the created role
        role_assign($role1->id, $user->id, $context->id);
        $enrol = new stdClass();
        $enrol->courseid = $course->id;
        $enrol->roleid = $role1->id;
        $enrol->id = $DB->insert_record('enrol', $enrol);
        $enrolment = new stdClass();
        $enrolment->userid = $user->id;
        $enrolment->enrolid = $enrol->id;
        $enrolment->id = $DB->insert_record('user_enrolments', $enrolment);

        //create a group in the course
        $group = new stdClass();
        $group->courseid = $course->id;
        $group->name = 'tmpgroufortest123';
        $group->enrolmentkey = '';
        $group->description = '';
        $group->id = $DB->insert_record('groups', $group);
        $group2 = new stdClass();
        $group2->courseid = $course->id;
        $group2->name = 'tmpgroufortest1233';
        $group2->enrolmentkey = '';
        $group2->description = '';
        $group2->id = $DB->insert_record('groups', $group2);
        $paramgroups = array($group, $group2);

        require_once($CFG->dirroot . "/group/lib.php");
        $groups = groups_get_all_groups($course->id);
        $this->assertEqual(2, count($groups));

        //WEBSERVICE CALL -  delete the group
        $function = 'moodle_group_delete_groups';
        $params = array('groupids' => array($group->id, $group2->id));
        $client->call($function, $params);

        $groups = groups_get_all_groups($course->id);
        $this->assertEqual(0, count($groups));

        //unenrol the user
        $DB->delete_records('user_enrolments', array('id' => $enrolment->id));
        $DB->delete_records('enrol', array('id' => $enrol->id));
        role_unassign($role1->id, $user->id, $context->id);

        //delete course context
        delete_context(CONTEXT_COURSE, $course->id);

        //delete the user
        $DB->delete_records('user', array('id' => $user->id));

        //delete the role
        delete_role($role1->id);

        //delete the course
        $DB->delete_records('course', array('id' => $course->id));

        //delete the category
        $DB->delete_records('course_categories', array('id' => $category->id));

    }

}
