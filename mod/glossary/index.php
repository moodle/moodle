<?PHP // $Id$

/// This page lists all the instances of glossary in a particular course
/// Replace glossary with the name of your module

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "glossary", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strglossarys = get_string("modulenameplural", "glossary");
    $strglossary  = get_string("modulename", "glossary");


/// Print the header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    print_header("$course->shortname: $strglossarys", "$course->fullname", "$navigation $strglossarys", "", "", true, "", navmenu($course));

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

    $currentsection = "";

    foreach ($glossarys as $glossary) {
        if (!$glossary->visible) {
            //Show dimmed if the mod is hidden
            $link = "<A class=\"dimmed\" HREF=\"view.php?id=$glossary->coursemodule\">$glossary->name</A>";
        } else {
            //Show normal if the mod is visible
            $link = "<A HREF=\"view.php?id=$glossary->coursemodule\">$glossary->name</A>";
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

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($printsection, $link, $count);
        } else {
            $table->data[] = array ($link, $count);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
