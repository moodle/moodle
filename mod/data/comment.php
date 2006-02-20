<?php

    require_once('../../config.php');
    require_once('lib.php');


    //param needed to go back to view.php
    $d     = optional_param('d', 0, PARAM_INT);   // database id
    $search = optional_param('search','',PARAM_NOTAGS);    //search string
    $page = optional_param('page', 0, PARAM_INT);    //offset of the current record
    $rid = optional_param('rid', 0, PARAM_INT);    //record id
    $sort = optional_param('sort',0,PARAM_INT);    //sort by field
    $order = optional_param('order','ASC',PARAM_ALPHA);    //sort order
    $group = optional_param('group','0',PARAM_INT);    //groupid

    //param needed for comment operations
    $mode = optional_param('mode','',PARAM_ALPHA);
    $recordid = optional_param('recordid','',PARAM_INT);
    $commentid = optional_param('commentid','',PARAM_INT);
    $confirm = optional_param('confirm','',PARAM_INT);
    $commentcontent = optional_param('commentcontent','',PARAM_NOTAGS);
    
    
    if ((!$record = get_record('data_records','id',$recordid))) {
        if (!$comment = get_record('data_comments','id',$commentid)) {
            error ('this record does not exist');
        } else {
            $record = get_record('data_records','id',$comment->recordid);
        }
    }
    
    if (!$data = get_record('data','id',$record->dataid)) {
        error ('this database does not exist');
    }
    
    switch ($mode) {
        case 'add':
            $newcomment = new object;
            $newcomment->userid = $USER->id;
            if (($newcomment->content = $commentcontent) && ($newcomment->recordid = $recordid)) {
                insert_record('data_comments',$newcomment);
            }
            redirect('view.php?d='.s($d).'&amp;search='.s($search).'&amp;sort='.s($sort).'&amp;order='.s($order).'&amp;group='.s($group).'&amp;page='.s($page).'&amp;rid='.s($rid), get_string("commentsaved", "data"));
        break;
        
        case 'edit':    //print edit form
            print_header();
            $comment = get_record('data_comments','id',$commentid);
            print_heading('Edit');
            echo '<div align="center">';
            echo '<form action="comment.php" method="post">';
            echo '<input type="hidden" name="commentid" value="'.$commentid.'" />';
            
            echo '<input type="hidden" name="d" value="'.$d.'" />';
            echo '<input type="hidden" name="search" value="'.$search.'" />';
            echo '<input type="hidden" name="rid" value="'.$rid.'" />';
            echo '<input type="hidden" name="sort" value="'.$sort.'" />';
            echo '<input type="hidden" name="order" value="'.$order.'" />';
            echo '<input type="hidden" name="group" value="'.$group.'" />';
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
            $newcomment = new object;
            $newcomment->id = $commentid;
            $newcomment->content = $commentcontent;
            update_record('data_comments',$newcomment);
            redirect('view.php?d='.s($d).'&amp;search='.s($search).'&amp;sort='.s($sort).'&amp;order='.s($order).'&amp;group='.s($group).'&amp;page='.s($page).'&amp;rid='.s($rid), get_string("commentsaved", "data"));
        break;
        
        case 'delete':    //deletes single comment from db
            if ($confirm and confirm_sesskey()) {
                delete_records('data_comments','id',$commentid);
                redirect('view.php?d='.s($d).'&amp;search='.s($search).'&amp;sort='.s($sort).'&amp;order='.s($order).'&amp;group='.s($group).'&amp;page='.s($page).'&amp;rid='.s($rid), get_string("commentsaved", "data"));
            } else {    //print confirm delete form
                print_header();
                print_heading('Delete Confirm');
                echo '<div align="center">';
                echo '<form action="comment.php" method="post">';
                echo '<input type="hidden" name="commentid" value="'.$commentid.'" />';
                echo '<input type="hidden" name="d" value="'.$d.'" />';
                echo '<input type="hidden" name="search" value="'.$search.'" />';
                echo '<input type="hidden" name="rid" value="'.$rid.'" />';
                echo '<input type="hidden" name="sort" value="'.$sort.'" />';
                echo '<input type="hidden" name="order" value="'.$order.'" />';
                echo '<input type="hidden" name="group" value="'.$group.'" />';
                echo '<input type="hidden" name="page" value="'.$page.'" />';
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                echo '<br />'.$comment->content.'<br />';
                echo '<input type="hidden" name="mode" value="delete" />';
                echo '<input type="hidden" name="confirm" value="1" />';
                echo '<br /><input type="submit" value="'.get_string('ok').'" />';
                echo '<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
                echo '</form></div>';
                print_footer();
            }

        break;
        
        default:    //print all listing, and add comment form
            print_header();
            data_print_comments($data, $record, $search, $template, $sort, $page, $rid, $order, $group);
            print_footer();
        break;
        
    }


?>
