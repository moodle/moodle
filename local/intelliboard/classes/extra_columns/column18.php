<?php

namespace local_intelliboard\extra_columns;

class column18 extends base_column {
    public function get_join() {
        $userid = isset($this->fields[0]) ? $this->fields[0] : "u.id";
        $coursemoduleid = isset($this->fields[4]) ? $this->fields[4] : "cm.id";

        return [
            "LEFT JOIN {course_modules_completion} course_module_completion ON course_module_completion.coursemoduleid = {$coursemoduleid} AND course_module_completion.userid = {$userid}",
            []
        ];
    }
}