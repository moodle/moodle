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
 * A form for group import.
 *
 * @package    core_group
 * @copyright  2010 Toyomoyo (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir . '/csvlib.class.php');

/**
 * Groups import form class
 *
 * @package    core_group
 * @copyright  2010 Toyomoyo (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groups_import_form extends moodleform {

    /**
     * Form definition
     */
    function definition() {
        $mform =& $this->_form;
        $data  = $this->_customdata;

        //fill in the data depending on page params
        //later using set_data
        $mform->addElement('header', 'general', get_string('general'));

        $filepickeroptions = array();
        $filepickeroptions['filetypes'] = '*';
        $filepickeroptions['maxbytes'] = get_max_upload_file_size();
        $mform->addElement('filepicker', 'userfile', get_string('import'), null, $filepickeroptions);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'group'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'group'), $choices);
        $mform->setDefault('encoding', 'UTF-8');
        $this->add_action_buttons(true, get_string('importgroups', 'core_group'));

        $this->set_data($data);
    }
}

