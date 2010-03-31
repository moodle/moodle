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
 * This file is part of the User section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/repository/lib.php');

$config = optional_param('config', 0, PARAM_INT);
$course  = optional_param('course', SITEID, PARAM_INT);

$url = new moodle_url('/user/repository.php', array('course'=>$course));
if ($config !== 0) {
    $url->param('config', $config);
}
$PAGE->set_url($url);

$course = $DB->get_record("course", array("id"=>$course), '*', MUST_EXIST);

$user = $USER;
$baseurl = $CFG->wwwroot . '/user/repository.php';
$namestr = get_string('name');
$fullname = fullname($user);
$strrepos = get_string('repositories', 'repository');
$configstr = get_string('manageuserrepository', 'repository');
$pluginstr = get_string('plugin', 'repository');

require_login($course, false);
$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

$link = new moodle_url('/user/view.php', array('id'=>$user->id));
$PAGE->navbar->add($fullname, $link);
$PAGE->navbar->add($strrepos);
$PAGE->set_title("$course->fullname: $fullname: $strrepos");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$currenttab = 'repositories';
include('tabs.php');

echo $OUTPUT->heading($configstr);
echo $OUTPUT->box_start();

$params = array();
$params['context'] = $coursecontext;
$params['currentcontext'] = $PAGE->context;
$params['userid']   = $USER->id;
if (!$instances = repository::get_instances($params)) {
    print_error('noinstances', 'repository', $CFG->wwwroot . '/user/view.php');
}

$table = new html_table();
$table->head = array($namestr, $pluginstr, '');
$table->data = array();

foreach ($instances as $i) {
    $path = '/repository/'.$i->type.'/settings.php';
    $settings = file_exists($CFG->dirroot.$path);
    $table->data[] = array($i->name, $i->type,
        $settings ? '<a href="'.$CFG->wwwroot.$path.'">'
            .get_string('settings', 'repository').'</a>' : '');
}

echo html_writer::table($table);
echo $OUTPUT->footer();


