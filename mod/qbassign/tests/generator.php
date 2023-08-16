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
 * Base class for unit tests for mod_qbassign.
 *
 * @package    mod_qbassign
 * @category   phpunit
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/locallib.php');
require_once(__DIR__ . '/fixtures/testable_qbassign.php');

/**
 * Generator helper trait.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait mod_qbassign_test_generator {

    /**
     * Convenience function to create a testable instance of an qbassignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @return testable_qbassign Testable wrapper around the qbassign class.
     */
    protected function create_instance($course, $params = [], $options = []) {
        $params['course'] = $course->id;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_qbassign');
        $instance = $generator->create_instance($params, $options);
        $cm = get_coursemodule_from_instance('qbassign', $instance->id);
        $context = context_module::instance($cm->id);

        return new mod_qbassign_testable_qbassign($context, $cm, $course);
    }

    /**
     * Add a user submission to the qbassignment.
     *
     * @param   \stdClass   $student The user to submit for
     * @param   \qbassign     $qbassign The qbassignment to submit to
     * @param   string      $onlinetex The text tobe submitted
     * @param   bool        $changeuser Whether to switch user to the user being submitted as.
     */
    protected function add_submission($student, $qbassign, $onlinetex = null, $changeuser = true) {
        // Add a submission.
        if ($changeuser) {
            $this->setUser($student);
        }

        if ($onlinetex === null) {
            $onlinetex = 'Submission text';
        }

        $data = (object) [
            'userid' => $student->id,

            'onlinetex_editor' => [
                'itemid' => file_get_unused_draft_itemid(),
                'text' => $onlinetex,
                'format' => FORMAT_HTML,
            ]
        ];

        $qbassign->save_submission($data, $notices);
    }

    /**
     * Submit the qbassignemnt for grading.
     *
     * @param   \stdClass   $student The user to submit for
     * @param   \qbassign     $qbassign The qbassignment to submit to
     * @param   array       $data Additional data to set
     * @param   bool        $changeuser Whether to switch user to the user being submitted as.
     */
    public function submit_for_grading($student, $qbassign, $data = [], $changeuser = true) {
        if ($changeuser) {
            $this->setUser($student);
        }

        $data = (object) array_merge($data, [
                'userid' => $student->id,
            ]);

        $sink = $this->redirectMessages();
        $qbassign->submit_for_grading($data, []);
        $sink->close();

        return $data;
    }

    /**
     * Mark the submission.
     *
     * @param   \stdClass   $teacher The user to mark as
     * @param   \qbassign     $qbassign The qbassignment to mark
     * @param   \stdClass   $student The user to grade
     * @param   array       $data Additional data to set
     * @param   bool        $changeuser Whether to switch user to the user being submitted as.
     */
    protected function mark_submission($teacher, $qbassign, $student, $grade = 50.0, $data = [], $attempt = 0) {
        global $DB;

        // Mark the submission.
        $this->setUser($teacher);
        $data = (object) array_merge($data, [
                'grade' => $grade,
            ]);

        // Bump all timecreated and timemodified for this user back.
        $DB->execute('UPDATE {qbassign_submission} SET timecreated = timecreated - 1, timemodified = timemodified - 1 WHERE userid = :userid',
            ['userid' => $student->id]);

        $qbassign->testable_apply_grade_to_user($data, $student->id, $attempt);
    }
}
