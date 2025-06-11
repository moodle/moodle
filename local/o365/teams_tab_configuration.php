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
 * This page allows a Microsoft Teams Tab to be configured.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2018 onwards Microsoft, Inc. (http://microsoft.com/)
 */

// phpcs:ignore moodle.Files.RequireLogin.Missing -- This file is called from Microsoft Teams tab.
require_once(__DIR__ . '/../../config.php');

$url = new moodle_url('/local/o365/teams_tab_configuration.php');

$PAGE->set_context(context_system::instance());

// Force a theme without navigation and block.
$customtheme = get_config('local_o365', 'customtheme');
if (!empty($customtheme) && get_config('theme_' . $customtheme, 'version')) {
    $SESSION->theme = $customtheme;
} else if (get_config('theme_boost_o365teams', 'version')) {
    $SESSION->theme = 'boost_o365teams';
}

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">";
echo "<script src=\"https://statics.teams.microsoft.com/sdk/v1.9.0/js/MicrosoftTeams.min.js\" crossorigin=\"anonymous\"></script>";
echo "<script src=\"https://secure.aadcdn.microsoftonline-p.com/lib/1.0.17/js/adal.min.js\" crossorigin=\"anonymous\"></script>";
echo "<script src=\"https://code.jquery.com/jquery-3.1.1.js\" crossorigin=\"anonymous\"></script>";

$redirecturl = new moodle_url('/local/o365/teams_tab_redirect.php');
$ssostarturl = new moodle_url('/local/o365/sso_start.php');
$oidcloginurl = new moodle_url('/auth/oidc/index.php');
$externalloginurl = new moodle_url('/login/index.php');
$ssologinurl = new moodle_url('/local/o365/sso_login.php');

$url->params(['sesskey' => sesskey()]);
$SESSION->wantsurl = $url;

// Output login pages.
echo html_writer::start_div('local_o365_manual_login');
// Microsoft Entra ID login box.
echo html_writer::tag('button', get_string('sso_login', 'local_o365'),
    ['onclick' => 'login()', 'class' => 'local_o365_manual_login_button']);
// Manual login link.
echo html_writer::tag('button', get_string('other_login', 'local_o365'),
    ['onclick' => 'otherLogin()', 'class' => 'local_o365_manual_login_button']);
echo html_writer::end_div();

$tenantid = get_config('local_o365', 'entratenantid');
if (!$tenantid) {
    $tenantid = 'common';
}

$js = "
microsoftTeams.initialize();

if (!inIframe() && !isMobileApp()) {
    window.location.href = '" . $redirecturl->out(false) . "';
}

let queryParams = getQueryParameters();
let loginHint = queryParams['loginHint'];
let tenantId = '{$tenantid}';

let config = {
    tenant: tenantId,
    clientId: '" . get_config('auth_oidc', 'clientid') . "',
    redirectUri: '" . $CFG->wwwroot . "/local/sso_end.php',
    cacheLocation: 'localStorage',
    navigateToLoginRequestUrl: false,
    extraQueryParameter: 'scope=openid+profile&login_hint=' + encodeURIComponent(loginHint),
};

window.onload = setTitles;

// Parse query parameters into key-value pairs
function getQueryParameters() {
    let queryParams = {};
    location.search.substr(1).split('&').forEach(function(item) {
        let s = item.split('='),
        k = s[0],
        v = s[1] && decodeURIComponent(s[1]);
        queryParams[k] = v;
    });
    return queryParams;
}

function setTitles() {
    var text;
    var id_course = document.getElementById('id_local_o365_teams_course');
    if (id_course) {
        var x = id_course.options.length;
        for (i = 0; i < x; i++) {
            text = id_course.options[i].text;
            id_course.options[i].title=text;
        }
    }
}

function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

/**
 * This is hacky check for access from Teams mobile app.
 * It only tells if the userAgent contains the key words.
 * Providing userAgent is not modified, this will tell if the visit is from a mobile device.
 * If a visitor visits teams web site from mobile browser, Teams will tell the visitor to download mobile app and prevent access
 * by default.
 * However, if the visitor enables 'mobile mode' or equivalent, the message can be bypassed, thus this check may fail.
 */
function isMobileApp() {
    if(/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        return true;
    } else {
        return false;
    }
}

function onCourseChange() {
    var course = document.getElementsByName('local_o365_teams_course[]')[0];
    var courseid = course.value;
    course.removeAttribute('multiple');

    var options = course.options;
    for (var i = 0; i < options.length; i++) {
        if (options[i].value != courseid) {
            options[i].selected = false;
        }
    }

    var tabname =  document.getElementsByName('local_o365_teams_tab_name')[0];
    var tabnamevalue = tabname.value;

    microsoftTeams.settings.setSettings({
        entityId: 'course_' + courseid,
        contentUrl: '" . $CFG->wwwroot . "/local/o365/teams_tab.php?id=' + courseid,
        websiteUrl: '" . $CFG->wwwroot . "/course/view.php?id=' + courseid,
        suggestedTabName: tabnamevalue,
    });
    microsoftTeams.settings.setValidityState(true);
}

function onTabNameChange() {
    var course = document.getElementsByName('local_o365_teams_course[]')[0];
    var courseid = course.value;

    var tabname =  document.getElementsByName('local_o365_teams_tab_name')[0];
    var tabnamevalue = tabname.value;

    microsoftTeams.settings.setSettings({
        entityId: 'course_' + courseid,
        contentUrl: '" . $CFG->wwwroot . "/local/o365/teams_tab.php?id=' + courseid,
        websiteUrl: '" . $CFG->wwwroot . "/course/view.php?id=' + courseid,
        suggestedTabName: tabnamevalue,
    });
}

function login() {
    microsoftTeams.authentication.authenticate({
        url: '" . $ssostarturl->out(false) . "',
        width: 600,
        height: 400,
        successCallback: function (result) {
            // AuthenticationContext is a singleton
            let authContext = new AuthenticationContext(config);
            let idToken = authContext.getCachedToken(config.clientId);

            if (idToken) {
                // login using the token
                window.location.href = '" . $oidcloginurl->out(false) . "';
            } else {
                console.error('Error getting cached id token. This should never happen.');
                // At this point sso login does not work. redirect to normal Moodle login page.
                window.location.href = '" . $externalloginurl->out(false) . "';
            };
        },
        failureCallback: function (reason) {
            console.log('Login failed: ' + reason);
            if (reason === 'CancelledByUser' || reason === 'FailedToOpenWindow') {
                console.log('Login was blocked by popup blocker or canceled by user.');
            }
            // At this point sso login does not work. redirect to normal Moodle login page.
            window.location.href = '" . $externalloginurl->out(false) . "';
        }
    });
}

function otherLogin() {
    window.location.href = '" . $externalloginurl->out(false) . "';
}

function ssoLogin() {
    var isloggedin = " . (int) ($USER->id != 0) . ";

    if (!isloggedin) {
        microsoftTeams.authentication.getAuthToken({
            successCallback: (result) => {
                const url = '" . $ssologinurl->out() . "';

                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type' : 'application/x-www-form-urlencoded',
                        'Authorization' : result
                    },
                    mode: 'cors',
                    cache: 'default'
                }).then((response) => {
                    if (response.status == 200) {
                        // Nothing to do here.
                        if (!window.location.hash) {
                            window.location = window.location + '#loaded';
                            window.location.reload();
                        }
                    } else {
                        // Manual login.
                        $('.local_o365_manual_login').css('display', 'block');
                        $('#local_o365_course_list').css('display', 'none');
                    }
                });
            },
            failureCallback: function (error) {
                // Manual login.
                $('.local_o365_manual_login').css('display', 'block');
                $('#local_o365_course_list').css('display', 'none');
            }
        });
    }
}

ssoLogin();
";

echo html_writer::script($js);

$form = new \local_o365\form\teamstabconfiguration(null, null, 'post', '', ['id' => 'local_o365_course_list']);
$form->display();
