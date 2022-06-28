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
 * Helper functions for PHPUnit tests.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../locallib.php');

/**
 * Phpunit Tests for locallib
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_moodleoverflow_locallib_testcase extends advanced_testcase {

    public function setUp(): void {
        \mod_moodleoverflow\subscriptions::reset_moodleoverflow_cache();
    }

    public function tearDown(): void {
        \mod_moodleoverflow\subscriptions::reset_moodleoverflow_cache();
    }

    /**
     * Test subscription using automatic subscription on create.
     */
    public function test_moodleoverflow_auto_subscribe_on_create() {
        global $DB;

        $this->resetAfterTest();

        $usercount = 5;
        $course = $this->getDataGenerator()->create_course();
        $users = array();

        for ($i = 0; $i < $usercount; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $users[] = $user;
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }

        $options = array('course' => $course->id, 'forcesubscribe' => MOODLEOVERFLOW_INITIALSUBSCRIBE); // Automatic Subscription.
        $mo = $this->getDataGenerator()->create_module('moodleoverflow', $options);

        // Get the module context.
        $cm = $DB->get_record('course_modules', array('id' => $mo->cmid));
        $context = \context_module::instance($cm->id);

        $result = \mod_moodleoverflow\subscriptions::get_subscribed_users($mo, $context);
        $this->assertEquals($usercount, count($result));
        foreach ($users as $user) {
            $this->assertTrue(\mod_moodleoverflow\subscriptions::is_subscribed($user->id, $mo));
        }
    }

    /**
     * Test subscription using forced subscription on create.
     */
    public function test_moodleoverflow_forced_subscribe_on_create() {
        global $DB;

        $this->resetAfterTest();

        $usercount = 5;
        $course = $this->getDataGenerator()->create_course();
        $users = array();

        for ($i = 0; $i < $usercount; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $users[] = $user;
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }

        $options = array('course' => $course->id, 'forcesubscribe' => MOODLEOVERFLOW_FORCESUBSCRIBE);
        $mo = $this->getDataGenerator()->create_module('moodleoverflow', $options);

        $cm = $DB->get_record('course_modules', array('id' => $mo->cmid));
        $context = \context_module::instance($cm->id);

        $result = \mod_moodleoverflow\subscriptions::get_subscribed_users($mo, $context);
        $this->assertEquals($usercount, count($result));
        foreach ($users as $user) {
            $this->assertTrue(\mod_moodleoverflow\subscriptions::is_subscribed($user->id, $mo));
        }
    }

    /**
     * Test subscription using optional subscription on create.
     */
    public function test_moodleoverflow_optional_subscribe_on_create() {
        global $DB;

        $this->resetAfterTest();

        $usercount = 5;
        $course = $this->getDataGenerator()->create_course();
        $users = array();

        for ($i = 0; $i < $usercount; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $users[] = $user;
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }

        $options = array('course' => $course->id, 'forcesubscribe' => MOODLEOVERFLOW_CHOOSESUBSCRIBE); // Subscription optional.
        $mo = $this->getDataGenerator()->create_module('moodleoverflow', $options);
        $cm = $DB->get_record('course_modules', array('id' => $mo->cmid));
        $context = \context_module::instance($cm->id);

        $result = \mod_moodleoverflow\subscriptions::get_subscribed_users($mo, $context);
        $this->assertEquals(0, count($result));
        foreach ($users as $user) {
            $this->assertFalse(\mod_moodleoverflow\subscriptions::is_subscribed($user->id, $mo));
        }
    }

    /**
     * Test subscription using disallow subscription on create.
     */
    public function test_moodleoverflow_disallow_subscribe_on_create() {
        global $DB;

        $this->resetAfterTest();

        $usercount = 5;
        $course = $this->getDataGenerator()->create_course();
        $users = array();

        for ($i = 0; $i < $usercount; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $users[] = $user;
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
        }

        $options = array('course' => $course->id, 'forcesubscribe' => MOODLEOVERFLOW_DISALLOWSUBSCRIBE); // Subscription prevented.
        $mo = $this->getDataGenerator()->create_module('moodleoverflow', $options);
        $cm = $DB->get_record('course_modules', array('id' => $mo->cmid));
        $context = \context_module::instance($cm->id);

        $result = \mod_moodleoverflow\subscriptions::get_subscribed_users($mo, $context);
        $this->assertEquals(0, count($result));
        foreach ($users as $user) {
            $this->assertFalse(\mod_moodleoverflow\subscriptions::is_subscribed($user->id, $mo));
        }
    }


}
