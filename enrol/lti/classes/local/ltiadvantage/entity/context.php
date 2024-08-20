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

namespace enrol_lti\local\ltiadvantage\entity;

/**
 * Class context, instances of which represent a context in the platform.
 *
 * See: http://www.imsglobal.org/spec/lti/v1p3/#context-type-vocabulary for supported context types.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later */
class context {

    // The following full contexts are per the spec:
    // http://www.imsglobal.org/spec/lti/v1p3/#context-type-vocabulary.
    /** @var string course template context */
    private const CONTEXT_TYPE_COURSE_TEMPLATE = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseTemplate';

    /** @var string course offering context */
    private const CONTEXT_TYPE_COURSE_OFFERING = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering';

    /** @var string course section context */
    private const CONTEXT_TYPE_COURSE_SECTION = 'http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection';

    /** @var string group context */
    private const CONTEXT_TYPE_GROUP = 'http://purl.imsglobal.org/vocab/lis/v2/course#Group';

    // The following simple names are deprecated but are still supported in 1.3 for backwards compatibility.
    // http://www.imsglobal.org/spec/lti/v1p3/#context-type-vocabulary.
    /** @var string deprecated simple course template context */
    private const LEGACY_CONTEXT_TYPE_COURSE_TEMPLATE = 'CourseTemplate';

    /** @var string deprecated simple course offering context */
    private const LEGACY_CONTEXT_TYPE_COURSE_OFFERING = 'CourseOffering';

    /** @var string deprecated simple course section context */
    private const LEGACY_CONTEXT_TYPE_COURSE_SECTION = 'CourseSection';

    /** @var string deprecated simple group context */
    private const LEGACY_CONTEXT_TYPE_GROUP = 'Group';

    /** @var int the local id of the deployment instance to which this context belongs. */
    private $deploymentid;

    /** @var string the contextid as supplied by the platform. */
    private $contextid;

    /** @var int|null the local id of this object instance, which can be null if the object hasn't been stored before */
    private $id;

    /** @var string[] the array of context types */
    private $types;

    /**
     * Private constructor.
     *
     * @param int $deploymentid the local id of the deployment instance to which this context belongs.
     * @param string $contextid the context id string, as provided by the platform during launch.
     * @param array $types an array of string context types, as provided by the platform during launch.
     * @param int|null $id local id of this object instance, nullable for new objects.
     */
    private function __construct(int $deploymentid, string $contextid, array $types, ?int $id) {
        if (!is_null($id) && $id <= 0) {
            throw new \coding_exception('id must be a positive int');
        }
        $this->deploymentid = $deploymentid;
        $this->contextid = $contextid;
        $this->set_types($types); // Handles type validation.
        $this->id = $id;
    }

    /**
     * Factory method for creating a context instance.
     *
     * @param int $deploymentid the local id of the deployment instance to which this context belongs.
     * @param string $contextid the context id string, as provided by the platform during launch.
     * @param array $types an array of string context types, as provided by the platform during launch.
     * @param int|null $id local id of this object instance, nullable for new objects.
     * @return context the context instance.
     */
    public static function create(int $deploymentid, string $contextid, array $types, ?int $id = null): context {
        return new self($deploymentid, $contextid, $types, $id);
    }

    /**
     * Check whether a context is valid or not, checking also deprecated but supported legacy context names.
     *
     * @param string $type context type to check.
     * @param bool $includelegacy whether to check the legacy simple context names too.
     * @return bool true if the type is valid, false otherwise.
     */
    private function is_valid_type(string $type, bool $includelegacy = false): bool {
        // Check LTI Advantage types.
        $valid = in_array($type, [
            self::CONTEXT_TYPE_COURSE_TEMPLATE,
            self::CONTEXT_TYPE_COURSE_OFFERING,
            self::CONTEXT_TYPE_COURSE_SECTION,
            self::CONTEXT_TYPE_GROUP
        ]);

        // Check legacy short names.
        if ($includelegacy) {
            $valid = $valid || in_array($type, [
                self::LEGACY_CONTEXT_TYPE_COURSE_TEMPLATE,
                self::LEGACY_CONTEXT_TYPE_COURSE_OFFERING,
                self::LEGACY_CONTEXT_TYPE_COURSE_SECTION,
                self::LEGACY_CONTEXT_TYPE_GROUP
            ]);
        }

        return $valid;
    }

    /**
     * Get the object instance id.
     *
     * @return int|null the id, or null if the object doesn't yet have one assigned.
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Return the platform contextid string.
     *
     * @return string the id of the context in the platform.
     */
    public function get_contextid(): string {
        return $this->contextid;
    }

    /**
     * Get the id of the local deployment instance to which this context instance belongs.
     *
     * @return int the id of the local deployment instance to which this context instance belongs.
     */
    public function get_deploymentid(): int {
        return $this->deploymentid;
    }

    /**
     * Get the context types this context instance represents.
     *
     * @return string[] the array of context types this context instance represents.
     */
    public function get_types(): array {
        return $this->types;
    }

    /**
     * Set the list of types this context instance represents.
     *
     * @param array $types the array of string types.
     * @throws \coding_exception if any of the supplied types are invalid.
     */
    public function set_types(array $types): void {
        foreach ($types as $type) {
            if (!$this->is_valid_type($type, true)) {
                throw new \coding_exception("Cannot set invalid context type '{$type}'.");
            }
        }
        $this->types = $types;
    }
}
