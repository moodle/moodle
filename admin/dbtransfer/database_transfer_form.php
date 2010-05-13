<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class database_transfer_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'database', get_string('dbtransfer', 'dbtransfer'));

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

        $mform->addRule('dbhost', get_string('required'), 'required', null);
        $mform->addRule('dbname', get_string('required'), 'required', null);
        $mform->addRule('dbuser', get_string('required'), 'required', null);
        $mform->addRule('dbpass', get_string('required'), 'required', null);
        $mform->addRule('prefix', get_string('required'), 'required', null);

        $this->add_action_buttons(false, get_string('transferdata', 'dbtransfer'));
    }
}
