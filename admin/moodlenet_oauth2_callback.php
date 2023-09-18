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
 * MoodleNet callback.
 *
 * @package    core
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\moodlenet\moodlenet_client;
use core\oauth2\api;

require_once(__DIR__ . '/../config.php');
require_login();

// Parameters.
$issuerid = required_param('issuerid', PARAM_INT);
$error = optional_param('error', '', PARAM_RAW);
$message = optional_param('error_description', null, PARAM_RAW);

// Headers to make it not cacheable.
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/admin/moodlenet_oauth2_callback.php');
$PAGE->set_pagelayout('popup');

// Wait as long as it takes for this script to finish.
core_php_time_limit::raise();

$issuer = api::get_issuer($issuerid);
$returnurl = new moodle_url('/admin/moodlenet_oauth2_callback.php');
$returnurl->param('issuerid', $issuerid);
$returnurl->param('callback', 'yes');
$returnurl->param('sesskey', sesskey());
$oauthclient = api::get_user_oauth_client($issuer, $returnurl, moodlenet_client::API_SCOPE_CREATE_RESOURCE, true);
$oauthclient->is_logged_in(); // Will upgrade the auth code to a token.

echo $OUTPUT->header();
$PAGE->requires->js_call_amd('core/moodlenet/oauth2callback', 'init', [$error, $message]);
echo $OUTPUT->footer();
