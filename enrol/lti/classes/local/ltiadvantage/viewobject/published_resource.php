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

namespace enrol_lti\local\ltiadvantage\viewobject;

/**
 * The class published_resource, instances of which represent a specific VIEW of a published resource.
 *
 * This class performs no validation and is only meant to be used as a slice of the existing data for use in the
 * content selection flow.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class published_resource {
    /** @var string the name of this resource. */
    private $name;

    /** @var string full name of the course to which this published resource belongs. */
    private $coursefullname;

    /** @var int id of the course to which this published resource belongs. */
    private $courseid;

    /** @var int the context id of the resource */
    private $contextid;

    /** @var int id of the enrol_lti_tools instance (i.e. the id of the 'published resource'). */
    private $id;

    /** @var string a v4 uuid identifier for this published resource. */
    private $uuid;

    /** @var bool whether or not this resource supports grades. */
    private $supportsgrades;

    /** @var float the max grade or null if not a graded resource. */
    private $grademax;

    /** @var bool whether or not this resource is itself a course. */
    private $iscourse;

    /**
     * The published_resource constructor.
     *
     * @param string $name the name of this resource.
     * @param string $coursefullname full name of the course to which this published resource belongs.
     * @param int $courseid id of the course to which this published resource belongs.
     * @param int $contextid id of the context.
     * @param int $id id of the enrol_lti_tools instance (i.e. the id of the 'published resource').
     * @param string $uuid a v4 uuid identifier for this published resource.
     * @param bool $supportsgrades whether or not this resource supports grades.
     * @param float|null $grademax the max grade or null if this is not a graded resource.
     * @param bool $iscourse whether or not this resource is itself a course.
     */
    public function __construct(string $name, string $coursefullname, int $courseid, int $contextid, int $id,
            string $uuid, bool $supportsgrades, ?float $grademax, bool $iscourse) {

        $this->name = $name;
        $this->coursefullname = $coursefullname;
        $this->courseid = $courseid;
        $this->contextid = $contextid;
        $this->id = $id;
        $this->uuid = $uuid;
        $this->supportsgrades = $supportsgrades;
        $this->grademax = $grademax;
        $this->iscourse = $iscourse;
    }

    /**
     * Get the name of this published resource.
     *
     * @return string the localised name.
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get the full name of the course owning this published resource.
     *
     * @return string the localised course full name.
     */
    public function get_coursefullname(): string {
        return $this->coursefullname;
    }

    /**
     * Get the id of the course owning this published resource.
     *
     * @return int the course id.
     */
    public function get_courseid(): int {
        return $this->courseid;
    }

    /**
     * Get the id of the context for this published resource.
     *
     * @return int the context id.
     */
    public function get_contextid(): int {
        return $this->contextid;
    }

    /**
     * Get the id of this published resource.
     *
     * @return int the id.
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Get the uuid for this published resource.
     *
     * @return string v4 uuid.
     */
    public function get_uuid(): string {
        return $this->uuid;
    }

    /**
     * Check whether this resource supports grades or not.
     *
     * @return bool true if supported, false otherwise.
     */
    public function supports_grades(): bool {
        return $this->supportsgrades;
    }

    /**
     * Get the max grade for this published resource, if its a graded resource.
     *
     * @return float|null the grade max, if grades are supported, else null.
     */
    public function get_grademax(): ?float {
        return $this->grademax;
    }

    /**
     * Check whether this published resource is a course itself.
     *
     * @return bool true if it's a course, false otherwise.
     */
    public function is_course(): bool {
        return $this->iscourse;
    }
}
