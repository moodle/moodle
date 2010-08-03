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
 * This file contains the backup task for the lesson module
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Require the backup lesson steps lib
 */
require_once($CFG->dirroot . '/mod/lesson/backup/moodle2/backup_lesson_stepslib.php');

/**
 * The backup task class for the lesson module
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_lesson_activity_task extends backup_activity_task {

    protected function define_my_settings() {
        // There are no settings
    }

    protected function define_my_steps() {
        $this->add_step(new backup_lesson_activity_structure_step('lesson structure', 'lesson.xml'));
    }

    /**
     * Replaces links within the content with links that can be corrected during
     * restore
     *
     * @param string $content
     * @return string
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot.'/mod/lesson','#');

        // Provides the interface for overall authoring of lessons
        $pattern = '#'.$base.'/edit\.php\?id=([0-9]+)#';
        $replacement = '$@LESSONEDIT*$1@$';
        $content = preg_replace($pattern, $replacement, $content);

        // Action for adding a question page.  Prints an HTML form.
        $pattern = '#'.$base.'/editpage\.php\?id=([0-9]+)&(amp;)?pageid=([0-9]+)#';
        $replacement = '$@LESSONEDITPAGE*$1*$3@$';
        $content = preg_replace($pattern, $replacement, $content);

        // Provides the interface for grading essay questions
        $pattern = '#'.$base.'/essay\.php\?id=([0-9]+)#';
        $replacement = '$@LESSONESSAY*$1@$';
        $content = preg_replace($pattern, $replacement, $content);

        // Provides the interface for viewing and adding high scores
        $pattern = '#'.$base.'/highscores\.php\?id=([0-9]+)#';
        $replacement = '$@LESSONHIGHSCORES*$1@$';
        $content = preg_replace($pattern, $replacement, $content);

        // Provides the interface for viewing the report
        $pattern = '#'.$base.'/report\.php\?id=([0-9]+)#';
        $replacement = '$@LESSONREPORT*$1@$';
        $content = preg_replace($pattern, $replacement, $content);

        // This file plays the mediafile set in lesson settings.
        $pattern = '#'.$base.'/mediafile\.php\?id=([0-9]+)#';
        $replacement = '$@LESSONMEDIAFILE*$1@$';
        $content = preg_replace($pattern, $replacement, $content);

        // This page lists all the instances of lesson in a particular course
        $pattern = '#'.$base.'/index\.php\?id=([0-9]+)#';
        $replacement = '$@LESSONINDEX*$1@$';
        $content = preg_replace($pattern, $replacement, $content);

        // This page prints a particular page of lesson
        $pattern = '#'.$base.'/view\.php\?id=([0-9]+)&(amp;)?pageid=([0-9]+)#';
        $replacement = '$@LESSONVIEWPAGE*$1*$3@$';
        $content = preg_replace($pattern, $replacement, $content);

        // Link to one lesson by cmid
        $pattern = '#'.$base.'/view\.php\?id=([0-9]+)#';
        $replacement = '$@LESSONVIEWBYID*$1@$';
        $content = preg_replace($pattern, $replacement, $content);

        // Return the now encoded content
        return $content;
    }

}
