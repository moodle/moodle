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
 * Student Activity Dashboard Main Page.
 *
 * Displays enrolled/completed courses, pending assignments, upcoming quizzes,
 * login count, last login, and average completion %. Course clicks open detailed
 * statistics in a modal with warning alerts for courses below 50% completion.
 *
 * @package    local_student_dashboard
 * @copyright  2026 Antigravity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_login();

$userid = $USER->id;
$context = context_user::instance($userid);

// Page settings
$PAGE->set_url(new moodle_url('/local/student_dashboard/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_student_dashboard'));
$PAGE->set_heading(get_string('pluginname', 'local_student_dashboard'));
$PAGE->set_pagelayout('mydashboard');

// Gather KPIs
// 1. Enrolled Courses
$enrolledcourses_sql = "
    SELECT DISTINCT c.id, c.fullname, cc.name AS categoryname, c.category AS categoryid
    FROM {course} c
    JOIN {course_categories} cc ON c.category = cc.id
    JOIN {enrol} e ON e.courseid = c.id
    JOIN {user_enrolments} ue ON ue.enrolid = e.id
    WHERE ue.userid = :userid AND c.id != :siteid
";
$courses = $DB->get_records_sql($enrolledcourses_sql, array('userid' => $userid, 'siteid' => SITEID));
$enrolled_count = count($courses);

// 2. Completed Courses (marked completed in course_completions)
$completed_sql = "
    SELECT COUNT(DISTINCT course)
    FROM {course_completions}
    WHERE userid = :userid AND timecompleted IS NOT NULL AND timecompleted > 0
";
$completed_count = $DB->count_records_sql($completed_sql, array('userid' => $userid));

// 3. Pending Assignments
$pending_assign_sql = "
    SELECT COUNT(a.id)
    FROM {assign} a
    JOIN {enrol} e ON e.courseid = a.course
    JOIN {user_enrolments} ue ON ue.enrolid = e.id
    LEFT JOIN {assign_submission} asub ON asub.assignment = a.id AND asub.userid = :userid
    WHERE ue.userid = :userid2 AND (asub.status IS NULL OR asub.status != 'submitted')
";
$pending_assignments = $DB->count_records_sql($pending_assign_sql, array('userid' => $userid, 'userid2' => $userid));

// 4. Upcoming Quizzes (close date is within next 7 days and user has not finished attempt)
$now = time();
$upcoming_limit = $now + (7 * 24 * 3600);
$upcoming_quiz_sql = "
    SELECT COUNT(q.id)
    FROM {quiz} q
    JOIN {enrol} e ON e.courseid = q.course
    JOIN {user_enrolments} ue ON ue.enrolid = e.id
    LEFT JOIN {quiz_attempts} qa ON qa.quiz = q.id AND qa.userid = :userid AND qa.state = 'finished'
    WHERE ue.userid = :userid2 AND q.timeclose > :now AND q.timeclose <= :limit AND qa.id IS NULL
";
$upcoming_quizzes = $DB->count_records_sql($upcoming_quiz_sql, array(
    'userid' => $userid,
    'userid2' => $userid,
    'now' => $now,
    'limit' => $upcoming_limit
));

// 5. Login Count (from logstore standard log)
$login_count_sql = "
    SELECT COUNT(id)
    FROM {logstore_standard_log}
    WHERE userid = :userid AND eventname = :eventname
";
$login_count = $DB->count_records_sql($login_count_sql, array(
    'userid' => $userid,
    'eventname' => '\core\event\user_loggedin'
));

// 6. Last Login
$last_login_time = $USER->lastlogin;
$last_login_str = $last_login_time ? userdate($last_login_time, get_string('strftimedatetime', 'langconfig')) : get_string('never', 'local_student_dashboard');

// Fetch course completion percentages & last access
$course_details = [];
$total_completion = 0.0;

foreach ($courses as $c) {
    // Check total completion-enabled modules in course
    $total_modules = $DB->count_records_select('course_modules', "course = :courseid AND completion > 0", array('courseid' => $c->id));
    
    // Check user's completed modules in course
    $completed_modules = 0;
    if ($total_modules > 0) {
        $completed_sql = "
            SELECT COUNT(cmc.id)
            FROM {course_modules_completion} cmc
            JOIN {course_modules} cm ON cmc.coursemoduleid = cm.id
            WHERE cm.course = :courseid AND cmc.userid = :userid AND cmc.completionstate = 1
        ";
        $completed_modules = $DB->count_records_sql($completed_sql, array('courseid' => $c->id, 'userid' => $userid));
    }
    
    $completion_pct = ($total_modules > 0) ? ($completed_modules * 100.0 / $total_modules) : 0.0;
    $total_completion += $completion_pct;
    
    // Last accessed
    $last_access_record = $DB->get_record('user_lastaccess', array('userid' => $userid, 'courseid' => $c->id), 'timeaccess');
    $last_access = $last_access_record ? $last_access_record->timeaccess : 0;
    $last_access_str = $last_access ? userdate($last_access, get_string('strftimedate', 'langconfig')) : get_string('never', 'local_student_dashboard');
    
    // Grade (simplified check in grade_grades / grade_items)
    $grade_sql = "
        SELECT gg.finalgrade, gi.grademax
        FROM {grade_grades} gg
        JOIN {grade_items} gi ON gg.itemid = gi.id
        WHERE gi.courseid = :courseid AND gg.userid = :userid AND gi.itemtype = 'course'
    ";
    $grade_rec = $DB->get_record_sql($grade_sql, array('courseid' => $c->id, 'userid' => $userid));
    $grade_str = $grade_rec && !is_null($grade_rec->finalgrade) ? number_format($grade_rec->finalgrade, 1) . '/' . number_format($grade_rec->grademax, 0) : 'N/A';

    // Pending counts in this specific course
    $course_pending_assign = $DB->count_records_sql("
        SELECT COUNT(a.id) FROM {assign} a
        LEFT JOIN {assign_submission} asub ON asub.assignment = a.id AND asub.userid = :userid
        WHERE a.course = :courseid AND (asub.status IS NULL OR asub.status != 'submitted')
    ", array('userid' => $userid, 'courseid' => $c->id));

    $course_upcoming_quiz = $DB->count_records_sql("
        SELECT COUNT(q.id) FROM {quiz} q
        LEFT JOIN {quiz_attempts} qa ON qa.quiz = q.id AND qa.userid = :userid AND qa.state = 'finished'
        WHERE q.course = :courseid AND q.timeclose > :now AND qa.id IS NULL
    ", array('userid' => $userid, 'courseid' => $c->id, 'now' => $now));

    $course_details[$c->id] = (object)[
        'id' => $c->id,
        'fullname' => $c->fullname,
        'category' => $c->categoryname,
        'completion' => $completion_pct,
        'lastaccess' => $last_access_str,
        'grade' => $grade_str,
        'pending_assignments' => $course_pending_assign,
        'upcoming_quizzes' => $course_upcoming_quiz,
        'total_activities' => $total_modules,
        'completed_activities' => $completed_modules
    ];
}

$avg_completion = $enrolled_count > 0 ? ($total_completion / $enrolled_count) : 0.0;

// Render HTML
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('dashboard', 'local_student_dashboard'));

// Include inline style overrides for premium clean dashboard aesthetic
echo '
<style>
.dashboard-kpi-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.dashboard-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(0,0,0,0.1);
}
.course-card {
    cursor: pointer;
    transition: transform 0.2s, border-color 0.2s;
    border-radius: 12px;
}
.course-card:hover {
    transform: translateY(-4px);
    border-color: #0f6cbf;
}
.warning-badge {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 0.8; }
    50% { opacity: 1; }
    100% { opacity: 0.8; }
}
</style>
';

// Render KPI Cards
echo html_writer::start_div('row mb-4');

$kpis = [
    ['title' => get_string('enrolledcourses', 'local_student_dashboard'), 'value' => $enrolled_count, 'color' => 'primary', 'icon' => 'fa-book'],
    ['title' => get_string('completedcourses', 'local_student_dashboard'), 'value' => $completed_count, 'color' => 'success', 'icon' => 'fa-graduation-cap'],
    ['title' => get_string('pendingassignments', 'local_student_dashboard'), 'value' => $pending_assignments, 'color' => 'warning', 'icon' => 'fa-clock'],
    ['title' => get_string('upcomingquizzes', 'local_student_dashboard'), 'value' => $upcoming_quizzes, 'color' => 'danger', 'icon' => 'fa-question-circle'],
    ['title' => get_string('logincount', 'local_student_dashboard'), 'value' => $login_count, 'color' => 'info', 'icon' => 'fa-sign-in-alt'],
    ['title' => get_string('avgcompletion', 'local_student_dashboard'), 'value' => number_format($avg_completion, 1) . '%', 'color' => 'secondary', 'icon' => 'fa-percent']
];

foreach ($kpis as $kpi) {
    echo html_writer::start_div('col-md-4 col-lg-2 mb-3');
    echo html_writer::start_div('card dashboard-kpi-card bg-light');
    echo html_writer::start_div('card-body text-center p-3');
    echo html_writer::span('<i class="fa ' . $kpi['icon'] . ' fa-2x text-' . $kpi['color'] . ' mb-2"></i>');
    echo html_writer::tag('h6', $kpi['title'], ['class' => 'text-muted mb-1 small']);
    echo html_writer::tag('h4', $kpi['value'], ['class' => 'm-0 font-weight-bold']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
}
echo html_writer::end_div();

// Last Login Info Banner
echo html_writer::start_div('alert alert-info d-flex justify-content-between align-items-center mb-4');
echo html_writer::span('<i class="fa fa-info-circle mr-2"></i> ' . get_string('clickfordetails', 'local_student_dashboard'));
echo html_writer::span('<strong>' . get_string('lastlogin', 'local_student_dashboard') . ':</strong> ' . $last_login_str, ['class' => 'small']);
echo html_writer::end_div();

// Enrolled Courses Grid
echo html_writer::tag('h4', get_string('enrolledcourses', 'local_student_dashboard'), ['class' => 'mb-3']);
echo html_writer::start_div('row');

if (empty($course_details)) {
    echo html_writer::div($OUTPUT->notification(get_string('nocourses', 'report_course_analytics'), 'info'), 'col-12');
} else {
    foreach ($course_details as $cd) {
        $warning_html = '';
        $card_border = '';
        if ($cd->completion < 50.0) {
            $warning_html = ' <span class="badge badge-danger warning-badge float-right"><i class="fa fa-exclamation-triangle"></i> &lt; 50%</span>';
            $card_border = ' border-danger';
        }
        
        echo html_writer::start_div('col-md-6 col-lg-4 mb-4');
        echo html_writer::start_div('card course-card h-100 shadow-sm' . $card_border, [
            'onclick' => 'showCourseDetails(' . json_encode($cd) . ')',
            'data-id' => $cd->id
        ]);
        echo html_writer::start_div('card-body');
        echo html_writer::tag('h5', format_string($cd->fullname) . $warning_html, ['class' => 'card-title font-weight-bold']);
        echo html_writer::tag('h6', $cd->category, ['class' => 'card-subtitle mb-3 text-muted small']);
        
        // Progress display
        echo html_writer::start_div('progress mb-2', ['style' => 'height: 8px;']);
        $progress_color = ($cd->completion < 50.0) ? 'bg-danger' : (($cd->completion > 80.0) ? 'bg-success' : 'bg-primary');
        echo html_writer::empty_tag('div', [
            'class' => 'progress-bar ' . $progress_color,
            'role' => 'progressbar',
            'style' => 'width: ' . $cd->completion . '%',
            'aria-valuenow' => $cd->completion,
            'aria-valuemin' => '0',
            'aria-valuemax' => '100'
        ]);
        echo html_writer::end_div();
        
        echo html_writer::start_div('d-flex justify-content-between small text-muted');
        echo html_writer::span(number_format($cd->completion, 1) . '% ' . get_string('completion', 'local_student_dashboard'));
        echo html_writer::span(get_string('lastaccess', 'local_student_dashboard') . ': ' . $cd->lastaccess);
        echo html_writer::end_div();
        
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }
}
echo html_writer::end_div();

// Custom JS Drawer Modal for detailed stats
echo '
<div class="modal fade" id="courseDetailModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title font-weight-bold" id="modalTitle">Course Name</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <!-- Warnings alert -->
        <div id="belowFiftyWarning" class="alert alert-danger d-none">
          <i class="fa fa-exclamation-triangle mr-2"></i>
          <span>' . get_string('belowfiftywarning', 'local_student_dashboard') . '</span>
        </div>
        
        <h6 class="text-muted text-uppercase small font-weight-bold mb-3">' . get_string('coursestatistics', 'local_student_dashboard') . '</h6>
        
        <div class="row text-center mb-4">
          <div class="col-6 mb-3">
            <div class="p-3 bg-light rounded" style="border-radius: 10px;">
              <h4 id="statGrade" class="font-weight-bold text-primary m-0">N/A</h4>
              <small class="text-muted">' . get_string('gradetest', 'local_student_dashboard') . '</small>
            </div>
          </div>
          <div class="col-6 mb-3">
            <div class="p-3 bg-light rounded" style="border-radius: 10px;">
              <h4 id="statCompleted" class="font-weight-bold text-success m-0">0/0</h4>
              <small class="text-muted">' . get_string('completedactivities', 'local_student_dashboard') . '</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 bg-light rounded" style="border-radius: 10px;">
              <h4 id="statPendingAssign" class="font-weight-bold text-warning m-0">0</h4>
              <small class="text-muted">' . get_string('pendingassignments', 'local_student_dashboard') . '</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 bg-light rounded" style="border-radius: 10px;">
              <h4 id="statUpcomingQuiz" class="font-weight-bold text-danger m-0">0</h4>
              <small class="text-muted">' . get_string('upcomingquizzes', 'local_student_dashboard') . '</small>
            </div>
          </div>
        </div>
        
        <div class="mb-3">
          <label class="font-weight-bold small text-muted text-uppercase mb-2">' . get_string('overallprogress', 'local_student_dashboard') . '</label>
          <div class="progress" style="height: 12px; border-radius: 6px;">
            <div id="statProgressBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div class="d-flex justify-content-between mt-2 font-weight-bold text-primary">
            <span id="statProgressPct">0.0%</span>
            <span class="text-muted small">' . get_string('lastaccess', 'local_student_dashboard') . ': <span id="statLastAccess" class="text-dark">Never</span></span>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" onclick="closeModal()" style="border-radius: 8px;">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function showCourseDetails(course) {
    document.getElementById("modalTitle").innerText = course.fullname;
    document.getElementById("statGrade").innerText = course.grade;
    document.getElementById("statCompleted").innerText = course.completed_activities + " / " + course.total_activities;
    document.getElementById("statPendingAssign").innerText = course.pending_assignments;
    document.getElementById("statUpcomingQuiz").innerText = course.upcoming_quizzes;
    document.getElementById("statLastAccess").innerText = course.lastaccess;
    
    // Progress bar
    var progressBar = document.getElementById("statProgressBar");
    progressBar.style.width = course.completion + "%";
    progressBar.setAttribute("aria-valuenow", course.completion);
    document.getElementById("statProgressPct").innerText = parseFloat(course.completion).toFixed(1) + "%";
    
    // Change progress bar color based on value
    progressBar.className = "progress-bar";
    if (course.completion < 50.0) {
        progressBar.classList.add("bg-danger");
        document.getElementById("belowFiftyWarning").classList.remove("d-none");
    } else {
        if (course.completion > 80.0) {
            progressBar.classList.add("bg-success");
        } else {
            progressBar.classList.add("bg-primary");
        }
        document.getElementById("belowFiftyWarning").classList.add("d-none");
    }
    
    // Show Modal
    $("#courseDetailModal").modal("show");
}

function closeModal() {
    $("#courseDetailModal").modal("hide");
}
</script>
';

echo $OUTPUT->footer();
