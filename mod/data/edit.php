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
 * @package mod_data
 */

require_once('../../config.php');
require_once('locallib.php');
require_once("$CFG->libdir/rsslib.php");
require_once("$CFG->libdir/form/filemanager.php");

$id    = optional_param('id', 0, PARAM_INT);    // course module id
$d     = optional_param('d', 0, PARAM_INT);    // database id
$rid   = optional_param('rid', 0, PARAM_INT);    //record id
$mode ='addtemplate';    //define the mode for this page, only 1 mode available
$tags = optional_param_array('tags', [], PARAM_TAGLIST);
$redirectbackto = optional_param('backto', '', PARAM_LOCALURL); // The location to redirect back.

$url = new moodle_url('/mod/data/edit.php');
if ($rid !== 0) {
    $record = $DB->get_record('data_records', array(
            'id' => $rid,
            'dataid' => $d,
        ), '*', MUST_EXIST);
    $url->param('rid', $rid);
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

$url->param('backto', $redirectbackto);

require_login($course, false, $cm);

if (isguestuser()) {
    redirect('view.php?d='.$data->id);
}

$context = context_module::instance($cm->id);

/// If it's hidden then it doesn't show anything.  :)
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    $strdatabases = get_string("modulenameplural", "data");

    $PAGE->set_title($data->name);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    notice(get_string("activityiscurrentlyhidden"));
}

/// Can't use this if there are no fields
if (has_capability('mod/data:managetemplates', $context)) {
    if (!$DB->record_exists('data_fields', array('dataid'=>$data->id))) {      // Brand new database!
        redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
    }
}

if ($rid) {
    // When editing an existing record, we require the session key
    require_sesskey();
}

// Get Group information for permission testing and record creation
$currentgroup = groups_get_activity_group($cm);
$groupmode = groups_get_activity_groupmode($cm);

if (!has_capability('mod/data:manageentries', $context)) {
    if ($rid) {
        // User is editing an existing record
        if (!data_user_can_manage_entry($record, $data, $context)) {
            print_error('noaccess','data');
        }
    } else if (!data_user_can_add_entry($data, $currentgroup, $groupmode, $context)) {
        // User is trying to create a new record
        print_error('noaccess','data');
    }
}

/// RSS and CSS and JS meta
if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
    $courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
    $rsstitle = $courseshortname . ': ' . format_string($data->name);
    rss_add_http_header($context, 'mod_data', $data, $rsstitle);
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
$PAGE->force_settings_menu(true);
$PAGE->set_secondary_active_tab('modulepage');
$PAGE->activityheader->disable();

// Process incoming data for adding/updating records.

// Keep track of any notifications.
$generalnotifications = array();
$fieldnotifications = array();

// Process the submitted form.
if ($datarecord = data_submitted() and confirm_sesskey()) {
    if ($rid) {
        // Updating an existing record.

        // Retrieve the format for the fields.
        $fields = $DB->get_records('data_fields', array('dataid' => $datarecord->d));

        // Validate the form to ensure that enough data was submitted.
        $processeddata = data_process_submission($data, $fields, $datarecord);

        // Add the new notification data.
        $generalnotifications = array_merge($generalnotifications, $processeddata->generalnotifications);
        $fieldnotifications = array_merge($fieldnotifications, $processeddata->fieldnotifications);

        if ($processeddata->validated) {
            // Enough data to update the record.
            data_update_record_fields_contents($data, $record, $context, $datarecord, $processeddata);
            core_tag_tag::set_item_tags('mod_data', 'data_records', $rid, $context, $tags);

            $viewurl = new moodle_url('/mod/data/view.php', array(
                'd' => $data->id,
                'rid' => $rid,
            ));
            redirect($viewurl);
        }

    } else {
        // No recordid was specified - creating a new entry.

        // Retrieve the format for the fields.
        $fields = $DB->get_records('data_fields', array('dataid' => $datarecord->d));

        // Validate the form to ensure that enough data was submitted.
        $processeddata = data_process_submission($data, $fields, $datarecord);

        // Add the new notification data.
        $generalnotifications = array_merge($generalnotifications, $processeddata->generalnotifications);
        $fieldnotifications = array_merge($fieldnotifications, $processeddata->fieldnotifications);

        // Add instance to data_record.
        if ($processeddata->validated && $recordid = data_add_record($data, $currentgroup)) {

            // Now populate the fields contents of the new record.
            data_add_fields_contents_to_new_record($data, $context, $recordid, $fields, $datarecord, $processeddata);

            core_tag_tag::set_item_tags('mod_data', 'data_records', $recordid, $context, $tags);

            if (!empty($datarecord->saveandview)) {
                $viewurl = new moodle_url('/mod/data/view.php', array(
                    'd' => $data->id,
                    'rid' => $recordid,
                ));
                redirect($viewurl);
            } else if (!empty($datarecord->saveandadd)) {
                // User has clicked "Save and add another". Reset all of the fields.
                $datarecord = null;
            }
        }
    }
}
// End of form processing.


/// Print the page header

echo $OUTPUT->header();

groups_print_activity_menu($cm, $CFG->wwwroot.'/mod/data/edit.php?d='.$data->id);

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
    echo $OUTPUT->heading(get_string('newentry','data'));
} else {
    echo $OUTPUT->heading(get_string('editentry','data'));
}

/******************************************
 * Regular expression replacement section *
 ******************************************/
if ($data->addtemplate){
    $possiblefields = $DB->get_records('data_fields', array('dataid'=>$data->id), 'id');
    $patterns = array();
    $replacements = array();

    ///then we generate strings to replace
    foreach ($possiblefields as $eachfield) {
        $field = data_get_field($eachfield, $data);
        // To skip unnecessary calls to display_add_field().
        if (strpos($data->addtemplate, "[[" . $field->field->name . "]]") !== false) {
            // Display an error in case the field type is not found.
            $errors = '';
            if (!empty($fieldnotifications[$field->field->name])) {
                foreach ($fieldnotifications[$field->field->name] as $notification) {
                    $errors .= $OUTPUT->notification($notification);
                }
            }
            // Replace the field tag.
            $fielddisplay = '';
            if ($field->type === 'unknown') {
                if (has_capability('mod/data:manageentries', $context)) {
                    // Display notification for users that can manage entries.
                    $errors .= $OUTPUT->notification(get_string('missingfieldtype', 'data',
                    (object)['name' => $field->field->name]));
                }
            } else {
                $fielddisplay = $field->display_add_field($rid, $datarecord);
            }
            $patterns[] = "[[" . $field->field->name . "]]";
            $replacements[] = $errors . $fielddisplay;
        }    // Replace the field id tag.
        $patterns[] = "[[" . $field->field->name . "#id]]";
        $replacements[] = 'field_' . $field->field->id;
    }

    if (core_tag_tag::is_enabled('mod_data', 'data_records')) {
        $patterns[] = "##tags##";
        $replacements[] = data_generate_tag_form($rid);
    }

    $newtext = str_ireplace($patterns, $replacements, $data->{$mode});

} else {    //if the add template is not yet defined, print the default form!
    echo data_generate_default_template($data, 'addtemplate', $rid, true, false);
    $newtext = '';
}

foreach ($generalnotifications as $notification) {
    echo $OUTPUT->notification($notification);
}
echo $newtext;

$redirectbackto = !empty($redirectbackto) ? $redirectbackto :
    new \moodle_url('/mod/data/view.php', ['d' => $data->id]);
$actionbuttons = html_writer::link($redirectbackto, get_string('cancel'), ['class' => 'btn btn-secondary']);
$actionbuttons .= html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'saveandview',
    'value' => get_string('save'), 'class' => 'btn btn-primary ml-2']);

if (!$rid && ((!$data->maxentries) ||
        has_capability('mod/data:manageentries', $context) ||
        (data_numentries($data) < ($data->maxentries - 1)))) {
    $actionbuttons .= html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'saveandadd',
        'value' => get_string('saveandadd', 'data'), 'class' => 'btn btn-primary ml-2']);
}

echo html_writer::div($actionbuttons, 'mdl-align mt-2');
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
