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
 * This file is responsible for serving of yui images
 *
 * @package   moodlecore
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php

// get special url parameters
if (!$parts = combo_params()) {
    combo_not_found();
}

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
    $lifetime = 60*60*24*30; // 30 days
    header('HTTP/1.1 304 Not Modified');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Cache-Control: max-age='.$lifetime);
    header('Content-Type: '.$mimetype);
    die;
}

$parts = explode('&', $parts);
$cache = true;

foreach ($parts as $part) {
    if (empty($part)) {
        continue;
    }
    $part = min_clean_param($part, 'SAFEPATH');
    $bits = explode('/', $part);
    if (count($bits) < 2) {
        $content .= "\n// Wrong combo resource $part!\n";
        continue;
    }
    //debug($bits);
    $version = array_shift($bits);
    if ($version == 'moodle') {
        //TODO: this is a ugly hack because we should not load any libs here!
        define('MOODLE_INTERNAL', true);
        require_once($CFG->libdir.'/moodlelib.php');
        $revision = (int)array_shift($bits);
        if ($revision === -1) {
            // Revision -1 says please don't cache the JS
            $cache = false;
        }
        $frankenstyle = array_shift($bits);
        $filename = array_pop($bits);
        $dir = get_component_directory($frankenstyle);
        if ($mimetype == 'text/css') {
            $bits[] = 'assets';
            $bits[] = 'skins';
            $bits[] = 'sam';
        }
        $contentfile = $dir.'/yui/'.join('/', $bits).'/'.$filename;
    } else {
        if ($version != $CFG->yui3version and $version != $CFG->yui2version and $version != 'gallery') {
            $content .= "\n// Wrong yui version $part!\n";
            continue;
        }
        $contentfile = "$CFG->libdir/yui/$part";
    }
    if (!file_exists($contentfile) or !is_file($contentfile)) {
        $content .= "\n// Combo resource $part not found!\n";
        continue;
    }
    $filecontent = file_get_contents($contentfile);

    if ($mimetype === 'text/css') {
        if ($version == 'moodle') {
            $filecontent = preg_replace('/([a-z0-9_-]+)\.(png|gif)/', 'yui_image.php?file='.$version.'/'.$frankenstyle.'/'.array_shift($bits).'/$1.$2', $filecontent);
        } else if ($version == 'gallery') {
            // search for all images in gallery module CSS and serve them through the yui_image.php script
            $filecontent = preg_replace('/([a-z0-9_-]+)\.(png|gif)/', 'yui_image.php?file='.$version.'/'.$bits[0].'/'.$bits[1].'/$1.$2', $filecontent);
        } else {
            // First we need to remove relative paths to images. These are used by YUI modules to make use of global assets.
            // I've added this as a separate regex so it can be easily removed once
            // YUI standardise there CSS methods
            $filecontent = preg_replace('#(\.\./\.\./\.\./\.\./assets/skins/sam/)?([a-z0-9_-]+)\.(png|gif)#', '$2.$3', $filecontent);

            // search for all images in yui2 CSS and serve them through the yui_image.php script
            $filecontent = preg_replace('/([a-z0-9_-]+)\.(png|gif)/', 'yui_image.php?file='.$version.'/$1.$2', $filecontent);
        }
    }

    $content .= $filecontent;
}

if ($cache) {
    combo_send_cached($content, $mimetype);
} else {
    combo_send_uncached($content, $mimetype);
}


/**
 * Send the JavaScript cached
 * @param string $content
 * @param string $mimetype
 */
function combo_send_cached($content, $mimetype) {
    $lifetime = 60*60*24*30; // 30 days

    header('Content-Disposition: inline; filename="combo"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: max-age='.$lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
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
    // note: buggy or misconfigured IIS does return the query string in REQUEST_URL
    if (isset($_SERVER['REQUEST_URI']) and strpos($_SERVER['REQUEST_URI'], '?') !== false) {
        $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        return $parts[1];

    } else if (isset($_SERVER['QUERY_STRING'])) {
        return $_SERVER['QUERY_STRING'];

    } else {
        // unsupported server, sorry!
        combo_not_found('Unsupported server - query string can not be determined, try disabling YUI combo loading in admin settings.');
    }
}
