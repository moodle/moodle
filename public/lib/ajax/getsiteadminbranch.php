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
 * This file is used to deliver a branch from the site administration
 * in XML format back to a page from an AJAX call
 *
 * @since Moodle 2.6
 * @package core
 * @copyright 2013 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

// This should be accessed by only valid logged in user.
require_login(null, false);

// This identifies the type of the branch we want to get. Make sure it's SITE_ADMIN.
$branchtype = required_param('type', PARAM_INT);
if ($branchtype !== navigation_node::TYPE_SITE_ADMIN) {
    throw new coding_exception('Incorrect node type passed');
}

// Start capturing output in case of broken plugins.
\core\ajax::capture_output();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/ajax/getsiteadminbranch.php', array('type'=>$branchtype));

$sitenavigation = new settings_navigation_ajax($PAGE);

// Convert and output the branch as JSON.
$converter = new navigation_json();
$branch = $sitenavigation->get('root');

\core\ajax::check_captured_output();
echo $converter->convert($branch);
