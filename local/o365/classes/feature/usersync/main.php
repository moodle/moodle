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
 * User sync feature.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\usersync;

use context_user;
use core_text;
use core_user;
use local_o365\oauth2\clientdata;
use local_o365\httpclient;
use local_o365\oauth2\token;
use local_o365\obj\o365user;
use local_o365\rest\unified;
use local_o365\utils;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/local/o365/lib.php');
require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * User sync feature.
 */
class main {
    /**
     * @var clientdata|null
     */
    protected $clientdata = null;
    /**
     * @var httpclient|null
     */
    protected $httpclient = null;

    /**
     * Constructor
     *
     * @param clientdata|null $clientdata $clientdata The client data to use for API construction.
     * @param httpclient|null $httpclient $httpclient The HTTP client to use for API construction.
     *
     * @throws moodle_exception
     */
    public function __construct(?clientdata $clientdata = null, ?httpclient $httpclient = null) {
        if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
            $this->clientdata = (!empty($clientdata))
                ? $clientdata
                : clientdata::instance_from_oidc();

            $this->httpclient = (!empty($httpclient))
                ? $httpclient
                : new httpclient();
        }
    }

    /**
     * Determine whether any sync-related options are enabled.
     *
     * @return bool Enabled/disabled.
     */
    public static function is_enabled() {
        $usersyncsettings = get_config('local_o365', 'usersync');
        if (empty($usersyncsettings) || $usersyncsettings === 'photosynconlogin' || $usersyncsettings === 'tzsynconlogin') {
            return false;
        }
        return true;
    }

    /**
     * Construct a user API client, accounting for Microsoft Graph API presence, and fall back to system api user if desired.
     *
     * @return unified A constructed unified API client, or false if error.
     * @throws moodle_exception
     */
    public function construct_user_api() {
        $tokenresource = unified::get_tokenresource();
        $token = utils::get_application_token($tokenresource, $this->clientdata, $this->httpclient);
        if (empty($token)) {
            throw new moodle_exception('errornotokenforusersync', 'local_o365');
        }
        return new unified($token, $this->httpclient);
    }

    /**
     * Get information on the app.
     *
     * @return array|null Array of app service information, or null if failure.
     */
    public function get_application_serviceprincipal_info() {
        $apiclient = $this->construct_user_api();
        return $apiclient->get_application_serviceprincipal_info();
    }

    /**
     * Assign user to application.
     *
     * @param int $muserid The Moodle user ID.
     * @param string $userobjectid Object ID of user.
     * @return array|null Array of user information, or null if failure.
     */
    public function assign_user($muserid, $userobjectid) {
        // Not supported in unit tests at the moment.
        if (PHPUNIT_TEST || defined('BEHAT_SITE_RUNNING')) {
            return null;
        }
        $this->mtrace('Assigning Moodle user '.$muserid.' (objectid '.$userobjectid.') to application');

        // Get object ID on first call.
        static $appobjectid = null;
        if (empty($objectid)) {
            $appinfo = $this->get_application_serviceprincipal_info();
            if (empty($appinfo)) {
                return null;
            }
            $appobjectid = (unified::is_configured())
                ? $appinfo['value'][0]['id']
                : $appinfo['value'][0]['objectId'];
        }

        $apiclient = $this->construct_user_api();
        $result = $apiclient->assign_user($muserid, $userobjectid, $appobjectid);
        if (!empty($result['odata.error'])) {
            $error = '';
            $code = '';
            if (!empty($result['odata.error']['code'])) {
                $code = $result['odata.error']['code'];
            }
            if (!empty($result['odata.error']['message']['value'])) {
                $error = $result['odata.error']['message']['value'];
            }
            $user = core_user::get_user($muserid);
            $this->mtrace('Error assigning users "'.$user->username.'" Reason: '.$code.' '.$error);
        } else {
            $this->mtrace('User assigned to application.');
        }
        return $result;
    }

    /**
     * Assign photo to Moodle user account.
     *
     * @param int $muserid
     * @return boolean True on photo updated.
     */
    public function assign_photo(int $muserid) {
        global $DB, $CFG;

        $result = false;
        $apiclient = $this->construct_user_api();
        $oidcusername = $DB->get_field('local_o365_objects', 'o365name', ['moodleid' => $muserid, 'type' => 'user']);
        if (!$oidcusername) {
            return false;
        }

        $muser = core_user::get_user($muserid, 'id, picture', MUST_EXIST);
        $context = context_user::instance($muserid);

        try {
            $image = $apiclient->get_photo($oidcusername);
            if (!$image) {
                // Profile photo has been deleted.
                if (!empty($muser->picture)) {
                    // User has no photo. Deleting previous profile photo.
                    $fs = \get_file_storage();
                    $fs->delete_area_files($context->id, 'user', 'icon');
                    $DB->set_field('user', 'picture', 0, ['id' => $muser->id]);
                }
                $result = false;
            } else {
                // Check if json error message was returned.
                if (!preg_match('/^{/', $image)) {
                    // Update profile picture.
                    $tempfile = tempnam($CFG->tempdir . '/', 'profileimage') . '.jpg';
                    if (!$fp = fopen($tempfile, 'w+b')) {
                        @unlink($tempfile);
                        return false;
                    }
                    fwrite($fp, $image);
                    fclose($fp);
                    require_once("$CFG->libdir/gdlib.php");
                    $newpicture = process_new_icon($context, 'user', 'icon', 0, $tempfile);
                    if ($newpicture != $muser->picture) {
                        $DB->set_field('user', 'picture', $newpicture, ['id' => $muser->id]);
                        $result = true;
                    }
                    @unlink($tempfile);
                }

                // Update appassign record.
                $record = $DB->get_record('local_o365_appassign', ['muserid' => $muserid]);
                if (empty($record)) {
                    $record = new stdClass();
                    $record->muserid = $muserid;
                    $record->assigned = 0;
                }
                $record->photoupdated = time();
                if (empty($record->id)) {
                    $DB->insert_record('local_o365_appassign', $record);
                } else {
                    $DB->update_record('local_o365_appassign', $record);
                }
            }
        } catch (moodle_exception $e) {
            if ($e->getMessage() === get_string('erroro365nophoto', 'local_o365')) {
                // User has no photo - if the user has an existing photo in Moodle profile, delete it.
                if (!empty($muser->picture)) {
                    // User has no photo. Deleting previous profile photo.
                    $fs = \get_file_storage();
                    $fs->delete_area_files($context->id, 'user', 'icon');
                    $DB->set_field('user', 'picture', 0, ['id' => $muser->id]);
                }
            }
        }

        return $result;
    }

    /**
     * Sync timezone of user from Outlook to Moodle.
     *
     * @param int $muserid
     */
    public function sync_timezone(int $muserid) {
        global $DB;

        $tokenresource = unified::get_tokenresource();
        $token = utils::get_application_token($tokenresource, $this->clientdata, $this->httpclient);
        if (empty($token)) {
            throw new moodle_exception('errornotokenforusersync', 'local_o365');
        }
        $apiclient = new unified($token, $this->httpclient);
        $oidcusername = $DB->get_field('local_o365_objects', 'o365name', ['moodleid' => $muserid, 'type' => 'user']);
        if (!$oidcusername) {
            return false;
        }
        $remotetimezone = $apiclient->get_timezone($oidcusername);
        if (is_array($remotetimezone) && !empty($remotetimezone['value'])) {
            $remotetimezonesetting = $remotetimezone['value'];
            $moodletimezone = \core_date::normalise_timezone($remotetimezonesetting);

            $etcgmttimezonemappings = [
                'Etc/GMT+12' => 'Pacific/Fiji',
                'Etc/GMT+11' => 'Pacific/Pago_Pago',
                'Etc/GMT+10' => 'Pacific/Honolulu',
                'Etc/GMT+9' => 'America/Anchorage',
                'Etc/GMT+8' => 'America/Los_Angeles',
                'Etc/GMT+7' => 'America/Denver',
                'Etc/GMT+6' => 'America/Chicago',
                'Etc/GMT+5' => 'America/New_York',
                'Etc/GMT+4' => 'America/Antigua',
                'Etc/GMT+3' => 'America/Buenos_Aires',
                'Etc/GMT+2' => 'Atlantic/Noronha',
                'Etc/GMT+1' => 'Atlantic/Cape_Verde',
                'Etc/GMT' => 'Europe/London',
                'Etc/GMT-1' => 'Europe/Paris',
                'Etc/GMT-2' => 'Europe/Helsinki',
                'Etc/GMT-3' => 'Asia/Qatar',
                'Etc/GMT-4' => 'Asia/Baku',
                'Etc/GMT-5' => 'Asia/Karachi',
                'Etc/GMT-6' => 'Asia/Dhaka',
                'Etc/GMT-7' => 'Asia/Bangkok',
                'Etc/GMT-8' => 'Asia/Hong_Kong',
                'Etc/GMT-9' => 'Asia/Tokyo',
                'Etc/GMT-10' => 'Pacific/Guam',
                'Etc/GMT-11' => 'Asia/Sakhalin',
                'Etc/GMT-12' => 'Pacific/Auckland',
                'Etc/GMT-13' => 'Pacific/Tongatapu',
                'Etc/GMT-14' => 'Pacific/Kiritimati',
            ];
            if (isset($etcgmttimezonemappings[$moodletimezone])) {
                $moodletimezone = $etcgmttimezonemappings[$moodletimezone];
            }

            $moodletimezone = clean_param($moodletimezone, PARAM_TIMEZONE);

            if ($moodletimezone) {
                $existinguser = core_user::get_user($muserid);
                $existinguser->timezone = $moodletimezone;
                user_update_user($existinguser, false, true);
            }
        }
    }

    /**
     * Extract a parameter value from a URL.
     *
     * @param string $link A URL.
     * @param string $param Parameter name.
     * @return string|null The extracted deltalink value, or null if none found.
     */
    protected function extract_param_from_link($link, $param) {
        $link = parse_url($link);
        if (isset($link['query'])) {
            $output = [];
            parse_str($link['query'], $output);
            if (isset($output[$param])) {
                return $output[$param];
            }
        }
        return null;
    }

    /**
     * Get Entra ID data for a single user.
     *
     * @param string $objectid
     * @param bool $guestuser if the user is a guest user in Microsoft tenant
     *
     * @return array|null Array of user information, or null if failure.
     */
    public function get_user(string $objectid, bool $guestuser = false) {
        $apiclient = $this->construct_user_api();
        $result = $apiclient->get_user($objectid, $guestuser);
        if (!empty($result) && is_array($result)) {
            return $result;
        }
        return [];
    }

    /**
     * Get all users in the configured directory.
     *
     * @param string|array $params Requested user parameters.
     * @return array Array of user information.
     */
    public function get_users($params = 'default') {
        $apiclient = $this->construct_user_api();
        $users = $apiclient->get_users($params);

        return $users;
    }

    /**
     * Return the users search delta, along with skip token and delta tokens.
     *
     * @param string $params
     * @param null $deltatoken
     * @return array
     * @throws moodle_exception
     */
    public function get_users_delta($params = 'default', $deltatoken = null) {
        $tokenresource = unified::get_tokenresource();
        $token = utils::get_application_token($tokenresource, $this->clientdata, $this->httpclient);
        if (empty($token)) {
            throw new moodle_exception('errornotokenforusersync', 'local_o365');
        }
        $apiclient = new unified($token, $this->httpclient);
        return $apiclient->get_users_delta($params, $deltatoken);
    }

    /**
     * Return the $field of the manager of the Microsoft 365 user with the given oid.
     *
     * @param string $userobjectid
     * @param string $field
     *
     * @return mixed|string
     */
    public function get_user_manager(string $userobjectid, string $field = 'displayName') {
        $apiclient = $this->construct_user_api();
        $result = $apiclient->get_user_manager($userobjectid);
        if ($result && isset($result[$field])) {
            return $result[$field];
        } else {
            return '';
        }
    }

    /**
     * Return the names of groups that the Microsoft 365 user with the given oid are in, joined by comma.
     *
     * @param string $userobjectid
     *
     * @return string
     */
    public function get_user_group_names($userobjectid) {
        $apiclient = $this->construct_user_api();
        $usergroups = $apiclient->get_user_groups($userobjectid);
        $groupnames = [];
        foreach ($usergroups as $usergroup) {
            $groupnames[] = $usergroup['displayName'];
        }

        return join(',', $groupnames);
    }

    /**
     * Return the names of teams that the Microsoft 365 user with the given oid are in, joined by comma.
     *
     * @param string $userobjectid
     *
     * @return string
     */
    public function get_user_teams($userobjectid) {
        $apiclient = $this->construct_user_api();

        $userteams = $apiclient->get_user_teams($userobjectid);
        $teamnames = [];
        foreach ($userteams as $userteam) {
            $teamnames[] = $userteam['displayName'];
        }
        return join(',', $teamnames);
    }

    /**
     * Return the names of roles that the Microsoft 365 user with the given oid has, joined by comma.
     *
     * @param string $userobjectid
     *
     * @return string
     */
    public function get_user_roles($userobjectid) {
        $apiclient = $this->construct_user_api();
        $objectsids = $apiclient->get_user_objects($userobjectid);
        $roles = [];
        if (!empty($objectsids)) {
            $results = $apiclient->get_directory_objects($objectsids);
            foreach ($results as $result) {
                if (stripos($result['@odata.type'], 'role') !== false) {
                    $roles[] = $result['displayName'];
                }
            }
        }

        return join(',', $roles);
    }

    /**
     * Return the preferred name of the Microsoft 365 user with the given oid.
     *
     * @param string $userobjectid
     *
     * @return mixed
     */
    public function get_preferred_name($userobjectid) {
        $apiclient = $this->construct_user_api();
        $result = $apiclient->get_user($userobjectid);
        if (isset($result['preferredName'])) {
            return $result['preferredName'];
        }
    }

    /**
     * Apply the configured field map.
     *
     * @param array $entraiduserdata User data from Microsoft Entra ID.
     * @param stdClass $user Moodle user data.
     * @param string $eventtype 'login', or 'create'
     *
     * @return stdClass Modified Moodle user data.
     */
    public static function apply_configured_fieldmap(array $entraiduserdata, stdClass $user, $eventtype) {
        global $CFG;

        require_once($CFG->dirroot . '/auth/oidc/lib.php');

        if (PHPUNIT_TEST || defined('BEHAT_SITE_RUNNING')) {
            $fieldmappings = [
                'firstname' => [
                    'field_map' => 'givenName',
                    'field_lock' => 'unlocked',
                    'update_local' => 'always',
                ],
                'lastname' => [
                    'field_map' => 'surname',
                    'field_lock' => 'unlocked',
                    'update_local' => 'always',
                ],
                'email' => [
                    'field_map' => 'mail',
                    'field_lock' => 'unlocked',
                    'update_local' => 'always',
                ],
                'idnumber' => [
                    'field_map' => 'userPrincipalName',
                    'field_lock' => 'unlocked',
                    'update_local' => 'always',
                ],
                'city' => [
                    'field_map' => 'city',
                    'field_lock' => 'unlocked',
                    'update_local' => 'always',
                ],
                'country' => [
                    'field_map' => 'country',
                    'field_lock' => 'unlocked',
                    'update_local' => 'always',
                ],
                'department' => [
                    'field_map' => 'department',
                    'field_lock' => 'unlocked',
                    'update_local' => 'always',
                ],
            ];
        } else {
            $fieldmappings = auth_oidc_get_field_mappings();
        }

        if (isset($user->lang)) {
            $originallangsetting = $user->lang;
        } else {
            $originallangsetting = $CFG->lang;
        }

        if (unified::is_configured() && (array_key_exists('id', $entraiduserdata) && $entraiduserdata['id'])) {
            $objectidfieldname = 'id';
            $userobjectid = $entraiduserdata['id'];
        } else if (array_key_exists('objectId', $entraiduserdata) && $entraiduserdata['objectId']) {
            $objectidfieldname = 'objectId';
            $userobjectid = $entraiduserdata['objectId'];
        } else {
            $objectidfieldname = 'userPrincipalName';
            $userobjectid = $entraiduserdata['userPrincipalName'];
        }

        $usersync = new self();

        $countrymapping = get_string_manager()->get_list_of_countries();
        foreach ($countrymapping as $code => $country) {
            $country = strtolower($country);
            $country = preg_replace('/\s*\([^)]*\)/', '', $country);
            $country = preg_replace('/\s*(the\s+)?republic\s+of/', '', $country);
            $countrymapping[$code] = $country;
        }

        foreach ($fieldmappings as $localfield => $fieldmapping) {
            $remotefield = $fieldmapping['field_map'];
            $behavior = $fieldmapping['update_local'];

            if ($behavior !== 'on' . $eventtype && $behavior !== 'always') {
                // Field mapping doesn't apply to this event type.
                continue;
            }

            if ($remotefield == 'objectId') {
                $remotefield = $objectidfieldname;
            }

            if (isset($entraiduserdata[$remotefield])) {
                switch ($remotefield) {
                    case 'country':
                        // Update country with two-letter country code.
                        $incoming = strtoupper($entraiduserdata[$remotefield]);
                        if (isset($countrymapping[$incoming])) {
                            $countrycode = $incoming;
                        } else {
                            $incoming = strtolower($incoming);
                            foreach ($countrymapping as $code => $country) {
                                if (stripos($country, $incoming) !== false ||
                                    stripos($incoming, $country) !== false) {
                                    $countrycode = $code;
                                    break;
                                }
                            }
                        }
                        $user->$localfield = (!empty($countrycode)) ? $countrycode : '';
                        break;
                    case 'businessPhones':
                        $user->$localfield = implode(', ', $entraiduserdata[$remotefield]);
                        break;
                    default:
                        $user->$localfield = $entraiduserdata[$remotefield];
                }
            }

            if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
                switch ($remotefield) {
                    case 'manager':
                        $user->$localfield = $usersync->get_user_manager($userobjectid, 'displayName');
                        break;
                    case 'manager_email':
                        $user->$localfield = $usersync->get_user_manager($userobjectid, 'mail');
                        break;
                    case 'groups':
                        $user->$localfield = $usersync->get_user_group_names($userobjectid);
                        break;
                    case 'teams':
                        $user->$localfield = $usersync->get_user_teams($userobjectid);
                        break;
                    case 'roles':
                        $user->$localfield = $usersync->get_user_roles($userobjectid);
                        break;
                    case 'preferredName':
                        if (!isset($entraiduserdata[$remotefield])) {
                            if (stripos($entraiduserdata['userPrincipalName'], '_ext_') !== false) {
                                $user->$localfield = $usersync->get_preferred_name($userobjectid);
                            }
                        }
                        break;
                    default:
                        if (substr($remotefield, 0, 18) == 'extensionAttribute') {
                            $extensionattributeid = substr($remotefield, 18);
                            if (ctype_digit($extensionattributeid) && $extensionattributeid >= 1 && $extensionattributeid <= 15) {
                                if (isset($entraiduserdata['onPremisesExtensionAttributes']) &&
                                    isset($entraiduserdata['onPremisesExtensionAttributes'][$remotefield])) {
                                    $user->$localfield = $entraiduserdata['onPremisesExtensionAttributes'][$remotefield];
                                }
                            }
                        }
                }
            }
        }

        // Validate language sync.
        if (array_key_exists('lang', $fieldmappings) && ($behavior === 'on' . $eventtype || $behavior === 'always')) {
            if (!get_string_manager()->translation_exists($originallangsetting, false)) {
                $originallangsetting = $CFG->lang;
            }

            if (!isset($user->lang) || !$user->lang) {
                // If the user's new language setting is empty, use original setting.
                $user->lang = $originallangsetting;
            } else {
                $newlangsetting = strtolower(str_replace('-', '_' , $user->lang));
                $newlangsettingwp = $newlangsetting . '_wp';
                $newlangsettingsimple = substr($newlangsetting, 0, 2);

                $validlangsettings = [];
                if (!get_string_manager()->translation_exists($newlangsettingwp, false)) {
                    $newlangsettingwp = null;
                } else {
                    $validlangsettings[] = 'newlangsettingwp';
                }

                if (!get_string_manager()->translation_exists($newlangsetting, false)) {
                    $newlangsetting = null;
                } else {
                    $validlangsettings[] = 'newlangsetting';
                }

                if (!get_string_manager()->translation_exists($newlangsettingsimple, false)) {
                    $newlangsettingsimple = null;
                } else {
                    $validlangsettings[] = 'newlangsettingsimple';
                }

                if (!$validlangsettings) {
                    // No version of the new language setting exists, keep existing setting.
                    $user->lang = $originallangsetting;
                } else {
                    // At least one version exists, update settings.
                    if ($newlangsettingwp && $originallangsetting == $newlangsettingwp) {
                        $user->lang = $newlangsettingwp;
                    } else if ($newlangsetting) {
                        $user->lang = $newlangsetting;
                    } else {
                        $user->lang = $newlangsettingsimple;
                    }

                    if (!$user->lang) {
                        $user->lang = $originallangsetting;
                    }
                }
            }
        }

        return $user;
    }

    /**
     * Check if any of the fields in the field map configuration would require calling Graph API function to get user details.
     *
     * @param string $eventtype
     *
     * @return bool
     */
    public static function fieldmap_require_graph_api_call($eventtype) {
        global $CFG;

        require_once($CFG->dirroot . '/auth/oidc/lib.php');

        // Microsoft Identity Platform can only get user profile from Graph API.
        if (get_config('auth_oidc', 'idptype') == AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM) {
            return true;
        } else {
            $fieldmappings = auth_oidc_get_field_mappings();
            $idtokenfields = ['givenName', 'surname', 'mail', 'objectId', 'userPrincipalName', 'bindingusernameclaim'];

            foreach ($fieldmappings as $fieldmapping) {
                $remotefield = $fieldmapping['field_map'];
                if (!in_array($remotefield, $idtokenfields)) {
                    if ($fieldmapping['update_local'] == 'always' || $fieldmapping['update_local'] == 'on' . $eventtype) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check the configured user creation restriction and determine whether a user can be created.
     *
     * @param array $entraiduserdata Array of user data from Microsoft Entra ID.
     * @return bool Whether the user can be created.
     */
    protected function check_usercreationrestriction($entraiduserdata) {
        $restriction = get_config('local_o365', 'usersynccreationrestriction');
        if (empty($restriction)) {
            return true;
        }
        $restriction = @unserialize($restriction);
        if (empty($restriction) || !is_array($restriction)) {
            return true;
        }
        if (empty($restriction['remotefield']) || empty($restriction['value'])) {
            return true;
        }
        $useregex = (!empty($restriction['useregex'])) ? true : false;

        if ($restriction['remotefield'] === 'o365group') {
            if (unified::is_configured() !== true) {
                utils::debug('graph api is not configured.', __METHOD__);

                return false;
            }

            $apiclient = $this->construct_user_api();

            try {
                $group = $apiclient->get_group_by_name($restriction['value']);
                if (empty($group) || !isset($group['id'])) {
                    utils::debug('Could not find group (1)', __METHOD__, $group);

                    return false;
                }
                $usergroups = $apiclient->get_user_transitive_groups($entraiduserdata['id']);

                foreach ($usergroups as $usergroup) {
                    if ($group['id'] === $usergroup) {
                        return true;
                    }
                }

                return false;
            } catch (moodle_exception $e) {
                utils::debug('Could not find group (2)', __METHOD__, $e);

                return false;
            }
        } else if ($restriction['remotefield'] === 'o365groupid') {
            if (unified::is_configured() !== true) {
                utils::debug('graph api is not configured.', __METHOD__);

                return false;
            }

            $apiclient = $this->construct_user_api();

            try {
                $group = $apiclient->get_group($restriction['value']);
                if (empty($group) || !isset($group['id'])) {
                    utils::debug('Could not find group (1)', __METHOD__, $group);

                    return false;
                }
                $usergroups = $apiclient->get_user_transitive_groups($entraiduserdata['id']);

                foreach ($usergroups as $usergroup) {
                    if ($group['id'] === $usergroup) {
                        return true;
                    }
                }

                return false;
            } catch (moodle_exception $e) {
                utils::debug('Could not find group (2)', __METHOD__, $e);

                return false;
            }
        } else if (substr($restriction['remotefield'], 0, 18) == 'extensionAttribute') {
            $extensionattributeid = substr($restriction['remotefield'], 18);
            if (ctype_digit($extensionattributeid) && $extensionattributeid >= 1 && $extensionattributeid <= 15) {
                if (isset($entraiduserdata['onPremisesExtensionAttributes']) &&
                    isset($entraiduserdata['onPremisesExtensionAttributes'][$restriction['remotefield']])) {
                    $fieldval = $entraiduserdata['onPremisesExtensionAttributes'][$restriction['remotefield']];
                    $restrictionval = $restriction['value'];

                    if ($useregex === true) {
                        $count = @preg_match('/' . $restrictionval . '/', $fieldval, $matches);
                        if (!empty($count)) {
                            return true;
                        }
                    } else {
                        if ($fieldval === $restrictionval) {
                            return true;
                        }
                    }
                }

                return false;
            } else {
                utils::debug('Invalid extension attribute ID', __METHOD__);
                return false;
            }
        } else {
            if (!isset($entraiduserdata[$restriction['remotefield']])) {
                return false;
            }
            $fieldval = $entraiduserdata[$restriction['remotefield']];
            $restrictionval = $restriction['value'];

            if ($useregex === true) {
                $count = @preg_match('/' . $restrictionval . '/', $fieldval, $matches);
                if (!empty($count)) {
                    return true;
                }
            } else {
                if ($fieldval === $restrictionval) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Create a Moodle user from Microsoft Entra ID user data.
     *
     * @param array $entraiduserdata Array of Microsoft Entra ID user data.
     * @param array $syncoptions
     * @return stdClass|bool An object representing the created Moodle user.
     */
    public function create_user_from_entra_id_data($entraiduserdata, $syncoptions) {
        global $CFG, $DB;

        $creationallowed = $this->check_usercreationrestriction($entraiduserdata);

        if ($creationallowed !== true) {
            $this->mtrace('Cannot create user because they do not meet the configured user creation restrictions.');
            return false;
        }

        $username = $entraiduserdata['useridentifier'];
        if (isset($entraiduserdata['convertedidentifier']) && $entraiduserdata['convertedidentifier']) {
            $username = $entraiduserdata['convertedidentifier'];
        }
        $newuser = (object)[
            'auth' => 'oidc',
            'username' => trim(core_text::strtolower($username)),
            'confirmed' => 1,
            'timecreated' => time(),
            'mnethostid' => $CFG->mnet_localhost_id,
        ];

        // Determine if the newly created user needs to be suspended.
        if (isset($syncoptions['disabledsync'])) {
            if (isset($entraiduserdata['accountEnabled']) && $entraiduserdata['accountEnabled'] == false) {
                $newuser->suspended = 1;
            }
        }

        $newuser = static::apply_configured_fieldmap($entraiduserdata, $newuser, 'create');

        $password = '';

        if (!empty($newuser->email)) {
            if (email_is_not_allowed($newuser->email)) {
                unset($newuser->email);
            }
        }

        $newuser->timemodified = $newuser->timecreated;
        $newuser->id = user_create_user($newuser, false, false);

        // Save user profile data.
        profile_save_data($newuser);

        $user = get_complete_user_data('id', $newuser->id);
        // Set the password.
        update_internal_user_password($user, $password);

        // Add o365 object.
        if (!$DB->record_exists('local_o365_objects', ['type' => 'user', 'moodleid' => $newuser->id])) {
            if (unified::is_configured()) {
                $userobjectid = $entraiduserdata['id'];
            } else {
                $userobjectid = $entraiduserdata['objectId'];
            }
            $now = time();
            $userobjectdata = (object)[
                'type' => 'user',
                'subtype' => '',
                'objectid' => $userobjectid,
                'o365name' => $entraiduserdata['useridentifier'],
                'moodleid' => $newuser->id,
                'tenant' => '',
                'timecreated' => $now,
                'timemodified' => $now,
            ];
            $userobjectdata->id = $DB->insert_record('local_o365_objects', $userobjectdata);
        }

        // Trigger event.
        \core\event\user_created::create_from_userid($newuser->id)->trigger();

        return $user;
    }

    /**
     * Updates a Moodle user from Microsoft Entra ID user data.
     *
     * @param array $entraiduserdata Array of Microsoft Entra ID user data.
     * @param object $fullexistinguser
     *
     * @return bool An boolean indicating that was created Moodle user.
     */
    public function update_user_from_entra_id_data($entraiduserdata, $fullexistinguser) {
        $existinguser = static::apply_configured_fieldmap($entraiduserdata, $fullexistinguser, 'login');

        if (!empty($existinguser->email)) {
            if (email_is_not_allowed($existinguser->email)) {
                unset($existinguser->email);
            }
        } else {
            // Email is originally pulled (optionally) from UPN, so an empty email should not wipe out Moodle email.
            unset($existinguser->email);
        }

        $existinguser->timemodified = time();

        // Update a user with a user object (will compare against the ID).
        user_update_user($existinguser, false, false);

        // Save user profile data.
        profile_save_data($existinguser);

        // Trigger event.
        \core\event\user_updated::create_from_userid($existinguser->id)->trigger();

        return true;
    }

    /**
     * Selectively run mtrace.
     *
     * @param string $msg The message.
     */
    public static function mtrace($msg) {
        if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
            mtrace('......... '.$msg);
        }
    }

    /**
     * Get an array of sync options.
     *
     * @return array Sync options
     */
    public static function get_sync_options() {
        $usersyncsettings = get_config('local_o365', 'usersync');
        $usersyncsettings = array_flip(explode(',', $usersyncsettings));
        return $usersyncsettings;
    }

    /**
     * Determine whether a sync option is enabled.
     *
     * @param string $option The option to check.
     * @return bool Whether the option is enabled.
     */
    public static function sync_option_enabled($option) {
        $options = static::get_sync_options();
        return isset($options[$option]);
    }

    /**
     * Sync Moodle users with the configured Microsoft Entra ID users.
     *
     * @param array $entraidusers Array of Microsoft Entra ID users from $this->get_users().
     * @param string $bindingusernameclaim
     * @return bool Success/Failure
     */
    public function sync_users(array $entraidusers = [], string $bindingusernameclaim = 'userPrincipalName') {
        global $DB, $CFG;

        $usersyncsettings = $this->get_sync_options();
        $switchauthminupnsplit0 = get_config('local_o365', 'switchauthminupnsplit0');
        if (empty($switchauthminupnsplit0)) {
            $switchauthminupnsplit0 = 10;
        }

        $usernames = [];
        $useridentifiers = [];

        $guestsync = array_key_exists('guestsync', $usersyncsettings);

        foreach ($entraidusers as $i => $user) {
            if (!isset($user[$bindingusernameclaim])) {
                // User doesn't have the binding username claim, should be deleted users.
                unset($entraidusers[$i]);
                continue;
            }

            if (!$guestsync || $bindingusernameclaim != 'userPrincipalName') {
                if (strpos($user['userPrincipalName'], '#EXT#') !== false) {
                    // The user is a guest user, and the guest sync option is disabled. Skip processing the user.
                    unset($entraidusers[$i]);
                    continue;
                }
            }

            $entraidusers[$i]['useridentifier'] = $user[$bindingusernameclaim];

            $useridentifierlower = core_text::strtolower($user[$bindingusernameclaim]);
            $entraidusers[$i]['useridentifierlower'] = $useridentifierlower;

            $usernames[] = $useridentifierlower;
            $useridentifiers[] = $useridentifierlower;

            if ($bindingusernameclaim == 'userPrincipalName') {
                $upnsplit = explode('@', $useridentifierlower);
                if (!empty($upnsplit[0])) {
                    $entraidusers[$i]['upnsplit0'] = $upnsplit[0];
                    $usernames[] = $upnsplit[0];
                }

                // Convert upn for guest users.
                if (stripos($useridentifierlower, '#ext#') !== false) {
                    $useridentifierlower = core_text::strtolower($user['mail']);

                    $usernames[] = $useridentifierlower;
                    $useridentifiers[] = $useridentifierlower;

                    $upnsplit = explode('@', $useridentifierlower);
                    if (!empty($upnsplit[0])) {
                        $usernames[] = $upnsplit[0];
                    }
                }
            }

        }

        if (!$entraidusers) {
            return true;
        }

        // In order to find existing user accounts using isset($existingusers[$entraidupn]) we have to index the array
        // by email address if we match Entra ID UPNs against Moodle email addresses!
        $basesql = " u.id as muserid,
                     u.auth,
                     u.suspended,
                     tok.id as tokid,
                     conn.id as existingconnectionid,
                     assign.assigned assigned,
                     assign.photoid photoid,
                     assign.photoupdated photoupdated,
                     obj.id AS objectid
                FROM {user} u
           LEFT JOIN {auth_oidc_token} tok ON tok.userid = u.id
           LEFT JOIN {local_o365_connections} conn ON conn.muserid = u.id
           LEFT JOIN {local_o365_appassign} assign ON assign.muserid = u.id
           LEFT JOIN {local_o365_objects} obj ON obj.type = ? AND obj.moodleid = u.id
               WHERE u.mnethostid = ? AND u.deleted = ? ";
        $orderbysql = " ORDER BY CONCAT(u.username, '~')"; // Sort john.smith@email.com before john.smith.

        $fallbackusers = [];
        if (isset($usersyncsettings['emailsync'])) {
            $select = "SELECT LOWER(u.email) AS email, LOWER(u.username) AS username, ";

            $duplicateemailaddresses = local_o365_get_duplicate_emails();
            if ($duplicateemailaddresses) {
                // Match by email, but duplicate email exists, revert to match by username.
                $fallbackselect = "SELECT LOWER(u.username) AS username, LOWER(u.email) AS email, ";
                [$duplicateemailsql, $duplicateemailparams] = $DB->get_in_or_equal($duplicateemailaddresses);
                $fallbackadditionalcondition = " AND LOWER(u.email) {$duplicateemailsql} ";
                $sql = $fallbackselect . $basesql . $fallbackadditionalcondition . $orderbysql;
                $fallbackparams = array_merge(['user'], [$CFG->mnet_localhost_id, '0'], $duplicateemailparams);
                $fallbackusers = $DB->get_records_sql($sql, $fallbackparams);
            }
        } else {
            $select = "SELECT LOWER(u.username) AS username, LOWER(u.email) AS email, ";
        }

        if ($fallbackusers) {
            [$duplicateemailsql, $duplicateemailparams] = $DB->get_in_or_equal($duplicateemailaddresses, SQL_PARAMS_QM, 'param',
                false);
            $sql = $select . $basesql . " AND LOWER(u.email) {$duplicateemailsql} " . $orderbysql;
            $params = array_merge(['user'], [$CFG->mnet_localhost_id, '0'], $duplicateemailparams);
        } else {
            $sql = $select . $basesql . $orderbysql;
            $params = array_merge(['user'], [$CFG->mnet_localhost_id, '0']);
        }

        $existingusers = $DB->get_records_sql($sql, $params);
        $existingusers = $existingusers + $fallbackusers;

        foreach ($existingusers as $id => $existinguser) {
            if (isset($usersyncsettings['emailsync'])) {
                if (!in_array($existinguser->email, $usernames)) {
                    unset($existingusers[$id]);
                }
            } else {
                if (!in_array($existinguser->username, $usernames)) {
                    unset($existingusers[$id]);
                }
            }
        }

        // Fetch linked Entra ID user accounts.
        if ($useridentifiers && $usernames) {
            [$useridentifiersql, $useridentifierparams] = $DB->get_in_or_equal($useridentifiers);
            [$usernamesql, $usernameparams] = $DB->get_in_or_equal($usernames, SQL_PARAMS_QM, 'param', false);
            $sql = 'SELECT tok.oidcusername,
                       u.username as username,
                       u.id as muserid,
                       u.auth,
                       u.suspended,
                       tok.id as tokid,
                       conn.id as existingconnectionid,
                       assign.assigned assigned,
                       assign.photoid photoid,
                       assign.photoupdated photoupdated,
                       obj.id AS objectid
                  FROM {user} u
             LEFT JOIN {auth_oidc_token} tok ON tok.userid = u.id
             LEFT JOIN {local_o365_connections} conn ON conn.muserid = u.id
             LEFT JOIN {local_o365_appassign} assign ON assign.muserid = u.id
             LEFT JOIN {local_o365_objects} obj ON obj.type = ? AND obj.moodleid = u.id
                 WHERE tok.oidcusername ' . $useridentifiersql . ' AND u.username ' . $usernamesql . ' AND u.mnethostid = ?
                    AND u.deleted = ? ';
            $params = array_merge(['user'], $useridentifierparams, $usernameparams, [$CFG->mnet_localhost_id, '0']);
            $linkedexistingusers = $DB->get_records_sql($sql, $params);

            $existingusers = $existingusers + $linkedexistingusers;
        }

        $processedusers = [];

        $supportuseridentifierchangeconfig = get_config('local_o365', 'support_user_identifier_change');

        foreach ($entraidusers as $entraiduser) {
            if (unified::is_configured()) {
                $userobjectid = $entraiduser['id'];
            } else {
                $userobjectid = $entraiduser['objectId'];
            }

            if (empty($entraiduser['useridentifierlower'])) {
                $this->mtrace('Microsoft Entra ID user missing identifier (' . $userobjectid . '); skipping...');
                continue;
            }

            $outputmessage = 'Syncing user ' . $entraiduser['useridentifierlower'];
            if ($bindingusernameclaim != 'userPrincipalName') {
                $outputmessage .= ' (UPN: ' . $entraiduser['userPrincipalName'] . ')';
            }
            $this->mtrace($outputmessage);

            // Process guest users.
            $entraiduser['convertedidentifier'] = $entraiduser['useridentifierlower'];
            if ($guestsync && $bindingusernameclaim == 'userPrincipalName' &&
                stripos($entraiduser['userPrincipalName'], '#EXT#') !== false) {
                $entraiduser['convertedidentifier'] = strtolower($entraiduser['mail']);
            }

            if (in_array($entraiduser['convertedidentifier'], $processedusers)) {
                $this->mtrace('User already processed; skipping...');
                continue;
            } else {
                $processedusers[] = $entraiduser['convertedidentifier'];
            }

            $needsyncprofile = false;
            $connected = false;
            if (!isset($existingusers[$entraiduser['useridentifierlower']]) &&
                (!isset($entraiduser['upnsplit0']) || !isset($existingusers[$entraiduser['upnsplit0']])) &&
                !isset($existingusers[$entraiduser['convertedidentifier']])) {
                // Check if the user has been renamed.
                $syncnewuser = array_key_exists('create', $usersyncsettings);
                if (isset($entraiduser['id']) && $entraiduser['id'] && $existingusermatching = $DB->get_record('local_o365_objects',
                        ['type' => 'user', 'objectid' => $entraiduser['id']])) {
                    // This is a previously connected user who has been renamed in Microsoft.
                    $needsyncprofile = true;

                    $renamedmoodleuser = core_user::get_user($existingusermatching->moodleid);
                    if ($renamedmoodleuser) {
                        $this->mtrace('The user has been renamed in Microsoft...');
                        $syncnewuser = false;

                        if ($supportuseridentifierchangeconfig == 1) {
                            // Check if manually matched users, who shouldn't be renamed.
                            if ($DB->record_exists('local_o365_connections', ['muserid' => $renamedmoodleuser->id])) {
                                $this->mtrace('The user is manually matched, skipping renaming...');
                            } else {
                                $this->mtrace('Updating Moodle username...');

                                // Update user record.
                                $username = $entraiduser['useridentifier'];
                                if (isset($entraiduser['convertedidentifier']) && $entraiduser['convertedidentifier']) {
                                    $username = $entraiduser['convertedidentifier'];
                                }
                                $username = trim(core_text::strtolower($username));
                                $renamedmoodleuser->username = $username;
                                user_update_user($renamedmoodleuser, false);

                                // Update connection record.
                                $existingusermatching->o365name = $entraiduser['useridentifierlower'];
                                $DB->update_record('local_o365_objects', $existingusermatching);

                                // Update token record.
                                if ($existingtoken = $DB->get_record('auth_oidc_token', ['userid' => $renamedmoodleuser->id])) {
                                    $existingtoken->useridentifier = $entraiduser['useridentifier'];
                                    $existingtoken->username = $username;
                                    $DB->update_record('auth_oidc_token', $existingtoken);
                                }

                                if (in_array($username, [$entraiduser['useridentifierlower'], $entraiduser['upnsplit0']])) {
                                    $exactmatch = true;
                                } else {
                                    $exactmatch = false;
                                }

                                $existinguserrecord = new stdClass();
                                $existinguserrecord->muserid = $renamedmoodleuser->id;
                                $existinguserrecord->objectid = $existingusermatching->id;
                                $existinguserrecord->username = $username;
                                $existinguserrecord->suspended = $renamedmoodleuser->suspended;
                                $existinguserrecord->auth = $renamedmoodleuser->auth;

                                $connected = $this->sync_existing_user($usersyncsettings, $entraiduser, $existinguserrecord,
                                    $exactmatch);
                                $existinguser = $renamedmoodleuser;
                            }
                        } else {
                            $this->mtrace('The option to update Moodle username is disabled...');
                        }
                    }
                }
                if ($syncnewuser) {
                    $this->sync_new_user($usersyncsettings, $entraiduser, isset($usersyncsettings['guestsync']));
                }
            } else {
                // Entra ID user details match existing user record.
                $needsyncprofile = true;

                // First check if this is a previously connected user who has been renamed in Microsoft, but the new username
                // matches an existing Moodle account.
                $userrenamefailed = false;
                $userrenamed = false;
                $syncexistinguser = true;
                if (isset($entraiduser['id']) && $entraiduser['id'] &&
                    $existingusermatching = $DB->get_record('local_o365_objects',
                        ['type' => 'user', 'objectid' => $entraiduser['id']])) {
                    $possibleo365names = [$entraiduser['useridentifierlower'], $entraiduser['convertedidentifier'],
                        $entraiduser['useridentifier']];
                    if (isset($entraiduser['upnsplit0'])) {
                        $possibleo365names[] = $entraiduser['upnsplit0'];
                    }
                    if (!in_array($existingusermatching->o365name, $possibleo365names)) {
                        $syncexistinguser = false;
                        $this->mtrace('The user has been renamed in Microsoft...');
                        if ($supportuseridentifierchangeconfig == 1) {
                            // Check if the user is manually matched, which shouldn't be renamed.
                            if ($DB->record_exists('local_o365_connections', ['muserid' => $existingusermatching->moodleid])) {
                                $this->mtrace('The user is manually matched, skipping renaming...');
                            } else {
                                // This is a previously connected user who has been renamed in Microsoft.
                                $this->mtrace('Updating Moodle username...');
                                $renamedmoodleuser = core_user::get_user($existingusermatching->moodleid);
                                if ($renamedmoodleuser) {
                                    // Update user record.
                                    $username = $entraiduser['useridentifier'];
                                    if (isset($entraiduser['convertedidentifier']) && $entraiduser['convertedidentifier']) {
                                        $username = $entraiduser['convertedidentifier'];
                                    }
                                    $username = trim(core_text::strtolower($username));
                                    // Check if existing user with same username exists.
                                    $userwithduplicateusername = core_user::get_user_by_username($username);
                                    if ($userwithduplicateusername) {
                                        // User with same username exists, cannot rename.
                                        $this->mtrace('User with same username exists; cannot rename.');
                                        $userrenamefailed = true;
                                        $needsyncprofile = false;
                                    } else {
                                        $userrenamed = true;

                                        $renamedmoodleuser->username = $username;
                                        user_update_user($renamedmoodleuser, false);

                                        // Update connection record.
                                        $existingusermatching->o365name = $entraiduser['useridentifierlower'];
                                        $DB->update_record('local_o365_objects', $existingusermatching);

                                        // Update token record.
                                        if ($existingtoken = $DB->get_record('auth_oidc_token',
                                            ['userid' => $renamedmoodleuser->id])) {
                                            $existingtoken->useridentifier = $entraiduser['useridentifier'];
                                            $existingtoken->username = $username;
                                            $DB->update_record('auth_oidc_token', $existingtoken);
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->mtrace('The option to update Moodle username is disabled...');
                        }
                    }
                }

                if (!$userrenamefailed || $userrenamed) {
                    $existinguser = null;
                    if (isset($existingusers[$entraiduser['useridentifierlower']])) {
                        $existinguser = $existingusers[$entraiduser['useridentifierlower']];
                        $exactmatch = true;
                    } else if (isset($entraiduser['upnsplit0']) && ($existingusers[$entraiduser['upnsplit0']])) {
                        $existinguser = $existingusers[$entraiduser['upnsplit0']];
                        $exactmatch = strlen($entraiduser['upnsplit0']) >= $switchauthminupnsplit0;
                    } else if (isset($existingusers[$entraiduser['convertedidentifier']])) {
                        $existinguser = $existingusers[$entraiduser['convertedidentifier']];
                        $exactmatch = true;
                    }

                    // Process guest users.
                    if (stripos($entraiduser['userPrincipalName'], '_ext_') !== false) {
                        $this->mtrace('The user is a guest user.');
                        if (!isset($usersyncsettings['guestsync'])) {
                            $this->mtrace('The option to sync guest users is turned off.');
                            $this->mtrace('User is already synced, but not updated.');

                            continue;
                        }
                    }

                    $connected = false;
                    if ($syncexistinguser) {
                        $connected = $this->sync_existing_user($usersyncsettings, $entraiduser, $existinguser, $exactmatch);
                    }

                    if (($existinguser->auth === 'oidc' || empty($existinguser->tokid)) && $connected) {
                        // Create userobject if it does not exist.
                        if (empty($existinguser->objectid)) {
                            $this->mtrace('Adding o365 object record for user.');
                            $now = time();
                            $userobjectdata = (object) [
                                'type' => 'user',
                                'subtype' => '',
                                'objectid' => $userobjectid,
                                'o365name' => $entraiduser['useridentifier'],
                                'moodleid' => $existinguser->muserid,
                                'tenant' => '',
                                'timecreated' => $now,
                                'timemodified' => $now,
                            ];
                            $userobjectdata->id = $DB->insert_record('local_o365_objects', $userobjectdata);
                        }
                        // User already connected.
                        $this->mtrace('User is now synced.');
                    }
                }
            }

            if ($needsyncprofile) {
                // Update existing user on moodle from AD.
                if ($existinguser->auth === 'oidc' && $connected) {
                    if (isset($usersyncsettings['update'])) {
                        $this->mtrace('Updating Moodle user data from Microsoft Entra ID user data.');
                        $fullexistinguser = get_complete_user_data('username', $existinguser->username);
                        if ($fullexistinguser) {
                            $existingusercopy = core_user::get_user_by_username($existinguser->username);
                            $fullexistinguser->description = $existingusercopy->description;
                            $this->update_user_from_entra_id_data($entraiduser, $fullexistinguser);
                            $this->mtrace('User is now updated.');
                        } else {
                            $this->mtrace('Update failed for user with username "' . $existinguser->username . '".');
                        }
                    }
                }
            }
            $this->mtrace(' ');
        }

        return true;
    }

    /**
     * Sync a Microsoft Entra ID user that hasn't been synced before - create a new Moodle account.
     *
     * @param array $syncoptions
     * @param array $entraiduserdata
     * @param bool $syncguestusers
     * @return false|stdClass|null
     */
    protected function sync_new_user($syncoptions, $entraiduserdata, bool $syncguestusers = false) {
        global $DB;

        $this->mtrace('User doesn\'t exist in Moodle');

        $newmuser = null;

        $userobjectid = (unified::is_configured())
            ? $entraiduserdata['id']
            : $entraiduserdata['objectId'];

        // Create moodle account, if enabled.
        if (!isset($syncoptions['create'])) {
            $this->mtrace('Not creating a Moodle user because that sync option is disabled.');
            return null;
        }

        // Process guest users.
        if (stripos($entraiduserdata['userPrincipalName'], '_ext_') !== false) {
            $this->mtrace('The user is a guest user.');
            if (!$syncguestusers) {
                $this->mtrace('The option to sync guest users is turned off.');
                $this->mtrace('User is not created.');
                return null;
            }
        }

        try {
            $newmuser = $this->create_user_from_entra_id_data($entraiduserdata, $syncoptions);
            if (!empty($newmuser)) {
                $this->mtrace('Created user #' . $newmuser->id);
            }
        } catch (moodle_exception $e) {
            if (isset($syncoptions['emailsync'])) {
                if ($DB->record_exists('user', ['username' => $entraiduserdata['useridentifier']])) {
                    $this->mtrace('Could not create user "' . $entraiduserdata['useridentifier'] .
                        '" Reason: user with same username, but different email already exists.');
                } else {
                    $this->mtrace('Could not create user with email "' . $entraiduserdata['useridentifier'] . '" Reason: ' .
                        $e->getMessage());
                }
            } else {
                $this->mtrace('Could not create user "' . $entraiduserdata['useridentifier'] . '" Reason: ' . $e->getMessage());
            }
        }

        // User app assignment.
        if (!empty($syncoptions['appassign'])) {
            try {
                if (!empty($newmuser) && !empty($userobjectid)) {
                    $this->assign_user($newmuser->id, $userobjectid);
                }
            } catch (moodle_exception $e) {
                $this->mtrace('Could not assign user "' . $entraiduserdata['useridentifier'] . '" Reason: ' . $e->getMessage());
            }
        }

        // User photo sync.
        if (!empty($syncoptions['photosync'])) {
            if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
                try {
                    if (!empty($newmuser)) {
                        $this->assign_photo($newmuser->id);
                    }
                } catch (moodle_exception $e) {
                    $this->mtrace('Could not assign photo to user "' . $entraiduserdata['useridentifier'] . '" Reason: ' .
                        $e->getMessage());
                }
            }
        }

        // User timezone.
        if (!empty($syncoptions['tzsync'])) {
            if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
                try {
                    if (!empty($newmuser)) {
                        $this->sync_timezone($newmuser->id);
                    }
                } catch (moodle_exception $e) {
                    $this->mtrace('Could not sync timezone for user "' . $entraiduserdata['useridentifier'] . '" Reason: ' .
                        $e->getMessage());
                }
            }
        }

        return $newmuser;
    }

    /**
     * Sync a Moodle user who has been previously connected to a Microsoft Entra ID account.
     *
     * @param array $syncoptions
     * @param array $entraiduserdata
     * @param object $existinguser
     * @param bool $exactmatch
     *
     * @return bool
     */
    protected function sync_existing_user($syncoptions, $entraiduserdata, $existinguser, $exactmatch) {
        global $DB;

        $photoexpire = get_config('local_o365', 'photoexpire');
        if (empty($photoexpire) || !is_numeric($photoexpire)) {
            $photoexpire = 24;
        }
        $photoexpiresec = $photoexpire * 3600;

        $userobjectid = (unified::is_configured()) ? $entraiduserdata['id'] : $entraiduserdata['objectId'];

        // Check for user GUID changes.
        // There shouldn't be multiple token records, but just in case.
        $oidctokenrecords = $DB->get_records('auth_oidc_token',
            ['userid' => $existinguser->muserid, 'useridentifier' => $existinguser->username]);
        foreach ($oidctokenrecords as $oidctokenrecord) {
            if ($oidctokenrecord->oidcuniqid != $userobjectid) {
                $DB->delete_records('auth_oidc_token', ['id' => $oidctokenrecord->id]);
                $this->mtrace('Deleted auth_oidc token due to conflicts.');
            }
        }

        if ($localo365objectrecord = $DB->get_record('local_o365_objects', ['id' => $existinguser->objectid])) {
            if ($localo365objectrecord->objectid != $userobjectid) {
                $localo365objectrecord->objectid = $userobjectid;
                $DB->update_record('local_o365_objects', $localo365objectrecord);
                $this->mtrace('Updated user object ID in local_o365_object record.');
            }
        }

        // Assign user to app if not already assigned.
        if (isset($syncoptions['appassign'])) {
            if (empty($existinguser->assigned)) {
                try {
                    if (!empty($existinguser->muserid) && !empty($userobjectid)) {
                        $this->assign_user($existinguser->muserid, $userobjectid);
                    }
                } catch (moodle_exception $e) {
                    $this->mtrace('Could not assign user "'.$entraiduserdata['useridentifier'].'" Reason: '.$e->getMessage());
                }
            }
        }

        // Perform photo sync.
        if (isset($syncoptions['photosync'])) {
            if (empty($existinguser->photoupdated) || ($existinguser->photoupdated + $photoexpiresec) < time()) {
                try {
                    if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
                        $this->assign_photo($existinguser->muserid);
                    }
                } catch (moodle_exception $e) {
                    $this->mtrace('Could not assign profile photo to user "' . $entraiduserdata['useridentifier'] . '" Reason: ' .
                        $e->getMessage());
                }
            }
        }

        // Perform timezone sync.
        if (isset($syncoptions['tzsync'])) {
            try {
                if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
                    $this->sync_timezone($existinguser->muserid);
                }
            } catch (moodle_exception $e) {
                $this->mtrace('Could not sync timezone for user "' . $entraiduserdata['useridentifier'] . '" Reason: ' .
                    $e->getMessage());
            }
        }

        // Sync disabled status.
        if (isset($syncoptions['disabledsync'])) {
            if (isset($entraiduserdata['accountEnabled'])) {
                if ($entraiduserdata['accountEnabled']) {
                    if ($existinguser->suspended == 1) {
                        $completeexistinguser = core_user::get_user($existinguser->muserid);
                        $completeexistinguser->suspended = 0;
                        user_update_user($completeexistinguser, false);
                    }
                } else {
                    if ($existinguser->suspended == 0) {
                        $completeexistinguser = core_user::get_user($existinguser->muserid);
                        $completeexistinguser->suspended = 1;
                        user_update_user($completeexistinguser, false);
                    }
                }
            }
        }

        // Match user if needed.
        if ($existinguser->auth !== 'oidc') {
            $this->mtrace('Found a user in Microsoft Entra ID that seems to match a user in Moodle');
            $this->mtrace(sprintf('moodle username: %s, Entra ID user identifier: %s', $existinguser->username,
                $entraiduserdata['useridentifierlower']));
            return $this->sync_users_matchuser($syncoptions, $entraiduserdata, $existinguser, $exactmatch);
        } else {
            $this->mtrace('The user is already using OIDC for authentication.');
            return true;
        }
    }

    /**
     * Match a Microsoft Entra ID user with a Moodle user.
     *
     * @param array $syncoptions
     * @param array $entraiduserdata
     * @param object $existinguser
     * @param bool $exactmatch
     * @return bool
     */
    protected function sync_users_matchuser($syncoptions, $entraiduserdata, $existinguser, $exactmatch) {
        global $DB;

        if (!isset($syncoptions['match'])) {
            $this->mtrace('Not matching user because that sync option is disabled.');
            return false;
        }

        if (isset($syncoptions['matchswitchauth']) && $exactmatch) {
            // Switch the user to OIDC authentication method, but only if this setting is enabled and full username matched.
            // Do not switch Moodle user to OIDC if another Moodle user is already using same Microsoft 365 account for logging in.
            $sql = 'SELECT u.username
                      FROM {user} u
                 LEFT JOIN {local_o365_objects} obj ON obj.type = ? AND obj.moodleid = u.id
                 WHERE obj.o365name = ?
                   AND u.username != ?';
            $params = ['user', $entraiduserdata['useridentifierlower'], $existinguser->username];
            $alreadylinkedusername = $DB->get_field_sql($sql, $params);

            if ($alreadylinkedusername !== false) {
                $errmsg = 'This Microsoft Entra ID user has already been linked with Moodle user %s. ' .
                    'Not switching Moodle user %s to OIDC.';
                $this->mtrace(sprintf($errmsg, $alreadylinkedusername, $existinguser->username));
                return true;
            } else {
                if (!empty($existinguser->existingconnectionid)) {
                    // Delete existing connection before linking (in case matching was performed without auth switching previously).
                    $DB->delete_records_select('local_o365_connections', "id = {$existinguser->existingconnectionid}");
                }
                $fullexistinguser = get_complete_user_data('username', $existinguser->username);
                $existinguser->id = $fullexistinguser->id;
                $existinguser->auth = 'oidc';
                user_update_user($existinguser, true);
                // Clear user's password.
                $password = '';
                $existinguser->password = $fullexistinguser->password;
                update_internal_user_password($existinguser, $password);
                $this->mtrace('Switched user to OIDC.');

                // Clear force password change preference.
                unset_user_preference('auth_forcepasswordchange', $existinguser);

                return true;
            }
        } else if (!empty($existinguser->existingconnectionid)) {
            $this->mtrace('User is already matched.');
            return true;
        } else {
            // Match to o365 account, if enabled.
            if ($existingconnectionrecord = $DB->get_record('local_o365_connections',
                ['entraidupn' => $entraiduserdata['useridentifierlower']])) {
                if ($existingconnectionrecord->muserid != $existinguser->muserid) {
                    $existingconnectionrecord->muserid = $existinguser->muserid;
                    $DB->update_record('local_o365_connections', $existingconnectionrecord);
                }
            } else {
                $matchrec = [
                    'muserid' => $existinguser->muserid,
                    'entraidupn' => $entraiduserdata['useridentifierlower'],
                    'uselogin' => isset($syncoptions['matchswitchauth']) ? 1 : 0,
                ];
                $DB->insert_record('local_o365_connections', $matchrec);
                $this->mtrace('Matched user, but did not switch them to OIDC.');
            }

            return true;
        }
    }

    /**
     * Suspend users that have been deleted from Microsoft Entra ID, and optionally delete them.
     * This function will get the list of recently deleted users in the last 30 days first, and suspend their accounts.
     * It will then try to find all remaining users matched with Microsoft Entra ID users, and check if a valid user can be found
     * in Microsoft Entra ID.
     * If a valid user is not found, it will suspend the user in the first run, and delete it in the next run if the option is set.
     *
     * So in a normal use case, where the option is enabled and not changed, and a Microsoft Entra ID account is deleted:
     *  - Their matching Moodle account will be suspended on the first task run after Microsoft Entra ID account deletion;
     *  - The account will be deleted on the first run 30 days after their Microsoft Entra ID account deletion, if $delete is true.
     *
     * In case the option to delete Moodle users is changed from disabled to enabled:
     *  - If the deletion of the Microsoft 365 account happened before 30 days:
     *    - The matching Moodle account will be suspended on the first task run after the configuration change is made.
     *    - The account will be deleted on the second task run after the configuration change is made, if $delete is true.
     *  - If the deletion of the Microsoft 365 account happened within 30 days:
     *    - The matching Moodle account will be suspended on the first task run after the configuration change is made.
     *    - The account will be deleted on the first run 30 days after their Microsoft 365 account deletion, if $delete is true.
     *
     * Note this will not catch oidc users without matching Microsoft 365 account.
     *
     * @param array $entraidusers
     * @param bool $delete
     *
     * @return bool
     */
    public function suspend_users(array $entraidusers, bool $delete = false) {
        global $CFG, $DB;

        $apiclient = $this->construct_user_api();

        try {
            $deletedusersids = [];

            $deletedusers = $apiclient->list_deleted_users();
            foreach ($deletedusers as $deleteduser) {
                if (!empty($deleteduser) && isset($deleteduser['id'])) {
                    // Check for synced user.
                    $sql = 'SELECT u.*
                              FROM {user} u
                              JOIN {local_o365_objects} obj ON obj.type = ? AND obj.moodleid = u.id
                             WHERE u.mnethostid = ?
                               AND u.deleted = ?
                               AND u.suspended = ?
                               AND u.auth = ?
                               AND obj.objectid = ? ';
                    $params = ['user', $CFG->mnet_localhost_id, '0', '0', 'oidc', $deleteduser['id']];
                    $synceduser = $DB->get_record_sql($sql, $params);
                    if (!empty($synceduser)) {
                        $synceduser->suspended = 1;
                        user_update_user($synceduser, false);
                        $this->mtrace($synceduser->username . ' was deleted in Entra ID, the matching account is suspended.');
                    }
                    $deletedusersids[] = $deleteduser['id'];
                }
            }

            $existingsql = 'SELECT u.*, obj.objectid
                              FROM {user} u
                              JOIN {local_o365_objects} obj ON obj.type = ? AND obj.moodleid = u.id
                             WHERE u.mnethostid = ?
                               AND u.deleted = ?
                               AND u.auth = ? ';
            $existingsqlparams = ['user', $CFG->mnet_localhost_id, '0', 'oidc'];
            if ($deletedusersids) {
                // Check if all Moodle users with oidc authentication and matching records are still existing users in Entra ID.
                [$objectidsql, $objectidparams] = $DB->get_in_or_equal($deletedusersids, SQL_PARAMS_QM, 'param', false);
                $existingsql .= ' AND obj.objectid ' . $objectidsql;
                $existingsqlparams = array_merge($existingsqlparams, $objectidparams);
            }

            $existingusers = $DB->get_records_sql($existingsql, $existingsqlparams);
            $validentraiduserids = [];
            foreach ($entraidusers as $entraiduser) {
                $validentraiduserids[] = $entraiduser['id'];
            }

            foreach ($existingusers as $existinguser) {
                if (!in_array($existinguser->objectid, $validentraiduserids)) {
                    if ($existinguser->suspended) {
                        if ($delete) {
                            $this->mtrace('Could not find suspended user ' . $existinguser->username .
                                ' in Microsoft Entra ID. Deleting user...');
                            unset($existinguser->objectid);
                            delete_user($existinguser);
                        }
                    } else if (!$existinguser->suspended) {
                        $this->mtrace('Could not find user ' . $existinguser->username .
                            ' in Microsoft Entra ID. Suspending user...');
                        $existinguser->suspended = 1;
                        unset($existinguser->objectid);
                        user_update_user($existinguser, false);
                    }
                }
            }

            return true;
        } catch (moodle_exception $e) {
            utils::debug('Could not delete users', __METHOD__, $e);

            return false;
        }
    }

    /**
     * Re-enable suspended users.
     * This function will ensure that for all the users in the array received, if they have a Moodle account that's suspended but
     * not deleted, the account will unsuspended.
     *
     * @param array $entraidusers
     * @param bool $syncdisabledstatus
     *
     * @return bool
     */
    public function reenable_suspsend_users(array $entraidusers, $syncdisabledstatus) {
        global $DB;

        $valientraiduserids = [];
        if ($syncdisabledstatus) {
            foreach ($entraidusers as $entraiduser) {
                if ($entraiduser['accountEnabled']) {
                    $valientraiduserids[] = $entraiduser['id'];
                }
            }
        } else {
            foreach ($entraidusers as $entraiduser) {
                $valientraiduserids[] = $entraiduser['id'];
            }
        }

        if ($valientraiduserids) {
            [$objectidsql, $objectidparams] = $DB->get_in_or_equal($valientraiduserids, SQL_PARAMS_NAMED);
            $query = 'SELECT u.*
                        FROM {user} u
                        JOIN {local_o365_objects} obj ON obj.type = :user AND obj.moodleid = u.id
                       WHERE u.auth = :oidc
                         AND u.deleted = :deleted
                         AND u.suspended = :suspended
                         AND obj.objectid ' . $objectidsql;
            $params = [
                'user' => 'user',
                'oidc' => 'oidc',
                'deleted' => 0,
                'suspended' => 1,
            ];
            $params = array_merge($params, $objectidparams);
            $suspendedusers = $DB->get_records_sql($query, $params);
            foreach ($suspendedusers as $suspendeduser) {
                $this->mtrace('Re-enabling user ' . $suspendeduser->username . '...');
                $suspendeduser->suspended = 0;
                user_update_user($suspendeduser, false);
            }
        }

        return true;
    }
}
