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

defined('MOODLE_INTERNAL') || die();

require_once dirname(__FILE__) . '/lib.php';

class azure_semesters extends azure_source implements semester_processor {

    function parse_term($term) {
        $year = (int)substr($term, 0, 4);

        $semester_code = substr($term, -2);

        switch ($semester_code) {
            case self::FALL:
                return array($year - 1, 'Fall');
            case self::FALL1:
                return array($year - 1, 'First Fall');
            case self::FALL2:
                return array($year - 1, 'Second Fall');
            case self::SPRING:
                return array($year, 'Spring');
            case self::SPRING1:
                return array($year, 'First Spring');
            case self::SPRING2:
                return array($year, 'Second Spring');
            case self::SUMMER:
                return array($year, 'Summer');
            case self::SUMMER1:
                return array($year, 'First Summer');
            case self::SUMMER2:
                return array($year - 1, 'Second Summer');
            case self::WINTER_INT:
                return array($year - 1, 'WinterInt');
            case self::SPRING_INT:
                return array($year, 'SpringInt');
            case self::SUMMER_INT:
                return array($year, 'SummerInt');
        }
    }

    function semesters($date_threshold) {

        if (is_numeric($date_threshold)) {
            $date_threshold = ues::format_time($date_threshold);
        }
        $xml_semesters = $this->invoke(array());

        $lookup = array();
        $semesters = array();

        foreach ($xml_semesters->ROW as $xml_semester) {
            $code = $xml_semester->CODE_VALUE;

            $term = (string) $xml_semester->TERM_CODE;

            $session = (string) $xml_semester->SESSION;

            $date = $this->parse_date($xml_semester->CALENDAR_DATE);

            switch ($code) {
                case self::LSU_SEM:
                case self::LSU_FINAL:
                    $campus = 'LSU';
                    $starting = ($code == self::LSU_SEM);
                    break;
                case self::ONLINE_SEM:
                case self::ONLINE_FINAL:
                    $campus = 'ONLINE';
                    $starting = ($code == self::ONLINE_SEM);
                    break;
                case self::LAW_SEM:
                case self::LAW_FINAL:
                    $campus = 'LAW';
                    $starting = ($code == self::LAW_SEM);
                    break;
                default:
                    continue 2;
            }

            if (!isset($lookup[$campus])) {
                $lookup[$campus] = array();
            }

            if ($starting) {
                list($year, $name) = $this->parse_term($term);

                $semester = new stdClass;
                $semester->year = $year;
                $semester->name = $name;
                $semester->campus = $campus;
                $semester->session_key = $session;
                $semester->classes_start = $date;
                $semesters[] = $semester;
            } else if (isset($lookup[$campus][$term][$session])) {

                $semester =& $lookup[$campus][$term][$session];
                $semester->grades_due = $date;

                // Make a semester end 21 days later for our post grade process.
                $semester->grades_due += (21 * 24 * 60 * 60);
                if ($campus == 'LAW') {
                    $semester->grades_due += (25 * 24 * 60 * 60);
                }
            } else {
                continue;
            }

            if (!isset($lookup[$campus][$term])) {
                $lookup[$campus][$term] = array();
            }

            $lookup[$campus][$term][$session] = $semester;
        }

        unset($lookup);

        return $semesters;
    }
}

class azure_courses extends azure_source implements course_processor {

    function courses($semester) {
        $semester_term = $this->encode_semester($semester->year, $semester->name);

        $courses = array();

        $xml_courses = $this->invoke(array($semester_term, $semester->session_key));

        foreach ($xml_courses->ROW as $xml_course) {
            $department = (string) $xml_course->DEPT_CODE;
            $course_number = (string) $xml_course->COURSE_NBR;

            $law_not = ($semester->campus == 'LAW' and $department != 'LAW');
            $azure_not = ($semester->campus == 'LSU' and $department == 'LAW');
            $online_not = ($semester->campus == 'ONLINE' and $department == 'LAW');

            // Course is not semester applicable.
            if ($law_not or $azure_not or $online_not) {
                continue;
            }

            // TODO: this may never get called, considering the conditional below.
            $is_unique = function ($course) use ($department, $course_number) {
                return ($course->department != $department or
                    $course->cou_number != $course_number);
            };

            // TODO: why is this checking the emptiness of an uninitialized var?
            if (empty($course) or $is_unique($course)) {
                $course = new stdClass;
                $course->department = $department;
                $course->cou_number = $course_number;
                $course->course_type = (string) $xml_course->CLASS_TYPE;
                $course->course_first_year = (int) $xml_course->COURSE_NBR < 5200 ? 1 : 0;

                $course->fullname = (string) $xml_course->COURSE_TITLE;
                $course->course_grade_type = (string) $xml_course->GRADE_SYSTEM_CODE;

                $course->sections = array();

                $courses[] = $course;
            }

            $section = new stdClass;
            $section->sec_number = (string) $xml_course->SECTION_NBR;

            $course->sections[] = $section;
        }

        return $courses;
    }
}

class azure_teachers_by_department extends azure_teacher_format implements teacher_by_department {

    function teachers($semester, $department) {
        $semester_term = $this->encode_semester($semester->year, $semester->name);

        $teachers = array();

        // LAW teachers should NOT be processed on an incoming LSU semester.
        if ($department == 'LAW' and $semester->campus == 'LSU') {
            return $teachers;
        }

        // Always use LSU campus code.
        if ($semester->campus == 'ONLINE') {
            $campus = self::ONLINE_CAMPUS;
        } else {
            $campus = self::LSU_CAMPUS;
        }

        $params = array($semester->session_key, $department, $semester_term, $campus);

        $xml_teachers = $this->invoke($params);

        foreach ($xml_teachers->ROW as $xml_teacher) {
            $teacher = $this->format_teacher($xml_teacher);

            // Section information.
            $teacher->department = $department;
            $teacher->cou_number = (string) $xml_teacher->CLASS_COURSE_NBR;
            $teacher->sec_number = (string) $xml_teacher->SECTION_NBR;

            $teachers[] = $teacher;
        }

        return $teachers;
    }
}

class azure_students_by_department extends azure_student_format implements student_by_department {

    function students($semester, $department) {
        $semester_term = $this->encode_semester($semester->year, $semester->name);

        $campus = $semester->campus == 'LSU' ? self::LSU_CAMPUS : ($semester->campus == 'ONLINE' ? self::ONLINE_CAMPUS : self::LAW_CAMPUS);

        $inst = $semester->campus == 'LSU' || $semester->campus == 'ONLINE' ? self::LSU_INST : self::LAW_INST;

        $params = array($campus, $semester_term, $department, $inst, $semester->session_key);

        $xml_students = $this->invoke($params);

        $students = array();
        foreach ($xml_students->ROW as $xml_student) {

            $student = $this->format_student($xml_student);

            // Section information.
            $student->department = $department;
            $student->cou_number = (string) $xml_student->COURSE_NBR;
            $student->sec_number = (string) $xml_student->SECTION_NBR;

            $students[] = $student;
        }

        return $students;
    }
}

class azure_teachers extends azure_teacher_format implements teacher_processor {

    function teachers($semester, $course, $section) {
        $semester_term = $this->encode_semester($semester->year, $semester->name);

        $teachers = array();

        // LAW teachers should NOT be processed on an incoming LSU semester.
        if ($course->department == 'LAW' and $semester->campus == 'LSU') {
            return $teachers;
        }

        if ($semester->campus == 'ONLINE') {
            $campus = self::ONLINE_CAMPUS;
        } else {
            $campus = self::LSU_CAMPUS;
        }

        $params = array($course->cou_number, $semester->session_key,
            $section->sec_number, $course->department, $semester_term, $campus);

        $xml_teachers = $this->invoke($params);

        foreach ($xml_teachers->ROW as $xml_teacher) {

            $teachers[] = $this->format_teacher($xml_teacher);
        }

        return $teachers;
    }
}

class azure_students extends azure_student_format implements student_processor {

    function students($semester, $course, $section) {
        $semester_term = $this->encode_semester($semester->year, $semester->name);

        $campus = $semester->campus == 'LSU' ? self::LSU_CAMPUS : ($semester->campus == 'ONLINE' ? self::ONLINE_CAMPUS : self::LAW_CAMPUS);

        $params = array($campus, $semester_term, $course->department,
            $course->cou_number, $section->sec_number, $semester->session_key);

        $xml_students = $this->invoke($params);

        $students = array();
        foreach ($xml_students->ROW as $xml_student) {

            $students[] = $this->format_student($xml_student);
        }

        return $students;
    }
}

class azure_student_data extends azure_source {

    function student_data($semester) {
        $semester_term = $this->encode_semester($semester->year, $semester->name);
        $params = array($semester_term);

        if ($semester->campus == 'LSU') {
            $params += array(1 => self::LSU_INST, 2 => self::LSU_CAMPUS);
        } else if ($semester->campus == 'ONLINE') {
            $params += array(1 => self::LSU_INST, 2 => self::ONLINE_CAMPUS);
        } else {
            $params += array(1 => self::LAW_INST, 2 => self::LAW_CAMPUS);
        }

        $xml_data = $this->invoke($params);
        $student_data = array();

        foreach ($xml_data->ROW as $xml_student_data) {
            $stud_data = new stdClass;

            $reg = trim((string) $xml_student_data->REGISTRATION_DATE);
            $stud_data->user_year = (string) $xml_student_data->YEAR_CLASS;
            $stud_data->user_college = (string) $xml_student_data->COLLEGE_CODE;
            $stud_data->user_major = (string) $xml_student_data->CURRIC_CODE;
            $stud_data->user_reg_status = $reg == 'null' ? null : $this->parse_date($reg);
            $stud_data->user_keypadid = (string) $xml_student_data->KEYPAD_ID;
            $stud_data->idnumber = trim((string)$xml_student_data->LSU_ID);

            $student_data[$stud_data->idnumber] = $stud_data;
        }

        return $student_data;
    }
}

class azure_degree extends azure_source {

    function student_data($semester) {
        $term = $this->encode_semester($semester->year, $semester->name);
        $params = array($term);

        if ($semester->campus == 'LSU') {
            $params += array(
                1 => self::LSU_INST,
                2 => self::LSU_CAMPUS
            );
        } else if ($semester->campus == 'ONLINE') {
            $params += array(
                1 => self::LSU_INST,
                2 => self::ONLINE_CAMPUS
            );
        } else {
            $params += array(
                1 => self::LAW_INST,
                2 => self::LAW_CAMPUS
            );
        }

        $xml_grads = $this->invoke($params);

        $graduates = array();
        foreach($xml_grads->ROW as $xml_grad) {
            $graduate = new stdClass;

            $graduate->idnumber = (string) $xml_grad->LSU_ID;
            $graduate->user_degree = 'Y';
            $graduates[$graduate->idnumber] = $graduate;
        }

        return $graduates;
    }
}

class azure_anonymous extends azure_source {

    function student_data($semester) {
        if ($semester->campus == 'LSU') {
            return array();
        }

        $term = $this->encode_semester($semester->year, $semester->name);
        $xml_numbers = $this->invoke(array($term));

        $numbers = array();
        foreach ($xml_numbers->ROW as $xml_number) {
            $number = new stdClass;

            $number->idnumber = (string) $xml_number->LSU_ID;
            $number->user_anonymous_number = (string) $xml_number->LAW_ANONYMOUS_NBR;
            $numbers[$number->idnumber] = $number;
        }

        return $numbers;
    }
}

class azure_sports extends azure_source {

    /**
     * @todo refactor to take advantage of the DateTime classes
     * @param type $time
     * @return type
     */
    function find_season($time) {
        $now = getdate($time);

        $june = get_config('local_azure', 'junedate');
        $dec = get_config('local_azure', 'decemberdate');

//        $june = 604;
//        $dec = 1231;

        $cur = (int)(sprintf("%d%02d", $now['mon'], $now['mday']));

        mtrace("Current - $cur, June - $june, December - $dec.");

        if ($cur >= $june and $cur <= $dec) {
            return ($now['year']) . substr($now['year'] + 1, 2);
        } else {
            return ($now['year'] - 1) . substr($now['year'], 2);
        }
    }

    function student_data($semester) {
        if ($semester->campus == 'LAW') {
            return array();
        }

        $now = time();
        $xml_infos = $this->invoke(array($this->find_season($now)));

        $numbers = array();
        foreach ($xml_infos->ROW as $xml_info) {
            $number = new stdClass;

            $number->idnumber = (string) $xml_info->LSU_ID;
            $number->user_sport1 = (string) $xml_info->SPORT_CODE_1;
            $number->user_sport2 = (string) $xml_info->SPORT_CODE_2;
            $number->user_sport3 = (string) $xml_info->SPORT_CODE_3;
            $number->user_sport4 = (string) $xml_info->SPORT_CODE_4;

            $numbers[$number->idnumber] = $number;
        }

        return $numbers;
    }
}
