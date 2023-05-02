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
 * Admin control panel page.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\page;

use admin_setting_configcheckbox;
use admin_setting_heading;
use auth_oidc\httpclient;
use auth_oidc\loginflow\authcode;
use auth_plugin_oidc;
use core_course_category;
use core_php_time_limit;
use core_plugin_manager;
use core_user;
use Exception;
use finfo;
use html_table;
use html_writer;
use local_o365\feature\coursesync\main;
use local_o365\feature\userconnections\filtering;
use local_o365\feature\userconnections\table;
use local_o365\form\manualusermatch;
use local_o365\form\teamsconnection;
use local_o365\form\usermatch;
use local_o365\healthcheck\healthcheckinterface;
use local_o365\utils;
use moodle_exception;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/o365/lib.php');
require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * Admin control panel page.
 */
class acp extends base {

    /**
     * Override set_title() function - not showing heading.
     *
     * @param string $title
     */
    public function set_title($title) {
        global $PAGE;
        $this->title = $title;
        $PAGE->set_title($this->title);
    }

    /**
     * Add base navbar for this page.
     */
    protected function add_navbar() {
        global $PAGE;

        $PAGE->navbar->add(get_string('administrationsite'), new moodle_url('/admin/search.php'));
        $PAGE->navbar->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', ['category' => 'modules']));
        $PAGE->navbar->add(get_string('localplugins'), new moodle_url('/admin/category.php', ['category' => 'localplugins']));
        $PAGE->navbar->add(get_string('pluginname', 'local_o365'),
            new moodle_url('/admin/settings.php', ['section' => 'local_o365']));

        $mode = optional_param('mode', '', PARAM_TEXT);
        $params = ['section' => 'local_o365'];
        switch ($mode) {
            case 'coursesynccustom':
                $params['s_local_o365_tabs'] = LOCAL_O365_TAB_SYNC;
                $this->title = get_string('settings_header_syncsettings', 'local_o365');
                break;
            case 'healthcheck':
            case 'usermatch':
            case 'teamconnections':
            case 'maintenance':
            case 'maintenance_recreatedeletedgroups':
            case 'maintenance_resyncgroupusers':
            case 'maintenance_cleandeltatoken':
            case 'tenants':
                $params['s_local_o365_tabs'] = LOCAL_O365_TAB_ADVANCED;
                break;
        }
        $PAGE->navbar->add($this->title, new moodle_url('/admin/settings.php', $params));

        switch ($mode) {
            case 'maintenance_recreatedeletedgroups':
            case 'maintenance_resyncgroupusers':
            case 'maintenance_cleandeltatoken':
                $PAGE->navbar->add(get_string('acp_maintenance', 'local_o365'),
                    new moodle_url('/local/o365/acp.php', ['mode' => 'maintenance']));
                break;
        }
    }

    /**
     * Provide admin consent.
     */
    public function mode_adminconsent() {
        $auth = new authcode;
        $auth->set_httpclient(new httpclient());
        $stateparams = ['redirect' => '/admin/settings.php?section=local_o365', 'justauth' => true, 'forceflow' => 'authcode',
            'action' => 'adminconsent',];
        $idptype = get_config('auth_oidc', 'idptype');
        if ($idptype == AUTH_OIDC_IDP_TYPE_MICROSOFT) {
            $auth->initiateadminconsentrequest($stateparams);
        } else {
            $extraparams = ['prompt' => 'admin_consent'];
            $auth->initiateauthrequest(true, $stateparams, $extraparams);
        }
    }

    /**
     * Set the system API user.
     */
    public function mode_setsystemuser() {
        set_config('enableapponlyaccess', '0', 'local_o365');
        $auth = new authcode;
        $auth->set_httpclient(new httpclient());
        $stateparams = ['redirect' => '/admin/settings.php?section=local_o365', 'justauth' => true, 'forceflow' => 'authcode',
            'action' => 'setsystemapiuser',];
        $extraparams = ['prompt' => 'admin_consent'];
        $auth->initiateauthrequest(true, $stateparams, $extraparams);
    }

    /**
     * This function ensures setup is sufficiently complete to add additional tenants.
     *
     * @return bool
     */
    public function checktenantsetup() : bool {
        $config = get_config('local_o365');
        if (empty($config->aadtenant)) {
            return false;
        }
        if (utils::is_configured_apponlyaccess() === true || !empty($config->systemtokens)) {
            return true;
        }
        return false;
    }

    /**
     * Configure additional tenants.
     */
    public function mode_tenants() {
        global $CFG, $PAGE;

        $this->set_title(get_string('acp_tenants_title', 'local_o365'));
        $PAGE->navbar->add(get_string('acp_tenants_title', 'local_o365'),
            new moodle_url($this->url, ['mode' => 'tenants']));

        $this->standard_header();

        echo html_writer::div(get_string('acp_tenants_title_desc', 'local_o365'));
        echo html_writer::empty_tag('br');
        $config = get_config('local_o365');
        if ($this->checktenantsetup() !== true) {
            $errmsg = get_string('acp_tenants_errornotsetup', 'local_o365');
            echo html_writer::div($errmsg, 'alert alert-info');
            $this->standard_footer();
            return;
        }

        $multitenantdesc = get_string('acp_tenants_intro', 'local_o365', $CFG->wwwroot);
        echo html_writer::div($multitenantdesc, 'alert alert-info');

        echo html_writer::empty_tag('br');
        $hosttenantstr = get_string('acp_tenants_hosttenant', 'local_o365', $config->aadtenant);
        $hosttenanthtml = html_writer::tag('h4', $hosttenantstr);
        echo html_writer::div($hosttenanthtml);
        echo html_writer::empty_tag('br');

        $addtenantstr = get_string('acp_tenants_add', 'local_o365');
        $addtenanturl = new moodle_url('/local/o365/acp.php', ['mode' => 'tenantsadd']);
        echo html_writer::link($addtenanturl, $addtenantstr, ['class' => 'btn btn-primary']);

        $configuredtenants = get_config('local_o365', 'multitenants');
        if (!empty($configuredtenants)) {
            $configuredtenants = json_decode($configuredtenants, true);
            if (!is_array($configuredtenants)) {
                $configuredtenants = [];
            }
        }

        if (!empty($configuredtenants)) {
            $table = new html_table();
            $table->head[] = get_string('acp_tenants_tenant', 'local_o365');
            $table->head[] = get_string('acp_tenants_actions', 'local_o365');
            $revokeaccessstr = get_string('acp_tenants_revokeaccess', 'local_o365');
            foreach ($configuredtenants as $tenantid => $tenantdomains) {
                $revokeurlparams = [
                    'mode' => 'tenantsrevoke',
                    't' => base64_encode($tenantid),
                    'sesskey' => sesskey(),
                ];
                $revokeurl = new moodle_url('/local/o365/acp.php', $revokeurlparams);
                $table->data[] = [
                    implode(', ', $tenantdomains),
                    html_writer::link($revokeurl, $revokeaccessstr),
                ];
            }
            echo html_writer::table($table);
        } else {
            $emptytenantstr = get_string('acp_tenants_none', 'local_o365');
            echo html_writer::empty_tag('br');
            echo html_writer::empty_tag('br');
            echo html_writer::div($emptytenantstr, 'alert alert-error');
        }

        // Show legacy tenants table.
        $legacyconfiguredtenants = get_config('local_o365', 'legacymultitenants');

        if (!empty($legacyconfiguredtenants)) {
            $legacyconfiguredtenants = json_decode($legacyconfiguredtenants, true);
            if (!is_array($legacyconfiguredtenants)) {
                $legacyconfiguredtenants = [];
            }
        }

        if (!empty($legacyconfiguredtenants)) {
            echo html_writer::empty_tag('hr');
            echo html_writer::tag('h4', get_string('acp_tenants_legacy_tenants', 'local_o365'));
            echo html_writer::div(get_string('acp_tenants_legacy_tenants_help', 'local_o365'), 'warning');

            $table = new html_table();
            $table->head[] = get_string('acp_tenants_tenant', 'local_o365');
            $table->head[] = get_string('acp_tenants_actions', 'local_o365');
            foreach ($legacyconfiguredtenants as $configuredtenant) {
                $deleturlparams = [
                    'mode' => 'tenantsdeletelegacy',
                    't' => base64_encode($configuredtenant),
                    'sesskey' => sesskey(),
                ];
                $deleteurl = new moodle_url('/local/o365/acp.php', $deleturlparams);
                $table->data[] = [
                    $configuredtenant,
                    html_writer::link($deleteurl, get_string('acp_tenants_delete', 'local_o365')),
                ];
            }
            echo html_writer::table($table);
        }

        $this->standard_footer();
    }

    /**
     * Description page shown before adding a new tenant.
     */
    public function mode_tenantsadd() {
        $this->standard_header();
        echo html_writer::tag('h2', get_string('acp_tenants_title', 'local_o365'));
        echo html_writer::div(get_string('acp_tenants_title_desc', 'local_o365'));
        echo html_writer::empty_tag('br');
        if ($this->checktenantsetup() !== true) {
            $errmsg = get_string('acp_tenants_errornotsetup', 'local_o365');
            echo html_writer::div($errmsg, 'alert alert-info');
            $this->standard_footer();
            return;
        }
        echo html_writer::div(get_string('acp_tenantsadd_desc', 'local_o365'));
        echo html_writer::empty_tag('br');
        $addtenantstr = get_string('acp_tenantsadd_linktext', 'local_o365');
        $addtenanturl = new moodle_url('/local/o365/acp.php', ['mode' => 'tenantsaddgo']);
        echo html_writer::link($addtenanturl, $addtenantstr, ['class' => 'btn btn-primary']);

        $this->standard_footer();
    }

    /**
     * Revoke access to a specific tenant.
     */
    public function mode_tenantsrevoke() {
        require_sesskey();
        $tenantid = required_param('t', PARAM_TEXT);
        $tenantid = (string)base64_decode($tenantid);
        utils::disableadditionaltenant($tenantid);
        redirect(new moodle_url('/local/o365/acp.php', ['mode' => 'tenants']));
    }

    /**
     * Delete a legacy tenant form the legacy configuration settings.
     *
     * @return void
     */
    public function mode_tenantsdeletelegacy() {
        require_sesskey();
        $tenant = required_param('t', PARAM_TEXT);
        $tenant = (string) base64_decode($tenant);
        utils::disableadditionaltenant($tenant);
        redirect(new moodle_url('/local/o365/acp.php', ['mode' => 'tenants']));
    }

    /**
     * Perform auth request for tenant addition.
     */
    public function mode_tenantsaddgo() {
        $auth = new authcode;
        $auth->set_httpclient(new httpclient());
        $stateparams = ['redirect' => '/local/o365/acp.php?mode=tenantsadd', 'justauth' => true, 'forceflow' => 'authcode',
            'action' => 'addtenant', 'ignorerestrictions' => true,];
        $idptype = get_config('auth_oidc', 'idptype');
        if ($idptype == AUTH_OIDC_IDP_TYPE_MICROSOFT) {
            $auth->initiateadminconsentrequest($stateparams);
        } else {
            $extraparams = ['prompt' => 'admin_consent'];
            $auth->initiateauthrequest(true, $stateparams, $extraparams);
        }
    }

    /**
     * Perform health checks.
     */
    public function mode_healthcheck() {
        global $PAGE;

        $this->set_title(get_string('acp_healthcheck', 'local_o365'));
        $PAGE->navbar->add(get_string('acp_healthcheck', 'local_o365'),
            new moodle_url($this->url, ['mode' => 'healthcheck']));

        $this->standard_header();

        $enableapponlyaccess = get_config('local_o365', 'enableapponlyaccess');
        if (empty($enableapponlyaccess)) {
            $healthchecks = ['systemapiuser', 'ratelimit'];
        } else {
            $healthchecks = ['ratelimit'];
        }
        foreach ($healthchecks as $healthcheck) {
            $healthcheckclass = '\local_o365\healthcheck\\' . $healthcheck;
            $healthcheck = new $healthcheckclass();
            $result = $healthcheck->run();

            echo '<h5>' . $healthcheck->get_name() . '</h5>';
            if ($result['result'] === true) {
                echo '<div class="alert alert-success">' . $result['message'] . '</div><br />';
            } else {
                switch ($result['severity']) {
                    case healthcheckinterface::SEVERITY_TRIVIAL:
                        $severityclass = 'alert-info';
                        break;

                    default:
                        $severityclass = 'alert-error';
                }

                echo '<div class="alert ' . $severityclass . '">';
                echo $result['message'];
                if (isset($result['fixlink'])) {
                    echo '<br /><br />' . html_writer::link($result['fixlink'], get_string('healthcheck_fixlink', 'local_o365'));
                }
                echo '</div><br />';
            }
        }

        $this->standard_footer();
    }

    /**
     * Clear items from the match queue.
     */
    public function mode_usermatchclear() {
        global $DB;

        $type = optional_param('type', null, PARAM_TEXT);
        switch ($type) {
            case 'success':
                $DB->delete_records_select('local_o365_matchqueue', 'completed = "1" AND errormessage = ""');
                $return = ['success' => true];
                break;

            case 'error':
                $DB->delete_records_select('local_o365_matchqueue', 'completed = "1" AND errormessage != ""');
                $return = ['success' => true];
                break;

            case 'queued':
                $DB->delete_records_select('local_o365_matchqueue', 'completed = "0"');
                $return = ['success' => true];
                break;

            case 'all':
                $DB->delete_records('local_o365_matchqueue');
                $return = ['success' => true];
                break;

            default:
                $return = ['success' => false];
        }
        echo json_encode($return);
        die();
    }

    /**
     * User match tool.
     */
    public function mode_usermatch() {
        global $DB, $OUTPUT, $PAGE, $SESSION;

        $this->set_title(get_string('acp_usermatch', 'local_o365'));

        $PAGE->navbar->add(get_string('acp_usermatch', 'local_o365'),
            new moodle_url($this->url, ['mode' => 'usermatch']));

        $errors = [];
        $mform = new usermatch('?mode=usermatch');
        if ($mform->get_data()) {
            $datafile = $mform->save_temp_file('matchdatafile');
            if (!empty($datafile)) {
                $finfo = new finfo();
                $type = $finfo->file($datafile, FILEINFO_MIME);
                $type = explode(';', $type);
                if (strtolower($type[0]) === 'text/plain') {
                    try {
                        $fh = fopen($datafile, 'r');
                        if (!empty($fh)) {
                            $row = 1;
                            while (($data = fgetcsv($fh)) !== false) {
                                if (!empty($data)) {
                                    if (isset($data[0]) && isset($data[1])) {
                                        $newrec = new stdClass;
                                        $newrec->musername = trim($data[0]);
                                        $newrec->o365username = trim($data[1]);
                                        $newrec->openidconnect = (isset($data[2]) && intval(trim($data[2]))) > 0 ? 1 : 0;
                                        $newrec->completed = 0;
                                        $newrec->errormessage = '';
                                        $DB->insert_record('local_o365_matchqueue', $newrec);
                                    } else {
                                        $errors[] = get_string('acp_usermatch_upload_err_data', 'local_o365', $row);
                                    }
                                }
                                $row++;
                            }
                            fclose($fh);
                        } else {
                            $errors[] = get_string('acp_usermatch_upload_err_fileopen', 'local_o365');
                        }
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                } else {
                    $errors[] = get_string('acp_usermatch_upload_err_badmime', 'local_o365', $type[0]);
                }
                @unlink($datafile);
                $mform->set_data([]);
            } else {
                $errors[] = get_string('acp_usermatch_upload_err_nofile', 'local_o365');
            }
            if (!empty($errors)) {
                $SESSION->o365matcherrors = $errors;
            }
            redirect(new moodle_url('/local/o365/acp.php', ['mode' => 'usermatch']));
            die();
        }

        $PAGE->requires->jquery();
        $this->standard_header();
        echo html_writer::div(get_string('acp_usermatch_desc', 'local_o365'));
        echo html_writer::empty_tag('br');
        echo html_writer::empty_tag('br');
        echo html_writer::tag('h4', get_string('acp_usermatch_upload', 'local_o365'));
        echo html_writer::div(get_string('acp_usermatch_upload_desc', 'local_o365'));
        echo html_writer::empty_tag('br');
        if (!empty($SESSION->o365matcherrors)) {
            foreach ($SESSION->o365matcherrors as $error) {
                echo html_writer::div($error, 'alert-error alert local_o365_statusmessage');
            }
            $SESSION->o365matcherrors = [];
        }
        $mform->display();

        echo html_writer::empty_tag('br');
        echo html_writer::tag('h4', get_string('acp_usermatch_matchqueue', 'local_o365'));
        echo html_writer::div(get_string('acp_usermatch_matchqueue_desc', 'local_o365'));
        $matchqueuelength = $DB->count_records('local_o365_matchqueue');
        if ($matchqueuelength > 0) {

            echo html_writer::start_tag('div', ['class' => 'local_o365_matchqueuetoolbar']);

            $clearurl = new moodle_url('/local/o365/acp.php', ['mode' => 'usermatchclear']);
            $clearurl = $clearurl->out();

            // Clear successful button.
            $checkicon = $OUTPUT->pix_icon('t/check', 'success', 'moodle');
            $clearcallback = '$(\'table.local_o365_matchqueue\').find(\'tr.success\').fadeOut();';
            $attrs = ['onclick' => '$.post(\'' . $clearurl . '\', {type:\'success\'}, function(data) { ' . $clearcallback . ' })'];
            $buttontext = get_string('acp_usermatch_matchqueue_clearsuccess', 'local_o365');
            echo html_writer::tag('button', $checkicon . ' ' . $buttontext, $attrs);

            // Clear error button.
            $warningicon = $OUTPUT->pix_icon('i/warning', 'warning', 'moodle');
            $clearcallback = '$(\'table.local_o365_matchqueue\').find(\'tr.error\').fadeOut();';
            $attrs = ['onclick' => '$.post(\'' . $clearurl . '\', {type:\'error\'}, function(data) { ' . $clearcallback . ' })'];
            $buttontext = get_string('acp_usermatch_matchqueue_clearerrors', 'local_o365');
            echo html_writer::tag('button', $warningicon . ' ' . $buttontext, $attrs);

            // Clear warning button.
            $queuedicon = $OUTPUT->pix_icon('i/scheduled', 'warning', 'moodle');
            $clearcallback = '$(\'table.local_o365_matchqueue\').find(\'tr.queued\').fadeOut();';
            $attrs = ['onclick' => '$.post(\'' . $clearurl . '\', {type:\'queued\'}, function(data) { ' . $clearcallback . ' })'];
            $buttontext = get_string('acp_usermatch_matchqueue_clearqueued', 'local_o365');
            echo html_writer::tag('button', $queuedicon . ' ' . $buttontext, $attrs);

            // Clear all button.
            $removeicon = $OUTPUT->pix_icon('t/delete', 'warning', 'moodle');
            $clearcallback = '$(\'table.local_o365_matchqueue\').find(\'tr:not(:first-child)\').fadeOut();';
            $attrs = ['onclick' => '$.post(\'' . $clearurl . '\', {type:\'all\'}, function(data) { ' . $clearcallback . ' })'];
            $buttontext = get_string('acp_usermatch_matchqueue_clearall', 'local_o365');
            echo html_writer::tag('button', $removeicon . ' ' . $buttontext, $attrs);

            echo html_writer::end_tag('div');

            $matchqueue = $DB->get_recordset('local_o365_matchqueue', null, 'id ASC');
            // Constructing table manually instead of \html_table for memory reasons.
            echo html_writer::start_tag('table', ['class' => 'local_o365_matchqueue']);
            echo html_writer::start_tag('tr');
            echo html_writer::tag('th', '');
            echo html_writer::tag('th', get_string('acp_usermatch_matchqueue_column_muser', 'local_o365'));
            echo html_writer::tag('th', get_string('acp_usermatch_matchqueue_column_o365user', 'local_o365'));
            echo html_writer::tag('th', get_string('acp_usermatch_matchqueue_column_openidconnect', 'local_o365'));
            echo html_writer::tag('th', get_string('acp_usermatch_matchqueue_column_status', 'local_o365'));
            echo html_writer::end_tag('tr');
            foreach ($matchqueue as $queuerec) {
                $status = 'queued';
                $trclass = 'alert-info queued';
                if (!empty($queuerec->completed) && empty($queuerec->errormessage)) {
                    $status = 'success';
                    $trclass = 'alert-success success';
                } else if (!empty($queuerec->errormessage)) {
                    $status = 'error';
                    $trclass = 'alert-error error';
                }

                echo html_writer::start_tag('tr', ['class' => $trclass]);

                switch ($status) {
                    case 'success':
                        echo html_writer::tag('td', $checkicon);
                        break;

                    case 'error':
                        echo html_writer::tag('td', $warningicon);
                        break;

                    default:
                        echo html_writer::tag('td', $queuedicon);
                }

                echo html_writer::tag('td', $queuerec->musername);
                echo html_writer::tag('td', $queuerec->o365username);
                echo html_writer::tag('td', $queuerec->openidconnect > 0 ? get_string('yes') : get_string('no'));

                switch ($status) {
                    case 'success':
                        echo html_writer::tag('td', get_string('acp_usermatch_matchqueue_status_success', 'local_o365'));
                        break;

                    case 'error':
                        $statusstr = get_string('acp_usermatch_matchqueue_status_error', 'local_o365', $queuerec->errormessage);
                        echo html_writer::tag('td', $statusstr);
                        break;

                    default:
                        echo html_writer::tag('td', get_string('acp_usermatch_matchqueue_status_queued', 'local_o365'));
                }
                echo html_writer::end_tag('tr');
            }
            echo html_writer::end_tag('table');
            $matchqueue->close();
        } else {
            $msgclasses = 'alert-info alert local_o365_statusmessage';
            echo html_writer::div(get_string('acp_usermatch_matchqueue_empty', 'local_o365'), $msgclasses);
        }
        $this->standard_footer();
    }

    /**
     * Course sync customization.
     */
    public function mode_coursesynccustom() {
        global $CFG, $OUTPUT, $PAGE;

        $this->set_title(get_string('acp_coursesynccustom', 'local_o365'));

        $PAGE->navbar->add(get_string('acp_coursesynccustom', 'local_o365'),
            new moodle_url($this->url, ['mode' => 'coursesynccustom']));

        $totalcount = 0;
        $perpage = 20;

        $curpage = optional_param('page', 0, PARAM_INT);
        $sort = optional_param('sort', '', PARAM_ALPHA);
        $search = optional_param('search', '', PARAM_TEXT);
        $sortdir = strtolower(optional_param('sortdir', 'asc', PARAM_ALPHA));

        $headers = ['fullname' => get_string('fullnamecourse'), 'shortname' => get_string('shortnamecourse'),];
        if (empty($sort) || !isset($headers[$sort])) {
            $sort = 'fullname';
        }
        if (!in_array($sortdir, ['asc', 'desc'], true)) {
            $sortdir = 'asc';
        }

        $table = new html_table();
        foreach ($headers as $hkey => $desc) {
            $diffsortdir = ($sort === $hkey && $sortdir === 'asc') ? 'desc' : 'asc';
            $linkattrs = ['mode' => 'coursesynccustom', 'sort' => $hkey, 'sortdir' => $diffsortdir];
            $link = new moodle_url('/local/o365/acp.php', $linkattrs);

            if ($sort === $hkey) {
                $desc .= ' ' . $OUTPUT->pix_icon('t/' . 'sort_' . $sortdir, 'sort');
            }
            $table->head[] = html_writer::link($link, $desc);
        }
        $table->head[] = get_string('acp_coursesynccustom_enabled', 'local_o365');

        $limitfrom = $curpage * $perpage;
        $coursesid = [];

        if (empty($search)) {
            $sortdir = 1;
            if ($sortdir == 'desc') {
                $sortdir = -1;
            }
            $options = ['recursive' => true, 'sort' => [$sort => $sortdir], 'offset' => $limitfrom, 'limit' => $perpage,];
            $topcat = core_course_category::get(0);
            $courses = $topcat->get_courses($options);
            $totalcount = $topcat->get_courses_count($options);
        } else {
            $searchar = explode(' ', $search);
            $courses = get_courses_search($searchar, 'c.' . $sort . ' ' . $sortdir, $curpage, $perpage, $totalcount);
        }

        $sdscourseids = \local_o365\feature\sds\utils::get_sds_course_ids();

        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            $coursesid[] = $course->id;
            $isenabled = \local_o365\feature\coursesync\utils::is_course_sync_enabled($course->id);
            $enabledname = 'course_' . $course->id . '_enabled';

            $enablecheckboxattrs = ['class' => 'course_sync_enabled',
                'onchange' => 'local_o365_set_coursesync(\'' . $course->id . '\', $(this).prop(\'checked\'), $(this))'];

            $sdscoursetext = '';

            if (in_array($course->id, $sdscourseids)) {
                $enablecheckboxattrs['disabled'] = 'disabled';
                $sdscoursetext = get_string('acp_coursesynccustom_sds_course', 'local_o365');
            }

            $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);

            $rowdata = [html_writer::link($courseurl, $course->fullname), $course->shortname,
                html_writer::checkbox($enabledname, 1, $isenabled, '', $enablecheckboxattrs) . ' ' . $sdscoursetext];
            $table->data[] = $rowdata;
        }

        $PAGE->requires->jquery();
        $this->standard_header();

        $endpoint = new moodle_url('/local/o365/acp.php', ['mode' => 'coursesynccustom_change', 'sesskey' => sesskey()]);
        $custompageurl = new moodle_url('/local/o365/acp.php', ['mode' => 'coursesynccustom']);
        $allchangeendpoint =
            new moodle_url('/local/o365/acp.php', ['mode' => 'coursesynccustom_allchange', 'sesskey' => sesskey()]);

        $js = '
var local_o365_coursesync_bulk_set_enable = function(state) {
    var enabled = (state == 1) ? true : false;
    $("input.course_sync_enabled:not(:disabled)").prop("checked", enabled);
};

var local_o365_coursesync_coursesid = ' . json_encode($coursesid) . ';

var local_o365_coursesync_save = function() {
    var coursedata = {};
    for (var i = 0; i < local_o365_coursesync_coursesid.length; i++) {
        var courseid = local_o365_coursesync_coursesid[i];
        var enabled = $("input[name=\'course_"+courseid+"_enabled\']").is(\':checked\');
        var syncstatus = {enabled: enabled};
        coursedata[courseid] = syncstatus;
    }
    // Send data to server.
    $.ajax({
        url: \'' . $endpoint->out(false) . '\',
        data: {
            coursedata: JSON.stringify(coursedata),
            newcourse: $("input#id_s_local_o365_sync_new_course").prop("checked"),
            percourse: $("input#id_s_local_o365_course_sync_per_course").prop("checked"),
        },
        type: "POST",
        success: function(data) {
            console.log(data);
            $(\'#acp_coursesynccustom_savemessage\').show();
            setTimeout(function () { $(\'#acp_coursesynccustom_savemessage\').hide(); }, 5000);
        }
    });
};

var local_o365_coursesync_all_set_feature = function(state) {
    if (confirm("' . get_string('acp_coursesynccustom_confirm_all_action', 'local_o365') . '")) {
        var enabled = (state == 1) ? true : false;
        
        // Send data to server
        $.ajax({
            url: \'' . $allchangeendpoint->out(false) . '\',
            data: {state: enabled},
            type: "POST",
            success: function(data) {
                console.log(data);
                window.location.href = "' . $custompageurl->out(false) . '";
            }
        });
    }
};';

        echo html_writer::script($js);

        require_once($CFG->libdir . '/adminlib.php');
        echo html_writer::empty_tag('hr');
        $coursesynccustomisesettingheader = new admin_setting_heading('local_o365/course_sync_customize_header',
            get_string('acp_coursesynccustom_settings_header', 'local_o365'), '');
        echo $coursesynccustomisesettingheader->output_html(null);

        // Option to enable sync by default for new courses.
        $enablefornewcoursesetting = new admin_setting_configcheckbox('local_o365/sync_new_course',
            get_string('acp_coursesynccustom_new_course', 'local_o365'),
            get_string('acp_coursesynccustom_new_course_desc', 'local_o365'), '0');
        echo $enablefornewcoursesetting->output_html(get_config('local_o365', 'sync_new_course'));

        // Allow course sync controlled at course level.
        $controlpercoursesetting = new admin_setting_configcheckbox('local_o365/course_sync_per_course',
            get_string('acp_coursesynccustom_controlled_per_course', 'local_o365'),
            get_string('acp_coursesynccustom_controlled_per_course_desc', 'local_o365'), '0');
        echo $controlpercoursesetting->output_html(get_config('local_o365', 'course_sync_per_course'));

        echo html_writer::empty_tag('hr');

        // Bulk Operations.
        echo html_writer::tag('h3', get_string('acp_coursesynccustom_bulk', 'local_o365'));

        // Option to enable all sync features on all pages.
        echo html_writer::start_tag('div', ['style' => 'display: block; margin: 1rem']);
        echo html_writer::tag('button', get_string('acp_coursesynccustom_enable_all', 'local_o365'),
            ['onclick' => 'local_o365_coursesync_all_set_feature(1)']);
        echo html_writer::tag('span', '&nbsp;');
        echo html_writer::tag('button', get_string('acp_coursesynccustom_disable_all', 'local_o365'),
            ['onclick' => 'local_o365_coursesync_all_set_feature(0)']);
        echo html_writer::end_tag('div');

        // Option to enable sync features on this page only.
        echo html_writer::start_tag('div', ['style' => 'display: block;margin: 1rem']);
        echo html_writer::tag('button', get_string('acp_coursesynccustom_bulk_enable', 'local_o365'),
            ['onclick' => 'local_o365_coursesync_bulk_set_enable(1)']);
        echo html_writer::tag('span', '&nbsp;');
        echo html_writer::tag('button', get_string('acp_coursesynccustom_bulk_disable', 'local_o365'),
            ['onclick' => 'local_o365_coursesync_bulk_set_enable(0)']);
        echo html_writer::end_tag('div');

        echo html_writer::empty_tag('hr');

        // Search form.
        echo html_writer::tag('h3', get_string('search'));
        echo html_writer::start_tag('form', ['id' => 'coursesearchform', 'method' => 'get']);
        echo html_writer::start_tag('fieldset', ['class' => 'coursesearchbox invisiblefieldset']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'mode', 'value' => 'coursesynccustom']);
        echo html_writer::empty_tag('input',
            ['type' => 'text', 'id' => 'coursesearchbox', 'size' => 30, 'name' => 'search', 'value' => s($search)]);
        echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('go')]);
        echo html_writer::div(html_writer::tag('strong', get_string('acp_coursesynccustom_searchwarning', 'local_o365')));
        echo html_writer::end_tag('fieldset');
        echo html_writer::end_tag('form');
        echo html_writer::empty_tag('br');

        echo html_writer::tag('h5', get_string('courses'));
        echo html_writer::table($table);
        echo html_writer::tag('p', get_string('acp_coursesynccustom_savemessage', 'local_o365'),
            ['id' => 'acp_coursesynccustom_savemessage', 'style' => 'display: none; font-weight: bold; color: red']);
        echo html_writer::tag('button', get_string('savechanges'),
            ['class' => 'buttonsbar', 'onclick' => 'local_o365_coursesync_save()']);

        $searchtext = optional_param('search', '', PARAM_TEXT);
        $cururl = new moodle_url('/local/o365/acp.php', ['mode' => 'coursesynccustom', 'search' => $searchtext]);
        echo $OUTPUT->paging_bar($totalcount, $curpage, $perpage, $cururl);
        $this->standard_footer();
    }

    /**
     * Endpoint to change course sync customization.
     */
    public function mode_coursesynccustom_change() {
        require_sesskey();

        // Save enabled by default on new course settings.
        $enabledfornewcoursesetting = required_param('newcourse', PARAM_BOOL);
        set_config('sync_new_course', $enabledfornewcoursesetting, 'local_o365');

        // Save allow configuring course sync per course.
        $controlpercoursesetting = required_param('percourse', PARAM_BOOL);
        set_config('course_sync_per_course', $controlpercoursesetting, 'local_o365');

        // Save course settings.
        $coursedata = json_decode(required_param('coursedata', PARAM_RAW), true);
        foreach ($coursedata as $courseid => $course) {
            if (!is_scalar($courseid) || ((string) $courseid !== (string) (int) $courseid)) {
                // Non-int-like course ID value. Invalid. Skip.
                continue;
            }
            foreach ($course as $feature => $value) {
                // Value must be boolean - existing set_* functions below already treat non-true as false, so let's be clear.
                if (!is_bool($value)) {
                    $value = false;
                }
                if ($feature === 'enabled') {
                    \local_o365\feature\coursesync\utils::set_course_sync_enabled($courseid, $value);
                }
            }
        }
        echo json_encode(['Saved']);
    }

    /**
     * Enable / disable all sync features on all course when using custom sync settings.
     */
    public function mode_coursesynccustom_allchange() {
        global $DB;

        $enabled = (bool) required_param('state', PARAM_BOOL);
        require_sesskey();

        $courses = $DB->get_records('course');
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            \local_o365\feature\coursesync\utils::set_course_sync_enabled($course->id, $enabled);
        }
    }

    /**
     * Teams connections.
     */
    public function mode_teamconnections() {
        global $DB, $OUTPUT, $PAGE;

        $this->set_title(get_string('acp_teamconnections', 'local_o365'));

        $PAGE->navbar->add(get_string('acp_teamconnections', 'local_o365'),
            new moodle_url($this->url, ['mode' => 'teamconnections']));

        // Check settings.
        $coursesyncsetting = get_config('local_o365', 'coursesync');
        if ($coursesyncsetting === 'off') {
            $redirecturl = new moodle_url('/admin/settings.php', ['section' => 'local_o365', 's_local_o365_tabs' => 1]);
            redirect($redirecturl, get_string('acp_teamconnections_sync_disabled', 'local_o365'));
        }

        $totalcount = 0;
        $perpage = 20;

        $curpage = optional_param('page', 0, PARAM_INT);
        $sort = optional_param('sort', '', PARAM_ALPHA);
        $search = optional_param('search', '', PARAM_TEXT);
        $sortdir = strtolower(optional_param('sortdir', 'asc', PARAM_ALPHA));

        $headers = ['fullname' => get_string('fullnamecourse'), 'shortname' => get_string('shortnamecourse'),];
        if (empty($sort) || !isset($headers[$sort])) {
            $sort = 'fullname';
        }
        if (!in_array($sortdir, ['asc', 'desc'], true)) {
            $sortdir = 'asc';
        }

        $table = new html_table();
        foreach ($headers as $hkey => $desc) {
            $diffsortdir = ($sort === $hkey && $sortdir === 'asc') ? 'desc' : 'asc';
            $linkattrs = ['mode' => 'teamconnections', 'sort' => $hkey, 'sortdir' => $diffsortdir];
            $link = new moodle_url('/local/o365/acp.php', $linkattrs);

            if ($sort === $hkey) {
                $desc .= ' ' . $OUTPUT->pix_icon('t/sort_' . $sortdir, 'sort');
            }
            $table->head[] = html_writer::link($link, $desc);
        }
        $table->head[] = get_string('acp_teamconnections_connected_team', 'local_o365');
        $table->head[] = get_string('acp_teamconnections_actions', 'local_o365');

        $limitfrom = $curpage * $perpage;

        if (empty($search)) {
            $sortdir = 1;
            if ($sortdir == 'desc') {
                $sortdir = -1;
            }
            $options = ['recursive' => true, 'sort' => [$sort => $sortdir], 'offset' => $limitfrom, 'limit' => $perpage,];
            $topcat = core_course_category::get(0);
            $courses = $topcat->get_courses($options);
            $totalcount = $topcat->get_courses_count($options);
        } else {
            $searchar = explode(' ', $search);
            $courses = get_courses_search($searchar, 'c.' . $sort . ' ' . $sortdir, $curpage, $perpage, $totalcount);
        }

        foreach ($courses as $course) {
            $actions = [];

            if ($course->id == SITEID) {
                continue;
            }

            if ($grouprecord = $DB->get_record('local_o365_objects',
                ['moodleid' => $course->id, 'type' => 'group', 'subtype' => 'course'])) {
                if ($DB->record_exists('local_o365_objects',
                    ['moodleid' => $course->id, 'type' => 'group', 'subtype' => 'courseteam']) ||
                    $DB->record_exists('local_o365_objects',
                        ['moodleid' => $course->id, 'type' => 'group', 'subtype' => 'teamfromgroup'])) {
                    // Connected to both group and team.
                    if ($teamscache = $DB->get_record('local_o365_teams_cache', ['objectid' => $grouprecord->objectid])) {
                        // Team record can be found in cache.
                        $existingconnection = html_writer::link($teamscache->url, $teamscache->name);
                        if (!$DB->record_exists('local_o365_objects',
                            ['type' => 'sdssection', 'subtype' => 'course', 'moodleid' => $course->id])) {
                            $updateurl = new moodle_url('/local/o365/acp.php',
                                ['mode' => 'teamconnections_update', 'course' => $course->id, 'sesskey' => sesskey()]);
                            $updatelabel = get_string('acp_teamconnections_table_update', 'local_o365');

                            $actions = [html_writer::link($updateurl, $updatelabel)];
                        } else {
                            $actions = [get_string('acp_coursesynccustom_sds_course', 'local_o365')];
                        }
                    } else {
                        // A matching record exists in local_o365_objects, but the team cannot be found.
                        $existingconnection = $grouprecord->o365name . get_string('acp_teamconnections_team_missing', 'local_o365');

                        $actions = [html_writer::span(get_string('acp_teamconnections_table_missing_team', 'local_o365'))];
                    }
                } else {
                    // Connected to group only.
                    $metadata = (!empty($grouprecord->metadata)) ? json_decode($grouprecord->metadata, true) : [];
                    if (is_array($metadata) && !empty($metadata['softdelete'])) {
                        // Deleted group connection.
                        $existingconnection = get_string('acp_teamconnections_not_connected', 'local_o365');
                        $connecturl = new moodle_url('/local/o365/acp.php',
                            ['mode' => 'teamconnections_connect', 'course' => $course->id, 'sesskey' => sesskey()]);
                        $connectlabel = get_string('acp_teamconnections_table_connect', 'local_o365');

                        $actions = [html_writer::link($connecturl, $connectlabel)];
                    } else if ($teamscache = $DB->get_record('local_o365_teams_cache', ['objectid' => $grouprecord->objectid])) {
                        // Connect the course with the team.
                        $teamobjectrecord = ['type' => 'group', 'subtype' => 'courseteam', 'objectid' => $teamscache->objectid,
                            'moodleid' => $course->id, 'o365name' => $teamscache->name, 'timecreated' => time(),
                            'timemodified' => time()];
                        $teamobjectrecord['id'] = $DB->insert_record('local_o365_objects', (object) $teamobjectrecord);

                        $existingconnection = html_writer::link($teamscache->url, $teamscache->name);

                        if (!$DB->record_exists('local_o365_objects',
                            ['type' => 'sdssection', 'subtype' => 'course', 'moodleid' => $course->id])) {
                            $updateurl = new moodle_url('/local/o365/acp.php',
                                ['mode' => 'teamconnections_update', 'course' => $course->id, 'sesskey' => sesskey()]);
                            $updatelabel = get_string('acp_teamconnections_table_update', 'local_o365');

                            $actions = [html_writer::link($updateurl, $updatelabel)];
                        }
                    } else {
                        // A team does not exist for the synced group.
                        $existingconnection = $grouprecord->o365name . get_string('acp_teamconnections_group_only', 'local_o365');

                        $actions = [html_writer::span(get_string('acp_teamconnections_table_cannot_create_team_from_group',
                            'local_o365'))];

                        if (!$DB->record_exists('local_o365_objects',
                            ['type' => 'sdssection', 'subtype' => 'course', 'moodleid' => $course->id])) {
                            $connecturl = new moodle_url('/local/o365/acp.php',
                                ['mode' => 'teamconnections_connect', 'course' => $course->id, 'sesskey' => sesskey()]);
                            $connectlabel = get_string('acp_teamconnections_table_connect_to_different_team', 'local_o365');
                            $actions[] = html_writer::link($connecturl, $connectlabel);
                        }
                    }
                }
            } else {
                $existingconnection = get_string('acp_teamconnections_not_connected', 'local_o365');

                $teamownerids = \local_o365\feature\coursesync\utils::get_team_owner_user_ids_by_course_id($course->id);
                if (!empty($teamownerids)) {
                    $connecturl = new moodle_url('/local/o365/acp.php',
                        ['mode' => 'teamconnections_connect', 'course' => $course->id, 'sesskey' => sesskey()]);
                    $connectlabel = get_string('acp_teamconnections_table_connect', 'local_o365');

                    $actions = [html_writer::link($connecturl, $connectlabel)];
                } else {
                    $actions[] = get_string('acp_teamconnections_no_owner', 'local_o365');
                }
            }

            $actionsfield = implode('<br/>', $actions);

            $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);

            $rowdata = [html_writer::link($courseurl, $course->fullname), $course->shortname, $existingconnection, $actionsfield,];

            $table->data[] = $rowdata;
        }

        $PAGE->requires->jquery();
        $this->standard_header();

        // Cache status.
        $teamscacheupdated = get_config('local_o365', 'teamscacheupdated');
        $updatecacheurl = new moodle_url('/local/o365/acp.php', ['mode' => 'teamconnections_update_cache', 'sesskey' => sesskey()]);
        $linkparams = ['updateurl' => $updatecacheurl->out()];
        if ($teamscacheupdated) {
            $linkparams['lastupdated'] = userdate($teamscacheupdated);
            echo html_writer::div(get_string('acp_teamconnections_cache_last_updated', 'local_o365', $linkparams));
        } else {
            echo html_writer::div(get_string('acp_teamconnections_cache_never_updated', 'local_o365', $linkparams));
        }

        // Search form.
        echo html_writer::tag('h5', get_string('search'));
        echo html_writer::start_tag('form', ['id' => 'coursesearchform', 'method' => 'get']);
        echo html_writer::start_tag('fieldset', ['class' => 'coursesearchbox invisiblefieldset']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'mode', 'value' => 'teamconnections']);
        echo html_writer::empty_tag('input',
            ['type' => 'text', 'id' => 'coursesearchbox', 'size' => 30, 'name' => 'search', 'value' => s($search)]);
        echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('go')]);
        echo html_writer::div(html_writer::tag('strong', get_string('acp_coursesynccustom_searchwarning', 'local_o365')));
        echo html_writer::end_tag('fieldset');
        echo html_writer::end_tag('form');
        echo html_writer::empty_tag('br');

        echo html_writer::tag('h5', get_string('courses'));
        echo html_writer::table($table);

        $searchtext = optional_param('search', '', PARAM_TEXT);
        $cururl = new moodle_url('/local/o365/acp.php', ['mode' => 'teamconnections', 'search' => $searchtext]);
        echo $OUTPUT->paging_bar($totalcount, $curpage, $perpage, $cururl);

        $this->standard_footer();
    }

    /**
     * Update Teams cache.
     */
    public function mode_teamconnections_update_cache() {
        confirm_sesskey();

        $graphclient = \local_o365\feature\coursesync\utils::get_graphclient();
        $coursesync = new main($graphclient);
        $coursesync->update_teams_cache();

        $redirecturl = new moodle_url('/local/o365/acp.php', ['mode' => 'teamconnections']);
        redirect($redirecturl, get_string('acp_teamconnections_teams_cache_updated', 'local_o365'));
    }

    /**
     * Connect a course to a Team.
     *
     * @throws moodle_exception
     */
    public function mode_teamconnections_connect() {
        global $DB, $PAGE;

        $this->set_title(get_string('acp_teamconnection', 'local_o365'));

        $courseid = required_param('course', PARAM_INT);
        confirm_sesskey();

        $redirecturl = new moodle_url('/local/o365/acp.php', ['mode' => 'teamconnections']);

        if (utils::is_connected() !== true) {
            throw new moodle_exception('acp_teamconnections_exception_not_configured', 'local_o365', $redirecturl);
        }

        if (!$course = $DB->get_record('course', ['id' => $courseid])) {
            throw new moodle_exception('acp_teamconnections_exception_course_not_exist', 'local_o365', $redirecturl);
        }

        if ($DB->record_exists('local_o365_objects', ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid])) {
            $updateurl = new moodle_url('/local/o365/acp.php',
                ['mode' => 'teamconnections_update', 'course' => $courseid, 'sesskey' => sesskey()]);
            redirect($updateurl);
        }

        [$teamsoptions, $unused] = \local_o365\feature\coursesync\utils::get_matching_team_options();

        $urlparams = ['mode' => 'teamconnections_connect', 'course' => $courseid];
        $connectteamsurl = new moodle_url('/local/o365/acp.php', $urlparams);
        $customdata = ['course' => $courseid, 'teamsoptions' => $teamsoptions];
        $mform = new teamsconnection($connectteamsurl, $customdata);

        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($fromform = $mform->get_data()) {
            $teamid = $fromform->team;

            if (!$teamid) {
                redirect($redirecturl);
            }

            if (!$teamcacherecord = $DB->get_record('local_o365_teams_cache', ['id' => $teamid])) {
                throw new moodle_exception('acp_teamconnections_exception_invalid_team_id', 'local_o365', $redirecturl);
            } else if ($DB->record_exists('local_o365_objects',
                ['type' => 'group', 'subtype' => 'course', 'objectid' => $teamcacherecord->objectid])) {
                throw new moodle_exception('acp_teamconnections_exception_team_already_connected', 'local_o365', $redirecturl);
            }

            // Create record in local_o365_object table.
            if ($grouprecord = $DB->get_record('local_o365_objects',
                ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid])) {
                $grouprecord->objectid = $teamcacherecord->objectid;
                $grouprecord->o365name = $teamcacherecord->name;
                $grouprecord->metadata = null;
                $DB->update_record('local_o365_objects', $grouprecord);
            } else {
                $grouprecord = new stdClass();
                $grouprecord->type = 'group';
                $grouprecord->subtype = 'course';
                $grouprecord->objectid = $teamcacherecord->objectid;
                $grouprecord->moodleid = $courseid;
                $grouprecord->o365name = $teamcacherecord->name;
                $grouprecord->timecreated = time();
                $grouprecord->timemodified = $grouprecord->timecreated;
                $DB->insert_record('local_o365_objects', $grouprecord);
            }

            $DB->delete_records('local_o365_objects', ['type' => 'group', 'subtype' => 'courseteam', 'moodleid' => $courseid]);
            $DB->delete_records('local_o365_objects', ['type' => 'group', 'subtype' => 'teamfromgroup', 'moodleid' => $courseid]);
            $teamrecord = new stdClass();
            $teamrecord->type = 'group';
            $teamrecord->subtype = 'courseteam';
            $teamrecord->objectid = $teamcacherecord->objectid;
            $teamrecord->moodleid = $courseid;
            $teamrecord->o365name = $teamcacherecord->name;
            $teamrecord->timecreated = time();
            $teamrecord->timemodified = $teamrecord->timecreated;
            $DB->insert_record('local_o365_objects', $teamrecord);

            // Update course sync settings.
            \local_o365\feature\coursesync\utils::set_course_sync_enabled($courseid, true);

            // Create course sync client.
            $graphclient = \local_o365\feature\coursesync\utils::get_graphclient();
            $coursesync = new main($graphclient);

            // Sync users.
            $coursesync->resync_group_owners_and_members($courseid, $teamcacherecord->objectid);

            // Provision app, add tab.
            $coursesync->install_moodle_app_in_team($teamcacherecord->objectid, $courseid);

            redirect($redirecturl, get_string('acp_teamconnections_course_connected', 'local_o365'));
        } else {
            $url = new moodle_url($this->url, ['mode' => 'teamconnections']);
            $PAGE->navbar->add(get_string('acp_teamconnections', 'local_o365'), $url);
            $PAGE->requires->jquery();
            $this->standard_header();
            echo html_writer::tag('h4', get_string('acp_teamconnections_form_connect_course', 'local_o365', $course->fullname));
            echo html_writer::tag('h5', get_string('acp_teamconnections_form_sds_warning', 'local_o365'),
                ['class' => 'warning red']);
            $mform->display();
            $this->standard_footer();
        }
    }

    /**
     * Update the connection between a course and a Team.
     *
     * @throws moodle_exception
     */
    public function mode_teamconnections_update() {
        global $DB, $PAGE;

        $this->set_title(get_string('acp_teamconnection', 'local_o365'));

        $courseid = required_param('course', PARAM_INT);
        confirm_sesskey();

        $redirecturl = new moodle_url('/local/o365/acp.php', ['mode' => 'teamconnections']);

        if (utils::is_connected() !== true) {
            throw new moodle_exception('acp_teamconnections_exception_not_configured', 'local_o365', $redirecturl);
        }

        if (!$course = $DB->get_record('course', ['id' => $courseid])) {
            throw new moodle_exception('acp_teamconnections_exception_course_not_exist', 'local_o365', $redirecturl);
        }

        if (!$groupobject = $DB->get_record('local_o365_objects',
            ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid])) {
            $connecturl = new moodle_url('/local/o365/acp.php',
                ['mode' => 'teamconnections_connect', 'course' => $courseid, 'sesskey' => sesskey()]);
            redirect($connecturl);
        }

        [$teamsoptions, $connectedteamrecordid] = \local_o365\feature\coursesync\utils::get_matching_team_options(
            $groupobject->objectid);

        $urlparams = ['mode' => 'teamconnections_update', 'course' => $courseid];
        $updateconnectionurl = new moodle_url('/local/o365/acp.php', $urlparams);
        $customdata = ['course' => $courseid, 'teamsoptions' => $teamsoptions];
        $mform = new teamsconnection($updateconnectionurl, $customdata);
        $mform->set_data(['team' => $connectedteamrecordid]);

        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($fromform = $mform->get_data()) {
            $teamid = $fromform->team;

            if (!$teamid) {
                redirect($redirecturl);
            }

            if (!$teamcacherecord = $DB->get_record('local_o365_teams_cache', ['id' => $teamid])) {
                throw new moodle_exception('acp_teamconnections_exception_invalid_team_id', 'local_o365', $redirecturl);
            } else if ($teamobjectrecord = $DB->get_record('local_o365_objects',
                ['type' => 'group', 'subtype' => 'course', 'objectid' => $teamcacherecord->objectid])) {
                if ($teamobjectrecord->moodleid == $courseid) {
                    redirect($redirecturl);
                } else {
                    throw new moodle_exception('acp_teamconnections_exception_team_already_connected', 'local_o365', $redirecturl);
                }
            }

            // Create record in local_o365_object table.
            if ($grouprecord =
                $DB->get_record('local_o365_objects', ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid])) {
                $grouprecord->objectid = $teamcacherecord->objectid;
                $grouprecord->o365name = $teamcacherecord->name;
                $grouprecord->metadata = null;
                $DB->update_record('local_o365_objects', $grouprecord);
            } else {
                $grouprecord = new stdClass();
                $grouprecord->type = 'group';
                $grouprecord->subtype = 'course';
                $grouprecord->objectid = $teamcacherecord->objectid;
                $grouprecord->moodleid = $courseid;
                $grouprecord->o365name = $teamcacherecord->name;
                $grouprecord->timecreated = time();
                $grouprecord->timemodified = $grouprecord->timecreated;
                $DB->insert_record('local_o365_objects', $grouprecord);
            }

            $DB->delete_records('local_o365_objects', ['type' => 'group', 'subtype' => 'courseteam', 'moodleid' => $courseid]);
            $DB->delete_records('local_o365_objects', ['type' => 'group', 'subtype' => 'teamfromgroup', 'moodleid' => $courseid]);
            $teamrecord = new stdClass();
            $teamrecord->type = 'group';
            $teamrecord->subtype = 'courseteam';
            $teamrecord->objectid = $teamcacherecord->objectid;
            $teamrecord->moodleid = $courseid;
            $teamrecord->o365name = $teamcacherecord->name;
            $teamrecord->timecreated = time();
            $teamrecord->timemodified = $teamrecord->timecreated;
            $DB->insert_record('local_o365_objects', $teamrecord);

            // Update course sync settings.
            \local_o365\feature\coursesync\utils::set_course_sync_enabled($courseid, true);

            // Create course sync client.
            $graphclient = \local_o365\feature\coursesync\utils::get_graphclient();
            $coursesync = new main($graphclient);

            // Sync users.
            $coursesync->resync_group_owners_and_members($courseid, $teamcacherecord->objectid);

            // Provision app, add tab.
            $coursesync->install_moodle_app_in_team($teamcacherecord->objectid, $courseid);

            redirect($redirecturl, get_string('acp_teamconnections_course_connected', 'local_o365'));
        } else {
            $url = new moodle_url($this->url, ['mode' => 'teamconnections']);
            $PAGE->navbar->add(get_string('acp_teamconnections', 'local_o365'), $url);
            $PAGE->requires->jquery();
            $this->standard_header();
            echo html_writer::tag('h4', get_string('acp_teamconnections_form_connect_course', 'local_o365', $course->fullname));
            echo html_writer::tag('h5', get_string('acp_teamconnections_form_sds_warning', 'local_o365'),
                ['class' => 'warning red']);
            $mform->display();
            $this->standard_footer();
        }
    }

    /**
     * Resync deleted Microsoft 365 groups for courses.
     */
    public function mode_maintenance_recreatedeletedgroups() {
        global $DB, $PAGE;

        $this->set_title(get_string('acp_maintenance_recreatedeletedgroups', 'local_o365'));

        $coursesenabled = \local_o365\feature\coursesync\utils::get_enabled_courses();

        $graphclient = \local_o365\feature\coursesync\utils::get_graphclient();
        $coursesync = new main($graphclient, true);
        $groupids = $coursesync->get_all_group_ids();

        $sql = "SELECT *
                  FROM {local_o365_objects}
                 WHERE type = :group
                   AND subtype = :course";

        $groupobjects = $DB->get_recordset_sql($sql, ['group' => 'group', 'course' => 'course']);

        $groupcheckstatushead = [
            get_string('acp_maintenance_recreatedeletedgroups_group_type', 'local_o365'),
            get_string('acp_maintenance_recreatedeletedgroups_course', 'local_o365'),
            get_string('acp_maintenance_recreatedeletedgroups_course_group', 'local_o365'),
            get_string('acp_maintenance_recreatedeletedgroups_status', 'local_o365'),
        ];
        $groupcheckstatus = [];

        foreach ($groupobjects as $groupobject) {
            // Course connection record.
            $groupconnectiontype = 'course';
            if (!$course = $DB->get_record('course', ['id' => $groupobject->moodleid])) {
                // Moodle course doesn't exist. Delete the invalid record.
                $DB->delete_records('local_o365_objects',
                    ['type' => 'group', 'subtype' => 'course', 'moodleid' => $groupobject->moodleid]);
                $DB->delete_records('local_o365_objects',
                    ['type' => 'group', 'subtype' => 'courseteam', 'moodleid' => $groupobject->moodleid]);
                $DB->delete_records('local_o365_objects',
                    ['type' => 'group', 'subtype' => 'teamfromgroup', 'moodleid' => $groupobject->moodleid]);
                continue;
            }
            $group = null;
            $groupname = '';

            $courselink = html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]), $course->fullname);
            $groupcheckstatusitem = [
                get_string('acp_maintenance_recreatedeletedgroups_group_type_' . $groupconnectiontype, 'local_o365'),
                $courselink,
                $groupname,
            ];

            $status = '';
            if (!in_array($groupobject->objectid, $groupids)) {
                $DB->delete_records('local_o365_objects', ['id' => $groupobject->id]);
                if (!in_array($course->id, $coursesenabled)) {
                    $status = get_string('acp_maintenance_recreatedeletedgroups_status_sync_disabled', 'local_o365');
                } else {
                    // Group should exist but not. Try to create the group.
                    if ($coursesync->create_group_for_course($course)) {
                        $status = get_string('acp_maintenance_recreatedeletedgroups_status_created_success', 'local_o365');
                    } else {
                        $status = get_string('acp_maintenance_recreatedeletedgroups_status_created_fail', 'local_o365');
                    }
                }
            }

            if ($status) {
                $groupcheckstatus[] = $groupcheckstatusitem;
            }
        }

        $url = new moodle_url($this->url, ['mode' => 'recreatedeletedgroups']);
        $PAGE->navbar->add(get_string('acp_maintenance_recreatedeletedgroups', 'local_o365'), $url);
        $PAGE->requires->jquery();
        $this->standard_header();
        if ($groupcheckstatus) {
            $groupstable = new html_table();
            $groupstable->head = $groupcheckstatushead;
            $groupstable->data = $groupcheckstatus;
            echo html_writer::table($groupstable);
        } else {
            echo html_writer::tag('h5', get_string('acp_maintenance_recreatedeletedgroups_all_groups_exist', 'local_o365'));
        }
        $this->standard_footer();
    }

    /**
     * Resync Microsoft 365 group membership for connected courses.
     */
    public function mode_maintenance_resyncgroupusers() {
        global $DB, $PAGE;

        $this->set_title(get_string('acp_maintenance_resyncgroupusers', 'local_o365'));

        $courseid = optional_param('courseid', 0, PARAM_INT);
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_EXTRA);
        disable_output_buffering();

        $graphclient = \local_o365\feature\coursesync\utils::get_graphclient();
        $coursesync = new main($graphclient, true);

        $coursesenabled = \local_o365\feature\coursesync\utils::get_enabled_courses();

        $sql = 'SELECT crs.id,
                       crs.fullname,
                       obj.objectid as groupobjectid
                  FROM {course} crs
                  JOIN {local_o365_objects} obj ON obj.type = ? AND obj.subtype = ? AND obj.moodleid = crs.id
                 WHERE crs.id != ?';
        $params = ['group', 'course', SITEID];
        if (!empty($courseid)) {
            $sql .= ' AND crs.id = ?';
            $params[] = $courseid;
        }
        if (is_array($coursesenabled)) {
            [$coursesinsql, $coursesparams] = $DB->get_in_or_equal($coursesenabled);
            $sql .= ' AND crs.id ' . $coursesinsql;
            $params = array_merge($params, $coursesparams);
        }
        $courses = $DB->get_recordset_sql($sql, $params);
        $outputsbycourse = [];
        foreach ($courses as $course) {
            $courseitem = [];
            $courseitem['link'] = html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]), $course->fullname);
            try {
                ob_start();
                $coursesync->resync_group_owners_and_members($course->id, $course->groupobjectid);
                $courseitem['output'] = ob_get_contents();
                ob_clean();
                $courseitem['output'] = '<pre>' . $courseitem['output'] . '</pre>';
            } catch (Exception $e) {
                $courseitem['output'] = 'Could not sync course ' . $course->id . '. Reason: ' . $e->getMessage();
            }
            $outputsbycourse[] = $courseitem;
        }
        $courses->close();

        $url = new moodle_url($this->url, ['mode' => 'resyncgroupusers']);
        $PAGE->navbar->add(get_string('acp_maintenance_resyncgroupusers', 'local_o365'), $url);
        $PAGE->requires->jquery();
        $this->standard_header();
        if ($outputsbycourse) {
            $coursetables = new html_table();
            $coursetables->head = [
                get_string('course'),
                get_string('acp_maintenance_resyncgroupusers_course_output', 'local_o365'),
            ];
            $coursetables->data = $outputsbycourse;
            echo html_writer::table($coursetables);
        } else {
            echo html_writer::tag('h5', get_string('acp_maintenance_resyncgroupusers_no_course', 'local_o365'));
        }
        $this->standard_footer();
    }

    /**
     * Export debug data.
     *
     * @return false|void
     */
    public function mode_maintenance_debugdata() {
        global $CFG;

        if (!empty($CFG->local_o365_disabledebugdata)) {
            return false;
        }

        $pluginmanager = core_plugin_manager::instance();

        $plugins = [
            'auth_oidc' => [
                'authendpoint',
                'tokenendpoint',
                'oidcresource',
                'oidcscope',
                'redirecturi',
                'forceredirect',
                'autoappend',
                'domainhint',
                'loginflow',
                'debugmode',
                'userrestrictions',
                'userrestrictionscasesensitive',
                'single_sign_off',
                'logouturi',
            ],
            'block_microsoft' => [
                'settings_showmydelve',
                'settings_showemail',
                'settings_showmyforms',
                'settings_showonenotenotebook',
                'settings_showonedrive',
                'settings_showmsstream',
                'settings_showmsteams',
                'settings_showsways',
                'settings_showoutlooksync',
                'settings_showpreferences',
                'settings_showo365connect',
                'settings_showmanageo365conection',
                'settings_showcoursespsite',
                'showo365download',
                'settings_geto365link',
                'settings_showcoursegroup',
            ],
            'local_o365' => [
                'enableapponlyaccess',
                'apptokens',
                'systemtokens',
                'systemapiuser',
                'aadsync',
                'aadtenant',
                'aadtenantid',
                'azuresetupresult',
                'chineseapi',
                'coursesync',
                'coursesynccustom',
                'debugmode',
                'enableunifiedapi',
                'disablegraphapi',
                'odburl',
                'photoexpire',
                'usersynccreationrestriction',
                'task_usersync_lastdelete',
                'task_usersync_lastdeltatoken',
                'task_usersync_lastskiptokendelta',
                'task_usersync_lastskiptokenfull',
                'unifiedapiactive',
                'delete_group_on_course_deletion',
                'delete_group_on_course_sync_disabled',
                'courses_per_task',
                'team_name_prefix',
                'team_name_course',
                'team_name_suffix',
                'group_mail_alias_prefix',
                'group_mail_alias_course',
                'group_mail_alias_suffix',
                'team_name_sync',
                'multitenants',
                'legacymultitenants',
                'course_reset_teams',
                'reset_team_name_prefix',
                'reset_group_name_prefix',
                'switchauthminupnsplit0',
                'customtheme',
                'sdsenrolmentenabled',
                'sdsenrolmentstudentrole',
                'sdsenrolmentteacherrole',
                'sdsfieldmap',
                'sdsprofilesync',
                'sdsprofilesyncenabled',
                'sdsschools',
                'sdssyncenrolmenttosds',
                'sdsteamsenabled',
                'bot_app_id',
                'bot_app_password',
                'bot_sharedsecret',
                'teams_moodle_app_external_id',
                'teams_moodle_app_short_name',
                'bot_feature_enabled',
                'bot_webhook_endpoint',
                'manifest_downloaded',
                'moodle_app_id',
                'teamscacheupdated',
                'sync_new_course',
                'ratelimit',
                'courseresetsettings',
                'course_sync_per_course',
            ],
            'local_office365' => [],
            'local_onenote' => [],
            'assignfeedback_onenote' => [
                'default',
            ],
            'assignsubmission_onenote' => [
                'default',
                'maxbytes',
            ],
            'repository_office365' => [],
            'theme_boost_o365teams' => [
                'showteamfeedbacklink',
                'footer_stamp',
            ],
        ];

        $authoidcplugin = get_auth_plugin('oidc');
        $profilefields = array_merge($authoidcplugin->userfields, $authoidcplugin->get_custom_user_profile_fields());
        foreach ($profilefields as $profilefield) {
            $plugins['auth_oidc'][] = 'field_lock_' . $profilefield;
            $plugins['auth_oidc'][] = 'field_map_' . $profilefield;
            $plugins['auth_oidc'][] = 'field_updatelocal_' . $profilefield;
        }

        $configdata = [];

        $configdata['moodlecfg'] = [
            'dbtype' => $CFG->dbtype,
            'debug' => $CFG->debug,
            'debugdisplay' => $CFG->debugdisplay,
            'debugdeveloper' => $CFG->debugdeveloper,
            'auth' => $CFG->auth,
            'timezone' => $CFG->timezone,
            'forcetimezone' => $CFG->forcetimezone,
            'authpreventaccountcreation' => $CFG->authpreventaccountcreation,
            'alternateloginurl' => $CFG->alternateloginurl,
            'release' => $CFG->release,
            'version' => $CFG->version,
        ];

        $configdata['plugin_data'] = [];
        foreach ($plugins as $plugin => $settings) {
            $plugintype = substr($plugin, 0, strpos($plugin, '_'));
            $pluginsubtype = substr($plugin, strpos($plugin, '_') + 1);

            $plugindata = [];
            $plugincfg = get_config($plugin);

            $plugindata['version'] = (isset($plugincfg->version)) ? $plugincfg->version : 'null';

            $enabled = $pluginmanager->get_enabled_plugins($plugintype);
            $plugindata['enabled'] = (isset($enabled[$pluginsubtype])) ? 1 : 0;

            foreach ($settings as $setting) {
                $plugindata[$setting] = (isset($plugincfg->$setting)) ? $plugincfg->$setting : null;
            }

            $configdata['plugin_data'][$plugin] = $plugindata;
        }

        echo json_encode($configdata);
    }

    /**
     * Maintenance tools main page.
     */
    public function mode_maintenance() {
        global $PAGE, $CFG;

        $this->set_title(get_string('acp_maintenance', 'local_o365'));

        $PAGE->navbar->add(get_string('acp_maintenance', 'local_o365'), new moodle_url($this->url, ['mode' => 'maintenance']));
        $PAGE->requires->jquery();
        $this->standard_header();

        echo html_writer::div(get_string('acp_maintenance_desc', 'local_o365'));
        echo html_writer::empty_tag('br');
        echo html_writer::div(get_string('acp_maintenance_warning', 'local_o365'), 'alert alert-info');

        $toolurl = new moodle_url($this->url, ['mode' => 'maintenance_resyncgroupusers']);
        $toolname = get_string('acp_maintenance_resyncgroupusers', 'local_o365');
        echo html_writer::link($toolurl, $toolname, ['target' => '_blank']);
        echo html_writer::div(get_string('acp_maintenance_resyncgroupusers_desc', 'local_o365'));

        $toolurl = new moodle_url($this->url, ['mode' => 'maintenance_recreatedeletedgroups']);
        $toolname = get_string('acp_maintenance_recreatedeletedgroups', 'local_o365');
        echo html_writer::empty_tag('br');
        echo html_writer::link($toolurl, $toolname, ['target' => '_blank']);
        echo html_writer::div(get_string('acp_maintenance_recreatedeletedgroups_desc', 'local_o365'));

        if (empty($CFG->local_o365_disabledebugdata)) {
            $toolurl = new moodle_url($this->url, ['mode' => 'maintenance_debugdata']);
            $toolname = get_string('acp_maintenance_debugdata', 'local_o365');
            echo html_writer::empty_tag('br');
            echo html_writer::link($toolurl, $toolname);
            echo html_writer::div(get_string('acp_maintenance_debugdata_desc', 'local_o365'));
        }

        $toolurl = new moodle_url('/auth/oidc/cleanupoidctokens.php');
        $toolname = get_string('cfg_cleanupoidctokens_key', 'auth_oidc');
        echo html_writer::empty_tag('br');
        echo html_writer::link($toolurl, $toolname, ['target' => '_blank']);
        echo html_writer::div(get_string('cfg_cleanupoidctokens_desc', 'auth_oidc'));

        // Clear delta token.
        $toolurl = new moodle_url($this->url, ['mode' => 'maintenance_cleandeltatoken']);
        $toolname = get_string('acp_maintenance_cleandeltatoken', 'local_o365');
        echo html_writer::empty_tag('br');
        echo html_writer::link($toolurl, $toolname);
        echo html_writer::div(get_string('acp_maintenance_cleandeltatoken_desc', 'local_o365'));

        $this->standard_footer();
    }

    /**
     * Clean up user sync delta token.
     */
    public function mode_maintenance_cleandeltatoken() {
        global $PAGE;

        $this->set_title(get_string('acp_maintenance_cleandeltatoken', 'local_o365'));

        set_config('task_usersync_lastdeltatoken', '', 'local_o365');
        set_config('task_usersync_lastskiptokendelta', '', 'local_o365');

        $url = new moodle_url($this->url, ['mode' => 'cleandeltatoken']);
        $PAGE->navbar->add(get_string('acp_maintenance_cleandeltatoken', 'local_o365'), $url);
        $PAGE->requires->jquery();
        $this->standard_header();
        echo html_writer::tag('h5', get_string('acp_maintenance_cleandeltatoken_completed', 'local_o365'));
        $this->standard_footer();
    }

    /**
     * User connection management.
     */
    public function mode_userconnections() {
        global $PAGE, $CFG;

        $this->set_title(get_string('acp_userconnections', 'local_o365'));

        $PAGE->navbar->add(get_string('acp_userconnections', 'local_o365'),
            new moodle_url($this->url, ['mode' => 'userconnections']));

        $PAGE->requires->jquery();
        $this->standard_header();

        $searchurl = new moodle_url('/local/o365/acp.php', ['mode' => 'userconnections']);
        $filterfields = ['o365username' => 0, 'realname' => 0, 'username' => 0, 'idnumber' => 1, 'firstname' => 1, 'lastname' => 1,
            'email' => 1,];
        $ufiltering = new filtering($filterfields, $searchurl);
        [$extrasql, $params] = $ufiltering->get_sql_filter();
        [$o365usernamesql, $o365usernameparams] = $ufiltering->get_filter_o365username();

        $ufiltering->display_add();
        $ufiltering->display_active();

        $table = new table('local_o365_userconnections');
        $table->define_baseurl($CFG->wwwroot . '/local/o365/acp.php?mode=userconnections');
        $table->set_where($extrasql, $params);
        $table->set_having($o365usernamesql, $o365usernameparams);
        $table->out(25, true);

        $this->standard_footer();
    }

    /**
     * Resync action from the userconnections tool.
     *
     * @return bool
     */
    public function mode_userconnections_resync() : bool {
        global $DB;
        $userid = required_param('userid', PARAM_INT);
        confirm_sesskey();

        if (utils::is_connected() !== true) {
            mtrace('Microsoft 365 not configured');
            return false;
        }

        // Perform prechecks.
        $userrecord = core_user::get_user($userid, '*', MUST_EXIST);
        $isguestuser = false;
        if (stripos($userrecord->username, '_ext_') !== false) {
            $isguestuser = true;
        }

        $params = ['type' => 'user', 'moodleid' => $userid];
        $objectrecord = $DB->get_record('local_o365_objects', $params);
        if (empty($objectrecord) || empty($objectrecord->objectid)) {
            throw new moodle_exception('acp_userconnections_resync_nodata', 'local_o365');
        }

        // Get aad data.
        $usersync = new \local_o365\feature\usersync\main();
        $userdata = $usersync->get_user($objectrecord->objectid, $isguestuser);
        echo '<pre>';
        $usersync->sync_users([$userdata]);
        echo '</pre>';

        return true;
    }

    /**
     * Manual match action from the userconnections tool.
     */
    public function mode_userconnections_manualmatch() {
        global $DB, $PAGE;

        $userid = required_param('userid', PARAM_INT);
        confirm_sesskey();

        // Perform prechecks.
        $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

        // Check whether Moodle user is already o365 connected.
        if (utils::is_o365_connected($userid)) {
            throw new moodle_exception('acp_userconnections_manualmatch_error_muserconnected', 'local_o365');
        }

        // Check existing matches for Moodle user.
        $existingmatchforuser = $DB->get_record('local_o365_connections', ['muserid' => $userid]);
        if (!empty($existingmatchforuser)) {
            throw new moodle_exception('acp_userconnections_manualmatch_error_musermatched', 'local_o365');
        }

        $urlparams = ['mode' => 'userconnections_manualmatch', 'userid' => $userid];
        $redirect = new moodle_url('/local/o365/acp.php', $urlparams);
        $customdata = ['userid' => $userid];
        $mform = new manualusermatch($redirect, $customdata);
        if ($fromform = $mform->get_data()) {
            $o365username = trim($fromform->o365username);

            // Check existing matches for Microsoft user.
            $existingmatchforo365user = $DB->get_record('local_o365_connections', ['aadupn' => $o365username]);
            if (!empty($existingmatchforo365user)) {
                throw new moodle_exception('acp_userconnections_manualmatch_error_o365usermatched', 'local_o365');
            }

            // Check existing tokens for Microsoft 365 user (indicates o365 user is already connected to someone).
            $existingtokenforo365user = $DB->get_record('auth_oidc_token', ['oidcusername' => $o365username]);
            if (!empty($existingtokenforo365user)) {
                throw new moodle_exception('acp_userconnections_manualmatch_error_o365userconnected', 'local_o365');
            }

            // Check if a o365 user object record already exists.
            $params = ['moodleid' => $userid, 'type' => 'user',];
            $existingobject = $DB->get_record('local_o365_objects', $params);
            if (!empty($existingobject) && $existingobject->o365name === $o365username) {
                throw new moodle_exception('acp_userconnections_manualmatch_error_muserconnected2', 'local_o365');
            }

            $uselogin = (!empty($fromform->uselogin)) ? 1 : 0;
            $matchrec = (object) ['muserid' => $userid, 'aadupn' => $o365username, 'uselogin' => $uselogin,];
            $DB->insert_record('local_o365_connections', $matchrec);
            redirect(new moodle_url('/local/o365/acp.php', ['mode' => 'userconnections']));
            die();
        }

        $url = new moodle_url($this->url, ['mode' => 'userconnections']);
        $PAGE->navbar->add(get_string('acp_userconnections', 'local_o365'), $url);
        $PAGE->requires->jquery();
        $this->standard_header();
        $mform->display();
        $this->standard_footer();
    }

    /**
     * Unmatch action from the userconnections tool.
     */
    public function mode_userconnections_unmatch() {
        global $DB, $PAGE;

        $userid = required_param('userid', PARAM_INT);
        confirm_sesskey();
        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
        $confirmed = optional_param('confirmed', 0, PARAM_INT);
        if (!empty($confirmed)) {
            $DB->delete_records('local_o365_connections', ['muserid' => $userid]);
            redirect(new moodle_url('/local/o365/acp.php', ['mode' => 'userconnections']));
            die();
        } else {
            $url = new moodle_url($this->url, ['mode' => 'userconnections']);
            $PAGE->navbar->add(get_string('acp_userconnections', 'local_o365'), $url);
            $PAGE->requires->jquery();
            $this->standard_header();
            $message = get_string('acp_userconnections_table_unmatch_confirmmsg', 'local_o365', $user->username);
            $message .= '<br /><br />';
            $urlparams = ['mode' => 'userconnections_unmatch', 'userid' => $userid, 'confirmed' => 1, 'sesskey' => sesskey(),];
            $url = new moodle_url('/local/o365/acp.php', $urlparams);
            $label = get_string('acp_userconnections_table_unmatch', 'local_o365');
            $message .= html_writer::link($url, $label);
            echo html_writer::tag('div', $message, ['class' => 'alert alert-info', 'style' => 'text-align:center']);
            $this->standard_footer();
        }
    }

    /**
     * Disconnect action from the userconnections tool.
     */
    public function mode_userconnections_disconnect() {
        global $CFG, $DB, $PAGE;

        require_once($CFG->dirroot . '/auth/oidc/auth.php');
        $userid = required_param('userid', PARAM_INT);
        confirm_sesskey();
        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
        $confirmed = optional_param('confirmed', 0, PARAM_INT);
        if (!empty($confirmed)) {
            $auth = new auth_plugin_oidc;
            $auth->set_httpclient(new httpclient());
            $redirect = new moodle_url('/local/o365/acp.php', ['mode' => 'userconnections']);
            $selfurlparams = ['mode' => 'userconnections_disconnect', 'userid' => $userid, 'confirmed' => 1];
            $selfurl = new moodle_url('/local/o365/acp.php', $selfurlparams);
            $justtokens = !(($user->auth == 'oidc'));
            $auth->disconnect($justtokens, false, $redirect, $selfurl, $userid);
            die();
        } else {
            $url = new moodle_url($this->url, ['mode' => 'userconnections']);
            $PAGE->navbar->add(get_string('acp_userconnections', 'local_o365'), $url);
            $PAGE->requires->jquery();
            $this->standard_header();
            $message = get_string('acp_userconnections_table_disconnect_confirmmsg', 'local_o365', $user->username);
            $message .= '<br /><br />';
            $urlparams = ['mode' => 'userconnections_disconnect', 'userid' => $userid, 'confirmed' => 1, 'sesskey' => sesskey(),];
            $url = new moodle_url('/local/o365/acp.php', $urlparams);
            $label = get_string('acp_userconnections_table_disconnect', 'local_o365');
            $message .= html_writer::link($url, $label);
            echo html_writer::tag('div', $message, ['class' => 'alert alert-info', 'style' => 'text-align:center']);
            $this->standard_footer();
        }
    }
}
