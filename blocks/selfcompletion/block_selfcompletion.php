<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->libdir.'/completionlib.php');

/**
 * Self course completion marking
 * Let's a user manually complete a course
 *
 * Will only display if the course has completion enabled,
 * there is a self completion criteria, and the logged in user is yet
 * to complete the course.
 */
class block_selfcompletion extends block_base {

    public function init() {
        $this->title   = get_string('selfcompletion', 'block_selfcompletion');
    }

    public function get_content() {
        global $USER;

        // If content is cached
        if ($this->content !== NULL) {
          return $this->content;
        }

        global $CFG;

        // Create empty content
        $this->content = new stdClass;

        // Don't display if completion isn't enabled!
        if (!$this->page->course->enablecompletion) {
            $this->content->text = get_string('completionnotenabled', 'block_selfcompletion');
            return $this->content;
        }

        // Get course completion data
        $info = new completion_info($this->page->course);
        $completion = $info->get_completion($USER->id, COMPLETION_CRITERIA_TYPE_SELF);

        // Is course complete?
        if ($info->is_course_complete($USER->id)) {
            return $this->content;
        }

        // Check if self completion is one of this course's criteria
        if (empty($completion)) {
            $this->content->text = get_string('selfcompletionnotenabled', 'block_selfcompletion');
            return $this->content;
        }

        // Check this user is enroled
        if (!$info->is_tracked_user($USER->id)) {
            $this->content->text = get_string('notenroled', 'completion');
            return $this->content;
        }

        // Check if the user has already marked themselves as complete
        if ($completion->is_complete()) {
            return $this->content;
        } else {
            $this->content->text = '';
            $this->content->footer = '<br /><a href="'.$CFG->wwwroot.'/course/togglecompletion.php?course='.$this->page->course->id.'">'.
                                       get_string('completecourse', 'block_selfcompletion').'</a>...';
        }

        return $this->content;
    }
}
