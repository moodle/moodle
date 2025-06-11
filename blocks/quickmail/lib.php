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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function block_quickmail_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = []) {
    // Check the contextlevel is as expected.
    if ($context->contextlevel != CONTEXT_COURSE) {
        send_file_not_found();
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'attachments' && $filearea !== 'message_editor') {
        send_file_not_found();
    }

    // Make sure the user is logged in and has access to the module.
    // Plugins that are not course modules should leave out the 'cm' part.
    // Depending on configuration, allow unauthenticated users to download file.
    if (!empty(block_quickmail_config::get('downloads'))) {
        require_course_login($course, true, $cm);
    }

    // TODO: check permission here?

    // Extract params through args.
    $itemid = array_shift($args);
    $filename = array_pop($args);
    $path = ! count($args)
        ? '/'
        : '/' . implode('/', $args) . '/';

    // Get the message from the itemid.
    if (!$message = \block_quickmail\persistents\message::find_or_null($itemid)) {
        send_file_not_found();
    }

    // Handle a request for serving the master zip download (includes all attachments).
    if (strpos($filename, '_attachments.zip') !== false) {
        global $USER;

        $zipname = 'attachments.zip';

        $path = \block_quickmail\filemanager\message_file_handler::zip_attachments_for_user($message, $USER, $zipname);

        send_temp_file($path, $zipname);

        // Otherwise, serve the selected file.
    } else {
        $fs = get_file_storage();

        $file = $fs->get_file($context->id, 'block_quickmail', $filearea, $itemid, $path, $filename);

        // If the file does not exist.
        if (!$file) {
            send_file_not_found();
        }

        send_stored_file($file, 86400, 0, $forcedownload); // Options.
    }

}
