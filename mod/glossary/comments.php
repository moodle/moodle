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
    
    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id", "$glossary->id",$cm->id);
    
    
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
    $strcomments = get_string("comments", "glossary");
    $straddcomment = get_string("addcomment", "glossary");
    
    print_header(strip_tags("$course->shortname: $strcomments: $entry->concept"), "$course->fullname",
        "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> <A HREF=view.php?id=$cm->id>$glossary->name</a> -> $strcomments",
        "", "", true, update_module_button($cm->id, $course->id, $strglossary),
        navmenu($course, $cm));
    
/// original glossary entry

    echo "<center>";
    glossary_print_entry($course, $cm, $glossary, $entry, "", "", false);
    echo "</center>";

/// comments

    print_heading(get_string('commentson','glossary')." <b>\"$entry->concept\"</b>");

    if ($glossary->allowcomments) {	
        print_heading("<a href=\"comment.php?id=$cm->id&eid=$entry->id\">$straddcomment</a> <img title=\"$straddcomment\" src=\"comment.gif\" height=11 width=11 border=0>");
    }

    if ($comments = get_records("glossary_comments","entryid",$entry->id,"timemodified ASC")) {
        foreach ($comments as $comment) {
		    glossary_print_comment($course, $cm, $glossary, $entry, $comment);
            echo '<br />';
        }
    } else {
        print_heading(get_string("nocomments","glossary"));
    }


/// Finish the page

    print_footer($course);

?>
