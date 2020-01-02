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
 * Kaltura config settings script.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

// It must be included from a Moodle page.
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot.'/local/kaltura/locallib.php');

if ($hassiteconfig) {

    // Add local plug-in configuration settings link to the navigation block.
    $settings = new admin_settingpage('local_kaltura', get_string('pluginname', 'local_kaltura'));
    $ADMIN->add('localplugins', $settings);

    $configsettings = get_config(KALTURA_PLUGIN_NAME);
    $missinginfo = get_string('missing_required_info', 'local_kaltura');
    $message = '';

    if (isset($configsettings->kaf_uri) && !empty($configsettings->kaf_uri)) {
        $url = local_kaltura_add_protocol_to_url($configsettings->kaf_uri);
        if (empty($url)) {
            $message = get_string('invalid_url', 'local_kaltura');
        } else {
            $message = $url.'/admin';
            $message = html_writer::tag('a', $message, array('href' => $message));
            $message = html_writer::tag('center', $message);
        }
    }

    if (empty($configsettings->partner_id) || empty($configsettings->adminsecret)) {
        $message .= html_writer::empty_tag('br');
        $message .= html_writer::tag('center', $missinginfo);
    }

    // Pull the Kaltura repository settings (if exists).
    $kalrepoconfig = get_config(KALTURA_REPO_NAME);
    $repoprofileid = (!empty($kalrepoconfig) && !empty($kalrepoconfig->metadata_profile_id)) ? $kalrepoconfig->metadata_profile_id : '';

    $adminsetting = new admin_setting_heading('kaf_url_heading', get_string('kaf_configuration_hdr', 'local_kaltura'), $message);
    $adminsetting->plugin = KALTURA_PLUGIN_NAME;
    $settings->add($adminsetting);

    $adminsetting = new admin_setting_configtext('kaf_uri', get_string('kaf_uri', 'local_kaltura'), get_string('kaf_uri_desc', 'local_kaltura'), '', PARAM_URL);
    $adminsetting->plugin = KALTURA_PLUGIN_NAME;
    $settings->add($adminsetting);

    $adminsetting = new admin_setting_configtext('uri', get_string('server_uri', 'local_kaltura'), get_string('server_uri_desc', 'local_kaltura'), KALTURA_DEFAULT_URI, PARAM_URL);
    $adminsetting->plugin = KALTURA_PLUGIN_NAME;
    $settings->add($adminsetting);

    $adminsetting = new admin_setting_configtext('partner_id', get_string('partner_id', 'local_kaltura'), get_string('partner_id_desc', 'local_kaltura'), '', PARAM_INT);
    $adminsetting->plugin = KALTURA_PLUGIN_NAME;
    $settings->add($adminsetting);

    $adminsetting = new admin_setting_configtext('adminsecret', get_string('admin_secret', 'local_kaltura'), get_string('admin_secret_desc', 'local_kaltura'), '', PARAM_ALPHANUM);
    $adminsetting->plugin = KALTURA_PLUGIN_NAME;
    $settings->add($adminsetting);

    $url = new moodle_url('/local/kaltura/download_log.php');
    $adminsetting = new admin_setting_configcheckbox('enable_logging', get_string('trace_log', 'local_kaltura'), get_string('trace_log_desc', 'local_kaltura', $url->out()), 0);
    $adminsetting->plugin = KALTURA_PLUGIN_NAME;
    $settings->add($adminsetting);

    $adminsetting = new admin_setting_configcheckbox('enable_submission', get_string('enable_submission', 'local_kaltura'), get_string('enable_submission_desc', 'local_kaltura'), 0);
    $adminsetting->plugin = KALTURA_PLUGIN_NAME;
    $settings->add($adminsetting);

    if (isset($configsettings->migration_yes) && $configsettings->migration_yes == 1) {
        $url = new moodle_url('/local/kaltura/migration.php');

        $adminsetting = new admin_setting_heading('migration_url_heading', get_string('migration_notice', 'local_kaltura', $url->out()), '');
        $adminsetting->plugin = KALTURA_PLUGIN_NAME;
        $settings->add($adminsetting);
    }
}
