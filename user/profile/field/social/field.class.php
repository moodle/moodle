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
 * Social profile field define.
 *
 * @package    profilefield_social
 * @copyright  2020 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class profile_field_social.
 *
 * @copyright  2020 Bas Brands <bas@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_social extends profile_field_base {

    /**
     * Adds elements for this field type to the edit form.
     * @param moodleform $mform
     */
    public function edit_field_add($mform) {
        $mform->addElement('text', $this->inputname, $this->field->name, null, null);
        if ($this->field->param1 === 'url') {
            $mform->setType($this->inputname, PARAM_URL);
        } else {
            $mform->setType($this->inputname, PARAM_NOTAGS);
        }
    }

    /**
     * alter the fieldname to be fetched from the language file.
     *
     * @param stdClass $field
     */
    public function set_field($field) {
        $networks = profilefield_social\helper::get_networks();
        $field->name = $networks[$field->name];
        parent::set_field($field);
    }

    /**
     * Display the data for this field
     * @return string
     */
    public function display_data() {
        $network = $this->field->param1;
        $networkurls = profilefield_social\helper::get_network_urls();

        if (array_key_exists($network, $networkurls)) {
            return str_replace('%%DATA%%', $this->data, $networkurls[$network]);
        }

        return $this->data;
    }

    /**
     * Return the field type and null properties.
     * This will be used for validating the data submitted by a user.
     *
     * @return array the param type and null property
     * @since Moodle 3.2
     */
    public function get_field_properties() {
        return array(PARAM_TEXT, NULL_NOT_ALLOWED);
    }
}


