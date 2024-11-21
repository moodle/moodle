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
 * @website    http://intelliboard.net/
 */

require('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/student/lib.php');
require_once($CFG->dirroot .'/local/intelliboard/student/tables.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$mod = optional_param('mod', 0, PARAM_INT);
$search = clean_raw(optional_param('search', '', PARAM_TEXT));
$other_user = optional_param('user', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

require_login();
require_capability('local/intelliboard:students', context_system::instance());

if ($search) {
	require_sesskey();
}

if(!get_config('local_intelliboard', 't1') or !get_config('local_intelliboard', 't4')){
	throw new moodle_exception('invalidaccess', 'error');
}

$showing_user = $USER;
if(get_config('local_intelliboard', 't09')>0 && $other_user>0 && intelliboard_instructor_have_access($USER->id)){
    $showing_user = core_user::get_user($other_user, '*', MUST_EXIST);
}
$email = get_config('local_intelliboard', 'te1');
$params = array(
	'do'=>'learner',
	'mode'=> 1
);
$intelliboard = intelliboard($params);
$factorInfo = chart_options();

$PAGE->set_url(new moodle_url("/local/intelliboard/student/grades.php", array("search"=>s($search), "id"=>$id, "mod"=>$mod, "sesskey"=> sesskey(), "user"=>$other_user)));
$PAGE->set_pagetype('grades');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->js('/local/intelliboard/assets/js/jquery.circlechart.js');
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');


$totals = intelliboard_learner_totals($showing_user->id);
if($id){
	$table = new intelliboard_activities_grades_table('table', $showing_user->id, $id, s($search), $mod);
	$courseObj = intelliboard_learner_course($showing_user->id, $id);
}else{
	$table = new intelliboard_courses_grades_table('table', $showing_user->id, s($search));
}

$alt_name = get_config('local_intelliboard', 'grades_alt_text');
$def_name = get_string('grades', 'local_intelliboard');
$grade_name = ($alt_name) ? $alt_name : $def_name;

$table->show_download_buttons_at(array(TABLE_P_BOTTOM));
$table->is_downloadable(true);
$table->is_downloading($download, $grade_name, $grade_name);

if ($download) {
	$table->out(10, true);
	exit;
}

$scale_real = get_config('local_intelliboard', 'scale_real');

echo $OUTPUT->header();
?>
<?php if(!isset($intelliboard) || !$intelliboard->token): ?>
	<div class="alert alert-error alert-block" role="alert"><?php echo get_string('intelliboardaccess', 'local_intelliboard'); ?></div>
<?php else: ?>
<div class="intelliboard-page intelliboard-student">
	<?php include("views/menu.php"); ?>
		<div class="intelliboard-overflow grades-table">
			<?php if(isset($courseObj)): ?>
				<div class="intelliboard-course-header clearfix">
					<div class="grade">
						<div class="circle-progress-course"  data-percent="<?php echo ($scale_real)?$courseObj->grade:(int)$courseObj->grade; ?>"></div>
					</div>
					<div class="details">
						<h3><?php echo format_string($courseObj->fullname); ?></h3>
						<p>
							<?php if($courseObj->enablecompletion and get_config('local_intelliboard', 't41')): ?>
								<?php echo ($courseObj->timecompleted) ? " <i class='green-color ion-android-done'></i> ". get_string('completed_on', 'local_intelliboard', date('m/d/Y', $courseObj->timecompleted)): " <i class='orange-color ion-android-radio-button-on'></i> ".get_string('incomplete', 'local_intelliboard'); ?>
							<?php endif; ?>
							<?php if(get_config('local_intelliboard', 't42')): ?>
							&nbsp; &nbsp; &nbsp;

							<?php echo ($courseObj->timeaccess) ? " <i class='ion-android-person'></i> ".get_string('last_access_on_course', 'local_intelliboard', userdate($courseObj->timeaccess, '%B %d, %Y %I:%M %P')) : "" ?>
							<?php endif; ?>
						</p>
					</div>
					<a href="<?php echo $CFG->wwwroot.'/local/intelliboard/student/grades.php'; ?>" class="btn">
					<i class="ion-android-arrow-back"></i> <?php echo get_string('return_to_grades', 'local_intelliboard');?></a>
				</div>
			<?php endif; ?>
			<div class="intelliboard-search clearfix">
				<form action="<?php echo $PAGE->url; ?>" method="GET">
					<?php if ($id): ?>
						<select name="mod" class="pull-left form-control" onchange="this.form.submit()" style="margin-right:3px;">
							<option value="0"><?php echo get_string('allmod', 'local_intelliboard');?></option>
							<option value="1" <?php echo ($mod)?'selected="selected"':''; ?>><?php echo get_string('customod', 'local_intelliboard');?></option>
						</select>
					<?php endif; ?>
					<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
					<input name="id" type="hidden" value="<?php echo $id; ?>" />
					<span class="pull-left">
                        <input class="form-control" name="search" type="text"
                               value="<?php echo format_string($search); ?>"
                               placeholder="<?php echo get_string('type_here', 'local_intelliboard');?>"
                               aria-label="<?php echo get_string('search');?>"
                        >
                    </span>
					<button class="btn btn-default"><?php echo get_string('search');?></button>
				</form>
			</div>
			<div class="clear"></div>

			<?php $table->out(10, true); ?>
		</div>
	<?php include("../views/footer.php"); ?>
</div>
<script type="text/javascript">
    function decodeJson(htmlstring) {
        var taEl = document.createElement("textarea");
        taEl.innerHTML = htmlstring;
        return JSON.parse(taEl.value);
    }
	jQuery(document).ready(function(){
		jQuery('.circle-progress').percentcircle(decodeJson('<?php echo format_string($factorInfo->GradesXCalculation); ?>'));
		jQuery('.circle-progress-course').percentcircle(decodeJson('<?php echo format_string($factorInfo->GradesZCalculation); ?>'));
	});
</script>
<?php endif; ?>
<?php echo $OUTPUT->footer();
