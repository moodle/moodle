<?php // $Id$

/// This page prints a particular instance of glossary
    require_once('../../config.php');
    require_once('lib.php');

    $id   = required_param('id', PARAM_INT);             // Course Module ID
    $eid  = required_param('eid', PARAM_INT);            // Entry ID
    $cid  = optional_param('cid', 0, PARAM_INT);         // Comment ID
    $confirm = optional_param('confirm',0, PARAM_INT);  // Confirm the action
    $action = optional_param('action','add', PARAM_ALPHA);

    $action = strtolower($action);

    global $USER, $CFG;

    if (! $cm = get_coursemodule_from_id('glossary', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $glossary = get_record('glossary', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

    if (! $entry = get_record('glossary_entries', 'id', $eid)) {
        error('Entry is incorrect');
    }

    if ( $cid ) {
        if (! $comment = get_record('glossary_comments', 'id', $cid)) {
            error('Comment is incorrect');
        }
    }

    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    if (isguest()) {
        error('Guests are not allowed to post comments', $_SERVER['HTTP_REFERER']);
    }
    add_to_log($course->id, 'glossary', 'view', "view.php?id=$cm->id", "$glossary->id",$cm->id);

    switch ( $action ){
        case 'add':
            $straction = get_string('addingcomment','glossary');
            break;
        case 'edit':
            $straction = get_string('editingcomment','glossary');
            break;
        case 'delete':
            $straction = get_string('deletingcomment','glossary');
            break;
        default:
            $action = 'add';
            $straction = get_string('addingcomment','glossary');
            break;
    }
    $strglossaries = get_string('modulenameplural', 'glossary');
    $strglossary = get_string('modulename', 'glossary');
    $strcomments = get_string('comments', 'glossary');

    print_header_simple(format_string($glossary->name), '',
            "<a href=\"index.php?id=$course->id\">$strglossaries</a> -> <a href=\"view.php?id=$cm->id\">".format_string($glossary->name,true)."</a> -> <a href=\"comments.php?id=$cm->id&amp;eid=$entry->id\">$strcomments</a> -> " . $straction,
            '', '', true, update_module_button($cm->id, $course->id, $strglossary),
            navmenu($course, $cm));

    echo '<center>';

/// Input section

    if ( $action == 'delete' ) {
        if (($comment->userid <> $USER->id) and !has_capability('mod/glossary:managecomments', $context)) {
            error('You can\'t delete other people\'s comments!');
        }
        if (!$glossary->allowcomments && !has_capability('mod/glossary:managecomments', $context)) {
                error('You can\'t delete comments in this glossary!');
            }
        if ( data_submitted() and $confirm ) {
            delete_records('glossary_comments','id', $cid);

            print_simple_box_start('center','40%', '#FFBBBB');
            echo '<center>'.get_string('commentdeleted','glossary').'</center>';
            print_simple_box_end();

            print_footer($course);
            add_to_log($course->id, 'glossary', 'delete comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$comment->id",$cm->id);
            redirect("comments.php?id=$cm->id&amp;eid=$entry->id");

        } else {

            glossary_print_comment($course, $cm, $glossary, $entry, $comment);

            print_simple_box_start('center','40%', '#FFBBBB');
            echo '<center><br />'.get_string('areyousuredeletecomment','glossary');
            ?>
                <form name="form" method="post" action="comment.php">
                <input type="hidden" name="id"          value="<?php p($id) ?>" />
                <input type="hidden" name="eid"         value="<?php p($eid) ?>" />
                <input type="hidden" name="cid"         value="<?php p($cid) ?>" />
                <input type="hidden" name="action"      value="delete" />
                <input type="hidden" name="confirm"     value="1" />
                <input type="submit" value="<?php print_string('yes')?>" />
                <input type="button" value="<?php print_string('no')?>" onclick="javascript:history.go(-1);" />

                </form>
                </center>
            <?php
            print_simple_box_end();
        }
    } else {
        if (!$glossary->allowcomments || !has_capability('mod/glossary:comment', $context)) {
            error('You can\'t add/edit comments to this glossary!');
        }
        if ( $action == 'edit' ) {
            if (!isset($comment->timemodified)) {
                $timetocheck = 0;
            } else {
                $timetocheck = $comment->timemodified;
            }
            $ineditperiod = ((time() - $timetocheck <  $CFG->maxeditingtime) || $glossary->editalways);
            if ( (!$ineditperiod || $USER->id != $comment->userid) and !has_capability('mod/glossary:comment', $context) and $cid) {
                if ( $USER->id != $comment->userid ) {
                    error('You can\'t edit other people\'s comments!');
                } elseif (!$ineditperiod) {
                    error('You can\'t edit this. Time expired!');
                }
                die;
            }
        }

        if ( $confirm and $form = data_submitted() ) {
            trusttext_after_edit($form->text, $context);

            $newentry->entryid = $entry->id;
            $newentry->entrycomment = $form->text;
            $newentry->format = $form->format;
            $newentry->timemodified = time();

            if ( $action == 'add' ) {
                $newentry->userid = $USER->id;

                if (! $newentry->id = insert_record('glossary_comments', $newentry)) {
                    error('Could not insert this new comment');
                } else {
                    add_to_log($course->id, 'glossary', 'add comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$newentry->id", $cm->id);
                }
            } else {
                $newentry->id = $form->cid;
                $newentry->userid = $comment->userid;

                if (! update_record('glossary_comments', $newentry)) {
                    error('Could not update this comment');
                } else {
                    add_to_log($course->id, 'glossary', 'update comment', "comments.php?id=$cm->id&amp;eid=$entry->id", "$newentry->id",$cm->id);
                }
            }
            print_simple_box_start('center','40%', '#FFBBBB');
            echo '<center>'.get_string('commentupdated','glossary').'</center>';
            print_simple_box_end();

            print_footer($course);

            redirect("comments.php?id=$cm->id&amp;eid=$entry->id");

        } else {
            /// original glossary entry
            glossary_print_entry($course, $cm, $glossary, $entry, 'approval', '', false);
            echo '<br />';

            if ($usehtmleditor = can_use_html_editor()) {
                $defaultformat = FORMAT_HTML;
            } else {
                $defaultformat = FORMAT_MOODLE;
            }
            if (isset($comment) ) {
                $form->text = $comment->entrycomment;
                $form->format = $comment->format;
            } else {
                $form->text = '';
                $form->format = $defaultformat;
            }
            include('comment.html');

            if ($usehtmleditor) {
                use_html_editor('text');
            }
        }
    }

    echo '</center>';

    /// Finish the page
    print_footer($course);
?>
