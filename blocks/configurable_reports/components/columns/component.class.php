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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class component_columns
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class component_columns extends component_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->plugins = true;
        $this->ordering = true;
        $this->form = true;
        $this->help = true;
    }

    /**
     * process_form
     *
     * @return true|void
     */
    public function process_form() {
        if ($this->form) {
            return true;
        }
    }

    /**
     * add_form_elements
     *
     * @param MoodleQuickForm $mform
     * @param string|object $components
     */
    public function add_form_elements(MoodleQuickForm $mform, $components): void {

        $mform->addElement('header', 'crformheader', get_string('columnandcellproperties', 'block_configurable_reports'), '');

        $mform->addElement('text', 'columname', get_string('name'));
        $mform->setType('columname', PARAM_TEXT);

        $mform->addElement(
            'select',
            'align',
            get_string('cellalign', 'block_configurable_reports'),
            ['center' => 'center', 'left' => 'left', 'right' => 'right']
        );
        $mform->setAdvanced('align');

        $mform->addElement('text', 'size', get_string('cellsize', 'block_configurable_reports'));
        $mform->setType('size', PARAM_TEXT);
        $mform->setAdvanced('size');

        $mform->addElement(
            'select',
            'wrap',
            get_string('cellwrap', 'block_configurable_reports'),
            ['' => 'Wrap', 'nowrap' => 'No Wrap']
        );
        $mform->setAdvanced('wrap');

        $mform->addRule('columname', get_string('required'), 'required');
    }

    /**
     * validate_form_elements
     *
     * @param array $data
     * @param array $errors
     * @return array
     */
    public function validate_form_elements(array $data, array $errors): array {
        if (!empty($data['size']) && !preg_match("/^\d+(%|px)$/i", trim($data['size']))) {
            $errors['size'] = get_string('badsize', 'block_configurable_reports');
        }

        return $errors;
    }

    /**
     * form_process_data
     *
     * @param moodleform $cform
     * @return void
     */
    public function form_process_data(moodleform $cform): void {
        global $DB;
        if ($this->form) {
            $data = $cform->get_data();
            // Function cr_serialize() will add slashes.

            $components = cr_unserialize($this->config->components);
            $components['columns']['config'] = $data;
            $this->config->components = cr_serialize($components);
            $DB->update_record('block_configurable_reports', $this->config);
        }
    }

    /**
     * form_set_data
     *
     * @param moodleform $cform
     * @return void
     */
    public function form_set_data(moodleform $cform): void {
        if ($this->form) {
            $fdata = new stdclass;
            $components = cr_unserialize($this->config->components);

            $fdata = $components['columns']['config'] ?? $fdata;
            $cform->set_data($fdata);
        }
    }

}
