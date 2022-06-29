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
 * This file is serving optimised JS and WASM for ogv.js.
 *
 * @package media_videojs
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// We need just the values from config.php and minlib.php.
define('ABORT_AFTER_CONFIG', true);
require_once('../../../config.php'); // This stops immediately at the beginning of lib/setup.php.
require_once($CFG->dirroot . '/lib/jslib.php');
require_once($CFG->dirroot . '/lib/wasmlib.php');

$slashargument = min_get_slash_argument();
if (!$slashargument) {
    // The above call to min_get_slash_argument should always work.
    die('Invalid request');
}

$slashargument = ltrim($slashargument, '/');
if (substr_count($slashargument, '/') < 1) {
    header('HTTP/1.0 404 not found');
    die('Slash argument must contain both a revision and a file path');
}

// Get all the library files (js and wasm) of the OGV.
$basepath = $CFG->dirroot . '/media/player/videojs/ogvjs/';
$jsfiles = [];
$files = glob("{$basepath}*.{js,wasm}", GLOB_BRACE);
foreach ($files as $file) {
    $jsfiles[] = basename($file);
}

// Split into revision and module name.
list($rev, $file) = explode('/', $slashargument, 2);
$rev  = min_clean_param($rev, 'INT');
$file = min_clean_param($file, 'SAFEPATH');

if (empty($jsfiles) || !in_array($file, $jsfiles)) {
    // We can't find the requested file.
    header('HTTP/1.0 404 not found');
    exit(0);
}

// Check if the requesting file is Javascript or Web Assembly.
$iswasm = media_videojs_ogvloader_is_wasm_file($file);

// Use the caching only for meaningful revision numbers which prevents future cache poisoning.
if ($rev > 0 and $rev < (time() + 60 * 60)) {
    // We are lazy loading a single file - so include the filename in the etag.
    $etag = sha1($rev . '/' . $file);
    $candidate = $CFG->localcachedir . '/ogvloader/' . $etag;

    if (file_exists($candidate)) {
        // Cache exist.
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // We do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev parameter.
            media_videojs_ogvloader_send_unmodified($iswasm, $candidate, $etag);
        }
        media_videojs_ogvloader_send_cached($iswasm, $candidate, $etag);
        exit(0);
    } else {
        // Cache does not exist. Create one.
        $filecontent = file_get_contents($basepath . $file);
        if ($filecontent === false) {
            error_log('Failed to load the library ' . $file);
            $filecontent = "/* Failed to load library file {$file}. */\n";
        }

        $filecontent = media_videojs_ogvloader_add_module_module_name_if_necessary($iswasm, $filecontent);
        media_videojs_ogvloader_write_cache_file_content($iswasm, $candidate, $filecontent);
        // Verify nothing failed in cache file creation.
        clearstatcache();
        if (file_exists($candidate)) {
            media_videojs_ogvloader_send_cached($iswasm, $candidate, $etag);
            exit(0);
        }
    }
}

// If we've made it here then we're in "dev mode" where everything is lazy loaded.
// So all files will be served one at a time.
$filecontent = file_get_contents($basepath . $file);
$filecontent = rtrim($filecontent);
$filecontent = media_videojs_ogvloader_add_module_module_name_if_necessary($iswasm, $filecontent);
media_videojs_ogvloader_send_uncached($iswasm, $filecontent);

/**
 * Check the given file is a Web Assembly file or not
 *
 * @param string $filename File name to check
 * @return bool Whether the file is Web Assembly or not
 */
function media_videojs_ogvloader_is_wasm_file(string $filename): bool {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return $ext == 'wasm';
}

/**
 * Add Moodle module name to the Javascript module if necessary
 *
 * @param bool $iswasm Whether the file is Web Assembly or not
 * @param string $content File content
 * @return string
 */
function media_videojs_ogvloader_add_module_module_name_if_necessary(bool $iswasm, string $content): string {
    if (!$iswasm && preg_match('/define\(\s*(\[|function)/', $content)) {
        // If the JavaScript module has been defined without specifying a name then we'll
        // add the Moodle module name now.
        $replace = 'define(\'media_videojs/video-lazy\', ';
        $search = 'define(';
        // Replace only the first occurrence.
        $content = implode($replace, explode($search, $content, 2));
    }

    return $content;
}

/**
 * Create cache file content
 *
 * @param bool $iswasm Whether the file is Web Assembly or not
 * @param string $candidate Full file path to cache file
 * @param string $filecontent File content
 */
function media_videojs_ogvloader_write_cache_file_content(bool $iswasm, string $candidate, string $filecontent): void {
    $iswasm ? wasm_write_cache_file_content($candidate, $filecontent) : js_write_cache_file_content($candidate, $filecontent);
}

/**
 * Send file content with as much caching as possible
 *
 * @param bool $iswasm Whether the file is Web Assembly or not
 * @param string $candidate Full file path to cache file
 * @param string $etag Etag
 */
function media_videojs_ogvloader_send_cached(bool $iswasm, string $candidate, string $etag): void {
    $iswasm ? wasm_send_cached($candidate, $etag, 'ogvloader.php') : js_send_cached($candidate, $etag, 'ogvloader.php');
}

/**
 * Send file without any caching
 *
 * @param bool $iswasm Whether the file is Web Assembly or not
 * @param string $ilecontent File content
 */
function media_videojs_ogvloader_send_uncached(bool $iswasm, string $ilecontent): void {
    $iswasm ? wasm_send_uncached($ilecontent, 'ogvloader.php') : js_send_uncached($ilecontent, 'ogvloader.php');
}

/**
 * Send the file not modified headers
 *
 * @param bool $iswasm Whether the file is Web Assembly or not
 * @param int $candidate Full file path to cache file
 * @param string $etag Etag
 */
function media_videojs_ogvloader_send_unmodified(bool $iswasm, int $candidate, string $etag): void {
    $iswasm ? wasm_send_unmodified(filemtime($candidate), $etag) : js_send_unmodified(filemtime($candidate), $etag);
}
