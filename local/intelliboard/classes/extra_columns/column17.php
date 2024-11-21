<?php

namespace local_intelliboard\extra_columns;

class column17 extends base_column {
    public function get_join() {
        $coursetablealias = isset($this->fields[1]) ? $this->fields[1] : "c.id";
        $coursesectionid = isset($this->fields[3]) ? $this->fields[3] : "cm.section";

        return [
            "LEFT JOIN {course_sections} course_sections ON course_sections.id = {$coursesectionid} AND course_sections.course = {$coursetablealias}",
            []
        ];
    }
}