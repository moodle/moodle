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

namespace core;

/**
 * Unit tests for sessionlib.php file.
 *
 * @package   core
 * @category  test
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sessionlib_test extends \advanced_testcase {
    public function test_cron_setup_user() {
        global $PAGE, $USER, $SESSION, $SITE, $CFG;
        $this->resetAfterTest();

        // NOTE: this function contains some static caches, let's reset first.
        cron_setup_user('reset');

        $admin = get_admin();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        cron_setup_user();
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertSame($CFG->timezone, $USER->timezone);
        $this->assertSame('', $USER->lang);
        $this->assertSame('', $USER->theme);
        $SESSION->test1 = true;
        $adminsession = $SESSION;
        $adminuser = $USER;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user(null, $course);
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($course->id));
        $this->assertSame($adminsession, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertObjectNotHasAttribute('test1', $SESSION);
        $this->assertEmpty((array)$SESSION);
        $usersession1 = $SESSION;
        $SESSION->test2 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertSame($usersession1, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user($user2);
        $this->assertSame($user2->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertEmpty((array)$SESSION);
        $usersession2 = $SESSION;
        $usersession2->test3 = true;
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user($user2, $course);
        $this->assertSame($user2->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($course->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertSame($usersession2, $SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user($user1);
        $this->assertSame($user1->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($usersession1, $SESSION);
        $this->assertEmpty((array)$SESSION);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user();
        $this->assertSame($admin->id, $USER->id);
        $this->assertSame($PAGE->context, \context_course::instance($SITE->id));
        $this->assertSame($adminsession, $SESSION);
        $this->assertSame($adminuser, $USER);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user('reset');
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        cron_setup_user();
        $this->assertNotSame($adminsession, $SESSION);
        $this->assertNotSame($adminuser, $USER);
        $this->assertSame($GLOBALS['SESSION'], $_SESSION['SESSION']);
        $this->assertSame($GLOBALS['SESSION'], $SESSION);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);
    }

    /**
     * Test provided for secure cookie
     *
     * @return array of config and secure result
     */
    public function moodle_cookie_secure_provider() {
        return array(
            array(
                // Non ssl, not set.
                'config' => array(
                    'wwwroot'       => 'http://example.com',
                    'sslproxy'      => null,
                    'cookiesecure'  => null,
                ),
                'secure' => false,
            ),
            array(
                // Non ssl, off and ignored.
                'config' => array(
                    'wwwroot'       => 'http://example.com',
                    'sslproxy'      => null,
                    'cookiesecure'  => false,
                ),
                'secure' => false,
            ),
            array(
                // Non ssl, on and ignored.
                'config' => array(
                    'wwwroot'       => 'http://example.com',
                    'sslproxy'      => null,
                    'cookiesecure'  => true,
                ),
                'secure' => false,
            ),
            array(
                // SSL via proxy, off.
                'config' => array(
                    'wwwroot'       => 'http://example.com',
                    'sslproxy'      => true,
                    'cookiesecure'  => false,
                ),
                'secure' => false,
            ),
            array(
                // SSL via proxy, on.
                'config' => array(
                    'wwwroot'       => 'http://example.com',
                    'sslproxy'      => true,
                    'cookiesecure'  => true,
                ),
                'secure' => true,
            ),
            array(
                // SSL and off.
                'config' => array(
                    'wwwroot'       => 'https://example.com',
                    'sslproxy'      => null,
                    'cookiesecure'  => false,
                ),
                'secure' => false,
            ),
            array(
                // SSL and on.
                'config' => array(
                    'wwwroot'       => 'https://example.com',
                    'sslproxy'      => null,
                    'cookiesecure'  => true,
                ),
                'secure' => true,
            ),
        );
    }

    /**
     * Test for secure cookie
     *
     * @dataProvider moodle_cookie_secure_provider
     *
     * @param array $config Array of key value config settings
     * @param bool $secure Wether cookies should be secure or not
     */
    public function test_is_moodle_cookie_secure($config, $secure) {
        global $CFG;
        $this->resetAfterTest();
        foreach ($config as $key => $value) {
            $CFG->$key = $value;
        }
        $this->assertEquals($secure, is_moodle_cookie_secure());
    }

    public function test_sesskey() {
        global $USER;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        \core\session\manager::init_empty_session();
        $this->assertObjectNotHasAttribute('sesskey', $USER);

        $sesskey = sesskey();
        $this->assertNotEmpty($sesskey);
        $this->assertSame($sesskey, $USER->sesskey);
        $this->assertSame($GLOBALS['USER'], $_SESSION['USER']);
        $this->assertSame($GLOBALS['USER'], $USER);

        $this->assertSame($sesskey, sesskey());

        // Test incomplete session init - the sesskeys should return random values.
        $_SESSION = array();
        unset($GLOBALS['USER']);
        unset($GLOBALS['SESSION']);

        $this->assertFalse(sesskey());
        $this->assertArrayNotHasKey('USER', $GLOBALS);
        $this->assertFalse(sesskey());
    }

    public function test_confirm_sesskey() {
        $this->resetAfterTest();

        $sesskey = sesskey();

        try {
            confirm_sesskey();
            $this->fail('Exception expected when sesskey not present');
        } catch (\moodle_exception $e) {
            $this->assertSame('missingparam', $e->errorcode);
        }

        $this->assertTrue(confirm_sesskey($sesskey));
        $this->assertFalse(confirm_sesskey('blahblah'));

        $_GET['sesskey'] = $sesskey;
        $this->assertTrue(confirm_sesskey());

        $_GET['sesskey'] = 'blah';
        $this->assertFalse(confirm_sesskey());
    }

    public function test_require_sesskey() {
        $this->resetAfterTest();

        $sesskey = sesskey();

        try {
            require_sesskey();
            $this->fail('Exception expected when sesskey not present');
        } catch (\moodle_exception $e) {
            $this->assertSame('missingparam', $e->errorcode);
        }

        $_GET['sesskey'] = $sesskey;
        require_sesskey();

        $_GET['sesskey'] = 'blah';
        try {
            require_sesskey();
            $this->fail('Exception expected when sesskey not incorrect');
        } catch (\moodle_exception $e) {
            $this->assertSame('invalidsesskey', $e->errorcode);
        }
    }
}
