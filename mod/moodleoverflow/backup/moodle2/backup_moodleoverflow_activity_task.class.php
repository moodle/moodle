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
 * Defines backup_moodleoverflow_activity_task class
 *
 * @package   mod_moodleoverflow
 * @category  backup
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/moodleoverflow/backup/moodle2/backup_moodleoverflow_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the moodleoverflow instance
 *
 * @package   mod_moodleoverflow
 * @category  backup
 * @copyright 2016 Your Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_moodleoverflow_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the moodleoverflow.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_moodleoverflow_activity_structure_step('moodleoverflow_structure', 'moodleoverflow.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     *
     * @return string the content with the URLs encoded
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of moodleoverflows.
        $search  = '/(' . $base . '\/mod\/moodleoverflow\/index.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@MOODLEOVERFLOWINDEX*$2@$', $content);

        // Link to moodleoverflow view by moduleid.
        $search  = '/(' . $base . '\/mod\/moodleoverflow\/view.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@MOODLEOVERFLOWVIEWBYID*$2@$', $content);

        // Link to moodleoverflow view by forumid.
        $search  = "/(" . $base . "\/mod\/forum\/view.php\?f\=)([0-9]+)/";
        $content = preg_replace($search, '$@MOODLEOVERFLOWVIEWBYF*$2@$', $content);

        // Link to moodleoverflow discussion with parent syntax.
        $search  = "/(" . $base . "\/mod\/forum\/discuss.php\?d\=)([0-9]+)(?:\&amp;|\&)parent\=([0-9]+)/";
        $content = preg_replace($search, '$@MOODLEOVERFLOWDISCUSSIONVIEWPARENT*$2*$3@$', $content);

        // Link to moodleoverflow discussion with relative syntax.
        $search  = "/(" . $base . "\/mod\/forum\/discuss.php\?d\=)([0-9]+)\#([0-9]+)/";
        $content = preg_replace($search, '$@MOODLEOVERFLOWDISCUSSIONVIEWINSIDE*$2*$3@$', $content);

        // Link to moodleoverflow discussion by discussionid.
        $search  = "/(" . $base . "\/mod\/forum\/discuss.php\?d\=)([0-9]+)/";
        $content = preg_replace($search, '$@MOODLEOVERFLOWDISCUSSIONVIEW*$2@$', $content);

        return $content;
    }
}
