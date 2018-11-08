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
 * Prints the contact form to the site's Data Protection Officer
 *
 * @copyright 2018 onwards Jun Pataleta
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tool_dataprivacy
 */

require_once("../../../config.php");
require_once('lib.php');

require_login(null, false);

$perpage = optional_param('perpage', 0, PARAM_INT);

$url = new moodle_url('/admin/tool/dataprivacy/datarequests.php');

$title = get_string('datarequests', 'tool_dataprivacy');

\tool_dataprivacy\page_helper::setup($url, $title, '', 'tool/dataprivacy:managedatarequests');

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

if (\tool_dataprivacy\api::is_site_dpo($USER->id)) {
    $filtersapplied = optional_param_array('request-filters', [-1], PARAM_NOTAGS);
    $filterscleared = optional_param('filters-cleared', 0, PARAM_INT);
    if ($filtersapplied === [-1]) {
        // If there are no filters submitted, check if there is a saved filters from the user preferences.
        $filterprefs = get_user_preferences(\tool_dataprivacy\local\helper::PREF_REQUEST_FILTERS, null);
        if ($filterprefs && empty($filterscleared)) {
            $filtersapplied = json_decode($filterprefs);
        } else {
            $filtersapplied = [];
        }
    }
    // Save the current applied filters to the user preferences.
    set_user_preference(\tool_dataprivacy\local\helper::PREF_REQUEST_FILTERS, json_encode($filtersapplied));

    $types = [];
    $statuses = [];
    $creationmethods = [];
    foreach ($filtersapplied as $filter) {
        list($category, $value) = explode(':', $filter);
        switch($category) {
            case \tool_dataprivacy\local\helper::FILTER_TYPE:
                $types[] = $value;
                break;
            case \tool_dataprivacy\local\helper::FILTER_STATUS:
                $statuses[] = $value;
                break;
            case \tool_dataprivacy\local\helper::FILTER_CREATION:
                $creationmethods[] = $value;
                break;
        }
    }

    $table = new \tool_dataprivacy\output\data_requests_table(0, $statuses, $types, $creationmethods, true);
    if (!empty($perpage)) {
        set_user_preference(\tool_dataprivacy\local\helper::PREF_REQUEST_PERPAGE, $perpage);
    } else {
        $prefperpage = get_user_preferences(\tool_dataprivacy\local\helper::PREF_REQUEST_PERPAGE);
        $perpage = ($prefperpage) ? $prefperpage : $table->get_requests_per_page_options()[0];
    }
    $table->set_requests_per_page($perpage);
    $table->baseurl = $url;

    $requestlist = new tool_dataprivacy\output\data_requests_page($table, $filtersapplied);
    $requestlistoutput = $PAGE->get_renderer('tool_dataprivacy');
    echo $requestlistoutput->render($requestlist);
} else {
    $dponamestring = implode (', ', tool_dataprivacy\api::get_dpo_role_names());
    $message = get_string('privacyofficeronly', 'tool_dataprivacy', $dponamestring);
    echo $OUTPUT->notification($message, 'error');
}

echo $OUTPUT->footer();
