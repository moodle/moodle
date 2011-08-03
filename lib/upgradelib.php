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
 * Upgrade savepoint, marks end of each upgrade block.
 * It stores new main version, resets upgrade timeout
 * and abort upgrade if user cancels page loading.
 *
 * Please do not make large upgrade blocks with lots of operations,
 * for example when adding tables keep only one table operation per block.
 *
 * @global object
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
 * @global object
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $modname name of module
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_mod_savepoint($result, $version, $modname, $allowabort=true) {
    global $DB;

    if (!$result) {
        throw new upgrade_exception("mod_$modname", $version);
    }

    if (!$module = $DB->get_record('modules', array('name'=>$modname))) {
        print_error('modulenotexist', 'debug', '', $modname);
    }

    if ($module->version >= $version) {
        // something really wrong is going on in upgrade script
        throw new downgrade_exception("mod_$modname", $module->version, $version);
    }
    $module->version = $version;
    $DB->update_record('modules', $module);
    upgrade_log(UPGRADE_LOG_NORMAL, "mod_$modname", 'Upgrade savepoint reached');

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
 * @global object
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $blockname name of block
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_block_savepoint($result, $version, $blockname, $allowabort=true) {
    global $DB;

    if (!$result) {
        throw new upgrade_exception("block_$blockname", $version);
    }

    if (!$block = $DB->get_record('block', array('name'=>$blockname))) {
        print_error('blocknotexist', 'debug', '', $blockname);
    }

    if ($block->version >= $version) {
        // something really wrong is going on in upgrade script
        throw new downgrade_exception("block_$blockname", $block->version, $version);
    }
    $block->version = $version;
    $DB->update_record('block', $block);
    upgrade_log(UPGRADE_LOG_NORMAL, "block_$blockname", 'Upgrade savepoint reached');

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
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $type name of plugin
 * @param string $dir location of plugin
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_plugin_savepoint($result, $version, $type, $plugin, $allowabort=true) {
    $component = $type.'_'.$plugin;

    if (!$result) {
        throw new upgrade_exception($component, $version);
    }

    $installedversion = get_config($component, 'version');
    if ($installedversion >= $version) {
        // Something really wrong is going on in the upgrade script
        throw new downgrade_exception($component, $installedversion, $version);
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

    $plugs = get_plugin_list($type);

    foreach ($plugs as $plug=>$fullplug) {
        $component = $type.'_'.$plug; // standardised plugin name

        // check plugin dir is valid name
        $cplug = strtolower($plug);
        $cplug = clean_param($cplug, PARAM_SAFEDIR);
        $cplug = str_replace('-', '', $cplug);
        if ($plug !== $cplug) {
            throw new plugin_defective_exception($component, 'Invalid plugin directory name.');
        }

        if (!is_readable($fullplug.'/version.php')) {
            continue;
        }

        $plugin = new stdClass();
        require($fullplug.'/version.php');  // defines $plugin with version etc

        // if plugin tells us it's full name we may check the location
        if (isset($plugin->component)) {
            if ($plugin->component !== $component) {
                throw new plugin_defective_exception($component, 'Plugin installed in wrong folder.');
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
                    message_update_providers($component);
                    if ($type === 'message') {
                        message_update_processors($plug);
                    }
                    upgrade_plugin_mnet_functions($component);
                    $endcallback($component, true, $verbose);
                }
            }
        }

        $installedversion = get_config($plugin->fullname, 'version');
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
            message_update_providers($component);
            if ($type === 'message') {
                message_update_processors($plug);
            }
            upgrade_plugin_mnet_functions($component);

            purge_all_caches();
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

            $installedversion = get_config($plugin->fullname, 'version');
            if ($installedversion < $plugin->version) {
                // store version if not already there
                upgrade_plugin_savepoint($result, $plugin->version, $type, $plug, false);
            }

        /// Upgrade various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            message_update_providers($component);
            if ($type === 'message') {
                message_update_processors($plug);
            }
            upgrade_plugin_mnet_functions($component);

            purge_all_caches();
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

    $mods = get_plugin_list('mod');

    foreach ($mods as $mod=>$fullmod) {

        if ($mod === 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $component = 'mod_'.$mod;

        // check module dir is valid name
        $cmod = strtolower($mod);
        $cmod = clean_param($cmod, PARAM_SAFEDIR);
        $cmod = str_replace('-', '', $cmod);
        $cmod = str_replace('_', '', $cmod); // modules MUST not have '_' in name and never will, sorry
        if ($mod !== $cmod) {
            throw new plugin_defective_exception($component, 'Invalid plugin directory name.');
        }

        if (!is_readable($fullmod.'/version.php')) {
            throw new plugin_defective_exception($component, 'Missing version.php');
        }

        $module = new stdClass();
        require($fullmod .'/version.php');  // defines $module with version etc

        // if plugin tells us it's full name we may check the location
        if (isset($module->component)) {
            if ($module->component !== $component) {
                throw new plugin_defective_exception($component, 'Plugin installed in wrong folder.');
            }
        }

        if (empty($module->version)) {
            if (isset($module->version)) {
                // Version is empty but is set - it means its value is 0 or ''. Let us skip such module.
                // This is intended for developers so they can work on the early stages of the module.
                continue;
            }
            throw new plugin_defective_exception($component, 'Missing version value in version.php');
        }

        if (!empty($module->requires)) {
            if ($module->requires > $CFG->version) {
                throw new upgrade_requires_exception($component, $module->version, $CFG->version, $module->requires);
            } else if ($module->requires < 2010000000) {
                throw new plugin_defective_exception($component, 'Plugin is not compatible with Moodle 2.x or later.');
            }
        }

        // all modules must have en lang pack
        if (!is_readable("$fullmod/lang/en/$mod.php")) {
            throw new plugin_defective_exception($component, 'Missing mandatory en language pack.');
        }

        $module->name = $mod;   // The name MUST match the directory

        $currmodule = $DB->get_record('modules', array('name'=>$module->name));

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
                    message_update_providers($component);
                    upgrade_plugin_mnet_functions($component);
                    $endcallback($component, true, $verbose);
                }
            }
        }

        if (empty($currmodule->version)) {
            $startcallback($component, true, $verbose);

        /// Execute install.xml (XMLDB) - must be present in all modules
            $DB->get_manager()->install_from_xmldb_file($fullmod.'/db/install.xml');

        /// Add record into modules table - may be needed in install.php already
            $module->id = $DB->insert_record('modules', $module);

        /// Post installation hook - optional
            if (file_exists("$fullmod/db/install.php")) {
                require_once("$fullmod/db/install.php");
                // Set installation running flag, we need to recover after exception or error
                set_config('installrunning', 1, $module->name);
                $post_install_function = 'xmldb_'.$module->name.'_install';;
                $post_install_function();
                unset_config('installrunning', $module->name);
            }

        /// Install various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            message_update_providers($component);
            upgrade_plugin_mnet_functions($component);

            purge_all_caches();
            $endcallback($component, true, $verbose);

        } else if ($currmodule->version < $module->version) {
        /// If versions say that we need to upgrade but no upgrade files are available, notify and continue
            $startcallback($component, false, $verbose);

            if (is_readable($fullmod.'/db/upgrade.php')) {
                require_once($fullmod.'/db/upgrade.php');  // defines new upgrading function
                $newupgrade_function = 'xmldb_'.$module->name.'_upgrade';
                $result = $newupgrade_function($currmodule->version, $module);
            } else {
                $result = true;
            }

            $currmodule = $DB->get_record('modules', array('name'=>$module->name));
            if ($currmodule->version < $module->version) {
                // store version if not already there
                upgrade_mod_savepoint($result, $module->version, $mod, false);
            }

        /// Upgrade various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            message_update_providers($component);
            upgrade_plugin_mnet_functions($component);

            purge_all_caches();

            $endcallback($component, false, $verbose);

        } else if ($currmodule->version > $module->version) {
            throw new downgrade_exception($component, $currmodule->version, $module->version);
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

    $blocks = get_plugin_list('block');

    foreach ($blocks as $blockname=>$fullblock) {

        if (is_null($first_install)) {
            $first_install = ($DB->count_records('block_instances') == 0);
        }

        if ($blockname == 'NEWBLOCK') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $component = 'block_'.$blockname;

        // check block dir is valid name
        $cblockname = strtolower($blockname);
        $cblockname = clean_param($cblockname, PARAM_SAFEDIR);
        $cblockname = str_replace('-', '', $cblockname);
        if ($blockname !== $cblockname) {
            throw new plugin_defective_exception($component, 'Invalid plugin directory name.');
        }

        if (!is_readable($fullblock.'/version.php')) {
            throw new plugin_defective_exception('block/'.$blockname, 'Missing version.php file.');
        }
        $plugin = new stdClass();
        $plugin->version = NULL;
        $plugin->cron    = 0;
        include($fullblock.'/version.php');
        $block = $plugin;

        // if plugin tells us it's full name we may check the location
        if (isset($block->component)) {
            if ($block->component !== $component) {
                throw new plugin_defective_exception($component, 'Plugin installed in wrong folder.');
            }
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

        if (empty($block->version)) {
            throw new plugin_defective_exception($component, 'Missing block version.');
        }

        $currblock = $DB->get_record('block', array('name'=>$block->name));

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
                    message_update_providers($component);
                    upgrade_plugin_mnet_functions($component);
                    $endcallback($component, true, $verbose);
                }
            }
        }

        if (empty($currblock->version)) { // block not installed yet, so install it
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

            if (file_exists($fullblock.'/db/install.php')) {
                require_once($fullblock.'/db/install.php');
                // Set installation running flag, we need to recover after exception or error
                set_config('installrunning', 1, 'block_'.$blockname);
                $post_install_function = 'xmldb_block_'.$blockname.'_install';;
                $post_install_function();
                unset_config('installrunning', 'block_'.$blockname);
            }

            $blocktitles[$block->name] = $blocktitle;

            // Install various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            message_update_providers($component);
            upgrade_plugin_mnet_functions($component);

            purge_all_caches();
            $endcallback($component, true, $verbose);

        } else if ($currblock->version < $block->version) {
            $startcallback($component, false, $verbose);

            if (is_readable($fullblock.'/db/upgrade.php')) {
                require_once($fullblock.'/db/upgrade.php');  // defines new upgrading function
                $newupgrade_function = 'xmldb_block_'.$blockname.'_upgrade';
                $result = $newupgrade_function($currblock->version, $block);
            } else {
                $result = true;
            }

            $currblock = $DB->get_record('block', array('name'=>$block->name));
            if ($currblock->version < $block->version) {
                // store version if not already there
                upgrade_block_savepoint($result, $block->version, $block->name, false);
            }

            if ($currblock->cron != $block->cron) {
                // update cron flag if needed
                $currblock->cron = $block->cron;
                $DB->update_record('block', $currblock);
            }

            // Upgrade various components
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            events_update_definition($component);
            message_update_providers($component);
            upgrade_plugin_mnet_functions($component);

            purge_all_caches();
            $endcallback($component, false, $verbose);

        } else if ($currblock->version > $block->version) {
            throw new downgrade_exception($component, $currblock->version, $block->version);
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

    $defpath = get_component_directory($component).'/db/log.php';

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
    global $DB;

    $defpath = get_component_directory($component).'/db/services.php';

    if (!file_exists($defpath)) {
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
        $functioncapabilities = key_exists('capabilities', $function)?$function['capabilities']:'';
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
        $dbfunction->capabilities = key_exists('capabilities', $function)?$function['capabilities']:'';
        $dbfunction->id = $DB->insert_record('external_functions', $dbfunction);
    }
    unset($functions);

    // now deal with services
    $dbservices = $DB->get_records('external_services', array('component'=>$component));
    foreach ($dbservices as $dbservice) {
        if (empty($services[$dbservice->name])) {
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
 * Delete all service and external functions information defined in the specified component.
 * @param string $component name of component (moodle, mod_assignment, etc.)
 * @return void
 */
function external_delete_descriptions($component) {
    global $DB;

    $params = array($component);

    $DB->delete_records_select('external_services_users', "externalserviceid IN (SELECT id FROM {external_services} WHERE component = ?)", $params);
    $DB->delete_records_select('external_services_functions', "externalserviceid IN (SELECT id FROM {external_services} WHERE component = ?)", $params);
    $DB->delete_records('external_services', array('component'=>$component));
    $DB->delete_records('external_functions', array('component'=>$component));
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
    $CFG->debug = DEBUG_DEVELOPER;

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

    list($plugintype, $pluginname) = normalize_component($plugin);
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

    } else if ($plugintype === 'mod') {
        try {
            $currentversion = $DB->get_field('modules', 'version', array('name'=>$pluginname));
            $currentversion = ($currentversion === false) ? null : $currentversion;
        } catch (Exception $ignored) {
        }
        $cd = get_component_directory($component);
        if (file_exists("$cd/version.php")) {
            $module = new stdClass();
            $module->version = null;
            include("$cd/version.php");
            $targetversion = $module->version;
        }

    } else if ($plugintype === 'block') {
        try {
            if ($block = $DB->get_record('block', array('name'=>$pluginname))) {
                $currentversion = $block->version;
            }
        } catch (Exception $ignored) {
        }
        $cd = get_component_directory($component);
        if (file_exists("$cd/version.php")) {
            $plugin = new stdClass();
            $plugin->version = null;
            include("$cd/version.php");
            $targetversion = $plugin->version;
        }

    } else {
        $pluginversion = get_config($component, 'version');
        if (!empty($pluginversion)) {
            $currentversion = $pluginversion;
        }
        $cd = get_component_directory($component);
        if (file_exists("$cd/version.php")) {
            $plugin = new stdClass();
            $plugin->version = null;
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
        register_shutdown_function('upgrade_finished_handler');
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

/**
 * @global object
 */
function print_upgrade_reload($url) {
    global $OUTPUT;

    echo "<br />";
    echo '<div class="continuebutton">';
    echo '<a href="'.$url.'" title="'.get_string('reload').'" ><img src="'.$OUTPUT->pix_url('i/reload') . '" alt="" /> '.get_string('reload').'</a>';
    echo '</div><br />';
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
function upgrade_language_pack($lang='') {
    global $CFG, $OUTPUT;

    get_string_manager()->reset_caches();

    if (empty($lang)) {
        $lang = current_language();
    }

    if ($lang == 'en') {
        return true;  // Nothing to do
    }

    upgrade_started(false);
    echo $OUTPUT->heading(get_string('langimport', 'admin').': '.$lang);

    @mkdir ($CFG->dataroot.'/temp/');    //make it in case it's a fresh install, it might not be there
    @mkdir ($CFG->dataroot.'/lang/');

    require_once($CFG->libdir.'/componentlib.class.php');

    $installer = new lang_installer($lang);
    $results = $installer->run();
    foreach ($results as $langcode => $langstatus) {
        switch ($langstatus) {
        case lang_installer::RESULT_DOWNLOADERROR:
            echo $OUTPUT->notification($langcode . '.zip');
            break;
        case lang_installer::RESULT_INSTALLED:
            echo $OUTPUT->notification(get_string('langpackinstalled', 'admin', $langcode), 'notifysuccess');
            break;
        case lang_installer::RESULT_UPTODATE:
            echo $OUTPUT->notification(get_string('langpackuptodate', 'admin', $langcode), 'notifysuccess');
            break;
        }
    }

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

    try {
        set_time_limit(600);
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
        message_update_providers('moodle');

        // Write default settings unconditionally
        admin_apply_default_settings(NULL, true);

        print_upgrade_part_end(null, true, $verbose);
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
    global $CFG;

    raise_memory_limit(MEMORY_EXTRA);

    require_once($CFG->libdir.'/db/upgrade.php');    // Defines upgrades

    try {
        // Reset caches before any output
        purge_all_caches();

        // Upgrade current language pack if we can
        if (empty($CFG->skiplangupgrade)) {
            if (get_string_manager()->translation_exists(current_language())) {
                upgrade_language_pack(false);
            }
        }

        print_upgrade_part_start('moodle', false, $verbose);

        // one time special local migration pre 2.0 upgrade script
        if ($CFG->version < 2007101600) {
            $pre20upgradefile = "$CFG->dirroot/local/upgrade_pre20.php";
            if (file_exists($pre20upgradefile)) {
                set_time_limit(0);
                require($pre20upgradefile);
                // reset upgrade timeout to default
                upgrade_set_timeout();
            }
        }

        $result = xmldb_main_upgrade($CFG->version);
        if ($version > $CFG->version) {
            // store version if not already there
            upgrade_main_savepoint($result, $version, false);
        }

        // perform all other component upgrade routines
        update_capabilities('moodle');
        log_update_descriptions('moodle');
        external_update_descriptions('moodle');
        events_update_definition('moodle');
        message_update_providers('moodle');

        // Reset caches again, just to be sure
        purge_all_caches();

        // Clean up contexts - more and more stuff depends on existence of paths and contexts
        cleanup_contexts();
        create_contexts();
        build_context_path();
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        mark_context_dirty($syscontext->path);

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
        $plugintypes = get_plugin_types();
        foreach ($plugintypes as $type=>$location) {
            upgrade_plugins($type, 'print_upgrade_part_start', 'print_upgrade_part_end', $verbose);
        }
    } catch (Exception $ex) {
        upgrade_handle_exception($ex);
    }
}

/**
 * Checks if the main tables have been installed yet or not.
 * @return bool
 */
function core_tables_exist() {
    global $DB;

    if (!$tables = $DB->get_tables() ) {    // No tables yet at all.
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

    list($type, $plugin) = explode('_', $component);
    $path = get_plugin_directory($type, $plugin);

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
    $proto = array_pop($function->getPrototypes());
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
