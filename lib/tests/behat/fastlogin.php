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
// phpcs:disable moodle.Files.RequireLogin.Missing
// phpcs:disable moodle.PHP.ForbiddenFunctions.Found

/**
 * Fast login end point for BEHAT TESTS ONLY.
 *
 * @package    theme_cfz
 * @author     Guy Thomas
 * @copyright  2021 Class Technologies Inc. {@link https://www.class.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__.'/../../../config.php');

$behatrunning = defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING;
if (!$behatrunning) {
    die;
}

$username = required_param('username', PARAM_ALPHANUMEXT);
// Note - with behat, the password is always the same as the username.
$password = $username;

$failurereason = null;
$user = authenticate_user_login($username, $password, true, $failurereason, false);
if ($failurereason) {
    error_log("Failed to login as behat step for $username with reason: " . $failurereason);
    throw new Exception($failurereason);
}
if (!complete_user_login($user)) {
    throw new Exception("Failed to login as behat step for $username");
}

$redirecturl = optional_param('redirecturl', null, PARAM_URL);
$redirecturl = $redirecturl ?? $CFG->wwwroot;

if (optional_param('forceeditmode', false, PARAM_INT)) {
    $sesskey = sesskey();
    $url = new moodle_url($redirecturl);
    $url->param('edit', 1);
    $url->param('sesskey', $sesskey);
    $redirecturl = $url.'';
}

redirect($redirecturl);
