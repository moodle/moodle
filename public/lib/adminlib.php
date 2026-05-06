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
        $subplugins = \core\component::get_subplugins("{$type}_{$name}");

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

    // Perform clean-up task common for all the plugin/subplugin types.

    // Delete the web service functions and pre-built services.
    \core_external\util::delete_service_descriptions($component);

    // delete calendar events
    $DB->delete_records('event', array('modulename' => $pluginname));
    $DB->delete_records('event', ['component' => $component]);

    // Delete scheduled tasks.
    $DB->delete_records('task_adhoc', ['component' => $component]);
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

    // Wipe any xAPI state information.
    if (core_xapi\handler::supports_xapi($component)) {
        core_xapi\api::remove_states_from_component($component);
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
    if (file_exists($file)) {
        $DB->get_manager()->delete_tables_from_xmldb_file($file);
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
    global $CFG, $SITE;

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
        $data = bootstrap_renderer::plain_page(get_string('sitemaintenancetitle', 'admin',
            format_string($SITE->fullname, true, ['context' => context_system::instance()])), $data);
    }

    file_put_contents("$CFG->dataroot/climaintenance.html", $data);
    chmod("$CFG->dataroot/climaintenance.html", $CFG->filepermissions);
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
 *      nosearch - Do not display search bar
 */
function admin_externalpage_setup($section, $extrabutton = '', ?array $extraurlparams = null, $actualurl = '', array $options = array()) {
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

    $hassiteconfig = has_capability('moodle/site:config', context_system::instance());
    if (empty($extpage) or !($extpage instanceof admin_externalpage)) {
        // The requested section isn't in the admin tree
        // It could be because the user has inadequate capapbilities or because the section doesn't exist
        if (!$hassiteconfig) {
            // The requested section could depend on a different capability
            // but most likely the user has inadequate capabilities
            throw new \moodle_exception('accessdenied', 'admin');
        } else {
            throw new \moodle_exception('sectionerror', 'admin', "$CFG->wwwroot/$CFG->admin/");
        }
    }

    // this eliminates our need to authenticate on the actual pages
    if (!$extpage->check_access()) {
        throw new \moodle_exception('accessdenied', 'admin');
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

    if ($PAGE->user_allowed_editing() && !$PAGE->theme->haseditswitch) {
        if ($PAGE->user_is_editing()) {
            $caption = get_string('blockseditoff');
            $url = new moodle_url($PAGE->url, array('adminedit'=>'0', 'sesskey'=>sesskey()));
        } else {
            $caption = get_string('blocksediton');
            $url = new moodle_url($PAGE->url, array('adminedit'=>'1', 'sesskey'=>sesskey()));
        }
        $PAGE->set_button($OUTPUT->single_button($url, $caption, 'get'));
    }

    $PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $extpage->visiblepath));
    $PAGE->set_heading($SITE->fullname);

    if ($hassiteconfig && empty($options['nosearch'])) {
        $PAGE->add_header_action($OUTPUT->render_from_template('core_admin/header_search_input', [
            'action' => new moodle_url('/admin/search.php'),
            'query' => $PAGE->url->get_param('query'),
        ]));
    }

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
 * This function applies default settings recursively.
 *
 * Because setting the defaults of some settings can enable other settings,
 * this function calls itself repeatedly (max 4 times) until no more new settings are saved.
 *
 * NOTE: previous "internal" parameters $admindefaultsettings, $settingsoutput were removed in Moodle 4.3.
 *
 * @param part_of_admin_tree|null $node NULL means apply all settings with repeated recursion
 * @param bool $unconditional if true overrides all values with defaults (true for installation, false for CLI upgrade)
 * @return array The names and values of the applied setting defaults
 */
function admin_apply_default_settings(?part_of_admin_tree $node = null, bool $unconditional = true): array {
    if (is_null($node)) {
        // This function relies heavily on config cache, so we need to enable in-memory caches if it
        // is used during install when normal caching is disabled.
        $token = new \core_cache\allow_temporary_caches(); // Value not used intentionally, see its destructor.

        core_plugin_manager::reset_caches();
        $root = admin_get_root(true, true);
        $saved = admin_apply_default_settings($root, $unconditional);
        if (!$saved) {
            return [];
        }

        for ($i = 1; $i <= 3; $i++) {
            core_plugin_manager::reset_caches();
            $root = admin_get_root(true, true);
            // No need to force defaults in repeated runs.
            $moresaved = admin_apply_default_settings($root, false);
            if (!$moresaved) {
                // No more setting defaults to save.
                return $saved;
            }
            $saved += $moresaved;
        }

        // We should not get here unless there are some problematic settings.php files.
        core_plugin_manager::reset_caches();
        return $saved;
    }

    // Recursive applying of defaults in admin tree.
    $saved = [];
    if ($node instanceof admin_category) {
        foreach ($node->children as $child) {
            if ($child === null) {
                // This should not happen,
                // this is to prevent theoretical infinite loops.
                continue;
            }
            if ($child instanceof admin_externalpage) {
                continue;
            }
            $saved += admin_apply_default_settings($child, $unconditional);
        }

    } else if ($node instanceof admin_settingpage) {
        /** @var admin_setting $setting */
        foreach ((array)$node->settings as $setting) {
            if ($setting->nosave) {
                // Not a real setting, must be a heading or description.
                continue;
            }
            if (!$unconditional && !is_null($setting->get_setting())) {
                // Do not override existing defaults.
                continue;
            }
            $defaultsetting = $setting->get_defaultsetting();
            if (is_null($defaultsetting)) {
                // No value yet - default maybe applied after admin user creation or in upgradesettings.
                continue;
            }
            // This should be unique-enough setting name that matches administration UI.
            if ($setting->plugin === null) {
                $settingname = $setting->name;
            } else {
                $settingname = $setting->plugin . '/' . $setting->name;
            }
            // Set the default for this setting.
            $error = $setting->write_setting($defaultsetting);
            if ($error === '') {
                $setting->write_setting_flags(null);
                if (is_int($defaultsetting) || $defaultsetting instanceof lang_string
                    || $defaultsetting instanceof moodle_url) {
                    $defaultsetting = (string)$defaultsetting;
                }
                $saved[$settingname] = $defaultsetting;
            } else {
                debugging("Error applying default setting '$settingname': " . $error, DEBUG_DEVELOPER);
            }
        }
    }

    return $saved;
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

    $sortedresults = admin_search::sort_search_results($findings);

    foreach ($sortedresults as $result) {
        $page = $result->page;
        $settings = $result->settings;
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
 * @param ?string $defaultinfo defaults info, null means nothing, '' is converted to "Empty" string, defaults to null
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
        if ($setting->is_forceable() && array_key_exists($setting->name, $CFG->config_php_settings)) {
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
    $context->customcontrol = $setting->has_custom_form_control();

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
 * Given a table and optionally a column name should replaces be done?
 *
 * @param string $table name
 * @param string $column name
 * @return bool success or fail
 */
function db_should_replace($table, $column = '', $additionalskiptables = ''): bool {

    // TODO: this is horrible hack, we should have a hook and each plugin should be responsible for proper replacing...
    $skiptables = ['config', 'config_plugins', 'filter_config', 'sessions',
        'events_queue', 'repository_instance_config', 'block_instances', 'files'];

    // Additional skip tables.
    if (!empty($additionalskiptables)) {
        $skiptables = array_merge($skiptables, explode(',', str_replace(' ', '',  $additionalskiptables)));
    }

    // Don't process these.
    if (in_array($table, $skiptables)) {
        return false;
    }

    // To be safe never replace inside a table that looks related to logging.
    if (preg_match('/(^|_)logs?($|_)/', $table)) {
        return false;
    }

    // Do column based exclusions.
    if (!empty($column)) {
        // Don't touch anything that looks like a hash.
        if (preg_match('/hash$/', $column)) {
            return false;
        }
    }

    return true;
}

/**
 * Moved from admin/replace.php so that we can use this in cron
 *
 * @param string $search string to look for
 * @param string $replace string to replace
 * @return bool success or fail
 */
function db_replace($search, $replace, $additionalskiptables = '') {
    global $DB, $CFG, $OUTPUT;

    // Turn off time limits, sometimes upgrades can be slow.
    core_php_time_limit::raise();

    if (!$tables = $DB->get_tables() ) {    // No tables yet at all.
        return false;
    }
    foreach ($tables as $table) {

        if (!db_should_replace($table, '', $additionalskiptables)) {
            continue;
        }

        if ($columns = $DB->get_columns($table)) {
            $DB->set_debug(true);
            foreach ($columns as $column) {
                if (!db_should_replace($table, $column->name)) {
                    continue;
                }
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

    // Trigger an event.
    $eventargs = [
        'context' => context_system::instance(),
        'other' => [
            'search' => $search,
            'replace' => $replace
        ]
    ];
    $event = \core\event\database_text_field_content_replaced::create($eventargs);
    $event->trigger();

    purge_all_caches();

    return true;
}
