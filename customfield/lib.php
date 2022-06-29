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
 * Callbacks
 *
 * @package   core_customfield
 * @copyright 2018 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Edit customfield elements inplace
 *
 * @param string $itemtype
 * @param int    $itemid
 * @param string $newvalue
 * @return \core\output\inplace_editable
 */
function core_customfield_inplace_editable($itemtype, $itemid, $newvalue) {
    if ($itemtype === 'category') {
        $category = core_customfield\category_controller::create($itemid);
        $handler = $category->get_handler();
        \external_api::validate_context($handler->get_configuration_context());
        if (!$handler->can_configure()) {
            throw new moodle_exception('nopermissionconfigure', 'core_customfield');
        }
        $newvalue = clean_param($newvalue, PARAM_NOTAGS);
        $handler->rename_category($category, $newvalue);
        return \core_customfield\api::get_category_inplace_editable($category, true);
    }
}

/**
 * Serve the files from the core_customfield file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param context $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return
 */
function core_customfield_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    if ($filearea !== 'description') {
        return false;
    }

    $itemid = array_shift($args);
    $filename = array_pop($args); // The last item in the $args array.

    $field = \core_customfield\field_controller::create($itemid);
    $handler = $field->get_handler();
    if ($handler->get_configuration_context()->id != $context->id) {
        return false;
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'core_customfield', $filearea, $itemid, '/', $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    // From Moodle 2.3, use send_stored_file instead.
    send_file($file, 86400, 0, $forcedownload, $options);
}
