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
 * CSV profile field import/update/delete block.
 *
 * @package   block_csv_profile
 * @copyright 2012 onwared Ted vd Brink, Brightally custom code
 * @copyright 2018 onwards Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

/**
 * Class extends moodleform base class
 */
class block_csv_profile_form extends moodleform {

    /**
     * Standard Moodle function
     *
     * @object $mform instantiated
     * @object $data instantiated
     * @object $options instantiated
     *
     */
    public function definition() {
        $mform = $this->_form;
        $data = $this->_customdata['data'];
        $options = $this->_customdata['options'];
        $fpoptions = array('subdirs' => 0, 'accepted_types' => 'csv');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('static', 'name', '', get_string('description', 'block_csv_profile'));

        $mform->addElement('filepicker', 'userfile', get_string('uploadcsv', 'block_csv_profile'), null, $fpoptions);

        $mform->addElement('filemanager', 'files_filemanager', get_string('resultfiles', 'block_csv_profile'), null, $options);

        $profilefields = self::get_profile_fields();

        $mform->addElement('select', 'profilefield', 'Profile Field', $profilefields);
        $mform->setType('profilefield', PARAM_INT);
        $mform->getElement('profilefield')->setSelected(block_csv_profile_get_default_profile_field_id());

        $mform->addElement('html',
                '<style>#page-blocks-csv_profile-edit .fp-btn-add,
                 #page-blocks-csv_profile-edit .fp-btn-mkdir { display: none; }</style>'
                );

        $this->add_action_buttons(true, get_string('savechanges'));
        $this->set_data($data);
    }

    /**
     * Grab the user profile fiends from the DB and present them as key->value pairs
     *
     * @global $DB instantiate the DB functionality
     * @var $fsn multidimentional array of prifile field ids and their corresponding shortnames
     * @var $infofields array of profile field object data
     */
    public static function get_profile_fields() {
        global $DB;
        $fsn = array();
        $infofields = $DB->get_records('user_info_field', null, null, 'id, shortname');
        foreach ($infofields as $profilefield) {
            $fsn[$profilefield->id] = $profilefield->shortname;
        }
        return $fsn;
    }
}