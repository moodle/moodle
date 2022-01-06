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
 * Readme file for local customisations
 *
 * @package    local
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

Local customisations directory
==============================
This directory is the recommended place for local customisations.
Wherever possible, customisations should be written using one
of the standard plug-in points like modules, blocks, auth plugins, themes, etc.

See also http://docs.moodle.org/dev/Local_customisation for more
information.


Directory structure
-------------------
This directory has standard plugin structure. All standard plugin features
are supported. There may be some extra files with special meaning in /local/.

Sample /local/ directory listing:
/local/nicehack/         - first customisation plugin
/local/otherhack/        - other customisation plugin
/local/preupgrade.php    - executed before each core upgrade, use $version and $CFG->version
                           if you need to tweak specific local hacks
/local/defaults.php      - custom admin setting defaults



Local plugins
=============
Local plugins are used in cases when no standard plugin fits, examples are:
* event consumers communicating with external systems
* custom definitions of web services and external functions
* applications that extend moodle at the system level (hub server, amos server, etc.)
* new database tables used in core hacks (discouraged)
* new capability definitions used in core hacks
* custom admin settings

Standard plugin features:
* /local/pluginname/version.php - version of script (must be incremented after changes)
* /local/pluginname/db/install.xml - executed during install (new version.php found)
* /local/pluginname/db/install.php - executed right after install.xml
* /local/pluginname/db/uninstall.php - executed during uninstallation
* /local/pluginname/db/upgrade.php - executed after version.php change
* /local/pluginname/db/access.php - definition of capabilities
* /local/pluginname/db/events.php - event handlers and subscripts
* /local/pluginname/db/messages.php - messaging registration
* /local/pluginname/db/services.php - definition of web services and web service functions
* /local/pluginname/db/subplugins.php - list of subplugins types supported by this local plugin
* /local/pluginname/lang/en/local_pluginname.php - language file
* /local/pluginname/settings.php - admin settings


Local plugin version specification
----------------------------------
version.php is mandatory for most of the standard plugin infrastructure.
The version number must be incremented most plugin changes, the changed
version tells Moodle to invalidate all caches, do db upgrades if necessary,
install new capabilities, register event handlers, etc.

Example:
/local/nicehack/version.php
<?php
$plugin->version  = 2010022400;   // The (date) version of this plugin
$plugin->requires = 2010021900;   // Requires this Moodle version


Local plugin capabilities
-------------------------
Each local plugin may define own capabilities. It is not recommended to define
capabilities belonging to other plugins here, but it should work too.

/local/nicehack/access.php content
<?php
$local_nicehack_capabilities = array(
    'local/nicehack:nicecapability' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
);


Local plugin language strings
-----------------------------
If customisation needs new strings it is recommended to use normal plugin
strings.

sample language file /local/nicehack/lang/en/local_nicehack.php
<?php
$string['hello'] = 'Hi {$a}';
$string['nicehack:nicecapability'] = 'Some capability';


use of the new string in code:
echo get_string('hello', 'local_nicehack', 'petr');


Local plugin admin menu items
-----------------------------
It is possible to add new items and categories to the admin_tree block.
I you need to define new admin setting classes put them into separate
file and require_once() from settings.php

For example if you want to add new external page use following
/local/nicehack/settings.php
<?php
$ADMIN->add('root', new admin_category('tweaks', 'Custom tweaks'));
$ADMIN->add('tweaks', new admin_externalpage('nicehackery', 'Tweak something',
            $CFG->wwwroot.'/local/nicehack/setuppage.php'));

Or if you want a new standard settings page for the plugin, inside the local
plugins category:
<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // needs this condition or there is error on login page
    $settings = new admin_settingpage('local_thisplugin', 'This plugin');
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext('local_thisplugin/option',
        'Option', 'Information about this option', 100, PARAM_INT));
}

Local plugin event handlers
---------------------------
Events are intended primarily for communication "core --> plugins".
(It should not be use in opposite direction!)
In theory it could be also used for "plugin --> plugin" communication too.
The list of core events is documented in lib/db/events.php

sample files
/local/nicehack/db/events.php
$handlers = array (
    'user_deleted' => array (
         'handlerfile'      => '/local/nicehack/lib.php',
         'handlerfunction'  => 'nicehack_userdeleted_handler',
         'schedule'         => 'instant'
     ),
);

NOTE: events are not yet fully implemented in current Moodle 2.0dev.


Local plugin database tables
----------------------------
XMLDB editors is the recommended tool. Please note that modification
of core table structure is highly discouraged.

If you really really really need to modify core tables you might want to do
that in install.php and later upgrade.php

Note: it is forbidden to manually modify the DB structure, without corresponding
      changes in install.xml files.

List of upgrade related files:
/local/nicehack/db/install.xml - contains XML definition of new tables
/local/nicehack/db/install.php - executed after db creation, may be also used
                                 for general install code
/local/nicehack/db/upgrade.php - executed when version changes


Local plugin web services
-------------------------
During plugin installation or upgrade, the web service definitions are read
from /local/nicehack/db/services.php and are automatically installed/updated in Moodle.

sample files
/local/nicehack/db/services.php
$$functions = array (
    'nicehack_hello_world' => array(
                'classname'   => 'local_nicehack_external',
                'methodname'  => 'hello_world',
                'classpath'   => 'local/nicehack/externallib.php',
                'description' => 'Get hello world string',
                'type'        => 'read',
    ),
);
$services = array(
        'Nice hack service 1' => array(
                'functions' => array ('nicehack_hello_world'),
                'enabled'=>1,
        ),
);


You will need to write the /local/nicehack/externallib.php - external functions
description and code. See some examples from the core files (/user/externallib.php,
/group/externallib.php...).

Local plugin navigation hooks
-----------------------------
There are two functions that your plugin can define that allow it to extend the main
navigation and the settings navigation.
These two functions both need to be defined within /local/nicehack/lib.php.

sample code
<?php
function local_nicehack_extend_navigation(global_navigation $nav) {
    // $nav is the global navigation instance.
    // Here you can add to and manipulate the navigation structure as you like.
    // This callback was introduced in 2.0 as nicehack_extends_navigation(global_navigation $nav)
    // In 2.3 support was added for local_nicehack_extends_navigation(global_navigation $nav).
    // In 2.9 the name was corrected to local_nicehack_extend_navigation() for consistency
}
function local_nicehack_extend_settings_navigation(settings_navigation $nav, context $context) {
    // $nav is the settings navigation instance.
    // $context is the context the settings have been loaded for (settings is context specific)
    // Here you can add to and manipulate the settings structure as you like.
    // This callback was introduced in 2.3, originally as local_nicehack_extends_settings_navigation()
    // In 2.9 the name was corrected to the imperative mood ('extend', not 'extends')
}

Other local customisation files
===============================

Customised site defaults
------------------------
Different default site settings can be stored in file /local/defaults.php.
These new defaults are used during installation, upgrade and later are
displayed as default values in admin settings. This means that the content
of the defaults files is usually updated BEFORE installation or upgrade.

These customised defaults are useful especially when using CLI tools
for installation and upgrade.

Sample /local/defaults.php file content:
<?php
$defaults['moodle']['forcelogin'] = 1;  // new default for $CFG->forcelogin
$defaults['scorm']['maxgrade'] = 20;    // default for get_config('scorm', 'maxgrade')
$defaults['moodlecourse']['numsections'] = 11;
$defaults['moodle']['hiddenuserfields'] = array('city', 'country');

First bracket contains string from column plugin of config_plugins table.
Second bracket is the name of setting. In the admin settings UI the plugin and
name of setting is separated by "|".

The values usually correspond to the raw string in config table, with the exception
of comma separated lists that are usually entered as real arrays.

Please note that not all settings are converted to admin_tree,
they are mostly intended to be set directly in config.php.


2.0 pre-upgrade script
----------------------
You an use /local/upgrade_pre20.php script for any code that needs to
be executed before the main upgrade to 2.0. Most probably this will
be used for undoing of old hacks that would otherwise break normal
2.0 upgrade.

This file is just included directly, there does not need to be any
function inside. If the execution stops the script is executed again
during the next upgrade. The first execution of lib/db/upgrade.php
increments the version number and the pre upgrade script is not
executed any more.



1.9.x upgrade notes
===================
1.9.x contains basic support for local hacks placed directly into
/local/ directory. This old local API was completely removed and can
not be used any more in 2.0. All old customisations need to be
migrated to new local plugins before running of the 2.0 upgrade script.



Other site customisation outside of "/local/" directory
=======================================================

Local language pack modifications
---------------------------------
Moodle supports other type of local customisation of standard language
packs. If you want to create your own language pack based on another
language create new dataroot directory with "_local" suffix, for example
following file with content changes string "Login" to "Sign in":
moodledata/lang/en_local
<?php
  $string['login'] = 'Sign in';

See also http://docs.moodle.org/en/Language_editing


Custom script injection
-----------------------
Very old customisation option that allows you to modify scripts by injecting
code right after the require 'config.php' call.

This setting is enabled by manually setting $CFG->customscripts variable
in config.php script. The value is expected to be full path to directory
with the same structure as dirroot. Please note this hack only affects
files that actually include the config.php!

Examples:
* disable one specific moodle page without code modification
* alter page parameters on the fly
