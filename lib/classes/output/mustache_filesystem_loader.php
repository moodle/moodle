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
 * Perform some custom name mapping for template file names (strip leading component/).
 *
 * @package    core
 * @category   output
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use coding_exception;

/**
 * Perform some custom name mapping for template file names.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
class mustache_filesystem_loader extends \Mustache_Loader_FilesystemLoader {

    /**
     * Provide a default no-args constructor (we don't really need anything).
     */
    public function __construct() {
    }

    /**
     * Helper function for getting a Mustache template file name.
     * Uses the leading component to restrict us specific directories.
     *
     * @param string $name
     * @return string Template file name
     */
    protected function getFileName($name) {
        // Call the Moodle template finder.
        return mustache_template_finder::get_template_filepath($name);
    }

    /**
     * Only check if baseDir is a directory and requested templates are files if
     * baseDir is using the filesystem stream wrapper.
     *
     * Always check path for mustache_filesystem_loader.
     *
     * @return bool Whether to check `is_dir` and `file_exists`
     */
    protected function shouldCheckPath() {
        return true;
    }

    /**
     * Load a Template by name.
     *
     * @param string $name the template name
     * @return string Mustache Template source
     */
    public function load($name) {
        global $CFG;
        if (!empty($CFG->debugtemplateinfo)) {
            // We use many templates per page. We don't want to allocate more memory than necessary.
            return "<!-- template(PHP): $name -->" . parent::load($name) . "<!-- /template(PHP): $name -->";
        }
        return parent::load($name);
    }
}
