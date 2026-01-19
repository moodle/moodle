<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

class block_continue_learning extends block_base {

    /**
     * Init block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_continue_learning');
    }

    /**
     * Where this block can be added
     */
    public function applicable_formats() {
        return [
            'my' => true,        // Dashboard
            'site-index' => true,
            'course-view' => true,
            'course' => true,
            'all' => false,
        ];
    }

    /**
     * Block content
     */
    public function get_content() {
        global $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $templatecontext = [
            'title' => 'Continue learning',
            'coursename' => 'Demo Course',
            'progress' => 45,
            'resumeurl' => new moodle_url('/course/view.php', ['id' => 320]),
        ];

        $this->content->text = $OUTPUT->render_from_template(
            'block_continue_learning/continue_learning',
            $templatecontext
        );

        return $this->content;
    }


    /**
     * Core logic: get last accessed course
     */
    private function get_continue_learning_course(int $userid) {
        global $DB;

        $sql = "
            SELECT c.*
            FROM {course} c
            JOIN {user_lastaccess} ula ON ula.courseid = c.id
            WHERE ula.userid = :userid
              AND c.id <> 1
            ORDER BY ula.timeaccess DESC
        ";

        return $DB->get_record_sql($sql, ['userid' => $userid], IGNORE_MULTIPLE);
    }
}
