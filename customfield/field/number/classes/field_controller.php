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
 * Field controller class
 *
 * @package    customfield_number
 * @copyright  2024 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_controller  extends \core_customfield\field_controller {

    /**
     * Add form elements for editing the custom field definition
     *
     * @param MoodleQuickForm $mform
     */
    public function config_form_definition(MoodleQuickForm $mform): void {
        $mform->addElement('header', 'specificsettings', get_string('specificsettings', 'customfield_number'));
        $mform->setExpanded('specificsettings');

        // Default value.
        $mform->addElement('float', 'configdata[defaultvalue]', get_string('defaultvalue', 'core_customfield'));
        if ($this->get_configdata_property('defaultvalue') === null) {
            $mform->setDefault('configdata[defaultvalue]', '');
        }

        // Minimum value.
        $mform->addElement('float', 'configdata[minimumvalue]', get_string('minimumvalue', 'customfield_number'));
        if ($this->get_configdata_property('minimumvalue') === null) {
            $mform->setDefault('configdata[minimumvalue]', '');
        }

        // Maximum value.
        $mform->addElement('float', 'configdata[maximumvalue]', get_string('maximumvalue', 'customfield_number'));
        if ($this->get_configdata_property('maximumvalue') === null) {
            $mform->setDefault('configdata[maximumvalue]', '');
        }

        // Decimal places.
        $mform->addElement('text', 'configdata[decimalplaces]', get_string('decimalplaces', 'customfield_number'));
        if ($this->get_configdata_property('decimalplaces') === null) {
            $mform->setDefault('configdata[decimalplaces]', 0);
        }
        $mform->setType('configdata[decimalplaces]', PARAM_INT);
    }

    /**
     * Validate the data on the field configuration form
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function config_form_validation(array $data, $files = []): array {
        $errors = parent::config_form_validation($data, $files);

        // Each of these configuration fields are optional.
        $defaultvalue = $data['configdata']['defaultvalue'] ?? '';
        $minimumvalue = $data['configdata']['minimumvalue'] ?? '';
        $maximumvalue = $data['configdata']['maximumvalue'] ?? '';

        // Early exit if neither maximum/minimum are specified.
        if ($minimumvalue === '' && $maximumvalue === '') {
            return $errors;
        }

        $minimumvaluefloat = (float) $minimumvalue;
        $maximumvaluefloat = (float) $maximumvalue;

        // If maximum is set, it must be greater than minimum.
        if ($maximumvalue !== '' && $minimumvaluefloat >= $maximumvaluefloat) {
            $errors['configdata[minimumvalue]'] = get_string('minimumvalueconfigerror', 'customfield_number');
        }

        // If default value is set, it must be in range of minimum and maximum.
        if ($defaultvalue !== '') {
            $defaultvaluefloat = (float) $defaultvalue;

            if ($defaultvaluefloat < $minimumvaluefloat || ($maximumvalue !== '' && $defaultvaluefloat > $maximumvaluefloat)) {
                $errors['configdata[defaultvalue]'] = get_string('defaultvalueconfigerror', 'customfield_number');
            }
        }

        return $errors;
    }
}
