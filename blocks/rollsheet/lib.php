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
 * Library file for filemanager demo
 *
 * @package   local_filemanager
 * @copyright 2013 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

// Create the Moodle Form.
class simplehtml_form extends moodleform {

    public function definition() {
        $mform = $this->_form; // Don't forget the underscore!
        $filemanageropts = $this->_customdata['filemanageropts'];

        // FILE MANAGER.
        $mform->addElement('filemanager', 'attachments', get_string('selectlogo', 'block_rollsheet'), null, $filemanageropts);

        // Buttons.
        $this->add_action_buttons();
    }
}

//
// Plugin File
//
// I M P O R T A N T
//
// This is the most confusing part. For each plugin using a file manager will automatically
// look for this function. It always ends with _pluginfile. Depending on where you build
// your plugin, the name will change. In case, it is a local plugin called file manager.

function block_rollsheet_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
    }

    require_login();

    if ($filearea != 'attachment') {
    }

    $itemid = (int)array_shift($args);

    if ($itemid != 0) {
    }

    $fs = get_file_storage();
    $filename = array_pop($args);
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }

    $file = $fs->get_file($context->id, 'block_rollsheet', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, true, $options); // Download MUST be forced - security!
}