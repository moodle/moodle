<?php

require_once('../../config.php');
require_once('lib.php');
require_once('edit_form.php');

$cmid = required_param('cmid', PARAM_INT);            // Course Module ID
$id   = optional_param('id', 0, PARAM_INT);           // EntryID

if (!$cm = get_coursemodule_from_id('glossary', $cmid)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
    print_error('coursemisconf');
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

if (!$glossary = $DB->get_record('glossary', array('id'=>$cm->instance))) {
    print_error('invalidid', 'glossary');
}

$url = new moodle_url('/mod/glossary/edit.php', array('cmid'=>$cm->id));
if (!empty($id)) {
    $url->param('id', $id);
}
$PAGE->set_url($url);

if ($id) { // if entry is specified
    if (isguestuser()) {
        print_error('guestnoedit', 'glossary', "$CFG->wwwroot/mod/glossary/view.php?id=$cmid");
    }

    if (!$entry = $DB->get_record('glossary_entries', array('id'=>$id, 'glossaryid'=>$glossary->id))) {
        print_error('invalidentry');
    }

    // Check if the user can update the entry (trigger exception if he can't).
    mod_glossary_can_update_entry($entry, $glossary, $context, $cm, false);
    // Prepare extra data.
    $entry = mod_glossary_prepare_entry_for_edition($entry);

} else { // new entry
    require_capability('mod/glossary:write', $context);
    // note: guest user does not have any write capability
    $entry = new stdClass();
    $entry->id = null;
}

list($definitionoptions, $attachmentoptions) = glossary_get_editor_and_attachment_options($course, $context, $entry);

$entry = file_prepare_standard_editor($entry, 'definition', $definitionoptions, $context, 'mod_glossary', 'entry', $entry->id);
$entry = file_prepare_standard_filemanager($entry, 'attachment', $attachmentoptions, $context, 'mod_glossary', 'attachment', $entry->id);

$entry->cmid = $cm->id;

// create form and set initial data
$mform = new mod_glossary_entry_form(null, array('current'=>$entry, 'cm'=>$cm, 'glossary'=>$glossary,
                                                 'definitionoptions'=>$definitionoptions, 'attachmentoptions'=>$attachmentoptions));

if ($mform->is_cancelled()){
    if ($id){
        redirect("view.php?id=$cm->id&mode=entry&hook=$id");
    } else {
        redirect("view.php?id=$cm->id");
    }

} else if ($data = $mform->get_data()) {
    $entry = glossary_edit_entry($data, $course, $cm, $glossary, $context);
    if (core_tag_tag::is_enabled('mod_glossary', 'glossary_entries') && isset($data->tags)) {
        core_tag_tag::set_item_tags('mod_glossary', 'glossary_entries', $data->id, $context, $data->tags);
    }
    redirect("view.php?id=$cm->id&mode=entry&hook=$entry->id");
}

if (!empty($id)) {
    $PAGE->navbar->add(get_string('edit'));
}

$PAGE->set_title($glossary->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_secondary_active_tab('modulepage');
$PAGE->activityheader->set_attrs([
    'hidecompletion' => true,
    'description' => ''
]);
echo $OUTPUT->header();
if (!$id) {
    echo $OUTPUT->heading(get_string('addsingleentry', 'mod_glossary'));
} else {
    echo $OUTPUT->heading(get_string('editentry', 'mod_glossary'));
}

$data = new StdClass();
$data->tags = core_tag_tag::get_item_tags_array('mod_glossary', 'glossary_entries', $id);
$mform->set_data($data);

$mform->display();

echo $OUTPUT->footer();

