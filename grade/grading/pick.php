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
 * Allows to choose a form from the list of available templates
 *
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/grade/grading/lib.php');
require_once($CFG->dirroot.'/grade/grading/pick_form.php');

$targetid   = required_param('targetid', PARAM_INT); // area we are coming from
$pick       = optional_param('pick', null, PARAM_INT); // create new form from this template
$remove     = optional_param('remove', null, PARAM_INT); // remove this template
$confirmed  = optional_param('confirmed', false, PARAM_BOOL); // is the action confirmed

// the manager of the target area
$targetmanager = get_grading_manager($targetid);

if ($targetmanager->get_context()->contextlevel < CONTEXT_COURSE) {
    throw new coding_exception('Unsupported gradable area context level');
}

// currently active method in the target area
$method = $targetmanager->get_active_method();
$targetcontroller = $targetmanager->get_controller($method);
$targetcontrollerclass = get_class($targetcontroller);

// make sure there is no such form defined in the target area
if ($targetcontroller->is_form_defined()) {
    throw new moodle_exception('target_defined', 'core_grading');
}

list($context, $course, $cm) = get_context_info_array($targetmanager->get_context()->id);

require_login($course, true, $cm);
require_capability('moodle/grade:managegradingforms', $context);

// user's capability in the templates bank
$canshare   = has_capability('moodle/grade:sharegradingforms', get_system_context());
$canmanage  = has_capability('moodle/grade:managesharedforms', get_system_context());

// setup the page
$PAGE->set_url(new moodle_url('/grade/grading/pick.php', array('targetid' => $targetid)));
navigation_node::override_active_url($targetmanager->get_management_url());
$PAGE->set_title(get_string('gradingmanagement', 'core_grading'));
$PAGE->set_heading(get_string('gradingmanagement', 'core_grading'));
$output = $PAGE->get_renderer('core_grading');

// process picking a template
if ($pick) {
    $sourceid = $DB->get_field('grading_definitions', 'areaid', array('id' => $pick), MUST_EXIST);
    $sourcemanager = get_grading_manager($sourceid);
    $sourcecontroller = $sourcemanager->get_controller($method);
    if (!$sourcecontroller->is_form_defined()) {
        throw new moodle_exception('form_definition_mismatch', 'core_grading');
    }
    $definition = $sourcecontroller->get_definition();
    if (!$confirmed) {
        echo $output->header();
        echo $output->confirm(get_string('templatepickconfirm', 'core_grading',array(
            'formname'  => s($definition->name),
            'component' => $targetmanager->get_component_title(),
            'area'      => $targetmanager->get_area_title())),
            new moodle_url($PAGE->url, array('pick' => $pick, 'confirmed' => 1)),
            $PAGE->url);
        echo $output->box($sourcecontroller->render_preview($PAGE), 'template-preview-confirm');
        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $targetcontroller->update_definition($sourcecontroller->get_definition_copy($targetcontroller),
            gradingform_controller::DEFINITION_STATUS_READY);
        $DB->set_field('grading_definitions', 'timecopied', time(), array('id' => $definition->id));
        redirect(new moodle_url('/grade/grading/manage.php', array('areaid' => $targetid)));
    }
}

// process removing a template
if ($remove) {
    $sourceid = $DB->get_field('grading_definitions', 'areaid', array('id' => $remove), MUST_EXIST);
    $sourcemanager = get_grading_manager($sourceid);
    $sourcecontroller = $sourcemanager->get_controller($method);
    if (!$sourcecontroller->is_form_defined()) {
        throw new moodle_exception('form_definition_mismatch', 'core_grading');
    }
    $definition = $sourcecontroller->get_definition();
    if ($canmanage or ($canshare and ($definition->usercreated == $USER->id))) {
        // ok, this user can drop the template
    } else {
        throw new moodle_exception('no_permission_to_remove_template', 'core_grading');
    }
    if (!$confirmed) {
        echo $output->header();
        echo $output->confirm(get_string('templatedeleteconfirm', 'core_grading', s($definition->name)),
            new moodle_url($PAGE->url, array('remove' => $remove, 'confirmed' => 1)),
            $PAGE->url);
        echo $output->box($sourcecontroller->render_preview($PAGE), 'template-preview-confirm');
        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $sourcecontroller->delete_definition();
        redirect($PAGE->url);
    }
}

$searchform = new grading_search_template_form($PAGE->url, null, 'GET', '', array('class' => 'templatesearchform'));

if ($searchdata = $searchform->get_data()) {
    $needle = $searchdata->needle;
    $searchform->set_data(array(
        'needle' => $needle,
    ));
} else {
    $needle = '';
}

// construct the SQL to find all matching templates
$sql = "SELECT DISTINCT gd.id, gd.areaid, gd.name, gd.description, gd.descriptionformat, gd.timecreated,
                        gd.usercreated, gd.timemodified, gd.usermodified
          FROM {grading_definitions} gd
          JOIN {grading_areas} ga ON (gd.areaid = ga.id)";
// join method-specific tables from the plugin scope
$sql .= $targetcontrollerclass::sql_search_from_tables('gd.id');

$sql .= " WHERE gd.method = ?
               AND ga.contextid = ?
               AND ga.component = 'core_grading'";

$params = array($method, get_system_context()->id);

$tokens = grading_manager::tokenize($needle);
if ($tokens) {
    $subsql = array();

    // search for any of the tokens in the definition name
    foreach ($tokens as $token) {
        $subsql[] = $DB->sql_like('gd.name', '?', false, false);
        $params[] = '%'.$DB->sql_like_escape($token).'%';
    }

    // search for any of the tokens in the definition description
    foreach ($tokens as $token) {
        $subsql[] = $DB->sql_like('gd.description', '?', false, false);
        $params[] = '%'.$DB->sql_like_escape($token).'%';
    }

    // search for the needle in method-specific tables
    foreach ($tokens as $token) {
        list($methodsql, $methodparams) = $targetcontrollerclass::sql_search_where($token);
        $subsql = array_merge($subsql, $methodsql);
        $params = array_merge($params, $methodparams);
    }

    $sql .= " AND ((" . join(")\n OR (", $subsql) . "))";
}

$sql .= " ORDER BY gd.name";

$rs = $DB->get_recordset_sql($sql, $params);

echo $output->header();
$searchform->display();

$found = 0;
foreach ($rs as $template) {
    $found++;
    $out = '';
    $out .= $output->heading(s($template->name), 2, 'template-name');
    $manager = get_grading_manager($template->areaid);
    $controller = $manager->get_controller($method);
    $out .= $output->box($controller->render_preview($PAGE), 'template-preview');
    $actions = array($output->pick_action_icon(new moodle_url($PAGE->url, array('pick' => $template->id)),
        get_string('templatepick', 'core_grading'), 'i/tick_green_big', 'pick'));
    if ($canmanage or ($canshare and ($template->usercreated == $USER->id))) {
        //$actions[] = $output->pick_action_icon(new moodle_url($PAGE->url, array('edit' => $template->id)),
        //    get_string('templateedit', 'core_grading'), 'i/edit', 'edit');
        $actions[] = $output->pick_action_icon(new moodle_url($PAGE->url, array('remove' => $template->id)),
            get_string('templatedelete', 'core_grading'), 't/delete', 'remove');
    }
    $out .= $output->box(join(' ', $actions), 'template-actions');
    $out .= $output->box(format_text($template->description, $template->descriptionformat), 'template-description');

    // ideally we should highlight just the name, description and the fields
    // in the preview that were actually searched. to make our life easier, we
    // simply highlight the tokens everywhere they appear, even if that exact
    // piece was not searched.
    echo highlight(join(' ', $tokens), $out);
}
$rs->close();

if (!$found) {
    echo $output->heading(get_string('nosharedformfound', 'core_grading'));
}

echo $output->single_button(
    new moodle_url('/grade/grading/manage.php', array('areaid' => $targetid)),
    get_string('back'), 'get');

echo $output->footer();

////////////////////////////////////////////////////////////////////////////////


