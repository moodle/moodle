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
 * Functions and classes used during installation, upgrades and for admin settings.
 *
 *  ADMIN SETTINGS TREE INTRODUCTION
 *
 *  This file performs the following tasks:
 *   -it defines the necessary objects and interfaces to build the Moodle
 *    admin hierarchy
 *   -it defines the admin_externalpage_setup()
 *
 *  ADMIN_SETTING OBJECTS
 *
 *  Moodle settings are represented by objects that inherit from the admin_setting
 *  class. These objects encapsulate how to read a setting, how to write a new value
 *  to a setting, and how to appropriately display the HTML to modify the setting.
 *
 *  ADMIN_SETTINGPAGE OBJECTS
 *
 *  The admin_setting objects are then grouped into admin_settingpages. The latter
 *  appear in the Moodle admin tree block. All interaction with admin_settingpage
 *  objects is handled by the admin/settings.php file.
 *
 *  ADMIN_EXTERNALPAGE OBJECTS
 *
 *  There are some settings in Moodle that are too complex to (efficiently) handle
 *  with admin_settingpages. (Consider, for example, user management and displaying
 *  lists of users.) In this case, we use the admin_externalpage object. This object
 *  places a link to an external PHP file in the admin tree block.
 *
 *  If you're using an admin_externalpage object for some settings, you can take
 *  advantage of the admin_externalpage_* functions. For example, suppose you wanted
 *  to add a foo.php file into admin. First off, you add the following line to
 *  admin/settings/first.php (at the end of the file) or to some other file in
 *  admin/settings:
 * <code>
 *     $ADMIN->add('userinterface', new admin_externalpage('foo', get_string('foo'),
 *         $CFG->wwwdir . '/' . '$CFG->admin . '/foo.php', 'some_role_permission'));
 * </code>
 *
 *  Next, in foo.php, your file structure would resemble the following:
 * <code>
 *         require(__DIR__.'/../../config.php');
 *         require_once($CFG->libdir.'/adminlib.php');
 *         admin_externalpage_setup('foo');
 *         // functionality like processing form submissions goes here
 *         echo $OUTPUT->header();
 *         // your HTML goes here
 *         echo $OUTPUT->footer();
 * </code>
 *
 *  The admin_externalpage_setup() function call ensures the user is logged in,
 *  and makes sure that they have the proper role permission to access the page.
 *  It also configures all $PAGE properties needed for navigation.
 *
 *  ADMIN_CATEGORY OBJECTS
 *
 *  Above and beyond all this, we have admin_category objects. These objects
 *  appear as folders in the admin tree block. They contain admin_settingpage's,
 *  admin_externalpage's, and other admin_category's.
 *
 *  OTHER NOTES
 *
 *  admin_settingpage's, admin_externalpage's, and admin_category's all inherit
 *  from part_of_admin_tree (a pseudointerface). This interface insists that
 *  a class has a check_access method for access permissions, a locate method
 *  used to find a specific node in the admin tree and find parent path.
 *
 *  admin_category's inherit from parentable_part_of_admin_tree. This pseudo-
 *  interface ensures that the class implements a recursive add function which
 *  accepts a part_of_admin_tree object and searches for the proper place to
 *  put it. parentable_part_of_admin_tree implies part_of_admin_tree.
 *
 *  Please note that the $this->name field of any part_of_admin_tree must be
 *  UNIQUE throughout the ENTIRE admin tree.
 *
 *  The $this->name field of an admin_setting object (which is *not* part_of_
 *  admin_tree) must be unique on the respective admin_settingpage where it is
 *  used.
 *
 * Original author: Vincenzo K. Marcovecchio
 * Maintainer:      Petr Skoda
 *
 * @package    core
 * @subpackage admin
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/// Add libraries
require_once($CFG->libdir.'/ddllib.php');
require_once($CFG->libdir.'/xmlize.php');
require_once($CFG->libdir.'/messagelib.php');

define('INSECURE_DATAROOT_WARNING', 1);
define('INSECURE_DATAROOT_ERROR', 2);

/**
 * Automatically clean-up all plugin data and remove the plugin DB tables
 *
 * NOTE: do not call directly, use new /admin/plugins.php?uninstall=component instead!
 *
 * @param string $type The plugin type, eg. 'mod', 'qtype', 'workshopgrading' etc.
 * @param string $name The plugin name, eg. 'forum', 'multichoice', 'accumulative' etc.
 * @uses global $OUTPUT to produce notices and other messages
 * @return void
 */
function uninstall_plugin($type, $name) {
    global $CFG, $DB, $OUTPUT;

    // This may take a long time.
    core_php_time_limit::raise();

    // Recursively uninstall all subplugins first.
    $subplugintypes = core_component::get_plugin_types_with_subplugins();
    if (isset($subplugintypes[$type])) {
        $base = core_component::get_plugin_directory($type, $name);

        $subpluginsfile = "{$base}/db/subplugins.json";
        if (file_exists($subpluginsfile)) {
            $subplugins = (array) json_decode(file_get_contents($subpluginsfile))->plugintypes;
        } else if (file_exists("{$base}/db/subplugins.php")) {
            debugging('Use of subplugins.php has been deprecated. ' .
                    'Please update your plugin to provide a subplugins.json file instead.',
                    DEBUG_DEVELOPER);
            $subplugins = [];
            include("{$base}/db/subplugins.php");
        }

        if (!empty($subplugins)) {
            foreach (array_keys($subplugins) as $subplugintype) {
                $instances = core_component::get_plugin_list($subplugintype);
                foreach ($instances as $subpluginname => $notusedpluginpath) {
                    uninstall_plugin($subplugintype, $subpluginname);
                }
            }
        }
    }

    $component = $type . '_' . $name;  // eg. 'qtype_multichoice' or 'workshopgrading_accumulative' or 'mod_forum'

    if ($type === 'mod') {
        $pluginname = $name;  // eg. 'forum'
        if (get_string_manager()->string_exists('modulename', $component)) {
            $strpluginname = get_string('modulename', $component);
        } else {
            $strpluginname = $component;
        }

    } else {
        $pluginname = $component;
        if (get_string_manager()->string_exists('pluginname', $component)) {
            $strpluginname = get_string('pluginname', $component);
        } else {
            $strpluginname = $component;
        }
    }

    echo $OUTPUT->heading($pluginname);

    // Delete all tag areas, collections and instances associated with this plugin.
    core_tag_area::uninstall($component);

    // Custom plugin uninstall.
    $plugindirectory = core_component::get_plugin_directory($type, $name);
    $uninstalllib = $plugindirectory . '/db/uninstall.php';
    if (file_exists($uninstalllib)) {
        require_once($uninstalllib);
        $uninstallfunction = 'xmldb_' . $pluginname . '_uninstall';    // eg. 'xmldb_workshop_uninstall()'
        if (function_exists($uninstallfunction)) {
            // Do not verify result, let plugin complain if necessary.
            $uninstallfunction();
        }
    }

    // Specific plugin type cleanup.
    $plugininfo = core_plugin_manager::instance()->get_plugin_info($component);
    if ($plugininfo) {
        $plugininfo->uninstall_cleanup();
        core_plugin_manager::reset_caches();
    }
    $plugininfo = null;

    // perform clean-up task common for all the plugin/subplugin types

    //delete the web service functions and pre-built services
    require_once($CFG->dirroot.'/lib/externallib.php');
    external_delete_descriptions($component);

    // delete calendar events
    $DB->delete_records('event', array('modulename' => $pluginname));

    // Delete scheduled tasks.
    $DB->delete_records('task_scheduled', array('component' => $component));

    // Delete Inbound Message datakeys.
    $DB->delete_records_select('messageinbound_datakeys',
            'handler IN (SELECT id FROM {messageinbound_handlers} WHERE component = ?)', array($component));

    // Delete Inbound Message handlers.
    $DB->delete_records('messageinbound_handlers', array('component' => $component));

    // delete all the logs
    $DB->delete_records('log', array('module' => $pluginname));

    // delete log_display information
    $DB->delete_records('log_display', array('component' => $component));

    // delete the module configuration records
    unset_all_config_for_plugin($component);
    if ($type === 'mod') {
        unset_all_config_for_plugin($pluginname);
    }

    // delete message provider
    message_provider_uninstall($component);

    // delete the plugin tables
    $xmldbfilepath = $plugindirectory . '/db/install.xml';
    drop_plugin_tables($component, $xmldbfilepath, false);
    if ($type === 'mod' or $type === 'block') {
        // non-frankenstyle table prefixes
        drop_plugin_tables($name, $xmldbfilepath, false);
    }

    // delete the capabilities that were defined by this module
    capabilities_cleanup($component);

    // Delete all remaining files in the filepool owned by the component.
    $fs = get_file_storage();
    $fs->delete_component_files($component);

    // Finally purge all caches.
    purge_all_caches();

    // Invalidate the hash used for upgrade detections.
    set_config('allversionshash', '');

    echo $OUTPUT->notification(get_string('success'), 'notifysuccess');
}

/**
 * Returns the version of installed component
 *
 * @param string $component component name
 * @param string $source either 'disk' or 'installed' - where to get the version information from
 * @return string|bool version number or false if the component is not found
 */
function get_component_version($component, $source='installed') {
    global $CFG, $DB;

    list($type, $name) = core_component::normalize_component($component);

    // moodle core or a core subsystem
    if ($type === 'core') {
        if ($source === 'installed') {
            if (empty($CFG->version)) {
                return false;
            } else {
                return $CFG->version;
            }
        } else {
            if (!is_readable($CFG->dirroot.'/version.php')) {
                return false;
            } else {
                $version = null; //initialize variable for IDEs
                include($CFG->dirroot.'/version.php');
                return $version;
            }
        }
    }

    // activity module
    if ($type === 'mod') {
        if ($source === 'installed') {
            if ($CFG->version < 2013092001.02) {
                return $DB->get_field('modules', 'version', array('name'=>$name));
            } else {
                return get_config('mod_'.$name, 'version');
            }

        } else {
            $mods = core_component::get_plugin_list('mod');
            if (empty($mods[$name]) or !is_readable($mods[$name].'/version.php')) {
                return false;
            } else {
                $plugin = new stdClass();
                $plugin->version = null;
                $module = $plugin;
                include($mods[$name].'/version.php');
                return $plugin->version;
            }
        }
    }

    // block
    if ($type === 'block') {
        if ($source === 'installed') {
            if ($CFG->version < 2013092001.02) {
                return $DB->get_field('block', 'version', array('name'=>$name));
            } else {
                return get_config('block_'.$name, 'version');
            }
        } else {
            $blocks = core_component::get_plugin_list('block');
            if (empty($blocks[$name]) or !is_readable($blocks[$name].'/version.php')) {
                return false;
            } else {
                $plugin = new stdclass();
                include($blocks[$name].'/version.php');
                return $plugin->version;
            }
        }
    }

    // all other plugin types
    if ($source === 'installed') {
        return get_config($type.'_'.$name, 'version');
    } else {
        $plugins = core_component::get_plugin_list($type);
        if (empty($plugins[$name])) {
            return false;
        } else {
            $plugin = new stdclass();
            include($plugins[$name].'/version.php');
            return $plugin->version;
        }
    }
}

/**
 * Delete all plugin tables
 *
 * @param string $name Name of plugin, used as table prefix
 * @param string $file Path to install.xml file
 * @param bool $feedback defaults to true
 * @return bool Always returns true
 */
function drop_plugin_tables($name, $file, $feedback=true) {
    global $CFG, $DB;

    // first try normal delete
    if (file_exists($file) and $DB->get_manager()->delete_tables_from_xmldb_file($file)) {
        return true;
    }

    // then try to find all tables that start with name and are not in any xml file
    $used_tables = get_used_table_names();

    $tables = $DB->get_tables();

    /// Iterate over, fixing id fields as necessary
    foreach ($tables as $table) {
        if (in_array($table, $used_tables)) {
            continue;
        }

        if (strpos($table, $name) !== 0) {
            continue;
        }

        // found orphan table --> delete it
        if ($DB->get_manager()->table_exists($table)) {
            $xmldb_table = new xmldb_table($table);
            $DB->get_manager()->drop_table($xmldb_table);
        }
    }

    return true;
}

/**
 * Returns names of all known tables == tables that moodle knows about.
 *
 * @return array Array of lowercase table names
 */
function get_used_table_names() {
    $table_names = array();
    $dbdirs = get_db_directories();

    foreach ($dbdirs as $dbdir) {
        $file = $dbdir.'/install.xml';

        $xmldb_file = new xmldb_file($file);

        if (!$xmldb_file->fileExists()) {
            continue;
        }

        $loaded    = $xmldb_file->loadXMLStructure();
        $structure = $xmldb_file->getStructure();

        if ($loaded and $tables = $structure->getTables()) {
            foreach($tables as $table) {
                $table_names[] = strtolower($table->getName());
            }
        }
    }

    return $table_names;
}

/**
 * Returns list of all directories where we expect install.xml files
 * @return array Array of paths
 */
function get_db_directories() {
    global $CFG;

    $dbdirs = array();

    /// First, the main one (lib/db)
    $dbdirs[] = $CFG->libdir.'/db';

    /// Then, all the ones defined by core_component::get_plugin_types()
    $plugintypes = core_component::get_plugin_types();
    foreach ($plugintypes as $plugintype => $pluginbasedir) {
        if ($plugins = core_component::get_plugin_list($plugintype)) {
            foreach ($plugins as $plugin => $plugindir) {
                $dbdirs[] = $plugindir.'/db';
            }
        }
    }

    return $dbdirs;
}

/**
 * Try to obtain or release the cron lock.
 * @param string  $name  name of lock
 * @param int  $until timestamp when this lock considered stale, null means remove lock unconditionally
 * @param bool $ignorecurrent ignore current lock state, usually extend previous lock, defaults to false
 * @return bool true if lock obtained
 */
function set_cron_lock($name, $until, $ignorecurrent=false) {
    global $DB;
    if (empty($name)) {
        debugging("Tried to get a cron lock for a null fieldname");
        return false;
    }

    // remove lock by force == remove from config table
    if (is_null($until)) {
        set_config($name, null);
        return true;
    }

    if (!$ignorecurrent) {
        // read value from db - other processes might have changed it
        $value = $DB->get_field('config', 'value', array('name'=>$name));

        if ($value and $value > time()) {
            //lock active
            return false;
        }
    }

    set_config($name, $until);
    return true;
}

/**
 * Test if and critical warnings are present
 * @return bool
 */
function admin_critical_warnings_present() {
    global $SESSION;

    if (!has_capability('moodle/site:config', context_system::instance())) {
        return 0;
    }

    if (!isset($SESSION->admin_critical_warning)) {
        $SESSION->admin_critical_warning = 0;
        if (is_dataroot_insecure(true) === INSECURE_DATAROOT_ERROR) {
            $SESSION->admin_critical_warning = 1;
        }
    }

    return $SESSION->admin_critical_warning;
}

/**
 * Detects if float supports at least 10 decimal digits
 *
 * Detects if float supports at least 10 decimal digits
 * and also if float-->string conversion works as expected.
 *
 * @return bool true if problem found
 */
function is_float_problem() {
    $num1 = 2009010200.01;
    $num2 = 2009010200.02;

    return ((string)$num1 === (string)$num2 or $num1 === $num2 or $num2 <= (string)$num1);
}

/**
 * Try to verify that dataroot is not accessible from web.
 *
 * Try to verify that dataroot is not accessible from web.
 * It is not 100% correct but might help to reduce number of vulnerable sites.
 * Protection from httpd.conf and .htaccess is not detected properly.
 *
 * @uses INSECURE_DATAROOT_WARNING
 * @uses INSECURE_DATAROOT_ERROR
 * @param bool $fetchtest try to test public access by fetching file, default false
 * @return mixed empty means secure, INSECURE_DATAROOT_ERROR found a critical problem, INSECURE_DATAROOT_WARNING might be problematic
 */
function is_dataroot_insecure($fetchtest=false) {
    global $CFG;

    $siteroot = str_replace('\\', '/', strrev($CFG->dirroot.'/')); // win32 backslash workaround

    $rp = preg_replace('|https?://[^/]+|i', '', $CFG->wwwroot, 1);
    $rp = strrev(trim($rp, '/'));
    $rp = explode('/', $rp);
    foreach($rp as $r) {
        if (strpos($siteroot, '/'.$r.'/') === 0) {
            $siteroot = substr($siteroot, strlen($r)+1); // moodle web in subdirectory
        } else {
            break; // probably alias root
        }
    }

    $siteroot = strrev($siteroot);
    $dataroot = str_replace('\\', '/', $CFG->dataroot.'/');

    if (strpos($dataroot, $siteroot) !== 0) {
        return false;
    }

    if (!$fetchtest) {
        return INSECURE_DATAROOT_WARNING;
    }

    // now try all methods to fetch a test file using http protocol

    $httpdocroot = str_replace('\\', '/', strrev($CFG->dirroot.'/'));
    preg_match('|(https?://[^/]+)|i', $CFG->wwwroot, $matches);
    $httpdocroot = $matches[1];
    $datarooturl = $httpdocroot.'/'. substr($dataroot, strlen($siteroot));
    make_upload_directory('diag');
    $testfile = $CFG->dataroot.'/diag/public.txt';
    if (!file_exists($testfile)) {
        file_put_contents($testfile, 'test file, do not delete');
        @chmod($testfile, $CFG->filepermissions);
    }
    $teststr = trim(file_get_contents($testfile));
    if (empty($teststr)) {
    // hmm, strange
        return INSECURE_DATAROOT_WARNING;
    }

    $testurl = $datarooturl.'/diag/public.txt';
    if (extension_loaded('curl') and
        !(stripos(ini_get('disable_functions'), 'curl_init') !== FALSE) and
        !(stripos(ini_get('disable_functions'), 'curl_setop') !== FALSE) and
        ($ch = @curl_init($testurl)) !== false) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        if (!curl_errno($ch)) {
            $data = trim($data);
            if ($data === $teststr) {
                curl_close($ch);
                return INSECURE_DATAROOT_ERROR;
            }
        }
        curl_close($ch);
    }

    if ($data = @file_get_contents($testurl)) {
        $data = trim($data);
        if ($data === $teststr) {
            return INSECURE_DATAROOT_ERROR;
        }
    }

    preg_match('|https?://([^/]+)|i', $testurl, $matches);
    $sitename = $matches[1];
    $error = 0;
    if ($fp = @fsockopen($sitename, 80, $error)) {
        preg_match('|https?://[^/]+(.*)|i', $testurl, $matches);
        $localurl = $matches[1];
        $out = "GET $localurl HTTP/1.1\r\n";
        $out .= "Host: $sitename\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        $data = '';
        $incoming = false;
        while (!feof($fp)) {
            if ($incoming) {
                $data .= fgets($fp, 1024);
            } else if (@fgets($fp, 1024) === "\r\n") {
                    $incoming = true;
                }
        }
        fclose($fp);
        $data = trim($data);
        if ($data === $teststr) {
            return INSECURE_DATAROOT_ERROR;
        }
    }

    return INSECURE_DATAROOT_WARNING;
}

/**
 * Enables CLI maintenance mode by creating new dataroot/climaintenance.html file.
 */
function enable_cli_maintenance_mode() {
    global $CFG;

    if (file_exists("$CFG->dataroot/climaintenance.html")) {
        unlink("$CFG->dataroot/climaintenance.html");
    }

    if (isset($CFG->maintenance_message) and !html_is_blank($CFG->maintenance_message)) {
        $data = $CFG->maintenance_message;
        $data = bootstrap_renderer::early_error_content($data, null, null, null);
        $data = bootstrap_renderer::plain_page(get_string('sitemaintenance', 'admin'), $data);

    } else if (file_exists("$CFG->dataroot/climaintenance.template.html")) {
        $data = file_get_contents("$CFG->dataroot/climaintenance.template.html");

    } else {
        $data = get_string('sitemaintenance', 'admin');
        $data = bootstrap_renderer::early_error_content($data, null, null, null);
        $data = bootstrap_renderer::plain_page(get_string('sitemaintenance', 'admin'), $data);
    }

    file_put_contents("$CFG->dataroot/climaintenance.html", $data);
    chmod("$CFG->dataroot/climaintenance.html", $CFG->filepermissions);
}

/// CLASS DEFINITIONS /////////////////////////////////////////////////////////


/**
 * Interface for anything appearing in the admin tree
 *
 * The interface that is implemented by anything that appears in the admin tree
 * block. It forces inheriting classes to define a method for checking user permissions
 * and methods for finding something in the admin tree.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface part_of_admin_tree {

/**
 * Finds a named part_of_admin_tree.
 *
 * Used to find a part_of_admin_tree. If a class only inherits part_of_admin_tree
 * and not parentable_part_of_admin_tree, then this function should only check if
 * $this->name matches $name. If it does, it should return a reference to $this,
 * otherwise, it should return a reference to NULL.
 *
 * If a class inherits parentable_part_of_admin_tree, this method should be called
 * recursively on all child objects (assuming, of course, the parent object's name
 * doesn't match the search criterion).
 *
 * @param string $name The internal name of the part_of_admin_tree we're searching for.
 * @return mixed An object reference or a NULL reference.
 */
    public function locate($name);

    /**
     * Removes named part_of_admin_tree.
     *
     * @param string $name The internal name of the part_of_admin_tree we want to remove.
     * @return bool success.
     */
    public function prune($name);

    /**
     * Search using query
     * @param string $query
     * @return mixed array-object structure of found settings and pages
     */
    public function search($query);

    /**
     * Verifies current user's access to this part_of_admin_tree.
     *
     * Used to check if the current user has access to this part of the admin tree or
     * not. If a class only inherits part_of_admin_tree and not parentable_part_of_admin_tree,
     * then this method is usually just a call to has_capability() in the site context.
     *
     * If a class inherits parentable_part_of_admin_tree, this method should return the
     * logical OR of the return of check_access() on all child objects.
     *
     * @return bool True if the user has access, false if she doesn't.
     */
    public function check_access();

    /**
     * Mostly useful for removing of some parts of the tree in admin tree block.
     *
     * @return True is hidden from normal list view
     */
    public function is_hidden();

    /**
     * Show we display Save button at the page bottom?
     * @return bool
     */
    public function show_save();
}


/**
 * Interface implemented by any part_of_admin_tree that has children.
 *
 * The interface implemented by any part_of_admin_tree that can be a parent
 * to other part_of_admin_tree's. (For now, this only includes admin_category.) Apart
 * from ensuring part_of_admin_tree compliancy, it also ensures inheriting methods
 * include an add method for adding other part_of_admin_tree objects as children.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface parentable_part_of_admin_tree extends part_of_admin_tree {

/**
 * Adds a part_of_admin_tree object to the admin tree.
 *
 * Used to add a part_of_admin_tree object to this object or a child of this
 * object. $something should only be added if $destinationname matches
 * $this->name. If it doesn't, add should be called on child objects that are
 * also parentable_part_of_admin_tree's.
 *
 * $something should be appended as the last child in the $destinationname. If the
 * $beforesibling is specified, $something should be prepended to it. If the given
 * sibling is not found, $something should be appended to the end of $destinationname
 * and a developer debugging message should be displayed.
 *
 * @param string $destinationname The internal name of the new parent for $something.
 * @param part_of_admin_tree $something The object to be added.
 * @return bool True on success, false on failure.
 */
    public function add($destinationname, $something, $beforesibling = null);

}


/**
 * The object used to represent folders (a.k.a. categories) in the admin tree block.
 *
 * Each admin_category object contains a number of part_of_admin_tree objects.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_category implements parentable_part_of_admin_tree {

    /** @var part_of_admin_tree[] An array of part_of_admin_tree objects that are this object's children */
    protected $children;
    /** @var string An internal name for this category. Must be unique amongst ALL part_of_admin_tree objects */
    public $name;
    /** @var string The displayed name for this category. Usually obtained through get_string() */
    public $visiblename;
    /** @var bool Should this category be hidden in admin tree block? */
    public $hidden;
    /** @var mixed Either a string or an array or strings */
    public $path;
    /** @var mixed Either a string or an array or strings */
    public $visiblepath;

    /** @var array fast lookup category cache, all categories of one tree point to one cache */
    protected $category_cache;

    /** @var bool If set to true children will be sorted when calling {@link admin_category::get_children()} */
    protected $sort = false;
    /** @var bool If set to true children will be sorted in ascending order. */
    protected $sortasc = true;
    /** @var bool If set to true sub categories and pages will be split and then sorted.. */
    protected $sortsplit = true;
    /** @var bool $sorted True if the children have been sorted and don't need resorting */
    protected $sorted = false;

    /**
     * Constructor for an empty admin category
     *
     * @param string $name The internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     * @param string $visiblename The displayed named for this category. Usually obtained through get_string()
     * @param bool $hidden hide category in admin tree block, defaults to false
     */
    public function __construct($name, $visiblename, $hidden=false) {
        $this->children    = array();
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->hidden      = $hidden;
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @param bool $findpath initialize path and visiblepath arrays
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     *                  defaults to false
     */
    public function locate($name, $findpath=false) {
        if (!isset($this->category_cache[$this->name])) {
            // somebody much have purged the cache
            $this->category_cache[$this->name] = $this;
        }

        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath[] = $this->visiblename;
                $this->path[]        = $this->name;
            }
            return $this;
        }

        // quick category lookup
        if (!$findpath and isset($this->category_cache[$name])) {
            return $this->category_cache[$name];
        }

        $return = NULL;
        foreach($this->children as $childid=>$unused) {
            if ($return = $this->children[$childid]->locate($name, $findpath)) {
                break;
            }
        }

        if (!is_null($return) and $findpath) {
            $return->visiblepath[] = $this->visiblename;
            $return->path[]        = $this->name;
        }

        return $return;
    }

    /**
     * Search using query
     *
     * @param string query
     * @return mixed array-object structure of found settings and pages
     */
    public function search($query) {
        $result = array();
        foreach ($this->get_children() as $child) {
            $subsearch = $child->search($query);
            if (!is_array($subsearch)) {
                debugging('Incorrect search result from '.$child->name);
                continue;
            }
            $result = array_merge($result, $subsearch);
        }
        return $result;
    }

    /**
     * Removes part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want to remove.
     * @return bool success
     */
    public function prune($name) {

        if ($this->name == $name) {
            return false;  //can not remove itself
        }

        foreach($this->children as $precedence => $child) {
            if ($child->name == $name) {
                // clear cache and delete self
                while($this->category_cache) {
                    // delete the cache, but keep the original array address
                    array_pop($this->category_cache);
                }
                unset($this->children[$precedence]);
                return true;
            } else if ($this->children[$precedence]->prune($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds a part_of_admin_tree to a child or grandchild (or great-grandchild, and so forth) of this object.
     *
     * By default the new part of the tree is appended as the last child of the parent. You
     * can specify a sibling node that the new part should be prepended to. If the given
     * sibling is not found, the part is appended to the end (as it would be by default) and
     * a developer debugging message is displayed.
     *
     * @throws coding_exception if the $beforesibling is empty string or is not string at all.
     * @param string $destinationame The internal name of the immediate parent that we want for $something.
     * @param mixed $something A part_of_admin_tree or setting instance to be added.
     * @param string $beforesibling The name of the parent's child the $something should be prepended to.
     * @return bool True if successfully added, false if $something can not be added.
     */
    public function add($parentname, $something, $beforesibling = null) {
        global $CFG;

        $parent = $this->locate($parentname);
        if (is_null($parent)) {
            debugging('parent does not exist!');
            return false;
        }

        if ($something instanceof part_of_admin_tree) {
            if (!($parent instanceof parentable_part_of_admin_tree)) {
                debugging('error - parts of tree can be inserted only into parentable parts');
                return false;
            }
            if ($CFG->debugdeveloper && !is_null($this->locate($something->name))) {
                // The name of the node is already used, simply warn the developer that this should not happen.
                // It is intentional to check for the debug level before performing the check.
                debugging('Duplicate admin page name: ' . $something->name, DEBUG_DEVELOPER);
            }
            if (is_null($beforesibling)) {
                // Append $something as the parent's last child.
                $parent->children[] = $something;
            } else {
                if (!is_string($beforesibling) or trim($beforesibling) === '') {
                    throw new coding_exception('Unexpected value of the beforesibling parameter');
                }
                // Try to find the position of the sibling.
                $siblingposition = null;
                foreach ($parent->children as $childposition => $child) {
                    if ($child->name === $beforesibling) {
                        $siblingposition = $childposition;
                        break;
                    }
                }
                if (is_null($siblingposition)) {
                    debugging('Sibling '.$beforesibling.' not found', DEBUG_DEVELOPER);
                    $parent->children[] = $something;
                } else {
                    $parent->children = array_merge(
                        array_slice($parent->children, 0, $siblingposition),
                        array($something),
                        array_slice($parent->children, $siblingposition)
                    );
                }
            }
            if ($something instanceof admin_category) {
                if (isset($this->category_cache[$something->name])) {
                    debugging('Duplicate admin category name: '.$something->name);
                } else {
                    $this->category_cache[$something->name] = $something;
                    $something->category_cache =& $this->category_cache;
                    foreach ($something->children as $child) {
                        // just in case somebody already added subcategories
                        if ($child instanceof admin_category) {
                            if (isset($this->category_cache[$child->name])) {
                                debugging('Duplicate admin category name: '.$child->name);
                            } else {
                                $this->category_cache[$child->name] = $child;
                                $child->category_cache =& $this->category_cache;
                            }
                        }
                    }
                }
            }
            return true;

        } else {
            debugging('error - can not add this element');
            return false;
        }

    }

    /**
     * Checks if the user has access to anything in this category.
     *
     * @return bool True if the user has access to at least one child in this category, false otherwise.
     */
    public function check_access() {
        foreach ($this->children as $child) {
            if ($child->check_access()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is this category hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    public function is_hidden() {
        return $this->hidden;
    }

    /**
     * Show we display Save button at the page bottom?
     * @return bool
     */
    public function show_save() {
        foreach ($this->children as $child) {
            if ($child->show_save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sets sorting on this category.
     *
     * Please note this function doesn't actually do the sorting.
     * It can be called anytime.
     * Sorting occurs when the user calls get_children.
     * Code using the children array directly won't see the sorted results.
     *
     * @param bool $sort If set to true children will be sorted, if false they won't be.
     * @param bool $asc If true sorting will be ascending, otherwise descending.
     * @param bool $split If true we sort pages and sub categories separately.
     */
    public function set_sorting($sort, $asc = true, $split = true) {
        $this->sort = (bool)$sort;
        $this->sortasc = (bool)$asc;
        $this->sortsplit = (bool)$split;
    }

    /**
     * Returns the children associated with this category.
     *
     * @return part_of_admin_tree[]
     */
    public function get_children() {
        // If we should sort and it hasn't already been sorted.
        if ($this->sort && !$this->sorted) {
            if ($this->sortsplit) {
                $categories = array();
                $pages = array();
                foreach ($this->children as $child) {
                    if ($child instanceof admin_category) {
                        $categories[] = $child;
                    } else {
                        $pages[] = $child;
                    }
                }
                core_collator::asort_objects_by_property($categories, 'visiblename');
                core_collator::asort_objects_by_property($pages, 'visiblename');
                if (!$this->sortasc) {
                    $categories = array_reverse($categories);
                    $pages = array_reverse($pages);
                }
                $this->children = array_merge($pages, $categories);
            } else {
                core_collator::asort_objects_by_property($this->children, 'visiblename');
                if (!$this->sortasc) {
                    $this->children = array_reverse($this->children);
                }
            }
            $this->sorted = true;
        }
        return $this->children;
    }

    /**
     * Magically gets a property from this object.
     *
     * @param $property
     * @return part_of_admin_tree[]
     * @throws coding_exception
     */
    public function __get($property) {
        if ($property === 'children') {
            return $this->get_children();
        }
        throw new coding_exception('Invalid property requested.');
    }

    /**
     * Magically sets a property against this object.
     *
     * @param string $property
     * @param mixed $value
     * @throws coding_exception
     */
    public function __set($property, $value) {
        if ($property === 'children') {
            $this->sorted = false;
            $this->children = $value;
        } else {
            throw new coding_exception('Invalid property requested.');
        }
    }

    /**
     * Checks if an inaccessible property is set.
     *
     * @param string $property
     * @return bool
     * @throws coding_exception
     */
    public function __isset($property) {
        if ($property === 'children') {
            return isset($this->children);
        }
        throw new coding_exception('Invalid property requested.');
    }
}


/**
 * Root of admin settings tree, does not have any parent.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_root extends admin_category {
/** @var array List of errors */
    public $errors;
    /** @var string search query */
    public $search;
    /** @var bool full tree flag - true means all settings required, false only pages required */
    public $fulltree;
    /** @var bool flag indicating loaded tree */
    public $loaded;
    /** @var mixed site custom defaults overriding defaults in settings files*/
    public $custom_defaults;

    /**
     * @param bool $fulltree true means all settings required,
     *                            false only pages required
     */
    public function __construct($fulltree) {
        global $CFG;

        parent::__construct('root', get_string('administration'), false);
        $this->errors   = array();
        $this->search   = '';
        $this->fulltree = $fulltree;
        $this->loaded   = false;

        $this->category_cache = array();

        // load custom defaults if found
        $this->custom_defaults = null;
        $defaultsfile = "$CFG->dirroot/local/defaults.php";
        if (is_readable($defaultsfile)) {
            $defaults = array();
            include($defaultsfile);
            if (is_array($defaults) and count($defaults)) {
                $this->custom_defaults = $defaults;
            }
        }
    }

    /**
     * Empties children array, and sets loaded to false
     *
     * @param bool $requirefulltree
     */
    public function purge_children($requirefulltree) {
        $this->children = array();
        $this->fulltree = ($requirefulltree || $this->fulltree);
        $this->loaded   = false;
        //break circular dependencies - this helps PHP 5.2
        while($this->category_cache) {
            array_pop($this->category_cache);
        }
        $this->category_cache = array();
    }
}


/**
 * Links external PHP pages into the admin tree.
 *
 * See detailed usage example at the top of this document (adminlib.php)
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_externalpage implements part_of_admin_tree {

    /** @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects */
    public $name;

    /** @var string The displayed name for this external page. Usually obtained through get_string(). */
    public $visiblename;

    /** @var string The external URL that we should link to when someone requests this external page. */
    public $url;

    /** @var string The role capability/permission a user must have to access this external page. */
    public $req_capability;

    /** @var object The context in which capability/permission should be checked, default is site context. */
    public $context;

    /** @var bool hidden in admin tree block. */
    public $hidden;

    /** @var mixed either string or array of string */
    public $path;

    /** @var array list of visible names of page parents */
    public $visiblepath;

    /**
     * Constructor for adding an external page into the admin tree.
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param string $url The external URL that we should link to when someone requests this external page.
     * @param mixed $req_capability The role capability/permission a user must have to access this external page. Defaults to 'moodle/site:config'.
     * @param boolean $hidden Is this external page hidden in admin tree block? Default false.
     * @param stdClass $context The context the page relates to. Not sure what happens
     *      if you specify something other than system or front page. Defaults to system.
     */
    public function __construct($name, $visiblename, $url, $req_capability='moodle/site:config', $hidden=false, $context=NULL) {
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->url         = $url;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden = $hidden;
        $this->context = $context;
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @param bool $findpath defaults to false
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     */
    public function locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath = array($this->visiblename);
                $this->path        = array($this->name);
            }
            return $this;
        } else {
            $return = NULL;
            return $return;
        }
    }

    /**
     * This function always returns false, required function by interface
     *
     * @param string $name
     * @return false
     */
    public function prune($name) {
        return false;
    }

    /**
     * Search using query
     *
     * @param string $query
     * @return mixed array-object structure of found settings and pages
     */
    public function search($query) {
        $found = false;
        if (strpos(strtolower($this->name), $query) !== false) {
            $found = true;
        } else if (strpos(core_text::strtolower($this->visiblename), $query) !== false) {
                $found = true;
            }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }

    /**
     * Determines if the current user has access to this external page based on $this->req_capability.
     *
     * @return bool True if user has access, false otherwise.
     */
    public function check_access() {
        global $CFG;
        $context = empty($this->context) ? context_system::instance() : $this->context;
        foreach($this->req_capability as $cap) {
            if (has_capability($cap, $context)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is this external page hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    public function is_hidden() {
        return $this->hidden;
    }

    /**
     * Show we display Save button at the page bottom?
     * @return bool
     */
    public function show_save() {
        return false;
    }
}

/**
 * Used to store details of the dependency between two settings elements.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017 Davo Smith, Synergy Learning
 */
class admin_settingdependency {
    /** @var string the name of the setting to be shown/hidden */
    public $settingname;
    /** @var string the setting this is dependent on */
    public $dependenton;
    /** @var string the condition to show/hide the element */
    public $condition;
    /** @var string the value to compare against */
    public $value;

    /** @var string[] list of valid conditions */
    private static $validconditions = ['checked', 'notchecked', 'noitemselected', 'eq', 'neq', 'in'];

    /**
     * admin_settingdependency constructor.
     * @param string $settingname
     * @param string $dependenton
     * @param string $condition
     * @param string $value
     * @throws \coding_exception
     */
    public function __construct($settingname, $dependenton, $condition, $value) {
        $this->settingname = $this->parse_name($settingname);
        $this->dependenton = $this->parse_name($dependenton);
        $this->condition = $condition;
        $this->value = $value;

        if (!in_array($this->condition, self::$validconditions)) {
            throw new coding_exception("Invalid condition '$condition'");
        }
    }

    /**
     * Convert the setting name into the form field name.
     * @param string $name
     * @return string
     */
    private function parse_name($name) {
        $bits = explode('/', $name);
        $name = array_pop($bits);
        $plugin = '';
        if ($bits) {
            $plugin = array_pop($bits);
            if ($plugin === 'moodle') {
                $plugin = '';
            }
        }
        return 's_'.$plugin.'_'.$name;
    }

    /**
     * Gather together all the dependencies in a format suitable for initialising javascript
     * @param admin_settingdependency[] $dependencies
     * @return array
     */
    public static function prepare_for_javascript($dependencies) {
        $result = [];
        foreach ($dependencies as $d) {
            if (!isset($result[$d->dependenton])) {
                $result[$d->dependenton] = [];
            }
            if (!isset($result[$d->dependenton][$d->condition])) {
                $result[$d->dependenton][$d->condition] = [];
            }
            if (!isset($result[$d->dependenton][$d->condition][$d->value])) {
                $result[$d->dependenton][$d->condition][$d->value] = [];
            }
            $result[$d->dependenton][$d->condition][$d->value][] = $d->settingname;
        }
        return $result;
    }
}

/**
 * Used to group a number of admin_setting objects into a page and add them to the admin tree.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_settingpage implements part_of_admin_tree {

    /** @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects */
    public $name;

    /** @var string The displayed name for this external page. Usually obtained through get_string(). */
    public $visiblename;

    /** @var mixed An array of admin_setting objects that are part of this setting page. */
    public $settings;

    /** @var admin_settingdependency[] list of settings to hide when certain conditions are met */
    protected $dependencies = [];

    /** @var string The role capability/permission a user must have to access this external page. */
    public $req_capability;

    /** @var object The context in which capability/permission should be checked, default is site context. */
    public $context;

    /** @var bool hidden in admin tree block. */
    public $hidden;

    /** @var mixed string of paths or array of strings of paths */
    public $path;

    /** @var array list of visible names of page parents */
    public $visiblepath;

    /**
     * see admin_settingpage for details of this function
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param mixed $req_capability The role capability/permission a user must have to access this external page. Defaults to 'moodle/site:config'.
     * @param boolean $hidden Is this external page hidden in admin tree block? Default false.
     * @param stdClass $context The context the page relates to. Not sure what happens
     *      if you specify something other than system or front page. Defaults to system.
     */
    public function __construct($name, $visiblename, $req_capability='moodle/site:config', $hidden=false, $context=NULL) {
        $this->settings    = new stdClass();
        $this->name        = $name;
        $this->visiblename = $visiblename;
        if (is_array($req_capability)) {
            $this->req_capability = $req_capability;
        } else {
            $this->req_capability = array($req_capability);
        }
        $this->hidden      = $hidden;
        $this->context     = $context;
    }

    /**
     * see admin_category
     *
     * @param string $name
     * @param bool $findpath
     * @return mixed Object (this) if name ==  this->name, else returns null
     */
    public function locate($name, $findpath=false) {
        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath = array($this->visiblename);
                $this->path        = array($this->name);
            }
            return $this;
        } else {
            $return = NULL;
            return $return;
        }
    }

    /**
     * Search string in settings page.
     *
     * @param string $query
     * @return array
     */
    public function search($query) {
        $found = array();

        foreach ($this->settings as $setting) {
            if ($setting->is_related($query)) {
                $found[] = $setting;
            }
        }

        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = $found;
            return array($this->name => $result);
        }

        $found = false;
        if (strpos(strtolower($this->name), $query) !== false) {
            $found = true;
        } else if (strpos(core_text::strtolower($this->visiblename), $query) !== false) {
                $found = true;
            }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }

    /**
     * This function always returns false, required by interface
     *
     * @param string $name
     * @return bool Always false
     */
    public function prune($name) {
        return false;
    }

    /**
     * adds an admin_setting to this admin_settingpage
     *
     * not the same as add for admin_category. adds an admin_setting to this admin_settingpage. settings appear (on the settingpage) in the order in which they're added
     * n.b. each admin_setting in an admin_settingpage must have a unique internal name
     *
     * @param object $setting is the admin_setting object you want to add
     * @return bool true if successful, false if not
     */
    public function add($setting) {
        if (!($setting instanceof admin_setting)) {
            debugging('error - not a setting instance');
            return false;
        }

        $name = $setting->name;
        if ($setting->plugin) {
            $name = $setting->plugin . $name;
        }
        $this->settings->{$name} = $setting;
        return true;
    }

    /**
     * Hide the named setting if the specified condition is matched.
     *
     * @param string $settingname
     * @param string $dependenton
     * @param string $condition
     * @param string $value
     */
    public function hide_if($settingname, $dependenton, $condition = 'notchecked', $value = '1') {
        $this->dependencies[] = new admin_settingdependency($settingname, $dependenton, $condition, $value);

        // Reformat the dependency name to the plugin | name format used in the display.
        $dependenton = str_replace('/', ' | ', $dependenton);

        // Let the setting know, so it can be displayed underneath.
        $findname = str_replace('/', '', $settingname);
        foreach ($this->settings as $name => $setting) {
            if ($name === $findname) {
                $setting->add_dependent_on($dependenton);
            }
        }
    }

    /**
     * see admin_externalpage
     *
     * @return bool Returns true for yes false for no
     */
    public function check_access() {
        global $CFG;
        $context = empty($this->context) ? context_system::instance() : $this->context;
        foreach($this->req_capability as $cap) {
            if (has_capability($cap, $context)) {
                return true;
            }
        }
        return false;
    }

    /**
     * outputs this page as html in a table (suitable for inclusion in an admin pagetype)
     * @return string Returns an XHTML string
     */
    public function output_html() {
        $adminroot = admin_get_root();
        $return = '<fieldset>'."\n".'<div class="clearer"><!-- --></div>'."\n";
        foreach($this->settings as $setting) {
            $fullname = $setting->get_full_name();
            if (array_key_exists($fullname, $adminroot->errors)) {
                $data = $adminroot->errors[$fullname]->data;
            } else {
                $data = $setting->get_setting();
                // do not use defaults if settings not available - upgrade settings handles the defaults!
            }
            $return .= $setting->output_html($data);
        }
        $return .= '</fieldset>';
        return $return;
    }

    /**
     * Is this settings page hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    public function is_hidden() {
        return $this->hidden;
    }

    /**
     * Show we display Save button at the page bottom?
     * @return bool
     */
    public function show_save() {
        foreach($this->settings as $setting) {
            if (empty($setting->nosave)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Should any of the settings on this page be shown / hidden based on conditions?
     * @return bool
     */
    public function has_dependencies() {
        return (bool)$this->dependencies;
    }

    /**
     * Format the setting show/hide conditions ready to initialise the page javascript
     * @return array
     */
    public function get_dependencies_for_javascript() {
        if (!$this->has_dependencies()) {
            return [];
        }
        return admin_settingdependency::prepare_for_javascript($this->dependencies);
    }
}


/**
 * Admin settings class. Only exists on setting pages.
 * Read & write happens at this level; no authentication.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class admin_setting {
    /** @var string unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins. */
    public $name;
    /** @var string localised name */
    public $visiblename;
    /** @var string localised long description in Markdown format */
    public $description;
    /** @var mixed Can be string or array of string */
    public $defaultsetting;
    /** @var string */
    public $updatedcallback;
    /** @var mixed can be String or Null.  Null means main config table */
    public $plugin; // null means main config table
    /** @var bool true indicates this setting does not actually save anything, just information */
    public $nosave = false;
    /** @var bool if set, indicates that a change to this setting requires rebuild course cache */
    public $affectsmodinfo = false;
    /** @var array of admin_setting_flag - These are extra checkboxes attached to a setting. */
    private $flags = array();
    /** @var bool Whether this field must be forced LTR. */
    private $forceltr = null;
    /** @var array list of other settings that may cause this setting to be hidden */
    private $dependenton = [];

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param mixed $defaultsetting string or array depending on implementation
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        $this->parse_setting_name($name);
        $this->visiblename    = $visiblename;
        $this->description    = $description;
        $this->defaultsetting = $defaultsetting;
    }

    /**
     * Generic function to add a flag to this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     * @param string $shortname - The shortname for this flag. Used as a suffix for the setting name.
     * @param string $displayname - The display name for this flag. Used as a label next to the checkbox.
     */
    protected function set_flag_options($enabled, $default, $shortname, $displayname) {
        if (empty($this->flags[$shortname])) {
            $this->flags[$shortname] = new admin_setting_flag($enabled, $default, $shortname, $displayname);
        } else {
            $this->flags[$shortname]->set_options($enabled, $default);
        }
    }

    /**
     * Set the enabled options flag on this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     */
    public function set_enabled_flag_options($enabled, $default) {
        $this->set_flag_options($enabled, $default, 'enabled', new lang_string('enabled', 'core_admin'));
    }

    /**
     * Set the advanced options flag on this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     */
    public function set_advanced_flag_options($enabled, $default) {
        $this->set_flag_options($enabled, $default, 'adv', new lang_string('advanced'));
    }


    /**
     * Set the locked options flag on this admin setting.
     *
     * @param bool $enabled - One of self::OPTION_ENABLED or self::OPTION_DISABLED
     * @param bool $default - The default for the flag
     */
    public function set_locked_flag_options($enabled, $default) {
        $this->set_flag_options($enabled, $default, 'locked', new lang_string('locked', 'core_admin'));
    }

    /**
     * Get the currently saved value for a setting flag
     *
     * @param admin_setting_flag $flag - One of the admin_setting_flag for this admin_setting.
     * @return bool
     */
    public function get_setting_flag_value(admin_setting_flag $flag) {
        $value = $this->config_read($this->name . '_' . $flag->get_shortname());
        if (!isset($value)) {
            $value = $flag->get_default();
        }

        return !empty($value);
    }

    /**
     * Get the list of defaults for the flags on this setting.
     *
     * @param array of strings describing the defaults for this setting. This is appended to by this function.
     */
    public function get_setting_flag_defaults(& $defaults) {
        foreach ($this->flags as $flag) {
            if ($flag->is_enabled() && $flag->get_default()) {
                $defaults[] = $flag->get_displayname();
            }
        }
    }

    /**
     * Output the input fields for the advanced and locked flags on this setting.
     *
     * @param bool $adv - The current value of the advanced flag.
     * @param bool $locked - The current value of the locked flag.
     * @return string $output - The html for the flags.
     */
    public function output_setting_flags() {
        $output = '';

        foreach ($this->flags as $flag) {
            if ($flag->is_enabled()) {
                $output .= $flag->output_setting_flag($this);
            }
        }

        if (!empty($output)) {
            return html_writer::tag('span', $output, array('class' => 'adminsettingsflags'));
        }
        return $output;
    }

    /**
     * Write the values of the flags for this admin setting.
     *
     * @param array $data - The data submitted from the form or null to set the default value for new installs.
     * @return bool - true if successful.
     */
    public function write_setting_flags($data) {
        $result = true;
        foreach ($this->flags as $flag) {
            $result = $result && $flag->write_setting_flag($this, $data);
        }
        return $result;
    }

    /**
     * Set up $this->name and potentially $this->plugin
     *
     * Set up $this->name and possibly $this->plugin based on whether $name looks
     * like 'settingname' or 'plugin/settingname'. Also, do some sanity checking
     * on the names, that is, output a developer debug warning if the name
     * contains anything other than [a-zA-Z0-9_]+.
     *
     * @param string $name the setting name passed in to the constructor.
     */
    private function parse_setting_name($name) {
        $bits = explode('/', $name);
        if (count($bits) > 2) {
            throw new moodle_exception('invalidadminsettingname', '', '', $name);
        }
        $this->name = array_pop($bits);
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->name)) {
            throw new moodle_exception('invalidadminsettingname', '', '', $name);
        }
        if (!empty($bits)) {
            $this->plugin = array_pop($bits);
            if ($this->plugin === 'moodle') {
                $this->plugin = null;
            } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->plugin)) {
                    throw new moodle_exception('invalidadminsettingname', '', '', $name);
                }
        }
    }

    /**
     * Returns the fullname prefixed by the plugin
     * @return string
     */
    public function get_full_name() {
        return 's_'.$this->plugin.'_'.$this->name;
    }

    /**
     * Returns the ID string based on plugin and name
     * @return string
     */
    public function get_id() {
        return 'id_s_'.$this->plugin.'_'.$this->name;
    }

    /**
     * @param bool $affectsmodinfo If true, changes to this setting will
     *   cause the course cache to be rebuilt
     */
    public function set_affects_modinfo($affectsmodinfo) {
        $this->affectsmodinfo = $affectsmodinfo;
    }

    /**
     * Returns the config if possible
     *
     * @return mixed returns config if successful else null
     */
    public function config_read($name) {
        global $CFG;
        if (!empty($this->plugin)) {
            $value = get_config($this->plugin, $name);
            return $value === false ? NULL : $value;

        } else {
            if (isset($CFG->$name)) {
                return $CFG->$name;
            } else {
                return NULL;
            }
        }
    }

    /**
     * Used to set a config pair and log change
     *
     * @param string $name
     * @param mixed $value Gets converted to string if not null
     * @return bool Write setting to config table
     */
    public function config_write($name, $value) {
        global $DB, $USER, $CFG;

        if ($this->nosave) {
            return true;
        }

        // make sure it is a real change
        $oldvalue = get_config($this->plugin, $name);
        $oldvalue = ($oldvalue === false) ? null : $oldvalue; // normalise
        $value = is_null($value) ? null : (string)$value;

        if ($oldvalue === $value) {
            return true;
        }

        // store change
        set_config($name, $value, $this->plugin);

        // Some admin settings affect course modinfo
        if ($this->affectsmodinfo) {
            // Clear course cache for all courses
            rebuild_course_cache(0, true);
        }

        $this->add_to_config_log($name, $oldvalue, $value);

        return true; // BC only
    }

    /**
     * Log config changes if necessary.
     * @param string $name
     * @param string $oldvalue
     * @param string $value
     */
    protected function add_to_config_log($name, $oldvalue, $value) {
        add_to_config_log($name, $oldvalue, $value, $this->plugin);
    }

    /**
     * Returns current value of this setting
     * @return mixed array or string depending on instance, NULL means not set yet
     */
    public abstract function get_setting();

    /**
     * Returns default setting if exists
     * @return mixed array or string depending on instance; NULL means no default, user must supply
     */
    public function get_defaultsetting() {
        $adminroot =  admin_get_root(false, false);
        if (!empty($adminroot->custom_defaults)) {
            $plugin = is_null($this->plugin) ? 'moodle' : $this->plugin;
            if (isset($adminroot->custom_defaults[$plugin])) {
                if (array_key_exists($this->name, $adminroot->custom_defaults[$plugin])) { // null is valid value here ;-)
                    return $adminroot->custom_defaults[$plugin][$this->name];
                }
            }
        }
        return $this->defaultsetting;
    }

    /**
     * Store new setting
     *
     * @param mixed $data string or array, must not be NULL
     * @return string empty string if ok, string error message otherwise
     */
    public abstract function write_setting($data);

    /**
     * Return part of form with setting
     * This function should always be overwritten
     *
     * @param mixed $data array or string depending on setting
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
    // should be overridden
        return;
    }

    /**
     * Function called if setting updated - cleanup, cache reset, etc.
     * @param string $functionname Sets the function name
     * @return void
     */
    public function set_updatedcallback($functionname) {
        $this->updatedcallback = $functionname;
    }

    /**
     * Execute postupdatecallback if necessary.
     * @param mixed $original original value before write_setting()
     * @return bool true if changed, false if not.
     */
    public function post_write_settings($original) {
        // Comparison must work for arrays too.
        if (serialize($original) === serialize($this->get_setting())) {
            return false;
        }

        $callbackfunction = $this->updatedcallback;
        if (!empty($callbackfunction) and is_callable($callbackfunction)) {
            $callbackfunction($this->get_full_name());
        }
        return true;
    }

    /**
     * Is setting related to query text - used when searching
     * @param string $query
     * @return bool
     */
    public function is_related($query) {
        if (strpos(strtolower($this->name), $query) !== false) {
            return true;
        }
        if (strpos(core_text::strtolower($this->visiblename), $query) !== false) {
            return true;
        }
        if (strpos(core_text::strtolower($this->description), $query) !== false) {
            return true;
        }
        $current = $this->get_setting();
        if (!is_null($current)) {
            if (is_string($current)) {
                if (strpos(core_text::strtolower($current), $query) !== false) {
                    return true;
                }
            }
        }
        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            if (is_string($default)) {
                if (strpos(core_text::strtolower($default), $query) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get whether this should be displayed in LTR mode.
     *
     * @return bool|null
     */
    public function get_force_ltr() {
        return $this->forceltr;
    }

    /**
     * Set whether to force LTR or not.
     *
     * @param bool $value True when forced, false when not force, null when unknown.
     */
    public function set_force_ltr($value) {
        $this->forceltr = $value;
    }

    /**
     * Add a setting to the list of those that could cause this one to be hidden
     * @param string $dependenton
     */
    public function add_dependent_on($dependenton) {
        $this->dependenton[] = $dependenton;
    }

    /**
     * Get a list of the settings that could cause this one to be hidden.
     * @return array
     */
    public function get_dependent_on() {
        return $this->dependenton;
    }
}

/**
 * An additional option that can be applied to an admin setting.
 * The currently supported options are 'ADVANCED' and 'LOCKED'.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_flag {
    /** @var bool Flag to indicate if this option can be toggled for this setting */
    private $enabled = false;
    /** @var bool Flag to indicate if this option defaults to true or false */
    private $default = false;
    /** @var string Short string used to create setting name - e.g. 'adv' */
    private $shortname = '';
    /** @var string String used as the label for this flag */
    private $displayname = '';
    /** @const Checkbox for this flag is displayed in admin page */
    const ENABLED = true;
    /** @const Checkbox for this flag is not displayed in admin page */
    const DISABLED = false;

    /**
     * Constructor
     *
     * @param bool $enabled Can this option can be toggled.
     *                      Should be one of admin_setting_flag::ENABLED or admin_setting_flag::DISABLED.
     * @param bool $default The default checked state for this setting option.
     * @param string $shortname The shortname of this flag. Currently supported flags are 'locked' and 'adv'
     * @param string $displayname The displayname of this flag. Used as a label for the flag.
     */
    public function __construct($enabled, $default, $shortname, $displayname) {
        $this->shortname = $shortname;
        $this->displayname = $displayname;
        $this->set_options($enabled, $default);
    }

    /**
     * Update the values of this setting options class
     *
     * @param bool $enabled Can this option can be toggled.
     *                      Should be one of admin_setting_flag::ENABLED or admin_setting_flag::DISABLED.
     * @param bool $default The default checked state for this setting option.
     */
    public function set_options($enabled, $default) {
        $this->enabled = $enabled;
        $this->default = $default;
    }

    /**
     * Should this option appear in the interface and be toggleable?
     *
     * @return bool Is it enabled?
     */
    public function is_enabled() {
        return $this->enabled;
    }

    /**
     * Should this option be checked by default?
     *
     * @return bool Is it on by default?
     */
    public function get_default() {
        return $this->default;
    }

    /**
     * Return the short name for this flag. e.g. 'adv' or 'locked'
     *
     * @return string
     */
    public function get_shortname() {
        return $this->shortname;
    }

    /**
     * Return the display name for this flag. e.g. 'Advanced' or 'Locked'
     *
     * @return string
     */
    public function get_displayname() {
        return $this->displayname;
    }

    /**
     * Save the submitted data for this flag - or set it to the default if $data is null.
     *
     * @param admin_setting $setting - The admin setting for this flag
     * @param array $data - The data submitted from the form or null to set the default value for new installs.
     * @return bool
     */
    public function write_setting_flag(admin_setting $setting, $data) {
        $result = true;
        if ($this->is_enabled()) {
            if (!isset($data)) {
                $value = $this->get_default();
            } else {
                $value = !empty($data[$setting->get_full_name() . '_' . $this->get_shortname()]);
            }
            $result = $setting->config_write($setting->name . '_' . $this->get_shortname(), $value);
        }

        return $result;

    }

    /**
     * Output the checkbox for this setting flag. Should only be called if the flag is enabled.
     *
     * @param admin_setting $setting - The admin setting for this flag
     * @return string - The html for the checkbox.
     */
    public function output_setting_flag(admin_setting $setting) {
        global $OUTPUT;

        $value = $setting->get_setting_flag_value($this);

        $context = new stdClass();
        $context->id = $setting->get_id() . '_' . $this->get_shortname();
        $context->name = $setting->get_full_name() .  '_' . $this->get_shortname();
        $context->value = 1;
        $context->checked = $value ? true : false;
        $context->label = $this->get_displayname();

        return $OUTPUT->render_from_template('core_admin/setting_flag', $context);
    }
}


/**
 * No setting - just heading and text.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_heading extends admin_setting {

    /**
     * not a setting, just text
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $heading heading
     * @param string $information text in box
     */
    public function __construct($name, $heading, $information) {
        $this->nosave = true;
        parent::__construct($name, $heading, $information, '');
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Returns an HTML string
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
        $context = new stdClass();
        $context->title = $this->visiblename;
        $context->description = $this->description;
        $context->descriptionformatted = highlight($query, markdown_to_html($this->description));
        return $OUTPUT->render_from_template('core_admin/setting_heading', $context);
    }
}

/**
 * No setting - just name and description in same row.
 *
 * @copyright 2018 onwards Amaia Anabitarte
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_description extends admin_setting {

    /**
     * Not a setting, just text
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct($name, $visiblename, $description) {
        $this->nosave = true;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Always returns true
     *
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     *
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     *
     * @param mixed $data Gets converted to str for comparison against yes value
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Returns an HTML string
     *
     * @param string $data
     * @param string $query
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = new stdClass();
        $context->title = $this->visiblename;
        $context->description = $this->description;

        return $OUTPUT->render_from_template('core_admin/setting_description', $context);
    }
}



/**
 * The most flexible setting, the user enters text.
 *
 * This type of field should be used for config settings which are using
 * English words and are not localised (passwords, database name, list of values, ...).
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext extends admin_setting {

    /** @var mixed int means PARAM_XXX type, string is a allowed format in regex */
    public $paramtype;
    /** @var int default field size */
    public $size;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $size=null) {
        $this->paramtype = $paramtype;
        if (!is_null($size)) {
            $this->size  = $size;
        } else {
            $this->size  = ($paramtype === PARAM_INT) ? 5 : 30;
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get whether this should be displayed in LTR mode.
     *
     * Try to guess from the PARAM type unless specifically set.
     */
    public function get_force_ltr() {
        $forceltr = parent::get_force_ltr();
        if ($forceltr === null) {
            return !is_rtl_compatible($this->paramtype);
        }
        return $forceltr;
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        if ($this->paramtype === PARAM_INT and $data === '') {
        // do not complain if '' used instead of 0
            $data = 0;
        }
        // $data is a string
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        // allow paramtype to be a custom regex if it is the form of /pattern/
        if (preg_match('#^/.*/$#', $this->paramtype)) {
            if (preg_match($this->paramtype, $data)) {
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }

        } else if ($this->paramtype === PARAM_RAW) {
            return true;

        } else {
            $cleaned = clean_param($data, $this->paramtype);
            if ("$data" === "$cleaned") { // implicit conversion to string is needed to do exact comparison
                return true;
            } else {
                return get_string('validateerror', 'admin');
            }
        }
    }

    /**
     * Return an XHTML string for the setting
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'size' => $this->size,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configtext', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}

/**
 * Text input with a maximum length constraint.
 *
 * @copyright 2015 onwards Ankit Agarwal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext_with_maxlength extends admin_setting_configtext {

    /** @var int maximum number of chars allowed. */
    protected $maxlength;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     * @param mixed $maxlength int maxlength allowed, 0 for infinite.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW,
                                $size=null, $maxlength = 0) {
        $this->maxlength = $maxlength;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    /**
     * Validate data before storage
     *
     * @param string $data data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        $parentvalidation = parent::validate($data);
        if ($parentvalidation === true) {
            if ($this->maxlength > 0) {
                // Max length check.
                $length = core_text::strlen($data);
                if ($length > $this->maxlength) {
                    return get_string('maximumchars', 'moodle',  $this->maxlength);
                }
                return true;
            } else {
                return true; // No max length check needed.
            }
        } else {
            return $parentvalidation;
        }
    }
}

/**
 * General text area without html editor.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtextarea extends admin_setting_configtext {
    private $rows;
    private $cols;

    /**
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param mixed $paramtype
     * @param string $cols The number of columns to make the editor
     * @param string $rows The number of rows to make the editor
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $cols='60', $rows='8') {
        $this->rows = $rows;
        $this->cols = $cols;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    /**
     * Returns an XHTML string for the editor
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $defaultinfo = $default;
        if (!is_null($default) and $default !== '') {
            $defaultinfo = "\n".$default;
        }

        $context = (object) [
            'cols' => $this->cols,
            'rows' => $this->rows,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configtextarea', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * General text area with html editor.
 */
class admin_setting_confightmleditor extends admin_setting_configtextarea {

    /**
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param mixed $paramtype
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $cols='60', $rows='8') {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $cols, $rows);
        $this->set_force_ltr(false);
        editors_head_setup();
    }

    /**
     * Returns an XHTML string for the editor
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        $editor = editors_get_preferred_editor(FORMAT_HTML);
        $editor->set_text($data);
        $editor->use_editor($this->get_id(), array('noclean'=>true));
        return parent::output_html($data, $query);
    }

    /**
     * Checks if data has empty html.
     *
     * @param string $data
     * @return string Empty when no errors.
     */
    public function write_setting($data) {
        if (trim(html_to_text($data)) === '') {
            $data = '';
        }
        return parent::write_setting($data);
    }
}


/**
 * Password field, allows unmasking of password
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configpasswordunmask extends admin_setting_configtext {

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting default password
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, PARAM_RAW, 30);
    }

    /**
     * Log config changes if necessary.
     * @param string $name
     * @param string $oldvalue
     * @param string $value
     */
    protected function add_to_config_log($name, $oldvalue, $value) {
        if ($value !== '') {
            $value = '********';
        }
        if ($oldvalue !== '' and $oldvalue !== null) {
            $oldvalue = '********';
        }
        parent::add_to_config_log($name, $oldvalue, $value);
    }

    /**
     * Returns HTML for the field.
     *
     * @param   string  $data       Value for the field
     * @param   string  $query      Passed as final argument for format_admin_setting
     * @return  string              Rendered HTML
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $data,
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configpasswordunmask', $context);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', null, $query);
    }
}

/**
 * Password field, allows unmasking of password, with an advanced checkbox that controls an additional $name.'_adv' setting.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2018 Paul Holden (pholden@greenhead.ac.uk)
 */
class admin_setting_configpasswordunmask_with_advanced extends admin_setting_configpasswordunmask {

    /**
     * Constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, 'adv'=>bool)
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value']);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }
}

/**
 * Empty setting used to allow flags (advanced) on settings that can have no sensible default.
 * Note: Only advanced makes sense right now - locked does not.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configempty extends admin_setting_configtext {

    /**
     * @param string $name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct($name, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description, '', PARAM_RAW);
    }

    /**
     * Returns an XHTML string for the hidden field
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name()
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configempty', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', get_string('none'), $query);
    }
}


/**
 * Path to directory
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configfile extends admin_setting_configtext {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultdirectory default directory location
     */
    public function __construct($name, $visiblename, $description, $defaultdirectory) {
        parent::__construct($name, $visiblename, $description, $defaultdirectory, PARAM_RAW, 50);
    }

    /**
     * Returns XHTML for the field
     *
     * Returns XHTML for the field and also checks whether the file
     * specified in $data exists using file_exists()
     *
     * @param string $data File name and path to use in value attr
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $data,
            'showvalidity' => !empty($data),
            'valid' => $data && file_exists($data),
            'readonly' => !empty($CFG->preventexecpath),
            'forceltr' => $this->get_force_ltr(),
        ];

        if ($context->readonly) {
            $this->visiblename .= '<div class="form-overridden">'.get_string('execpathnotallowed', 'admin').'</div>';
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configfile', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }

    /**
     * Checks if execpatch has been disabled in config.php
     */
    public function write_setting($data) {
        global $CFG;
        if (!empty($CFG->preventexecpath)) {
            if ($this->get_setting() === null) {
                // Use default during installation.
                $data = $this->get_defaultsetting();
                if ($data === null) {
                    $data = '';
                }
            } else {
                return '';
            }
        }
        return parent::write_setting($data);
    }

}


/**
 * Path to executable file
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configexecutable extends admin_setting_configfile {

    /**
     * Returns an XHTML field
     *
     * @param string $data This is the value for the field
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        $default = $this->get_defaultsetting();
        require_once("$CFG->libdir/filelib.php");

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $data,
            'showvalidity' => !empty($data),
            'valid' => $data && file_exists($data) && !is_dir($data) && file_is_executable($data),
            'readonly' => !empty($CFG->preventexecpath),
            'forceltr' => $this->get_force_ltr()
        ];

        if (!empty($CFG->preventexecpath)) {
            $this->visiblename .= '<div class="form-overridden">'.get_string('execpathnotallowed', 'admin').'</div>';
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configexecutable', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}


/**
 * Path to directory
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configdirectory extends admin_setting_configfile {

    /**
     * Returns an XHTML field
     *
     * @param string $data This is the value for the field
     * @param string $query
     * @return string XHTML
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        $default = $this->get_defaultsetting();

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => $this->size,
            'value' => $data,
            'showvalidity' => !empty($data),
            'valid' => $data && file_exists($data) && is_dir($data),
            'readonly' => !empty($CFG->preventexecpath),
            'forceltr' => $this->get_force_ltr()
        ];

        if (!empty($CFG->preventexecpath)) {
            $this->visiblename .= '<div class="form-overridden">'.get_string('execpathnotallowed', 'admin').'</div>';
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configdirectory', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}


/**
 * Checkbox
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcheckbox extends admin_setting {
    /** @var string Value used when checked */
    public $yes;
    /** @var string Value used when not checked */
    public $no;

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param string $yes value used when checked
     * @param string $no value used when not checked
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0') {
        parent::__construct($name, $visiblename, $description, $defaultsetting);
        $this->yes = (string)$yes;
        $this->no  = (string)$no;
    }

    /**
     * Retrieves the current setting using the objects name
     *
     * @return string
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Sets the value for the setting
     *
     * Sets the value for the setting to either the yes or no values
     * of the object by comparing $data to yes
     *
     * @param mixed $data Gets converted to str for comparison against yes value
     * @return string empty string or error
     */
    public function write_setting($data) {
        if ((string)$data === $this->yes) { // convert to strings before comparison
            $data = $this->yes;
        } else {
            $data = $this->no;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns an XHTML checkbox field
     *
     * @param string $data If $data matches yes then checkbox is checked
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'no' => $this->no,
            'value' => $this->yes,
            'checked' => (string) $data === $this->yes,
        ];

        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            if ((string)$default === $this->yes) {
                $defaultinfo = get_string('checkboxyes', 'admin');
            } else {
                $defaultinfo = get_string('checkboxno', 'admin');
            }
        } else {
            $defaultinfo = NULL;
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configcheckbox', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}


/**
 * Multiple checkboxes, each represents different value, stored in csv format
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configmulticheckbox extends admin_setting {
    /** @var array Array of choices value=>label */
    public $choices;

    /**
     * Constructor: uses parent::__construct
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected
     * @param array $choices array of $value=>$label for each checkbox
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * This public function may be used in ancestors for lazy loading of choices
     *
     * @todo Check if this function is still required content commented out only returns true
     * @return bool true if loaded, false if error
     */
    public function load_choices() {
        /*
        if (is_array($this->choices)) {
            return true;
        }
        .... load choices here
        */
        return true;
    }

    /**
     * Is setting related to query text - used when searching
     *
     * @param string $query
     * @return bool true on related, false on not or failure
     */
    public function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        foreach ($this->choices as $desc) {
            if (strpos(core_text::strtolower($desc), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the current setting if it is set
     *
     * @return mixed null if null, else an array
     */
    public function get_setting() {
        $result = $this->config_read($this->name);

        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        $enabled = explode(',', $result);
        $setting = array();
        foreach ($enabled as $option) {
            $setting[$option] = 1;
        }
        return $setting;
    }

    /**
     * Saves the setting(s) provided in $data
     *
     * @param array $data An array of data, if not array returns empty str
     * @return mixed empty string on useless data or bool true=success, false=failed
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        unset($data['xxxxx']);
        $result = array();
        foreach ($data as $key => $value) {
            if ($value and array_key_exists($key, $this->choices)) {
                $result[] = $key;
            }
        }
        return $this->config_write($this->name, implode(',', $result)) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Returns XHTML field(s) as required by choices
     *
     * Relies on data being an array should data ever be another valid vartype with
     * acceptable value this may cause a warning/error
     * if (!is_array($data)) would fix the problem
     *
     * @todo Add vartype handling to ensure $data is an array
     *
     * @param array $data An array of checked values
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }

        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            $data = array();
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
        ];

        $options = array();
        $defaults = array();
        foreach ($this->choices as $key => $description) {
            if (!empty($default[$key])) {
                $defaults[] = $description;
            }

            $options[] = [
                'key' => $key,
                'checked' => !empty($data[$key]),
                'label' => highlightfast($query, $description)
            ];
        }

        if (is_null($default)) {
            $defaultinfo = null;
        } else if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $context->options = $options;
        $context->hasoptions = !empty($options);

        $element = $OUTPUT->render_from_template('core_admin/setting_configmulticheckbox', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', $defaultinfo, $query);

    }
}


/**
 * Multiple checkboxes 2, value stored as string 00101011
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configmulticheckbox2 extends admin_setting_configmulticheckbox {

    /**
     * Returns the setting if set
     *
     * @return mixed null if not set, else an array of set settings
     */
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if (!$this->load_choices()) {
            return NULL;
        }
        $result = str_pad($result, count($this->choices), '0');
        $result = preg_split('//', $result, -1, PREG_SPLIT_NO_EMPTY);
        $setting = array();
        foreach ($this->choices as $key=>$unused) {
            $value = array_shift($result);
            if ($value) {
                $setting[$key] = 1;
            }
        }
        return $setting;
    }

    /**
     * Save setting(s) provided in $data param
     *
     * @param array $data An array of settings to save
     * @return mixed empty string for bad data or bool true=>success, false=>error
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $result = '';
        foreach ($this->choices as $key=>$unused) {
            if (!empty($data[$key])) {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }
        return $this->config_write($this->name, $result) ? '' : get_string('errorsetting', 'admin');
    }
}


/**
 * Select one value from list
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configselect extends admin_setting {
    /** @var array Array of choices value=>label */
    public $choices;
    /** @var array Array of choices grouped using optgroups */
    public $optgroups;

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string|int $defaultsetting
     * @param array $choices array of $value=>$label for each selection
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        // Look for optgroup and single options.
        if (is_array($choices)) {
            $this->choices = [];
            foreach ($choices as $key => $val) {
                if (is_array($val)) {
                    $this->optgroups[$key] = $val;
                    $this->choices = array_merge($this->choices, $val);
                } else {
                    $this->choices[$key] = $val;
                }
            }
        }

        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * This function may be used in ancestors for lazy loading of choices
     *
     * Override this method if loading of choices is expensive, such
     * as when it requires multiple db requests.
     *
     * @return bool true if loaded, false if error
     */
    public function load_choices() {
        /*
        if (is_array($this->choices)) {
            return true;
        }
        .... load choices here
        */
        return true;
    }

    /**
     * Check if this is $query is related to a choice
     *
     * @param string $query
     * @return bool true if related, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        if (!$this->load_choices()) {
            return false;
        }
        foreach ($this->choices as $key=>$value) {
            if (strpos(core_text::strtolower($key), $query) !== false) {
                return true;
            }
            if (strpos(core_text::strtolower($value), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Save a setting
     *
     * @param string $data
     * @return string empty of error string
     */
    public function write_setting($data) {
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        if (!array_key_exists($data, $this->choices)) {
            return ''; // ignore it
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns XHTML select field
     *
     * Ensure the options are loaded, and generate the XHTML for the select
     * element and any warning message. Separating this out from output_html
     * makes it easier to subclass this class.
     *
     * @param string $data the option to show as selected.
     * @param string $current the currently selected option in the database, null if none.
     * @param string $default the default selected option.
     * @return array the HTML for the select element, and a warning message.
     * @deprecated since Moodle 3.2
     */
    public function output_select_html($data, $current, $default, $extraname = '') {
        debugging('The method admin_setting_configselect::output_select_html is depreacted, do not use any more.', DEBUG_DEVELOPER);
    }

    /**
     * Returns XHTML select field and wrapping div(s)
     *
     * @see output_select_html()
     *
     * @param string $data the option to show as selected
     * @param string $query
     * @return string XHTML field and wrapping div
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $current = $this->get_setting();

        if (!$this->load_choices() || empty($this->choices)) {
            return '';
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
        ];

        if (!is_null($default) && array_key_exists($default, $this->choices)) {
            $defaultinfo = $this->choices[$default];
        } else {
            $defaultinfo = NULL;
        }

        // Warnings.
        $warning = '';
        if ($current === null) {
            // First run.
        } else if (empty($current) && (array_key_exists('', $this->choices) || array_key_exists(0, $this->choices))) {
            // No warning.
        } else if (!array_key_exists($current, $this->choices)) {
            $warning = get_string('warningcurrentsetting', 'admin', $current);
            if (!is_null($default) && $data == $current) {
                $data = $default; // Use default instead of first value when showing the form.
            }
        }

        $options = [];
        $template = 'core_admin/setting_configselect';

        if (!empty($this->optgroups)) {
            $optgroups = [];
            foreach ($this->optgroups as $label => $choices) {
                $optgroup = array('label' => $label, 'options' => []);
                foreach ($choices as $value => $name) {
                    $optgroup['options'][] = [
                        'value' => $value,
                        'name' => $name,
                        'selected' => (string) $value == $data
                    ];
                    unset($this->choices[$value]);
                }
                $optgroups[] = $optgroup;
            }
            $context->options = $options;
            $context->optgroups = $optgroups;
            $template = 'core_admin/setting_configselect_optgroup';
        }

        foreach ($this->choices as $value => $name) {
            $options[] = [
                'value' => $value,
                'name' => $name,
                'selected' => (string) $value == $data
            ];
        }
        $context->options = $options;

        $element = $OUTPUT->render_from_template($template, $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, $warning, $defaultinfo, $query);
    }
}


/**
 * Select multiple items from list
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configmultiselect extends admin_setting_configselect {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array of selected items
     * @param array $choices array of $value=>$label for each list item
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $choices);
    }

    /**
     * Returns the select setting(s)
     *
     * @return mixed null or array. Null if no settings else array of setting(s)
     */
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    /**
     * Saves setting(s) provided through $data
     *
     * Potential bug in the works should anyone call with this function
     * using a vartype that is not an array
     *
     * @param array $data
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; //ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }

        unset($data['xxxxx']);

        $save = array();
        foreach ($data as $value) {
            if (!array_key_exists($value, $this->choices)) {
                continue; // ignore it
            }
            $save[] = $value;
        }

        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Is setting related to query text - used when searching
     *
     * @param string $query
     * @return bool true if related, false if not
     */
    public function is_related($query) {
        if (!$this->load_choices() or empty($this->choices)) {
            return false;
        }
        if (parent::is_related($query)) {
            return true;
        }

        foreach ($this->choices as $desc) {
            if (strpos(core_text::strtolower($desc), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns XHTML multi-select field
     *
     * @todo Add vartype handling to ensure $data is an array
     * @param array $data Array of values to select by default
     * @param string $query
     * @return string XHTML multi-select field
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }

        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            $data = array();
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'size' => min(10, count($this->choices))
        ];

        $defaults = [];
        $options = [];
        $template = 'core_admin/setting_configmultiselect';

        if (!empty($this->optgroups)) {
            $optgroups = [];
            foreach ($this->optgroups as $label => $choices) {
                $optgroup = array('label' => $label, 'options' => []);
                foreach ($choices as $value => $name) {
                    if (in_array($value, $default)) {
                        $defaults[] = $name;
                    }
                    $optgroup['options'][] = [
                        'value' => $value,
                        'name' => $name,
                        'selected' => in_array($value, $data)
                    ];
                    unset($this->choices[$value]);
                }
                $optgroups[] = $optgroup;
            }
            $context->optgroups = $optgroups;
            $template = 'core_admin/setting_configmultiselect_optgroup';
        }

        foreach ($this->choices as $value => $name) {
            if (in_array($value, $default)) {
                $defaults[] = $name;
            }
            $options[] = [
                'value' => $value,
                'name' => $name,
                'selected' => in_array($value, $data)
            ];
        }
        $context->options = $options;

        if (is_null($default)) {
            $defaultinfo = NULL;
        } if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }

        $element = $OUTPUT->render_from_template($template, $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}

/**
 * Time selector
 *
 * This is a liiitle bit messy. we're using two selects, but we're returning
 * them as an array named after $name (so we only use $name2 internally for the setting)
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtime extends admin_setting {
    /** @var string Used for setting second select (minutes) */
    public $name2;

    /**
     * Constructor
     * @param string $hoursname setting for hours
     * @param string $minutesname setting for hours
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting array representing default time 'h'=>hours, 'm'=>minutes
     */
    public function __construct($hoursname, $minutesname, $visiblename, $description, $defaultsetting) {
        $this->name2 = $minutesname;
        parent::__construct($hoursname, $visiblename, $description, $defaultsetting);
    }

    /**
     * Get the selected time
     *
     * @return mixed An array containing 'h'=>xx, 'm'=>xx, or null if not set
     */
    public function get_setting() {
        $result1 = $this->config_read($this->name);
        $result2 = $this->config_read($this->name2);
        if (is_null($result1) or is_null($result2)) {
            return NULL;
        }

        return array('h' => $result1, 'm' => $result2);
    }

    /**
     * Store the time (hours and minutes)
     *
     * @param array $data Must be form 'h'=>xx, 'm'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }

        $result = $this->config_write($this->name, (int)$data['h']) && $this->config_write($this->name2, (int)$data['m']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'h'=>xx, 'm'=>xx
     * @param string $query
     * @return string XHTML time select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        if (is_array($default)) {
            $defaultinfo = $default['h'].':'.$default['m'];
        } else {
            $defaultinfo = NULL;
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'hours' => array_map(function($i) use ($data) {
                return [
                    'value' => $i,
                    'name' => $i,
                    'selected' => $i == $data['h']
                ];
            }, range(0, 23)),
            'minutes' => array_map(function($i) use ($data) {
                return [
                    'value' => $i,
                    'name' => $i,
                    'selected' => $i == $data['m']
                ];
            }, range(0, 59, 5))
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_configtime', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description,
            $this->get_id() . 'h', '', $defaultinfo, $query);
    }

}


/**
 * Seconds duration setting.
 *
 * @copyright 2012 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configduration extends admin_setting {

    /** @var int default duration unit */
    protected $defaultunit;

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param mixed $defaultsetting string or array depending on implementation
     * @param int $defaultunit - day, week, etc. (in seconds)
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $defaultunit = 86400) {
        if (is_number($defaultsetting)) {
            $defaultsetting = self::parse_seconds($defaultsetting);
        }
        $units = self::get_units();
        if (isset($units[$defaultunit])) {
            $this->defaultunit = $defaultunit;
        } else {
            $this->defaultunit = 86400;
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Returns selectable units.
     * @static
     * @return array
     */
    protected static function get_units() {
        return array(
            604800 => get_string('weeks'),
            86400 => get_string('days'),
            3600 => get_string('hours'),
            60 => get_string('minutes'),
            1 => get_string('seconds'),
        );
    }

    /**
     * Converts seconds to some more user friendly string.
     * @static
     * @param int $seconds
     * @return string
     */
    protected static function get_duration_text($seconds) {
        if (empty($seconds)) {
            return get_string('none');
        }
        $data = self::parse_seconds($seconds);
        switch ($data['u']) {
            case (60*60*24*7):
                return get_string('numweeks', '', $data['v']);
            case (60*60*24):
                return get_string('numdays', '', $data['v']);
            case (60*60):
                return get_string('numhours', '', $data['v']);
            case (60):
                return get_string('numminutes', '', $data['v']);
            default:
                return get_string('numseconds', '', $data['v']*$data['u']);
        }
    }

    /**
     * Finds suitable units for given duration.
     * @static
     * @param int $seconds
     * @return array
     */
    protected static function parse_seconds($seconds) {
        foreach (self::get_units() as $unit => $unused) {
            if ($seconds % $unit === 0) {
                return array('v'=>(int)($seconds/$unit), 'u'=>$unit);
            }
        }
        return array('v'=>(int)$seconds, 'u'=>1);
    }

    /**
     * Get the selected duration as array.
     *
     * @return mixed An array containing 'v'=>xx, 'u'=>xx, or null if not set
     */
    public function get_setting() {
        $seconds = $this->config_read($this->name);
        if (is_null($seconds)) {
            return null;
        }

        return self::parse_seconds($seconds);
    }

    /**
     * Store the duration as seconds.
     *
     * @param array $data Must be form 'h'=>xx, 'm'=>xx
     * @return bool true if success, false if not
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }

        $seconds = (int)($data['v']*$data['u']);
        if ($seconds < 0) {
            return get_string('errorsetting', 'admin');
        }

        $result = $this->config_write($this->name, $seconds);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns duration text+select fields.
     *
     * @param array $data Must be form 'v'=>xx, 'u'=>xx
     * @param string $query
     * @return string duration text+select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        if (is_number($default)) {
            $defaultinfo = self::get_duration_text($default);
        } else if (is_array($default)) {
            $defaultinfo = self::get_duration_text($default['v']*$default['u']);
        } else {
            $defaultinfo = null;
        }

        $inputid = $this->get_id() . 'v';
        $units = self::get_units();
        $defaultunit = $this->defaultunit;

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data['v'],
            'options' => array_map(function($unit) use ($units, $data, $defaultunit) {
                return [
                    'value' => $unit,
                    'name' => $units[$unit],
                    'selected' => ($data['v'] == 0 && $unit == $defaultunit) || $unit == $data['u']
                ];
            }, array_keys($units))
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_configduration', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, $inputid, '', $defaultinfo, $query);
    }
}


/**
 * Seconds duration setting with an advanced checkbox, that controls a additional
 * $name.'_adv' setting.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2014 The Open University
 */
class admin_setting_configduration_with_advanced extends admin_setting_configduration {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array  $defaultsetting array of int value, and bool whether it is
     *                     is advanced by default.
     * @param int $defaultunit - day, week, etc. (in seconds)
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $defaultunit = 86400) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $defaultunit);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }
}


/**
 * Used to validate a textarea used for ip addresses
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2011 Petr Skoda (http://skodak.org)
 */
class admin_setting_configiplist extends admin_setting_configtextarea {

    /**
     * Validate the contents of the textarea as IP addresses
     *
     * Used to validate a new line separated list of IP addresses collected from
     * a textarea control
     *
     * @param string $data A list of IP Addresses separated by new lines
     * @return mixed bool true for success or string:error on failure
     */
    public function validate($data) {
        if(!empty($data)) {
            $lines = explode("\n", $data);
        } else {
            return true;
        }
        $result = true;
        $badips = array();
        foreach ($lines as $line) {
            $tokens = explode('#', $line);
            $ip = trim($tokens[0]);
            if (empty($ip)) {
                continue;
            }
            if (preg_match('#^(\d{1,3})(\.\d{1,3}){0,3}$#', $ip, $match) ||
                preg_match('#^(\d{1,3})(\.\d{1,3}){0,3}(\/\d{1,2})$#', $ip, $match) ||
                preg_match('#^(\d{1,3})(\.\d{1,3}){3}(-\d{1,3})$#', $ip, $match)) {
            } else {
                $result = false;
                $badips[] = $ip;
            }
        }
        if($result) {
            return true;
        } else {
            return get_string('validateiperror', 'admin', join(', ', $badips));
        }
    }
}

/**
 * Used to validate a textarea used for domain names, wildcard domain names and IP addresses/ranges (both IPv4 and IPv6 format).
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 Jake Dallimore (jrhdallimore@gmail.com)
 */
class admin_setting_configmixedhostiplist extends admin_setting_configtextarea {

    /**
     * Validate the contents of the textarea as either IP addresses, domain name or wildcard domain name (RFC 4592).
     * Used to validate a new line separated list of entries collected from a textarea control.
     *
     * This setting provides support for internationalised domain names (IDNs), however, such UTF-8 names will be converted to
     * their ascii-compatible encoding (punycode) on save, and converted back to their UTF-8 representation when fetched
     * via the get_setting() method, which has been overriden.
     *
     * @param string $data A list of FQDNs, DNS wildcard format domains, and IP addresses, separated by new lines.
     * @return mixed bool true for success or string:error on failure
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }
        $entries = explode("\n", $data);
        $badentries = [];

        foreach ($entries as $key => $entry) {
            $entry = trim($entry);
            if (empty($entry)) {
                return get_string('validateemptylineerror', 'admin');
            }

            // Validate each string entry against the supported formats.
            if (\core\ip_utils::is_ip_address($entry) || \core\ip_utils::is_ipv6_range($entry)
                    || \core\ip_utils::is_ipv4_range($entry) || \core\ip_utils::is_domain_name($entry)
                    || \core\ip_utils::is_domain_matching_pattern($entry)) {
                continue;
            }

            // Otherwise, the entry is invalid.
            $badentries[] = $entry;
        }

        if ($badentries) {
            return get_string('validateerrorlist', 'admin', join(', ', $badentries));
        }
        return true;
    }

    /**
     * Convert any lines containing international domain names (IDNs) to their ascii-compatible encoding (ACE).
     *
     * @param string $data the setting data, as sent from the web form.
     * @return string $data the setting data, with all IDNs converted (using punycode) to their ascii encoded version.
     */
    protected function ace_encode($data) {
        if (empty($data)) {
            return $data;
        }
        $entries = explode("\n", $data);
        foreach ($entries as $key => $entry) {
            $entry = trim($entry);
            // This regex matches any string that has non-ascii character.
            if (preg_match('/[^\x00-\x7f]/', $entry)) {
                // If we can convert the unicode string to an idn, do so.
                // Otherwise, leave the original unicode string alone and let the validation function handle it (it will fail).
                $val = idn_to_ascii($entry, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
                $entries[$key] = $val ? $val : $entry;
            }
        }
        return implode("\n", $entries);
    }

    /**
     * Decode any ascii-encoded domain names back to their utf-8 representation for display.
     *
     * @param string $data the setting data, as found in the database.
     * @return string $data the setting data, with all ascii-encoded IDNs decoded back to their utf-8 representation.
     */
    protected function ace_decode($data) {
        $entries = explode("\n", $data);
        foreach ($entries as $key => $entry) {
            $entry = trim($entry);
            if (strpos($entry, 'xn--') !== false) {
                $entries[$key] = idn_to_utf8($entry, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
            }
        }
        return implode("\n", $entries);
    }

    /**
     * Override, providing utf8-decoding for ascii-encoded IDN strings.
     *
     * @return mixed returns punycode-converted setting string if successful, else null.
     */
    public function get_setting() {
        // Here, we need to decode any ascii-encoded IDNs back to their native, utf-8 representation.
        $data = $this->config_read($this->name);
        if (function_exists('idn_to_utf8') && !is_null($data)) {
            $data = $this->ace_decode($data);
        }
        return $data;
    }

    /**
     * Override, providing ascii-encoding for utf8 (native) IDN strings.
     *
     * @param string $data
     * @return string
     */
    public function write_setting($data) {
        if ($this->paramtype === PARAM_INT and $data === '') {
            // Do not complain if '' used instead of 0.
            $data = 0;
        }

        // Try to convert any non-ascii domains to ACE prior to validation - we can't modify anything in validate!
        if (function_exists('idn_to_ascii')) {
            $data = $this->ace_encode($data);
        }

        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }
}

/**
 * Used to validate a textarea used for port numbers.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 Jake Dallimore (jrhdallimore@gmail.com)
 */
class admin_setting_configportlist extends admin_setting_configtextarea {

    /**
     * Validate the contents of the textarea as port numbers.
     * Used to validate a new line separated list of ports collected from a textarea control.
     *
     * @param string $data A list of ports separated by new lines
     * @return mixed bool true for success or string:error on failure
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }
        $ports = explode("\n", $data);
        $badentries = [];
        foreach ($ports as $port) {
            $port = trim($port);
            if (empty($port)) {
                return get_string('validateemptylineerror', 'admin');
            }

            // Is the string a valid integer number?
            if (strval(intval($port)) !== $port || intval($port) <= 0) {
                $badentries[] = $port;
            }
        }
        if ($badentries) {
            return get_string('validateerrorlist', 'admin', $badentries);
        }
        return true;
    }
}


/**
 * An admin setting for selecting one or more users who have a capability
 * in the system context
 *
 * An admin setting for selecting one or more users, who have a particular capability
 * in the system context. Warning, make sure the list will never be too long. There is
 * no paging or searching of this list.
 *
 * To correctly get a list of users from this config setting, you need to call the
 * get_users_from_config($CFG->mysetting, $capability); function in moodlelib.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_users_with_capability extends admin_setting_configmultiselect {
    /** @var string The capabilities name */
    protected $capability;
    /** @var int include admin users too */
    protected $includeadmins;

    /**
     * Constructor.
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array $defaultsetting array of usernames
     * @param string $capability string capability name.
     * @param bool $includeadmins include administrators
     */
    function __construct($name, $visiblename, $description, $defaultsetting, $capability, $includeadmins = true) {
        $this->capability    = $capability;
        $this->includeadmins = $includeadmins;
        parent::__construct($name, $visiblename, $description, $defaultsetting, NULL);
    }

    /**
     * Load all of the uses who have the capability into choice array
     *
     * @return bool Always returns true
     */
    function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        list($sort, $sortparams) = users_order_by_sql('u');
        if (!empty($sortparams)) {
            throw new coding_exception('users_order_by_sql returned some query parameters. ' .
                    'This is unexpected, and a problem because there is no way to pass these ' .
                    'parameters to get_users_by_capability. See MDL-34657.');
        }
        $userfields = 'u.id, u.username, ' . get_all_user_name_fields(true, 'u');
        $users = get_users_by_capability(context_system::instance(), $this->capability, $userfields, $sort);
        $this->choices = array(
            '$@NONE@$' => get_string('nobody'),
            '$@ALL@$' => get_string('everyonewhocan', 'admin', get_capability_string($this->capability)),
        );
        if ($this->includeadmins) {
            $admins = get_admins();
            foreach ($admins as $user) {
                $this->choices[$user->id] = fullname($user);
            }
        }
        if (is_array($users)) {
            foreach ($users as $user) {
                $this->choices[$user->id] = fullname($user);
            }
        }
        return true;
    }

    /**
     * Returns the default setting for class
     *
     * @return mixed Array, or string. Empty string if no default
     */
    public function get_defaultsetting() {
        $this->load_choices();
        $defaultsetting = parent::get_defaultsetting();
        if (empty($defaultsetting)) {
            return array('$@NONE@$');
        } else if (array_key_exists($defaultsetting, $this->choices)) {
                return $defaultsetting;
            } else {
                return '';
            }
    }

    /**
     * Returns the current setting
     *
     * @return mixed array or string
     */
    public function get_setting() {
        $result = parent::get_setting();
        if ($result === null) {
            // this is necessary for settings upgrade
            return null;
        }
        if (empty($result)) {
            $result = array('$@NONE@$');
        }
        return $result;
    }

    /**
     * Save the chosen setting provided as $data
     *
     * @param array $data
     * @return mixed string or array
     */
    public function write_setting($data) {
    // If all is selected, remove any explicit options.
        if (in_array('$@ALL@$', $data)) {
            $data = array('$@ALL@$');
        }
        // None never needs to be written to the DB.
        if (in_array('$@NONE@$', $data)) {
            unset($data[array_search('$@NONE@$', $data)]);
        }
        return parent::write_setting($data);
    }
}


/**
 * Special checkbox for calendar - resets SESSION vars.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_adminseesall extends admin_setting_configcheckbox {
    /**
     * Calls the parent::__construct with default values
     *
     * name =>  calendar_adminseesall
     * visiblename => get_string('adminseesall', 'admin')
     * description => get_string('helpadminseesall', 'admin')
     * defaultsetting => 0
     */
    public function __construct() {
        parent::__construct('calendar_adminseesall', get_string('adminseesall', 'admin'),
            get_string('helpadminseesall', 'admin'), '0');
    }

    /**
     * Stores the setting passed in $data
     *
     * @param mixed gets converted to string for comparison
     * @return string empty string or error message
     */
    public function write_setting($data) {
        global $SESSION;
        return parent::write_setting($data);
    }
}

/**
 * Special select for settings that are altered in setup.php and can not be altered on the fly
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_selectsetup extends admin_setting_configselect {
    /**
     * Reads the setting directly from the database
     *
     * @return mixed
     */
    public function get_setting() {
    // read directly from db!
        return get_config(NULL, $this->name);
    }

    /**
     * Save the setting passed in $data
     *
     * @param string $data The setting to save
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $CFG;
        // do not change active CFG setting!
        $current = $CFG->{$this->name};
        $result = parent::write_setting($data);
        $CFG->{$this->name} = $current;
        return $result;
    }
}


/**
 * Special select for frontpage - stores data in course table
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_sitesetselect extends admin_setting_configselect {
    /**
     * Returns the site name for the selected site
     *
     * @see get_site()
     * @return string The site name of the selected site
     */
    public function get_setting() {
        $site = course_get_format(get_site())->get_course();
        return $site->{$this->name};
    }

    /**
     * Updates the database and save the setting
     *
     * @param string data
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $DB, $SITE, $COURSE;
        if (!in_array($data, array_keys($this->choices))) {
            return get_string('errorsetting', 'admin');
        }
        $record = new stdClass();
        $record->id           = SITEID;
        $temp                 = $this->name;
        $record->$temp        = $data;
        $record->timemodified = time();

        course_get_format($SITE)->update_course_format_options($record);
        $DB->update_record('course', $record);

        // Reset caches.
        $SITE = $DB->get_record('course', array('id'=>$SITE->id), '*', MUST_EXIST);
        if ($SITE->id == $COURSE->id) {
            $COURSE = $SITE;
        }
        format_base::reset_course_cache($SITE->id);

        return '';

    }
}


/**
 * Select for blog's bloglevel setting: if set to 0, will set blog_menu
 * block to hidden.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_bloglevel extends admin_setting_configselect {
    /**
     * Updates the database and save the setting
     *
     * @param string data
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $DB, $CFG;
        if ($data == 0) {
            $blogblocks = $DB->get_records_select('block', "name LIKE 'blog_%' AND visible = 1");
            foreach ($blogblocks as $block) {
                $DB->set_field('block', 'visible', 0, array('id' => $block->id));
            }
        } else {
            // reenable all blocks only when switching from disabled blogs
            if (isset($CFG->bloglevel) and $CFG->bloglevel == 0) {
                $blogblocks = $DB->get_records_select('block', "name LIKE 'blog_%' AND visible = 0");
                foreach ($blogblocks as $block) {
                    $DB->set_field('block', 'visible', 1, array('id' => $block->id));
                }
            }
        }
        return parent::write_setting($data);
    }
}


/**
 * Special select - lists on the frontpage - hacky
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_courselist_frontpage extends admin_setting {
    /** @var array Array of choices value=>label */
    public $choices;

    /**
     * Construct override, requires one param
     *
     * @param bool $loggedin Is the user logged in
     */
    public function __construct($loggedin) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        $name        = 'frontpage'.($loggedin ? 'loggedin' : '');
        $visiblename = get_string('frontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $description = get_string('configfrontpage'.($loggedin ? 'loggedin' : ''),'admin');
        $defaults    = array(FRONTPAGEALLCOURSELIST);
        parent::__construct($name, $visiblename, $description, $defaults);
    }

    /**
     * Loads the choices available
     *
     * @return bool always returns true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array(FRONTPAGENEWS          => get_string('frontpagenews'),
            FRONTPAGEALLCOURSELIST => get_string('frontpagecourselist'),
            FRONTPAGEENROLLEDCOURSELIST => get_string('frontpageenrolledcourselist'),
            FRONTPAGECATEGORYNAMES => get_string('frontpagecategorynames'),
            FRONTPAGECATEGORYCOMBO => get_string('frontpagecategorycombo'),
            FRONTPAGECOURSESEARCH  => get_string('frontpagecoursesearch'),
            'none'                 => get_string('none'));
        if ($this->name === 'frontpage') {
            unset($this->choices[FRONTPAGEENROLLEDCOURSELIST]);
        }
        return true;
    }

    /**
     * Returns the selected settings
     *
     * @param mixed array or setting or null
     */
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        return explode(',', $result);
    }

    /**
     * Save the selected options
     *
     * @param array $data
     * @return mixed empty string (data is not an array) or bool true=success false=failure
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        $this->load_choices();
        $save = array();
        foreach($data as $datum) {
            if ($datum == 'none' or !array_key_exists($datum, $this->choices)) {
                continue;
            }
            $save[$datum] = $datum; // no duplicates
        }
        return ($this->config_write($this->name, implode(',', $save)) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Return XHTML select field and wrapping div
     *
     * @todo Add vartype handling to make sure $data is an array
     * @param array $data Array of elements to select by default
     * @return string XHTML select field and wrapping div
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $this->load_choices();
        $currentsetting = array();
        foreach ($data as $key) {
            if ($key != 'none' and array_key_exists($key, $this->choices)) {
                $currentsetting[] = $key; // already selected first
            }
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
        ];

        $options = $this->choices;
        $selects = [];
        for ($i = 0; $i < count($this->choices) - 1; $i++) {
            if (!array_key_exists($i, $currentsetting)) {
                $currentsetting[$i] = 'none';
            }
            $selects[] = [
                'key' => $i,
                'options' => array_map(function($option) use ($options, $currentsetting, $i) {
                    return [
                        'name' => $options[$option],
                        'value' => $option,
                        'selected' => $currentsetting[$i] == $option
                    ];
                }, array_keys($options))
            ];
        }
        $context->selects = $selects;

        $element = $OUTPUT->render_from_template('core_admin/setting_courselist_frontpage', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', null, $query);
    }
}


/**
 * Special checkbox for frontpage - stores data in course table
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_sitesetcheckbox extends admin_setting_configcheckbox {
    /**
     * Returns the current sites name
     *
     * @return string
     */
    public function get_setting() {
        $site = course_get_format(get_site())->get_course();
        return $site->{$this->name};
    }

    /**
     * Save the selected setting
     *
     * @param string $data The selected site
     * @return string empty string or error message
     */
    public function write_setting($data) {
        global $DB, $SITE, $COURSE;
        $record = new stdClass();
        $record->id            = $SITE->id;
        $record->{$this->name} = ($data == '1' ? 1 : 0);
        $record->timemodified  = time();

        course_get_format($SITE)->update_course_format_options($record);
        $DB->update_record('course', $record);

        // Reset caches.
        $SITE = $DB->get_record('course', array('id'=>$SITE->id), '*', MUST_EXIST);
        if ($SITE->id == $COURSE->id) {
            $COURSE = $SITE;
        }
        format_base::reset_course_cache($SITE->id);

        return '';
    }
}

/**
 * Special text for frontpage - stores data in course table.
 * Empty string means not set here. Manual setting is required.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_sitesettext extends admin_setting_configtext {

    /**
     * Constructor.
     */
    public function __construct() {
        call_user_func_array(['parent', '__construct'], func_get_args());
        $this->set_force_ltr(false);
    }

    /**
     * Return the current setting
     *
     * @return mixed string or null
     */
    public function get_setting() {
        $site = course_get_format(get_site())->get_course();
        return $site->{$this->name} != '' ? $site->{$this->name} : NULL;
    }

    /**
     * Validate the selected data
     *
     * @param string $data The selected value to validate
     * @return mixed true or message string
     */
    public function validate($data) {
        global $DB, $SITE;
        $cleaned = clean_param($data, PARAM_TEXT);
        if ($cleaned === '') {
            return get_string('required');
        }
        if ($this->name ==='shortname' &&
                $DB->record_exists_sql('SELECT id from {course} WHERE shortname = ? AND id <> ?', array($data, $SITE->id))) {
            return get_string('shortnametaken', 'error', $data);
        }
        if ("$data" == "$cleaned") { // implicit conversion to string is needed to do exact comparison
            return true;
        } else {
            return get_string('validateerror', 'admin');
        }
    }

    /**
     * Save the selected setting
     *
     * @param string $data The selected value
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $DB, $SITE, $COURSE;
        $data = trim($data);
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        $record = new stdClass();
        $record->id            = $SITE->id;
        $record->{$this->name} = $data;
        $record->timemodified  = time();

        course_get_format($SITE)->update_course_format_options($record);
        $DB->update_record('course', $record);

        // Reset caches.
        $SITE = $DB->get_record('course', array('id'=>$SITE->id), '*', MUST_EXIST);
        if ($SITE->id == $COURSE->id) {
            $COURSE = $SITE;
        }
        format_base::reset_course_cache($SITE->id);

        return '';
    }
}


/**
 * Special text editor for site description.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_frontpagedesc extends admin_setting_confightmleditor {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('summary', get_string('frontpagedescription'), get_string('frontpagedescriptionhelp'), null,
            PARAM_RAW, 60, 15);
    }

    /**
     * Return the current setting
     * @return string The current setting
     */
    public function get_setting() {
        $site = course_get_format(get_site())->get_course();
        return $site->{$this->name};
    }

    /**
     * Save the new setting
     *
     * @param string $data The new value to save
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $DB, $SITE, $COURSE;
        $record = new stdClass();
        $record->id            = $SITE->id;
        $record->{$this->name} = $data;
        $record->timemodified  = time();

        course_get_format($SITE)->update_course_format_options($record);
        $DB->update_record('course', $record);

        // Reset caches.
        $SITE = $DB->get_record('course', array('id'=>$SITE->id), '*', MUST_EXIST);
        if ($SITE->id == $COURSE->id) {
            $COURSE = $SITE;
        }
        format_base::reset_course_cache($SITE->id);

        return '';
    }
}


/**
 * Administration interface for emoticon_manager settings.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_emoticons extends admin_setting {

    /**
     * Calls parent::__construct with specific args
     */
    public function __construct() {
        global $CFG;

        $manager = get_emoticon_manager();
        $defaults = $this->prepare_form_data($manager->default_emoticons());
        parent::__construct('emoticons', get_string('emoticons', 'admin'), get_string('emoticons_desc', 'admin'), $defaults);
    }

    /**
     * Return the current setting(s)
     *
     * @return array Current settings array
     */
    public function get_setting() {
        global $CFG;

        $manager = get_emoticon_manager();

        $config = $this->config_read($this->name);
        if (is_null($config)) {
            return null;
        }

        $config = $manager->decode_stored_config($config);
        if (is_null($config)) {
            return null;
        }

        return $this->prepare_form_data($config);
    }

    /**
     * Save selected settings
     *
     * @param array $data Array of settings to save
     * @return bool
     */
    public function write_setting($data) {

        $manager = get_emoticon_manager();
        $emoticons = $this->process_form_data($data);

        if ($emoticons === false) {
            return false;
        }

        if ($this->config_write($this->name, $manager->encode_stored_config($emoticons))) {
            return ''; // success
        } else {
            return get_string('errorsetting', 'admin') . $this->visiblename . html_writer::empty_tag('br');
        }
    }

    /**
     * Return XHTML field(s) for options
     *
     * @param array $data Array of options to set in HTML
     * @return string XHTML string for the fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = (object) [
            'name' => $this->get_full_name(),
            'emoticons' => [],
            'forceltr' => true,
        ];

        $i = 0;
        foreach ($data as $field => $value) {

            // When $i == 0: text.
            // When $i == 1: imagename.
            // When $i == 2: imagecomponent.
            // When $i == 3: altidentifier.
            // When $i == 4: altcomponent.
            $fields[$i] = (object) [
                'field' => $field,
                'value' => $value,
                'index' => $i
            ];
            $i++;

            if ($i > 4) {
                $icon = null;
                if (!empty($fields[1]->value)) {
                    if (get_string_manager()->string_exists($fields[3]->value, $fields[4]->value)) {
                        $alt = get_string($fields[3]->value, $fields[4]->value);
                    } else {
                        $alt = $fields[0]->value;
                    }
                    $icon = new pix_emoticon($fields[1]->value, $alt, $fields[2]->value);
                }
                $context->emoticons[] = [
                    'fields' => $fields,
                    'icon' => $icon ? $icon->export_for_template($OUTPUT) : null
                ];
                $fields = [];
                $i = 0;
            }
        }

        $context->reseturl = new moodle_url('/admin/resetemoticons.php');
        $element = $OUTPUT->render_from_template('core_admin/setting_emoticons', $context);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', NULL, $query);
    }

    /**
     * Converts the array of emoticon objects provided by {@see emoticon_manager} into admin settings form data
     *
     * @see self::process_form_data()
     * @param array $emoticons array of emoticon objects as returned by {@see emoticon_manager}
     * @return array of form fields and their values
     */
    protected function prepare_form_data(array $emoticons) {

        $form = array();
        $i = 0;
        foreach ($emoticons as $emoticon) {
            $form['text'.$i]            = $emoticon->text;
            $form['imagename'.$i]       = $emoticon->imagename;
            $form['imagecomponent'.$i]  = $emoticon->imagecomponent;
            $form['altidentifier'.$i]   = $emoticon->altidentifier;
            $form['altcomponent'.$i]    = $emoticon->altcomponent;
            $i++;
        }
        // add one more blank field set for new object
        $form['text'.$i]            = '';
        $form['imagename'.$i]       = '';
        $form['imagecomponent'.$i]  = '';
        $form['altidentifier'.$i]   = '';
        $form['altcomponent'.$i]    = '';

        return $form;
    }

    /**
     * Converts the data from admin settings form into an array of emoticon objects
     *
     * @see self::prepare_form_data()
     * @param array $data array of admin form fields and values
     * @return false|array of emoticon objects
     */
    protected function process_form_data(array $form) {

        $count = count($form); // number of form field values

        if ($count % 5) {
            // we must get five fields per emoticon object
            return false;
        }

        $emoticons = array();
        for ($i = 0; $i < $count / 5; $i++) {
            $emoticon                   = new stdClass();
            $emoticon->text             = clean_param(trim($form['text'.$i]), PARAM_NOTAGS);
            $emoticon->imagename        = clean_param(trim($form['imagename'.$i]), PARAM_PATH);
            $emoticon->imagecomponent   = clean_param(trim($form['imagecomponent'.$i]), PARAM_COMPONENT);
            $emoticon->altidentifier    = clean_param(trim($form['altidentifier'.$i]), PARAM_STRINGID);
            $emoticon->altcomponent     = clean_param(trim($form['altcomponent'.$i]), PARAM_COMPONENT);

            if (strpos($emoticon->text, ':/') !== false or strpos($emoticon->text, '//') !== false) {
                // prevent from breaking http://url.addresses by accident
                $emoticon->text = '';
            }

            if (strlen($emoticon->text) < 2) {
                // do not allow single character emoticons
                $emoticon->text = '';
            }

            if (preg_match('/^[a-zA-Z]+[a-zA-Z0-9]*$/', $emoticon->text)) {
                // emoticon text must contain some non-alphanumeric character to prevent
                // breaking HTML tags
                $emoticon->text = '';
            }

            if ($emoticon->text !== '' and $emoticon->imagename !== '' and $emoticon->imagecomponent !== '') {
                $emoticons[] = $emoticon;
            }
        }
        return $emoticons;
    }

}


/**
 * Special setting for limiting of the list of available languages.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_langlist extends admin_setting_configtext {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('langlist', get_string('langlist', 'admin'), get_string('configlanglist', 'admin'), '', PARAM_NOTAGS);
    }

    /**
     * Save the new setting
     *
     * @param string $data The new setting
     * @return bool
     */
    public function write_setting($data) {
        $return = parent::write_setting($data);
        get_string_manager()->reset_caches();
        return $return;
    }
}


/**
 * Selection of one of the recognised countries using the list
 * returned by {@link get_list_of_countries()}.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_settings_country_select extends admin_setting_configselect {
    protected $includeall;
    public function __construct($name, $visiblename, $description, $defaultsetting, $includeall=false) {
        $this->includeall = $includeall;
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    /**
     * Lazy-load the available choices for the select box
     */
    public function load_choices() {
        global $CFG;
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array_merge(
                array('0' => get_string('choosedots')),
                get_string_manager()->get_list_of_countries($this->includeall));
        return true;
    }
}


/**
 * admin_setting_configselect for the default number of sections in a course,
 * simply so we can lazy-load the choices.
 *
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_settings_num_course_sections extends admin_setting_configselect {
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, array());
    }

    /** Lazy-load the available choices for the select box */
    public function load_choices() {
        $max = get_config('moodlecourse', 'maxsections');
        if (!isset($max) || !is_numeric($max)) {
            $max = 52;
        }
        for ($i = 0; $i <= $max; $i++) {
            $this->choices[$i] = "$i";
        }
        return true;
    }
}


/**
 * Course category selection
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_settings_coursecat_select extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, NULL);
    }

    /**
     * Load the available choices for the select box
     *
     * @return bool
     */
    public function load_choices() {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = make_categories_options();
        return true;
    }
}


/**
 * Special control for selecting days to backup
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_backupdays extends admin_setting_configmulticheckbox2 {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('backup_auto_weekdays', get_string('automatedbackupschedule','backup'), get_string('automatedbackupschedulehelp','backup'), array(), NULL);
        $this->plugin = 'backup';
    }

    /**
     * Load the available choices for the select box
     *
     * @return bool Always returns true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();
        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        foreach ($days as $day) {
            $this->choices[$day] = get_string($day, 'calendar');
        }
        return true;
    }
}

/**
 * Special setting for backup auto destination.
 *
 * @package    core
 * @subpackage admin
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_backup_auto_destination extends admin_setting_configdirectory {

    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        parent::__construct('backup/backup_auto_destination', new lang_string('saveto'), new lang_string('backupsavetohelp'), '');
    }

    /**
     * Check if the directory must be set, depending on backup/backup_auto_storage.
     *
     * Note: backup/backup_auto_storage must be specified BEFORE this setting otherwise
     * there will be conflicts if this validation happens before the other one.
     *
     * @param string $data Form data.
     * @return string Empty when no errors.
     */
    public function write_setting($data) {
        $storage = (int) get_config('backup', 'backup_auto_storage');
        if ($storage !== 0) {
            if (empty($data) || !file_exists($data) || !is_dir($data) || !is_writable($data) ) {
                // The directory must exist and be writable.
                return get_string('backuperrorinvaliddestination');
            }
        }
        return parent::write_setting($data);
    }
}


/**
 * Special debug setting
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_debug extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('debug', get_string('debug', 'admin'), get_string('configdebug', 'admin'), DEBUG_NONE, NULL);
    }

    /**
     * Load the available choices for the select box
     *
     * @return bool
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array(DEBUG_NONE      => get_string('debugnone', 'admin'),
            DEBUG_MINIMAL   => get_string('debugminimal', 'admin'),
            DEBUG_NORMAL    => get_string('debugnormal', 'admin'),
            DEBUG_ALL       => get_string('debugall', 'admin'),
            DEBUG_DEVELOPER => get_string('debugdeveloper', 'admin'));
        return true;
    }
}


/**
 * Special admin control
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_calendar_weekend extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $name = 'calendar_weekend';
        $visiblename = get_string('calendar_weekend', 'admin');
        $description = get_string('helpweekenddays', 'admin');
        $default = array ('0', '6'); // Saturdays and Sundays
        parent::__construct($name, $visiblename, $description, $default);
    }

    /**
     * Gets the current settings as an array
     *
     * @return mixed Null if none, else array of settings
     */
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if ($result === '') {
            return array();
        }
        $settings = array();
        for ($i=0; $i<7; $i++) {
            if ($result & (1 << $i)) {
                $settings[] = $i;
            }
        }
        return $settings;
    }

    /**
     * Save the new settings
     *
     * @param array $data Array of new settings
     * @return bool
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return '';
        }
        unset($data['xxxxx']);
        $result = 0;
        foreach($data as $index) {
            $result |= 1 << $index;
        }
        return ($this->config_write($this->name, $result) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Return XHTML to display the control
     *
     * @param array $data array of selected days
     * @param string $query
     * @return string XHTML for display (field + wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        // The order matters very much because of the implied numeric keys.
        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $context = (object) [
            'name' => $this->get_full_name(),
            'id' => $this->get_id(),
            'days' => array_map(function($index) use ($days, $data) {
                return [
                    'index' => $index,
                    'label' => get_string($days[$index], 'calendar'),
                    'checked' => in_array($index, $data)
                ];
            }, array_keys($days))
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_special_calendar_weekend', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', NULL, $query);

    }
}


/**
 * Admin setting that allows a user to pick a behaviour.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_question_behaviour extends admin_setting_configselect {
    /**
     * @param string $name name of config variable
     * @param string $visiblename display name
     * @param string $description description
     * @param string $default default.
     */
    public function __construct($name, $visiblename, $description, $default) {
        parent::__construct($name, $visiblename, $description, $default, null);
    }

    /**
     * Load list of behaviours as choices
     * @return bool true => success, false => error.
     */
    public function load_choices() {
        global $CFG;
        require_once($CFG->dirroot . '/question/engine/lib.php');
        $this->choices = question_engine::get_behaviour_options('');
        return true;
    }
}


/**
 * Admin setting that allows a user to pick appropriate roles for something.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_pickroles extends admin_setting_configmulticheckbox {
    /** @var array Array of capabilities which identify roles */
    private $types;

    /**
     * @param string $name Name of config variable
     * @param string $visiblename Display name
     * @param string $description Description
     * @param array $types Array of archetypes which identify
     *              roles that will be enabled by default.
     */
    public function __construct($name, $visiblename, $description, $types) {
        parent::__construct($name, $visiblename, $description, NULL, NULL);
        $this->types = $types;
    }

    /**
     * Load roles as choices
     *
     * @return bool true=>success, false=>error
     */
    public function load_choices() {
        global $CFG, $DB;
        if (during_initial_install()) {
            return false;
        }
        if (is_array($this->choices)) {
            return true;
        }
        if ($roles = get_all_roles()) {
            $this->choices = role_fix_names($roles, null, ROLENAME_ORIGINAL, true);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the default setting for this control
     *
     * @return array Array of default settings
     */
    public function get_defaultsetting() {
        global $CFG;

        if (during_initial_install()) {
            return null;
        }
        $result = array();
        foreach($this->types as $archetype) {
            if ($caproles = get_archetype_roles($archetype)) {
                foreach ($caproles as $caprole) {
                    $result[$caprole->id] = 1;
                }
            }
        }
        return $result;
    }
}


/**
 * Admin setting that is a list of installed filter plugins.
 *
 * @copyright 2015 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_pickfilters extends admin_setting_configmulticheckbox {

    /**
     * Constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings
     *      that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array $default the default. E.g. array('urltolink' => 1, 'emoticons' => 1)
     */
    public function __construct($name, $visiblename, $description, $default) {
        if (empty($default)) {
            $default = array();
        }
        $this->load_choices();
        foreach ($default as $plugin) {
            if (!isset($this->choices[$plugin])) {
                unset($default[$plugin]);
            }
        }
        parent::__construct($name, $visiblename, $description, $default, null);
    }

    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        foreach (core_component::get_plugin_list('filter') as $plugin => $unused) {
            $this->choices[$plugin] = filter_get_name($plugin);
        }
        return true;
    }
}


/**
 * Text field with an advanced checkbox, that controls a additional $name.'_adv' setting.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext_with_advanced extends admin_setting_configtext {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, '__construct'=>bool)
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $size=null) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $paramtype, $size);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }
}


/**
 * Checkbox with an advanced checkbox that controls an additional $name.'_adv' config setting.
 *
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcheckbox_with_advanced extends admin_setting_configcheckbox {

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, 'adv'=>bool)
     * @param string $yes value used when checked
     * @param string $no value used when not checked
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0') {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $yes, $no);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }

}


/**
 * Checkbox with an advanced checkbox that controls an additional $name.'_locked' config setting.
 *
 * This is nearly a copy/paste of admin_setting_configcheckbox_with_adv
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcheckbox_with_lock extends admin_setting_configcheckbox {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, 'locked'=>bool)
     * @param string $yes value used when checked
     * @param string $no value used when not checked
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0') {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $yes, $no);
        $this->set_locked_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['locked']));
    }

}


/**
 * Dropdown menu with an advanced checkbox, that controls a additional $name.'_adv' setting.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configselect_with_advanced extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $choices);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }

}

/**
 * Select with an advanced checkbox that controls an additional $name.'_locked' config setting.
 *
 * @copyright 2017 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configselect_with_lock extends admin_setting_configselect {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, 'locked'=>bool)
     * @param array $choices array of $value=>$label for each selection
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $choices);
        $this->set_locked_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['locked']));
    }
}


/**
 * Graded roles in gradebook
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_gradebookroles extends admin_setting_pickroles {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('gradebookroles', get_string('gradebookroles', 'admin'),
            get_string('configgradebookroles', 'admin'),
            array('student'));
    }
}


/**
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_regradingcheckbox extends admin_setting_configcheckbox {
    /**
     * Saves the new settings passed in $data
     *
     * @param string $data
     * @return mixed string or Array
     */
    public function write_setting($data) {
        global $CFG, $DB;

        $oldvalue  = $this->config_read($this->name);
        $return    = parent::write_setting($data);
        $newvalue  = $this->config_read($this->name);

        if ($oldvalue !== $newvalue) {
        // force full regrading
            $DB->set_field('grade_items', 'needsupdate', 1, array('needsupdate'=>0));
        }

        return $return;
    }
}


/**
 * Which roles to show on course description page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_coursecontact extends admin_setting_pickroles {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('coursecontact', get_string('coursecontact', 'admin'),
            get_string('coursecontact_desc', 'admin'),
            array('editingteacher'));
        $this->set_updatedcallback(function (){
            cache::make('core', 'coursecontacts')->purge();
        });
    }
}


/**
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_gradelimiting extends admin_setting_configcheckbox {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('unlimitedgrades', get_string('unlimitedgrades', 'grades'),
            get_string('unlimitedgrades_help', 'grades'), '0', '1', '0');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function admin_setting_special_gradelimiting() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Force site regrading
     */
    function regrade_all() {
        global $CFG;
        require_once("$CFG->libdir/gradelib.php");
        grade_force_site_regrading();
    }

    /**
     * Saves the new settings
     *
     * @param mixed $data
     * @return string empty string or error message
     */
    function write_setting($data) {
        $previous = $this->get_setting();

        if ($previous === null) {
            if ($data) {
                $this->regrade_all();
            }
        } else {
            if ($data != $previous) {
                $this->regrade_all();
            }
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

}

/**
 * Special setting for $CFG->grade_minmaxtouse.
 *
 * @package    core
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_grademinmaxtouse extends admin_setting_configselect {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('grade_minmaxtouse', new lang_string('minmaxtouse', 'grades'),
            new lang_string('minmaxtouse_desc', 'grades'), GRADE_MIN_MAX_FROM_GRADE_ITEM,
            array(
                GRADE_MIN_MAX_FROM_GRADE_ITEM => get_string('gradeitemminmax', 'grades'),
                GRADE_MIN_MAX_FROM_GRADE_GRADE => get_string('gradegrademinmax', 'grades')
            )
        );
    }

    /**
     * Saves the new setting.
     *
     * @param mixed $data
     * @return string empty string or error message
     */
    function write_setting($data) {
        global $CFG;

        $previous = $this->get_setting();
        $result = parent::write_setting($data);

        // If saved and the value has changed.
        if (empty($result) && $previous != $data) {
            require_once($CFG->libdir . '/gradelib.php');
            grade_force_site_regrading();
        }

        return $result;
    }

}


/**
 * Primary grade export plugin - has state tracking.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_gradeexport extends admin_setting_configmulticheckbox {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('gradeexport', get_string('gradeexport', 'admin'),
            get_string('configgradeexport', 'admin'), array(), NULL);
    }

    /**
     * Load the available choices for the multicheckbox
     *
     * @return bool always returns true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        if ($plugins = core_component::get_plugin_list('gradeexport')) {
            foreach($plugins as $plugin => $unused) {
                $this->choices[$plugin] = get_string('pluginname', 'gradeexport_'.$plugin);
            }
        }
        return true;
    }
}


/**
 * A setting for setting the default grade point value. Must be an integer between 1 and $CFG->gradepointmax.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_gradepointdefault extends admin_setting_configtext {
    /**
     * Config gradepointmax constructor
     *
     * @param string $name Overidden by "gradepointmax"
     * @param string $visiblename Overridden by "gradepointmax" language string.
     * @param string $description Overridden by "gradepointmax_help" language string.
     * @param string $defaultsetting Not used, overridden by 100.
     * @param mixed $paramtype Overridden by PARAM_INT.
     * @param int $size Overridden by 5.
     */
    public function __construct($name = '', $visiblename = '', $description = '', $defaultsetting = '', $paramtype = PARAM_INT, $size = 5) {
        $name = 'gradepointdefault';
        $visiblename = get_string('gradepointdefault', 'grades');
        $description = get_string('gradepointdefault_help', 'grades');
        $defaultsetting = 100;
        $paramtype = PARAM_INT;
        $size = 5;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    /**
     * Validate data before storage
     * @param string $data The submitted data
     * @return bool|string true if ok, string if error found
     */
    public function validate($data) {
        global $CFG;
        if (((string)(int)$data === (string)$data && $data > 0 && $data <= $CFG->gradepointmax)) {
            return true;
        } else {
            return get_string('gradepointdefault_validateerror', 'grades');
        }
    }
}


/**
 * A setting for setting the maximum grade value. Must be an integer between 1 and 10000.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_gradepointmax extends admin_setting_configtext {

    /**
     * Config gradepointmax constructor
     *
     * @param string $name Overidden by "gradepointmax"
     * @param string $visiblename Overridden by "gradepointmax" language string.
     * @param string $description Overridden by "gradepointmax_help" language string.
     * @param string $defaultsetting Not used, overridden by 100.
     * @param mixed $paramtype Overridden by PARAM_INT.
     * @param int $size Overridden by 5.
     */
    public function __construct($name = '', $visiblename = '', $description = '', $defaultsetting = '', $paramtype = PARAM_INT, $size = 5) {
        $name = 'gradepointmax';
        $visiblename = get_string('gradepointmax', 'grades');
        $description = get_string('gradepointmax_help', 'grades');
        $defaultsetting = 100;
        $paramtype = PARAM_INT;
        $size = 5;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }

    /**
     * Save the selected setting
     *
     * @param string $data The selected site
     * @return string empty string or error message
     */
    public function write_setting($data) {
        if ($data === '') {
            $data = (int)$this->defaultsetting;
        } else {
            $data = $data;
        }
        return parent::write_setting($data);
    }

    /**
     * Validate data before storage
     * @param string $data The submitted data
     * @return bool|string true if ok, string if error found
     */
    public function validate($data) {
        if (((string)(int)$data === (string)$data && $data > 0 && $data <= 10000)) {
            return true;
        } else {
            return get_string('gradepointmax_validateerror', 'grades');
        }
    }

    /**
     * Return an XHTML string for the setting
     * @param array $data Associative array of value=>xx, forced=>xx, adv=>xx
     * @param string $query search query to be highlighted
     * @return string XHTML to display control
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'size' => $this->size,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'attributes' => [
                'maxlength' => 5
            ],
            'forceltr' => $this->get_force_ltr()
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configtext', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }
}


/**
 * Grade category settings
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_gradecat_combo extends admin_setting {
    /** @var array Array of choices */
    public $choices;

    /**
     * Sets choices and calls parent::__construct with passed arguments
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array depending on implementation
     * @param array $choices An array of choices for the control
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        $this->choices = $choices;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the current setting(s) array
     *
     * @return array Array of value=>xx, forced=>xx, adv=>xx
     */
    public function get_setting() {
        global $CFG;

        $value = $this->config_read($this->name);
        $flag  = $this->config_read($this->name.'_flag');

        if (is_null($value) or is_null($flag)) {
            return NULL;
        }

        $flag   = (int)$flag;
        $forced = (boolean)(1 & $flag); // first bit
        $adv    = (boolean)(2 & $flag); // second bit

        return array('value' => $value, 'forced' => $forced, 'adv' => $adv);
    }

    /**
     * Save the new settings passed in $data
     *
     * @todo Add vartype handling to ensure $data is array
     * @param array $data Associative array of value=>xx, forced=>xx, adv=>xx
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $CFG;

        $value  = $data['value'];
        $forced = empty($data['forced']) ? 0 : 1;
        $adv    = empty($data['adv'])    ? 0 : 2;
        $flag   = ($forced | $adv); //bitwise or

        if (!in_array($value, array_keys($this->choices))) {
            return 'Error setting ';
        }

        $oldvalue  = $this->config_read($this->name);
        $oldflag   = (int)$this->config_read($this->name.'_flag');
        $oldforced = (1 & $oldflag); // first bit

        $result1 = $this->config_write($this->name, $value);
        $result2 = $this->config_write($this->name.'_flag', $flag);

        // force regrade if needed
        if ($oldforced != $forced or ($forced and $value != $oldvalue)) {
            require_once($CFG->libdir.'/gradelib.php');
            grade_category::updated_forced_settings();
        }

        if ($result1 and $result2) {
            return '';
        } else {
            return get_string('errorsetting', 'admin');
        }
    }

    /**
     * Return XHTML to display the field and wrapping div
     *
     * @todo Add vartype handling to ensure $data is array
     * @param array $data Associative array of value=>xx, forced=>xx, adv=>xx
     * @param string $query
     * @return string XHTML to display control
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $value  = $data['value'];

        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            $defaultinfo = array();
            if (isset($this->choices[$default['value']])) {
                $defaultinfo[] = $this->choices[$default['value']];
            }
            if (!empty($default['forced'])) {
                $defaultinfo[] = get_string('force');
            }
            if (!empty($default['adv'])) {
                $defaultinfo[] = get_string('advanced');
            }
            $defaultinfo = implode(', ', $defaultinfo);

        } else {
            $defaultinfo = NULL;
        }

        $options = $this->choices;
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'forced' => !empty($data['forced']),
            'advanced' => !empty($data['adv']),
            'options' => array_map(function($option) use ($options, $value) {
                return [
                    'value' => $option,
                    'name' => $options[$option],
                    'selected' => $option == $value
                ];
            }, array_keys($options)),
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_gradecat_combo', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}


/**
 * Selection of grade report in user profiles
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_grade_profilereport extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('grade_profilereport', get_string('profilereport', 'grades'), get_string('profilereport_help', 'grades'), 'user', null);
    }

    /**
     * Loads an array of choices for the configselect control
     *
     * @return bool always return true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        foreach (core_component::get_plugin_list('gradereport') as $plugin => $plugindir) {
            if (file_exists($plugindir.'/lib.php')) {
                require_once($plugindir.'/lib.php');
                $functionname = 'grade_report_'.$plugin.'_profilereport';
                if (function_exists($functionname)) {
                    $this->choices[$plugin] = get_string('pluginname', 'gradereport_'.$plugin);
                }
            }
        }
        return true;
    }
}

/**
 * Provides a selection of grade reports to be used for "grades".
 *
 * @copyright 2015 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_my_grades_report extends admin_setting_configselect {

    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        parent::__construct('grade_mygrades_report', new lang_string('mygrades', 'grades'),
                new lang_string('mygrades_desc', 'grades'), 'overview', null);
    }

    /**
     * Loads an array of choices for the configselect control.
     *
     * @return bool always returns true.
     */
    public function load_choices() {
        global $CFG; // Remove this line and behold the horror of behat test failures!
        $this->choices = array();
        foreach (core_component::get_plugin_list('gradereport') as $plugin => $plugindir) {
            if (file_exists($plugindir . '/lib.php')) {
                require_once($plugindir . '/lib.php');
                // Check to see if the class exists. Check the correct plugin convention first.
                if (class_exists('gradereport_' . $plugin)) {
                    $classname = 'gradereport_' . $plugin;
                } else if (class_exists('grade_report_' . $plugin)) {
                    // We are using the old plugin naming convention.
                    $classname = 'grade_report_' . $plugin;
                } else {
                    continue;
                }
                if ($classname::supports_mygrades()) {
                    $this->choices[$plugin] = get_string('pluginname', 'gradereport_' . $plugin);
                }
            }
        }
        // Add an option to specify an external url.
        $this->choices['external'] = get_string('externalurl', 'grades');
        return true;
    }
}

/**
 * Special class for register auth selection
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_registerauth extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('registerauth', get_string('selfregistration', 'auth'), get_string('selfregistration_help', 'auth'), '', null);
    }

    /**
     * Returns the default option
     *
     * @return string empty or default option
     */
    public function get_defaultsetting() {
        $this->load_choices();
        $defaultsetting = parent::get_defaultsetting();
        if (array_key_exists($defaultsetting, $this->choices)) {
            return $defaultsetting;
        } else {
            return '';
        }
    }

    /**
     * Loads the possible choices for the array
     *
     * @return bool always returns true
     */
    public function load_choices() {
        global $CFG;

        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();
        $this->choices[''] = get_string('disable');

        $authsenabled = get_enabled_auth_plugins(true);

        foreach ($authsenabled as $auth) {
            $authplugin = get_auth_plugin($auth);
            if (!$authplugin->can_signup()) {
                continue;
            }
            // Get the auth title (from core or own auth lang files)
            $authtitle = $authplugin->get_title();
            $this->choices[$auth] = $authtitle;
        }
        return true;
    }
}


/**
 * General plugins manager
 */
class admin_page_pluginsoverview extends admin_externalpage {

    /**
     * Sets basic information about the external page
     */
    public function __construct() {
        global $CFG;
        parent::__construct('pluginsoverview', get_string('pluginsoverview', 'core_admin'),
            "$CFG->wwwroot/$CFG->admin/plugins.php");
    }
}

/**
 * Module manage page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_managemods extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('managemodules', get_string('modsettings', 'admin'), "$CFG->wwwroot/$CFG->admin/modules.php");
    }

    /**
     * Try to find the specified module
     *
     * @param string $query The module to search for
     * @return array
     */
    public function search($query) {
        global $CFG, $DB;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if ($modules = $DB->get_records('modules')) {
            foreach ($modules as $module) {
                if (!file_exists("$CFG->dirroot/mod/$module->name/lib.php")) {
                    continue;
                }
                if (strpos($module->name, $query) !== false) {
                    $found = true;
                    break;
                }
                $strmodulename = get_string('modulename', $module->name);
                if (strpos(core_text::strtolower($strmodulename), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}


/**
 * Special class for enrol plugins management.
 *
 * @copyright 2010 Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_manageenrols extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('enrolsui', get_string('manageenrols', 'enrol'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Checks if $query is one of the available enrol plugins
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $query = core_text::strtolower($query);
        $enrols = enrol_get_plugins(false);
        foreach ($enrols as $name=>$enrol) {
            $localised = get_string('pluginname', 'enrol_'.$name);
            if (strpos(core_text::strtolower($name), $query) !== false) {
                return true;
            }
            if (strpos(core_text::strtolower($localised), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $DB, $PAGE;

        // Display strings.
        $strup        = get_string('up');
        $strdown      = get_string('down');
        $strsettings  = get_string('settings');
        $strenable    = get_string('enable');
        $strdisable   = get_string('disable');
        $struninstall = get_string('uninstallplugin', 'core_admin');
        $strusage     = get_string('enrolusage', 'enrol');
        $strversion   = get_string('version');
        $strtest      = get_string('testsettings', 'core_enrol');

        $pluginmanager = core_plugin_manager::instance();

        $enrols_available = enrol_get_plugins(false);
        $active_enrols    = enrol_get_plugins(true);

        $allenrols = array();
        foreach ($active_enrols as $key=>$enrol) {
            $allenrols[$key] = true;
        }
        foreach ($enrols_available as $key=>$enrol) {
            $allenrols[$key] = true;
        }
        // Now find all borked plugins and at least allow then to uninstall.
        $condidates = $DB->get_fieldset_sql("SELECT DISTINCT enrol FROM {enrol}");
        foreach ($condidates as $candidate) {
            if (empty($allenrols[$candidate])) {
                $allenrols[$candidate] = true;
            }
        }

        $return = $OUTPUT->heading(get_string('actenrolshhdr', 'enrol'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox enrolsui');

        $table = new html_table();
        $table->head  = array(get_string('name'), $strusage, $strversion, $strenable, $strup.'/'.$strdown, $strsettings, $strtest, $struninstall);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'courseenrolmentplugins';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data  = array();

        // Iterate through enrol plugins and add to the display table.
        $updowncount = 1;
        $enrolcount = count($active_enrols);
        $url = new moodle_url('/admin/enrol.php', array('sesskey'=>sesskey()));
        $printed = array();
        foreach($allenrols as $enrol => $unused) {
            $plugininfo = $pluginmanager->get_plugin_info('enrol_'.$enrol);
            $version = get_config('enrol_'.$enrol, 'version');
            if ($version === false) {
                $version = '';
            }

            if (get_string_manager()->string_exists('pluginname', 'enrol_'.$enrol)) {
                $name = get_string('pluginname', 'enrol_'.$enrol);
            } else {
                $name = $enrol;
            }
            // Usage.
            $ci = $DB->count_records('enrol', array('enrol'=>$enrol));
            $cp = $DB->count_records_select('user_enrolments', "enrolid IN (SELECT id FROM {enrol} WHERE enrol = ?)", array($enrol));
            $usage = "$ci / $cp";

            // Hide/show links.
            $class = '';
            if (isset($active_enrols[$enrol])) {
                $aurl = new moodle_url($url, array('action'=>'disable', 'enrol'=>$enrol));
                $hideshow = "<a href=\"$aurl\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', $strdisable) . '</a>';
                $enabled = true;
                $displayname = $name;
            } else if (isset($enrols_available[$enrol])) {
                $aurl = new moodle_url($url, array('action'=>'enable', 'enrol'=>$enrol));
                $hideshow = "<a href=\"$aurl\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', $strenable) . '</a>';
                $enabled = false;
                $displayname = $name;
                $class = 'dimmed_text';
            } else {
                $hideshow = '';
                $enabled = false;
                $displayname = '<span class="notifyproblem">'.$name.'</span>';
            }
            if ($PAGE->theme->resolve_image_location('icon', 'enrol_' . $name, false)) {
                $icon = $OUTPUT->pix_icon('icon', '', 'enrol_' . $name, array('class' => 'icon pluginicon'));
            } else {
                $icon = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'icon pluginicon noicon'));
            }

            // Up/down link (only if enrol is enabled).
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $aurl = new moodle_url($url, array('action'=>'up', 'enrol'=>$enrol));
                    $updown .= "<a href=\"$aurl\">";
                    $updown .= $OUTPUT->pix_icon('t/up', $strup) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                if ($updowncount < $enrolcount) {
                    $aurl = new moodle_url($url, array('action'=>'down', 'enrol'=>$enrol));
                    $updown .= "<a href=\"$aurl\">";
                    $updown .= $OUTPUT->pix_icon('t/down', $strdown) . '</a>&nbsp;';
                } else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                ++$updowncount;
            }

            // Add settings link.
            if (!$version) {
                $settings = '';
            } else if ($surl = $plugininfo->get_settings_url()) {
                $settings = html_writer::link($surl, $strsettings);
            } else {
                $settings = '';
            }

            // Add uninstall info.
            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('enrol_'.$enrol, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $struninstall);
            }

            $test = '';
            if (!empty($enrols_available[$enrol]) and method_exists($enrols_available[$enrol], 'test_settings')) {
                $testsettingsurl = new moodle_url('/enrol/test_settings.php', array('enrol'=>$enrol, 'sesskey'=>sesskey()));
                $test = html_writer::link($testsettingsurl, $strtest);
            }

            // Add a row to the table.
            $row = new html_table_row(array($icon.$displayname, $usage, $version, $hideshow, $updown, $settings, $test, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;

            $printed[$enrol] = true;
        }

        $return .= html_writer::table($table);
        $return .= get_string('configenrolplugins', 'enrol').'<br />'.get_string('tablenosave', 'admin');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}


/**
 * Blocks manage page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_manageblocks extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('manageblocks', get_string('blocksettings', 'admin'), "$CFG->wwwroot/$CFG->admin/blocks.php");
    }

    /**
     * Search for a specific block
     *
     * @param string $query The string to search for
     * @return array
     */
    public function search($query) {
        global $CFG, $DB;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if ($blocks = $DB->get_records('block')) {
            foreach ($blocks as $block) {
                if (!file_exists("$CFG->dirroot/blocks/$block->name/")) {
                    continue;
                }
                if (strpos($block->name, $query) !== false) {
                    $found = true;
                    break;
                }
                $strblockname = get_string('pluginname', 'block_'.$block->name);
                if (strpos(core_text::strtolower($strblockname), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}

/**
 * Message outputs configuration
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_managemessageoutputs extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('managemessageoutputs',
            get_string('defaultmessageoutputs', 'message'),
            new moodle_url('/admin/message.php')
        );
    }

    /**
     * Search for a specific message processor
     *
     * @param string $query The string to search for
     * @return array
     */
    public function search($query) {
        global $CFG, $DB;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        if ($processors = get_message_processors()) {
            foreach ($processors as $processor) {
                if (!$processor->available) {
                    continue;
                }
                if (strpos($processor->name, $query) !== false) {
                    $found = true;
                    break;
                }
                $strprocessorname = get_string('pluginname', 'message_'.$processor->name);
                if (strpos(core_text::strtolower($strprocessorname), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}

/**
 * Default message outputs configuration
 *
 * @deprecated since Moodle 3.7 MDL-64495. Please use admin_page_managemessageoutputs instead.
 * @todo       MDL-64866 This will be deleted in Moodle 4.1.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_defaultmessageoutputs extends admin_page_managemessageoutputs {
    /**
     * Calls parent::__construct with specific arguments
     *
     * @deprecated since Moodle 3.7 MDL-64495. Please use admin_page_managemessageoutputs instead.
     * @todo       MDL-64866 This will be deleted in Moodle 4.1.
     */
    public function __construct() {
        global $CFG;

        debugging('admin_page_defaultmessageoutputs class is deprecated. Please use admin_page_managemessageoutputs instead.',
            DEBUG_DEVELOPER);

        admin_externalpage::__construct('defaultmessageoutputs', get_string('defaultmessageoutputs', 'message'), new moodle_url('/message/defaultoutputs.php'));
    }
}


/**
 * Manage question behaviours page
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_manageqbehaviours extends admin_externalpage {
    /**
     * Constructor
     */
    public function __construct() {
        global $CFG;
        parent::__construct('manageqbehaviours', get_string('manageqbehaviours', 'admin'),
                new moodle_url('/admin/qbehaviours.php'));
    }

    /**
     * Search question behaviours for the specified string
     *
     * @param string $query The string to search for in question behaviours
     * @return array
     */
    public function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        require_once($CFG->dirroot . '/question/engine/lib.php');
        foreach (core_component::get_plugin_list('qbehaviour') as $behaviour => $notused) {
            if (strpos(core_text::strtolower(question_engine::get_behaviour_name($behaviour)),
                    $query) !== false) {
                $found = true;
                break;
            }
        }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}


/**
 * Question type manage page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_manageqtypes extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('manageqtypes', get_string('manageqtypes', 'admin'),
                new moodle_url('/admin/qtypes.php'));
    }

    /**
     * Search question types for the specified string
     *
     * @param string $query The string to search for in question types
     * @return array
     */
    public function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        require_once($CFG->dirroot . '/question/engine/bank.php');
        foreach (question_bank::get_all_qtypes() as $qtype) {
            if (strpos(core_text::strtolower($qtype->local_name()), $query) !== false) {
                $found = true;
                break;
            }
        }
        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}


class admin_page_manageportfolios extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('manageportfolios', get_string('manageportfolios', 'portfolio'),
                "$CFG->wwwroot/$CFG->admin/portfolio.php");
    }

    /**
     * Searches page for the specified string.
     * @param string $query The string to search for
     * @return bool True if it is found on this page
     */
    public function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        $portfolios = core_component::get_plugin_list('portfolio');
        foreach ($portfolios as $p => $dir) {
            if (strpos($p, $query) !== false) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            foreach (portfolio_instances(false, false) as $instance) {
                $title = $instance->get('name');
                if (strpos(core_text::strtolower($title), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}


class admin_page_managerepositories extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('managerepositories', get_string('manage',
                'repository'), "$CFG->wwwroot/$CFG->admin/repository.php");
    }

    /**
     * Searches page for the specified string.
     * @param string $query The string to search for
     * @return bool True if it is found on this page
     */
    public function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        $repositories= core_component::get_plugin_list('repository');
        foreach ($repositories as $p => $dir) {
            if (strpos($p, $query) !== false) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            foreach (repository::get_types() as $instance) {
                $title = $instance->get_typename();
                if (strpos(core_text::strtolower($title), $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            $result = new stdClass();
            $result->page     = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}


/**
 * Special class for authentication administration.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_manageauths extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('authsui', get_string('authsettings', 'admin'), '', '');
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Search to find if Query is related to auth plugin
     *
     * @param string $query The string to search for
     * @return bool true for related false for not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $authsavailable = core_component::get_plugin_list('auth');
        foreach ($authsavailable as $auth => $dir) {
            if (strpos($auth, $query) !== false) {
                return true;
            }
            $authplugin = get_auth_plugin($auth);
            $authtitle = $authplugin->get_title();
            if (strpos(core_text::strtolower($authtitle), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return XHTML to display control
     *
     * @param mixed $data Unused
     * @param string $query
     * @return string highlight
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $DB;

        // display strings
        $txt = get_strings(array('authenticationplugins', 'users', 'administration',
            'settings', 'edit', 'name', 'enable', 'disable',
            'up', 'down', 'none', 'users'));
        $txt->updown = "$txt->up/$txt->down";
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');
        $txt->testsettings = get_string('testsettings', 'core_auth');

        $authsavailable = core_component::get_plugin_list('auth');
        get_enabled_auth_plugins(true); // fix the list of enabled auths
        if (empty($CFG->auth)) {
            $authsenabled = array();
        } else {
            $authsenabled = explode(',', $CFG->auth);
        }

        // construct the display array, with enabled auth plugins at the top, in order
        $displayauths = array();
        $registrationauths = array();
        $registrationauths[''] = $txt->disable;
        $authplugins = array();
        foreach ($authsenabled as $auth) {
            $authplugin = get_auth_plugin($auth);
            $authplugins[$auth] = $authplugin;
            /// Get the auth title (from core or own auth lang files)
            $authtitle = $authplugin->get_title();
            /// Apply titles
            $displayauths[$auth] = $authtitle;
            if ($authplugin->can_signup()) {
                $registrationauths[$auth] = $authtitle;
            }
        }

        foreach ($authsavailable as $auth => $dir) {
            if (array_key_exists($auth, $displayauths)) {
                continue; //already in the list
            }
            $authplugin = get_auth_plugin($auth);
            $authplugins[$auth] = $authplugin;
            /// Get the auth title (from core or own auth lang files)
            $authtitle = $authplugin->get_title();
            /// Apply titles
            $displayauths[$auth] = $authtitle;
            if ($authplugin->can_signup()) {
                $registrationauths[$auth] = $authtitle;
            }
        }

        $return = $OUTPUT->heading(get_string('actauthhdr', 'auth'), 3, 'main');
        $return .= $OUTPUT->box_start('generalbox authsui');

        $table = new html_table();
        $table->head  = array($txt->name, $txt->users, $txt->enable, $txt->updown, $txt->settings, $txt->testsettings, $txt->uninstall);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->data  = array();
        $table->attributes['class'] = 'admintable generaltable';
        $table->id = 'manageauthtable';

        //add always enabled plugins first
        $displayname = $displayauths['manual'];
        $settings = "<a href=\"settings.php?section=authsettingmanual\">{$txt->settings}</a>";
        $usercount = $DB->count_records('user', array('auth'=>'manual', 'deleted'=>0));
        $table->data[] = array($displayname, $usercount, '', '', $settings, '', '');
        $displayname = $displayauths['nologin'];
        $usercount = $DB->count_records('user', array('auth'=>'nologin', 'deleted'=>0));
        $table->data[] = array($displayname, $usercount, '', '', '', '', '');


        // iterate through auth plugins and add to the display table
        $updowncount = 1;
        $authcount = count($authsenabled);
        $url = "auth.php?sesskey=" . sesskey();
        foreach ($displayauths as $auth => $name) {
            if ($auth == 'manual' or $auth == 'nologin') {
                continue;
            }
            $class = '';
            // hide/show link
            if (in_array($auth, $authsenabled)) {
                $hideshow = "<a href=\"$url&amp;action=disable&amp;auth=$auth\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', get_string('disable')) . '</a>';
                $enabled = true;
                $displayname = $name;
            }
            else {
                $hideshow = "<a href=\"$url&amp;action=enable&amp;auth=$auth\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', get_string('enable')) . '</a>';
                $enabled = false;
                $displayname = $name;
                $class = 'dimmed_text';
            }

            $usercount = $DB->count_records('user', array('auth'=>$auth, 'deleted'=>0));

            // up/down link (only if auth is enabled)
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updown .= "<a href=\"$url&amp;action=up&amp;auth=$auth\">";
                    $updown .= $OUTPUT->pix_icon('t/up', get_string('moveup')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                if ($updowncount < $authcount) {
                    $updown .= "<a href=\"$url&amp;action=down&amp;auth=$auth\">";
                    $updown .= $OUTPUT->pix_icon('t/down', get_string('movedown')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                ++ $updowncount;
            }

            // settings link
            if (file_exists($CFG->dirroot.'/auth/'.$auth.'/settings.php')) {
                $settings = "<a href=\"settings.php?section=authsetting$auth\">{$txt->settings}</a>";
            } else if (file_exists($CFG->dirroot.'/auth/'.$auth.'/config.html')) {
                $settings = "<a href=\"auth_config.php?auth=$auth\">{$txt->settings}</a>";
            } else {
                $settings = '';
            }

            // Uninstall link.
            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('auth_'.$auth, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $txt->uninstall);
            }

            $test = '';
            if (!empty($authplugins[$auth]) and method_exists($authplugins[$auth], 'test_settings')) {
                $testurl = new moodle_url('/auth/test_settings.php', array('auth'=>$auth, 'sesskey'=>sesskey()));
                $test = html_writer::link($testurl, $txt->testsettings);
            }

            // Add a row to the table.
            $row = new html_table_row(array($displayname, $usercount, $hideshow, $updown, $settings, $test, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }
        $return .= html_writer::table($table);
        $return .= get_string('configauthenticationplugins', 'admin').'<br />'.get_string('tablenosave', 'filters');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}


/**
 * Special class for authentication administration.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_manageeditors extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('editorsui', get_string('editorsettings', 'editor'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Checks if $query is one of the available editors
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $editors_available = editors_get_available();
        foreach ($editors_available as $editor=>$editorstr) {
            if (strpos($editor, $query) !== false) {
                return true;
            }
            if (strpos(core_text::strtolower($editorstr), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        // display strings
        $txt = get_strings(array('administration', 'settings', 'edit', 'name', 'enable', 'disable',
            'up', 'down', 'none'));
        $struninstall = get_string('uninstallplugin', 'core_admin');

        $txt->updown = "$txt->up/$txt->down";

        $editors_available = editors_get_available();
        $active_editors = explode(',', $CFG->texteditors);

        $active_editors = array_reverse($active_editors);
        foreach ($active_editors as $key=>$editor) {
            if (empty($editors_available[$editor])) {
                unset($active_editors[$key]);
            } else {
                $name = $editors_available[$editor];
                unset($editors_available[$editor]);
                $editors_available[$editor] = $name;
            }
        }
        if (empty($active_editors)) {
        //$active_editors = array('textarea');
        }
        $editors_available = array_reverse($editors_available, true);
        $return = $OUTPUT->heading(get_string('acteditorshhdr', 'editor'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox editorsui');

        $table = new html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->updown, $txt->settings, $struninstall);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'editormanagement';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data  = array();

        // iterate through auth plugins and add to the display table
        $updowncount = 1;
        $editorcount = count($active_editors);
        $url = "editors.php?sesskey=" . sesskey();
        foreach ($editors_available as $editor => $name) {
        // hide/show link
            $class = '';
            if (in_array($editor, $active_editors)) {
                $hideshow = "<a href=\"$url&amp;action=disable&amp;editor=$editor\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', get_string('disable')) . '</a>';
                $enabled = true;
                $displayname = $name;
            }
            else {
                $hideshow = "<a href=\"$url&amp;action=enable&amp;editor=$editor\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', get_string('enable')) . '</a>';
                $enabled = false;
                $displayname = $name;
                $class = 'dimmed_text';
            }

            // up/down link (only if auth is enabled)
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updown .= "<a href=\"$url&amp;action=up&amp;editor=$editor\">";
                    $updown .= $OUTPUT->pix_icon('t/up', get_string('moveup')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                if ($updowncount < $editorcount) {
                    $updown .= "<a href=\"$url&amp;action=down&amp;editor=$editor\">";
                    $updown .= $OUTPUT->pix_icon('t/down', get_string('movedown')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $OUTPUT->spacer() . '&nbsp;';
                }
                ++ $updowncount;
            }

            // settings link
            if (file_exists($CFG->dirroot.'/lib/editor/'.$editor.'/settings.php')) {
                $eurl = new moodle_url('/admin/settings.php', array('section'=>'editorsettings'.$editor));
                $settings = "<a href='$eurl'>{$txt->settings}</a>";
            } else {
                $settings = '';
            }

            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('editor_'.$editor, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $struninstall);
            }

            // Add a row to the table.
            $row = new html_table_row(array($displayname, $hideshow, $updown, $settings, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }
        $return .= html_writer::table($table);
        $return .= get_string('configeditorplugins', 'editor').'<br />'.get_string('tablenosave', 'admin');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

/**
 * Special class for antiviruses administration.
 *
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_manageantiviruses extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('antivirusesui', get_string('antivirussettings', 'antivirus'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param string $data Unused
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available editors
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $antivirusesavailable = \core\antivirus\manager::get_available();
        foreach ($antivirusesavailable as $antivirus => $antivirusstr) {
            if (strpos($antivirus, $query) !== false) {
                return true;
            }
            if (strpos(core_text::strtolower($antivirusstr), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        // Display strings.
        $txt = get_strings(array('administration', 'settings', 'edit', 'name', 'enable', 'disable',
            'up', 'down', 'none'));
        $struninstall = get_string('uninstallplugin', 'core_admin');

        $txt->updown = "$txt->up/$txt->down";

        $antivirusesavailable = \core\antivirus\manager::get_available();
        $activeantiviruses = explode(',', $CFG->antiviruses);

        $activeantiviruses = array_reverse($activeantiviruses);
        foreach ($activeantiviruses as $key => $antivirus) {
            if (empty($antivirusesavailable[$antivirus])) {
                unset($activeantiviruses[$key]);
            } else {
                $name = $antivirusesavailable[$antivirus];
                unset($antivirusesavailable[$antivirus]);
                $antivirusesavailable[$antivirus] = $name;
            }
        }
        $antivirusesavailable = array_reverse($antivirusesavailable, true);
        $return = $OUTPUT->heading(get_string('actantivirushdr', 'antivirus'), 3, 'main', true);
        $return .= $OUTPUT->box_start('generalbox antivirusesui');

        $table = new html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->updown, $txt->settings, $struninstall);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'antivirusmanagement';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data  = array();

        // Iterate through auth plugins and add to the display table.
        $updowncount = 1;
        $antiviruscount = count($activeantiviruses);
        $baseurl = new moodle_url('/admin/antiviruses.php', array('sesskey' => sesskey()));
        foreach ($antivirusesavailable as $antivirus => $name) {
            // Hide/show link.
            $class = '';
            if (in_array($antivirus, $activeantiviruses)) {
                $hideshowurl = $baseurl;
                $hideshowurl->params(array('action' => 'disable', 'antivirus' => $antivirus));
                $hideshowimg = $OUTPUT->pix_icon('t/hide', get_string('disable'));
                $hideshow = html_writer::link($hideshowurl, $hideshowimg);
                $enabled = true;
                $displayname = $name;
            } else {
                $hideshowurl = $baseurl;
                $hideshowurl->params(array('action' => 'enable', 'antivirus' => $antivirus));
                $hideshowimg = $OUTPUT->pix_icon('t/show', get_string('enable'));
                $hideshow = html_writer::link($hideshowurl, $hideshowimg);
                $enabled = false;
                $displayname = $name;
                $class = 'dimmed_text';
            }

            // Up/down link.
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updownurl = $baseurl;
                    $updownurl->params(array('action' => 'up', 'antivirus' => $antivirus));
                    $updownimg = $OUTPUT->pix_icon('t/up', get_string('moveup'));
                    $updown = html_writer::link($updownurl, $updownimg);
                } else {
                    $updownimg = $OUTPUT->spacer();
                }
                if ($updowncount < $antiviruscount) {
                    $updownurl = $baseurl;
                    $updownurl->params(array('action' => 'down', 'antivirus' => $antivirus));
                    $updownimg = $OUTPUT->pix_icon('t/down', get_string('movedown'));
                    $updown = html_writer::link($updownurl, $updownimg);
                } else {
                    $updownimg = $OUTPUT->spacer();
                }
                ++ $updowncount;
            }

            // Settings link.
            if (file_exists($CFG->dirroot.'/lib/antivirus/'.$antivirus.'/settings.php')) {
                $eurl = new moodle_url('/admin/settings.php', array('section' => 'antivirussettings'.$antivirus));
                $settings = html_writer::link($eurl, $txt->settings);
            } else {
                $settings = '';
            }

            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('antivirus_'.$antivirus, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $struninstall);
            }

            // Add a row to the table.
            $row = new html_table_row(array($displayname, $hideshow, $updown, $settings, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }
        $return .= html_writer::table($table);
        $return .= get_string('configantivirusplugins', 'antivirus') . html_writer::empty_tag('br') . get_string('tablenosave', 'admin');
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

/**
 * Special class for license administration.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managelicenses extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('licensesui', get_string('licensesettings', 'admin'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // do not write any setting
        return '';
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        require_once($CFG->libdir . '/licenselib.php');
        $url = "licenses.php?sesskey=" . sesskey();

        // display strings
        $txt = get_strings(array('administration', 'settings', 'name', 'enable', 'disable', 'none'));
        $licenses = license_manager::get_licenses();

        $return = $OUTPUT->heading(get_string('availablelicenses', 'admin'), 3, 'main', true);

        $return .= $OUTPUT->box_start('generalbox editorsui');

        $table = new html_table();
        $table->head  = array($txt->name, $txt->enable);
        $table->colclasses = array('leftalign', 'centeralign');
        $table->id = 'availablelicenses';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data  = array();

        foreach ($licenses as $value) {
            $displayname = html_writer::link($value->source, get_string($value->shortname, 'license'), array('target'=>'_blank'));

            if ($value->enabled == 1) {
                $hideshow = html_writer::link($url.'&action=disable&license='.$value->shortname,
                    $OUTPUT->pix_icon('t/hide', get_string('disable')));
            } else {
                $hideshow = html_writer::link($url.'&action=enable&license='.$value->shortname,
                    $OUTPUT->pix_icon('t/show', get_string('enable')));
            }

            if ($value->shortname == $CFG->sitedefaultlicense) {
                $displayname .= ' '.$OUTPUT->pix_icon('t/locked', get_string('default'));
                $hideshow = '';
            }

            $enabled = true;

            $table->data[] =array($displayname, $hideshow);
        }
        $return .= html_writer::table($table);
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

/**
 * Course formats manager. Allows to enable/disable formats and jump to settings
 */
class admin_setting_manageformats extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('formatsui', new lang_string('manageformats', 'core_admin'), '', '');
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @param mixed $data string or array, must not be NULL
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // do not write any setting
        return '';
    }

    /**
     * Search to find if Query is related to format plugin
     *
     * @param string $query The string to search for
     * @return bool true for related false for not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        $formats = core_plugin_manager::instance()->get_plugins_of_type('format');
        foreach ($formats as $format) {
            if (strpos($format->component, $query) !== false ||
                    strpos(core_text::strtolower($format->displayname), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return XHTML to display control
     *
     * @param mixed $data Unused
     * @param string $query
     * @return string highlight
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        $return = '';
        $return = $OUTPUT->heading(new lang_string('courseformats'), 3, 'main');
        $return .= $OUTPUT->box_start('generalbox formatsui');

        $formats = core_plugin_manager::instance()->get_plugins_of_type('format');

        // display strings
        $txt = get_strings(array('settings', 'name', 'enable', 'disable', 'up', 'down', 'default'));
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');
        $txt->updown = "$txt->up/$txt->down";

        $table = new html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->updown, $txt->uninstall, $txt->settings);
        $table->align = array('left', 'center', 'center', 'center', 'center');
        $table->attributes['class'] = 'manageformattable generaltable admintable';
        $table->data  = array();

        $cnt = 0;
        $defaultformat = get_config('moodlecourse', 'format');
        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));
        foreach ($formats as $format) {
            $url = new moodle_url('/admin/courseformats.php',
                    array('sesskey' => sesskey(), 'format' => $format->name));
            $isdefault = '';
            $class = '';
            if ($format->is_enabled()) {
                $strformatname = $format->displayname;
                if ($defaultformat === $format->name) {
                    $hideshow = $txt->default;
                } else {
                    $hideshow = html_writer::link($url->out(false, array('action' => 'disable')),
                            $OUTPUT->pix_icon('t/hide', $txt->disable, 'moodle', array('class' => 'iconsmall')));
                }
            } else {
                $strformatname = $format->displayname;
                $class = 'dimmed_text';
                $hideshow = html_writer::link($url->out(false, array('action' => 'enable')),
                    $OUTPUT->pix_icon('t/show', $txt->enable, 'moodle', array('class' => 'iconsmall')));
            }
            $updown = '';
            if ($cnt) {
                $updown .= html_writer::link($url->out(false, array('action' => 'up')),
                    $OUTPUT->pix_icon('t/up', $txt->up, 'moodle', array('class' => 'iconsmall'))). '';
            } else {
                $updown .= $spacer;
            }
            if ($cnt < count($formats) - 1) {
                $updown .= '&nbsp;'.html_writer::link($url->out(false, array('action' => 'down')),
                    $OUTPUT->pix_icon('t/down', $txt->down, 'moodle', array('class' => 'iconsmall')));
            } else {
                $updown .= $spacer;
            }
            $cnt++;
            $settings = '';
            if ($format->get_settings_url()) {
                $settings = html_writer::link($format->get_settings_url(), $txt->settings);
            }
            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('format_'.$format->name, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $txt->uninstall);
            }
            $row = new html_table_row(array($strformatname, $hideshow, $updown, $uninstall, $settings));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }
        $return .= html_writer::table($table);
        $link = html_writer::link(new moodle_url('/admin/settings.php', array('section' => 'coursesettings')), new lang_string('coursesettings'));
        $return .= html_writer::tag('p', get_string('manageformatsgotosettings', 'admin', $link));
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

/**
 * Custom fields manager. Allows to enable/disable custom fields and jump to settings.
 *
 * @package    core
 * @copyright  2018 Toni Barbera
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managecustomfields extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('customfieldsui', new lang_string('managecustomfields', 'core_admin'), '', '');
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @param mixed $data string or array, must not be NULL
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Search to find if Query is related to format plugin
     *
     * @param string $query The string to search for
     * @return bool true for related false for not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        $formats = core_plugin_manager::instance()->get_plugins_of_type('customfield');
        foreach ($formats as $format) {
            if (strpos($format->component, $query) !== false ||
                    strpos(core_text::strtolower($format->displayname), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return XHTML to display control
     *
     * @param mixed $data Unused
     * @param string $query
     * @return string highlight
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        $return = '';
        $return = $OUTPUT->heading(new lang_string('customfields', 'core_customfield'), 3, 'main');
        $return .= $OUTPUT->box_start('generalbox customfieldsui');

        $fields = core_plugin_manager::instance()->get_plugins_of_type('customfield');

        $txt = get_strings(array('settings', 'name', 'enable', 'disable', 'up', 'down'));
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');
        $txt->updown = "$txt->up/$txt->down";

        $table = new html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->uninstall, $txt->settings);
        $table->align = array('left', 'center', 'center', 'center');
        $table->attributes['class'] = 'managecustomfieldtable generaltable admintable';
        $table->data  = array();

        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));
        foreach ($fields as $field) {
            $url = new moodle_url('/admin/customfields.php',
                    array('sesskey' => sesskey(), 'field' => $field->name));

            if ($field->is_enabled()) {
                $strfieldname = $field->displayname;
                $hideshow = html_writer::link($url->out(false, array('action' => 'disable')),
                        $OUTPUT->pix_icon('t/hide', $txt->disable, 'moodle', array('class' => 'iconsmall')));
            } else {
                $strfieldname = $field->displayname;
                $class = 'dimmed_text';
                $hideshow = html_writer::link($url->out(false, array('action' => 'enable')),
                    $OUTPUT->pix_icon('t/show', $txt->enable, 'moodle', array('class' => 'iconsmall')));
            }
            $settings = '';
            if ($field->get_settings_url()) {
                $settings = html_writer::link($field->get_settings_url(), $txt->settings);
            }
            $uninstall = '';
            if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('customfield_'.$field->name, 'manage')) {
                $uninstall = html_writer::link($uninstallurl, $txt->uninstall);
            }
            $row = new html_table_row(array($strfieldname, $hideshow, $uninstall, $settings));
            $table->data[] = $row;
        }
        $return .= html_writer::table($table);
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

/**
 * Data formats manager. Allow reorder and to enable/disable data formats and jump to settings
 *
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managedataformats extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('managedataformats', new lang_string('managedataformats'), '', '');
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '' and doesn't write anything
     *
     * @param mixed $data string or array, must not be NULL
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Search to find if Query is related to format plugin
     *
     * @param string $query The string to search for
     * @return bool true for related false for not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }
        $formats = core_plugin_manager::instance()->get_plugins_of_type('dataformat');
        foreach ($formats as $format) {
            if (strpos($format->component, $query) !== false ||
                    strpos(core_text::strtolower($format->displayname), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return XHTML to display control
     *
     * @param mixed $data Unused
     * @param string $query
     * @return string highlight
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;
        $return = '';

        $formats = core_plugin_manager::instance()->get_plugins_of_type('dataformat');

        $txt = get_strings(array('settings', 'name', 'enable', 'disable', 'up', 'down', 'default'));
        $txt->uninstall = get_string('uninstallplugin', 'core_admin');
        $txt->updown = "$txt->up/$txt->down";

        $table = new html_table();
        $table->head  = array($txt->name, $txt->enable, $txt->updown, $txt->uninstall, $txt->settings);
        $table->align = array('left', 'center', 'center', 'center', 'center');
        $table->attributes['class'] = 'manageformattable generaltable admintable';
        $table->data  = array();

        $cnt = 0;
        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));
        $totalenabled = 0;
        foreach ($formats as $format) {
            if ($format->is_enabled() && $format->is_installed_and_upgraded()) {
                $totalenabled++;
            }
        }
        foreach ($formats as $format) {
            $status = $format->get_status();
            $url = new moodle_url('/admin/dataformats.php',
                    array('sesskey' => sesskey(), 'name' => $format->name));

            $class = '';
            if ($format->is_enabled()) {
                $strformatname = $format->displayname;
                if ($totalenabled == 1&& $format->is_enabled()) {
                    $hideshow = '';
                } else {
                    $hideshow = html_writer::link($url->out(false, array('action' => 'disable')),
                        $OUTPUT->pix_icon('t/hide', $txt->disable, 'moodle', array('class' => 'iconsmall')));
                }
            } else {
                $class = 'dimmed_text';
                $strformatname = $format->displayname;
                $hideshow = html_writer::link($url->out(false, array('action' => 'enable')),
                    $OUTPUT->pix_icon('t/show', $txt->enable, 'moodle', array('class' => 'iconsmall')));
            }

            $updown = '';
            if ($cnt) {
                $updown .= html_writer::link($url->out(false, array('action' => 'up')),
                    $OUTPUT->pix_icon('t/up', $txt->up, 'moodle', array('class' => 'iconsmall'))). '';
            } else {
                $updown .= $spacer;
            }
            if ($cnt < count($formats) - 1) {
                $updown .= '&nbsp;'.html_writer::link($url->out(false, array('action' => 'down')),
                    $OUTPUT->pix_icon('t/down', $txt->down, 'moodle', array('class' => 'iconsmall')));
            } else {
                $updown .= $spacer;
            }

            $uninstall = '';
            if ($status === core_plugin_manager::PLUGIN_STATUS_MISSING) {
                $uninstall = get_string('status_missing', 'core_plugin');
            } else if ($status === core_plugin_manager::PLUGIN_STATUS_NEW) {
                $uninstall = get_string('status_new', 'core_plugin');
            } else if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('dataformat_'.$format->name, 'manage')) {
                if ($totalenabled != 1 || !$format->is_enabled()) {
                    $uninstall = html_writer::link($uninstallurl, $txt->uninstall);
                }
            }

            $settings = '';
            if ($format->get_settings_url()) {
                $settings = html_writer::link($format->get_settings_url(), $txt->settings);
            }

            $row = new html_table_row(array($strformatname, $hideshow, $updown, $uninstall, $settings));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
            $cnt++;
        }
        $return .= html_writer::table($table);
        return highlight($query, $return);
    }
}

/**
 * Special class for filter administration.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_page_managefilters extends admin_externalpage {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('managefilters', get_string('filtersettings', 'admin'), "$CFG->wwwroot/$CFG->admin/filters.php");
    }

    /**
     * Searches all installed filters for specified filter
     *
     * @param string $query The filter(string) to search for
     * @param string $query
     */
    public function search($query) {
        global $CFG;
        if ($result = parent::search($query)) {
            return $result;
        }

        $found = false;
        $filternames = filter_get_all_installed();
        foreach ($filternames as $path => $strfiltername) {
            if (strpos(core_text::strtolower($strfiltername), $query) !== false) {
                $found = true;
                break;
            }
            if (strpos($path, $query) !== false) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $result = new stdClass;
            $result->page = $this;
            $result->settings = array();
            return array($this->name => $result);
        } else {
            return array();
        }
    }
}

/**
 * Generic class for managing plugins in a table that allows re-ordering and enable/disable of each plugin.
 * Requires a get_rank method on the plugininfo class for sorting.
 *
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class admin_setting_manage_plugins extends admin_setting {

    /**
     * Get the admin settings section name (just a unique string)
     *
     * @return string
     */
    public function get_section_name() {
        return 'manage' . $this->get_plugin_type() . 'plugins';
    }

    /**
     * Get the admin settings section title (use get_string).
     *
     * @return string
     */
    abstract public function get_section_title();

    /**
     * Get the type of plugin to manage.
     *
     * @return string
     */
    abstract public function get_plugin_type();

    /**
     * Get the name of the second column.
     *
     * @return string
     */
    public function get_info_column_name() {
        return '';
    }

    /**
     * Get the type of plugin to manage.
     *
     * @param plugininfo The plugin info class.
     * @return string
     */
    abstract public function get_info_column($plugininfo);

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct($this->get_section_name(), $this->get_section_title(), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param mixed $data
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available plugins of this type
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $query = core_text::strtolower($query);
        $plugins = core_plugin_manager::instance()->get_plugins_of_type($this->get_plugin_type());
        foreach ($plugins as $name => $plugin) {
            $localised = $plugin->displayname;
            if (strpos(core_text::strtolower($name), $query) !== false) {
                return true;
            }
            if (strpos(core_text::strtolower($localised), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * The URL for the management page for this plugintype.
     *
     * @return moodle_url
     */
    protected function get_manage_url() {
        return new moodle_url('/admin/updatesetting.php');
    }

    /**
     * Builds the HTML to display the control.
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT, $DB, $PAGE;

        $context = (object) [
            'manageurl' => new moodle_url($this->get_manage_url(), [
                    'type' => $this->get_plugin_type(),
                    'sesskey' => sesskey(),
                ]),
            'infocolumnname' => $this->get_info_column_name(),
            'plugins' => [],
        ];

        $pluginmanager = core_plugin_manager::instance();
        $allplugins = $pluginmanager->get_plugins_of_type($this->get_plugin_type());
        $enabled = $pluginmanager->get_enabled_plugins($this->get_plugin_type());
        $plugins = array_merge($enabled, $allplugins);
        foreach ($plugins as $key => $plugin) {
            $pluginlink = new moodle_url($context->manageurl, ['plugin' => $key]);

            $pluginkey = (object) [
                'plugin' => $plugin->displayname,
                'enabled' => $plugin->is_enabled(),
                'togglelink' => '',
                'moveuplink' => '',
                'movedownlink' => '',
                'settingslink' => $plugin->get_settings_url(),
                'uninstalllink' => '',
                'info' => '',
            ];

            // Enable/Disable link.
            $togglelink = new moodle_url($pluginlink);
            if ($plugin->is_enabled()) {
                $toggletarget = false;
                $togglelink->param('action', 'disable');

                if (count($context->plugins)) {
                    // This is not the first plugin.
                    $pluginkey->moveuplink = new moodle_url($pluginlink, ['action' => 'up']);
                }

                if (count($enabled) > count($context->plugins) + 1) {
                    // This is not the last plugin.
                    $pluginkey->movedownlink = new moodle_url($pluginlink, ['action' => 'down']);
                }

                $pluginkey->info = $this->get_info_column($plugin);
            } else {
                $toggletarget = true;
                $togglelink->param('action', 'enable');
            }

            $pluginkey->toggletarget = $toggletarget;
            $pluginkey->togglelink = $togglelink;

            $frankenstyle = $plugin->type . '_' . $plugin->name;
            if ($uninstalllink = core_plugin_manager::instance()->get_uninstall_url($frankenstyle, 'manage')) {
                // This plugin supports uninstallation.
                $pluginkey->uninstalllink = $uninstalllink;
            }

            if (!empty($this->get_info_column_name())) {
                // This plugintype has an info column.
                $pluginkey->info = $this->get_info_column($plugin);
            }

            $context->plugins[] = $pluginkey;
        }

        $str = $OUTPUT->render_from_template('core_admin/setting_manage_plugins', $context);
        return highlight($query, $str);
    }
}

/**
 * Generic class for managing plugins in a table that allows re-ordering and enable/disable of each plugin.
 * Requires a get_rank method on the plugininfo class for sorting.
 *
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_manage_fileconverter_plugins extends admin_setting_manage_plugins {
    public function get_section_title() {
        return get_string('type_fileconverter_plural', 'plugin');
    }

    public function get_plugin_type() {
        return 'fileconverter';
    }

    public function get_info_column_name() {
        return get_string('supportedconversions', 'plugin');
    }

    public function get_info_column($plugininfo) {
        return $plugininfo->get_supported_conversions();
    }
}

/**
 * Special class for media player plugins management.
 *
 * @copyright 2016 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managemediaplayers extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('managemediaplayers', get_string('managemediaplayers', 'media'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param mixed $data
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Checks if $query is one of the available enrol plugins
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $query = core_text::strtolower($query);
        $plugins = core_plugin_manager::instance()->get_plugins_of_type('media');
        foreach ($plugins as $name => $plugin) {
            $localised = $plugin->displayname;
            if (strpos(core_text::strtolower($name), $query) !== false) {
                return true;
            }
            if (strpos(core_text::strtolower($localised), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sort plugins so enabled plugins are displayed first and all others are displayed in the end sorted by rank.
     * @return \core\plugininfo\media[]
     */
    protected function get_sorted_plugins() {
        $pluginmanager = core_plugin_manager::instance();

        $plugins = $pluginmanager->get_plugins_of_type('media');
        $enabledplugins = $pluginmanager->get_enabled_plugins('media');

        // Sort plugins so enabled plugins are displayed first and all others are displayed in the end sorted by rank.
        \core_collator::asort_objects_by_method($plugins, 'get_rank', \core_collator::SORT_NUMERIC);

        $order = array_values($enabledplugins);
        $order = array_merge($order, array_diff(array_reverse(array_keys($plugins)), $order));

        $sortedplugins = array();
        foreach ($order as $name) {
            $sortedplugins[$name] = $plugins[$name];
        }

        return $sortedplugins;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $DB, $PAGE;

        // Display strings.
        $strup        = get_string('up');
        $strdown      = get_string('down');
        $strsettings  = get_string('settings');
        $strenable    = get_string('enable');
        $strdisable   = get_string('disable');
        $struninstall = get_string('uninstallplugin', 'core_admin');
        $strversion   = get_string('version');
        $strname      = get_string('name');
        $strsupports  = get_string('supports', 'core_media');

        $pluginmanager = core_plugin_manager::instance();

        $plugins = $this->get_sorted_plugins();
        $enabledplugins = $pluginmanager->get_enabled_plugins('media');

        $return = $OUTPUT->box_start('generalbox mediaplayersui');

        $table = new html_table();
        $table->head  = array($strname, $strsupports, $strversion,
            $strenable, $strup.'/'.$strdown, $strsettings, $struninstall);
        $table->colclasses = array('leftalign', 'leftalign', 'centeralign',
            'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'mediaplayerplugins';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data  = array();

        // Iterate through media plugins and add to the display table.
        $updowncount = 1;
        $url = new moodle_url('/admin/media.php', array('sesskey' => sesskey()));
        $printed = array();
        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'iconsmall'));

        $usedextensions = [];
        foreach ($plugins as $name => $plugin) {
            $url->param('media', $name);
            $plugininfo = $pluginmanager->get_plugin_info('media_'.$name);
            $version = $plugininfo->versiondb;
            $supports = $plugininfo->supports($usedextensions);

            // Hide/show links.
            $class = '';
            if (!$plugininfo->is_installed_and_upgraded()) {
                $hideshow = '';
                $enabled = false;
                $displayname = '<span class="notifyproblem">'.$name.'</span>';
            } else {
                $enabled = $plugininfo->is_enabled();
                if ($enabled) {
                    $hideshow = html_writer::link(new moodle_url($url, array('action' => 'disable')),
                        $OUTPUT->pix_icon('t/hide', $strdisable, 'moodle', array('class' => 'iconsmall')));
                } else {
                    $hideshow = html_writer::link(new moodle_url($url, array('action' => 'enable')),
                        $OUTPUT->pix_icon('t/show', $strenable, 'moodle', array('class' => 'iconsmall')));
                    $class = 'dimmed_text';
                }
                $displayname = $plugin->displayname;
                if (get_string_manager()->string_exists('pluginname_help', 'media_' . $name)) {
                    $displayname .= '&nbsp;' . $OUTPUT->help_icon('pluginname', 'media_' . $name);
                }
            }
            if ($PAGE->theme->resolve_image_location('icon', 'media_' . $name, false)) {
                $icon = $OUTPUT->pix_icon('icon', '', 'media_' . $name, array('class' => 'icon pluginicon'));
            } else {
                $icon = $OUTPUT->pix_icon('spacer', '', 'moodle', array('class' => 'icon pluginicon noicon'));
            }

            // Up/down link (only if enrol is enabled).
            $updown = '';
            if ($enabled) {
                if ($updowncount > 1) {
                    $updown = html_writer::link(new moodle_url($url, array('action' => 'up')),
                        $OUTPUT->pix_icon('t/up', $strup, 'moodle', array('class' => 'iconsmall')));
                } else {
                    $updown = $spacer;
                }
                if ($updowncount < count($enabledplugins)) {
                    $updown .= html_writer::link(new moodle_url($url, array('action' => 'down')),
                        $OUTPUT->pix_icon('t/down', $strdown, 'moodle', array('class' => 'iconsmall')));
                } else {
                    $updown .= $spacer;
                }
                ++$updowncount;
            }

            $uninstall = '';
            $status = $plugininfo->get_status();
            if ($status === core_plugin_manager::PLUGIN_STATUS_MISSING) {
                $uninstall = get_string('status_missing', 'core_plugin') . '<br/>';
            }
            if ($status === core_plugin_manager::PLUGIN_STATUS_NEW) {
                $uninstall = get_string('status_new', 'core_plugin');
            } else if ($uninstallurl = $pluginmanager->get_uninstall_url('media_'.$name, 'manage')) {
                $uninstall .= html_writer::link($uninstallurl, $struninstall);
            }

            $settings = '';
            if ($plugininfo->get_settings_url()) {
                $settings = html_writer::link($plugininfo->get_settings_url(), $strsettings);
            }

            // Add a row to the table.
            $row = new html_table_row(array($icon.$displayname, $supports, $version, $hideshow, $updown, $settings, $uninstall));
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;

            $printed[$name] = true;
        }

        $return .= html_writer::table($table);
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

/**
 * Initialise admin page - this function does require login and permission
 * checks specified in page definition.
 *
 * This function must be called on each admin page before other code.
 *
 * @global moodle_page $PAGE
 *
 * @param string $section name of page
 * @param string $extrabutton extra HTML that is added after the blocks editing on/off button.
 * @param array $extraurlparams an array paramname => paramvalue, or parameters that need to be
 *      added to the turn blocks editing on/off form, so this page reloads correctly.
 * @param string $actualurl if the actual page being viewed is not the normal one for this
 *      page (e.g. admin/roles/allow.php, instead of admin/roles/manage.php, you can pass the alternate URL here.
 * @param array $options Additional options that can be specified for page setup.
 *      pagelayout - This option can be used to set a specific pagelyaout, admin is default.
 */
function admin_externalpage_setup($section, $extrabutton = '', array $extraurlparams = null, $actualurl = '', array $options = array()) {
    global $CFG, $PAGE, $USER, $SITE, $OUTPUT;

    $PAGE->set_context(null); // hack - set context to something, by default to system context

    $site = get_site();
    require_login(null, false);

    if (!empty($options['pagelayout'])) {
        // A specific page layout has been requested.
        $PAGE->set_pagelayout($options['pagelayout']);
    } else if ($section === 'upgradesettings') {
        $PAGE->set_pagelayout('maintenance');
    } else {
        $PAGE->set_pagelayout('admin');
    }

    $adminroot = admin_get_root(false, false); // settings not required for external pages
    $extpage = $adminroot->locate($section, true);

    if (empty($extpage) or !($extpage instanceof admin_externalpage)) {
        // The requested section isn't in the admin tree
        // It could be because the user has inadequate capapbilities or because the section doesn't exist
        if (!has_capability('moodle/site:config', context_system::instance())) {
            // The requested section could depend on a different capability
            // but most likely the user has inadequate capabilities
            print_error('accessdenied', 'admin');
        } else {
            print_error('sectionerror', 'admin', "$CFG->wwwroot/$CFG->admin/");
        }
    }

    // this eliminates our need to authenticate on the actual pages
    if (!$extpage->check_access()) {
        print_error('accessdenied', 'admin');
        die;
    }

    navigation_node::require_admin_tree();

    // $PAGE->set_extra_button($extrabutton); TODO

    if (!$actualurl) {
        $actualurl = $extpage->url;
    }

    $PAGE->set_url($actualurl, $extraurlparams);
    if (strpos($PAGE->pagetype, 'admin-') !== 0) {
        $PAGE->set_pagetype('admin-' . $PAGE->pagetype);
    }

    if (empty($SITE->fullname) || empty($SITE->shortname)) {
        // During initial install.
        $strinstallation = get_string('installation', 'install');
        $strsettings = get_string('settings');
        $PAGE->navbar->add($strsettings);
        $PAGE->set_title($strinstallation);
        $PAGE->set_heading($strinstallation);
        $PAGE->set_cacheable(false);
        return;
    }

    // Locate the current item on the navigation and make it active when found.
    $path = $extpage->path;
    $node = $PAGE->settingsnav;
    while ($node && count($path) > 0) {
        $node = $node->get(array_pop($path));
    }
    if ($node) {
        $node->make_active();
    }

    // Normal case.
    $adminediting = optional_param('adminedit', -1, PARAM_BOOL);
    if ($PAGE->user_allowed_editing() && $adminediting != -1) {
        $USER->editing = $adminediting;
    }

    $visiblepathtosection = array_reverse($extpage->visiblepath);

    if ($PAGE->user_allowed_editing()) {
        if ($PAGE->user_is_editing()) {
            $caption = get_string('blockseditoff');
            $url = new moodle_url($PAGE->url, array('adminedit'=>'0', 'sesskey'=>sesskey()));
        } else {
            $caption = get_string('blocksediton');
            $url = new moodle_url($PAGE->url, array('adminedit'=>'1', 'sesskey'=>sesskey()));
        }
        $PAGE->set_button($OUTPUT->single_button($url, $caption, 'get'));
    }

    $PAGE->set_title("$SITE->shortname: " . implode(": ", $visiblepathtosection));
    $PAGE->set_heading($SITE->fullname);

    // prevent caching in nav block
    $PAGE->navigation->clear_cache();
}

/**
 * Returns the reference to admin tree root
 *
 * @return object admin_root object
 */
function admin_get_root($reload=false, $requirefulltree=true) {
    global $CFG, $DB, $OUTPUT, $ADMIN;

    if (is_null($ADMIN)) {
    // create the admin tree!
        $ADMIN = new admin_root($requirefulltree);
    }

    if ($reload or ($requirefulltree and !$ADMIN->fulltree)) {
        $ADMIN->purge_children($requirefulltree);
    }

    if (!$ADMIN->loaded) {
    // we process this file first to create categories first and in correct order
        require($CFG->dirroot.'/'.$CFG->admin.'/settings/top.php');

        // now we process all other files in admin/settings to build the admin tree
        foreach (glob($CFG->dirroot.'/'.$CFG->admin.'/settings/*.php') as $file) {
            if ($file == $CFG->dirroot.'/'.$CFG->admin.'/settings/top.php') {
                continue;
            }
            if ($file == $CFG->dirroot.'/'.$CFG->admin.'/settings/plugins.php') {
            // plugins are loaded last - they may insert pages anywhere
                continue;
            }
            require($file);
        }
        require($CFG->dirroot.'/'.$CFG->admin.'/settings/plugins.php');

        $ADMIN->loaded = true;
    }

    return $ADMIN;
}

/// settings utility functions

/**
 * This function applies default settings.
 * Because setting the defaults of some settings can enable other settings,
 * this function is called recursively until no more new settings are found.
 *
 * @param object $node, NULL means complete tree, null by default
 * @param bool $unconditional if true overrides all values with defaults, true by default
 * @param array $admindefaultsettings default admin settings to apply. Used recursively
 * @param array $settingsoutput The names and values of the changed settings. Used recursively
 * @return array $settingsoutput The names and values of the changed settings
 */
function admin_apply_default_settings($node=null, $unconditional=true, $admindefaultsettings=array(), $settingsoutput=array()) {

    if (is_null($node)) {
        core_plugin_manager::reset_caches();
        $node = admin_get_root(true, true);
    }

    if ($node instanceof admin_category) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            $settingsoutput = admin_apply_default_settings(
                    $node->children[$entry], $unconditional, $admindefaultsettings, $settingsoutput
                    );
        }

    } else if ($node instanceof admin_settingpage) {
        foreach ($node->settings as $setting) {
            if (!$unconditional and !is_null($setting->get_setting())) {
                // Do not override existing defaults.
                continue;
            }
            $defaultsetting = $setting->get_defaultsetting();
            if (is_null($defaultsetting)) {
                // No value yet - default maybe applied after admin user creation or in upgradesettings.
                continue;
            }

            $settingname = $node->name . '_' . $setting->name; // Get a unique name for the setting.

            if (!array_key_exists($settingname, $admindefaultsettings)) {  // Only update a setting if not already processed.
                $admindefaultsettings[$settingname] = $settingname;
                $settingsoutput[$settingname] = $defaultsetting;

                // Set the default for this setting.
                $setting->write_setting($defaultsetting);
                $setting->write_setting_flags(null);
            } else {
                unset($admindefaultsettings[$settingname]); // Remove processed settings.
            }
        }
    }

    // Call this function recursively until all settings are processed.
    if (($node instanceof admin_root) && (!empty($admindefaultsettings))) {
        $settingsoutput = admin_apply_default_settings(null, $unconditional, $admindefaultsettings, $settingsoutput);
    }
    // Just in case somebody modifies the list of active plugins directly.
    core_plugin_manager::reset_caches();

    return $settingsoutput;
}

/**
 * Store changed settings, this function updates the errors variable in $ADMIN
 *
 * @param object $formdata from form
 * @return int number of changed settings
 */
function admin_write_settings($formdata) {
    global $CFG, $SITE, $DB;

    $olddbsessions = !empty($CFG->dbsessions);
    $formdata = (array)$formdata;

    $data = array();
    foreach ($formdata as $fullname=>$value) {
        if (strpos($fullname, 's_') !== 0) {
            continue; // not a config value
        }
        $data[$fullname] = $value;
    }

    $adminroot = admin_get_root();
    $settings = admin_find_write_settings($adminroot, $data);

    $count = 0;
    foreach ($settings as $fullname=>$setting) {
        /** @var $setting admin_setting */
        $original = $setting->get_setting();
        $error = $setting->write_setting($data[$fullname]);
        if ($error !== '') {
            $adminroot->errors[$fullname] = new stdClass();
            $adminroot->errors[$fullname]->data  = $data[$fullname];
            $adminroot->errors[$fullname]->id    = $setting->get_id();
            $adminroot->errors[$fullname]->error = $error;
        } else {
            $setting->write_setting_flags($data);
        }
        if ($setting->post_write_settings($original)) {
            $count++;
        }
    }

    if ($olddbsessions != !empty($CFG->dbsessions)) {
        require_logout();
    }

    // Now update $SITE - just update the fields, in case other people have a
    // a reference to it (e.g. $PAGE, $COURSE).
    $newsite = $DB->get_record('course', array('id'=>$SITE->id));
    foreach (get_object_vars($newsite) as $field => $value) {
        $SITE->$field = $value;
    }

    // now reload all settings - some of them might depend on the changed
    admin_get_root(true);
    return $count;
}

/**
 * Internal recursive function - finds all settings from submitted form
 *
 * @param object $node Instance of admin_category, or admin_settingpage
 * @param array $data
 * @return array
 */
function admin_find_write_settings($node, $data) {
    $return = array();

    if (empty($data)) {
        return $return;
    }

    if ($node instanceof admin_category) {
        if ($node->check_access()) {
            $entries = array_keys($node->children);
            foreach ($entries as $entry) {
                $return = array_merge($return, admin_find_write_settings($node->children[$entry], $data));
            }
        }

    } else if ($node instanceof admin_settingpage) {
        if ($node->check_access()) {
            foreach ($node->settings as $setting) {
                $fullname = $setting->get_full_name();
                if (array_key_exists($fullname, $data)) {
                    $return[$fullname] = $setting;
                }
            }
        }

    }

    return $return;
}

/**
 * Internal function - prints the search results
 *
 * @param string $query String to search for
 * @return string empty or XHTML
 */
function admin_search_settings_html($query) {
    global $CFG, $OUTPUT, $PAGE;

    if (core_text::strlen($query) < 2) {
        return '';
    }
    $query = core_text::strtolower($query);

    $adminroot = admin_get_root();
    $findings = $adminroot->search($query);
    $savebutton = false;

    $tpldata = (object) [
        'actionurl' => $PAGE->url->out(false),
        'results' => [],
        'sesskey' => sesskey(),
    ];

    foreach ($findings as $found) {
        $page     = $found->page;
        $settings = $found->settings;
        if ($page->is_hidden()) {
        // hidden pages are not displayed in search results
            continue;
        }

        $heading = highlight($query, $page->visiblename);
        $headingurl = null;
        if ($page instanceof admin_externalpage) {
            $headingurl = new moodle_url($page->url);
        } else if ($page instanceof admin_settingpage) {
            $headingurl = new moodle_url('/admin/settings.php', ['section' => $page->name]);
        } else {
            continue;
        }

        // Locate the page in the admin root and populate its visiblepath attribute.
        $path = array();
        $located = $adminroot->locate($page->name, true);
        if ($located) {
            foreach ($located->visiblepath as $pathitem) {
                array_unshift($path, (string) $pathitem);
            }
        }

        $sectionsettings = [];
        if (!empty($settings)) {
            foreach ($settings as $setting) {
                if (empty($setting->nosave)) {
                    $savebutton = true;
                }
                $fullname = $setting->get_full_name();
                if (array_key_exists($fullname, $adminroot->errors)) {
                    $data = $adminroot->errors[$fullname]->data;
                } else {
                    $data = $setting->get_setting();
                // do not use defaults if settings not available - upgradesettings handles the defaults!
                }
                $sectionsettings[] = $setting->output_html($data, $query);
            }
        }

        $tpldata->results[] = (object) [
            'title' => $heading,
            'path' => $path,
            'url' => $headingurl->out(false),
            'settings' => $sectionsettings
        ];
    }

    $tpldata->showsave = $savebutton;
    $tpldata->hasresults = !empty($tpldata->results);

    return $OUTPUT->render_from_template('core_admin/settings_search_results', $tpldata);
}

/**
 * Internal function - returns arrays of html pages with uninitialised settings
 *
 * @param object $node Instance of admin_category or admin_settingpage
 * @return array
 */
function admin_output_new_settings_by_page($node) {
    global $OUTPUT;
    $return = array();

    if ($node instanceof admin_category) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            $return += admin_output_new_settings_by_page($node->children[$entry]);
        }

    } else if ($node instanceof admin_settingpage) {
            $newsettings = array();
            foreach ($node->settings as $setting) {
                if (is_null($setting->get_setting())) {
                    $newsettings[] = $setting;
                }
            }
            if (count($newsettings) > 0) {
                $adminroot = admin_get_root();
                $page = $OUTPUT->heading(get_string('upgradesettings','admin').' - '.$node->visiblename, 2, 'main');
                $page .= '<fieldset class="adminsettings">'."\n";
                foreach ($newsettings as $setting) {
                    $fullname = $setting->get_full_name();
                    if (array_key_exists($fullname, $adminroot->errors)) {
                        $data = $adminroot->errors[$fullname]->data;
                    } else {
                        $data = $setting->get_setting();
                        if (is_null($data)) {
                            $data = $setting->get_defaultsetting();
                        }
                    }
                    $page .= '<div class="clearer"><!-- --></div>'."\n";
                    $page .= $setting->output_html($data);
                }
                $page .= '</fieldset>';
                $return[$node->name] = $page;
            }
        }

    return $return;
}

/**
 * Format admin settings
 *
 * @param object $setting
 * @param string $title label element
 * @param string $form form fragment, html code - not highlighted automatically
 * @param string $description
 * @param mixed $label link label to id, true by default or string being the label to connect it to
 * @param string $warning warning text
 * @param sting $defaultinfo defaults info, null means nothing, '' is converted to "Empty" string, defaults to null
 * @param string $query search query to be highlighted
 * @return string XHTML
 */
function format_admin_setting($setting, $title='', $form='', $description='', $label=true, $warning='', $defaultinfo=NULL, $query='') {
    global $CFG, $OUTPUT;

    $context = (object) [
        'name' => empty($setting->plugin) ? $setting->name : "$setting->plugin | $setting->name",
        'fullname' => $setting->get_full_name(),
    ];

    // Sometimes the id is not id_s_name, but id_s_name_m or something, and this does not validate.
    if ($label === true) {
        $context->labelfor = $setting->get_id();
    } else if ($label === false) {
        $context->labelfor = '';
    } else {
        $context->labelfor = $label;
    }

    $form .= $setting->output_setting_flags();

    $context->warning = $warning;
    $context->override = '';
    if (empty($setting->plugin)) {
        if (array_key_exists($setting->name, $CFG->config_php_settings)) {
            $context->override = get_string('configoverride', 'admin');
        }
    } else {
        if (array_key_exists($setting->plugin, $CFG->forced_plugin_settings) and array_key_exists($setting->name, $CFG->forced_plugin_settings[$setting->plugin])) {
            $context->override = get_string('configoverride', 'admin');
        }
    }

    $defaults = array();
    if (!is_null($defaultinfo)) {
        if ($defaultinfo === '') {
            $defaultinfo = get_string('emptysettingvalue', 'admin');
        }
        $defaults[] = $defaultinfo;
    }

    $context->default = null;
    $setting->get_setting_flag_defaults($defaults);
    if (!empty($defaults)) {
        $defaultinfo = implode(', ', $defaults);
        $defaultinfo = highlight($query, nl2br(s($defaultinfo)));
        $context->default = get_string('defaultsettinginfo', 'admin', $defaultinfo);
    }


    $context->error = '';
    $adminroot = admin_get_root();
    if (array_key_exists($context->fullname, $adminroot->errors)) {
        $context->error = $adminroot->errors[$context->fullname]->error;
    }

    if ($dependenton = $setting->get_dependent_on()) {
        $context->dependenton = get_string('settingdependenton', 'admin', implode(', ', $dependenton));
    }

    $context->id = 'admin-' . $setting->name;
    $context->title = highlightfast($query, $title);
    $context->name = highlightfast($query, $context->name);
    $context->description = highlight($query, markdown_to_html($description));
    $context->element = $form;
    $context->forceltr = $setting->get_force_ltr();

    return $OUTPUT->render_from_template('core_admin/setting', $context);
}

/**
 * Based on find_new_settings{@link ()}  in upgradesettings.php
 * Looks to find any admin settings that have not been initialized. Returns 1 if it finds any.
 *
 * @param object $node Instance of admin_category, or admin_settingpage
 * @return boolean true if any settings haven't been initialised, false if they all have
 */
function any_new_admin_settings($node) {

    if ($node instanceof admin_category) {
        $entries = array_keys($node->children);
        foreach ($entries as $entry) {
            if (any_new_admin_settings($node->children[$entry])) {
                return true;
            }
        }

    } else if ($node instanceof admin_settingpage) {
            foreach ($node->settings as $setting) {
                if ($setting->get_setting() === NULL) {
                    return true;
                }
            }
        }

    return false;
}

/**
 * Moved from admin/replace.php so that we can use this in cron
 *
 * @param string $search string to look for
 * @param string $replace string to replace
 * @return bool success or fail
 */
function db_replace($search, $replace) {
    global $DB, $CFG, $OUTPUT;

    // TODO: this is horrible hack, we should do whitelisting and each plugin should be responsible for proper replacing...
    $skiptables = array('config', 'config_plugins', 'config_log', 'upgrade_log', 'log',
                        'filter_config', 'sessions', 'events_queue', 'repository_instance_config',
                        'block_instances', '');

    // Turn off time limits, sometimes upgrades can be slow.
    core_php_time_limit::raise();

    if (!$tables = $DB->get_tables() ) {    // No tables yet at all.
        return false;
    }
    foreach ($tables as $table) {

        if (in_array($table, $skiptables)) {      // Don't process these
            continue;
        }

        if ($columns = $DB->get_columns($table)) {
            $DB->set_debug(true);
            foreach ($columns as $column) {
                $DB->replace_all_text($table, $column, $search, $replace);
            }
            $DB->set_debug(false);
        }
    }

    // delete modinfo caches
    rebuild_course_cache(0, true);

    // TODO: we should ask all plugins to do the search&replace, for now let's do only blocks...
    $blocks = core_component::get_plugin_list('block');
    foreach ($blocks as $blockname=>$fullblock) {
        if ($blockname === 'NEWBLOCK') {   // Someone has unzipped the template, ignore it
            continue;
        }

        if (!is_readable($fullblock.'/lib.php')) {
            continue;
        }

        $function = 'block_'.$blockname.'_global_db_replace';
        include_once($fullblock.'/lib.php');
        if (!function_exists($function)) {
            continue;
        }

        echo $OUTPUT->notification("Replacing in $blockname blocks...", 'notifysuccess');
        $function($search, $replace);
        echo $OUTPUT->notification("...finished", 'notifysuccess');
    }

    purge_all_caches();

    return true;
}

/**
 * Manage repository settings
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managerepository extends admin_setting {
/** @var string */
    private $baseurl;

    /**
     * calls parent::__construct with specific arguments
     */
    public function __construct() {
        global $CFG;
        parent::__construct('managerepository', get_string('manage', 'repository'), '', '');
        $this->baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/repository.php?sesskey=' . sesskey();
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns s_managerepository
     *
     * @return string Always return 's_managerepository'
     */
    public function get_full_name() {
        return 's_managerepository';
    }

    /**
     * Always returns '' doesn't do anything
     */
    public function write_setting($data) {
        $url = $this->baseurl . '&amp;new=' . $data;
        return '';
    // TODO
    // Should not use redirect and exit here
    // Find a better way to do this.
    // redirect($url);
    // exit;
    }

    /**
     * Searches repository plugins for one that matches $query
     *
     * @param string $query The string to search for
     * @return bool true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $repositories= core_component::get_plugin_list('repository');
        foreach ($repositories as $p => $dir) {
            if (strpos($p, $query) !== false) {
                return true;
            }
        }
        foreach (repository::get_types() as $instance) {
            $title = $instance->get_typename();
            if (strpos(core_text::strtolower($title), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Helper function that generates a moodle_url object
     * relevant to the repository
     */

    function repository_action_url($repository) {
        return new moodle_url($this->baseurl, array('sesskey'=>sesskey(), 'repos'=>$repository));
    }

    /**
     * Builds XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string XHTML
     */
    public function output_html($data, $query='') {
        global $CFG, $USER, $OUTPUT;

        // Get strings that are used
        $strshow = get_string('on', 'repository');
        $strhide = get_string('off', 'repository');
        $strdelete = get_string('disabled', 'repository');

        $actionchoicesforexisting = array(
            'show' => $strshow,
            'hide' => $strhide,
            'delete' => $strdelete
        );

        $actionchoicesfornew = array(
            'newon' => $strshow,
            'newoff' => $strhide,
            'delete' => $strdelete
        );

        $return = '';
        $return .= $OUTPUT->box_start('generalbox');

        // Set strings that are used multiple times
        $settingsstr = get_string('settings');
        $disablestr = get_string('disable');

        // Table to list plug-ins
        $table = new html_table();
        $table->head = array(get_string('name'), get_string('isactive', 'repository'), get_string('order'), $settingsstr);
        $table->align = array('left', 'center', 'center', 'center', 'center');
        $table->data = array();

        // Get list of used plug-ins
        $repositorytypes = repository::get_types();
        if (!empty($repositorytypes)) {
            // Array to store plugins being used
            $alreadyplugins = array();
            $totalrepositorytypes = count($repositorytypes);
            $updowncount = 1;
            foreach ($repositorytypes as $i) {
                $settings = '';
                $typename = $i->get_typename();
                // Display edit link only if you can config the type or if it has multiple instances (e.g. has instance config)
                $typeoptionnames = repository::static_function($typename, 'get_type_option_names');
                $instanceoptionnames = repository::static_function($typename, 'get_instance_option_names');

                if (!empty($typeoptionnames) || !empty($instanceoptionnames)) {
                    // Calculate number of instances in order to display them for the Moodle administrator
                    if (!empty($instanceoptionnames)) {
                        $params = array();
                        $params['context'] = array(context_system::instance());
                        $params['onlyvisible'] = false;
                        $params['type'] = $typename;
                        $admininstancenumber = count(repository::static_function($typename, 'get_instances', $params));
                        // site instances
                        $admininstancenumbertext = get_string('instancesforsite', 'repository', $admininstancenumber);
                        $params['context'] = array();
                        $instances = repository::static_function($typename, 'get_instances', $params);
                        $courseinstances = array();
                        $userinstances = array();

                        foreach ($instances as $instance) {
                            $repocontext = context::instance_by_id($instance->instance->contextid);
                            if ($repocontext->contextlevel == CONTEXT_COURSE) {
                                $courseinstances[] = $instance;
                            } else if ($repocontext->contextlevel == CONTEXT_USER) {
                                $userinstances[] = $instance;
                            }
                        }
                        // course instances
                        $instancenumber = count($courseinstances);
                        $courseinstancenumbertext = get_string('instancesforcourses', 'repository', $instancenumber);

                        // user private instances
                        $instancenumber =  count($userinstances);
                        $userinstancenumbertext = get_string('instancesforusers', 'repository', $instancenumber);
                    } else {
                        $admininstancenumbertext = "";
                        $courseinstancenumbertext = "";
                        $userinstancenumbertext = "";
                    }

                    $settings .= '<a href="' . $this->baseurl . '&amp;action=edit&amp;repos=' . $typename . '">' . $settingsstr .'</a>';

                    $settings .= $OUTPUT->container_start('mdl-left');
                    $settings .= '<br/>';
                    $settings .= $admininstancenumbertext;
                    $settings .= '<br/>';
                    $settings .= $courseinstancenumbertext;
                    $settings .= '<br/>';
                    $settings .= $userinstancenumbertext;
                    $settings .= $OUTPUT->container_end();
                }
                // Get the current visibility
                if ($i->get_visible()) {
                    $currentaction = 'show';
                } else {
                    $currentaction = 'hide';
                }

                $select = new single_select($this->repository_action_url($typename, 'repos'), 'action', $actionchoicesforexisting, $currentaction, null, 'applyto' . basename($typename));

                // Display up/down link
                $updown = '';
                // Should be done with CSS instead.
                $spacer = $OUTPUT->spacer(array('height' => 15, 'width' => 15, 'class' => 'smallicon'));

                if ($updowncount > 1) {
                    $updown .= "<a href=\"$this->baseurl&amp;action=moveup&amp;repos=".$typename."\">";
                    $updown .= $OUTPUT->pix_icon('t/up', get_string('moveup')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $spacer;
                }
                if ($updowncount < $totalrepositorytypes) {
                    $updown .= "<a href=\"$this->baseurl&amp;action=movedown&amp;repos=".$typename."\">";
                    $updown .= $OUTPUT->pix_icon('t/down', get_string('movedown')) . '</a>&nbsp;';
                }
                else {
                    $updown .= $spacer;
                }

                $updowncount++;

                $table->data[] = array($i->get_readablename(), $OUTPUT->render($select), $updown, $settings);

                if (!in_array($typename, $alreadyplugins)) {
                    $alreadyplugins[] = $typename;
                }
            }
        }

        // Get all the plugins that exist on disk
        $plugins = core_component::get_plugin_list('repository');
        if (!empty($plugins)) {
            foreach ($plugins as $plugin => $dir) {
                // Check that it has not already been listed
                if (!in_array($plugin, $alreadyplugins)) {
                    $select = new single_select($this->repository_action_url($plugin, 'repos'), 'action', $actionchoicesfornew, 'delete', null, 'applyto' . basename($plugin));
                    $table->data[] = array(get_string('pluginname', 'repository_'.$plugin), $OUTPUT->render($select), '', '');
                }
            }
        }

        $return .= html_writer::table($table);
        $return .= $OUTPUT->box_end();
        return highlight($query, $return);
    }
}

/**
 * Special checkbox for enable mobile web service
 * If enable then we store the service id of the mobile service into config table
 * If disable then we unstore the service id from the config table
 */
class admin_setting_enablemobileservice extends admin_setting_configcheckbox {

    /** @var boolean True means that the capability 'webservice/rest:use' is set for authenticated user role */
    private $restuse;

    /**
     * Return true if Authenticated user role has the capability 'webservice/rest:use', otherwise false.
     *
     * @return boolean
     */
    private function is_protocol_cap_allowed() {
        global $DB, $CFG;

        // If the $this->restuse variable is not set, it needs to be set.
        if (empty($this->restuse) and $this->restuse!==false) {
            $params = array();
            $params['permission'] = CAP_ALLOW;
            $params['roleid'] = $CFG->defaultuserroleid;
            $params['capability'] = 'webservice/rest:use';
            $this->restuse = $DB->record_exists('role_capabilities', $params);
        }

        return $this->restuse;
    }

    /**
     * Set the 'webservice/rest:use' to the Authenticated user role (allow or not)
     * @param type $status true to allow, false to not set
     */
    private function set_protocol_cap($status) {
        global $CFG;
        if ($status and !$this->is_protocol_cap_allowed()) {
            //need to allow the cap
            $permission = CAP_ALLOW;
            $assign = true;
        } else if (!$status and $this->is_protocol_cap_allowed()){
            //need to disallow the cap
            $permission = CAP_INHERIT;
            $assign = true;
        }
        if (!empty($assign)) {
            $systemcontext = context_system::instance();
            assign_capability('webservice/rest:use', $permission, $CFG->defaultuserroleid, $systemcontext->id, true);
        }
    }

    /**
     * Builds XHTML to display the control.
     * The main purpose of this overloading is to display a warning when https
     * is not supported by the server
     * @param string $data Unused
     * @param string $query
     * @return string XHTML
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
        $html = parent::output_html($data, $query);

        if ((string)$data === $this->yes) {
            $notifications = tool_mobile\api::get_potential_config_issues(); // Safe to call, plugin available if we reach here.
            foreach ($notifications as $notification) {
                $message = get_string($notification[0], $notification[1]);
                $html .= $OUTPUT->notification($message, \core\output\notification::NOTIFY_WARNING);
            }
        }

        return $html;
    }

    /**
     * Retrieves the current setting using the objects name
     *
     * @return string
     */
    public function get_setting() {
        global $CFG;

        // First check if is not set.
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return null;
        }

        // For install cli script, $CFG->defaultuserroleid is not set so return 0
        // Or if web services aren't enabled this can't be,
        if (empty($CFG->defaultuserroleid) || empty($CFG->enablewebservices)) {
            return 0;
        }

        require_once($CFG->dirroot . '/webservice/lib.php');
        $webservicemanager = new webservice();
        $mobileservice = $webservicemanager->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
        if ($mobileservice->enabled and $this->is_protocol_cap_allowed()) {
            return $result;
        } else {
            return 0;
        }
    }

    /**
     * Save the selected setting
     *
     * @param string $data The selected site
     * @return string empty string or error message
     */
    public function write_setting($data) {
        global $DB, $CFG;

        //for install cli script, $CFG->defaultuserroleid is not set so do nothing
        if (empty($CFG->defaultuserroleid)) {
            return '';
        }

        $servicename = MOODLE_OFFICIAL_MOBILE_SERVICE;

        require_once($CFG->dirroot . '/webservice/lib.php');
        $webservicemanager = new webservice();

        $updateprotocol = false;
        if ((string)$data === $this->yes) {
             //code run when enable mobile web service
             //enable web service systeme if necessary
             set_config('enablewebservices', true);

             //enable mobile service
             $mobileservice = $webservicemanager->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
             $mobileservice->enabled = 1;
             $webservicemanager->update_external_service($mobileservice);

             // Enable REST server.
             $activeprotocols = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);

             if (!in_array('rest', $activeprotocols)) {
                 $activeprotocols[] = 'rest';
                 $updateprotocol = true;
             }

             if ($updateprotocol) {
                 set_config('webserviceprotocols', implode(',', $activeprotocols));
             }

             // Allow rest:use capability for authenticated user.
             $this->set_protocol_cap(true);

         } else {
             //disable web service system if no other services are enabled
             $otherenabledservices = $DB->get_records_select('external_services',
                     'enabled = :enabled AND (shortname != :shortname OR shortname IS NULL)', array('enabled' => 1,
                         'shortname' => MOODLE_OFFICIAL_MOBILE_SERVICE));
             if (empty($otherenabledservices)) {
                 set_config('enablewebservices', false);

                 // Also disable REST server.
                 $activeprotocols = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);

                 $protocolkey = array_search('rest', $activeprotocols);
                 if ($protocolkey !== false) {
                    unset($activeprotocols[$protocolkey]);
                    $updateprotocol = true;
                 }

                 if ($updateprotocol) {
                    set_config('webserviceprotocols', implode(',', $activeprotocols));
                 }

                 // Disallow rest:use capability for authenticated user.
                 $this->set_protocol_cap(false);
             }

             //disable the mobile service
             $mobileservice = $webservicemanager->get_external_service_by_shortname(MOODLE_OFFICIAL_MOBILE_SERVICE);
             $mobileservice->enabled = 0;
             $webservicemanager->update_external_service($mobileservice);
         }

        return (parent::write_setting($data));
    }
}

/**
 * Special class for management of external services
 *
 * @author Petr Skoda (skodak)
 */
class admin_setting_manageexternalservices extends admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('webservicesui', get_string('externalservices', 'webservice'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Checks if $query is one of the available external services
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        global $DB;

        if (parent::is_related($query)) {
            return true;
        }

        $services = $DB->get_records('external_services', array(), 'id, name');
        foreach ($services as $service) {
            if (strpos(core_text::strtolower($service->name), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $DB;

        // display strings
        $stradministration = get_string('administration');
        $stredit = get_string('edit');
        $strservice = get_string('externalservice', 'webservice');
        $strdelete = get_string('delete');
        $strplugin = get_string('plugin', 'admin');
        $stradd = get_string('add');
        $strfunctions = get_string('functions', 'webservice');
        $strusers = get_string('users');
        $strserviceusers = get_string('serviceusers', 'webservice');

        $esurl = "$CFG->wwwroot/$CFG->admin/webservice/service.php";
        $efurl = "$CFG->wwwroot/$CFG->admin/webservice/service_functions.php";
        $euurl = "$CFG->wwwroot/$CFG->admin/webservice/service_users.php";

        // built in services
         $services = $DB->get_records_select('external_services', 'component IS NOT NULL', null, 'name');
         $return = "";
         if (!empty($services)) {
            $return .= $OUTPUT->heading(get_string('servicesbuiltin', 'webservice'), 3, 'main');



            $table = new html_table();
            $table->head  = array($strservice, $strplugin, $strfunctions, $strusers, $stredit);
            $table->colclasses = array('leftalign service', 'leftalign plugin', 'centeralign functions', 'centeralign users', 'centeralign ');
            $table->id = 'builtinservices';
            $table->attributes['class'] = 'admintable externalservices generaltable';
            $table->data  = array();

            // iterate through auth plugins and add to the display table
            foreach ($services as $service) {
                $name = $service->name;

                // hide/show link
                if ($service->enabled) {
                    $displayname = "<span>$name</span>";
                } else {
                    $displayname = "<span class=\"dimmed_text\">$name</span>";
                }

                $plugin = $service->component;

                $functions = "<a href=\"$efurl?id=$service->id\">$strfunctions</a>";

                if ($service->restrictedusers) {
                    $users = "<a href=\"$euurl?id=$service->id\">$strserviceusers</a>";
                } else {
                    $users = get_string('allusers', 'webservice');
                }

                $edit = "<a href=\"$esurl?id=$service->id\">$stredit</a>";

                // add a row to the table
                $table->data[] = array($displayname, $plugin, $functions, $users, $edit);
            }
            $return .= html_writer::table($table);
        }

        // Custom services
        $return .= $OUTPUT->heading(get_string('servicescustom', 'webservice'), 3, 'main');
        $services = $DB->get_records_select('external_services', 'component IS NULL', null, 'name');

        $table = new html_table();
        $table->head  = array($strservice, $strdelete, $strfunctions, $strusers, $stredit);
        $table->colclasses = array('leftalign service', 'leftalign plugin', 'centeralign functions', 'centeralign users', 'centeralign ');
        $table->id = 'customservices';
        $table->attributes['class'] = 'admintable externalservices generaltable';
        $table->data  = array();

        // iterate through auth plugins and add to the display table
        foreach ($services as $service) {
            $name = $service->name;

            // hide/show link
            if ($service->enabled) {
                $displayname = "<span>$name</span>";
            } else {
                $displayname = "<span class=\"dimmed_text\">$name</span>";
            }

            // delete link
            $delete = "<a href=\"$esurl?action=delete&amp;sesskey=".sesskey()."&amp;id=$service->id\">$strdelete</a>";

            $functions = "<a href=\"$efurl?id=$service->id\">$strfunctions</a>";

            if ($service->restrictedusers) {
                $users = "<a href=\"$euurl?id=$service->id\">$strserviceusers</a>";
            } else {
                $users = get_string('allusers', 'webservice');
            }

            $edit = "<a href=\"$esurl?id=$service->id\">$stredit</a>";

            // add a row to the table
            $table->data[] = array($displayname, $delete, $functions, $users, $edit);
        }
        // add new custom service option
        $return .= html_writer::table($table);

        $return .= '<br />';
        // add a token to the table
        $return .= "<a href=\"$esurl?id=0\">$stradd</a>";

        return highlight($query, $return);
    }
}

/**
 * Special class for overview of external services
 *
 * @author Jerome Mouneyrac
 */
class admin_setting_webservicesoverview extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('webservicesoverviewui',
                        get_string('webservicesoverview', 'webservice'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // do not write any setting
        return '';
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $return = "";
        $brtag = html_writer::empty_tag('br');

        /// One system controlling Moodle with Token
        $return .= $OUTPUT->heading(get_string('onesystemcontrolling', 'webservice'), 3, 'main');
        $table = new html_table();
        $table->head = array(get_string('step', 'webservice'), get_string('status'),
            get_string('description'));
        $table->colclasses = array('leftalign step', 'leftalign status', 'leftalign description');
        $table->id = 'onesystemcontrol';
        $table->attributes['class'] = 'admintable wsoverview generaltable';
        $table->data = array();

        $return .= $brtag . get_string('onesystemcontrollingdescription', 'webservice')
                . $brtag . $brtag;

        /// 1. Enable Web Services
        $row = array();
        $url = new moodle_url("/admin/search.php?query=enablewebservices");
        $row[0] = "1. " . html_writer::tag('a', get_string('enablews', 'webservice'),
                        array('href' => $url));
        $status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
        if ($CFG->enablewebservices) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enablewsdescription', 'webservice');
        $table->data[] = $row;

        /// 2. Enable protocols
        $row = array();
        $url = new moodle_url("/admin/settings.php?section=webserviceprotocols");
        $row[0] = "2. " . html_writer::tag('a', get_string('enableprotocols', 'webservice'),
                        array('href' => $url));
        $status = html_writer::tag('span', get_string('none'), array('class' => 'statuscritical'));
        //retrieve activated protocol
        $active_protocols = empty($CFG->webserviceprotocols) ?
                array() : explode(',', $CFG->webserviceprotocols);
        if (!empty($active_protocols)) {
            $status = "";
            foreach ($active_protocols as $protocol) {
                $status .= $protocol . $brtag;
            }
        }
        $row[1] = $status;
        $row[2] = get_string('enableprotocolsdescription', 'webservice');
        $table->data[] = $row;

        /// 3. Create user account
        $row = array();
        $url = new moodle_url("/user/editadvanced.php?id=-1");
        $row[0] = "3. " . html_writer::tag('a', get_string('createuser', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createuserdescription', 'webservice');
        $table->data[] = $row;

        /// 4. Add capability to users
        $row = array();
        $url = new moodle_url("/admin/roles/check.php?contextid=1");
        $row[0] = "4. " . html_writer::tag('a', get_string('checkusercapability', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('checkusercapabilitydescription', 'webservice');
        $table->data[] = $row;

        /// 5. Select a web service
        $row = array();
        $url = new moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "5. " . html_writer::tag('a', get_string('selectservice', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createservicedescription', 'webservice');
        $table->data[] = $row;

        /// 6. Add functions
        $row = array();
        $url = new moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "6. " . html_writer::tag('a', get_string('addfunctions', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('addfunctionsdescription', 'webservice');
        $table->data[] = $row;

        /// 7. Add the specific user
        $row = array();
        $url = new moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "7. " . html_writer::tag('a', get_string('selectspecificuser', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('selectspecificuserdescription', 'webservice');
        $table->data[] = $row;

        /// 8. Create token for the specific user
        $row = array();
        $url = new moodle_url("/admin/webservice/tokens.php?sesskey=" . sesskey() . "&action=create");
        $row[0] = "8. " . html_writer::tag('a', get_string('createtokenforuser', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createtokenforuserdescription', 'webservice');
        $table->data[] = $row;

        /// 9. Enable the documentation
        $row = array();
        $url = new moodle_url("/admin/search.php?query=enablewsdocumentation");
        $row[0] = "9. " . html_writer::tag('a', get_string('enabledocumentation', 'webservice'),
                        array('href' => $url));
        $status = '<span class="warning">' . get_string('no') . '</span>';
        if ($CFG->enablewsdocumentation) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enabledocumentationdescription', 'webservice');
        $table->data[] = $row;

        /// 10. Test the service
        $row = array();
        $url = new moodle_url("/admin/webservice/testclient.php");
        $row[0] = "10. " . html_writer::tag('a', get_string('testwithtestclient', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('testwithtestclientdescription', 'webservice');
        $table->data[] = $row;

        $return .= html_writer::table($table);

        /// Users as clients with token
        $return .= $brtag . $brtag . $brtag;
        $return .= $OUTPUT->heading(get_string('userasclients', 'webservice'), 3, 'main');
        $table = new html_table();
        $table->head = array(get_string('step', 'webservice'), get_string('status'),
            get_string('description'));
        $table->colclasses = array('leftalign step', 'leftalign status', 'leftalign description');
        $table->id = 'userasclients';
        $table->attributes['class'] = 'admintable wsoverview generaltable';
        $table->data = array();

        $return .= $brtag . get_string('userasclientsdescription', 'webservice') .
                $brtag . $brtag;

        /// 1. Enable Web Services
        $row = array();
        $url = new moodle_url("/admin/search.php?query=enablewebservices");
        $row[0] = "1. " . html_writer::tag('a', get_string('enablews', 'webservice'),
                        array('href' => $url));
        $status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
        if ($CFG->enablewebservices) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enablewsdescription', 'webservice');
        $table->data[] = $row;

        /// 2. Enable protocols
        $row = array();
        $url = new moodle_url("/admin/settings.php?section=webserviceprotocols");
        $row[0] = "2. " . html_writer::tag('a', get_string('enableprotocols', 'webservice'),
                        array('href' => $url));
        $status = html_writer::tag('span', get_string('none'), array('class' => 'statuscritical'));
        //retrieve activated protocol
        $active_protocols = empty($CFG->webserviceprotocols) ?
                array() : explode(',', $CFG->webserviceprotocols);
        if (!empty($active_protocols)) {
            $status = "";
            foreach ($active_protocols as $protocol) {
                $status .= $protocol . $brtag;
            }
        }
        $row[1] = $status;
        $row[2] = get_string('enableprotocolsdescription', 'webservice');
        $table->data[] = $row;


        /// 3. Select a web service
        $row = array();
        $url = new moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "3. " . html_writer::tag('a', get_string('selectservice', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createserviceforusersdescription', 'webservice');
        $table->data[] = $row;

        /// 4. Add functions
        $row = array();
        $url = new moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "4. " . html_writer::tag('a', get_string('addfunctions', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('addfunctionsdescription', 'webservice');
        $table->data[] = $row;

        /// 5. Add capability to users
        $row = array();
        $url = new moodle_url("/admin/roles/check.php?contextid=1");
        $row[0] = "5. " . html_writer::tag('a', get_string('addcapabilitytousers', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('addcapabilitytousersdescription', 'webservice');
        $table->data[] = $row;

        /// 6. Test the service
        $row = array();
        $url = new moodle_url("/admin/webservice/testclient.php");
        $row[0] = "6. " . html_writer::tag('a', get_string('testwithtestclient', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('testauserwithtestclientdescription', 'webservice');
        $table->data[] = $row;

        $return .= html_writer::table($table);

        return highlight($query, $return);
    }

}


/**
 * Special class for web service protocol administration.
 *
 * @author Petr Skoda (skodak)
 */
class admin_setting_managewebserviceprotocols extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('webservicesui', get_string('manageprotocols', 'webservice'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Checks if $query is one of the available webservices
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $protocols = core_component::get_plugin_list('webservice');
        foreach ($protocols as $protocol=>$location) {
            if (strpos($protocol, $query) !== false) {
                return true;
            }
            $protocolstr = get_string('pluginname', 'webservice_'.$protocol);
            if (strpos(core_text::strtolower($protocolstr), $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        // display strings
        $stradministration = get_string('administration');
        $strsettings = get_string('settings');
        $stredit = get_string('edit');
        $strprotocol = get_string('protocol', 'webservice');
        $strenable = get_string('enable');
        $strdisable = get_string('disable');
        $strversion = get_string('version');

        $protocols_available = core_component::get_plugin_list('webservice');
        $active_protocols = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
        ksort($protocols_available);

        foreach ($active_protocols as $key=>$protocol) {
            if (empty($protocols_available[$protocol])) {
                unset($active_protocols[$key]);
            }
        }

        $return = $OUTPUT->heading(get_string('actwebserviceshhdr', 'webservice'), 3, 'main');
        $return .= $OUTPUT->box_start('generalbox webservicesui');

        $table = new html_table();
        $table->head  = array($strprotocol, $strversion, $strenable, $strsettings);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'webserviceprotocols';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data  = array();

        // iterate through auth plugins and add to the display table
        $url = "$CFG->wwwroot/$CFG->admin/webservice/protocols.php?sesskey=" . sesskey();
        foreach ($protocols_available as $protocol => $location) {
            $name = get_string('pluginname', 'webservice_'.$protocol);

            $plugin = new stdClass();
            if (file_exists($CFG->dirroot.'/webservice/'.$protocol.'/version.php')) {
                include($CFG->dirroot.'/webservice/'.$protocol.'/version.php');
            }
            $version = isset($plugin->version) ? $plugin->version : '';

            // hide/show link
            if (in_array($protocol, $active_protocols)) {
                $hideshow = "<a href=\"$url&amp;action=disable&amp;webservice=$protocol\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', $strdisable) . '</a>';
                $displayname = "<span>$name</span>";
            } else {
                $hideshow = "<a href=\"$url&amp;action=enable&amp;webservice=$protocol\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', $strenable) . '</a>';
                $displayname = "<span class=\"dimmed_text\">$name</span>";
            }

            // settings link
            if (file_exists($CFG->dirroot.'/webservice/'.$protocol.'/settings.php')) {
                $settings = "<a href=\"settings.php?section=webservicesetting$protocol\">$strsettings</a>";
            } else {
                $settings = '';
            }

            // add a row to the table
            $table->data[] = array($displayname, $version, $hideshow, $settings);
        }
        $return .= html_writer::table($table);
        $return .= get_string('configwebserviceplugins', 'webservice');
        $return .= $OUTPUT->box_end();

        return highlight($query, $return);
    }
}


/**
 * Special class for web service token administration.
 *
 * @author Jerome Mouneyrac
 */
class admin_setting_managewebservicetokens extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('webservicestokenui', get_string('managetokens', 'webservice'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        require_once($CFG->dirroot . '/webservice/classes/token_table.php');
        $baseurl = new moodle_url('/' . $CFG->admin . '/settings.php?section=webservicetokens');

        $return = $OUTPUT->box_start('generalbox webservicestokenui');

        if (has_capability('moodle/webservice:managealltokens', context_system::instance())) {
            $return .= \html_writer::div(get_string('onlyseecreatedtokens', 'webservice'));
        }

        $table = new \webservice\token_table('webservicetokens');
        $table->define_baseurl($baseurl);
        $table->attributes['class'] = 'admintable generaltable'; // Any need changing?
        $table->data  = array();
        ob_start();
        $table->out(10, false);
        $tablehtml = ob_get_contents();
        ob_end_clean();
        $return .= $tablehtml;

        $tokenpageurl = "$CFG->wwwroot/$CFG->admin/webservice/tokens.php?sesskey=" . sesskey();

        $return .= $OUTPUT->box_end();
        // add a token to the table
        $return .= "<a href=\"".$tokenpageurl."&amp;action=create\">";
        $return .= get_string('add')."</a>";

        return highlight($query, $return);
    }
}


/**
 * Colour picker
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcolourpicker extends admin_setting {

    /**
     * Information for previewing the colour
     *
     * @var array|null
     */
    protected $previewconfig = null;

    /**
     * Use default when empty.
     */
    protected $usedefaultwhenempty = true;

    /**
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param string $defaultsetting
     * @param array $previewconfig Array('selector'=>'.some .css .selector','style'=>'backgroundColor');
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, array $previewconfig = null,
            $usedefaultwhenempty = true) {
        $this->previewconfig = $previewconfig;
        $this->usedefaultwhenempty = $usedefaultwhenempty;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
        $this->set_force_ltr(true);
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Saves the setting
     *
     * @param string $data
     * @return bool
     */
    public function write_setting($data) {
        $data = $this->validate($data);
        if ($data === false) {
            return  get_string('validateerror', 'admin');
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validates the colour that was entered by the user
     *
     * @param string $data
     * @return string|false
     */
    protected function validate($data) {
        /**
         * List of valid HTML colour names
         *
         * @var array
         */
         $colornames = array(
            'aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure',
            'beige', 'bisque', 'black', 'blanchedalmond', 'blue',
            'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse',
            'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson',
            'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray',
            'darkgrey', 'darkgreen', 'darkkhaki', 'darkmagenta',
            'darkolivegreen', 'darkorange', 'darkorchid', 'darkred',
            'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray',
            'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink',
            'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick',
            'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro',
            'ghostwhite', 'gold', 'goldenrod', 'gray', 'grey', 'green',
            'greenyellow', 'honeydew', 'hotpink', 'indianred', 'indigo',
            'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen',
            'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan',
            'lightgoldenrodyellow', 'lightgray', 'lightgrey', 'lightgreen',
            'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue',
            'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow',
            'lime', 'limegreen', 'linen', 'magenta', 'maroon',
            'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple',
            'mediumseagreen', 'mediumslateblue', 'mediumspringgreen',
            'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream',
            'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive',
            'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod',
            'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip',
            'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red',
            'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown',
            'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue',
            'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan',
            'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white',
            'whitesmoke', 'yellow', 'yellowgreen'
        );

        if (preg_match('/^#?([[:xdigit:]]{3}){1,2}$/', $data)) {
            if (strpos($data, '#')!==0) {
                $data = '#'.$data;
            }
            return $data;
        } else if (in_array(strtolower($data), $colornames)) {
            return $data;
        } else if (preg_match('/rgb\(\d{0,3}%?\, ?\d{0,3}%?, ?\d{0,3}%?\)/i', $data)) {
            return $data;
        } else if (preg_match('/rgba\(\d{0,3}%?\, ?\d{0,3}%?, ?\d{0,3}%?\, ?\d(\.\d)?\)/i', $data)) {
            return $data;
        } else if (preg_match('/hsl\(\d{0,3}\, ?\d{0,3}%, ?\d{0,3}%\)/i', $data)) {
            return $data;
        } else if (preg_match('/hsla\(\d{0,3}\, ?\d{0,3}%,\d{0,3}%\, ?\d(\.\d)?\)/i', $data)) {
            return $data;
        } else if (($data == 'transparent') || ($data == 'currentColor') || ($data == 'inherit')) {
            return $data;
        } else if (empty($data)) {
            if ($this->usedefaultwhenempty){
                return $this->defaultsetting;
            } else {
                return '';
            }
        } else {
            return false;
        }
    }

    /**
     * Generates the HTML for the setting
     *
     * @global moodle_page $PAGE
     * @global core_renderer $OUTPUT
     * @param string $data
     * @param string $query
     */
    public function output_html($data, $query = '') {
        global $PAGE, $OUTPUT;

        $icon = new pix_icon('i/loading', get_string('loading', 'admin'), 'moodle', ['class' => 'loadingicon']);
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'icon' => $icon->export_for_template($OUTPUT),
            'haspreviewconfig' => !empty($this->previewconfig),
            'forceltr' => $this->get_force_ltr()
        ];

        $element = $OUTPUT->render_from_template('core_admin/setting_configcolourpicker', $context);
        $PAGE->requires->js_init_call('M.util.init_colour_picker', array($this->get_id(), $this->previewconfig));

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '',
            $this->get_defaultsetting(), $query);
    }

}


/**
 * Class used for uploading of one file into file storage,
 * the file name is stored in config table.
 *
 * Please note you need to implement your own '_pluginfile' callback function,
 * this setting only stores the file, it does not deal with file serving.
 *
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configstoredfile extends admin_setting {
    /** @var array file area options - should be one file only */
    protected $options;
    /** @var string name of the file area */
    protected $filearea;
    /** @var int intemid */
    protected $itemid;
    /** @var string used for detection of changes */
    protected $oldhashes;

    /**
     * Create new stored file setting.
     *
     * @param string $name low level setting name
     * @param string $visiblename human readable setting name
     * @param string $description description of setting
     * @param mixed $filearea file area for file storage
     * @param int $itemid itemid for file storage
     * @param array $options file area options
     */
    public function __construct($name, $visiblename, $description, $filearea, $itemid = 0, array $options = null) {
        parent::__construct($name, $visiblename, $description, '');
        $this->filearea = $filearea;
        $this->itemid   = $itemid;
        $this->options  = (array)$options;
    }

    /**
     * Applies defaults and returns all options.
     * @return array
     */
    protected function get_options() {
        global $CFG;

        require_once("$CFG->libdir/filelib.php");
        require_once("$CFG->dirroot/repository/lib.php");
        $defaults = array(
            'mainfile' => '', 'subdirs' => 0, 'maxbytes' => -1, 'maxfiles' => 1,
            'accepted_types' => '*', 'return_types' => FILE_INTERNAL, 'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'context' => context_system::instance());
        foreach($this->options as $k => $v) {
            $defaults[$k] = $v;
        }

        return $defaults;
    }

    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        global $USER;

        // Let's not deal with validation here, this is for admins only.
        $current = $this->get_setting();
        if (empty($data) && $current === null) {
            // This will be the case when applying default settings (installation).
            return ($this->config_write($this->name, '') ? '' : get_string('errorsetting', 'admin'));
        } else if (!is_number($data)) {
            // Draft item id is expected here!
            return get_string('errorsetting', 'admin');
        }

        $options = $this->get_options();
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;

        $this->oldhashes = null;
        if ($current) {
            $hash = sha1('/'.$options['context']->id.'/'.$component.'/'.$this->filearea.'/'.$this->itemid.$current);
            if ($file = $fs->get_file_by_hash($hash)) {
                $this->oldhashes = $file->get_contenthash().$file->get_pathnamehash();
            }
            unset($file);
        }

        if ($fs->file_exists($options['context']->id, $component, $this->filearea, $this->itemid, '/', '.')) {
            // Make sure the settings form was not open for more than 4 days and draft areas deleted in the meantime.
            // But we can safely ignore that if the destination area is empty, so that the user is not prompt
            // with an error because the draft area does not exist, as he did not use it.
            $usercontext = context_user::instance($USER->id);
            if (!$fs->file_exists($usercontext->id, 'user', 'draft', $data, '/', '.') && $current !== '') {
                return get_string('errorsetting', 'admin');
            }
        }

        file_save_draft_area_files($data, $options['context']->id, $component, $this->filearea, $this->itemid, $options);
        $files = $fs->get_area_files($options['context']->id, $component, $this->filearea, $this->itemid, 'sortorder,filepath,filename', false);

        $filepath = '';
        if ($files) {
            /** @var stored_file $file */
            $file = reset($files);
            $filepath = $file->get_filepath().$file->get_filename();
        }

        return ($this->config_write($this->name, $filepath) ? '' : get_string('errorsetting', 'admin'));
    }

    public function post_write_settings($original) {
        $options = $this->get_options();
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;

        $current = $this->get_setting();
        $newhashes = null;
        if ($current) {
            $hash = sha1('/'.$options['context']->id.'/'.$component.'/'.$this->filearea.'/'.$this->itemid.$current);
            if ($file = $fs->get_file_by_hash($hash)) {
                $newhashes = $file->get_contenthash().$file->get_pathnamehash();
            }
            unset($file);
        }

        if ($this->oldhashes === $newhashes) {
            $this->oldhashes = null;
            return false;
        }
        $this->oldhashes = null;

        $callbackfunction = $this->updatedcallback;
        if (!empty($callbackfunction) and function_exists($callbackfunction)) {
            $callbackfunction($this->get_full_name());
        }
        return true;
    }

    public function output_html($data, $query = '') {
        global $PAGE, $CFG;

        $options = $this->get_options();
        $id = $this->get_id();
        $elname = $this->get_full_name();
        $draftitemid = file_get_submitted_draft_itemid($elname);
        $component = is_null($this->plugin) ? 'core' : $this->plugin;
        file_prepare_draft_area($draftitemid, $options['context']->id, $component, $this->filearea, $this->itemid, $options);

        // Filemanager form element implementation is far from optimal, we need to rework this if we ever fix it...
        require_once("$CFG->dirroot/lib/form/filemanager.php");

        $fmoptions = new stdClass();
        $fmoptions->mainfile       = $options['mainfile'];
        $fmoptions->maxbytes       = $options['maxbytes'];
        $fmoptions->maxfiles       = $options['maxfiles'];
        $fmoptions->client_id      = uniqid();
        $fmoptions->itemid         = $draftitemid;
        $fmoptions->subdirs        = $options['subdirs'];
        $fmoptions->target         = $id;
        $fmoptions->accepted_types = $options['accepted_types'];
        $fmoptions->return_types   = $options['return_types'];
        $fmoptions->context        = $options['context'];
        $fmoptions->areamaxbytes   = $options['areamaxbytes'];

        $fm = new form_filemanager($fmoptions);
        $output = $PAGE->get_renderer('core', 'files');
        $html = $output->render($fm);

        $html .= '<input value="'.$draftitemid.'" name="'.$elname.'" type="hidden" />';
        $html .= '<input value="" id="'.$id.'" type="hidden" />';

        return format_admin_setting($this, $this->visiblename,
            '<div class="form-filemanager" data-fieldtype="filemanager">'.$html.'</div>',
            $this->description, true, '', '', $query);
    }
}


/**
 * Administration interface for user specified regular expressions for device detection.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_devicedetectregex extends admin_setting {

    /**
     * Calls parent::__construct with specific args
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = '') {
        global $CFG;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the current setting(s)
     *
     * @return array Current settings array
     */
    public function get_setting() {
        global $CFG;

        $config = $this->config_read($this->name);
        if (is_null($config)) {
            return null;
        }

        return $this->prepare_form_data($config);
    }

    /**
     * Save selected settings
     *
     * @param array $data Array of settings to save
     * @return bool
     */
    public function write_setting($data) {
        if (empty($data)) {
            $data = array();
        }

        if ($this->config_write($this->name, $this->process_form_data($data))) {
            return ''; // success
        } else {
            return get_string('errorsetting', 'admin') . $this->visiblename . html_writer::empty_tag('br');
        }
    }

    /**
     * Return XHTML field(s) for regexes
     *
     * @param array $data Array of options to set in HTML
     * @return string XHTML string for the fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $context = (object) [
            'expressions' => [],
            'name' => $this->get_full_name()
        ];

        if (empty($data)) {
            $looplimit = 1;
        } else {
            $looplimit = (count($data)/2)+1;
        }

        for ($i=0; $i<$looplimit; $i++) {

            $expressionname = 'expression'.$i;

            if (!empty($data[$expressionname])){
                $expression = $data[$expressionname];
            } else {
                $expression = '';
            }

            $valuename = 'value'.$i;

            if (!empty($data[$valuename])){
                $value = $data[$valuename];
            } else {
                $value= '';
            }

            $context->expressions[] = [
                'index' => $i,
                'expression' => $expression,
                'value' => $value
            ];
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_devicedetectregex', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', null, $query);
    }

    /**
     * Converts the string of regexes
     *
     * @see self::process_form_data()
     * @param $regexes string of regexes
     * @return array of form fields and their values
     */
    protected function prepare_form_data($regexes) {

        $regexes = json_decode($regexes);

        $form = array();

        $i = 0;

        foreach ($regexes as $value => $regex) {
            $expressionname  = 'expression'.$i;
            $valuename = 'value'.$i;

            $form[$expressionname] = $regex;
            $form[$valuename] = $value;
            $i++;
        }

        return $form;
    }

    /**
     * Converts the data from admin settings form into a string of regexes
     *
     * @see self::prepare_form_data()
     * @param array $data array of admin form fields and values
     * @return false|string of regexes
     */
    protected function process_form_data(array $form) {

        $count = count($form); // number of form field values

        if ($count % 2) {
            // we must get five fields per expression
            return false;
        }

        $regexes = array();
        for ($i = 0; $i < $count / 2; $i++) {
            $expressionname  = "expression".$i;
            $valuename       = "value".$i;

            $expression = trim($form['expression'.$i]);
            $value      = trim($form['value'.$i]);

            if (empty($expression)){
                continue;
            }

            $regexes[$value] = $expression;
        }

        $regexes = json_encode($regexes);

        return $regexes;
    }

}

/**
 * Multiselect for current modules
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configmultiselect_modules extends admin_setting_configmultiselect {
    private $excludesystem;

    /**
     * Calls parent::__construct - note array $choices is not required
     *
     * @param string $name setting name
     * @param string $visiblename localised setting name
     * @param string $description setting description
     * @param array $defaultsetting a plain array of default module ids
     * @param bool $excludesystem If true, excludes modules with 'system' archetype
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = array(),
            $excludesystem = true) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
        $this->excludesystem = $excludesystem;
    }

    /**
     * Loads an array of current module choices
     *
     * @return bool always return true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        global $CFG, $DB;
        $records = $DB->get_records('modules', array('visible'=>1), 'name');
        foreach ($records as $record) {
            // Exclude modules if the code doesn't exist
            if (file_exists("$CFG->dirroot/mod/$record->name/lib.php")) {
                // Also exclude system modules (if specified)
                if (!($this->excludesystem &&
                        plugin_supports('mod', $record->name, FEATURE_MOD_ARCHETYPE) ===
                        MOD_ARCHETYPE_SYSTEM)) {
                    $this->choices[$record->id] = $record->name;
                }
            }
        }
        return true;
    }
}

/**
 * Admin setting to show if a php extension is enabled or not.
 *
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_php_extension_enabled extends admin_setting {

    /** @var string The name of the extension to check for */
    private $extension;

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct($name, $visiblename, $description, $extension) {
        $this->extension = $extension;
        $this->nosave = true;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Outputs the html for this setting.
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $o = '';
        if (!extension_loaded($this->extension)) {
            $warning = $OUTPUT->pix_icon('i/warning', '', '', array('role' => 'presentation')) . ' ' . $this->description;

            $o .= format_admin_setting($this, $this->visiblename, $warning);
        }
        return $o;
    }
}

/**
 * Server timezone setting.
 *
 * @copyright 2015 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */
class admin_setting_servertimezone extends admin_setting_configselect {
    /**
     * Constructor.
     */
    public function __construct() {
        $default = core_date::get_default_php_timezone();
        if ($default === 'UTC') {
            // Nobody really wants UTC, so instead default selection to the country that is confused by the UTC the most.
            $default = 'Europe/London';
        }

        parent::__construct('timezone',
            new lang_string('timezone', 'core_admin'),
            new lang_string('configtimezone', 'core_admin'), $default, null);
    }

    /**
     * Lazy load timezone options.
     * @return bool true if loaded, false if error
     */
    public function load_choices() {
        global $CFG;
        if (is_array($this->choices)) {
            return true;
        }

        $current = isset($CFG->timezone) ? $CFG->timezone : null;
        $this->choices = core_date::get_list_of_timezones($current, false);
        if ($current == 99) {
            // Do not show 99 unless it is current value, we want to get rid of it over time.
            $this->choices['99'] = new lang_string('timezonephpdefault', 'core_admin',
                core_date::get_default_php_timezone());
        }

        return true;
    }
}

/**
 * Forced user timezone setting.
 *
 * @copyright 2015 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */
class admin_setting_forcetimezone extends admin_setting_configselect {
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('forcetimezone',
            new lang_string('forcetimezone', 'core_admin'),
            new lang_string('helpforcetimezone', 'core_admin'), '99', null);
    }

    /**
     * Lazy load timezone options.
     * @return bool true if loaded, false if error
     */
    public function load_choices() {
        global $CFG;
        if (is_array($this->choices)) {
            return true;
        }

        $current = isset($CFG->forcetimezone) ? $CFG->forcetimezone : null;
        $this->choices = core_date::get_list_of_timezones($current, true);
        $this->choices['99'] = new lang_string('timezonenotforced', 'core_admin');

        return true;
    }
}


/**
 * Search setup steps info.
 *
 * @package core
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_searchsetupinfo extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('searchsetupinfo', '', '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param array $data
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Builds the HTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $return = '';
        $brtag = html_writer::empty_tag('br');

        $searchareas = \core_search\manager::get_search_areas_list();
        $anyenabled = !empty(\core_search\manager::get_search_areas_list(true));
        $anyindexed = false;
        foreach ($searchareas as $areaid => $searcharea) {
            list($componentname, $varname) = $searcharea->get_config_var_name();
            if (get_config($componentname, $varname . '_indexingstart')) {
                $anyindexed = true;
                break;
            }
        }

        $return .= $OUTPUT->heading(get_string('searchsetupinfo', 'admin'), 3, 'main');

        $table = new html_table();
        $table->head = array(get_string('step', 'search'), get_string('status'));
        $table->colclasses = array('leftalign step', 'leftalign status');
        $table->id = 'searchsetup';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data = array();

        $return .= $brtag . get_string('searchsetupdescription', 'search') . $brtag . $brtag;

        // Select a search engine.
        $row = array();
        $url = new moodle_url('/admin/settings.php?section=manageglobalsearch#admin-searchengine');
        $row[0] = '1. ' . html_writer::tag('a', get_string('selectsearchengine', 'admin'),
                        array('href' => $url));

        $status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
        if (!empty($CFG->searchengine)) {
            $status = html_writer::tag('span', get_string('pluginname', 'search_' . $CFG->searchengine),
                array('class' => 'statusok'));

        }
        $row[1] = $status;
        $table->data[] = $row;

        // Available areas.
        $row = array();
        $url = new moodle_url('/admin/searchareas.php');
        $row[0] = '2. ' . html_writer::tag('a', get_string('enablesearchareas', 'admin'),
                        array('href' => $url));

        $status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
        if ($anyenabled) {
            $status = html_writer::tag('span', get_string('yes'), array('class' => 'statusok'));

        }
        $row[1] = $status;
        $table->data[] = $row;

        // Setup search engine.
        $row = array();
        if (empty($CFG->searchengine)) {
            $row[0] = '3. ' . get_string('setupsearchengine', 'admin');
            $row[1] = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
        } else {
            $url = new moodle_url('/admin/settings.php?section=search' . $CFG->searchengine);
            $row[0] = '3. ' . html_writer::tag('a', get_string('setupsearchengine', 'admin'),
                            array('href' => $url));
            // Check the engine status.
            $searchengine = \core_search\manager::search_engine_instance();
            try {
                $serverstatus = $searchengine->is_server_ready();
            } catch (\moodle_exception $e) {
                $serverstatus = $e->getMessage();
            }
            if ($serverstatus === true) {
                $status = html_writer::tag('span', get_string('yes'), array('class' => 'statusok'));
            } else {
                $status = html_writer::tag('span', $serverstatus, array('class' => 'statuscritical'));
            }
            $row[1] = $status;
        }
        $table->data[] = $row;

        // Indexed data.
        $row = array();
        $url = new moodle_url('/admin/searchareas.php');
        $row[0] = '4. ' . html_writer::tag('a', get_string('indexdata', 'admin'), array('href' => $url));
        if ($anyindexed) {
            $status = html_writer::tag('span', get_string('yes'), array('class' => 'statusok'));
        } else {
            $status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
        }
        $row[1] = $status;
        $table->data[] = $row;

        // Enable global search.
        $row = array();
        $url = new moodle_url("/admin/search.php?query=enableglobalsearch");
        $row[0] = '5. ' . html_writer::tag('a', get_string('enableglobalsearch', 'admin'),
                        array('href' => $url));
        $status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
        if (\core_search\manager::is_global_search_enabled()) {
            $status = html_writer::tag('span', get_string('yes'), array('class' => 'statusok'));
        }
        $row[1] = $status;
        $table->data[] = $row;

        $return .= html_writer::table($table);

        return highlight($query, $return);
    }

}

/**
 * Used to validate the contents of SCSS code and ensuring they are parsable.
 *
 * It does not attempt to detect undefined SCSS variables because it is designed
 * to be used without knowledge of other config/scss included.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 Dan Poltawski <dan@moodle.com>
 */
class admin_setting_scsscode extends admin_setting_configtextarea {

    /**
     * Validate the contents of the SCSS to ensure its parsable. Does not
     * attempt to detect undefined scss variables.
     *
     * @param string $data The scss code from text field.
     * @return mixed bool true for success or string:error on failure.
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }

        $scss = new core_scss();
        try {
            $scss->compile($data);
        } catch (ScssPhp\ScssPhp\Exception\ParserException $e) {
            return get_string('scssinvalid', 'admin', $e->getMessage());
        } catch (ScssPhp\ScssPhp\Exception\CompilerException $e) {
            // Silently ignore this - it could be a scss variable defined from somewhere
            // else which we are not examining here.
            return true;
        }

        return true;
    }
}


/**
 * Administration setting to define a list of file types.
 *
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @copyright 2017 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_filetypes extends admin_setting_configtext {

    /** @var array Allow selection from these file types only. */
    protected $onlytypes = [];

    /** @var bool Allow selection of 'All file types' (will be stored as '*'). */
    protected $allowall = true;

    /** @var core_form\filetypes_util instance to use as a helper. */
    protected $util = null;

    /**
     * Constructor.
     *
     * @param string $name Unique ascii name like 'mycoresetting' or 'myplugin/mysetting'
     * @param string $visiblename Localised label of the setting
     * @param string $description Localised description of the setting
     * @param string $defaultsetting Default setting value.
     * @param array $options Setting widget options, an array with optional keys:
     *   'onlytypes' => array Allow selection from these file types only; for example ['onlytypes' => ['web_image']].
     *   'allowall' => bool Allow to select 'All file types', defaults to true. Does not apply if onlytypes are set.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = '', array $options = []) {

        parent::__construct($name, $visiblename, $description, $defaultsetting, PARAM_RAW);

        if (array_key_exists('onlytypes', $options) && is_array($options['onlytypes'])) {
            $this->onlytypes = $options['onlytypes'];
        }

        if (!$this->onlytypes && array_key_exists('allowall', $options)) {
            $this->allowall = (bool)$options['allowall'];
        }

        $this->util = new \core_form\filetypes_util();
    }

    /**
     * Normalize the user's input and write it to the database as comma separated list.
     *
     * Comma separated list as a text representation of the array was chosen to
     * make this compatible with how the $CFG->courseoverviewfilesext values are stored.
     *
     * @param string $data Value submitted by the admin.
     * @return string Epty string if all good, error message otherwise.
     */
    public function write_setting($data) {
        return parent::write_setting(implode(',', $this->util->normalize_file_types($data)));
    }

    /**
     * Validate data before storage
     *
     * @param string $data The setting values provided by the admin
     * @return bool|string True if ok, the string if error found
     */
    public function validate($data) {

        // No need to call parent's validation here as we are PARAM_RAW.

        if ($this->util->is_whitelisted($data, $this->onlytypes)) {
            return true;

        } else {
            $troublemakers = $this->util->get_not_whitelisted($data, $this->onlytypes);
            return get_string('filetypesnotwhitelisted', 'core_form', implode(' ', $troublemakers));
        }
    }

    /**
     * Return an HTML string for the setting element.
     *
     * @param string $data The current setting value
     * @param string $query Admin search query to be highlighted
     * @return string HTML to be displayed
     */
    public function output_html($data, $query='') {
        global $OUTPUT, $PAGE;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'descriptions' => $this->util->describe_file_types($data),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_filetypes', $context);

        $PAGE->requires->js_call_amd('core_form/filetypes', 'init', [
            $this->get_id(),
            $this->visiblename->out(),
            $this->onlytypes,
            $this->allowall,
        ]);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }

    /**
     * Should the values be always displayed in LTR mode?
     *
     * We always return true here because these values are not RTL compatible.
     *
     * @return bool True because these values are not RTL compatible.
     */
    public function get_force_ltr() {
        return true;
    }
}

/**
 * Used to validate the content and format of the age of digital consent map and ensuring it is parsable.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2018 Mihail Geshoski <mihail@moodle.com>
 */
class admin_setting_agedigitalconsentmap extends admin_setting_configtextarea {

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param mixed $paramtype
     * @param string $cols
     * @param string $rows
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype = PARAM_RAW,
                                $cols = '60', $rows = '8') {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $cols, $rows);
        // Pre-set force LTR to false.
        $this->set_force_ltr(false);
    }

    /**
     * Validate the content and format of the age of digital consent map to ensure it is parsable.
     *
     * @param string $data The age of digital consent map from text field.
     * @return mixed bool true for success or string:error on failure.
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }

        try {
            \core_auth\digital_consent::parse_age_digital_consent_map($data);
        } catch (\moodle_exception $e) {
            return get_string('invalidagedigitalconsent', 'admin', $e->getMessage());
        }

        return true;
    }
}

/**
 * Selection of plugins that can work as site policy handlers
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2018 Marina Glancy
 */
class admin_settings_sitepolicy_handler_select extends admin_setting_configselect {

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting'
     *        for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = '') {
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    /**
     * Lazy-load the available choices for the select box
     */
    public function load_choices() {
        if (during_initial_install()) {
            return false;
        }
        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = ['' => new lang_string('sitepolicyhandlercore', 'core_admin')];
        $manager = new \core_privacy\local\sitepolicy\manager();
        $plugins = $manager->get_all_handlers();
        foreach ($plugins as $pname => $unused) {
            $this->choices[$pname] = new lang_string('sitepolicyhandlerplugin', 'core_admin',
                ['name' => new lang_string('pluginname', $pname), 'component' => $pname]);
        }

        return true;
    }
}
