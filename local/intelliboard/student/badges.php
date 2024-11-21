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
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

use local_intelliboard\output\student_menu;
use local_intelliboard\output\tables\student_badges;

require('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/student/lib.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');

require_login();
require_capability('local/intelliboard:students', context_system::instance());

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

$PAGE->set_url(new moodle_url(
    "/local/intelliboard/student/badges.php",
    ["sesskey"=> sesskey()]
));
$PAGE->set_pagetype('badges');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->js('/local/intelliboard/assets/js/jquery.circlechart.js');
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');

$params = array(
    'do'=>'learner',
    'mode'=> 1
);
$intelliboard = intelliboard($params);
$renderer = $PAGE->get_renderer('local_intelliboard');
$studentmenurenderable = new student_menu(
    ['intelliboard' => $intelliboard, 'showinguser' => $USER]
);
$table = new student_badges('student_badges');

echo $OUTPUT->header();

if(!isset($intelliboard) || !$intelliboard->token) {
    echo $OUTPUT->render_from_template('local_intelliboard/access_alert', []);
} else {
    echo $OUTPUT->render_from_template(
        'local_intelliboard/student_badges',
        ['student_menu' => $renderer->render($studentmenurenderable)]
    );
    echo $table->out(10, false);
}

$PAGE->requires->js_call_amd(
    'local_intelliboard/intelliboard', 'circleProgress', [chart_options()->GradesXCalculationJSON]
);

echo $OUTPUT->footer();
