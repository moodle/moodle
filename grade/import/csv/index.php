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

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once '../grade_import_form.php';
require_once '../lib.php';

$id            = required_param('id', PARAM_INT); // course id
$separator     = optional_param('separator', '', PARAM_ALPHA);
$verbosescales = optional_param('verbosescales', 1, PARAM_BOOL);

$url = new moodle_url('/grade/import/csv/index.php', array('id'=>$id));
if ($separator !== '') {
    $url->param('separator', $separator);
}
if ($verbosescales !== 1) {
    $url->param('verbosescales', $verbosescales);
}
$PAGE->set_url($url);

define('GRADE_CSV_LINE_LENGTH', 4096);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);
require_capability('moodle/grade:import', $context);
require_capability('gradeimport/csv:view', $context);

$separatemode = (groups_get_course_groupmode($COURSE) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context));
$currentgroup = groups_get_course_group($course);

// sort out delimiter
if (isset($CFG->CSV_DELIMITER)) {
    $csv_delimiter = $CFG->CSV_DELIMITER;

    if (isset($CFG->CSV_ENCODE)) {
        $csv_encode = '/\&\#' . $CFG->CSV_ENCODE . '/';
    }
} else if ($separator == 'tab') {
    $csv_delimiter = "\t";
    $csv_encode = "";
} else {
    $csv_delimiter = ",";
    $csv_encode = '/\&\#44/';
}

print_grade_page_head($course->id, 'import', 'csv', get_string('importcsv', 'grades'));

// set up import form
$mform = new grade_import_form(null, array('includeseparator'=>!isset($CFG->CSV_DELIMITER), 'verbosescales'=>true));

// set up grade import mapping form
$header = '';
$gradeitems = array();
if ($id) {
    if ($grade_items = grade_item::fetch_all(array('courseid'=>$id))) {
        foreach ($grade_items as $grade_item) {
            // skip course type and category type
            if ($grade_item->itemtype == 'course' || $grade_item->itemtype == 'category') {
                continue;
            }

            $displaystring = null;
            if (!empty($grade_item->itemmodule)) {
                $displaystring = get_string('modulename', $grade_item->itemmodule).': '.$grade_item->get_name();
            } else {
                $displaystring = $grade_item->get_name();
            }
            $gradeitems[$grade_item->id] = $displaystring;
        }
    }
}

if ($importcode = optional_param('importcode', '', PARAM_FILE)) {
    $filename = $CFG->dataroot.'/temp/gradeimport/cvs/'.$USER->id.'/'.$importcode;
    $fp = fopen($filename, "r");
    $headers = fgets($fp, GRADE_CSV_LINE_LENGTH);
    $header = explode($csv_delimiter, $headers);
    fclose($fp);
}

$mform2 = new grade_import_mapping_form(null, array('gradeitems'=>$gradeitems, 'header'=>$header));

// if import form is submitted
if ($formdata = $mform->get_data()) {

    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    @set_time_limit(0);
    raise_memory_limit(MEMORY_EXTRA);

    // use current (non-conflicting) time stamp
    $importcode = get_new_importcode();
    $filename = make_upload_directory('temp/gradeimport/cvs/'.$USER->id);
    $filename = $filename.'/'.$importcode;

    $text = $mform->get_file_content('userfile');
    // trim utf-8 bom
    $textlib = textlib_get_instance();
    /// normalize line endings and do the encoding conversion
    $text = $textlib->convert($text, $formdata->encoding);
    $text = $textlib->trim_utf8_bom($text);
    // Fix mac/dos newlines
    $text = preg_replace('!\r\n?!',"\n",$text);
    $fp = fopen($filename, "w");
    fwrite($fp,$text);
    fclose($fp);

    $fp = fopen($filename, "r");

    // --- get header (field names) ---
    $header = explode($csv_delimiter, fgets($fp, GRADE_CSV_LINE_LENGTH));

    // print some preview
    $numlines = 0; // 0 preview lines displayed

    echo $OUTPUT->heading(get_string('importpreview', 'grades'));
    echo '<table>';
    echo '<tr>';
    foreach ($header as $h) {
        $h = clean_param($h, PARAM_RAW);
        echo '<th>'.$h.'</th>';
    }
    echo '</tr>';
    while (!feof ($fp) && $numlines <= $formdata->previewrows) {
        $lines = explode($csv_delimiter, fgets($fp, GRADE_CSV_LINE_LENGTH));
        echo '<tr>';
        foreach ($lines as $line) {
            echo '<td>'.$line.'</td>';
        }
        $numlines ++;
        echo '</tr>';
    }
    echo '</table>';

    // display the mapping form with header info processed
    $mform2 = new grade_import_mapping_form(null, array('gradeitems'=>$gradeitems, 'header'=>$header));
    $mform2->set_data(array('importcode'=>$importcode, 'id'=>$id, 'verbosescales'=>$verbosescales, 'separator'=>$separator));
    $mform2->display();

//} else if (($formdata = data_submitted()) && !empty($formdata->map)) {

// else if grade import mapping form is submitted
} else if ($formdata = $mform2->get_data()) {

    $importcode = clean_param($formdata->importcode, PARAM_FILE);
    $filename = $CFG->dataroot.'/temp/gradeimport/cvs/'.$USER->id.'/'.$importcode;

    if (!file_exists($filename)) {
        print_error('cannotuploadfile');
    }

    if ($fp = fopen($filename, "r")) {
        // --- get header (field names) ---
        $header = explode($csv_delimiter, clean_param(fgets($fp,GRADE_CSV_LINE_LENGTH), PARAM_RAW));

        foreach ($header as $i => $h) {
            $h = trim($h); $header[$i] = $h; // remove whitespace
        }
    } else {
        print_error('cannotopenfile');
    }

    $map = array();
    // loops mapping_0, mapping_1 .. mapping_n and construct $map array
    foreach ($header as $i => $head) {
        if (isset($formdata->{'mapping_'.$i})) {
            $map[$i] = $formdata->{'mapping_'.$i};
        }
    }

    // if mapping information is supplied
    $map[clean_param($formdata->mapfrom, PARAM_RAW)] = clean_param($formdata->mapto, PARAM_RAW);

    // check for mapto collisions
    $maperrors = array();
    foreach ($map as $i => $j) {
        if ($j == 0) {
            // you can have multiple ignores
            continue;
        } else {
            if (!isset($maperrors[$j])) {
                $maperrors[$j] = true;
            } else {
                // collision
                fclose($fp);
                unlink($filename); // needs to be uploaded again, sorry
                print_error('cannotmapfield', '', '', $j);
            }
        }
    }

    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    @set_time_limit(0);
    raise_memory_limit(MEMORY_EXTRA);

    // we only operate if file is readable
    if ($fp = fopen($filename, "r")) {

        // read the first line makes sure this doesn't get read again
        $header = explode($csv_delimiter, fgets($fp, GRADE_CSV_LINE_LENGTH));

        $newgradeitems = array(); // temporary array to keep track of what new headers are processed
        $status = true;

        while (!feof ($fp)) {
            // add something
            $line = explode($csv_delimiter, fgets($fp, GRADE_CSV_LINE_LENGTH));

            if(count($line) <= 1){
                // there is no data on this line, move on
                continue;
            }

            // array to hold all grades to be inserted
            $newgrades = array();
            // array to hold all feedback
            $newfeedbacks = array();
            // each line is a student record
            foreach ($line as $key => $value) {
                //decode encoded commas
                $value = clean_param($value, PARAM_RAW);
                $value = trim($value);
                if (!empty($csv_encode)) {
                    $value = preg_replace($csv_encode, $csv_delimiter, $value);
                }

                /*
                 * the options are
                 * 1) userid, useridnumber, usermail, username - used to identify user row
                 * 2) new - new grade item
                 * 3) id - id of the old grade item to map onto
                 * 3) feedback_id - feedback for grade item id
                 */

                $t = explode("_", $map[$key]);
                $t0 = $t[0];
                if (isset($t[1])) {
                    $t1 = (int)$t[1];
                } else {
                    $t1 = '';
                }

                switch ($t0) {
                    case 'userid': //
                        if (!$user = $DB->get_record('user', array('id' => $value))) {
                            // user not found, abort whole import
                            import_cleanup($importcode);
                            echo $OUTPUT->notification("user mapping error, could not find user with id \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $value;
                    break;
                    case 'useridnumber':
                        if (!$user = $DB->get_record('user', array('idnumber' => $value))) {
                             // user not found, abort whole import
                            import_cleanup($importcode);
                            echo $OUTPUT->notification("user mapping error, could not find user with idnumber \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $user->id;
                    break;
                    case 'useremail':
                        if (!$user = $DB->get_record('user', array('email' => $value))) {
                            import_cleanup($importcode);
                            echo $OUTPUT->notification("user mapping error, could not find user with email address \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $user->id;
                    break;
                    case 'username':
                        if (!$user = $DB->get_record('user', array('username' => $value))) {
                            import_cleanup($importcode);
                            echo $OUTPUT->notification("user mapping error, could not find user with username \"$value\"");
                            $status = false;
                            break 3;
                        }
                        $studentid = $user->id;
                    break;
                    case 'new':
                        // first check if header is already in temp database

                        if (empty($newgradeitems[$key])) {

                            $newgradeitem = new stdClass();
                            $newgradeitem->itemname = $header[$key];
                            $newgradeitem->importcode = $importcode;
                            $newgradeitem->importer   = $USER->id;

                            // insert into new grade item buffer
                            $newgradeitems[$key] = $DB->insert_record('grade_import_newitem', $newgradeitem);
                        }
                        $newgrade = new stdClass();
                        $newgrade->newgradeitem = $newgradeitems[$key];

                        // if the user has a grade for this grade item
                        if (trim($value) != '-') {
                            // instead of omitting the grade we could insert one with finalgrade set to 0
                            // we do not have access to grade item min grade
                            $newgrade->finalgrade   = $value;
                            $newgrades[] = $newgrade;
                        }
                    break;
                    case 'feedback':
                        if ($t1) {
                            // case of an id, only maps id of a grade_item
                            // this was idnumber
                            if (!$gradeitem = new grade_item(array('id'=>$t1, 'courseid'=>$course->id))) {
                                // supplied bad mapping, should not be possible since user
                                // had to pick mapping
                                $status = false;
                                import_cleanup($importcode);
                                echo $OUTPUT->notification(get_string('importfailed', 'grades'));
                                break 3;
                            }

                            // t1 is the id of the grade item
                            $feedback = new stdClass();
                            $feedback->itemid   = $t1;
                            $feedback->feedback = $value;
                            $newfeedbacks[] = $feedback;
                        }
                    break;
                    default:
                        // existing grade items
                        if (!empty($map[$key])) {
                            // case of an id, only maps id of a grade_item
                            // this was idnumber
                            if (!$gradeitem = new grade_item(array('id'=>$map[$key], 'courseid'=>$course->id))) {
                                // supplied bad mapping, should not be possible since user
                                // had to pick mapping
                                $status = false;
                                import_cleanup($importcode);
                                echo $OUTPUT->notification(get_string('importfailed', 'grades'));
                                break 3;
                            }

                            // check if grade item is locked if so, abort
                            if ($gradeitem->is_locked()) {
                                $status = false;
                                import_cleanup($importcode);
                                echo $OUTPUT->notification(get_string('gradeitemlocked', 'grades'));
                                break 3;
                            }

                            $newgrade = new stdClass();
                            $newgrade->itemid     = $gradeitem->id;
                            if ($gradeitem->gradetype == GRADE_TYPE_SCALE and $verbosescales) {
                                if ($value === '' or $value == '-') {
                                    $value = null; // no grade
                                } else {
                                    $scale = $gradeitem->load_scale();
                                    $scales = explode(',', $scale->scale);
                                    $scales = array_map('trim', $scales); //hack - trim whitespace around scale options
                                    array_unshift($scales, '-'); // scales start at key 1
                                    $key = array_search($value, $scales);
                                    if ($key === false) {
                                        echo "<br/>t0 is $t0";
                                        echo "<br/>grade is $value";
                                        $status = false;
                                        import_cleanup($importcode);
                                        echo $OUTPUT->notification(get_string('badgrade', 'grades'));
                                        break 3;
                                    }
                                    $value = $key;
                                }
                                $newgrade->finalgrade = $value;
                            } else {
                                if ($value === '' or $value == '-') {
                                    $value = null; // no grade

                                } else if (!is_numeric($value)) {
                                // non numeric grade value supplied, possibly mapped wrong column
                                    echo "<br/>t0 is $t0";
                                    echo "<br/>grade is $value";
                                    $status = false;
                                    import_cleanup($importcode);
                                    echo $OUTPUT->notification(get_string('badgrade', 'grades'));
                                    break 3;
                                }
                                $newgrade->finalgrade = $value;
                            }
                            $newgrades[] = $newgrade;
                        } // otherwise, we ignore this column altogether
                          // because user has chosen to ignore them (e.g. institution, address etc)
                    break;
                }
            }

            // no user mapping supplied at all, or user mapping failed
            if (empty($studentid) || !is_numeric($studentid)) {
                // user not found, abort whole import
                $status = false;
                import_cleanup($importcode);
                echo $OUTPUT->notification('user mapping error, could not find user!');
                break;
            }

            if ($separatemode and !groups_is_member($currentgroup, $studentid)) {
                // not allowed to import into this group, abort
                $status = false;
                import_cleanup($importcode);
                echo $OUTPUT->notification('user not member of current group, can not update!');
                break;
            }

            // insert results of this students into buffer
            if ($status and !empty($newgrades)) {

                foreach ($newgrades as $newgrade) {

                    // check if grade_grade is locked and if so, abort
                    if (!empty($newgrade->itemid) and $grade_grade = new grade_grade(array('itemid'=>$newgrade->itemid, 'userid'=>$studentid))) {
                        if ($grade_grade->is_locked()) {
                            // individual grade locked
                            $status = false;
                            import_cleanup($importcode);
                            echo $OUTPUT->notification(get_string('gradelocked', 'grades'));
                            break 2;
                        }
                    }

                    $newgrade->importcode = $importcode;
                    $newgrade->userid     = $studentid;
                    $newgrade->importer   = $USER->id;
                    $DB->insert_record('grade_import_values', $newgrade);
                }
            }

            // updating/inserting all comments here
            if ($status and !empty($newfeedbacks)) {
                foreach ($newfeedbacks as $newfeedback) {
                    $sql = "SELECT *
                              FROM {grade_import_values}
                             WHERE importcode=? AND userid=? AND itemid=? AND importer=?";
                    if ($feedback = $DB->get_record_sql($sql, array($importcode, $studentid, $newfeedback->itemid, $USER->id))) {
                        $newfeedback->id = $feedback->id;
                        $DB->update_record('grade_import_values', $newfeedback);

                    } else {
                        // the grade item for this is not updated
                        $newfeedback->importcode = $importcode;
                        $newfeedback->userid     = $studentid;
                        $newfeedback->importer   = $USER->id;
                        $DB->insert_record('grade_import_values', $newfeedback);
                    }
                }
            }
        }

        /// at this stage if things are all ok, we commit the changes from temp table
        if ($status) {
            grade_import_commit($course->id, $importcode);
        }
        // temporary file can go now
        fclose($fp);
        unlink($filename);
    } else {
        print_error('cannotreadfile');
    }

} else {
    groups_print_course_menu($course, 'index.php?id='.$id);
    echo '<div class="clearer"></div>';

    // display the standard upload file form
    $mform->display();
}

echo $OUTPUT->footer();

