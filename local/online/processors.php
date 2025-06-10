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

require_once(dirname(__FILE__) . '/lib.php');

class online_semesters extends online_source implements semester_processor {

    public function parse_term($term) {
        $year = (int)substr($term, 0, 4);

        $semestercode = substr($term, -2);

        switch ($semestercode) {
            case self::FALL1:
                return array($year - 1, 'First Fall');
            case self::FALL2:
                return array($year - 1, 'Second Fall');
            case self::SPRING1:
                return array($year, 'First Spring');
            case self::SPRING2:
                return array($year, 'Second Spring');
            case self::SUMMER1:
                return array($year, 'First Summer');
            case self::SUMMER2:
                return array($year - 1, 'Second Summer');
        }
    }


    /**
     * @param string $semestername English name of the the semester
     * @return online_semester_data the value of the constant
     * representing the incoming semester name
     * @throws Exception if the incoming semester name is not valid
     */
    public static function semesterCode($semestername) {
        $map = array(
            'First Fall'    => self::Fall1,
            'Second Fall'   => self::FALL2,
            'First Spring'  => self::SPRING1,
            'Second Spring' => self::SPRING2,
            'First Summer'  => self::SUMMER1,
            'Second Summer' => self::SUMMER2,
            );
        if (!array_key_exists($semestername, $map)) {
            throw new Exception(sprintf("No such semester named '%d'.", $semestername));
        }
        return $map[$semestername];
    }

    public function semesters($datethreshold) {

        if (is_numeric($datethreshold)) {
            $datethreshold = ues::format_time($datethreshold);
        }

        $xmlsemesters = $this->invoke(array());

        $lookup = array();
        $semesters = array();

        foreach ($xmlsemesters->ROW as $xmlsemester) {
            $code = $xmlsemester->CODE_VALUE;

            $term = (string) $xmlsemester->TERM_CODE;

            $session = (string) $xmlsemester->SESSION;

            $date = $this->parse_date($xmlsemester->CALENDAR_DATE);

            switch ($code) {
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
                    break;
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

class online_courses extends online_source implements course_processor {

    public function courses($semester) {
        $semesterterm = $this->encode_semester($semester->year, $semester->name);

        $courses = array();

        $xmlcourses = $this->invoke(array($semesterterm, $semester->session_key));

        foreach ($xmlcourses->ROW as $xmlcourse) {
            $department = (string) $xmlcourse->DEPT_CODE;
            $coursenumber = (string) $xmlcourse->COURSE_NBR;

            $lawnot = ($semester->campus == 'LAW' and $department != 'LAW');
            $onlinenot = ($semester->campus == 'ONLINE' and $department == 'LAW');

            // Course is not semester applicable.
            if ($lawnot or $onlinenot) {
                continue;
            }

            $isunique = function ($course) use ($department, $coursenumber) {
                return ($course->department != $department or
                    $course->cou_number != $coursenumber);
            };

            if (empty($course) or $isunique($course)) {
                $course = new stdClass;
                $course->department = $department;
                $course->cou_number = $coursenumber;
                $course->course_type = (string) $xmlcourse->CLASS_TYPE;
                $course->course_first_year = (int) $xmlcourse->COURSE_NBR < 5200 ? 1 : 0;

                $course->fullname = (string) $xmlcourse->COURSE_TITLE;
                $course->course_grade_type = (string) $xmlcourse->GRADE_SYSTEM_CODE;

                $course->sections = array();

                $courses[] = $course;
            }

            $section = new stdClass;
            $section->sec_number = (string) $xmlcourse->SECTION_NBR;

            $course->sections[] = $section;
        }

        return $courses;
    }
}

class online_teachers_by_department extends online_teacher_format implements teacher_by_department {

    public function teachers($semester, $department) {
        $semesterterm = $this->encode_semester($semester->year, $semester->name);

        $teachers = array();

        // LAW teachers should NOT be processed on an incoming ONLINE semester.
        if ($department == 'LAW' and $semester->campus == 'ONLINE') {
            return $teachers;
        }

        // Always use ONLINE campus code.
        $campus = self::ONLINE_CAMPUS;

        $params = array($semester->session_key, $department, $semesterterm, $campus);

        $xmlteachers = $this->invoke($params);

        foreach ($xmlteachers->ROW as $xmlteacher) {
            $teacher = $this->format_teacher($xmlteacher);

            // Section information.
            $teacher->department = $department;
            $teacher->cou_number = (string) $xmlteacher->CLASS_COURSE_NBR;
            $teacher->sec_number = (string) $xmlteacher->SECTION_NBR;

            $teachers[] = $teacher;
        }

        return $teachers;
    }
}

class online_students_by_department extends online_student_format implements student_by_department {

    public function students($semester, $department) {
        $semesterterm = $this->encode_semester($semester->year, $semester->name);

        $campus = $semester->campus == 'ONLINE' ? self::ONLINE_CAMPUS : self::LAW_CAMPUS;

        $inst = $semester->campus == 'ONLINE' ? self::ONLINE_INST : self::LAW_INST;

        $params = array($campus, $semesterterm, $department, $inst, $semester->session_key);

        $xmlstudents = $this->invoke($params);

        $students = array();
        foreach ($xmlstudents->ROW as $xmlstudent) {

            $student = $this->format_student($xmlstudent);

            // Section information.
            $student->department = $department;
            $student->cou_number = (string) $xmlstudent->COURSE_NBR;
            $student->sec_number = (string) $xmlstudent->SECTION_NBR;

            $students[] = $student;
        }

        return $students;
    }
}

class online_teachers extends online_teacher_format implements teacher_processor {

    public function teachers($semester, $course, $section) {
        $semesterterm = $this->encode_semester($semester->year, $semester->name);

        $teachers = array();

        // LAW teachers should NOT be processed on an incoming ONLINE semester.
        if ($course->department == 'LAW' and $semester->campus == 'ONLINE') {
            return $teachers;
        }

        $campus = self::ONLINE_CAMPUS;

        $params = array($course->cou_number, $semester->session_key,
            $section->sec_number, $course->department, $semesterterm, $campus);

        $xmlteachers = $this->invoke($params);

        foreach ($xmlteachers->ROW as $xmlteacher) {

            $teachers[] = $this->format_teacher($xmlteacher);
        }

        return $teachers;
    }
}

class online_students extends online_student_format implements student_processor {

    public function students($semester, $course, $section) {
        $semesterterm = $this->encode_semester($semester->year, $semester->name);

        $campus = $semester->campus == 'ONLINE' ? self::ONLINE_CAMPUS : self::LAW_CAMPUS;

        $params = array($campus, $semesterterm, $course->department,
            $course->cou_number, $section->sec_number, $semester->session_key);

        $xmlstudents = $this->invoke($params);

        $students = array();
        foreach ($xmlstudents->ROW as $xmlstudent) {

            $students[] = $this->format_student($xmlstudent);
        }

        return $students;
    }
}

class online_student_data extends online_source {

    public function student_data($semester) {
        $semesterterm = $this->encode_semester($semester->year, $semester->name);

        $params = array($semesterterm);

        if ($semester->campus == 'ONLINE') {
            $params += array(1 => self::ONLINE_INST, 2 => self::ONLINE_CAMPUS);
        } else {
            $params += array(1 => self::LAW_INST, 2 => self::LAW_CAMPUS);
        }

        $xmldata = $this->invoke($params);

        $studentdata = array();

        foreach ($xmldata->ROW as $xmlstudentdata) {
            $studdata = new stdClass;

            $reg = trim((string) $xmlstudentdata->REGISTRATION_DATE);

            $studdata->user_year = (string) $xmlstudentdata->YEAR_CLASS;
            $studdata->user_college = (string) $xmlstudentdata->COLLEGE_CODE;
            $studdata->user_major = (string) $xmlstudentdata->CURRIC_CODE;
            $studdata->user_reg_status = $reg == 'null' ? null : $this->parse_date($reg);
            $studdata->user_keypadid = (string) $xmlstudentdata->KEYPAD_ID;
            $studdata->idnumber = trim((string)$xmlstudentdata->LSU_ID);

            $studentdata[$studdata->idnumber] = $studdata;
        }

        return $studentdata;
    }
}

class online_degree extends online_source {

    public function student_data($semester) {
        $term = $this->encode_semester($semester->year, $semester->name);

        $params = array($term);

        if ($semester->campus == 'ONLINE') {
            $params += array(
                1 => self::ONLINE_INST,
                2 => self::ONLINE_CAMPUS
            );
        } else {
            $params += array(
                1 => self::LAW_INST,
                2 => self::LAW_CAMPUS
            );
        }

        $xmlgrads = $this->invoke($params);

        $graduates = array();
        foreach ($xmlgrads->ROW as $xmlgrad) {
            $graduate = new stdClass;

            $graduate->idnumber = (string) $xmlgrad->LSU_ID;
            $graduate->user_degree = 'Y';

            $graduates[$graduate->idnumber] = $graduate;
        }

        return $graduates;
    }
}

class online_anonymous extends online_source {

    public function student_data($semester) {
        if ($semester->campus == 'ONLINE') {
            return array();
        }

        $term = $this->encode_semester($semester->year, $semester->name);

        $xmlnumbers = $this->invoke(array($term));

        $numbers = array();
        foreach ($xmlnumbers->ROW as $xmlnumber) {
            $number = new stdClass;

            $number->idnumber = (string) $xmlnumber->LSU_ID;
            $number->user_anonymous_number = (string) $xmlnumber->LAW_ANONYMOUS_NBR;

            $numbers[$number->idnumber] = $number;
        }

        return $numbers;
    }
}

class online_sports extends online_source {

    public function find_season($time) {
        $now = getdate($time);

        $june = 610;
        $dec = 1231;

        $cur = (int)(sprintf("%d%02d", $now['mon'], $now['mday']));

        if ($cur >= $june and $cur <= $dec) {
            return ($now['year']) . substr($now['year'] + 1, 2);
        } else {
            return ($now['year'] - 1) . substr($now['year'], 2);
        }
    }

    public function student_data($semester) {
        if ($semester->campus == 'LAW') {
            return array();
        }

        $now = time();

        $xmlinfos = $this->invoke(array($this->find_season($now)));

        $numbers = array();
        foreach ($xmlinfos->ROW as $xmlinfo) {
            $number = new stdClass;

            $number->idnumber = (string) $xmlinfo->LSU_ID;
            $number->user_sport1 = (string) $xmlinfo->SPORT_CODE_1;
            $number->user_sport2 = (string) $xmlinfo->SPORT_CODE_2;
            $number->user_sport3 = (string) $xmlinfo->SPORT_CODE_3;
            $number->user_sport4 = (string) $xmlinfo->SPORT_CODE_4;

            $numbers[$number->idnumber] = $number;
        }

        return $numbers;
    }
}