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

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Class field_config_form
 *
 * @package core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_config_form extends \moodleform {

    /**
     * Class definition
     *
     * @throws \coding_exception
     */
    public function definition() {
        global $PAGE;
        $mform = $this->_form;

        $field = $this->_customdata['field'];
        if (!($field && $field instanceof field_controller)) {
            throw new \coding_exception('Field must be passed in customdata');
        }
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

        $this->add_action_buttons(true);
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
        /** @var field_controller $field */
        $field = $this->_customdata['field'];
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
}
