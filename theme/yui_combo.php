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
 * This file is responsible for serving of yui Javascript and CSS
 *
 * @package   core
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php

// get special url parameters

list($parts, $slasharguments) = combo_params();
if (!$parts) {
    combo_not_found();
}

$etag = sha1($parts);
$parts = trim($parts, '&');

// find out what we are serving - only one type per request
$content = '';
if (substr($parts, -3) === '.js') {
    $mimetype = 'application/javascript';
} else if (substr($parts, -4) === '.css') {
    $mimetype = 'text/css';
} else {
    combo_not_found();
}

// if they are requesting a revision that's not -1, and they have supplied an
// If-Modified-Since header, we can send back a 304 Not Modified since the
// content never changes (the rev number is increased any time the content changes)
if (strpos($parts, '/-1/') === false and (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))) {
    $lifetime = 60*60*24*360; // 1 year, we do not change YUI versions often, there are a few custom yui modules
    header('HTTP/1.1 304 Not Modified');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Content-Type: '.$mimetype);
    header('Etag: "'.$etag.'"');
    die;
}

$parts = explode('&', $parts);
$cache = true;
$lastmodified = 0;

while (count($parts)) {
    $part = array_shift($parts);
    if (empty($part)) {
        continue;
    }
    $filecontent = '';
    $part = min_clean_param($part, 'SAFEPATH');
    $bits = explode('/', $part);
    if (count($bits) < 2) {
        $content .= "\n// Wrong combo resource $part!\n";
        continue;
    }

    $version = array_shift($bits);
    if ($version === 'rollup') {
        $yuipatchedversion = explode('_', array_shift($bits));
        $revision = $yuipatchedversion[0];
        $rollupname = array_shift($bits);

        if (strpos($rollupname, 'yui-moodlesimple') !== false) {
            if (substr($rollupname, -3) === '.js') {
                // Determine which version of this rollup should be used.
                $filesuffix = '.js';
                preg_match('/(-(debug|min))?\.js/', $rollupname, $matches);
                if (isset($matches[1])) {
                    $filesuffix = $matches[0];
                }

                $type = 'js';
            } else if (substr($rollupname, -4) === '.css') {
                $type = 'css';
            } else {
                continue;
            }

            // Allow support for revisions on YUI between official releases.
            // We can just discard the subrevision since it is only used to invalidate the browser cache.
            $yuipatchedversion = explode('_', $revision);
            $yuiversion = $yuipatchedversion[0];

            $yuimodules = array(
                'yui',
                'oop',
                'event-custom-base',
                'dom-core',
                'dom-base',
                'color-base',
                'dom-style',
                'selector-native',
                'selector',
                'node-core',
                'node-base',
                'event-base',
                'event-base-ie',
                'pluginhost-base',
                'pluginhost-config',
                'event-delegate',
                'node-event-delegate',
                'node-pluginhost',
                'dom-screen',
                'node-screen',
                'node-style',
                'querystring-stringify-simple',
                'io-base',
                'json-parse',
                'transition',
                'selector-css2',
                'selector-css3',
                'dom-style-ie',

                // Some extras we use everywhere.
                'escape',

                'attribute-core',
                'event-custom-complex',
                'base-core',
                'attribute-base',
                'attribute-extras',
                'attribute-observable',
                'base-observable',
                'base-base',
                'base-pluginhost',
                'base-build',
                'event-synthetic',

                'attribute-complex',
                'event-mouseenter',
                'event-key',
                'event-outside',
                'event-autohide',
                'event-focus',
                'classnamemanager',
                'widget-base',
                'widget-htmlparser',
                'widget-skin',
                'widget-uievents',
                'widget-stdmod',
                'widget-position',
                'widget-position-align',
                'widget-stack',
                'widget-position-constrain',
                'overlay',

                'widget-autohide',
                'button-core',
                'button-plugin',
                'widget-buttons',
                'widget-modality',
                'panel',
                'yui-throttle',
                'dd-ddm-base',
                'dd-drag',
                'dd-plugin',

                // Cache is used by moodle-core-tooltip which we include everywhere.
                'cache-base',
            );

            // We need to add these new parts to the beginning of the $parts list, not the end.
            if ($type === 'js') {
                $newparts = array();
                foreach ($yuimodules as $module) {
                    $newparts[] = $yuiversion . '/' . $module . '/' . $module . $filesuffix;
                }
                $newparts[] = 'yuiuseall/yuiuseall';
                $parts = array_merge($newparts, $parts);
            } else {
                $newparts = array();
                foreach ($yuimodules as $module) {
                    $candidate =  $yuiversion . '/' . $module . '/assets/skins/sam/' . $module . '.css';
                    if (!file_exists("$CFG->libdir/yuilib/$candidate")) {
                        continue;
                    }
                    $newparts[] = $candidate;
                }
                if ($newparts) {
                    $parts = array_merge($newparts, $parts);
                }
            }
        }

        // Handle the mcore rollup.
        if (strpos($rollupname, 'mcore') !== false) {
            $yuimodules = array(
                'core/tooltip/tooltip',
                'core/popuphelp/popuphelp',
                'core/widget-focusafterclose/widget-focusafterclose',
                'core/dock/dock-loader',
                'core/notification/notification-dialogue',
            );

            // Determine which version of this rollup should be used.
            $filesuffix = '.js';
            preg_match('/(-(debug|min))?\.js/', $rollupname, $matches);
            if (isset($matches[1])) {
                $filesuffix = $matches[0];
            }

            // We need to add these new parts to the beginning of the $parts list, not the end.
            $newparts = array();
            foreach ($yuimodules as $module) {
                $newparts[] = 'm/' . $revision . '/' . $module . $filesuffix;
            }
            $parts = array_merge($newparts, $parts);
        }
        continue;
    }
    if ($version === 'm') {
        $version = 'moodle';
    }
    if ($version === 'moodle') {
        if (count($bits) <= 3) {
            // This is an invalid module load attempt.
            $content .= "\n// Incorrect moodle module inclusion. Not enough component information in {$part}.\n";
            continue;
        }
        $revision = (int)array_shift($bits);
        if ($revision === -1) {
            // Revision -1 says please don't cache the JS
            $cache = false;
        }
        $frankenstyle = array_shift($bits);
        $filename = array_pop($bits);
        $modulename = $bits[0];
        $dir = core_component::get_component_directory($frankenstyle);

        // For shifted YUI modules, we need the YUI module name in frankenstyle format.
        $frankenstylemodulename = join('-', array($version, $frankenstyle, $modulename));
        $frankenstylefilename = preg_replace('/' . $modulename . '/', $frankenstylemodulename, $filename);

        // Submodules are stored in a directory with the full submodule name.
        // We need to remove the -debug.js, -min.js, and .js from the file name to calculate that directory name.
        $frankenstyledirectoryname = str_replace(array('-min.js', '-debug.js', '.js', '.css'), '', $frankenstylefilename);

        // By default, try and use the /yui/build directory.
        $contentfile = $dir . '/yui/build/' . $frankenstyledirectoryname;
        if ($mimetype == 'text/css') {
            // CSS assets are in a slightly different place to the JS.
            $contentfile = $contentfile . '/assets/skins/sam/' . $frankenstylefilename;

            // Add the path to the bits to handle fallback for non-shifted assets.
            $bits[] = 'assets';
            $bits[] = 'skins';
            $bits[] = 'sam';
        } else {
            $contentfile = $contentfile . '/' . $frankenstylefilename;
        }

        // If the shifted versions don't exist, fall back to the non-shifted file.
        if (!file_exists($contentfile) or !is_file($contentfile)) {
            // We have to revert to the non-minified and non-debug versions.
            $filename = preg_replace('/-(min|debug)\./', '.', $filename);
            $contentfile = $dir . '/yui/' . join('/', $bits) . '/' . $filename;
        }
    } else if ($version === '2in3') {
        $contentfile = "$CFG->libdir/yuilib/$part";

    } else if ($version == 'gallery') {
        if (count($bits) <= 2) {
            // This is an invalid module load attempt.
            $content .= "\n// Incorrect moodle module inclusion. Not enough component information in {$part}.\n";
            continue;
        }
        $revision = (int)array_shift($bits);
        if ($revision === -1) {
            // Revision -1 says please don't cache the JS
            $cache = false;
        }
        $contentfile = "$CFG->libdir/yuilib/gallery/" . join('/', $bits);

    } else if ($version == 'yuiuseall') {
        // Create global Y that is available in global scope,
        // this is the trick behind original SimpleYUI.
        $filecontent = "var Y = YUI().use('*');";

    } else {
        // Allow support for revisions on YUI between official releases.
        // We can just discard the subrevision since it is only used to invalidate the browser cache.
        $yuipatchedversion = explode('_', $version);
        $yuiversion = $yuipatchedversion[0];
        if ($yuiversion != $CFG->yui3version) {
            $content .= "\n// Wrong yui version $part!\n";
            continue;
        }
        $newpart = explode('/', $part);
        $newpart[0] = $yuiversion;
        $part = implode('/', $newpart);
        $contentfile = "$CFG->libdir/yuilib/$part";
    }
    if (!file_exists($contentfile) or !is_file($contentfile)) {
        $location = '$CFG->dirroot'.preg_replace('/^'.preg_quote($CFG->dirroot, '/').'/', '', $contentfile);
        $content .= "\n// Combo resource $part ($location) not found!\n";
        continue;
    }

    if (empty($filecontent)) {
        $filecontent = file_get_contents($contentfile);
    }
    $fmodified = filemtime($contentfile);
    if ($fmodified > $lastmodified) {
        $lastmodified = $fmodified;
    }

    $relroot = preg_replace('|^http.?://[^/]+|', '', $CFG->wwwroot);
    $sep = ($slasharguments ? '/' : '?file=');

    if ($mimetype === 'text/css') {
        if ($version == 'moodle') {
            // Search for all images in the file and replace with an appropriate link to the yui_image.php script
            $imagebits = array(
                $sep . $version,
                $frankenstyle,
                $modulename,
                array_shift($bits),
                '$1.$2'
            );

            $filecontent = preg_replace('/([a-z0-9_-]+)\.(png|gif)/', $relroot . '/theme/yui_image.php' . implode('/', $imagebits), $filecontent);
        } else if ($version == '2in3') {
            // First we need to remove relative paths to images. These are used by YUI modules to make use of global assets.
            // I've added this as a separate regex so it can be easily removed once
            // YUI standardise there CSS methods
            $filecontent = preg_replace('#(\.\./\.\./\.\./\.\./assets/skins/sam/)?([a-z0-9_-]+)\.(png|gif)#', '$2.$3', $filecontent);

            // search for all images in yui2 CSS and serve them through the yui_image.php script
            $filecontent = preg_replace('/([a-z0-9_-]+)\.(png|gif)/', $relroot.'/theme/yui_image.php'.$sep.$CFG->yui2version.'/$1.$2', $filecontent);

        } else if ($version == 'gallery') {
            // Replace any references to the CDN with a relative link.
            $filecontent = preg_replace('#(' . preg_quote('http://yui.yahooapis.com/') . '(gallery-[^/]*/))#', '../../../../', $filecontent);

            // Replace all relative image links with the a link to yui_image.php.
            $filecontent = preg_replace('#(' . preg_quote('../../../../') . ')(gallery-[^/]*/assets/skins/sam/[a-z0-9_-]+)\.(png|gif)#',
                    $relroot . '/theme/yui_image.php' . $sep . '/gallery/' . $revision . '/$2.$3', $filecontent);

        } else {
            // First we need to remove relative paths to images. These are used by YUI modules to make use of global assets.
            // I've added this as a separate regex so it can be easily removed once
            // YUI standardise there CSS methods
            $filecontent = preg_replace('#(\.\./\.\./\.\./\.\./assets/skins/sam/)?([a-z0-9_-]+)\.(png|gif)#', '$2.$3', $filecontent);

            // search for all images in yui2 CSS and serve them through the yui_image.php script
            $filecontent = preg_replace('/([a-z0-9_-]+)\.(png|gif)/', $relroot.'/theme/yui_image.php'.$sep.$version.'/$1.$2', $filecontent);
        }
    }

    $content .= $filecontent;
}

if ($lastmodified == 0) {
    $lastmodified = time();
}

if ($cache) {
    combo_send_cached($content, $mimetype, $etag, $lastmodified);
} else {
    combo_send_uncached($content, $mimetype);
}


/**
 * Send the JavaScript cached
 * @param string $content
 * @param string $mimetype
 * @param string $etag
 * @param int $lastmodified
 */
function combo_send_cached($content, $mimetype, $etag, $lastmodified) {
    $lifetime = 60*60*24*360; // 1 year, we do not change YUI versions often, there are a few custom yui modules

    header('Content-Disposition: inline; filename="combo"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Etag: "'.$etag.'"');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.strlen($content));
    }

    echo $content;
    die;
}

/**
 * Send the JavaScript uncached
 * @param string $content
 * @param string $mimetype
 */
function combo_send_uncached($content, $mimetype) {
    header('Content-Disposition: inline; filename="combo"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + 2) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.strlen($content));
    }

    echo $content;
    die;
}

function combo_not_found($message = '') {
    header('HTTP/1.0 404 not found');
    if ($message) {
        echo $message;
    } else {
        echo 'Combo resource not found, sorry.';
    }
    die;
}

function combo_params() {
    if (isset($_SERVER['QUERY_STRING']) and strpos($_SERVER['QUERY_STRING'], 'file=/') === 0) {
        // url rewriting
        $slashargument = substr($_SERVER['QUERY_STRING'], 6);
        return array($slashargument, true);

    } else if (isset($_SERVER['REQUEST_URI']) and strpos($_SERVER['REQUEST_URI'], '?') !== false) {
        $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        return array($parts[1], false);

    } else if (isset($_SERVER['QUERY_STRING']) and strpos($_SERVER['QUERY_STRING'], '?') !== false) {
        // note: buggy or misconfigured IIS does return the query string in REQUEST_URI
        return array($_SERVER['QUERY_STRING'], false);

    } else if ($slashargument = min_get_slash_argument()) {
        $slashargument = ltrim($slashargument, '/');
        return array($slashargument, true);

    } else {
        // unsupported server, sorry!
        combo_not_found('Unsupported server - query string can not be determined, try disabling YUI combo loading in admin settings.');
    }
}
