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

use cache;
use coding_exception;
use enrol_oneroster\local\entity;
use enrol_oneroster\local\endpoint;
use enrol_oneroster\local\exceptions\not_found as not_found_exception;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
    // Entities which resemble an org.
use enrol_oneroster\local\entities\org as org_entity;
use enrol_oneroster\local\entities\school as school_entity;

    // Entities which resemble a class.
use enrol_oneroster\local\entities\class_entity;

    // Entities which resemble a course.
use enrol_oneroster\local\entities\course as course_entity;

    // Entities which resemble an academicSession.
use enrol_oneroster\local\entities\academic_session as academic_session_entity;
use enrol_oneroster\local\entities\term as term_entity;
use enrol_oneroster\local\entities\grading_period as grading_period_entity;

    // Entities which resemble a user.
use enrol_oneroster\local\entities\user as user_entity;
use enrol_oneroster\local\entities\student as student_entity;
use enrol_oneroster\local\entities\teacher as teacher_entity;

    // Entities which resemble an enrollment.
use enrol_oneroster\local\entities\enrollment as enrollment_entity;
use stdClass;

/**
 * One Roster generic entity factory.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entity_factory extends abstract_factory implements entity_factory_interface {
    /**
     * Fetch an entity from its cache.
     *
     * Note: At this time filters preclude the use of the cache but this may change.
     *
     * @param   string $entitytype The type of entity
     * @param   string $id The sourcedId of the entity
     * @param   filter|null $filter Any filter to apply
     * @return  stdClass|null The data stored in the cache
     */
    protected function fetch_from_cache(string $entitytype, string $id, ?filter $filter = null): ?stdClass {
        if ($filter !== null) {
            // It is not possible to use the cache when a filter is applied.
            return null;
        }

        $cache = $this->get_cache_for_type($entitytype);

        $data = $cache->get($id);

        if ($data) {
            return $data;
        } else {
            // Nothing found in the cache.
            return null;
        }
    }

    /**
     * Store the data for an entity in the cache.
     *
     * @param   string $entitytype The type of entity
     * @param   string $id The sourcedId of the entity
     * @param   stdClass $data The data to store
     */
    protected function store_record_in_cache(string $entitytype, string $id, stdClass $data): void {
        $cache = $this->get_cache_for_type($entitytype);

        if (!$cache->has($id)) {
            $cache->set($id, $data);
        }
    }

    /**
     * Fetch the cache instance fo the supplied type of entity.
     *
     * @param   string $entitytype The type of entity
     * @return  cache
     */
    protected function get_cache_for_type(string $entitytype): cache {
        switch($entitytype) {
            case 'org':
                return $this->container->get_cache_factory()->get_org_cache();
            case 'academic_session':
                return $this->container->get_cache_factory()->get_academic_session_cache();
            case 'course':
                return $this->container->get_cache_factory()->get_course_cache();
            case 'class':
                return $this->container->get_cache_factory()->get_class_cache();
            case 'user':
                return $this->container->get_cache_factory()->get_user_cache();
            case 'enrolment':
            case 'enrollment':
                return $this->container->get_cache_factory()->get_enrolment_cache();
            default:
                throw new coding_exception("Unknown cache type '{$entitytype}'");
        }
    }

    /**
     * Get an Organisation record from an organisation dtype record.
     *
     * @param   stdClass $data The data to create a record from
     * @return  org_entity An org entity, or subtype of an org entity.
     */
    public function get_org_from_result(stdClass $data): org_entity {
        switch ($data->type) {
            case 'department':
            case 'school':
                $this->store_record_in_cache('org', $data->sourcedId, $data);
                return new school_entity($this->container, $data->sourcedId, $data);
                break;
            case 'national':
            case 'state':
            case 'local':
            case 'district':
                $this->store_record_in_cache('org', $data->sourcedId, $data);
                return new org_entity($this->container, $data->sourcedId, $data);
                break;
            default:
                throw new \coding_exception("Unknown entity type '{$data->type}'");
        }
    }

    /**
     * Fetch an organisation, or organisation-like entity by the sourcedId.
     *
     * @param   string $id
     * @return  org_entity
     */
    public function fetch_org_by_id(string $id): ?org_entity {
        $data = $this->fetch_from_cache('org', $id);

        if (!$data) {
            $data = org_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_org_from_result($data);
    }

    /**
     * Fetch a school, or school-like entity by the sourcedId.
     *
     * @param   string $id
     * @return  school_entity
     */
    public function fetch_school_by_id(string $id): ?school_entity {
        $data = $this->fetch_from_cache('org', $id);

        if (!$data) {
            $data = school_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_org_from_result($data);
    }

    /**
     * Get the class for the supplied dataset.
     *
     * @param   stdClass $data
     * @return  class_entity
     */
    public function get_class_from_result(stdClass $data): class_entity {
        $this->store_record_in_cache('class', $data->sourcedId, $data);
        return new class_entity($this->container, $data->sourcedId, $data);
    }

    /**
     * Fetch a class by sourcedId.
     *
     * @param   string $id
     * @return  class_entity
     */
    public function fetch_class_by_id(string $id): ?class_entity {
        $data = $this->fetch_from_cache('class', $id);

        if (!$data) {
            $data = class_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_class_from_result($data);
    }

    /**
     * Get the course for the supplied dataset.
     *
     * @param   stdClass $data
     * @return  course_entity
     */
    public function get_course_from_result(stdClass $data): course_entity {
        $this->store_record_in_cache('course', $data->sourcedId, $data);
        return new course_entity($this->container, $data->sourcedId, $data);
    }

    /**
     * Fetch a course by sourcedId.
     *
     * @param   string $id
     * @return  course_entity
     */
    public function fetch_course_by_id(string $id): ?course_entity {
        $data = $this->fetch_from_cache('course', $id);

        if (!$data) {
            $data = course_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_course_from_result($data);
    }

    /**
     * Get an academic session entity from an academicSession dtype record.
     *
     * @param   stdClass $data The data to create a record from
     * @return  academic_session_entity An academicSession entity, or subtype of an academicSession entity.
     */
    public function get_academic_session_from_result(stdClass $data): academic_session_entity {
        switch ($data->type) {
            case 'semester':
            case 'term':
                $this->store_record_in_cache('academic_session', $data->sourcedId, $data);
                return new term_entity($this->container, $data->sourcedId, $data);
            case 'gradingPeriod':
                $this->store_record_in_cache('academic_session', $data->sourcedId, $data);
                return new grading_period_entity($this->container, $data->sourcedId, $data);
            case 'academicSession':
            case 'schoolYear':
                $this->store_record_in_cache('academic_session', $data->sourcedId, $data);
                return new academic_session_entity($this->container, $data->sourcedId, $data);
            default:
                throw new \coding_exception("Unknown entity type '{$data->type}'");
        }
    }

    /**
     * Fetch a academic_session by sourcedId.
     *
     * @param   string $id
     * @return  academic_session_entity
     */
    public function fetch_academic_session_by_id(string $id): ?academic_session_entity {
        $data = $this->fetch_from_cache('academic_session', $id);

        if (!$data) {
            $data = academic_session_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_academic_session_from_result($data);
    }

    /**
     * Fetch a term by sourcedId.
     *
     * @param   string $id
     * @return  term_entity
     */
    public function fetch_term_by_id(string $id): ?term_entity {
        $data = $this->fetch_from_cache('academic_session', $id);

        if (!$data) {
            $data = term_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_academic_session_from_result($data);
    }

    /**
     * Fetch a grading_period by sourcedId.
     *
     * @param   string $id
     * @return  grading_period_entity
     */
    public function fetch_grading_period_by_id(string $id): ?grading_period_entity {
        $data = $this->fetch_from_cache('academic_session', $id);

        if (!$data) {
            $data = grading_period_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_academic_session_from_result($data);
    }

    /**
     * Get the user for the supplied dataset.
     *
     * @param   stdClass $data
     * @return  user_entity
     */
    public function get_user_from_result(stdClass $data): user_entity {
        $this->store_record_in_cache('user', $data->sourcedId, $data);
        return new user_entity($this->container, $data->sourcedId, $data);
    }

    /**
     * Fetch a user by sourcedId.
     *
     * @param   string $id
     * @return  user_entity
     */
    public function fetch_user_by_id(string $id): ?user_entity {
        $data = $this->fetch_from_cache('user', $id);

        if (!$data) {
            try {
                $data = user_entity::fetch_data(
                    $this->container,
                    [
                        ':id' => $id,
                    ]
                );
            } catch (not_found_exception $e) {
                return null;
            }
        }

        if ($data === null) {
            return null;
        }

        return $this->get_user_from_result($data);
    }

    /**
     * Fetch a student by sourcedId.
     *
     * @param   string $id
     * @return  user_entity
     */
    public function fetch_student_by_id(string $id): ?user_entity {
        $data = $this->fetch_from_cache('user', $id);

        if (!$data) {
            $data = student_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_user_from_result($data);
    }

    /**
     * Fetch a teacher by sourcedId.
     *
     * @param   string $id
     * @return  user_entity
     */
    public function fetch_teacher_by_id(string $id): ?user_entity {
        $data = $this->fetch_from_cache('user', $id);

        if (!$data) {
            $data = teacher_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_user_from_result($data);
    }

    /**
     * Get the enrollment for the supplied dataset.
     *
     * @param   stdClass $data
     * @return  enrollment_entity
     */
    public function get_enrollment_from_result(stdClass $data): enrollment_entity {
        $this->store_record_in_cache('enrollment', $data->sourcedId, $data);
        return new enrollment_entity($this->container, $data->sourcedId, $data);
    }

    /**
     * Fetch a enrollment by sourcedId.
     *
     * @param   string $id
     * @return  enrollment_entity
     */
    public function fetch_enrollment_by_id(string $id): ?enrollment_entity {
        $data = $this->fetch_from_cache('enrollment', $id);

        if (!$data) {
            $data = enrollment_entity::fetch_data(
                $this->container,
                [
                    ':id' => $id,
                ]
            );
        }

        if ($data === null) {
            return null;
        }

        return $this->get_enrollment_from_result($data);
    }
}
