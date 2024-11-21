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
require_once($CFG->dirroot . '/local/intelliboard/locallib.php');
require_once($CFG->dirroot . '/local/intelliboard/competencies/lib.php');

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$view = optional_param('view', 'progress', PARAM_ALPHANUMEXT);
$search = clean_raw(optional_param('search', '', PARAM_RAW));
$type = optional_param('type', '', PARAM_ALPHANUMEXT);
$time = optional_param('time', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$length = optional_param('length', 100, PARAM_INT);
$cohortid = optional_param("cohortid", 0, PARAM_INT);

require_login();
intelliboard_competency_access();

if (!get_config('local_intelliboard', 'competency_dashboard')) {
    throw new moodle_exception('invalidaccess', 'error');
}

if (!$action) {
    $params = array(
        'do' => 'competencies',
        'mode' => 3
    );
    $intelliboard = intelliboard($params);
    $factorInfo = chart_options();
}

$PAGE->set_url(new moodle_url("/local/intelliboard/competencies/index.php", array("type" => $type, "search" => $search)));
$PAGE->set_pagetype('home');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');
$PAGE->requires->css('/local/intelliboard/assets/css/multiple-select.css');
$PAGE->requires->js_call_amd(
    'local_intelliboard/competency_dashboard', 'cohortFilter', []
);

$compcourses = intelliboard_competency_courses();
$frameworks = intelliboard_competency_frameworks();
$competencies = intelliboard_competencies_progress($cohortid ? [$cohortid] : []);
$totals = intelliboard_competencies_total($cohortid ? [$cohortid] : []);
$usercohorts = user_cohorts($USER->id);

$n1 = get_config('local_intelliboard', 'a36');
$n4 = get_config('local_intelliboard', 'a39');
$n5 = get_config('local_intelliboard', 'a4');
$n6 = get_config('local_intelliboard', 'a38');
$n7 = get_config('local_intelliboard', 'a31');

echo $OUTPUT->header();
?>
<?php if (!isset($intelliboard) || !$intelliboard->token): ?>
    <div class="alert alert-error alert-block"
         role="alert"><?php echo get_string('intelliboardaccess', 'local_intelliboard'); ?></div>
<?php else: ?>
    <?php include("views/menu.php"); ?>
    <div class="intelliboard-page intelliboard-instructor competency-dashboard">
    <span class="cohort-filter-wrapper competency-cohort-filter-wrapper">
        <select id="competencyCohortFilter">
            <option value="0"
                    data-href="<?php echo new moodle_url($PAGE->url, ['cohortid' => 0]); ?>"
                    <?php echo 0 == $cohortid ? "selected=\"selected\"" : ""; ?>
            >
                <?php echo get_string("all_cohorts", "local_intelliboard"); ?>
            </option>
            <?php foreach ($usercohorts as $cohort): ?>
                <option value="<?php echo $cohort->id; ?>"
                        data-href="<?php echo new moodle_url($PAGE->url, ['cohortid' => $cohort->id]); ?>"
                        <?php echo $cohort->id == $cohortid ? "selected=\"selected\"" : ""; ?>
                >
                    <?php echo $cohort->name; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </span>
        <?php if (isset($totals->competencies) and $totals->competencies > 0): ?>
            <div class="intelli-instructor-header clearfix">
                <div class="instructor-head <?php echo ($n5) ? '' : 'full'; ?>">
                    <?php if ($n1): ?>
                        <h3><?php echo get_string('a36', 'local_intelliboard'); ?></h3>
                        <div class="clear"></div>
                        <div id="instructor-chart-progress" class="instructor-chart"></div>
                    <?php endif; ?>
                    <?php if ($n4): ?>
                        <ul class="instructor-total">
                            <li>
                                <strong><?php echo (int)$totals->frameworks; ?></strong>
                                <?php echo get_string('a31', 'local_intelliboard'); ?>
                            </li>
                            <li>
                                <strong><?php echo (int)$totals->competencies; ?></strong>
                                <?php echo get_string('a1', 'local_intelliboard'); ?>
                            </li>
                            <li>
                                <strong><?php echo (int)$totals->plans; ?></strong>
                                <?php echo get_string('a32', 'local_intelliboard'); ?>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
                <?php if ($n5): ?>
                    <div class="summary">
                        <h3><?php echo get_string('a4', 'local_intelliboard'); ?></h3>

                        <div class="summary-chart-wrap">
                            <span class="summary-chart-label"><?php echo (int)$totals->proficient; ?>
                                <i><?php echo get_string('a2', 'local_intelliboard'); ?></i>
                            </span>
                            <div id="summary-chart" class="summary-chart"></div>
                        </div>
                        <ul class="instructor-summary  clearfix">
                            <li>
                                <?php if (!isset($CFG->totara_version)) {
                                    echo get_string('a33', 'local_intelliboard');
                                } else {
                                    echo get_string('a33b', 'local_intelliboard');
                                } ?>
                                <strong><?php echo (int)$totals->proficient; ?></strong>
                            </li>
                            <?php if (!isset($CFG->totara_version)) { ?>
                                <li>
                                    <?php echo get_string('a34', 'local_intelliboard'); ?>
                                    <strong><?php echo (int)$totals->unproficient; ?></strong>
                                </li>
                            <?php } ?>
                            <li>
                                <?php if (!isset($CFG->totara_version)) {
                                    echo get_string('a35', 'local_intelliboard');
                                } else {
                                    echo get_string('a35b', 'local_intelliboard');
                                } ?>
                                <strong><?php echo (int)$totals->unrated; ?></strong>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="intelliboard-box competency-dashboard-intelliboard-box">
                <?php if ($n6): ?>
                    <div class="box<?php echo ($n7) ? '50' : '100'; ?> pull-left h410">
                        <ul class="nav nav-tabs clearfix">
                            <li role="presentation" class="nav-item active"><a class="nav-link active"
                                                                               href="#"><?php echo get_string('a38', 'local_intelliboard'); ?></a>
                            </li>
                        </ul>
                        <div class="card-block">
                            <div id="chart4"
                                 class="chart-tab active"><?php echo get_string('loading', 'local_intelliboard'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($n7): ?>
                    <div class="box<?php echo ($n6) ? '40' : '100'; ?> pull-right h410">
                        <ul class="nav nav-tabs clearfix">
                            <li role="presentation" class="nav-item active"><a class="nav-link active"
                                                                               href="#"><?php echo get_string('a31', 'local_intelliboard'); ?></a>
                            </li>
                        </ul>
                        <div class="card-block">
                            <div id="chart2"
                                 class="chart-tab active"><?php echo get_string('loading', 'local_intelliboard'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript" src="https://www.google.com/jsapi"></script>
            <script type="text/javascript">
                google.charts.load('current', {
                    'callback': googleChartsCallback,
                    'name': 'visualization',
                    'version': 1,
                    'packages': ['corechart'],
                    'language': '<?php echo current_language(); ?>'
                });

                function decodeJson(htmlstring) {
                    var taEl = document.createElement("textarea");
                    taEl.innerHTML = htmlstring;
                    return JSON.parse(taEl.value);
                }

                function googleChartsCallback() {
                    <?php if($n7): ?>
                    (function () {
                        var data = google.visualization.arrayToDataTable([
                            [
                                '<?php echo addslashes(get_string('a13', 'local_intelliboard')); ?>',
                                '<?php if (!isset($CFG->totara_version)) {
                                    echo addslashes(get_string('a33', 'local_intelliboard'));
                                } else {
                                    echo addslashes(get_string('a33b', 'local_intelliboard'));
                                } ?>',
                            ],
                            <?php foreach($compcourses as $row):  ?>
                            [
                                '<?php echo intellitext(format_string($row->shortname, true, ['escape' => true])); ?>',
                                {
                                    v:<?php echo (int)$row->courses; ?>,
                                    f: '<?php echo addslashes(get_string('a40', 'local_intelliboard')) . ": " . addslashes($row->courses); ?>'
                                },
                            ],
                            <?php endforeach; ?>
                        ]);
                        var options = decodeJson('<?php echo format_string($factorInfo->LearningProgressCalculation); ?>');
                        options.pieSliceText = 'label';
                        var chart = new google.visualization.PieChart(document.getElementById('chart4'));
                        chart.draw(data, options);
                    })();
                    <?php endif; ?>

                    <?php if($n6): ?>
                    (function () {
                        var data = google.visualization.arrayToDataTable([
                            [
                                '<?php echo addslashes(get_string('a31', 'local_intelliboard')); ?>',
                                '<?php echo addslashes(get_string('a1', 'local_intelliboard')); ?>',
                            ],
                            <?php foreach($frameworks as $row):  ?>
                            [
                                '<?php echo intellitext(format_string($row->shortname)); ?>',
                                <?php echo (int)$row->competencies; ?>,
                            ],
                            <?php endforeach; ?>
                        ]);
                        var options = decodeJson('<?php echo format_string($factorInfo->LearningProgressCalculation); ?>');
                        var chart = new google.visualization.BarChart(document.getElementById('chart2'));
                        chart.draw(data, options);
                    })();
                    <?php endif; ?>

                    /** Chart "Proficiency Progress" */
                    <?php if($n5): ?>
                    (function () {
                        var data = google.visualization.arrayToDataTable([
                            [
                                '<?php echo addslashes(get_string('learners', 'local_intelliboard')); ?>',
                                '<?php echo addslashes(get_string('a8', 'local_intelliboard')); ?>'
                            ],
                            ['<?php if (!isset($CFG->totara_version)) {
                                echo addslashes(get_string('a33', 'local_intelliboard'));
                            } else {
                                echo addslashes(get_string('a33b', 'local_intelliboard'));
                            } ?>', <?php echo (int)$totals->proficient; ?>],
                            <?php if (!isset($CFG->totara_version)) { ?>
                                ['<?php echo addslashes(get_string('a34', 'local_intelliboard')); ?>', <?php echo (int)$totals->unproficient; ?>],
                            <?php } ?>
                            ['<?php if (!isset($CFG->totara_version)) {
                                echo addslashes(get_string('a35', 'local_intelliboard'));
                            } else {
                                echo addslashes(get_string('a35b', 'local_intelliboard'));
                            }?>', <?php echo (int)$totals->unrated; ?>]
                        ]);
                        var options = {
                            chartArea: {width: '100%', height: '90%',},
                            pieHole: 0.8,
                            pieSliceTextStyle: {
                                color: 'transparent',
                            },
                            colors: ['#1db34f', '#1d7fb3', '#dddddd'],
                            legend: 'none'
                        };
                        var chart = new google.visualization.PieChart(document.getElementById('summary-chart'));
                        chart.draw(data, options);
                    })();
                    <?php endif; ?>
                    /** Chart "Proficiency Progress" */

                    /** Chart "Competency Overview" **/
                    (function () {
                        var options = {
                            title: '',
                            legend: {position: 'top', alignment: 'end'},
                            seriesType: 'bars',
                            chartArea: {width: '90%', height: '76%'},
                            colors: ['#1db34f', '#1d7fb3', '#dddddd'],
                            backgroundColor: {fill: 'transparent'}
                        };
                        var data = google.visualization.arrayToDataTable([
                            [
                                '<?php echo addslashes(get_string('a13', 'local_intelliboard')); ?>',
                                '<?php if (!isset($CFG->totara_version)) {
                                    echo addslashes(get_string('a33', 'local_intelliboard'));
                                } else {
                                    echo addslashes(get_string('a33b', 'local_intelliboard'));
                                } ?>',
                                <?php if (!isset($CFG->totara_version)) {
                                    echo "'" . addslashes(get_string('a34', 'local_intelliboard')) . "',"; }
                                ?>
                                '<?php if (!isset($CFG->totara_version)) {
                                    echo addslashes(get_string('a35', 'local_intelliboard'));
                                } else {
                                    echo addslashes(get_string('a35b', 'local_intelliboard'));
                                }?>'
                            ],
                            <?php foreach($competencies as $row):  ?>
                            [
                                '<?php echo intellitext(format_string($row->shortname)); ?>',
                                <?php echo (int)$row->proficient; ?>,
                                <?php if (!isset($CFG->totara_version)) { echo (int)$row->unproficient . ', '; } ?>
                                <?php echo (int)$row->unrated; ?>
                            ],
                            <?php endforeach; ?>
                        ]);
                        var chart = new google.visualization.ColumnChart(document.getElementById('instructor-chart<?php echo ($view) ? "-" . $view : ""; ?>'));
                        chart.draw(data, options);
                    })();
                    /** Chart "Competency Overview" **/
                }
            </script>
        <?php else: ?>
        <br>
            <div class="alert alert-info alert-block"><?php echo get_string('a37', 'local_intelliboard'); ?></div>
        <?php endif; ?>
        <?php include("../views/footer.php"); ?>
    </div>
<?php endif; ?>
<?php echo $OUTPUT->footer();

