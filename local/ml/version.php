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
defined('MOODLE_INTERNAL') || die();
$plugin->version  = 2018061101;   // The (date) version of this module + 2 extra digital for daily versions
                                  // This version number is displayed into /admin/forms.php
// sbd TODO: Assess. 2010112400=2.0, 2014051200=2.7.
$plugin->requires = 2010112400;  // Requires this Moodle version - at least 2.0
$plugin->cron     = 0; // Disabled.
$plugin->release = '1.03 (Build: 2018061101)';
$plugin->maturity = MATURITY_STABLE; // One of: MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC or MATURITY_STABLE.
// As far as I know, any other than MATURITY_STABLE has no side effects except a warning on install validation.
// Optional.  Allows to declare explicit dependency on other plugin(s) for this plugin to work.
// Moodle core checks these declared dependencies.
// It will not allow the plugin installation and/or upgrade until all dependencies are satisfied.
// sbd TODO: Assess.  Perhaps for each core API function used.
/*
 $plugin->dependencies = array(
     'mod_forum' => ANY_VERSION, // Can be ANY_VERSION
     'mod_data'  => TODO
 );
*/

// sbd SYSIN-3644
/*
The full frankenstyle component name in the form of plugintype_pluginname. It is used during the installation and
upgrade process for diagnostics and validation purposes to make sure the plugin code has been deployed to the correct
location within the Moodle code tree.
*) Required since Moodle 3.0, strongly recommended for earlier versions (MDL-48494).
*/
$plugin->component = 'local_ml';
