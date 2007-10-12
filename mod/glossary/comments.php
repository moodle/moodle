<?php // $Id$

/// This page prints a particular instance of glossary
    require_once('../../config.php');
    require_once('lib.php');

    $id  = required_param('id', PARAM_INT);           // Course Module ID
    $eid = required_param('eid', PARAM_INT);          // Entry ID

    global $USER, $CFG;

    if (! $cm = get_coursemodule_from_id('glossary', $id)) {
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

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    require_login($course->id, false, $cm);

    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id", "$glossary->id",$cm->id);

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

    $navigation = build_navigation($strcomments, $cm);
    print_header_simple(strip_tags("$strcomments: $entry->concept"), "", $navigation,
        "", "", true, update_module_button($cm->id, $course->id, $strglossary),
        navmenu($course, $cm));

/// original glossary entry

    echo "<div class=\"boxaligncenter\">";
    glossary_print_entry($course, $cm, $glossary, $entry, "", "", false);
    echo "</div>";

/// comments

    print_heading(format_string(get_string('commentson','glossary')." <b>\"$entry->concept\"</b>"));

    if (has_capability('mod/glossary:comment', $context) and $glossary->allowcomments) {
        print_heading("<a href=\"comment.php?action=add&amp;eid=$entry->id\">$straddcomment <img title=\"$straddcomment\" src=\"comment.gif\" class=\"iconsmall\" alt=\"$straddcomment\" /></a>");
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
