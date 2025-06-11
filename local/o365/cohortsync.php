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
 * A page to manage Microsoft group and Moodle cohort mapping.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_once($CFG->libdir . '/adminlib.php');

use local_o365\feature\cohortsync\main;
use local_o365\form\cohortsync;

require_login();
require_capability('moodle/site:config', context_system::instance());

$pageurl = new moodle_url('/local/o365/cohortsync.php');

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('cohortsync_title', 'local_o365'));
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('cohortsync_title', 'local_o365'));

$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->set_secondary_active_tab('modules');

$PAGE->navbar->add(get_string('administrationsite'), new moodle_url('/admin/search.php'));
$PAGE->navbar->add(get_string('localplugins'), new moodle_url('/admin/category.php', ['category' => 'localplugins']));
$PAGE->navbar->add(get_string('pluginname', 'local_o365'), new moodle_url('/admin/settings.php', ['section' => 'local_o365']));
$PAGE->navbar->add(get_string('settings_cohortsync_title', 'local_o365'), new moodle_url('/local/o365/cohortsync.php'));

$apiclient = main::get_unified_api(__METHOD__);
if (empty($apiclient)) {
    throw new moodle_exception('cohortsync_unifiedapierror', 'local_o365');
}
$cohortsyncmain = new main($apiclient);
$cohortsyncmain->fetch_groups_from_cache();

$cohortsyncform = new cohortsync(null, ['cohortsyncmain' => $cohortsyncmain]);

$action = optional_param('action', '', PARAM_ALPHA);
if ($action == 'delete') {
    $connectionid = required_param('connectionid', PARAM_INT);
    if (!$connectionrecord = $DB->get_record('local_o365_objects', ['id' => $connectionid])) {
        throw new moodle_exception('cohortsync_connectionnotfound', 'local_o365');
    }
    if ($connectionrecord->type != 'group' || $connectionrecord->subtype != 'cohort') {
        throw new moodle_exception('cohortsync_connectionnotcohortsync', 'local_o365');
    }

    $cohortsyncmain->delete_mapping_by_id($connectionid);

    redirect($pageurl, get_string('cohortsync_mappingdeleted', 'local_o365'));
}

if ($fromform = $cohortsyncform->get_data()) {
    $groupoid = $fromform->groupoid;
    $cohortid = $fromform->cohortid;

    if ($cohortsyncmain->add_mapping($groupoid, $cohortid)) {
        redirect($pageurl, get_string('cohortsync_mappingadded', 'local_o365'));
    } else {
        throw new moodle_exception('cohortsync_mappingfailed', 'local_o365');
    }
}

echo $OUTPUT->header();

$cohortsyncform->display();

echo $OUTPUT->footer();
