<?php  // $Id$

    require_once('../../config.php');
    require_once('lib.php');

    //param needed to go back to view.php
    $rid   = required_param('rid', PARAM_INT);   // Record ID
    $page  = optional_param('page', 0, PARAM_INT);   // Page ID

    //param needed for comment operations
    $mode = optional_param('mode','',PARAM_ALPHA);
    $commentid = optional_param('commentid','',PARAM_INT);
    $confirm = optional_param('confirm','',PARAM_INT);
    $commentcontent = trim(optional_param('commentcontent','',PARAM_NOTAGS));
    $template = optional_param('template','',PARAM_ALPHA);


    if (! $record = get_record('data_records', 'id', $rid)) {
        error('Record ID is incorrect');
    }
    if (! $data = get_record('data', 'id', $record->dataid)) {
        error('Data ID is incorrect');
    }
    if (! $course = get_record('course', 'id', $data->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

    require_login($course->id, false, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if ($commentid) {
        if (! $comment = get_record('data_comments', 'id', $commentid)) {
            error('Comment ID is misconfigured');
        }
        if ($comment->recordid != $record->id) {
            error('Comment ID is misconfigured');
        }
        if (!has_capability('mod/data:managecomments', $context) && $comment->userid != $USER->id) {
            error('Comment is not yours to edit!');
        }
    }

    switch ($mode) {
        case 'add':
            if (empty($commentcontent)) {
                redirect('view.php?rid='.$record->id.'&amp;page='.$page, get_string('commentempty', 'data'));
            }

            $newcomment = new object;
            $newcomment->userid = $USER->id;
            $newcomment->created = time();
            $newcomment->modified = time();
            if (($newcomment->content = $commentcontent) && ($newcomment->recordid = $record->id)) {
                insert_record('data_comments',$newcomment);
            }
            redirect('view.php?rid='.$record->id.'&amp;page='.$page, get_string('commentsaved', 'data'));
        break;

        case 'edit':    //print edit form
            print_header();
            print_heading(get_string('edit'));
            echo '<div align="center">';
            echo '<form action="comment.php" method="post">';
            echo '<input type="hidden" name="commentid" value="'.$comment->id.'" />';
            echo '<input type="hidden" name="rid" value="'.$record->id.'" />';
            echo '<input type="hidden" name="page" value="'.$page.'" />';

            echo '<textarea name="commentcontent">'.s($comment->content).'</textarea>';
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo '<input type="hidden" name="mode" value="editcommit" />';
            echo '<br /><input type="submit" value="'.get_string('ok').'" />';
            echo '<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
            echo '</form></div>';
            print_footer();
        break;

        case 'editcommit':  //update db
            if (empty($commentcontent)) {
                redirect('view.php?rid='.$record->id.'&amp;page='.$page, get_string('commentempty', 'data'));
            }

            if ($comment) {
                $newcomment = new object;
                $newcomment->id = $comment->id;
                $newcomment->content = $commentcontent;
                $newcomment->modified = time();
                update_record('data_comments',$newcomment);
            }
            redirect('view.php?rid='.$record->id.'&amp;page='.$page, get_string('commentsaved', 'data'));
        break;

        case 'delete':    //deletes single comment from db
            if ($confirm and confirm_sesskey() and $comment) {
                delete_records('data_comments','id',$comment->id);
                redirect('view.php?rid='.$record->id.'&amp;page='.$page, get_string('commentdeleted', 'data'));

            } else {    //print confirm delete form
                print_header();
                data_print_comment($data, $comment, $page);

                notice_yesno(get_string('deletecomment','data'),
                  'comment.php?rid='.$record->id.'&amp;commentid='.$comment->id.'&amp;page='.$page.
                              '&amp;sesskey='.sesskey().'&amp;mode=delete&amp;confirm=1',
                  'view.php?rid='.$record->id.'&amp;page='.$page);
                print_footer();
            }

        break;

        default:    //print all listing, and add comment form
            print_header();
            data_print_comments($data, $record, $page);
            print_footer();
        break;

    }


?>
