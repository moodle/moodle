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

namespace local_ai_manager\local;

use stdClass;

/**
 * Helper class for providing the necessary extension functions to implement the temperature parameter into an ai tool.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aitool_option_azure {

    /**
     * Extends the form definition of the edit instance form by adding azure options.
     *
     * @param \MoodleQuickForm $mform the mform object
     */
    public static function extend_form_definition(\MoodleQuickForm $mform): void {
        $mform->addElement('selectyesno', 'azure_enabled', get_string('use_openai_by_azure_heading', 'local_ai_manager'));
        $mform->setDefault('azure_enabled', false);

        $mform->addElement('text', 'azure_resourcename', get_string('use_openai_by_azure_name', 'local_ai_manager'));
        $mform->setType('azure_resourcename', PARAM_TEXT);
        $mform->hideIf('azure_resourcename', 'azure_enabled', 'eq', '0');

        $mform->addElement('text', 'azure_deploymentid', get_string('use_openai_by_azure_deploymentid', 'local_ai_manager'));
        $mform->setType('azure_deploymentid', PARAM_TEXT);
        $mform->hideIf('azure_deploymentid', 'azure_enabled', 'eq', '0');

        $mform->addElement('text', 'azure_apiversion', get_string('use_openai_by_azure_apiversion', 'local_ai_manager'));
        $mform->setType('azure_apiversion', PARAM_TEXT);
        $mform->hideIf('azure_apiversion', 'azure_enabled', 'eq', '0');

        // We leave the endpoint empty on creation, because it depends if azure is being used or not.
        $mform->setDefault('endpoint', '');
        $mform->freeze('endpoint');

        $mform->hideIf('model', 'azure_enabled', 'eq', 1);
    }

    /**
     * Helper function to convert the given azure data to an object which then can be passed to the form when loading.
     *
     * @param bool $enabled if azure is enabled for this instance
     * @param ?string $resourcename the azure resource name
     * @param ?string $deploymentid the azure deployment id
     * @param ?string $apiversion the api version of the azure resource
     * @return stdClass the stdClass which then can be passed to the form for loading
     */
    public static function add_azure_options_to_form_data(bool $enabled, ?string $resourcename, ?string $deploymentid,
            ?string $apiversion): stdClass {
        $data = new stdClass();
        $data->azure_enabled = $enabled;
        if ($enabled) {
            $data->azure_resourcename = $resourcename;
            $data->azure_deploymentid = $deploymentid;
            $data->azure_apiversion = $apiversion;
        }
        return $data;
    }

    /**
     * Helper function to extract the azure data from the data being submitted by the form.
     *
     * @param stdClass $data the data being submitted by the form
     * @return array array with the extracted azure information
     */
    public static function extract_azure_data_to_store(stdClass $data): array {
        $resourcename = empty($data->azure_resourcename) ? null : trim($data->azure_resourcename);
        $deploymentid = empty($data->azure_deploymentid) ? null : trim($data->azure_deploymentid);
        $apiversion = empty($data->azure_apiversion) ? null : trim($data->azure_apiversion);
        return [$data->azure_enabled, $resourcename, $deploymentid, $apiversion];
    }

    /**
     * Validation function for the azure options in the mform.
     *
     * @param array $data the data being submitted by the form
     * @return array associative array ['mformelementname' => 'error string'] if there are validation errors, otherwise empty array
     */
    public static function validate_azure_options(array $data): array {
        $errors = [];
        if (!empty($data['azure_enabled'])) {
            if (empty($data['azure_resourcename'])) {
                $errors['azure_resourcename'] = get_string('formvalidation_editinstance_azureresourcename', 'local_ai_manager');
            }
            if (empty($data['azure_deploymentid'])) {
                $errors['azure_deploymentid'] = get_string('formvalidation_editinstance_azuredeploymentid', 'local_ai_manager');
            }
            if (empty($data['azure_apiversion'])) {
                $errors['azure_apiversion'] = get_string('formvalidation_editinstance_azureapiversion', 'local_ai_manager');
            }
        }
        return $errors;
    }

    /**
     * Define the model name in case we are using azure.
     *
     * When using azure we cannot select a model, because it is preconfigured in the azure resource.
     * This function defines the string to use as model for logging etc.
     *
     * @param ?string $connectorname The name of the connector, will be included into the model name
     * @return string the string defining the name of the model
     * @throws \coding_exception if the $connectorname is null or empty
     */
    public static function get_azure_model_name(?string $connectorname): string {
        if (empty($connectorname)) {
            throw new \coding_exception('Azure model name cannot be empty or null');
        }
        return $connectorname . '_preconfigured_azure';
    }
}
