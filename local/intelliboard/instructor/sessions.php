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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

require('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/tables.php');

$sessionid = optional_param('id', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$mod = optional_param('mod', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$search = clean_raw(optional_param('search', '', PARAM_TEXT));

require_login();
intelliboard_instructor_access();

if ($search) {
	require_sesskey();
}

$params = array(
	'do'=>'instructor',
	'mode'=> 2
);
$intelliboard = intelliboard($params);
$factorInfo = chart_options();
$scale_real = get_config('local_intelliboard', 'scale_real');

$PAGE->set_url(new moodle_url("/local/intelliboard/instructor/sessions.php",
			array("search"=>$search, "action"=>$action, "id"=>$sessionid, "courseid"=>$courseid, "userid"=>$userid, "cmid"=>$cmid, "mod"=>$mod, "sesskey"=> sesskey())));
$PAGE->set_pagetype('sessions');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->js('/local/intelliboard/assets/js/jquery.circlechart.js');
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');

if($action === 'learner'){
	$table = new intelliboard_learner_grades_table('intelliboard_learner__table', $userid, $courseid, $search, $mod);
	$data = intelliboard_learner_data($userid, $courseid);
	$user = $DB->get_record('user', array('id'=>$userid));
}elseif($action === 'activity'){
	$table = new intelliboard_sessions_activity_grades_table('intelliboard_activity_table', $cmid, $sessionid, $courseid, $search);
	$data = intelliboard_course_session_activity_data($cmid, $sessionid, $courseid);
}elseif($action === 'learners'){
	$table = new intelliboard_sessions_learners_grades_table('intelliboard_sessions_learners_table', $sessionid, $courseid, $search);
	$course = intelliboard_course_session_learners_total($sessionid, $courseid);
	if (!$course) {
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $course->grade = 0;
    }
}elseif($action == 'activities'){
	$table = new intelliboard_sessions_activities_grades_table('intelliboard_sessions_activities_table', $sessionid, $courseid, $search, $mod);
	$course = intelliboard_course_session_activities_data($sessionid, $courseid);
}else{
	$table = new intelliboard_sessions_grades_table('intelliboard_sessions_table', $search);
}
$table->show_download_buttons_at(array());
$table->is_downloading('', '', '');
$session = $DB->get_record('local_intellicart_sessions', ['id' => $sessionid]);

echo $OUTPUT->header();
?>
<?php if(!isset($intelliboard) || !$intelliboard->token): ?>
	<div class="alert alert-error alert-block" role="alert"><?php echo get_string('intelliboardaccess', 'local_intelliboard'); ?></div>
<?php else: ?>
<div class="intelliboard-page intelliboard-instructor">
	<?php include("views/menu.php"); ?>
		<div class="grades-table">
				<?php if(!empty($action)): ?>
					<div class="intelliboard-course-header clearfix">
						<?php if($action === 'learner'): ?>
							<div class="avatar">
								<?php echo $OUTPUT->user_picture($user, array('size'=>80)); ?>
							</div>
							<div class="details">
								<h3><?php echo format_string($data->learner); ?></h3>
								<p><?php echo get_string('course'); ?>: <strong><?php echo format_string($data->course); ?></strong></p>
                                <?php  if ($session) : ?>
                                    <p><?php echo get_string('session_name', 'local_intelliboard');?>: <strong><?php echo $session->name;?></strong></p>
                                    <p>
                                        <?php
                                        if (userdate($session->timestart, '%Y/%m/%d') != userdate($session->timeend, '%Y/%m/%d')) {
                                            $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                    userdate($session->timeend, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                        } else {
                                            $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                    userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                        }
                                        ?>
                                        <?php echo get_string('session_time', 'local_intelliboard');?>: <strong><?php echo $session_time; ?></strong>
                                    </p>
                                <?php  endif; ?>
							</div>
							<ul class="totals">
								<li><?php echo ($scale_real>0)?$data->grade:(int)$data->grade; ?><span><?php echo get_string('course_grade', 'local_intelliboard'); ?></span></li>
								<li><?php echo (int)$data->progress; ?><span><?php echo get_string('completed_activities_resourses', 'local_intelliboard'); ?></span></li>
							</ul>

							<ul class="summary">
								<li><span><?php echo get_string('status', 'local_intelliboard');?></span><?php echo ($data->timecompleted) ? get_string('completed_on', 'local_intelliboard', date('m/d/Y', $data->timecompleted)) : get_string('incomplete', 'local_intelliboard'); ?></li>


									<li><span><?php echo get_string('enrolled', 'local_intelliboard'); ?> </span><?php echo date('m/d/Y', $data->enrolled); ?></li>
									<li><span><?php echo get_string('in16', 'local_intelliboard'); ?> </span><?php echo ($data->timeaccess)?date('m/d/Y', $data->timeaccess):'-'; ?></li>
									<li><span><?php echo get_string('in17', 'local_intelliboard'); ?> </span><?php echo seconds_to_time($data->timespend); ?></li>
									<li><span><?php echo get_string('in18', 'local_intelliboard'); ?> </span><?php echo (int)$data->visits; ?></li>


								<a href="<?php echo $CFG->wwwroot.'/local/intelliboard/instructor/sessions.php?search&action=learners&courseid='.$courseid.'&id='.$sessionid; ?>" class="btn btn-default btn-back"><i class="ion-android-arrow-back"></i> <?php echo get_string('in20', 'local_intelliboard'); ?></a>
							</ul>
						<?php elseif($action === 'activity'): ?>
							<div class="activity"><?php echo substr($data->module, 0,1); ?></div>
							<div class="details">
								<h3><?php echo $data->name ?></h3>
								<p><?php echo get_string('course'); ?>: <strong><?php echo $data->course ?></strong></p>
                                <?php  if ($session) : ?>
                                    <p><?php echo get_string('session_name', 'local_intelliboard');?>: <strong><?php echo $session->name;?></strong></p>
                                    <p>
                                        <?php
                                        if (userdate($session->timestart, '%Y/%m/%d') != userdate($session->timeend, '%Y/%m/%d')) {
                                            $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                    userdate($session->timeend, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                        } else {
                                            $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                    userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                        }
                                        ?>
                                        <?php echo get_string('session_time', 'local_intelliboard');?>: <strong><?php echo $session_time; ?></strong>
                                    </p>
                                <?php  endif; ?>
							</div>
							<ul class="totals">
								<li><?php echo ($scale_real>0)?$data->grade:(int)$data->grade; ?><span><?php echo get_string('in19', 'local_intelliboard'); ?></span></li>
								<li><?php echo (int)$data->completed; ?><span><?php echo get_string('completed', 'local_intelliboard'); ?></span></li>
							</ul>

							<ul class="summary">
								<li><span><?php echo get_string('section', 'local_intelliboard'); ?> </span><?php echo (int)$data->section; ?></li>
								<li><span><?php echo get_string('type', 'local_intelliboard'); ?> </span><?php echo $data->module; ?></li>
								<li><span><?php echo get_string('in17', 'local_intelliboard'); ?> </span><?php echo seconds_to_time($data->timespend); ?></li>
								<li><span><?php echo get_string('in18', 'local_intelliboard'); ?> </span><?php echo $data->visits; ?></li>

								<a href="<?php echo $CFG->wwwroot.'/local/intelliboard/instructor/sessions.php?search&action=activities&courseid='.$data->courseid.'&id='.$sessionid; ?>" class="btn btn-default btn-back"><i class="ion-android-arrow-back"></i> <?php echo get_string('in201', 'local_intelliboard'); ?></a>
							</ul>
						<?php elseif($action === 'learners' and !empty($course)): ?>
							<div class="grade" title="<?php echo get_string('in21', 'local_intelliboard'); ?>">
                                <div class="circle-progress-course"  data-percent="<?php echo ($scale_real>0)?$course->grade:(int)$course->grade; ?>"></div>
							</div>
							<div class="details">
							<h3><?php echo $course->fullname ?> <span class="" title='<?php echo get_string('completion','local_intelliboard'); ?>: <?php echo ($course->enablecompletion)?get_string('in22','local_intelliboard'):get_string('disabled','local_intelliboard') ?>'><i class='<?php echo ($course->enablecompletion)?'ion-android-checkbox-outline':'ion-android-checkbox-outline-blank' ?>'></i></span></h3>
                            <?php  if ($session) : ?>
                                <p><?php echo get_string('session_name', 'local_intelliboard');?>: <strong><?php echo $session->name;?></strong></p>
                                <p>
                                    <?php
                                    if (userdate($session->timestart, '%Y/%m/%d') != userdate($session->timeend, '%Y/%m/%d')) {
                                        $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                userdate($session->timeend, get_string('strftimedate', 'langconfig')) . ', ' .
                                                userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                    } else {
                                        $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                    }
                                    ?>
                                    <?php echo get_string('session_time', 'local_intelliboard');?>: <strong><?php echo $session_time; ?></strong>
                                </p>
                            <?php  endif; ?>

                            <?php if (isset($course->timespend)) : ?>
                                <span class="intelliboard-tooltip" title='<?php echo get_string('course_category','local_intelliboard'); ?>'><i class='ion-folder'></i> <?php echo $course->category; ?> </span>

                                <?php if($course->startdate): ?>
                                <span class="intelliboard-tooltip" title='<?php echo get_string('course_started','local_intelliboard'); ?>'><i class='ion-ios-calendar-outline'></i> <?php echo date("m/d/Y", $course->startdate); ?> </span>
                                <?php endif; ?>
                                <span class="intelliboard-tooltip" title='<?php echo get_string('total_time_spent_enrolled_learners','local_intelliboard'); ?>'><i class='ion-ios-clock-outline'></i> <?php echo seconds_to_time($course->timespend); ?> </span>
                                <span class="intelliboard-tooltip" title='<?php echo get_string('total_visits_enrolled_learners','local_intelliboard'); ?>'><i class='ion-log-in'></i> <?php echo (int)$course->visits; ?></span>
                            <?php endif; ?>
							</div>
                            <?php if (isset($course->learners)) : ?>
                                <ul class="totals">
                                    <li><?php echo (int)$course->learners; ?> <span><?php echo get_string('learners_enrolled','local_intelliboard'); ?></span></li>
                                    <li><?php echo (int)$course->learners_completed; ?><span><?php echo get_string('in6','local_intelliboard'); ?></span></li>
                                    <li><?php echo intval((intval($course->learners_completed) / intval($course->learners))*100); ?>%<span><?php echo get_string('learning_progress','local_intelliboard'); ?></span></li>
                                </ul>
                            <?php endif; ?>
						<?php elseif($action === 'activities'): ?>
							<div class="grade" title="<?php echo get_string('in21','local_intelliboard'); ?>">
									<div class="circle-progress-course"  data-percent="<?php echo ($scale_real>0)?$course->grade:(int)$course->grade; ?>"></div>
							</div>
							<div class="details">
								<h3><?php echo $course->fullname ?> <span class="" title='<?php echo get_string('completion','local_intelliboard'); ?>: <?php echo ($course->enablecompletion)?get_string('in22','local_intelliboard'):get_string('disabled','local_intelliboard') ?>'><i class='<?php echo ($course->enablecompletion)?'ion-android-checkbox-outline':'ion-android-checkbox-outline-blank' ?>'></i></span></h3>
                                <?php  if ($session) : ?>
                                    <p><?php echo get_string('session_name', 'local_intelliboard');?>: <strong><?php echo $session->name;?></strong></p>
                                    <p>
                                        <?php
                                        if (userdate($session->timestart, '%Y/%m/%d') != userdate($session->timeend, '%Y/%m/%d')) {
                                            $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                    userdate($session->timeend, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                        } else {
                                            $session_time = userdate($session->timestart, get_string('strftimedate', 'langconfig')) . ', ' .
                                                    userdate($session->timestart, get_string('strftimetime', 'langconfig')) . ' - ' .
                                                    userdate($session->timeend, get_string('strftimetime', 'langconfig'));
                                        }
                                        ?>
                                        <?php echo get_string('session_time', 'local_intelliboard');?>: <strong><?php echo $session_time; ?></strong>
                                    </p>
                                <?php  endif; ?>
								<span class="intelliboard-tooltip" title='<?php echo get_string('course_category','local_intelliboard'); ?>'><i class='ion-folder'></i> <?php echo $course->category; ?> </span>
								<?php if($course->startdate): ?>
								<span class="intelliboard-tooltip" title='<?php echo get_string('course_started','local_intelliboard'); ?>'><i class='ion-ios-calendar-outline'></i> <?php echo date("m/d/Y", $course->startdate); ?></span>
								<?php endif; ?>
								<span class="intelliboard-tooltip" title='<?php echo get_string('total_time_spent_enrolled_learners','local_intelliboard'); ?>'><i class='ion-ios-clock-outline'></i> <?php echo seconds_to_time($course->timespend); ?></span>
								<span class="intelliboard-tooltip" title='<?php echo get_string('total_visits_enrolled_learners','local_intelliboard'); ?>'><i class='ion-log-in'></i> <?php echo (int)$course->visits; ?></span>
							</div>
							<ul class="totals">
								<li><?php echo (int)$course->sections; ?><span><?php echo get_string('sections','local_intelliboard'); ?></span></li>
								<li><?php echo (int)$course->modules; ?><span><?php echo get_string('total_activities_resources','local_intelliboard'); ?></span></li>
								<li><?php echo (int)$course->completed; ?><span><?php echo get_string('completions','local_intelliboard'); ?></span></li>
							</ul>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			<div class="intelliboard-search clearfix">
				<form action="<?php echo $PAGE->url; ?>" method="GET">
					<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

					<?php if ($action == 'activities' or $action == 'learner'): ?>
						<select name="mod" class="pull-left form-control" onchange="this.form.submit()" style="margin-right:3px;">
							<option value="0"><?php echo get_string('allmod', 'local_intelliboard');?></option>
							<option value="1" <?php echo ($mod)?'selected="selected"':''; ?>><?php echo get_string('customod', 'local_intelliboard');?></option>
						</select>
					<?php endif; ?>

					<input name="userid" type="hidden" value="<?php echo $userid; ?>" />
					<input name="cmid" type="hidden" value="<?php echo $cmid; ?>" />
					<input name="id" type="hidden" value="<?php echo $sessionid; ?>" />
					<input name="courseid" type="hidden" value="<?php echo $courseid; ?>" />
					<input name="action" type="hidden" value="<?php echo $action; ?>" />

					<span class="pull-left">
					<input class="form-control" name="search" type="text" value="<?php echo $search; ?>" placeholder="<?php echo get_string('type_here','local_intelliboard'); ?>" />
					</span>
					<button class="btn btn-default"><?php echo get_string('search'); ?></button>
					<?php if(in_array($action, array('learners', 'activities'))): ?>
					<a href="<?php echo $CFG->wwwroot.'/local/intelliboard/instructor/sessions.php'; ?>" class="btn btn-default">
					<i class="ion-android-arrow-back"></i> <?php echo get_string('return_to_sessions','local_intelliboard'); ?></a>
					<?php endif; ?>
				</form>
			</div>
			<div class="clear"></div>
			<div class="progress-table">
				<?php $table->out(10, true); ?>
			</div>
		</div>
	<?php include("../views/footer.php"); ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.circle-progress').percentcircle(<?php echo $factorInfo->GradesXCalculation; ?>);
		jQuery('.circle-progress-course').percentcircle(<?php echo $factorInfo->GradesZCalculation; ?>);
	});
</script>
<?php endif; ?>
<?php echo $OUTPUT->footer();
