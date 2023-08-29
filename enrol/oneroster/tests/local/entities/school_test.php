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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\entities;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/entity_testcase.php');
use enrol_oneroster\local\entities\entity_testcase;

use enrol_oneroster\local\interfaces\coursecat_representation;
use enrol_oneroster\local\collections\courses_for_school as courses_for_school_collection;
use enrol_oneroster\local\collections\terms_for_school as terms_for_school_collection;
use enrol_oneroster\local\collections\classes_for_school as classes_for_school_collection;
use enrol_oneroster\local\collections\users as users_collection;
use enrol_oneroster\local\collections\enrollments_for_school as enrollments_for_school_collection;
use enrol_oneroster\local\filter;
use stdClass;
use OutOfRangeException;

/**
 * One Roster tests for the school entity.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  enrol_oneroster\local\entity
 * @covers  enrol_oneroster\local\entities\school
 */
class school_testcase extends entity_testcase {

    /**
     * Test the properties of the entity.
     */
    public function test_entity(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        // A School is an Organisation.
        $this->assertInstanceOf(org::class, $entity);

        // An Organisation can represent a Moodle Course Category.
        $this->assertInstanceOf(coursecat_representation::class, $entity);
    }

    /**
     * Ensure that the get function calls the web service correctly.
     */
    public function test_get(): void {
        $container = $this->get_mocked_container();

        $rostering = $this->mock_rostering_endpoint($container, ['execute']);
        $rostering
            ->expects($this->once())
            ->method('execute')
            ->willReturn((object) [
                'org' => (object) [
                    'sourcedId' => 'C666',
                    'name' => 'Sister Assumpta\'s RC School of Discipline',
                ],
            ]);
        $container->method('get_rostering_endpoint')->willReturn($rostering);

        $entity = new school($container, '12345');

        // The get_data() function should contain the data.
        $data = $entity->get_data();
        $this->assertIsObject($data);
        $this->assertEquals('C666', $data->sourcedId);
        $this->assertEquals('Sister Assumpta\'s RC School of Discipline', $data->name);

        // And it can be retrieved via `get()` without incurring another fetch.
        $this->assertEquals('C666', $entity->get('sourcedId'));
        $this->assertEquals('Sister Assumpta\'s RC School of Discipline', $entity->get('name'));

        // Non-existent objects return null.
        $this->assertNull($entity->get('fake'));
    }

    /**
     * Ensure that a call to fetch courses for a school hands off to the correct collection factory calls.
     */
    public function test_get_courses(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $coursesforschool = new courses_for_school_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_courses_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_courses_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo([]),
                $this->equalTo(null),
                $this->equalTo(null)
            )
            ->willReturn($coursesforschool);

        $courses = $entity->get_courses();
        $this->assertInstanceOf(courses_for_school_collection::class, $courses);
        $this->assertEquals($coursesforschool, $courses);
    }

    /**
     * Ensure that a call to fetch courses for a school hands off to the correct collection factory calls.
     */
    public function test_get_courses_with_args(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $params = ['sort' => 'sourcedId'];
        $filter = $this->mock_filter();
        $latefilter = function() {
        };
        $coursesforschool = new courses_for_school_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_courses_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_courses_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo($params),
                $this->equalTo($filter),
                $this->equalTo($latefilter)
            )
            ->willReturn($coursesforschool);

        $courses = $entity->get_courses($params, $filter, $latefilter);
        $this->assertInstanceOf(courses_for_school_collection::class, $courses);
        $this->assertEquals($coursesforschool, $courses);
    }

    /**
     * Ensure that a call to fetch terms for a school hands off to the correct collection factory calls.
     */
    public function test_get_terms(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $termsforschool = new terms_for_school_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_terms_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_terms_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo([]),
                $this->equalTo(null),
                $this->equalTo(null)
            )
            ->willReturn($termsforschool);

        $terms = $entity->get_terms();
        $this->assertInstanceOf(terms_for_school_collection::class, $terms);
        $this->assertEquals($termsforschool, $terms);
    }

    /**
     * Ensure that a call to fetch terms for a school hands off to the correct collection factory calls.
     */
    public function test_get_terms_with_args(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $params = ['sort' => 'sourcedId'];
        $filter = $this->mock_filter();
        $latefilter = function() {
        };
        $termsforschool = new terms_for_school_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_terms_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_terms_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo($params),
                $this->equalTo($filter),
                $this->equalTo($latefilter)
            )
            ->willReturn($termsforschool);

        $terms = $entity->get_terms($params, $filter, $latefilter);
        $this->assertInstanceOf(terms_for_school_collection::class, $terms);
        $this->assertEquals($termsforschool, $terms);
    }

    /**
     * Ensure that a call to fetch classes for a school hands off to the correct collection factory calls.
     */
    public function test_get_classes(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $classesforschool = new classes_for_school_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_classes_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_classes_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo([]),
                $this->equalTo(null),
                $this->equalTo(null)
            )
            ->willReturn($classesforschool);

        $classes = $entity->get_classes();
        $this->assertInstanceOf(classes_for_school_collection::class, $classes);
        $this->assertEquals($classesforschool, $classes);
    }

    /**
     * Ensure that a call to fetch classes for a school hands off to the correct collection factory calls.
     */
    public function test_get_classes_with_args(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $classesforschool = new classes_for_school_collection($container);

        $params = ['sort' => 'sourcedId'];
        $filter = $this->mock_filter();
        $latefilter = function() {
        };
        $collectionfactory = $this->mock_collection_factory($container, ['get_classes_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_classes_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo($params),
                $this->equalTo($filter),
                $this->equalTo($latefilter)
            )
            ->willReturn($classesforschool);

        $classes = $entity->get_classes($params, $filter, $latefilter);
        $this->assertInstanceOf(classes_for_school_collection::class, $classes);
        $this->assertEquals($classesforschool, $classes);
    }

    /**
     * Ensure that a call to fetch users for a school hands off to the correct collection factory calls.
     */
    public function test_get_users(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $usersforschool = new users_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_users_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_users_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo([]),
                $this->equalTo(null),
                $this->equalTo(null)
            )
            ->willReturn($usersforschool);

        $users = $entity->get_users();
        $this->assertInstanceOf(users_collection::class, $users);
        $this->assertEquals($usersforschool, $users);
    }

    /**
     * Ensure that a call to fetch users for a school hands off to the correct collection factory calls.
     */
    public function test_get_users_with_args(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $params = ['sort' => 'sourcedId'];
        $filter = $this->mock_filter();
        $latefilter = function() {
        };
        $usersforschool = new users_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_users_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_users_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo($params),
                $this->equalTo($filter),
                $this->equalTo($latefilter)
            )
            ->willReturn($usersforschool);

        $users = $entity->get_users($params, $filter, $latefilter);
        $this->assertInstanceOf(users_collection::class, $users);
        $this->assertEquals($usersforschool, $users);
    }

    /**
     * Ensure that a call to fetch enrollments for a school hands off to the correct collection factory calls.
     */
    public function test_get_enrollments(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $enrollmentsforschool = new enrollments_for_school_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_enrollments_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_enrollments_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo([]),
                $this->equalTo(null),
                $this->equalTo(null)
            )
            ->willReturn($enrollmentsforschool);

        $enrollments = $entity->get_enrollments();
        $this->assertInstanceOf(enrollments_for_school_collection::class, $enrollments);
        $this->assertEquals($enrollmentsforschool, $enrollments);
    }

    /**
     * Ensure that a call to fetch enrollments for a school hands off to the correct collection factory calls.
     */
    public function test_get_enrollments_with_args(): void {
        $container = $this->get_mocked_container();

        $entity = new school($container, '12345');

        $params = ['sort' => 'sourcedId'];
        $filter = $this->mock_filter();
        $latefilter = function() {
        };
        $enrollmentsforschool = new enrollments_for_school_collection($container);

        $collectionfactory = $this->mock_collection_factory($container, ['get_enrollments_for_school']);
        $collectionfactory
            ->expects($this->once())
            ->method('get_enrollments_for_school')
            ->with(
                $this->equalTo($entity),
                $this->equalTo($params),
                $this->equalTo($filter),
                $this->equalTo($latefilter)
            )
            ->willReturn($enrollmentsforschool);

        $enrollments = $entity->get_enrollments($params, $filter, $latefilter);
        $this->assertInstanceOf(enrollments_for_school_collection::class, $enrollments);
        $this->assertEquals($enrollmentsforschool, $enrollments);
    }
}
