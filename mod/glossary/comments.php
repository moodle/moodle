<?php // $Id$

/// This page prints a particular instance of glossary
    require_once("../../config.php");
    require_once("lib.php");
    
    require_variable($id);           // Course Module ID
    require_variable($eid);         // Entry ID
	
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


    require_login($course->id);    
    if (!$cm->visible and !isteacher($course->id)) {
        notice(get_string("activityiscurrentlyhidden"));
    } 
    
    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id", "$glossary->id");
    
    
/// Printing the page header
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } 
    
    $strglossaries = get_string("modulenameplural", "glossary");
    $strglossary = get_string("modulename", "glossary");
    $strallcategories = get_string("allcategories", "glossary");
    $straddentry = get_string("addentry", "glossary");
    $strnoentries = get_string("noentries", "glossary");
    $strsearchconcept = get_string("searchconcept", "glossary");
    $strsearchindefinition = get_string("searchindefinition", "glossary");
    $strsearch = get_string("search");
    
    print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
        "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> <A HREF=view.php?id=$cm->id>$glossary->name</a> -> " . get_string("comments","glossary"),
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
	    echo "<table border=0 width=100%><tr><td width=33%></td><td width=33% align=center>";
	    echo get_string("commentson","glossary") . ' <b>' . glossary_print_entry_concept($entry) . '</b></td>';
	    echo "<td width=33% align=right>";	
        if ( $glossary->allowcomments ) {	
            echo "<a href=\"comment.php?id=$cm->id&eid=$entry->id\"><img  alt=\"" . get_string("addcomment","glossary") . "\" src=\"comment.gif\" height=16 width=16 border=0></a> ";
        }
	    echo "</td></tr></table>";
			
	echo "</td></tr>";
    echo "<tr><TD WIDTH=100% BGCOLOR=\"#FFFFFF\">";
    if ($entry->attachment) {
          $entry->course = $course->id;
          echo "<table border=0 align=right><tr><td>";
          echo glossary_print_attachments($entry,"html");
          echo "</td></tr></table>";
    }
    echo "<b>$entry->concept</b>: ";
    glossary_print_entry_definition($entry);
    echo "</td>";
    echo "</TR></table>";

/// comments
    $comments = get_records("glossary_comments","entryid",$entry->id,"timemodified ASC");
    if ( !$comments ) {
        echo "<p align=center><strong>" .  get_string("nocomments","glossary") . "</string>";
    } else {
        foreach ($comments as $comment) {
		    echo "<p align=center>";
		    glossary_print_comment($course, $cm, $glossary, $entry, $comment);
        }
    }

/// Finish the page
    print_footer($course);
?>