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
 * Show a user the policy documents to be agreed to.
 *
 * Script parameters:
 *  listdoc=<array> List of policy version ids that were displayed to the user to accept.
 *  statusXX=<int> Acceptance status to be set for the policy version with id XX.
 *  behalfid=<id> The user id to view the policy version as (such as child's id).
 *
 * @package     tool_policy
 * @copyright   2018 Sara Arjona (sara@moodle.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_policy\api;
use tool_policy\output\page_agreedocs;

// Do not check for the site policies in require_login() to avoid the redirect loop.
define('NO_SITEPOLICY_CHECK', true);

// @codingStandardsIgnoreLine See the {@link page_agreedocs} for the access control checks.
require(__DIR__.'/../../../config.php');

$submit = optional_param('submit', null, PARAM_NOTAGS);
$cancel = optional_param('cancel', null, PARAM_NOTAGS);
$listdocs = optional_param_array('listdoc', [], PARAM_INT);
$behalfid = optional_param('userid', null, PARAM_INT);

$agreedocs = [];
$declinedocs = [];

foreach ($listdocs as $pvid) {
    $status = optional_param('status'.$pvid, null, PARAM_INT);
    if ($status === 1) {
        $agreedocs[] = $pvid;
    } else if ($status === 0) {
        $declinedocs[] = $pvid;
    }
}

$listdocs = array_values(array_unique($listdocs));
$agreedocs = array_values(array_unique($agreedocs));
$declinedocs = array_values(array_unique($declinedocs));

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/admin/tool/policy/index.php');
$PAGE->set_popup_notification_allowed(false);

if (array_diff($agreedocs, $listdocs) || array_diff($declinedocs, $listdocs)) {
    throw new moodle_exception('invalidaccessparameter');
}

if (isloggedin() && !isguestuser()) {
    // Existing user.
    $haspermissionagreedocs = api::can_accept_policies($listdocs, $behalfid);
} else {
    // New user.
    $haspermissionagreedocs = true;
}

if (!$haspermissionagreedocs) {
    $outputpage = new \tool_policy\output\page_nopermission($listdocs, $behalfid);
} else if ($cancel) {
    redirect(new moodle_url('/'));
} else {
    if (!$behalfid && \core\session\manager::is_loggedinas()) {
        $behalfid = $USER->id;
    }
    $outputpage = new \tool_policy\output\page_agreedocs($listdocs, $agreedocs, $declinedocs, $behalfid, $submit);
}

$output = $PAGE->get_renderer('tool_policy');

echo $output->header();
echo $output->render($outputpage);
echo $output->footer();
