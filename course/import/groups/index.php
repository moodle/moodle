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
 * Bulk group creation registration script from a comma separated file
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
include_once('import_form.php');

$id = required_param('id', PARAM_INT);    // Course id

$PAGE->set_url('/course/import/groups/index.php', array('id'=>$id));

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}

require_login($course->id);
$context = get_context_instance(CONTEXT_COURSE, $id);

if (!has_capability('moodle/course:managegroups', $context)) {
    print_error('nopermissiontomanagegroup');
}

$stradministration = get_string("administration");
$strimportgroups   = get_string("importgroups");
$streditmyprofile  = get_string("editmyprofile");
$strchoose         = get_string("choose");
$struser           = get_string("user");
$strusers          = get_string("users");
$strusersnew       = get_string("usersnew");

/// Print the header
$PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', array('id'=>$course->id)));
$PAGE->navbar->add(get_string('import'), new moodle_url('/course/import.php', array('id'=>$course->id)));
$PAGE->navbar->add($strimportgroups);

$PAGE->set_title("$course->shortname: $strimportgroups");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

$mform_post = new course_import_groups_form($CFG->wwwroot.'/course/import/groups/index.php?id='.$id);

// If a file has been uploaded, then process it
if (!$mform_post->get_data()) {
    echo $OUTPUT->heading($strimportgroups);
    /// Print the form
    $mform_post ->display();
    echo $OUTPUT->footer();
die;
} else {
    $csv_encode = '/\&\#44/';
    if (isset($CFG->CSV_DELIMITER)) {
        $csv_delimiter = '\\' . $CFG->CSV_DELIMITER;
        $csv_delimiter2 = $CFG->CSV_DELIMITER;

        if (isset($CFG->CSV_ENCODE)) {
            $csv_encode = '/\&\#' . $CFG->CSV_ENCODE . '/';
        }
    } else {
        $csv_delimiter = "\,";
        $csv_delimiter2 = ",";
    }

    // prepare temp file
    $filename = $CFG->dataroot . '/temp/groupimport/importedfile_'.time().'.csv';
    make_upload_directory('temp/groupimport');
    //Fix mac/dos newlines
    $text = $mform_post->get_file_content('userfile');
    $text = preg_replace('!\r\n?!',"\n",$text);
    $fp = fopen($filename, "w");
    fwrite($fp,$text);
    fclose($fp);

    $fp = fopen($filename, "r");

    // make arrays of valid fields for error checking
    $required = array("groupname" => 1);
    $optionalDefaults = array("lang" => 1);
    $optional = array("coursename" => 1,
            "idnumber" => 1,
            "description" => 1,
            "enrolmentkey" => 1,
            "theme" => 1,
            "picture" => 1,
            "hidepicture" => 1);

    // --- get header (field names) ---
    $header = split($csv_delimiter, fgets($fp,1024));
    // check for valid field names
    foreach ($header as $i => $h) {
        $h = trim($h); $header[$i] = $h; // remove whitespace
        if ( !(isset($required[$h]) or
            isset($optionalDefaults[$h]) or
            isset($optional[$h])) ) {
                print_error('invalidfieldname', 'error', 'index.php?id='.$id.'&amp;sesskey='.sesskey(), $h);
            }
        if ( isset($required[$h]) ) {
            $required[$h] = 2;
        }
    }
    // check for required fields
    foreach ($required as $key => $value) {
        if ($value < 2) {
            print_error('fieldrequired', 'error', 'uploaduser.php?id='.$id.'&amp;sesskey='.sesskey(), $key);
        }
    }
    $linenum = 2; // since header is line 1

    while (!feof ($fp)) {

        $newgroup = new object();//to make Martin happy
        foreach ($optionalDefaults as $key => $value) {
            $newgroup->$key = current_language(); //defaults to current language
        }
        //Note: commas within a field should be encoded as &#44 (for comma separated csv files)
        //Note: semicolon within a field should be encoded as &#59 (for semicolon separated csv files)
        $line = split($csv_delimiter, fgets($fp,1024));
        foreach ($line as $key => $value) {
            //decode encoded commas
            $record[$header[$key]] = preg_replace($csv_encode,$csv_delimiter2,trim($value));
        }
        if ($record[$header[0]]) {
            // add a new group to the database

            // add fields to object $user
            foreach ($record as $name => $value) {
                // check for required values
                if (isset($required[$name]) and !$value) {
                    print_error('missingfield', 'error', 'uploaduser.php?sesskey='.sesskey(), $name);
                }
                else if ($name == "groupname") {
                    $newgroup->name = $value;
                }
                // normal entry
                else {
                    $newgroup->{$name} = $value;
                }
            }
            ///Find the courseid of the course with the given shortname

            //if idnumber is set, we use that.
            //unset invalid courseid
            if (isset($newgroup->idnumber)){
                if (!$mycourse = $DB->get_record('course', array('idnumber'=>$newgroup->idnumber))) {
                    echo $OUTPUT->notification(get_string('unknowncourseidnumber', 'error', $newgroup->idnumber));
                    unset($newgroup->courseid);//unset so 0 doesnt' get written to database
                }
                $newgroup->courseid = $mycourse->id;
            }
            //else use course short name to look up
            //unset invalid coursename (if no id)

            else if (isset($newgroup->coursename)){
                if (!$mycourse = $DB->get_record('course', array('shortname', $newgroup->coursename))) {
                    echo $OUTPUT->notification(get_string('unknowncourse', 'error', $newgroup->coursename));
                    unset($newgroup->courseid);//unset so 0 doesnt' get written to database
                }
                $newgroup->courseid = $mycourse->id;
            }
            //else juse use current id
            else{
                $newgroup->courseid = $id;
            }

            //if courseid is set
            if (isset($newgroup->courseid)){

                $newgroup->courseid = (int)$newgroup->courseid;
                $newgroup->timecreated = time();
                $linenum++;
                $groupname = $newgroup->name;
                $newgrpcoursecontext = get_context_instance(CONTEXT_COURSE, $newgroup->courseid);

                ///Users cannot upload groups in courses they cannot update.
                if (!has_capability('moodle/course:managegroups', $newgrpcoursecontext)){
                    echo $OUTPUT->notification(get_string('nopermissionforcreation','group',$groupname));

                } else {
                    if ( $groupid = groups_get_group_by_name($newgroup->courseid, $groupname) || !($newgroup->id = groups_create_group($newgroup)) ) {

                        //Record not added - probably because group is already registered
                        //In this case, output groupname from previous registration
                        if ($groupid) {
                            echo $OUTPUT->notification("$groupname :".get_string('groupexistforcourse', 'error', $groupname));
                        } else {
                            echo $OUTPUT->notification(get_string('groupnotaddederror', 'error', $groupname));
                        }
                    }
                    else {
                        echo $OUTPUT->notification(get_string('groupaddedsuccesfully', 'group', $groupname));
                    }
                }
            } //close courseid validity check
            unset ($newgroup);
        }//close if ($record[$header[0]])
    }//close while($fp)
    fclose($fp);
    // remove temp file
    unlink($filename);

    echo '<hr />';
}
