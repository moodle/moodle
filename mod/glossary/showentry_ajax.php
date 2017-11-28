<?php
define('AJAX_SCRIPT', true);

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir . '/filelib.php');

$concept  = optional_param('concept', '', PARAM_CLEAN);
$courseid = optional_param('courseid', 0, PARAM_INT);
$eid      = optional_param('eid', 0, PARAM_INT); // glossary entry id
$displayformat = optional_param('displayformat',-1, PARAM_SAFEDIR);

$url = new moodle_url('/mod/glossary/showentry.php');
$url->param('concept', $concept);
$url->param('courseid', $courseid);
$url->param('eid', $eid);
$url->param('displayformat', $displayformat);
$PAGE->set_url($url);

if ($CFG->forcelogin) {
    require_login();
}

if ($eid) {
    $entry = $DB->get_record('glossary_entries', array('id'=>$eid), '*', MUST_EXIST);
    $glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('glossary', $glossary->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    require_course_login($course, true, $cm);
    $entry->glossaryname = $glossary->name;
    $entry->cmid = $cm->id;
    $entry->courseid = $cm->course;
    $entries = array($entry);

} else if ($concept) {
    $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
    require_course_login($course);
    $entries = glossary_get_entries_search($concept, $courseid);

} else {
    print_error('invalidelementid');
}

if ($entries) {
    foreach ($entries as $key => $entry) {
        // Need to get the course where the entry is,
        // in order to check for visibility/approve permissions there
        $entrycourse = $DB->get_record('course', array('id' => $entry->courseid), '*', MUST_EXIST);
        $modinfo = get_fast_modinfo($entrycourse);
        // make sure the entry is visible
        if (empty($modinfo->cms[$entry->cmid]->uservisible)) {
            unset($entries[$key]);
            continue;
        }
        // make sure the entry is approved (or approvable by current user)
        if (!$entry->approved and ($USER->id != $entry->userid)) {
            $context = context_module::instance($entry->cmid);
            if (!has_capability('mod/glossary:approve', $context)) {
                unset($entries[$key]);
                continue;
            }
        }

        // Make sure entry is not autolinking itself.
        $GLOSSARY_EXCLUDEENTRY = $entry->id;

        $context = context_module::instance($entry->cmid);
        $definition = file_rewrite_pluginfile_urls($entry->definition, 'pluginfile.php', $context->id, 'mod_glossary', 'entry', $entry->id);

        $options = new stdClass();
        $options->para = false;
        $options->trusted = $entry->definitiontrust;
        $options->context = $context;
        $entries[$key]->definition = format_text($definition, $entry->definitionformat, $options);

        if (core_tag_tag::is_enabled('mod_glossary', 'glossary_entries')) {
            $entries[$key]->definition .= $OUTPUT->tag_list(
                core_tag_tag::get_item_tags('mod_glossary', 'glossary_entries', $entry->id), null, 'glossary-tags');
        }

        $entries[$key]->attachments = '';
        if (!empty($entries[$key]->attachment)) {
            $attachments = glossary_print_attachments($entry, $cm, 'html');
            $entries[$key]->attachments = html_writer::tag('p', $attachments);
        }

        $entries[$key]->footer = "<p style=\"text-align:right\">&raquo;&nbsp;<a href=\"$CFG->wwwroot/mod/glossary/view.php?g=$entry->glossaryid\">".format_string($entry->glossaryname,true)."</a></p>";
        glossary_entry_view($entry, $modinfo->cms[$entry->cmid]->context);
    }
}

echo $OUTPUT->header();

$result = new stdClass;
$result->success = true;
$result->entries = $entries;
echo json_encode($result);

echo $OUTPUT->footer();

