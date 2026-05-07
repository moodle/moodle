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
 * Provides a mock testable_tool_installaddon_installer_without_site_info class.
 *
 * @package     tool_installaddon
 * @subpackage  fixtures
 * @category    test
 * @copyright   2026 Safat Shahin <safat.shahin@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/testable_installer.php');

/**
 * Testable subclass with site-info sharing disabled.
 *
 * @copyright 2026 Safat Shahin <safat.shahin@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_tool_installaddon_installer_without_site_info extends testable_tool_installaddon_installer {
    /**
     * Disable site info sharing.
     *
     * @return bool
     */
    protected function should_send_site_info() {
        return false;
    }
}
