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
 * This page contains the SSO login page.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2018 onwards Microsoft, Inc. (http://microsoft.com/)
 */

require_once(__DIR__ . '/../../config.php');

echo "<script src=\"https://statics.teams.microsoft.com/sdk/v1.9.0/js/MicrosoftTeams.min.js\" crossorigin=\"anonymous\"></script>";
echo "<script src=\"https://secure.aadcdn.microsoftonline-p.com/lib/1.0.17/js/adal.min.js\" crossorigin=\"anonymous\"></script>";

$js = '
microsoftTeams.initialize();

// ADAL.js configuration
let config = {
    clientId: "' . get_config('auth_oidc', 'clientid') . '",
    redirectUri: "' . $CFG->wwwroot . '/local/o365/sso_end.php",
    cacheLocation: "localStorage",
    navigateToLoginRequestUrl: false,
};

let authContext = new AuthenticationContext(config);

if (authContext.isCallback(window.location.hash)) {
    authContext.handleWindowCallback(window.location.hash);
    if (authContext.getCachedUser()) {
        microsoftTeams.authentication.notifySuccess();
    } else {
        microsoftTeams.authentication.notifyFailure(authContext.getLoginError());
    }
}
';

echo html_writer::script($js);
