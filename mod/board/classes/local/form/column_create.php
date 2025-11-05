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

use mod_board\board;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/formslib.php");

/**
 * Create column.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class column_create extends \moodleform {
    use \mod_board\local\ajax_form_trait;

    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;

        $board = $this->_customdata['board'];

        $mform->addElement('hidden', 'boardid');
        $mform->setType('boardid', PARAM_INT);
        $mform->setDefault('boardid', $board->id);

        $mform->addElement('text', 'name', get_string('name'), ['size' => '50']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule(
            'name',
            get_string('maximumchars', '', board::LENGTH_COLNAME),
            'maxlength',
            board::LENGTH_COLNAME,
            'client'
        );
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (trim($data['name']) === '') {
            $errors['name'] = get_string('required');
        }

        return $errors;
    }
}
