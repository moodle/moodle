<?PHP // $Id$

/// This page lists all the instances of wiki in a particular course
/// Replace wiki with the name of your module

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "wiki", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strwikis = get_string("modulenameplural", "wiki");
    $strwiki  = get_string("modulename", "wiki");


/// Print the header

    print_header_simple("$strwikis", "", "$strwikis", "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $wikis = get_all_instances_in_course("wiki", $course)) {
        notice("There are no wikis", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string('wikiname', 'wiki');
    $strsummary = get_string('summary');
    $strtype = get_string('wikitype', 'wiki');
    $strlastmodified = get_string('lastmodified');
    $strweek  = get_string('week');
    $strtopic  = get_string('topic');

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strsummary, $strtype, $strlastmodified);
        $table->align = array ('CENTER', 'LEFT', 'LEFT', 'LEFT', 'LEFT');
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strsummary, $strtype, $strlastmodified);
        $table->align = array ('CENTER', 'LEFT', 'LEFT', 'LEFT', 'LEFT');
    } else {
        $table->head  = array ($strname, $strsummary, $strtype, $strlastmodified);
        $table->align = array ('LEFT', 'LEFT', 'LEFT', 'LEFT');
    }

    foreach ($wikis as $wiki) {
        if (!$wiki->visible) {
            //Show dimmed if the mod is hidden
            $link = '<A class="dimmed" HREF="view.php?id='.$wiki->coursemodule.'">'.$wiki->name.'</A>';
        } else {
            //Show normal if the mod is visible
            $link = '<A HREF="view.php?id='.$wiki->coursemodule.'">'.$wiki->name.'</A>';
        }

        $timmod = '<span class="smallinfo">'.userdate($wiki->timemodified).'</span>';
        $summary = '<span class="smallinfo">'.$wiki->summary.'</span>';

        $site = get_site();
        switch ($wiki->wtype) {

        case 'teacher':
            $wtype = $site->teacher;
            break;

        case 'student':
            $wtype = $site->student;
            break;

        case 'group':
        default:
            $wtype = get_string('group');
            break;
        }

        $wtype = '<span class="smallinfo">'.$wtype.'</span>';

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($wiki->section, $link, $summary, $wtype, $timmod);
        } else {
            $table->data[] = array ($link, $summary, $wtype, $timmod);
        }
    }

    echo "<BR>";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
