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
 * Return data about an entity generator.
 *
 * @package   tool_behat
 * @copyright 2022 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_behat\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External function for getting properties of entity generators.
 */
class get_entity_generator extends external_api {

    /**
     * Define parameters for external function.
     *
     * The parameter is either in the format 'entity' or 'component_name > entity'. There is no appropriate param type for a
     * string like this containing angle brackets, so we will do PARAM_RAW. The value will be parsed by
     * behat_data_generators::parse_entity_type, which validates the format of the parameter and throws an exception if it is not
     * correct.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'entitytype' => new external_value(PARAM_RAW, 'Entity type that can be created by a generator.'),
        ]);
    }

    /**
     * Return a list of the required fields for a given entity type.
     *
     * @param string $entitytype
     * @return array
     */
    public static function execute(string $entitytype): array {
        global $CFG;

        // Ensure we can load Behat and Facebook namespaces in behat libraries.
        require_once("{$CFG->dirroot}/../vendor/autoload.php");
        require_once("{$CFG->libdir}/tests/behat/behat_data_generators.php");

        $params = self::validate_parameters(self::execute_parameters(), ['entitytype' => $entitytype]);
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $generators = new \behat_data_generators();
        $entity = $generators->get_entity($params['entitytype']);
        return ['required' => $entity['required']];
    }

    /**
     * Define return values.
     *
     * Return required fields
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'required' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'Required field'),
                'Required fields',
                VALUE_OPTIONAL
            ),
        ]);
    }
}
