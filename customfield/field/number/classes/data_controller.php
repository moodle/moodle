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

declare(strict_types=1);

namespace customfield_number;

use MoodleQuickForm;

/**
 * Data controller class
 *
 * @package    customfield_number
 * @copyright  2024 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Return the name of the field where the information is stored
     *
     * @return string
     */
    public function datafield(): string {
        return 'decvalue';
    }

    /**
     * Add form elements for editing the custom field instance
     *
     * @param MoodleQuickForm $mform
     */
    public function instance_form_definition(MoodleQuickForm $mform): void {
        $elementname = $this->get_form_element_name();

        $mform->addElement('float', $elementname, $this->get_field()->get_formatted_name());
        if (!$this->get('id')) {
            $mform->setDefault($elementname, $this->get_default_value());
        }
    }

    /**
     * Validate the data on the field instance form
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function instance_form_validation(array $data, array $files): array {
        $errors = parent::instance_form_validation($data, $files);

        $elementname = $this->get_form_element_name();
        $elementvalue = $data[$elementname];

        $minimumvalue = $this->get_field()->get_configdata_property('minimumvalue') ?? '';
        $maximumvalue = $this->get_field()->get_configdata_property('maximumvalue') ?? '';

        // Early exit if element value isn't specified, or neither maximum/minimum are specified.
        if ($elementvalue === '' || ($minimumvalue === '' && $maximumvalue === '')) {
            return $errors;
        }

        $elementvaluefloat = (float) $elementvalue;
        $minimumvaluefloat = (float) $minimumvalue;
        $maximumvaluefloat = (float) $maximumvalue;

        $decimalplaces = (int) $this->get_field()->get_configdata_property('decimalplaces');

        // Value must be greater than minimum. If maximum is set, value must not exceed it.
        if ($minimumvalue !== '' && $elementvaluefloat < $minimumvaluefloat) {
            $errors[$elementname] = get_string('minimumvalueerror', 'customfield_number',
                format_float($minimumvaluefloat, $decimalplaces));
        } else if ($maximumvalue !== '' && $elementvaluefloat > $maximumvaluefloat) {
            $errors[$elementname] = get_string('maximumvalueerror', 'customfield_number',
                format_float($maximumvaluefloat, $decimalplaces));
        }

        return $errors;
    }

    /**
     * Checks if the value is empty
     *
     * @param mixed $value
     * @return bool
     */
    protected function is_empty($value): bool {
        return (string) $value === '';
    }

    /**
     * Returns the default value in non human-readable format
     *
     * @return float|null
     */
    public function get_default_value(): ?float {
        $defaultvalue = $this->get_field()->get_configdata_property('defaultvalue');
        if ($this->is_empty($defaultvalue)) {
            return null;
        }
        return (float) $defaultvalue;
    }

    /**
     * Returns value in a human-readable format
     *
     * @return string|null
     */
    public function export_value(): ?string {
        $value = $this->get_value();
        if ($this->is_empty($value)) {
            return null;
        }

        $decimalplaces = (int) $this->get_field()->get_configdata_property('decimalplaces');
        return format_float((float) $value, $decimalplaces);
    }
}
