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
 * @copyright 2015 Itamar Tzadok <itamar@substantialmethods.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once('itemsform.php');

$urlparams = new stdClass;
$urlparams->d = optional_param('d', 0, PARAM_INT);
$urlparams->id = optional_param('id', 0, PARAM_INT);

// Must be enabled by admin.
if (!$CFG->dataform_multigradeitems) {
    if ($urlparams->d) {
        $params = array('d' => $urlparams->d);
    } else {
        $params = array('id' => $urlparams->id);
    }
    redirect(new moodle_url('/mod/dataform/view.php', $params));
}

// Set a dataform object.
$df = mod_dataform_dataform::instance($urlparams->d, $urlparams->id);
$df->require_manage_permission('templates');

$df->set_page('grade/items', array('urlparams' => $urlparams));
$grademan = $df->grade_manager;

// Get the list of grade items for the activity.
$gradeitems = $grademan->grade_items;

$customdata = array(
    'dataformid' => $df->id,
    'gradeitems' => $gradeitems
);
$mform = new mod_dataform_grade_items_form($PAGE->url, $customdata);

// Process validated.
if ($data = $mform->get_data()) {
    if (empty($data->gradeitem)) {
        // Update the Dataform instance.
        // This will trigger the update_grade_item in the manager.
        $grademan->adjust_dataform_settings(null, array('deleted' => 1));
        redirect($PAGE->url);

    }

    // There are some items to add/update.
    $dataitems = $data->gradeitem;

    // First delete excessive items.
    if ($gradeitems) {
        $gradeitemscount = count($gradeitems);
        $dataitemscount = count($dataitems);
        if ($gradeitemscount > $dataitemscount) {
            for ($i = $dataitemscount; $i < $gradeitemscount; $i++) {
                $grademan->delete_grade_items($i);
            }
        }
    }

    // Add/update grade item from data.
    $itemnumber = 0;
    foreach ($dataitems as $key => $details) {
        $gradevar = "gradeitem[$key]";

        $gradedata = (object) array('grade' => $data->$gradevar);
        $gradeparams = $grademan->get_grade_item_params_from_data($gradedata);
        $details = array_merge($details, $gradeparams);

        // Update the grade item.
        $details['itemnumber'] = $itemnumber;
        $grademan->update_grade_item($itemnumber, $details);

        // Update instance settings (e.g. grade calc).
        $grademan->adjust_dataform_settings($itemnumber, $details);
        $itemnumber++;
    }

    redirect($PAGE->url);
}

// Activate navigation node.
navigation_node::override_active_url(new moodle_url('/mod/dataform/grade/items.php', array('id' => $df->cm->id)));

$output = $df->get_renderer();
$header = array(
    'tab' => '',
    'nonotifications' => true,
    'urlparams' => $urlparams
);
echo $output->header($header);

// Print heading.
$strgradeitemsin = get_string('gradeitemsin', 'dataform', $df->name);
echo $output->heading_with_help($strgradeitemsin, 'gradeitems', 'mod_dataform');

// Display form.
$mform->set_data($gradeitems);
$mform->display();

echo $output->footer();
