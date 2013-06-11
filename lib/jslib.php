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
 * This file contains various javascript related functions,
 * all functions here are self contained and can be used in ABORT_AFTER_CONFIG scripts.
 *
 * @package   core_lib
 * @copyright 2012 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//NOTE: do not verify MOODLE_INTERNAL here, this is used from themes too

/**
 * Send javascript file content with as much caching as possible
 * @param string $jspath
 * @param string $etag
 * @param string $filename
 */
function js_send_cached($jspath, $etag, $filename = 'javascript.php') {
    require(__DIR__ . '/xsendfilelib.php');

    $lifetime = 60*60*24*60; // 60 days only - the revision may get incremented quite often

    header('Etag: "'.$etag.'"');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($jspath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: application/javascript; charset=utf-8');

    if (xsendfile($jspath)) {
        die;
    }

    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.filesize($jspath));
    }

    readfile($jspath);
    die;
}

/**
 * Send javascript without any caching
 * @param string $js
 * @param string $filename
 */
function js_send_uncached($js, $filename = 'javascript.php') {
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + 2) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: application/javascript; charset=utf-8');
    header('Content-Length: '.strlen($js));

    echo $js;
    die;
}

/**
 * Send file not modified headers
 * @param int $lastmodified
 * @param string $etag
 */
function js_send_unmodified($lastmodified, $etag) {
    $lifetime = 60*60*24*60; // 60 days only - the revision may get incremented quite often
    header('HTTP/1.1 304 Not Modified');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Content-Type: application/javascript; charset=utf-8');
    header('Etag: "'.$etag.'"');
    if ($lastmodified) {
        header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');
    }
    die;
}

/**
 * Minify javascript files
 * @param array $files
 * @return string
 */
function js_minify($files) {
    // setup include path
    set_include_path(__DIR__ . '/minify/lib' . PATH_SEPARATOR . get_include_path());
    require_once('Minify.php');

    if (empty($files)) {
        return '';
    }

    if (0 === stripos(PHP_OS, 'win')) {
        Minify::setDocRoot(); // IIS may need help
    }
    // disable all caching, we do it in moodle
    Minify::setCache(null, false);

    $options = array(
        // JSMin is not GNU GPL compatible, use the plus version instead.
        'minifiers' => array(Minify::TYPE_JS => array('JSMinPlus', 'minify')),
        'bubbleCssImports' => false,
        // Don't gzip content we just want text for storage
        'encodeOutput' => false,
        // Maximum age to cache, not used but required
        'maxAge' => 1800,
        // The files to minify
        'files' => $files,
        // Turn orr URI rewriting
        'rewriteCssUris' => false,
        // This returns the CSS rather than echoing it for display
        'quiet' => true
    );

    $error = 'unknown';
    try {
        $result = Minify::serve('Files', $options);
        if ($result['success']) {
            return $result['content'];
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $error = str_replace("\r", ' ', $error);
        $error = str_replace("\n", ' ', $error);
    }

    // minification failed - try to inform the theme developer and include the non-minified version
    $js = <<<EOD
try {console.log('Error: Minimisation of javascript failed!');} catch (e) {}

// Error: $error
// Problem detected during javascript minimisation, please review the following code
// =================================================================================


EOD;
    foreach ($files as $jsfile) {
        $js .= file_get_contents($jsfile)."\n";
    }
    return $js;
}

/**
 * Create cache file for JS content
 * @param string $file full file path to cache file
 * @param string $content JS code
 */
function js_write_cache_file_content($file, $content) {
    global $CFG;

    clearstatcache();
    if (!file_exists(dirname($file))) {
        @mkdir(dirname($file), $CFG->directorypermissions, true);
    }

    // Prevent serving of incomplete file from concurrent request,
    // the rename() should be more atomic than fwrite().
    ignore_user_abort(true);
    if ($fp = fopen($file.'.tmp', 'xb')) {
        fwrite($fp, $content);
        fclose($fp);
        rename($file.'.tmp', $file);
        @chmod($file, $CFG->filepermissions);
        @unlink($file.'.tmp'); // just in case anything fails
    }
    ignore_user_abort(false);
    if (connection_aborted()) {
        die;
    }
}

/**
 * Sends a 404 message about CSS not being found.
 */
function js_send_css_not_found() {
    header('HTTP/1.0 404 not found');
    die('JS was not found, sorry.');
}
