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
 * Helper class for providing the necessary extension functions to implement the Google OAuth authentication for access to
 * Google's Vertex AI.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aitool_option_vertexai {

    /**
     * Extends the form definition of the edit instance form by adding the Vertex AI options.
     *
     * @param \MoodleQuickForm $mform the mform object
     */
    public static function extend_form_definition(\MoodleQuickForm $mform): void {
        global $OUTPUT;
        $mform->freeze('endpoint');
        $mform->addElement('textarea', 'serviceaccountjson',
                get_string('serviceaccountjson', 'local_ai_manager'), ['rows' => '20']);
        $vertexcachestatushtml = $OUTPUT->render_from_template('local_ai_manager/vertexcachestatus', ['noStatus' => true]);
        $mform->addElement('static', 'vertexcachestatus',
                get_string('vertexcachestatus', 'local_ai_manager'),
                $vertexcachestatushtml, ['class' => 'mw-100']);
    }

    /**
     * Adds the Vertex AI data to the form data to be passed to the form when loading.
     *
     * @param string $serviceaccountjson the service account JSON string
     * @return stdClass the object to pass to the form when loading
     */
    public static function add_vertexai_to_form_data(string $serviceaccountjson): stdClass {
        $data = new stdClass();
        $data->serviceaccountjson = $serviceaccountjson;
        return $data;
    }

    /**
     * Extract the service account JSON and calculate the new base endpoint from the form data submitted by the form.
     *
     * @param stdClass $data the form data after submission
     * @return array array of the service account JSON and the calculated endpoint
     */
    public static function extract_vertexai_to_store(stdClass $data): array {
        $serviceaccountjson = trim($data->serviceaccountjson);
        $serviceaccountinfo = json_decode($serviceaccountjson);
        $projectid = $serviceaccountinfo->project_id;

        $baseendpoint = 'https://europe-west3-aiplatform.googleapis.com/v1/projects/' . $projectid
                . '/locations/europe-west3/publishers/google/models/'
                . $data->model;
        return [$serviceaccountjson, $baseendpoint];
    }

    /**
     * Validation function for the Vertex AI option when form is being submitted.
     *
     * @param array $data the data being submitted by the form
     * @return array associative array ['mformelementname' => 'error string'] if there are validation errors, otherwise empty array
     */
    public static function validate_vertexai(array $data): array {
        $errors = [];
        if (empty($data['serviceaccountjson'])) {
            $errors['serviceaccountjson'] = get_string('error_vertexai_serviceaccountjsonempty', 'local_ai_manager');
            return $errors;
        }

        $serviceaccountinfo = json_decode(trim($data['serviceaccountjson']));
        if (is_null($serviceaccountinfo)) {
            $errors['serviceaccountjson'] = get_string('error_vertexai_serviceaccountjsoninvalid', 'local_ai_manager');
        } else {
            foreach (['private_key_id', 'private_key', 'client_email'] as $field) {
                if (!property_exists($serviceaccountinfo, $field)) {
                    $errors['serviceaccountjson'] =
                            get_string('error_vertexai_serviceaccountjsoninvalidmissing', 'local_ai_manager', $field);
                    break;
                }
            }
        }

        return $errors;
    }
}
