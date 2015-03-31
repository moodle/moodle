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
 * Various upgrade/install related functions and classes.
 *
 * @package    core
 * @subpackage upgrade
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** UPGRADE_LOG_NORMAL = 0 */
define('UPGRADE_LOG_NORMAL', 0);
/** UPGRADE_LOG_NOTICE = 1 */
define('UPGRADE_LOG_NOTICE', 1);
/** UPGRADE_LOG_ERROR = 2 */
define('UPGRADE_LOG_ERROR',  2);

/**
 * Exception indicating unknown error during upgrade.
 *
 * @package    core
 * @subpackage upgrade
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_exception extends moodle_exception {
    function __construct($plugin, $version, $debuginfo=NULL) {
        global $CFG;
        $a = (object)array('plugin'=>$plugin, 'version'=>$version);
        parent::__construct('upgradeerror', 'admin', "$CFG->wwwroot/$CFG->admin/index.php", $a, $debuginfo);
    }
}

/**
 * Exception indicating downgrade error during upgrade.
 *
 * @package    core
 * @subpackage upgrade
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class downgrade_exception extends moodle_exception {
    function __construct($plugin, $oldversion, $newversion) {
        global $CFG;
        $plugin = is_null($plugin) ? 'moodle' : $plugin;
        $a = (object)array('plugin'=>$plugin, 'oldversion'=>$oldversion, 'newversion'=>$newversion);
        parent::__construct('cannotdowngrade', 'debug', "$CFG->wwwroot/$CFG->admin/index.php", $a);
    }
}

/**
 * @package    core
 * @subpackage upgrade
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_requires_exception extends moodle_exception {
    function __construct($plugin, $pluginversion, $currentmoodle, $requiremoodle) {
        global $CFG;
        $a = new stdClass();
        $a->pluginname     = $plugin;
        $a->pluginversion  = $pluginversion;
        $a->currentmoodle  = $currentmoodle;
        $a->requiremoodle  = $requiremoodle;
        parent::__construct('pluginrequirementsnotmet', 'error', "$CFG->wwwroot/$CFG->admin/index.php", $a);
    }
}

/**
 * @package    core
 * @subpackage upgrade
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_defective_exception extends moodle_exception {
    function __construct($plugin, $details) {
        global $CFG;
        parent::__construct('detectedbrokenplugin', 'error', "$CFG->wwwroot/$CFG->admin/index.php", $plugin, $details);
    }
}

/**
 * Misplaced plugin exception.
 *
 * Note: this should be used only from the upgrade/admin code.
 *
 * @package    core
 * @subpackage upgrade
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_misplaced_exception extends moodle_exception {
    /**
     * Constructor.
     * @param string $component the component from version.php
     * @param string $expected expected directory, null means calculate
     * @param string $current plugin directory path
     */
    public function __construct($component, $expected, $current) {
        global $CFG;
        if (empty($expected)) {
            list($type, $plugin) = core_component::normalize_component($component);
            $plugintypes = core_component::get_plugin_types();
            if (isset($plugintypes[$type])) {
                $expected = $plugintypes[$type] . '/' . $plugin;
            }
        }
        if (strpos($expected, '$CFG->dirroot') !== 0) {
            $expected = str_replace($CFG->dirroot, '$CFG->dirroot', $expected);
        }
        if (strpos($current, '$CFG->dirroot') !== 0) {
            $current = str_replace($CFG->dirroot, '$CFG->dirroot', $current);
        }
        $a = new stdClass();
        $a->component = $component;
        $a->expected  = $expected;
        $a->current   = $current;
        parent::__construct('detectedmisplacedplugin', 'core_plugin', "$CFG->wwwroot/$CFG->admin/index.php", $a);
    }
}

/**
 * Sets maximum expected time needed for upgrade task.
 * Please always make sure that upgrade will not run longer!
 *
 * The script may be automatically aborted if upgrade times out.
 *
 * @category upgrade
 * @param int $max_execution_time in seconds (can not be less than 60 s)
 */
function upgrade_set_timeout($max_execution_time=300) {
    global $CFG;

    if (!isset($CFG->upgraderunning) or $CFG->upgraderunning < time()) {
        $upgraderunning = get_config(null, 'upgraderunning');
    } else {
        $upgraderunning = $CFG->upgraderunning;
    }

    if (!$upgraderunning) {
        if (CLI_SCRIPT) {
            // never stop CLI upgrades
            $upgraderunning = 0;
        } else {
            // web upgrade not running or aborted
            print_error('upgradetimedout', 'admin', "$CFG->wwwroot/$CFG->admin/");
        }
    }

    if ($max_execution_time < 60) {
        // protection against 0 here
        $max_execution_time = 60;
    }

    $expected_end = time() + $max_execution_time;

    if ($expected_end < $upgraderunning + 10 and $expected_end > $upgraderunning - 10) {
        // no need to store new end, it is nearly the same ;-)
        return;
    }

    if (CLI_SCRIPT) {
        // there is no point in timing out of CLI scripts, admins can stop them if necessary
        core_php_time_limit::raise();
    } else {
        core_php_time_limit::raise($max_execution_time);
    }
    set_config('upgraderunning', $expected_end); // keep upgrade locked until this time
}

/**
 * Upgrade savepoint, marks end of each upgrade block.
 * It stores new main version, resets upgrade timeout
 * and abort upgrade if user cancels page loading.
 *
 * Please do not make large upgrade blocks with lots of operations,
 * for example when adding tables keep only one table operation per block.
 *
 * @category upgrade
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_main_savepoint($result, $version, $allowabort=true) {
    global $CFG;

    //sanity check to avoid confusion with upgrade_mod_savepoint usage.
    if (!is_bool($allowabort)) {
        $errormessage = 'Parameter type mismatch. Are you mixing up upgrade_main_savepoint() and upgrade_mod_savepoint()?';
        throw new coding_exception($errormessage);
    }

    if (!$result) {
        throw new upgrade_exception(null, $version);
    }

    if ($CFG->version >= $version) {
        // something really wrong is going on in main upgrade script
        throw new downgrade_exception(null, $CFG->version, $version);
    }

    set_config('version', $version);
    upgrade_log(UPGRADE_LOG_NORMAL, null, 'Upgrade savepoint reached');

    // reset upgrade timeout to default
    upgrade_set_timeout();

    // this is a safe place to stop upgrades if user aborts page loading
    if ($allowabort and connection_aborted()) {
        die;
    }
}

/**
 * Module upgrade savepoint, marks end of module upgrade blocks
 * It stores module version, resets upgrade timeout
 * and abort upgrade if user cancels page loading.
 *
 * @category upgrade
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $modname name of module
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_mod_savepoint($result, $version, $modname, $allowabort=true) {
    global $DB;

    $component = 'mod_'.$modname;

    if (!$result) {
        throw new upgrade_exception($component, $version);
    }

    $dbversion = $DB->get_field('config_plugins', 'value', array('plugin'=>$component, 'name'=>'version'));

    if (!$module = $DB->get_record('modules', array('name'=>$modname))) {
        print_error('modulenotexist', 'debug', '', $modname);
    }

    if ($dbversion >= $version) {
        // something really wrong is going on in upgrade script
        throw new downgrade_exception($component, $dbversion, $version);
    }
    set_config('version', $version, $component);

    upgrade_log(UPGRADE_LOG_NORMAL, $component, 'Upgrade savepoint reached');

    // reset upgrade timeout to default
    upgrade_set_timeout();

    // this is a safe place to stop upgrades if user aborts page loading
    if ($allowabort and connection_aborted()) {
        die;
    }
}

/**
 * Blocks upgrade savepoint, marks end of blocks upgrade blocks
 * It stores block version, resets upgrade timeout
 * and abort upgrade if user cancels page loading.
 *
 * @category upgrade
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $blockname name of block
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_block_savepoint($result, $version, $blockname, $allowabort=true) {
    global $DB;

    $component = 'block_'.$blockname;

    if (!$result) {
        throw new upgrade_exception($component, $version);
    }

    $dbversion = $DB->get_field('config_plugins', 'value', array('plugin'=>$component, 'name'=>'version'));

    if (!$block = $DB->get_record('block', array('name'=>$blockname))) {
        print_error('blocknotexist', 'debug', '', $blockname);
    }

    if ($dbversion >= $version) {
        // something really wrong is going on in upgrade script
        throw new downgrade_exception($component, $dbversion, $version);
    }
    set_config('version', $version, $component);

    upgrade_log(UPGRADE_LOG_NORMAL, $component, 'Upgrade savepoint reached');

    // reset upgrade timeout to default
    upgrade_set_timeout();

    // this is a safe place to stop upgrades if user aborts page loading
    if ($allowabort and connection_aborted()) {
        die;
    }
}

/**
 * Plugins upgrade savepoint, marks end of blocks upgrade blocks
 * It stores plugin version, resets upgrade timeout
 * and abort upgrade if user cancels page loading.
 *
 * @category upgrade
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $type name of plugin
 * @param string $dir location of plugin
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_plugin_savepoint($result, $version, $type, $plugin, $allowabort=true) {
    global $DB;

    $component = $type.'_'.$plugin;

    if (!$result) {
        throw new upgrade_exception($component, $version);
    }

    $dbversion = $DB->get_field('config_plugins', 'value', array('plugin'=>$component, 'name'=>'version'));

    if ($dbversion >= $version) {
        // Something really wrong is going on in the upgrade script
        throw new downgrade_exception($component, $dbversion, $version);
    }
    set_config('version', $version, $component);
    upgrade_log(UPGRADE_LOG_NORMAL, $component, 'Upgrade savepoint reached');

    // Reset upgrade timeout to default
    upgrade_set_timeout();

    // This is a safe place to stop upgrades if user aborts page loading
    if ($allowabort and connection_aborted()) {
        die;
    }
}

/**
 * Detect if there are leftovers in PHP source files.
 *
 * During main version upgrades administrators MUST move away
 * old PHP source files and start from scratch (or better
 * use git).
 *
 * @return bool true means borked upgrade, false means previous PHP files were properly removed
 */
function upgrade_stale_php_files_present() {
    global $CFG;

    $someexamplesofremovedfiles = array(
        // Removed in 2.7.
        '/admin/tool/qeupgradehelper/version.php',
        // Removed in 2.6.
        '/admin/block.php',
        '/admin/oacleanup.php',
        // Removed in 2.5.
        '/backup/lib.php',
        '/backup/bb/README.txt',
        '/lib/excel/test.php',
        // Removed in 2.4.
        '/admin/tool/unittest/simpletestlib.php',
        // Removed in 2.3.
        '/lib/minify/builder/',
        // Removed in 2.2.
        '/lib/yui/3.4.1pr1/',
        // Removed in 2.2.
        '/search/cron_php5.php',
        '/course/report/log/indexlive.php',
        '/admin/report/backups/index.php',
        '/admin/generator.php',
        // Removed in 2.1.
        '/lib/yui/2.8.0r4/',
        // Removed in 2.0.
        '/blocks/admin/block_admin.php',
        '/blocks/admin_tree/block_admin_tree.php',
    );

    foreach ($someexamplesofremovedfiles as $file) {
        if (file_exists($CFG->dirroot.$file)) {
            return true;
        }
    }

    return false;
}

/**
 * Upgrade plugins
 * @param string $type The type of plugins that should be updated (e.g. 'enrol', 'qtype')
 * return void
 */
function upgrade_plugins($type, $startcallback, $endcallback, $verbose) {
    global $CFG, $DB;

/// special cases
    if ($type === 'mod') {
        return upgrade_plugins_modules($startcallback, $endcallback, $verbose);
    } else if ($type === 'block') {
        return upgrade_plugins_blocks($startcallback, $endcallback, $verbose);
    }

    $plugs = core_component::get_plugin_list($type);

    foreach ($plugs as $plug=>$fullplug) {
        // Reset time so that it works when installing a large number of plugins
        core_php_time_limit::raise(600);
        $component = clean_param($type.'_'.$plug, PARAM_COMPONENT); // standardised plugin name

        // check plugin dir is valid name
        if (empty($component)) {
            throw new plugin_defective_exception($type.'_'.$plug, 'Invalid plugin directory name.');
        }

        if (!is_readable($fullplug.'/version.php')) {
            continue;
        }

        $plugin = new stdClass();
        $plugin->version = null;
        $module = $plugin; // Prevent some notices when plugin placed in wrong directory.
        require($fullplug.'/version.php');  // defines $plugin with version etc
        unset($module);

        // if plugin tells us it's full name we may check the location
        if (isset($plugin->component)) {
            if ($plugin->component !== $component) {
                throw new plugin_misplaced_exception($plugin->component, null, $fullplug);
            }
        }

        if (empty($plugin->version)) {
            throw new plugin_defective_exception($component, 'Missing version value in version.php');
        }

        $plugin->name     = $plug;
        $plugin->fullname = $component;

        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                throw new upgrade_requires_exception($component, $plugin->version, $CFG->version, $plugin->requires);
            } else if ($plugin->requires < 2010000000) {
                throw new plugin_defective_exception($component, 'Plugin is not compatible with Moodle 2.x or later.');
            }
        }

        // try to recover from interrupted install.php if needed
        if (file_exists($fullplug.'/db/install.php')) {
            if (get_config($plugin->fullname, 'installrunning')) {
                require_once($fullplug.'/db/install.php');
                $recover_install_function = 'xmldb_'.$plugin->fullname.'_install_recovery';
                if (function_exists($recover_install_function)) {
                    $startcallback($component, true, $verbose);
                    $recover_install_function();
                    unset_config('installrunning', $plugin->fullname);
                    update_capabilities($component);
                    log_update_descriptions($component);
                    external_update_descriptions($component);
                    events_update_definition($component);
                    \core\task\manager::reset_scheduled_tasks_for_component($component);
                    message_update_providers($component);
                    \core\message\inbound\manager::update_handlers_for_component($component);
                    if ($type === 'message') {
                        message_update_processors($plug);
                    }
                    upgrade_plugin_mnet_functions($component);
                    $endcallback($component, true, $verbose);
                }
            }
        }

        $installedversion = $DB->get_field('config_plugins', 'value', array('name'=>'version', 'plugin'=>$component)); // No caching!
        if (empty($installedversion)) { // new installation
            $startcallback($component, true, $verbose);

        /// Install tables if defined
            if (file_exists($fullplug.'/db/install.xml')) {
                $DB->get_manager()->install_from_xmldb_file($fullplug.'/db/install.xml');
            }

        /// store version
            upgrade_plugin_savepoint(true, $plugin->version, $type, $plug, false);

        /// execute post install file
            if (file_exists($fullplug.'/db/install.php')) {
                require_once($fullplug.'/db/install.php');
                set_config('installrunning', 1, $plugin->fullname);
                $post_install_function = 'xmldb_'.$plugin->fullname.'_install';
                $post_install_function();
                unset_config('installrunning', $plugin->fullname);
            }

        /// Install various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            \core\task\manager::reset_scheduled_tasks_for_component($component);
            message_update_providers($component);
            \core\message\inbound\manager::update_handlers_for_component($component);
            if ($type === 'message') {
                message_update_processors($plug);
            }
            upgrade_plugin_mnet_functions($component);
            $endcallback($component, true, $verbose);

        } else if ($installedversion < $plugin->version) { // upgrade
        /// Run the upgrade function for the plugin.
            $startcallback($component, false, $verbose);

            if (is_readable($fullplug.'/db/upgrade.php')) {
                require_once($fullplug.'/db/upgrade.php');  // defines upgrading function

                $newupgrade_function = 'xmldb_'.$plugin->fullname.'_upgrade';
                $result = $newupgrade_function($installedversion);
            } else {
                $result = true;
            }

            $installedversion = $DB->get_field('config_plugins', 'value', array('name'=>'version', 'plugin'=>$component)); // No caching!
            if ($installedversion < $plugin->version) {
                // store version if not already there
                upgrade_plugin_savepoint($result, $plugin->version, $type, $plug, false);
            }

        /// Upgrade various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            \core\task\manager::reset_scheduled_tasks_for_component($component);
            message_update_providers($component);
            \core\message\inbound\manager::update_handlers_for_component($component);
            if ($type === 'message') {
                // Ugly hack!
                message_update_processors($plug);
            }
            upgrade_plugin_mnet_functions($component);
            $endcallback($component, false, $verbose);

        } else if ($installedversion > $plugin->version) {
            throw new downgrade_exception($component, $installedversion, $plugin->version);
        }
    }
}

/**
 * Find and check all modules and load them up or upgrade them if necessary
 *
 * @global object
 * @global object
 */
function upgrade_plugins_modules($startcallback, $endcallback, $verbose) {
    global $CFG, $DB;

    $mods = core_component::get_plugin_list('mod');

    foreach ($mods as $mod=>$fullmod) {

        if ($mod === 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $component = clean_param('mod_'.$mod, PARAM_COMPONENT);

        // check module dir is valid name
        if (empty($component)) {
            throw new plugin_defective_exception('mod_'.$mod, 'Invalid plugin directory name.');
        }

        if (!is_readable($fullmod.'/version.php')) {
            throw new plugin_defective_exception($component, 'Missing version.php');
        }

        // TODO: Support for $module will end with Moodle 2.10 by MDL-43896. Was deprecated for Moodle 2.7 by MDL-43040.
        $plugin = new stdClass();
        $plugin->version = null;
        $module = $plugin;
        require($fullmod .'/version.php');  // Defines $plugin with version etc.
        $plugin = clone($module);
        unset($module->version);
        unset($module->component);
        unset($module->dependencies);
        unset($module->release);

        // if plugin tells us it's full name we may check the location
        if (isset($plugin->component)) {
            if ($plugin->component !== $component) {
                throw new plugin_misplaced_exception($plugin->component, null, $fullmod);
            }
        }

        if (empty($plugin->version)) {
            // Version must be always set now!
            throw new plugin_defective_exception($component, 'Missing version value in version.php');
        }

        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                throw new upgrade_requires_exception($component, $plugin->version, $CFG->version, $plugin->requires);
            } else if ($plugin->requires < 2010000000) {
                throw new plugin_defective_exception($component, 'Plugin is not compatible with Moodle 2.x or later.');
            }
        }

        if (empty($module->cron)) {
            $module->cron = 0;
        }

        // all modules must have en lang pack
        if (!is_readable("$fullmod/lang/en/$mod.php")) {
            throw new plugin_defective_exception($component, 'Missing mandatory en language pack.');
        }

        $module->name = $mod;   // The name MUST match the directory

        $installedversion = $DB->get_field('config_plugins', 'value', array('name'=>'version', 'plugin'=>$component)); // No caching!

        if (file_exists($fullmod.'/db/install.php')) {
            if (get_config($module->name, 'installrunning')) {
                require_once($fullmod.'/db/install.php');
                $recover_install_function = 'xmldb_'.$module->name.'_install_recovery';
                if (function_exists($recover_install_function)) {
                    $startcallback($component, true, $verbose);
                    $recover_install_function();
                    unset_config('installrunning', $module->name);
                    // Install various components too
                    update_capabilities($component);
                    log_update_descriptions($component);
                    external_update_descriptions($component);
                    events_update_definition($component);
                    \core\task\manager::reset_scheduled_tasks_for_component($component);
                    message_update_providers($component);
                    \core\message\inbound\manager::update_handlers_for_component($component);
                    upgrade_plugin_mnet_functions($component);
                    $endcallback($component, true, $verbose);
                }
            }
        }

        if (empty($installedversion)) {
            $startcallback($component, true, $verbose);

        /// Execute install.xml (XMLDB) - must be present in all modules
            $DB->get_manager()->install_from_xmldb_file($fullmod.'/db/install.xml');

        /// Add record into modules table - may be needed in install.php already
            $module->id = $DB->insert_record('modules', $module);
            upgrade_mod_savepoint(true, $plugin->version, $module->name, false);

        /// Post installation hook - optional
            if (file_exists("$fullmod/db/install.php")) {
                require_once("$fullmod/db/install.php");
                // Set installation running flag, we need to recover after exception or error
                set_config('installrunning', 1, $module->name);
                $post_install_function = 'xmldb_'.$module->name.'_install';
                $post_install_function();
                unset_config('installrunning', $module->name);
            }

        /// Install various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            \core\task\manager::reset_scheduled_tasks_for_component($component);
            message_update_providers($component);
            \core\message\inbound\manager::update_handlers_for_component($component);
            upgrade_plugin_mnet_functions($component);

            $endcallback($component, true, $verbose);

        } else if ($installedversion < $plugin->version) {
        /// If versions say that we need to upgrade but no upgrade files are available, notify and continue
            $startcallback($component, false, $verbose);

            if (is_readable($fullmod.'/db/upgrade.php')) {
                require_once($fullmod.'/db/upgrade.php');  // defines new upgrading function
                $newupgrade_function = 'xmldb_'.$module->name.'_upgrade';
                $result = $newupgrade_function($installedversion, $module);
            } else {
                $result = true;
            }

            $installedversion = $DB->get_field('config_plugins', 'value', array('name'=>'version', 'plugin'=>$component)); // No caching!
            $currmodule = $DB->get_record('modules', array('name'=>$module->name));
            if ($installedversion < $plugin->version) {
                // store version if not already there
                upgrade_mod_savepoint($result, $plugin->version, $mod, false);
            }

            // update cron flag if needed
            if ($currmodule->cron != $module->cron) {
                $DB->set_field('modules', 'cron', $module->cron, array('name' => $module->name));
            }

            // Upgrade various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            \core\task\manager::reset_scheduled_tasks_for_component($component);
            message_update_providers($component);
            \core\message\inbound\manager::update_handlers_for_component($component);
            upgrade_plugin_mnet_functions($component);

            $endcallback($component, false, $verbose);

        } else if ($installedversion > $plugin->version) {
            throw new downgrade_exception($component, $installedversion, $plugin->version);
        }
    }
}


/**
 * This function finds all available blocks and install them
 * into blocks table or do all the upgrade process if newer.
 *
 * @global object
 * @global object
 */
function upgrade_plugins_blocks($startcallback, $endcallback, $verbose) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/blocks/moodleblock.class.php');

    $blocktitles   = array(); // we do not want duplicate titles

    //Is this a first install
    $first_install = null;

    $blocks = core_component::get_plugin_list('block');

    foreach ($blocks as $blockname=>$fullblock) {

        if (is_null($first_install)) {
            $first_install = ($DB->count_records('block_instances') == 0);
        }

        if ($blockname === 'NEWBLOCK') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $component = clean_param('block_'.$blockname, PARAM_COMPONENT);

        // check block dir is valid name
        if (empty($component)) {
            throw new plugin_defective_exception('block_'.$blockname, 'Invalid plugin directory name.');
        }

        if (!is_readable($fullblock.'/version.php')) {
            throw new plugin_defective_exception('block/'.$blockname, 'Missing version.php file.');
        }
        $plugin = new stdClass();
        $plugin->version = null;
        $plugin->cron    = 0;
        $module = $plugin; // Prevent some notices when module placed in wrong directory.
        include($fullblock.'/version.php');
        unset($module);
        $block = clone($plugin);
        unset($block->version);
        unset($block->component);
        unset($block->dependencies);
        unset($block->release);

        // if plugin tells us it's full name we may check the location
        if (isset($plugin->component)) {
            if ($plugin->component !== $component) {
                throw new plugin_misplaced_exception($plugin->component, null, $fullblock);
            }
        }

        if (empty($plugin->version)) {
            throw new plugin_defective_exception($component, 'Missing block version.');
        }

        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                throw new upgrade_requires_exception($component, $plugin->version, $CFG->version, $plugin->requires);
            } else if ($plugin->requires < 2010000000) {
                throw new plugin_defective_exception($component, 'Plugin is not compatible with Moodle 2.x or later.');
            }
        }

        if (!is_readable($fullblock.'/block_'.$blockname.'.php')) {
            throw new plugin_defective_exception('block/'.$blockname, 'Missing main block class file.');
        }
        include_once($fullblock.'/block_'.$blockname.'.php');

        $classname = 'block_'.$blockname;

        if (!class_exists($classname)) {
            throw new plugin_defective_exception($component, 'Can not load main class.');
        }

        $blockobj    = new $classname;   // This is what we'll be testing
        $blocktitle  = $blockobj->get_title();

        // OK, it's as we all hoped. For further tests, the object will do them itself.
        if (!$blockobj->_self_test()) {
            throw new plugin_defective_exception($component, 'Self test failed.');
        }

        $block->name     = $blockname;   // The name MUST match the directory

        $installedversion = $DB->get_field('config_plugins', 'value', array('name'=>'version', 'plugin'=>$component)); // No caching!

        if (file_exists($fullblock.'/db/install.php')) {
            if (get_config('block_'.$blockname, 'installrunning')) {
                require_once($fullblock.'/db/install.php');
                $recover_install_function = 'xmldb_block_'.$blockname.'_install_recovery';
                if (function_exists($recover_install_function)) {
                    $startcallback($component, true, $verbose);
                    $recover_install_function();
                    unset_config('installrunning', 'block_'.$blockname);
                    // Install various components
                    update_capabilities($component);
                    log_update_descriptions($component);
                    external_update_descriptions($component);
                    events_update_definition($component);
                    \core\task\manager::reset_scheduled_tasks_for_component($component);
                    message_update_providers($component);
                    \core\message\inbound\manager::update_handlers_for_component($component);
                    upgrade_plugin_mnet_functions($component);
                    $endcallback($component, true, $verbose);
                }
            }
        }

        if (empty($installedversion)) { // block not installed yet, so install it
            $conflictblock = array_search($blocktitle, $blocktitles);
            if ($conflictblock !== false) {
                // Duplicate block titles are not allowed, they confuse people
                // AND PHP's associative arrays ;)
                throw new plugin_defective_exception($component, get_string('blocknameconflict', 'error', (object)array('name'=>$block->name, 'conflict'=>$conflictblock)));
            }
            $startcallback($component, true, $verbose);

            if (file_exists($fullblock.'/db/install.xml')) {
                $DB->get_manager()->install_from_xmldb_file($fullblock.'/db/install.xml');
            }
            $block->id = $DB->insert_record('block', $block);
            upgrade_block_savepoint(true, $plugin->version, $block->name, false);

            if (file_exists($fullblock.'/db/install.php')) {
                require_once($fullblock.'/db/install.php');
                // Set installation running flag, we need to recover after exception or error
                set_config('installrunning', 1, 'block_'.$blockname);
                $post_install_function = 'xmldb_block_'.$blockname.'_install';
                $post_install_function();
                unset_config('installrunning', 'block_'.$blockname);
            }

            $blocktitles[$block->name] = $blocktitle;

            // Install various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            \core\task\manager::reset_scheduled_tasks_for_component($component);
            message_update_providers($component);
            \core\message\inbound\manager::update_handlers_for_component($component);
            upgrade_plugin_mnet_functions($component);

            $endcallback($component, true, $verbose);

        } else if ($installedversion < $plugin->version) {
            $startcallback($component, false, $verbose);

            if (is_readable($fullblock.'/db/upgrade.php')) {
                require_once($fullblock.'/db/upgrade.php');  // defines new upgrading function
                $newupgrade_function = 'xmldb_block_'.$blockname.'_upgrade';
                $result = $newupgrade_function($installedversion, $block);
            } else {
                $result = true;
            }

            $installedversion = $DB->get_field('config_plugins', 'value', array('name'=>'version', 'plugin'=>$component)); // No caching!
            $currblock = $DB->get_record('block', array('name'=>$block->name));
            if ($installedversion < $plugin->version) {
                // store version if not already there
                upgrade_block_savepoint($result, $plugin->version, $block->name, false);
            }

            if ($currblock->cron != $block->cron) {
                // update cron flag if needed
                $DB->set_field('block', 'cron', $block->cron, array('id' => $currblock->id));
            }

            // Upgrade various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            \core\task\manager::reset_scheduled_tasks_for_component($component);
            message_update_providers($component);
            \core\message\inbound\manager::update_handlers_for_component($component);
            upgrade_plugin_mnet_functions($component);

            $endcallback($component, false, $verbose);

        } else if ($installedversion > $plugin->version) {
            throw new downgrade_exception($component, $installedversion, $plugin->version);
        }
    }


    // Finally, if we are in the first_install of BLOCKS setup frontpage and admin page blocks
    if ($first_install) {
        //Iterate over each course - there should be only site course here now
        if ($courses = $DB->get_records('course')) {
            foreach ($courses as $course) {
                blocks_add_default_course_blocks($course);
            }
        }

        blocks_add_default_system_blocks();
    }
}


/**
 * Log_display description function used during install and upgrade.
 *
 * @param string $component name of component (moodle, mod_assignment, etc.)
 * @return void
 */
function log_update_descriptions($component) {
    global $DB;

    $defpath = core_component::get_component_directory($component).'/db/log.php';

    if (!file_exists($defpath)) {
        $DB->delete_records('log_display', array('component'=>$component));
        return;
    }

    // load new info
    $logs = array();
    include($defpath);
    $newlogs = array();
    foreach ($logs as $log) {
        $newlogs[$log['module'].'-'.$log['action']] = $log; // kind of unique name
    }
    unset($logs);
    $logs = $newlogs;

    $fields = array('module', 'action', 'mtable', 'field');
    // update all log fist
    $dblogs = $DB->get_records('log_display', array('component'=>$component));
    foreach ($dblogs as $dblog) {
        $name = $dblog->module.'-'.$dblog->action;

        if (empty($logs[$name])) {
            $DB->delete_records('log_display', array('id'=>$dblog->id));
            continue;
        }

        $log = $logs[$name];
        unset($logs[$name]);

        $update = false;
        foreach ($fields as $field) {
            if ($dblog->$field != $log[$field]) {
                $dblog->$field = $log[$field];
                $update = true;
            }
        }
        if ($update) {
            $DB->update_record('log_display', $dblog);
        }
    }
    foreach ($logs as $log) {
        $dblog = (object)$log;
        $dblog->component = $component;
        $DB->insert_record('log_display', $dblog);
    }
}

/**
 * Web service discovery function used during install and upgrade.
 * @param string $component name of component (moodle, mod_assignment, etc.)
 * @return void
 */
function external_update_descriptions($component) {
    global $DB, $CFG;

    $defpath = core_component::get_component_directory($component).'/db/services.php';

    if (!file_exists($defpath)) {
        require_once($CFG->dirroot.'/lib/externallib.php');
        external_delete_descriptions($component);
        return;
    }

    // load new info
    $functions = array();
    $services = array();
    include($defpath);

    // update all function fist
    $dbfunctions = $DB->get_records('external_functions', array('component'=>$component));
    foreach ($dbfunctions as $dbfunction) {
        if (empty($functions[$dbfunction->name])) {
            $DB->delete_records('external_functions', array('id'=>$dbfunction->id));
            // do not delete functions from external_services_functions, beacuse
            // we want to notify admins when functions used in custom services disappear

            //TODO: this looks wrong, we have to delete it eventually (skodak)
            continue;
        }

        $function = $functions[$dbfunction->name];
        unset($functions[$dbfunction->name]);
        $function['classpath'] = empty($function['classpath']) ? null : $function['classpath'];

        $update = false;
        if ($dbfunction->classname != $function['classname']) {
            $dbfunction->classname = $function['classname'];
            $update = true;
        }
        if ($dbfunction->methodname != $function['methodname']) {
            $dbfunction->methodname = $function['methodname'];
            $update = true;
        }
        if ($dbfunction->classpath != $function['classpath']) {
            $dbfunction->classpath = $function['classpath'];
            $update = true;
        }
        $functioncapabilities = array_key_exists('capabilities', $function)?$function['capabilities']:'';
        if ($dbfunction->capabilities != $functioncapabilities) {
            $dbfunction->capabilities = $functioncapabilities;
            $update = true;
        }
        if ($update) {
            $DB->update_record('external_functions', $dbfunction);
        }
    }
    foreach ($functions as $fname => $function) {
        $dbfunction = new stdClass();
        $dbfunction->name       = $fname;
        $dbfunction->classname  = $function['classname'];
        $dbfunction->methodname = $function['methodname'];
        $dbfunction->classpath  = empty($function['classpath']) ? null : $function['classpath'];
        $dbfunction->component  = $component;
        $dbfunction->capabilities = array_key_exists('capabilities', $function)?$function['capabilities']:'';
        $dbfunction->id = $DB->insert_record('external_functions', $dbfunction);
    }
    unset($functions);

    // now deal with services
    $dbservices = $DB->get_records('external_services', array('component'=>$component));
    foreach ($dbservices as $dbservice) {
        if (empty($services[$dbservice->name])) {
            $DB->delete_records('external_tokens', array('externalserviceid'=>$dbservice->id));
            $DB->delete_records('external_services_functions', array('externalserviceid'=>$dbservice->id));
            $DB->delete_records('external_services_users', array('externalserviceid'=>$dbservice->id));
            $DB->delete_records('external_services', array('id'=>$dbservice->id));
            continue;
        }
        $service = $services[$dbservice->name];
        unset($services[$dbservice->name]);
        $service['enabled'] = empty($service['enabled']) ? 0 : $service['enabled'];
        $service['requiredcapability'] = empty($service['requiredcapability']) ? null : $service['requiredcapability'];
        $service['restrictedusers'] = !isset($service['restrictedusers']) ? 1 : $service['restrictedusers'];
        $service['downloadfiles'] = !isset($service['downloadfiles']) ? 0 : $service['downloadfiles'];
        $service['uploadfiles'] = !isset($service['uploadfiles']) ? 0 : $service['uploadfiles'];
        $service['shortname'] = !isset($service['shortname']) ? null : $service['shortname'];

        $update = false;
        if ($dbservice->requiredcapability != $service['requiredcapability']) {
            $dbservice->requiredcapability = $service['requiredcapability'];
            $update = true;
        }
        if ($dbservice->restrictedusers != $service['restrictedusers']) {
            $dbservice->restrictedusers = $service['restrictedusers'];
            $update = true;
        }
        if ($dbservice->downloadfiles != $service['downloadfiles']) {
            $dbservice->downloadfiles = $service['downloadfiles'];
            $update = true;
        }
        if ($dbservice->uploadfiles != $service['uploadfiles']) {
            $dbservice->uploadfiles = $service['uploadfiles'];
            $update = true;
        }
        //if shortname is not a PARAM_ALPHANUMEXT, fail (tested here for service update and creation)
        if (isset($service['shortname']) and
                (clean_param($service['shortname'], PARAM_ALPHANUMEXT) != $service['shortname'])) {
            throw new moodle_exception('installserviceshortnameerror', 'webservice', '', $service['shortname']);
        }
        if ($dbservice->shortname != $service['shortname']) {
            //check that shortname is unique
            if (isset($service['shortname'])) { //we currently accepts multiple shortname == null
                $existingservice = $DB->get_record('external_services',
                        array('shortname' => $service['shortname']));
                if (!empty($existingservice)) {
                    throw new moodle_exception('installexistingserviceshortnameerror', 'webservice', '', $service['shortname']);
                }
            }
            $dbservice->shortname = $service['shortname'];
            $update = true;
        }
        if ($update) {
            $DB->update_record('external_services', $dbservice);
        }

        $functions = $DB->get_records('external_services_functions', array('externalserviceid'=>$dbservice->id));
        foreach ($functions as $function) {
            $key = array_search($function->functionname, $service['functions']);
            if ($key === false) {
                $DB->delete_records('external_services_functions', array('id'=>$function->id));
            } else {
                unset($service['functions'][$key]);
            }
        }
        foreach ($service['functions'] as $fname) {
            $newf = new stdClass();
            $newf->externalserviceid = $dbservice->id;
            $newf->functionname      = $fname;
            $DB->insert_record('external_services_functions', $newf);
        }
        unset($functions);
    }
    foreach ($services as $name => $service) {
        //check that shortname is unique
        if (isset($service['shortname'])) { //we currently accepts multiple shortname == null
            $existingservice = $DB->get_record('external_services',
                    array('shortname' => $service['shortname']));
            if (!empty($existingservice)) {
                throw new moodle_exception('installserviceshortnameerror', 'webservice');
            }
        }

        $dbservice = new stdClass();
        $dbservice->name               = $name;
        $dbservice->enabled            = empty($service['enabled']) ? 0 : $service['enabled'];
        $dbservice->requiredcapability = empty($service['requiredcapability']) ? null : $service['requiredcapability'];
        $dbservice->restrictedusers    = !isset($service['restrictedusers']) ? 1 : $service['restrictedusers'];
        $dbservice->downloadfiles      = !isset($service['downloadfiles']) ? 0 : $service['downloadfiles'];
        $dbservice->uploadfiles        = !isset($service['uploadfiles']) ? 0 : $service['uploadfiles'];
        $dbservice->shortname          = !isset($service['shortname']) ? null : $service['shortname'];
        $dbservice->component          = $component;
        $dbservice->timecreated        = time();
        $dbservice->id = $DB->insert_record('external_services', $dbservice);
        foreach ($service['functions'] as $fname) {
            $newf = new stdClass();
            $newf->externalserviceid = $dbservice->id;
            $newf->functionname      = $fname;
            $DB->insert_record('external_services_functions', $newf);
        }
    }
}

/**
 * upgrade logging functions
 */
function upgrade_handle_exception($ex, $plugin = null) {
    global $CFG;

    // rollback everything, we need to log all upgrade problems
    abort_all_db_transactions();

    $info = get_exception_info($ex);

    // First log upgrade error
    upgrade_log(UPGRADE_LOG_ERROR, $plugin, 'Exception: ' . get_class($ex), $info->message, $info->backtrace);

    // Always turn on debugging - admins need to know what is going on
    set_debugging(DEBUG_DEVELOPER, true);

    default_exception_handler($ex, true, $plugin);
}

/**
 * Adds log entry into upgrade_log table
 *
 * @param int $type UPGRADE_LOG_NORMAL, UPGRADE_LOG_NOTICE or UPGRADE_LOG_ERROR
 * @param string $plugin frankenstyle component name
 * @param string $info short description text of log entry
 * @param string $details long problem description
 * @param string $backtrace string used for errors only
 * @return void
 */
function upgrade_log($type, $plugin, $info, $details=null, $backtrace=null) {
    global $DB, $USER, $CFG;

    if (empty($plugin)) {
        $plugin = 'core';
    }

    list($plugintype, $pluginname) = core_component::normalize_component($plugin);
    $component = is_null($pluginname) ? $plugintype : $plugintype . '_' . $pluginname;

    $backtrace = format_backtrace($backtrace, true);

    $currentversion = null;
    $targetversion  = null;

    //first try to find out current version number
    if ($plugintype === 'core') {
        //main
        $currentversion = $CFG->version;

        $version = null;
        include("$CFG->dirroot/version.php");
        $targetversion = $version;

    } else {
        $pluginversion = get_config($component, 'version');
        if (!empty($pluginversion)) {
            $currentversion = $pluginversion;
        }
        $cd = core_component::get_component_directory($component);
        if (file_exists("$cd/version.php")) {
            $plugin = new stdClass();
            $plugin->version = null;
            $module = $plugin;
            include("$cd/version.php");
            $targetversion = $plugin->version;
        }
    }

    $log = new stdClass();
    $log->type          = $type;
    $log->plugin        = $component;
    $log->version       = $currentversion;
    $log->targetversion = $targetversion;
    $log->info          = $info;
    $log->details       = $details;
    $log->backtrace     = $backtrace;
    $log->userid        = $USER->id;
    $log->timemodified  = time();
    try {
        $DB->insert_record('upgrade_log', $log);
    } catch (Exception $ignored) {
        // possible during install or 2.0 upgrade
    }
}

/**
 * Marks start of upgrade, blocks any other access to site.
 * The upgrade is finished at the end of script or after timeout.
 *
 * @global object
 * @global object
 * @global object
 */
function upgrade_started($preinstall=false) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    static $started = false;

    if ($preinstall) {
        ignore_user_abort(true);
        upgrade_setup_debug(true);

    } else if ($started) {
        upgrade_set_timeout(120);

    } else {
        if (!CLI_SCRIPT and !$PAGE->headerprinted) {
            $strupgrade  = get_string('upgradingversion', 'admin');
            $PAGE->set_pagelayout('maintenance');
            upgrade_init_javascript();
            $PAGE->set_title($strupgrade.' - Moodle '.$CFG->target_release);
            $PAGE->set_heading($strupgrade);
            $PAGE->navbar->add($strupgrade);
            $PAGE->set_cacheable(false);
            echo $OUTPUT->header();
        }

        ignore_user_abort(true);
        core_shutdown_manager::register_function('upgrade_finished_handler');
        upgrade_setup_debug(true);
        set_config('upgraderunning', time()+300);
        $started = true;
    }
}

/**
 * Internal function - executed if upgrade interrupted.
 */
function upgrade_finished_handler() {
    upgrade_finished();
}

/**
 * Indicates upgrade is finished.
 *
 * This function may be called repeatedly.
 *
 * @global object
 * @global object
 */
function upgrade_finished($continueurl=null) {
    global $CFG, $DB, $OUTPUT;

    if (!empty($CFG->upgraderunning)) {
        unset_config('upgraderunning');
        // We have to forcefully purge the caches using the writer here.
        // This has to be done after we unset the config var. If someone hits the site while this is set they will
        // cause the config values to propogate to the caches.
        // Caches are purged after the last step in an upgrade but there is several code routines that exceute between
        // then and now that leaving a window for things to fall out of sync.
        cache_helper::purge_all(true);
        upgrade_setup_debug(false);
        ignore_user_abort(false);
        if ($continueurl) {
            echo $OUTPUT->continue_button($continueurl);
            echo $OUTPUT->footer();
            die;
        }
    }
}

/**
 * @global object
 * @global object
 */
function upgrade_setup_debug($starting) {
    global $CFG, $DB;

    static $originaldebug = null;

    if ($starting) {
        if ($originaldebug === null) {
            $originaldebug = $DB->get_debug();
        }
        if (!empty($CFG->upgradeshowsql)) {
            $DB->set_debug(true);
        }
    } else {
        $DB->set_debug($originaldebug);
    }
}

function print_upgrade_separator() {
    if (!CLI_SCRIPT) {
        echo '<hr />';
    }
}

/**
 * Default start upgrade callback
 * @param string $plugin
 * @param bool $installation true if installation, false means upgrade
 */
function print_upgrade_part_start($plugin, $installation, $verbose) {
    global $OUTPUT;
    if (empty($plugin) or $plugin == 'moodle') {
        upgrade_started($installation); // does not store upgrade running flag yet
        if ($verbose) {
            echo $OUTPUT->heading(get_string('coresystem'));
        }
    } else {
        upgrade_started();
        if ($verbose) {
            echo $OUTPUT->heading($plugin);
        }
    }
    if ($installation) {
        if (empty($plugin) or $plugin == 'moodle') {
            // no need to log - log table not yet there ;-)
        } else {
            upgrade_log(UPGRADE_LOG_NORMAL, $plugin, 'Starting plugin installation');
        }
    } else {
        if (empty($plugin) or $plugin == 'moodle') {
            upgrade_log(UPGRADE_LOG_NORMAL, $plugin, 'Starting core upgrade');
        } else {
            upgrade_log(UPGRADE_LOG_NORMAL, $plugin, 'Starting plugin upgrade');
        }
    }
}

/**
 * Default end upgrade callback
 * @param string $plugin
 * @param bool $installation true if installation, false means upgrade
 */
function print_upgrade_part_end($plugin, $installation, $verbose) {
    global $OUTPUT;
    upgrade_started();
    if ($installation) {
        if (empty($plugin) or $plugin == 'moodle') {
            upgrade_log(UPGRADE_LOG_NORMAL, $plugin, 'Core installed');
        } else {
            upgrade_log(UPGRADE_LOG_NORMAL, $plugin, 'Plugin installed');
        }
    } else {
        if (empty($plugin) or $plugin == 'moodle') {
            upgrade_log(UPGRADE_LOG_NORMAL, $plugin, 'Core upgraded');
        } else {
            upgrade_log(UPGRADE_LOG_NORMAL, $plugin, 'Plugin upgraded');
        }
    }
    if ($verbose) {
        echo $OUTPUT->notification(get_string('success'), 'notifysuccess');
        print_upgrade_separator();
    }
}

/**
 * Sets up JS code required for all upgrade scripts.
 * @global object
 */
function upgrade_init_javascript() {
    global $PAGE;
    // scroll to the end of each upgrade page so that ppl see either error or continue button,
    // no need to scroll continuously any more, it is enough to jump to end once the footer is printed ;-)
    $js = "window.scrollTo(0, 5000000);";
    $PAGE->requires->js_init_code($js);
}

/**
 * Try to upgrade the given language pack (or current language)
 *
 * @param string $lang the code of the language to update, defaults to the current language
 */
function upgrade_language_pack($lang = null) {
    global $CFG;

    if (!empty($CFG->skiplangupgrade)) {
        return;
    }

    if (!file_exists("$CFG->dirroot/$CFG->admin/tool/langimport/lib.php")) {
        // weird, somebody uninstalled the import utility
        return;
    }

    if (!$lang) {
        $lang = current_language();
    }

    if (!get_string_manager()->translation_exists($lang)) {
        return;
    }

    get_string_manager()->reset_caches();

    if ($lang === 'en') {
        return;  // Nothing to do
    }

    upgrade_started(false);

    require_once("$CFG->dirroot/$CFG->admin/tool/langimport/lib.php");
    tool_langimport_preupgrade_update($lang);

    get_string_manager()->reset_caches();

    print_upgrade_separator();
}

/**
 * Install core moodle tables and initialize
 * @param float $version target version
 * @param bool $verbose
 * @return void, may throw exception
 */
function install_core($version, $verbose) {
    global $CFG, $DB;

    // We can not call purge_all_caches() yet, make sure the temp and cache dirs exist and are empty.
    remove_dir($CFG->cachedir.'', true);
    make_cache_directory('', true);

    remove_dir($CFG->localcachedir.'', true);
    make_localcache_directory('', true);

    remove_dir($CFG->tempdir.'', true);
    make_temp_directory('', true);

    remove_dir($CFG->dataroot.'/muc', true);
    make_writable_directory($CFG->dataroot.'/muc', true);

    try {
        core_php_time_limit::raise(600);
        print_upgrade_part_start('moodle', true, $verbose); // does not store upgrade running flag

        $DB->get_manager()->install_from_xmldb_file("$CFG->libdir/db/install.xml");
        upgrade_started();     // we want the flag to be stored in config table ;-)

        // set all core default records and default settings
        require_once("$CFG->libdir/db/install.php");
        xmldb_main_install(); // installs the capabilities too

        // store version
        upgrade_main_savepoint(true, $version, false);

        // Continue with the installation
        log_update_descriptions('moodle');
        external_update_descriptions('moodle');
        events_update_definition('moodle');
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');
        message_update_providers('moodle');
        \core\message\inbound\manager::update_handlers_for_component('moodle');

        // Write default settings unconditionally
        admin_apply_default_settings(NULL, true);

        print_upgrade_part_end(null, true, $verbose);

        // Purge all caches. They're disabled but this ensures that we don't have any persistent data just in case something
        // during installation didn't use APIs.
        cache_helper::purge_all();
    } catch (exception $ex) {
        upgrade_handle_exception($ex);
    }
}

/**
 * Upgrade moodle core
 * @param float $version target version
 * @param bool $verbose
 * @return void, may throw exception
 */
function upgrade_core($version, $verbose) {
    global $CFG, $SITE, $DB, $COURSE;

    raise_memory_limit(MEMORY_EXTRA);

    require_once($CFG->libdir.'/db/upgrade.php');    // Defines upgrades

    try {
        // Reset caches before any output.
        cache_helper::purge_all(true);
        purge_all_caches();

        // Upgrade current language pack if we can
        upgrade_language_pack();

        print_upgrade_part_start('moodle', false, $verbose);

        // Pre-upgrade scripts for local hack workarounds.
        $preupgradefile = "$CFG->dirroot/local/preupgrade.php";
        if (file_exists($preupgradefile)) {
            core_php_time_limit::raise();
            require($preupgradefile);
            // Reset upgrade timeout to default.
            upgrade_set_timeout();
        }

        $result = xmldb_main_upgrade($CFG->version);
        if ($version > $CFG->version) {
            // store version if not already there
            upgrade_main_savepoint($result, $version, false);
        }

        // In case structure of 'course' table has been changed and we forgot to update $SITE, re-read it from db.
        $SITE = $DB->get_record('course', array('id' => $SITE->id));
        $COURSE = clone($SITE);

        // perform all other component upgrade routines
        update_capabilities('moodle');
        log_update_descriptions('moodle');
        external_update_descriptions('moodle');
        events_update_definition('moodle');
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');
        message_update_providers('moodle');
        \core\message\inbound\manager::update_handlers_for_component('moodle');
        // Update core definitions.
        cache_helper::update_definitions(true);

        // Purge caches again, just to be sure we arn't holding onto old stuff now.
        cache_helper::purge_all(true);
        purge_all_caches();

        // Clean up contexts - more and more stuff depends on existence of paths and contexts
        context_helper::cleanup_instances();
        context_helper::create_instances(null, false);
        context_helper::build_all_paths(false);
        $syscontext = context_system::instance();
        $syscontext->mark_dirty();

        print_upgrade_part_end('moodle', false, $verbose);
    } catch (Exception $ex) {
        upgrade_handle_exception($ex);
    }
}

/**
 * Upgrade/install other parts of moodle
 * @param bool $verbose
 * @return void, may throw exception
 */
function upgrade_noncore($verbose) {
    global $CFG;

    raise_memory_limit(MEMORY_EXTRA);

    // upgrade all plugins types
    try {
        // Reset caches before any output.
        cache_helper::purge_all(true);
        purge_all_caches();

        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $type=>$location) {
            upgrade_plugins($type, 'print_upgrade_part_start', 'print_upgrade_part_end', $verbose);
        }
        // Update cache definitions. Involves scanning each plugin for any changes.
        cache_helper::update_definitions();
        // Mark the site as upgraded.
        set_config('allversionshash', core_component::get_all_versions_hash());

        // Purge caches again, just to be sure we arn't holding onto old stuff now.
        cache_helper::purge_all(true);
        purge_all_caches();

    } catch (Exception $ex) {
        upgrade_handle_exception($ex);
    }
}

/**
 * Checks if the main tables have been installed yet or not.
 *
 * Note: we can not use caches here because they might be stale,
 *       use with care!
 *
 * @return bool
 */
function core_tables_exist() {
    global $DB;

    if (!$tables = $DB->get_tables(false) ) {    // No tables yet at all.
        return false;

    } else {                                 // Check for missing main tables
        $mtables = array('config', 'course', 'groupings'); // some tables used in 1.9 and 2.0, preferable something from the start and end of install.xml
        foreach ($mtables as $mtable) {
            if (!in_array($mtable, $tables)) {
                return false;
            }
        }
        return true;
    }
}

/**
 * upgrades the mnet rpc definitions for the given component.
 * this method doesn't return status, an exception will be thrown in the case of an error
 *
 * @param string $component the plugin to upgrade, eg auth_mnet
 */
function upgrade_plugin_mnet_functions($component) {
    global $DB, $CFG;

    list($type, $plugin) = core_component::normalize_component($component);
    $path = core_component::get_plugin_directory($type, $plugin);

    $publishes = array();
    $subscribes = array();
    if (file_exists($path . '/db/mnet.php')) {
        require_once($path . '/db/mnet.php'); // $publishes comes from this file
    }
    if (empty($publishes)) {
        $publishes = array(); // still need this to be able to disable stuff later
    }
    if (empty($subscribes)) {
        $subscribes = array(); // still need this to be able to disable stuff later
    }

    static $servicecache = array();

    // rekey an array based on the rpc method for easy lookups later
    $publishmethodservices = array();
    $subscribemethodservices = array();
    foreach($publishes as $servicename => $service) {
        if (is_array($service['methods'])) {
            foreach($service['methods'] as $methodname) {
                $service['servicename'] = $servicename;
                $publishmethodservices[$methodname][] = $service;
            }
        }
    }

    // Disable functions that don't exist (any more) in the source
    // Should these be deleted? What about their permissions records?
    foreach ($DB->get_records('mnet_rpc', array('pluginname'=>$plugin, 'plugintype'=>$type), 'functionname ASC ') as $rpc) {
        if (!array_key_exists($rpc->functionname, $publishmethodservices) && $rpc->enabled) {
            $DB->set_field('mnet_rpc', 'enabled', 0, array('id' => $rpc->id));
        } else if (array_key_exists($rpc->functionname, $publishmethodservices) && !$rpc->enabled) {
            $DB->set_field('mnet_rpc', 'enabled', 1, array('id' => $rpc->id));
        }
    }

    // reflect all the services we're publishing and save them
    require_once($CFG->dirroot . '/lib/zend/Zend/Server/Reflection.php');
    static $cachedclasses = array(); // to store reflection information in
    foreach ($publishes as $service => $data) {
        $f = $data['filename'];
        $c = $data['classname'];
        foreach ($data['methods'] as $method) {
            $dataobject = new stdClass();
            $dataobject->plugintype  = $type;
            $dataobject->pluginname  = $plugin;
            $dataobject->enabled     = 1;
            $dataobject->classname   = $c;
            $dataobject->filename    = $f;

            if (is_string($method)) {
                $dataobject->functionname = $method;

            } else if (is_array($method)) { // wants to override file or class
                $dataobject->functionname = $method['method'];
                $dataobject->classname     = $method['classname'];
                $dataobject->filename      = $method['filename'];
            }
            $dataobject->xmlrpcpath = $type.'/'.$plugin.'/'.$dataobject->filename.'/'.$method;
            $dataobject->static = false;

            require_once($path . '/' . $dataobject->filename);
            $functionreflect = null; // slightly different ways to get this depending on whether it's a class method or a function
            if (!empty($dataobject->classname)) {
                if (!class_exists($dataobject->classname)) {
                    throw new moodle_exception('installnosuchmethod', 'mnet', '', (object)array('method' => $dataobject->functionname, 'class' => $dataobject->classname));
                }
                $key = $dataobject->filename . '|' . $dataobject->classname;
                if (!array_key_exists($key, $cachedclasses)) { // look to see if we've already got a reflection object
                    try {
                        $cachedclasses[$key] = Zend_Server_Reflection::reflectClass($dataobject->classname);
                    } catch (Zend_Server_Reflection_Exception $e) { // catch these and rethrow them to something more helpful
                        throw new moodle_exception('installreflectionclasserror', 'mnet', '', (object)array('method' => $dataobject->functionname, 'class' => $dataobject->classname, 'error' => $e->getMessage()));
                    }
                }
                $r =& $cachedclasses[$key];
                if (!$r->hasMethod($dataobject->functionname)) {
                    throw new moodle_exception('installnosuchmethod', 'mnet', '', (object)array('method' => $dataobject->functionname, 'class' => $dataobject->classname));
                }
                // stupid workaround for zend not having a getMethod($name) function
                $ms = $r->getMethods();
                foreach ($ms as $m) {
                    if ($m->getName() == $dataobject->functionname) {
                        $functionreflect = $m;
                        break;
                    }
                }
                $dataobject->static = (int)$functionreflect->isStatic();
            } else {
                if (!function_exists($dataobject->functionname)) {
                    throw new moodle_exception('installnosuchfunction', 'mnet', '', (object)array('method' => $dataobject->functionname, 'file' => $dataobject->filename));
                }
                try {
                    $functionreflect = Zend_Server_Reflection::reflectFunction($dataobject->functionname);
                } catch (Zend_Server_Reflection_Exception $e) { // catch these and rethrow them to something more helpful
                    throw new moodle_exception('installreflectionfunctionerror', 'mnet', '', (object)array('method' => $dataobject->functionname, '' => $dataobject->filename, 'error' => $e->getMessage()));
                }
            }
            $dataobject->profile =  serialize(admin_mnet_method_profile($functionreflect));
            $dataobject->help = $functionreflect->getDescription();

            if ($record_exists = $DB->get_record('mnet_rpc', array('xmlrpcpath'=>$dataobject->xmlrpcpath))) {
                $dataobject->id      = $record_exists->id;
                $dataobject->enabled = $record_exists->enabled;
                $DB->update_record('mnet_rpc', $dataobject);
            } else {
                $dataobject->id = $DB->insert_record('mnet_rpc', $dataobject, true);
            }

            // TODO this API versioning must be reworked, here the recently processed method
            // sets the service API which may not be correct
            foreach ($publishmethodservices[$dataobject->functionname] as $service) {
                if ($serviceobj = $DB->get_record('mnet_service', array('name'=>$service['servicename']))) {
                    $serviceobj->apiversion = $service['apiversion'];
                    $DB->update_record('mnet_service', $serviceobj);
                } else {
                    $serviceobj = new stdClass();
                    $serviceobj->name        = $service['servicename'];
                    $serviceobj->description = empty($service['description']) ? '' : $service['description'];
                    $serviceobj->apiversion  = $service['apiversion'];
                    $serviceobj->offer       = 1;
                    $serviceobj->id          = $DB->insert_record('mnet_service', $serviceobj);
                }
                $servicecache[$service['servicename']] = $serviceobj;
                if (!$DB->record_exists('mnet_service2rpc', array('rpcid'=>$dataobject->id, 'serviceid'=>$serviceobj->id))) {
                    $obj = new stdClass();
                    $obj->rpcid = $dataobject->id;
                    $obj->serviceid = $serviceobj->id;
                    $DB->insert_record('mnet_service2rpc', $obj, true);
                }
            }
        }
    }
    // finished with methods we publish, now do subscribable methods
    foreach($subscribes as $service => $methods) {
        if (!array_key_exists($service, $servicecache)) {
            if (!$serviceobj = $DB->get_record('mnet_service', array('name' =>  $service))) {
                debugging("TODO: skipping unknown service $service - somebody needs to fix MDL-21993");
                continue;
            }
            $servicecache[$service] = $serviceobj;
        } else {
            $serviceobj = $servicecache[$service];
        }
        foreach ($methods as $method => $xmlrpcpath) {
            if (!$rpcid = $DB->get_field('mnet_remote_rpc', 'id', array('xmlrpcpath'=>$xmlrpcpath))) {
                $remoterpc = (object)array(
                    'functionname' => $method,
                    'xmlrpcpath' => $xmlrpcpath,
                    'plugintype' => $type,
                    'pluginname' => $plugin,
                    'enabled'    => 1,
                );
                $rpcid = $remoterpc->id = $DB->insert_record('mnet_remote_rpc', $remoterpc, true);
            }
            if (!$DB->record_exists('mnet_remote_service2rpc', array('rpcid'=>$rpcid, 'serviceid'=>$serviceobj->id))) {
                $obj = new stdClass();
                $obj->rpcid = $rpcid;
                $obj->serviceid = $serviceobj->id;
                $DB->insert_record('mnet_remote_service2rpc', $obj, true);
            }
            $subscribemethodservices[$method][] = $service;
        }
    }

    foreach ($DB->get_records('mnet_remote_rpc', array('pluginname'=>$plugin, 'plugintype'=>$type), 'functionname ASC ') as $rpc) {
        if (!array_key_exists($rpc->functionname, $subscribemethodservices) && $rpc->enabled) {
            $DB->set_field('mnet_remote_rpc', 'enabled', 0, array('id' => $rpc->id));
        } else if (array_key_exists($rpc->functionname, $subscribemethodservices) && !$rpc->enabled) {
            $DB->set_field('mnet_remote_rpc', 'enabled', 1, array('id' => $rpc->id));
        }
    }

    return true;
}

/**
 * Given some sort of Zend Reflection function/method object, return a profile array, ready to be serialized and stored
 *
 * @param Zend_Server_Reflection_Function_Abstract $function can be any subclass of this object type
 *
 * @return array
 */
function admin_mnet_method_profile(Zend_Server_Reflection_Function_Abstract $function) {
    $protos = $function->getPrototypes();
    $proto = array_pop($protos);
    $ret = $proto->getReturnValue();
    $profile = array(
        'parameters' =>  array(),
        'return'     =>  array(
            'type'        => $ret->getType(),
            'description' => $ret->getDescription(),
        ),
    );
    foreach ($proto->getParameters() as $p) {
        $profile['parameters'][] = array(
            'name' => $p->getName(),
            'type' => $p->getType(),
            'description' => $p->getDescription(),
        );
    }
    return $profile;
}


/**
 * This function finds duplicate records (based on combinations of fields that should be unique)
 * and then progamatically generated a "most correct" version of the data, update and removing
 * records as appropriate
 *
 * Thanks to Dan Marsden for help
 *
 * @param   string  $table      Table name
 * @param   array   $uniques    Array of field names that should be unique
 * @param   array   $fieldstocheck  Array of fields to generate "correct" data from (optional)
 * @return  void
 */
function upgrade_course_completion_remove_duplicates($table, $uniques, $fieldstocheck = array()) {
    global $DB;

    // Find duplicates
    $sql_cols = implode(', ', $uniques);

    $sql = "SELECT {$sql_cols} FROM {{$table}} GROUP BY {$sql_cols} HAVING (count(id) > 1)";
    $duplicates = $DB->get_recordset_sql($sql, array());

    // Loop through duplicates
    foreach ($duplicates as $duplicate) {
        $pointer = 0;

        // Generate SQL for finding records with these duplicate uniques
        $sql_select = implode(' = ? AND ', $uniques).' = ?'; // builds "fieldname = ? AND fieldname = ?"
        $uniq_values = array();
        foreach ($uniques as $u) {
            $uniq_values[] = $duplicate->$u;
        }

        $sql_order = implode(' DESC, ', $uniques).' DESC'; // builds "fieldname DESC, fieldname DESC"

        // Get records with these duplicate uniques
        $records = $DB->get_records_select(
            $table,
            $sql_select,
            $uniq_values,
            $sql_order
        );

        // Loop through and build a "correct" record, deleting the others
        $needsupdate = false;
        $origrecord = null;
        foreach ($records as $record) {
            $pointer++;
            if ($pointer === 1) { // keep 1st record but delete all others.
                $origrecord = $record;
            } else {
                // If we have fields to check, update original record
                if ($fieldstocheck) {
                    // we need to keep the "oldest" of all these fields as the valid completion record.
                    // but we want to ignore null values
                    foreach ($fieldstocheck as $f) {
                        if ($record->$f && (($origrecord->$f > $record->$f) || !$origrecord->$f)) {
                            $origrecord->$f = $record->$f;
                            $needsupdate = true;
                        }
                    }
                }
                $DB->delete_records($table, array('id' => $record->id));
            }
        }
        if ($needsupdate || isset($origrecord->reaggregate)) {
            // If this table has a reaggregate field, update to force recheck on next cron run
            if (isset($origrecord->reaggregate)) {
                $origrecord->reaggregate = time();
            }
            $DB->update_record($table, $origrecord);
        }
    }
}

/**
 * Find questions missing an existing category and associate them with
 * a category which purpose is to gather them.
 *
 * @return void
 */
function upgrade_save_orphaned_questions() {
    global $DB;

    // Looking for orphaned questions
    $orphans = $DB->record_exists_select('question',
            'NOT EXISTS (SELECT 1 FROM {question_categories} WHERE {question_categories}.id = {question}.category)');
    if (!$orphans) {
        return;
    }

    // Generate a unique stamp for the orphaned questions category, easier to identify it later on
    $uniquestamp = "unknownhost+120719170400+orphan";
    $systemcontext = context_system::instance();

    // Create the orphaned category at system level
    $cat = $DB->get_record('question_categories', array('stamp' => $uniquestamp,
            'contextid' => $systemcontext->id));
    if (!$cat) {
        $cat = new stdClass();
        $cat->parent = 0;
        $cat->contextid = $systemcontext->id;
        $cat->name = get_string('orphanedquestionscategory', 'question');
        $cat->info = get_string('orphanedquestionscategoryinfo', 'question');
        $cat->sortorder = 999;
        $cat->stamp = $uniquestamp;
        $cat->id = $DB->insert_record("question_categories", $cat);
    }

    // Set a category to those orphans
    $params = array('catid' => $cat->id);
    $DB->execute('UPDATE {question} SET category = :catid WHERE NOT EXISTS
            (SELECT 1 FROM {question_categories} WHERE {question_categories}.id = {question}.category)', $params);
}

/**
 * Rename old backup files to current backup files.
 *
 * When added the setting 'backup_shortname' (MDL-28657) the backup file names did not contain the id of the course.
 * Further we fixed that behaviour by forcing the id to be always present in the file name (MDL-33812).
 * This function will explore the backup directory and attempt to rename the previously created files to include
 * the id in the name. Doing this will put them back in the process of deleting the excess backups for each course.
 *
 * This function manually recreates the file name, instead of using
 * {@link backup_plan_dbops::get_default_backup_filename()}, use it carefully if you're using it outside of the
 * usual upgrade process.
 *
 * @see backup_cron_automated_helper::remove_excess_backups()
 * @link http://tracker.moodle.org/browse/MDL-35116
 * @return void
 * @since Moodle 2.4
 */
function upgrade_rename_old_backup_files_using_shortname() {
    global $CFG;
    $dir = get_config('backup', 'backup_auto_destination');
    $useshortname = get_config('backup', 'backup_shortname');
    if (empty($dir) || !is_dir($dir) || !is_writable($dir)) {
        return;
    }

    require_once($CFG->dirroot.'/backup/util/includes/backup_includes.php');
    $backupword = str_replace(' ', '_', core_text::strtolower(get_string('backupfilename')));
    $backupword = trim(clean_filename($backupword), '_');
    $filename = $backupword . '-' . backup::FORMAT_MOODLE . '-' . backup::TYPE_1COURSE . '-';
    $regex = '#^'.preg_quote($filename, '#').'.*\.mbz$#';
    $thirtyapril = strtotime('30 April 2012 00:00');

    // Reading the directory.
    if (!$files = scandir($dir)) {
        return;
    }
    foreach ($files as $file) {
        // Skip directories and files which do not start with the common prefix.
        // This avoids working on files which are not related to this issue.
        if (!is_file($dir . '/' . $file) || !preg_match($regex, $file)) {
            continue;
        }

        // Extract the information from the XML file.
        try {
            $bcinfo = backup_general_helper::get_backup_information_from_mbz($dir . '/' . $file);
        } catch (backup_helper_exception $e) {
            // Some error while retrieving the backup informations, skipping...
            continue;
        }

        // Make sure this a course backup.
        if ($bcinfo->format !== backup::FORMAT_MOODLE || $bcinfo->type !== backup::TYPE_1COURSE) {
            continue;
        }

        // Skip the backups created before the short name option was initially introduced (MDL-28657).
        // This was integrated on the 2nd of May 2012. Let's play safe with timezone and use the 30th of April.
        if ($bcinfo->backup_date < $thirtyapril) {
            continue;
        }

        // Let's check if the file name contains the ID where it is supposed to be, if it is the case then
        // we will skip the file. Of course it could happen that the course ID is identical to the course short name
        // even though really unlikely, but then renaming this file is not necessary. If the ID is not found in the
        // file name then it was probably the short name which was used.
        $idfilename = $filename . $bcinfo->original_course_id . '-';
        $idregex = '#^'.preg_quote($idfilename, '#').'.*\.mbz$#';
        if (preg_match($idregex, $file)) {
            continue;
        }

        // Generating the file name manually. We do not use backup_plan_dbops::get_default_backup_filename() because
        // it will query the database to get some course information, and the course could not exist any more.
        $newname = $filename . $bcinfo->original_course_id . '-';
        if ($useshortname) {
            $shortname = str_replace(' ', '_', $bcinfo->original_course_shortname);
            $shortname = core_text::strtolower(trim(clean_filename($shortname), '_'));
            $newname .= $shortname . '-';
        }

        $backupdateformat = str_replace(' ', '_', get_string('backupnameformat', 'langconfig'));
        $date = userdate($bcinfo->backup_date, $backupdateformat, 99, false);
        $date = core_text::strtolower(trim(clean_filename($date), '_'));
        $newname .= $date;

        if (isset($bcinfo->root_settings['users']) && !$bcinfo->root_settings['users']) {
            $newname .= '-nu';
        } else if (isset($bcinfo->root_settings['anonymize']) && $bcinfo->root_settings['anonymize']) {
            $newname .= '-an';
        }
        $newname .= '.mbz';

        // Final check before attempting the renaming.
        if ($newname == $file || file_exists($dir . '/' . $newname)) {
            continue;
        }
        @rename($dir . '/' . $file, $dir . '/' . $newname);
    }
}

/**
 * Detect duplicate grade item sortorders and resort the
 * items to remove them.
 */
function upgrade_grade_item_fix_sortorder() {
    global $DB;

    // The simple way to fix these sortorder duplicates would be simply to resort each
    // affected course. But in order to reduce the impact of this upgrade step we're trying
    // to do it more efficiently by doing a series of update statements rather than updating
    // every single grade item in affected courses.

    $sql = "SELECT DISTINCT g1.courseid
              FROM {grade_items} g1
              JOIN {grade_items} g2 ON g1.courseid = g2.courseid
             WHERE g1.sortorder = g2.sortorder AND g1.id != g2.id
             ORDER BY g1.courseid ASC";
    foreach ($DB->get_fieldset_sql($sql) as $courseid) {
        $transaction = $DB->start_delegated_transaction();
        $items = $DB->get_records('grade_items', array('courseid' => $courseid), '', 'id, sortorder, sortorder AS oldsort');

        // Get all duplicates in course order, highest sort order, and higest id first so that we can make space at the
        // bottom higher end of the sort orders and work down by id.
        $sql = "SELECT DISTINCT g1.id, g1.sortorder
                FROM {grade_items} g1
                JOIN {grade_items} g2 ON g1.courseid = g2.courseid
                WHERE g1.sortorder = g2.sortorder AND g1.id != g2.id AND g1.courseid = :courseid
                ORDER BY g1.sortorder DESC, g1.id DESC";

        // This is the O(N*N) like the database version we're replacing, but at least the constants are a billion times smaller...
        foreach ($DB->get_records_sql($sql, array('courseid' => $courseid)) as $duplicate) {
            foreach ($items as $item) {
                if ($item->sortorder > $duplicate->sortorder || ($item->sortorder == $duplicate->sortorder && $item->id > $duplicate->id)) {
                    $item->sortorder += 1;
                }
            }
        }
        foreach ($items as $item) {
            if ($item->sortorder != $item->oldsort) {
                $DB->update_record('grade_items', array('id' => $item->id, 'sortorder' => $item->sortorder));
            }
        }

        $transaction->allow_commit();
    }
}

/**
 * Detect file areas with missing root directory records and add them.
 */
function upgrade_fix_missing_root_folders() {
    global $DB, $USER;

    $transaction = $DB->start_delegated_transaction();

    $sql = "SELECT contextid, component, filearea, itemid
              FROM {files}
             WHERE (component <> 'user' OR filearea <> 'draft')
          GROUP BY contextid, component, filearea, itemid
            HAVING MAX(CASE WHEN filename = '.' AND filepath = '/' THEN 1 ELSE 0 END) = 0";

    $rs = $DB->get_recordset_sql($sql);
    $defaults = array('filepath' => '/',
        'filename' => '.',
        'userid' => 0, // Don't rely on any particular user for these system records.
        'filesize' => 0,
        'timecreated' => time(),
        'timemodified' => time(),
        'contenthash' => sha1(''));
    foreach ($rs as $r) {
        $pathhash = sha1("/$r->contextid/$r->component/$r->filearea/$r->itemid/.");
        $DB->insert_record('files', (array)$r + $defaults +
            array('pathnamehash' => $pathhash));
    }
    $rs->close();
    $transaction->allow_commit();
}

/**
 * Detect draft file areas with missing root directory records and add them.
 */
function upgrade_fix_missing_root_folders_draft() {
    global $DB;

    $transaction = $DB->start_delegated_transaction();

    $sql = "SELECT contextid, itemid, MAX(timecreated) AS timecreated, MAX(timemodified) AS timemodified
              FROM {files}
             WHERE (component = 'user' AND filearea = 'draft')
          GROUP BY contextid, itemid
            HAVING MAX(CASE WHEN filename = '.' AND filepath = '/' THEN 1 ELSE 0 END) = 0";

    $rs = $DB->get_recordset_sql($sql);
    $defaults = array('component' => 'user',
        'filearea' => 'draft',
        'filepath' => '/',
        'filename' => '.',
        'userid' => 0, // Don't rely on any particular user for these system records.
        'filesize' => 0,
        'contenthash' => sha1(''));
    foreach ($rs as $r) {
        $r->pathnamehash = sha1("/$r->contextid/user/draft/$r->itemid/.");
        $DB->insert_record('files', (array)$r + $defaults);
    }
    $rs->close();
    $transaction->allow_commit();
}

/**
 * This function verifies that the database is not using an unsupported storage engine.
 *
 * @param environment_results $result object to update, if relevant
 * @return environment_results|null updated results object, or null if the storage engine is supported
 */
function check_database_storage_engine(environment_results $result) {
    global $DB;

    // Check if MySQL is the DB family (this will also be the same for MariaDB).
    if ($DB->get_dbfamily() == 'mysql') {
        // Get the database engine we will either be using to install the tables, or what we are currently using.
        $engine = $DB->get_dbengine();
        // Check if MyISAM is the storage engine that will be used, if so, do not proceed and display an error.
        if ($engine == 'MyISAM') {
            $result->setInfo('unsupported_db_storage_engine');
            $result->setStatus(false);
            return $result;
        }
    }

    return null;
}

/**
 * Method used to check the usage of slasharguments config and display a warning message.
 *
 * @param environment_results $result object to update, if relevant.
 * @return environment_results|null updated results or null if slasharguments is disabled.
 */
function check_slasharguments(environment_results $result){
    global $CFG;

    if (empty($CFG->slasharguments)) {
        $result->setInfo('slasharguments');
        $result->setStatus(false);
        return $result;
    }

    return null;
}
