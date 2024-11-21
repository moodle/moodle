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
require_once('../externallib.php');


$id = required_param('id', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$length = optional_param('length', 20, PARAM_INT);
$action = optional_param('action', '', PARAM_RAW);
$courseid = optional_param('courseid', 0, PARAM_INT);
$cohortid = optional_param('cohortid', 0, PARAM_INT);
$custom = optional_param('custom', 0, PARAM_INT);
$custom2 = optional_param('custom2', 0, PARAM_INT);
$daterange = clean_raw(optional_param('daterange', '', PARAM_RAW));

require_login();
intelliboard_instructor_access();

if ($action == 'export_pdf' || $action == 'export_excel' || $action == 'export_csv'){
    include("analytic_templates/analytic_export_$id.php");
    exit;
}

$params = array(
    'do'=>'instructor',
    'mode'=> 2
);
$intelliboard = intelliboard($params);
$factorInfo = chart_options();

$PAGE->set_url(new moodle_url("/local/intelliboard/instructor/analytics.php"));
$PAGE->set_pagetype('analytics');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->js('/local/intelliboard/assets/js/jquery.multiple.select.js');
$PAGE->requires->js('/local/intelliboard/assets/js/flatpickr.min.js');
if(file_exists('/local/intelliboard/assets/js/flatpickr_l10n/'.current_language().'.js')) {
    $PAGE->requires->js('/local/intelliboard/assets/js/flatpickr_l10n/'.current_language().'.js');
}
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');
$PAGE->requires->css('/local/intelliboard/assets/css/multiple-select.css');
$PAGE->requires->css('/local/intelliboard/assets/css/flatpickr.min.css');

echo $OUTPUT->header();
?>
<?php if(!isset($intelliboard) || !$intelliboard->token): ?>
    <div class="alert alert-error alert-block" role="alert"><?php echo get_string('intelliboardaccess', 'local_intelliboard'); ?></div>
<?php else: ?>
    <?php echo '<script src="//code.highcharts.com/highcharts.js"></script>';?>
    <div class="intelliboard-page intelliboard-instructor intelliboard-analytics">
        <?php include("views/menu.php"); ?>
        <?php include("analytic_templates/analytic_$id.php"); ?>
        <?php include("../views/footer.php"); ?>
    </div>
<?php endif; ?>
<?php echo $OUTPUT->footer();
