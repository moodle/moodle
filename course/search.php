<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays external information about a course
 * @package    core
 * @category   course
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot.'/course/lib.php');

$search    = optional_param('search', '', PARAM_RAW);  // search words
$page      = optional_param('page', 0, PARAM_INT);     // which page to show
$perpage   = optional_param('perpage', 10, PARAM_INT); // how many per page
$moveto    = optional_param('moveto', 0, PARAM_INT);   // move to category
$edit      = optional_param('edit', -1, PARAM_BOOL);
$hide      = optional_param('hide', 0, PARAM_INT);
$show      = optional_param('show', 0, PARAM_INT);
$blocklist = optional_param('blocklist', 0, PARAM_INT);
$modulelist= optional_param('modulelist', '', PARAM_PLUGIN);

// List of minimum capabilities which user need to have for editing/moving course
$capabilities = array('moodle/course:create', 'moodle/category:manage');

// List of category id's in which current user has course:create and category:manage capability.
$usercatlist = array();

// List of parent category id's
$catparentlist = array();

// Populate usercatlist with list of category id's with required capabilities.
make_categories_list($usercatlist, $catparentlist, $capabilities);

$search = trim(strip_tags($search)); // trim & clean raw searched string
if ($search) {
    $searchterms = explode(" ", $search);    // Search for words independently
    foreach ($searchterms as $key => $searchterm) {
        if (strlen($searchterm) < 2) {
            unset($searchterms[$key]);
        }
    }
    $search = trim(implode(" ", $searchterms));
}

$site = get_site();

$urlparams = array();
foreach (array('search', 'page', 'blocklist', 'modulelist', 'edit') as $param) {
    if (!empty($$param)) {
        $urlparams[$param] = $$param;
    }
}
if ($perpage != 10) {
    $urlparams['perpage'] = $perpage;
}
$PAGE->set_url('/course/search.php', $urlparams);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

if ($CFG->forcelogin) {
    require_login();
}

// Editing is possible if user has system or category level create and manage capability
if (can_edit_in_category() || !empty($usercatlist)) {
    if ($edit !== -1) {
        $USER->editing = $edit;
    }
    $adminediting = $PAGE->user_is_editing();

    // Set perpage if user can edit in category
    if ($perpage != 99999) {
        $perpage = 30;
    }
} else {
    $adminediting = false;
}

// Editing functions
if (has_capability('moodle/course:visibility', context_system::instance())) {
    // Hide or show a course
    if (($hide || $show) && confirm_sesskey()) {
        if ($hide) {
            $course = $DB->get_record("course", array("id" => $hide));
            $visible = 0;
        } else {
            $course = $DB->get_record("course", array("id" => $show));
            $visible = 1;
        }
        if ($course) {
            $DB->set_field("course", "visible", $visible, array("id" => $course->id));
        }
    }
}

$displaylist = array();
$parentlist = array();
make_categories_list($displaylist, $parentlist);

$strcourses = new lang_string("courses");
$strsearch = new lang_string("search");
$strsearchresults = new lang_string("searchresults");
$strcategory = new lang_string("category");
$strselect   = new lang_string("select");
$strselectall = new lang_string("selectall");
$strdeselectall = new lang_string("deselectall");
$stredit = new lang_string("edit");
$strfrontpage = new lang_string('frontpage', 'admin');
$strnovalidcourses = new lang_string('novalidcourses');

if (empty($search) and empty($blocklist) and empty($modulelist) and empty($moveto) and ($edit != -1)) {
    $PAGE->navbar->add($strcourses, new moodle_url('/course/index.php'));
    $PAGE->navbar->add($strsearch);
    $PAGE->set_title("$site->fullname : $strsearch");
    $PAGE->set_heading($site->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    echo "<center>";
    echo "<br />";
    print_course_search("", false, "plain");
    echo "<br /><p>";
    print_string("searchhelp");
    echo "</p>";
    echo "</center>";
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

$courses = array();
if (!empty($moveto) and $data = data_submitted() and confirm_sesskey()) {   // Some courses are being moved
    if (!$destcategory = $DB->get_record("course_categories", array("id" => $moveto))) {
        print_error('cannotfindcategory', '', '', $moveto);
    }

    // User should have manage and create capablity on destination category.
    require_capability('moodle/category:manage', context_coursecat::instance($moveto));
    require_capability('moodle/course:create', context_coursecat::instance($moveto));

    foreach ( $data as $key => $value ) {
        if (preg_match('/^c\d+$/', $key)) {
            $courseid = substr($key, 1);
            // user must have category:manage and course:create capability for the course to be moved.
            $coursecontext = context_course::instance($courseid);
            foreach ($capabilities as $capability) {
                // Require capability here will result in a fatal error should the user not
                // have the requried category ensuring that no moves occur if they are
                // trying to move multiple courses.
                require_capability($capability, $coursecontext);
                array_push($courses, $courseid);
            }
        }
    }
    move_courses($courses, $moveto);
}

// get list of courses containing blocks if required
if (!empty($blocklist) and confirm_sesskey()) {
    $blockname = $DB->get_field('block', 'name', array('id' => $blocklist));
    $courses = array();
    list($select, $join) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    $sql = "SELECT c.* $select FROM {course} c
            $join JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
            WHERE bi.blockname = ?";
    $courses = $DB->get_records_sql($sql, array($blockname));
    $totalcount = count($courses);
    // Keep only chunk of array which you want to display
    if ($totalcount > $perpage) {
        $courses = array_chunk($courses, $perpage, true);
        $courses = $courses[$page];
    }
    foreach ($courses as $course) {
        $courses[$course->id] = $course;
    }
} elseif (!empty($modulelist) and confirm_sesskey()) { // get list of courses containing modules
    $modulename = $modulelist;
    list($select, $join) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    $sql = "SELECT c.* $select FROM {course} c $join
            WHERE c.id IN (SELECT DISTINCT cc.id FROM {".$modulelist."} module, {course} cc
                           WHERE module.course = cc.id)";
    $courselist = $DB->get_records_sql($sql);
    $courses = array();
    if (!empty($courselist)) {
        $firstcourse = $page*$perpage;
        $lastcourse = $page*$perpage + $perpage -1;
        $i = 0;
        foreach ($courselist as $course) {
            if ($i >= $firstcourse && $i <= $lastcourse) {
                $courses[$course->id] = $course;
            }
            $i++;
        }
    }
    $totalcount = count($courselist);
} else if (!empty($searchterm)) {
    // Donot do search for empty search request.
    $courses = get_courses_search($searchterms, "fullname ASC", $page, $perpage, $totalcount);
}

$searchform = '';
// Turn editing should be visible if user have system or category level capability
if (!empty($courses) && (can_edit_in_category() || !empty($usercatlist))) {
    if ($PAGE->user_is_editing()) {
        $string = new lang_string("turneditingoff");
        $edit = "off";
    } else {
        $string = new lang_string("turneditingon");
        $edit = "on";
    }
    $params = array_merge($urlparams, array('sesskey' => sesskey(), 'edit' => $edit));
    $aurl = new moodle_url("$CFG->wwwroot/course/search.php", $params);
    $searchform = $OUTPUT->single_button($aurl, $string, 'get');
} else {
    $searchform = print_course_search($search, true, "navbar");
}

$PAGE->navbar->add($strcourses, new moodle_url('/course/index.php'));
$PAGE->navbar->add($strsearch, new moodle_url('/course/search.php'));
if (!empty($search)) {
    $PAGE->navbar->add(s($search));
}
$PAGE->set_title("$site->fullname : $strsearchresults");
$PAGE->set_heading($site->fullname);
$PAGE->set_button($searchform);

echo $OUTPUT->header();

$lastcategory = -1;
if ($courses) {
    echo $OUTPUT->heading("$strsearchresults: $totalcount");
    $encodedsearch = urlencode($search);

    // add the module/block parameter to the paging bar if they exists
    $modulelink = "";
    if (!empty($modulelist) and confirm_sesskey()) {
        $modulelink = "&amp;modulelist=".$modulelist."&amp;sesskey=".sesskey();
    } else if (!empty($blocklist) and confirm_sesskey()) {
        $modulelink = "&amp;blocklist=".$blocklist."&amp;sesskey=".sesskey();
    }

    print_navigation_bar($totalcount, $page, $perpage, $encodedsearch, $modulelink);

    // Show list of courses
    if (!$adminediting) { //Not editing mode
        foreach ($courses as $course) {
            // front page don't belong to any category and block can exist.
            if ($course->category > 0) {
                $course->summary .= "<br /><p class=\"category\">";
                $course->summary .= "$strcategory: <a href=\"category.php?id=$course->category\">";
                $course->summary .= $displaylist[$course->category];
                $course->summary .= "</a></p>";
            }
            print_course($course, $search);
            echo $OUTPUT->spacer(array('height'=>5, 'width'=>5, 'br'=>true)); // should be done with CSS instead
        }
    } else {
        // Editing mode
        echo "<form id=\"movecourses\" action=\"search.php\" method=\"post\">\n";
        echo "<div><input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />\n";
        echo "<input type=\"hidden\" name=\"search\" value=\"".s($search)."\" />\n";
        echo "<input type=\"hidden\" name=\"page\" value=\"$page\" />\n";
        echo "<input type=\"hidden\" name=\"perpage\" value=\"$perpage\" /></div>\n";
        if (!empty($modulelist) and confirm_sesskey()) {
            echo "<input type=\"hidden\" name=\"modulelist\" value=\"$modulelist\" /></div>\n";
        } else if (!empty($blocklist) and confirm_sesskey()) {
            echo "<input type=\"hidden\" name=\"blocklist\" value=\"$blocklist\" /></div>\n";
        }
        echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"4\" class=\"generalbox boxaligncenter\">\n<tr>\n";
        echo "<th scope=\"col\">$strcourses</th>\n";
        echo "<th scope=\"col\">$strcategory</th>\n";
        echo "<th scope=\"col\">$strselect</th>\n";
        echo "<th scope=\"col\">$stredit</th></tr>\n";

        foreach ($courses as $course) {

            context_helper::preload_from_record($course);
            $coursecontext = context_course::instance($course->id);

            $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

            // are we displaying the front page (courseid=1)?
            if ($course->id == 1) {
                echo "<tr>\n";
                echo "<td><a href=\"$CFG->wwwroot\">$strfrontpage</a></td>\n";

                // can't do anything else with the front page
                echo "  <td>&nbsp;</td>\n"; // category place
                echo "  <td>&nbsp;</td>\n"; // select place
                echo "  <td>&nbsp;</td>\n"; // edit place
                echo "</tr>\n";
                continue;
            }

            echo "<tr>\n";
            echo "<td><a $linkcss href=\"view.php?id=$course->id\">"
                . highlight($search, $coursecontext->get_context_name(false)) . "</a></td>\n";
            echo "<td>".$displaylist[$course->category]."</td>\n";
            echo "<td>\n";

            // If user has all required capabilities to move course then show selectable checkbox
            if (has_all_capabilities($capabilities, $coursecontext)) {
                echo "<input type=\"checkbox\" name=\"c$course->id\" />\n";
            } else {
                echo "<input type=\"checkbox\" name=\"c$course->id\" disabled=\"disabled\" />\n";
            }

            echo "</td>\n";
            echo "<td>\n";

            // checks whether user can update course settings
            if (has_capability('moodle/course:update', $coursecontext)) {
                echo "<a title=\"".get_string("settings")."\" href=\"$CFG->wwwroot/course/edit.php?id=$course->id\">\n<img".
                    " src=\"" . $OUTPUT->pix_url('t/edit') . "\" class=\"iconsmall\" alt=\"".get_string("settings")."\" /></a>\n ";
            }

            // checks whether user can do role assignment
            if (has_capability('moodle/course:enrolreview', $coursecontext)) {
                echo'<a title="'.get_string('enrolledusers', 'enrol').'" href="'.$CFG->wwwroot.'/enrol/users.php?id='.$course->id.'">';
                echo '<img src="'.$OUTPUT->pix_url('i/enrolusers') . '" class="iconsmall" alt="'.get_string('enrolledusers', 'enrol').'" /></a> ' . "\n";
            }

            // checks whether user can delete course
            if (has_capability('moodle/course:delete', $coursecontext)) {
                echo "<a title=\"".get_string("delete")."\" href=\"delete.php?id=$course->id\">\n<img".
                    " src=\"" . $OUTPUT->pix_url('t/delete') . "\" class=\"iconsmall\" alt=\"".get_string("delete")."\" /></a>\n ";
            }

            // checks whether user can change visibility
            if (has_capability('moodle/course:visibility', $coursecontext)) {
                if (!empty($course->visible)) {
                    echo "<a title=\"".get_string("hide")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;hide=$course->id&amp;sesskey=".sesskey()."\">\n<img".
                        " src=\"" . $OUTPUT->pix_url('t/hide') . "\" class=\"iconsmall\" alt=\"".get_string("hide")."\" /></a>\n ";
                } else {
                    echo "<a title=\"".get_string("show")."\" href=\"search.php?search=$encodedsearch&amp;perpage=$perpage&amp;page=$page&amp;show=$course->id&amp;sesskey=".sesskey()."\">\n<img".
                        " src=\"" . $OUTPUT->pix_url('t/show') . "\" class=\"iconsmall\" alt=\"".get_string("show")."\" /></a>\n ";
                }
            }

            // checks whether user can do site backup
            if (has_capability('moodle/backup:backupcourse', $coursecontext)) {
                $backupurl = new moodle_url('/backup/backup.php', array('id' => $course->id));
                echo "<a title=\"".get_string("backup")."\" href=\"".$backupurl."\">\n<img".
                    " src=\"" . $OUTPUT->pix_url('t/backup') . "\" class=\"iconsmall\" alt=\"".get_string("backup")."\" /></a>\n ";
            }

            // checks whether user can do restore
            if (has_capability('moodle/restore:restorecourse', $coursecontext)) {
                $restoreurl = new moodle_url('/backup/restorefile.php', array('contextid' => $coursecontext->id));
                echo "<a title=\"".get_string("restore")."\" href=\"".$restoreurl."\">\n<img".
                    " src=\"" . $OUTPUT->pix_url('t/restore') . "\" class=\"iconsmall\" alt=\"".get_string("restore")."\" /></a>\n ";
            }

            echo "</td>\n</tr>\n";
        }
        echo "<tr>\n<td colspan=\"4\" style=\"text-align:center\">\n";
        echo "<br />";
        echo "<input type=\"button\" onclick=\"checkall()\" value=\"$strselectall\" />\n";
        echo "<input type=\"button\" onclick=\"checknone()\" value=\"$strdeselectall\" />\n";
        // Select box should only show categories in which user has min capability to move course.
        echo html_writer::label(get_string('moveselectedcoursesto'), 'movetoid', false, array('class' => 'accesshide'));
        echo html_writer::select($usercatlist, 'moveto', '', array(''=>get_string('moveselectedcoursesto')), array('id'=>'movetoid', 'class' => 'autosubmit'));
        $PAGE->requires->yui_module('moodle-core-formautosubmit',
            'M.core.init_formautosubmit',
            array(array('selectid' => 'movetoid', 'nothing' => false))
        );
        echo "</td>\n</tr>\n";
        echo "</table>\n</form>";

    }

    print_navigation_bar($totalcount,$page,$perpage,$encodedsearch,$modulelink);

} else {
    if (!empty($search)) {
        echo $OUTPUT->heading(get_string("nocoursesfound",'', s($search)));
    }
    else {
        echo $OUTPUT->heading($strnovalidcourses);
    }
}

echo "<br /><br />";

print_course_search($search);

echo $OUTPUT->footer();

/**
 * Print a list navigation bar
 * Display page numbers, and a link for displaying all entries
 * @param int $totalcount number of entry to display
 * @param int $page page number
 * @param int $perpage number of entry per page
 * @param string $encodedsearch
 * @param string $modulelink module name
 */
function print_navigation_bar($totalcount, $page, $perpage, $encodedsearch, $modulelink) {
    global $OUTPUT;
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "search.php?search=$encodedsearch".$modulelink."&perpage=$perpage");

    // display
    if ($perpage != 99999 && $totalcount > $perpage) {
        echo "<center><p>";
        echo "<a href=\"search.php?search=$encodedsearch".$modulelink."&amp;perpage=99999\">".get_string("showall", "", $totalcount)."</a>";
        echo "</p></center>";
    } else if ($perpage === 99999) {
        $defaultperpage = 10;
        // If user has course:create or category:manage capability the show 30 records.
        $capabilities = array('moodle/course:create', 'moodle/category:manage');
        if (has_any_capability($capabilities, context_system::instance())) {
            $defaultperpage = 30;
        }

        echo "<center><p>";
        echo "<a href=\"search.php?search=$encodedsearch".$modulelink."&amp;perpage=".$defaultperpage."\">".get_string("showperpage", "", $defaultperpage)."</a>";
        echo "</p></center>";
    }
}
