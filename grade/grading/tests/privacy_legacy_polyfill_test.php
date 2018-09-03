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
 * Unit tests for the privacy legacy polyfill for gradingform.
 *
 * @package     core_grading
 * @category    test
 * @copyright   2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the Grading API's privacy legacy_polyfill.
 *
 * @copyright   2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradeform_privacy_legacy_polyfill_test extends advanced_testcase {
    /**
     * Test that the core_grading\privacy\legacy_polyfill works and that the static _get_grading_export_data can be called.
     */
    public function test_get_gradingform_export_data() {
        $userid = 476;
        $context = context_system::instance();

        $mock = $this->createMock(test_gradingform_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_get_gradingform_export_data', [$context, (object)[], $userid]);

        test_legacy_polyfill_gradingform_provider::$mock = $mock;
        test_legacy_polyfill_gradingform_provider::get_gradingform_export_data($context, (object)[], $userid);
    }

    /**
     * Test for _get_metadata shim.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('core_gradingform');
        $this->assertSame($collection, test_legacy_polyfill_gradingform_provider::get_metadata($collection));
    }

    /**
     * Test the _delete_gradingform_for_context shim.
     */
    public function test_delete_gradingform_for_context() {
        $context = context_system::instance();

        $mock = $this->createMock(test_gradingform_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_gradingform_for_context', [$context]);

        test_legacy_polyfill_gradingform_provider::$mock = $mock;
        test_legacy_polyfill_gradingform_provider::delete_gradingform_for_context($context);
    }

    /**
     * Test the _delete_gradingform_for_context shim.
     */
    public function test_delete_gradingform_for_user() {
        $userid = 696;
        $context = \context_system::instance();

        $mock = $this->createMock(test_gradingform_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_gradingform_for_userid', [$userid, $context]);

        test_legacy_polyfill_gradingform_provider::$mock = $mock;
        test_legacy_polyfill_gradingform_provider::delete_gradingform_for_userid($userid, $context);
    }
}

/**
 * Legacy polyfill test class for the gradingform_provider.
 *
 * @copyright   2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_legacy_polyfill_gradingform_provider implements
    \core_privacy\local\metadata\provider,
    \core_grading\privacy\gradingform_provider {

    use \core_grading\privacy\gradingform_legacy_polyfill;
    use \core_privacy\local\legacy_polyfill;

    /**
     * @var test_legacy_polyfill_gradingform_provider $mock.
     */
    public static $mock = null;

    /**
     * Export all user data for the gradingform plugin.
     *
     * @param context $context
     * @param stdClass $definition
     * @param int $userid
     */
    protected static function _get_gradingform_export_data(\context $context, $definition, $userid) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Deletes all user data for the given context.
     *
     * @param context $context
     */
    protected static function _delete_gradingform_for_context(\context $context) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Delete personal data for the given user and context.
     *
     * @param int $userid
     * @param context $context
     */
    protected static function _delete_gradingform_for_userid($userid, \context $context) {
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
class test_gradingform_legacy_polyfill_mock_wrapper {
    /**
     * Get the return value for the specified item.
     */
    public function get_return_value() {
    }
}
