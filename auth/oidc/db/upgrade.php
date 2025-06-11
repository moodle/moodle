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
 * Plugin upgrade script.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * Update plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_oidc_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014111703) {
        // Lengthen field.
        $table = new xmldb_table('auth_oidc_token');
        $field = new xmldb_field('scope', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'username');
        $dbman->change_field_type($table, $field);

        upgrade_plugin_savepoint(true, 2014111703, 'auth', 'oidc');
    }

    if ($oldversion < 2015012702) {
        $table = new xmldb_table('auth_oidc_state');
        $field = new xmldb_field('additionaldata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timecreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2015012702, 'auth', 'oidc');
    }

    if ($oldversion < 2015012703) {
        $table = new xmldb_table('auth_oidc_token');
        $field = new xmldb_field('oidcusername', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'username');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2015012703, 'auth', 'oidc');
    }

    if ($oldversion < 2015012704) {
        // Update OIDC users.
        $sql = 'SELECT u.id as userid,
                       u.username as username,
                       tok.id as tokenid,
                       tok.oidcuniqid as oidcuniqid,
                       tok.idtoken as idtoken,
                       tok.oidcusername as oidcusername
                  FROM {auth_oidc_token} tok
                  JOIN {user} u ON u.username = tok.username
                 WHERE u.auth = ? AND deleted = ?';
        $params = ['oidc', 0];
        $userstoupdate = $DB->get_recordset_sql($sql, $params);
        foreach ($userstoupdate as $user) {
            if (empty($user->idtoken)) {
                continue;
            }

            try {
                // Decode idtoken and determine oidc username.
                $idtoken = \auth_oidc\jwt::instance_from_encoded($user->idtoken);
                $oidcusername = $idtoken->claim('upn');
                if (empty($oidcusername)) {
                    $oidcusername = $idtoken->claim('sub');
                }

                // Populate token oidcusername.
                if (empty($user->oidcusername)) {
                    $updatedtoken = new stdClass;
                    $updatedtoken->id = $user->tokenid;
                    $updatedtoken->oidcusername = $oidcusername;
                    $DB->update_record('auth_oidc_token', $updatedtoken);
                }

                // Update user username (if applicable), so user can use rocreds loginflow.
                if ($user->username == strtolower($user->oidcuniqid)) {
                    // Old username, update to upn/sub.
                    if ($oidcusername != $user->username) {
                        // Update username.
                        $updateduser = new stdClass;
                        $updateduser->id = $user->userid;
                        $updateduser->username = $oidcusername;
                        $DB->update_record('user', $updateduser);

                        $updatedtoken = new stdClass;
                        $updatedtoken->id = $user->tokenid;
                        $updatedtoken->username = $oidcusername;
                        $DB->update_record('auth_oidc_token', $updatedtoken);
                    }
                }
            } catch (moodle_exception $e) {
                continue;
            }
        }
        upgrade_plugin_savepoint(true, 2015012704, 'auth', 'oidc');
    }

    if ($oldversion < 2015012707) {
        if (!$dbman->table_exists('auth_oidc_prevlogin')) {
            $dbman->install_one_table_from_xmldb_file(__DIR__.'/install.xml', 'auth_oidc_prevlogin');
        }
        upgrade_plugin_savepoint(true, 2015012707, 'auth', 'oidc');
    }

    if ($oldversion < 2015012710) {
        // Lengthen field.
        $table = new xmldb_table('auth_oidc_token');
        $field = new xmldb_field('scope', XMLDB_TYPE_TEXT, null, null, null, null, null, 'oidcusername');
        $dbman->change_field_type($table, $field);
        upgrade_plugin_savepoint(true, 2015012710, 'auth', 'oidc');
    }

    if ($oldversion < 2015111904.01) {
        // Ensure the username field in auth_oidc_token is lowercase.
        $authtokensrs = $DB->get_recordset('auth_oidc_token');
        foreach ($authtokensrs as $authtokenrec) {
            $newusername = trim(\core_text::strtolower($authtokenrec->username));
            if ($newusername !== $authtokenrec->username) {
                $updatedrec = new stdClass;
                $updatedrec->id = $authtokenrec->id;
                $updatedrec->username = $newusername;
                $DB->update_record('auth_oidc_token', $updatedrec);
            }
        }
        upgrade_plugin_savepoint(true, 2015111904.01, 'auth', 'oidc');
    }

    if ($oldversion < 2015111905.01) {
        // Update old endpoints.
        $config = get_config('auth_oidc');
        if ($config->authendpoint === 'https://login.windows.net/common/oauth2/authorize') {
            add_to_config_log('authendpoint', $config->authendpoint, 'https://login.microsoftonline.com/common/oauth2/authorize',
                'auth_oidc');
            set_config('authendpoint', 'https://login.microsoftonline.com/common/oauth2/authorize', 'auth_oidc');
        }

        if ($config->tokenendpoint === 'https://login.windows.net/common/oauth2/token') {
            add_to_config_log('tokenendpoint', $config->tokenendpoint, 'https://login.microsoftonline.com/common/oauth2/token',
                'auth_oidc');
            set_config('tokenendpoint', 'https://login.microsoftonline.com/common/oauth2/token', 'auth_oidc');
        }

        upgrade_plugin_savepoint(true, 2015111905.01, 'auth', 'oidc');
    }

    if ($oldversion < 2018051700.01) {
        $table = new xmldb_table('auth_oidc_token');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'username');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $sql = 'SELECT tok.id, tok.username, u.username, u.id as userid
                      FROM {auth_oidc_token} tok
                      JOIN {user} u ON u.username = tok.username';
            $records = $DB->get_recordset_sql($sql);
            foreach ($records as $record) {
                $newrec = new stdClass;
                $newrec->id = $record->id;
                $newrec->userid = $record->userid;
                $DB->update_record('auth_oidc_token', $newrec);
            }
        }
        upgrade_plugin_savepoint(true, 2018051700.01, 'auth', 'oidc');
    }

    if ($oldversion < 2020020301) {
        $oldgraphtokens = $DB->get_records('auth_oidc_token', ['resource' => 'https://graph.windows.net']);
        foreach ($oldgraphtokens as $graphtoken) {
            $graphtoken->resource = 'https://graph.microsoft.com';
            $DB->update_record('auth_oidc_token', $graphtoken);
        }

        $oidcresource = get_config('auth_oidc', 'oidcresource');
        if ($oidcresource !== false && strpos($oidcresource, 'windows') !== false) {
            $existingoidcresource = get_config('auth_oidc', 'oidcresource');
            if ($existingoidcresource != 'https://graph.windows.net') {
                add_to_config_log('oidcresource', $existingoidcresource, 'https://graph.microsoft.com', 'auth_oidc');
            }
            set_config('oidcresource', 'https://graph.microsoft.com', 'auth_oidc');
        }

        upgrade_plugin_savepoint(true, 2020020301, 'auth', 'oidc');
    }

    if ($oldversion < 2020071503) {
        $localo365singlesignoffsetting = get_config('local_o365', 'single_sign_off');
        if ($localo365singlesignoffsetting !== false) {
            $existingsignlesignoffsetting = get_config('auth_oidc', 'single_sign_off');
            if ($existingsignlesignoffsetting !== true) {
                add_to_config_log('single_sign_off', $existingsignlesignoffsetting, true, 'auth_oidc');
            }
            set_config('single_sign_off', true, 'auth_oidc');
            unset_config('single_sign_off', 'local_o365');
        }

        upgrade_plugin_savepoint(true, 2020071503, 'auth', 'oidc');
    }

    if ($oldversion < 2020110901) {
        if ($dbman->field_exists('auth_oidc_token', 'resource')) {
            // Rename field resource on table auth_oidc_token to tokenresource.
            $table = new xmldb_table('auth_oidc_token');

            $field = new xmldb_field('resource', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, null, 'scope');

            // Launch rename field resource.
            $dbman->rename_field($table, $field, 'tokenresource');
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2020110901, 'auth', 'oidc');
    }

    if ($oldversion < 2020110903) {
        // Part 1: add index to auth_oidc_token table.
        $table = new xmldb_table('auth_oidc_token');

        // Define index userid (not unique) to be added to auth_oidc_token.
        $useridindex = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table, $useridindex)) {
            $dbman->add_index($table, $useridindex);
        }

        // Define index username (not unique) to be added to auth_oidc_token.
        $usernameindex = new xmldb_index('username', XMLDB_INDEX_NOTUNIQUE, ['username']);

        // Conditionally launch add index username.
        if (!$dbman->index_exists($table, $usernameindex)) {
            $dbman->add_index($table, $usernameindex);
        }

        // Part 2: update Authorization and token end point URL.
        $entratenant = get_config('local_o365', 'aadtenant');

        if ($entratenant) {
            $authorizationendpoint = get_config('auth_oidc', 'authendpoint');
            if ($authorizationendpoint == 'https://login.microsoftonline.com/common/oauth2/authorize') {
                $authorizationendpoint = str_replace('common', $entratenant, $authorizationendpoint);
                $existingauthorizationendpoint = get_config('auth_oidc', 'authendpoint');
                if ($existingauthorizationendpoint != $authorizationendpoint) {
                    add_to_config_log('authendpoint', $existingauthorizationendpoint, $authorizationendpoint, 'auth_oidc');
                }
                set_config('authendpoint', $authorizationendpoint, 'auth_oidc');
            }

            $tokenendpoint = get_config('auth_oidc', 'tokenendpoint');
            if ($tokenendpoint == 'https://login.microsoftonline.com/common/oauth2/token') {
                $tokenendpoint = str_replace('common', $entratenant, $tokenendpoint);
                $existingtokenendpoint = get_config('auth_oidc', 'tokenendpoint');
                if ($existingtokenendpoint != $tokenendpoint) {
                    add_to_config_log('tokenendpoint', $existingtokenendpoint, $tokenendpoint, 'auth_oidc');
                }
                set_config('tokenendpoint', $tokenendpoint, 'auth_oidc');
            }
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2020110903, 'auth', 'oidc');
    }

    if ($oldversion < 2021051701) {
        // Migrate field mapping settings from local_o365.
        $existingfieldmappingsettings = get_config('local_o365', 'fieldmap');
        if ($existingfieldmappingsettings !== false) {
            $userfields = auth_oidc_get_all_user_fields();

            $existingfieldmappingsettings = @unserialize($existingfieldmappingsettings);
            if (is_array($existingfieldmappingsettings)) {
                foreach ($existingfieldmappingsettings as $existingfieldmappingsetting) {
                    $fieldmap = explode('/', $existingfieldmappingsetting);

                    if (count($fieldmap) !== 3) {
                        // Invalid settings, ignore.
                        continue;
                    }

                    [$remotefield, $localfield, $behaviour] = $fieldmap;

                    if ($remotefield == 'facsimileTelephoneNumber') {
                        $remotefield = 'faxNumber';
                    }

                    $existingmapsetting = get_config('auth_oidc', 'field_map_' . $localfield);
                    if ($existingmapsetting !== $remotefield) {
                        add_to_config_log('field_map_' . $localfield, $existingmapsetting, $remotefield, 'auth_oidc');
                    }
                    set_config('field_map_' . $localfield, $remotefield, 'auth_oidc');

                    $existinglocksetting = get_config('auth_oidc', 'field_lock_' . $localfield);
                    if ($existinglocksetting !== 'unlocked') {
                        add_to_config_log('field_lock_' . $localfield, $existinglocksetting, 'unlocked', 'auth_oidc');
                    }
                    set_config('field_lock_' . $localfield, 'unlocked', 'auth_oidc');

                    $existingupdatelocalsetting = get_config('auth_oidc', 'field_updatelocal_' . $localfield);
                    if ($existingupdatelocalsetting !== $behaviour) {
                        add_to_config_log('field_updatelocal_' . $localfield, $existingupdatelocalsetting, $behaviour, 'auth_oidc');
                    }
                    set_config('field_updatelocal_' . $localfield, $behaviour, 'auth_oidc');

                    if (($key = array_search($localfield, $userfields)) !== false) {
                        unset($userfields[$key]);
                    }
                }

                foreach ($userfields as $userfield) {
                    $existingmapsetting = get_config('auth_oidc', 'field_map_' . $userfield);
                    if ($existingmapsetting !== '') {
                        add_to_config_log('field_map_' . $userfield, $existingmapsetting, '', 'auth_oidc');
                    }
                    set_config('field_map_' . $userfield, '', 'auth_oidc');

                    $existinglocksetting = get_config('auth_oidc', 'field_lock_' . $userfield);
                    if ($existinglocksetting !== 'unlocked') {
                        add_to_config_log('field_lock_' . $userfield, $existinglocksetting, 'unlocked', 'auth_oidc');
                    }
                    set_config('field_lock_' . $userfield, 'unlocked', 'auth_oidc');

                    $existingupdatelocalsetting = get_config('auth_oidc', 'field_updatelocal_' . $userfield);
                    if ($existingupdatelocalsetting !== 'always') {
                        add_to_config_log('field_updatelocal_' . $userfield, $existingupdatelocalsetting, 'always', 'auth_oidc');
                    }
                    set_config('field_updatelocal_' . $userfield, 'always', 'auth_oidc');
                }
            }

            unset_config('fieldmap', 'local_o365');
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2021051701, 'auth', 'oidc');
    }

    if ($oldversion < 2022041901) {
        // Define field sid to be added to auth_oidc_token.
        $table = new xmldb_table('auth_oidc_token');
        $field = new xmldb_field('sid', XMLDB_TYPE_CHAR, '36', null, null, null, null, 'idtoken');

        // Conditionally launch add field sid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2022041901, 'auth', 'oidc');
    }

    if ($oldversion < 2022041906) {
        // Update idptype config.
        $idptypeconfig = get_config('auth_oidc', 'idptype');
        $authorizationendpoint = get_config('auth_oidc', 'authendpoint');
        if (empty($idptypeconfig)) {
            if (!$authorizationendpoint) {
                $existingidptype = get_config('auth_oidc', 'idptype');
                if ($existingidptype != AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID) {
                    add_to_config_log('idptype', $existingidptype, AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID, 'auth_oidc');
                }
                set_config('idptype', AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID, 'auth_oidc');
            } else {
                $endpointversion = auth_oidc_determine_endpoint_version($authorizationendpoint);
                switch ($endpointversion) {
                    case AUTH_OIDC_MICROSOFT_ENDPOINT_VERSION_1:
                        $existingidptype = get_config('auth_oidc', 'idptype');
                        if ($existinglocksetting != AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID) {
                            add_to_config_log('idptype', $existingidptype, AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID, 'auth_oidc');
                        }
                        set_config('idptype', AUTH_OIDC_IDP_TYPE_MICROSOFT_ENTRA_ID, 'auth_oidc');
                        break;
                    case AUTH_OIDC_MICROSOFT_ENDPOINT_VERSION_2:
                        $existingidptype = get_config('auth_oidc', 'idptype');
                        if ($existinglocksetting != AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM) {
                            add_to_config_log(
                                    'idptype',
                                    $existingidptype,
                                    AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM,
                                    'auth_oidc'
                            );
                        }
                        set_config('idptype', AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM, 'auth_oidc');
                        break;
                    default:
                        $existingidptype = get_config('auth_oidc', 'idptype');
                        if ($existinglocksetting != AUTH_OIDC_IDP_TYPE_OTHER) {
                            add_to_config_log('idptype', $existingidptype, AUTH_OIDC_IDP_TYPE_OTHER, 'auth_oidc');
                        }
                        set_config('idptype', AUTH_OIDC_IDP_TYPE_OTHER, 'auth_oidc');
                }
            }
        }

        // Update client authentication type configuration settings.
        $clientauthmethodconfig = get_config('auth_oidc', 'clientauthmethod');
        if (empty($clientauthmethodconfig)) {
            $clientsecretconfig = get_config('auth_oidc', 'clientsecret');
            $clientcertificateconfig = get_config('auth_oidc', 'clientcert');
            $clientprivatekeyconfig = get_config('auth_oidc', 'clientprivatekey');
            if (empty($clientsecretconfig) && !empty($clientcertificateconfig) && !empty($clientprivatekeyconfig)) {
                $existingclientauthmethod = get_config('auth_oidc', 'clientauthmethod');
                if ($existingclientauthmethod != AUTH_OIDC_AUTH_METHOD_CERTIFICATE) {
                    add_to_config_log('clientauthmethod', $existingclientauthmethod, AUTH_OIDC_AUTH_METHOD_CERTIFICATE,
                        'auth_oidc');
                }
                set_config('clientauthmethod', AUTH_OIDC_AUTH_METHOD_CERTIFICATE, 'auth_oidc');
            } else {
                $existingclientauthmethod = get_config('auth_oidc', 'clientauthmethod');
                if ($existingclientauthmethod != AUTH_OIDC_AUTH_METHOD_SECRET) {
                    add_to_config_log('clientauthmethod', $existingclientauthmethod, AUTH_OIDC_AUTH_METHOD_SECRET, 'auth_oidc');
                }
                set_config('clientauthmethod', AUTH_OIDC_AUTH_METHOD_SECRET, 'auth_oidc');
            }
        }

        // Update tenantnameorguid config.
        $tenantnameorguidconfig = get_config('auth_oidc', 'tenantnameorguid');
        if (empty($tenantnameorguidconfig)) {
            $entratenant = get_config('local_o365', 'aadtenant');
            if ($entratenant) {
                $existingtenantnameorguid = get_config('auth_oidc', 'tenantnameorguid');
                if ($existingtenantnameorguid != $entratenant) {
                    add_to_config_log('tenantnameorguid', $existingtenantnameorguid, $entratenant, 'auth_oidc');
                }
                set_config('tenantnameorguid', $entratenant, 'auth_oidc');
            }
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2022041906, 'auth', 'oidc');
    }

    if ($oldversion < 2022112801) {
        // Update tenantnameorguid config.
        unset_config('auth_oidc', 'tenantnameorguid');

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2022112801, 'auth', 'oidc');
    }

    if ($oldversion < 2023100902) {
        // Set initial value for "clientcertsource" config.
        if (empty(get_config('auth_oidc', 'clientcertsource'))) {
            $existingclientcertsource = get_config('auth_oidc', 'clientcertsource');
            if ($existingclientcertsource != AUTH_OIDC_AUTH_CERT_SOURCE_TEXT) {
                add_to_config_log('clientcertsource', $existingclientcertsource, AUTH_OIDC_AUTH_CERT_SOURCE_TEXT, 'auth_oidc');
            }
            set_config('clientcertsource', AUTH_OIDC_AUTH_CERT_SOURCE_TEXT, 'auth_oidc');
        }

        upgrade_plugin_savepoint(true, 2023100902, 'auth', 'oidc');
    }

    if ($oldversion < 2024042201) {
        // Set default values for new settings "bindingusernameclaim" and "customclaimname".
        if (!get_config('auth_oidc', 'bindingusernameclaim')) {
            set_config('bindingusernameclaim', 'auto', 'auth_oidc');
        }

        if (!get_config('auth_oidc', 'customclaimname')) {
            set_config('customclaimname', '', 'auth_oidc');
        }

        // Define field useridentifier to be added to auth_oidc_token.
        $table = new xmldb_table('auth_oidc_token');
        $field = new xmldb_field('useridentifier', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'oidcusername');

        // Conditionally launch add field useridentifier.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Save current value of oidcusername to useridentifier.
            $sql = 'UPDATE {auth_oidc_token} SET useridentifier = oidcusername';
            $DB->execute($sql);
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2024042201, 'auth', 'oidc');
    }

    if ($oldversion < 2024100701) {
        // Set the default value for the bindingusernameclaim setting.
        $bindingusernameclaimconfig = get_config('auth_oidc', 'bindingusernameclaim');
        if (empty($bindingusernameclaimconfig)) {
            set_config('bindingusernameclaim', 'auto', 'auth_oidc');
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2024100701, 'auth', 'oidc');
    }

    if ($oldversion < 2024100702) {
        // Define table auth_oidc_sid to be created.
        $table = new xmldb_table('auth_oidc_sid');

        // Adding fields to table auth_oidc_sid.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sid', XMLDB_TYPE_CHAR, '36', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table auth_oidc_sid.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for auth_oidc_sid.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Migrate existing sid values from auth_oidc_tokens to auth_oidc_sid.
        if ($dbman->field_exists('auth_oidc_token', 'sid')) {
            $sql = "INSERT INTO {auth_oidc_sid} (userid, sid, timecreated)
                    SELECT userid, sid, ? AS timecreated
                    FROM {auth_oidc_token}
                    WHERE sid IS NOT NULL AND sid != ''";
            $DB->execute($sql, [time()]);
        }

        // Define field sid to be dropped from auth_oidc_token.
        $table = new xmldb_table('auth_oidc_token');
        $field = new xmldb_field('sid');

        // Conditionally launch drop field sid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Oidc savepoint reached.
        upgrade_plugin_savepoint(true, 2024100702, 'auth', 'oidc');
    }

    return true;
}
