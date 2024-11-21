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

 use local_intelliboard\repositories\modules_repository;

require('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/tables.php');

$courseid = optional_param('id', 0, PARAM_INT);
$mod = optional_param('mod', 0, PARAM_INT);
$modulep = optional_param('module', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$search = clean_raw(optional_param('search', '', PARAM_TEXT));
$pagesize = optional_param('pagesize', 10, PARAM_INT);
$download = optional_param('download', 0, PARAM_ALPHA);

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
$page_url = new moodle_url(
    "/local/intelliboard/instructor/courses.php", [
        "search"=>$search, "action"=>$action, "id"=>$courseid, "userid"=>$userid,
        "cmid"=>$cmid, "mod"=>$mod, "sesskey"=> sesskey(), 'pagesize' => $pagesize
    ]
);

$PAGE->set_url($page_url);
$PAGE->set_pagetype('courses');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->js('/local/intelliboard/assets/js/jquery.circlechart.js');
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');

if($action === 'learner'){
	$table = new intelliboard_learner_grades_table('table', $userid, $courseid, $search, $mod, $modulep);
	$data = intelliboard_learner_data($userid, $courseid);
	$user = $DB->get_record('user', array('id'=>$userid));
    $export_file_name = get_string('instructor_courses_table_name_learner', 'local_intelliboard', $data);
}elseif($action === 'activity'){
	$table = new intelliboard_activity_grades_table('table', $cmid, $courseid, $search);
	$data = intelliboard_activity_data($cmid, $courseid);
    $export_file_name = get_string('instructor_courses_table_name_activity', 'local_intelliboard', $data);
}elseif($action === 'learners'){
	$table = new intelliboard_learners_grades_table('table', $courseid, $search, $download);
	$course = intelliboard_course_learners_total($courseid);
    $export_file_name = get_string('instructor_courses_table_name_learners', 'local_intelliboard', $course);
}elseif($action == 'activities'){
	$table = new intelliboard_activities_grades_table('table', $courseid, $search, $mod, $modulep, $download);
	$course = intelliboard_activities_data($courseid);
    $export_file_name = get_string('instructor_courses_table_name_activities', 'local_intelliboard', $course);
}else{
	$table = new intelliboard_courses_grades_table('table', $search, $download);
	$export_file_name = get_string('instructor_courses_table_name', 'local_intelliboard');
}

if (in_array($action, ['learner', 'activities'])) {
    $modules = modules_repository::getAllModules();
    $modules = array_map(function($module) {
        return ['id' => $module->id, 'name' => get_string('modulename', $module->name)];
    }, $modules);
    $modules = array_merge(
        [['id' => 0, 'name' => get_string('all_modules', 'local_intelliboard')]],
        $modules
    );
}
if($table->is_downloading($download, $export_file_name, '', $action, PHP_INT_MAX)){
    $table->out(PHP_INT_MAX, true);
    exit;
}

$exporturls = (object) [];
$page_url->param('download', 'excel');
$exporturls->xls = $page_url->out();
$page_url->param('download', 'pdf');
$exporturls->pdf = $page_url->out();
$page_url->param('download', 'csv');
$exporturls->csv = $page_url->out();

$table->show_download_buttons_at(array());

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
								<h3><?php echo fullname($user); ?></h3>
								<p><?php echo get_string('course'); ?>: <strong><?php echo format_string($data->course); ?></strong></p>
							</div>
							<ul class="totals">
								<li><?php echo ($scale_real>0)?$data->grade:(int)$data->grade; ?><span><?php echo get_string('course_grade', 'local_intelliboard'); ?></span></li>
								<li><?php echo (int)$data->progress; ?><span><?php echo get_string('completed_activities_resourses', 'local_intelliboard'); ?></span></li>
							</ul>

							<ul class="summary">
								<li>
                                    <span><?php echo get_string('status', 'local_intelliboard');?> </span>
                                    <?php echo ($data->timecompleted) ? get_string('completed_on', 'local_intelliboard', intelli_date($data->timecompleted)) : get_string('incomplete', 'local_intelliboard'); ?>
                                </li>
                                <li>
                                    <span><?php echo get_string('enrolled', 'local_intelliboard'); ?> </span>
                                    <?php echo intelli_date($data->enrolled); ?>
                                </li>
								<li>
                                    <span><?php echo get_string('in16', 'local_intelliboard'); ?> </span>
                                    <?php echo ($data->timeaccess)?intelli_date($data->timeaccess):'-'; ?>
                                </li>
								<li>
                                    <span><?php echo get_string('in17', 'local_intelliboard'); ?> </span>
                                    <?php echo seconds_to_time($data->timespend); ?>
                                </li>
								<li>
                                    <span><?php echo get_string('in18', 'local_intelliboard'); ?> </span>
                                    <?php echo (int)$data->visits; ?>
                                </li>


								<a href="<?php echo $CFG->wwwroot.'/local/intelliboard/instructor/courses.php?search&action=learners&id='.$id; ?>" class="btn btn-default btn-back"><i class="ion-android-arrow-back"></i> <?php echo get_string('in20', 'local_intelliboard'); ?></a>
							</ul>
						<?php elseif($action === 'activity'): ?>
							<div class="activity"><?php echo substr($data->module, 0,1); ?></div>
							<div class="details">
								<h3><?php echo $data->name ?></h3>
								<p><?php echo get_string('course'); ?>: <strong><?php echo $data->course ?></strong></p>
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

								<a href="<?php echo $CFG->wwwroot.'/local/intelliboard/instructor/courses.php?search&action=activities&id='.$data->courseid; ?>" class="btn btn-default btn-back"><i class="ion-android-arrow-back"></i> <?php echo get_string('in201', 'local_intelliboard'); ?></a>
							</ul>
						<?php elseif($action === 'learners' && $course): ?>
							<div class="grade" title="<?php echo get_string('in21', 'local_intelliboard'); ?>">
                                <div class="circle-progress-course"  data-percent="<?php echo ($scale_real>0)?$course->grade:(int)$course->grade; ?>"></div>
							</div>
							<div class="details">
							<h3><?php echo $course->fullname ?> <span class="" title='<?php echo get_string('completion','local_intelliboard'); ?>: <?php echo ($course->enablecompletion)?get_string('in22','local_intelliboard'):get_string('disabled','local_intelliboard') ?>'><i class='<?php echo ($course->enablecompletion)?'ion-android-checkbox-outline':'ion-android-checkbox-outline-blank' ?>'></i></span></h3>

							<span class="intelliboard-tooltip" title='<?php echo get_string('course_category','local_intelliboard'); ?>'><i class='ion-folder'></i> <?php echo $course->category; ?> </span>

							<?php if($course->startdate): ?>
							<span class="intelliboard-tooltip" title='<?php echo get_string('course_started','local_intelliboard'); ?>'><i class='ion-ios-calendar-outline'></i> <?php echo date("m/d/Y", $course->startdate); ?> </span>
							<?php endif; ?>
							<span class="intelliboard-tooltip" title='<?php echo get_string('total_time_spent_enrolled_learners','local_intelliboard'); ?>'><i class='ion-ios-clock-outline'></i> <?php echo seconds_to_time($course->timespend); ?> </span>
							<span class="intelliboard-tooltip" title='<?php echo get_string('total_visits_enrolled_learners','local_intelliboard'); ?>'><i class='ion-log-in'></i> <?php echo (int)$course->visits; ?></span>
							</div>
                            <ul class="totals">
                                <li><?php echo isset($course->learners) ? (int)$course->learners : 0; ?> <span><?php echo get_string('learners_enrolled','local_intelliboard'); ?></span></li>
                                <li><?php echo isset($course->learners_completed) ? (int)$course->learners_completed : 0; ?><span><?php echo get_string('in6','local_intelliboard'); ?></span></li>
                                <li>
                                    <?php echo (isset($course->learners_completed) && $course->learners) ? (intval((intval($course->learners_completed) / intval($course->learners))*100)) : 0; ?>%
                                    <span>
                                        <?php echo get_string('learning_progress','local_intelliboard'); ?>
                                    </span>
                                </li>
                            </ul>
						<?php elseif($action === 'activities'): ?>
							<div class="grade" title="<?php echo get_string('in21','local_intelliboard'); ?>">
									<div class="circle-progress-course"  data-percent="<?php echo ($scale_real>0)?$course->grade:(int)$course->grade; ?>"></div>
							</div>
							<div class="details">
								<h3><?php echo $course->fullname ?> <span class="" title='<?php echo get_string('completion','local_intelliboard'); ?>: <?php echo ($course->enablecompletion)?get_string('in22','local_intelliboard'):get_string('disabled','local_intelliboard') ?>'><i class='<?php echo ($course->enablecompletion)?'ion-android-checkbox-outline':'ion-android-checkbox-outline-blank' ?>'></i></span></h3>
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
						<select name="mod" class="pull-left form-control" onchange="this.form.submit()">
							<option value="0"><?php echo get_string('allmod', 'local_intelliboard');?></option>
							<option value="1" <?php echo ($mod)?'selected="selected"':''; ?>><?php echo get_string('customod', 'local_intelliboard');?></option>
						</select>
					<?php endif; ?>

                    <?php if (in_array($action, ['learner', 'activities'])): ?>
                        <select class="pull-left form-control" name="module" autocomplete="off">
                            <?php foreach($modules as $module):?>
                                <option value="<?php echo $module['id']; ?>" <?php echo $module['id'] == $modulep ? 'selected="selected"' : '';?>>
                                    <?php echo ucfirst($module['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>

					<input name="userid" type="hidden" value="<?php echo $userid; ?>" />
					<input name="cmid" type="hidden" value="<?php echo $cmid; ?>" />
					<input name="id" type="hidden" value="<?php echo $courseid; ?>" />
					<input name="action" type="hidden" value="<?php echo $action; ?>" />
					<input name="pagesize" type="hidden" value="<?php echo $pagesize; ?>" />

					<span class="pull-left">
					<input class="form-control" name="search" type="text" value="<?php echo $search; ?>" placeholder="<?php echo get_string('type_here','local_intelliboard'); ?>" />
					</span>
					<button class="btn btn-default"><?php echo get_string('search'); ?></button>
					<?php if(in_array($action, array('learners', 'activities'))): ?>
					<a href="<?php echo $CFG->wwwroot.'/local/intelliboard/instructor/courses.php'; ?>" class="btn btn-default">
					<i class="ion-android-arrow-back"></i> <?php echo get_string('return_to_courses','local_intelliboard'); ?></a>
					<?php endif; ?>
				</form>
                <div class="report-export-panel">
                    <?php echo $OUTPUT->render_from_template('local_intelliboard/instructor_export_buttons', ["items" => $exporturls, "totara_version" => isset($CFG->totara_version) ? $CFG->totara_version : null]); ?>
                </div>
			</div>
			<div class="clear"></div>
			<div class="progress-table">
				<?php $table->columns ? $table->out(($action == 'learners' || $action == 'activities') ? $pagesize : 10, true) : ''; ?>
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
