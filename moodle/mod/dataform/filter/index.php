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
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

$urlparams = new stdClass;

$urlparams->d          = optional_param('d', 0, PARAM_INT);
$urlparams->id         = optional_param('id', 0, PARAM_INT);

// Filters list actions.
$urlparams->default    = optional_param('default', 0, PARAM_INT);  // id of filter to default
$urlparams->showhide    = optional_param('showhide', 0, PARAM_SEQUENCE);     // filter ids (comma delimited) to hide/show
$urlparams->delete     = optional_param('delete', 0, PARAM_SEQUENCE);   // filter ids (comma delim) to delete
$urlparams->duplicate  = optional_param('duplicate', 0, PARAM_SEQUENCE);   // filter ids (comma delim) to duplicate

$urlparams->confirmed  = optional_param('confirmed', 0, PARAM_INT);

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id);
$df->require_manage_permission('filters');

$df->set_page('filter/index', array('urlparams' => $urlparams));

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/filter/index.php', array('id' => $df->cm->id)));

$fm = mod_dataform_filter_manager::instance($df->id);

// DATA PROCESSING.
if ($urlparams->duplicate and confirm_sesskey()) {
    // Duplicate any requested filters.
    $fm->process_filters('duplicate', $urlparams->duplicate, $urlparams->confirmed);

} else if ($urlparams->delete and confirm_sesskey()) {
    // Delete any requested filters.
    $fm->process_filters('delete', $urlparams->delete, $urlparams->confirmed);

} else if ($urlparams->showhide and confirm_sesskey()) {
    // Set filter's visibility (confirmed by default).
    $fm->process_filters('visible', $urlparams->showhide, true);

} else if ($urlparams->default and confirm_sesskey()) {
    // Set filter to default.
    if ($urlparams->default == -1) {
        // Reset.
        $df->update((object) array('defaultfilter' => 0));
    } else {
        $df->update((object) array('defaultfilter' => $urlparams->default));
    }
}

// Any notifications?
if (!$filters = $fm->get_filters(null, false, true)) {
    $df->notifications = array('problem' => array('filtersnoneindataform' => get_string('filtersnoneindataform', 'dataform')));
}

$output = $df->get_renderer();
echo $output->header(array('tab' => 'filters', 'heading' => $df->name, 'urlparams' => $urlparams));

// Display add new filter link.
echo $output->add_filter_link();

// Display admin style list of filters.
echo $output->filters_admin_list();

echo $output->footer();
