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
 * View user acceptances to the policies
 *
 * @package     tool_policy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

use core\output\notification;

$policyid = optional_param('policyid', null, PARAM_INT);
$versionid = optional_param('versionid', null, PARAM_INT);
$versionid = optional_param('versionid', null, PARAM_INT);
$filtersapplied = optional_param_array('unified-filters', [], PARAM_NOTAGS);

$acceptancesfilter = new \tool_policy\output\acceptances_filter($policyid, $versionid, $filtersapplied);
$policyid = $acceptancesfilter->get_policy_id_filter();
$versionid = $acceptancesfilter->get_version_id_filter();

// Set up the page as an admin page 'tool_policy_managedocs'.
$urlparams = ($policyid ? ['policyid' => $policyid] : []) + ($versionid ? ['versionid' => $versionid] : []);
admin_externalpage_setup('tool_policy_acceptances', '', $urlparams,
    new moodle_url('/admin/tool/policy/acceptances.php'));

$acceptancesfilter->validate_ids();
$output = $PAGE->get_renderer('tool_policy');
if ($acceptancesfilter->get_versions()) {
    $acceptances = new \tool_policy\acceptances_table('tool_policy_user_acceptances', $acceptancesfilter, $output);
    if ($acceptances->is_downloading()) {
        $acceptances->download();
    }
}

echo $output->header();
echo $output->heading(get_string('useracceptances', 'tool_policy'));
echo $output->render($acceptancesfilter);
if (!empty($acceptances)) {
    $acceptances->display();
} else if ($acceptancesfilter->get_avaliable_policies()) {
    // There are no non-guest policies.
    echo $output->notification(get_string('selectpolicyandversion', 'tool_policy'), notification::NOTIFY_INFO);
} else {
    // There are no non-guest policies.
    echo $output->notification(get_string('nopolicies', 'tool_policy'), notification::NOTIFY_INFO);
}
echo $output->footer();
