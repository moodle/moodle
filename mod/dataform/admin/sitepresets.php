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
 * @category admin
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once("$CFG->libdir/adminlib.php");

$urlparams = new stdClass;

// Presets list actions.
// Ids of presets to delete.
$urlparams->delete = optional_param('delete', '', PARAM_SEQUENCE);
// Ids of presets to download in one zip.
$urlparams->download = optional_param('download', '', PARAM_SEQUENCE);
$urlparams->confirmed = optional_param('confirmed', 0, PARAM_INT);

admin_externalpage_setup('moddataform_sitepresets');

$pm = new mod_dataform_preset_manager(0);

// DATA PROCESSING.
$pm->process_presets($urlparams);

echo $OUTPUT->header();

// Print the preset form.
$pm->print_preset_form();

// If there are presets print admin style list of them.
echo html_writer::tag('h4', get_string('presetavailableinsite', 'dataform'));
$presets = $pm->get_user_presets($pm::PRESET_SITEAREA);
echo $pm->get_site_presets_list($presets);

echo $OUTPUT->footer();
