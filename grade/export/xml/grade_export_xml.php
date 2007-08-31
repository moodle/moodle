<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot.'/grade/export/lib.php');

class grade_export_xml extends grade_export {

    var $plugin = 'xml';

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

        $strgrades = get_string('grades', 'grade');

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

        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            $user = $userdata->user;

            // studentgrades[] index should match with corresponding $index
            foreach ($userdata->grades as $itemid => $grade) {
                $grade_item = $this->grade_items[$itemid];
                $grade->grade_item =& $grade_item;
                $gradestr = $this->format_grade($grade);

                fwrite($handle,  "\t<result>\n");
                // if exported, check grade_history, if modified after export, set state to regrade
                $status = 'new';
/*                if (!empty($grade_grade->exported)) {
                    //TODO: use timemodified or something else instead
                    if (record_exists_select('grade_history', 'itemid = '.$gradeitem->id.' AND userid = '.$userid.' AND timemodified > '.$grade_grade->exported)) {
                        $status = 'regrade';
                    } else {
                        $status = 'new';
                    }
                } else {
                    // never exported
                    $status = 'new';
                }
*/
                fwrite($handle,  "\t\t<state>$status</state>\n");
                // only need id number
                fwrite($handle,  "\t\t<assignment>{$grade_item->idnumber}</assignment>\n");
                // this column should be customizable to use either student id, idnumber, uesrname or email.
                fwrite($handle,  "\t\t<student>{$user->id}</student>\n");
                fwrite($handle,  "\t\t<score>$gradestr</score>\n");
                if ($this->export_feedback) {
                    $feedbackstr = $this->format_feedback($userdata->feedbacks[$itemid]);
                    fwrite($handle,  "\t\t<feedback>$feedbackstr</feedback>\n");
                }
                fwrite($handle,  "\t</result>\n");

                // timestamp this if needed
/*                if ($export) {
                    $grade_grade->exported = time();
                    // update the time stamp;
                    $grade_grade->update();
                }
*/
            }
        }
        fwrite($handle,  "</results>");
        fclose($handle);

        @header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        @header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        @header('Pragma: no-cache');
        header("Content-type: text/xml; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$downloadfilename\"");

        readfile_chunked($tempfilename);

        @unlink($tempfilename);

        exit();
    }
}

?>
