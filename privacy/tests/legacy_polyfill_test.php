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
 * Unit tests for the privacy legacy polyfill.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;

/**
 * Tests for the \core_privacy API's types\user_preference functionality.
 * Unit tests for the Privacy API's legacy_polyfill.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\legacy_polyfill
 */
class core_privacy_legacy_polyfill_test extends advanced_testcase {
    /**
     * Test that the null_provider polyfill works and that the static _get_reason can be
     * successfully called.
     *
     * @covers ::get_reason
     */
    public function test_null_provider() {
        $this->assertEquals('thisisareason', test_legacy_polyfill_null_provider::get_reason());
    }

    /**
     * Test that the metdata\provider polyfill works and that the static _get_metadata can be
     * successfully called.
     *
     * @covers ::get_metadata
     */
    public function test_metadata_provider() {
        $collection = new collection('core_privacy');
        $this->assertSame($collection, test_legacy_polyfill_metadata_provider::get_metadata($collection));
    }

    /**
     * Test that the local\request\user_preference_provider polyfill works and that the static
     * _export_user_preferences can be successfully called.
     *
     * @covers ::export_user_preferences
     */
    public function test_user_preference_provider() {
        $userid = 417;

        $mock = $this->createMock(test_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_export_user_preferences', [$userid]);

        test_legacy_polyfill_user_preference_provider::$mock = $mock;
        test_legacy_polyfill_user_preference_provider::export_user_preferences($userid);
    }

    /**
     * Test that the local\request\core_user_preference_provider polyfill works and that the static
     * _get_contexts_for_userid can be successfully called.
     *
     * @covers ::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid() {
        $userid = 417;
        $contextlist = new contextlist('core_privacy');

        $mock = $this->createMock(test_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_get_contexts_for_userid', [$userid])
            ->willReturn($contextlist);

        test_legacy_polyfill_request_provider::$mock = $mock;
        $result = test_legacy_polyfill_request_provider::get_contexts_for_userid($userid);
        $this->assertSame($contextlist, $result);
    }

    /**
     * Test that the local\request\core_user_preference_provider polyfill works and that the static
     * _export_user_data can be successfully called.
     *
     * @covers ::export_user_data
     */
    public function test_export_user_data() {
        $contextlist = new approved_contextlist(\core_user::get_user_by_username('admin'), 'core_privacy', [98]);

        $mock = $this->createMock(test_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_export_user_data', [$contextlist]);

        test_legacy_polyfill_request_provider::$mock = $mock;
        test_legacy_polyfill_request_provider::export_user_data($contextlist);
    }

    /**
     * Test that the local\request\core_user_preference_provider polyfill works and that the static
     * _delete_data_for_all_users_in_context can be successfully called.
     *
     * @covers ::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context() {
        $mock = $this->createMock(test_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_data_for_all_users_in_context', [\context_system::instance()]);

        test_legacy_polyfill_request_provider::$mock = $mock;
        test_legacy_polyfill_request_provider::delete_data_for_all_users_in_context(\context_system::instance());
    }

    /**
     * Test that the local\request\core_user_preference_provider polyfill works and that the static
     * _delete_data_for_user can be successfully called.
     *
     * @covers ::delete_data_for_user
     */
    public function test_delete_data_for_user() {
        $contextlist = new approved_contextlist(\core_user::get_user_by_username('admin'), 'core_privacy', [98]);

        $mock = $this->createMock(test_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_data_for_user', [$contextlist]);

        test_legacy_polyfill_request_provider::$mock = $mock;
        test_legacy_polyfill_request_provider::delete_data_for_user($contextlist);
    }
}

/**
 * Legacy polyfill test for the null provider.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_legacy_polyfill_null_provider implements \core_privacy\local\metadata\null_provider {

    use \core_privacy\local\legacy_polyfill;

    /**
     * Test for get_reason
     */
    protected static function _get_reason() {
        return 'thisisareason';
    }
}

/**
 * Legacy polyfill test for the metadata provider.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_legacy_polyfill_metadata_provider implements \core_privacy\local\metadata\provider {

    use \core_privacy\local\legacy_polyfill;

    /**
     * Test for get_metadata.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    protected static function _get_metadata(collection $collection) {
        return $collection;
    }
}

/**
 * Legacy polyfill test for the metadata provider.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_legacy_polyfill_user_preference_provider implements \core_privacy\local\request\user_preference_provider {

    use \core_privacy\local\legacy_polyfill;

    /**
     * @var test_legacy_polyfill_request_provider $mock
     */
    public static $mock = null;

    /**
     * Export all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    protected static function _export_user_preferences($userid) {
        return static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }
}

/**
 * Legacy polyfill test for the request provider.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_legacy_polyfill_request_provider implements \core_privacy\local\request\core_user_data_provider {

    use \core_privacy\local\legacy_polyfill;

    /**
     * @var test_legacy_polyfill_request_provider $mock
     */
    public static $mock = null;

    /**
     * Test for get_contexts_for_userid.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    protected static function _get_contexts_for_userid($userid) {
        return static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Test for export_user_data.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    protected static function _export_user_data(approved_contextlist $contextlist) {
        return static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }


    /**
     * Delete all use data which matches the specified deletion criteria.
     *
     * @param   context         $context   The specific context to delete data for.
     */
    public static function _delete_data_for_all_users_in_context(\context $context) {
        return static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function _delete_data_for_user(approved_contextlist $contextlist) {
        return static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }
}

class test_legacy_polyfill_mock_wrapper {
    /**
     * Get the return value for the specified item.
     */
    public function get_return_value() {}
}
