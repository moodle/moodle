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

/** LearnerScript Reports
 * A Moodle block for creating LearnerScript Reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\form;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');  // It must be included from a Moodle page.
}

require_once($CFG->libdir . '/formslib.php');
use moodleform;
class import_form extends moodleform {

    public function definition() {
        global $DB, $USER, $CFG;

        $mform = & $this->_form;

        $mform->addElement('header', 'importreport', get_string('importreport', 'block_learnerscript'));

        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->setType('userfile', PARAM_FILE);
        $mform->addRule('userfile', null, 'required');

        $mform->addElement('hidden', 'courseid', $this->_customdata);
        $mform->setType('courseid', PARAM_INT);

        // buttons
        $this->add_action_buttons(false, get_string('importreport', 'block_learnerscript'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }

}
