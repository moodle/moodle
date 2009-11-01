<?php

/// This page prints a particular instance of glossary
require_once('../../config.php');
require_once('lib.php');

$id  = required_param('id', PARAM_INT);           // Course Module ID
$eid = required_param('eid', PARAM_INT);          // Entry ID

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/glossary/comments.php', array('id'=>$id,'eid'=>$eid)));

if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
    print_error('invalidcousemodule');
}

if (! $entry = $DB->get_record("glossary_entries", array("id"=>$eid))) {
    print_error('invalidentry');
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

$PAGE->navbar->add($strcomments);
$PAGE->set_title(strip_tags("$strcomments: $entry->concept"));
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'glossary'));
echo $OUTPUT->header();

/// original glossary entry

echo "<div class=\"boxaligncenter\">";
glossary_print_entry($course, $cm, $glossary, $entry, "", "", false);
echo "</div>";

/// comments

echo $OUTPUT->heading(format_string(get_string('commentson','glossary')." <b>\"$entry->concept\"</b>"));

if (has_capability('mod/glossary:comment', $context) and $glossary->allowcomments) {
    echo $OUTPUT->heading("<a href=\"comment.php?action=add&amp;entryid=$entry->id\">$straddcomment <img title=\"$straddcomment\" src=\"comment.gif\" class=\"iconsmall\" alt=\"$straddcomment\" /></a>");
}

if ($comments = $DB->get_records("glossary_comments", array("entryid"=>$entry->id), "timemodified ASC")) {
    foreach ($comments as $comment) {
        glossary_print_comment($course, $cm, $glossary, $entry, $comment);
        echo '<br />';
    }
} else {
    echo $OUTPUT->heading(get_string("nocomments","glossary"));
}


/// Finish the page

echo $OUTPUT->footer();

