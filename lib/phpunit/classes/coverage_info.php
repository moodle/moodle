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
 * Coverage information for PHPUnit.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Coverage information for PHPUnit.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_coverage_info {

    /** @var array The list of folders relative to the plugin root to whitelist in coverage generation. */
    protected $whitelistfolders = [];

    /** @var array The list of files relative to the plugin root to whitelist in coverage generation. */
    protected $whitelistfiles = [];

    /** @var array The list of folders relative to the plugin root to excludelist in coverage generation. */
    protected $excludelistfolders = [];

    /** @var array The list of files relative to the plugin root to excludelist in coverage generation. */
    protected $excludelistfiles = [];

    /**
     * Get the formatted XML list of files and folders to whitelist.
     *
     * @param   string  $plugindir The root of the plugin, relative to the dataroot.
     * @return  array
     */
    final public function get_whitelists(string $plugindir) : array {
        $filters = [];

        if (!empty($plugindir)) {
            $plugindir .= "/";
        }

        foreach ($this->whitelistfolders as $folder) {
            $filters[] = html_writer::tag('directory', "{$plugindir}{$folder}", ['suffix' => '.php']);
        }

        foreach ($this->whitelistfiles as $file) {
            $filters[] = html_writer::tag('file', "{$plugindir}{$file}");
        }

        return $filters;
    }

    /**
     * Get the formatted XML list of files and folders to exclude.
     *
     * @param   string  $plugindir The root of the plugin, relative to the dataroot.
     * @return  array
     */
    final public function get_excludelists(string $plugindir) : array {
        $filters = [];

        if (!empty($plugindir)) {
            $plugindir .= "/";
        }

        foreach ($this->excludelistfolders as $folder) {
            $filters[] = html_writer::tag('directory', "{$plugindir}{$folder}", ['suffix' => '.php']);
        }

        foreach ($this->excludelistfiles as $file) {
            $filters[] = html_writer::tag('file', "{$plugindir}{$file}");
        }

        return $filters;
    }
}
