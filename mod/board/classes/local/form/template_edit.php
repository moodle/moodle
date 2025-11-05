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

namespace mod_board\local\form;

use mod_board\local\template;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/formslib.php");

/**
 * Template editing form.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class template_edit extends \moodleform {
    use \mod_board\local\ajax_form_trait;

    /**
     * Form definition.
     */
    protected function definition(): void {
        $mform = $this->_form;

        $id = $this->_customdata['id'];
        $contextid = $this->_customdata['contextid'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name'), ['size' => '50']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        $mform->addElement(
            'editor',
            'description_editor',
            get_string('template_description', 'mod_board'),
            ['autosave' => false, 'rows' => 7]
        );

        $options = template::get_context_menu($contextid);
        $mform->addElement('select', 'contextid', get_string('category'), $options);

        $mform->addElement(
            'textarea',
            'columns',
            get_string('template_columns', 'mod_board'),
            ['cols' => 30, 'rows' => 6]
        );

        $allsettings = template::get_all_settings();
        foreach ($allsettings as $field => $setting) {
            if ($setting['type'] === 'select') {
                $mform->addElement('select', $field, $setting['name'], $setting['options']);
            } else if ($setting['type'] === 'html') {
                $mform->addElement('editor', $field . '_editor', $setting['name'], ['autosave' => false, 'rows' => 7]);
            } else {
                debugging('Unknown template setting type: ' . $setting['type'], DEBUG_DEVELOPER);
            }
        }
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (trim($data['name']) === '') {
            $errors['name'] = get_string('required');
        }

        $context = \context::instance_by_id($data['contextid'], IGNORE_MISSING);
        if (!$context) {
            $errors['contextid'] = get_string('error');
        }

        return $errors;
    }
}
