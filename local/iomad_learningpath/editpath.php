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
 * Management page for Iomad Learning Paths
 *
 * @package    local_iomadlearninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Security
$context = context_system::instance();
require_login();
iomad::require_capability('local/iomad_learningpath:manage', $context);

// Parameters
$id = optional_param('id', 0, PARAM_INT);

// Page boilerplate stuff.
$url = new moodle_url('/local/iomad_learningpath/editpath.php', array('id' => $id));
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('managetitle', 'local_iomad_learningpath'));
$PAGE->set_heading(get_string('learningpathedit', 'local_iomad_learningpath'));
$output = $PAGE->get_renderer('local_iomad_learningpath');

// IOMAD stuff
$companyid = iomad::get_my_companyid($context);
$companypaths = new local_iomad_learningpath\companypaths($companyid, $context);
$paths = $companypaths->get_paths();

// Attempt to locate path
$path = $companypaths->get_path($id);

// Set up picture draft area
$picturedraftid = file_get_submitted_draft_itemid('picture');
file_prepare_draft_area($picturedraftid, $context->id, 'local_iomad_learningpath', 'picture', $id,
    ['maxfiles' => 1]);

// Form
$form = new local_iomad_learningpath\forms\editpath_form();

// Handle form activity.
$exiturl = new moodle_url('/local/iomad_learningpath/manage.php');
if ($form->is_cancelled()) {

    redirect($exiturl);

} else if ($data = $form->get_data()) {
    $path->name = $data->name;
    $path->description = $data->description['text'];
    $path->active = $data->active;
    $path->timeupdated = time();
    if ($id == 0) {
        $path->timecreated = time();
        $path->active = 0;
        $id = $DB->insert_record('local_iomad_learningpath', $path);
    } else {
        $DB->update_record('local_iomad_learningpath', $path);
    }
    file_save_draft_area_files($data->picture, $context->id, 'local_iomad_learningpath', 'picture', $id,
        ['maxfiles' => 1]);

    // Resize image and create thumbnail
    $companypaths->process_image($context, $id);

    redirect($exiturl);
}

$path->description = ['text' => $path->description];
$path->picture = $picturedraftid;
$form->set_data($path);

// Get renderer for page (and pass data).
$editpath_page = new local_iomad_learningpath\output\editpath_page($form);

echo $OUTPUT->header();

echo $output->render($editpath_page);

echo $OUTPUT->footer();
