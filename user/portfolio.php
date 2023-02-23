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
 * @package core_user
 */

require_once(__DIR__ . '/../config.php');

if (empty($CFG->enableportfolios)) {
    throw new \moodle_exception('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/forms.php');

$config   = optional_param('config', 0, PARAM_INT);
$hide     = optional_param('hide', 0, PARAM_INT);
$courseid = optional_param('courseid', SITEID, PARAM_INT);

$url = new moodle_url('/user/portfolio.php', array('courseid' => $courseid));

if ($config !== 0) {
    $url->param('config', $config);
}
if (! $course = $DB->get_record("course", array("id" => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}

$user = $USER;
$fullname = fullname($user);
$strportfolios = get_string('portfolios', 'portfolio');
$configstr = get_string('manageyourportfolios', 'portfolio');
$namestr = get_string('name');
$pluginstr = get_string('plugin', 'portfolio');
$baseurl = $CFG->wwwroot . '/user/portfolio.php';
$introstr = get_string('intro', 'portfolio');
$showhide = get_string('showhide', 'portfolio');

$display = true; // Set this to false in the conditions to stop processing.

require_login($course, false);

$PAGE->set_url($url);
$PAGE->set_context(context_user::instance($user->id));
$PAGE->set_title($configstr);
$PAGE->set_heading($fullname);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
$showroles = 1;

if (!empty($config)) {
    navigation_node::override_active_url(new moodle_url('/user/portfolio.php', array('courseid' => $courseid)));
    $instance = portfolio_instance($config);
    $mform = new portfolio_user_form('', array('instance' => $instance, 'userid' => $user->id));
    if ($mform->is_cancelled()) {
        redirect($baseurl);
        exit;
    } else if ($fromform = $mform->get_data()) {
        if (!confirm_sesskey()) {
            throw new \moodle_exception('confirmsesskeybad', '', $baseurl);
        }
        // This branch is where you process validated data.
        $instance->set_user_config($fromform, $USER->id);
        core_plugin_manager::reset_caches();
        redirect($baseurl, get_string('instancesaved', 'portfolio'), 3);

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
    core_plugin_manager::reset_caches();
}

if ($display) {
    echo $OUTPUT->heading($configstr);
    echo $OUTPUT->box_start();

    echo html_writer::tag('p', $introstr);

    if (!$instances = portfolio_instances(true, false)) {
        throw new \moodle_exception('noinstances', 'portfolio', $CFG->wwwroot . '/user/view.php');
    }

    $table = new html_table();
    $table->head = array($namestr, $pluginstr, $showhide);
    $table->data = array();

    foreach ($instances as $i) {
        // Contents of the actions (Show / hide) column.
        $actions = '';

        // Configure icon.
        if ($i->has_user_config()) {
            $configurl = new moodle_url($baseurl);
            $configurl->param('config', $i->get('id'));
            $actions .= html_writer::link($configurl, $OUTPUT->pix_icon('t/edit', get_string('configure', 'portfolio')));
        }

        // Hide/show icon.
        $visible = $i->get_user_config('visible', $USER->id);
        $visibilityaction = $visible ? 'hide' : 'show';
        $showhideurl = new moodle_url($baseurl);
        $showhideurl->param('hide', $i->get('id'));
        $actions .= html_writer::link($showhideurl, $OUTPUT->pix_icon('t/' . $visibilityaction, get_string($visibilityaction)));

        $table->data[] = array($i->get('name'), $i->get('plugin'), $actions);
    }

    echo html_writer::table($table);
    echo $OUTPUT->box_end();
}
echo $OUTPUT->footer();
