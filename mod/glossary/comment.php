<?php

require_once('../../config.php');
require_once('lib.php');
require_once('comment_form.php');

$action = optional_param('action','add', PARAM_ACTION);

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/glossary/comment.php', array('action'=>$action)));

if (has_capability('moodle/legacy:guest', get_context_instance(CONTEXT_SYSTEM), 0, false)) {
    print_error('guestnocomment');
}

switch ($action) {
    case 'add':
        glossary_comment_add();
        die;
    case 'delete':
        glossary_comment_delete();
        die;
    case 'edit':
        glossary_comment_edit();
        die;
    default:
        print_error('invalidaction');
}

/**
 * Add new comment
 */
function glossary_comment_add() {
    global $USER, $DB;

    $entryid = optional_param('entryid', 0, PARAM_INT); // Entry ID

    if (!$entry = $DB->get_record('glossary_entries', array('id'=>$entryid))) {
        print_error('invalidentry');
    }
    if (!$glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid))) {
        print_error('invalidid', 'glossary');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }

    require_login($course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    /// Both the configuration and capability must allow comments
    if (!$glossary->allowcomments or !has_capability('mod/glossary:comment', $context)) {
        print_error('nopermissiontocomment');
    }

    $commentoptions = array('trusttext'=>true, 'maxfiles'=>0, 'context'=>$context);

    $comment = new object();
    $comment->id      = null;
    $comment->action  = 'add';
    $comment->entryid = $entry->id;

    $comment = file_prepare_standard_editor($comment, 'entrycomment', $commentoptions, $context);

    $mform = new mod_glossary_comment_form(null, array('current'=>$comment, 'commentoptions'=>$commentoptions));

    if ($mform->is_cancelled()) {
        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");
    }

    if ($newcomment = $mform->get_data()) {

        $newcomment = file_postupdate_standard_editor($newcomment, 'entrycomment', $commentoptions, $context);//no files - can be used before insert
        $newcomment->timemodified = time();
        $newcomment->userid       = $USER->id;

        $newcomment->id = $DB->insert_record('glossary_comments', $newcomment);

        add_to_log($course->id, 'glossary', 'add comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$newcomment->id", $cm->id);
        redirect("comments.php?id=$cm->id&eid=$entry->id");

    } else {
        glossary_comment_print_header($course, $cm, $glossary, $entry, 'add');
        $mform->display();
        echo $OUTPUT->footer();
        die;
    }
}

/**
 * Deleting existing comments
 */
function glossary_comment_delete() {
    global $USER, $DB, $OUTPUT;

    $id      = optional_param('id', 0, PARAM_INT);      // Comment ID
    $confirm = optional_param('confirm', 0, PARAM_BOOL); // delete confirmation

    if (!$comment = $DB->get_record('glossary_comments', array('id'=>$id))) {
        print_error('invalidcomment');
    }
    if (!$entry = $DB->get_record('glossary_entries', array('id'=>$comment->entryid))) {
        print_error('invalidentry');
    }
    if (!$glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid))) {
        print_error('invalidid', 'glossary');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }

    require_login($course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (($comment->userid <> $USER->id) and !has_capability('mod/glossary:managecomments', $context)) {
        print_error('nopermissiontodelcomment', 'glossary');
    }
    if (!$glossary->allowcomments and !has_capability('mod/glossary:managecomments', $context)) {
        print_error('nopermissiontodelinglossary', 'glossary');
    }

    if (data_submitted() and $confirm) {
        $DB->delete_records('glossary_comments', array('id'=>$id));
        add_to_log($course->id, 'glossary', 'delete comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$comment->id",$cm->id);
        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");

    } else {
        $linkyes    = 'comment.php';
        $optionsyes = array('action'=>'delete', 'id'=>$id, 'confirm'=>1);
        $linkno     = 'comments.php';
        $optionsno  = array('id'=>$cm->id, 'entryid'=>$entry->id);
        $strdeletewarning = get_string('areyousuredeletecomment','glossary');

        glossary_comment_print_header($course, $cm, $glossary, $entry, 'delete');
        glossary_print_comment($course, $cm, $glossary, $entry, $comment);
        echo $OUTPUT->confirm($strdeletewarning, new moodle_url($linkyes, $optionsyes), new moodle_url($linkno, $optionsno));
        echo $OUTPUT->footer();
        die;
    }
}

/**
 * Edit existing comments
 */
function glossary_comment_edit() {
    global $CFG, $USER, $DB, $OUTPUT;

    $id = optional_param('id', 0, PARAM_INT); // Comment ID

    if (!$comment = $DB->get_record('glossary_comments', array('id'=>$id))) {
        print_error('invalidcomment');
    }
    if (!$entry = $DB->get_record('glossary_entries', array('id'=>$comment->entryid))) {
        print_error('invalidentry');
    }
    if (!$glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid))) {
        print_error('invalidid', 'glossary');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }

    require_login($course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (!$glossary->allowcomments and !has_capability('mod/glossary:managecomments', $context)) {
        print_error('nopermissiontodelinglossary', 'glossary');
    }
    if (($comment->userid <> $USER->id) and !has_capability('mod/glossary:managecomments', $context)) {
        print_error('nopermissiontoeditcomment');
    }
    $ineditperiod = ((time() - $comment->timemodified <  $CFG->maxeditingtime) || $glossary->editalways);
    if ((!has_capability('mod/glossary:comment', $context) or !$ineditperiod) and !has_capability('mod/glossary:managecomments', $context)) {
        print_error('cannoteditcommentexpired');
    }

    $commentoptions = array('trusttext'=>true, 'maxfiles'=>0);

    $comment->action  = 'edit';
    $comment = file_prepare_standard_editor($comment, 'entrycomment', $commentoptions, $context);

    $mform = new mod_glossary_comment_form(null, array('current'=>$comment, 'commentoptions'=>$commentoptions));

    if ($updatedcomment = $mform->get_data()) {

        $updatedcomment = file_postupdate_standard_editor($updatedcomment, 'entrycomment', $commentoptions, $context);
        $updatedcomment->timemodified = time();

        $DB->update_record('glossary_comments', $updatedcomment);
        add_to_log($course->id, 'glossary', 'update comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$updatedcomment->id",$cm->id);

        redirect("comments.php?id=$cm->id&eid=$entry->id");

    } else {
        glossary_comment_print_header($course, $cm, $glossary, $entry, 'edit');
        $mform->display();
        echo $OUTPUT->footer();
        die;
    }
}

//////////////////////////////////
/// utility functions
//////////////////////////////////

function glossary_comment_print_header($course, $cm, $glossary, $entry, $action) {
    global $PAGE, $OUTPUT;
    switch ($action){
        case 'add':
            $straction = get_string('addingcomment','glossary');
            break;
        case 'edit':
            $straction = get_string('editingcomment','glossary');
            break;
        case 'delete':
            $straction = get_string('deletingcomment','glossary');
            break;
    }

    $strglossary   = get_string('modulename', 'glossary');
    $strcomments   = get_string('comments', 'glossary');

    $PAGE->navbar->add($strcomments, new moodle_url($CFG->wwwroot.'/mod/glossary/comments.php', array('id'=>$cm->id,'eid'=>$entry->id)));
    $PAGE->navbar->add($straction);
    $PAGE->set_title(format_string($glossary->name));
    $PAGE->set_button($OUTPUT->update_module_button($cm->id, 'glossary'));
    echo $OUTPUT->header();

/// print original glossary entry for any comment action (add, update, delete)
    glossary_print_entry($course, $cm, $glossary, $entry, 'approval', '', false);
}

