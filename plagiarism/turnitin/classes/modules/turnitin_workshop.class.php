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
 * Class turnitin_workshop
 *
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class turnitin_workshop {

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
        $this->modname = 'workshop';
        $this->gradestable = 'grade_grades';
        $this->filecomponent = 'mod_'.$this->modname;
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
     * Whether the user has the capability to view the full report
     *
     * @return string
     */
    public function get_tutor_capability() {
        return 'plagiarism/turnitin:viewfullreport';
    }

    /**
     * Whether the user is enrolled on the course and has the capability to submit a workshop submission
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
     * Get the author of the workshop submission
     *
     * @param int $itemid The item id
     * @return void
     */
    public function get_author($itemid) {
        return;
    }

    /**
     * Set the content of the workshop submission
     *
     * @param array $linkarray The link array
     * @param int $moduleid The module id
     * @return mixed
     */
    public function set_content($linkarray, $moduleid) {
        return $linkarray["content"];
    }

    /**
     * Get the onlinetext
     *
     * @param int $userid The user id
     * @param object $cm The course module.
     * @return stdClass
     * @throws dml_exception
     */
    public function get_onlinetext($userid, $cm) {
        global $DB;

        $submission = $DB->get_record('workshop_submissions',
                                        ['authorid' => $userid, 'workshopid' => $cm->instance]);

        $onlinetextdata = new stdClass();
        $onlinetextdata->itemid = $submission->id;
        $onlinetextdata->onlinetext = $submission->content;
        $onlinetextdata->onlineformat = $submission->contentformat;

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
        return \mod_workshop\event\assessable_uploaded::create($params);
    }

    /**
     * Create a text event
     *
     * @param array $params The params
     * @return \core\event\base
     * @throws coding_exception
     */
    public function create_text_event($params) {
        return \mod_workshop\event\assessable_uploaded::create($params);
    }

    /**
     * Get the current grade query
     *
     * @param int $userid The user id
     * @param int $moduleid The module id
     * @param int $itemid The item id
     * @return false|mixed|stdClass
     * @throws dml_exception
     */
    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $currentgradequery = $DB->get_record('grade_grades', ['userid' => $userid, 'itemid' => $itemid]);
        return $currentgradequery;
    }

    /**
     * Initialise the post date for the module
     *
     * @param stdClass $moduledata The module data
     * @return mixed
     */
    public function initialise_post_date($moduledata) {
        return $moduledata->assessmentend;
    }
}
