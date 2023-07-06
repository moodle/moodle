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
 * This file is serving optimised JS for RequireJS.
 *
 * @package    core
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// We need just the values from config.php and minlib.php.
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // This stops immediately at the beginning of lib/setup.php.
require_once("$CFG->dirroot/lib/jslib.php");
require_once("$CFG->dirroot/lib/classes/requirejs.php");

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
// Split into revision and module name.
list($rev, $file) = explode('/', $slashargument, 2);
$rev  = min_clean_param($rev, 'INT');
$file = '/' . min_clean_param($file, 'SAFEPATH');

// Only load js files from the js modules folder from the components.
$jsfiles = array();
list($unused, $component, $module) = explode('/', $file, 3);

/**
 * Helper function to fix missing module names in JavaScript.
 *
 * TODO Remove this function when we find a reliable way to do this in the Grunt task.
 * @param string $modulename
 * @param string $js
 * @return string The modified JavaScript.
 */
function requirejs_fix_define(string $modulename, string $js): string {
    // First check whether there is a possible missing module name. That is:
    // define (function(Foo) {
    // instead of:
    // define('mod_foo/bar', function(Foo) {
    $missingmodule = preg_match('/define\(\s*(\[|function)/', $js);

    // Now check whether the module name is already defined elsewhere.
    // This could be a totally unrelated use of the word define.
    // Note: This code needs to die, in a fire. It is evil and wrong.
    $missingmodule = $missingmodule && !preg_match("@define\s*\(\s*['\"]{$modulename}['\"]@", $js);

    if ($missingmodule) {
        // If the JavaScript module has been defined without specifying a name then we'll
        // add the Moodle module name now.
        $replace = 'define(\'' . $modulename . '\', ';

        // Replace only the first occurrence.
        return implode($replace, explode('define(', $js, 2));
    } else if (!preg_match('/define\s*\(/', $js)) {
        echo(
            "// JS module '{$modulename}' cannot be loaded, or does not contain a javascript" .
            ' module in AMD format. "define()" not found.' . "\n"
        );
    }

    return $js;
}

// Use the caching only for meaningful revision numbers which prevents future cache poisoning.
if ($rev > 0 and $rev < (time() + 60 * 60)) {
    // This is "production mode".
    // Some (huge) modules are better loaded lazily (when they are used). If we are requesting
    // one of these modules, only return the one module, not the combo.
    $lazysuffix = "-lazy.js";
    $lazyload = (strpos($module, $lazysuffix) !== false);

    if ($lazyload) {
        // We are lazy loading a single file - so include the component/filename pair in the etag.
        $etag = sha1($rev . '/' . $component . '/' . $module);
    } else {
        // We loading all (non-lazy) files - so only the rev makes this request unique.
        $etag = sha1($rev);
    }

    $candidate = $CFG->localcachedir . '/requirejs/' . $etag;

    if (file_exists($candidate)) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // We do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev parameter.
            js_send_unmodified(filemtime($candidate), $etag);
        }
        js_send_cached($candidate, $etag, 'requirejs.php');
        exit(0);

    } else {
        $jsfiles = array();
        if ($lazyload) {
            $jsfiles = core_requirejs::find_one_amd_module($component, $module);
        } else {
            // Here we respond to the request by returning ALL amd modules. This saves
            // round trips in production.

            $jsfiles = core_requirejs::find_all_amd_modules();
        }

        $content = '';
        foreach ($jsfiles as $modulename => $jsfile) {
            $js = file_get_contents($jsfile);
            if ($js === false) {
                error_log('Failed to load JavaScript file ' . $jsfile);
                $js = "/* Failed to load JavaScript file {$jsfile}. */\n";
                $content = $js . $content;
                continue;
            }
            // Remove source map link.
            $js = preg_replace('~//# sourceMappingURL.*$~s', '', $js);
            $js = rtrim($js);
            $js .= "\n";

            $js = requirejs_fix_define($modulename, $js);

            $content .= $js;
        }

        js_write_cache_file_content($candidate, $content);
        // Verify nothing failed in cache file creation.
        clearstatcache();
        if (file_exists($candidate)) {
            js_send_cached($candidate, $etag, 'requirejs.php');
            exit(0);
        }
    }
}

// If we've made it here then we're in "dev mode" where everything is lazy loaded.
// So all files will be served one at a time.
$jsfiles = core_requirejs::find_one_amd_module($component, $module, false);

if (!empty($jsfiles)) {
    $modulename = array_keys($jsfiles)[0];
    $jsfile = $jsfiles[$modulename];
    $shortfilename = str_replace($CFG->dirroot, '', $jsfile);
    $mapfile = $jsfile . '.map';

    if (file_exists($mapfile)) {
        // We've got a a source map file so we can return the minified file here and
        // the source map will be used by the browser to debug.
        $js = file_get_contents($jsfile);
        // Fix the source map link for the file.
        $js = preg_replace(
            '~//# sourceMappingURL.*$~s',
            "//# sourceMappingURL={$CFG->wwwroot}/lib/jssourcemap.php{$file}",
            $js
        );
        $js = rtrim($js);
    } else {
        // This file doesn't have a map file. We might be dealing with an older source file from
        // a plugin or previous version of Moodle so we should just return the full original source
        // like we used to.
        $originalsource = str_replace('/amd/build/', '/amd/src/', $jsfile);
        $originalsource = str_replace('.min.js', '.js', $originalsource);
        $js = file_get_contents($originalsource);
        $js = rtrim($js);
    }

    $js = requirejs_fix_define($modulename, $js);

    js_send_uncached($js, 'requirejs.php');
} else {
    // We can't find the requested file.
    header('HTTP/1.0 404 not found');
}
