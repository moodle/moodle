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
 * Customfields textarea plugin
 *
 * @package   customfield_textarea
 * @copyright 2018 Daniel Neis Araujo <daniel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_textarea;

use backup_nested_element;

defined('MOODLE_INTERNAL') || die;

/**
 * Class data
 *
 * @package customfield_textarea
 * @copyright 2018 Daniel Neis Araujo <daniel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Return the name of the field where the information is stored
     * @return string
     */
    public function datafield(): string {
        return 'value';
    }

    /**
     * Options for the editor
     *
     * @return array
     */
    protected function value_editor_options() {
        /** @var field_controller $field */
        $field = $this->get_field();
        return $field->value_editor_options($this->get('id') ? $this->get_context() : null);
    }

    /**
     * Returns the name of the field to be used on HTML forms.
     *
     * @return string
     */
    public function get_form_element_name(): string {
        return parent::get_form_element_name() . '_editor';
    }

    /**
     * Add fields for editing a textarea field.
     *
     * @param \MoodleQuickForm $mform
     */
    public function instance_form_definition(\MoodleQuickForm $mform) {
        $field = $this->get_field();
        $desceditoroptions = $this->value_editor_options();
        $elementname = $this->get_form_element_name();
        $mform->addElement('editor', $elementname, $this->get_field()->get_formatted_name(), null, $desceditoroptions);
        if ($field->get_configdata_property('required')) {
            $mform->addRule($elementname, null, 'required', null, 'client');
        }
    }

    /**
     * Saves the data coming from form
     *
     * @param \stdClass $datanew data coming from the form
     */
    public function instance_form_save(\stdClass $datanew) {
        $fieldname = $this->get_form_element_name();
        if (!property_exists($datanew, $fieldname)) {
            return;
        }

        // Normalise form data, for cases it's come from an external source.
        $fromform = $datanew->$fieldname;
        if (!is_array($fromform)) {
            $fromform = ['text' => $fromform];
            $fromform['format'] = $this->get('id') ? $this->get('valueformat') :
                $this->get_field()->get_configdata_property('defaultvalueformat');
        }

        if (!$this->get('id')) {
            $this->data->set('value', '');
            $this->data->set('valueformat', FORMAT_MOODLE);
            $this->data->set('valuetrust', false);
            $this->save();
        }

        if (array_key_exists('text', $fromform)) {
            $textoptions = $this->value_editor_options();
            $context = $textoptions['context'];

            $data = (object) ['field_editor' => $fromform];
            $data = file_postupdate_standard_editor($data, 'field', $textoptions, $context,
                'customfield_textarea', 'value', $this->get('id'));

            $this->data->set('value', $data->field);
            $this->data->set('valueformat', $data->fieldformat);
            $this->data->set('valuetrust', trusttext_trusted($context));
            $this->save();
        }
    }

    /**
     * Prepares the custom field data related to the object to pass to mform->set_data() and adds them to it
     *
     * This function must be called before calling $form->set_data($object);
     *
     * @param \stdClass $instance the entity that has custom fields, if 'id' attribute is present the custom
     *    fields for this entity will be added, otherwise the default values will be added.
     */
    public function instance_form_before_set_data(\stdClass $instance) {
        $textoptions = $this->value_editor_options();
        $context = $textoptions['context'];
        if ($this->get('id')) {
            $text = $this->get('value');
            $format = $this->get('valueformat');
            $temp = (object) ['field' => $text, 'fieldformat' => $format, 'fieldtrust' => trusttext_trusted($context)];
            file_prepare_standard_editor($temp, 'field', $textoptions, $context, 'customfield_textarea',
                'value', $this->get('id'));
            $value = $temp->field_editor;
        } else {
            $text = $this->get_field()->get_configdata_property('defaultvalue');
            $format = $this->get_field()->get_configdata_property('defaultvalueformat');
            $temp = (object) ['field' => $text, 'fieldformat' => $format, 'fieldtrust' => trusttext_trusted($context)];
            file_prepare_standard_editor($temp, 'field', $textoptions, $context, 'customfield_textarea',
                'defaultvalue', $this->get_field()->get('id'));
            $value = $temp->field_editor;
        }
        $instance->{$this->get_form_element_name()} = $value;
    }

    /**
     * Checks if the value is empty, overriding the base method to ensure it's the "text" element of our value being compared
     *
     * @param string|string[] $value
     * @return bool
     */
    protected function is_empty($value): bool {
        if (is_array($value)) {
            $value = $value['text'];
        }
        return html_is_blank($value);
    }

    /**
     * Checks if the value is unique, overriding the base method to ensure it's the "text" element of our value being compared
     *
     * @param mixed $value
     * @return bool
     */
    protected function is_unique($value): bool {
        return parent::is_unique($value['text']);
    }

    /**
     * Delete data
     *
     * @return bool
     */
    public function delete() {
        get_file_storage()->delete_area_files($this->get('contextid'), 'customfield_textarea',
            'value', $this->get('id'));
        return parent::delete();
    }

    /**
     * Returns the default value as it would be stored in the database (not in human-readable format).
     *
     * @return mixed
     */
    public function get_default_value() {
        return $this->get_field()->get_configdata_property('defaultvalue');
    }

    /**
     * Implement the backup callback for the custom field element.
     * This includes any embedded files in the custom field element.
     *
     * @param \backup_nested_element $customfieldelement The custom field element to be backed up.
     */
    public function backup_define_structure(backup_nested_element $customfieldelement): void {
        $annotations = $customfieldelement->get_file_annotations();

        if (!isset($annotations['customfield_textarea']['value'])) {
            $customfieldelement->annotate_files('customfield_textarea', 'value', 'id');
        }
    }

    /**
     * Implement the restore callback for the custom field element.
     * This includes restoring any embedded files in the custom field element.
     *
     * @param \restore_structure_step $step The restore step instance.
     * @param int $newid The new ID for the custom field data after restore.
     * @param int $oldid The original ID of the custom field data before backup.
     */
    public function restore_define_structure(\restore_structure_step $step, int $newid, int $oldid): void {
        if (!$step->get_mappingid('customfield_data', $oldid)) {
            $step->set_mapping('customfield_data', $oldid, $newid, true);
            $step->add_related_files('customfield_textarea', 'value', 'customfield_data');
        }
    }

    /**
     * Returns value in a human-readable format
     *
     * @return mixed|null value or null if empty
     */
    public function export_value() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $value = $this->get_value();
        if ($this->is_empty($value)) {
            return null;
        }

        if ($dataid = $this->get('id')) {
            $context = $this->get_context();
            $processed = file_rewrite_pluginfile_urls($value, 'pluginfile.php',
                $context->id, 'customfield_textarea', 'value', $dataid);
            $value = format_text($processed, $this->get('valueformat'), [
                'context' => $context,
                'trusted' => $this->get('valuetrust'),
            ]);
        } else {
            $field = $this->get_field();

            $context = $field->get_handler()->get_configuration_context();
            $processed = file_rewrite_pluginfile_urls($value, 'pluginfile.php',
                $context->id, 'customfield_textarea', 'defaultvalue', $field->get('id'));
            $value = format_text($processed, $field->get_configdata_property('defaultvalueformat'), [
                'context' => $context,
                'trusted' => true,
            ]);
        }

        return $value;
    }
}
