<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
// Only allow running on local installations.
local_courseanalytics_ensure_local();
require_login();
$context = context_system::instance();
require_capability('local/courseanalytics:viewreport', $context);

$PAGE->set_url(new moodle_url('/local/courseanalytics/report.php'));
$PAGE->set_context($context);
$PAGE->set_title('Course Analytics Report');
$PAGE->set_heading('Course Analytics Report');

echo $OUTPUT->header();

global $DB, $PAGE, $OUTPUT;

$search = optional_param('search', '', PARAM_TEXT);
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = optional_param('perpage', 20, PARAM_INT);
$sort = optional_param('sort', 'fullname', PARAM_ALPHANUMEXT);
$category = optional_param('category', 0, PARAM_INT);
$mincompletion = optional_param('mincompletion', 0, PARAM_INT);
$lastaccess_days = optional_param('lastaccess_days', 0, PARAM_INT);

$where = [];
$params = [];
if ($search !== '') {
    $where[] = "(c.fullname LIKE :search OR c.shortname LIKE :search)";
    $params['search'] = '%'.$search.'%';
}
if ($category) {
    $where[] = 'c.category = :category';
    $params['category'] = $category;
}

$whereclauses = '';
if (!empty($where)) {
    $whereclauses = 'WHERE ' . implode(' AND ', $where);
}

$countsql = "SELECT COUNT(*) FROM {course} c $whereclauses";
$total = $DB->count_records_sql($countsql, $params);

$offset = $page * $perpage;

$sortwhitelist = ['fullname','category','enrolled','assignments','quizzes','completionpct','lastaccess'];
if (!in_array($sort, $sortwhitelist)) {
    $sort = 'fullname';
}

$sql = "SELECT c.id, c.fullname, c.shortname, c.category
    FROM {course} c
    $whereclauses
    ORDER BY c.fullname ASC";

$courses = $DB->get_records_sql($sql, $params, $offset, $perpage);

echo html_writer::start_tag('form', ['method'=>'get']);
echo "Search: <input type='text' name='search' value='".s($search)."' /> ";
echo " Category ID: <input type='number' name='category' value='".s($category)."' /> ";
echo " Min completion %: <input type='number' name='mincompletion' value='".s($mincompletion)."' /> ";
echo " Last access days: <input type='number' name='lastaccess_days' value='".s($lastaccess_days)."' /> ";
echo " <input type='submit' value='Filter' class='btn'/>";
echo html_writer::end_tag('form');

echo html_writer::start_tag('table', ['class'=>'generaltable stickytable']);
echo html_writer::start_tag('thead');
echo html_writer::start_tag('tr');
foreach (['Course Name','Category','Enrolled Students','Assignments','Quizzes','Completed','Last Access','Completion %'] as $h) {
    echo html_writer::tag('th', $h);
}
echo html_writer::end_tag('tr');
echo html_writer::end_tag('thead');
echo html_writer::start_tag('tbody');

foreach ($courses as $course) {
    $catname = $DB->get_field('course_categories', 'name', ['id'=>$course->category]);
    $enrolled = $DB->count_records_sql("SELECT COUNT(DISTINCT ue.userid) FROM {user_enrolments} ue JOIN {enrol} e ON ue.enrolid = e.id WHERE e.courseid = ? AND ue.status = 0", [$course->id]);
    $assignments = $DB->count_records('assign', ['course'=>$course->id]);
    $quizzes = $DB->count_records('quiz', ['course'=>$course->id]);
    $completed = $DB->count_records('course_completions', ['course'=>$course->id]);
    $completionpct = $enrolled ? round(($completed/$enrolled)*100, 1) : 0;
    $lastaccess = $DB->get_field_sql("SELECT MAX(ul.timeaccess) FROM {user_lastaccess} ul WHERE ul.courseid = ?", [$course->id]);
    $lastaccessstr = $lastaccess ? userdate($lastaccess) : 'n/a';

    if ($mincompletion && $completionpct < $mincompletion) {
        continue;
    }
    if ($lastaccess_days) {
        if (!$lastaccess || $lastaccess < (time() - ($lastaccess_days * 86400))) {
            // keep it - this filter means last access older than days
        } else {
            continue;
        }
    }

    echo html_writer::start_tag('tr');
    echo html_writer::tag('td', format_string($course->fullname));
    echo html_writer::tag('td', format_string($catname));
    echo html_writer::tag('td', $enrolled);
    echo html_writer::tag('td', $assignments);
    echo html_writer::tag('td', $quizzes);
    echo html_writer::tag('td', $completed);
    echo html_writer::tag('td', $lastaccessstr);
    echo html_writer::tag('td', $completionpct . '%');
    echo html_writer::end_tag('tr');
}

echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');

// pagination
echo $OUTPUT->paging_bar($total, $page, $perpage, new moodle_url('/local/courseanalytics/report.php', array('search'=>$search,'category'=>$category,'mincompletion'=>$mincompletion,'lastaccess_days'=>$lastaccess_days)));

echo $OUTPUT->footer();
