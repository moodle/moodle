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
 * Group page for Iomad Learning Paths
 *
 * @package    local_iomadlearninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Security
require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

iomad::require_capability('local/iomad_learningpath:manage', $companycontext);

// Parameters
$learningpath = required_param('learningpath', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

// Page boilerplate stuff.
$url = new moodle_url('/local/iomad_learningpath/editgroup.php', ['id' => $id, 'learningpath' => $learningpath]);
$exiturl = new moodle_url('/local/iomad_learningpath/courselist.php', ['id' => $learningpath]);
$PAGE->set_context($companycontext);
$PAGE->set_url($url);
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('grouptitle', 'local_iomad_learningpath'));
$PAGE->set_heading(get_string('grouptitle', 'local_iomad_learningpath'));
$output = $PAGE->get_renderer('local_iomad_learningpath');

// IOMAD stuff
$companypaths = new local_iomad_learningpath\companypaths($companyid, $systemcontext);
$paths = $companypaths->get_paths();
$PAGE->navbar->add(get_string('grouptitle', 'local_iomad_learningpath'), $url);

// Attempt to locate path
$path = $companypaths->get_path($learningpath);

// Get/create group
$group = $companypaths->get_group($learningpath, $id);

// Delete?
if ($delete) {
    $companypaths->delete_group($learningpath, $delete);
    redirect($exiturl);
}

// Form
$form = new local_iomad_learningpath\forms\editgroup_form();

// Handle form activity.
if ($form->is_cancelled()) {

    redirect($exiturl);

} else if ($data = $form->get_data()) {
    $group->name = $data->name;
    $group->sequence = $data->sequence;
    if ($id == 0) {
        $id = $DB->insert_record('iomad_learningpathgroup', $group);
    } else {
        $DB->update_record('iomad_learningpathgroup', $group);
    }

    redirect($exiturl);
}

$form->set_data($group);

// Get renderer for page (and pass data).
$editgroup_page = new local_iomad_learningpath\output\editgroup_page($companypaths, $form);

echo $OUTPUT->header();

echo $output->render($editgroup_page);

echo $OUTPUT->footer();