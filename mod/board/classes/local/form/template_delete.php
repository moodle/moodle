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
 * Template deletion confirmation form.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class template_delete extends \moodleform {
    use \mod_board\local\ajax_form_trait;

    /**
     * Form definition.
     */
    protected function definition(): void {
        $mform = $this->_form;

        $template = $this->_customdata['template'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('static', 'namestatic', get_string('name'), s($template->name));

        $description = format_text($template->description, FORMAT_HTML);
        $mform->addElement('static', 'namedescription', get_string('template_description', 'mod_board'), $description);

        $options = template::get_context_menu($template->contextid);
        $mform->addElement('static', 'contextidstatic', get_string('category'), $options[$template->contextid]);

        $this->set_data($template);
    }
}
