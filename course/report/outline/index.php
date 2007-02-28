<?php // $Id$

// Display user activity reports for a course (totals)

    require_once("../../../config.php");
    require_once("../../lib.php");

    $modes = array("outline", "complete", "todaylogs", "alllogs");

    $id      = required_param('id',PARAM_INT);       // course id
    $page    = optional_param('page', 0, PARAM_INT);
    $perpage = optional_param('perpage', 100, PARAM_INT);

    require_login();

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    require_capability('moodle/site:viewreports', get_context_instance(CONTEXT_COURSE, $course->id));

    add_to_log($course->id, "course", "report outline", "report/outline/index.php?id=$course->id", $course->id); 

    $stractivityreport = get_string("activityreport");
    $strparticipants   = get_string("participants");
    $stroutline        = get_string("outline");
    $strcomplete       = get_string("complete");
    $stralllogs        = get_string("alllogs");
    $strtodaylogs      = get_string("todaylogs");
    $strreports        = get_string("reports");

    print_header("$course->shortname: $stractivityreport", $course->fullname,
                 "<a href=\"../../view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"../../report.php?id=$course->id\">$strreports</a> ->
                  $stractivityreport");

    print_heading(format_string($course->fullname));

    echo "<table cellpadding=\"10\" align=\"center\"><tr>";
    echo "<td>$stractivityreport: </td>";
    echo "</tr></table>";

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    $sections = get_all_sections($course->id);

    for ($i=0; $i<=$course->numsections; $i++) {

        if (isset($sections[$i])) {   // should always be true

            $section = $sections[$i];

            if ($section->sequence) {
                echo "<hr />";
                echo "<h2>";
                switch ($course->format) {
                    case "weeks": print_string("week"); break;
                    case "topics": print_string("topic"); break;
                    default: print_string("section"); break;
                }
                echo " $i</h2>";
                echo "<table cellpadding=\"4\" cellspacing=\"0\">";

                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    if (empty($mods[$sectionmod])) {
                        continue;
                    }
                    $mod = $mods[$sectionmod];
                    $instance = get_record("$mod->modname", "id", "$mod->instance");
                    $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";


                    $result = null;
                    if ($logs = get_records_select("log", "module='$mod->modname'
                                           AND action LIKE 'view%' AND info='$mod->instance'", "time ASC")) {

                        $numviews = count($logs);
                        $lastlog = array_pop($logs);

                        $result->info = get_string("numviews", "", $numviews);
                        $result->time = $lastlog->time;
                    }
                    print_outline_row($mod, $instance, $result);
                }

                echo "</table>";
            }
        }
    }

    print_footer($course);


function print_outline_row($mod, $instance, $result) {

    global $CFG;

    $image = "<img src=\"$CFG->modpixpath/$mod->modname/icon.gif\" class=\"icon\" alt=\"$mod->modfullname\" />";

    echo "<tr>";
    echo "<td valign=\"top\">$image</td>";
    echo "<td valign=\"top\" width=\"300\">";
    echo "   <a title=\"$mod->modfullname\"";
    echo "   href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">$instance->name</a></td>";
    echo "<td>&nbsp;&nbsp;&nbsp;</td>";
    echo "<td valign=\"top\" bgcolor=\"white\">";
    if (isset($result->info)) {
        echo "$result->info";
    } else {
        echo "<p align=\"center\">-</p>";
    }
    echo "</td>";
    echo "<td>&nbsp;&nbsp;&nbsp;</td>";
    if (isset($result->time)) {
        $timeago = format_time(time() - $result->time);
        echo "<td valign=\"top\" style=\"white-space: nowrap\">".userdate($result->time)." ($timeago)</td>";
    }
    echo "</tr>";
}

?>