<?php // $Id$

require_once('../../config.php');
require_once('lib.php');
require_once('comment_form.php');

$action = optional_param('action','add', PARAM_ACTION);

if (isguest()) {
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

    $eid = optional_param('eid', 0, PARAM_INT); // Entry ID

    if (!$entry = $DB->get_record('glossary_entries', array('id'=>$eid))) {
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

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    /// Both the configuration and capability must allow comments
    if (!$glossary->allowcomments or !has_capability('mod/glossary:comment', $context)) {
        print_error('nopermissiontocomment');
    }

    $mform = new mod_glossary_comment_form();
    $mform->set_data(array('eid'=>$eid, 'action'=>'add'));

    if ($mform->is_cancelled()) {
        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");
    }

    if ($data = $mform->get_data()) {
        $newcomment = new object();
        $newcomment->entryid            = $entry->id;
        $newcomment->entrycomment       = $data->entrycomment;
        $newcomment->entrycommentformat = $data->entrycommentformat;
        $newcomment->entrycommenttrust  = trusttext_trusted($context);
        $newcomment->timemodified       = time();
        $newcomment->userid             = $USER->id;

        if (!$newcomment->id = $DB->insert_record('glossary_comments', $newcomment)) {
            print_error('cannotinsertcomment');
        } else {
            add_to_log($course->id, 'glossary', 'add comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$newcomment->id", $cm->id);
        }
        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");

    } else {
        glossary_comment_print_header($course, $cm, $glossary, $entry, 'add');
        $mform->display();
        print_footer($course);
        die;
    }
}

/**
 * Deleting existing comments
 */
function glossary_comment_delete() {
    global $USER, $DB;

    $cid     = optional_param('cid', 0, PARAM_INT);      // Comment ID
    $confirm = optional_param('confirm', 0, PARAM_BOOL); // delete confirmation

    if (!$comment = $DB->get_record('glossary_comments', array('id'=>$cid))) {
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

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (($comment->userid <> $USER->id) and !has_capability('mod/glossary:managecomments', $context)) {
        print_error('nopermissiontodelcomment', 'glossary');
    }
    if (!$glossary->allowcomments and !has_capability('mod/glossary:managecomments', $context)) {
        print_error('nopermissiontodelinglossary', 'glossary');
    }

    if (data_submitted() and $confirm) {
        $DB->delete_records('glossary_comments', array('id'=>$cid));
        add_to_log($course->id, 'glossary', 'delete comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$comment->id",$cm->id);
        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");

    } else {
        $linkyes    = 'comment.php';
        $optionsyes = array('action'=>'delete', 'cid'=>$cid, 'confirm'=>1);
        $linkno     = 'comments.php';
        $optionsno  = array('id'=>$cm->id, 'eid'=>$entry->id);
        $strdeletewarning = get_string('areyousuredeletecomment','glossary');

        glossary_comment_print_header($course, $cm, $glossary, $entry, 'delete');
        glossary_print_comment($course, $cm, $glossary, $entry, $comment);
        notice_yesno($strdeletewarning, $linkyes, $linkno, $optionsyes, $optionsno, 'post', 'get');
        print_footer($course);
        die;
    }
}

/**
 * Edit existing comments
 */
function glossary_comment_edit() {
    global $CFG, $USER, $DB;

    $cid = optional_param('cid', 0, PARAM_INT); // Comment ID

    if (!$comment = $DB->get_record('glossary_comments', array('id'=>$cid))) {
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

    require_login($course->id, false, $cm);
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

    // clean up existing text if needed
    $comment = trusttext_pre_edit($comment, 'entrycomment', $context);

    $mform = new mod_glossary_comment_form();
    $mform->set_data(array('cid'=>$cid, 'action'=>'edit', 'entrycomment'=>$comment->entrycomment, 'entrycommentformat'=>$comment->entrycommentformat));

    if ($data = $mform->get_data()) {

        $updatedcomment = new object();
        $updatedcomment->id                 = $cid;
        $updatedcomment->entrycomment       = $data->entrycomment;
        $updatedcomment->entrycommentformat = $data->entrycommentformat;
        $updatedcomment->entrycommenttrust  = trusttext_trusted($context);
        $updatedcomment->timemodified       = time();

        $DB->update_record('glossary_comments', $updatedcomment);
        add_to_log($course->id, 'glossary', 'update comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$updatedcomment->id",$cm->id);

        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");

    } else {
        glossary_comment_print_header($course, $cm, $glossary, $entry, 'edit');
        $mform->display();
        print_footer($course);
        die;
    }
}

//////////////////////////////////
/// utility functions
//////////////////////////////////

function glossary_comment_print_header($course, $cm, $glossary, $entry, $action) {
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

    $navlinks = array();
    $navlinks[] = array('name' => $strcomments, 'link' => "comments.php?id=$cm->id&amp;eid=$entry->id", 'type' => 'title');
    $navlinks[] = array('name' => $straction, 'link' => '', 'type' => 'action');
    $navigation = build_navigation($navlinks, $cm);

    print_header_simple(format_string($glossary->name), '', $navigation,
        '', '', true, update_module_button($cm->id, $course->id, $strglossary),
        navmenu($course, $cm));
/// print original glossary entry for any comment action (add, update, delete)
    glossary_print_entry($course, $cm, $glossary, $entry, 'approval', '', false);
}
?>
