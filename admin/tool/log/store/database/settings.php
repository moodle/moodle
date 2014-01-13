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
 * External database log store settings.
 *
 * @package    logstore_database
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $options = array(
        '' => get_string('choose'),
        'native/mysqli' => moodle_database::get_driver_instance('mysqli', 'native')->get_name(),
        'native/mariadb'=> moodle_database::get_driver_instance('mariadb', 'native')->get_name(),
        'native/pgsql' => moodle_database::get_driver_instance('pgsql',  'native')->get_name(),
        'native/oci' => moodle_database::get_driver_instance('oci',    'native')->get_name(),
        'native/sqlsrv' => moodle_database::get_driver_instance('sqlsrv', 'native')->get_name(),
        'native/mssql' => moodle_database::get_driver_instance('mssql',  'native')->get_name(),
    );

    // TODO: Localise these settings.

    $settings->add(new admin_setting_configselect('logstore_database/dbdriver', 'dbdriver', '', '', $options));

    $settings->add(new admin_setting_configtext('logstore_database/dbhost', 'dbhost', '', ''));
    $settings->add(new admin_setting_configtext('logstore_database/dbuser', 'dbuser', '', ''));
    $settings->add(new admin_setting_configtext('logstore_database/dbpass', 'dbpass', '', ''));
    $settings->add(new admin_setting_configtext('logstore_database/dbname', 'dbname', '', ''));
    $settings->add(new admin_setting_configtext('logstore_database/dbname', 'dbprefix', '', ''));

    $settings->add(new admin_setting_configcheckbox('logstore_database/dbpersist', 'dbpersist', '', '0'));
    $settings->add(new admin_setting_configtext('logstore_database/dbsocket', 'dbsocket', '', ''));
    $settings->add(new admin_setting_configtext('logstore_database/dbport', 'dbport', '', ''));
    $settings->add(new admin_setting_configtext('logstore_database/dbschema', 'dbschema', '', ''));
    $settings->add(new admin_setting_configtext('logstore_database/dbcollation', 'dbcollation', '', ''));

}
