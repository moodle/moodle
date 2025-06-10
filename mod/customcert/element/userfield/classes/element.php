<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the customcert element userfield's core interaction API.
 *
 * @package    customcertelement_userfield
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_userfield;

use core_user\fields;

/**
 * The customcert element userfield's core interaction API.
 *
 * @package    customcertelement_userfield
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \mod_customcert\element {

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        // Get the user profile fields.
        $userfields = [
            'firstname' => fields::get_display_name('firstname'),
            'lastname' => fields::get_display_name('lastname'),
            'username' => fields::get_display_name('username'),
            'email' => fields::get_display_name('email'),
            'city' => fields::get_display_name('city'),
            'country' => fields::get_display_name('country'),
            'url' => fields::get_display_name('url'),
            'idnumber' => fields::get_display_name('idnumber'),
            'institution' => fields::get_display_name('institution'),
            'department' => fields::get_display_name('department'),
            'phone1' => fields::get_display_name('phone1'),
            'phone2' => fields::get_display_name('phone2'),
            'address' => fields::get_display_name('address'),
        ];
        // Get the user custom fields.
        $arrcustomfields = \availability_profile\condition::get_custom_profile_fields();
        $customfields = [];
        foreach ($arrcustomfields as $key => $customfield) {
            $customfields[$customfield->id] = $customfield->name;
        }
        // Combine the two.
        $fields = $userfields + $customfields;
        \core_collator::asort($fields);

        // Create the select box where the user field is selected.
        $mform->addElement('select', 'userfield', get_string('userfield', 'customcertelement_userfield'), $fields);
        $mform->setType('userfield', PARAM_ALPHANUM);
        $mform->addHelpButton('userfield', 'userfield', 'customcertelement_userfield');

        parent::render_form_elements($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the text
     */
    public function save_unique_data($data) {
        return $data->userfield;
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        \mod_customcert\element_helper::render_content($pdf, $this, $this->get_user_field_value($user, $preview));
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     */
    public function render_html() {
        global $USER;

        return \mod_customcert\element_helper::render_html_content($this, $this->get_user_field_value($USER, true));
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $element = $mform->getElement('userfield');
            $element->setValue($this->get_data());
        }
        parent::definition_after_data($mform);
    }

    /**
     * Helper function that returns the text.
     *
     * @param \stdClass $user the user we are rendering this for
     * @param bool $preview Is this a preview?
     * @return string
     */
    protected function get_user_field_value(\stdClass $user, bool $preview): string {
        global $CFG, $DB;

        // The user field to display.
        $field = $this->get_data();
        // The value to display - we always want to show a value here so it can be repositioned.
        if ($preview) {
            $value = $field;
        } else {
            $value = '';
        }
        if (is_number($field)) { // Must be a custom user profile field.
            if ($field = $DB->get_record('user_info_field', ['id' => $field])) {
                // Found the field name, let's update the value to display.
                $value = $field->name;
                $file = $CFG->dirroot . '/user/profile/field/' . $field->datatype . '/field.class.php';
                if (file_exists($file)) {
                    require_once($CFG->dirroot . '/user/profile/lib.php');
                    require_once($file);
                    $class = "profile_field_{$field->datatype}";
                    $field = new $class($field->id, $user->id);
                    $value = $field->display_data();
                }
            }
        } else if (!empty($user->$field)) { // Field in the user table.
            $value = $user->$field;
        }

        $context = \mod_customcert\element_helper::get_context($this->get_id());
        return format_string($value, true, ['context' => $context]);
    }
}
