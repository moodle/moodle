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
 * Privacy implementation.
 *
 * @package    local_adminer
 * @author Andreas Grabs <moodle@grabs-edv.de>
 * @copyright  Andreas Grabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_adminer;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

/**
 * The local plugin adminer does not store any data.
 *
 * @copyright  2018 Andreas Grabs <moodle@grabs-edv.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secret_form extends \moodleform {

    /**
     * Form definition for a single input field.
     *
     * @return void
     */
    public function definition() {
        global $OUTPUT;

        $mform = $this->_form;

        $elementarray = [];

        $mform->addElement('static', 'statictitle', '', $OUTPUT->render_from_template('local_adminer/secret_note', []));
        $mform->addElement('static', 'statictitle', '', get_string('adminer_secret', 'local_adminer'));
        $textbox = $mform->createElement('password', 'adminersecret');
        $mform->setType('adminersecret', PARAM_RAW);
        $elementarray[] = $textbox;
        $elementarray[] = $mform->createElement('submit', 'submitbutton', get_string('ok'));
        $mform->addGroup($elementarray, 'elementarray', '', [' '], false);
    }
}
