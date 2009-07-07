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

class grade_export_xml extends grade_export {

    var $plugin = 'xml';
    var $updatedgradesonly = false; // default to export ALL grades
    
    /**
     * To be implemented by child classes
     * @param boolean $feedback
     * @param boolean $publish Whether to output directly, or send as a file
     * @return string
     */
    function print_grades($feedback = false) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $export_tracking = $this->track_exports();

        $strgrades = get_string('grades');

        /// Calculate file name
        $downloadfilename = clean_filename("{$this->course->shortname} $strgrades.xml");

        make_upload_directory('temp/gradeexport', false);
        $tempfilename = $CFG->dataroot .'/temp/gradeexport/'. md5(sesskey().microtime().$downloadfilename);
        if (!$handle = fopen($tempfilename, 'w+b')) {
            error("Could not create a temporary file into which to dump the XML data.");
            return false;
        }

        /// time stamp to ensure uniqueness of batch export
        fwrite($handle,  '<results batch="xml_export_'.time().'">'."\n");

        $export_buffer = array();

        $geub = new grade_export_update_buffer();
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            $user = $userdata->user;

            if (empty($user->idnumber)) {
                //id number must exist
                continue;
            }

            // studentgrades[] index should match with corresponding $index
            foreach ($userdata->grades as $itemid => $grade) {
                $grade_item = $this->grade_items[$itemid];
                $grade->grade_item =& $grade_item;
                $gradestr = $this->format_grade($grade); // no formating for now
                
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
                fwrite($handle,  "\t\t<assignment>{$grade_item->idnumber}</assignment>\n");
                // this column should be customizable to use either student id, idnumber, uesrname or email.
                fwrite($handle,  "\t\t<student>{$user->idnumber}</student>\n");
                fwrite($handle,  "\t\t<score>$gradestr</score>\n");
                if ($this->export_feedback) {
                    $feedbackstr = $this->format_feedback($userdata->feedbacks[$itemid]);
                    fwrite($handle,  "\t\t<feedback>$feedbackstr</feedback>\n");
                }
                fwrite($handle,  "\t</result>\n");
            }
        }
        fwrite($handle,  "</results>");
        fclose($handle);
        $gui->close();
        $geub->close();

        if (strpos($CFG->wwwroot, 'https://') === 0) { //https sites - watch out for IE! KB812935 and KB316431
            @header('Cache-Control: max-age=10');
            @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            @header('Pragma: ');
        } else { //normal http - prevent caching at all cost
            @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
            @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            @header('Pragma: no-cache');
        }
        header("Content-type: text/xml; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$downloadfilename\"");

        readfile_chunked($tempfilename);

        @unlink($tempfilename);

        exit();
    }
}

?>
