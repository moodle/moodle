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
    
    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id", "$glossary->id");
    
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
    
    print_heading($glossary->name);

/// Info boxes

    if ( $glossary->intro ) {
	    print_simple_box_start("center","70%");
        echo '<p>';
        echo $glossary->intro;
        echo '</p>';
        print_simple_box_end();
    }

    echo "<p align=center>";
    echo "<table class=\"generalbox\" width=\"70%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
    echo "<tr bgcolor=$THEME->cellheading2><td align=center>";
        echo "<b>$entry->concept</b>";
    echo "</td></tr>";
    echo "<tr><TD WIDTH=100% BGCOLOR=\"#FFFFFF\">";
    if ($entry->attachment) {
          $entry->course = $course->id;
          echo "<table border=0 align=right><tr><td>";
          echo glossary_print_attachments($entry,"html");
          echo "</td></tr></table>";
    }
    echo format_text($entry->definition, $entry->format);
    echo "</td>";
    echo "</TR></table><p align=center>";

/// Input section
    if ( $action == "delete" ) {
        if ( $confirm ) {
            delete_records("glossary_comments","id", $cid);				

            print_simple_box_start("center","40%", "#FFBBBB");
            echo "<center>" . get_string("commentdeleted","glossary") . "</center>";
            print_simple_box_end();

            print_footer($course);
            add_to_log($course->id, "glossary", "delete comment", "comments.php?id=$cm->id&eid=$entry->id", $comment);
            redirect("comments.php?id=$cm->id&eid=$entry->id");
        } else {
            echo "<p align=center>";
            echo "<table class=\"generalbox\" width=\"70%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">";
            echo "<tr><td align=right width=25% BGCOLOR=\"$THEME->cellheading\">";

            $user = get_record("user", "id", $comment->userid);
            $strby = get_string("writtenby","glossary");

            print_user_picture($user->id, $course->id, $user->picture);
            echo "<br><FONT SIZE=2>$strby $user->firstname $user->lastname</font>";
            echo "<br><FONT SIZE=1>(".get_string("lastedited").": ".userdate($comment->timemodified).")</FONT></small>";

            echo "</td><TD WIDTH=75% BGCOLOR=\"$THEME->cellcontent\">";
                echo format_text($comment->comment, $comment->format);
            echo "</td></TR></table>";
            echo "<p align=center>";

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
            if ( (time() - $comment->timemodified >= $CFG->maxeditingtime or $USER->id != $comment->userid) and !isteacher($course->id) ) {
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
            $newentry->timemodified = $comment->timemodified;
			
            if ( $action == "add" ) {
                $newentry->userid = $USER->id;

                if (! $newentry->id = insert_record("glossary_comments", $newentry)) {
                    error("Could not insert this new comment");
                } else {
                    add_to_log($course->id, "glossary", "add comment", "comments.php?id=$cm->id&eid=$entry->id", "$newentry->id");
                }
            } else {
                $newentry->id = $form->cid;
                $newentry->userid = $comment->userid;

                if (! update_record("glossary_comments", $newentry)) {
                    error("Could not update this comment");
                } else {
                    add_to_log($course->id, "glossary", "update comment", "comments.php?id=$cm->id&eid=$entry->id", "$newentry->id");
                }
            }
            print_simple_box_start("center","40%", "#FFBBBB");
            echo "<center>" . get_string("commentupdated","glossary") . "</center>";
            print_simple_box_end();

            print_footer($course);

            redirect("comments.php?id=$cm->id&eid=$entry->id");
        } else {
            if ($usehtmleditor = can_use_richtext_editor()) {
                $defaultformat = FORMAT_HTML;
                $onsubmit = "onsubmit=\"copyrichtext(theform.text);\"";
            } else {
                $defaultformat = FORMAT_MOODLE;
                $onsubmit = "";
            }

            $form->text = $comment->comment;
            $form->format = $comment->format;
            include("comment.html");
        }
    }
/// Finish the page
    print_footer($course);
?>