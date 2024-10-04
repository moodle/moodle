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

require_once($CFG->dirroot.'/grade/export/lib.php');
require_once($CFG->libdir.'/filelib.php');

class grade_export_xml extends grade_export {

    public $plugin = 'xml';
    public $updatedgradesonly = false; // default to export ALL grades

    /**
     * Ensure we produce correctly formed XML content by encoding idnumbers appropriately
     *
     * @param string $idnumber
     * @return string
     */
    private static function xml_export_idnumber(string $idnumber): string {
        return htmlspecialchars($idnumber, ENT_QUOTES | ENT_XML1);
    }

    /**
     * Handle form processing for export. Note we need to handle the case where there are no 'itemids[]' being included in the
     * form, because each is disabled for selection due to having empty idnumber
     *
     * @param stdClass $formdata
     */
    public function process_form($formdata) {
        if (!isset($formdata->itemids)) {
            $formdata->itemids = self::EXPORT_SELECT_NONE;
        }

        parent::process_form($formdata);
    }

    /**
     * To be implemented by child classes
     * @param boolean $feedback
     * @param boolean $publish Whether to output directly, or send as a file
     * @return string
     */
    public function print_grades($feedback = false) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $export_tracking = $this->track_exports();

        $strgrades = get_string('grades');

        /// Calculate file name
        $shortname = format_string($this->course->shortname, true, array('context' => context_course::instance($this->course->id)));
        $downloadfilename = clean_filename("$shortname $strgrades.xml");

        make_temp_directory('gradeexport');
        $tempfilename = $CFG->tempdir .'/gradeexport/'. md5(sesskey().microtime().$downloadfilename);
        if (!$handle = fopen($tempfilename, 'w+b')) {
            throw new \moodle_exception('cannotcreatetempdir');
        }

        /// time stamp to ensure uniqueness of batch export
        fwrite($handle,  '<results batch="xml_export_'.time().'">'."\n");

        $export_buffer = array();

        $geub = new grade_export_update_buffer();
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->require_active_enrolment($this->onlyactive);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            $user = $userdata->user;

            if (empty($user->idnumber)) {
                //id number must exist otherwise we cant match up students when importing
                continue;
            }

            // studentgrades[] index should match with corresponding $index
            foreach ($userdata->grades as $itemid => $grade) {
                $grade_item = $this->grade_items[$itemid];
                $grade->grade_item =& $grade_item;

                // MDL-11669, skip exported grades or bad grades (if setting says so)
                if ($export_tracking) {
                    $status = $geub->track($grade);
                    if ($this->updatedgradesonly && ($status == 'nochange' || $status == 'unknown')) {
                        continue;
                    }
                }

                fwrite($handle,  "\t<result>\n");

                if ($export_tracking) {
                    fwrite($handle,  "\t\t<state>$status</state>\n");
                }

                // only need id number
                $gradeitemidnumber = self::xml_export_idnumber($grade_item->idnumber);
                fwrite($handle, "\t\t<assignment>{$gradeitemidnumber}</assignment>\n");
                // this column should be customizable to use either student id, idnumber, uesrname or email.
                $useridnumber = self::xml_export_idnumber($user->idnumber);
                fwrite($handle, "\t\t<student>{$useridnumber}</student>\n");
                // Format and display the grade in the selected display type (real, letter, percentage).
                if (is_array($this->displaytype)) {
                    // Grades display type came from the return of export_bulk_export_data() on grade publishing.
                    foreach ($this->displaytype as $gradedisplayconst) {
                        $gradestr = $this->format_grade($grade, $gradedisplayconst);
                        fwrite($handle,  "\t\t<score>$gradestr</score>\n");
                    }
                } else {
                    // Grade display type submitted directly from the grade export form.
                    $gradestr = $this->format_grade($grade, $this->displaytype);
                    fwrite($handle,  "\t\t<score>$gradestr</score>\n");
                }

                if ($this->export_feedback) {
                    $feedbackstr = $this->format_feedback($userdata->feedbacks[$itemid], $grade);
                    fwrite($handle,  "\t\t<feedback>$feedbackstr</feedback>\n");
                }
                fwrite($handle,  "\t</result>\n");
            }
        }
        fwrite($handle,  "</results>");
        fclose($handle);
        $gui->close();
        $geub->close();

        if (defined('BEHAT_SITE_RUNNING')) {
            // If behat is running, we cannot test the output if we force a file download.
            include($tempfilename);
        } else {
            @header("Content-type: text/xml; charset=UTF-8");
            send_temp_file($tempfilename, $downloadfilename, false);
        }
    }
}


