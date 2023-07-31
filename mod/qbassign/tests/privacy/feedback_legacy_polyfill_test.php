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
 * Unit tests for the privacy legacy polyfill for mod_qbassign.
 *
 * @package     mod_qbassign
 * @category    test
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_qbassign\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/feedbackplugin.php');
require_once($CFG->dirroot . '/mod/qbassign/feedback/comments/locallib.php');

/**
 * Unit tests for the qbassignment feedback subplugins API's privacy legacy_polyfill.
 *
 * @package     mod_qbassign
 * @category    test
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_legacy_polyfill_test extends \advanced_testcase {

    /**
     * Convenience function to create an instance of an qbassignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @return qbassign The qbassign class.
     */
    protected function create_instance($params = array()) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_qbassign');
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('qbassign', $instance->id);
        $context = \context_module::instance($cm->id);
        return new \qbassign($context, $cm, $params['course']);
    }

    /**
     * Test the get_context_for_userid_within_feedback shim.
     */
    public function test_get_context_for_userid_within_feedback() {
        $userid = 21;
        $contextlist = new \core_privacy\local\request\contextlist();
        $mock = $this->createMock(test_qbassignfeedback_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_get_context_for_userid_within_feedback', [$userid, $contextlist]);
        test_legacy_polyfill_feedback_provider::$mock = $mock;
        test_legacy_polyfill_feedback_provider::get_context_for_userid_within_feedback($userid, $contextlist);
    }

    /**
     * Test the get_student_user_ids shim.
     */
    public function test_get_student_user_ids() {
        $teacherid = 107;
        $qbassignid = 15;
        $useridlist = new \mod_qbassign\privacy\useridlist($teacherid, $qbassignid);
        $mock = $this->createMock(test_qbassignfeedback_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_get_student_user_ids', [$useridlist]);
        test_legacy_polyfill_feedback_provider::$mock = $mock;
        test_legacy_polyfill_feedback_provider::get_student_user_ids($useridlist);
    }

    /**
     * Test the export_feedback_user_data shim.
     */
    public function test_export_feedback_user_data() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $qbassign = $this->create_instance(['course' => $course]);
        $context = \context_system::instance();
        $subplugin = new \qbassign_feedback_comments($qbassign, 'comments');
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context,$qbassign);
        $mock = $this->createMock(test_qbassignfeedback_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_export_feedback_user_data', [$requestdata]);
        test_legacy_polyfill_feedback_provider::$mock = $mock;
        test_legacy_polyfill_feedback_provider::export_feedback_user_data($requestdata);
    }

    /**
     * Test the delete_feedback_for_context shim.
     */
    public function test_delete_feedback_for_context() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $qbassign = $this->create_instance(['course' => $course]);
        $context = \context_system::instance();
        $subplugin = new \qbassign_feedback_comments($qbassign, 'comments');
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context,$qbassign);
        $mock = $this->createMock(test_qbassignfeedback_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_feedback_for_context', [$requestdata]);
        test_legacy_polyfill_feedback_provider::$mock = $mock;
        test_legacy_polyfill_feedback_provider::delete_feedback_for_context($requestdata);
    }

    /**
     * Test the delete feedback for grade shim.
     */
    public function test_delete_feedback_for_grade() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $qbassign = $this->create_instance(['course' => $course]);
        $context = \context_system::instance();
        $subplugin = new \qbassign_feedback_comments($qbassign, 'comments');
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context,$qbassign);
        $mock = $this->createMock(test_qbassignfeedback_legacy_polyfill_mock_wrapper::class);
        $mock->expects($this->once())
            ->method('get_return_value')
            ->with('_delete_feedback_for_grade', [$requestdata]);
        test_legacy_polyfill_feedback_provider::$mock = $mock;
        test_legacy_polyfill_feedback_provider::delete_feedback_for_grade($requestdata);
    }
}
/**
 * Legacy polyfill test class for the qbassignfeedback_provider.
 *
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_legacy_polyfill_feedback_provider implements \mod_qbassign\privacy\qbassignfeedback_provider {
    use \mod_qbassign\privacy\feedback_legacy_polyfill;
    /**
     * @var test_legacy_polyfill_feedback_provider $mock.
     */
    public static $mock = null;

    /**
     * Retrieves the contextids associated with the provided userid for this subplugin.
     * NOTE if your subplugin must have an entry in the qbassign_grade table to work, then this
     * method can be empty.
     *
     * @param  int $userid The user ID to get context IDs for.
     * @param  contextlist $contextlist Use add_from_sql with this object to add your context IDs.
     */
    public static function _get_context_for_userid_within_feedback(int $userid,
            \core_privacy\local\request\contextlist $contextlist) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Returns student user ids related to the provided teacher ID. If an entry must be present in the qbassign_grade table for
     * your plugin to work then there is no need to fill in this method. If you filled in get_context_for_userid_within_feedback()
     * then you probably have to fill this in as well.
     *
     * @param  useridlist $useridlist A list of user IDs of students graded by this user.
     */
    public static function _get_student_user_ids(\mod_qbassign\privacy\useridlist $useridlist) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Export feedback data with the available grade and userid information provided.
     * qbassign_plugin_request_data contains:
     * - context
     * - grade object
     * - current path (subcontext)
     * - user object
     *
     * @param  qbassign_plugin_request_data $exportdata Contains data to help export the user information.
     */
    public static function _export_feedback_user_data(\mod_qbassign\privacy\qbassign_plugin_request_data $exportdata) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     * qbassign_plugin_request_data contains:
     * - context
     * - qbassign object
     *
     * @param  qbassign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function _delete_feedback_for_context(\mod_qbassign\privacy\qbassign_plugin_request_data $requestdata) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }

    /**
     * Calling this function should delete all user data associated with this grade.
     * qbassign_plugin_request_data contains:
     * - context
     * - grade object
     * - user object
     * - qbassign object
     *
     * @param  qbassign_plugin_request_data $requestdata Data useful for deleting user data.
     */
    public static function _delete_feedback_for_grade(\mod_qbassign\privacy\qbassign_plugin_request_data $requestdata) {
        static::$mock->get_return_value(__FUNCTION__, func_get_args());
    }
}
/**
 * Called inside the polyfill methods in the test polyfill provider, allowing us to ensure these are called with correct params.
 *
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_qbassignfeedback_legacy_polyfill_mock_wrapper {
    /**
     * Get the return value for the specified item.
     */
    public function get_return_value() {
    }
}
