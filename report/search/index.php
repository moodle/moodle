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
 * Global search report
 *
 * @package   report_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('reportsearch');

$pagetitle = get_string('pluginname', 'report_search');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);

if (\core_search\manager::is_global_search_enabled() === false) {
    $renderer = $PAGE->get_renderer('core_search');
    echo $renderer->render_search_disabled();
}

$renderer = $PAGE->get_renderer('report_search');
$search = \core_search\manager::instance();

// All enabled components.
$searchareas = $search->get_search_areas_list(true);

$mform = new \report_search\output\form(null, array('searchareas' => $searchareas));
if ($data = $mform->get_data()) {

    if (!empty($data->delete)) {
        if (!empty($data->all)) {
            $search->delete_index();
        } else {
            $anydelete = false;
            // We check that the component exist and is enabled.
            foreach ($searchareas as $areaid => $searcharea) {
                if (!empty($data->{$areaid})) {
                    $anydelete = true;
                    $search->delete_index($areaid);
                }
            }
        }

        if (!empty($data->all) || $anydelete) {
            echo $OUTPUT->notification(get_string('deleted', 'report_search'), 'notifysuccess');
        }
    }

    if (!empty($data->reindex)) {
        // Force full reindex. Quite heavy operation.
        $search->index(true);
        $search->optimize_index();
        echo $OUTPUT->notification(get_string('indexed', 'report_search'), 'notifysuccess');
    }
}

// After processing the form as config might change depending on the action.
$areasconfig = $search->get_areas_config($searchareas);

// Ensure that all search areas that we are going to display have config.
$missingareas = array_diff_key($searchareas, $areasconfig);
foreach ($missingareas as $searcharea) {
    $search->reset_config($searcharea->get_area_id());
}

echo $renderer->render_report($mform, $searchareas, $areasconfig);
echo $OUTPUT->footer();
