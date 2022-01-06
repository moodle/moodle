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
 * Defines backup_pdfannotator_activity_task class
 *
 * Moodle creates backups of courses or their parts by executing a so called backup plan.
 * The backup plan consists of a set of backup tasks and finally each backup task consists of one or more backup steps.
 * This file provides the activity task class.
 *
 * See https://docs.moodle.org/dev/Backup_API and https://docs.moodle.org/dev/Backup_2.0_for_developers for more information.
 *
 * @package   mod_pdfannotator
 * @category  backup
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/pdfannotator/backup/moodle2/backup_pdfannotator_stepslib.php');

/**
 * Pdfannotator backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_pdfannotator_activity_task extends backup_activity_task {


    /**
     * Define (add) particular settings this activity can have
     * If your module declares own backup settings defined in the file backup_foobar_settingslib.php, add them here.
     * Most modules just leave the method body empty.
     */
    protected function define_my_settings() {
        // No particular settings for the pdfannotator activity.
    }

    /**
     * Defines a backup step to store the instance data in the pdfannotator.xml file
     *
     * Define (add) particular steps this activity can have
     * This method typically consists of one or more $this->add_step() calls.
     * This is the place where you define the task as a sequence of steps to execute.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_pdfannotator_activity_structure_step('pdfannotator_structure', 'pdfannotator.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * The current instance of the activity may be referenced from other places in the course by
     * URLs like http://my.moodle.site/mod/foobar/view.php?id=42
     * Obviously, such URLs are not valid any more once the course is restored elsewhere.
     * For this reason the backup file does not store the original URLs but encodes them into a
     * transportable form. During the restore, the reverse process is applied and the encoded URLs
     * are replaced with the new ones valid for the target site.
     *
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {

        global $CFG, $DB;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of pdfannotators.
        $search = "/(".$base."\/mod\/pdfannotator\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@PDFANNOTATORINDEX*$2@$', $content);

        // Link to pdfannotator view by moduleid.
        $search = "/(".$base."\/mod\/pdfannotator\/view.php\?id\=)([0-9]+)/";
        // Link to pdfannotator view by recordid.
        $search2 = "/(".$base."\/mod\/pdfannotator\/view.php\?r\=)([0-9]+)/";

        return $content;
    }
}
