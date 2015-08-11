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

$title = get_string('competencies', 'tool_lp');
$id = optional_param('id', 0, PARAM_INT);
$competencyframeworkid = required_param('competencyframeworkid', PARAM_INT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to the context we came from.
$parentid = optional_param('parentid', 0, PARAM_INT);

require_login();
$pagecontext = context::instance_by_id($pagecontextid);

if (empty($id)) {
    $pagetitle = get_string('addnewcompetency', 'tool_lp');
} else {
    $pagetitle = get_string('editcompetency', 'tool_lp');
}

// Set up the page.
$url = new moodle_url("/admin/tool/lp/editcompetency.php", array('id' => $id, 'competencyframeworkid' => $competencyframeworkid,
    'parentid' => $parentid, 'pagecontextid' => $pagecontextid));
$frameworksurl = new moodle_url('/admin/tool/lp/competencyframeworks.php', array('pagecontextid' => $pagecontextid));
$frameworkurl = new moodle_url('/admin/tool/lp/competencies.php', array('competencyframeworkid' => $competencyframeworkid,
    'pagecontextid' => $pagecontextid));

$competencyframework = \tool_lp\api::read_framework($competencyframeworkid);
$parent = null;
if ($parentid) {
    $parent = \tool_lp\api::read_competency($parentid);
}

$PAGE->navigation->override_active_url($frameworksurl);
$PAGE->set_context($pagecontext);
$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($competencyframework->get_shortname(), $frameworkurl);
$output = $PAGE->get_renderer('tool_lp');

$form = new \tool_lp\form\competency($url->out(false), array('id' => $id, 'competencyframework' => $competencyframework,
    'parent' => $parent));

if ($form->is_cancelled()) {
    redirect($frameworkurl);
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
        echo $output->continue_button($frameworkurl);
    } else {
        require_sesskey();
        \tool_lp\api::update_competency($data);
        echo $output->notification(get_string('competencyupdated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button($frameworkurl);
    }
} else {
    $form->display();
}

echo $output->footer();
