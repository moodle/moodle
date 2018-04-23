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
 * Unit Tests for sitepolicy manager
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Unit Tests for sitepolicy manager
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sitepolicy_test extends advanced_testcase {


    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_handler_classname() behaviour.
     */
    public function test_get_handler_classname() {
        global $CFG;
        $this->resetAfterTest(true);

        $manager = $this->get_mock_manager_with_handler();

        // If no handler is specified, then we should get the default one.
        $CFG->sitepolicyhandler = '';
        $this->assertEquals($manager->get_handler_classname(), \core_privacy\local\sitepolicy\default_handler::class);

        // If non-existing handler is specified, we should get the default one too.
        $CFG->sitepolicyhandler = 'non_existing_plugin_which_i_really_hope_will_never_exist';
        $this->assertEquals($manager->get_handler_classname(), \core_privacy\local\sitepolicy\default_handler::class);

        // If the defined handler is among known handlers, we should get its class name.
        $CFG->sitepolicyhandler = 'testtool_testhandler';
        $this->assertEquals($manager->get_handler_classname(), 'mock_sitepolicy_handler');
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::is_defined()
     */
    public function test_is_defined() {
        global $CFG;
        $this->resetAfterTest(true);

        $manager = new \core_privacy\local\sitepolicy\manager();

        $this->assertFalse($manager->is_defined(true));
        $this->assertFalse($manager->is_defined(false));

        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';
        $this->assertFalse($manager->is_defined(true));
        $this->assertTrue($manager->is_defined(false));

        $CFG->sitepolicyguest = 'http://example.com/sitepolicyguest.html';
        $this->assertTrue($manager->is_defined(true));
        $this->assertTrue($manager->is_defined(false));

        $CFG->sitepolicy = null;
        $this->assertTrue($manager->is_defined(true));
        $this->assertFalse($manager->is_defined(false));

        // When non existing plugin is set as $CFG->sitepolicyhandler, assume that $CFG->sitepolicy* are all not set.
        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';
        $CFG->sitepolicyguest = 'http://example.com/sitepolicyguest.html';
        $CFG->sitepolicyhandler = 'non_existing_plugin_which_i_really_hope_will_never_exist';
        $this->assertFalse($manager->is_defined(true));
        $this->assertFalse($manager->is_defined(false));
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url()
     */
    public function test_get_redirect_url() {
        global $CFG;
        $this->resetAfterTest(true);

        $manager = new \core_privacy\local\sitepolicy\manager();

        $this->assertEquals(null, $manager->get_redirect_url(true));
        $this->assertEquals(null, $manager->get_redirect_url(false));

        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';
        $this->assertEquals(null, $manager->get_redirect_url(true));
        $this->assertEquals($CFG->wwwroot.'/user/policy.php', $manager->get_redirect_url(false)->out(false));

        $CFG->sitepolicyguest = 'http://example.com/sitepolicyguest.html';
        $this->assertEquals($CFG->wwwroot.'/user/policy.php', $manager->get_redirect_url(true)->out(false));
        $this->assertEquals($CFG->wwwroot.'/user/policy.php', $manager->get_redirect_url(false)->out(false));

        $CFG->sitepolicy = null;
        $this->assertEquals($CFG->wwwroot.'/user/policy.php', $manager->get_redirect_url(true)->out(false));
        $this->assertEquals(null, $manager->get_redirect_url(false));

        // When non existing plugin is set as $CFG->sitepolicyhandler, assume that $CFG->sitepolicy* are all not set.
        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';
        $CFG->sitepolicyguest = 'http://example.com/sitepolicyguest.html';
        $CFG->sitepolicyhandler = 'non_existing_plugin_which_i_really_hope_will_never_exist';
        $this->assertEquals(null, $manager->get_redirect_url(true));
        $this->assertEquals(null, $manager->get_redirect_url(false));
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url()
     */
    public function test_get_embed_url() {
        global $CFG;
        $this->resetAfterTest(true);

        $manager = new \core_privacy\local\sitepolicy\manager();

        $this->assertEquals(null, $manager->get_embed_url(true));
        $this->assertEquals(null, $manager->get_embed_url(false));

        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';
        $this->assertEquals(null, $manager->get_embed_url(true));
        $this->assertEquals($CFG->sitepolicy, $manager->get_embed_url(false)->out(false));

        $CFG->sitepolicyguest = 'http://example.com/sitepolicyguest.html';
        $this->assertEquals($CFG->sitepolicyguest, $manager->get_embed_url(true)->out(false));
        $this->assertEquals($CFG->sitepolicy, $manager->get_embed_url(false)->out(false));

        $CFG->sitepolicy = null;
        $this->assertEquals($CFG->sitepolicyguest, $manager->get_embed_url(true)->out(false));
        $this->assertEquals(null, $manager->get_embed_url(false));

        // When non existing plugin is set as $CFG->sitepolicyhandler, assume that $CFG->sitepolicy* are all not set.
        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';
        $CFG->sitepolicyguest = 'http://example.com/sitepolicyguest.html';
        $CFG->sitepolicyhandler = 'non_existing_plugin_which_i_really_hope_will_never_exist';
        $this->assertEquals(null, $manager->get_embed_url(true));
        $this->assertEquals(null, $manager->get_embed_url(false));
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url()
     */
    public function test_accept() {
        global $CFG, $USER, $DB;
        $this->resetAfterTest(true);

        $manager = new \core_privacy\local\sitepolicy\manager();

        // No site policy.
        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);
        $this->assertFalse($manager->accept());
        $this->assertEquals(0, $USER->policyagreed);

        // With site policy.
        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';

        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $this->assertEquals(0, $USER->policyagreed);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $USER->id]));
        $this->assertTrue($manager->accept());
        $this->assertEquals(1, $USER->policyagreed);
        $this->assertEquals(1, $DB->get_field('user', 'policyagreed', ['id' => $USER->id]));

        // When non existing plugin is set as $CFG->sitepolicyhandler, assume that $CFG->sitepolicy* are all not set.
        $CFG->sitepolicy = 'http://example.com/sitepolicy.html';
        $CFG->sitepolicyhandler = 'non_existing_plugin_which_i_really_hope_will_never_exist';
        $user3 = $this->getDataGenerator()->create_user();
        $this->setUser($user3);
        $this->assertEquals(0, $USER->policyagreed);
        $this->assertFalse($manager->accept());
        $this->assertEquals(0, $USER->policyagreed);
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url() for guests
     */
    public function test_accept_guests() {
        global $CFG, $USER, $DB;
        $this->resetAfterTest(true);

        $manager = new \core_privacy\local\sitepolicy\manager();

        $this->setGuestUser();

        // No site policy.
        $this->assertFalse($manager->accept());
        $this->assertEquals(0, $USER->policyagreed);

        // With site policy.
        $CFG->sitepolicyguest = 'http://example.com/sitepolicy.html';

        $this->assertEquals(0, $USER->policyagreed);
        $this->assertTrue($manager->accept());
        $this->assertEquals(1, $USER->policyagreed);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $USER->id]));

        // When non existing plugin is set as $CFG->sitepolicyhandler, assume that $CFG->sitepolicy* are all not set.
        $USER->policyagreed = 0; // Reset.
        $CFG->sitepolicyguest = 'http://example.com/sitepolicyguest.html';
        $CFG->sitepolicyhandler = 'non_existing_plugin_which_i_really_hope_will_never_exist';
        $this->assertFalse($manager->accept());
        $this->assertEquals(0, $USER->policyagreed);
    }

    /**
     * Helper to spoof the results of the internal function get_all_handlers, allowing mock handler to be tested.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function get_mock_manager_with_handler() {
        global $CFG;
        require_once($CFG->dirroot.'/privacy/tests/fixtures/mock_sitepolicy_handler.php');

        $mock = $this->getMockBuilder(\core_privacy\local\sitepolicy\manager::class)
            ->setMethods(['get_all_handlers'])
            ->getMock();
        $mock->expects($this->any())
            ->method('get_all_handlers')
            ->will($this->returnValue(['testtool_testhandler' => 'mock_sitepolicy_handler']));
        return $mock;
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::is_defined() with a handler
     */
    public function test_is_defined_with_handler() {
        global $CFG;
        $this->resetAfterTest(true);
        $CFG->sitepolicyhandler = 'testtool_testhandler';
        $manager = $this->get_mock_manager_with_handler();
        $this->assertTrue($manager->is_defined(true));
        $this->assertTrue($manager->is_defined(false));
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url() with a handler
     */
    public function test_get_redirect_url_with_handler() {
        global $CFG;
        $this->resetAfterTest(true);

        $CFG->sitepolicyhandler = 'testtool_testhandler';
        $manager = $this->get_mock_manager_with_handler();

        $this->assertEquals('http://example.com/policy.php', $manager->get_redirect_url(true)->out(false));
        $this->assertEquals('http://example.com/policy.php', $manager->get_redirect_url(false)->out(false));
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url() with a handler
     */
    public function test_get_embed_url_with_handler() {
        global $CFG;
        $this->resetAfterTest(true);

        $CFG->sitepolicyhandler = 'testtool_testhandler';
        $manager = $this->get_mock_manager_with_handler();

        $this->assertEquals('http://example.com/view.htm', $manager->get_embed_url(true)->out(false));
        $this->assertEquals('http://example.com/view.htm', $manager->get_embed_url(false)->out(false));
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url() with a handler
     */
    public function test_accept_with_handler() {
        global $CFG, $USER, $DB;
        $this->resetAfterTest(true);

        $CFG->sitepolicyhandler = 'testtool_testhandler';
        $manager = $this->get_mock_manager_with_handler();

        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $this->assertEquals(0, $USER->policyagreed);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $USER->id]));
        $this->assertTrue($manager->accept());
        $this->assertEquals(2, $USER->policyagreed);
        $this->assertEquals(2, $DB->get_field('user', 'policyagreed', ['id' => $USER->id]));
    }

    /**
     * Tests for \core_privacy\local\sitepolicy\manager::get_redirect_url() for guests with a handler
     */
    public function test_accept_guests_with_handler() {
        global $CFG, $USER, $DB;
        $this->resetAfterTest(true);

        $CFG->sitepolicyhandler = 'testtool_testhandler';
        $manager = $this->get_mock_manager_with_handler();

        $this->setGuestUser();

        $this->assertEquals(0, $USER->policyagreed);
        $this->assertTrue($manager->accept());
        $this->assertEquals(2, $USER->policyagreed);
        $this->assertEquals(0, $DB->get_field('user', 'policyagreed', ['id' => $USER->id]));
    }

    /**
     * Test behaviour of \core_privacy\local\sitepolicy\manager with a handler not implementing all required methods.
     */
    public function test_incomplete_handler() {
        global $CFG;
        require_once($CFG->dirroot.'/privacy/tests/fixtures/mock_incomplete_sitepolicy_handler.php');
        $this->resetAfterTest(true);

        $CFG->sitepolicyhandler = 'testtool_incompletehandler';

        $manager = $this->getMockBuilder(\core_privacy\local\sitepolicy\manager::class)
            ->setMethods(['get_all_handlers'])
            ->getMock();
        $manager->expects($this->any())
            ->method('get_all_handlers')
            ->will($this->returnValue(['testtool_incompletehandler' => 'mock_incomplete_sitepolicy_handler']));

        // This works because the handler implements get_redirect_url().
        $this->assertEquals('http://example.com/policy.php', $manager->get_redirect_url()->out());

        // This must inform them developer that the handler does not implement a method.
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Method get_embed_url() not implemented by the handler');
        $manager->get_embed_url();
    }
}

/**
 * Mock handler for site policies
 *
 * @package    core_privacy
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler extends \core_privacy\local\sitepolicy\handler {

    /**
     * Checks if the site has site policy defined
     *
     * @param bool $forguests
     * @return bool
     */
    public static function is_defined($forguests = false) {
        return true;
    }

    /**
     * Returns URL to redirect user to when user needs to agree to site policy
     *
     * This is a regular interactive page for web users. It should have normal Moodle header/footers, it should
     * allow user to view policies and accept them.
     *
     * @param bool $forguests
     * @return moodle_url|null (returns null if site policy is not defined)
     */
    public static function get_redirect_url($forguests = false) {
        return 'http://example.com/policy.php';
    }

    /**
     * Returns URL of the site policy that needs to be displayed to the user (inside iframe or to use in WS such as mobile app)
     *
     * This page should not have any header/footer, it does not also have any buttons/checkboxes. The caller needs to implement
     * the "Accept" button and call {@link self::accept()} on completion.
     *
     * @param bool $forguests
     * @return moodle_url|null
     */
    public static function get_embed_url($forguests = false) {
        return 'http://example.com/view.htm';
    }

    /**
     * Accept site policy for the current user
     *
     * @return bool - false if sitepolicy not defined, user is not logged in or user has already agreed to site policy;
     *     true - if we have successfully marked the user as agreed to the site policy
     */
    public static function accept() {
        global $USER, $DB;
        // Accepts policy on behalf of the current user. We set it to 2 here to check that this callback was called.
        $USER->policyagreed = 2;
        if (!isguestuser()) {
            $DB->update_record('user', ['policyagreed' => 2, 'id' => $USER->id]);
        }
        return true;
    }
}
