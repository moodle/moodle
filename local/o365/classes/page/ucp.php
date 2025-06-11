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
 * User control panel page.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\page;

use html_writer;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/auth/oidc/lib.php');
require_once($CFG->dirroot.'/local/o365/lib.php');

/**
 * User control panel page.
 */
class ucp extends base {
    /** @var bool Whether the user is using o365 login. */
    protected $o365loginconnected = false;

    /** @var bool Whether the user is connected to o365 or not (has an active token). */
    protected $o365connected = false;

    /**
     * Run before the main page mode - determines connection status.
     *
     * @return bool Success/Failure.
     */
    public function header() {
        global $USER, $DB;
        $this->o365loginconnected = ($USER->auth === 'oidc') ? true : false;
        $this->o365connected = \local_o365\utils::is_o365_connected($USER->id);
        return true;
    }

    /**
     * Manage calendar syncing.
     *
     * @throws moodle_exception
     */
    public function mode_calendar() {
        global $DB, $USER, $OUTPUT, $PAGE, $SITE;

        $PAGE->navbar->add(get_string('ucp_calsync_title', 'local_o365'), new \moodle_url('/local/o365/ucp.php?action=calendar'));

        if (empty($this->o365connected)) {
            throw new moodle_exception('ucp_notconnected', 'local_o365');
        }

        $calsync = new \local_o365\feature\calsync\main();
        $o365calendars = $calsync->get_calendars();

        $customdata = [
            'o365calendarcheck' => false,
            'sitecalendarexists' => false,
            'o365calendars' => [],
            'usercourses' => enrol_get_my_courses(['id', 'fullname']),
            'cancreatesiteevents' => false,
            'cancreatecourseevents' => [],
        ];

        // Determine outlook calendar setting check.
        $usersetting = $DB->get_record('local_o365_calsettings', ['user_id' => $USER->id]);
        if (!empty($usersetting)) {
            $customdata['o365calendarcheck'] = true;
            $updatecalendar = true;
            foreach ($o365calendars as $o365calendar) {
                if ($o365calendar['name'] === $SITE->fullname) {
                    $updatecalendar = false;
                }
            }
            if ($updatecalendar) {
                // Update the existing calendar.
                $calsync->update_outlook_calendar($usersetting->o365calid, ['name' => $SITE->fullname]);
                $o365calendars = $calsync->get_calendars();
            }
        }

        foreach ($o365calendars as $o365calendar) {
            $customdata['o365calendars'][] = [
                'id' => $o365calendar['id'],
                'name' => $o365calendar['name'],
            ];
            if ($o365calendar['name'] === $SITE->fullname) {
                $customdata['sitecalendarexists'] = $o365calendar['id'];
            }
        }
        $primarycalid = $customdata['o365calendars'][0]['id'];

        // Determine permissions to create events. Determines whether user can sync from o365 to Moodle.
        $customdata['cancreatesiteevents'] = has_capability('moodle/calendar:manageentries', \context_course::instance(SITEID));
        foreach ($customdata['usercourses'] as $courseid => $course) {
            $cancreateincourse = has_capability('moodle/calendar:manageentries', \context_course::instance($courseid));
            $customdata['cancreatecourseevents'][$courseid] = $cancreateincourse;
        }

        $mform = new \local_o365\feature\calsync\form\subscriptions('?action=calendar', $customdata);
        $fromform = $mform->get_data();

        if (empty($customdata['sitecalendarexists']) && !empty($fromform->settingcal)) {
            // Create new outlook calendar with site name.
            $sitecalendar = $calsync->create_outlook_calendar($SITE->fullname);
            array_push($customdata['o365calendars'], ['id' => $sitecalendar['Id'], 'name' => $sitecalendar['Name']]);
        }

        if ($mform->is_cancelled()) {
            redirect(new \moodle_url('/local/o365/ucp.php'));
        } else if ($fromform) {
            if (!empty($customdata['sitecalendarexists'])) {
                $sitecalenderid = $customdata['sitecalendarexists'];
            } else if (!empty($sitecalendar['Id'])) {
                    $sitecalenderid = $sitecalendar['Id'];
            } else {
                    $sitecalenderid = null;
            }
            \local_o365\feature\calsync\form\subscriptions::update_subscriptions($fromform, $primarycalid,
                    $customdata['cancreatesiteevents'], $customdata['cancreatecourseevents'], $sitecalenderid);
            redirect(new \moodle_url('/local/o365/ucp.php?action=calendar&saved=1'));
        } else {
            $PAGE->requires->jquery();
            $defaultdata = [];
            $existingsubsrs = $DB->get_recordset('local_o365_calsub', ['user_id' => $USER->id]);
            foreach ($existingsubsrs as $existingsubrec) {
                if ($existingsubrec->caltype === 'site') {
                    $defaultdata['sitecal']['checked'] = '1';
                    $defaultdata['sitecal']['syncwith'] = $existingsubrec->o365calid;
                    $defaultdata['sitecal']['syncbehav'] = $existingsubrec->syncbehav;
                } else if ($existingsubrec->caltype === 'user') {
                    $defaultdata['usercal']['checked'] = '1';
                    $defaultdata['usercal']['syncwith'] = $existingsubrec->o365calid;
                    $defaultdata['usercal']['syncbehav'] = $existingsubrec->syncbehav;
                } else if ($existingsubrec->caltype === 'course') {
                    $defaultdata['coursecal'][$existingsubrec->caltypeid]['checked'] = '1';
                    $defaultdata['coursecal'][$existingsubrec->caltypeid]['syncwith'] = $existingsubrec->o365calid;
                    $defaultdata['coursecal'][$existingsubrec->caltypeid]['syncbehav'] = $existingsubrec->syncbehav;
                }
            }

            $existingsubsrs->close();
            $mform->set_data($defaultdata);
            echo $OUTPUT->header();
            $mform->display();
            echo $OUTPUT->footer();
        }
    }

    /**
     * Initiate an OIDC authorization request.
     *
     * @param bool $uselogin Whether to switch the user's Moodle login method to OpenID Connect upon successful authorization.
     */
    protected function doauthrequest($uselogin) {
        global $CFG, $SESSION, $DB, $USER;
        require_once($CFG->dirroot.'/auth/oidc/auth.php');
        $stateparams = ['redirect' => '/local/o365/ucp.php'];
        $extraparams = [];
        $promptlogin = false;
        $o365connected = \local_o365\utils::is_o365_connected($USER->id);
        if ($o365connected === true && isset($USER->auth) && $USER->auth === 'oidc') {
            // User is already connected.
            redirect('/local/o365/ucp.php');
        }

        $connection = $DB->get_record('local_o365_connections', ['muserid' => $USER->id]);
        if (!empty($connection)) {
            // Matched user.
            $extraparams['login_hint'] = $connection->entraidupn;
            $promptlogin = true;
        }
        $auth = new \auth_oidc\loginflow\authcode;
        $auth->set_httpclient(new \auth_oidc\httpclient());
        if ($uselogin !== true) {
            $stateparams['connectiononly'] = true;
        }
        $auth->initiateauthrequest($promptlogin, $stateparams, $extraparams);
    }

    /**
     * Connect to o365 and use o365 login.
     */
    public function mode_connectlogin() {
        global $CFG, $USER;
        auth_oidc_connectioncapability($USER->id, 'connect', true);
        $this->doauthrequest(true);
    }

    /**
     * Connect to o365 without switching user's login method.
     */
    public function mode_connecttoken() {
        global $USER, $CFG;
        if (\local_o365\utils::is_o365_connected($USER->id) !== true) {
            local_o365_connectioncapability($USER->id, 'link', true);
        }
        $this->doauthrequest(false);
    }

    /**
     * Disconnect from o365.
     */
    public function mode_disconnecttoken() {
        global $CFG, $USER;
        require_once($CFG->dirroot.'/auth/oidc/auth.php');
        local_o365_connectioncapability($USER->id, 'unlink', true);
        $auth = new \auth_plugin_oidc;
        $auth->set_httpclient(new \auth_oidc\httpclient());
        $redirect = new \moodle_url('/local/o365/ucp.php');
        $selfurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'disconnecttoken']);
        $auth->disconnect(true, false, $redirect, $selfurl);
    }

    /**
     * Disconnect from o365.
     */
    public function mode_disconnectlogin() {
        global $CFG, $USER;
        auth_oidc_connectioncapability($USER->id, 'disconnect', true);
        require_once($CFG->dirroot.'/auth/oidc/auth.php');
        $auth = new \auth_plugin_oidc;
        $auth->set_httpclient(new \auth_oidc\httpclient());
        $redirect = new \moodle_url('/local/o365/ucp.php');
        $selfurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'disconnectlogin']);
        $auth->disconnect(false, false, $redirect, $selfurl);
    }

    /**
     * Disconnect from o365.
     */
    public function mode_migratetolinked() {
        global $CFG, $USER;
        auth_oidc_connectioncapability($USER->id, 'disconnect', true);
        local_o365_connectioncapability($USER->id, 'link', true);
        require_once($CFG->dirroot.'/auth/oidc/auth.php');
        $auth = new \auth_plugin_oidc;
        $auth->set_httpclient(new \auth_oidc\httpclient());
        $redirect = new \moodle_url('/local/o365/ucp.php');
        $selfurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'migratetolinked']);
        $auth->disconnect(false, true, $redirect, $selfurl);
    }

    /**
     * Microsoft 365 connection management page.
     */
    public function mode_connection() {
        global $OUTPUT, $USER, $PAGE, $CFG;
        $strtitle = get_string('ucp_index_connection_title', 'local_o365');
        $PAGE->navbar->add($strtitle, new \moodle_url('/local/o365/ucp.php?action=connection'));
        $candisconnect = auth_oidc_connectioncapability($USER->id, 'disconnect');
        $canconnect = auth_oidc_connectioncapability($USER->id, 'connect');
        $canlink = local_o365_connectioncapability($USER->id, 'link');
        $canunlink = local_o365_connectioncapability($USER->id, 'unlink');

        // If the user cannot disconnect or connect than they cannot use this page.
        if (!($candisconnect || $canconnect || $canlink || $canunlink)) {
            auth_oidc_connectioncapability($USER->id, 'both', true);
        }
        $connectiontype = $this->get_connection_type();
        $opname = get_config('auth_oidc', 'opname');
        echo $OUTPUT->header();
        echo html_writer::start_div('local_o365_ucp_featurepage local_o365_feature_connection');

        echo html_writer::tag('h3', $strtitle, ['class' => 'local_o365_featureheader local_o365_feature_connection']);

        if (optional_param('o365accountconnected', null, PARAM_TEXT) == 'true') {
            $statusstring = get_string('ucp_o365accountconnected', 'local_o365');
            $statusclasses = 'alert alert-error';
        } else {
            $statusstring = get_string('ucp_connection_disconnected', 'local_o365');
            $statusclasses = 'alert alert-info';
        }

        switch ($connectiontype) {
            case 'entraidlogin':
                $statusclasses = 'alert alert-success';
                $o365upn = \local_o365\utils::get_o365_upn($USER->id);
                $statusstring = get_string('ucp_connection_entraidlogin_active', 'local_o365', $o365upn);
                break;

            case 'linked':
                $statusclasses = 'alert alert-success';
                $o365upn = \local_o365\utils::get_o365_upn($USER->id);
                $statusstring = get_string('ucp_connection_linked_active', 'local_o365', $o365upn);
                break;
        }

        // Is user connected.
        $howconnected = (\local_o365\utils::is_o365_connected($USER->id) === true) ? $connectiontype : false;
        // If the user cannot connect and is not already connected they cannot do anything.
        $canconnectnotconnected = (($canconnect || $canlink) && $howconnected === false) ||
                ($canconnect && $canunlink && $howconnected != 'entraidlogin') ||
                ($candisconnect && $canlink && $howconnected != 'linked');
        // If the user cannot disconnect and is connected, they cannot do anything.
        $candisconnectconnected = ($canunlink && $howconnected == 'linked') || ($candisconnect && $howconnected == 'entraidlogin');
        $cannotdoanything = !($candisconnect || $canconnect || $canlink || $canunlink) ||
            !($canconnectnotconnected || $candisconnectconnected);
        if (!$cannotdoanything) {
            echo html_writer::div(get_string('ucp_connection_desc', 'local_o365'));
        }
        echo html_writer::start_div('connectionstatus '.$statusclasses);
        echo html_writer::tag('h5', $statusstring);
        echo html_writer::end_div();

        if ($cannotdoanything) {
            echo html_writer::end_div();
            echo $OUTPUT->footer();
            return;
        }

        echo html_writer::tag('h5', get_string('ucp_connection_options', 'local_o365'));

        // Entra ID Login.
        if (is_enabled_auth('oidc')) {
            $options = html_writer::start_div('local_o365_connectionoption');
            $header = html_writer::tag('h4', get_string('ucp_connection_entraidlogin', 'local_o365'));
            $loginflow = get_config('auth_oidc', 'loginflow');
            switch ($loginflow) {
                case 'authcode':
                case 'rocreds':
                    $header .= get_string('ucp_connection_entraidlogin_desc_'.$loginflow, 'local_o365', $opname);
                    break;
            }

            switch ($connectiontype) {
                case 'entraidlogin';
                    if (is_enabled_auth('manual') === true && $candisconnect) {
                        $disconnectlinkurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'disconnectlogin']);
                        $strdisconnect = get_string('ucp_connection_entraidlogin_stop', 'local_o365', $opname);
                        $linkhtml = html_writer::link($disconnectlinkurl, $strdisconnect);
                        echo $options.$header. html_writer::tag('h5', $linkhtml);
                        $options = '';
                    }
                    break;

                default:
                    if ($canconnect && ($howconnected != 'linked' || $canunlink)) {
                        $connectlinkurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'connectlogin']);
                        $linkhtml = html_writer::link($connectlinkurl,
                            get_string('ucp_connection_entraidlogin_start', 'local_o365', $opname));
                        echo $options.$header. html_writer::tag('h5', $linkhtml);
                        $options = '';
                    }
            }
            echo html_writer::end_div();
        }

        // Connected account.
        $header = html_writer::start_div('local_o365_connectionoption');
        $header .= html_writer::tag('h4', get_string('ucp_connection_linked', 'local_o365'));
        $header .= html_writer::div(get_string('ucp_connection_linked_desc', 'local_o365'));

        $linkhtml = '';
        switch ($connectiontype) {
            case 'linked':
                if ($canunlink) {
                    $disconnecttokenurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'disconnecttoken']);
                    $linkhtml = html_writer::link($disconnecttokenurl,
                        get_string('ucp_connection_linked_stop', 'local_o365', $opname));
                }
                break;

            case 'entraidlogin':
                if ($canlink && auth_oidc_connectioncapability($USER->id, 'disconnect')) {
                    $connecttokenurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'migratetolinked']);
                    $linkhtml = html_writer::link($connecttokenurl,
                        get_string('ucp_connection_linked_migrate', 'local_o365', $opname));
                }
                break;

            default:
                if ($canlink) {
                    $connecttokenurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'connecttoken']);
                    $linkhtml = html_writer::link($connecttokenurl,
                        get_string('ucp_connection_linked_start', 'local_o365', $opname));
                }
        }

        if (!empty($linkhtml)) {
            echo $options.$header. html_writer::tag('h5', $linkhtml);
            echo html_writer::end_div();
        }

        echo html_writer::end_div();
        echo $OUTPUT->footer();
    }

    /**
     * Microsoft Entra ID login status page.
     */
    public function mode_entraidlogin() {
        redirect(new \moodle_url('/local/o365/ucp.php', ['action' => 'connection']));
    }

    /**
     * Print a feature on the index page.
     *
     * @param string $id The feature identifier: "connection", "calendar".
     * @param bool $enabled Whether the feature is accessible or not.
     * @return string HTML for the feature entry.
     */
    protected function print_index_feature($id, $enabled) {
        $html = html_writer::start_div('local_o365_feature_'.$id);
        $featureuri = new \moodle_url('/local/o365/ucp.php?action='.$id);
        $strtitle = get_string('ucp_index_'.$id.'_title', 'local_o365');

        if ($enabled === true) {
            $html .= html_writer::link($featureuri, $strtitle);
        } else {
            $html .= html_writer::tag('b', $strtitle);
        }

        $strdesc = get_string('ucp_index_'.$id.'_desc', 'local_o365');
        $html .= html_writer::tag('p', $strdesc);
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Return connection type.
     *
     * @return string
     */
    protected function get_connection_type() {
        global $USER;

        if ($this->o365connected === true) {
            return (isset($USER->auth) && $USER->auth === 'oidc') ? 'entraidlogin' : 'linked';
        } else {
            return 'notconnected';
        }
    }

    /**
     * Get HTML for the connection status indicator box.
     *
     * @param string $status The current connection status.
     * @return string The HTML for the connection status indicator box.
     */
    protected function print_connection_status($status = 'connected') {
        global $OUTPUT, $USER, $DB, $CFG;
        $classes = 'connectionstatus';
        $icon = '';
        $msg = '';
        $manageconnectionurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'connection']);

        $mode = 'disconnect';
        $connection = $DB->get_record('local_o365_connections', ['muserid' => $USER->id]);
        if ($status == 'notconnected' || $status == 'matched' && empty($connection)) {
            $mode = 'connect';
        } else if ($status == 'matched') {
            if (!empty($connection) && auth_oidc_connectioncapability($USER->id, 'connect')) {
                $mode = 'connect';
            }
        }

        $canmanage = (auth_oidc_connectioncapability($USER->id, $mode) === true) ? true : false;
        $canlink = local_o365_connectioncapability($USER->id, 'link');
        $canunlink = local_o365_connectioncapability($USER->id, 'unlink');
        switch ($status) {
            case 'connected':
                $classes .= ' alert-success';
                $icon = $OUTPUT->pix_icon('t/check', 'valid', 'moodle');
                $connectiontype = $this->get_connection_type();
                switch($connectiontype) {
                    case 'linked':
                        $msg = get_string('ucp_index_connectionstatus_connected', 'local_o365');
                        $msg .= '<br /><br />';
                        $msg .= get_string('ucp_index_connectionstatus_usinglinked', 'local_o365');
                        if ($canunlink) {
                            $msg .= '<br /><br />';
                            $msg .= $OUTPUT->pix_icon('t/edit', 'valid', 'moodle');
                            $msg .= html_writer::link($manageconnectionurl,
                                get_string('ucp_index_connectionstatus_manage', 'local_o365'));
                        }
                        $msg .= '<br />';
                        $msg .= $OUTPUT->pix_icon('i/reload', 'valid', 'moodle');
                        $refreshurl = new \moodle_url('/local/o365/ucp.php', ['action' => 'connecttoken']);
                        $msg .= html_writer::link($refreshurl, get_string('ucp_index_connectionstatus_reconnect', 'local_o365'));
                        break;

                    case 'entraidlogin':
                        $msg = get_string('ucp_index_connectionstatus_connected', 'local_o365');
                        $msg .= '<br /><br />';
                        $msg .= get_string('ucp_index_connectionstatus_usinglogin', 'local_o365');
                        if ($canmanage === true) {
                            $msg .= '<br /><br />';
                            $msg .= $OUTPUT->pix_icon('t/edit', 'valid', 'moodle');
                            $msg .= html_writer::link($manageconnectionurl,
                                get_string('ucp_index_connectionstatus_manage', 'local_o365'));
                        }
                        break;
                }
                break;

            case 'matched':
                if ($canmanage === true || $canlink) {
                    $matchrec = $DB->get_record('local_o365_connections', ['muserid' => $USER->id]);
                    $classes .= ' alert-info';
                    $msg = get_string('ucp_index_connectionstatus_matched', 'local_o365', $matchrec->ipdupn);
                    $connecturl = new \moodle_url('/local/o365/ucp.php', ['action' => 'connecttoken']);
                    $msg .= '<br /><br />';
                    $msg .= html_writer::link($connecturl, get_string('ucp_index_connectionstatus_login', 'local_o365'));
                }
                break;

            case 'notconnected':
                if ($canmanage === true || $canlink) {
                    $classes .= ' alert-error';
                    $icon = $OUTPUT->pix_icon('i/info', 'valid', 'moodle');
                    $msg = get_string('ucp_index_connectionstatus_notconnected', 'local_o365');
                    $msg .= '<br /><br />';
                    $msg .= $OUTPUT->pix_icon('t/edit', 'valid', 'moodle');
                    $msg .= html_writer::link($manageconnectionurl, get_string('ucp_index_connectionstatus_manage', 'local_o365'));
                }
                break;
        }

        $html = html_writer::start_div($classes);
        $html .= html_writer::tag('h5', get_string('ucp_index_connectionstatus_title', 'local_o365'));
        $html .= $icon;
        $html .= html_writer::tag('p', $msg);
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Default mode - show connection status and a list of features to manage.
     */
    public function mode_default() {
        global $OUTPUT, $DB, $USER;

        echo $OUTPUT->header();
        echo html_writer::start_div('local_o365_ucp_index');
        echo html_writer::tag('h2', $this->title);
        // Is user connected.
        $isconnected = \local_o365\utils::is_o365_connected($USER->id) === true;
        if (auth_oidc_connectioncapability($USER->id, 'connect') === true || $isconnected) {
            echo get_string('ucp_general_intro', 'local_o365');
        } else {
            echo get_string('ucp_general_intro_notconnected_nopermissions', 'local_o365');
        }

        if ($this->o365connected === true) {
            echo $this->print_connection_status('connected');
        } else {
            $matchrec = $DB->get_record('local_o365_connections', ['muserid' => $USER->id]);
            if (!empty($matchrec)) {
                echo $this->print_connection_status('matched');
            } else {
                echo $this->print_connection_status('notconnected');
            }
        }

        echo html_writer::start_div('local_o365_features');
        echo '<br />';
        echo html_writer::tag('h5', get_string('ucp_features', 'local_o365'));
        $introstr = get_string('ucp_features_intro', 'local_o365');
        if ($this->o365connected !== true) {
            $introstr .= get_string('ucp_features_intro_notconnected', 'local_o365');
        }
        echo html_writer::tag('p', $introstr);

        $canconnect = auth_oidc_connectioncapability($USER->id, 'connect');
        $candisconnect = auth_oidc_connectioncapability($USER->id, 'disconnect');
        $canlink = local_o365_connectioncapability($USER->id, 'link');
        $canunlink = local_o365_connectioncapability($USER->id, 'unlink');
        if ($canconnect || $candisconnect || $canlink || $canunlink) { // TBD?
            echo $this->print_index_feature('connection', true);
        }
        echo $this->print_index_feature('calendar', $this->o365connected);

        echo html_writer::end_div();

        echo html_writer::end_div();
        echo $OUTPUT->footer();
    }
}
