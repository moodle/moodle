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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/formslib.php");

/**
 * Template selection form.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class template_apply extends \moodleform {
    /**
     * Form definition.
     */
    protected function definition(): void {
        $mform = $this->_form;
        $board = $this->_customdata['board'];
        $templates = $this->_customdata['templates'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $templates = ['' => get_string('choosedots')] + $templates;
        $mform->addElement('select', 'templateid', get_string('template', 'mod_board'), $templates);
        $mform->addRule('templateid', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('continue'));
    }
}
