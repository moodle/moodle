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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../lib.php');

/**
 * Data generator for core_webservice plugin.
 *
 * @package    core_webservice
 * @category   test
 * @copyright  2021 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_webservice_generator extends component_generator_base {
    /**
     * Create a new webservice service.
     *
     * @param   array $data
     * @return  stdClass
     */
    public function create_service(array $data): \stdClass {
        $webservicemanager = new webservice();

        $requiredfields = [
            'name',
            'shortname',
        ];

        foreach ($requiredfields as $fieldname) {
            if (!array_key_exists($fieldname, $data)) {
                throw new \coding_exception("Field '{$fieldname}' missing when creating new service");
            }
        }

        $optionalfields = [
            'enabled' => false,
            'requiredcapability' => '',
            'restrictedusers' => 0,
            'component' => '',
            'timemodified' => time(),
        ];

        foreach ($optionalfields as $fieldname => $value) {
            if (!array_key_exists($fieldname, $data)) {
                $data[$fieldname] = $value;
            }
        }

        $serviceid = $webservicemanager->add_external_service((object) $data);

        return $webservicemanager->get_external_service_by_id($serviceid);
    }

    /**
     * Associate a webservice function with service.
     *
     * @param   array $data
     */
    public function create_service_functions(array $data): void {
        $webservicemanager = new webservice();

        $requiredfields = [
            'service',
            'functions',
        ];

        foreach ($requiredfields as $fieldname) {
            if (!array_key_exists($fieldname, $data)) {
                throw new \coding_exception("Field '{$fieldname}' missing when creating new service");
            }
        }

        $service = $webservicemanager->get_external_service_by_shortname($data['service']);

        $functions = explode(',', $data['functions']);
        foreach ($functions as $functionname) {
            $functionname = trim($functionname);
            $webservicemanager->add_external_function_to_service($functionname, $service->id);
        }
    }

    /**
     * Create a new webservice token.
     *
     * @param   array $data
     */
    public function create_token(array $data): void {
        $webservicemanager = new webservice();

        $requiredfields = [
            'userid',
            'service',
        ];

        foreach ($requiredfields as $fieldname) {
            if (!array_key_exists($fieldname, $data)) {
                throw new \coding_exception("Field '{$fieldname}' missing when creating new service");
            }
        }

        $optionalfields = [
            'context' => context_system::instance(),
            'validuntil' => 0,
            'iprestriction' => '',
        ];

        foreach ($optionalfields as $fieldname => $value) {
            if (!array_key_exists($fieldname, $data)) {
                $data[$fieldname] = $value;
            }
        }

        $service = $webservicemanager->get_external_service_by_shortname($data['service']);

        \core_external\util::generate_token(
            EXTERNAL_TOKEN_PERMANENT,
            $service,
            $data['userid'],
            $data['context'],
            $data['validuntil'],
            $data['iprestriction']
        );
    }
}
