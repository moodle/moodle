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
 * LDAP authentication plugin tests.
 *
 * NOTE: in order to execute this test you need to set up
 *       OpenLDAP server with core, cosine, nis and internet schemas
 *       and add configuration constants to config.php or phpunit.xml configuration file:
 *
 * define('TEST_AUTH_LDAP_HOST_URL', 'ldap://127.0.0.1');
 * define('TEST_AUTH_LDAP_BIND_DN', 'cn=someuser,dc=example,dc=local');
 * define('TEST_AUTH_LDAP_BIND_PW', 'somepassword');
 * define('TEST_AUTH_LDAP_DOMAIN', 'dc=example,dc=local');
 *
 * @package    auth_ldap
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class auth_ldap_testcase extends advanced_testcase {

    public function test_auth_ldap() {
        global $CFG, $DB;

        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('LDAP extension is not loaded.');
        }

        $this->resetAfterTest();

        require_once($CFG->dirroot.'/auth/ldap/auth.php');
        require_once($CFG->libdir.'/ldaplib.php');

        if (!defined('TEST_AUTH_LDAP_HOST_URL') or !defined('TEST_AUTH_LDAP_BIND_DN') or !defined('TEST_AUTH_LDAP_BIND_PW') or !defined('TEST_AUTH_LDAP_DOMAIN')) {
            $this->markTestSkipped('External LDAP test server not configured.');
        }

        // Make sure we can connect the server.
        $debuginfo = '';
        if (!$connection = ldap_connect_moodle(TEST_AUTH_LDAP_HOST_URL, 3, 'rfc2307', TEST_AUTH_LDAP_BIND_DN, TEST_AUTH_LDAP_BIND_PW, LDAP_DEREF_NEVER, $debuginfo, false)) {
            $this->markTestSkipped('Can not connect to LDAP test server: '.$debuginfo);
        }

        $this->enable_plugin();

        // Create new empty test container.
        $topdn = 'dc=moodletest,'.TEST_AUTH_LDAP_DOMAIN;

        $this->recursive_delete($connection, TEST_AUTH_LDAP_DOMAIN, 'dc=moodletest');

        $o = array();
        $o['objectClass'] = array('dcObject', 'organizationalUnit');
        $o['dc']         = 'moodletest';
        $o['ou']         = 'MOODLETEST';
        if (!ldap_add($connection, 'dc=moodletest,'.TEST_AUTH_LDAP_DOMAIN, $o)) {
            $this->markTestSkipped('Can not create test LDAP container.');
        }

        // Create a few users.
        $o = array();
        $o['objectClass'] = array('organizationalUnit');
        $o['ou']          = 'users';
        ldap_add($connection, 'ou='.$o['ou'].','.$topdn, $o);

        for ($i=1; $i<=5; $i++) {
            $this->create_ldap_user($connection, $topdn, $i);
        }

        // Set up creators group.
        $o = array();
        $o['objectClass'] = array('posixGroup');
        $o['cn']          = 'creators';
        $o['gidNumber']   = 1;
        $o['memberUid']   = array('username1', 'username2');
        ldap_add($connection, 'cn='.$o['cn'].','.$topdn, $o);

        $creatorrole = $DB->get_record('role', array('shortname'=>'coursecreator'));
        $this->assertNotEmpty($creatorrole);


        // Configure the plugin a bit.
        set_config('host_url', TEST_AUTH_LDAP_HOST_URL, 'auth/ldap');
        set_config('start_tls', 0, 'auth/ldap');
        set_config('ldap_version', 3, 'auth/ldap');
        set_config('ldapencoding', 'utf-8', 'auth/ldap');
        set_config('pagesize', '2', 'auth/ldap');
        set_config('bind_dn', TEST_AUTH_LDAP_BIND_DN, 'auth/ldap');
        set_config('bind_pw', TEST_AUTH_LDAP_BIND_PW, 'auth/ldap');
        set_config('user_type', 'rfc2307', 'auth/ldap');
        set_config('contexts', 'ou=users,'.$topdn, 'auth/ldap');
        set_config('search_sub', 0, 'auth/ldap');
        set_config('opt_deref', LDAP_DEREF_NEVER, 'auth/ldap');
        set_config('user_attribute', 'cn', 'auth/ldap');
        set_config('memberattribute', 'memberuid', 'auth/ldap');
        set_config('memberattribute_isdn', 0, 'auth/ldap');
        set_config('creators', 'cn=creators,'.$topdn, 'auth/ldap');
        set_config('removeuser', AUTH_REMOVEUSER_KEEP, 'auth/ldap');

        set_config('field_map_email', 'mail', 'auth/ldap');
        set_config('field_updatelocal_email', 'oncreate', 'auth/ldap');
        set_config('field_updateremote_email', '0', 'auth/ldap');
        set_config('field_lock_email', 'unlocked', 'auth/ldap');

        set_config('field_map_firstname', 'givenName', 'auth/ldap');
        set_config('field_updatelocal_firstname', 'oncreate', 'auth/ldap');
        set_config('field_updateremote_firstname', '0', 'auth/ldap');
        set_config('field_lock_firstname', 'unlocked', 'auth/ldap');

        set_config('field_map_lastname', 'sn', 'auth/ldap');
        set_config('field_updatelocal_lastname', 'oncreate', 'auth/ldap');
        set_config('field_updateremote_lastname', '0', 'auth/ldap');
        set_config('field_lock_lastname', 'unlocked', 'auth/ldap');


        $this->assertEquals(2, $DB->count_records('user'));
        $this->assertEquals(0, $DB->count_records('role_assignments'));

        /** @var auth_plugin_ldap $auth */
        $auth = get_auth_plugin('ldap');

        ob_start();
        $auth->sync_users(true);
        ob_end_clean();

        $this->assertEquals(5, $DB->count_records('user', array('auth'=>'ldap')));
        $this->assertEquals(2, $DB->count_records('role_assignments'));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$creatorrole->id)));

        for ($i=1; $i<=5; $i++) {
            $this->assertTrue($DB->record_exists('user', array('username'=>'username'.$i, 'email'=>'user'.$i.'@example.com', 'firstname'=>'Firstname'.$i, 'lastname'=>'Lastname'.$i)));
        }

        $this->delete_ldap_user($connection, $topdn, 1);

        ob_start();
        $auth->sync_users(true);
        ob_end_clean();

        $this->assertEquals(5, $DB->count_records('user', array('auth'=>'ldap')));
        $this->assertEquals(0, $DB->count_records('user', array('suspended'=>1)));
        $this->assertEquals(0, $DB->count_records('user', array('deleted'=>1)));
        $this->assertEquals(2, $DB->count_records('role_assignments'));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$creatorrole->id)));


        set_config('removeuser', AUTH_REMOVEUSER_SUSPEND, 'auth/ldap');

        /** @var auth_plugin_ldap $auth */
        $auth = get_auth_plugin('ldap');

        ob_start();
        $auth->sync_users(true);
        ob_end_clean();

        $this->assertEquals(4, $DB->count_records('user', array('auth'=>'ldap')));
        $this->assertEquals(1, $DB->count_records('user', array('auth'=>'nologin', 'username'=>'username1')));
        $this->assertEquals(0, $DB->count_records('user', array('suspended'=>1)));
        $this->assertEquals(0, $DB->count_records('user', array('deleted'=>1)));
        $this->assertEquals(2, $DB->count_records('role_assignments'));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$creatorrole->id)));

        $this->create_ldap_user($connection, $topdn, 1);

        ob_start();
        $auth->sync_users(true);
        ob_end_clean();

        $this->assertEquals(5, $DB->count_records('user', array('auth'=>'ldap')));
        $this->assertEquals(0, $DB->count_records('user', array('suspended'=>1)));
        $this->assertEquals(0, $DB->count_records('user', array('deleted'=>1)));
        $this->assertEquals(2, $DB->count_records('role_assignments'));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$creatorrole->id)));


        set_config('removeuser', AUTH_REMOVEUSER_FULLDELETE, 'auth/ldap');

        /** @var auth_plugin_ldap $auth */
        $auth = get_auth_plugin('ldap');

        $this->delete_ldap_user($connection, $topdn, 1);

        ob_start();
        $auth->sync_users(true);
        ob_end_clean();

        $this->assertEquals(5, $DB->count_records('user', array('auth'=>'ldap')));
        $this->assertEquals(0, $DB->count_records('user', array('username'=>'username1')));
        $this->assertEquals(0, $DB->count_records('user', array('suspended'=>1)));
        $this->assertEquals(1, $DB->count_records('user', array('deleted'=>1)));
        $this->assertEquals(1, $DB->count_records('role_assignments'));
        $this->assertEquals(1, $DB->count_records('role_assignments', array('roleid'=>$creatorrole->id)));

        $this->create_ldap_user($connection, $topdn, 1);

        ob_start();
        $auth->sync_users(true);
        ob_end_clean();

        $this->assertEquals(6, $DB->count_records('user', array('auth'=>'ldap')));
        $this->assertEquals(1, $DB->count_records('user', array('username'=>'username1')));
        $this->assertEquals(0, $DB->count_records('user', array('suspended'=>1)));
        $this->assertEquals(1, $DB->count_records('user', array('deleted'=>1)));
        $this->assertEquals(2, $DB->count_records('role_assignments'));
        $this->assertEquals(2, $DB->count_records('role_assignments', array('roleid'=>$creatorrole->id)));


        $this->recursive_delete($connection, TEST_AUTH_LDAP_DOMAIN, 'dc=moodletest');
        ldap_close($connection);
    }

    protected function create_ldap_user($connection, $topdn, $i) {
        $o = array();
        $o['objectClass']   = array('inetOrgPerson', 'organizationalPerson', 'person', 'posixAccount');
        $o['cn']            = 'username'.$i;
        $o['sn']            = 'Lastname'.$i;
        $o['givenName']     = 'Firstname'.$i;
        $o['uid']           = $o['cn'];
        $o['uidnumber']     = 2000+$i;
        $o['gidNumber']     = 1000+$i;
        $o['homeDirectory'] = '/';
        $o['mail']          = 'user'.$i.'@example.com';
        $o['userPassword']  = 'pass'.$i;
        ldap_add($connection, 'cn='.$o['cn'].',ou=users,'.$topdn, $o);
    }

    protected function delete_ldap_user($connection, $topdn, $i) {
        ldap_delete($connection, 'cn=username'.$i.',ou=users,'.$topdn);
    }

    protected function enable_plugin() {
        $auths = get_enabled_auth_plugins(true);
        if (!in_array('ldap', $auths)) {
            $auths[] = 'ldap';

        }
        set_config('auth', implode(',', $auths));
    }

    protected function recursive_delete($connection, $dn, $filter) {
        if ($res = ldap_list($connection, $dn, $filter, array('dn'))) {
            $info = ldap_get_entries($connection, $res);
            ldap_free_result($res);
            if ($info['count'] > 0) {
                if ($res = ldap_search($connection, "$filter,$dn", 'cn=*', array('dn'))) {
                    $info = ldap_get_entries($connection, $res);
                    ldap_free_result($res);
                    foreach ($info as $i) {
                        if (isset($i['dn'])) {
                            ldap_delete($connection, $i['dn']);
                        }
                    }
                }
                if ($res = ldap_search($connection, "$filter,$dn", 'ou=*', array('dn'))) {
                    $info = ldap_get_entries($connection, $res);
                    ldap_free_result($res);
                    foreach ($info as $i) {
                        if (isset($i['dn']) and $info[0]['dn'] != $i['dn']) {
                            ldap_delete($connection, $i['dn']);
                        }
                    }
                }
                ldap_delete($connection, "$filter,$dn");
            }
        }
    }
}
