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
 * Template selection form.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class template_apply_confirm extends \moodleform {
    /**
     * Form definition.
     */
    protected function definition(): void {
        $mform = $this->_form;
        $board = $this->_customdata['board'];
        $template = $this->_customdata['template'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'templateid');
        $mform->setType('templateid', PARAM_INT);

        $mform->addElement('static', 'namestatic', get_string('name'), s($template->name));

        $description = format_text($template->description, FORMAT_HTML);
        $mform->addElement('static', 'namedescription', get_string('template_description', 'mod_board'), $description);

        $options = template::get_context_menu($template->contextid);
        $mform->addElement('static', 'contextidstatic', get_string('category'), $options[$template->contextid]);

        if ($template->columns !== '') {
            $collumns = template::format_columns($template->columns);
            $mform->addElement('static', 'columnsstatic', get_string('template_columns', 'mod_board'), $collumns);
        }

        $settings = template::format_settings($template->jsonsettings);
        $mform->addElement('static', 'settingsstatic', get_string('settings'), $settings);

        $this->add_action_buttons(true, get_string('template_apply', 'mod_board'));
    }
}
