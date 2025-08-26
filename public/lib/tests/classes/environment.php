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

namespace core\tests;

/**
 * Test helper for the \core\environment class.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class environment extends \core\environment {
    /** @var string|null The path to the vendor dir if modified */
    protected static ?string $vendorpath;

    /** @var bool|null Whether developer mode is enabled, or defer to $CFG */
    protected static ?bool $devmode = null;

    /**
     * Set the developer mode for testing purposes.
     *
     * @param bool $enabled
     */
    public static function set_developer_mode(bool $enabled): void {
        self::$devmode = $enabled;
    }

    /**
     * Set the vendor path for testing purposes.
     *
     * @param string $path
     */
    public static function set_vendor_path(string $path): void {
        self::$vendorpath = $path;
    }

    #[\Override]
    protected static function get_vendor_path(): string {
        if (self::$vendorpath) {
            return self::$vendorpath;
        }

        return parent::get_vendor_path();
    }

    #[\Override]
    protected static function is_developer_mode_enabled(): bool {
        if (self::$devmode !== null) {
            return self::$devmode;
        }

        return parent::is_developer_mode_enabled();
    }
}
