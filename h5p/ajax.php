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
 * Responsible for handling AJAX requests related to H5P.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>, based on code by Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_h5p\factory;
use core_h5p\framework;

define('AJAX_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/filelib.php');

require_login();

$action = required_param('action', PARAM_ALPHA);
$contextid = required_param('contextId', PARAM_INT);

$context = context::instance_by_id($contextid);

if (!has_capability('moodle/h5p:updatelibraries', $context)) {
    H5PCore::ajaxError(get_string('nopermissiontoedit', 'h5p'));
    header('HTTP/1.1 403 Forbidden');
    return;
}

$factory = new factory();
$editor = $factory->get_editor();

switch ($action) {
    // Load list of libraries or details for library.
    case 'libraries':
        // Get parameters.
        $name = optional_param('machineName', '', PARAM_TEXT);
        $major = optional_param('majorVersion', 0, PARAM_INT);
        $minor = optional_param('minorVersion', 0, PARAM_INT);

        $language = optional_param('default-language', null, PARAM_ALPHA);

        if (!empty($name)) {
            $editor->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $name,
                $major, $minor, framework::get_language(), '', '', $language);
        } else {
            $editor->ajax->action(H5PEditorEndpoints::LIBRARIES);
        }

        break;

    // Load content type cache list to display available libraries in hub.
    case 'contenttypecache':
        $editor->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE);
        break;

    // Handle file upload through the editor.
    case 'files':
        $token = required_param('token', PARAM_RAW);
        $contentid = required_param('contentId', PARAM_INT);

        $editor->ajax->action(H5PEditorEndpoints::FILES, $token, $contentid);
        break;

    // Install libraries from H5P and retrieve content json.
    case 'libraryinstall':
        $token = required_param('token', PARAM_RAW);
        $machinename = required_param('id', PARAM_TEXT);
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $token, $machinename);
        break;

    // Handle file upload through the editor.
    case 'libraryupload':
        $token = required_param('token', PARAM_RAW);

        $uploadpath = $_FILES['h5p']['tmp_name'];
        $contentid = optional_param('contentId', 0, PARAM_INT);
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $token, $uploadpath, $contentid);
        break;

    // Get the $language libraries translations.
    case 'translations':
        $language = required_param('language', PARAM_RAW);
        $editor->ajax->action(H5PEditorEndpoints::TRANSLATIONS, $language);
        break;

    // Handle filtering of parameters through AJAX.
    case 'filter':
        $token = required_param('token', PARAM_RAW);
        $libraryparameters = required_param('libraryParameters', PARAM_RAW);

        $editor->ajax->action(H5PEditorEndpoints::FILTER, $token, $libraryparameters);
        break;

    // Throw error if AJAX action is not handled.
    default:
        throw new coding_exception('Unhandled AJAX');
        break;
}
