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
 * PHPUnit tests for privacy provider.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb\privacy;

use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\request\approved_contextlist;
use core_privacy\tests\provider_testcase;
use quizaccess_seb\privacy\provider;
use quizaccess_seb\seb_quiz_settings;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../test_helper_trait.php');

/**
 * PHPUnit tests for privacy provider.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {
    use \quizaccess_seb_test_helper_trait;

    /**
     * Setup the user, the quiz and ensure that the user is the last user to modify the SEB quiz settings.
     */
    public function setup_test_data() {
        $this->resetAfterTest();

        $this->setAdminUser();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->create_test_quiz($this->course, \quizaccess_seb\settings_provider::USE_SEB_CONFIG_MANUALLY);

        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);

        $template = $this->create_template();

        $quizsettings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);

        // Modify settings so usermodified is updated. This is the user data we are testing for.
        $quizsettings->set('requiresafeexambrowser', \quizaccess_seb\settings_provider::USE_SEB_TEMPLATE);
        $quizsettings->set('templateid', $template->get('id'));
        $quizsettings->save();

    }

    /**
     * Test that the module context for a user who last modified the module is retrieved.
     */
    public function test_get_contexts_for_userid() {
        $this->setup_test_data();

        $contexts = provider::get_contexts_for_userid($this->user->id);
        $contextids = $contexts->get_contextids();
        $this->assertEquals(\context_module::instance($this->quiz->cmid)->id, reset($contextids));
    }

    /**
     * That that no module context is found for a user who has not modified any quiz settings.
     */
    public function test_get_no_contexts_for_userid() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $contexts = provider::get_contexts_for_userid($user->id);
        $contextids = $contexts->get_contextids();
        $this->assertEmpty($contextids);
    }

    /**
     * Test that user data is exported in format expected.
     */
    public function test_export_user_data() {
        $this->setup_test_data();

        $context = \context_module::instance($this->quiz->cmid);

        // Add another course_module of a differenty type - doing this lets us
        // test that the data exporter is correctly limiting its selection to
        // the quiz and not anything with the same instance id.
        // (note this is only effective with databases not using fed (+1000) sequences
        // per table, like postgres and mysql do, rendering this useless. In any
        // case better to have the situation covered by some DBs,
        // like sqlsrv or oracle than by none).
        $this->getDataGenerator()->create_module('label', ['course' => $this->course->id]);

        $contextlist = provider::get_contexts_for_userid($this->user->id);
        $approvedcontextlist = new approved_contextlist(
            $this->user,
            'quizaccess_seb',
            $contextlist->get_contextids()
        );

        writer::reset();
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        provider::export_user_data($approvedcontextlist);

        $index = '1'; // Get first data returned from the quizsettings table metadata.
        $data = $writer->get_data([
            get_string('pluginname', 'quizaccess_seb'),
            seb_quiz_settings::TABLE,
            $index,
        ]);
        $this->assertNotEmpty($data);

        $index = '1'; // Get first data returned from the template table metadata.
        $data = $writer->get_data([
            get_string('pluginname', 'quizaccess_seb'),
            \quizaccess_seb\template::TABLE,
            $index,
        ]);
        $this->assertNotEmpty($data);

        $index = '2'; // There should not be more than one instance with data.
        $data = $writer->get_data([
            get_string('pluginname', 'quizaccess_seb'),
            seb_quiz_settings::TABLE,
            $index,
        ]);
        $this->assertEmpty($data);

        $index = '2'; // There should not be more than one instance with data.
        $data = $writer->get_data([
            get_string('pluginname', 'quizaccess_seb'),
            \quizaccess_seb\template::TABLE,
            $index,
        ]);
        $this->assertEmpty($data);
    }

    /**
     * Test that a userlist with module context is populated by usermodified user.
     */
    public function test_get_users_in_context() {
        $this->setup_test_data();

        // Create empty userlist with quiz module context.
        $userlist = new userlist(\context_module::instance($this->quiz->cmid), 'quizaccess_seb');

        // Test that the userlist is populated with expected user/s.
        provider::get_users_in_context($userlist);
        $this->assertEquals($this->user->id, $userlist->get_userids()[0]);
    }

    /**
     * Test that data is deleted for a list of users.
     */
    public function test_delete_data_for_users() {
        $this->setup_test_data();

        $approveduserlist = new approved_userlist(\context_module::instance($this->quiz->cmid),
                'quizaccess_seb', [$this->user->id]);

        // Test data exists.
        $this->assertNotEmpty(seb_quiz_settings::get_record(['quizid' => $this->quiz->id]));

        // Test data is deleted.
        provider::delete_data_for_users($approveduserlist);
        $record = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $this->assertEmpty($record->get('usermodified'));

        $template = \quizaccess_seb\template::get_record(['id' => $record->get('templateid')]);
        $this->assertEmpty($template->get('usermodified'));
    }

    /**
     * Test that data is deleted for a list of contexts.
     */
    public function test_delete_data_for_user() {
        $this->setup_test_data();

        $context = \context_module::instance($this->quiz->cmid);
        $approvedcontextlist = new approved_contextlist($this->user,
                'quizaccess_seb', [$context->id]);

        // Test data exists.
        $this->assertNotEmpty(seb_quiz_settings::get_record(['quizid' => $this->quiz->id]));

        // Test data is deleted.
        provider::delete_data_for_user($approvedcontextlist);
        $record = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $this->assertEmpty($record->get('usermodified'));

        $template = \quizaccess_seb\template::get_record(['id' => $record->get('templateid')]);
        $this->assertEmpty($template->get('usermodified'));
    }

    /**
     * Test that data is deleted for a single context.
     */
    public function test_delete_data_for_all_users_in_context() {
        $this->setup_test_data();

        $context = \context_module::instance($this->quiz->cmid);

        // Test data exists.
        $record = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $template = \quizaccess_seb\template::get_record(['id' => $record->get('templateid')]);
        $this->assertNotEmpty($record->get('usermodified'));
        $this->assertNotEmpty($template->get('usermodified'));

        // Test data is deleted.
        provider::delete_data_for_all_users_in_context($context);

        $record = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $template = \quizaccess_seb\template::get_record(['id' => $record->get('templateid')]);
        $this->assertEmpty($record->get('usermodified'));
        $this->assertEmpty($template->get('usermodified'));
    }
}
