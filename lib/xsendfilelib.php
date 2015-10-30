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
 * X-Sendfile support
 *
 * @package   core_files
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//NOTE: do not verify MOODLE_INTERNAL here, this is used from themes too

/**
 * Serve file using X-Sendfile header, this needs special server module
 * or configuration. Please make sure that all headers are already sent
 * and the all access control checks passed.
 *
 * @param string $filepath
 * @return bool success
 */
function xsendfile($filepath) {
    global $CFG;

    if (empty($CFG->xsendfile)) {
        return false;
    }

    if (!file_exists($filepath)) {
        return false;
    }

    if (headers_sent()) {
        return false;
    }

    $filepath = realpath($filepath);

    $aliased = false;
    if (!empty($CFG->xsendfilealiases) and is_array($CFG->xsendfilealiases)) {
        foreach ($CFG->xsendfilealiases as $alias=>$dir) {
            $dir = realpath($dir);
            if ($dir === false) {
                continue;
            }
            if (substr($dir, -1) !== DIRECTORY_SEPARATOR) {
                // add trailing dir separator
                $dir .= DIRECTORY_SEPARATOR;
            }
            if (strpos($filepath, $dir) === 0) {
                $filepath = $alias.substr($filepath, strlen($dir));
                $aliased = true;
                break;
            }
        }
    }

    if ($CFG->xsendfile === 'X-LIGHTTPD-send-file') {
        // http://redmine.lighttpd.net/projects/lighttpd/wiki/X-LIGHTTPD-send-file says 1.4 it does not support byteserving
        header('Accept-Ranges: none');

    } else if ($CFG->xsendfile === 'X-Accel-Redirect') {
        // http://wiki.nginx.org/XSendfile
        // Nginx requires paths relative to aliases, you need to specify them in config.php
        if (!$aliased) {
            return false;
        }
    }

    header("$CFG->xsendfile: $filepath");

    return true;
}
