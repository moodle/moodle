<?PHP // $Id$

/// This page lists all the instances of wiki in a particular course
/// Replace wiki with the name of your module

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);

    add_to_log($course->id, "wiki", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strwikis = get_string("modulenameplural", "wiki");
    $strwiki  = get_string("modulename", "wiki");


/// Print the header
    $navlinks = array();
    $navlinks[] = array('name' => $strwikis, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple("$strwikis", "", $navigation, "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (!$cms = get_coursemodules_in_course('wiki', $course->id, 'm.summary, m.wtype, m.timemodified')) {
        notice(get_string('thereareno', 'moodle', $strwikis), "../../course/view.php?id=$course->id");
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

    $modinfo = get_fast_modinfo($course);
    foreach ($modinfo->instances['wiki'] as $cm) {
        if (!$cm->uservisible) {
            continue;
        }

        $cm->summary      = $cms[$cm->id]->summary;
        $cm->wtype        = $cms[$cm->id]->wtype;
        $cm->timemodified = $cms[$cm->id]->timemodified;

        $class = $cm->visible ? '' : 'class="dimmed"';
        $link = '<a '.$class.' href="view.php?id='.$cm->id.'">'.format_string($cm->name).'</a>';

        $timmod = '<span class="smallinfo">'.userdate($cm->timemodified).'</span>';
        $summary = '<span class="smallinfo">'.format_text($cm->summary).'</span>';

        $site = get_site();
        switch ($cm->wtype) {

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
            $table->data[] = array ($cm->sectionnum, $link, $summary, $wtype, $timmod);
        } else {
            $table->data[] = array ($link, $summary, $wtype, $timmod);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
