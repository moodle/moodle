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
 * View current document policy version.
 *
 * Script parameters:
 *  versionid=<int> Policy version id, defaults to the current one.
 *  policyid=<int> Policy document id, defaults to the one matching the version.
 *  returnurl=<local url> URL to continue to after reading the policy document.
 *  behalfid=<id> The user id to view the policy version as (such as child's id).
 *  manage=<bool> View the policy as a part of the management UI (managedocs.php).
 *
 * @package     tool_policy
 * @copyright   2018 Sara Arjona (sara@moodle.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_policy\api;
use tool_policy\output\page_viewdoc;

// Do not check for the site policies in require_login() to avoid the redirect loop.
define('NO_SITEPOLICY_CHECK', true);

// See the {@see page_viewdoc} for the access control checks.
require(__DIR__.'/../../../config.php'); // phpcs:ignore

$versionid = optional_param('versionid', null, PARAM_INT);
$policyid = $versionid ? optional_param('policyid', null, PARAM_INT) : required_param('policyid', PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);
$behalfid = optional_param('behalfid', null, PARAM_INT);
$manage = optional_param('manage', false, PARAM_BOOL);
$numpolicy = optional_param('numpolicy', null, PARAM_INT);
$totalpolicies = optional_param('totalpolicies', null, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$viewpage = new page_viewdoc($policyid, $versionid, $returnurl, $behalfid, $manage, $numpolicy, $totalpolicies);

$output = $PAGE->get_renderer('tool_policy');

echo $output->header();
echo $output->render($viewpage);
echo $output->footer();
