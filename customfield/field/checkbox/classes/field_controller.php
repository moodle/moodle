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
 * Customfields checkbox plugin
 *
 * @package   customfield_checkbox
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_checkbox;

defined('MOODLE_INTERNAL') || die;

/**
 * Class field
 *
 * @package customfield_checkbox
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_controller  extends \core_customfield\field_controller {
    /**
     * Plugin type
     */
    const TYPE = 'checkbox';

    /**
     * Add fields for editing a checkbox field.
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform) {
        $mform->addElement('header', 'header_specificsettings', get_string('specificsettings', 'customfield_checkbox'));
        $mform->setExpanded('header_specificsettings', true);

        $mform->addElement('selectyesno', 'configdata[checkbydefault]', get_string('checkedbydefault', 'customfield_checkbox'));
        $mform->setType('configdata[checkbydefault]', PARAM_BOOL);
    }

    /**
     * Validate the data on the field configuration form
     *
     * @param array $data from the add/edit profile field form
     * @param array $files
     * @return array associative array of error messages
     */
    public function config_form_validation(array $data, $files = array()) : array {
        $errors = parent::config_form_validation($data, $files);

        if ($data['configdata']['uniquevalues']) {
            $errors['configdata[uniquevalues]'] = get_string('errorconfigunique', 'customfield_checkbox');
        }

        return $errors;
    }
}
