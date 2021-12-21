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
 * Customfield package
 *
 * @package   core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield;

defined('MOODLE_INTERNAL') || die;

/**
 * Class field_config_form
 *
 * @package core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_config_form extends \core_form\dynamic_form {

    /** @var field_controller */
    protected $field;

    /**
     * Class definition
     *
     * @throws \coding_exception
     */
    public function definition() {
        $mform = $this->_form;

        $field = $this->get_field();
        $handler = $field->get_handler();

        $mform->addElement('header', '_commonsettings', get_string('commonsettings', 'core_customfield'));

        $mform->addElement('text', 'name', get_string('fieldname', 'core_customfield'), 'size="50"');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        // Accepted values for 'shortname' would follow [a-z0-9_] pattern,
        // but we are accepting any PARAM_TEXT value here,
        // and checking [a-zA-Z0-9_] pattern in validation() function to throw an error when needed.
        $mform->addElement('text', 'shortname', get_string('fieldshortname', 'core_customfield'), 'size=20');
        $mform->addHelpButton('shortname', 'shortname', 'core_customfield');
        $mform->addRule('shortname', null, 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);

        $desceditoroptions = $handler->get_description_text_options();
        $mform->addElement('editor', 'description_editor', get_string('description', 'core_customfield'), null, $desceditoroptions);
        $mform->addHelpButton('description_editor', 'description', 'core_customfield');

        // If field is required.
        $mform->addElement('selectyesno', 'configdata[required]', get_string('isfieldrequired', 'core_customfield'));
        $mform->addHelpButton('configdata[required]', 'isfieldrequired', 'core_customfield');
        $mform->setType('configdata[required]', PARAM_BOOL);

        // If field data is unique.
        $mform->addElement('selectyesno', 'configdata[uniquevalues]', get_string('isdataunique', 'core_customfield'));
        $mform->addHelpButton('configdata[uniquevalues]', 'isdataunique', 'core_customfield');
        $mform->setType('configdata[uniquevalues]', PARAM_BOOL);

        // Field specific settings from field type.
        $field->config_form_definition($mform);

        // Handler/component settings.
        $handler->config_form_definition($mform);

        // We add hidden fields.
        $mform->addElement('hidden', 'categoryid');
        $mform->setType('categoryid', PARAM_INT);

        $mform->addElement('hidden', 'type');
        $mform->setType('type', PARAM_COMPONENT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // This form is only used inside modal dialogues and never needs action buttons.
    }

    /**
     * Field data validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files = array()) {
        global $DB;

        $errors = array();
        $field = $this->get_field();
        $handler = $field->get_handler();

        // Check the shortname is specified and is unique for this component-area-itemid combination.
        if (!preg_match('/^[a-z0-9_]+$/', $data['shortname'])) {
            // Check allowed pattern (numbers, letters and underscore).
            $errors['shortname'] = get_string('invalidshortnameerror', 'core_customfield');
        } else if ($DB->record_exists_sql('SELECT 1 FROM {customfield_field} f ' .
            'JOIN {customfield_category} c ON c.id = f.categoryid ' .
            'WHERE f.shortname = ? AND f.id <> ? AND c.component = ? AND c.area = ? AND c.itemid = ?',
            [$data['shortname'], $data['id'],
                $handler->get_component(), $handler->get_area(), $handler->get_itemid()])) {
            $errors['shortname'] = get_string('formfieldcheckshortname', 'core_customfield');
        }

        $errors = array_merge($errors, $field->config_form_validation($data, $files));

        return $errors;
    }

    /**
     * Get field
     *
     * @return field_controller
     * @throws \moodle_exception
     */
    protected function get_field(): field_controller {
        if ($this->field === null) {
            if (!empty($this->_ajaxformdata['id'])) {
                $this->field = \core_customfield\field_controller::create((int)$this->_ajaxformdata['id']);
            } else if (!empty($this->_ajaxformdata['categoryid']) && !empty($this->_ajaxformdata['type'])) {
                $category = \core_customfield\category_controller::create((int)$this->_ajaxformdata['categoryid']);
                $type = clean_param($this->_ajaxformdata['type'], PARAM_PLUGIN);
                $this->field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);
            } else {
                throw new \moodle_exception('fieldnotfound', 'core_customfield');
            }
        }
        return $this->field;
    }

    /**
     * Check if current user has access to this form, otherwise throw exception
     *
     * Sometimes permission check may depend on the action and/or id of the entity.
     * If necessary, form data is available in $this->_ajaxformdata
     */
    protected function check_access_for_dynamic_submission(): void {
        $field = $this->get_field();
        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }
    }

    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $this->set_data(get_entity($this->_ajaxformdata['id']));
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data(api::prepare_field_for_config_form($this->get_field()));
    }

    /**
     * Process the form submission
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * @return mixed
     */
    public function process_dynamic_submission() {
        $data = $this->get_data();
        $field = $this->get_field();
        $handler = $field->get_handler();
        $handler->save_field_configuration($field, $data);
        return null;
    }

    /**
     * Form context
     * @return \context
     */
    protected function get_context_for_dynamic_submission(): \context {
        return $this->get_field()->get_handler()->get_configuration_context();
    }

    /**
     * Page url
     * @return \moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): \moodle_url {
        $field = $this->get_field();
        if ($field->get('id')) {
            $params = ['action' => 'editfield', 'id' => $field->get('id')];
        } else {
            $params = ['action' => 'addfield', 'categoryid' => $field->get('categoryid'), 'type' => $field->get('type')];
        }
        return new \moodle_url($field->get_handler()->get_configuration_url(), $params);
    }
}
