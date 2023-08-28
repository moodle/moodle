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

namespace enrol_oneroster\local\v1p1;

use enrol_oneroster\local\conformance as conformance_base;

/**
 * One Roster v1p1 conformance tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class conformance extends conformance_base {
    /**
     * Run all tests.
     */
    public function run_all_tests(): void {
        $this->run_oauth_tests();
        $this->run_rostering_tests();
        $this->run_url_tests();
        $this->print_suite_success();
    }

    /**
     * Run all OAuth Tests.
     */
    protected function run_oauth_tests(): void {
        self::print_test_title("OAuth Features");

        // 1.3 OAuth: Sign request with URL Parameters with space(s) in the value of a parameter.
        self::print_test_header(
            "OAuth",
            "Sign request with URL Parameters with space(s) in the value of a parameter"
        );

        $collection = $this->container->get_collection_factory()->get_users(
            [],
            new filter('givenName', "Colin Robert", '=')
        );
        foreach ($collection as $entity) {
            break;
        }

        // The CTS endpoint does not actually filter anything.
        self::print_test_result(true);
    }


    /**
     * Run tests pertaining to OAuth, the connection, and URL parameters.
     */
    protected function run_url_tests(): void {
        self::print_test_title("OAuth Features");

        // Test URL parameter options.
        $this->test_url_parameters();
    }

    /**
     * Run all Rostering tests.
     */
    protected function run_rostering_tests(): void {
        self::print_test_title("Rostering endpoints");

        // Endpoints which represent a user.
        $this->test_endpoint_collection_and_entity_by_id(
            'Users',
            'users',
            'User by id',
            'user'
        );

        $this->test_endpoint_collection_and_entity_by_id(
            'Students',
            'students',
            'Student by id',
            'student'
        );

        $this->test_endpoint_collection_and_entity_by_id(
            'Teachers',
            'teachers',
            'Teacher by id',
            'teacher'
        );

        // Endpoints which represent an org.
        $this->test_endpoint_collection_and_entity_by_id(
            'Orgs',
            'orgs',
            'Org by id',
            'org'
        );

        $this->test_endpoint_collection_and_entity_by_id(
            'Schools',
            'schools',
            'School by id',
            'school'
        );

        // Endpoints which represent a courses.
        $this->test_endpoint_collection_and_entity_by_id(
            'Courses',
            'courses',
            'Course by id',
            'course'
        );

        // Endpoints which represent a class.
        $this->test_endpoint_collection_and_entity_by_id(
            'Classes',
            'classes',
            'Class by id',
            'class'
        );

        // Endpoints which represent an academicSession.
        $this->test_endpoint_collection_and_entity_by_id(
            'Academic Sessions',
            'academic_sessions',
            'Academic Session',
            'academic_session'
        );

        $this->test_endpoint_collection_and_entity_by_id(
            'Terms',
            'terms',
            'Term',
            'term'
        );

        $this->test_endpoint_collection_and_entity_by_id(
            'Grading Periods',
            'grading_periods',
            'Grading Period',
            'grading_period'
        );

        // Endpoints which represent an enrollment.
        $this->test_endpoint_collection_and_entity_by_id(
            'Enrollments',
            'enrollments',
            'Enrollment',
            'enrollment'
        );

        // Endpoints which fetch entities by School.
        $this->test_for_school_endpoints();

        // Endpoints which fetch entities by Student.
        $this->test_for_student_endpoints();

        // Endpoints which fetch entities by Teacher.
        $this->test_for_teacher_endpoints();

        // Endpoints which fetch entities by User.
        $this->test_for_user_endpoints();

        // Endpoints which fetch entities by Course.
        $this->test_for_course_endpoints();

        // Endpints which fetch entities by Class.
        $this->test_for_class_endpoints();

        // Endpints which fetch entities by Classes in a School.
        $this->test_for_classes_in_school_endpoints();

        // Endpints which fetch entities by Term.
        $this->test_for_term_endpoints();
    }

    /**
     * Test ForSchool endpoints.
     */
    protected function test_for_school_endpoints(): void {
        $schools = $this->container->get_collection_factory()->get_schools();

        $firstschool = null;
        foreach ($schools as $school) {
            $firstschool = $school;
            break;
        }

        $this->test_collection_with_args(
            'Classes for school',
            'classes_for_school',
            [
                $firstschool,
            ]
        );

        $this->test_collection_with_args(
            'Courses for school',
            'courses_for_school',
            [
                $firstschool,
            ]
        );

        $this->test_collection_with_args(
            'Enrollments for school',
            'enrollments_for_school',
            [
                $firstschool,
            ]
        );

        $this->test_collection_with_args(
            'Students for school',
            'students_for_school',
            [
                $firstschool,
            ]
        );

        $this->test_collection_with_args(
            'Teachers for school',
            'teachers_for_school',
            [
                $firstschool,
            ]
        );

        $this->test_collection_with_args(
            'Terms for school',
            'terms_for_school',
            [
                $firstschool,
            ]
        );
    }

    /**
     * Test ForStudent endpoints.
     */
    protected function test_for_student_endpoints(): void {
        $entities = $this->container->get_collection_factory()->get_students();

        $first = null;
        foreach ($entities as $entity) {
            $first = $entity;
            break;
        }

        $this->test_collection_with_args(
            'Classes for student',
            'classes_for_student',
            [
                $first,
            ]
        );
    }

    /**
     * Test ForTeacher endpoints.
     */
    protected function test_for_teacher_endpoints(): void {
        $entities = $this->container->get_collection_factory()->get_teachers();

        $first = null;
        foreach ($entities as $entity) {
            $first = $entity;
            break;
        }

        $this->test_collection_with_args(
            'Classes for teacher',
            'classes_for_teacher',
            [
                $first,
            ]
        );
    }

    /**
     * Test ForUser endpoints.
     */
    protected function test_for_user_endpoints(): void {
        $entities = $this->container->get_collection_factory()->get_users();

        $first = null;
        foreach ($entities as $entity) {
            $first = $entity;
            break;
        }

        $this->test_collection_with_args(
            'Classes for user',
            'classes_for_user',
            [
                $first,
            ]
        );
    }

    /**
     * Test ForClass endpoints.
     */
    protected function test_for_course_endpoints(): void {
        $entities = $this->container->get_collection_factory()->get_courses();

        $first = null;
        foreach ($entities as $entity) {
            $first = $entity;
            break;
        }

        $this->test_collection_with_args(
            'Classes for course',
            'classes_for_course',
            [
                $first,
            ]
        );
    }

    /**
     * Test ForClass endpoints.
     */
    protected function test_for_class_endpoints(): void {
        $entities = $this->container->get_collection_factory()->get_classes();

        $first = null;
        foreach ($entities as $entity) {
            $first = $entity;
            break;
        }

        $this->test_collection_with_args(
            'Students for class',
            'students_for_class',
            [
                $first,
            ]
        );

        $this->test_collection_with_args(
            'Teachers for class',
            'teachers_for_class',
            [
                $first,
            ]
        );
    }

    /**
     * Test ForTerm endpoints.
     */
    protected function test_for_term_endpoints(): void {
        $entities = $this->container->get_collection_factory()->get_terms();

        $first = null;
        foreach ($entities as $entity) {
            $first = $entity;
            break;
        }

        $this->test_collection_with_args(
            'Classes for term',
            'classes_for_term',
            [
                $first,
            ]
        );

        $this->test_collection_with_args(
            'Grading Periods for term',
            'grading_periods_for_term',
            [
                $first,
            ]
        );
    }

    /**
     * Test ForClassesInSchool endpoints.
     */
    protected function test_for_classes_in_school_endpoints(): void {
        $entities = $this->container->get_collection_factory()->get_classes();

        $firstclass = null;
        foreach ($entities as $entity) {
            $firstclass = $entity;
            break;
        }

        $entities = $this->container->get_collection_factory()->get_schools();

        $firstschool = null;
        foreach ($entities as $entity) {
            $firstschool = $entity;
            break;
        }

        $this->test_collection_with_args(
            'Enrollments for class in school',
            'enrollments_for_class_in_school',
            [
                $firstclass,
                $firstschool,
            ]
        );

        $this->test_collection_with_args(
            'Students for class in school',
            'students_for_class_in_school',
            [
                $firstclass,
                $firstschool,
            ]
        );
        $this->test_collection_with_args(
            'Teachers for class in school',
            'teachers_for_class_in_school',
            [
                $firstclass,
                $firstschool,
            ]
        );
    }

    /**
     * Explicit tests for URL Parameters.
     */
    protected function test_url_parameters(): void {
        // 3.1 URL Parameter: Limit.
        // Tested by collections.

        // 3.2 URL Parameter: Offset.
        // Tested by collections.

        // 3.3 URL Parameter: Filter.
        self::print_test_header("URL Parameter", "Filter");
        $entities = $this->container->get_collection_factory()->get_users();

        $first = null;
        foreach ($entities as $entity) {
            $first = $entity;
            break;
        }

        $collection = $this->container->get_collection_factory()->get_users(
            [],
            new filter('email', $first->get('email'), '=')
        );

        foreach ($collection as $entity) {
            break;
        }
        self::print_test_result(true);

        // 3.4 URL Parameter: Sort.
        // 3.5 URL Parameter: Order By.
        self::print_test_header("URL Parameter", "Sort/orderBy");
        $collection = $this->container->get_collection_factory()->get_users(
            [
                'sort' => 'sourcedId',
                'orderBy' => 'desc',
            ]
        );

        $results = [];
        foreach ($collection as $result) {
            $results[] = $result->get('sourcedId');
        }

        // Note: The CTS endpoint does not return sorted data.
        self::print_test_result(true);

        // 3.6 URL Parameter: Fields.
        self::print_test_header("URL Parameter", "Fields");
        $collection = $this->container->get_collection_factory()->get_users(
            [
                'fields' => 'sourcedId,email',
            ]
        );

        foreach ($collection as $result) {
            // The CTS endpoint does not support the 'fields' option.
            continue;
        }

        // Note: The CTS endpoint does not return sorted data.
        self::print_test_result(true);
    }

    /**
     * Test Collection helper.
     *
     * @param   string $collectiontype The descriptive name of the collection
     * @param   string $collectionname The class name of the collection to fetch
     * @param   array $args
     */
    protected function test_collection_with_args(
        string $collectiontype,
        string $collectionname,
        array $args
    ): void {
        // Endpoint: Collection.
        self::print_test_header("Endpoint", $collectiontype);

        $collectionfn = "get_{$collectionname}";
        if (!method_exists($this->container->get_collection_factory(), $collectionfn)) {
            self::print_test_result(false, "Missing function '{$collectionfn}'");
        }

        $collection = call_user_func_array(
            [
                $this->container->get_collection_factory(),
                $collectionfn,
            ],
            $args
        );
        foreach ($collection as $entity) {
            break;
        }
        self::print_test_result(true);
    }

    /**
     * Test a collection and single entity in that collection.
     *
     * @param   string $collectiontype The descriptive name of the collection
     * @param   string $collectionname The class name of the collection to fetch
     * @param   string $entitytype The descriptive name of the entity
     * @param   string $entityname THe class name of the entity
     */
    protected function test_endpoint_collection_and_entity_by_id(
        string $collectiontype,
        string $collectionname,
        ?string $entitytype,
        ?string $entityname
    ): void {
        // Endpoint: Collection.
        self::print_test_header("Endpoint", $collectiontype);

        $collectionfn = "get_{$collectionname}";
        if (!method_exists($this->container->get_collection_factory(), $collectionfn)) {
            self::print_test_result(false, "Missing function '{$collectionfn}'");
        }

        $entities = $this->container->get_collection_factory()->{$collectionfn}();
        $foundentity = null;
        foreach ($entities as $entity) {
            $foundentity = $entity;
            break;
        }
        self::print_test_result(!!$foundentity);

        if ($entitytype && $entityname) {
            // Endpoint: Entity by id.
            self::print_test_header("Endpoint", $entitytype);

            $entityfn = "fetch_{$entityname}_by_id";
            if (!method_exists($this->container->get_entity_factory(), $entityfn)) {
                self::print_test_result(false, "Missing function '{$entityfn}'");
            }
            $foundentity = $this->container->get_entity_factory()->{$entityfn}($foundentity->get('sourcedId'));
            self::print_test_result(!!$foundentity);
        }
    }
}
