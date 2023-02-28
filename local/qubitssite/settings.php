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
 * Local plugin "QubitsSite"
 *
 * @package   local_qubitssite
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Ensure the configurations for this site are set
if ( $hassiteconfig ){

	$ADMIN->add('root', new admin_category('qubitssite', get_string('pluginname', 'local_qubitssite')));
	
	$ADMIN->add('qubitssite', new admin_externalpage('siteslist', get_string('siteslist', 'local_qubitssite'),
                 new moodle_url('/local/qubitssite/index.php')));

	// Create the new settings page
	// - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
	// $settings will be NULL
	$settings = new admin_settingpage( 'local_qubitssite', get_string('pluginname', 'local_qubitssite'));

	// Create 
	$ADMIN->add( 'localplugins', $settings );

	// Add a setting field to the settings for this page
	$settings->add( new admin_setting_configtext(
		
		// This is the reference you will use to your configuration
		'local_qubitssite/dummysetting',
	
		// This is the friendly title for the config, which will be displayed
		'Example Dummy Settings',
	
		// This is helper text for this config field
		'This is example of settings page. We will add the fields later.',
	
		// This is the default value
		'Dummy Value',
	
		// This is the type of Parameter this config is
		PARAM_TEXT
	
	) );

}