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
 * This page prints a particular instance of realtimequiz
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once("../../config.php");
global $CFG, $DB, $PAGE, $OUTPUT, $USER;
require_once($CFG->dirroot.'/mod/realtimequiz/lib.php');
require_once($CFG->dirroot.'/mod/realtimequiz/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or ...
$q = optional_param('q', 0, PARAM_INT);  // Realtimequiz ID.

if ($id) {
    $cm = get_coursemodule_from_id('realtimequiz', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $realtimequiz = $DB->get_record('realtimequiz', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    $realtimequiz = $DB->get_record('realtimequiz', ['id' => $q], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('realtimequiz', $realtimequiz->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $id = $cm->id;
}

$PAGE->set_url(new moodle_url('/mod/realtimequiz/view.php', ['id' => $cm->id]));

require_login($course->id, false, $cm);
$PAGE->set_pagelayout('incourse');

if ($CFG->version < 2011120100) {
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    $context = context_module::instance($cm->id);
}

$questioncount = $DB->count_records('realtimequiz_question', ['quizid' => $realtimequiz->id]);
if ($questioncount == 0 && has_capability('mod/realtimequiz:editquestions', $context)) {
    redirect('edit.php?id='.$id);
}

require_capability('mod/realtimequiz:attempt', $context);

if ($CFG->version > 2014051200) { // Moodle 2.7+.
    $params = [
        'context' => $context,
        'objectid' => $realtimequiz->id,
    ];
    $event = \mod_realtimequiz\event\course_module_viewed::create($params);
    $event->add_record_snapshot('realtimequiz', $realtimequiz);
    $event->trigger();
} else {
    add_to_log($course->id, 'realtimequiz', 'view all', "index.php?id=$course->id", "");
}

$quizstatus = realtimequiz_update_status($realtimequiz->id, $realtimequiz->status);

// Print the page header.

$strrealtimequizzes = get_string("modulenameplural", "realtimequiz");
$strrealtimequiz = get_string("modulename", "realtimequiz");

$PAGE->set_title(strip_tags($course->shortname.': '.$strrealtimequiz.': '.format_string($realtimequiz->name, true)));
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if ($CFG->branch < 400) {
    echo $OUTPUT->heading(format_string($realtimequiz->name));

    if (class_exists('\core_completion\activity_custom_completion')) {
        // Render the activity information.
        $modinfo = get_fast_modinfo($course);
        $cminfo = $modinfo->get_cm($cm->id);
        $completiondetails = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id);
        $activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id);
        echo $OUTPUT->activity_information($cminfo, $completiondetails, $activitydates);
    }

    realtimequiz_view_tabs('view', $cm->id, $context);

    echo format_text($realtimequiz->intro, $realtimequiz->introformat);
}

// Print the main part of the page.

if ($CFG->version < 2013111800) {
    $tickimg = $OUTPUT->pix_url('i/tick_green_big');
    $crossimg = $OUTPUT->pix_url('i/cross_red_big');
    $spacer = $OUTPUT->pix_url('spacer');
} else if ($CFG->branch < 33) {
    $tickimg = $OUTPUT->pix_url('i/grade_correct');
    $crossimg = $OUTPUT->pix_url('i/grade_incorrect');
    $spacer = $OUTPUT->pix_url('spacer');
} else {
    $tickimg = $OUTPUT->image_url('i/grade_correct');
    $crossimg = $OUTPUT->image_url('i/grade_incorrect');
    $spacer = $OUTPUT->image_url('spacer');
}

echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter realtimequizbox');
?>
    <div id="questionarea"></div>
    <!--    <div id="debugarea" style="border: 1px dashed black; width: 600px; height: 100px; overflow: scroll; "></div>
        <button onclick="realtimequiz_debug_stopall();">Stop</button> -->
    <script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/realtimequiz/view_student.js"></script>
    <script type="text/javascript">
        realtimequiz_set_maxanswers(10);
        realtimequiz_set_quizid(<?php echo $realtimequiz->id; ?>);
        realtimequiz_set_userid(<?php echo $USER->id; ?>);
        realtimequiz_set_sesskey('<?php echo sesskey(); ?>');
        realtimequiz_set_coursepage('<?php echo "$CFG->wwwroot/course/view.php?id=$course->id"; ?>');
        realtimequiz_set_siteroot('<?php echo "$CFG->wwwroot"; ?>');
        realtimequiz_set_running(<?php echo(realtimequiz_is_running($quizstatus) ? 'true' : 'false'); ?>);

        realtimequiz_set_image('tick', "<?php echo $tickimg ?>");
        realtimequiz_set_image('cross', "<?php echo $crossimg ?>");
        realtimequiz_set_image('blank', "<?php echo $spacer ?>");

        //Pass all the text strings into the javascript (to allow for translation)
        // Used by view_student.js
        realtimequiz_set_text('joinquiz', "<?php echo addslashes(get_string('joinquiz', 'realtimequiz')); ?>");
        realtimequiz_set_text('joininstruct', "<?php echo addslashes(get_string('joininstruct', 'realtimequiz')); ?>");
        realtimequiz_set_text('waitstudent', "<?php echo addslashes(get_string('waitstudent', 'realtimequiz')); ?>");
        realtimequiz_set_text('clicknext', "<?php echo addslashes(get_string('clicknext', 'realtimequiz')); ?>");
        realtimequiz_set_text('waitfirst', "<?php echo addslashes(get_string('waitfirst', 'realtimequiz')); ?>");
        realtimequiz_set_text('question', "<?php echo addslashes(get_string('question', 'realtimequiz')); ?>");
        realtimequiz_set_text('invalidanswer', "<?php echo addslashes(get_string('invalidanswer',
                                                                                 'realtimequiz')); ?>");
        realtimequiz_set_text('finalresults', "<?php echo addslashes(get_string('finalresults', 'realtimequiz')); ?>");
        realtimequiz_set_text('quizfinished', "<?php echo addslashes(get_string('quizfinished', 'realtimequiz')); ?>");
        realtimequiz_set_text('classresult', "<?php echo addslashes(get_string('classresult', 'realtimequiz')); ?>");
        realtimequiz_set_text('classresultcorrect', "<?php echo addslashes(get_string('classresultcorrect',
                                                                                      'realtimequiz')); ?>");
        realtimequiz_set_text('questionfinished', "<?php echo addslashes(get_string('questionfinished',
                                                                                    'realtimequiz')); ?>");
        realtimequiz_set_text('httprequestfail', "<?php echo addslashes(get_string('httprequestfail',
                                                                                   'realtimequiz')); ?>");
        realtimequiz_set_text('noquestion', "<?php echo addslashes(get_string('noquestion', 'realtimequiz')); ?>");
        realtimequiz_set_text('tryagain', "<?php echo addslashes(get_string('tryagain', 'realtimequiz')); ?>");
        realtimequiz_set_text('resultthisquestion', "<?php echo addslashes(get_string('resultthisquestion',
                                                                                      'realtimequiz')); ?>");
        realtimequiz_set_text('resultoverall', "<?php echo addslashes(get_string('resultoverall',
                                                                                 'realtimequiz')); ?>");
        realtimequiz_set_text('resultcorrect', "<?php echo addslashes(get_string('resultcorrect',
                                                                                 'realtimequiz')); ?>");
        realtimequiz_set_text('answersent', "<?php echo addslashes(get_string('answersent', 'realtimequiz')); ?>");
        realtimequiz_set_text('quiznotrunning', "<?php echo addslashes(get_string('quiznotrunning',
                                                                                  'realtimequiz')); ?>");
        realtimequiz_set_text('servererror', "<?php echo addslashes(get_string('servererror', 'realtimequiz')); ?>");
        realtimequiz_set_text('badresponse', "<?php echo addslashes(get_string('badresponse', 'realtimequiz')); ?>");
        realtimequiz_set_text('httperror', "<?php echo addslashes(get_string('httperror', 'realtimequiz')); ?>");
        realtimequiz_set_text('yourresult', "<?php echo addslashes(get_string('yourresult', 'realtimequiz')); ?>");

        realtimequiz_set_text('timeleft', "<?php echo addslashes(get_string('timeleft', 'realtimequiz')); ?>");
        realtimequiz_set_text('displaynext', "<?php echo addslashes(get_string('displaynext', 'realtimequiz')); ?>");
        realtimequiz_set_text('sendinganswer', "<?php echo addslashes(get_string('sendinganswer',
                                                                                 'realtimequiz')); ?>");
        realtimequiz_set_text('tick', "<?php echo addslashes(get_string('tick', 'realtimequiz')); ?>");
        realtimequiz_set_text('cross', "<?php echo addslashes(get_string('cross', 'realtimequiz')); ?>");

        // Used by view_teacher.js
        realtimequiz_set_text('joinquizasstudent', "<?php echo addslashes(get_string('joinquizasstudent',
                                                                                     'realtimequiz')); ?>");
        realtimequiz_set_text('next', "<?php echo addslashes(get_string('next', 'realtimequiz')); ?>");
        realtimequiz_set_text('startquiz', "<?php echo addslashes(get_string('startquiz', 'realtimequiz')); ?>");
        realtimequiz_set_text('startnewquiz', "<?php echo addslashes(get_string('startnewquiz', 'realtimequiz')); ?>");
        realtimequiz_set_text('startnewquizconfirm', "<?php echo addslashes(get_string('startnewquizconfirm',
                                                                                       'realtimequiz')); ?>");
        realtimequiz_set_text('studentconnected', "<?php echo addslashes(get_string('studentconnected',
                                                                                    'realtimequiz')); ?>");
        realtimequiz_set_text('studentsconnected', "<?php echo addslashes(get_string('studentsconnected',
                                                                                     'realtimequiz')); ?>");
        realtimequiz_set_text('teacherstartinstruct', "<?php echo addslashes(get_string('teacherstartinstruct',
                                                                                        'realtimequiz')); ?>");
        realtimequiz_set_text('teacherstartnewinstruct', "<?php echo addslashes(get_string('teacherstartnewinstruct',
                                                                                           'realtimequiz')); ?>");
        realtimequiz_set_text('teacherjoinquizinstruct', "<?php echo addslashes(get_string('teacherjoinquizinstruct',
                                                                                           'realtimequiz')); ?>");
        realtimequiz_set_text('reconnectquiz', "<?php echo addslashes(get_string('reconnectquiz',
                                                                                 'realtimequiz')); ?>");
        realtimequiz_set_text('reconnectinstruct', "<?php echo addslashes(get_string('reconnectinstruct',
                                                                                     'realtimequiz')); ?>");
    </script>

<?php

if (has_capability('mod/realtimequiz:control', $context)) {
    ?>
    <script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/mod/realtimequiz/view_teacher.js"></script>
    <script type="text/javascript">
        realtimequiz_init_teacher_view();
    </script>
    <?php
} else {
    echo '<script type="text/javascript">realtimequiz_init_student_view();</script>';
}

echo $OUTPUT->box_end();

// Finish the page.
echo $OUTPUT->footer();

