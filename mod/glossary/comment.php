<?php // $Id$

/// This page prints a particular instance of glossary
    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);             // Course Module ID
    require_variable($eid);            // Entry ID
    optional_variable($cid,0);         // Comment ID

    optional_variable($action,"add");     // Action to perform
    optional_variable($confirm,0);     // Confirm the action

    $action = strtolower($action);

    global $THEME, $USER, $CFG;

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    } 

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    } 

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 

    if (! $entry = get_record("glossary_entries", "id", $eid)) {
        error("Entry is incorrect");
    }

    if ( $cid ) {
        if (! $comment = get_record("glossary_comments", "id", $cid)) {
            error("Comment is incorrect");
        }
    } 

    require_login($course->id);    
    if (!$cm->visible and !isteacher($course->id)) {
        notice(get_string("activityiscurrentlyhidden"));
    } 
    if (isguest()) {
        error("Guests are not allowed to post comments", $_SERVER["HTTP_REFERER"]);
    }    
    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id", "$glossary->id",$cm->id);

    /// Printing the page header
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } 

    switch ( $action ){
        case "add":
            $straction = get_string("addingcomment","glossary");
        break;
        case "edit":
            $straction = get_string("editingcomment","glossary");
        break;
        case "delete":
            $straction = get_string("deletingcomment","glossary");
        break;
    }
    $strglossaries = get_string("modulenameplural", "glossary");
    $strglossary = get_string("modulename", "glossary");
    $strcomments = get_string("comments", "glossary");

    print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
            "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> <A HREF=view.php?id=$cm->id>$glossary->name</a> -> <A HREF=comments.php?id=$cm->id&eid=$entry->id>$strcomments</a> -> " . $straction,
            "", "", true, update_module_button($cm->id, $course->id, $strglossary),
            navmenu($course, $cm));
    
    echo "<center>";

/// Input section

    if ( $action == "delete" ) {
        if ( $confirm ) {
            delete_records("glossary_comments","id", $cid);				

            print_simple_box_start("center","40%", "#FFBBBB");
            echo "<center>" . get_string("commentdeleted","glossary") . "</center>";
            print_simple_box_end();

            print_footer($course);
            add_to_log($course->id, "glossary", "delete comment", "comments.php?id=$cm->id&eid=$entry->id", "$comment->id",$cm->id);
            redirect("comments.php?id=$cm->id&eid=$entry->id");

        } else {

		    glossary_print_comment($course, $cm, $glossary, $entry, $comment);

            print_simple_box_start("center","40%", "#FFBBBB");
            echo "<center><br>" . get_string("areyousuredeletecomment","glossary");
            ?>
                <form name="form" method="post" action="comment.php">
                <input type="hidden" name=id            value="<?php p($id) ?>">
                <input type="hidden" name=eid           value="<?php p($eid) ?>">
                <input type="hidden" name=cid           value="<?php p($cid) ?>">
                <input type="hidden" name=action        value="delete">
                <input type="hidden" name=confirm       value="1">
                <input type="submit" value=" <?php print_string("yes")?> ">
                <input type=button value=" <?php print_string("no")?> " onclick="javascript:history.go(-1);">

                </form>
                </center>
            <?php
            print_simple_box_end();
        }
    } else {
        if ( $action == "edit" ) {
            if ( (time() - $comment->timemodified >= $CFG->maxeditingtime or 
                  $USER->id != $comment->userid) and !isteacher($course->id) ) {
                echo "<center><strong>";
                if ( $USER->id != $comment->userid ) {
                    echo get_string("youarenottheauthor","glossary",$CFG->maxeditingtime);
                } elseif (time() - $comment->timemodified >= $CFG->maxeditingtime ) {
                    echo get_string("maxtimehaspassed","glossary",$CFG->maxeditingtime);
                }
                echo "</strong></center>";
                print_footer($course);
                die;
            }
        }

        if ( $confirm and $form = data_submitted() ) {
            $form->text = clean_text($form->text, $form->format);

            $newentry->entryid = $entry->id;
            $newentry->comment = $form->text;
            $newentry->format = $form->format;
            $newentry->timemodified = time();

            if ( $action == "add" ) {
                $newentry->userid = $USER->id;

                if (! $newentry->id = insert_record("glossary_comments", $newentry)) {
                    error("Could not insert this new comment");
                } else {
                    add_to_log($course->id, "glossary", "add comment", "comments.php?id=$cm->id&eid=$entry->id", "$newentry->id", $cm->id);
                }
            } else {
                $newentry->id = $form->cid;
                $newentry->userid = $comment->userid;

                if (! update_record("glossary_comments", $newentry)) {
                    error("Could not update this comment");
                } else {
                    add_to_log($course->id, "glossary", "update comment", "comments.php?id=$cm->id&eid=$entry->id", "$newentry->id",$cm->id);
                }
            }
            print_simple_box_start("center","40%", "#FFBBBB");
            echo "<center>" . get_string("commentupdated","glossary") . "</center>";
            print_simple_box_end();

            print_footer($course);

            redirect("comments.php?id=$cm->id&eid=$entry->id");

        } else {
            /// original glossary entry
            glossary_print_entry($course, $cm, $glossary, $entry, "", "", false);
            echo "<br />";

            if ($usehtmleditor = can_use_html_editor()) {
                $defaultformat = FORMAT_HTML;
            } else {
                $defaultformat = FORMAT_MOODLE;
            }
            if (isset($comment) ) {
                $form->text = $comment->comment;
                $form->format = $comment->format;
            } else {
                $form->text = "";
                $form->format = $defaultformat;
            }
            include("comment.html");

            if ($usehtmleditor) { 
                use_html_editor("text");
            }
        }
    }
    /// Finish the page
    print_footer($course);
?>
