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
 * Customfield textarea plugin
 *
 * @package   customfield_textarea
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_textarea;

defined('MOODLE_INTERNAL') || die;

/**
 * Class field
 *
 * @package customfield_textarea
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_controller extends \core_customfield\field_controller {
    /**
     * Const type
     */
    const TYPE = 'textarea';

    /**
     * Before delete bulk actions
     */
    public function delete() : bool {
        global $DB;
        $fs = get_file_storage();

        // Delete files in the defaultvalue.
        $fs->delete_area_files($this->get_handler()->get_configuration_context()->id, 'customfield_textarea',
            'defaultvalue', $this->get('id'));

        // Delete files in the data. We can not use $fs->delete_area_files_select() because context may be different.
        $params = ['component' => 'customfield_textarea', 'filearea' => 'value', 'fieldid' => $this->get('id')];
        $where = "component = :component AND filearea = :filearea
                AND itemid IN (SELECT cfd.id FROM {customfield_data} cfd WHERE cfd.fieldid = :fieldid)";
        $filerecords = $DB->get_recordset_select('files', $where, $params);
        foreach ($filerecords as $filerecord) {
            $fs->get_file_instance($filerecord)->delete();
        }
        $filerecords->close();

        // Delete data and field.
        return parent::delete();
    }

    /**
     * Prepare the field data to set in the configuration form
     *
     * Necessary if some preprocessing required for editor or filemanager fields
     *
     * @param \stdClass $formdata
     */
    public function prepare_for_config_form(\stdClass $formdata) {

        if (!empty($formdata->configdata['defaultvalue'])) {
            $textoptions = $this->value_editor_options();
            $context = $textoptions['context'];

            $record = new \stdClass();
            $record->defaultvalue = $formdata->configdata['defaultvalue'];
            $record->defaultvalueformat = $formdata->configdata['defaultvalueformat'];
            file_prepare_standard_editor($record, 'defaultvalue', $textoptions, $context,
                'customfield_textarea', 'defaultvalue', $formdata->id);
            $formdata->configdata['defaultvalue_editor'] = $record->defaultvalue_editor;
        }
    }

    /**
     * Add fields for editing a textarea field.
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform) {
        $mform->addElement('header', 'header_specificsettings', get_string('specificsettings', 'customfield_textarea'));
        $mform->setExpanded('header_specificsettings', true);

        $desceditoroptions = $this->value_editor_options();

        $mform->addElement('editor', 'configdata[defaultvalue_editor]', get_string('defaultvalue', 'core_customfield'),
            null, $desceditoroptions);
    }

    /**
     * Options for editor
     *
     * @param \context|null $context context if known, otherwise configuration context will be used
     * @return array
     */
    public function value_editor_options(\context $context = null) {
        global $CFG;
        require_once($CFG->libdir.'/formslib.php');
        if (!$context) {
            $context = $this->get_handler()->get_configuration_context();
        }
        return ['maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $CFG->maxbytes, 'context' => $context];
    }

    /**
     * Saves the field configuration
     */
    public function save() {
        $configdata = $this->get('configdata');
        if (!array_key_exists('defaultvalue_editor', $configdata)) {
            $this->field->save();
            return;
        }

        if (!$this->get('id')) {
            $this->field->save();
        }

        // Store files.
        $textoptions = $this->value_editor_options();
        $tempvalue = (object) ['defaultvalue_editor' => $configdata['defaultvalue_editor']];
        $tempvalue = file_postupdate_standard_editor($tempvalue, 'defaultvalue', $textoptions, $textoptions['context'],
            'customfield_textarea', 'defaultvalue', $this->get('id'));

        $configdata['defaultvalue'] = $tempvalue->defaultvalue;
        $configdata['defaultvalueformat'] = $tempvalue->defaultvalueformat;
        unset($configdata['defaultvalue_editor']);
        $this->field->set('configdata', json_encode($configdata));
        $this->field->save();
    }
}
