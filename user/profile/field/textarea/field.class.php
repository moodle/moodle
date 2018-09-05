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
 * Textarea profile field define.
 *
 * @package   profilefield_textarea
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class profile_field_textarea.
 *
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_textarea extends profile_field_base {

    /**
     * Adds elements for this field type to the edit form.
     * @param moodleform $mform
     */
    public function edit_field_add($mform) {
        // Create the form field.
        $mform->addElement('editor', $this->inputname, format_string($this->field->name), null, null);
        $mform->setType($this->inputname, PARAM_RAW); // We MUST clean this before display!
    }

    /**
     * Overwrite base class method, data in this field type is potentially too large to be included in the user object.
     * @return bool
     */
    public function is_user_object_data() {
        return false;
    }

    /**
     * Process incoming data for the field.
     * @param stdClass $data
     * @param stdClass $datarecord
     * @return mixed|stdClass
     */
    public function edit_save_data_preprocess($data, $datarecord) {
        if (is_array($data)) {
            $datarecord->dataformat = $data['format'];
            $data = $data['text'];
        }
        return $data;
    }

    /**
     * Load user data for this profile field, ready for editing.
     * @param stdClass $user
     */
    public function edit_load_user_data($user) {
        if ($this->data !== null) {
            $this->data = clean_text($this->data, $this->dataformat);
            $user->{$this->inputname} = array('text' => $this->data, 'format' => $this->dataformat);
        }
    }

    /**
     * Display the data for this field
     * @return string
     */
    public function display_data() {
        return format_text($this->data, $this->dataformat, array('overflowdiv' => true));
    }

    /**
     * Return the field type and null properties.
     * This will be used for validating the data submitted by a user.
     *
     * @return array the param type and null property
     * @since Moodle 3.2
     */
    public function get_field_properties() {
        return array(PARAM_RAW, NULL_NOT_ALLOWED);
    }
}


