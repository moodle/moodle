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
 * external API for mobile web services
 *
 * @package    core
 * @subpackage webservice
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_webservice_external extends external_api {

    public static function get_siteinfo_parameters() {
        return new external_function_parameters(
            array('serviceshortnames' => new external_multiple_structure (
                new external_value(
                    PARAM_ALPHANUMEXT,
                    'service shortname'),
                    'service shortnames - by default, if the list is empty and mobile web services are enabled,
                    we return the mobile service functions',
                    VALUE_DEFAULT,
                    array()
                ),
            )
        );
    }

    /**
     * Return user information including profile picture + basic site information
     * Note:
     * - no capability checking because we return just known information by logged user
     * @param array $serviceshortnames of service shortnames - the functions of these services will be returned
     * @return array
     */
    function get_siteinfo($serviceshortnames = array()) {
        global $USER, $SITE, $CFG;

        $params = self::validate_parameters(self::get_siteinfo_parameters(),
                      array('serviceshortnames'=>$serviceshortnames));

        $profileimageurl = moodle_url::make_pluginfile_url(
                get_context_instance(CONTEXT_USER, $USER->id)->id, 'user', 'icon', NULL, '/', 'f1');

        require_once($CFG->dirroot . "/webservice/lib.php");
        $webservice = new webservice();

        //If no service listed always return the mobile one by default
        if (empty($params['serviceshortnames']) and $CFG->enablewebservices) {
           $mobileservice = $webservice->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
           if ($mobileservice->enabled) {
               $params['serviceshortnames'] = array(MOODLE_OFFICIAL_MOBILE_SERVICE); //return mobile service by default
           }
        }

        //retrieve the functions related to the services
        $functions = $webservice->get_external_functions_by_enabled_services($params['serviceshortnames']);

        //built up the returned values of the list of functions
        $componentversions = array();
        $avalaiblefunctions = array();
        foreach ($functions as $function) {
            $functioninfo = array();
            $functioninfo['name'] = $function->name;
            if ($function->component == 'moodle') {
                $version = $CFG->version; //moodle version
            } else {
                $versionpath = get_component_directory($function->component).'/version.php';
                if (is_readable($versionpath)) {
                    //we store the component version once retrieved (so we don't load twice the version.php)
                    if (!isset($componentversions[$function->component])) {
                        include($versionpath);
                        $componentversions[$function->component] = $plugin->version;
                        $version = $plugin->version;
                    } else {
                        $version = $componentversions[$function->component];
                    }
                } else {
                    //function component should always have a version.php,
                    //otherwise the function should have been described with component => 'moodle'
                    throw new moodle_exception('missingversionfile', 'webservice', '', $function->component);
                }
            }
            $functioninfo['version'] = $version;
            $avalaiblefunctions[] = $functioninfo;
        }

        return array(
            'sitename' => $SITE->fullname,
            'siteurl' => $CFG->wwwroot,
            'username' => $USER->username,
            'firstname' => $USER->firstname,
            'lastname' => $USER->lastname,
            'fullname' => fullname($USER),
            'userid' => $USER->id,
            'userpictureurl' => $profileimageurl->out(false),
            'functions' => $avalaiblefunctions
        );
    }

    public static function get_siteinfo_returns() {
        return new external_single_structure(
            array(
                'sitename'       => new external_value(PARAM_RAW, 'site name'),
                'username'       => new external_value(PARAM_RAW, 'username'),
                'firstname'      => new external_value(PARAM_TEXT, 'first name'),
                'lastname'       => new external_value(PARAM_TEXT, 'last name'),
                'fullname'       => new external_value(PARAM_TEXT, 'user full name'),
                'userid'         => new external_value(PARAM_INT, 'user id'),
                'siteurl'        => new external_value(PARAM_RAW, 'site url'),
                'userpictureurl' => new external_value(PARAM_URL, 'the user profile picture'),
                'functions'      => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'function name'),
                            'version' => new external_value(PARAM_FLOAT, 'The version number of moodle site/local plugin linked to the function')
                        ), 'functions that are available')
                    ),
            )
        );
    }
}