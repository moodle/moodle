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
 * @package   core
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Serve files using the X-Sendfile header.
 *
 * This needs special server module or configuration.
 * Please make sure that all headers are already sent and the all access control checks passed.
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

    $localrequestdir = realpath($CFG->localrequestdir);
    if (str_contains($filepath, $localrequestdir)) {
        // Do not serve files from local request directory using xsendfile.
        // They are likely to be removed before xsendfile can serve them.
        return false;
    }

    $aliased = false;
    if (!empty($CFG->xsendfilealiases) && is_array($CFG->xsendfilealiases)) {
        foreach ($CFG->xsendfilealiases as $alias => $dir) {
            $dir = realpath($dir);
            if ($dir === false) {
                continue;
            }
            if (substr($dir, -1) !== DIRECTORY_SEPARATOR) {
                // Add trailing dir separator.
                $dir .= DIRECTORY_SEPARATOR;
            }
            if (str_starts_with($filepath, $dir)) {
                $filepath = $alias . substr($filepath, strlen($dir));
                $aliased = true;
                break;
            }
        }
    }

    if ($CFG->xsendfile === 'X-LIGHTTPD-send-file') {
        // Version 1.4.40 and earlier do not support byte serving.
        // See http://redmine.lighttpd.net/projects/lighttpd/wiki/X-LIGHTTPD-send-file for more information.
        header('Accept-Ranges: none');
    } else if ($CFG->xsendfile === 'X-Accel-Redirect') {
        // Nginx requires paths relative to aliases, you need to specify them in config.php
        // See http://wiki.nginx.org/XSendfile for more information.
        if (!$aliased) {
            return false;
        }
    }

    header("$CFG->xsendfile: $filepath");

    return true;
}
