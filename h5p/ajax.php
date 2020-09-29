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
use core_h5p\local\library\autoloader;

define('AJAX_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/filelib.php');

if (!confirm_sesskey()) {
    autoloader::register();
    H5PCore::ajaxError(get_string('invalidsesskey', 'error'));
    header('HTTP/1.1 403 Forbidden');
    return;
}
require_login();

$action = required_param('action', PARAM_ALPHA);

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
    // This endpoint needs a token that only users with H5P editor access could get.
    // TODO: MDL-68907 to check capabilities.
    case 'files':
        $token = required_param('token', PARAM_RAW);
        $contentid = required_param('contentId', PARAM_INT);

        // Check size of each uploaded file and scan for viruses.
        foreach ($_FILES as $uploadedfile) {
            $filename = clean_param($uploadedfile['name'], PARAM_FILE);
            $maxsize = get_max_upload_file_size($CFG->maxbytes);
            if ($uploadedfile['size'] > $maxsize) {
                H5PCore::ajaxError(get_string('maxbytesfile', 'error', ['file' => $filename, 'size' => display_size($maxsize)]));
                return;
            }
            \core\antivirus\manager::scan_file($uploadedfile['tmp_name'], $filename, true);
        }

        $editor->ajax->action(H5PEditorEndpoints::FILES, $token, $contentid);
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
