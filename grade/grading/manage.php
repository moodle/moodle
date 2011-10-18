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
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/grade/grading/lib.php');

$areaid         = optional_param('areaid', null, PARAM_INT);
$contextid      = optional_param('contextid', null, PARAM_INT);
$component      = optional_param('component', null, PARAM_COMPONENT);
$area           = optional_param('area', null, PARAM_AREA);
$returnurl      = optional_param('returnurl', null, PARAM_LOCALURL);
$activemethod   = optional_param('activemethod', null, PARAM_PLUGIN);

if (!is_null($areaid)) {
    // get manager by id
    $manager = get_grading_manager($areaid);
} else {
    // get manager by context and component
    if (is_null($contextid) or is_null($component) or is_null($area)) {
        throw new coding_exception('The caller script must identify the gradable area.');
    }
    $context = get_context_instance_by_id($contextid, MUST_EXIST);
    $manager = get_grading_manager($context, $component, $area);
}

// currently active method
$method = $manager->get_active_method();

list($context, $course, $cm) = get_context_info_array($manager->get_context()->id);

if (is_null($returnurl)) {
    $returnurl = new moodle_url('/course/view.php', array('id' => $course->id));
} else {
    $returnurl = new moodle_url($returnurl);
}

require_login($course, true, $cm);
require_capability('moodle/grade:managegradingforms', $context);

$PAGE->set_url($manager->get_management_url($returnurl));
navigation_node::override_active_url($manager->get_management_url());
$PAGE->set_title(get_string('gradingmanagement', 'core_grading'));
$PAGE->set_heading(get_string('gradingmanagement', 'core_grading'));
$output = $PAGE->get_renderer('core_grading');

// process the eventual change of the active grading method
if (!empty($activemethod)) {
    require_sesskey();
    if ($activemethod == 'none') {
        // here we expect that noone would actually want to call their plugin as 'none'
        $activemethod = null;
    }
    $manager->set_active_method($activemethod);
    redirect($PAGE->url);
}

echo $output->header();
echo $output->heading(get_string('gradingmanagementtitle', 'core_grading', array(
    'component' => $manager->get_component_title(), 'area' => $manager->get_area_title())));

// display the active grading method information and selector
echo $output->management_method_selector($manager, $PAGE->url);

// get the currently active method's controller
if (!empty($method)) {
    $controller = $manager->get_controller($method);
    // display relevant actions
    echo $output->container_start('actions');
    if ($controller->is_form_defined()) {
        echo $output->management_action_icon($controller->get_editor_url($returnurl),
            get_string('manageactionedit', 'core_grading'), 'b/document-properties');
        echo $output->management_action_icon($PAGE->url,
            get_string('manageactiondelete', 'core_grading'), 'b/edit-delete');
    } else {
        echo $output->management_action_icon($controller->get_editor_url($returnurl),
            get_string('manageactionnew', 'core_grading'), 'b/document-new');
        echo $output->management_action_icon($PAGE->url,
            get_string('manageactionclone', 'core_grading'), 'b/edit-copy');
    }
    echo $output->container_end();

    // display the grading form preview
    if ($controller->is_form_defined()) {
        echo $output->box($controller->render_preview($PAGE), 'preview');
    }
}


echo $output->footer();
