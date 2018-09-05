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
 * Transfer form
 *
 * @package    tool_dbtransfer
 * @copyright  2008 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'/locallib.php');


/**
 * Definition of db transfer settings form.
 *
 * @copyright  2008 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_transfer_form extends moodleform {

    /**
     * Define transfer form.
     */
    protected function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'database', get_string('targetdatabase', 'tool_dbtransfer'));

        $drivers = tool_dbtransfer_get_drivers();
        $drivers = array_reverse($drivers, true);
        $drivers[''] = get_string('choosedots');
        $drivers = array_reverse($drivers, true);

        $mform->addElement('select', 'driver', get_string('dbtype', 'install'), $drivers);
        $mform->setType('driver', PARAM_RAW);

        $mform->addElement('text', 'dbhost', get_string('databasehost', 'install'));
        $mform->setType('dbhost', PARAM_HOST);

        $mform->addElement('text', 'dbname', get_string('databasename', 'install'));
        $mform->setType('dbname', PARAM_ALPHANUMEXT);

        $mform->addElement('text', 'dbuser', get_string('databaseuser', 'install'));
        $mform->setType('dbuser', PARAM_ALPHANUMEXT);

        $mform->addElement('passwordunmask', 'dbpass', get_string('databasepass', 'install'));
        $mform->setType('dbpass', PARAM_RAW);

        $mform->addElement('text', 'prefix', get_string('dbprefix', 'install'));
        $mform->setType('prefix', PARAM_ALPHANUMEXT);

        $mform->addElement('text', 'dbport', get_string('dbport', 'install'));
        $mform->setType('dbport', PARAM_INT);

        if ($CFG->ostype !== 'WINDOWS') {
            $mform->addElement('text', 'dbsocket', get_string('databasesocket', 'install'));
        } else {
            $mform->addElement('hidden', 'dbsocket');
        }
        $mform->setType('dbsocket', PARAM_RAW);

        $mform->addRule('driver', get_string('required'), 'required', null);
        $mform->addRule('dbhost', get_string('required'), 'required', null);
        $mform->addRule('dbname', get_string('required'), 'required', null);
        $mform->addRule('dbuser', get_string('required'), 'required', null);
        $mform->addRule('dbpass', get_string('required'), 'required', null);
        if (!isset($drivers['mysqli/native'])) {
            $mform->addRule('prefix', get_string('required'), 'required', null);
        }

        $mform->addElement('header', 'database', get_string('options', 'tool_dbtransfer'));

        $mform->addElement('advcheckbox', 'enablemaintenance', get_string('enablemaintenance', 'tool_dbtransfer'));
        $mform->setType('enablemaintenance', PARAM_BOOL);
        $mform->addHelpButton('enablemaintenance', 'enablemaintenance', 'tool_dbtransfer');

        $this->add_action_buttons(false, get_string('transferdata', 'tool_dbtransfer'));
    }

    /**
     * Validate prefix is present for non-mysql drivers.
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['driver'] !== 'mysqli/native') {
            // This is a bloody hack, let's pretend we do not need to look at db family...
            if ($data['prefix'] === '') {
                $errors['prefix'] = get_string('required');
            }
        }
        return $errors;
    }
}
