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

namespace core_files\tests\redactor\services;

use core_files\redactor\services\file_redactor_service_interface;
use core_files\redactor\services\service;

/**
 * Dummy service for testing only.
 *
 * @package   core
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dummy_file_service extends service implements file_redactor_service_interface {
    #[\Override]
    public function redact_file_by_content(string $mimetype, string $filecontent): string {
        return "redacted:{$filecontent}";
    }

    #[\Override]
    public function redact_file_by_path(string $mimetype, string $filepath): string {
        return "/redacted{$filepath}";
    }

    /**
     * Returns true if the service is enabled, and "false" if it is not.
     *
     * @return bool
     */
    public function is_enabled(): bool {
        return true;
    }

    /**
     * Determines whether a certain mime-type is supported by the service.
     * It will return true if the mime-type is supported, and false if it is not.
     *
     * @param string $mimetype
     * @return bool
     */
    public function is_mimetype_supported(string $mimetype): bool {
        if (str_starts_with($mimetype, 'image/')) {
            return true;
        }

        return false;
    }

    /**
     * Adds settings to the provided admin settings page.
     *
     * @param \admin_settingpage $settings The admin settings page to which settings are added.
     */
    public static function add_settings(\admin_settingpage $settings): void {
        // The function body.
    }
}
