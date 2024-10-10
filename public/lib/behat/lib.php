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
 * Behat basic functions
 *
 * It does not include MOODLE_INTERNAL because is part of the bootstrap.
 *
 * This script should not be usually included, neither any of its functions
 * used, within mooodle code at all. It's for exclusive use of behat and
 * moodle setup.php. For places requiring a different/special behavior
 * needing to check if are being run as part of behat tests, use:
 *     if (defined('BEHAT_SITE_RUNNING')) { ...
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../testing/lib.php');

define('BEHAT_EXITCODE_CONFIG', 250);
define('BEHAT_EXITCODE_REQUIREMENT', 251);
define('BEHAT_EXITCODE_PERMISSIONS', 252);
define('BEHAT_EXITCODE_REINSTALL', 253);
define('BEHAT_EXITCODE_INSTALL', 254);
define('BEHAT_EXITCODE_INSTALLED', 256);

/**
 * The behat test site fullname and shortname.
 */
define('BEHAT_PARALLEL_SITE_NAME', "behatrun");

/**
 * Exits with an error code
 *
 * @param  mixed $errorcode
 * @param  string $text
 * @return void Stops execution with error code
 */
function behat_error($errorcode, $text = '') {

    // Adding error prefixes.
    switch ($errorcode) {
        case BEHAT_EXITCODE_CONFIG:
            $text = 'Behat config error: ' . $text;
            break;
        case BEHAT_EXITCODE_REQUIREMENT:
            $text = 'Behat requirement not satisfied: ' . $text;
            break;
        case BEHAT_EXITCODE_PERMISSIONS:
            $text = 'Behat permissions problem: ' . $text . ', check the permissions';
            break;
        case BEHAT_EXITCODE_REINSTALL:
            $path = testing_cli_argument_path('/public/admin/tool/behat/cli/init.php');
            $text = "Reinstall Behat: ".$text.", use:\n php ".$path;
            break;
        case BEHAT_EXITCODE_INSTALL:
            $path = testing_cli_argument_path('/public/admin/tool/behat/cli/init.php');
            $text = "Install Behat before enabling it, use:\n php ".$path;
            break;
        case BEHAT_EXITCODE_INSTALLED:
            $text = "The Behat site is already installed";
            break;
        default:
            $text = 'Unknown error ' . $errorcode . ' ' . $text;
            break;
    }

    testing_error($errorcode, $text);
}

/**
 * Return logical error string.
 *
 * @param int $errtype php error type.
 * @return string string which will be returned.
 */
function behat_get_error_string($errtype) {
    switch ($errtype) {
        case E_USER_ERROR:
            $errnostr = 'Fatal error';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $errnostr = 'Warning';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $errnostr = 'Notice';
            break;
        case E_RECOVERABLE_ERROR:
            $errnostr = 'Catchable';
            break;
        default:
            $errnostr = 'Unknown error type';
    }

    return $errnostr;
}

/**
 * PHP errors handler to use when running behat tests.
 *
 * Adds specific CSS classes to identify
 * the messages.
 *
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return bool
 */
function behat_error_handler($errno, $errstr, $errfile, $errline) {

    // If is preceded by an @ we don't show it.
    if (!error_reporting()) {
        return true;
    }

    // This error handler receives E_ALL, running the behat test site the debug level is
    // set to DEVELOPER and will always include E_NOTICE,E_USER_NOTICE... as part of E_ALL, if the current
    // error_reporting() value does not include one of those levels is because it has been forced through
    // the moodle code (see fix_utf8() for example) in that cases we respect the forced error level value.
    $respect = [E_NOTICE, E_USER_NOTICE, E_WARNING, E_USER_WARNING, E_DEPRECATED, E_USER_DEPRECATED];
    foreach ($respect as $respectable) {

        // If the current value does not include this kind of errors and the reported error is
        // at that level don't print anything.
        if ($errno == $respectable && !(error_reporting() & $respectable)) {
            return true;
        }
    }

    // Using the default one in case there is a fatal catchable error.
    default_error_handler($errno, $errstr, $errfile, $errline);

    $errnostr = behat_get_error_string($errno);

    // If ajax script then throw exception, so the calling api catch it and show it on web page.
    if (defined('AJAX_SCRIPT')) {
        throw new Exception("$errnostr: $errstr in $errfile on line $errline");
    } else {
        // Wrapping the output.
        echo '<div class="phpdebugmessage" data-rel="phpdebugmessage">' . PHP_EOL;
        echo "$errnostr: $errstr in $errfile on line $errline" . PHP_EOL;
        echo '</div>';
    }

    // Also use the internal error handler so we keep the usual behaviour.
    return false;
}

/**
 * Before shutdown save last error entries, so we can fail the test.
 */
function behat_shutdown_function() {
    // If any error found, then save it.
    if ($error = error_get_last()) {
        // Ignore E_WARNING, as they might come via ( @ )suppression and might lead to false failure.
        if (isset($error['type']) && !($error['type'] & E_WARNING)) {

            $errors = behat_get_shutdown_process_errors();

            $errors[] = $error;
            $errorstosave = json_encode($errors);

            set_config('process_errors', $errorstosave, 'tool_behat');
        }
    }
}

/**
 * Return php errors save which were save during shutdown.
 *
 * @return array
 */
function behat_get_shutdown_process_errors() {
    global $DB;

    // Don't use get_config, as it use cache and return invalid value, between selenium and cli process.
    $phperrors = $DB->get_field('config_plugins', 'value', array('name' => 'process_errors', 'plugin' => 'tool_behat'));

    if (!empty($phperrors)) {
        return json_decode($phperrors, true);
    } else {
        return array();
    }
}

/**
 * Restrict the config.php settings allowed.
 *
 * When running the behat features the config.php
 * settings should not affect the results.
 *
 * @return void
 */
function behat_clean_init_config() {
    global $CFG;

    $allowed = array_flip(array(
        'wwwroot', 'dataroot', 'root', 'dirroot', 'admin', 'directorypermissions', 'filepermissions',
        'umaskpermissions', 'dbtype', 'dblibrary', 'dbhost', 'dbname', 'dbuser', 'dbpass', 'prefix',
        'dboptions', 'proxyhost', 'proxyport', 'proxytype', 'proxyuser', 'proxypassword',
        'proxybypass', 'pathtogs', 'pathtophp', 'pathtodu', 'aspellpath', 'pathtodot', 'skiplangupgrade',
        'altcacheconfigpath', 'pathtounoconv', 'alternative_file_system_class', 'pathtopython',
        'routerconfigured',
    ));

    // Add extra allowed settings.
    if (!empty($CFG->behat_extraallowedsettings)) {
        $allowed = array_merge($allowed, array_flip($CFG->behat_extraallowedsettings));
    }

    // Also allowing behat_ prefixed attributes.
    foreach ($CFG as $key => $value) {
        if (!isset($allowed[$key]) && strpos($key, 'behat_') !== 0) {
            unset($CFG->{$key});
        }
    }

    // Allow email catcher settings.
    if (defined('TEST_EMAILCATCHER_MAIL_SERVER')) {
        $CFG->noemailever = false;
        $CFG->smtphosts = TEST_EMAILCATCHER_MAIL_SERVER;
    }
}

/**
 * Checks that the behat config vars are properly set.
 *
 * @return void Stops execution with error code if something goes wrong.
 */
function behat_check_config_vars() {
    global $CFG;

    $moodleprefix = empty($CFG->prefix) ? '' : $CFG->prefix;
    $behatprefix = empty($CFG->behat_prefix) ? '' : $CFG->behat_prefix;
    $phpunitprefix = empty($CFG->phpunit_prefix) ? '' : $CFG->phpunit_prefix;
    $behatdbname = empty($CFG->behat_dbname) ? $CFG->dbname : $CFG->behat_dbname;
    $phpunitdbname = empty($CFG->phpunit_dbname) ? $CFG->dbname : $CFG->phpunit_dbname;
    $behatdbhost = empty($CFG->behat_dbhost) ? $CFG->dbhost : $CFG->behat_dbhost;
    $phpunitdbhost = empty($CFG->phpunit_dbhost) ? $CFG->dbhost : $CFG->phpunit_dbhost;

    // Verify prefix value.
    if (empty($CFG->behat_prefix)) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            'Define $CFG->behat_prefix in config.php');
    }
    if ($behatprefix == $moodleprefix && $behatdbname == $CFG->dbname && $behatdbhost == $CFG->dbhost) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            '$CFG->behat_prefix in config.php must be different from $CFG->prefix' .
            ' when $CFG->behat_dbname and $CFG->behat_host are not set or when $CFG->behat_dbname equals $CFG->dbname' .
            ' and $CFG->behat_dbhost equals $CFG->dbhost');
    }
    if ($phpunitprefix !== '' && $behatprefix == $phpunitprefix && $behatdbname == $phpunitdbname &&
            $behatdbhost == $phpunitdbhost) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            '$CFG->behat_prefix in config.php must be different from $CFG->phpunit_prefix' .
            ' when $CFG->behat_dbname equals $CFG->phpunit_dbname' .
            ' and $CFG->behat_dbhost equals $CFG->phpunit_dbhost');
    }

    // Verify behat wwwroot value.
    if (empty($CFG->behat_wwwroot)) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            'Define $CFG->behat_wwwroot in config.php');
    }
    if (!empty($CFG->wwwroot) and $CFG->behat_wwwroot == $CFG->wwwroot) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            '$CFG->behat_wwwroot in config.php must be different from $CFG->wwwroot');
    }

    // Verify behat dataroot value.
    if (empty($CFG->behat_dataroot)) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            'Define $CFG->behat_dataroot in config.php');
    }
    clearstatcache();
    if (!file_exists($CFG->behat_dataroot_parent)) {
        $permissions = isset($CFG->directorypermissions) ? $CFG->directorypermissions : 02777;
        umask(0);
        if (!mkdir($CFG->behat_dataroot_parent, $permissions, true)) {
            behat_error(BEHAT_EXITCODE_PERMISSIONS, '$CFG->behat_dataroot directory can not be created');
        }
    }
    $CFG->behat_dataroot_parent = realpath($CFG->behat_dataroot_parent);
    if (empty($CFG->behat_dataroot_parent) or !is_dir($CFG->behat_dataroot_parent) or !is_writable($CFG->behat_dataroot_parent)) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            '$CFG->behat_dataroot in config.php must point to an existing writable directory');
    }
    if (!empty($CFG->dataroot) and $CFG->behat_dataroot_parent == realpath($CFG->dataroot)) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            '$CFG->behat_dataroot in config.php must be different from $CFG->dataroot');
    }
    if (!empty($CFG->phpunit_dataroot) and $CFG->behat_dataroot_parent == realpath($CFG->phpunit_dataroot)) {
        behat_error(BEHAT_EXITCODE_CONFIG,
            '$CFG->behat_dataroot in config.php must be different from $CFG->phpunit_dataroot');
    }

    // This request is coming from admin/tool/behat/cli/util.php which will call util_single.php. So just return from
    // here as we don't need to create a dataroot for single run.
    if (defined('BEHAT_PARALLEL_UTIL') && BEHAT_PARALLEL_UTIL && empty($CFG->behatrunprocess)) {
        return;
    }

    if (!file_exists($CFG->behat_dataroot)) {
        $permissions = isset($CFG->directorypermissions) ? $CFG->directorypermissions : 02777;
        umask(0);
        if (!mkdir($CFG->behat_dataroot, $permissions, true)) {
            behat_error(BEHAT_EXITCODE_PERMISSIONS, '$CFG->behat_dataroot directory can not be created');
        }
    }
    $CFG->behat_dataroot = realpath($CFG->behat_dataroot);
}

/**
 * Should we switch to the test site data?
 * @return bool
 */
function behat_is_test_site() {
    global $CFG;

    if (defined('BEHAT_UTIL')) {
        // This is the admin tool that installs/drops the test site install.
        return true;
    }
    if (defined('BEHAT_TEST')) {
        // This is the main vendor/bin/behat script.
        return true;
    }
    if (empty($CFG->behat_wwwroot)) {
        return false;
    }
    if (defined('CLI_SCRIPT') && CLI_SCRIPT && getenv('BEHAT_CLI')) {
        // Environment variable makes CLI script run on Behat instance.
        echo "BEHAT_CLI: This command line script is running on the acceptance testing site.\n\n";
        return true;
    }
    if (isset($_SERVER['REMOTE_ADDR']) and behat_is_requested_url($CFG->behat_wwwroot)) {
        // Something is accessing the web server like a real browser.
        return true;
    }

    return false;
}

/**
 * Fix variables for parallel behat testing.
 * - behat_wwwroot = behat_wwwroot{behatrunprocess}
 * - behat_dataroot = behat_dataroot{behatrunprocess}
 * - behat_prefix = behat_prefix.{behatrunprocess}
 **/
function behat_update_vars_for_process() {
    global $CFG;

    $allowedconfigoverride = array('dbtype', 'dblibrary', 'dbhost', 'dbname', 'dbuser', 'dbpass', 'behat_prefix',
        'behat_wwwroot', 'behat_dataroot');
    $behatrunprocess = behat_get_run_process();
    $CFG->behatrunprocess = $behatrunprocess;

    // Data directory will be a directory under parent directory.
    $CFG->behat_dataroot_parent = $CFG->behat_dataroot;
    $CFG->behat_dataroot .= '/'. BEHAT_PARALLEL_SITE_NAME;

    if ($behatrunprocess) {
        if (empty($CFG->behat_parallel_run[$behatrunprocess - 1]['behat_wwwroot'])) {
            // Set www root for run process.
            if (isset($CFG->behat_wwwroot) &&
                !preg_match("#/" . BEHAT_PARALLEL_SITE_NAME . $behatrunprocess . "\$#", $CFG->behat_wwwroot)) {
                $CFG->behat_wwwroot .= "/" . BEHAT_PARALLEL_SITE_NAME . $behatrunprocess;
            }
        }

        if (empty($CFG->behat_parallel_run[$behatrunprocess - 1]['behat_dataroot'])) {
            // Set behat_dataroot.
            if (!preg_match("#" . $behatrunprocess . "\$#", $CFG->behat_dataroot)) {
                $CFG->behat_dataroot .= $behatrunprocess;
            }
        }

        // Set behat_prefix for db, just suffix run process number, to avoid max length exceed.
        // NOTE: This will not work for parallel process > 9.
        $CFG->behat_prefix .= "{$behatrunprocess}_";

        if (!empty($CFG->behat_parallel_run[$behatrunprocess - 1])) {
            // Override allowed config vars.
            foreach ($allowedconfigoverride as $config) {
                if (isset($CFG->behat_parallel_run[$behatrunprocess - 1][$config])) {
                    $CFG->$config = $CFG->behat_parallel_run[$behatrunprocess - 1][$config];
                }
            }
        }
    }
}

/**
 * Checks if the URL requested by the user matches the provided argument
 *
 * @param string $url
 * @return bool Returns true if it matches.
 */
function behat_is_requested_url($url) {

    $parsedurl = parse_url($url . '/');
    if (!isset($parsedurl['port'])) {
        $parsedurl['port'] = ($parsedurl['scheme'] === 'https') ? 443 : 80;
    }
    $parsedurl['path'] = rtrim($parsedurl['path'], '/');

    // Removing the port.
    $pos = strpos($_SERVER['HTTP_HOST'], ':');
    if ($pos !== false) {
        $requestedhost = substr($_SERVER['HTTP_HOST'], 0, $pos);
    } else {
        $requestedhost = $_SERVER['HTTP_HOST'];
    }

    // The path should also match.
    if (empty($parsedurl['path'])) {
        $matchespath = true;
    } else if (strpos($_SERVER['SCRIPT_NAME'], $parsedurl['path']) === 0) {
        $matchespath = true;
    }

    // The host and the port should match
    if ($parsedurl['host'] == $requestedhost && $parsedurl['port'] == $_SERVER['SERVER_PORT'] && !empty($matchespath)) {
        return true;
    }

    return false;
}

/**
 * Get behat run process from either $_SERVER or command config.
 *
 * @return bool|int false if single run, else run process number.
 */
function behat_get_run_process() {
    global $argv, $CFG;
    $behatrunprocess = false;

    // Get behat run process, if set.
    if (defined('BEHAT_CURRENT_RUN') && BEHAT_CURRENT_RUN) {
        $behatrunprocess = BEHAT_CURRENT_RUN;
    } else if (!empty($_SERVER['REMOTE_ADDR'])) {
        // Try get it from config if present.
        if (!empty($CFG->behat_parallel_run)) {
            foreach ($CFG->behat_parallel_run as $run => $behatconfig) {
                if (isset($behatconfig['behat_wwwroot']) && behat_is_requested_url($behatconfig['behat_wwwroot'])) {
                    $behatrunprocess = $run + 1; // We start process from 1.
                    break;
                }
            }
        }
        // Check if parallel site prefix is used.
        if (empty($behatrunprocess) && preg_match('#/' . BEHAT_PARALLEL_SITE_NAME . '(.+?)/#', $_SERVER['REQUEST_URI'])) {
            $dirrootrealpath = str_replace("\\", "/", realpath($CFG->dirroot));
            $serverrealpath = str_replace("\\", "/", realpath($_SERVER['SCRIPT_FILENAME']));
            $afterpath = str_replace($dirrootrealpath.'/', '', $serverrealpath);
            if (!$behatrunprocess = preg_filter("#.*/" . BEHAT_PARALLEL_SITE_NAME . "(.+?)/$afterpath#", '$1',
                $_SERVER['SCRIPT_FILENAME'])) {
                throw new Exception("Unable to determine behat process [afterpath=" . $afterpath .
                    ", scriptfilename=" . $_SERVER['SCRIPT_FILENAME'] . "]!");
            }
        }
    } else if (defined('BEHAT_TEST') || defined('BEHAT_UTIL')) {
        $behatconfig = '';

        if ($match = preg_filter('#--run=(.+)#', '$1', $argv)) {
            // Try to guess the run from the existence of the --run arg.
            $behatrunprocess = reset($match);

        } else {
            // Try to guess the run from the existence of the --config arg. Note there are 2 alternatives below.
            if ($k = array_search('--config', $argv)) {
                // Alternative 1: --config /path/to/config.yml => (next arg, pick it).
                $behatconfig = str_replace("\\", "/", $argv[$k + 1]);

            } else if ($config = preg_filter('#^(?:--config[ =]*)(.+)$#', '$1', $argv)) {
                // Alternative 2: --config=/path/to/config.yml => (same arg, just get the path part).
                $behatconfig = str_replace("\\", "/", reset($config));
            }

            // Try get it from config if present.
            if ($behatconfig) {
                if (!empty($CFG->behat_parallel_run)) {
                    foreach ($CFG->behat_parallel_run as $run => $parallelconfig) {
                        if (!empty($parallelconfig['behat_dataroot']) &&
                                $parallelconfig['behat_dataroot'] . '/behat/behat.yml' == $behatconfig) {
                            $behatrunprocess = $run + 1; // We start process from 1.
                            break;
                        }
                    }
                }
                // Check if default behat dataroot increment was done.
                if (empty($behatrunprocess)) {
                    $behatdataroot = str_replace("\\", "/", $CFG->behat_dataroot . '/' . BEHAT_PARALLEL_SITE_NAME);
                    $behatrunprocess = preg_filter("#^{$behatdataroot}" . "(.+?)[/|\\\]behat[/|\\\]behat\.yml#", '$1',
                        $behatconfig);
                }
            }
        }
    }

    return $behatrunprocess;
}

/**
 * Execute commands in parallel.
 *
 * @param array $cmds list of commands to be executed.
 * @param string $cwd absolute path of working directory.
 * @param int $delay time in seconds to add delay between each parallel process.
 * @return array list of processes.
 */
function cli_execute_parallel($cmds, $cwd = null, $delay = 0) {
    require_once(__DIR__ . "/../../../vendor/autoload.php");

    $processes = array();

    // Create child process.
    foreach ($cmds as $name => $cmd) {
        if (method_exists('\\Symfony\\Component\\Process\\Process', 'fromShellCommandline')) {
            // Process 4.2 and up.
            $process = Symfony\Component\Process\Process::fromShellCommandline($cmd);
        } else {
            // Process 4.1 and older.
            $process = new Symfony\Component\Process\Process(null);
            $process->setCommandLine($cmd);
        }

        $process->setWorkingDirectory($cwd);
        $process->setTimeout(null);
        $processes[$name] = $process;
        $processes[$name]->start();

        // If error creating process then exit.
        if ($processes[$name]->getStatus() !== 'started') {
            echo "Error starting process: $name";
            foreach ($processes[$name] as $process) {
                if ($process) {
                    $process->signal(SIGKILL);
                }
            }
            exit(1);
        }

        // Sleep for specified delay.
        if ($delay) {
            sleep($delay);
        }
    }
    return $processes;
}

/**
 * Get command flags for an option/value combination
 *
 * @param string $option
 * @param string|bool|null $value
 * @return string
 */
function behat_get_command_flags(string $option, $value): string {
    $commandoptions = '';
    if (is_bool($value)) {
        if ($value) {
            return " --{$option}";
        } else {
            return " --no-{$option}";
        }
    } else if ($value !== null) {
        return " --$option=\"$value\"";
    }
    return '';
}
