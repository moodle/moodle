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

namespace core_files\redactor\services;

/**
 * Class file_redactor_service_interface
 *
 * @package    core_files
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface file_redactor_service_interface {
    /**
     * Performs redaction on the specified stored_file.
     *
     * @param string $mimetype The mime-type of the file
     * @param string $filepath The path of the file to redact
     * @return string|null The path to the redacted file, or null if redaction was not attempted
     */
    public function redact_file_by_path(
        string $mimetype,
        string $filepath,
    ): ?string;

    /**
     * Performs redaction on the specified stored_file.
     *
     * @param string $mimetype The mime-type of the file
     * @param string $filecontent The content of the file to redact
     * @return string|null The redacted content, or null if redaction was not attempted
     */
    public function redact_file_by_content(
        string $mimetype,
        string $filecontent,
    ): ?string;

    /**
     * Determines whether a certain mime-type is supported by the service.
     *
     * @param string $mimetype
     * @return bool
     */
    public function is_mimetype_supported(string $mimetype): bool;
}
