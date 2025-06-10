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
class turnitin_coursework {

    private $modname;
    public $gradestable;
    public $filecomponent;

    public function __construct() {
        $this->modname = 'coursework';
        $this->gradestable = $this->modname.'_feedbacks';
        $this->filecomponent = 'mod_'.$this->modname;
    }

    public function is_tutor($context) {
        $capabilities = array($this->get_tutor_capability(), 'mod/coursework:addagreedgrade',
            'mod/coursework:addallocatedagreedgrade', 'mod/coursework:administergrades');
        return has_any_capability($capabilities, $context);
    }

    public function get_tutor_capability() {
        return 'mod/'.$this->modname.':addinitialgrade';
    }

    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':submit', $context, $userid);
    }

    public function get_author($itemid) {
        global $DB;

        $id = 0;

        if ($submission = $DB->get_record('coursework_submissions', array('id' => $itemid))) {
            $id = $submission->authorid;
        }

        return $id;
    }



    public function create_file_event($params) {
        return \mod_coursework\event\assessable_uploaded::create($params);
    }


    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $sql = "SELECT         *
                FROM           {coursework_submissions}    cs,
                               {coursework_feedbacks}      cf
                WHERE         cs.id   =   cf.submissionid
                AND           cs.authorid         =   :authorid
                AND           cs.courseworkid     =   :courseworkid
                AND           cf.stage_identifier =   :stage";

        $params = array('stage' => 'final_agreed_1', 'authorid' => $userid, 'courseworkid' => $moduleid);

        $currentgradesquery = $DB->get_record_sql($sql, $params);

        return $currentgradesquery;
    }

    public function initialise_post_date($moduledata) {
        return 0;
    }
}