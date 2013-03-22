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
 * Checks the write permission for the given plugin type
 *
 * @package     tool_installaddon
 * @subpackage  ajax
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(dirname(__FILE__).'/classes/installer.php');

require_login();

if (!has_capability('moodle/site:config', context_system::instance())) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

if (!empty($CFG->disableonclickaddoninstall)) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

if (!confirm_sesskey()) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

$plugintype = optional_param('plugintype', null, PARAM_ALPHANUMEXT);
if (is_null($plugintype)) {
    header('HTTP/1.1 400 Bad Request');
    die();
}

$installer = tool_installaddon_installer::instance();

$plugintypepath = $installer->get_plugintype_root($plugintype);

if (empty($plugintypepath)) {
    header('HTTP/1.1 400 Bad Request');
    die();
}

$response = array('path' => $plugintypepath);

if ($installer->is_plugintype_writable($plugintype)) {
    $response['writable'] = 1;
} else {
    $response['writable'] = 0;
}

header('Content-Type: application/json; charset: utf-8');
echo json_encode($response);
