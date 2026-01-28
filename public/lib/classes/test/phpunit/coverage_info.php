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

namespace core\test\phpunit;

use core\output\html_writer;

/**
 * Coverage information for PHPUnit.
 *
 * @package    core
 * @category   test
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coverage_info {
    /** @var array The list of folders relative to the plugin root to include in coverage generation. */
    protected $includelistfolders = [];

    /** @var array The list of files relative to the plugin root to include in coverage generation. */
    protected $includelistfiles = [];

    /** @var array The list of folders relative to the plugin root to exclude from coverage generation. */
    protected $excludelistfolders = [];

    /** @var array The list of files relative to the plugin root to exclude from coverage generation. */
    protected $excludelistfiles = [];

    /** @var string The base directory to use */
    protected string $basedir = "";

    /**
     * Specify the base directory to use for coverage.
     *
     * @param string $basedir
     */
    final public function set_basedir(string $basedir): void {
        if ($basedir === '') {
            $this->basedir = '';
        } else {
            $this->basedir = trim($basedir, '/') . "/";
        }
    }

    /**
     * Get the formatted XML list of files and folders to include.
     *
     * @param   string  $plugindir The root of the plugin, relative to the dataroot.
     * @return  array
     */
    final public function get_includelists(string $plugindir): array {
        $coverages = [];

        $includelistfolders = array_merge([
            'classes',
            'tests/generator',
        ], $this->includelistfolders);

        $includelistfiles = array_merge([
            'externallib.php',
            'lib.php',
            'locallib.php',
            'renderer.php',
            'rsslib.php',
        ], $this->includelistfiles);

        if (!empty($plugindir)) {
            $plugindir .= "/";
        }

        foreach (array_unique($includelistfolders) as $folder) {
            $coverages[] = html_writer::tag('directory', "{$this->basedir}{$plugindir}{$folder}", ['suffix' => '.php']);
        }

        foreach (array_unique($includelistfiles) as $file) {
            $coverages[] = html_writer::tag('file', "{$this->basedir}{$plugindir}{$file}");
        }

        return $coverages;
    }

    /**
     * Get the formatted XML list of files and folders to exclude.
     *
     * @param   string  $plugindir The root of the plugin, relative to the dataroot.
     * @return  array
     */
    final public function get_excludelists(string $plugindir): array {
        $coverages = [];

        if (!empty($plugindir)) {
            $plugindir .= "/";
        }

        foreach ($this->excludelistfolders as $folder) {
            $coverages[] = html_writer::tag('directory', "{$this->basedir}{$plugindir}{$folder}", ['suffix' => '.php']);
        }

        foreach ($this->excludelistfiles as $file) {
            $coverages[] = html_writer::tag('file', "{$this->basedir}{$plugindir}{$file}");
        }

        return $coverages;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(coverage_info::class, \phpunit_coverage_info::class);
