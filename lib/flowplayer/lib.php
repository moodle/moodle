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
 * Flowplayer library.
 *
 * @package core
 * @copyright  Petr Skoda <petr.skoda@totaralms.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Safer flash serving code.
 *
 * @param string $filename
 * @return void does not return, ends with die()
 */
function flowplayer_send_flash_content($filename) {
    global $CFG;
    // Note: Do not use any fancy APIs here, this must work in all supported versions.

    // No url params.
    if (!empty($_GET) or !empty($_POST)) {
        header("HTTP/1.1 404 Not Found");
        die;
    }

    // Our referrers only, nobody else should embed these scripts.
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $refhost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        $host = parse_url($CFG->wwwroot . '/', PHP_URL_HOST);
        if ($refhost and $host and strtolower($refhost) !== strtolower($host)) {
            header("HTTP/1.1 404 Not Found");
            die;
        }
    }

    // Fetch and decode the original content.
    $content = file_get_contents($CFG->dirroot . '/lib/flowplayer/' . $filename . '.bin');
    if (!$content) {
        header("HTTP/1.1 404 Not Found");
        die;
    }
    $content = base64_decode($content);

    // No caching allowed.
    if (strpos($CFG->wwwroot, 'https://') === 0) {
        // HTTPS sites - watch out for IE! KB812935 and KB316431.
        header('Cache-Control: private, max-age=10, no-transform');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: ');
    } else {
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0, no-transform');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
    }

    // Send the original binary code.
    header('Content-Type: application/x-shockwave-flash');
    echo $content;
    die;
}
