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

// phpcs:disable moodle.Commenting.TodoComment
// TODO: Split out all module specific code from plagiarism/turnitin/lib.php.

/**
 * Class turnitin_coursework
 *
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class turnitin_coursework {

    /**
     * @var string
     */
    private $modname;
    /**
     * @var string
     */
    public $gradestable;
    /**
     * @var string
     */
    public $filecomponent;

    /**
     * The constructor
     */
    public function __construct() {
        $this->modname = 'coursework';
        $this->gradestable = $this->modname.'_feedbacks';
        $this->filecomponent = 'mod_'.$this->modname;
    }

    /**
     * Check whether the user is a tutor
     *
     * @param context $context The context
     * @return bool
     */
    public function is_tutor($context) {
        $capabilities = [$this->get_tutor_capability(), 'mod/coursework:addagreedgrade',
            'mod/coursework:addallocatedagreedgrade', 'mod/coursework:administergrades', ];
        return has_any_capability($capabilities, $context);
    }

    /**
     * Check if the user has the capability to add the initial grade
     *
     * @return string
     */
    public function get_tutor_capability() {
        return 'mod/'.$this->modname.':addinitialgrade';
    }

    /**
     * Whether the user is enrolled on the course and has the capability to submit coursework
     *
     * @param context $context The context
     * @param int $userid The user id
     * @return bool
     * @throws coding_exception
     */
    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':submit', $context, $userid);
    }

    /**
     * Get the author of the submission
     *
     * @param int $itemid The item id
     * @return int
     * @throws dml_exception
     */
    public function get_author($itemid) {
        global $DB;

        $id = 0;

        if ($submission = $DB->get_record('coursework_submissions', ['id' => $itemid])) {
            $id = $submission->authorid;
        }

        return $id;
    }

    /**
     * Create a file event
     *
     * @param array $params The params
     * @return mixed
     */
    public function create_file_event($params) {
        return \mod_coursework\event\assessable_uploaded::create($params);
    }

    /**
     * Get the current grade query
     *
     * @param int $userid The user id
     * @param int $moduleid The module id
     * @param int $itemid The item id
     * @return false|mixed
     * @throws dml_exception
     */
    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $sql = "SELECT         *
                FROM           {coursework_submissions}    cs,
                               {coursework_feedbacks}      cf
                WHERE         cs.id   =   cf.submissionid
                AND           cs.authorid         =   :authorid
                AND           cs.courseworkid     =   :courseworkid
                AND           cf.stage_identifier =   :stage";

        $params = ['stage' => 'final_agreed_1', 'authorid' => $userid, 'courseworkid' => $moduleid];

        $currentgradesquery = $DB->get_record_sql($sql, $params);

        return $currentgradesquery;
    }

    /**
     * Initialise the post date for the module
     *
     * @param stdClass $moduledata The module data
     * @return int
     */
    public function initialise_post_date($moduledata) {
        return 0;
    }
}
