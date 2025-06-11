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

namespace core_files\hook;

use core\attribute;
use core\hook\stoppable_trait;

/**
 * A hook which is fired after a file is created in the file storage API.
 *
 * @package   core
 * @copyright 2024 Huong Nguyen <huongnv13@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[attribute\label('Allows subscribers to inspect a file after it is created in the file pool')]
#[attribute\tags('file')]
#[attribute\hook\replaces_callbacks('after_file_created')]
final class after_file_created {
    use stoppable_trait;
    /**
     * Hook to allow subscribers to modify file after it is created.
     *
     * @param \stored_file $storedfile The stored file.
     * @param \stdClass $filerecord The file record.
     */
    public function __construct(
        /** @var \stored_file The stored file. */
        public readonly \stored_file $storedfile,
        /** @var \stdClass The file record. */
        public readonly \stdClass $filerecord,
    ) {
    }

    /**
     * Process legacy callbacks.
     */
    public function process_legacy_callbacks(): void {
        if ($pluginsfunction = get_plugins_with_function(function: 'after_file_created', migratedtohook: true)) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $pluginfunction($this->filerecord);
                }
            }
        }
    }
}
