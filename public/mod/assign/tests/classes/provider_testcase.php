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

namespace mod_assign\tests;

/**
 * TODO describe file provider_testcase
 *
 * @package    mod_assign
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class provider_testcase extends \core_privacy\tests\provider_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;

        parent::setUpBeforeClass();

        require_once($CFG->dirroot . '/mod/assign/locallib.php');
    }

    /**
     * Convenience method for creating a submission.
     *
     * @param  assign  $assign The assign object
     * @param  stdClass  $user The user object
     * @param  string  $submissiontext Submission text
     * @param  integer $attemptnumber The attempt number
     * @return object A submission object.
     */
    protected function create_submission($assign, $user, $submissiontext, $attemptnumber = 0) {
        $submission = $assign->get_user_submission($user->id, true, $attemptnumber);
        $submission->onlinetext_editor = ['text' => $submissiontext,
                                         'format' => FORMAT_MOODLE];

        $this->setUser($user);
        $notices = [];
        $assign->save_submission($submission, $notices);
        return $submission;
    }

    /**
     * Convenience function to create an instance of an assignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @return assign The assign class.
     */
    protected function create_instance($params = array()) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = \context_module::instance($cm->id);
        return new \assign($context, $cm, $params['course']);
    }
}
