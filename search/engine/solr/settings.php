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
 * Solr search engine settings.
 *
 * @package    search_solr
 * @copyright  2015 Daniel Neis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    if (!during_initial_install()) {
        if (!function_exists('solr_get_version')) {
            $settings->add(new admin_setting_heading('search_solr_settings', '', get_string('extensionerror', 'search_solr')));

        } else {
            $settings->add(new admin_setting_heading('search_solr_connection',
                    new lang_string('connectionsettings', 'search_solr'), ''));
            $settings->add(new admin_setting_configtext('search_solr/server_hostname', new lang_string('solrserverhostname', 'search_solr'), new lang_string('solrserverhostname_desc', 'search_solr'), '127.0.0.1', PARAM_HOST));
            $settings->add(new admin_setting_configtext('search_solr/indexname', new lang_string('solrindexname', 'search_solr'), '', '', PARAM_ALPHANUMEXT));
            $settings->add(new admin_setting_configcheckbox('search_solr/secure', new lang_string('solrsecuremode', 'search_solr'), '', 0, 1, 0));

            $secure = get_config('search_solr', 'secure');
            $defaultport = !empty($secure) ? 8443 : 8983;
            $settings->add(new admin_setting_configtext('search_solr/server_port', new lang_string('solrhttpconnectionport', 'search_solr'), '', $defaultport, PARAM_INT));
            $settings->add(new admin_setting_configtext('search_solr/server_username', new lang_string('solrauthuser', 'search_solr'), '', '', PARAM_RAW));
            $settings->add(new admin_setting_configpasswordunmask('search_solr/server_password', new lang_string('solrauthpassword', 'search_solr'), '', ''));
            $settings->add(new admin_setting_configtext('search_solr/server_timeout', new lang_string('solrhttpconnectiontimeout', 'search_solr'), new lang_string('solrhttpconnectiontimeout_desc', 'search_solr'), 30, PARAM_INT));
            $settings->add(new admin_setting_configtext('search_solr/ssl_cert', new lang_string('solrsslcert', 'search_solr'), new lang_string('solrsslcert_desc', 'search_solr'), '', PARAM_RAW));
            $settings->add(new admin_setting_configtext('search_solr/ssl_key', new lang_string('solrsslkey', 'search_solr'), new lang_string('solrsslkey_desc', 'search_solr'), '', PARAM_RAW));
            $settings->add(new admin_setting_configpasswordunmask('search_solr/ssl_keypassword', new lang_string('solrsslkeypassword', 'search_solr'), new lang_string('solrsslkeypassword_desc', 'search_solr'), ''));
            $settings->add(new admin_setting_configtext('search_solr/ssl_cainfo', new lang_string('solrsslcainfo', 'search_solr'), new lang_string('solrsslcainfo_desc', 'search_solr'), '', PARAM_RAW));
            $settings->add(new admin_setting_configtext('search_solr/ssl_capath', new lang_string('solrsslcapath', 'search_solr'), new lang_string('solrsslcapath_desc', 'search_solr'), '', PARAM_RAW));

            $settings->add(new admin_setting_heading('search_solr_fileindexing',
                    new lang_string('fileindexsettings', 'search_solr'), ''));
            $settings->add(new admin_setting_configcheckbox('search_solr/fileindexing',
                    new lang_string('fileindexing', 'search_solr'),
                    new lang_string('fileindexing_help', 'search_solr'), 1));
            $settings->add(new admin_setting_configtext('search_solr/maxindexfilekb',
                    new lang_string('maxindexfilekb', 'search_solr'),
                    new lang_string('maxindexfilekb_help', 'search_solr'), '2097152', PARAM_INT));

            // Alternate connection.
            $settings->add(new admin_setting_heading('search_solr_alternatesettings',
                    new lang_string('searchalternatesettings', 'admin'),
                    new lang_string('searchalternatesettings_desc', 'admin')));
            $settings->add(new admin_setting_configtext('search_solr/alternateserver_hostname',
                    new lang_string('solrserverhostname', 'search_solr'),
                    new lang_string('solrserverhostname_desc', 'search_solr'), '127.0.0.1', PARAM_HOST));
            $settings->add(new admin_setting_configtext('search_solr/alternateindexname',
                    new lang_string('solrindexname', 'search_solr'), '', '', PARAM_ALPHANUMEXT));
            $settings->add(new admin_setting_configcheckbox('search_solr/alternatesecure',
                    new lang_string('solrsecuremode', 'search_solr'), '', 0, 1, 0));

            $secure = get_config('search_solr', 'alternatesecure');
            $defaultport = !empty($secure) ? 8443 : 8983;
            $settings->add(new admin_setting_configtext('search_solr/alternateserver_port',
                    new lang_string('solrhttpconnectionport', 'search_solr'), '', $defaultport, PARAM_INT));
            $settings->add(new admin_setting_configtext('search_solr/alternateserver_username',
                    new lang_string('solrauthuser', 'search_solr'), '', '', PARAM_RAW));
            $settings->add(new admin_setting_configpasswordunmask('search_solr/alternateserver_password',
                    new lang_string('solrauthpassword', 'search_solr'), '', ''));
            $settings->add(new admin_setting_configtext('search_solr/alternatessl_cert',
                    new lang_string('solrsslcert', 'search_solr'),
                    new lang_string('solrsslcert_desc', 'search_solr'), '', PARAM_RAW));
            $settings->add(new admin_setting_configtext('search_solr/alternatessl_key',
                    new lang_string('solrsslkey', 'search_solr'),
                    new lang_string('solrsslkey_desc', 'search_solr'), '', PARAM_RAW));
            $settings->add(new admin_setting_configpasswordunmask('search_solr/alternatessl_keypassword',
                    new lang_string('solrsslkeypassword', 'search_solr'),
                    new lang_string('solrsslkeypassword_desc', 'search_solr'), ''));
            $settings->add(new admin_setting_configtext('search_solr/alternatessl_cainfo',
                    new lang_string('solrsslcainfo', 'search_solr'),
                    new lang_string('solrsslcainfo_desc', 'search_solr'), '', PARAM_RAW));
            $settings->add(new admin_setting_configtext('search_solr/alternatessl_capath',
                    new lang_string('solrsslcapath', 'search_solr'),
                    new lang_string('solrsslcapath_desc', 'search_solr'), '', PARAM_RAW));
        }
    }
}
