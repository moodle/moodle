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

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/forms.php');

$config = optional_param('config', 0, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);
$course  = optional_param('course', SITEID, PARAM_INT);

$url = new moodle_url('/user/portfolio.php', array('course'=>$course));
if ($hide !== 0) {
    $url->param('hide', $hide);
}
if ($config !== 0) {
    $url->param('config', $config);
}
$PAGE->set_url($url);

if (! $course = $DB->get_record("course", array("id"=>$course))) {
    print_error('invalidcourseid');
}

$user = $USER;
$fullname = fullname($user);
$strportfolios = get_string('portfolios', 'portfolio');
$configstr = get_string('manageyourportfolios', 'portfolio');
$namestr = get_string('name');
$pluginstr = get_string('plugin', 'portfolio');
$baseurl = $CFG->wwwroot . '/user/portfolio.php';

$display = true; // set this to false in the conditions to stop processing

require_login($course, false);

echo $OUTPUT->header();
$currenttab = 'portfolioconf';
$showroles = 1;
include('tabs.php');

if (!empty($config)) {
    $instance = portfolio_instance($config);
    $mform = new portfolio_user_form('', array('instance' => $instance, 'userid' => $user->id));
    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    } else if ($fromform = $mform->get_data()){
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', '', $baseurl);
        }
        //this branch is where you process validated data.
        $success = $instance->set_user_config($fromform, $USER->id);
            //$success = $success && $instance->save();
        if ($success) {
            redirect($baseurl, get_string('instancesaved', 'portfolio'), 3);
        } else {
            print_error('instancenotsaved', 'portfolio', $baseurl);
        }
        exit;
    } else {
        echo $OUTPUT->heading(get_string('configplugin', 'portfolio'));
        echo $OUTPUT->box_start();
        $mform->display();
        echo $OUTPUT->box_end();
        $display = false;
    }

} else if (!empty($hide)) {
    $instance = portfolio_instance($hide);
    $instance->set_user_config(array('visible' => !$instance->get_user_config('visible', $USER->id)), $USER->id);
}

if ($display) {
    echo $OUTPUT->heading($configstr);
    echo $OUTPUT->box_start();

    if (!$instances = portfolio_instances(true, false)) {
        print_error('noinstances', 'portfolio', $CFG->wwwroot . '/user/view.php');
    }

    $table = new html_table();
    $table->head = array($namestr, $pluginstr, '');
    $table->data = array();

    foreach ($instances as $i) {
        $visible = $i->get_user_config('visible', $USER->id);
        $table->data[] = array($i->get('name'), $i->get('plugin'),
            ($i->has_user_config()
                ?  '<a href="' . $baseurl . '?config=' . $i->get('id') . '"><img src="' . $OUTPUT->pix_url('t/edit') . '" alt="' . get_string('configure') . '" /></a>' : '') .
                   ' <a href="' . $baseurl . '?hide=' . $i->get('id') . '"><img src="' . $OUTPUT->pix_url('t/' . (($visible) ? 'hide' : 'show')) . '" alt="' . get_string($visible ? 'hide' : 'show') . '" /></a><br />'
        );
    }

    echo html_writer::table($table);
    echo $OUTPUT->box_end();
}
echo $OUTPUT->footer();

