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
 * This file contains the form add/update a data purpose.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\form;
defined('MOODLE_INTERNAL') || die();

use core\form\persistent;

/**
 * Data purpose form.
 *
 * @package   tool_dataprivacy
 * @copyright 2018 David Monllao
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purpose extends persistent {

    /**
     * @var string The persistent class.
     */
    protected static $persistentclass = 'tool_dataprivacy\\purpose';

    /**
     * @var array The list of current overrides.
     */
    protected $existingoverrides = [];

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="100"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'server');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'server');

        $mform->addElement('editor', 'description', get_string('description'), null, ['autosave' => false]);
        $mform->setType('description', PARAM_CLEANHTML);

        // Field for selecting lawful bases (from GDPR Article 6.1).
        $this->add_field($this->get_lawful_base_field());
        $mform->addRule('lawfulbases', get_string('required'), 'required', null, 'server');

        // Optional field for selecting reasons for collecting sensitive personal data (from GDPR Article 9.2).
        $this->add_field($this->get_sensitive_base_field());

        $this->add_field($this->get_retention_period_fields());
        $this->add_field($this->get_protected_field());

        $this->add_override_fields();

        if (!empty($this->_customdata['showbuttons'])) {
            if (!$this->get_persistent()->get('id')) {
                $savetext = get_string('add');
            } else {
                $savetext = get_string('savechanges');
            }
            $this->add_action_buttons(true, $savetext);
        }
    }

    /**
     * Add a fieldset to the current form.
     *
     * @param   \stdClass   $data
     */
    protected function add_field(\stdClass $data) {
        foreach ($data->fields as $field) {
            $this->_form->addElement($field);
        }

        if (!empty($data->helps)) {
            foreach ($data->helps as $fieldname => $helpdata) {
                $help = array_merge([$fieldname], $helpdata);
                call_user_func_array([$this->_form, 'addHelpButton'], $help);
            }
        }

        if (!empty($data->types)) {
            foreach ($data->types as $fieldname => $type) {
                $this->_form->setType($fieldname, $type);
            }
        }

        if (!empty($data->rules)) {
            foreach ($data->rules as $fieldname => $ruledata) {
                $rule = array_merge([$fieldname], $ruledata);
                call_user_func_array([$this->_form, 'addRule'], $rule);
            }
        }

        if (!empty($data->defaults)) {
            foreach ($data->defaults as $fieldname => $default) {
                $this->_form($fieldname, $default);
            }
        }
    }

    /**
     * Handle addition of relevant repeated element fields for role overrides.
     */
    protected function add_override_fields() {
        $purpose = $this->get_persistent();

        if (empty($purpose->get('id'))) {
            // It is not possible to use repeated elements in a modal form yet.
            return;
        }

        $fields = [
            $this->get_role_override_id('roleoverride_'),
            $this->get_role_field('roleoverride_'),
            $this->get_retention_period_fields('roleoverride_'),
            $this->get_protected_field('roleoverride_'),
            $this->get_lawful_base_field('roleoverride_'),
            $this->get_sensitive_base_field('roleoverride_'),
        ];

        $options = [
            'type' => [],
            'helpbutton' => [],
        ];

        // Start by adding the title.
        $overrideelements = [
            $this->_form->createElement('header', 'roleoverride', get_string('roleoverride', 'tool_dataprivacy')),
            $this->_form->createElement(
                'static',
                'roleoverrideoverview',
                '',
                get_string('roleoverrideoverview', 'tool_dataprivacy')
            ),
        ];

        foreach ($fields as $fielddata) {
            foreach ($fielddata->fields as $field) {
                $overrideelements[] = $field;
            }

            if (!empty($fielddata->helps)) {
                foreach ($fielddata->helps as $name => $help) {
                    if (!isset($options[$name])) {
                        $options[$name] = [];
                    }
                    $options[$name]['helpbutton'] = $help;
                }
            }

            if (!empty($fielddata->types)) {
                foreach ($fielddata->types as $name => $type) {
                    if (!isset($options[$name])) {
                        $options[$name] = [];
                    }
                    $options[$name]['type'] = $type;
                }
            }

            if (!empty($fielddata->rules)) {
                foreach ($fielddata->rules as $name => $rule) {
                    if (!isset($options[$name])) {
                        $options[$name] = [];
                    }
                    $options[$name]['rule'] = $rule;
                }
            }

            if (!empty($fielddata->defaults)) {
                foreach ($fielddata->defaults as $name => $default) {
                    if (!isset($options[$name])) {
                        $options[$name] = [];
                    }
                    $options[$name]['default'] = $default;
                }
            }

            if (!empty($fielddata->advanceds)) {
                foreach ($fielddata->advanceds as $name => $advanced) {
                    if (!isset($options[$name])) {
                        $options[$name] = [];
                    }
                    $options[$name]['advanced'] = $advanced;
                }
            }
        }

        $this->existingoverrides = $purpose->get_purpose_overrides();
        $existingoverridecount = count($this->existingoverrides);

        $this->repeat_elements(
                $overrideelements,
                $existingoverridecount,
                $options,
                'overrides',
                'addoverride',
                1,
                get_string('addroleoverride', 'tool_dataprivacy')
            );
    }

    /**
     * Converts fields.
     *
     * @param \stdClass $data
     * @return \stdClass
     */
    public function filter_data_for_persistent($data) {
        $data = parent::filter_data_for_persistent($data);

        $classname = static::$persistentclass;
        $properties = $classname::properties_definition();

        $data = (object) array_filter((array) $data, function($value, $key) use ($properties) {
            return isset($properties[$key]);
        }, ARRAY_FILTER_USE_BOTH);

        return $data;
    }

    /**
     * Get the field for the role name.
     *
     * @param   string  $prefix The prefix to apply to the field
     * @return  \stdClass
     */
    protected function get_role_override_id(string $prefix = '') : \stdClass {
        $fieldname = "{$prefix}id";

        $fielddata = (object) [
            'fields' => [],
        ];

        $fielddata->fields[] = $this->_form->createElement('hidden', $fieldname);
        $fielddata->types[$fieldname] = PARAM_INT;

        return $fielddata;
    }

    /**
     * Get the field for the role name.
     *
     * @param   string  $prefix The prefix to apply to the field
     * @return  \stdClass
     */
    protected function get_role_field(string $prefix = '') : \stdClass {
        $fieldname = "{$prefix}roleid";

        $fielddata = (object) [
            'fields' => [],
            'helps' => [],
        ];

        $roles = [
            '' => get_string('none'),
        ];
        foreach (role_get_names() as $roleid => $role) {
            $roles[$roleid] = $role->localname;
        }

        $fielddata->fields[] = $this->_form->createElement('select', $fieldname, get_string('role'),
            $roles,
            [
                'multiple' => false,
            ]
        );
        $fielddata->helps[$fieldname] = ['role', 'tool_dataprivacy'];
        $fielddata->defaults[$fieldname] = null;

        return $fielddata;
    }

    /**
     * Get the mform field for lawful bases.
     *
     * @param   string  $prefix The prefix to apply to the field
     * @return  \stdClass
     */
    protected function get_lawful_base_field(string $prefix = '') : \stdClass {
        $fieldname = "{$prefix}lawfulbases";

        $data = (object) [
            'fields' => [],
        ];

        $bases = [];
        foreach (\tool_dataprivacy\purpose::GDPR_ART_6_1_ITEMS as $article) {
            $key = 'gdpr_art_6_1_' . $article;
            $bases[$key] = get_string("{$key}_name", 'tool_dataprivacy');
        }

        $data->fields[] = $this->_form->createElement('autocomplete', $fieldname, get_string('lawfulbases', 'tool_dataprivacy'),
            $bases,
            [
                'multiple' => true,
            ]
        );

        $data->helps = [
            $fieldname => ['lawfulbases', 'tool_dataprivacy'],
        ];

        $data->advanceds = [
            $fieldname => true,
        ];

        return $data;
    }

    /**
     * Get the mform field for sensitive bases.
     *
     * @param   string  $prefix The prefix to apply to the field
     * @return  \stdClass
     */
    protected function get_sensitive_base_field(string $prefix = '') : \stdClass {
        $fieldname = "{$prefix}sensitivedatareasons";

        $data = (object) [
            'fields' => [],
        ];

        $bases = [];
        foreach (\tool_dataprivacy\purpose::GDPR_ART_9_2_ITEMS as $article) {
            $key = 'gdpr_art_9_2_' . $article;
            $bases[$key] = get_string("{$key}_name", 'tool_dataprivacy');
        }

        $data->fields[] = $this->_form->createElement(
            'autocomplete',
            $fieldname,
            get_string('sensitivedatareasons', 'tool_dataprivacy'),
            $bases,
            [
                'multiple' => true,
            ]
        );
        $data->helps = [
            $fieldname => ['sensitivedatareasons', 'tool_dataprivacy'],
        ];

        $data->advanceds = [
            $fieldname => true,
        ];

        return $data;
    }

    /**
     * Get the retention period fields.
     *
     * @param   string  $prefix The name of the main field, and prefix for the subfields.
     * @return  \stdClass
     */
    protected function get_retention_period_fields(string $prefix = '') : \stdClass {
        $prefix = "{$prefix}retentionperiod";
        $data = (object) [
            'fields' => [],
            'types' => [],
        ];

        $number = $this->_form->createElement('text', "{$prefix}number", null, ['size' => 8]);
        $data->types["{$prefix}number"] = PARAM_INT;

        $unitoptions = [
            'Y' => get_string('years'),
            'M' => strtolower(get_string('months')),
            'D' => strtolower(get_string('days'))
        ];
        $unit = $this->_form->createElement('select', "{$prefix}unit", '', $unitoptions);

        $data->fields[] = $this->_form->createElement(
                'group',
                $prefix,
                get_string('retentionperiod', 'tool_dataprivacy'),
                [
                    'number' => $number,
                    'unit' => $unit,
                ],
                null,
                false
            );

        return $data;
    }

    /**
     * Get the mform field for the protected flag.
     *
     * @param   string  $prefix The prefix to apply to the field
     * @return  \stdClass
     */
    protected function get_protected_field(string $prefix = '') : \stdClass {
        $fieldname = "{$prefix}protected";

        return (object) [
            'fields' => [
                $this->_form->createElement(
                        'advcheckbox',
                        $fieldname,
                        get_string('protected', 'tool_dataprivacy'),
                        get_string('protectedlabel', 'tool_dataprivacy')
                    ),
            ],
        ];
    }

    /**
     * Converts data to data suitable for storage.
     *
     * @param \stdClass $data
     * @return \stdClass
     */
    protected static function convert_fields(\stdClass $data) {
        $data = parent::convert_fields($data);

        if (!empty($data->lawfulbases) && is_array($data->lawfulbases)) {
            $data->lawfulbases = implode(',', $data->lawfulbases);
        }
        if (!empty($data->sensitivedatareasons) && is_array($data->sensitivedatareasons)) {
            $data->sensitivedatareasons = implode(',', $data->sensitivedatareasons);
        } else {
            // Nothing selected. Set default value of null.
            $data->sensitivedatareasons = null;
        }

        // A single value.
        $data->retentionperiod = 'P' . $data->retentionperiodnumber . $data->retentionperiodunit;
        unset($data->retentionperiodnumber);
        unset($data->retentionperiodunit);

        return $data;
    }

    /**
     * Get the default data.
     *
     * @return \stdClass
     */
    protected function get_default_data() {
        $data = parent::get_default_data();

        return $this->convert_existing_data_to_values($data);
    }

    /**
     * Normalise any values stored in existing data.
     *
     * @param   \stdClass $data
     * @return  \stdClass
     */
    protected function convert_existing_data_to_values(\stdClass $data) : \stdClass {
        $data->lawfulbases = explode(',', $data->lawfulbases);
        if (!empty($data->sensitivedatareasons)) {
            $data->sensitivedatareasons = explode(',', $data->sensitivedatareasons);
        }

        // Convert the single properties into number and unit.
        $strlen = strlen($data->retentionperiod);
        $data->retentionperiodnumber = substr($data->retentionperiod, 1, $strlen - 2);
        $data->retentionperiodunit = substr($data->retentionperiod, $strlen - 1);
        unset($data->retentionperiod);

        return $data;
    }

    /**
     * Fetch the role override data from the list of submitted data.
     *
     * @param   \stdClass   $data The complete set of processed data
     * @return  \stdClass[] The list of overrides
     */
    public function get_role_overrides_from_data(\stdClass $data) {
        $overrides = [];
        if (!empty($data->overrides)) {
            $searchkey = 'roleoverride_';

            for ($i = 0; $i < $data->overrides; $i++) {
                $overridedata = (object) [];
                foreach ((array) $data as $fieldname => $value) {
                    if (strpos($fieldname, $searchkey) !== 0) {
                        continue;
                    }

                    $overridefieldname = substr($fieldname, strlen($searchkey));
                    $overridedata->$overridefieldname = $value[$i];
                }

                if (empty($overridedata->roleid) || empty($overridedata->retentionperiodnumber)) {
                    // Skip this one.
                    // There is no value and it will be delete.
                    continue;
                }

                $override = static::convert_fields($overridedata);

                $overrides[$i] = $override;
            }
        }

        return $overrides;
    }

    /**
     * Define extra validation mechanims.
     *
     * @param  stdClass $data Data to validate.
     * @param  array $files Array of files.
     * @param  array $errors Currently reported errors.
     * @return array of additional errors, or overridden errors.
     */
    protected function extra_validation($data, $files, array &$errors) {
        $overrides = $this->get_role_overrides_from_data($data);

        // Check role overrides to ensure that:
        // - roles are unique; and
        // - specifeid retention periods are numeric.
        $seenroleids = [];
        foreach ($overrides as $id => $override) {
            $override->purposeid = 0;
            $persistent = new \tool_dataprivacy\purpose_override($override->id, $override);

            if (isset($seenroleids[$persistent->get('roleid')])) {
                $errors["roleoverride_roleid[{$id}]"] = get_string('duplicaterole');
            }
            $seenroleids[$persistent->get('roleid')] = true;

            $errors = array_merge($errors, $persistent->get_errors());
        }

        return $errors;
    }

    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param stdClass $data
     */
    public function set_data($data) {
        $purpose = $this->get_persistent();

        $count = 0;
        foreach ($this->existingoverrides as $override) {
            $overridedata = $this->convert_existing_data_to_values($override->to_record());
            foreach ($overridedata as $key => $value) {
                $keyname = "roleoverride_{$key}[{$count}]";
                $data->$keyname = $value;
            }
            $count++;
        }

        parent::set_data($data);
    }
}
