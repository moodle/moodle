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
require_once($CFG->libdir.'/coursecatlib.php');

$search    = optional_param('search', '', PARAM_RAW);  // search words
$page      = optional_param('page', 0, PARAM_INT);     // which page to show
$perpage   = optional_param('perpage', '', PARAM_RAW); // how many per page, may be integer or 'all'
$blocklist = optional_param('blocklist', 0, PARAM_INT);
$modulelist= optional_param('modulelist', '', PARAM_PLUGIN);

// List of minimum capabilities which user need to have for editing/moving course
$capabilities = array('moodle/course:create', 'moodle/category:manage');

// Populate usercatlist with list of category id's with course:create and category:manage capabilities.
$usercatlist = coursecat::make_categories_list($capabilities);

$search = trim(strip_tags($search)); // trim & clean raw searched string

$site = get_site();

$searchcriteria = array();
foreach (array('search', 'blocklist', 'modulelist') as $param) {
    if (!empty($$param)) {
        $searchcriteria[$param] = $$param;
    }
}
$urlparams = array();
if ($perpage !== 'all' && !($perpage = (int)$perpage)) {
    // default number of courses per page
    $perpage = $CFG->coursesperpage;
} else {
    $urlparams['perpage'] = $perpage;
}
if (!empty($page)) {
    $urlparams['page'] = $page;
}
$PAGE->set_url('/course/search.php', $searchcriteria + $urlparams);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$courserenderer = $PAGE->get_renderer('core', 'course');

if ($CFG->forcelogin) {
    require_login();
}

$strcourses = new lang_string("courses");
$strsearch = new lang_string("search");
$strsearchresults = new lang_string("searchresults");
$strnovalidcourses = new lang_string('novalidcourses');

if (empty($searchcriteria)) {
    // no search criteria specified, print page with just search form
    $PAGE->navbar->add($strcourses, new moodle_url('/course/index.php'));
    $PAGE->navbar->add($strsearch);
    $PAGE->set_title("$site->fullname : $strsearch");
    $PAGE->set_heading($site->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    echo "<center>";
    echo "<br />";
    echo $courserenderer->course_search_form('', 'plain');
    echo "<br /><p>";
    print_string("searchhelp");
    echo "</p>";
    echo "</center>";
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// get list of courses
$searchoptions = array('recursive' => true, 'sort' => array('fullname' => 1));
if ($perpage !== 'all') {
   $searchoptions['offset'] = $page * $perpage;
   $searchoptions['limit'] = $perpage;
}
$courses = coursecat::get(0)->search_courses($searchcriteria, $searchoptions);
$totalcount = coursecat::get(0)->search_courses_count($searchcriteria, $searchoptions);

$searchform = '';
// Turn editing should be visible if user have system or category level capability
if (!empty($courses) && (can_edit_in_category() || !empty($usercatlist))) {
    $aurl = new moodle_url('/course/manage.php', $searchcriteria);
    $searchform = $OUTPUT->single_button($aurl, get_string('managecourses'), 'get');
} else {
    $searchform = $courserenderer->course_search_form($search, 'navbar');
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

if ($courses) {
    echo $OUTPUT->heading("$strsearchresults: $totalcount");

    // add the module/block parameter to the paging bar if they exists
    $modulelink = "";
    if (!empty($modulelist) and confirm_sesskey()) {
        $modulelink = "&amp;modulelist=".$modulelist."&amp;sesskey=".sesskey();
    } else if (!empty($blocklist) and confirm_sesskey()) {
        $modulelink = "&amp;blocklist=".$blocklist."&amp;sesskey=".sesskey();
    }

    print_navigation_bar($totalcount, $page, $perpage, $searchcriteria);

    // Show list of courses
    echo $courserenderer->courses_list($courses, $search, true);

    print_navigation_bar($totalcount, $page, $perpage, $searchcriteria);

} else {
    if (!empty($search)) {
        echo $OUTPUT->heading(get_string("nocoursesfound",'', s($search)));
    }
    else {
        echo $OUTPUT->heading($strnovalidcourses);
    }
}

echo "<br /><br />";

echo $courserenderer->course_search_form($search);

echo $OUTPUT->footer();

/**
 * Print a list navigation bar
 * Display page numbers, and a link for displaying all entries
 * @param int $totalcount number of entry to display
 * @param int $page page number
 * @param int $perpage number of entry per page
 * @param array $search
 */
function print_navigation_bar($totalcount, $page, $perpage, $search) {
    global $OUTPUT, $CFG;
    $url = new moodle_url('/course/search.php', $search);
    if ($perpage !== 'all' && $totalcount > $perpage) {
        echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $url->out(false, array('perpage' => $perpage)));
        echo "<center><p>";
        echo html_writer::link($url->out(false, array('perpage' => 'all')), get_string("showall", "", $totalcount));
        echo "</p></center>";
    } else if ($perpage === 'all') {
        echo "<center><p>";
        echo html_writer::link($url->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string("showperpage", "", $CFG->coursesperpage));
        echo "</p></center>";
    }
}
