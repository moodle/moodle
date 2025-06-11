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
 * Class profile_field_checkbox
 *
 * @package    profilefield_checkbox
 * @copyright  2008 onwards Shane Elliot {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_checkbox extends profile_field_base {

    /**
     * Add elements for editing the profile field value.
     * @param moodleform $mform
     */
    public function edit_field_add($mform) {
        // Create the form field.
        $checkbox = $mform->addElement('advcheckbox', $this->inputname, format_string($this->field->name));
        if ($this->data == '1') {
            $checkbox->setChecked(true);
        }
        $mform->setType($this->inputname, PARAM_BOOL);
        if ($this->is_required() and !has_capability('moodle/user:update', context_system::instance())) {
            $mform->addRule($this->inputname, get_string('required'), 'nonzero', null, 'client');
        }
    }

    /**
     * Override parent {@see profile_field_base::is_empty} check
     *
     * We can't check the "data" property, because if not set by the user then it's populated by "defaultdata" of the field,
     * which can also be 0 (false) therefore ensuring the parent class check could never return true for this comparison
     *
     * @return bool
     */
    public function is_empty() {
        return ($this->userid && !$this->field->hasuserdata);
    }

    /**
     * Override parent {@see profile_field_base::show_field_content} check
     *
     * We only need to determine whether the field is visible, because we also want to show the "defaultdata" of the field,
     * even if the user hasn't explicitly filled it in
     *
     * @param context|null $context
     * @return bool
     */
    public function show_field_content(?context $context = null): bool {
        return $this->is_visible($context);
    }

    /**
     * Display the data for this field
     *
     * @return string HTML.
     */
    public function display_data() {
        return $this->data ? get_string('yes') : get_string('no');
    }

    /**
     * Return the field type and null properties.
     * This will be used for validating the data submitted by a user.
     *
     * @return array the param type and null property
     * @since Moodle 3.2
     */
    public function get_field_properties() {
        return array(PARAM_BOOL, NULL_NOT_ALLOWED);
    }
}


