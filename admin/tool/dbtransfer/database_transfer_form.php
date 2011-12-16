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
 * @package    tool
 * @subpackage dbtransfer
 * @copyright  2008 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once $CFG->libdir.'/formslib.php';

class database_transfer_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'database', get_string('dbtransfer', 'tool_dbtransfer'));

        $supported = array (
            'mysqli/native',
            'pgsql/native',
            'mssql/native',
            'oci/native',
            'sqlite3/pdo',
        );
        $drivers = array();
        foreach($supported as $driver) {
            list($dbtype, $dblibrary) = explode('/', $driver);
            $targetdb = moodle_database::get_driver_instance($dbtype, $dblibrary);
            if ($targetdb->driver_installed() !== true) {
                continue;
            }
            $drivers[$driver] = $driver;
        }

        $mform->addElement('select', 'driver', get_string('dbtype', 'install'), $drivers);
        $mform->addElement('text', 'dbhost', get_string('dbhost', 'install'));
        $mform->addElement('text', 'dbname', get_string('database', 'install'));
        $mform->addElement('text', 'dbuser', get_string('user'));
        $mform->addElement('text', 'dbpass', get_string('password'));
        $mform->addElement('text', 'prefix', get_string('dbprefix', 'install'));
        $mform->addElement('text', 'dbport', get_string('dbport', 'install'));
        $mform->addElement('text', 'dbsocket', get_string('databasesocket', 'install'));

        $mform->addRule('dbhost', get_string('required'), 'required', null);
        $mform->addRule('dbname', get_string('required'), 'required', null);
        $mform->addRule('dbuser', get_string('required'), 'required', null);
        $mform->addRule('dbpass', get_string('required'), 'required', null);
        $mform->addRule('prefix', get_string('required'), 'required', null);

        $this->add_action_buttons(false, get_string('transferdata', 'tool_dbtransfer'));
    }
}
