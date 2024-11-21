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
require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');

require_login();
intelliboard_learner_access();

if(!get_config('local_intelliboard', 't1')){
    throw new moodle_exception('invalidaccess', 'error');
}elseif(!get_config('local_intelliboard', 't2')){
    if(get_config('local_intelliboard', 't3')){
        redirect("$CFG->wwwroot/local/intelliboard/student/courses.php");
    }if(get_config('local_intelliboard', 't4')){
        redirect("$CFG->wwwroot/local/intelliboard/student/grades.php");
    }
    throw new moodle_exception('invalidaccess', 'error');
}
$email = get_config('local_intelliboard', 'te1');
$params = array(
    'do'=>'learner',
    'mode'=> 1
);
$intelliboard = intelliboard($params);
$factorInfo = chart_options();

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$search = clean_raw(optional_param('search', '', PARAM_RAW));
$type = optional_param('type', '', PARAM_ALPHANUMEXT);
$time = optional_param('time', 0, PARAM_INT);
$other_user = optional_param('user', 0, PARAM_INT);

$activity_setting = optional_param('activity_setting', 0, PARAM_INT);
$activity_courses = optional_param('activity_courses', 0, PARAM_INT);
$activity_time = optional_param('activity_time', 0, PARAM_INT);

$debug = get_config('local_intelliboard', 'debug');
$debugmode = optional_param('debug', '', PARAM_RAW);

$showing_user = $USER;
if(get_config('local_intelliboard', 't09')>0 && $other_user>0 && intelliboard_instructor_have_access($USER->id)){
    $showing_user = core_user::get_user($other_user, '*', MUST_EXIST);
}

if($activity_setting){
    $showing_user->activity_courses = $activity_courses;
    $showing_user->activity_time = $activity_time;
}else{
    $showing_user->activity_courses = (isset($showing_user->activity_courses))?$showing_user->activity_courses:0;
    $showing_user->activity_time = (isset($showing_user->activity_time))?$showing_user->activity_time:-1;
}

if ($search or $activity_setting) {
    require_sesskey();
}

$PAGE->set_url(new moodle_url("/local/intelliboard/student/index.php", array("type"=>s($type), "search"=>s($search), "sesskey"=> sesskey(), "user"=>$other_user)));
$PAGE->set_pagetype('home');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->js('/local/intelliboard/assets/js/jquery.circlechart.js');
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');

$t5 = get_config('local_intelliboard', 't5');
$t6 = get_config('local_intelliboard', 't6');
$t7 = get_config('local_intelliboard', 't7');
$t8 = get_config('local_intelliboard', 't8');
$t9 = get_config('local_intelliboard', 't9');
$t10 = get_config('local_intelliboard', 't10');
$t11 = get_config('local_intelliboard', 't11');
$t12 = get_config('local_intelliboard', 't12');
$t13 = get_config('local_intelliboard', 't13');
$t14 = get_config('local_intelliboard', 't14');
$t15 = get_config('local_intelliboard', 't15');
$t31 = get_config('local_intelliboard', 't31');
$t32 = get_config('local_intelliboard', 't32');
$t33 = get_config('local_intelliboard', 't33');
$t34 = get_config('local_intelliboard', 't34');
$t35 = get_config('local_intelliboard', 't35');
$t36 = get_config('local_intelliboard', 't36');
$t37 = get_config('local_intelliboard', 't37');
$t38 = get_config('local_intelliboard', 't38');
$t53 = get_config('local_intelliboard', 't53');
$scale_real = get_config('local_intelliboard', 'scale_real');
$scale_percentage_round = clean_param(get_config('local_intelliboard', 'scale_percentage_round'), PARAM_INT);

$courses = intelliboard_learner_courses($showing_user->id, null);
$coursesprogress = intelliboard_learner_courses($showing_user->id, $time);
$totals = intelliboard_learner_totals($showing_user->id);
$active_tab = '';

if($t12 or $t13){
    $modules_progress = intelliboard_learner_modules($showing_user->id);
}
if ($t9) {
    $assignments = intelliboard_data('assignment', $showing_user->id, $showing_user);
    if (optional_param('assignment_page', 0, PARAM_INT) > 0) {
        $active_tab = 'assignment';
    }
}
if ($t10) {
    $quizes = intelliboard_data('quiz', $showing_user->id, $showing_user);
    if (optional_param('quiz_page', 0, PARAM_INT) > 0) {
        $active_tab = 'quiz';
    }
}
if ($t11) {
    $courses_report = intelliboard_data('course', $showing_user->id, $showing_user);
    if (optional_param('course_page', 0, PARAM_INT) > 0) {
        $active_tab = 'course';
    }
}

if($t5){
    $progress = intelliboard_learner_progress($time, $showing_user->id);
    $json_data = array();
    list($timestart, $timefinish) = get_timerange($time);

    $timefinish_y = date('Y', $timefinish);
    $timefinish_m = date('m', $timefinish)-1;
    $timefinish_d = date('d', $timefinish);
    $hAxis_max = "new Date($timefinish_y, $timefinish_m, $timefinish_d)";

    $mintimepoint = $timefinish;

    foreach($progress[0] as $item){
        if ($item->timepoint < $mintimepoint) {
            $mintimepoint = $item->timepoint;
        }


        $l = '';
        $lp = 0;
        if(isset($progress[1][$item->timepoint])){
            $d = $progress[1][$item->timepoint];
            $l = $d->grade;
            $lp = $d->grade_percent;
        }
        $tooltip = "<div class=\"chart-tooltip\">";
        $tooltip .= "<div class=\"chart-tooltip-header\">".date('D, M d Y', $item->timepoint)."</div>";
        $tooltip .= "<div class=\"chart-tooltip-body clearfix\">";
        $tooltip .= ($t53)?"<div class=\"chart-tooltip-left\">":"<div class=\"chart-tooltip-left full\">";
        $tooltip .= "<span>". $item->grade.((!$scale_real)?'%':'')."</span> ".get_string('current_grade','local_intelliboard')."</div>";
        if($t53){
            $tooltip .= "<div class=\"chart-tooltip-right\"><span>" . ((!$scale_real)?round((int) $l, 2) . '%':$l) . "</span> " . get_string('average_grade', 'local_intelliboard') . "</div>";
        }
        $tooltip .= "</div>";
        $tooltip .= "</div>";

        $y = date('Y', $item->timepoint);
        $m = date('m', $item->timepoint)-1;
        $d = date('d', $item->timepoint);
        $json_data[] = ($t53)?"[new Date($y, $m, $d), ".round((($scale_real)?$item->grade_percent:$item->grade), 2).", '$tooltip', $lp, '$tooltip']":"[new Date($y, $m, $d), ".round((($scale_real)?$item->grade_percent:$item->grade), 2).", '$tooltip']";
    }

    $timestart_y = date('Y', $mintimepoint);
    $timestart_m = date('m', $mintimepoint)-1;
    $timestart_d = date('d', $mintimepoint);
    $hAxis_min = "new Date($timestart_y, $timestart_m, $timestart_d)";
}
$json_data2 = array();
foreach($courses as $item){
    $l = intval($item->duration_calc);
    $d = seconds_to_time(intval($item->duration));

    $tooltip = "<div class=\"chart-tooltip\">";
    $tooltip .= "<div class=\"chart-tooltip-header\">". addslashes(format_string($item->fullname)) ."</div>";
    $tooltip .= "<div class=\"chart-tooltip-body clearfix\">";
    $tooltip .= "<div class=\"chart-tooltip-left\">".get_string('grade','local_intelliboard').": <span>". ((!$scale_real)?round((float) $item->grade, 2):$item->grade_real)."</span></div>";
    $tooltip .= "<div class=\"chart-tooltip-right\">".get_string('time_spent','local_intelliboard').": <span>". $d."</span></div>";
    $tooltip .= "</div>";
    $tooltip .= "</div>";
    $json_data2[] = "[$l, ".round((float) $item->grade, 2).",'$tooltip']";
}

$menu = array();
if(get_config('local_intelliboard', 'all_time')){
    $menu[0] = get_string('all_time','local_intelliboard');
}
if(get_config('local_intelliboard', 'learner_tf_last_week')){
    $menu[1] = get_string('last_week','local_intelliboard');
}
if(get_config('local_intelliboard', 't01')){
    $menu[2] = get_string('last_month','local_intelliboard');
}
if(get_config('local_intelliboard', 't02')){
    $menu[3] = get_string('last_quarter','local_intelliboard');
}
if(get_config('local_intelliboard', 't03')){
    $menu[4] =  get_string('last_semester','local_intelliboard');
}
if(get_config('local_intelliboard', 'this_year')){
    $menu[5] = get_string('this_year','local_intelliboard');
}
if(get_config('local_intelliboard', 'last_year')){
    $menu[6] = get_string('last_year','local_intelliboard');
}

$PAGE->requires->js_call_amd(
    'local_intelliboard/intelliboard', 'circleProgress', [chart_options()->GradesCalculationJSON]
);

echo $OUTPUT->header();
?>

<?php if ($debug and $debugmode and isset($intelliboard->debugging)): ?>
        <pre>
            <code><?php echo $intelliboard->debugging; ?></code>
        </pre>
<?php endif; ?>

<?php if(!isset($intelliboard) || !$intelliboard->token): ?>
    <div class="alert alert-error alert-block" role="alert"><?php echo get_string('intelliboardaccess', 'local_intelliboard'); ?></div>
<?php else: ?>
    <div class="intelliboard-page intelliboard-student">
        <?php include("views/menu.php"); ?>
        <div class="intelliboard-box intelliboard-origin">
            <?php if($t5 or $t6): ?>
                <div class="intelliboard-origin-head clearfix">
                    <?php if($t5): ?>
                        <a href="#"><?php echo get_string('activity_progress', 'local_intelliboard'); ?></a>
                    <?php endif; ?>
                    <?php if($t6): ?>
                        <a href="#" class="nofilter"><?php echo get_string('course_progress', 'local_intelliboard'); ?></a>
                    <?php endif; ?>
                    <div class="intelliboard-dropdown">
                        <?php if(isset($menu[$time])): ?>
                            <button><span value="<?php echo $time; ?>"><?php echo format_string($menu[$time]); ?></span> <i class="ion-android-arrow-dropdown"></i></button>
                        <?php else:?>
                            <button><span value="-1"><?php echo get_string('select_date', 'local_intelliboard'); ?></span> <i class="ion-android-arrow-dropdown"></i></button>
                        <?php endif; ?>
                        <ul>
                            <?php foreach($menu as $key=>$value): ?>
                                <?php if($key != $time): ?>
                                    <li value="<?php echo $key; ?>"><?php echo format_string($value); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php if($t5): ?>
                    <div id="intelliboard-chart" class="intelliboard-chart-dash"></div>
                <?php endif; ?>

                <?php if($t6): ?>
                    <div id="intelliboard-chart-combo" class="intelliboard-chart-dash"></div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($t7 or $t8): ?>
                <div class="avg <?php echo (!$t7 or !$t8)?'full':''; ?>">
                    <?php if($t7): ?>
                        <p class="user"><?php echo ($scale_real)?$totals->grade:round((float) $totals->grade, 2).'%'; ?> <span><?php echo get_string('my_course_average_all', 'local_intelliboard'); ?></span></p>
                    <?php endif; ?>
                    <?php if($t8): ?>
                        <p class="site"><?php echo ($scale_real)?$totals->average:round((float) $totals->average, 2).'%'; ?> <span><?php echo get_string('overall_course_average', 'local_intelliboard'); ?></span></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if($t9 or $t10 or $t11): ?>
            <div class="intelliboard-box">
                <?php if($t9 or $t10): ?>
                    <div class="<?php echo (!$t11)?'box100':'box45'; ?> pull-right ib-table-wrapper">
                        <ul class="nav nav-tabs">
                            <?php if($t9): ?>
                                <li role="presentation" class="nav-item active"><a class="nav-link active" href="assignment"><?php echo get_string('assignments', 'local_intelliboard'); ?></a></li>
                            <?php endif; ?>
                            <?php if($t10): ?>
                                <li role="presentation" class="nav-item <?php echo (!$t9)?'active':''; ?>"><a class="nav-link" href="quiz"><?php echo get_string('quizzes', 'local_intelliboard'); ?></a></li>
                            <?php endif; ?>
                            <span>
						<form action="<?php echo $PAGE->url; ?>" method="GET" class="clearfix">
                            <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

							<input class="intype" name="type" type="hidden" value="assignment" />
                            <input class="intsearch" name="search" aria-label="<?php echo get_string('search');?>"
                                   placeholder="<?php echo get_string('search');?>" type="text"
                                   value="<?php echo ($type == 'assignment' or $type == 'quiz')?$search:''; ?>">
							<button type="submit">
                                <?php echo get_string('search');?> <i class="ion-ios-search-strong"></i>
                            </button>
							<span class="searchviewclose"><i class="ion-android-close"></i></span>
						</form>
						<span class="searchview actbtn"><i class="ion-ios-search-strong"></i></span>
						<span class="intsettings actbtn"><i class="ion-ios-settings-strong"></i></span>
					</span>
                        </ul>
                        <div>
                            <?php if($t9): ?>
                                <table class="intelliboard-data-table table tab active">
                                    <thead>
                                    <th colspan="2"><?php echo get_string('assignment_name', 'local_intelliboard'); ?></th>
                                    <?php if($t31): ?><th class="align-center"><?php echo get_string('grade', 'local_intelliboard'); ?></th><?php endif; ?>
                                    <?php if($t32): ?><th class="align-center"><?php echo get_string('due_date', 'local_intelliboard'); ?></th><?php endif; ?>
                                    </thead>
                                    <tbody>
                                    <?php if(!count($assignments['data'])): ?>
                                        <tr>
                                            <td colspan="4"><?php echo get_string('no_data', 'local_intelliboard'); ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach($assignments['data'] as $item): ?>
                                        <?php
                                        $d = $item->duedate - time();
                                        if($item->completionstate){
                                            $class = "f4 ion-android-done";
                                        }elseif($d <= 0){
                                            $class = "f5 ion-clipboard";
                                        }elseif($d <= 3600){
                                            $class = "f3 ion-clipboard";
                                        }elseif($d <= 86400){
                                            $class = "f6 ion-clipboard";
                                        }elseif($d <= 604800){
                                            $class = "f6 ion-clipboard";
                                        }else{
                                            $class = "f6 ion-clipboard";
                                        }
                                        ?>
                                        <tr>
                                            <td  width="1%"><i class="intelliboard-icon <?php echo $class; ?>"></i></td>
                                            <td >
                                                <?php if($item->availableinfo): ?>
                                                    <?php echo format_string($item->name); ?>
                                                <?php else: ?>
                                                    <a href="<?php echo $CFG->wwwroot; ?>/mod/assign/view.php?id=<?php echo s($item->cmid); ?>"><?php echo format_string($item->name); ?></a>
                                                <?php endif; ?>
                                                <p class="intelliboard-fade60"><?php echo format_string($item->fullname); ?></p>
                                            </td>
                                            <?php if($t31): ?>
                                                <td  class="align-center">
                                                    <?php if($scale_real):?>
                                                        <?php echo $item->grade; ?>
                                                    <?php else:?>
                                                        <div class="circle-progress d-inline-block"  data-percent="<?php echo round((float) $item->grade, $scale_percentage_round); ?>"></div>
                                                    <?php endif;?>
                                                </td>
                                            <?php endif; ?>
                                            <?php if($t32): ?>
                                                <td  class="align-center"><?php echo ($item->duedate)?intelli_date($item->duedate):'-'; ?></span></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td  align="right" colspan="4"><?php echo $assignments['pagination']; ?></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            <?php endif; ?>

                            <?php if($t10): ?>
                                <table class="intelliboard-data-table table tab <?php echo (!$t9)?'active':''; ?>">
                                    <thead>
                                    <th colspan="2"><?php echo get_string('quiz_name', 'local_intelliboard'); ?></th>
                                    <?php if($t33): ?><th class="align-center"><?php echo get_string('grade', 'local_intelliboard'); ?></th><?php endif; ?>
                                    <?php if($t34): ?><th class="align-center"><?php echo get_string('due_date', 'local_intelliboard'); ?></th><?php endif; ?>
                                    </thead>
                                    <tbody>
                                    <?php if(!count($quizes['data'])): ?>
                                        <tr>
                                            <td colspan="3"><?php echo get_string('no_data', 'local_intelliboard'); ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach($quizes['data'] as $item): ?>
                                        <?php
                                        $d = $item->timeclose - time();
                                        if($item->completionstate){
                                            $class = "f4 ion-ios-list-outline";
                                        }elseif($d <= 0){
                                            $class = "f5 ion-ios-list-outline";
                                        }elseif($d <= 3600){
                                            $class = "f3 ion-ios-list-outline";
                                        }elseif($d <= 86400){
                                            $class = "f6 ion-ios-list-outline";
                                        }elseif($d <= 604800){
                                            $class = "f6 ion-ios-list-outline";
                                        }else{
                                            $class = "f6 ion-ios-list-outline";
                                        }
                                        ?>
                                        <tr class="">
                                            <td width="1%"><i class="intelliboard-icon <?php echo $class; ?>"></i></td>
                                            <td>
                                                <a href="<?php echo $CFG->wwwroot; ?>/mod/quiz/view.php?id=<?php echo s($item->cmid); ?>"><?php echo format_string($item->name); ?></a>
                                                <p class="intelliboard-fade60"><?php echo format_string($item->fullname); ?></p>
                                            </td>
                                            <?php if($t33): ?>
                                                <td  class="align-center">
                                                    <?php if($scale_real):?>
                                                        <?php echo $item->grade; ?>
                                                    <?php else:?>
                                                        <div class="circle-progress"  data-percent="<?php echo round((float) $item->grade, $scale_percentage_round); ?>"></div>
                                                    <?php endif;?>
                                                </td>
                                            <?php endif; ?>
                                            <?php if($t34): ?>
                                                <td class="align-center"><?php echo ($item->timeclose)?intelli_date($item->timeclose):'-'; ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td align="right" colspan="4"><?php echo $quizes['pagination']; ?></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            <?php endif; ?>

                            <div class="tab intsettings-box settings-tab">
                                <form action="<?php echo $PAGE->url; ?>" method="GET" class="clearfix">
                                    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

                                    <input name="activity_setting" type="hidden" value="1" />

                                    <div class="form-group">
                                        <label for="activityCoursesList"><?php echo get_string('courses', 'local_intelliboard'); ?></label>
                                        <select name="activity_courses" class="form-control" id="activityCoursesList">
                                            <option><?php echo get_string('all_courses', 'local_intelliboard'); ?></option>
                                            <?php foreach($courses as $row):  ?>
                                                <option <?php echo ($showing_user->activity_courses == $row->id)?'selected="selected"':''; ?> value="<?php echo $row->id; ?>"><?php echo format_string($row->fullname); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="activity_time"><?php echo get_string('time_period_due', 'local_intelliboard'); ?>:</label>
                                        <select id="activity_time" name="activity_time" class="form-control">
                                            <option value="-1"><?php echo get_string('all_data', 'local_intelliboard'); ?></option>
                                            <?php foreach($menu as $key=>$value): ?>
                                                <option <?php echo ($showing_user->activity_time == $key)?'selected="selected"':''; ?> value="<?php echo s($key); ?>"><?php echo format_string($value); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?php echo get_string('save', 'local_intelliboard');?></button>
                                    <button type="button" class="closesettings btn"><?php echo get_string('cancel');?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <?php if($t11): ?>
                    <div class="<?php echo (!$t9 and !$t10)?'box100':'box50'; ?>  pull-left ib-table-wrapper">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="nav-item active"><a class="nav-link active" href="#"><?php echo get_string('course_progress', 'local_intelliboard'); ?></a></li>

                            <span>
                                <form action="<?php echo $PAGE->url; ?>" method="GET" class="clearfix">
                                    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

                                    <input name="type" type="hidden" value="course" />
                                    <input class="intsearch" name="search" placeholder="<?php echo get_string('search');?>"
                                           type="text" value="<?php echo ($type == 'course')?format_string($search):''; ?>"
                                           aria-label="<?php echo get_string('search');?>">
                                    <button type="submit">
                                        <?php echo get_string('search');?> <i class="ion-ios-search-strong"></i>
                                    </button>
                                    <span class="searchviewclose"><i class="ion-android-close"></i></span>
                                </form>
                                <span class="searchview actbtn"><i class="ion-ios-search-strong"></i></span>
                                <span class="active actbtn cview" data-tableclass="tm"><i class="ion-android-apps"></i></span>
                                <span class="cview actbtn" data-tableclass="list"><i class="ion-android-menu"></i></span>
                            </span>
                        </ul>

                        <table class="intelliboard-data-table cview-table table tab active">
                            <thead >
                            <th colspan="2"><?php echo get_string('course');?></th>
                            <?php if($t35): ?><th><?php echo get_string('progress', 'local_intelliboard'); ?></th><?php endif; ?>
                            <?php if($t36): ?><th class="align-center"><?php echo get_string('grade', 'local_intelliboard'); ?></th><?php endif; ?>
                            <?php if($t37): ?><th class="align-center"><?php echo get_string('enrolled', 'local_intelliboard'); ?></th><?php endif; ?>
                            <?php if($t38): ?><th class="align-center"><?php echo get_string('completed', 'local_intelliboard'); ?></th><?php endif; ?>

                            </thead>
                            <tbody>
                            <?php if(!count($courses_report['data'])): ?>
                                <tr>
                                    <td colspan="6"><?php echo get_string('no_data', 'local_intelliboard'); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($courses_report['data'] as $item): ?>
                                <?php if($item->type == 'course'): ?>
                                    <tr class="">
                                        <td width="1%"><i class="intelliboard-icon <?php echo (!$item->timecompleted)?'f6 ion-social-buffer':'f4 ion-android-done'; ?>"></i></td>
                                        <td>
                                            <a href="<?php echo $CFG->wwwroot; ?>/course/view.php?id=<?php echo s($item->id); ?>"><?php echo format_string($item->fullname); ?></a>
                                        </td>
                                        <?php if($t35): ?>
                                        <?php
                                            $completion = 0;
                                            if ($item->timecompleted) {
                                                $completion = 100;
                                            } elseif ($item->completedmodules) {
                                                $completion = ($item->completedmodules / $item->modules) * 100;
                                            }
                                            ?>
                                            <td width="100">
                                                <div class="intelliboard-progress g1 xl intelliboard-tooltip"  title="<?php echo "Activities: ".s($item->modules).", Completed: ".s($item->completedmodules); ?>"><span style="width:<?php echo $completion; ?>%"></span></div>
                                            </td>
                                        <?php endif; ?>

                                        <?php if($t36): ?>
                                            <td class="align-center">
                                                <?php if($scale_real):?>
                                                    <?php echo $item->grade; ?>
                                                <?php else:?>
                                                    <div class="circle-progress d-inline-block"  data-percent="<?php echo round((float) $item->grade,$scale_percentage_round); ?>"></div>
                                                <?php endif;?>
                                            </td>
                                        <?php endif; ?>

                                        <?php if($t37): ?>
                                            <td align="center">
                                                <?php echo intelli_date($item->timemodified); ?>
                                            </td>
                                        <?php endif; ?>

                                        <?php if($t38): ?>
                                            <td align="center">
                                                <?php echo ($item->timecompleted) ? intelli_date($item->timecompleted):'-'; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php elseif($item->type == 'category'): ?>
                                    <tr class="">
                                    <td></td>
                                    <td colspan="<?php echo ($t35)?2:1; ?>">
                                        <?php echo get_string('course_category', 'local_intelliboard').': '.format_string($item->fullname); ?>
                                    </td>
                                    <?php if($t36): ?>
                                        <td class="align-center" colspan="4">
                                            <?php if($scale_real):?>
                                                <?php echo $item->grade; ?>
                                            <?php else:?>
                                                <div class="circle-progress"  data-percent="<?php echo round((float) $item->grade,$scale_percentage_round); ?>"></div>
                                            <?php endif;?>
                                        </td>
                                    <?php endif; ?>
                                    </tr>
                                <?php endif; ?>

                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td align="right" colspan="6"><?php echo $courses_report['pagination']; ?></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if($t12 or $t13 or $t14 or $t15): ?>
            <div class="intelliboard-box">
                <?php if($t12 or $t13): ?>
                    <div class="<?php echo (!$t14 and !$t15)?'box100':'box45'; ?> pull-right h410">
                        <ul class="nav nav-tabs chart-tabs">
                            <?php if($t12): ?>
                                <li role="presentation" class="nav-item active"><a class="nav-link active" href="#"><?php echo get_string('activity_participation', 'local_intelliboard'); ?></a></li>
                            <?php endif; ?>

                            <?php if($t13): ?>
                                <li role="presentation" class="nav-item <?php echo (!$t12)?'active':''; ?>"><a class="nav-link" href="#"><?php echo get_string('learning', 'local_intelliboard'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                        <?php if($t12): ?>
                            <div id="chart1" class="chart-tab active"></div>
                        <?php endif; ?>

                        <?php if($t13): ?>
                            <div id="chart2" class="chart-tab <?php echo (!$t12)?'active':''; ?>"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if($t14 or $t15): ?>
                    <div class="<?php echo (!$t12 and !$t13)?'box100':'box50'; ?> pull-left h410">
                        <ul class="nav nav-tabs chart-tabs">
                            <?php if($t14): ?>
                                <li role="presentation" class="nav-item active"><a class="nav-link active" href="#"><?php echo get_string('course_success', 'local_intelliboard'); ?></a></li>
                            <?php endif; ?>

                            <?php if($t15): ?>
                                <li role="presentation" class="nav-item <?php echo (!$t14)?'active':''; ?>"><a class="nav-link" href="#"><?php echo get_string('correlations', 'local_intelliboard'); ?></a></li>
                            <?php endif; ?>
                        </ul>

                        <?php if($t14): ?>
                            <div id="chart3" class="chart-tab active"></div>
                        <?php endif; ?>

                        <?php if($t15): ?>
                            <div id="chart4" class="chart-tab <?php echo (!$t14)?'active':''; ?>"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php include("../views/footer.php"); ?>
    </div>
    <!-- <script type="text/javascript"
            src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart'],
              'language': '<?php echo current_language(); ?>'
            }]
          }"></script> -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.charts.load('current', {'callback': googleChartsCallback, 'name':'visualization', 'version': 1, 'packages':['corechart'], 'language': '<?php echo current_language(); ?>'});
        function decodeJson(htmlstring) {
            var taEl = document.createElement("textarea");
            taEl.innerHTML = htmlstring;
            return JSON.parse(taEl.value);
        }
        jQuery(document).ready(function(){
            jQuery('.intelliboard-dropdown:not(.students) ul li').click(function(e){
                e.stopPropagation();
                var stext = jQuery(this).parent().parent().find('span').text();
                var svalue = jQuery(this).parent().parent().find('span').attr('value');
                var ctext = jQuery(this).text();
                var cvalue = jQuery(this).attr('value');

                jQuery(this).text(stext);
                jQuery(this).attr('value', svalue);
                jQuery(this).parent().parent().find('span').text(ctext);
                jQuery(this).parent().parent().find('span').attr('value', cvalue);
                jQuery(this).parent().hide();
                location = "<?php echo $CFG->wwwroot; ?>/local/intelliboard/student/index.php?userid=<?php echo $showing_user->id; ?>&time="+cvalue+"&user=<?php echo $other_user; ?>";
            });

            jQuery('.intelliboard-dropdown:not(.students) button').click(function(e){
                e.stopPropagation();
                if(jQuery(this).parent().hasClass('disabled')){
                    return false;
                }
                jQuery(this).parent().find('ul').toggle();
            });

            jQuery('.closesettings').click(function(e){
                e.stopPropagation();
                e.preventDefault();
                jQuery(this).parent().parent().parent().parent().find('.nav-tabs li:first a')[0].click();
            });

            jQuery('.intsettings').click(function(e){
                e.stopPropagation();
                e.preventDefault();
                jQuery(this).parent().parent().find('li').removeClass("active");
                jQuery(this).parent().parent().parent().find('.tab').removeClass("active");
                jQuery('.intsettings-box').addClass("active");
            });
            jQuery('.searchviewclose').click(function(e){
                e.stopPropagation();
                e.preventDefault();
                jQuery(this).parent().parent().removeClass("active");
            });
            jQuery('.searchview').click(function(e){
                e.stopPropagation();
                e.preventDefault();
                jQuery(this).parent().addClass("active");
            });

            jQuery('.cview').click(function(e){
                e.stopPropagation();
                e.preventDefault();

                jQuery(this).parent().find('span').removeClass("active");
                jQuery(this).addClass("active");
                var m = jQuery(this).attr('data-tableclass');
                jQuery('.cview-table').removeClass("list");
                jQuery('.cview-table').addClass(m);
            });
            jQuery('.nav-tabs li a').click(function(e){
                e.stopPropagation();
                e.preventDefault();
                jQuery(this).parent().parent().find('li').removeClass("active");
                jQuery(this).parent().parent().find('a').removeClass("active");
                jQuery(this).parent().parent().find('.intype').val(jQuery(this).attr('href'));
                jQuery(this).parent().addClass("active");
                jQuery(this).parent().parent().parent().find('.tab').removeClass("active").eq(jQuery(this).parent().index()).addClass("active");
            });

            jQuery('.chart-tabs a').click(function(e){
                e.stopPropagation();
                e.preventDefault();
                jQuery(this).parent().parent().parent().find('.chart-tab').hide().eq(jQuery(this).parent().index()).show();
            });

            jQuery('.intelliboard-origin-head a').click(function(e){
                e.stopPropagation();
                e.preventDefault();

                jQuery(this).parent().find('a').removeClass("active");
                jQuery(this).addClass("active");
                jQuery(this).parent().parent().find('.intelliboard-chart-dash').hide().eq(jQuery(this).index()).show();
            });

            <?php if($active_tab != ''):?>
            jQuery('.nav-tabs li a[href="<?php echo $active_tab;?>"]')[0].click();
            <?php endif;?>
        });

        function googleChartsCallback() {
            <?php if($t14): ?>
                function CourseSuccess() {
                    var data = google.visualization.arrayToDataTable([
                        ['<?php echo intellitext(get_string('status', 'local_intelliboard')); ?>', '<?php echo intellitext(get_string('courses', 'local_intelliboard')); ?>'],
                        ['<?php echo intellitext(get_string('completed', 'local_intelliboard')); ?>',<?php echo (int)$totals->completed; ?>],
                        ['<?php echo intellitext(get_string('inprogress', 'local_intelliboard')); ?>',<?php echo (int)$totals->inprogress; ?>],
                        ['<?php echo intellitext(get_string('notstarted', 'local_intelliboard')); ?>',<?php $t = intval($totals->enrolled)-(intval($totals->inprogress) + intval($totals->completed)); echo ($t>0)?$t:0 ?>],
                    ]);
                    var options = decodeJson('<?php echo format_string($factorInfo->CourseSuccessCalculation); ?>');
                    var chart = new google.visualization.PieChart(document.getElementById('chart3'));
                    chart.draw(data, options);
                }
                CourseSuccess();
            <?php endif; ?>

            <?php if($t15): ?>
                function Correlations() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('number', '<?php echo intellitext(get_string('grade', 'local_intelliboard')); ?>');
                    data.addColumn('number', '<?php echo intellitext(get_string('in13', 'local_intelliboard')); ?>');
                    data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
                    data.addRows([<?php echo ($json_data2) ? implode(",", $json_data2):"";?>]);
                    var options = decodeJson('<?php echo format_string($factorInfo->CorrelationsCalculation); ?>');
                    var chart = new google.visualization.ScatterChart(document.getElementById('chart4'));
                    chart.draw(data, options);
                }
                Correlations();
            <?php endif; ?>

            <?php if($t12): ?>
                function ActivityParticipation() {
                    var data = google.visualization.arrayToDataTable([
                        ['<?php echo intellitext(get_string('modulename', 'local_intelliboard')); ?>', '<?php echo intellitext(get_string('total', 'local_intelliboard')); ?>', '<?php echo intellitext(get_string('viewed', 'local_intelliboard')); ?>', '<?php echo intellitext(get_string('completed', 'local_intelliboard')); ?>'],
                        <?php foreach($modules_progress as $row):  ?>
                        ['<?php echo addslashes(get_string('modulename', $row->name)); ?>', <?php echo (int)$row->modules; ?>, <?php echo (int)$row->start_modules; ?>, <?php echo (int)$row->completed_modules; ?>],
                        <?php endforeach; ?>
                    ]);
                    var options = decodeJson('<?php echo format_string($factorInfo->ActivityParticipationCalculation); ?>');
                    var chart = new google.visualization.ColumnChart(document.getElementById('chart1'));
                    chart.draw(data, options);
                }
                ActivityParticipation();
            <?php endif; ?>

            <?php if($t13): ?>
                function LearningProgress() {

                    var data = google.visualization.arrayToDataTable([
                        ['<?php echo intellitext(get_string('modulename', 'local_intelliboard')); ?>', '<?php echo intellitext(get_string('time_spent', 'local_intelliboard')); ?>'],
                        <?php foreach($modules_progress as $row):  ?>
                        ['<?php echo intellitext(get_string('modulename', $row->name)); ?>', {v:<?php echo (int)$row->duration; ?>, f:'<?php echo seconds_to_time(intval($row->duration)); ?>'}],
                        <?php endforeach; ?>
                    ]);
                    var options = decodeJson('<?php echo format_string($factorInfo->LearningProgressCalculation); ?>');
                    var chart = new google.visualization.PieChart(document.getElementById('chart2'));
                    chart.draw(data, options);
                }
                LearningProgress();
            <?php endif; ?>

            <?php if($t6): ?>
                function drawCourseProgress() {
                    var data = google.visualization.arrayToDataTable([
                        ['<?php echo intellitext(get_string('course', 'local_intelliboard')); ?>', '<?php echo intellitext(get_string('courseaverage', 'local_intelliboard')); ?>', '<?php echo intellitext(get_string('mygrade', 'local_intelliboard')); ?>'],
                        <?php foreach($coursesprogress as $row):  ?>
                        ['<?php echo intellitext(format_string($row->fullname)); ?>', <?php echo ($scale_real)?"{v: ".(int)$row->average.", f: '".$row->average_real."'}":(int)$row->average; ?>, <?php echo ($scale_real)?"{v: ".(int)$row->grade.", f: '".$row->grade_real."'}":(int)$row->grade; ?>],
                        <?php endforeach; ?>
                    ]);

                    var options = decodeJson('<?php echo format_string($factorInfo->CourseProgressCalculation); ?>');
                    var chart = new google.visualization.ComboChart(document.getElementById('intelliboard-chart-combo'));
                    chart.draw(data, options);
                    setTimeout(function () {
                        jQuery('.intelliboard-origin-head a:first-child')[0].click();
                    }, 200);
                }
                drawCourseProgress();
            <?php endif; ?>

            <?php if($t5): ?>
                function drawActivityProgress() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('date', '<?php echo intellitext(get_string('time', 'local_intelliboard')); ?>');
                    data.addColumn('number', '<?php echo intellitext(get_string('myprogress', 'local_intelliboard')); ?>');
                    data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
                    <?php if($t53): ?>
                    data.addColumn('number', '<?php echo intellitext(get_string('average_grade', 'local_intelliboard')); ?>');
                    data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
                    <?php endif; ?>
                    data.addRows([<?php echo ($json_data) ? implode(",", $json_data):"";?>]);
                    var options = decodeJson('<?php echo format_string($factorInfo->ActivityProgressCalculation); ?>');
                    options.hAxis.minValue = <?php echo $hAxis_min; ?>;
                    options.hAxis.maxValue = <?php echo $hAxis_max; ?>;
                    var chart = new google.visualization.LineChart(document.getElementById('intelliboard-chart'));
                    chart.draw(data, options);
                    setTimeout(function () {
                        jQuery('.intelliboard-origin-head a:first-child')[0].click();
                    }, 200);
                }
                drawActivityProgress();
            <?php endif; ?>
        }
    </script>
<?php endif; ?>
<?php echo $OUTPUT->footer();
