<?php
// Demo insights page for the seeded course data.

require(__DIR__ . '/config.php');
require_once($CFG->libdir . '/tablelib.php');

global $DB, $OUTPUT, $PAGE;

$url = new moodle_url('/demo_insights.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('report');
$PAGE->set_title('Demo course insights');
$PAGE->set_heading('Demo course insights');

require_login();
require_capability('moodle/site:viewreports', context_system::instance());

$category = $DB->get_record('course_categories', ['idnumber' => 'seed_cs_category']);

function demo_insights_grade(?float $grade): string {
    if ($grade === null) {
        return '-';
    }
    return format_float($grade, 1);
}

function demo_insights_status(float $grade): string {
    if ($grade >= 82) {
        return 'Strong';
    }
    if ($grade >= 74) {
        return 'Watch';
    }
    return 'Needs review';
}

function demo_insights_fullname(stdClass $user): string {
    return s(trim($user->firstname . ' ' . $user->lastname));
}

function demo_insights_table(array $headers, array $rows, string $empty): string {
    global $OUTPUT;

    if (empty($rows)) {
        return $OUTPUT->notification($empty, 'info', false);
    }

    $table = new html_table();
    $table->head = $headers;
    $table->data = $rows;
    $table->attributes['class'] = 'generaltable table-sm demo-insights-table';
    return html_writer::table($table);
}

echo $OUTPUT->header();
echo html_writer::tag('style', '
.demo-insights {
    color: #1f2937;
}
.demo-insights .demo-insights-intro {
    background: #f8fafc;
    border: 1px solid #d9e2ec;
    border-radius: 6px;
    color: #334155;
    margin: 0 0 1.25rem;
    padding: 1rem;
}
.demo-insights h3 {
    color: #111827;
    margin-top: 1.75rem;
}
.demo-insights .demo-insights-table {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-collapse: collapse;
    color: #1f2937;
    margin-bottom: 1.5rem;
    width: 100%;
}
.demo-insights .demo-insights-table th {
    background: #e2e8f0;
    border: 1px solid #cbd5e1;
    color: #0f172a;
    font-weight: 700;
}
.demo-insights .demo-insights-table td {
    background: #fff;
    border: 1px solid #d9e2ec;
    color: #1f2937;
}
.demo-insights .demo-insights-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}
');
echo html_writer::start_div('demo-insights');

if (!$category) {
    echo $OUTPUT->notification('No seeded demo data was found. Run seed_demo_data.php first.', 'warning');
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

$assessmentlike = $DB->sql_like('gi.idnumber', ':assessmentpattern', false);
$studentlike = $DB->sql_like('u.idnumber', ':studentpattern', false);
$instructorlike = $DB->sql_like('u.idnumber', ':instructorpattern', false);

$courseparams = [
    'categoryid' => $category->id,
    'assessmentpattern' => '%_assessment_%',
    'studentpattern' => 'DEMO-S-%',
];

$courseoverview = $DB->get_records_sql("
    SELECT c.id,
           c.fullname,
           c.shortname,
           COUNT(DISTINCT gg.userid) AS studentcount,
           COUNT(gg.id) AS gradecount,
           AVG(gg.finalgrade) AS averagegrade,
           MIN(gg.finalgrade) AS lowestgrade,
           MAX(gg.finalgrade) AS highestgrade
      FROM {course} c
      JOIN {grade_items} gi ON gi.courseid = c.id
      JOIN {grade_grades} gg ON gg.itemid = gi.id
      JOIN {user} u ON u.id = gg.userid
     WHERE c.category = :categoryid
       AND gi.itemtype = 'manual'
       AND $assessmentlike
       AND $studentlike
       AND gg.finalgrade IS NOT NULL
  GROUP BY c.id, c.fullname, c.shortname
  ORDER BY averagegrade DESC", $courseparams);

$courseids = array_keys($courseoverview);

$instructorparams = [
    'categoryid' => $category->id,
    'contextlevel' => CONTEXT_COURSE,
    'assessmentpattern' => '%_assessment_%',
    'instructorpattern' => 'DEMO-I-%',
];

$instructors = $DB->get_records_sql("
    SELECT u.id,
           u.firstname,
           u.lastname,
           COUNT(DISTINCT c.id) AS coursecount,
           COUNT(gg.id) AS gradecount,
           AVG(gg.finalgrade) AS averagegrade
      FROM {course} c
      JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel
      JOIN {role_assignments} ra ON ra.contextid = ctx.id
      JOIN {role} r ON r.id = ra.roleid
      JOIN {user} u ON u.id = ra.userid
      JOIN {grade_items} gi ON gi.courseid = c.id
      JOIN {grade_grades} gg ON gg.itemid = gi.id
     WHERE c.category = :categoryid
       AND r.shortname IN ('editingteacher', 'teacher')
       AND gi.itemtype = 'manual'
       AND $assessmentlike
       AND $instructorlike
       AND gg.finalgrade IS NOT NULL
  GROUP BY u.id, u.firstname, u.lastname
  ORDER BY averagegrade DESC", $instructorparams);

$weakassessments = $DB->get_records_sql("
    SELECT gi.id,
           c.fullname AS coursename,
           gi.itemname,
           COUNT(gg.id) AS gradecount,
           AVG(gg.finalgrade) AS averagegrade
      FROM {course} c
      JOIN {grade_items} gi ON gi.courseid = c.id
      JOIN {grade_grades} gg ON gg.itemid = gi.id
      JOIN {user} u ON u.id = gg.userid
     WHERE c.category = :categoryid
       AND gi.itemtype = 'manual'
       AND $assessmentlike
       AND $studentlike
       AND gg.finalgrade IS NOT NULL
  GROUP BY gi.id, c.fullname, gi.itemname
  ORDER BY averagegrade ASC", $courseparams, 0, 8);

$studentcourseaverages = $DB->get_records_sql("
    SELECT MIN(gg.id) AS id,
           c.fullname AS coursename,
           u.firstname,
           u.lastname,
           COUNT(gg.id) AS gradecount,
           AVG(gg.finalgrade) AS averagegrade,
           MIN(gg.finalgrade) AS lowestgrade
      FROM {course} c
      JOIN {grade_items} gi ON gi.courseid = c.id
      JOIN {grade_grades} gg ON gg.itemid = gi.id
      JOIN {user} u ON u.id = gg.userid
     WHERE c.category = :categoryid
       AND gi.itemtype = 'manual'
       AND $assessmentlike
       AND $studentlike
       AND gg.finalgrade IS NOT NULL
  GROUP BY c.id, c.fullname, u.id, u.firstname, u.lastname
    HAVING AVG(gg.finalgrade) < 70
  ORDER BY averagegrade ASC", $courseparams, 0, 12);

$courseheaders = ['Course', 'Students', 'Grade entries', 'Average', 'Range', 'Signal'];
$courserows = [];
foreach ($courseoverview as $course) {
    $average = (float)$course->averagegrade;
    $courserows[] = [
        format_string($course->fullname),
        (int)$course->studentcount,
        (int)$course->gradecount,
        demo_insights_grade($average),
        demo_insights_grade((float)$course->lowestgrade) . ' - ' . demo_insights_grade((float)$course->highestgrade),
        demo_insights_status($average),
    ];
}

$instructorheaders = ['Instructor', 'Courses', 'Grades reviewed', 'Student average', 'Signal'];
$instructorrows = [];
foreach ($instructors as $instructor) {
    $average = (float)$instructor->averagegrade;
    $instructorrows[] = [
        demo_insights_fullname($instructor),
        (int)$instructor->coursecount,
        (int)$instructor->gradecount,
        demo_insights_grade($average),
        demo_insights_status($average),
    ];
}

$assessmentheaders = ['Course', 'Assessment / focus area', 'Average', 'Grade entries', 'Recommendation'];
$assessmentrows = [];
foreach ($weakassessments as $assessment) {
    $average = (float)$assessment->averagegrade;
    $assessmentrows[] = [
        format_string($assessment->coursename),
        format_string($assessment->itemname),
        demo_insights_grade($average),
        (int)$assessment->gradecount,
        $average < 70 ? 'Prioritize support now' : 'Add practice and review',
    ];
}

$studentheaders = ['Student', 'Course', 'Average', 'Lowest grade', 'Action'];
$studentrows = [];
foreach ($studentcourseaverages as $studentcourse) {
    $studentrows[] = [
        demo_insights_fullname($studentcourse),
        format_string($studentcourse->coursename),
        demo_insights_grade((float)$studentcourse->averagegrade),
        demo_insights_grade((float)$studentcourse->lowestgrade),
        'Review the weakest assessment first',
    ];
}

echo html_writer::tag('p',
    'These signals use only the deterministic demo grades seeded by seed_demo_data.php. Treat instructor flags as prompts for review, not final judgments.',
    ['class' => 'demo-insights-intro']
);

echo $OUTPUT->heading('Course performance', 3);
echo demo_insights_table($courseheaders, $courserows, 'No course grades are available yet.');

echo $OUTPUT->heading('Instructor signals', 3);
echo demo_insights_table($instructorheaders, $instructorrows, 'No instructor grade signals are available yet.');

echo $OUTPUT->heading('What students should focus on', 3);
echo demo_insights_table($assessmentheaders, $assessmentrows, 'No weak assessment areas were found.');

echo $OUTPUT->heading('Students needing support', 3);
echo demo_insights_table($studentheaders, $studentrows, 'No student/course averages are below 70.');

if (empty($courseids)) {
    echo $OUTPUT->notification('The demo category exists, but no graded seeded courses were found. Re-run seed_demo_data.php.', 'warning');
}

echo html_writer::end_div();
echo $OUTPUT->footer();
