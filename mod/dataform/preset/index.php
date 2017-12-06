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
 * @package mod_dataform
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

$urlparams = new stdClass;

$urlparams->d = optional_param('d', 0, PARAM_INT);
$urlparams->id = optional_param('id', 0, PARAM_INT);

// Presets list actions.
$urlparams->apply = optional_param('apply', 0, PARAM_INT);  // path of preset to apply
$urlparams->torestorer = optional_param('torestorer', 1, PARAM_INT);  // apply user data to restorer
$urlparams->map = optional_param('map', 0, PARAM_BOOL);  // map new preset fields to old fields
$urlparams->delete = optional_param('delete', '', PARAM_SEQUENCE);   // ids of presets to delete
$urlparams->share = optional_param('share', '', PARAM_SEQUENCE);     // ids of presets to share
$urlparams->download = optional_param('download', '', PARAM_SEQUENCE);     // ids of presets to download in one zip

$urlparams->confirmed = optional_param('confirmed', 0, PARAM_INT);

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id);
$df->require_manage_permission('presets');

$df->set_page('preset/index', array('urlparams' => $urlparams));
$PAGE->set_context($df->context);

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/preset/index.php', array('id' => $df->cm->id)));

$pm = mod_dataform_preset_manager::instance($df->id);

// DATA PROCESSING.
$pm->process_presets($urlparams);

$output = $df->get_renderer();
echo $output->header(array('tab' => 'presets', 'heading' => $df->name, 'urlparams' => $urlparams));

// Print the preset form.
$pm->print_preset_form();

// Print admin style list of course presets.
$presets = $pm->get_user_presets($pm::PRESET_COURSEAREA);
echo html_writer::tag('h4', get_string('presetavailableincourse', 'dataform'));
echo $pm->get_course_presets_list($presets);

// Print admin style list of site presets.
$presets = $pm->get_user_presets($pm::PRESET_SITEAREA);
echo html_writer::tag('h4', get_string('presetavailableinsite', 'dataform'));
echo $pm->get_site_presets_list($presets);

echo $output->footer();
