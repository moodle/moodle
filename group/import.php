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
 * @package core_group
 */

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
include_once('import_form.php');

$id = required_param('id', PARAM_INT);    // Course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

$PAGE->set_url('/group/import.php', array('id'=>$id));

require_login($course);
$context = context_course::instance($id);

require_capability('moodle/course:managegroups', $context);

$strimportgroups = get_string('importgroups', 'core_group');

$PAGE->navbar->add($strimportgroups);
navigation_node::override_active_url(new moodle_url('/group/index.php', array('id' => $course->id)));
$PAGE->set_title("$course->shortname: $strimportgroups");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('admin');

$returnurl = new moodle_url('/group/index.php', array('id'=>$id));

$importform = new groups_import_form(null, ['id' => $id]);

// If a file has been uploaded, then process it
if ($importform->is_cancelled()) {
    redirect($returnurl);

} else if ($formdata = $importform->get_data()) {
    echo $OUTPUT->header();

    $text = $importform->get_file_content('userfile');
    $text = preg_replace('!\r\n?!', "\n", $text);

    require_once($CFG->libdir . '/csvlib.class.php');
    $importid = csv_import_reader::get_new_iid('groupimport');
    $csvimport = new csv_import_reader($importid, 'groupimport');
    $delimiter = $formdata->delimiter_name;
    $encoding = $formdata->encoding;
    $readcount = $csvimport->load_csv_content($text, $encoding, $delimiter);

    if ($readcount === false) {
        throw new \moodle_exception('csvfileerror', 'error', $PAGE->url, $csvimport->get_error());
    } else if ($readcount == 0) {
        throw new \moodle_exception('csvemptyfile', 'error', $PAGE->url, $csvimport->get_error());
    } else if ($readcount == 1) {
        throw new \moodle_exception('csvnodata', 'error', $PAGE->url);
    }

    $csvimport->init();

    unset($text);

    // make arrays of valid fields for error checking
    $required = array("groupname" => 1);
    $optionaldefaults = array("lang" => 1);
    $optional = array("coursename" => 1,
            "idnumber" => 1,
            "groupidnumber" => 1,
            "description" => 1,
            "enrolmentkey" => 1,
            "groupingname" => 1,
            "enablemessaging" => 1,
        );
    // Check custom fields from group and grouping.
    $customfields = \core_group\customfield\group_handler::create()->get_fields();
    $customfieldnames = [];
    foreach ($customfields as $customfield) {
        $controller = \core_customfield\data_controller::create(0, null, $customfield);
        $customfieldnames['customfield_' . $customfield->get('shortname')] = 1;
    }
    $customfields = \core_group\customfield\grouping_handler::create()->get_fields();
    $groupingcustomfields = [];
    foreach ($customfields as $customfield) {
        $controller = \core_customfield\data_controller::create(0, null, $customfield);
        $groupingcustomfieldname = 'grouping_customfield_' . $customfield->get('shortname');
        $customfieldnames[$groupingcustomfieldname] = 1;
        $groupingcustomfields[$groupingcustomfieldname] = 'customfield_' . $customfield->get('shortname');
    }

    // --- get header (field names) ---
    // Using get_columns() ensures the Byte Order Mark is removed.
    $header = $csvimport->get_columns();

    // Check for valid field names.
    foreach ($header as $i => $h) {
        // Remove whitespace.
        $h = trim($h);
        $header[$i] = $h;
        if (!isset($required[$h]) && !isset($optionaldefaults[$h]) && !isset($optional[$h]) && !isset($customfieldnames[$h])) {
            throw new \moodle_exception('invalidfieldname', 'error', $PAGE->url, $h);
        }
        if (isset($required[$h])) {
            $required[$h] = 2;
        }
    }
    // check for required fields
    foreach ($required as $key => $value) {
        if ($value < 2) {
            throw new \moodle_exception('fieldrequired', 'error', $PAGE->url, $key);
        }
    }
    $linenum = 2; // since header is line 1

    while ($line = $csvimport->next()) {

        $newgroup = new stdClass();//to make Martin happy
        foreach ($optionaldefaults as $key => $value) {
            $newgroup->$key = current_language(); //defaults to current language
        }
        foreach ($line as $key => $value) {
            $record[$header[$key]] = trim($value);
        }
        if (trim(implode($line)) !== '') {
            // add a new group to the database

            // add fields to object $user
            foreach ($record as $name => $value) {
                // check for required values
                if (isset($required[$name]) and !$value) {
                    throw new \moodle_exception('missingfield', 'error', $PAGE->url, $name);
                } else if ($name == "groupname") {
                    $newgroup->name = $value;
                } else {
                // normal entry
                    $newgroup->{$name} = $value;
                }
            }

            if (isset($newgroup->idnumber) && strlen($newgroup->idnumber)) {
                //if idnumber is set, we use that.
                //unset invalid courseid
                if (!$mycourse = $DB->get_record('course', array('idnumber'=>$newgroup->idnumber))) {
                    echo $OUTPUT->notification(get_string('unknowncourseidnumber', 'error', $newgroup->idnumber));
                    unset($newgroup->courseid);//unset so 0 doesn't get written to database
                } else {
                    $newgroup->courseid = $mycourse->id;
                }

            } else if (isset($newgroup->coursename) && strlen($newgroup->coursename)) {
                //else use course short name to look up
                //unset invalid coursename (if no id)
                if (!$mycourse = $DB->get_record('course', array('shortname' => $newgroup->coursename))) {
                    echo $OUTPUT->notification(get_string('unknowncourse', 'error', $newgroup->coursename));
                    unset($newgroup->courseid);//unset so 0 doesn't get written to database
                } else {
                    $newgroup->courseid = $mycourse->id;
                }

            } else {
                //else use use current id
                $newgroup->courseid = $id;
            }
            unset($newgroup->idnumber);
            unset($newgroup->coursename);

            //if courseid is set
            if (isset($newgroup->courseid)) {
                $linenum++;
                $groupname = $newgroup->name;
                $newgrpcoursecontext = context_course::instance($newgroup->courseid);

                ///Users cannot upload groups in courses they cannot update.
                if (!has_capability('moodle/course:managegroups', $newgrpcoursecontext) or (!is_enrolled($newgrpcoursecontext) and !has_capability('moodle/course:view', $newgrpcoursecontext))) {
                    echo $OUTPUT->notification(get_string('nopermissionforcreation', 'group', $groupname));

                } else {
                    if (isset($newgroup->groupidnumber)) {
                        // The CSV field for the group idnumber is groupidnumber rather than
                        // idnumber as that field is already in use for the course idnumber.
                        $newgroup->groupidnumber = trim($newgroup->groupidnumber);
                        if (has_capability('moodle/course:changeidnumber', $newgrpcoursecontext)) {
                            $newgroup->idnumber = $newgroup->groupidnumber;
                            if ($existing = groups_get_group_by_idnumber($newgroup->courseid, $newgroup->idnumber)) {
                                // idnumbers must be unique to a course but we shouldn't ignore group creation for duplicates
                                $existing->name = s($existing->name);
                                $existing->idnumber = s($existing->idnumber);
                                $existing->problemgroup = $groupname;
                                echo $OUTPUT->notification(get_string('groupexistforcoursewithidnumber', 'error', $existing));
                                unset($newgroup->idnumber);
                            }
                        }
                        // Always drop the groupidnumber key. It's not a valid database field
                        unset($newgroup->groupidnumber);
                    }
                    if ($groupid = groups_get_group_by_name($newgroup->courseid, $groupname)) {
                        echo $OUTPUT->notification("$groupname :".get_string('groupexistforcourse', 'error', $groupname));
                    } else if ($groupid = groups_create_group($newgroup)) {
                        echo $OUTPUT->notification(get_string('groupaddedsuccesfully', 'group', $groupname), 'notifysuccess');
                    } else {
                        echo $OUTPUT->notification(get_string('groupnotaddederror', 'error', $groupname));
                        continue;
                    }

                    // Add group to grouping
                    if (isset($newgroup->groupingname) && strlen($newgroup->groupingname)) {
                        $groupingname = $newgroup->groupingname;
                        if (! $groupingid = groups_get_grouping_by_name($newgroup->courseid, $groupingname)) {
                            $data = new stdClass();
                            $data->courseid = $newgroup->courseid;
                            $data->name = $groupingname;
                            // Add customfield if exists.
                            foreach ($header as $fieldname) {
                                if (isset($customfieldnames[$fieldname]) && isset($newgroup->$fieldname)) {
                                    $data->{$groupingcustomfields[$groupingcustomfieldname]} = $newgroup->$fieldname;
                                }
                            }
                            if ($groupingid = groups_create_grouping($data)) {
                                echo $OUTPUT->notification(get_string('groupingaddedsuccesfully', 'group', $groupingname), 'notifysuccess');
                            } else {
                                echo $OUTPUT->notification(get_string('groupingnotaddederror', 'error', $groupingname));
                                continue;
                            }
                        }

                        // if we have reached here we definitely have a groupingid
                        $a = array('groupname' => $groupname, 'groupingname' => $groupingname);
                        try {
                            groups_assign_grouping($groupingid, $groupid);
                            echo $OUTPUT->notification(get_string('groupaddedtogroupingsuccesfully', 'group', $a), 'notifysuccess');
                        } catch (Exception $e) {
                            echo $OUTPUT->notification(get_string('groupnotaddedtogroupingerror', 'error', $a));
                        }

                    }
                }
            }
            unset ($newgroup);
        }
    }

    $csvimport->close();
    echo $OUTPUT->single_button($returnurl, get_string('continue'), 'get');
    echo $OUTPUT->footer();
    die;
}

/// Print the form
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($strimportgroups, 'importgroups', 'core_group');
$importform->display();
echo $OUTPUT->footer();
