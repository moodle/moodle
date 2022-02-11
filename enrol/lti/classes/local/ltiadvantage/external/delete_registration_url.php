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
use external_value;
use external_warnings;
use external_single_structure;
use external_function_parameters;

/**
 * This is the external method for deleting a registration URL for use with LTI Advantage Dynamic Registration.
 *
 * @package enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_registration_url extends external_api {

    /**
     * Service parameters definition.
     *
     * @return external_function_parameters the parameters.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Delete the registration URL.
     */
    public static function execute() {

        self::validate_parameters(self::execute_parameters(), []);

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
        $appregservice->delete_registration_url();

        return [
            'status' => true,
            'warnings' => []
        ];
    }

    /**
     * Service return values definition.
     *
     * @return external_single_structure the return value defintion.
     */
    public static function execute_returns() {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_BOOL, 'True if the URL was deleted, false otherwise.'),
                'warnings' => new external_warnings()
            ]
        );
    }
}
