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
 * Defines backup_feedback_activity_task class
 *
 * @package     mod_feedback
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/feedback/backup/moodle2/backup_feedback_stepslib.php');
require_once($CFG->dirroot . '/mod/feedback/backup/moodle2/backup_feedback_settingslib.php');

/**
 * Provides the steps to perform one complete backup of the Feedback instance
 */
class backup_feedback_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the feedback.xml file
     */
    protected function define_my_steps() {
        // feedback only has one structure step
        $this->add_step(new backup_feedback_activity_structure_step('feedback structure', 'feedback.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of feedbacks
        $search="/(".$base."\/mod\/feedback\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FEEDBACKINDEX*$2@$', $content);

        // Link to feedback view by moduleid
        $search="/(".$base."\/mod\/feedback\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FEEDBACKVIEWBYID*$2@$', $content);

        // Link to feedback analyis by moduleid
        $search="/(".$base."\/mod\/feedback\/analysis.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FEEDBACKANALYSISBYID*$2@$', $content);

        // Link to feedback entries by moduleid
        $search="/(".$base."\/mod\/feedback\/show_entries.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FEEDBACKSHOWENTRIESBYID*$2@$', $content);

        return $content;
    }
}
