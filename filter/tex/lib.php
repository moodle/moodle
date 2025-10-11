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
 * TeX filter library functions.
 *
 * @package    filter
 * @subpackage tex
 * @copyright  2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu
 *             Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Default timeout in seconds for mimetex command execution.
defined('FILTER_TEX_MIMETEX_TIMEOUT') || define('FILTER_TEX_MIMETEX_TIMEOUT', 5);

/**
 * Check if the current operating system is Windows.
 *
 * @return bool True if running on Windows, false otherwise.
 */
function filter_tex_is_windows(): bool {
    return (PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows");
}

function filter_tex_get_executable($debug=false) {
    global $CFG;

    if (filter_tex_is_windows()) {
        return "$CFG->dirroot/filter/tex/mimetex.exe";
    }

    if ($pathmimetex = get_config('filter_tex', 'pathmimetex')) {
        if (is_executable($pathmimetex)) {
            return $pathmimetex;
        } else {
            throw new \moodle_exception('mimetexnotexecutable', 'error');
        }
    }

    $custom_commandpath = "$CFG->dirroot/filter/tex/mimetex";
    if (file_exists($custom_commandpath)) {
        if (is_executable($custom_commandpath)) {
            return $custom_commandpath;
        } else {
            throw new \moodle_exception('mimetexnotexecutable', 'error');
        }
    }

    switch (PHP_OS) {
        case "Darwin":  return "$CFG->dirroot/filter/tex/mimetex.darwin";
        case "FreeBSD": return "$CFG->dirroot/filter/tex/mimetex.freebsd";
        case "Linux":
            if (php_uname('m') == 'aarch64') {
                return "$CFG->dirroot/filter/tex/mimetex.linux.aarch64";
            }

            return "$CFG->dirroot/filter/tex/mimetex.linux";
    }

    throw new \moodle_exception('mimetexisnotexist', 'error');
}

/**
 * Check the formula expression against the list of denied keywords.
 *
 * List of allowed could be more complete but also harder to maintain.
 *
 * @param string $texexp Formula expression to check.
 * @return string Formula expression with denied keywords replaced with 'forbiddenkeyword'.
 */
function filter_tex_sanitize_formula(string $texexp): string {

    $denylist = [
        'include', 'command', 'loop', 'repeat', 'open', 'toks', 'output',
        'input', 'catcode', 'name', '^^',
        '\def', '\edef', '\gdef', '\xdef',
        '\every', '\errhelp', '\errorstopmode', '\scrollmode', '\nonstopmode',
        '\batchmode', '\read', '\write', 'csname', '\newhelp', '\uppercase',
        '\lowercase', '\relax', '\aftergroup',
        '\afterassignment', '\expandafter', '\noexpand', '\special',
        '\let', '\futurelet', '\else', '\fi', '\chardef', '\makeatletter', '\afterground',
        '\noexpand', '\line', '\mathcode', '\item', '\section', '\mbox', '\declarerobustcommand',
        '\ExplSyntaxOn', '\pdffiledump', '\mathtex',
    ];

    $allowlist = ['inputenc'];

    // Add encoded backslash (&#92;) versions of backslashed items to deny list.
    $encodedslashdenylist = array_map(function($value) {
        $encoded = str_replace('\\', '&#92;', $value);
        // Return an encoded slash version if a slash is found, otherwise null so we can filter it off.
        return $encoded != $value ? $encoded : null;
    }, $denylist);
    $encodedslashdenylist = array_filter($encodedslashdenylist);
    $denylist = array_merge($denylist, $encodedslashdenylist);

    // Prepare the denylist for regular expression.
    $denylist = array_map(function($value){
        return '/' . preg_quote($value, '/') . '/i';
    }, $denylist);

    // Prepare the allowlist for regular expression.
    $allowlist = array_map(function($value){
        return '/\bforbiddenkeyword_(' . preg_quote($value, '/') . ')\b/i';
    }, $allowlist);

    // First, mangle all denied words.
    $texexp = preg_replace_callback($denylist,
        function($matches) {
            // Remove backslashes to make commands impotent.
            $noslashes = str_replace('\\', '', $matches[0]);
            return 'forbiddenkeyword_' . $noslashes;
        },
        $texexp
    );

    // Then, change back the allowed words.
    $texexp = preg_replace_callback($allowlist,
        function($matches) {
            return $matches[1];
        },
        $texexp
    );

    return $texexp;
}

function filter_tex_get_cmd($pathname, $texexp) {
    $texexp = filter_tex_sanitize_formula($texexp);
    $texexp = escapeshellarg($texexp);
    $executable = filter_tex_get_executable(false);

    if (filter_tex_is_windows()) {
        $executable = str_replace(' ', '^ ', $executable);
        return "$executable ++ -e  \"$pathname\" -- $texexp";

    } else {
        return "\"$executable\" -e \"$pathname\" -- $texexp";
    }
}

/**
 * Run mimetex command with a timeout on Windows.
 *
 * @param string $cmd            Command string to execute.
 * @param int    $timeoutmicros  Timeout in microseconds.
 * @return array Array with keys: code, timedout, status, errors.
 */
function filter_tex_exec_windows(string $cmd, int $timeoutmicros): array {
    // Create temporary file for stderr.
    $temperr = tempnam(sys_get_temp_dir(), 'err_');

    $descriptors = [
        0 => ['file', 'NUL', 'r'], // STDIN.
        1 => ['file', 'NUL', 'w'], // STDOUT.
        2 => ['file', $temperr, 'w'], // STDERR.
    ];

    $process = proc_open($cmd, $descriptors, $pipes);
    if (!is_resource($process)) {
        unlink($temperr);
        return [
            'code' => 127, // Command not found.
            'timedout' => false,
            'status' => [],
            'errors' => '',
        ];
    }

    $timedout = false;
    while ($timeoutmicros > 0) {
        $start = microtime(true);
        $status = proc_get_status($process);

        if (!$status['running']) {
            break;
        }

        $timeoutmicros -= (microtime(true) - $start) * 1000000;
        if ($timeoutmicros <= 0) {
            $timedout = true;
            $pid = (int)($status['pid'] ?? 0);
            exec('taskkill /F /T /PID ' . $pid . ' 2>NUL');
            break;
        }

        usleep(50000); // Sleep for 50ms.
    }

    $status = proc_get_status($process);
    $code = proc_close($process);

    // Capture stderr from temp file.
    $errors = file_get_contents($temperr);
    unlink($temperr);

    return [
        'code' => $code,
        'timedout' => $timedout,
        'status' => $status,
        'errors' => $errors,
    ];
}

/**
 * Run mimetex command with a timeout on Unix-like systems.
 *
 * @param string $cmd            Command string to execute.
 * @param int    $timeoutmicros  Timeout in microseconds.
 * @return array Array with keys: code, timedout, status, errors.
 */
function filter_tex_exec_unix(string $cmd, int $timeoutmicros): array {
    // File descriptors passed to the process.
    $descriptors = [
        0 => ['pipe', 'r'], // STDIN.
        1 => ['pipe', 'w'], // STDOUT.
        2 => ['pipe', 'w'], // STDERR.
    ];

    $process = proc_open('exec ' . $cmd, $descriptors, $pipes);
    if (!is_resource($process)) {
        return [
            'code' => 127, // Command not found.
            'timedout' => false,
            'status' => [],
            'errors' => '',
        ];
    }

    fclose($pipes[0]);
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $errors = '';
    $timedout = false;
    while ($timeoutmicros > 0) {
        $start = microtime(true);

        $read = [$pipes[1], $pipes[2]];
        $other = [];
        stream_select($read, $other, $other, 0, (int)$timeoutmicros);

        $status = proc_get_status($process);

        stream_get_contents($pipes[1]); // Discard stdout to prevent pipe blocking.
        $errors .= stream_get_contents($pipes[2]);

        if (!$status['running']) {
            break;
        }

        $timeoutmicros -= (microtime(true) - $start) * 1000000;
        if ($timeoutmicros <= 0) {
            $timedout = true;
            proc_terminate($process);
            break;
        }

        usleep(50000); // Sleep for 50ms.
    }

    // Read any remaining data from pipes.
    stream_get_contents($pipes[1]); // Discard remaining stdout.
    $errors .= stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $status = proc_get_status($process);
    $code = proc_close($process);

    return [
        'code' => $code,
        'timedout' => $timedout,
        'status' => $status,
        'errors' => $errors,
    ];
}

/**
 * Run mimetex command with a timeout.
 *
 * @param string   $cmd   Command string to execute.
 * @param int|null &$code Exit code (passed by reference, set by function).
 * @return void
 */
function filter_tex_exec(string $cmd, ?int &$code): void {
    $timeoutmicros = FILTER_TEX_MIMETEX_TIMEOUT * 1000000;

    if (filter_tex_is_windows()) {
        $result = filter_tex_exec_windows($cmd, $timeoutmicros);
    } else {
        $result = filter_tex_exec_unix($cmd, $timeoutmicros);
    }

    if ($result['errors']) {
        debugging('filter_tex_exec errors: ' . $result['errors'], DEBUG_DEVELOPER);
    }

    if ($result['timedout']) {
        $code = 124;
    } else if ($result['code'] === -1 && isset($result['status']['exitcode']) && $result['status']['exitcode'] !== -1) {
        $code = $result['status']['exitcode'];
    } else {
        $code = $result['code'];
    }
}

/**
 * Purge all caches when settings changed.
 */
function filter_tex_updatedcallback($name) {
    global $CFG, $DB;
    reset_text_filters_cache();

    if (file_exists("$CFG->dataroot/filter/tex")) {
        remove_dir("$CFG->dataroot/filter/tex");
    }
    if (file_exists("$CFG->dataroot/filter/algebra")) {
        remove_dir("$CFG->dataroot/filter/algebra");
    }
    if (file_exists("$CFG->tempdir/latex")) {
        remove_dir("$CFG->tempdir/latex");
    }

    $DB->delete_records('cache_filters', array('filter'=>'tex'));
    $DB->delete_records('cache_filters', array('filter'=>'algebra'));

    $pathlatex = get_config('filter_tex', 'pathlatex');
    if ($pathlatex === false) {
        // detailed settings not present yet
        return;
    }

    $pathlatex = trim($pathlatex, " '\"");
    $pathdvips = trim(get_config('filter_tex', 'pathdvips'), " '\"");
    $pathconvert = trim(get_config('filter_tex', 'pathconvert'), " '\"");
    $pathdvisvgm = trim(get_config('filter_tex', 'pathdvisvgm'), " '\"");

    $supportedformats = array('gif');
    if ((is_file($pathlatex) && is_executable($pathlatex)) &&
            (is_file($pathdvips) && is_executable($pathdvips))) {
        if (is_file($pathconvert) && is_executable($pathconvert)) {
             $supportedformats[] = 'png';
        }
        if (is_file($pathdvisvgm) && is_executable($pathdvisvgm)) {
             $supportedformats[] = 'svg';
        }
    }
    if (!in_array(get_config('filter_tex', 'convertformat'), $supportedformats)) {
        set_config('convertformat', array_pop($supportedformats), 'filter_tex');
    }

}

