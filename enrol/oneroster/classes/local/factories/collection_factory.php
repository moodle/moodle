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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\factories;

use enrol_oneroster\local\interfaces\collection_factory as collection_factory_interface;
    // Entities which resemble an org.
use enrol_oneroster\local\collections\orgs as orgs_collection;
use enrol_oneroster\local\collections\schools as schools_collection;

    // Entities which resemble a class.
use enrol_oneroster\local\collections\classes as classes_collection;
use enrol_oneroster\local\collections\classes_for_course as classes_for_course_collection;
use enrol_oneroster\local\collections\classes_for_school as classes_for_school_collection;
use enrol_oneroster\local\collections\classes_for_term as classes_for_term_collection;
use enrol_oneroster\local\collections\classes_for_student as classes_for_student_collection;
use enrol_oneroster\local\collections\classes_for_teacher as classes_for_teacher_collection;

    // Entities which resemble a course.
use enrol_oneroster\local\collections\courses as courses_collection;
use enrol_oneroster\local\collections\courses_for_school as courses_for_school_collection;

    // Entities which resemble an academicSession.
use enrol_oneroster\local\collections\academic_sessions as academic_sessions_collection;
use enrol_oneroster\local\collections\terms as terms_collection;
use enrol_oneroster\local\collections\terms_for_school as terms_for_school_collection;
use enrol_oneroster\local\collections\grading_periods as grading_periods_collection;
use enrol_oneroster\local\collections\grading_periods_for_term as grading_periods_for_term_collection;

    // Entities which resemble a user.
use enrol_oneroster\local\collections\users as users_collection;
use enrol_oneroster\local\collections\students as students_collection;
use enrol_oneroster\local\collections\teachers as teachers_collection;

use enrol_oneroster\local\collections\students_for_class as students_for_class_collection;
use enrol_oneroster\local\collections\students_for_school as students_for_school_collection;
use enrol_oneroster\local\collections\students_for_class_in_school as students_for_class_in_school_collection;

use enrol_oneroster\local\collections\teachers_for_class as teachers_for_class_collection;
use enrol_oneroster\local\collections\teachers_for_school as teachers_for_school_collection;
use enrol_oneroster\local\collections\teachers_for_class_in_school as teachers_for_class_in_school_collection;

    // Entities which resemble an enrollment.
use enrol_oneroster\local\collections\enrollments as enrollments_collection;
use enrol_oneroster\local\collections\enrollments_for_school as enrollments_for_school_collection;
use enrol_oneroster\local\collections\enrollments_for_class_in_school as enrollments_for_class_in_school_collection;
use enrol_oneroster\local\entities\school as school_entity;
use enrol_oneroster\local\entities\course as course_entity;
use enrol_oneroster\local\entities\term as term_entity;
use enrol_oneroster\local\entities\class_entity;
use enrol_oneroster\local\entities\user as user_entity;
use enrol_oneroster\local\filter;

/**
 * One Roster Collection factory.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collection_factory extends abstract_factory implements collection_factory_interface {

    /**
     * Fetch a collection of organisations.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  orgs_collection
     */
    public function get_orgs(array $params = [], ?filter $filter = null, ?callable $recordfilter = null): orgs_collection {
        return new orgs_collection($this->container, $params, $filter, $recordfilter);
    }

    /**
     * Fetch a collection of schools.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  schools_collection
     */
    public function get_schools(array $params = [], ?filter $filter = null, ?callable $recordfilter = null): schools_collection {
        return new schools_collection($this->container, $params, $filter, $recordfilter);
    }

    /**
     * Fetch a collection of students for a school.
     *
     * The school id is automatically filled. Additional parameters can be supplied.
     *
     * @param   school_entity $school The school to fetch students for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  students_for_school_collection
     */
    public function get_students_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): students_for_school_collection {
        return new students_for_school_collection(
            $this->container,
            array_merge([
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of students for a class.
     *
     * The class id is automatically filled. Additional parameters can be supplied.
     *
     * @param   class_entity $class The class to fetch students for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  students_for_class_collection
     */
    public function get_students_for_class(
        class_entity $class,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): students_for_class_collection {
        return new students_for_class_collection(
            $this->container,
            array_merge([
                ':class_id' => $class->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of students for a class that is in a specific school.
     *
     * The class and school ids are automatically filled. Additional parameters can be supplied.
     *
     * @param   class_entity $class The class to fetch students for
     * @param   school_entity $school The school to fetch students for
     * @param   array $params The parameters to use when fetching the collection
     * @param   null|filter $filter The filter to use when fetching the collection
     * @param   null|callable $recordfilter Any subsequent filter to apply to the results
     * @return  students_for_class_in_school_collection
     */
    public function get_students_for_class_in_school(
        class_entity $class,
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): students_for_class_in_school_collection {
        return new students_for_class_in_school_collection(
            $this->container,
            array_merge([
                ':class_id' => $class->get('sourcedId'),
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of teachers for a school.
     *
     * The school id is automatically filled. Additional parameters can be supplied.
     *
     * @param   school_entity $school The school to fetch teachers for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  teachers_for_school_collection
     */
    public function get_teachers_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): teachers_for_school_collection {
        return new teachers_for_school_collection(
            $this->container,
            array_merge([
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of teachers for a class.
     *
     * The class id is automatically filled. Additional parameters can be supplied.
     *
     * @param   class_entity $class The class to fetch teachers for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  teachers_for_class_collection
     */
    public function get_teachers_for_class(
        class_entity $class,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): teachers_for_class_collection {
        return new teachers_for_class_collection(
            $this->container,
            array_merge([
                ':class_id' => $class->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of teachers for a class that is in a specific school.
     *
     * The class and school ids are automatically filled. Additional parameters can be supplied.
     *
     * @param   class_entity $class The class to fetch teachers for
     * @param   school_entity $school The school to fetch teachers for
     * @param   array $params The parameters to use when fetching the collection
     * @param   null|filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  teachers_for_class_in_school_collection
     */
    public function get_teachers_for_class_in_school(
        class_entity $class,
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): teachers_for_class_in_school_collection {
        return new teachers_for_class_in_school_collection(
            $this->container,
            array_merge([
                ':class_id' => $class->get('sourcedId'),
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of users.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  users_collection
     */
    public function get_users(array $params = [], ?filter $filter = null, ?callable $recordfilter = null): users_collection {
        return new users_collection($this->container, $params, $filter, $recordfilter);
    }

    /**
     * Fetch a collection of users for a school.
     *
     * The school id is automatically filled as a filter parameter.
     *
     * @param   school_entity $school The school to fetch courses for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  users_collection
     */
    public function get_users_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): users_collection {
        if ($filter === null) {
            $filter = new filter();
        }
        $filter->add_filter('orgs.sourcedId', $school->get('sourcedId'));

        return new users_collection(
            $this->container,
            $params,
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of students.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  students_collection
     */
    public function get_students(array $params = [], ?filter $filter = null, ?callable $recordfilter = null): students_collection {
        return new students_collection($this->container, $params, $filter, $recordfilter);
    }

    /**
     * Fetch a collection of teachers.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  teachers_collection
     */
    public function get_teachers(array $params = [], ?filter $filter = null, ?callable $recordfilter = null): teachers_collection {
        return new teachers_collection($this->container, $params, $filter, $recordfilter);
    }

    /**
     * Fetch a collection of courses.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  courses_for_school_collection
     */
    public function get_courses(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): courses_collection {
        return new courses_collection(
            $this->container,
            $params,
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of courses for a school.
     *
     * The school id is automatically filled. Additional parameters can be supplied.
     *
     * @param   school_entity $school The school to fetch courses for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  courses_for_school_collection
     */
    public function get_courses_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): courses_for_school_collection {
        return new courses_for_school_collection(
            $this->container,
            array_merge([
                ':id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of classes.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  classes_collection
     */
    public function get_classes(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_collection {
        return new classes_collection(
            $this->container,
            $params,
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of classes for a course.
     *
     * The course id is automatically filled. Additional parameters can be supplied.
     *
     * @param   course_entity $course The course to fetch classes for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  classes_for_course_collection
     */
    public function get_classes_for_course(
        course_entity $course,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_for_course_collection {
        return new classes_for_course_collection(
            $this->container,
            array_merge([
                ':course_id' => $course->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of classes for a school.
     *
     * The school id is automatically filled. Additional parameters can be supplied.
     *
     * @param   school_entity $school The school to fetch classes for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  classes_for_school_collection
     */
    public function get_classes_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_for_school_collection {
        return new classes_for_school_collection(
            $this->container,
            array_merge([
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of classes for a term.
     *
     * The term id is automatically filled. Additional parameters can be supplied.
     *
     * @param   term_entity $term The term to fetch classes for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  classes_for_term_collection
     */
    public function get_classes_for_term(
        term_entity $term,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_for_term_collection {
        return new classes_for_term_collection(
            $this->container,
            array_merge([
                ':term_id' => $term->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of classes for a student.
     *
     * The student id is automatically filled. Additional parameters can be supplied.
     *
     * @param   user_entity $student The student to fetch classes for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  classes_for_student_collection
     */
    public function get_classes_for_student(
        user_entity $student,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_for_student_collection {
        return new classes_for_student_collection(
            $this->container,
            array_merge([
                ':student_id' => $student->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of classes for a teacher.
     *
     * The teacher id is automatically filled. Additional parameters can be supplied.
     *
     * @param   user_entity $teacher The teacher to fetch classes for
     * @param   array $params The parameters to use when fetching the collection
     * @param   null|filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  classes_for_teacher_collection
     */
    public function get_classes_for_teacher(
        user_entity $teacher,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): classes_for_teacher_collection {
        return new classes_for_teacher_collection(
            $this->container,
            array_merge([
                ':teacher_id' => $teacher->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of academicSessions.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  academic_sessions_collection
     */
    public function get_academic_sessions(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): academic_sessions_collection {
        return new academic_sessions_collection(
            $this->container,
            $params,
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of terms.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  terms_collection
     */
    public function get_terms(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): terms_collection {
        return new terms_collection(
            $this->container,
            $params,
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of terms for a school.
     *
     * The school id is automatically filled. Additional parameters can be supplied.
     *
     * @param   school_entity $school The school to fetch terms for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  terms_for_school_collection
     */
    public function get_terms_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): terms_for_school_collection {
        return new terms_for_school_collection(
            $this->container,
            array_merge([
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of gradingPeriods.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  grading_periods_collection
     */
    public function get_grading_periods(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): grading_periods_collection {
        return new grading_periods_collection(
            $this->container,
            $params,
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of gradingPeriods for a school.
     *
     * The school id is automatically filled. Additional parameters can be supplied.
     *
     * @param   school_entity $school The school to fetch grading_periods for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  grading_periods_for_school_collection
     */
    public function get_grading_periods_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): grading_periods_for_school_collection {
        return new grading_periods_for_school_collection(
            $this->container,
            array_merge([
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of gradingPeriods for a term.
     *
     * The term id is automatically filled. Additional parameters can be supplied.
     *
     * @param   term_entity $term The term to fetch grading_periods for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  grading_periods_for_term_collection
     */
    public function get_grading_periods_for_term(
        term_entity $term,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): grading_periods_for_term_collection {
        return new grading_periods_for_term_collection(
            $this->container,
            array_merge([
                ':term_id' => $term->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of enrollments.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  enrollments_collection
     */
    public function get_enrollments(
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): enrollments_collection {
        return new enrollments_collection($this->container, $params, $filter, $recordfilter);
    }

    /**
     * Fetch a collection of enrollments for a school.
     *
     * The school id is automatically filled. Additional parameters can be supplied.
     *
     * @param   school_entity $school The school to fetch enrollments for
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  enrollments_for_school_collection
     */
    public function get_enrollments_for_school(
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): enrollments_for_school_collection {
        return new enrollments_for_school_collection(
            $this->container,
            array_merge([
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }

    /**
     * Fetch a collection of enrollments for a class that is in a specific school.
     *
     * The class and school ids are automatically filled. Additional parameters can be supplied.
     *
     * @param   class_entity $class The class to fetch enrollments for
     * @param   school_entity $school The school to fetch enrollments for
     * @param   array $params The parameters to use when fetching the collection
     * @param   null|filter $filter The filter to use when fetching the collection
     * @param   null|callable $recordfilter Any subsequent filter to apply to the results
     * @return  enrollments_for_class_in_school_collection
     */
    public function get_enrollments_for_class_in_school(
        class_entity $class,
        school_entity $school,
        array $params = [],
        ?filter $filter = null,
        ?callable $recordfilter = null
    ): enrollments_for_class_in_school_collection {
        return new enrollments_for_class_in_school_collection(
            $this->container,
            array_merge([
                ':class_id' => $class->get('sourcedId'),
                ':school_id' => $school->get('sourcedId'),
            ], $params),
            $filter,
            $recordfilter
        );
    }
}
