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
 * Unit tests for the privacy legacy polyfill for quiz access rules.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz;

/**
 * Unit tests for the privacy legacy polyfill for quiz access rules.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class privacy_legacy_quizaccess_polyfill_test extends \advanced_testcase {
    /**
     * Test that the core_quizaccess\privacy\legacy_polyfill works and that the static _export_quizaccess_user_data can
     * be called.
     */
    public function test_export_quizaccess_user_data(): void {
        $quiz = $this->createMock(quiz_settings::class);
        $user = (object) [];
        $returnvalue = (object) [];

        $mock = $this->createMock(test_privacy_legacy_quizaccess_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_export_quizaccess_user_data', [$quiz, $user])
            ->willReturn($returnvalue);

        test_privacy_legacy_quizaccess_polyfill_provider::$mock = $mock;
        $result = test_privacy_legacy_quizaccess_polyfill_provider::export_quizaccess_user_data($quiz, $user);
        $this->assertSame($returnvalue, $result);
    }

    /**
     * Test the _delete_quizaccess_for_context shim.
     */
    public function test_delete_quizaccess_for_context(): void {
        $context = \context_system::instance();

        $quiz = $this->createMock(quiz_settings::class);

        $mock = $this->createMock(test_privacy_legacy_quizaccess_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_quizaccess_data_for_all_users_in_context', [$quiz]);

        test_privacy_legacy_quizaccess_polyfill_provider::$mock = $mock;
        test_privacy_legacy_quizaccess_polyfill_provider::delete_quizaccess_data_for_all_users_in_context($quiz);
    }

    /**
     * Test the _delete_quizaccess_for_user shim.
     */
    public function test_delete_quizaccess_for_user(): void {
        $context = \context_system::instance();

        $quiz = $this->createMock(quiz_settings::class);
        $user = (object) [];

        $mock = $this->createMock(test_privacy_legacy_quizaccess_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_quizaccess_data_for_user', [$quiz, $user]);

        test_privacy_legacy_quizaccess_polyfill_provider::$mock = $mock;
        test_privacy_legacy_quizaccess_polyfill_provider::delete_quizaccess_data_for_user($quiz, $user);
    }

    /**
     * Test the _delete_quizaccess_for_users shim.
     */
    public function test_delete_quizaccess_for_users(): void {
        $context = $this->createMock(\context_module::class);
        $user = (object) [];
        $approveduserlist = new \core_privacy\local\request\approved_userlist($context, 'mod_quiz', [$user]);

        $mock = $this->createMock(test_privacy_legacy_quizaccess_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_quizaccess_data_for_users', [$approveduserlist]);

        test_privacy_legacy_quizaccess_polyfill_provider::$mock = $mock;
        test_privacy_legacy_quizaccess_polyfill_provider::delete_quizaccess_data_for_users($approveduserlist);
    }
}

/**
 * Legacy polyfill test class for the quizaccess_provider.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_privacy_legacy_quizaccess_polyfill_provider implements
        \core_privacy\local\metadata\provider,
        \mod_quiz\privacy\quizaccess_provider,
        \mod_quiz\privacy\quizaccess_user_provider {

    use \mod_quiz\privacy\legacy_quizaccess_polyfill;
    use \core_privacy\local\legacy_polyfill;

    /**
     * @var test_privacy_legacy_quizaccess_polyfill_provider $mock.
     */
    public static $mock = null;

    /**
     * Export all user data for the quizaccess plugin.
     *
     * @param \mod_quiz\quiz_settings $quiz
     * @param \stdClass $user
     */
    protected static function _export_quizaccess_user_data($quiz, $user) {
        return static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Deletes all user data for the given context.
     *
     * @param \mod_quiz\quiz_settings $quiz
     */
    protected static function _delete_quizaccess_data_for_all_users_in_context($quiz) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Delete personal data for the given user and context.
     *
     * @param   \mod_quiz\quiz_settings           $quiz The quiz being deleted
     * @param   \stdClass       $user The user to export data for
     */
    protected static function _delete_quizaccess_data_for_user($quiz, $user) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Delete all user data for the specified users, in the specified context.
     *
     * @param   \core_privacy\local\request\approved_userlist   $userlist
     */
    protected static function _delete_quizaccess_data_for_users($userlist) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Returns metadata about this plugin.
     *
     * @param   \core_privacy\local\metadata\collection $collection The initialised collection to add items to.
     * @return  \core_privacy\local\metadata\collection     A listing of user data stored through this system.
     */
    protected static function _get_metadata(\core_privacy\local\metadata\collection $collection) {
        return $collection;
    }
}

/**
 * Called inside the polyfill methods in the test polyfill provider, allowing us to ensure these are called with correct params.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_privacy_legacy_quizaccess_polyfill_mock_wrapper {
    /**
     * Get the return value for the specified item.
     */
    public function get_return_value() {
    }
}
