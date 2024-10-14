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

namespace core_portfolio\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the Portfolio API's privacy legacy_polyfill.
 *
 * @package     core_portfolio
 * @category    test
 * @copyright   2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class legacy_polyfill_test extends \advanced_testcase {
    /**
     * Test that the core_portfolio\privacy\legacy_polyfill works and that the static _export_portfolio_user_data can be called.
     */
    public function test_export_portfolio_user_data(): void {
        $userid = 476;
        $context = \context_system::instance();

        $mock = $this->createMock(test_portfolio_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_export_portfolio_user_data', [$userid, $context, [], []]);

        test_legacy_polyfill_portfolio_provider::$mock = $mock;
        test_legacy_polyfill_portfolio_provider::export_portfolio_user_data($userid, $context, [], []);
    }

    /**
     * Test for _get_metadata shim.
     */
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('core_portfolio');
        $this->assertSame($collection, test_legacy_polyfill_portfolio_provider::get_metadata($collection));
    }

    /**
     * Test the _delete_portfolio_for_context shim.
     */
    public function test_delete_portfolio_for_context(): void {
        $context = \context_system::instance();

        $mock = $this->createMock(test_portfolio_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_portfolio_for_context', [$context]);

        test_legacy_polyfill_portfolio_provider::$mock = $mock;
        test_legacy_polyfill_portfolio_provider::delete_portfolio_for_context($context);
    }

    /**
     * Test the _delete_portfolio_for_context shim.
     */
    public function test_delete_portfolio_for_user(): void {
        $userid = 696;
        $context = \context_system::instance();

        $mock = $this->createMock(test_portfolio_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_portfolio_for_user', [$userid, $context]);

        test_legacy_polyfill_portfolio_provider::$mock = $mock;
        test_legacy_polyfill_portfolio_provider::delete_portfolio_for_user($userid, $context);
    }
}

/**
 * Legacy polyfill test class for the portfolio_provider.
 *
 * @copyright   2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_legacy_polyfill_portfolio_provider implements
    \core_privacy\local\metadata\provider,
    \core_portfolio\privacy\portfolio_provider {

    use \core_portfolio\privacy\legacy_polyfill;
    use \core_privacy\local\legacy_polyfill;

    /**
     * @var test_legacy_polyfill_portfolio_provider $mock.
     */
    public static $mock = null;

    /**
     * Export all user data for the portfolio plugin.
     *
     * @param int $userid
     * @param context $context
     * @param array $subcontext
     * @param array $linkarray
     */
    protected static function _export_portfolio_user_data($userid, \context $context, array $subcontext, array $linkarray) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Deletes all user data for the given context.
     *
     * @param context $context
     */
    protected static function _delete_portfolio_for_context(\context $context) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Delete personal data for the given user and context.
     *
     * @param int $userid
     * @param context $context
     */
    protected static function _delete_portfolio_for_user($userid, \context $context) {
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
 * @copyright   2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_portfolio_legacy_polyfill_mock_wrapper {
    /**
     * Get the return value for the specified item.
     */
    public function get_return_value() {
    }
}
