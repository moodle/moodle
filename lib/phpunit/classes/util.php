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
 * Utility class.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../testing/classes/util.php');
require_once(__DIR__ . "/coverage_info.php");

/**
 * Collection of utility methods.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_util extends testing_util {
    /**
     * @var int last value of db writes counter, used for db resetting
     */
    public static $lastdbwrites = null;

    /** @var array An array of original globals, restored after each test */
    protected static $globals = array();

    /** @var array list of debugging messages triggered during the last test execution */
    protected static $debuggings = array();

    /** @var phpunit_message_sink alternative target for moodle messaging */
    protected static $messagesink = null;

    /** @var phpunit_phpmailer_sink alternative target for phpmailer messaging */
    protected static $phpmailersink = null;

    /** @var phpunit_message_sink alternative target for moodle messaging */
    protected static $eventsink = null;

    /**
     * @var array Files to skip when resetting dataroot folder
     */
    protected static $datarootskiponreset = array('.', '..', 'phpunittestdir.txt', 'phpunit', '.htaccess');

    /**
     * @var array Files to skip when dropping dataroot folder
     */
    protected static $datarootskipondrop = array('.', '..', 'lock');

    /**
     * Load global $CFG;
     * @internal
     * @static
     * @return void
     */
    public static function initialise_cfg() {
        global $DB;
        $dbhash = false;
        try {
            $dbhash = $DB->get_field('config', 'value', array('name'=>'phpunittest'));
        } catch (Exception $e) {
            // not installed yet
            initialise_cfg();
            return;
        }
        if ($dbhash !== core_component::get_all_versions_hash()) {
            // do not set CFG - the only way forward is to drop and reinstall
            return;
        }
        // standard CFG init
        initialise_cfg();
    }

    /**
     * Reset contents of all database tables to initial values, reset caches, etc.
     *
     * Note: this is relatively slow (cca 2 seconds for pg and 7 for mysql) - please use with care!
     *
     * @static
     * @param bool $detectchanges
     *      true  - changes in global state and database are reported as errors
     *      false - no errors reported
     *      null  - only critical problems are reported as errors
     * @return void
     */
    public static function reset_all_data($detectchanges = false) {
        global $DB, $CFG, $USER, $SITE, $COURSE, $PAGE, $OUTPUT, $SESSION, $FULLME, $FILTERLIB_PRIVATE;

        // Stop all hook redirections.
        \core\hook\manager::get_instance()->phpunit_stop_redirections();

        // Reset the hook manager instance.
        \core\hook\manager::phpunit_reset_instance();

        // Stop any message redirection.
        self::stop_message_redirection();

        // Stop any message redirection.
        self::stop_event_redirection();

        // Start a new email redirection.
        // This will clear any existing phpmailer redirection.
        // We redirect all phpmailer output to this message sink which is
        // called instead of phpmailer actually sending the message.
        self::start_phpmailer_redirection();

        // We used to call gc_collect_cycles here to ensure desctructors were called between tests.
        // This accounted for 25% of the total time running phpunit - so we removed it.

        // Show any unhandled debugging messages, the runbare() could already reset it.
        self::display_debugging_messages();
        self::reset_debugging();

        // reset global $DB in case somebody mocked it
        $DB = self::get_global_backup('DB');

        if ($DB->is_transaction_started()) {
            // we can not reset inside transaction
            $DB->force_transaction_rollback();
        }

        $resetdb = self::reset_database();
        $localename = self::get_locale_name();
        $warnings = array();

        if ($detectchanges === true) {
            if ($resetdb) {
                $warnings[] = 'Warning: unexpected database modification, resetting DB state';
            }

            $oldcfg = self::get_global_backup('CFG');
            $oldsite = self::get_global_backup('SITE');
            foreach($CFG as $k=>$v) {
                if (!property_exists($oldcfg, $k)) {
                    $warnings[] = 'Warning: unexpected new $CFG->'.$k.' value';
                } else if ($oldcfg->$k !== $CFG->$k) {
                    $warnings[] = 'Warning: unexpected change of $CFG->'.$k.' value';
                }
                unset($oldcfg->$k);

            }
            if ($oldcfg) {
                foreach($oldcfg as $k=>$v) {
                    $warnings[] = 'Warning: unexpected removal of $CFG->'.$k;
                }
            }

            if ($USER->id != 0) {
                $warnings[] = 'Warning: unexpected change of $USER';
            }

            if ($COURSE->id != $oldsite->id) {
                $warnings[] = 'Warning: unexpected change of $COURSE';
            }

            if ($FULLME !== self::get_global_backup('FULLME')) {
                $warnings[] = 'Warning: unexpected change of $FULLME';
            }

            if (setlocale(LC_TIME, 0) !== $localename) {
                $warnings[] = 'Warning: unexpected change of locale';
            }
        }

        if (ini_get('max_execution_time') != 0) {
            // This is special warning for all resets because we do not want any
            // libraries to mess with timeouts unintentionally.
            // Our PHPUnit integration is not supposed to change it either.

            if ($detectchanges !== false) {
                $warnings[] = 'Warning: max_execution_time was changed to '.ini_get('max_execution_time');
            }
            set_time_limit(0);
        }

        // restore original globals
        $_SERVER = self::get_global_backup('_SERVER');
        $CFG = self::get_global_backup('CFG');
        $SITE = self::get_global_backup('SITE');
        $FULLME = self::get_global_backup('FULLME');
        $_GET = array();
        $_POST = array();
        $_FILES = array();
        $_REQUEST = array();
        $COURSE = $SITE;

        // reinitialise following globals
        $OUTPUT = new bootstrap_renderer();
        $PAGE = new moodle_page();
        $FULLME = null;
        $ME = null;
        $SCRIPT = null;
        $FILTERLIB_PRIVATE = null;
        if (!empty($SESSION->notifications)) {
            $SESSION->notifications = [];
        }

        // Empty sessison and set fresh new not-logged-in user.
        \core\session\manager::init_empty_session();

        // reset all static caches
        \core\event\manager::phpunit_reset();
        accesslib_clear_all_caches(true);
        accesslib_reset_role_cache();
        get_string_manager()->reset_caches(true);
        reset_text_filters_cache(true);
        get_message_processors(false, true, true);
        filter_manager::reset_caches();
        core_filetypes::reset_caches();
        \core_search\manager::clear_static();
        core_user::reset_caches();
        \core\output\icon_system::reset_caches();
        if (class_exists('core_media_manager', false)) {
            core_media_manager::reset_caches();
        }

        // Reset static unit test options.
        if (class_exists('\availability_date\condition', false)) {
            \availability_date\condition::set_current_time_for_test(0);
        }

        // Reset internal users.
        core_user::reset_internal_users();

        // Clear static caches in calendar container.
        if (class_exists('\core_calendar\local\event\container', false)) {
            core_calendar\local\event\container::reset_caches();
        }

        //TODO MDL-25290: add more resets here and probably refactor them to new core function

        // Reset course and module caches.
        core_courseformat\base::reset_course_cache(0);
        get_fast_modinfo(0, 0, true);

        // Reset other singletons.
        if (class_exists('core_plugin_manager')) {
            core_plugin_manager::reset_caches(true);
        }
        if (class_exists('\core\update\checker')) {
            \core\update\checker::reset_caches(true);
        }
        if (class_exists('\core_course\customfield\course_handler')) {
            \core_course\customfield\course_handler::reset_caches();
        }
        if (class_exists('\core_reportbuilder\manager')) {
            \core_reportbuilder\manager::reset_caches();
        }
        if (class_exists('\core_cohort\customfield\cohort_handler')) {
            \core_cohort\customfield\cohort_handler::reset_caches();
        }
        if (class_exists('\core_group\customfield\group_handler')) {
            \core_group\customfield\group_handler::reset_caches();
        }
        if (class_exists('\core_group\customfield\grouping_handler')) {
            \core_group\customfield\grouping_handler::reset_caches();
        }

        // Clear static cache within restore.
        if (class_exists('restore_section_structure_step')) {
            restore_section_structure_step::reset_caches();
        }

        // purge dataroot directory
        self::reset_dataroot();

        // restore original config once more in case resetting of caches changed CFG
        $CFG = self::get_global_backup('CFG');

        // inform data generator
        self::get_data_generator()->reset();

        // fix PHP settings
        error_reporting($CFG->debug);

        // Reset the date/time class.
        core_date::phpunit_reset();

        // Make sure the time locale is consistent - that is Australian English.
        setlocale(LC_TIME, $localename);

        // Reset the log manager cache.
        get_log_manager(true);

        // Reset user agent.
        core_useragent::instance(true, null);

        // Reset the DI container.
        \core\di::reset_container();

        // verify db writes just in case something goes wrong in reset
        if (self::$lastdbwrites != $DB->perf_get_writes()) {
            error_log('Unexpected DB writes in phpunit_util::reset_all_data()');
            self::$lastdbwrites = $DB->perf_get_writes();
        }

        if ($warnings) {
            $warnings = implode("\n", $warnings);
            trigger_error($warnings, E_USER_WARNING);
        }
    }

    /**
     * Reset all database tables to default values.
     * @static
     * @return bool true if reset done, false if skipped
     */
    public static function reset_database() {
        global $DB;

        if (defined('PHPUNIT_ISOLATED_TEST') && PHPUNIT_ISOLATED_TEST && self::$lastdbwrites === null) {
            // This is an isolated test and the lastdbwrites has not yet been initialised.
            // Isolated test runs are reset by the test runner before the run starts.
            self::$lastdbwrites = $DB->perf_get_writes();
        }

        if (!is_null(self::$lastdbwrites) && self::$lastdbwrites == $DB->perf_get_writes()) {
            return false;
        }

        if (!parent::reset_database()) {
            return false;
        }

        self::$lastdbwrites = $DB->perf_get_writes();

        return true;
    }

    /**
     * Called during bootstrap only!
     * @internal
     * @static
     * @return void
     */
    public static function bootstrap_init() {
        global $CFG, $SITE, $DB, $FULLME;

        // backup the globals
        self::$globals['_SERVER'] = $_SERVER;
        self::$globals['CFG'] = clone($CFG);
        self::$globals['SITE'] = clone($SITE);
        self::$globals['DB'] = $DB;
        self::$globals['FULLME'] = $FULLME;

        // refresh data in all tables, clear caches, etc.
        self::reset_all_data();
    }

    /**
     * Print some Moodle related info to console.
     * @internal
     * @static
     * @return void
     */
    public static function bootstrap_moodle_info() {
        echo self::get_site_info();
    }

    /**
     * Returns original state of global variable.
     * @static
     * @param string $name
     * @return mixed
     */
    public static function get_global_backup($name) {
        if ($name === 'DB') {
            // no cloning of database object,
            // we just need the original reference, not original state
            return self::$globals['DB'];
        }
        if (isset(self::$globals[$name])) {
            if (is_object(self::$globals[$name])) {
                $return = clone(self::$globals[$name]);
                return $return;
            } else {
                return self::$globals[$name];
            }
        }
        return null;
    }

    /**
     * Is this site initialised to run unit tests?
     *
     * @static
     * @return int array errorcode=>message, 0 means ok
     */
    public static function testing_ready_problem() {
        global $DB;

        $localename = self::get_locale_name();
        if (setlocale(LC_TIME, $localename) === false) {
            return array(PHPUNIT_EXITCODE_CONFIGERROR, "Required locale '$localename' is not installed.");
        }

        if (!self::is_test_site()) {
            // dataroot was verified in bootstrap, so it must be DB
            return array(PHPUNIT_EXITCODE_CONFIGERROR, 'Can not use database for testing, try different prefix');
        }

        $tables = $DB->get_tables(false);
        if (empty($tables)) {
            return array(PHPUNIT_EXITCODE_INSTALL, '');
        }

        if (!self::is_test_data_updated()) {
            return array(PHPUNIT_EXITCODE_REINSTALL, '');
        }

        return array(0, '');
    }

    /**
     * Drop all test site data.
     *
     * Note: To be used from CLI scripts only.
     *
     * @static
     * @param bool $displayprogress if true, this method will echo progress information.
     * @return void may terminate execution with exit code
     */
    public static function drop_site($displayprogress = false) {
        global $DB, $CFG;

        if (!self::is_test_site()) {
            phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, 'Can not drop non-test site!!');
        }

        // Purge dataroot
        if ($displayprogress) {
            echo "Purging dataroot:\n";
        }

        self::reset_dataroot();
        testing_initdataroot($CFG->dataroot, 'phpunit');

        // Drop all tables.
        self::drop_database($displayprogress);

        // Drop dataroot.
        self::drop_dataroot();
    }

    /**
     * Perform a fresh test site installation
     *
     * Note: To be used from CLI scripts only.
     *
     * @static
     * @return void may terminate execution with exit code
     */
    public static function install_site() {
        global $DB, $CFG;

        if (!self::is_test_site()) {
            phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, 'Can not install on non-test site!!');
        }

        if ($DB->get_tables()) {
            list($errorcode, $message) = self::testing_ready_problem();
            if ($errorcode) {
                phpunit_bootstrap_error(PHPUNIT_EXITCODE_REINSTALL, 'Database tables already present, Moodle PHPUnit test environment can not be initialised');
            } else {
                phpunit_bootstrap_error(0, 'Moodle PHPUnit test environment is already initialised');
            }
        }

        $options = array();
        $options['adminpass'] = 'admin';
        $options['shortname'] = 'phpunit';
        $options['fullname'] = 'PHPUnit test site';

        install_cli_database($options, false);

        // Set the admin email address.
        $DB->set_field('user', 'email', 'admin@example.com', array('username' => 'admin'));

        // Disable all logging for performance and sanity reasons.
        set_config('enabled_stores', '', 'tool_log');

        // Remove any default blocked hosts and port restrictions, to avoid blocking tests (eg those using local files).
        set_config('curlsecurityblockedhosts', '');
        set_config('curlsecurityallowedport', '');

        // Execute all the adhoc tasks.
        while ($task = \core\task\manager::get_next_adhoc_task(time())) {
            $task->execute();
            \core\task\manager::adhoc_task_complete($task);
        }

        // We need to keep the installed dataroot filedir files.
        // So each time we reset the dataroot before running a test, the default files are still installed.
        self::save_original_data_files();

        // Store version hash in the database and in a file.
        self::store_versions_hash();

        // Store database data and structure.
        self::store_database_state();
    }

    /**
     * Builds dirroot/phpunit.xml file using defaults from /phpunit.xml.dist
     * @static
     * @return bool true means main config file created, false means only dataroot file created
     */
    public static function build_config_file() {
        global $CFG;

        $template = <<<EOF
            <testsuite name="@component@_testsuite">
              <directory suffix="_test.php">@dir@</directory>
            </testsuite>

        EOF;
        $data = file_get_contents("$CFG->dirroot/phpunit.xml.dist");

        $suites = '';
        $includelists = [];
        $excludelists = [];

        $subsystems = core_component::get_core_subsystems();
        $subsystems['core'] = $CFG->dirroot . '/lib';
        foreach ($subsystems as $subsystem => $fulldir) {
            if (empty($fulldir)) {
                continue;
            }
            if (!file_exists("{$fulldir}/tests/")) {
                // There are no tests - skip this directory.
                continue;
            }

            $dir = substr($fulldir, strlen($CFG->dirroot) + 1);
            if ($coverageinfo = self::get_coverage_info($fulldir)) {
                $includelists = array_merge($includelists, $coverageinfo->get_includelists($dir));
                $excludelists = array_merge($excludelists, $coverageinfo->get_excludelists($dir));
            }
        }

        $plugintypes = core_component::get_plugin_types();
        ksort($plugintypes);
        foreach (array_keys($plugintypes) as $type) {
            $plugs = core_component::get_plugin_list($type);
            ksort($plugs);
            foreach ($plugs as $plug => $plugindir) {
                if (!file_exists("{$plugindir}/tests/")) {
                    // There are no tests - skip this directory.
                    continue;
                }

                $dir = substr($plugindir, strlen($CFG->dirroot) + 1);
                $testdir = "{$dir}/tests";
                $component = "{$type}_{$plug}";

                $suite = str_replace('@component@', $component, $template);
                $suite = str_replace('@dir@', $testdir, $suite);

                $suites .= $suite;

                if ($coverageinfo = self::get_coverage_info($plugindir)) {

                    $includelists = array_merge($includelists, $coverageinfo->get_includelists($dir));
                    $excludelists = array_merge($excludelists, $coverageinfo->get_excludelists($dir));
                }
            }
        }

        // Start a sequence between 100000 and 199000 to ensure each call to init produces
        // different ids in the database.  This reduces the risk that hard coded values will
        // end up being placed in phpunit or behat test code.
        $sequencestart = 100000 + mt_rand(0, 99) * 1000;

        $data = preg_replace('| *<!--@plugin_suites_start@-->.*<!--@plugin_suites_end@-->|s', trim($suites, "\n"), $data, 1);
        $data = str_replace(
            '<const name="PHPUNIT_SEQUENCE_START" value=""/>',
            '<const name="PHPUNIT_SEQUENCE_START" value="' . $sequencestart . '"/>',
            $data);

        $coverages = self::get_coverage_config($includelists, $excludelists);
        $data = preg_replace('| *<!--@coveragelist@-->|s', trim($coverages, "\n"), $data);

        $result = false;
        if (is_writable($CFG->dirroot)) {
            if ($result = file_put_contents("$CFG->dirroot/phpunit.xml", $data)) {
                testing_fix_file_permissions("$CFG->dirroot/phpunit.xml");
            }
        }

        return (bool)$result;
    }

    /**
     * Builds phpunit.xml files for all components using defaults from /phpunit.xml.dist
     *
     * @static
     * @return void, stops if can not write files
     */
    public static function build_component_config_files() {
        global $CFG;

        $template = <<<EOT
            <testsuites>
              <testsuite name="@component@_testsuite">
                <directory suffix="_test.php">.</directory>
              </testsuite>
            </testsuites>
          EOT;
        $coveragedefault = <<<EOT
            <include>
              <directory suffix=".php">.</directory>
            </include>
            <exclude>
              <directory suffix="_test.php">.</directory>
            </exclude>
        EOT;

        // Start a sequence between 100000 and 199000 to ensure each call to init produces
        // different ids in the database.  This reduces the risk that hard coded values will
        // end up being placed in phpunit or behat test code.
        $sequencestart = 100000 + mt_rand(0, 99) * 1000;

        // Use the upstream file as source for the distributed configurations
        $ftemplate = file_get_contents("$CFG->dirroot/phpunit.xml.dist");
        $ftemplate = preg_replace('| *<!--All core suites.*</testsuites>|s', '<!--@component_suite@-->', $ftemplate);

        // Gets all the components with tests
        $components = tests_finder::get_components_with_tests('phpunit');

        // Create the corresponding phpunit.xml file for each component
        foreach ($components as $cname => $cpath) {
            // Calculate the component suite
            $ctemplate = $template;
            $ctemplate = str_replace('@component@', $cname, $ctemplate);

            $fcontents = str_replace('<!--@component_suite@-->', $ctemplate, $ftemplate);

            // Check for coverage configurations.
            if ($coverageinfo = self::get_coverage_info($cpath)) {
                $coverages = self::get_coverage_config($coverageinfo->get_includelists(''), $coverageinfo->get_excludelists(''));
            } else {
                $coverages = $coveragedefault;
            }
            $fcontents = preg_replace('| *<!--@coveragelist@-->|s', trim($coverages, "\n"), $fcontents);

            // Apply it to the file template.
            $fcontents = str_replace(
                '<const name="PHPUNIT_SEQUENCE_START" value=""/>',
                '<const name="PHPUNIT_SEQUENCE_START" value="' . $sequencestart . '"/>',
                $fcontents);

            // fix link to schema
            $level = substr_count(str_replace('\\', '/', $cpath), '/') - substr_count(str_replace('\\', '/', $CFG->dirroot), '/');
            $fcontents = str_replace('lib/phpunit/', str_repeat('../', $level).'lib/phpunit/', $fcontents);

            // Write the file
            $result = false;
            if (is_writable($cpath)) {
                if ($result = (bool)file_put_contents("$cpath/phpunit.xml", $fcontents)) {
                    testing_fix_file_permissions("$cpath/phpunit.xml");
                }
            }
            // Problems writing file, throw error
            if (!$result) {
                phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGWARNING, "Can not create $cpath/phpunit.xml configuration file, verify dir permissions");
            }
        }
    }

    /**
     * To be called from debugging() only.
     * @param string $message
     * @param int $level
     * @param string $from
     */
    public static function debugging_triggered($message, $level, $from) {
        // Store only if debugging triggered from actual test,
        // we need normal debugging outside of tests to find problems in our phpunit integration.
        $backtrace = debug_backtrace();

        // Only for advanced_testcase, database_driver_testcase (and descendants). Others aren't
        // able to manage the debugging sink, so any debugging has to be output normally and, hopefully,
        // PHPUnit execution will catch that unexpected output properly.
        $sinksupport = false;
        foreach ($backtrace as $bt) {
            if (isset($bt['object']) && is_object($bt['object'])
                && (
                    $bt['object'] instanceof advanced_testcase ||
                    $bt['object'] instanceof database_driver_testcase)
            ) {
                $sinksupport = true;
                break;
            }
        }
        if (!$sinksupport) {
            return false;
        }

        // Verify that we are inside a PHPUnit test (little bit redundant, because
        // we already have checked above that this is an advanced/database_driver
        // testcase, but let's keep things double safe for now).
        foreach ($backtrace as $bt) {
            if (isset($bt['object']) && is_object($bt['object'])
                    && $bt['object'] instanceof PHPUnit\Framework\TestCase) {
                $debug = new stdClass();
                $debug->message = $message;
                $debug->level   = $level;
                $debug->from    = $from;

                self::$debuggings[] = $debug;

                return true;
            }
        }
        return false;
    }

    /**
     * Resets the list of debugging messages.
     */
    public static function reset_debugging() {
        self::$debuggings = array();
        set_debugging(DEBUG_DEVELOPER);
    }

    /**
     * Returns all debugging messages triggered during test.
     * @return array with instances having message, level and stacktrace property.
     */
    public static function get_debugging_messages() {
        return self::$debuggings;
    }

    /**
     * Prints out any debug messages accumulated during test execution.
     *
     * @param bool $return true to return the messages or false to print them directly. Default false.
     * @return bool|string false if no debug messages, true if debug triggered or string of messages
     */
    public static function display_debugging_messages($return = false) {
        if (empty(self::$debuggings)) {
            return false;
        }

        $debugstring = '';
        foreach(self::$debuggings as $debug) {
            $debugstring .= 'Debugging: ' . $debug->message . "\n" . trim($debug->from) . "\n";
        }

        if ($return) {
            return $debugstring;
        }
        echo $debugstring;
        return true;
    }

    /**
     * Start message redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink = $this->redirectMessages() instead.
     *
     * @return phpunit_message_sink
     */
    public static function start_message_redirection() {
        if (self::$messagesink) {
            self::stop_message_redirection();
        }
        self::$messagesink = new phpunit_message_sink();
        return self::$messagesink;
    }

    /**
     * End message redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink->close() instead.
     */
    public static function stop_message_redirection() {
        self::$messagesink = null;
    }

    /**
     * Are messages redirected to some sink?
     *
     * Note: to be called from messagelib.php only!
     *
     * @return bool
     */
    public static function is_redirecting_messages() {
        return !empty(self::$messagesink);
    }

    /**
     * To be called from messagelib.php only!
     *
     * @param stdClass $message record from messages table
     * @return bool true means send message, false means message "sent" to sink.
     */
    public static function message_sent($message) {
        if (self::$messagesink) {
            self::$messagesink->add_message($message);
        }
    }

    /**
     * Start phpmailer redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink = $this->redirectEmails() instead.
     *
     * @return phpunit_phpmailer_sink
     */
    public static function start_phpmailer_redirection() {
        if (self::$phpmailersink) {
            // If an existing mailer sink is active, just clear it.
            self::$phpmailersink->clear();
        } else {
            self::$phpmailersink = new phpunit_phpmailer_sink();
        }
        return self::$phpmailersink;
    }

    /**
     * End phpmailer redirection.
     *
     * Note: Do not call directly from tests,
     *       use $sink->close() instead.
     */
    public static function stop_phpmailer_redirection() {
        self::$phpmailersink = null;
    }

    /**
     * Are messages for phpmailer redirected to some sink?
     *
     * Note: to be called from moodle_phpmailer.php only!
     *
     * @return bool
     */
    public static function is_redirecting_phpmailer() {
        return !empty(self::$phpmailersink);
    }

    /**
     * To be called from messagelib.php only!
     *
     * @param stdClass $message record from messages table
     * @return bool true means send message, false means message "sent" to sink.
     */
    public static function phpmailer_sent($message) {
        if (self::$phpmailersink) {
            self::$phpmailersink->add_message($message);
        }
    }

    /**
     * Start event redirection.
     *
     * @private
     * Note: Do not call directly from tests,
     *       use $sink = $this->redirectEvents() instead.
     *
     * @return phpunit_event_sink
     */
    public static function start_event_redirection() {
        if (self::$eventsink) {
            self::stop_event_redirection();
        }
        self::$eventsink = new phpunit_event_sink();
        return self::$eventsink;
    }

    /**
     * End event redirection.
     *
     * @private
     * Note: Do not call directly from tests,
     *       use $sink->close() instead.
     */
    public static function stop_event_redirection() {
        self::$eventsink = null;
    }

    /**
     * Are events redirected to some sink?
     *
     * Note: to be called from \core\event\base only!
     *
     * @private
     * @return bool
     */
    public static function is_redirecting_events() {
        return !empty(self::$eventsink);
    }

    /**
     * To be called from \core\event\base only!
     *
     * @private
     * @param \core\event\base $event record from event_read table
     * @return bool true means send event, false means event "sent" to sink.
     */
    public static function event_triggered(\core\event\base $event) {
        if (self::$eventsink) {
            self::$eventsink->add_event($event);
        }
    }

    /**
     * Gets the name of the locale for testing environment (Australian English)
     * depending on platform environment.
     *
     * @return string the locale name.
     */
    protected static function get_locale_name() {
        global $CFG;
        if ($CFG->ostype === 'WINDOWS') {
            return 'English_Australia.1252';
        } else {
            return 'en_AU.UTF-8';
        }
    }

    /**
     * Executes all adhoc tasks in the queue. Useful for testing asynchronous behaviour.
     *
     * @return void
     */
    public static function run_all_adhoc_tasks() {
        $now = time();
        while (($task = \core\task\manager::get_next_adhoc_task($now)) !== null) {
            try {
                $task->execute();
                \core\task\manager::adhoc_task_complete($task);
            } catch (Exception $e) {
                \core\task\manager::adhoc_task_failed($task);
            }
        }
    }

    /**
     * Helper function to call a protected/private method of an object using reflection.
     *
     * Example 1. Calling a protected object method:
     *   $result = call_internal_method($myobject, 'method_name', [$param1, $param2], '\my\namespace\myobjectclassname');
     *
     * Example 2. Calling a protected static method:
     *   $result = call_internal_method(null, 'method_name', [$param1, $param2], '\my\namespace\myclassname');
     *
     * @param object|null $object the object on which to call the method, or null if calling a static method.
     * @param string $methodname the name of the protected/private method.
     * @param array $params the array of function params to pass to the method.
     * @param string $classname the fully namespaced name of the class the object was created from (base in the case of mocks),
     *        or the name of the static class when calling a static method.
     * @return mixed the respective return value of the method.
     */
    public static function call_internal_method($object, $methodname, array $params, $classname) {
        $reflection = new \ReflectionClass($classname);
        $method = $reflection->getMethod($methodname);
        return $method->invokeArgs($object, $params);
    }

    /**
     * Pad the supplied string with $level levels of indentation.
     *
     * @param   string  $string The string to pad
     * @param   int     $level The number of levels of indentation to pad
     * @return  string
     */
    protected static function pad(string $string, int $level): string {
        return str_repeat(" ", $level * 2) . "{$string}\n";
    }

    /**
     * Get the coverage config for the supplied includelist and excludelist configuration.
     *
     * @param   string[] $includelists The list of files/folders in the includelist.
     * @param   string[] $excludelists The list of files/folders in the excludelist.
     * @return  string
     */
    protected static function get_coverage_config(array $includelists, array $excludelists): string {
        $coverages = '';
        if (!empty($includelists)) {
            $coverages .= self::pad("<include>", 2);
            foreach ($includelists as $line) {
                $coverages .= self::pad($line, 3);
            }
            $coverages .= self::pad("</include>", 2);
            if (!empty($excludelists)) {
                $coverages .= self::pad("<exclude>", 2);
                foreach ($excludelists as $line) {
                    $coverages .= self::pad($line, 3);
                }
                $coverages .= self::pad("</exclude>", 2);
            }
        }

        return $coverages;
    }

    /**
     * Get the phpunit_coverage_info for the specified plugin or subsystem directory.
     *
     * @param   string  $fulldir The directory to find the coverage info file in.
     * @return  phpunit_coverage_info
     */
    protected static function get_coverage_info(string $fulldir): phpunit_coverage_info {
        $coverageconfig = "{$fulldir}/tests/coverage.php";
        if (file_exists($coverageconfig)) {
            $coverageinfo = require($coverageconfig);
            if (!$coverageinfo instanceof phpunit_coverage_info) {
                throw new \coding_exception("{$coverageconfig} does not return a phpunit_coverage_info");
            }

            return $coverageinfo;
        }

        return new phpunit_coverage_info();;
    }

    /**
     * Whether the current process is an isolated test process.
     *
     * @return bool
     */
    public static function is_in_isolated_process(): bool {
        // Note: There is no function to call, or much to go by in order to tell whether we are in an isolated process
        // during Bootstrap, when this function is called.
        // We can do so by testing the existence of the wrapper function, but there is nothing set until that point.
        return function_exists('__phpunit_run_isolated_test');
    }
}
