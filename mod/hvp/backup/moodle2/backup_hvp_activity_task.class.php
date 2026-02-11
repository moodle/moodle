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
 * Defines backup_hvp_activity_task class
 *
 * @package     mod_hvp
 * @category    backup
 * @copyright   2016 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/hvp/backup/moodle2/backup_hvp_stepslib.php');

/**
 * Provides the steps to perform one complete backup of a H5P instance
 *
 * @copyright   2018 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_hvp_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the hvp.xml file
     */
    protected function define_my_steps() {
        global $CFG;

        // Add hvp activity data and content files.
        $this->add_step(new backup_hvp_activity_structure_step('hvp_structure', 'hvp.xml'));

        // Allow user to override library backup.
        $backuplibraries = !(isset($CFG->mod_hvp_backup_libraries) && $CFG->mod_hvp_backup_libraries === '0');

        // Exclude hvp libraries step for local 'imports'.
        if ($backuplibraries && backup_controller_dbops::backup_includes_files($this->plan->get_backupid())) {

            // Note that this step will only run once per backup as it generates
            // a shared resource.
            $this->add_step(new backup_hvp_libraries_structure_step('hvp_libraries', 'hvp_libraries.xml'));
        }
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of glossaries.
        $search = "/(".$base."\/mod\/hvp\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@HVPINDEX*$2@$', $content);

        // Link to hvp view by module id.
        $search = "/(".$base."\/mod\/hvp\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@HVPVIEWBYID*$2@$', $content);

        // Link to hvp embed by module id.
        $search = "/(".$base."\/mod\/hvp\/embed.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@HVPEMBEDBYID*$2@$', $content);

        return $content;
    }
}
