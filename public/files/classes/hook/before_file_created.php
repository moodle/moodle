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

use core\exception\coding_exception;
use core\attribute;

/**
 * A hook which is fired before a file is created in the file storage API.
 *
 * @package    core_files
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[attribute\label('Allows subscribers to modify file content before it is stored in the file pool')]
#[attribute\tags('file')]
#[attribute\hook\replaces_callbacks('before_file_created')]
final class before_file_created {
    use \core\hook\stoppable_trait;

    /** @var bool Whether the content has been updated at all */
    public bool $contentupdated = false;

    /**
     * Constructor.
     *
     * @param \stdClass|null $filerecord
     * @param string|null $filepath The path to the file on disk
     * @param string|null $filecontent The content of the file
     */
    public function __construct(
        /** @var \stdClass The file record */
        protected ?\stdClass $filerecord = null,
        /** @var string|null $filepath The source file on disk */
        protected ?string $filepath = null,
        /** @var string|null $filecontent The content of the file if it is not stored on disk */
        protected ?string $filecontent = null,
    ) {
        if ($filepath === null && $filecontent === null) {
            throw new \InvalidArgumentException('Either $filepath or $filecontent must be set');
        }

        if ($filepath !== null && $filecontent !== null) {
            throw new \InvalidArgumentException('Only one of $filepath or $filecontent can be set');
        }
    }

    /**
     * Whether the file path was specified.
     *
     * @return bool
     */
    public function has_filepath(): bool {
        return $this->filepath !== null;
    }

    /**
     * Whether the file content was specified.
     *
     * @return bool
     */
    public function has_filecontent(): bool {
        return $this->filecontent !== null;
    }

    /**
     * Get the file path to the file that will be stored.
     *
     * @return string
     */
    public function get_filepath(): ?string {
        return $this->filepath;
    }

    /**
     * Get the file content that will be stored.
     *
     * @return string
     */
    public function get_filecontent(): ?string {
        return $this->filecontent;
    }

    /**
     * Get the file record.
     *
     * @return \stdClass|null
     */
    public function get_filerecord(): ?\stdClass {
        return $this->filerecord;
    }

    /**
     * Update the file path to a new value.
     *
     * @param string $filepath
     */
    public function update_filepath(string $filepath): void {
        if ($this->filepath === null) {
            throw new coding_exception('Cannot update file path when the file path is not set');
        }

        if ($filepath !== $this->filepath) {
            $this->contentupdated = true;
            $this->filepath = $filepath;
        }
    }

    /**
     * Update the file content to a new value.
     *
     * @param string $filecontent
     */
    public function update_filecontent(string $filecontent): void {
        if ($this->filecontent === null) {
            throw new coding_exception('Cannot update file content when the file content is not set');
        }

        if ($filecontent !== $this->filecontent) {
            $this->contentupdated = true;
            $this->filecontent = $filecontent;
        }
    }

    /**
     * Whether the file path or file content has been changed.
     *
     * @return bool
     */
    public function has_changed(): bool {
        return $this->contentupdated;
    }

    /**
     * Process legacy callbacks.
     */
    public function process_legacy_callbacks(): void {
        if ($pluginsfunction = get_plugins_with_function(function: 'before_file_created', migratedtohook: true)) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $pluginfunction($this->filerecord);
                }
            }
        }
    }
}
