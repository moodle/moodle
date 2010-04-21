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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/mod/forum/backup/moodle2/backup_forum_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/forum/backup/moodle2/backup_forum_settingslib.php'); // Because it exists (optional)

/**
 * forum backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_forum_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Forum only has one structure step
        $this->add_step(new backup_forum_activity_structure_step('forum structure', 'forum.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of forums
        $search="/(".$base."\/mod\/forum\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FORUMINDEX*$2@$', $content);

        // Link to forum view by moduleid
        $search="/(".$base."\/mod\/forum\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FORUMVIEWBYID*$2@$', $content);

        // Link to forum view by forumid
        $search="/(".$base."\/mod\/forum\/view.php\?f\=)([0-9]+)/";
        $content= preg_replace($search, '$@FORUMVIEWBYF*$2@$', $content);

        // Link to forum discussion with parent syntax
        $search="/(".$base."\/mod\/forum\/discuss.php\?d\=)([0-9]+)\&parent\=([0-9]+)/";
        $content= preg_replace($search, '$@FORUMDISCUSSIONVIEWPARENT*$2*$3@$', $content);

        // Link to forum discussion with relative syntax
        $search="/(".$base."\/mod\/forum\/discuss.php\?d\=)([0-9]+)\#([0-9]+)/";
        $content= preg_replace($search, '$@FORUMDISCUSSIONVIEWINSIDE*$2*$3@$', $content);

        // Link to forum discussion by discussionid
        $search="/(".$base."\/mod\/forum\/discuss.php\?d\=)([0-9]+)/";
        $content= preg_replace($search, '$@FORUMDISCUSSIONVIEW*$2@$', $content);

        return $content;
    }
}
