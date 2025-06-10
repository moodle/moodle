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
 * Utility functions for the SDS feature.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\sds;

use Exception;
use local_o365\httpclient;
use local_o365\oauth2\clientdata;
use local_o365\rest\unified;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Utility functions for the SDS feature.
 */
class utils {
    /**
     * Get the unified api client.
     *
     * @return unified|null The SDS API client.
     */
    public static function get_apiclient() : ?unified {
        $httpclient = new httpclient();
        try {
            $clientdata = clientdata::instance_from_oidc();
            $unifiedresource = unified::get_tokenresource();
            $unifiedtoken = \local_o365\utils::get_app_or_system_token($unifiedresource, $clientdata, $httpclient, false, false);

            if (!empty($unifiedtoken)) {
                $apiclient = new unified($unifiedtoken, $httpclient);
                return $apiclient;
            } else {
                mtrace('Could not construct system API user token for SDS sync task.');
            }
        } catch (moodle_exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Return the configuration status of SDS profile sync, and the name of the school if configured.
     *
     * @param unified|null $apiclient
     * @return array
     */
    public static function get_profile_sync_status_with_id_name(unified $apiclient = null) : array {
        $profilesyncenabled = false;
        $schoolid = '';
        $schoolname = '';

        $sdsprofilesyncconfig = get_config('local_o365', 'sdsprofilesync');

        if ($sdsprofilesyncconfig) {
            if (is_null($apiclient)) {
                $apiclient = static::get_apiclient();
            }

            if ($apiclient) {
                try {
                    $schoolresults = $apiclient->get_schools();
                    $schools = $schoolresults['value'];
                    while (!empty($schoolresults['@odata.nextLink'])) {
                        $nextlink = parse_url($schoolresults['@odata.nextLink']);
                        $schoolresults = [];
                        if (isset($nextlink['query'])) {
                            $query = [];
                            parse_str($nextlink['query'], $query);
                            if (isset($query['$skiptoken'])) {
                                $schoolresults = $apiclient->get_schools($query['$skiptoken']);
                                $schools = array_merge($schools, $schoolresults['value']);
                            }
                        }
                    }

                    foreach ($schools as $school) {
                        if ($school['id'] == $sdsprofilesyncconfig) {
                            $profilesyncenabled = true;
                            $schoolid = $school['id'];
                            $schoolname = $school['displayName'];
                            break;
                        }
                    }
                } catch (Exception $e) {
                    // School invalid, reset settings.
                    set_config('sdsprofilesync', '', 'local_o365');
                }
            }
        }

        return [$profilesyncenabled, $schoolid, $schoolname];
    }

    /**
     * Return the basic (ID and name) SDS field mappings and the additional SDS field mappings from the auth_oidc configuration.
     *
     * @return array[]
     */
    public static function get_sds_profile_sync_api_requirements() : array {
        $idandnamemappings = [];
        $additionalprofilemappings = [];

        $idandnamefieldnames = ['sds_school_id', 'sds_school_name'];
        $additionalfieldnames = ['sds_school_role', 'sds_student_externalId', 'sds_student_birthDate', 'sds_student_grade',
            'sds_student_graduationYear', 'sds_student_studentNumber', 'sds_teacher_externalId', 'sds_teacher_teacherNumber'];

        $authoidcconfigs = get_config('auth_oidc');
        foreach ($authoidcconfigs as $configkey => $authoidcconfig) {
            if (stripos($configkey, 'field_map_') === 0) {
                // The config is about field mapping.
                if (in_array($authoidcconfig, $idandnamefieldnames)) {
                    $localfieldname = substr($configkey, strlen('field_map_'));
                    $idandnamemappings[$authoidcconfig] = $localfieldname;
                } else if (in_array($authoidcconfig, $additionalfieldnames)) {
                    $localfieldname = substr($configkey, strlen('field_map_'));
                    $additionalprofilemappings[$authoidcconfig] = $localfieldname;
                }
            }
        }

        return [$idandnamemappings, $additionalprofilemappings];
    }

    /**
     * Return the ID of Moodle courses connected to SDS course sections.
     *
     * @return array
     */
    public static function get_sds_course_ids() {
        global $DB;

        return $DB->get_fieldset_select('local_o365_objects', 'moodleid', 'type = ?', ['sdssection']);
    }
}
