<?php // $Id$

require_once('../../config.php');
require_once('lib.php');
require_once('comment_form.php');

$action = optional_param('action','add', PARAM_ACTION);

if (isguest()) {
    error('Guests are not allowed to post comments!');
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
        error('Incorrect action specified');
}

/**
 * Add new comment
 */
function glossary_comment_add() {
    global $USER;

    $eid = optional_param('eid', 0, PARAM_INT); // Entry ID

    if (!$entry = get_record('glossary_entries', 'id', $eid)) {
        error('Entry is incorrect');
    }
    if (!$glossary = get_record('glossary', 'id', $entry->glossaryid)) {
        error('Incorrect glossary');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        error('Course Module ID was incorrect');
    }
    if (!$course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    /// Both the configuration and capability must allow comments
    if (!$glossary->allowcomments or !has_capability('mod/glossary:comment', $context)) {
        error('You can\'t add comments to this glossary!');
    }

    $mform = new mod_glossary_comment_form();
    $mform->set_data(array('eid'=>$eid, 'action'=>'add'));

    if ($mform->is_cancelled()) {
        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");
    }

    if ($data = $mform->get_data()) {
        trusttext_after_edit($data->entrycomment, $context);

        $newcomment = new object();
        $newcomment->entryid      = $entry->id;
        $newcomment->entrycomment = $data->entrycomment;
        $newcomment->format       = $data->format;
        $newcomment->timemodified = time();
        $newcomment->userid       = $USER->id;

        if (!$newcomment->id = insert_record('glossary_comments', $newcomment)) {
            error('Could not insert this new comment');
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
    global $USER;

    $cid     = optional_param('cid', 0, PARAM_INT);      // Comment ID
    $confirm = optional_param('confirm', 0, PARAM_BOOL); // delete confirmation

    if (!$comment = get_record('glossary_comments', 'id', $cid)) {
        error('Comment is incorrect');
    }
    if (!$entry = get_record('glossary_entries', 'id', $comment->entryid)) {
        error('Entry is incorrect');
    }
    if (!$glossary = get_record('glossary', 'id', $entry->glossaryid)) {
        error('Incorrect glossary');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        error('Course Module ID was incorrect');
    }
    if (!$course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (($comment->userid <> $USER->id) and !has_capability('mod/glossary:managecomments', $context)) {
        error('You can\'t delete other people\'s comments!');
    }
    if (!$glossary->allowcomments and !has_capability('mod/glossary:managecomments', $context)) {
        error('You can\'t delete comments in this glossary!');
    }

    if (data_submitted() and $confirm and confirm_sesskey()) {
        delete_records('glossary_comments','id', $cid);
        add_to_log($course->id, 'glossary', 'delete comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$comment->id",$cm->id);
        redirect("comments.php?id=$cm->id&amp;eid=$entry->id");

    } else {
        $linkyes    = 'comment.php';
        $optionsyes = array('action'=>'delete', 'cid'=>$cid, 'confirm'=>1, 'sesskey'=>sesskey());
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
    global $CFG, $USER;

    $cid = optional_param('cid', 0, PARAM_INT); // Comment ID

    if (!$comment = get_record('glossary_comments', 'id', $cid)) {
        error('Comment is incorrect');
    }
    if (!$entry = get_record('glossary_entries', 'id', $comment->entryid)) {
        error('Entry is incorrect');
    }
    if (!$glossary = get_record('glossary', 'id', $entry->glossaryid)) {
        error('Incorrect glossary');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        error('Course Module ID was incorrect');
    }
    if (!$course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (!$glossary->allowcomments and !has_capability('mod/glossary:managecomments', $context)) {
        error('You can\'t edit comments in this glossary!');
    }
    if (($comment->userid <> $USER->id) and !has_capability('mod/glossary:managecomments', $context)) {
        error('You can\'t edit other people\'s comments!');
    }
    $ineditperiod = ((time() - $comment->timemodified <  $CFG->maxeditingtime) || $glossary->editalways);
    if ((!has_capability('mod/glossary:comment', $context) or !$ineditperiod) and !has_capability('mod/glossary:managecomments', $context)) {
        error('You can\'t edit this. Time expired!');
    }

    $mform = new mod_glossary_comment_form();
    trusttext_prepare_edit($comment->entrycomment, $comment->format, can_use_html_editor(), $context);
    $mform->set_data(array('cid'=>$cid, 'action'=>'edit', 'entrycomment'=>$comment->entrycomment, 'format'=>$comment->format));

    if ($data = $mform->get_data()) {
        trusttext_after_edit($data->entrycomment, $context);

        $updatedcomment = new object();
        $updatedcomment->id           = $cid;
        $updatedcomment->entrycomment = $data->entrycomment;
        $updatedcomment->format       = $data->format;
        $updatedcomment->timemodified = time();

        if (!update_record('glossary_comments', $updatedcomment)) {
            error('Could not update this comment');
        } else {
            add_to_log($course->id, 'glossary', 'update comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$updatedcomment->id",$cm->id);
        }
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
