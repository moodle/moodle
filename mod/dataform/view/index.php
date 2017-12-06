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
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once("$CFG->libdir/tablelib.php");

$urlparams = new stdClass;

$urlparams->d = optional_param('d', 0, PARAM_INT);             // Dataform id.
$urlparams->id = optional_param('id', 0, PARAM_INT);           // Course module id.
$urlparams->vedit = optional_param('vedit', 0, PARAM_INT);     // View id to edit.

// List actions.
$urlparams->default = optional_param('default', 0, PARAM_INT);  // Id of view to default.
$urlparams->visible = optional_param('visible', 0, PARAM_INT);     // Id of view whose visibility to change.
$urlparams->visibility = optional_param('visibility', 0, PARAM_INT);     // Visibility mode.
$urlparams->reset = optional_param('reset', 0, PARAM_SEQUENCE);   // Ids (comma delimited) of views to delete.
$urlparams->delete = optional_param('delete', 0, PARAM_SEQUENCE);   // Ids (comma delimited) of views to delete.
$urlparams->duplicate = optional_param('duplicate', 0, PARAM_SEQUENCE);   // Ids (comma delimited) of views to duplicate.
$urlparams->setfilter = optional_param('setfilter', 0, PARAM_INT);  // Id of view to filter.

$urlparams->confirmed = optional_param('confirmed', 0, PARAM_INT);

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id);
$df->require_manage_permission('views');

$df->set_page('view/index', array('urlparams' => $urlparams));
$PAGE->set_context($df->context);

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/view/index.php', array('id' => $df->cm->id)));

// DATA PROCESSING.
$viewman = $df->view_manager;
if ($urlparams->duplicate and confirm_sesskey()) {
    // Duplicate any requested views.
    $viewman->process_views('duplicate', $urlparams->duplicate, null, $urlparams->confirmed);

} else if ($urlparams->reset and confirm_sesskey()) {
    // Reset to default any requested views.
    $viewman->process_views('reset', $urlparams->reset, null, true);

} else if ($urlparams->delete and confirm_sesskey()) {
    // Delete any requested views.
    $viewman->process_views('delete', $urlparams->delete, null, $urlparams->confirmed);

} else if ($urlparams->visible and confirm_sesskey()) {
    // Set view's visibility (confirmed by default).
    $viewman->process_views('visible', $urlparams->visible, array('visibility' => $urlparams->visibility), true);

} else if ($urlparams->default and confirm_sesskey()) {
    // Set view to default (confirmed by default).
    $viewman->process_views('default', $urlparams->default, null, true);

} else if ($urlparams->setfilter and confirm_sesskey()) {
    // Re/set view filter (confirmed by default).
    $viewman->process_views('filter', $urlparams->setfilter, null, true);
}

// Any notifications?
$df->notifications = array('problem' => array('defaultview' => null));
if (!$views = $viewman->get_views(array('forceget' => true, 'sort' => 'name'))) {
    $df->notifications = array('problem' => array('getstartedviews' => get_string('viewnoneindataform', 'dataform')));
} else if (!$df->defaultview) {
    $df->notifications = array('problem' => array('defaultview' => get_string('viewnodefault', 'dataform', '')));
}

$output = $df->get_renderer();
echo $output->header(array('tab' => 'views', 'heading' => $df->name, 'urlparams' => $urlparams));

// Try cleanup first.
if ($patternscleanup = optional_param('patternscleanup', 0, PARAM_INT)) {
    mod_dataform_view_manager::patterns_cleanup($df->id, $patternscleanup);
}

// If not cleaning patterns, display view list.
if (!$patternscleanup) {
    // Display subplugin selector.
    echo $output->subplugin_select('view');

    // Print admin style list of views.
    echo $output->views_admin_list(null, $views);
}

echo $output->footer();
