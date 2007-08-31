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

    /**
     * To be implemented by child classes
     * @param boolean $feedback
     * @param boolean $publish Whether to output directly, or send as a file
     * @return string
     */
    function print_grades($feedback = false) {
        global $CFG;

        $this->load_grades();

        $retval = '';

        /// Whether this plugin is entitled to update export time
        if ($expplugins = explode(",", $CFG->gradeexport)) {
            if (in_array('xml', $expplugins)) {
                $export = true;
            } else {
                $export = false;
          }
        } else {
            $export = false;
        }

        /// Calculate file name
        $downloadfilename = clean_filename("{$this->course->shortname} $this->strgrades.xml");

        $tempfilename = $CFG->dataroot . MD5(microtime()) . $downloadfilename;
        if (!$handle = fopen($tempfilename, 'w+b')) {
            error("Could not create a temporary file into which to dump the XML data.");
            return false;
        }

        /// time stamp to ensure uniqueness of batch export
        fwrite($handle,  '<results batch="xml_export_'.time().'">'."\n");

        foreach ($this->columnidnumbers as $index => $idnumber) {

            // studentgrades[] index should match with corresponding $index
            foreach ($this->grades as $studentid => $studentgrades) {
                fwrite($handle,  "\t<result>\n");

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
                $status = 'new';
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

                fwrite($handle,  "\t\t<state>$status</state>\n");
                // only need id number
                fwrite($handle,  "\t\t<assignment>$idnumber</assignment>\n");
                // this column should be customizable to use either student id, idnumber, uesrname or email.
                fwrite($handle,  "\t\t<student>$studentid</student>\n");
                fwrite($handle,  "\t\t<score>{$studentgrades[$index]}</score>\n");
                if ($feedback) {
                    fwrite($handle,  "\t\t<feedback>{$this->comments[$studentid][$index]}</feedback>\n");
                }
                fwrite($handle,  "\t</result>\n");

                // timestamp this if needed
                if ($export) {
                    $grade_grade->exported = time();
                    // update the time stamp;
                    $grade_grade->update();
                }
            }
        }
        fwrite($handle,  "</results>");
        fclose($handle);

        require_once($CFG->libdir . '/filelib.php');

        header("Content-type: text/xml; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$downloadfilename\"");

        readfile_chunked($tempfilename);

        unlink($tempfilename);

        exit();
    }
}

?>
