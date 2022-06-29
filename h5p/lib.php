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
 * Callbacks.
 *
 * @package    core_h5p
 * @copyright  2019 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use core_h5p\local\library\autoloader;

/**
 * Serve the files from the core_h5p file areas.
 *
 * @package core_h5p
 * @category files
 *
 * @param  stdClass $course the course object
 * @param  stdClass $cm the course module object
 * @param  stdClass $context the newmodule's context
 * @param  string $filearea the name of the file area
 * @param  array $args extra arguments (itemid, path)
 * @param  bool $forcedownload whether or not force download
 * @param  array $options additional options affecting the file serving
 *
 * @return bool Returns false if we don't find a file.
 */
function core_h5p_pluginfile($course, $cm, $context, string $filearea, array $args, bool $forcedownload,
    array $options = []) : bool {
    global $DB;

    // Require classes from H5P third party library
    autoloader::register();

    $filesettingsset = false;

    switch ($filearea) {
        default:
            return false; // Invalid file area.

        case \core_h5p\file_storage::LIBRARY_FILEAREA:
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                 return false; // Invalid context because the libraries are loaded always in the context system.
            }

            $itemid = null;

            // The files that can be delivered to this function are unfortunately out of our control. Some of the
            // references are embedded into the JavaScript of the files and we have no ability to inject an item id.
            // We also don't know the location of the item id when we do include it, so we look for the first numeric
            // value and try to serve that file.
            foreach ($args as $key => $value) {
                if (is_numeric($value)) {
                    $itemid = $value;
                    unset($args[$key]);
                    break;
                }
            }

            if (!isset($itemid)) {
                // We didn't find an item id to use, so we fall back to retrieving the record using all the other
                // fields. The combination of component, filearea, filepath, and filename is enough for a unique
                // record.
                $filename = array_pop($args);
                $filepath = '/' . implode('/', $args) . '/';
                $itemid = $DB->get_field('files', 'itemid', [
                    'component' => \core_h5p\file_storage::COMPONENT,
                    'filearea' => \core_h5p\file_storage::LIBRARY_FILEAREA,
                    'filepath' => $filepath,
                    'filename' => $filename
                ]);
                $filesettingsset = true;
            }
            break;
        case \core_h5p\file_storage::CONTENT_FILEAREA:
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                return false; // Invalid context because the content files are loaded always in the context system.
            }
            $itemid = array_shift($args);
            break;
        case \core_h5p\file_storage::CACHED_ASSETS_FILEAREA:
        case \core_h5p\file_storage::EXPORT_FILEAREA:
            $itemid = 0;
            break;
    }

    if (!$filesettingsset) {
        $filename = array_pop($args);
        $filepath = (!$args ? '/' : '/' . implode('/', $args) . '/');
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, \core_h5p\file_storage::COMPONENT, $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // No such file.
    }

    send_stored_file($file, null, 0, $forcedownload, $options);

    return true;
}
