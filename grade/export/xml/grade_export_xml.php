<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
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

    var $format = 'xml'; // export format

    /**
     * To be implemented by child classes
     */
    function print_grades($feedback = false) {

        global $CFG;

        /// Whether this plugin is entitled to update export time
        if ($expplugins = explode(",", $CFG->gradeexport)) {
            if (in_array($this->format, $expplugins)) {
                $export = true;
            } else {
            $export = false;
          }
        } else {
            $export = false;
        }

        require_once($CFG->dirroot.'/lib/excellib.class.php');

        /// Calculate file name
        $downloadfilename = clean_filename("{$this->course->shortname} $this->strgrades.xml");

        header("Content-type: text/xml; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$downloadfilename\"");

        /// time stamp to ensure uniqueness of batch export
        echo '<results batch="xml_export_'.time().'">';

        foreach ($this->columnidnumbers as $index => $idnumber) {

            // studentgrades[] index should match with corresponding $index
            foreach ($this->grades as $studentid => $studentgrades) {
                echo '<result>';

                // state can be new, or regrade
                // require comparing of timestamps in db

                $params = new object();
                $params->idnumber = $idnumber;
                // get the grade item
                $gradeitem = new grade_item($params);

                // we are trying to figure out if this is a new grade, or a regraded grade
                // only relevant if this grade for this user is already exported

                // get the grade_grade for this user
                $params = new object();
                $params->itemid = $gradeitem->id;
                $params->userid = $studentid;

                $grade_grade = new grade_grade($params);

                // if exported, check grade_history, if modified after export, set state to regrade
                if (!empty($grade_grade->exported)) {
                    //TODO: use timemodified or something else instead
/*                    if (record_exists_select('grade_history', 'itemid = '.$gradeitem->id.' AND userid = '.$studentid.' AND timemodified > '.$grade_grade->exported)) {
                        $status = 'regrade';
                    } else {
                        $status = 'new';
                    }*/
                } else {
                    // never exported
                    $status = 'new';
                }

                echo '<state>'.$status.'</state>';
                // only need id number
                echo '<assignment>'.$idnumber.'</assignment>';
                // this column should be customizable to use either student id, idnumber, uesrname or email.
                echo '<student>'.$studentid.'</student>';
		        echo '<score>'.$studentgrades[$index].'</score>';
		        if ($feedback) {
                    echo '<feedback>'.$this->comments[$studentid][$index].'</feedback>';
		        }
                echo '</result>';

                // timestamp this if needed
                if ($export) {
                    $grade_grade->exported = time();
                    // update the time stamp;
                    $grade_grade->update();
                }
            }
        }
        echo '</results>';
        exit;
    }
}

?>