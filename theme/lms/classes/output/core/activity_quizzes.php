<?php
defined('MOODLE_INTERNAL') || die();

function render_user_active_quizzes($userid)
{
    $quizzes = get_active_quizzes_for_user($userid);

    if (empty($quizzes)) {
        return html_writer::div(get_string('no_active_quizzes'), 'no-active-quizzes');
    }

    $output = html_writer::start_tag('ul', ['class' => 'user-active-quizzes-list']);

    foreach ($quizzes as $quiz) {
        $quizurl = new moodle_url('/mod/quiz/view.php', ['id' => $quiz->quizid]);
        $output .= html_writer::tag('li',
            html_writer::link($quizurl, $quiz->quizname) .
            ' (' . userdate($quiz->timeopen) . ' - ' .
            ($quiz->timeclose ? userdate($quiz->timeclose) : get_string('no_end_time')) . ')'
        );
    }

    $output .= html_writer::end_tag('ul');

    return $output;
}

function get_active_quizzes_for_user($userid)
{
    global $DB;

    $now = time();

    $sql = "SELECT q.id AS quizid, q.name AS quizname, c.id AS courseid, c.fullname AS coursename, q.timeopen, q.timeclose
                FROM {quiz} q
                JOIN {course} c ON q.course = c.id
                JOIN {course_modules} cm ON cm.instance = q.id
                JOIN {modules} m ON m.id = cm.module
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE m.name = 'quiz'
                  AND cm.deletioninprogress = 0
                  AND ue.userid = :userid
                  AND (q.timeopen <= :timeopen OR q.timeopen = 0)
                  AND (q.timeclose >= :timeclose OR q.timeclose = 0)";

    return $DB->get_records_sql($sql, ['userid' => $userid, 'timeopen' => $now, 'timeclose' => $now]);
}

function get_user_active_quizzes_data($userid)
{
    $quizzes = get_active_quizzes_for_user($userid);

    $data = [];
    foreach ($quizzes as $quiz) {
        $data[] = [
            'quizurl' => (new moodle_url('/mod/quiz/view.php', ['id' => $quiz->quizid]))->out(),
            'quizname' => $quiz->quizname,
            'timeopen' => userdate($quiz->timeopen),
            'timeclose' => $quiz->timeclose ? userdate($quiz->timeclose) : get_string('no_end_time'),
        ];
    }
    return $data;
}


