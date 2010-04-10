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

Wherever possible, customisations should be written using one of the
standard plug-in points like modules, blocks, auth plugins, themes, etc.

See also http://docs.moodle.org/en/Development:Local_customisation for more
information.


Directory structure
-------------------
This directory has standard plugin structure. All standard plugin features
are supported. There may be some extra files with special meaning in /local/.

Sample /local/ directory listing:
/local/nicehack/         - first customisation plugin
/local/otherhack/        - other customisation plugin
/local/upgrade_pre20.php - one time upgrade and migration script which is
                           executed before main 2.0 upgrade
/local/defaults.php      - custom admin setting defaults


Custom capabilities
-------------------
Each plugin may define own capabilities. It is not recommended to define
capabilities belonging to other plugins here, but it should work too.

/local/nicehack/access.php content
<?php
$local_nicehack_capabilities = array(
    'local/nicehack:nicecapability' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
);
?>


Custom language strings
-----------------------
If customisation needs new strings it is recommended to use normal plugin
strings.

sample language file /local/nicehack/lang/en/local_nicehack.php
<?php
$string['hello'] = 'Hi $a';
$string['nicehack:nicecapability'] = 'Some capability';
?>

use of the new string in code
echo get_string('hello', 'local_nicehack', 'petr');


Custom admin menu items
----------------------
It is possible to add new items and categories to the admin_tree block.
I you need to define new admin setting classes put them into separate
file and require_once() from settings.php

For example if you want to add new external page use following
/local/nicehack/settings.php
<?php
$ADMIN->add('root', new admin_category('tweaks', 'Custom tweaks'));
$ADMIN->add('tweaks', new admin_externalpage('nicehackery', 'Tweak something',
            $CFG->wwwroot.'/local/nicehack/setuppage.php'));
?>


Custom event handlers
---------------------
Events intended primarily for communication "core --> plugins". (It should not
be use in opposite direction!) In theory it could be also used for
"plugin --> plugin" communication too. The list of core events is documented
in lib/db/events.php

sample files
/local/nicehack/db/events.php
$handlers = array (
    'user_deleted' => array (
         'handlerfile'      => '/local/nicehack/lib.php',
         'handlerfunction'  => 'nicehack_userdeleted_handler',
         'schedule'         => 'instant'
     ),
);

NOTE: events are not yet fulyl implemented in current Moodle 2.0dev.


Custom db tables
----------------
XMLDB editors is the recommended tool. Please note that modification
of core table structure is highly discouraged.

If you really really really need to modify core tables you might want to do
that in install.php and later upgrade.php

List of upgrade related files:
/local/nicehack/db/install.xml - contains XML definition of new tables
/local/nicehack/db/install.php - executed after db creation, may be also used
                                 for general install code
/local/nicehack/db/upgrade.php - executed when version changes
/local/nicehack/version.php    - version specification file


Version.php example
-------------------
/local/nicehack/version.php

$plugin->version  = 2010022400;   // The (date) version of this plugin
$plugin->requires = 2010021900;  // Requires this Moodle version


Customised site defaults
------------------------
Different default site settings can be stored in file /local/defaults.php.
These new defaults are used during installation, upgrade and later are
displayed as default values in admin settings.

Sample /local/defaults.php file content:
<?php
$defaults['moodle']['forcelogin'] = 1;
$defaults['scorm']['maxgrade'] = 20;
$defaults['moodlecourse']['numsections'] = 11;
?>

First bracket contains string from column plugin of config_plugins table.
Second bracket is the name of setting. In the admin settings UI the plugin and
name of setting is separated by "|".

Please note that not all settings are converted to admin_tree yet.


1.9.x upgrade notes
-------------------
1.9.x contains basic support for local hacks placed directly into
/local/ directory. This old local API is not supported any more in 2.0.

You an use /local/upgrade_pre20.php script for any code that needs to
be executed before the main upgrade to 2.0.



Other customisation information
===============================

Local language pack modifications
---------------------------------
Moodle supports other type of local customisation of standard language
packs. If you want to create your own language pack based on another
language create new dataroot directory with "_local" suffix, for example
following file with content changes string "Login" to "Sign in":
moodledata/lang/en_local
<?php
  $string['login'] = 'Sign in';
?>

See also http://docs.moodle.org/en/Language_editing
