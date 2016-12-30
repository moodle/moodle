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
 * ldap tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  Damyon Wiese, Iñaki Arenaza 2014
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/ldaplib.php');

class core_ldaplib_testcase extends advanced_testcase {

    public function test_ldap_addslashes() {
        // See http://tools.ietf.org/html/rfc4514#section-5.2 if you want
        // to add additional tests.

        $tests = array(
            array (
                'test' => 'Simplest',
                'expected' => 'Simplest',
            ),
            array (
                'test' => 'Simple case',
                'expected' => 'Simple\\20case',
            ),
            array (
                'test' => 'Medium ‒ case',
                'expected' => 'Medium\\20‒\\20case',
            ),
            array (
                'test' => '#Harder+case#',
                'expected' => '\\23Harder\\2bcase\\23',
            ),
            array (
                'test' => ' Harder (and); harder case ',
                'expected' => '\\20Harder\\20(and)\\3b\\20harder\\20case\\20',
            ),
            array (
                'test' => 'Really \\0 (hard) case!\\',
                'expected' => 'Really\\20\\5c0\\20(hard)\\20case!\\5c',
            ),
            array (
                'test' => 'James "Jim" = Smith, III',
                'expected' => 'James\\20\\22Jim\22\\20\\3d\\20Smith\\2c\\20III',
            ),
            array (
                'test' => '  <jsmith@example.com> ',
                'expected' => '\\20\\20\\3cjsmith@example.com\\3e\\20',
            ),
        );


        foreach ($tests as $test) {
            $this->assertSame($test['expected'], ldap_addslashes($test['test']));
        }
    }

    public function test_ldap_stripslashes() {
        // See http://tools.ietf.org/html/rfc4514#section-5.2 if you want
        // to add additional tests.

        // IMPORTANT NOTICE: While ldap_addslashes() only produces one
        // of the two defined ways of escaping/quoting (the ESC HEX
        // HEX way defined in the grammar in Section 3 of RFC-4514)
        // ldap_stripslashes() has to deal with both of them. So in
        // addition to testing the same strings we test in
        // test_ldap_stripslashes(), we need to also test strings
        // using the second method.

        $tests = array(
            array (
                'test' => 'Simplest',
                'expected' => 'Simplest',
            ),
            array (
                'test' => 'Simple\\20case',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Simple\\ case',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Simple\\ \\63\\61\\73\\65',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Medium\\ ‒\\ case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => 'Medium\\20‒\\20case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => 'Medium\\20\\E2\\80\\92\\20case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => '\\23Harder\\2bcase\\23',
                'expected' => '#Harder+case#',
            ),
            array (
                'test' => '\\#Harder\\+case\\#',
                'expected' => '#Harder+case#',
            ),
            array (
                'test' => '\\20Harder\\20(and)\\3b\\20harder\\20case\\20',
                'expected' => ' Harder (and); harder case ',
            ),
            array (
                'test' => '\\ Harder\\ (and)\\;\\ harder\\ case\\ ',
                'expected' => ' Harder (and); harder case ',
            ),
            array (
                'test' => 'Really\\20\\5c0\\20(hard)\\20case!\\5c',
                'expected' => 'Really \\0 (hard) case!\\',
            ),
            array (
                'test' => 'Really\\ \\\\0\\ (hard)\\ case!\\\\',
                'expected' => 'Really \\0 (hard) case!\\',
            ),
            array (
                'test' => 'James\\20\\22Jim\\22\\20\\3d\\20Smith\\2c\\20III',
                'expected' => 'James "Jim" = Smith, III',
            ),
            array (
                'test' => 'James\\ \\"Jim\\" \\= Smith\\, III',
                'expected' => 'James "Jim" = Smith, III',
            ),
            array (
                'test' => '\\20\\20\\3cjsmith@example.com\\3e\\20',
                'expected' => '  <jsmith@example.com> ',
            ),
            array (
                'test' => '\\ \\<jsmith@example.com\\>\\ ',
                'expected' => ' <jsmith@example.com> ',
            ),
            array (
                'test' => 'Lu\\C4\\8Di\\C4\\87',
                'expected' => 'Lučić',
            ),
        );

        foreach ($tests as $test) {
            $this->assertSame($test['expected'], ldap_stripslashes($test['test']));
        }
    }

    /**
     * Tests for ldap_normalise_objectclass.
     *
     * @dataProvider ldap_normalise_objectclass_provider
     * @param array $args Arguments passed to ldap_normalise_objectclass
     * @param string $expected The expected objectclass filter
     */
    public function test_ldap_normalise_objectclass($args, $expected) {
        $this->assertEquals($expected, call_user_func_array('ldap_normalise_objectclass', $args));
    }

    /**
     * Data provider for the test_ldap_normalise_objectclass testcase.
     *
     * @return array of testcases.
     */
    public function ldap_normalise_objectclass_provider() {
        return array(
            'Empty value' => array(
                array(null),
                '(objectClass=*)',
            ),
            'Empty value with different default' => array(
                array(null, 'lion'),
                '(objectClass=lion)',
            ),
            'Supplied unwrapped objectClass' => array(
                array('objectClass=tiger'),
                '(objectClass=tiger)',
            ),
            'Supplied string value' => array(
                array('leopard'),
                '(objectClass=leopard)',
            ),
            'Supplied complex' => array(
                array('(&(objectClass=cheetah)(enabledMoodleUser=1))'),
                '(&(objectClass=cheetah)(enabledMoodleUser=1))',
            ),
        );
    }

    /**
     * Tests for ldap_get_entries_moodle.
     *
     * NOTE: in order to execute this test you need to set up OpenLDAP server with core,
     *       cosine, nis and internet schemas and add configuration constants to
     *       config.php or phpunit.xml configuration file.  The bind users *needs*
     *       permissions to create objects in the LDAP server, under the bind domain.
     *
     * define('TEST_LDAPLIB_HOST_URL', 'ldap://127.0.0.1');
     * define('TEST_LDAPLIB_BIND_DN', 'cn=someuser,dc=example,dc=local');
     * define('TEST_LDAPLIB_BIND_PW', 'somepassword');
     * define('TEST_LDAPLIB_DOMAIN',  'dc=example,dc=local');
     *
     */
    public function test_ldap_get_entries_moodle() {
        $this->resetAfterTest();

        if (!defined('TEST_LDAPLIB_HOST_URL') or !defined('TEST_LDAPLIB_BIND_DN') or
                !defined('TEST_LDAPLIB_BIND_PW') or !defined('TEST_LDAPLIB_DOMAIN')) {
            $this->markTestSkipped('External LDAP test server not configured.');
        }

        // Make sure we can connect the server.
        $debuginfo = '';
        if (!$connection = ldap_connect_moodle(TEST_LDAPLIB_HOST_URL, 3, 'rfc2307', TEST_LDAPLIB_BIND_DN,
                                               TEST_LDAPLIB_BIND_PW, LDAP_DEREF_NEVER, $debuginfo, false)) {
            $this->markTestSkipped('Cannot connect to LDAP test server: '.$debuginfo);
        }

        // Create new empty test container.
        if (!($containerdn = $this->create_test_container($connection, 'moodletest'))) {
            $this->markTestSkipped('Can not create test LDAP container.');
        }

        // Add all the test objects.
        $testobjects = $this->get_ldap_get_entries_moodle_test_objects();
        if (!$this->add_test_objects($connection, $containerdn, $testobjects)) {
            $this->markTestSkipped('Can not create LDAP test objects.');
        }

        // Now query about them and compare results.
        foreach ($testobjects as $object) {
            $dn = $this->get_object_dn($object, $containerdn);
            $filter = $object['query']['filter'];
            $attributes = $object['query']['attributes'];

            $sr = ldap_read($connection, $dn, $filter, $attributes);
            if (!$sr) {
                $this->markTestSkipped('Cannot retrieve test objects from LDAP test server.');
            }

            $entries = ldap_get_entries_moodle($connection, $sr);
            $actual = array_keys($entries[0]);
            $expected = $object['expected'];

            // We need to sort both arrays to be able to compare them, as the LDAP server
            // might return attributes in any order.
            sort($expected);
            sort($actual);
            $this->assertEquals($expected, $actual);
        }

        // Clean up test objects and container.
        $this->remove_test_objects($connection, $containerdn, $testobjects);
        $this->remove_test_container($connection, $containerdn);
    }

    /**
     * Provide the array of test objects for the ldap_get_entries_moodle test case.
     *
     * @return array of test objects
     */
    protected function get_ldap_get_entries_moodle_test_objects() {
        $testobjects = array(
            // Test object 1.
            array(
                // Add/remove this object to LDAP directory? There are existing standard LDAP
                // objects that we might want to test, but that we should'nt add/remove ourselves.
                'addremove' => true,
                // Relative (to test container) or absolute distinguished name (DN).
                'relativedn' => true,
                // Distinguished name for this object (interpretation depends on 'relativedn').
                'dn' => 'cn=test1',
                // Values to add to LDAP directory.
                'values' => array(
                    'objectClass' => array('inetOrgPerson', 'organizationalPerson', 'person', 'posixAccount'),
                    'cn' => 'test1',  // We don't care about the actual values, as long as they are unique.
                    'sn' => 'test1',
                    'givenName' => 'test1',
                    'uid' => 'test1',
                    'uidNumber' => '20001',  // Start from 20000, then add test number.
                    'gidNumber' => '20001',  // Start from 20000, then add test number.
                    'homeDirectory' => '/',
                    'userPassword' => '*',
                ),
                // Attributes to query the object for.
                'query' => array(
                    'filter' => '(objectClass=posixAccount)',
                    'attributes' => array(
                        'cn',
                        'sn',
                        'givenName',
                        'uid',
                        'uidNumber',
                        'gidNumber',
                        'homeDirectory',
                        'userPassword'
                    ),
                ),
                // Expected values for the queried attributes' names.
                'expected' => array(
                    'cn',
                    'sn',
                    'givenname',
                    'uid',
                    'uidnumber',
                    'gidnumber',
                    'homedirectory',
                    'userpassword'
                ),
            ),
            // Test object 2.
            array(
                'addremove' => true,
                'relativedn' => true,
                'dn' => 'cn=group2',
                'values' => array(
                    'objectClass' => array('top', 'posixGroup'),
                    'cn' => 'group2',  // We don't care about the actual values, as long as they are unique.
                    'gidNumber' => '20002',  // Start from 20000, then add test number.
                    'memberUid' => '20002',  // Start from 20000, then add test number.
                ),
                'query' => array(
                    'filter' => '(objectClass=posixGroup)',
                    'attributes' => array(
                        'cn',
                        'gidNumber',
                        'memberUid'
                    ),
                ),
                'expected' => array(
                    'cn',
                    'gidnumber',
                    'memberuid'
                ),
            ),
            // Test object 3.
            array(
                'addremove' => false,
                'relativedn' => false,
                'dn' => '',  // To query the RootDSE, we must specify the empty string as the absolute DN.
                'values' => array(
                ),
                'query' => array(
                    'filter' => '(objectClass=*)',
                    'attributes' => array(
                        'supportedControl',
                        'namingContexts'
                    ),
                ),
                'expected' => array(
                    'supportedcontrol',
                    'namingcontexts'
                ),
            ),
        );

        return $testobjects;
    }

    /**
     * Create a new container in the LDAP domain, to hold the test objects. The
     * container is created as a domain component (dc) + organizational unit (ou) object.
     *
     * @param object $connection Valid LDAP connection
     * @param string $container Name of the test container to create.
     *
     * @return string or false Distinguished name for the created container, or false on error.
     */
    protected function create_test_container($connection, $container) {
        $object = array();
        $object['objectClass'] = array('dcObject', 'organizationalUnit');
        $object['dc'] = $container;
        $object['ou'] = $container;
        $containerdn = 'dc='.$container.','.TEST_LDAPLIB_DOMAIN;
        if (!ldap_add($connection, $containerdn, $object)) {
            return false;
        }
        return $containerdn;
    }

    /**
     * Remove the container in the LDAP domain root that holds the test objects. The container
     * *must* be empty before trying to remove it. Otherwise this function fails.
     *
     * @param object $connection Valid LDAP connection
     * @param string $containerdn The distinguished of the container to remove.
     */
    protected function remove_test_container($connection, $containerdn) {
        ldap_delete($connection, $containerdn);
    }

    /**
     * Add the test objects to the test container.
     *
     * @param resource $connection Valid LDAP connection
     * @param string $containerdn The distinguished name of the container for the created objects.
     * @param array $testobjects Array of the tests objects to create. The structure of
     *              the array elements *must* follow the structure of the value returned
     *              by ldap_get_entries_moodle_test_objects() member function.
     *
     * @return boolean True on success, false otherwise.
     */
    protected function add_test_objects($connection, $containerdn, $testobjects) {
        foreach ($testobjects as $object) {
            if ($object['addremove'] !== true) {
                continue;
            }
            $dn = $this->get_object_dn($object, $containerdn);
            $entry = $object['values'];
            if (!ldap_add($connection, $dn, $entry)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Remove the test objects from the test container.
     *
     * @param resource $connection Valid LDAP connection
     * @param string $containerdn The distinguished name of the container for the objects to remove.
     * @param array $testobjects Array of the tests objects to create. The structure of
     *              the array elements *must* follow the structure of the value returned
     *              by ldap_get_entries_moodle_test_objects() member function.
     *
     */
    protected function remove_test_objects($connection, $containerdn, $testobjects) {
        foreach ($testobjects as $object) {
            if ($object['addremove'] !== true) {
                continue;
            }
            $dn = $this->get_object_dn($object, $containerdn);
            ldap_delete($connection, $dn);
        }
    }

    /**
     * Get the distinguished name (DN) for a given object.
     *
     * @param object $object The LDAP object to calculate the DN for.
     * @param string $containerdn The DN of the container to use for objects with relative DNs.
     *
     * @return string The calculated DN.
     */
    protected function get_object_dn($object, $containerdn) {
        if ($object['relativedn']) {
            $dn = $object['dn'].','.$containerdn;
        } else {
            $dn = $object['dn'];
        }
        return $dn;
    }
}
