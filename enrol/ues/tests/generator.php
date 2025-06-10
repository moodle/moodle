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

class enrollmentGenerator {

    public function getEnrollment() {
        $params = array('2013', 'Fall', 'LSU', '', '123564324', '765473657346');
        return new Semester($params);
    }
}

abstract class Entity {
    public $keys;

    public function __construct($params = array()) {
        if (!empty($params)) {
            $this->instantiate($params);
        }
    }

    public function instantiate($values) {
        // Order is important!
        foreach ($values as $k => $v) {
            $key = $this->keys[$k];
            $this->$key = $v;
        }
    }
}

class Semester extends Entity {
    public $keys = array('year', 'name', 'campus', 'session_key', 'classes_start', 'grades_due');
}

class Course extends Entity {
    public $keys = array('DEPT_CODE', 'COURSE_NBR', 'COURSE_TITLE', 'SECTION_NBR');
}
