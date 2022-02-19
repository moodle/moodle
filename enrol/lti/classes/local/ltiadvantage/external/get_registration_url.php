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

namespace enrol_lti\local\ltiadvantage\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use enrol_lti\local\ltiadvantage\service\application_registration_service;
use external_api;
use external_warnings;
use external_single_structure;
use external_function_parameters;
use external_value;

/**
 * This is the external method for getting/creating a registration URL for use with LTI Advantage Dynamic Registration.
 *
 * @package enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_registration_url extends external_api {

    /**
     * Parameter description for the service.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'createifmissing' => new external_value(PARAM_BOOL,
                'Whether to create a registration URL if one is not found', VALUE_DEFAULT, false)
        ]);
    }

    /**
     * Get the registration URL, creating one on the fly if specified in the params.
     *
     * @param bool $createifmissing whether to create a new registration URL automatically or not.
     * @return array the URL string (empty if the URL could not be created) and warnings array.
     * @throws \moodle_exception if the user doesn't have permissions.
     */
    public static function execute(bool $createifmissing = false): array {

        $params = self::validate_parameters(self::execute_parameters(), ['createifmissing' => $createifmissing]);

        $context = \context_system::instance();
        self::validate_context($context);
        if (!has_capability('moodle/site:config', $context)) {
            throw new \moodle_exception('nopermissions', 'error', '', 'get registration url');
        }

        $appregservice = new application_registration_service(
            new application_registration_repository(),
            new deployment_repository(),
            new resource_link_repository(),
            new context_repository(),
            new user_repository()
        );

        if (!$regurl = $appregservice->get_registration_url()) {
            if ($params['createifmissing']) {
                $regurl = $appregservice->create_registration_url();
            }
        }

        return [
            'url' => isset($regurl) ? $regurl->out(false) : '',
            'expirystring' => isset($regurl) ? get_string('registrationurlexpiry', 'enrol_lti',
                date('H:i, M dS, Y', $regurl->get_expiry_time())) : '',
            'warnings' => [],
        ];
    }

    /**
     * Return description for the service
     *
     * @return external_single_structure the return structure.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'url' => new external_value(PARAM_URL, 'The registration URL'),
            'expirystring' => new external_value(PARAM_TEXT, 'String describing the expiry time period of the URL'),
            'warnings' => new external_warnings()
        ]);
    }

}
