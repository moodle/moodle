<?php // $Id$

/// This page lists all the instances of glossary in a particular course
/// Replace glossary with the name of your module

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->dirroot/rss/rsslib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);

    add_to_log($course->id, "glossary", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strglossarys = get_string("modulenameplural", "glossary");
    $strglossary  = get_string("modulename", "glossary");
    $strrss = get_string("rss");


/// Print the header

    print_header_simple("$strglossarys", "", "$strglossarys", "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $glossarys = get_all_instances_in_course("glossary", $course)) {
        notice("There are no glossaries", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");
    $strentries  = get_string("entries", "glossary");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strentries);
        $table->align = array ("CENTER", "LEFT", "CENTER");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strentries);
        $table->align = array ("CENTER", "LEFT", "CENTER");
    } else {
        $table->head  = array ($strname, $strentries);
        $table->align = array ("LEFT", "CENTER");
    }

    $can_subscribe = (isstudent($course->id) or isteacher($course->id) or isadmin());

    if ($show_rss = ($can_subscribe && isset($CFG->enablerssfeeds) && isset($CFG->glossary_enablerssfeeds) &&
                     $CFG->enablerssfeeds && $CFG->glossary_enablerssfeeds)) {
        $table->head[] = $strrss;
        $table->align[] = "CENTER";
    }

    $currentsection = "";

    foreach ($glossarys as $glossary) {
        if (!$glossary->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$glossary->coursemodule\">$glossary->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$glossary->coursemodule\">$glossary->name</a>";
        }
        $printsection = "";
        if ($glossary->section !== $currentsection) {
            if ($glossary->section) {
                $printsection = $glossary->section;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $glossary->section;
        }

        $count = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}glossary_entries where (glossaryid = $glossary->id or sourceglossaryid = $glossary->id)");

        //If this glossary has RSS activated, calculate it
        if ($show_rss) {
            $rsslink = '';
            if ($glossary->rsstype and $glossary->rssarticles) {
                //Calculate the tolltip text
                $tooltiptext = get_string("rsssubscriberss","glossary",$glossary->name);
                //Get html code for RSS link
                $rsslink = rss_get_link($course->id, $USER->id, "glossary", $glossary->id, $tooltiptext);
            }
        }

        if ($course->format == "weeks" or $course->format == "topics") {
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

    print_table($table);

/// Finish the page

    print_footer($course);

?>
