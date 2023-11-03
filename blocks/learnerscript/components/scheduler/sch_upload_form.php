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
 * Bulk user upload forms
 * @package    local
 * @subpackage user
 * @copyright  2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/formslib.php';

/**
 * Upload a file CSV file with user information.
 *
 * @copyright  2007 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//admin_user_form1
class bulkschreports extends moodleform {

	public function definition() {
		$mform = $this->_form;

		$mform->addElement('filepicker', 'userfile', get_string('file'));
		$mform->addHelpButton('userfile', 'uploaddec', 'block_learnerscript');
		$mform->addRule('userfile', null, 'required');

		$choices = csv_import_reader::get_delimiter_list();
		$mform->addElement('hidden', 'delimiter_name', get_string('csvdelimiter', 'block_learnerscript'), $choices);
		if (array_key_exists('cfg', $choices)) {
			$mform->setDefault('delimiter_name', 'cfg');
		} else if (get_string('listsep', 'langconfig') == ';') {
			$mform->setDefault('delimiter_name', 'semicolon');
		} else {
			$mform->setDefault('delimiter_name', 'comma');
		}
		$mform->setType('delimiter_name', PARAM_RAW);

		$choices = core_text::get_encodings();
		$mform->addElement('hidden', 'encoding', get_string('encoding', 'block_learnerscript'), $choices);
		$mform->setDefault('encoding', 'UTF-8');
		$mform->setType('encoding', PARAM_RAW);

		$choices = array('10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '100000' => 100000);
		$mform->addElement('hidden', 'previewrows', get_string('rowpreviewnum', 'block_learnerscript'), $choices);
		$mform->setType('previewrows', PARAM_INT);

		$this->add_action_buttons(true, get_string('upload'));
	}
}
