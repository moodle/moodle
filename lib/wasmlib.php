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
 * This file contains various Web Assembly related functions,
 * all functions here are self-contained and can be used in ABORT_AFTER_CONFIG scripts.
 *
 * @package core_lib
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Send Web Assembly file content with as much caching as possible
 *
 * @param string $wasmpath Path to Web Assembly file
 * @param string $etag Etag
 * @param string $filename File name to be served
 */
function wasm_send_cached(string $wasmpath, string $etag, string $filename = 'wasm.php'): void {
    require(__DIR__ . '/xsendfilelib.php');

    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;

    header('Etag: "' . $etag . '"');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($wasmpath)) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime . ', immutable');
    header('Accept-Ranges: none');
    header('Content-Type: application/wasm');

    if (xsendfile($wasmpath)) {
        die;
    }

    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . filesize($wasmpath));
    }

    readfile($wasmpath);
    die;
}

/**
 * Send Web Assembly file without any caching
 *
 * @param string $wasm Web Assembly file content
 * @param string $filename File name to be served
 */
function wasm_send_uncached(string $wasm, string $filename = 'wasm.php'): void {
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 2) . ' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: application/wasm');
    header('Content-Length: ' . strlen($wasm));

    echo $wasm;
    die;
}

/**
 * Send Web Assembly file not modified headers
 *
 * @param int $lastmodified Last modified timestamp
 * @param string $etag Etag
 */
function wasm_send_unmodified(int $lastmodified, string $etag): void {
    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;
    header('HTTP/1.1 304 Not Modified');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Content-Type: application/wasm');
    header('Etag: "' . $etag . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    die;
}

/**
 * Create cache file for Web Assembly content
 *
 * @param string $file full file path to cache file
 * @param string $content Web Assembly content
 */
function wasm_write_cache_file_content(string $file, string $content): void {
    global $CFG;

    clearstatcache();
    if (!file_exists(dirname($file))) {
        @mkdir(dirname($file), $CFG->directorypermissions, true);
    }

    // Prevent serving of incomplete file from concurrent request,
    // the rename() should be more atomic than fwrite().
    ignore_user_abort(true);
    if ($fp = fopen($file . '.tmp', 'xb')) {
        fwrite($fp, $content);
        fclose($fp);
        rename($file . '.tmp', $file);
        @chmod($file, $CFG->filepermissions);
        @unlink($file . '.tmp'); // Just in case anything fails.
    }
    ignore_user_abort(false);
    if (connection_aborted()) {
        die;
    }
}
