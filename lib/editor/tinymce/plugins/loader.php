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
 * Loader for resource files within TinyMCE plugins.
 *
 * This loader handles requests which have the plugin version number in. These
 * requests are set to never expire from cache, to improve performance. Only
 * files within the 'tinymce' folder of the plugin will be served.
 *
 * Note there are no access checks in this script - you do not have to be
 * logged in to retrieve the plugin resource files.
 *
 * @package editor_tinymce
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
require_once('../../../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/lib/jslib.php');

// Safely get slash params (cleaned using PARAM_PATH, without /../).
$path = get_file_argument();

// Param must be of the form [plugin]/[version]/[path] where path is a relative
// path inside the plugin tinymce folder.
$matches = array();
if (!preg_match('~^/([a-z0-9_]+)/((?:[0-9.]+)|-1)(/.*)$~', $path, $matches)) {
    print_error('filenotfound');
}
list($junk, $tinymceplugin, $version, $innerpath) = $matches;

// Note that version number is totally ignored, user can specify anything,
// except for the difference between '-1' and anything else.

// Check the file exists.
$pluginfolder = $CFG->dirroot . '/lib/editor/tinymce/plugins/' . $tinymceplugin;
$file = $pluginfolder . '/tinymce' .$innerpath;
if (!file_exists($file)) {
    print_error('filenotfound');
}

// We don't actually care what the version number is but there is a special
// case for '-1' which means, set the files to not be cached.
$allowcache = ($version !== '-1');
if ($allowcache) {
    // Set it to expire a year later. Note that this means we should never get
    // If-Modified-Since requests so there is no need to handle them specially.
    header('Expires: ' . date('r', time() + 365 * 24 * 3600));
    header('Cache-Control: max-age=' . 365 * 24 * 3600);
    // Pragma is set to no-cache by default so must be overridden.
    header('Pragma:');
}

// Get the right MIME type.
$mimetype = mimeinfo('type', $file);

// For JS files, these can be minified and stored in cache.
if ($mimetype === 'application/x-javascript' && $allowcache) {
    // The cached file is stored without version number etc. This is okay
    // because $CFG->cachedir is cleared each time there is a plugin update,
    // such as a new version of a tinymce plugin.

    // Flatten filename and include cache location.
    $cache = $CFG->cachedir . '/editor_tinymce/pluginjs';
    $cachefile = $cache . '/' . $tinymceplugin .
            str_replace('/', '_', $innerpath);

    // If it doesn't exist, minify it and save to that location.
    if (!file_exists($cachefile)) {
        $content = core_minify::js_files(array($file));
        js_write_cache_file_content($cachefile, $content);
    }

    $file = $cachefile;
} else if ($mimetype === 'text/html') {
    header('X-UA-Compatible: IE=edge');
}

// Serve file.
header('Content-Length: ' . filesize($file));
header('Content-Type: ' . $mimetype);
readfile($file);
