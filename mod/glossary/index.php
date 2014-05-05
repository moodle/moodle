<?php

/// This page lists all the instances of glossary in a particular course
/// Replace glossary with the name of your module

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->libdir/rsslib.php");
require_once("$CFG->dirroot/course/lib.php");

$id = required_param('id', PARAM_INT);   // course

$PAGE->set_url('/mod/glossary/index.php', array('id'=>$id));

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');
$context = context_course::instance($course->id);

$event = \mod_glossary\event\course_module_instance_list_viewed::create(array(
    'context' => $context
));
$event->add_record_snapshot('course', $course);
$event->trigger();

/// Get all required strings

$strglossarys = get_string("modulenameplural", "glossary");
$strglossary  = get_string("modulename", "glossary");
$strrss = get_string("rss");


/// Print the header
$PAGE->navbar->add($strglossarys, "index.php?id=$course->id");
$PAGE->set_title($strglossarys);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($strglossarys), 2);

/// Get all the appropriate data

if (! $glossarys = get_all_instances_in_course("glossary", $course)) {
    notice(get_string('thereareno', 'moodle', $strglossarys), "../../course/view.php?id=$course->id");
    die;
}

$usesections = course_format_uses_sections($course->format);

/// Print the list of instances (your module will probably extend this)

$timenow = time();
$strname  = get_string("name");
$strentries  = get_string("entries", "glossary");

$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strentries);
    $table->align = array ("CENTER", "LEFT", "CENTER");
} else {
    $table->head  = array ($strname, $strentries);
    $table->align = array ("LEFT", "CENTER");
}

if ($show_rss = (isset($CFG->enablerssfeeds) && isset($CFG->glossary_enablerssfeeds) &&
                 $CFG->enablerssfeeds && $CFG->glossary_enablerssfeeds)) {
    $table->head[] = $strrss;
    $table->align[] = "CENTER";
}

$currentsection = "";

foreach ($glossarys as $glossary) {
    if (!$glossary->visible && has_capability('moodle/course:viewhiddenactivities', $context)) {
        // Show dimmed if the mod is hidden.
        $link = "<a class=\"dimmed\" href=\"view.php?id=$glossary->coursemodule\">".format_string($glossary->name,true)."</a>";
    } else if ($glossary->visible) {
        // Show normal if the mod is visible.
        $link = "<a href=\"view.php?id=$glossary->coursemodule\">".format_string($glossary->name,true)."</a>";
    } else {
        // Don't show the glossary.
        continue;
    }
    $printsection = "";
    if ($usesections) {
        if ($glossary->section !== $currentsection) {
            if ($glossary->section) {
                $printsection = get_section_name($course, $glossary->section);
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $glossary->section;
        }
    }

    // TODO: count only approved if not allowed to see them

    $count = $DB->count_records_sql("SELECT COUNT(*) FROM {glossary_entries} WHERE (glossaryid = ? OR sourceglossaryid = ?)", array($glossary->id, $glossary->id));

    //If this glossary has RSS activated, calculate it
    if ($show_rss) {
        $rsslink = '';
        if ($glossary->rsstype and $glossary->rssarticles) {
            //Calculate the tolltip text
            $tooltiptext = get_string("rsssubscriberss","glossary",format_string($glossary->name));
            if (!isloggedin()) {
                $userid = 0;
            } else {
                $userid = $USER->id;
            }
            //Get html code for RSS link
            $rsslink = rss_get_link($context->id, $userid, 'mod_glossary', $glossary->id, $tooltiptext);
        }
    }

    if ($usesections) {
        $linedata = array ($printsection, $link, $count);
    } else {
        $linedata = array ($link, $count);
    }

    if ($show_rss) {
        $linedata[] = $rsslink;
    }

    $table->data[] = $linedata;
}

echo "<br />";

echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer();

