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
 * Defines classes used for plugin info.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for admin tool plugins
 */
class gradingform extends base {

    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * Pre-uninstall hook.
     * This is intended for disabling of plugin, some DB table purging, etc.
     */
    public function uninstall_cleanup() {
        global $DB;

        // Find all definitions and templates.
        $definitions = $DB->get_fieldset_select('grading_definitions', 'id', 'method = ?', [$this->name]);
        if ($definitions) {
            // Delete instances and definitions. Deleting instance will not delete grades because they were
            // already pushed to the module and gradebook.
            list($sqld, $paramsd) = $DB->get_in_or_equal($definitions);
            $DB->delete_records_select('grading_instances', 'definitionid ' . $sqld, $paramsd);
            $DB->delete_records_select('grading_definitions', 'id ' . $sqld, $paramsd);
        }
        // Delete templates for this grading method.
        $DB->delete_records_select('grading_areas', 'component = ? AND activemethod = ?', array('core_grading', $this->name));
        // Update the remaining grading areas to use simple grading method instead of this grading method.
        $DB->execute('UPDATE {grading_areas} SET activemethod = NULL WHERE activemethod = ?', [$this->name]);

        parent::uninstall_cleanup();
    }
}
