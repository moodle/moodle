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

class grade_export_ods extends grade_export {

    var $plugin = 'ods';

    /**
     * To be implemented by child classes
     */
    function print_grades() {
        global $CFG;
        require_once($CFG->dirroot.'/lib/odslib.class.php');

        $export_tracking = $this->track_exports();

        $strgrades = get_string('grades');

    /// Calculate file name
        $downloadfilename = clean_filename("{$this->course->shortname} $strgrades.ods");
    /// Creating a workbook
        $workbook = new MoodleODSWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($strgrades);

    /// Print names of all the fields
        $myxls->write_string(0,0,get_string("firstname"));
        $myxls->write_string(0,1,get_string("lastname"));
        $myxls->write_string(0,2,get_string("idnumber"));
        $myxls->write_string(0,3,get_string("institution"));
        $myxls->write_string(0,4,get_string("department"));
        $myxls->write_string(0,5,get_string("email"));
        $pos=6;
        foreach ($this->columns as $grade_item) {
            $myxls->write_string(0, $pos++, $this->format_column_name($grade_item));

            /// add a column_feedback column
            if ($this->export_feedback) {
                $myxls->write_string(0, $pos++, $this->format_column_name($grade_item, true));
            }
        }

    /// Print all the lines of data.
        $i = 0;
        $geub = new grade_export_update_buffer();
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            $i++;
            $user = $userdata->user;

            $myxls->write_string($i,0,$user->firstname);
            $myxls->write_string($i,1,$user->lastname);
            $myxls->write_string($i,2,$user->idnumber);
            $myxls->write_string($i,3,$user->institution);
            $myxls->write_string($i,4,$user->department);
            $myxls->write_string($i,5,$user->email);
            $j=6;
            foreach ($userdata->grades as $itemid => $grade) {
                if ($export_tracking) {
                    $status = $geub->track($grade);
                }

                $gradestr = $this->format_grade($grade);
                if (is_numeric($gradestr)) {
                    $myxls->write_number($i,$j++,$gradestr);
                }
                else {
                    $myxls->write_string($i,$j++,$gradestr);
                }

                // writing feedback if requested
                if ($this->export_feedback) {
                    $myxls->write_string($i, $j++, $this->format_feedback($userdata->feedbacks[$itemid]));
                }
            }
        }
        $gui->close();
        $geub->close();

    /// Close the workbook
        $workbook->close();

        exit;
    }
}

?>
