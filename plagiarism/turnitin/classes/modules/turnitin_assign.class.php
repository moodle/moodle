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
 * Class turnitin_assign
 *
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class turnitin_assign {

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
        $this->modname = 'assign';
        $this->gradestable = $this->modname.'_grades';
        $this->filecomponent = $this->modname.'submission_file';
    }

    /**
     * Check whether the user is a tutor
     *
     * @param context $context The context
     * @return bool
     * @throws coding_exception
     */
    public function is_tutor($context) {
        return has_capability($this->get_tutor_capability(), $context);
    }

    /**
     * Whether the user has the capability to grade
     *
     * @return string
     */
    public function get_tutor_capability() {
        return 'mod/'.$this->modname.':grade';
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

        if ($submission = $DB->get_record('assign_submission', ['id' => $itemid], 'userid')) {
            return $submission->userid;
        } else {
            return 0;
        }
    }

    /**
     * Whether the user is enrolled on the course and has the capability to submit assignments
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
     * Set the content of the submission
     *
     * @param array $linkarray The link array
     * @param object $cm The course module.
     * @return string
     */
    public function set_content($linkarray, $cm) {
        $onlinetextdata = $this->get_onlinetext($linkarray["userid"], $cm);

        return (empty($onlinetextdata->onlinetext)) ? '' : $onlinetextdata->onlinetext;
    }


    /**
     * Check if resubmissions in a Turnitin sense are allowed to an assignment.
     *
     * @param int $assignid The assignment id
     * @param int $reportgenspeed The report generation speed
     * @param string $submissiontype The submission type
     * @param int $maxattempts Number of max attempts
     * @param string $attemptreopened The attempt reopened status
     */
    public function is_resubmission_allowed($assignid, $reportgenspeed, $submissiontype, $maxattempts,
                                            $attemptreopened = null) {
        global $DB, $CFG;

        // Get the maximum number of file submissions allowed.
        $params = ['assignment' => $assignid,
            'subtype' => 'assignsubmission',
            'plugin' => 'file',
            'name' => 'maxfilesubmissions', ];

        $maxfilesubmissions = 0;
        if ($result = $DB->get_record('assign_plugin_config', $params, 'value')) {
            $maxfilesubmissions = $result->value;
        }

        // If resubmissions are enabled in a Turnitin sense.
        if ($reportgenspeed > 0) {
            // If the maximum number of attempts is 1, or an attempt has not been reopened/has previous submission.
            if ($maxattempts == 1 || $attemptreopened == 'submitted') {
                // If this is a text or file submission, or we can only submit one file.
                if ($submissiontype == 'text_content' || ($submissiontype == 'file' && $maxfilesubmissions == 1)) {
                    // Treat this as a resubmission.
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get the onlinetext submission
     *
     * @param int $userid The user id
     * @param object $cm The course module.
     * @return stdClass
     * @throws dml_exception
     */
    public function get_onlinetext($userid, $cm) {
        global $DB;

        // Get latest text content submitted as we do not have submission id.
        $submissions = $DB->get_records_select('assign_submission', ' userid = ? AND assignment = ? ',
                                        [$userid, $cm->instance], 'id DESC', 'id', 0, 1);
        $submission = end($submissions);
        $moodletextsubmission = $DB->get_record('assignsubmission_onlinetext',
                                            ['submission' => $submission->id], 'onlinetext, onlineformat');

        $onlinetextdata = new stdClass();
        $onlinetextdata->itemid = $submission->id;

        if (isset($moodletextsubmission->onlinetext)) {
            $onlinetextdata->onlinetext = $moodletextsubmission->onlinetext;
        }
        if (isset($moodletextsubmission->onlineformat)) {
            $onlinetextdata->onlineformat = $moodletextsubmission->onlineformat;
        }

        return $onlinetextdata;
    }

    /**
     * Create a file event
     *
     * @param array $params The params
     * @return \core\event\base
     * @throws coding_exception
     */
    public function create_file_event($params) {
        return \assignsubmission_file\event\assessable_uploaded::create($params);
    }

    /**
     * Create a text event
     *
     * @param array $params The params
     * @return \core\event\base
     * @throws coding_exception
     */
    public function create_text_event($params) {
        return \assignsubmission_onlinetext\event\assessable_uploaded::create($params);
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

        $currentgradesquery = $DB->get_records('assign_grades',
                                                    ['userid' => $userid, 'assignment' => $moduleid],
                                                    'id DESC'
                                                );
        return current($currentgradesquery);
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
