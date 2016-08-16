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
 * This is the external API for this tool.
 *
 * @package    tool_mobile
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mobile;

require_once("$CFG->libdir/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use external_warnings;

/**
 * This is the external API for this tool.
 *
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns description of get_plugins_supporting_mobile() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function get_plugins_supporting_mobile_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Returns a list of Moodle plugins supporting the mobile app.
     *
     * @return array an array of warnings and objects containing the plugin information
     * @since  Moodle 3.1
     */
    public static function get_plugins_supporting_mobile() {
        return array(
            'plugins' => api::get_plugins_supporting_mobile(),
            'warnings' => array(),
        );
    }

    /**
     * Returns description of get_plugins_supporting_mobile() result value.
     *
     * @return external_description
     * @since  Moodle 3.1
     */
    public static function get_plugins_supporting_mobile_returns() {
        return new external_single_structure(
            array(
                'plugins' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'component' => new external_value(PARAM_COMPONENT, 'The plugin component name.'),
                            'version' => new external_value(PARAM_NOTAGS, 'The plugin version number.'),
                            'addon' => new external_value(PARAM_COMPONENT, 'The Mobile addon (package) name.'),
                            'dependencies' => new external_multiple_structure(
                                                new external_value(PARAM_COMPONENT, 'Mobile addon name.'),
                                                'The list of Mobile addons this addon depends on.'
                                               ),
                            'fileurl' => new external_value(PARAM_URL, 'The addon package url for download
                                                            or empty if it doesn\'t exist.'),
                            'filehash' => new external_value(PARAM_RAW, 'The addon package hash or empty if it doesn\'t exist.'),
                            'filesize' => new external_value(PARAM_INT, 'The addon package size or empty if it doesn\'t exist.')
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of get_site_public_settings() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.2
     */
    public static function get_site_public_settings_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Returns a list of the site public settings, those not requiring authentication.
     *
     * @return array with the settings and warnings
     * @since  Moodle 3.2
     */
    public static function get_site_public_settings() {
        $result = api::get_site_public_settings();
        $result['warnings'] = array();
        return $result;
    }

    /**
     * Returns description of get_site_public_settings() result value.
     *
     * @return external_description
     * @since  Moodle 3.2
     */
    public static function get_site_public_settings_returns() {
        return new external_single_structure(
            array(
                'wwwroot' => new external_value(PARAM_RAW, 'Site URL.'),
                'httpswwwroot' => new external_value(PARAM_RAW, 'Site https URL (if httpslogin is enabled).'),
                'sitename' => new external_value(PARAM_TEXT, 'Site name.'),
                'guestlogin' => new external_value(PARAM_INT, 'Whether guest login is enabled.'),
                'rememberusername' => new external_value(PARAM_INT, 'Values: 0 for No, 1 for Yes, 2 for optional.'),
                'authloginviaemail' => new external_value(PARAM_INT, 'Whether log in via email is enabled.'),
                'registerauth' => new external_value(PARAM_PLUGIN, 'Authentication method for user registration.'),
                'forgottenpasswordurl' => new external_value(PARAM_URL, 'Forgotten password URL.'),
                'authinstructions' => new external_value(PARAM_RAW, 'Authentication instructions.'),
                'authnoneenabled' => new external_value(PARAM_INT, 'Whether auth none is enabled.'),
                'enablewebservices' => new external_value(PARAM_INT, 'Whether Web Services are enabled.'),
                'enablemobilewebservice' => new external_value(PARAM_INT, 'Whether the Mobile service is enabled.'),
                'maintenanceenabled' => new external_value(PARAM_INT, 'Whether site maintenance is enabled.'),
                'maintenancemessage' => new external_value(PARAM_RAW, 'Maintenance message.'),
                'warnings' => new external_warnings(),
            )
        );
    }

}
