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
 * This file is part of the Database module for Moodle
 *
 * @copyright 2005 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod-data
 */

require_once('../../config.php');
require_once('lib.php');
require_once("$CFG->libdir/rsslib.php");

$id    = optional_param('id', 0, PARAM_INT);    // course module id
$d     = optional_param('d', 0, PARAM_INT);    // database id
$rid   = optional_param('rid', 0, PARAM_INT);    //record id
$cancel   = optional_param('cancel', '', PARAM_RAW);    // cancel an add
$mode ='addtemplate';    //define the mode for this page, only 1 mode available

$url = new moodle_url('/mod/data/edit.php');
if ($rid !== 0) {
    $url->param('rid', $rid);
}
if ($cancel !== '') {
    $url->param('cancel', $cancel);
}

if ($id) {
    $url->param('id', $id);
    $PAGE->set_url($url);
    if (! $cm = get_coursemodule_from_id('data', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $data = $DB->get_record('data', array('id'=>$cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else {
    $url->param('d', $d);
    $PAGE->set_url($url);
    if (! $data = $DB->get_record('data', array('id'=>$d))) {
        print_error('invalidid', 'data');
    }
    if (! $course = $DB->get_record('course', array('id'=>$data->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_login($course->id, false, $cm);

if (isguestuser()) {
    redirect('view.php?d='.$data->id);
}

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

/// If it's hidden then it doesn't show anything.  :)
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    $strdatabases = get_string("modulenameplural", "data");

    $PAGE->set_title(format_string($data->name));
    $PAGE->set_heading(format_string($course->fullname));
    echo $OUTPUT->header();
    notice(get_string("activityiscurrentlyhidden"));
}

/// Can't use this if there are no fields
if (has_capability('mod/data:managetemplates', $context)) {
    if (!$DB->record_exists('data_fields', array('dataid'=>$data->id))) {      // Brand new database!
        redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
    }
}

if ($rid) {    // So do you have access?
    if (!(has_capability('mod/data:manageentries', $context) or data_isowner($rid)) or !confirm_sesskey() ) {
        print_error('noaccess','data');
    }
}

if ($cancel) {
    redirect('view.php?d='.$data->id);
}


/// RSS and CSS and JS meta
if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
    $rsspath = rss_get_url($context->id, $USER->id, 'mod_data', $data->id);
    $courseshortname = format_string($course->shortname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
    $PAGE->add_alternate_version($courseshortname . ': %fullname%', $rsspath, 'application/rss+xml');
}
if ($data->csstemplate) {
    $PAGE->requires->css('/mod/data/css.php?d='.$data->id);
}
if ($data->jstemplate) {
    $PAGE->requires->js('/mod/data/js.php?d='.$data->id, true);
}

$possiblefields = $DB->get_records('data_fields', array('dataid'=>$data->id), 'id');

foreach ($possiblefields as $field) {
    if ($field->type == 'file' || $field->type == 'picture') {
        require_once($CFG->dirroot.'/repository/lib.php');
        break;
    }
}

/// Define page variables
$strdata = get_string('modulenameplural','data');

if ($rid) {
    $PAGE->navbar->add(get_string('editentry', 'data'));
}

$PAGE->set_title($data->name);
$PAGE->set_heading($course->fullname);

/// Check to see if groups are being used here
$currentgroup = groups_get_activity_group($cm);
$groupmode = groups_get_activity_groupmode($cm);

if ($currentgroup) {
    $groupselect = " AND groupid = '$currentgroup'";
    $groupparam = "&amp;groupid=$currentgroup";
} else {
    $groupselect = "";
    $groupparam = "";
    $currentgroup = 0;
}


/// Process incoming data for adding/updating records

if ($datarecord = data_submitted() and confirm_sesskey()) {

    $ignorenames = array('MAX_FILE_SIZE','sesskey','d','rid','saveandview','cancel');  // strings to be ignored in input data

    if ($rid) {                                          /// Update some records

        /// All student edits are marked unapproved by default
        $record = $DB->get_record('data_records', array('id'=>$rid));

        /// reset approved flag after student edit
        if (!has_capability('mod/data:approve', $context)) {
            $record->approved = 0;
        }

        $record->groupid = $currentgroup;
        $record->timemodified = time();
        $DB->update_record('data_records', $record);

        /// Update all content
        $field = NULL;
        foreach ($datarecord as $name => $value) {
            if (!in_array($name, $ignorenames)) {
                $namearr = explode('_',$name);  // Second one is the field id
                if (empty($field->field) || ($namearr[1] != $field->field->id)) {  // Try to reuse classes
                    $field = data_get_field_from_id($namearr[1], $data);
                }
                if ($field) {
                    $field->update_content($rid, $value, $name);
                }
            }
        }

        add_to_log($course->id, 'data', 'update', "view.php?d=$data->id&amp;rid=$rid", $data->id, $cm->id);

        redirect($CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&rid='.$rid);

    } else { /// Add some new records

        if (!data_user_can_add_entry($data, $currentgroup, $groupmode, $context)) {
            print_error('cannotadd', 'data');
        }

    /// Check if maximum number of entry as specified by this database is reached
    /// Of course, you can't be stopped if you are an editting teacher! =)

        if (data_atmaxentries($data) and !has_capability('mod/data:manageentries',$context)){
            echo $OUTPUT->header();
            echo $OUTPUT->notification(get_string('atmaxentry','data'));
            echo $OUTPUT->footer();
            exit;
        }

        ///Empty form checking - you can't submit an empty form!

        $emptyform = true;      // assume the worst

        foreach ($datarecord as $name => $value) {
            if (!in_array($name, $ignorenames)) {
                $namearr = explode('_', $name);  // Second one is the field id
                if (empty($field->field) || ($namearr[1] != $field->field->id)) {  // Try to reuse classes
                    $field = data_get_field_from_id($namearr[1], $data);
                }
                if ($field->notemptyfield($value, $name)) {
                    $emptyform = false;
                    break;             // if anything has content, this form is not empty, so stop now!
                }
            }
        }

        if ($emptyform){    //nothing gets written to database
            echo $OUTPUT->notification(get_string('emptyaddform','data'));
        }

        if (!$emptyform && $recordid = data_add_record($data, $currentgroup)) {    //add instance to data_record

            /// Insert a whole lot of empty records to make sure we have them
            $fields = $DB->get_records('data_fields', array('dataid'=>$data->id));
            foreach ($fields as $field) {
                $content->recordid = $recordid;
                $content->fieldid = $field->id;
                $DB->insert_record('data_content',$content);
            }

            /// For each field in the add form, add it to the data_content.
            foreach ($datarecord as $name => $value){
                if (!in_array($name, $ignorenames)) {
                    $namearr = explode('_', $name);  // Second one is the field id
                    if (empty($field->field) || ($namearr[1] != $field->field->id)) {  // Try to reuse classes
                        $field = data_get_field_from_id($namearr[1], $data);
                    }
                    if ($field) {
                        $field->update_content($recordid, $value, $name);
                    }
                }
            }

            add_to_log($course->id, 'data', 'add', "view.php?d=$data->id&amp;rid=$recordid", $data->id, $cm->id);

            if (!empty($datarecord->saveandview)) {
                redirect($CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&rid='.$recordid);
            }
        }
    }
}  // End of form processing


/// Print the page header

echo $OUTPUT->header();
groups_print_activity_menu($cm, $CFG->wwwroot.'/mod/data/edit.php?d='.$data->id);
echo $OUTPUT->heading(format_string($data->name));

/// Print the tabs

$currenttab = 'add';
if ($rid) {
    $editentry = true;  //used in tabs
}
include('tabs.php');


/// Print the browsing interface

$patterns = array();    //tags to replace
$replacement = array();    //html to replace those yucky tags

//form goes here first in case add template is empty
echo '<form enctype="multipart/form-data" action="edit.php" method="post">';
echo '<div>';
echo '<input name="d" value="'.$data->id.'" type="hidden" />';
echo '<input name="rid" value="'.$rid.'" type="hidden" />';
echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />';
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');

if (!$rid){
    echo $OUTPUT->heading(get_string('newentry','data'), 2);
}

/******************************************
 * Regular expression replacement section *
 ******************************************/
if ($data->addtemplate){
    $possiblefields = $DB->get_records('data_fields', array('dataid'=>$data->id), 'id');
    $patterns = array();
    $replacements = array();

    ///then we generate strings to replace
    foreach ($possiblefields as $eachfield){
        $field = data_get_field($eachfield, $data);
        $patterns[]="[[".$field->field->name."]]";
        $replacements[] = $field->display_add_field($rid);
        $patterns[]="[[".$field->field->name."#id]]";
        $replacements[] = 'field_'.$field->field->id;
    }
    $newtext = str_ireplace($patterns, $replacements, $data->{$mode});

} else {    //if the add template is not yet defined, print the default form!
    echo data_generate_default_template($data, 'addtemplate', $rid, true, false);
    $newtext = '';
}

echo $newtext;

echo '<div class="mdl-align"><input type="submit" name="saveandview" value="'.get_string('saveandview','data').'" />';
if ($rid) {
    echo '&nbsp;<input type="submit" name="cancel" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
} else {
    if ((!$data->maxentries) || has_capability('mod/data:manageentries', $context) || (data_numentries($data) < ($data->maxentries - 1))) {
        echo '&nbsp;<input type="submit" value="'.get_string('saveandadd','data').'" />';
    }
}
echo '</div>';
echo $OUTPUT->box_end();
echo '</div></form>';


/// Finish the page

// Print the stuff that need to come after the form fields.
if (!$fields = $DB->get_records('data_fields', array('dataid'=>$data->id))) {
    print_error('nofieldindatabase', 'data');
}
foreach ($fields as $eachfield) {
    $field = data_get_field($eachfield, $data);
    $field->print_after_form();
}

echo $OUTPUT->footer();
