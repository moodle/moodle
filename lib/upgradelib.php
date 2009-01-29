<?php  //$Id$

/**
 * upgradelib.php - Contains functions used during upgrade
 *
 * @author Martin Dougiamas and many others
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

define('UPGRADE_LOG_NORMAL', 0);
define('UPGRADE_LOG_NOTICE', 1);
define('UPGRADE_LOG_ERROR', 2);

/**
 * Insert or update log display entry. Entry may already exist.
 * $module, $action must be unique
 *
 * @param string $module
 * @param string $action
 * @param string $mtable
 * @param string $field
 * @return void
 *
 */
function update_log_display_entry($module, $action, $mtable, $field) {
    global $DB;

    if ($type = $DB->get_record('log_display', array('module'=>$module, 'action'=>$action))) {
        $type->mtable = $mtable;
        $type->field  = $field;
        $DB->update_record('log_display', $type);

    } else {
        $type = new object();
        $type->module = $module;
        $type->action = $action;
        $type->mtable = $mtable;
        $type->field  = $field;

        $DB->insert_record('log_display', $type, false);
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
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_main_savepoint($result, $version, $allowabort=true) {
    global $CFG;

    if ($result) {
        if ($CFG->version >= $version) {
            // something really wrong is going on in main upgrade script
            print_error('cannotdowngrade', 'debug', '', (object)array('oldversion'=>$CFG->version, 'newversion'=>$version));
        }
        set_config('version', $version);
    } else {
        error("Upgrade savepoint: Error during main upgrade to version $version"); // TODO: localise
    }

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
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $modname name of module
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_mod_savepoint($result, $version, $modname, $allowabort=true) {
    global $DB;

    if (!$module = $DB->get_record('modules', array('name'=>$modname))) {
        print_error('modulenotexist', 'debug', '', $modname);
    }

    if ($result) {
        if ($module->version >= $version) {
            // something really wrong is going on in upgrade script
            print_error('cannotdowngrade', 'debug', '', (object)array('oldversion'=>$module->version, 'newversion'=>$version));
        }
        $module->version = $version;
        $DB->update_record('modules', $module);
    } else {
        error("Upgrade savepoint: Error during mod upgrade to version $version"); // TODO: localise
    }

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
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $blockname name of block
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_blocks_savepoint($result, $version, $blockname, $allowabort=true) {
    global $DB;

    if (!$block = $DB->get_record('block', array('name'=>$blockname))) {
        print_error('blocknotexist', 'debug', '', $blockname);
    }

    if ($result) {
        if ($block->version >= $version) {
            // something really wrong is going on in upgrade script
            print_error('cannotdowngrade', 'debug', '', (object)array('oldversion'=>$block->version, 'newversion'=>$version));
        }
        $block->version = $version;
        $DB->update_record('block', $block);
    } else {
        error("Upgrade savepoint: Error during mod upgrade to version $version"); // TODO: localise
    }

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
function upgrade_plugin_savepoint($result, $version, $type, $dir, $allowabort=true) {
    if ($result) {
        $fullname = $type . '_' . $dir;
        $installedversion = get_config($fullname, 'version');
        if ($installedversion >= $version) {
            // Something really wrong is going on in the upgrade script
            $a = new stdClass;
            $a->oldversion = $installedversion;
            $a->newversion = $version;
            print_error('cannotdowngrade', 'debug', '', $a);
        }
        set_config('version', $version, $fullname);
    } else {
        error("Upgrade savepoint: Error during mod upgrade to version $version"); // TODO: localise
    }

    // Reset upgrade timeout to default
    upgrade_set_timeout();

    // This is a safe place to stop upgrades if user aborts page loading
    if ($allowabort and connection_aborted()) {
        die;
    }
}


/**
 * Upgrade plugins
 *
 * @uses $CFG
 * @param string $type The type of plugins that should be updated (e.g. 'enrol', 'qtype')
 * @param string $dir  The directory where the plugins are located (e.g. 'question/questiontypes')
 * @param string $return The url to prompt the user to continue to
 */
function upgrade_plugins($type, $dir) {
    global $CFG, $DB;

/// special cases
    if ($type === 'mod') {
        return upgrade_activity_modules();
    } else if ($type === 'blocks') {
        return upgrade_blocks_plugins();
    }

    $plugs = get_list_of_plugins($dir);
    $updated_plugins = false;
    $strpluginsetup  = get_string('pluginsetup');

    foreach ($plugs as $plug) {

        $fullplug = $CFG->dirroot .'/'.$dir.'/'. $plug;

        unset($plugin);

        if (is_readable($fullplug .'/version.php')) {
            include($fullplug .'/version.php');  // defines $plugin with version etc
        } else {
            continue;                              // Nothing to do.
        }

        $newupgrade = false;
        if (is_readable($fullplug . '/db/upgrade.php')) {
            include_once($fullplug . '/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if (!isset($plugin)) {
            continue;
        }

        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                $info = new object();
                $info->pluginname = $plug;
                $info->pluginversion  = $plugin->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $plugin->requires;
                upgrade_started();
                notify(get_string('pluginrequirementsnotmet', 'error', $info));
                $updated_plugins = true;
                continue;
            }
        }

        $plugin->name = $plug;   // The name MUST match the directory
        $plugin->fullname = $type.'_'.$plug;   // The name MUST match the directory

        $installedversion = get_config($plugin->fullname, 'version');

        if ($installedversion === false) {
            set_config('version', 0, $plugin->fullname);
        }

        if ($installedversion == $plugin->version) {
            // do nothing
        } else if ($installedversion < $plugin->version) {
            $updated_plugins = true;
            upgrade_started();
            print_heading($dir.'/'. $plugin->name .' plugin needs upgrading');
            @set_time_limit(0);  // To allow slow databases to complete the long SQL

            if ($installedversion == 0) {    // It's a new install of this plugin
            /// Both old .sql files and new install.xml are supported
            /// but we priorize install.xml (XMLDB) if present
                if (file_exists($fullplug . '/db/install.xml')) {
                    $DB->get_manager()->install_from_xmldb_file($fullplug . '/db/install.xml'); //New method
                }
                $status = true;
            /// Continue with the instalation, roles and other stuff
                if ($status) {
                /// OK so far, now update the plugins record
                    set_config('version', $plugin->version, $plugin->fullname);

                /// Install capabilities
                    update_capabilities($type.'/'.$plug);

                /// Install events
                    events_update_definition($type.'/'.$plug);

                /// Install message providers
                    message_update_providers($type.'/'.$plug);

                /// Run local install function if there is one
                    if (is_readable($fullplug . '/lib.php')) {
                        include_once($fullplug . '/lib.php');
                        $installfunction = $plugin->name.'_install';
                        if (function_exists($installfunction)) {
                            if (! $installfunction() ) {
                                notify('Encountered a problem running install function for '.$module->name.'!');
                            }
                        }
                    }

                    notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                } else {
                    notify('Installing '. $plugin->name .' FAILED!');
                }
            } else {                            // Upgrade existing install
            /// Run the upgrade function for the plugin.
                $newupgrade_function = 'xmldb_' .$plugin->fullname .'_upgrade';
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function)) {
                    $newupgrade_status = $newupgrade_function($installedversion);
                } else if ($newupgrade) {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $fullplug . '/db/upgrade.php');
                }
            /// Now analyze upgrade results
                if ($newupgrade_status) {    // No upgrading failed
                /// OK so far, now update the plugins record
                    set_config('version', $plugin->version, $plugin->fullname);
                    update_capabilities($type.'/'.$plug);
                /// Update events
                    events_update_definition($type.'/'.$plug);

                /// Update message providers
                    message_update_providers($type.'/'.$plug);

                    notify(get_string('modulesuccess', '', $plugin->name), 'notifysuccess');
                } else {
                    notify('Upgrading '. $plugin->name .' from '. $installedversion .' to '. $plugin->version .' FAILED!');
                }
            }
            print_upgrade_separator();
        } else {
            print_error('cannotdowngrade', 'debug', '', (object)array('oldversion'=>$installedversion, 'newversion'=>$plugin->version));
        }
    }

    return $updated_plugins;
}

/**
 * Find and check all modules and load them up or upgrade them if necessary
 */
function upgrade_activity_modules() {
    global $CFG, $DB;

    if (!$mods = get_list_of_plugins('mod') ) {
        print_error('nomodules', 'debug');
    }

    $strmodulesetup  = get_string('modulesetup');

    foreach ($mods as $mod) {

        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullmod = $CFG->dirroot .'/mod/'. $mod;

        unset($module);


        if (is_readable($fullmod .'/version.php')) {
            require($fullmod .'/version.php');  // defines $module with version etc
        } else {
            error('Module '. $mod .': '. $fullmod .'/version.php was not readable'); // TODO: localise
        }

        $newupgrade = false;
        if ( is_readable($fullmod . '/db/upgrade.php')) {
            include_once($fullmod . '/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if (!isset($module)) {
            continue;
        }

        if (!empty($module->requires)) {
            if ($module->requires > $CFG->version) {
                $info = new object();
                $info->modulename = $mod;
                $info->moduleversion  = $module->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $module->requires;
                upgrade_started();
                notify(get_string('modulerequirementsnotmet', 'error', $info));
                continue;
            }
        }

        $module->name = $mod;   // The name MUST match the directory

        include_once($fullmod.'/lib.php');  // defines upgrading and/or installing functions

        if ($currmodule = $DB->get_record('modules', array('name'=>$module->name))) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
            /// If versions say that we need to upgrade but no upgrade files are available, notify and continue
                if (!$newupgrade) {
                    notify('Upgrade file ' . $mod . ': ' . $fullmod . '/db/upgrade.php is not readable');
                    continue;
                }
                upgrade_started();

                print_heading($module->name .' module needs upgrading');

            /// Run de old and new upgrade functions for the module
                $newupgrade_function = 'xmldb_' . $module->name . '_upgrade';

            /// Then, the new function if exists and the old one was ok
                $newupgrade_status = true;
                if ($newupgrade && function_exists($newupgrade_function)) {
                    $newupgrade_status = $newupgrade_function($currmodule->version, $module);
                } else if ($newupgrade) {
                    notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                             $mod . ': ' . $fullmod . '/db/upgrade.php');
                }
            /// Now analyze upgrade results
                if ($newupgrade_status) {    // No upgrading failed
                    // OK so far, now update the modules record
                    $module->id = $currmodule->id;
                    $DB->update_record('modules', $module);
                    remove_dir($CFG->dataroot . '/cache', true); // flush cache
                    notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
                    print_upgrade_separator();
                } else {
                    notify('Upgrading '. $module->name .' from '. $currmodule->version .' to '. $module->version .' FAILED!');
                }

            /// Update the capabilities table?
                update_capabilities('mod/'.$module->name);

            /// Update events
                events_update_definition('mod/'.$module->name);

            /// Update message providers
                message_update_providers('mod/'.$module->name);

            } else {
                print_error('cannotdowngrade', 'debug', '', (object)array('oldversion'=>$currmodule->version, 'newversion'=>$module->version));
            }

        } else {    // module not installed yet, so install it
            upgrade_started();
            print_heading($module->name);

        /// Execute install.xml (XMLDB) - must be present
            $DB->get_manager()->install_from_xmldb_file($fullmod . '/db/install.xml'); //New method

        /// Post installation hook - optional
            if (file_exists("$fullmod/db/install.php")) {
                require_once("$fullmod/db/install.php");
                $post_install_function = 'xmldb_'.$module->name.'_install';;
                $post_install_function();
            }

        /// Continue with the installation, roles and other stuff
            $module->id = $DB->insert_record('modules', $module);

        /// Capabilities
            update_capabilities('mod/'.$module->name);

        /// Events
            events_update_definition('mod/'.$module->name);

        /// Message providers
            message_update_providers('mod/'.$module->name);

            notify(get_string('modulesuccess', '', $module->name), 'notifysuccess');
            print_upgrade_separator();
        }

    /// Check submodules of this module if necessary

        $submoduleupgrade = $module->name.'_upgrade_submodules';
        if (function_exists($submoduleupgrade)) {
            $submoduleupgrade();
        }

    /// Run any defaults or final code that is necessary for this module

        if ( is_readable($fullmod .'/defaults.php')) {
            // Insert default values for any important configuration variables
            unset($defaults);
            include($fullmod .'/defaults.php'); // include here means execute, not library include
            if (!empty($defaults)) {
                if (!empty($defaults['_use_config_plugins'])) {
                    unset($defaults['_use_config_plugins']);
                    $localcfg = get_config($module->name);
                    foreach ($defaults as $name => $value) {
                        if (!isset($localcfg->$name)) {
                            set_config($name, $value, $module->name);
                        }
                    }
                } else {
                    foreach ($defaults as $name => $value) {
                        if (!isset($CFG->$name)) {
                            set_config($name, $value);
                        }
                    }
                }
            }
        }
    }
}


////////////////////////////////////////////////
/// upgrade logging functions
////////////////////////////////////////////////

function upgrade_handle_exception($exception, $plugin=null) {
    //TODO
}

/**
 * Adds log entry into upgrade_log table
 *
 * @param int $type UPGRADE_LOG_NORMAL, UPGRADE_LOG_NOTICE or UPGRADE_LOG_ERROR
 * @param string $plugin plugin or null if main
 * @param string $info short description text of log entry
 * @param string $details long problem description
 * @param string $backtrace string used for errors only
 * @return void
 */
function upgrade_log($type, $plugin, $info, $details=null, $backtrace=null) {
    global $DB, $USER, $CFG;

    static $plugins = null;
    if (!$plugins) {
        $plugins = get_plugin_types();
    }

    $version = null;

    //first try to find out current version number
    if (is_null($plugin)) {
        //main
        $version = $CFG->version;

    } else if (strpos('mod/', $plugin) === 0) {
        try {
            $modname = substr($plugin, strlen('mod/'));
            $version = $DB->get_field('modules', 'version', array('name'=>$modname));
            $version = $version === false ? null : $version;
        } catch (Exception $ignored) {
        }

    } else if (strpos('blocks/', $plugin) === 0) {
        try {
            $blockname = substr($plugin, strlen('blocks/'));
            if ($block = $DB->get_record('block', array('name'=>$blockname))) {
                $version = $block->version;
            }
        } catch (Exception $ignored) {
        }
    }

    $log = new object();
    $log->type         = $type;
    $log->plugin       = $plugin;
    $log->version      = $version;
    $log->info         = $info;
    $log->details      = $details;
    $log->backtrace    = $backtrace;
    $log->userid       = $USER->id;
    $log->timemodified = time();

    try {
        $DB->insert_record('upgrade_log', $log);
    } catch (Exception $ignored) {
        // possible during install or upgrade
    }
}

/**
 * Marks start of upgrade, blocks any other access to site.
 * The upgrade is finished at the end of script or after timeout.
 */
function upgrade_started($preinstall=false) {
    global $CFG, $DB;

    static $started = false;

    if ($preinstall) {
        ignore_user_abort(true);
        upgrade_setup_debug(true);

    } else if ($started) {
        upgrade_set_timeout(120);

    } else {
        if (!CLI_SCRIPT and !defined('HEADER_PRINTED')) {
            $strupgrade  = get_string('upgradingversion', 'admin');

            print_header($strupgrade, $strupgrade,
                build_navigation(array(array('name' => $strupgrade, 'link' => null, 'type' => 'misc'))), '',
                upgrade_get_javascript(), false, '&nbsp;', '&nbsp;');
        }

        ignore_user_abort(true);
        register_shutdown_function('upgrade_finished_handler');
        upgrade_setup_debug(true);
        set_config('upgraderunning', time()+300);
        $started = true;
    }
}

/**
 * Internal function - executed if upgrade interruped.
 */
function upgrade_finished_handler() {
    upgrade_finished();
}

/**
 * Indicates upgrade is finished.
 *
 * This function may be called repeatedly.
 */
function upgrade_finished($continueurl=null) {
    global $CFG, $DB;

    if (!empty($CFG->upgraderunning)) {
        unset_config('upgraderunning');
        upgrade_setup_debug(false);
        ignore_user_abort(false);
        if ($continueurl) {
            print_continue($continueurl);
            print_footer('none');
            die;
        }
    }
}

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


function upgrade_get_javascript() {
    global $CFG;

    return '<script type="text/javascript" src="'.$CFG->wwwroot.'/lib/scroll_to_errors.js"></script>';
}


/**
 * Try to upgrade the given language pack (or current language)
 * If it doesn't work, fail silently and return false
 */
function upgrade_language_pack($lang='') {
    global $CFG;

    if (empty($lang)) {
        $lang = current_language();
    }

    if ($lang == 'en_utf8') {
        return true;  // Nothing to do
    }

    notify(get_string('langimport', 'admin').': '.$lang.' ... ', 'notifysuccess');

    @mkdir ($CFG->dataroot.'/temp/');    //make it in case it's a fresh install, it might not be there
    @mkdir ($CFG->dataroot.'/lang/');

    require_once($CFG->libdir.'/componentlib.class.php');

    if ($cd = new component_installer('http://download.moodle.org', 'lang16', $lang.'.zip', 'languages.md5', 'lang')) {
        $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)

        if ($status == COMPONENT_INSTALLED) {
            debugging('Downloading successful: '.$lang);
            @unlink($CFG->dataroot.'/cache/languages');
            return true;
        }
    }

    return false;
}
