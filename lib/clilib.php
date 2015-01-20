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
 * Command line utility functions and classes
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, sometimes we use this before requiring Moodle libs!

/**
 * Get input from user
 * @param string $prompt text prompt, should include possible options
 * @param string $default default value when enter pressed
 * @param array $options list of allowed options, empty means any text
 * @param bool $casesensitive true if options are case sensitive
 * @return string entered text
 */
function cli_input($prompt, $default='', array $options=null, $casesensitiveoptions=false) {
    echo $prompt;
    echo "\n: ";
    $input = fread(STDIN, 2048);
    $input = trim($input);
    if ($input === '') {
        $input = $default;
    }
    if ($options) {
        if (!$casesensitiveoptions) {
            $input = strtolower($input);
        }
        if (!in_array($input, $options)) {
            echo "Incorrect value, please retry.\n"; // TODO: localize, mark as needed in install
            return cli_input($prompt, $default, $options, $casesensitiveoptions);
        }
    }
    return $input;
}

/**
 * Returns cli script parameters.
 * @param array $longoptions array of --style options ex:('verbose'=>false)
 * @param array $shortmapping array describing mapping of short to long style options ex:('h'=>'help', 'v'=>'verbose')
 * @return array array of arrays, options, unrecognised as optionlongname=>value
 */
function cli_get_params(array $longoptions, array $shortmapping=null) {
    $shortmapping = (array)$shortmapping;
    $options      = array();
    $unrecognized = array();

    if (empty($_SERVER['argv'])) {
        // bad luck, we can continue in interactive mode ;-)
        return array($options, $unrecognized);
    }
    $rawoptions = $_SERVER['argv'];

    //remove anything after '--', options can not be there
    if (($key = array_search('--', $rawoptions)) !== false) {
        $rawoptions = array_slice($rawoptions, 0, $key);
    }

    //remove script
    unset($rawoptions[0]);
    foreach ($rawoptions as $raw) {
        if (substr($raw, 0, 2) === '--') {
            $value = substr($raw, 2);
            $parts = explode('=', $value);
            if (count($parts) == 1) {
                $key   = reset($parts);
                $value = true;
            } else {
                $key = array_shift($parts);
                $value = implode('=', $parts);
            }
            if (array_key_exists($key, $longoptions)) {
                $options[$key] = $value;
            } else {
                $unrecognized[] = $raw;
            }

        } else if (substr($raw, 0, 1) === '-') {
            $value = substr($raw, 1);
            $parts = explode('=', $value);
            if (count($parts) == 1) {
                $key   = reset($parts);
                $value = true;
            } else {
                $key = array_shift($parts);
                $value = implode('=', $parts);
            }
            if (array_key_exists($key, $shortmapping)) {
                $options[$shortmapping[$key]] = $value;
            } else {
                $unrecognized[] = $raw;
            }
        } else {
            $unrecognized[] = $raw;
            continue;
        }
    }
    //apply defaults
    foreach ($longoptions as $key=>$default) {
        if (!array_key_exists($key, $options)) {
            $options[$key] = $default;
        }
    }
    // finished
    return array($options, $unrecognized);
}

/**
 * Print or return section separator string
 * @param bool $return false means print, true return as string
 * @return mixed void or string
 */
function cli_separator($return=false) {
    $separator = str_repeat('-', 79)."\n";
    if ($return) {
        return $separator;
    } else {
        echo $separator;
    }
}

/**
 * Print or return section heading string
 * @param string $string text
 * @param bool $return false means print, true return as string
 * @return mixed void or string
 */
function cli_heading($string, $return=false) {
    $string = "== $string ==\n";
    if ($return) {
        return $string;
    } else {
        echo $string;
    }
}

/**
 * Write error notification
 * @param $text
 * @return void
 */
function cli_problem($text) {
    fwrite(STDERR, $text."\n");
}

/**
 * Write to standard out and error with exit in error.
 *
 * @param string $text
 * @param int $errorcode
 * @return void (does not return)
 */
function cli_error($text, $errorcode=1) {
    fwrite(STDERR, $text);
    fwrite(STDERR, "\n");
    die($errorcode);
}

/**
 * Executes cli command and return handle.
 *
 * @param string $cmd command to be executed.
 * @param bool $die exit if command is not executed.
 * @return array list of handles and pipe.
 * @throws Exception if worker is not started,
 */
function cli_execute($cmd, $die = false) {
    $desc = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w'),
    );
    if (!($handle = proc_open($cmd, $desc, $pipes)) && $die) {
        throw new Exception('Error starting worker');
    }
    return array($handle, $pipes);
}

/**
 * Execute commands in parallel.
 *
 * @param array $cmds list of commands to be executed.
 * @param string $cwd aabsolute path of working directory.
 * @param bool $returnonfirstfail Will stop all process and return.
 *
 * @return bool status of all process.
 */
function cli_execute_parallel($cmds, $cwd = NULL, $returnonfirstfail = false, $addprefix = true) {
    require_once(__DIR__ . '/classes/process_manager.php');

    $overallstatus = false;
    $processmanager = new process_manager();

    // Create child process.
    foreach ($cmds as $name => $cmd) {
        if (!$processmanager->create($name, $cmd, $cwd) && $returnonfirstfail) {
            throw new Exception('Error starting worker');
        }
    }
    while (0 < count($processmanager)) {
        usleep(10000);
        list($status, $stdout, $stderr) = $processmanager->listen();

        if (!empty($status)) {
            foreach ($status as $name => $value) {
                // Something went wrong.
                if ((0 > $value)) {
                    throw new \RuntimeException();
                }
                $overallstatus = $overallstatus || (bool)$value;
                // Add prefix to process.
                $prefix = "";
                if ($addprefix && ($value === 0)) {
                    $prefix = '[' . $name . '] ';
                }
                if (!empty($stdout[$name]) && trim($stdout[$name])) {
                    echo $prefix . $stdout[$name];
                }
                if (!empty($stderr[$name]) && trim($stderr[$name])) {
                    echo $prefix . $stderr[$name];
                }
            }

            // Return if fail found.
            if ($returnonfirstfail && (bool)$value) {
                unset($processmanager);
                $processmanager = null;
                echo PHP_EOL;
                return $value;
            }
        }
    }

    echo PHP_EOL;
    return $overallstatus;
}

/**
 * Execute commands in sequence and return status code for each process.
 *
 * @param array $cmds commands to execute.
 * @param bool $returnonfirstfail if true then returns on any fail.
 * @return array status codes for each process.
 */
function cli_execute_sequential($cmds, $returnonfirstfail = false) {
    $procs = array();
    foreach ($cmds as $k => $cmd) {
        $procs[$k] = popen($cmd, 'r');
        passthru($cmd, $procs[$k]);
        if (($procs[$k] != 0) && $returnonfirstfail) {
            return $procs;
        }
    }
    return $procs;
}
