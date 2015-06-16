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
 * This page lets users to manage site wide competencies.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('toollpcompetencies');

$title = get_string('competencies', 'tool_lp');
$id = optional_param('id', 0, PARAM_INT);
$competencyframeworkid = required_param('competencyframeworkid', PARAM_INT);
$parentid = optional_param('parentid', 0, PARAM_INT);

if (empty($id)) {
    $pagetitle = get_string('addnewcompetency', 'tool_lp');
} else {
    $pagetitle = get_string('editcompetency', 'tool_lp');
}
// Set up the page.
$params = array('id' => $id, 'competencyframeworkid' => $competencyframeworkid, 'parentid' => $parentid);
$url = new moodle_url("/admin/tool/lp/editcompetency.php", $params);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$output = $PAGE->get_renderer('tool_lp');

$competencyframework = \tool_lp\api::read_framework($competencyframeworkid);
$parent = null;
if ($parentid) {
    $parent = \tool_lp\api::read_competency($parentid);
}

$form = new \tool_lp\form\competency(null, array('id' => $id, 'competencyframework' => $competencyframework, 'parent' => $parent));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/lp/competencies.php', array('competencyframeworkid' => $competencyframeworkid)));
}

echo $output->header();
echo $output->heading($pagetitle);

$data = $form->get_data();
if ($data) {
    // Save the changes and continue back to the manage page.
    // Massage the editor data.
    $data->descriptionformat = $data->description['format'];
    $data->description = $data->description['text'];
    if (empty($data->id)) {
        // Create new framework.
        require_sesskey();
        \tool_lp\api::create_competency($data);
        echo $output->notification(get_string('competencycreated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button(new moodle_url('/admin/tool/lp/competencies.php',
                array('competencyframeworkid' => $competencyframeworkid)));
    } else {
        require_sesskey();
        \tool_lp\api::update_competency($data);
        echo $output->notification(get_string('competencyupdated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button(new moodle_url('/admin/tool/lp/competencies.php',
                array('competencyframeworkid' => $competencyframeworkid)));
    }
} else {
    $form->display();
}

echo $output->footer();
