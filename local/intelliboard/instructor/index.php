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

if(!get_config('local_intelliboard', 'show_dashboard_tab')) {
    redirect(
        new \moodle_url('/local/intelliboard/instructor/courses.php')
    );
}

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$view = optional_param('view', '', PARAM_ALPHANUMEXT);
$search = clean_raw(optional_param('search', '', PARAM_RAW));
$type = optional_param('type', '', PARAM_ALPHANUMEXT);
$time = optional_param('time', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$course = optional_param('course', 0, PARAM_INT);
$length = optional_param('length', 100, PARAM_INT);
$daterange = clean_raw(optional_param('daterange', '', PARAM_RAW));
$filter_courses = optional_param('filter_courses', '', PARAM_ALPHANUMEXT);
$debug = get_config('local_intelliboard', 'debug');
$debugmode = optional_param('debug', '', PARAM_RAW);

require_login();
intelliboard_instructor_access();

if(!$action){
	$params = array('do'=>'instructor','mode'=> 2);
	$intelliboard = intelliboard($params);
	$factorInfo = chart_options();
}

if (!$daterange) {
    $timestart = strtotime('-7 days');
    $timefinish = time();

    $timestart_date = intelli_date($timestart);
    $timefinish_date = intelli_date($timefinish);

    $daterange = $timestart_date . ' to ' . $timefinish_date;
} else {
    $range = preg_split("/ (.)+ /", $daterange);

    if(isset($range[0]) && $range[0]) {
        $timestart = date_create_from_format(
            intelli_date_format(), trim($range[0])
        )->getTimestamp();
    } else {
        $timestart = strtotime('-7 days');
    }

    if(isset($range[1]) && $range[1]) {
        $timefinish = date_create_from_format(
            intelli_date_format(), trim($range[1])
        )->getTimestamp();
    } else {
        $timefinish = time();
    }

    $timestart_date = intelli_date($timestart);
    $timefinish_date = intelli_date($timefinish);
}

$PAGE->set_url(new moodle_url(
    "/local/intelliboard/instructor/index.php", array("type"=>$type, "search"=>$search, "daterange"=>$daterange)
));
$PAGE->set_pagetype('home');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));

// $PAGE->requires->jquery();
// $PAGE->requires->jquery_plugin('/local/intelliboard/assets/js/flatpickr.min.js');
// if(file_exists('/local/intelliboard/assets/js/flatpickr_l10n/'.current_language().'.js')) {
//     $PAGE->requires->js('/local/intelliboard/assets/js/flatpickr_l10n/'.current_language().'.js');
// }
$PAGE->requires->css('/local/intelliboard/assets/css/flatpickr.min.css');
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');
$PAGE->requires->css('/local/intelliboard/assets/css/multiple-select.css');


$instructor_course_shortname = get_config('local_intelliboard', 'instructor_course_shortname');
$mycourses = intelliboard_instructor_getcourses('', true, '');
$list_of_my_courses = array();

foreach($mycourses as $item){
    $list_of_my_courses[$item->id] = ($instructor_course_shortname) ? $item->shortname : $item->fullname;
}

if($course == 0){
    $course = key($list_of_my_courses);
}
if (!$course) {
	throw new moodle_exception('invalidcourse', 'error');
}

$n1 = get_config('local_intelliboard', 'n1');
$n2 = get_config('local_intelliboard', 'n2');
$n3 = get_config('local_intelliboard', 'n3');
$n4 = get_config('local_intelliboard', 'n4');
$n5 = get_config('local_intelliboard', 'n5');
$n6 = get_config('local_intelliboard', 'n6');
$n7 = get_config('local_intelliboard', 'n7');
$n12 = get_config('local_intelliboard', 'n12');
$n13 = get_config('local_intelliboard', 'n13');
$n14 = get_config('local_intelliboard', 'n14');
$n15 = get_config('local_intelliboard', 'n15');
$n16 = get_config('local_intelliboard', 'n16');
$n18 = get_config('local_intelliboard', 'n18');
$raw = get_config('local_intelliboard', 'scale_raw');

$menu = array();
if($n1){
	$menu['progress'] = get_string('in11', 'local_intelliboard');
}
if($n2){
	$menu['grades'] = get_string('in12', 'local_intelliboard');
}
if($n3){
    $menu['activities'] = get_string('activity_progress', 'local_intelliboard');
}
if($n12){
    $menu['course_overview'] = get_string('course_overview', 'local_intelliboard');
}

if(empty($view)){
    $view = key($menu);
}

$courses = intelliboard_instructor_courses($view, $page, $length, $course, $daterange);

$renderer = $PAGE->get_renderer("local_intelliboard");

$PAGE->requires->js_call_amd(
    'local_intelliboard/instructor', 'dashboardSettings', [get_string('all_courses', 'local_intelliboard'), get_string('all_selected', 'local_intelliboard'), get_string('selectall', 'local_intelliboard')]
);

echo $OUTPUT->header();
?>
<?php if(!isset($intelliboard) || !$intelliboard->token): ?>
	<div class="alert alert-error alert-block" role="alert"><?php echo get_string('intelliboardaccess', 'local_intelliboard'); ?></div>
	<?php if ($debug and $debugmode): ?>
        <pre>
            <code>[reports_status]: <?php echo (get_config('local_intelliboard', 'n9')) ? 1 : 0; ?></code>
            <code>[count_reports]: <?php echo (isset($intelliboard->reports) ? count($intelliboard->reports) : ''); ?></code>
            <code>[debug]: <?php echo $intelliboard->debugging; ?></code>
        </pre>
    <?php endif; ?>
<?php else: ?>
<div class="intelliboard-page intelliboard-instructor">
    <div class="additional_header clearfix">
        <?php include("views/menu.php"); ?>
        <div class="additional-form clearfix">
            <input type="text" id="general-daterange" class="daterange flatpickr-input form-control"
                    name="daterange" title="<?php echo get_string('filter_dates', 'local_intelliboard'); ?>" readonly="readonly"
                    placeholder="<?php echo get_string('select_date', 'local_intelliboard'); ?>">
        </div>
    </div>

    <?php
        echo $renderer->render(new \local_intelliboard\output\instructor_index([
            "pluginsettings" => (object) [
                "n1" => $n1, "n2" => $n2, "n3" => $n3, "n4" => $n4, "n5" => $n5, "n6" => $n6,
                "n7" => $n7, "n12" => $n12, "n13" => $n13, "n14" => $n14,
                "n15" => $n15, "n16" => $n16, "n18" => $n18, "raw" => $raw
            ],
            "menu" => $menu,
            "view" => $view,
            "listofmycourses" => $list_of_my_courses,
            "course" => $course,
            "timestartdate" => $timestart_date,
            "timefinishdate" => $timefinish_date,
            "courses" => $courses
        ]));
    ?>

	<?php include("../views/footer.php"); ?>
</div>

<?php endif; ?>
<?php echo $OUTPUT->footer();
