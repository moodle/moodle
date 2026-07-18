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
 * Course Analytics Report Main Page.
 *
 * Displays analytics reports from multiple database tables, providing
 * search, pagination, sorting, and filters (Category, Completion, Last Access).
 *
 * @package    report_course_analytics
 * @copyright  2026 Antigravity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get course id (typically SITEID for system-wide analytics, or a specific course id)
$courseid = optional_param('course', SITEID, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$sort = optional_param('sort', 'coursename', PARAM_ALPHAEXT);
$dir = optional_param('dir', 'ASC', PARAM_ALPHA);

// Filters
$filtercategory = optional_param('category', 0, PARAM_INT);
$filtercompletion = optional_param('completion', 'all', PARAM_ALPHA);
$filterlastaccess = optional_param('lastaccess', 'all', PARAM_ALPHAEXT);

// Require login and capabilities
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);
require_login($course);
require_capability('report/course_analytics:view', $context);

// Page setup
$url = new moodle_url('/report/course_analytics/index.php', array(
    'course' => $courseid,
    'page' => $page,
    'perpage' => $perpage,
    'search' => $search,
    'sort' => $sort,
    'dir' => $dir,
    'category' => $filtercategory,
    'completion' => $filtercompletion,
    'lastaccess' => $filterlastaccess
));

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'report_course_analytics'));
$PAGE->set_heading(get_string('pluginname', 'report_course_analytics'));
$PAGE->set_pagelayout('report');

// Build database queries
$wheres = ["1=1"];
$params = [];

// Apply Search
if ($search !== '') {
    $wheres[] = "(" . $DB->sql_like('c.fullname', ':search', false, false) . " OR " . $DB->sql_like('cc.name', ':search2', false, false) . ")";
    $params['search'] = '%' . $search . '%';
    $params['search2'] = '%' . $search . '%';
}

// Apply Category Filter
if ($filtercategory > 0) {
    $wheres[] = "c.category = :categoryid";
    $params['categoryid'] = $filtercategory;
}

// Apply Last Access Filter
$now = time();
if ($filterlastaccess === '7days') {
    $wheres[] = "(SELECT MAX(ul.timeaccess) FROM {user_lastaccess} ul WHERE ul.courseid = c.id) >= :last7";
    $params['last7'] = $now - (7 * 24 * 3600);
} else if ($filterlastaccess === '30days') {
    $wheres[] = "(SELECT MAX(ul.timeaccess) FROM {user_lastaccess} ul WHERE ul.courseid = c.id) >= :last30";
    $params['last30'] = $now - (30 * 24 * 3600);
} else if ($filterlastaccess === 'more30days') {
    $wheres[] = "(SELECT MAX(ul.timeaccess) FROM {user_lastaccess} ul WHERE ul.courseid = c.id) < :last30_old AND (SELECT MAX(ul.timeaccess) FROM {user_lastaccess} ul WHERE ul.courseid = c.id) IS NOT NULL";
    $params['last30_old'] = $now - (30 * 24 * 3600);
} else if ($filterlastaccess === 'never') {
    $wheres[] = "(SELECT MAX(ul.timeaccess) FROM {user_lastaccess} ul WHERE ul.courseid = c.id) IS NULL";
}

// Construct subquery
$innerquery = "
    SELECT
        c.id,
        c.fullname AS coursename,
        cc.name AS categoryname,
        c.category AS categoryid,
        (SELECT COUNT(DISTINCT ue.userid)
         FROM {user_enrolments} ue
         JOIN {enrol} e ON ue.enrolid = e.id
         WHERE e.courseid = c.id) AS enrolled_students,
        (SELECT COUNT(a.id)
         FROM {assign} a
         WHERE a.course = c.id) AS assignments_count,
        (SELECT COUNT(q.id)
         FROM {quiz} q
         WHERE q.course = c.id) AS quizzes_count,
        (SELECT COUNT(cmc.id)
         FROM {course_modules_completion} cmc
         JOIN {course_modules} cm ON cmc.coursemoduleid = cm.id
         WHERE cm.course = c.id AND cmc.completionstate = 1) AS completed_activities,
        (SELECT COUNT(cm.id)
         FROM {course_modules} cm
         WHERE cm.course = c.id AND cm.completion > 0) AS completion_enabled_activities,
        (SELECT MAX(ul.timeaccess)
         FROM {user_lastaccess} ul
         WHERE ul.courseid = c.id) AS last_access
    FROM {course} c
    JOIN {course_categories} cc ON c.category = cc.id
    WHERE " . implode(' AND ', $wheres) . "
";

// Wrap in outer query to allow calculations & filters on completed metrics
$outerwheres = ["1=1"];
if ($filtercompletion === 'low') {
    $outerwheres[] = "completion_percentage < 50.0";
} else if ($filtercompletion === 'medium') {
    $outerwheres[] = "completion_percentage >= 50.0 AND completion_percentage <= 80.0";
} else if ($filtercompletion === 'high') {
    $outerwheres[] = "completion_percentage > 80.0";
}

$sqlselect = "
    SELECT
        id, coursename, categoryname, categoryid, enrolled_students,
        assignments_count, quizzes_count, completed_activities, completion_enabled_activities, last_access,
        COALESCE(completed_activities * 100.0 / NULLIF(enrolled_students * completion_enabled_activities, 0), 0) AS completion_percentage
    FROM ($innerquery) course_data
    WHERE " . implode(' AND ', $outerwheres) . "
";

// Validate Sort
$allowedsort = ['coursename', 'categoryname', 'enrolled_students', 'assignments_count', 'quizzes_count', 'completed_activities', 'last_access', 'completion_percentage'];
if (!in_array($sort, $allowedsort)) {
    $sort = 'coursename';
}
$dir = ($dir === 'DESC') ? 'DESC' : 'ASC';
$sqlorder = " ORDER BY $sort $dir";

// Fetch count and paginated records
$countsql = "SELECT COUNT(id) FROM ($sqlselect) count_query";
$totalcount = $DB->count_records_sql($countsql, $params);

$records = [];
if ($totalcount > 0) {
    $records = $DB->get_records_sql($sqlselect . $sqlorder, $params, $page * $perpage, $perpage);
}

// Fetch categories for filter dropdown
$categories = $DB->get_records('course_categories', array(), 'name ASC', 'id, name');

// Render the page
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_course_analytics'));

// Filters and Search Form
echo html_writer::start_div('row mb-4 justify-content-between align-items-end');
echo html_writer::start_div('col-md-12');
echo html_writer::start_tag('form', array('method' => 'get', 'action' => $PAGE->url->out_omit_querystring(), 'class' => 'form-inline'));
echo html_writer::input_hidden_params($url);

// Search input
echo html_writer::start_div('form-group mr-2 mb-2');
echo html_writer::label(get_string('search', 'report_course_analytics'), 'searchinput', false, array('class' => 'sr-only'));
echo html_writer::empty_tag('input', array(
    'type' => 'text',
    'name' => 'search',
    'id' => 'searchinput',
    'value' => $search,
    'placeholder' => get_string('search', 'report_course_analytics') . '...',
    'class' => 'form-control'
));
echo html_writer::end_div();

// Category filter
echo html_writer::start_div('form-group mr-2 mb-2');
$catoptions = array(0 => get_string('allcategories', 'report_course_analytics'));
foreach ($categories as $cat) {
    $catoptions[$cat->id] = format_string($cat->name);
}
echo html_writer::select($catoptions, 'category', $filtercategory, false, array('class' => 'form-control'));
echo html_writer::end_div();

// Completion filter
echo html_writer::start_div('form-group mr-2 mb-2');
$compoptions = array(
    'all' => get_string('allcompletion', 'report_course_analytics'),
    'low' => get_string('lowcompletion', 'report_course_analytics'),
    'medium' => get_string('mediumcompletion', 'report_course_analytics'),
    'high' => get_string('highcompletion', 'report_course_analytics')
);
echo html_writer::select($compoptions, 'completion', $filtercompletion, false, array('class' => 'form-control'));
echo html_writer::end_div();

// Last Access filter
echo html_writer::start_div('form-group mr-2 mb-2');
$accessoptions = array(
    'all' => get_string('alllastaccess', 'report_course_analytics'),
    '7days' => get_string('last7days', 'report_course_analytics'),
    '30days' => get_string('last30days', 'report_course_analytics'),
    'more30days' => get_string('morethan30days', 'report_course_analytics'),
    'never' => get_string('never', 'report_course_analytics')
);
echo html_writer::select($accessoptions, 'lastaccess', $filterlastaccess, false, array('class' => 'form-control'));
echo html_writer::end_div();

// Submit button
echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search', 'report_course_analytics'), 'class' => 'btn btn-primary mr-2 mb-2'));

// Clear filters link
$clearurl = new moodle_url('/report/course_analytics/index.php', array('course' => $courseid));
echo html_writer::link($clearurl, get_string('clear', 'report_course_analytics'), array('class' => 'btn btn-secondary mb-2'));

echo html_writer::end_tag('form');
echo html_writer::end_div();
echo html_writer::end_div();

// Render Report Table
if (empty($records)) {
    echo $OUTPUT->notification(get_string('nocourses', 'report_course_analytics'), 'info');
} else {
    $table = new html_table();
    
    // Setup table headers with sort links
    $headers = [
        'coursename' => get_string('coursename', 'report_course_analytics'),
        'categoryname' => get_string('category', 'report_course_analytics'),
        'enrolled_students' => get_string('enrolledstudents', 'report_course_analytics'),
        'assignments_count' => get_string('assignments', 'report_course_analytics'),
        'quizzes_count' => get_string('quizzes', 'report_course_analytics'),
        'completed_activities' => get_string('completedactivities', 'report_course_analytics'),
        'last_access' => get_string('lastaccess', 'report_course_analytics'),
        'completion_percentage' => get_string('completionpercentage', 'report_course_analytics')
    ];
    
    $table->head = [];
    foreach ($headers as $key => $title) {
        $newdir = ($sort === $key && $dir === 'ASC') ? 'DESC' : 'ASC';
        $sorturl = new moodle_url($url, ['sort' => $key, 'dir' => $newdir, 'page' => 0]);
        $icon = '';
        if ($sort === $key) {
            $icon = ($dir === 'ASC') ? ' <i class="fa fa-caret-up"></i>' : ' <i class="fa fa-caret-down"></i>';
        }
        $table->head[] = html_writer::link($sorturl, $title . $icon);
    }
    
    // Fill table rows
    foreach ($records as $record) {
        // Handle last access date display
        $lastaccessdate = get_string('never', 'report_course_analytics');
        if (!empty($record->last_access)) {
            $lastaccessdate = userdate($record->last_access, get_string('strftimedatetime', 'langconfig'));
        }
        
        // Format completion percentage
        $completiontext = number_format($record->completion_percentage, 1) . '%';
        
        // Add color visual indicator to low completions
        if ($record->completion_percentage < 50.0) {
            $completiontext = html_writer::span($completiontext, 'text-danger font-weight-bold');
        } else if ($record->completion_percentage > 80.0) {
            $completiontext = html_writer::span($completiontext, 'text-success');
        }
        
        $table->data[] = [
            html_writer::link(new moodle_url('/course/view.php', ['id' => $record->id]), format_string($record->coursename)),
            format_string($record->categoryname),
            $record->enrolled_students,
            $record->assignments_count,
            $record->quizzes_count,
            $record->completed_activities,
            $lastaccessdate,
            $completiontext
        ];
    }
    
    echo html_writer::table($table);
    
    // Pagination rendering
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $url);
}

echo $OUTPUT->footer();
