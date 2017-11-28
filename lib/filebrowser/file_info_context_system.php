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
 * Utility class for browsing of system files.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filebrowser/file_info_context_coursecat.php');

/**
 * Represents the system context in the tree navigated by {@link file_browser}.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_context_system extends file_info_context_coursecat {

    /**
     * Constructor
     *
     * @param file_browser $browser file_browser instance
     * @param stdClass $context context object
     */
    public function __construct($browser, $context) {
        parent::__construct($browser, $context, (object)['id' => 0, 'parent' => 0, 'visible' => 1]);
    }

    /**
     * Return information about this specific part of context level
     *
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    public function get_file_info($component, $filearea, $itemid, $filepath, $filename) {
        if (empty($component)) {
            return $this;
        }

        $methodname = "get_area_{$component}_{$filearea}";

        if (method_exists($this, $methodname)) {
            return $this->$methodname($itemid, $filepath, $filename);
        }

        return null;
    }

    /**
     * Gets a stored file for the backup course filearea directory.
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    protected function get_area_backup_course($itemid, $filepath, $filename) {
        global $CFG;

        if (!isloggedin()) {
            return null;
        }

        if (!has_any_capability(array('moodle/backup:backupcourse', 'moodle/restore:restorecourse'), $this->context)) {
            return null;
        }

        if (is_null($itemid)) {
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'backup', 'course', 0, $filepath, $filename)) {
            if ($filepath === '/' && $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'backup', 'course', 0);
            } else {
                // Not found.
                return null;
            }
        }

        $downloadable = has_capability('moodle/backup:downloadfile', $this->context);
        $uploadable = has_capability('moodle/restore:uploadfile', $this->context);

        $urlbase = $CFG->wwwroot . '/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase,
            get_string('coursebackup', 'repository'), false, $downloadable, $uploadable, false);
    }

    /**
     * Returns localised visible name.
     *
     * @return string
     */
    public function get_visible_name() {
        return get_string('arearoot', 'repository');
    }

    /**
     * Whether or not new files or directories can be added
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Whether or not this is a directory
     *
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info|null file_info instance or null for root
     */
    public function get_parent() {
        return null;
    }
}
