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

namespace enrol_database;

/**
 * External database enrolment sync tests
 *
 * This also tests adodb drivers that are matching
 * our four supported Moodle database drivers.
 *
 * @package    enrol_database
 * @category   test
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class sync_test extends \advanced_testcase {
    protected static $courses = array();
    protected static $users = array();
    protected static $roles = array();

    /** @var string Original error log */
    protected $oldlog;

    public static function tearDownAfterClass(): void {
        global $DB;
        // Apply sqlsrv native driver error and logging default
        // settings while finishing the AdoDB tests.
        if ($DB->get_dbfamily() === 'mssql') {
            sqlsrv_configure("WarningsReturnAsErrors", false);
            sqlsrv_configure("LogSubsystems", SQLSRV_LOG_SYSTEM_OFF);
            sqlsrv_configure("LogSeverity", SQLSRV_LOG_SEVERITY_ERROR);
        }
        parent::tearDownAfterClass();
    }

    protected function init_enrol_database() {
        global $DB, $CFG;

        // Discard error logs from AdoDB.
        $this->oldlog = ini_get('error_log');
        ini_set('error_log', "$CFG->dataroot/testlog.log");

        $dbman = $DB->get_manager();

        set_config('dbencoding', 'utf-8', 'enrol_database');

        set_config('dbhost', $CFG->dbhost, 'enrol_database');
        set_config('dbuser', $CFG->dbuser, 'enrol_database');
        set_config('dbpass', $CFG->dbpass, 'enrol_database');
        set_config('dbname', $CFG->dbname, 'enrol_database');

        if (!empty($CFG->dboptions['dbport'])) {
            set_config('dbhost', $CFG->dbhost.':'.$CFG->dboptions['dbport'], 'enrol_database');
        }

        switch ($DB->get_dbfamily()) {

            case 'mysql':
                set_config('dbtype', 'mysqli', 'enrol_database');
                set_config('dbsetupsql', "SET NAMES 'UTF-8'", 'enrol_database');
                if (!empty($CFG->dboptions['dbsocket'])) {
                    $dbsocket = $CFG->dboptions['dbsocket'];
                    if ((strpos($dbsocket, '/') === false and strpos($dbsocket, '\\') === false)) {
                        $dbsocket = ini_get('mysqli.default_socket');
                    }
                    set_config('dbtype', 'mysqli://'.rawurlencode($CFG->dbuser).':'.rawurlencode($CFG->dbpass).'@'.rawurlencode($CFG->dbhost).'/'.rawurlencode($CFG->dbname).'?socket='.rawurlencode($dbsocket), 'enrol_database');
                }
                break;

            case 'oracle':
                set_config('dbtype', 'oci8po', 'enrol_database');
                set_config('dbsybasequoting', '1', 'enrol_database');
                break;

            case 'postgres':
                set_config('dbtype', 'postgres7', 'enrol_database');
                $setupsql = "SET NAMES 'UTF-8'";
                if (!empty($CFG->dboptions['dbschema'])) {
                    $setupsql .= "; SET search_path = '".$CFG->dboptions['dbschema']."'";
                }
                set_config('dbsetupsql', $setupsql, 'enrol_database');
                if (!empty($CFG->dboptions['dbsocket']) and ($CFG->dbhost === 'localhost' or $CFG->dbhost === '127.0.0.1')) {
                    if (strpos($CFG->dboptions['dbsocket'], '/') !== false) {
                        $socket = $CFG->dboptions['dbsocket'];
                        if (!empty($CFG->dboptions['dbport'])) {
                            $socket .= ':' . $CFG->dboptions['dbport'];
                        }
                        set_config('dbhost', $socket, 'enrol_database');
                    } else {
                      set_config('dbhost', '', 'enrol_database');
                    }
                }
                break;

            case 'mssql':
                set_config('dbtype', 'mssqlnative', 'enrol_database');

                // The native sqlsrv driver uses a comma as separator between host and port.
                $dbhost = $CFG->dbhost;
                if (!empty($dboptions['dbport'])) {
                    $dbhost .= ',' . $dboptions['dbport'];
                }
                set_config('dbhost', $dbhost, 'enrol_database');
                break;

            default:
                throw new exception('Unknown database driver '.get_class($DB));
        }

        // NOTE: It is stongly discouraged to create new tables in advanced_testcase classes,
        //       but there is no other simple way to test ext database enrol sync, so let's
        //       disable transactions are try to cleanup after the tests.

        $table = new \xmldb_table('enrol_database_test_enrols');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('userid', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('roleid', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('otheruser', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);
        set_config('remoteenroltable', $CFG->prefix.'enrol_database_test_enrols', 'enrol_database');
        set_config('remotecoursefield', 'courseid', 'enrol_database');
        set_config('remoteuserfield', 'userid', 'enrol_database');
        set_config('remoterolefield', 'roleid', 'enrol_database');
        set_config('remoteotheruserfield', 'otheruser', 'enrol_database');

        $table = new \xmldb_table('enrol_database_test_courses');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('category', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('startdate', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_field('enddate', XMLDB_TYPE_CHAR, '255', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);
        set_config('newcoursetable', $CFG->prefix.'enrol_database_test_courses', 'enrol_database');
        set_config('newcoursefullname', 'fullname', 'enrol_database');
        set_config('newcourseshortname', 'shortname', 'enrol_database');
        set_config('newcourseidnumber', 'idnumber', 'enrol_database');
        set_config('newcoursecategory', 'category', 'enrol_database');

        // Create some test users and courses.
        for ($i = 1; $i <= 4; $i++) {
            self::$courses[$i] = $this->getDataGenerator()->create_course(array('fullname' => 'Test course '.$i, 'shortname' => 'tc'.$i, 'idnumber' => 'courseid'.$i));
        }

        for ($i = 1; $i <= 10; $i++) {
            self::$users[$i] = $this->getDataGenerator()->create_user(array('username' => 'username'.$i, 'idnumber' => 'userid'.$i, 'email' => 'user'.$i.'@example.com'));
        }

        foreach (get_all_roles() as $role) {
            self::$roles[$role->shortname] = $role;
        }
    }

    protected function cleanup_enrol_database() {
        global $DB;

        $dbman = $DB->get_manager();
        $table = new \xmldb_table('enrol_database_test_enrols');
        $dbman->drop_table($table);
        $table = new \xmldb_table('enrol_database_test_courses');
        $dbman->drop_table($table);

        self::$courses = null;
        self::$users = null;
        self::$roles = null;

        ini_set('error_log', $this->oldlog);
    }

    protected function reset_enrol_database() {
        global $DB;

        $DB->delete_records('enrol_database_test_enrols', array());
        $DB->delete_records('enrol_database_test_courses', array());

        $plugin = enrol_get_plugin('database');
        $instances = $DB->get_records('enrol', array('enrol' => 'database'));
        foreach($instances as $instance) {
            $plugin->delete_instance($instance);
        }
    }

    protected function assertIsEnrolled($userindex, $courseindex, $status=null, $rolename = null) {
        global $DB;
        $dbinstance = $DB->get_record('enrol', array('courseid' => self::$courses[$courseindex]->id, 'enrol' => 'database'), '*', MUST_EXIST);

        $conditions = array('enrolid' => $dbinstance->id, 'userid' => self::$users[$userindex]->id);
        if ($status !== null) {
            $conditions['status'] = $status;
        }
        $this->assertTrue($DB->record_exists('user_enrolments', $conditions));

        $this->assertHasRoleAssignment($userindex, $courseindex, $rolename);
    }

    protected function assertHasRoleAssignment($userindex, $courseindex, $rolename = null) {
        global $DB;
        $dbinstance = $DB->get_record('enrol', array('courseid' => self::$courses[$courseindex]->id, 'enrol' => 'database'), '*', MUST_EXIST);

        $coursecontext = \context_course::instance(self::$courses[$courseindex]->id);
        if ($rolename === false) {
            $this->assertFalse($DB->record_exists('role_assignments', array('component' => 'enrol_database', 'itemid' => $dbinstance->id, 'userid' => self::$users[$userindex]->id, 'contextid' => $coursecontext->id)));
        } else if ($rolename !== null) {
            $this->assertTrue($DB->record_exists('role_assignments', array('component' => 'enrol_database', 'itemid' => $dbinstance->id, 'userid' => self::$users[$userindex]->id, 'contextid' => $coursecontext->id, 'roleid' => self::$roles[$rolename]->id)));
        }
    }

    protected function assertIsNotEnrolled($userindex, $courseindex) {
        global $DB;
        if (!$dbinstance = $DB->get_record('enrol', array('courseid' => self::$courses[$courseindex]->id, 'enrol' => 'database'))) {
            return;
        }
        $this->assertFalse($DB->record_exists('user_enrolments', array('enrolid' => $dbinstance->id, 'userid' => self::$users[$userindex]->id)));
    }

    public function test_sync_user_enrolments(): void {
        global $DB;

        $this->init_enrol_database();

        $this->resetAfterTest(false);
        $this->preventResetByRollback();

        $plugin = enrol_get_plugin('database');

        // Test basic enrol sync for one user after login.

        $this->reset_enrol_database();
        $plugin->set_config('localcoursefield', 'idnumber');
        $plugin->set_config('localuserfield', 'idnumber');
        $plugin->set_config('localrolefield', 'shortname');

        $plugin->set_config('defaultrole', self::$roles['student']->id);

        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid2', 'roleid' => 'teacher'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid2', 'courseid' => 'courseid1', 'roleid' => null));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid4', 'courseid' => 'courseid4', 'roleid' => 'editingteacher', 'otheruser' => '1'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'xxxxxxx', 'courseid' => 'courseid1', 'roleid' => 'student')); // Bogus record to be ignored.
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'xxxxxxxxx', 'roleid' => 'student')); // Bogus record to be ignored.

        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(0, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('component' => 'enrol_database')));

        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');

        // Make sure there are no errors or changes on the next login.

        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');

        $plugin->sync_user_enrolments(self::$users[2]);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(3, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');

        $plugin->sync_user_enrolments(self::$users[4]);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');

        // Enrolment removals.

        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_KEEP);
        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');


        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPEND);
        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_SUSPENDED, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');


        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);
        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(3, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_SUSPENDED, false);
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');


        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);
        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(3, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsNotEnrolled(1, 1);
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');

        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid4', 'courseid' => 'courseid4', 'roleid' => 'editingteacher'));
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);
        $plugin->sync_user_enrolments(self::$users[4]);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, false);

        // Test all other mapping options.

        $this->reset_enrol_database();

        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(0, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('component' => 'enrol_database')));

        $plugin->set_config('localcoursefield', 'id');
        $plugin->set_config('localuserfield', 'id');
        $plugin->set_config('localrolefield', 'id');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->id, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->id, 'courseid' => self::$courses[2]->id, 'roleid' => self::$roles['teacher']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[2]->id, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));

        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');


        $this->reset_enrol_database();
        $plugin->set_config('localcoursefield', 'shortname');
        $plugin->set_config('localuserfield', 'email');
        $plugin->set_config('localrolefield', 'id');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->email, 'courseid' => self::$courses[1]->shortname, 'roleid' => self::$roles['student']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->email, 'courseid' => self::$courses[2]->shortname, 'roleid' => self::$roles['teacher']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[2]->email, 'courseid' => self::$courses[1]->shortname, 'roleid' => self::$roles['student']->id));

        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');


        $this->reset_enrol_database();
        $plugin->set_config('localcoursefield', 'id');
        $plugin->set_config('localuserfield', 'username');
        $plugin->set_config('localrolefield', 'id');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->username, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->username, 'courseid' => self::$courses[2]->id, 'roleid' => self::$roles['teacher']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[2]->username, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));

        $plugin->sync_user_enrolments(self::$users[1]);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');
    }

    /**
     * @depends test_sync_user_enrolments
     */
    public function test_sync_users(): void {
        global $DB;

        $this->resetAfterTest(false);
        $this->preventResetByRollback();
        $this->reset_enrol_database();

        $plugin = enrol_get_plugin('database');

        $trace = new \null_progress_trace();

        // Test basic enrol sync for one user after login.

        $plugin->set_config('localcoursefield', 'idnumber');
        $plugin->set_config('localuserfield', 'idnumber');
        $plugin->set_config('localrolefield', 'shortname');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid2', 'roleid' => 'editingteacher'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid2', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid4', 'courseid' => 'courseid4', 'roleid' => 'editingteacher', 'otheruser' => '1'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'xxxxxxx', 'courseid' => 'courseid1', 'roleid' => 'student')); // Bogus record to be ignored.
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'xxxxxxxxx', 'roleid' => 'student')); // Bogus record to be ignored.
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(0, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('component' => 'enrol_database')));

        $plugin->sync_enrolments($trace);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');

        $plugin->set_config('defaultrole', self::$roles['teacher']->id);
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid3', 'courseid' => 'courseid3'));
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');


        // Test different unenrolment options.

        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_KEEP);
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');


        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPEND);
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_SUSPENDED, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');


        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_SUSPENDED, false);
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');


        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);
        $plugin->sync_enrolments($trace);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(4, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsNotEnrolled(1, 1);
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'student'));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'teacher'));
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(6, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'teacher');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');

        $DB->delete_records('enrol_database_test_enrols', array('userid' => 'userid1', 'courseid' => 'courseid1', 'roleid' => 'teacher'));
        $plugin->sync_enrolments($trace);
        $this->assertEquals(4, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(4, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(5, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'editingteacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsNotEnrolled(4, 4);
        $this->assertHasRoleAssignment(4, 4, 'editingteacher');
        $this->assertIsEnrolled(3, 3, ENROL_USER_ACTIVE, 'teacher');


        // Test all other mapping options.

        $this->reset_enrol_database();

        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(0, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('component' => 'enrol_database')));

        $plugin->set_config('localcoursefield', 'id');
        $plugin->set_config('localuserfield', 'id');
        $plugin->set_config('localrolefield', 'id');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->id, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->id, 'courseid' => self::$courses[2]->id, 'roleid' => self::$roles['teacher']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[2]->id, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));

        $plugin->sync_enrolments($trace);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(3, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');


        $this->reset_enrol_database();
        $plugin->set_config('localcoursefield', 'shortname');
        $plugin->set_config('localuserfield', 'email');
        $plugin->set_config('localrolefield', 'id');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->email, 'courseid' => self::$courses[1]->shortname, 'roleid' => self::$roles['student']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->email, 'courseid' => self::$courses[2]->shortname, 'roleid' => self::$roles['teacher']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[2]->email, 'courseid' => self::$courses[1]->shortname, 'roleid' => self::$roles['student']->id));

        $plugin->sync_enrolments($trace);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(3, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');


        $this->reset_enrol_database();
        $plugin->set_config('localcoursefield', 'id');
        $plugin->set_config('localuserfield', 'username');
        $plugin->set_config('localrolefield', 'id');

        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->username, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->username, 'courseid' => self::$courses[2]->id, 'roleid' => self::$roles['teacher']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[2]->username, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));

        $plugin->sync_enrolments($trace);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(3, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');


        // Test sync of one course only.

        $this->reset_enrol_database();

        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->username, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[1]->username, 'courseid' => self::$courses[2]->id, 'roleid' => self::$roles['teacher']->id));
        $DB->insert_record('enrol_database_test_enrols', array('userid' => self::$users[2]->username, 'courseid' => self::$courses[1]->id, 'roleid' => self::$roles['student']->id));

        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(0, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('component' => 'enrol_database')));

        $plugin->sync_enrolments($trace, self::$courses[3]->id);
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(1, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('component' => 'enrol_database')));

        $plugin->sync_enrolments($trace, self::$courses[1]->id);
        $this->assertEquals(2, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(2, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');

        $plugin->sync_enrolments($trace, self::$courses[2]->id);
        $this->assertEquals(3, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(3, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');
        $this->assertIsEnrolled(2, 1, ENROL_USER_ACTIVE, 'student');


        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        $DB->delete_records('enrol_database_test_enrols', array());

        $plugin->sync_enrolments($trace, self::$courses[1]->id);
        $this->assertEquals(1, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
        $this->assertIsEnrolled(1, 2, ENROL_USER_ACTIVE, 'teacher');

        $plugin->sync_enrolments($trace, self::$courses[2]->id);
        $this->assertEquals(0, $DB->count_records('user_enrolments', array()));
        $this->assertEquals(3, $DB->count_records('enrol', array('enrol' => 'database')));
        $this->assertEquals(0, $DB->count_records('role_assignments', array('component' => 'enrol_database')));
    }

    /**
     * @depends test_sync_users
     */
    public function test_sync_courses(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->preventResetByRollback();
        $this->reset_enrol_database();

        $plugin = enrol_get_plugin('database');

        $trace = new \null_progress_trace();

        $plugin->set_config('localcategoryfield', 'id');
        $coursecat = $this->getDataGenerator()->create_category(array('name' => 'Test category 1', 'idnumber' => 'tcid1'));
        $defcat = $DB->get_record('course_categories', array('id' => $plugin->get_config('defaultcategory')));

        $course1 = array('fullname' => 'New course 1', 'shortname' => 'nc1', 'idnumber' => 'ncid1', 'category' => $coursecat->id);
        $course2 = array('fullname' => 'New course 2', 'shortname' => 'nc2', 'idnumber' => 'ncid2', 'category' => null);
        // Duplicate records are to be ignored.
        $course3 = array('fullname' => 'New course 3', 'shortname' => 'xx', 'idnumber' => 'yy2', 'category' => $defcat->id);
        $course4 = array('fullname' => 'New course 4', 'shortname' => 'xx', 'idnumber' => 'yy3', 'category' => $defcat->id);
        $course5 = array('fullname' => 'New course 5', 'shortname' => 'xx1', 'idnumber' => 'yy', 'category' => $defcat->id);
        $course6 = array('fullname' => 'New course 6', 'shortname' => 'xx2', 'idnumber' => 'yy', 'category' => $defcat->id);

        $DB->insert_record('enrol_database_test_courses', $course1);
        $DB->insert_record('enrol_database_test_courses', $course2);
        $DB->insert_record('enrol_database_test_courses', $course3);
        $DB->insert_record('enrol_database_test_courses', $course4);
        $DB->insert_record('enrol_database_test_courses', $course5);
        $DB->insert_record('enrol_database_test_courses', $course6);

        $this->assertEquals(1+count(self::$courses), $DB->count_records('course'));

        $plugin->sync_courses($trace);

        $this->assertEquals(4+1+count(self::$courses), $DB->count_records('course'));

        $this->assertTrue($DB->record_exists('course', $course1));
        $course2['category'] = $defcat->id;
        $this->assertTrue($DB->record_exists('course', $course2));


        // People should NOT push duplicates there because the results are UNDEFINED! But anyway skip the duplicates.

        $this->assertEquals(1, $DB->count_records('course', array('idnumber' => 'yy')));
        $this->assertEquals(1, $DB->count_records('course', array('shortname' => 'xx')));

        // Check default number of sections matches with the created course sections.

        $recordcourse1 = $DB->get_record('course', $course1);
        $courseconfig = get_config('moodlecourse');
        $numsections = $DB->count_records('course_sections', array('course' => $recordcourse1->id));
        // To compare numsections we have to add topic 0 to default numsections.
        $this->assertEquals(($courseconfig->numsections + 1), $numsections);

        // Test category mapping via idnumber.

        $plugin->set_config('localcategoryfield', 'idnumber');
        $course7 = array('fullname' => 'New course 7', 'shortname' => 'nc7', 'idnumber' => 'ncid7', 'category' => 'tcid1');
        $DB->insert_record('enrol_database_test_courses', $course7);
        $plugin->sync_courses($trace);

        $this->assertEquals(1+4+1+count(self::$courses), $DB->count_records('course'));
        $this->assertTrue($DB->record_exists('course', $course1));
        $this->assertTrue($DB->record_exists('course', $course2));
        $course7['category'] = $coursecat->id;
        $this->assertTrue($DB->record_exists('course', $course7));


        // Test course template.

        $template = $this->getDataGenerator()->create_course(array('numsections' => 666, 'shortname' => 'crstempl'));
        $plugin->set_config('templatecourse', 'crstempl');

        $course8 = array('fullname' => 'New course 8', 'shortname' => 'nc8', 'idnumber' => 'ncid8', 'category' => null);
        $DB->insert_record('enrol_database_test_courses', $course8);
        $plugin->sync_courses($trace);

        $this->assertEquals(2+1+4+1+count(self::$courses), $DB->count_records('course'));
        $course8['category'] = $defcat->id;
        $record = $DB->get_record('course', $course8);
        $this->assertFalse(empty($record));
        $this->assertEquals(666, course_get_format($record)->get_last_section_number());

        // Test invalid category.

        $course9 = array('fullname' => 'New course 9', 'shortname' => 'nc9', 'idnumber' => 'ncid9', 'category' => 'xxxxxxx');
        $DB->insert_record('enrol_database_test_courses', $course9);
        $plugin->sync_courses($trace);
        $this->assertEquals(2+1+4+1+count(self::$courses), $DB->count_records('course'));
        $this->assertFalse($DB->record_exists('course', array('idnumber' => 'ncid9')));


        // Test when categories not specified.

        $plugin->set_config('newcoursecategory', '');
        $plugin->sync_courses($trace);
        $this->assertEquals(1+2+1+4+1+count(self::$courses), $DB->count_records('course'));
        $this->assertTrue($DB->record_exists('course', array('idnumber' => 'ncid9')));

        // Final cleanup - remove extra tables, fixtures and caches.
        $this->cleanup_enrol_database();
    }

    /**
     * Test syncing courses with start and end dates.
     *
     * @covers \enrol_database_plugin::sync_courses
     */
    public function test_sync_courses_start_end_dates(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->init_enrol_database();

        $courseconfig = get_config('moodlecourse');
        $nextyear = (int) date('Y') + 1;
        $prev = (int) date('Y') - 1;

        $midnightstartdate = usergetmidnight(time());
        $midnightenddate = usergetmidnight(time()) + $courseconfig->courseduration;

        $plugin = enrol_get_plugin('database');

        $trace = new \null_progress_trace();

        $course1 = ['fullname' => 'C1', 'shortname' => 'c1', 'idnumber' => 'c1', 'startdate' => 0,
            'enddate' => 0];
        $course2 = ['fullname' => 'C2', 'shortname' => 'c2', 'idnumber' => 'c2', 'startdate' => null,
            'enddate' => null];
        // This course won't be created. Broken start date.
        $course3 = ['fullname' => 'C3', 'shortname' => 'c3', 'idnumber' => 'c3', 'startdate' => 'not date',
            'enddate' => 0];
        // This course won't be created. Broken end date.
        $course4 = ['fullname' => 'C4', 'shortname' => 'c4', 'idnumber' => 'c4', 'startdate' => 0,
            'enddate' => 'not date'];
        // This course won't be created. Start date after end date.
        $course5 = ['fullname' => 'C5', 'shortname' => 'c5', 'idnumber' => 'c5', 'startdate' => '12.05.2024',
            'enddate' => '12.05.2021'];
        $course6 = ['fullname' => 'C6', 'shortname' => 'c6', 'idnumber' => 'c6', 'startdate' => '2024-05-22',
            'enddate' => '2027-05-12'];
        $course7 = ['fullname' => 'C7', 'shortname' => 'c7', 'idnumber' => 'c7', 'startdate' => null,
            'enddate' => '12.05.' . $nextyear];
        $course8 = ['fullname' => 'C8', 'shortname' => 'c8', 'idnumber' => 'c8', 'startdate' => '12.05.2024',
            'enddate' => null];
        // This course won't be created. Start date is not set, but it should be set to date after end date.
        $course9 = ['fullname' => 'C9', 'shortname' => 'c9', 'idnumber' => 'c9', 'startdate' => null,
            'enddate' => '12.05.' . $prev];

        $DB->insert_record('enrol_database_test_courses', $course1);
        $DB->insert_record('enrol_database_test_courses', $course2);
        $DB->insert_record('enrol_database_test_courses', $course3);
        $DB->insert_record('enrol_database_test_courses', $course4);
        $DB->insert_record('enrol_database_test_courses', $course5);
        $DB->insert_record('enrol_database_test_courses', $course6);
        $DB->insert_record('enrol_database_test_courses', $course7);
        $DB->insert_record('enrol_database_test_courses', $course8);
        $DB->insert_record('enrol_database_test_courses', $course9);

        $plugin->set_config('newcoursestartdate', 'startdate');
        $plugin->set_config('newcourseenddate', 'enddate');

        $plugin->sync_courses($trace);

        // Course 3, course 4, course 5 and course 9 should not be created.
        $this->assertTrue($DB->record_exists('course', ['shortname' => $course1['shortname']]));
        $this->assertTrue($DB->record_exists('course', ['shortname' => $course2['shortname']]));
        $this->assertFalse($DB->record_exists('course', ['shortname' => $course3['shortname']]));
        $this->assertFalse($DB->record_exists('course', ['shortname' => $course4['shortname']]));
        $this->assertFalse($DB->record_exists('course', ['shortname' => $course5['shortname']]));
        $this->assertTrue($DB->record_exists('course', ['shortname' => $course6['shortname']]));
        $this->assertTrue($DB->record_exists('course', ['shortname' => $course7['shortname']]));
        $this->assertTrue($DB->record_exists('course', ['shortname' => $course8['shortname']]));
        $this->assertFalse($DB->record_exists('course', ['shortname' => $course9['shortname']]));

        // Check dates for created courses.
        $this->assertEquals($midnightstartdate, $DB->get_field('course', 'startdate', ['shortname' => $course1['shortname']]));
        $this->assertEquals($midnightenddate, $DB->get_field('course', 'enddate', ['shortname' => $course1['shortname']]));

        $this->assertEquals($midnightstartdate, $DB->get_field('course', 'startdate', ['shortname' => $course2['shortname']]));
        $this->assertEquals($midnightenddate, $DB->get_field('course', 'enddate', ['shortname' => $course2['shortname']]));

        $this->assertEquals(strtotime('22.05.2024'), $DB->get_field('course', 'startdate', ['shortname' => $course6['shortname']]));
        $this->assertEquals(strtotime('12.05.2027'), $DB->get_field('course', 'enddate', ['shortname' => $course6['shortname']]));

        $this->assertEquals($midnightstartdate, $DB->get_field('course', 'startdate', ['shortname' => $course7['shortname']]));
        $expected = strtotime('12.05.' . $nextyear);
        $this->assertEquals($expected, $DB->get_field('course', 'enddate', ['shortname' => $course7['shortname']]));

        $this->assertEquals(strtotime('12.05.2024'), $DB->get_field('course', 'startdate', ['shortname' => $course8['shortname']]));
        $expected = strtotime('12.05.2024') + $courseconfig->courseduration;
        $this->assertEquals($expected, $DB->get_field('course', 'enddate', ['shortname' => $course8['shortname']]));

        // Push course with dates as timestamp.
        $course10 = ['fullname' => 'C10', 'shortname' => 'c10', 'idnumber' => 'c10', 'startdate' => 1810051200,
            'enddate' => 1810051211];
        $DB->insert_record('enrol_database_test_courses', $course10);

        $plugin->sync_courses($trace);

        $this->assertTrue($DB->record_exists('course', ['shortname' => $course10['shortname']]));
        $this->assertEquals(1810051200, $DB->get_field('course', 'startdate', ['shortname' => $course10['shortname']]));
        $this->assertEquals(1810051211, $DB->get_field('course', 'enddate', ['shortname' => $course10['shortname']]));

        // Push course with broken dates, but delete dates from plugin configuration before syncing.
        $course11 = ['fullname' => 'C11', 'shortname' => 'c11', 'idnumber' => 'c11', 'startdate' => 'not date',
            'enddate' => 'not date'];
        $DB->insert_record('enrol_database_test_courses', $course11);

        $plugin->set_config('newcoursestartdate', '');
        $plugin->set_config('newcourseenddate', '');
        $plugin->sync_courses($trace);

        $this->assertTrue($DB->record_exists('course', ['shortname' => $course11['shortname']]));
        $this->assertEquals($midnightstartdate, $DB->get_field('course', 'startdate', ['shortname' => $course11['shortname']]));
        $this->assertEquals($midnightenddate, $DB->get_field('course', 'enddate', ['shortname' => $course11['shortname']]));

        // Push courses with correct dates, but set date configuration to not existing date fields.
        $course12 = ['fullname' => 'C12', 'shortname' => 'c12', 'idnumber' => 'c12', 'startdate' => '2024-05-22',
            'enddate' => '2027-05-12'];
        $DB->insert_record('enrol_database_test_courses', $course11);

        $plugin->set_config('newcoursestartdate', 'startdate');
        $plugin->set_config('newcourseenddate', 'ed');
        $plugin->sync_courses($trace);

        // Course should not be synced to prevent setting up incorrect dates.
        $this->assertFalse($DB->record_exists('course', ['shortname' => $course12['shortname']]));

        $course13 = ['fullname' => 'C13', 'shortname' => 'c13', 'idnumber' => 'c13', 'startdate' => '2024-05-22',
            'enddate' => '2027-05-12'];
        $DB->insert_record('enrol_database_test_courses', $course11);

        $plugin->set_config('newcoursestartdate', 'sd');
        $plugin->set_config('newcourseenddate', 'enddate');
        $plugin->sync_courses($trace);

        // Course should not be synced to prevent setting up incorrect dates.
        $this->assertFalse($DB->record_exists('course', ['shortname' => $course13['shortname']]));

        $this->cleanup_enrol_database();
    }

    /**
     * Condition values must be bound as parameters, never interpolated into the statement,
     * using the placeholder syntax of the external database driver.
     *
     * @param string $driver ADOdb driver to build the statement for.
     * @param string $first expected placeholder for the first condition.
     * @param string $second expected placeholder for the second condition.     *
     * @covers \enrol_database_plugin::db_get_sql
     * @dataProvider db_get_query_placeholder_provider
     */
    public function test_db_get_sql_parameterises_conditions(string $driver, string $first, string $second): void {
        global $CFG;
        require_once($CFG->libdir . '/adodb/adodb.inc.php');

        $this->resetAfterTest();

        if ($driver === 'oci8') {
            // ADODB_oci8 cannot be instantiated without the oci8 extension, so mirror its named placeholders.
            // phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod
            $extdb = new class extends \ADOConnection {
                /**
                 * Mirror the oci8 driver's named placeholder syntax.
                 *
                 * @param string $name placeholder name.
                 * @param string $type ignored, kept to match the parent signature.
                 * @return string
                 */
                public function Param($name, $type = 'C') {
                    return ':' . $name;
                }
            };
            // phpcs:enable moodle.NamingConventions.ValidFunctionName.LowercaseMethod
        } else {
            $extdb = ADONewConnection($driver);
        }

        $plugin = enrol_get_plugin('database');
        $method = new \ReflectionMethod($plugin, 'db_get_query');

        $payload = "courseid1\\' OR 1=1 -- ";
        $conditions = ['courseid' => $payload, 'userid' => 'userid1'];
        $result = $method->invoke($plugin, $extdb, 'extenrols', $conditions, ['roleid']);
        $this->assertIsArray($result, 'db_get_query() must return the statement and its parameters.');
        [$sql, $params] = $result;

        // The value must not reach the statement at all, escaped or otherwise.
        $this->assertStringContainsString("courseid = {$first} AND userid = {$second}", $sql);
        $this->assertStringNotContainsString('OR 1=1', $sql);
        $this->assertStringNotContainsString("'", $sql);
        $this->assertSame(['p0' => $payload, 'p1' => 'userid1'], $params);

        // Placeholder numbering restarts for every statement.
        [$sql] = $method->invoke($plugin, $extdb, 'extenrols', $conditions, ['roleid']);
        $this->assertStringContainsString("courseid = {$first} AND userid = {$second}", $sql);

        // No conditions means no WHERE clause and no parameters.
        [$sql, $params] = $method->invoke($plugin, $extdb, 'extenrols', [], ['userid'], true);
        $this->assertStringNotContainsString('WHERE', $sql);
        $this->assertSame([], $params);
    }

    /**
     * Data provider for {@see test_db_get_query_parameterises_conditions}.
     *
     * @return array
     */
    public static function db_get_query_placeholder_provider(): array {
        // Each driver needs its PHP extension to instantiate; oci8 is mocked so it is always available.
        $drivers = [
            'MySQL' => ['driver' => 'mysqli', 'first' => '?', 'second' => '?', 'extension' => 'mysqli'],
            'PostgreSQL' => ['driver' => 'postgres', 'first' => '$1', 'second' => '$2', 'extension' => 'pgsql'],
            'MSSQL' => ['driver' => 'mssqlnative', 'first' => '?', 'second' => '?', 'extension' => 'sqlsrv'],
            'Oracle oci8' => ['driver' => 'oci8', 'first' => ':p0', 'second' => ':p1', 'extension' => null],
        ];

        $cases = [];
        foreach ($drivers as $name => ['extension' => $extension, 'driver' => $driver, 'first' => $first, 'second' => $second]) {
            if ($extension !== null && !extension_loaded($extension)) {
                continue;
            }
            $cases[$name] = ['driver' => $driver, 'first' => $first, 'second' => $second];
        }
        return $cases;
    }

    /**
     * A course mapping containing a quote must be matched as data, not executed as SQL.
     *
     * The breakout this guards against is only reachable on MySQL/MariaDB, where a backslash
     * escapes the quote that follows it. PostgreSQL (standard_conforming_strings) and MSSQL
     * read the pre-fix doubled quote as data, so on those engines this passes either way.
     *
     * @covers \enrol_database_plugin::sync_enrolments
     * @covers \enrol_database_plugin::db_get_sql
     */
    public function test_sync_enrolments_with_quoted_course_mapping(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->init_enrol_database();
        $this->reset_enrol_database();

        $plugin = enrol_get_plugin('database');
        $plugin->set_config('localcoursefield', 'idnumber');
        $plugin->set_config('localuserfield', 'idnumber');
        $plugin->set_config('localrolefield', 'shortname');
        $plugin->set_config('defaultrole', self::$roles['student']->id);
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        $trace = new \null_progress_trace();

        // The backslash ahead of the quote is what breaks out of the literal on MySQL.
        $mapping = "courseid1\\' OR 1=1 -- ";
        $DB->set_field('course', 'idnumber', $mapping, ['id' => self::$courses[1]->id]);

        // One enrolment for the renamed course, one belonging to an unrelated course.
        $DB->insert_record(
            'enrol_database_test_enrols',
            ['userid' => 'userid1', 'courseid' => $mapping, 'roleid' => 'student'],
        );
        $DB->insert_record(
            'enrol_database_test_enrols',
            ['userid' => 'userid5', 'courseid' => 'courseid2', 'roleid' => 'student'],
        );

        $plugin->sync_enrolments($trace);

        // The quoted mapping matches its own record.
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        // It must not pull in the unrelated course's enrolment.
        $this->assertIsNotEnrolled(5, 1);
        $this->assertIsEnrolled(5, 2, ENROL_USER_ACTIVE, 'student');

        $this->cleanup_enrol_database();
    }

    /**
     * Mappings holding characters that are significant to SQL must round-trip as plain data.
     *
     * @covers \enrol_database_plugin::sync_enrolments
     * @covers \enrol_database_plugin::db_get_sql
     */
    public function test_sync_enrolments_with_special_character_mappings(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->init_enrol_database();
        $this->reset_enrol_database();

        $plugin = enrol_get_plugin('database');
        $plugin->set_config('localcoursefield', 'idnumber');
        $plugin->set_config('localuserfield', 'idnumber');
        $plugin->set_config('localrolefield', 'shortname');
        $plugin->set_config('defaultrole', self::$roles['student']->id);
        $plugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        $trace = new \null_progress_trace();

        // A quote, and a Windows style path whose backslashes MySQL used to swallow.
        $quoted = "O'Brien's course";
        $backslashed = 'C:\\courses\\101';
        $DB->set_field('course', 'idnumber', $quoted, ['id' => self::$courses[1]->id]);
        $DB->set_field('course', 'idnumber', $backslashed, ['id' => self::$courses[2]->id]);

        $DB->insert_record(
            'enrol_database_test_enrols',
            ['userid' => 'userid1', 'courseid' => $quoted, 'roleid' => 'student'],
        );
        $DB->insert_record(
            'enrol_database_test_enrols',
            ['userid' => 'userid2', 'courseid' => $backslashed, 'roleid' => 'student'],
        );

        $plugin->sync_enrolments($trace);

        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        $this->assertIsEnrolled(2, 2, ENROL_USER_ACTIVE, 'student');

        $this->cleanup_enrol_database();
    }

    /**
     * A quoted user mapping must be treated as data by sync_user_enrolments(),
     * not executed, to prevent it from fetching another user's enrolments.
     *
     * @covers \enrol_database_plugin::sync_user_enrolments
     * @covers \enrol_database_plugin::db_get_sql
     */
    public function test_sync_user_enrolments_with_quoted_user_mapping(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->init_enrol_database();
        $this->reset_enrol_database();

        $plugin = enrol_get_plugin('database');
        $plugin->set_config('localcoursefield', 'idnumber');
        $plugin->set_config('localuserfield', 'idnumber');
        $plugin->set_config('localrolefield', 'shortname');
        $plugin->set_config('defaultrole', self::$roles['student']->id);

        // A user whose idnumber (the mapped user field) is a breakout payload; the
        // backslash ahead of the quote is what closes the literal on MySQL.
        $payload = "userid1\\' OR 1=1 -- ";
        $DB->set_field('user', 'idnumber', $payload, ['id' => self::$users[1]->id]);
        $user = $DB->get_record('user', ['id' => self::$users[1]->id]);

        // The attacker's own enrolment, plus one belonging to an unrelated user.
        $DB->insert_record(
            'enrol_database_test_enrols',
            ['userid' => $payload, 'courseid' => 'courseid1', 'roleid' => 'student'],
        );
        $DB->insert_record(
            'enrol_database_test_enrols',
            ['userid' => 'userid2', 'courseid' => 'courseid2', 'roleid' => 'student'],
        );

        $plugin->sync_user_enrolments($user);

        // The quoted mapping matches only its own record.
        $this->assertIsEnrolled(1, 1, ENROL_USER_ACTIVE, 'student');
        // It must not pull in the unrelated user's course.
        $this->assertIsNotEnrolled(1, 2);

        $this->cleanup_enrol_database();
    }
}
