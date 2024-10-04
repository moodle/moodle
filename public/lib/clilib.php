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
 * Write a text to the given stream
 *
 * @param string $text text to be written
 * @param resource $stream output stream to be written to, defaults to STDOUT
 */
function cli_write($text, $stream=STDOUT) {
    fwrite($stream, $text);
}

/**
 * Write a text followed by an end of line symbol to the given stream
 *
 * @param string $text text to be written
 * @param resource $stream output stream to be written to, defaults to STDOUT
 */
function cli_writeln($text, $stream=STDOUT) {
    cli_write($text.PHP_EOL, $stream);
}

/**
 * Get input from user
 * @param string $prompt text prompt, should include possible options
 * @param string $default default value when enter pressed
 * @param array $options list of allowed options, empty means any text
 * @param bool $casesensitive true if options are case sensitive
 * @return string entered text
 */
function cli_input($prompt, $default='', ?array $options=null, $casesensitiveoptions=false) {
    cli_writeln($prompt);
    cli_write(': ');
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
            cli_writeln(get_string('cliincorrectvalueretry', 'admin'));
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
function cli_get_params(array $longoptions, ?array $shortmapping=null) {
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

                if (substr($key, 0, 3) === 'no-' && !array_key_exists($key, $longoptions)
                        && array_key_exists(substr($key, 3), $longoptions)) {
                    // Support flipping the boolean value.
                    $value = !$value;
                    $key = substr($key, 3);
                }
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
 * This sets the cli process title suffix
 *
 * An example is appending current Task API info so a sysadmin can immediately
 * see what task a cron process is running at any given moment.
 *
 * @param string $suffix process suffix
 */
function cli_set_process_title_suffix(string $suffix) {
    if (CLI_SCRIPT && function_exists('cli_set_process_title') && isset($_SERVER['argv'])) {
        $command = join(' ', $_SERVER['argv']);
        @cli_set_process_title("php $command ($suffix)");
    }
}

/**
 * Print or return section separator string
 * @param bool $return false means print, true return as string
 * @return mixed void or string
 */
function cli_separator($return=false) {
    $separator = str_repeat('-', 79).PHP_EOL;
    if ($return) {
        return $separator;
    } else {
        cli_write($separator);
    }
}

/**
 * Print or return section heading string
 * @param string $string text
 * @param bool $return false means print, true return as string
 * @return mixed void or string
 */
function cli_heading($string, $return=false) {
    $string = "== $string ==".PHP_EOL;
    if ($return) {
        return $string;
    } else {
        cli_write($string);
    }
}

/**
 * Write error notification
 * @param $text
 * @return void
 */
function cli_problem($text) {
    cli_writeln($text, STDERR);
}

/**
 * Write to standard error output and exit with the given code
 *
 * @param string $text
 * @param int $errorcode
 * @return void (does not return)
 */
function cli_error($text, $errorcode=1) {
    cli_writeln($text.PHP_EOL, STDERR);
    die($errorcode);
}

/**
 * Print an ASCII version of the Moodle logo.
 *
 * @param int $padding left padding of the logo
 * @param bool $return should we print directly (false) or return the string (true)
 * @return mixed void or string
 */
function cli_logo($padding=2, $return=false) {

    $lines = array(
        '                               .-..-.       ',
        ' _____                         | || |       ',
        '/____/-.---_  .---.  .---.  .-.| || | .---. ',
        '| |  _   _  |/  _  \\/  _  \\/  _  || |/  __ \\',
        '* | | | | | || |_| || |_| || |_| || || |___/',
        '  |_| |_| |_|\\_____/\\_____/\\_____||_|\\_____)',
    );

    $logo = '';

    foreach ($lines as $line) {
        $logo .= str_repeat(' ', $padding);
        $logo .= $line;
        $logo .= PHP_EOL;
    }

    if ($return) {
        return $logo;
    } else {
        cli_write($logo);
    }
}

/**
 * Substitute cursor, colour, and bell placeholders in a CLI output to ANSI escape characters when ANSI is available.
 *
 * @param string $message
 * @return string
 */
function cli_ansi_format(string $message): string {
    global $CFG;

    $replacements = [
        "<newline>" => "\n",
        "<bell>" => "\007",

        // Cursor movement: https://www.tldp.org/HOWTO/Bash-Prompt-HOWTO/x361.html.
        "<cursor:save>"     => "\033[s",
        "<cursor:restore>"  => "\033[u",
        "<cursor:up>"       => "\033[1A",
        "<cursor:down>"     => "\033[1B",
        "<cursor:forward>"  => "\033[1C",
        "<cursor:back>"     => "\033[1D",
    ];

    $colours = [
        'normal'        => '0;0',
        'black'         => '0;30',
        'darkGray'      => '1;30',
        'red'           => '0;31',
        'lightRed'      => '1;31',
        'green'         => '0;32',
        'lightGreen'    => '1;32',
        'brown'         => '0;33',
        'yellow'        => '1;33',
        'lightYellow'   => '0;93',
        'blue'          => '0;34',
        'lightBlue'     => '1;34',
        'purple'        => '0;35',
        'lightPurple'   => '1;35',
        'cyan'          => '0;36',
        'lightCyan'     => '1;36',
        'lightGray'     => '0;37',
        'white'         => '1;37',
    ];
    $bgcolours = [
        'black'         => '40',
        'red'           => '41',
        'green'         => '42',
        'yellow'        => '43',
        'blue'          => '44',
        'magenta'       => '45',
        'cyan'          => '46',
        'white'         => '47',
    ];

    foreach ($colours as $colour => $code) {
        $replacements["<colour:{$colour}>"] = "\033[{$code}m";
    }
    foreach ($bgcolours as $colour => $code) {
        $replacements["<bgcolour:{$colour}>"] = "\033[{$code}m";
    }

    // Windows don't support ANSI code by default, but does if ANSICON is available.
    $isansicon = getenv('ANSICON');
    if (($CFG->ostype === 'WINDOWS') && empty($isansicon)) {
        return str_replace(array_keys($replacements), '', $message);
    }

    return str_replace(array_keys($replacements), array_values($replacements), $message);
}
