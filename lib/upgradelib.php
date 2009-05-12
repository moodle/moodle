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
define('UPGRADE_LOG_ERROR',  2);

/**
 * Exception indicating unknown error during upgrade.
 */
class upgrade_exception extends moodle_exception {
    function __construct($plugin, $version) {
        global $CFG;
        $a = (object)array('plugin'=>$plugin, 'version'=>$version);
        parent::__construct('upgradeerror', 'error', "$CFG->wwwroot/$CFG->admin/index.php", $a);
    }
}

/**
 * Exception indicating downgrade error during upgrade.
 */
class downgrade_exception extends moodle_exception {
    function __construct($plugin, $oldversion, $newversion) {
        global $CFG;
        $plugin = is_null($plugin) ? 'moodle' : $plugin;
        $a = (object)array('plugin'=>$plugin, 'oldversion'=>$oldversion, 'newversion'=>$newversion);
        parent::__construct('cannotdowngrade', 'debug', "$CFG->wwwroot/$CFG->admin/index.php", $a);
    }
}

class upgrade_requires_exception extends moodle_exception {
    function __construct($plugin, $pluginversion, $currentmoodle, $requiremoodle) {
        global $CFG;
        $a = new object();
        $a->pluginname     = $plugin;
        $a->pluginversion  = $pluginversion;
        $a->currentmoodle  = $currentmoodle;
        $a->requiremoodle  = $currentmoodle;
        parent::__construct('pluginrequirementsnotmet', 'error', "$CFG->wwwroot/$CFG->admin/index.php", $a);
    }
}

class plugin_defective_exception extends moodle_exception {
    function __construct($plugin, $details) {
        global $CFG;
        parent::__construct('detectedbrokenplugin', 'error', "$CFG->wwwroot/$CFG->admin/index.php", $plugin, $details);
    }
}

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

    if (!$result) {
        throw new upgrade_exception('moodle core', $version);
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
 * @param bool $result false if upgrade step failed, true if completed
 * @param string or float $version main version
 * @param string $modname name of module
 * @param bool $allowabort allow user to abort script execution here
 * @return void
 */
function upgrade_mod_savepoint($result, $version, $modname, $allowabort=true) {
    global $DB;

    if (!$result) {
        throw new upgrade_exception("mod/$modname", $version);
    }

    if (!$module = $DB->get_record('modules', array('name'=>$modname))) {
        print_error('modulenotexist', 'debug', '', $modname);
    }

    if ($module->version >= $version) {
        // something really wrong is going on in upgrade script
        throw new downgrade_exception("mod/$modname", $module->version, $version);
    }
    $module->version = $version;
    $DB->update_record('modules', $module);
    upgrade_log(UPGRADE_LOG_NORMAL, "mod/$modname", 'Upgrade savepoint reached');

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
function upgrade_block_savepoint($result, $version, $blockname, $allowabort=true) {
    global $DB;

    if (!$result) {
        throw new upgrade_exception("blocks/$blockname", $version);
    }

    if (!$block = $DB->get_record('block', array('name'=>$blockname))) {
        print_error('blocknotexist', 'debug', '', $blockname);
    }

    if ($block->version >= $version) {
        // something really wrong is going on in upgrade script
        throw new downgrade_exception("blocks/$blockname", $block->version, $version);
    }
    $block->version = $version;
    $DB->update_record('block', $block);
    upgrade_log(UPGRADE_LOG_NORMAL, "blocks/$blockname", 'Upgrade savepoint reached');

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
    if (!$result) {
        throw new upgrade_exception("$type/$dir", $version);
    }

    $fullname = $type.'_'.$dir;
    $component = $type.'/'.$dir;

    $installedversion = get_config($fullname, 'version');
    if ($installedversion >= $version) {
        // Something really wrong is going on in the upgrade script
        throw new downgrade_exception($component, $installedversion, $version);
    }
    set_config('version', $version, $fullname);
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
 *
 * @uses $CFG
 * @param string $type The type of plugins that should be updated (e.g. 'enrol', 'qtype')
 * @param string $dir  The directory where the plugins are located (e.g. 'question/questiontypes')
 * @param string $return The url to prompt the user to continue to
 */
function upgrade_plugins($type, $dir, $startcallback, $endcallback) {
    global $CFG, $DB;

/// special cases
    if ($type === 'mod') {
        return upgrade_plugins_modules($startcallback, $endcallback);
    } else if ($type === 'block') {
        return upgrade_plugins_blocks($startcallback, $endcallback);
    }

    $plugs = get_list_of_plugins($dir);

    foreach ($plugs as $plug) {

        $fullplug  = $CFG->dirroot.'/'.$dir.'/'.$plug;
        $component = $type.'/'.$plug; // standardised plugin name

        if (!is_readable($fullplug.'/version.php')) {
            continue;
        }

        $plugin = new object();
        require($fullplug.'/version.php');  // defines $plugin with version etc

        if (empty($plugin->version)) {
            throw new plugin_defective_exception($component, 'Missing version value in version.php');
        }

        $plugin->name     = $plug;   // The name MUST match the directory
        $plugin->fullname = $type.'_'.$plug;   // The name MUST match the directory


        if (!empty($plugin->requires)) {
            if ($plugin->requires > $CFG->version) {
                throw new upgrade_requires_exception($component, $plugin->version, $CFG->version, $plugin->requires);
            }
        }

        $installedversion = get_config($plugin->fullname, 'version');

        if (empty($installedversion)) { // new installation
            $startcallback($component, true);

        /// Install tables if defined
            if (file_exists($fullplug.'/db/install.xml')) {
                $DB->get_manager()->install_from_xmldb_file($fullplug.'/db/install.xml');
            }
        /// execute post install file
            if (file_exists($fullplug.'/db/install.php')) {
                require_once($fullplug.'/db/install.php');
                $post_install_function = 'xmldb_'.$plugin->fullname.'_install';;
                $post_install_function();
            }

        /// store version
            upgrade_plugin_savepoint(true, $plugin->version, $type, $plug, false);

        /// Install various components
            update_capabilities($component);
            events_update_definition($component);
            message_update_providers($component);

            $endcallback($component, true);

        } else if ($installedversion < $plugin->version) { // upgrade
        /// Run the upgrade function for the plugin.
            $startcallback($component, false);

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
            events_update_definition($component);
            message_update_providers($component);

            $endcallback($component, false);

        } else if ($installedversion > $plugin->version) {
            throw new downgrade_exception($component, $installedversion, $plugin->version);
        }
    }
}

/**
 * Find and check all modules and load them up or upgrade them if necessary
 */
function upgrade_plugins_modules($startcallback, $endcallback) {
    global $CFG, $DB;

    $mods = get_list_of_plugins('mod');

    foreach ($mods as $mod) {

        if ($mod == 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullmod   = $CFG->dirroot.'/mod/'.$mod;
        $component = 'mod/'.$mod;

        if (!is_readable($fullmod.'/version.php')) {
            throw new plugin_defective_exception($component, 'Missing version.php');
        }

        $module = new object();
        require($fullmod .'/version.php');  // defines $module with version etc

        if (empty($module->version)) {
            if (isset($module->version)) {
                // Version is empty but is set - it means its value is 0 or ''. Let us skip such module.
                // This is inteded for developers so they can work on the early stages of the module.
                continue;
            }
            throw new plugin_defective_exception($component, 'Missing version value in version.php');
        }

        if (!empty($module->requires)) {
            if ($module->requires > $CFG->version) {
                throw new upgrade_requires_exception($component, $module->version, $CFG->version, $module->requires);
            }
        }

        $module->name = $mod;   // The name MUST match the directory

        $currmodule = $DB->get_record('modules', array('name'=>$module->name));

        if (empty($currmodule->version)) {
            $startcallback($component, true);

        /// Execute install.xml (XMLDB) - must be present in all modules
            $DB->get_manager()->install_from_xmldb_file($fullmod.'/db/install.xml');

        /// Post installation hook - optional
            if (file_exists("$fullmod/db/install.php")) {
                require_once("$fullmod/db/install.php");
                $post_install_function = 'xmldb_'.$module->name.'_install';;
                $post_install_function();
            }

        /// Continue with the installation, roles and other stuff
            $module->id = $DB->insert_record('modules', $module);

        /// Install various components
            update_capabilities($component);
            events_update_definition($component);
            message_update_providers($component);

            $endcallback($component, true);

        } else if ($currmodule->version < $module->version) {
        /// If versions say that we need to upgrade but no upgrade files are available, notify and continue
            $startcallback($component, false);

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
            events_update_definition($component);
            message_update_providers($component);

            remove_dir($CFG->dataroot.'/cache', true); // flush cache

            $endcallback($component, false);

        } else if ($currmodule->version > $module->version) {
            throw new downgrade_exception($component, $currmodule->version, $module->version);
        }
    }
}


/**
 * This function finds all available blocks and install them
 * into blocks table or do all the upgrade process if newer.
 */
function upgrade_plugins_blocks($startcallback, $endcallback) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/blocks/moodleblock.class.php');

    $blocktitles   = array(); // we do not want duplicate titles

    //Is this a first install
    $first_install = null;

    $blocks = get_list_of_plugins('blocks');

    foreach ($blocks as $blockname) {

        if (is_null($first_install)) {
            $first_install = ($DB->count_records('block') == 0);
        }

        if ($blockname == 'NEWBLOCK') {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullblock = $CFG->dirroot.'/blocks/'.$blockname;
        $component = 'block/'.$blockname;

        if (!is_readable($fullblock.'/block_'.$blockname.'.php')) {
            throw new plugin_defective_exception('block/'.$blockname, 'Missing main block class file.');
        }
        require_once($fullblock.'/block_'.$blockname.'.php');

        $classname = 'block_'.$blockname;

        if (!class_exists($classname)) {
            throw new plugin_defective_exception($component, 'Can not load main class.');
        }

        $blockobj    = new $classname;   // This is what we 'll be testing
        $blocktitle  = $blockobj->get_title();

        // OK, it's as we all hoped. For further tests, the object will do them itself.
        if (!$blockobj->_self_test()) {
            throw new plugin_defective_exception($component, 'Self test failed.');
        }

        $block           = new object();     // This may be used to update the db below
        $block->name     = $blockname;   // The name MUST match the directory
        $block->version  = $blockobj->get_version();
        $block->cron     = !empty($blockobj->cron) ? $blockobj->cron : 0;
        $block->multiple = $blockobj->instance_allow_multiple() ? 1 : 0;

        if (empty($block->version)) {
            throw new plugin_defective_exception($component, 'Missing block version.');
        }

        $currblock = $DB->get_record('block', array('name'=>$block->name));

        if (empty($currblock->version)) { // block not installed yet, so install it
            // If it allows multiples, start with it enabled

            $conflictblock = array_search($blocktitle, $blocktitles);
            if ($conflictblock !== false) {
                // Duplicate block titles are not allowed, they confuse people
                // AND PHP's associative arrays ;)
                throw new plugin_defective_exception($component, get_string('blocknameconflict', '', (object)array('name'=>$block->name, 'conflict'=>$conflictblock)));
            }
            $startcallback($component, true);

            if (file_exists($fullblock.'/db/install.xml')) {
                $DB->get_manager()->install_from_xmldb_file($fullblock.'/db/install.xml');
            }
            $block->id = $DB->insert_record('block', $block);

            if (file_exists($fullblock.'/db/install.php')) {
                require_once($fullblock.'/db/install.php');
                $post_install_function = 'xmldb_block_'.$blockname.'_install';;
                $post_install_function();
            }

            $blocktitles[$block->name] = $blocktitle;

            // Install various components
            update_capabilities($component);
            events_update_definition($component);
            message_update_providers($component);

            $endcallback($component, true);

        } else if ($currblock->version < $block->version) {
            $startcallback($component, false);

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

            // Upgrade various componebts
            events_update_definition($component);
            update_capabilities($component);
            message_update_providers($component);

            $endcallback($component, false);

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
 * This function checks to see whether local database customisations are up-to-date
 * by comparing $CFG->local_version to the variable $local_version defined in
 * local/version.php. If not, it looks for a function called 'xmldb_local_upgrade'
 * in a file called 'local/db/upgrade.php', and if it's there calls it with the
 * appropiate $oldversion parameter. Then it updates $CFG->local_version.
 *
 * @uses $CFG
 */
function upgrade_local_db($startcallback, $endcallback) {
    global $CFG, $DB;

    // if we don't have code version, just return false
    if (!file_exists($CFG->dirroot.'/local/version.php')) {
        return;
    }

    $local_version = null;
    require($CFG->dirroot.'/local/version.php');  // Get code versions

    if (empty($CFG->local_version)) { // install
        $startcallback('local', true);

        if (file_exists($CFG->dirroot.'/local/db/install.php')) {
            require_once($CFG->dirroot.'/local/db/install.php');
            xmldb_local_install();
        }
        set_config('local_version', $local_version);

        /// Install various components
        events_update_definition('local');
        update_capabilities('local');
        message_update_providers('local');

        $endcallback('local', true);

    } else if ($local_version > $CFG->local_version) { // upgrade!
        $startcallback('local', false);

        if (file_exists($CFG->dirroot.'/local/db/upgrade.php')) {
            require_once($CFG->dirroot.'/local/db/upgrade.php');
            xmldb_local_upgrade($CFG->local_version);
        }
        set_config('local_version', $local_version);

        /// Upgrade various components
        events_update_definition('local');
        update_capabilities('local');
        message_update_providers('local');

        $endcallback('local', false);

    } else if ($local_version < $CFG->local_version) {
        throw new downgrade_exception('local', $CFG->local_version, $local_version);
    }
}


////////////////////////////////////////////////
/// upgrade logging functions
////////////////////////////////////////////////

function upgrade_handle_exception($ex, $plugin=null) {
    global $CFG;

    if ($ex instanceof moodle_exception) {
        $details = get_string($ex->errorcode, $ex->module, $ex->a)."<br />debugging:".$ex->debuginfo;
    } else {
        $details = get_string('generalexceptionmessage', 'error', $ex->getMessage());
    }
    $info = "Exception: ".get_class($ex);
    $backtrace = $ex->getTrace();
    $place = array('file'=>$ex->getFile(), 'line'=>$ex->getLine(), 'exception'=>get_class($ex));
    array_unshift($backtrace, $place);

    /// first log upgrade error
    upgrade_log(UPGRADE_LOG_ERROR, $plugin, $info, $details, $backtrace);

    // always turn on debugging - admins need to know what is going on
    $CFG->debug = DEBUG_DEVELOPER;

    // now print the exception info as usually
    if ($ex instanceof moodle_exception) {
        _print_normal_error($ex->errorcode, $ex->module, $ex->a, $ex->link, $backtrace, $ex->debuginfo);
    } else {
        _print_normal_error('generalexceptionmessage', 'error', $ex->getMessage(), '', $backtrace);
    }

    die; // not reached
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

    $plugin = ($plugin==='moodle') ? null : $plugin;

    $backtrace = print_backtrace($backtrace, true);

    $version = null;

    //first try to find out current version number
    if (empty($plugin) or $plugin === 'moodle') {
        //main
        $version = $CFG->version;

    } else if ($plugin === 'local') {
        //customisation
        $version = $CFG->local_version;

    } else if (strpos($plugin, 'mod/') === 0) {
        try {
            $modname = substr($plugin, strlen('mod/'));
            $version = $DB->get_field('modules', 'version', array('name'=>$modname));
            $version = ($version === false) ? null : $version;
        } catch (Exception $ignored) {
        }

    } else if (strpos($plugin, 'block/') === 0) {
        try {
            $blockname = substr($plugin, strlen('block/'));
            if ($block = $DB->get_record('block', array('name'=>$blockname))) {
                $version = $block->version;
            }
        } catch (Exception $ignored) {
        }

    } else {
        $pluginversion = get_config(str_replace('/', '_', $plugin), 'version');
        if (!empty($pluginversion)) {
            $version = $pluginversion;
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
        // possible during install or 2.0 upgrade
    }
}

/**
 * Marks start of upgrade, blocks any other access to site.
 * The upgrade is finished at the end of script or after timeout.
 */
function upgrade_started($preinstall=false) {
    global $CFG, $DB, $PAGE;

    static $started = false;

    if ($preinstall) {
        ignore_user_abort(true);
        upgrade_setup_debug(true);

    } else if ($started) {
        upgrade_set_timeout(120);

    } else {
        if (!CLI_SCRIPT and !$PAGE->headerprinted) {
            $strupgrade  = get_string('upgradingversion', 'admin');

            print_header($strupgrade.' - Moodle '.$CFG->target_release, $strupgrade,
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
            print_footer('upgrade');
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

function print_upgrade_reload($url) {
    global $CFG;

    echo "<br />";
    echo '<div class="continuebutton">';
    echo '<a href="'.$url.'" title="'.get_string('reload').'" ><img src="'.$CFG->pixpath.'/i/reload.gif" alt="" /> '.get_string('reload').'</a>';
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
 * @param bool $installation true if installation, false menas upgrade
 */
function print_upgrade_part_start($plugin, $installation) {
    if (empty($plugin) or $plugin == 'moodle') {
        upgrade_started($installation); // does not store upgrade running flag yet
        print_heading(get_string('coresystem'));
    } else {
        upgrade_started();
        print_heading($plugin);
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
 * @param bool $installation true if installation, false menas upgrade
 */
function print_upgrade_part_end($plugin, $installation) {
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
    notify(get_string('success'), 'notifysuccess');
    print_upgrade_separator();
}

function upgrade_get_javascript() {
    global $CFG;
    return '<script type="text/javascript" src="'.$CFG->wwwroot.'/lib/scroll_to_page_end.js"></script>';
}


/**
 * Try to upgrade the given language pack (or current language)
 */
function upgrade_language_pack($lang='') {
    global $CFG;

    if (empty($lang)) {
        $lang = current_language();
    }

    if ($lang == 'en_utf8') {
        return true;  // Nothing to do
    }

    upgrade_started(false);
    print_heading(get_string('langimport', 'admin').': '.$lang);

    @mkdir ($CFG->dataroot.'/temp/');    //make it in case it's a fresh install, it might not be there
    @mkdir ($CFG->dataroot.'/lang/');

    require_once($CFG->libdir.'/componentlib.class.php');

    if ($cd = new component_installer('http://download.moodle.org', 'lang16', $lang.'.zip', 'languages.md5', 'lang')) {
        $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)

        if ($status == COMPONENT_INSTALLED) {
            @unlink($CFG->dataroot.'/cache/languages');
            if ($parentlang = get_parent_language($lang)) {
                if ($cd = new component_installer('http://download.moodle.org', 'lang16', $parentlang.'.zip', 'languages.md5', 'lang')) {
                    $cd->install();
                }
            }
            notify(get_string('success'), 'notifysuccess');
        }
    }

    print_upgrade_separator();
}
