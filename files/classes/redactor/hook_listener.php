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

namespace core_files\redactor;

use core_files\hook\before_file_created;

/**
 * Allow the plugin to call as soon as possible before the file is created.
 *
 * @package   core
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Execute the before_file_created hook listener for file redaction.
     *
     * @param before_file_created $hook
     */
    public static function file_redaction_handler(before_file_created $hook): void {
        // The file mime-type must be present. Otherwise, bypass the process.
        if (empty($hook->get_filerecord()) || empty($hook->get_filerecord()->mimetype)) {
            return;
        }

        $manager = \core\di::get(manager::class);

        if ($hook->has_filepath()) {
            $file = $manager->redact_file(
                $hook->get_filerecord()->mimetype,
                $hook->get_filepath(),
            );

            if ($file !== null) {
                $hook->update_filepath($file);
            }
        } else {
            $data = $manager->redact_file_content(
                $hook->get_filerecord()->mimetype,
                $hook->get_filecontent(),
            );

            if ($data !== null) {
                $hook->update_filecontent($data);
            }
        }

        // Iterates through the errors returned by the manager and outputs each error message.
        foreach ($manager->get_errors() as $e) {
            debugging($e->getMessage());
        }
    }
}
