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
 * Script to allow set the Moodle LMS site referer header when embedding remote content on the app.
 *
 * @package tool_mobile
 * @copyright 2025 Juan Leyva <juan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
require(__DIR__ . '/../../../config.php');

// This script is only for the Moodle app, when referer protected remote content is being embedded.
// This is a security measure as well because the user agent cannot be tampered via XSS attacks.
if (!\core_useragent::is_moodle_app()) {
    throw new moodle_exception('apprequired', 'tool_mobile');
}

$url = required_param('url', PARAM_URL);
$delay = optional_param('delay', 500, PARAM_INT);
$debug = optional_param('debug', false, PARAM_BOOL);

// Check if the URL to redirect is valid and not a local URL.
if (empty($url) || !empty(clean_param($url, PARAM_LOCALURL))) {
    throw new moodle_exception('invalidurl');
}

// Delay has to be positive number and max of 5 seconds,
// enough to see the debugging info at least when required.
$delay = max(0, min($delay, 5000));

$data = [
    'lang' => current_language(),
    'url' => $url,
    'delay' => $delay,
    'debug' => $debug,
    'useragent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'referer' => $_SERVER['HTTP_REFERER'] ?? '',
];

echo $OUTPUT->render_from_template('tool_mobile/referer', $data);
