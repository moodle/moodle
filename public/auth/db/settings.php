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
 * Admin settings and defaults.
 *
 * @package auth_db
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // We use a couple of custom admin settings since we need to massage the data before it is inserted into the DB.
    require_once($CFG->dirroot.'/auth/db/classes/admin_setting_special_auth_configtext.php');

    // Needed for constants.
    require_once($CFG->libdir.'/authlib.php');

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_db/pluginname', '', new lang_string('auth_dbdescription', 'auth_db')));

    // Host.
    $settings->add(new admin_setting_configtext('auth_db/host', get_string('auth_dbhost_key', 'auth_db'),
            get_string('auth_dbhost', 'auth_db') . ' ' .get_string('auth_multiplehosts', 'auth'),
            '127.0.0.1', PARAM_RAW));

    // Type.
    $dboptions = array();
    $dbtypes = array("access", "ado_access", "ado", "ado_mssql", "borland_ibase", "csv", "db2",
        "fbsql", "firebird", "ibase", "informix72", "informix", "mssql", "mssql_n", "mssqlnative",
        "mysql", "mysqli", "mysqlt", "oci805", "oci8", "oci8po", "odbc", "odbc_mssql", "odbc_oracle",
        "oracle", "pdo", "postgres64", "postgres7", "postgres", "proxy", "sqlanywhere", "sybase", "vfp");
    foreach ($dbtypes as $dbtype) {
        $dboptions[$dbtype] = $dbtype;
    }

    $settings->add(new admin_setting_configselect('auth_db/type',
        new lang_string('auth_dbtype_key', 'auth_db'),
        new lang_string('auth_dbtype', 'auth_db'), 'mysqli', $dboptions));

    // Sybase quotes.
    $yesno = array(
        new lang_string('no'),
        new lang_string('yes'),
    );

    $settings->add(new admin_setting_configselect('auth_db/sybasequoting',
        new lang_string('auth_dbsybasequoting', 'auth_db'), new lang_string('auth_dbsybasequotinghelp', 'auth_db'), 0, $yesno));

    // DB Name.
    $settings->add(new admin_setting_configtext('auth_db/name', get_string('auth_dbname_key', 'auth_db'),
            get_string('auth_dbname', 'auth_db'), '', PARAM_RAW_TRIMMED));

    // DB Username.
    $settings->add(new admin_setting_configtext('auth_db/user', get_string('auth_dbuser_key', 'auth_db'),
            get_string('auth_dbuser', 'auth_db'), '', PARAM_RAW_TRIMMED));

    // Password.
    $settings->add(new admin_setting_configpasswordunmask('auth_db/pass', get_string('auth_dbpass_key', 'auth_db'),
            get_string('auth_dbpass', 'auth_db'), ''));

    // DB Table.
    $settings->add(new admin_setting_configtext('auth_db/table', get_string('auth_dbtable_key', 'auth_db'),
            get_string('auth_dbtable', 'auth_db'), '', PARAM_RAW_TRIMMED));

    // DB User field.
    $settings->add(new admin_setting_configtext('auth_db/fielduser', get_string('auth_dbfielduser_key', 'auth_db'),
            get_string('auth_dbfielduser', 'auth_db'), '', PARAM_RAW_TRIMMED));

    // DB User password.
    $settings->add(new admin_setting_configtext('auth_db/fieldpass', get_string('auth_dbfieldpass_key', 'auth_db'),
            get_string('auth_dbfieldpass', 'auth_db'), '', PARAM_RAW_TRIMMED));


    // DB Password Type.
    $passtype = array();
    $passtype["plaintext"]   = get_string("plaintext", "auth");
    $passtype["md5"]         = get_string("md5", "auth");
    $passtype["sha1"]        = get_string("sha1", "auth");
    $passtype["saltedcrypt"] = get_string("auth_dbsaltedcrypt", "auth_db");
    $passtype["internal"]    = get_string("internal", "auth");

    $settings->add(new admin_setting_configselect('auth_db/passtype',
        new lang_string('auth_dbpasstype_key', 'auth_db'), new lang_string('auth_dbpasstype', 'auth_db'), 'plaintext', $passtype));

    // Encoding.
    $settings->add(new admin_setting_configtext('auth_db/extencoding', get_string('auth_dbextencoding', 'auth_db'),
            get_string('auth_dbextencodinghelp', 'auth_db'), 'utf-8', PARAM_RAW_TRIMMED));

    // DB SQL SETUP.
    $settings->add(new admin_setting_configtext('auth_db/setupsql', get_string('auth_dbsetupsql', 'auth_db'),
            get_string('auth_dbsetupsqlhelp', 'auth_db'), '', PARAM_RAW_TRIMMED));

    // Debug ADOOB.
    $settings->add(new admin_setting_configselect('auth_db/debugauthdb',
        new lang_string('auth_dbdebugauthdb', 'auth_db'), new lang_string('auth_dbdebugauthdbhelp', 'auth_db'), 0, $yesno));

    // Password change URL.
    $settings->add(new auth_db_admin_setting_special_auth_configtext('auth_db/changepasswordurl',
            get_string('auth_dbchangepasswordurl_key', 'auth_db'),
            get_string('changepasswordhelp', 'auth'), '', PARAM_URL));

    // Label and Sync Options.
    $settings->add(new admin_setting_heading('auth_db/usersync', new lang_string('auth_sync_script', 'auth'), ''));

    // Sync Options.
    $deleteopt = array();
    $deleteopt[AUTH_REMOVEUSER_KEEP] = get_string('auth_remove_keep', 'auth');
    $deleteopt[AUTH_REMOVEUSER_SUSPEND] = get_string('auth_remove_suspend', 'auth');
    $deleteopt[AUTH_REMOVEUSER_FULLDELETE] = get_string('auth_remove_delete', 'auth');

    $settings->add(new admin_setting_configselect('auth_db/removeuser',
        new lang_string('auth_remove_user_key', 'auth'),
        new lang_string('auth_remove_user', 'auth'), AUTH_REMOVEUSER_KEEP, $deleteopt));

    // Update users.
    $settings->add(new admin_setting_configselect('auth_db/updateusers',
        new lang_string('auth_dbupdateusers', 'auth_db'),
        new lang_string('auth_dbupdateusers_description', 'auth_db'), 0, $yesno));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('db');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            get_string('auth_dbextrafields', 'auth_db'),
            true, true, $authplugin->get_custom_user_profile_fields());

}
