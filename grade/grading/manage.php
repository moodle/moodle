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
 * A single gradable area management page
 *
 * This page alows the user to set the current active method in the given
 * area, provides access to the plugin editor and allows user to save the
 * current form as a template or re-use some existing form.
 *
 * @package    core_grades
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/grade/grading/lib.php');

// identify gradable area by its id
$areaid     = optional_param('areaid', null, PARAM_INT);
// alternatively the context, component and areaname must be provided
$contextid  = optional_param('contextid', null, PARAM_INT);
$component  = optional_param('component', null, PARAM_COMPONENT);
$area       = optional_param('area', null, PARAM_AREA);
// keep the caller's URL so that we know where to send the user finally
$returnurl  = optional_param('returnurl', null, PARAM_LOCALURL);
// active method selector
$setmethod  = optional_param('setmethod', null, PARAM_PLUGIN);
// publish the given form definition as a new template in the forms bank
$shareform  = optional_param('shareform', null, PARAM_INT);
// delete the given form definition
$deleteform = optional_param('deleteform', null, PARAM_INT);
// consider the required action as confirmed
$confirmed  = optional_param('confirmed', false, PARAM_BOOL);
// a message to display, typically a previous action's result
$message    = optional_param('message', null, PARAM_NOTAGS);

if (!is_null($areaid)) {
    // get manager by id
    $manager = get_grading_manager($areaid);
} else {
    // get manager by context and component
    if (is_null($contextid) or is_null($component) or is_null($area)) {
        throw new coding_exception('The caller script must identify the gradable area.');
    }
    $context = context::instance_by_id($contextid, MUST_EXIST);
    $manager = get_grading_manager($context, $component, $area);
}

if ($manager->get_context()->contextlevel < CONTEXT_COURSE) {
    throw new coding_exception('Unsupported gradable area context level');
}

// get the currently active method
$method = $manager->get_active_method();

list($context, $course, $cm) = get_context_info_array($manager->get_context()->id);

require_login($course, true, $cm);
require_capability('moodle/grade:managegradingforms', $context);

if (!empty($returnurl)) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = null;
}

$PAGE->set_url($manager->get_management_url($returnurl));
navigation_node::override_active_url($manager->get_management_url());
$PAGE->set_title(get_string('gradingmanagement', 'core_grading'));
$PAGE->set_heading(get_string('gradingmanagement', 'core_grading'));
$output = $PAGE->get_renderer('core_grading');

// process the eventual change of the active grading method
if (!empty($setmethod)) {
    require_sesskey();
    if ($setmethod == 'none') {
        // here we expect that noone would actually want to call their plugin as 'none'
        $setmethod = null;
    }
    $manager->set_active_method($setmethod);
    redirect($PAGE->url);
}

// publish the form as a template
if (!empty($shareform)) {
    require_capability('moodle/grade:sharegradingforms', context_system::instance());
    $controller = $manager->get_controller($method);
    $definition = $controller->get_definition();
    if (!$confirmed) {
        // let the user confirm they understand what they are doing (haha ;-)
        echo $output->header();
        echo $output->confirm(get_string('manageactionshareconfirm', 'core_grading', s($definition->name)),
            new moodle_url($PAGE->url, array('shareform' => $shareform, 'confirmed' => 1)),
            $PAGE->url);
        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $newareaid = $manager->create_shared_area($method);
        $targetarea = get_grading_manager($newareaid);
        $targetcontroller = $targetarea->get_controller($method);
        $targetcontroller->update_definition($controller->get_definition_copy($targetcontroller));
        $DB->set_field('grading_definitions', 'timecopied', time(), array('id' => $definition->id));
        redirect(new moodle_url($PAGE->url, array('message' => get_string('manageactionsharedone', 'core_grading'))));
    }
}

// delete the form definition
if (!empty($deleteform)) {
    $controller = $manager->get_controller($method);
    $definition = $controller->get_definition();
    if (!$confirmed) {
        // let the user confirm they understand the consequences (also known as WTF-effect)
        echo $output->header();
        echo $output->confirm(markdown_to_html(get_string('manageactiondeleteconfirm', 'core_grading', array(
            'formname'  => s($definition->name),
            'component' => $manager->get_component_title(),
            'area'      => $manager->get_area_title()))),
            new moodle_url($PAGE->url, array('deleteform' => $deleteform, 'confirmed' => 1)), $PAGE->url);
        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $controller->delete_definition();
        redirect(new moodle_url($PAGE->url, array('message' => get_string('manageactiondeletedone', 'core_grading'))));
    }
}

echo $output->header();

if (!empty($message)) {
    echo $output->management_message($message);
}

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
        $definition = $controller->get_definition();
        // icon to edit the form definition
        echo $output->management_action_icon($controller->get_editor_url($returnurl),
            get_string('manageactionedit', 'core_grading'), 'b/document-edit');
        // icon to delete the current form definition
        echo $output->management_action_icon(new moodle_url($PAGE->url, array('deleteform' => $definition->id)),
            get_string('manageactiondelete', 'core_grading'), 'b/edit-delete');
        // icon to save the form as a new template
        if (has_capability('moodle/grade:sharegradingforms', context_system::instance())) {
            if (empty($definition->copiedfromid)) {
                $hasoriginal = false;
            } else {
                $hasoriginal = $DB->record_exists('grading_definitions', array('id' => $definition->copiedfromid));
            }
            if (!$controller->is_form_available()) {
                // drafts can not be shared
                $allowshare = false;
            } else if (!$hasoriginal) {
                // was created from scratch or is orphaned
                if (empty($definition->timecopied)) {
                    // was never shared before
                    $allowshare = true;
                } else if ($definition->timemodified > $definition->timecopied) {
                    // was modified since last time shared
                    $allowshare = true;
                } else {
                    // was not modified since last time shared
                    $allowshare = false;
                }
            } else {
                // was created from a template and the template still exists
                if ($definition->timecreated == $definition->timemodified) {
                    // was not modified since created
                    $allowshare = false;
                } else if (empty($definition->timecopied)) {
                    // was modified but was not re-shared yet
                    $allowshare = true;
                } else if ($definition->timemodified > $definition->timecopied) {
                    // was modified since last time re-shared
                    $allowshare = true;
                } else {
                    // was not modified since last time re-shared
                    $allowshare = false;
                }
            }
            if ($allowshare) {
                echo $output->management_action_icon(new moodle_url($PAGE->url, array('shareform' => $definition->id)),
                    get_string('manageactionshare', 'core_grading'), 'b/bookmark-new');
            }
        }
    } else {
        echo $output->management_action_icon($controller->get_editor_url($returnurl),
            get_string('manageactionnew', 'core_grading'), 'b/document-new');
        $pickurl = new moodle_url('/grade/grading/pick.php', array('targetid' => $controller->get_areaid()));
        if (!is_null($returnurl)) {
            $pickurl->param('returnurl', $returnurl->out(false));
        }
        echo $output->management_action_icon($pickurl,
            get_string('manageactionclone', 'core_grading'), 'b/edit-copy');
    }
    echo $output->container_end();

    // display the message if the form is currently not available (if applicable)
    if ($message = $controller->form_unavailable_notification()) {
        echo $output->notification($message);
    }
    // display the grading form preview
    if ($controller->is_form_defined()) {
        if ($definition->status == gradingform_controller::DEFINITION_STATUS_READY) {
            $tag = html_writer::tag('span', get_string('statusready', 'core_grading'), array('class' => 'status ready'));
        } else {
            $tag = html_writer::tag('span', get_string('statusdraft', 'core_grading'), array('class' => 'status draft'));
        }
        echo $output->heading(s($definition->name) . ' ' . $tag, 3, 'definition-name');
        echo $output->box($controller->render_preview($PAGE), 'definition-preview');
    }
}


echo $output->footer();
