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
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/auth.php');
require_once(__DIR__.'/lib.php');

// IOMAD
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
$companyid = iomad::get_my_companyid(context_system::instance(), false);
if (!empty($companyid)) {
    $postfix = "_$companyid";
} else {
    $postfix = "";
}

require_login();

$action = optional_param('action', null, PARAM_TEXT);

$iomadoidctoken = $DB->get_record('auth_iomadoidc_token', ['userid' => $USER->id]);
$iomadoidcconnected = (!empty($iomadoidctoken)) ? true : false;

$iomadoidcloginconnected = ($USER->auth === 'iomadoidc') ? true : false;

if (!empty($action)) {
    if ($action === 'connectlogin' && $iomadoidcloginconnected === false) {
        // Use authorization request login flow to connect existing users.
        if (!is_enabled_auth('iomadoidc')) {
            throw new \moodle_exception('erroriomadoidcnotenabled', 'auth_iomadoidc');
        }
        auth_iomadoidc_connectioncapability($USER->id, 'connect', true);
        $auth = new \auth_iomadoidc\loginflow\authcode;
        $auth->set_httpclient(new \auth_iomadoidc\httpclient());
        $auth->initiateauthrequest();
    } else if ($action === 'disconnectlogin' && $iomadoidcloginconnected === true) {
        if (is_enabled_auth('manual') === true) {
            auth_iomadoidc_connectioncapability($USER->id, 'disconnect', true);
            $auth = new \auth_plugin_iomadoidc;
            $auth->set_httpclient(new \auth_iomadoidc\httpclient());
            $auth->disconnect();
        }
    } else {
        throw new \moodle_exception('errorucpinvalidaction', 'auth_iomadoidc');
    }
} else {
    $PAGE->set_url('/auth/iomadoidc/ucp.php');
    $usercontext = \context_user::instance($USER->id);
    $PAGE->set_context(\context_system::instance());
    $PAGE->set_pagelayout('standard');
    $USER->editing = false;
    $authconfig = get_config('auth_iomadoidc');
    $confname = "opname$postfix";
    $opname = (!empty($authconfig->$configname)) ? $authconfig->$configname : get_string('pluginname', 'auth_iomadoidc');

    $ucptitle = get_string('ucp_title', 'auth_iomadoidc', $opname);
    $PAGE->navbar->add($ucptitle, $PAGE->url);
    $PAGE->set_title($ucptitle);

    echo $OUTPUT->header();
    echo \html_writer::tag('h2', $ucptitle);
    echo get_string('ucp_general_intro', 'auth_iomadoidc', $opname);
    echo '<br /><br />';

    if (optional_param('o365accountconnected', null, PARAM_TEXT) == 'true') {
        echo \html_writer::start_div('connectionstatus alert alert-error');
        echo \html_writer::tag('h5', get_string('ucp_o365accountconnected', 'auth_iomadoidc'));
        echo \html_writer::end_div();
    }

    // Login status.
    echo \html_writer::start_div('auth_iomadoidc_ucp_indicator');
    echo \html_writer::tag('h4', get_string('ucp_login_status', 'auth_iomadoidc', $opname));
    if ($iomadoidcloginconnected === true) {
        echo \html_writer::tag('h4', get_string('ucp_status_enabled', 'auth_iomadoidc'), ['class' => 'notifysuccess']);
        if (is_enabled_auth('manual') === true) {
            if (auth_iomadoidc_connectioncapability($USER->id, 'disconnect')) {
                $connectlinkuri = new \moodle_url('/auth/iomadoidc/ucp.php', ['action' => 'disconnectlogin']);
                $strdisconnect = get_string('ucp_login_stop', 'auth_iomadoidc', $opname);
                $linkhtml = \html_writer::link($connectlinkuri, $strdisconnect);
                echo \html_writer::tag('h5', $linkhtml);
                echo \html_writer::span(get_string('ucp_login_stop_desc', 'auth_iomadoidc', $opname));
            }
        }
    } else {
        echo \html_writer::tag('h4', get_string('ucp_status_disabled', 'auth_iomadoidc'), ['class' => 'notifyproblem']);
        if (auth_iomadoidc_connectioncapability($USER->id, 'connect')) {
            $connectlinkuri = new \moodle_url('/auth/iomadoidc/ucp.php', ['action' => 'connectlogin']);
            $linkhtml = \html_writer::link($connectlinkuri, get_string('ucp_login_start', 'auth_iomadoidc', $opname));
            echo \html_writer::tag('h5', $linkhtml);
            echo \html_writer::span(get_string('ucp_login_start_desc', 'auth_iomadoidc', $opname));
        }
    }
    echo \html_writer::end_div();

    echo $OUTPUT->footer();
}
