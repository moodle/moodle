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
 * Class containing the external API functions functions for the Policy tool.
 *
 * @package    tool_iomadpolicy
 * @copyright  2018 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context_system;
use context_user;
use core\invalid_persistent_exception;
use dml_exception;
use external_api;
use external_description;
use external_function_parameters;
use external_single_structure;
use external_value;
use external_warnings;
use invalid_parameter_exception;
use moodle_exception;
use restricted_context_exception;
use tool_iomadpolicy\api;
use tool_iomadpolicy\form\accept_iomadpolicy;

/**
 * Class external.
 *
 * The external API for the Policy tool.
 *
 * @copyright   2018 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Parameter description for get_iomadpolicy_version_parameters().
     *
     * @return external_function_parameters
     */
    public static function get_iomadpolicy_version_parameters() {
        return new external_function_parameters([
            'versionid' => new external_value(PARAM_INT, 'The iomadpolicy version ID', VALUE_REQUIRED),
            'behalfid' => new external_value(PARAM_INT, 'The id of user on whose behalf the user is viewing the iomadpolicy',
                VALUE_DEFAULT, 0)
        ]);
    }

    /**
     * Fetch the details of a iomadpolicy version.
     *
     * @param int $versionid The iomadpolicy version ID.
     * @param int $behalfid The id of user on whose behalf the user is viewing the iomadpolicy.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     * @throws moodle_exception
     */
    public static function get_iomadpolicy_version($versionid, $behalfid = null) {
        global $PAGE;

        $result = [];
        $warnings = [];
        $params = external_api::validate_parameters(self::get_iomadpolicy_version_parameters(), [
            'versionid' => $versionid,
            'behalfid' => $behalfid
        ]);
        $versionid = $params['versionid'];
        $behalfid = $params['behalfid'];

        $context = context_system::instance();
        $PAGE->set_context($context);

        try {
            // Validate if the user has access to the iomadpolicy version.
            $version = api::get_iomadpolicy_version($versionid);
            if (!api::can_user_view_iomadpolicy_version($version, $behalfid)) {
                $warnings[] = [
                    'item' => $versionid,
                    'warningcode' => 'errorusercantviewiomadpolicyversion',
                    'message' => get_string('errorusercantviewiomadpolicyversion', 'tool_iomadpolicy')
                ];
            } else if (!empty($version)) {
                $version = api::get_iomadpolicy_version($versionid);
                $iomadpolicy['name'] = $version->name;
                $iomadpolicy['versionid'] = $versionid;
                list($iomadpolicy['content'], $notusedformat) = external_format_text(
                    $version->content,
                    $version->contentformat,
                    SYSCONTEXTID,
                    'tool_iomadpolicy',
                    'iomadpolicydocumentcontent',
                    $version->id
                );
                $result['iomadpolicy'] = $iomadpolicy;
            }
        } catch (moodle_exception $e) {
            $warnings[] = [
                'item' => $versionid,
                'warningcode' => 'erroriomadpolicyversionnotfound',
                'message' => get_string('erroriomadpolicyversionnotfound', 'tool_iomadpolicy')
            ];
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for get_iomadpolicy_version().
     *
     * @return external_description
     */
    public static function get_iomadpolicy_version_returns() {
        return new external_single_structure([
            'result' => new external_single_structure([
                            'iomadpolicy' => new external_single_structure([
                                    'name' => new external_value(PARAM_RAW, 'The iomadpolicy version name', VALUE_OPTIONAL),
                                    'versionid' => new external_value(PARAM_INT, 'The iomadpolicy version id', VALUE_OPTIONAL),
                                    'content' => new external_value(PARAM_RAW, 'The iomadpolicy version content', VALUE_OPTIONAL)
                                    ], 'Policy information', VALUE_OPTIONAL)
                            ]),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Describes the parameters for submit_create_group_form webservice.
     * @return external_function_parameters
     */
    public static function submit_accept_on_behalf_parameters() {
        return new external_function_parameters(
            array(
                'jsonformdata' => new external_value(PARAM_RAW, 'The data from the create group form, encoded as a json array')
            )
        );
    }

    /**
     * Submit the create group form.
     *
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return int new group id.
     */
    public static function submit_accept_on_behalf($jsonformdata) {
        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::submit_accept_on_behalf_parameters(),
            ['jsonformdata' => $jsonformdata]);

        self::validate_context(context_system::instance());

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = array();
        parse_str($serialiseddata, $data);

        // The last param is the ajax submitted data.
        $mform = new accept_iomadpolicy(null, $data, 'post', '', null, true, $data);

        // Do the action.
        $mform->process();

        return true;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function submit_accept_on_behalf_returns() {
        return new external_value(PARAM_BOOL, 'success');
    }
}
