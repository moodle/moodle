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

use core\context\system;
use core\context;
use html_writer;
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

        $providers = provider_base::get_all_providers($this);
        if (count($providers) > 0) {
            $this->add_field_type_select($mform, $providers);
            // Add form config elements for each provider.
            foreach ($providers as $provider) {
                $provider->config_form_definition($mform);
            }
        }

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

        // Display format settings.
        // TODO: Change this after MDL-82996 fixed.
        $randelname = 'str_display_format';
        $mform->addGroup([], $randelname, html_writer::tag('h4', get_string('headerdisplaysettings', 'customfield_number')));

        // Display template.
        $mform->addElement('text', 'configdata[display]', get_string('display', 'customfield_number'),
            ['size' => 50]);
        $mform->setType('configdata[display]', PARAM_TEXT);
        $mform->addHelpButton('configdata[display]', 'display', 'customfield_number');
        if ($this->get_configdata_property('display') === null) {
            $mform->setDefault('configdata[display]', '{value}');
        }

        // Display when zero.
        $mform->addElement('text', 'configdata[displaywhenzero]', get_string('displaywhenzero', 'customfield_number'),
            ['size' => 50]);
        $mform->setType('configdata[displaywhenzero]', PARAM_TEXT);
        $mform->addHelpButton('configdata[displaywhenzero]', 'displaywhenzero', 'customfield_number');
        if ($this->get_configdata_property('displaywhenzero') === null) {
            $mform->setDefault('configdata[displaywhenzero]', 0);
        }
    }

    /**
     * Adds selector to provider for field population.
     *
     * @param MoodleQuickForm $mform
     * @param provider_base[] $providers
     */
    protected function add_field_type_select(MoodleQuickForm $mform, array $providers): void {
        $autooptions = [];
        foreach ($providers as $provider) {
            $autooptions[get_class($provider)] = $provider->get_name();
        }
        $options = ['' => get_string('genericfield', 'customfield_number')];
        $options = array_merge($options, $autooptions);
        $mform->addElement('select', 'configdata[fieldtype]', get_string('fieldtype', 'customfield_number'), $options);
        $mform->addHelpButton('configdata[fieldtype]', 'fieldtype', 'customfield_number');
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

        $display = $data['configdata']['display'];
        if (!preg_match('/\{value}/', $display)) {
            $errors['configdata[display]'] = get_string('displayvalueconfigerror', 'customfield_number');
        }

        // Each of these configuration fields are optional.
        $defaultvalue = $data['configdata']['defaultvalue'] ?? '';
        $minimumvalue = $data['configdata']['minimumvalue'] ?? '';
        $maximumvalue = $data['configdata']['maximumvalue'] ?? '';

        foreach (provider_base::get_all_providers($this) as $provider) {
            if (array_key_exists('fieldtype', $data["configdata"]) && $data["configdata"]["fieldtype"] == get_class($provider)) {
                $errors = array_merge($errors, $provider->config_form_validation($data, $files));
            }
        }
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

    /**
     * Prepares a value for export
     *
     * @param mixed $value
     * @param context|null $context
     * @return string|float|null
     */
    public function prepare_field_for_display(mixed $value, ?context $context = null): string|null|float {
        if ($provider = provider_base::instance($this)) {
            return $provider->prepare_export_value($value, $context);
        }

        if ($value === null) {
            return null;
        }

        $decimalplaces = (int) $this->get_configdata_property('decimalplaces');
        if (round((float) $value, $decimalplaces) == 0) {
            $value = $this->get_configdata_property('displaywhenzero');
            if ((string) $value === '') {
                return null;
            }
        } else {
            // Let's format the value.
            $value = format_float((float)$value, $decimalplaces);

            // Apply the display format.
            $format = $this->get_configdata_property('display') ?? '{value}';
            $value = str_replace('{value}', $value, $format);
        }

        return format_string($value, true, ['context' => $context ?? system::instance()]);
    }

    /**
     * Can the value of this field be manually editable in the edit forms
     *
     * @return bool
     */
    public function is_editable(): bool {
        return (string) $this->get_configdata_property('fieldtype') === '';
    }
}
