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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

// TODO: Split out all module specific code from plagiarism/turnitin/lib.php.
class turnitin_workshop {

    private $modname;
    public $gradestable;
    public $filecomponent;

    public function __construct() {
        $this->modname = 'workshop';
        $this->gradestable = 'grade_grades';
        $this->filecomponent = 'mod_'.$this->modname;
    }

    public function is_tutor($context) {
        return has_capability($this->get_tutor_capability(), $context);
    }

    public function get_tutor_capability() {
        return 'plagiarism/turnitin:viewfullreport';
    }

    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':submit', $context, $userid);
    }

    public function get_author($itemid) {
        return;
    }

    public function set_content($linkarray, $moduleid) {
        return $linkarray["content"];
    }

    public function get_onlinetext($userid, $cm) {
        global $DB;

        $submission = $DB->get_record('workshop_submissions',
                                        array('authorid' => $userid, 'workshopid' => $cm->instance));

        $onlinetextdata = new stdClass();
        $onlinetextdata->itemid = $submission->id;
        $onlinetextdata->onlinetext = $submission->content;
        $onlinetextdata->onlineformat = $submission->contentformat;

        return $onlinetextdata;
    }

    public function create_file_event($params) {
        return \mod_workshop\event\assessable_uploaded::create($params);
    }

    public function create_text_event($params) {
        return \mod_workshop\event\assessable_uploaded::create($params);
    }

    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $currentgradequery = $DB->get_record('grade_grades', array('userid' => $userid, 'itemid' => $itemid));
        return $currentgradequery;
    }

    public function initialise_post_date($moduledata) {
        return $moduledata->assessmentend;
    }
}