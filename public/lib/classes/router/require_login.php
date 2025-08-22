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

namespace core\router;

/**
 * Login metadata requirements for routes.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class require_login {
    /**
     * Create a new instance of the metadata class.
     *
     * @param bool $requirelogin Whether login is required
     * @param bool $requirecourselogin Whether a course login is required
     * @param mixed $courseattributename The name of the route attribute that the course object can be found in
     * @param bool $autologinguest Whether to automatically log in as guest
     * @throws \InvalidArgumentException
     */
    public function __construct(
        /** @var bool Whether to require login or not */
        public bool $requirelogin = true,
        /** @var bool Whether to require course login or not */
        public bool $requirecourselogin = false,
        /** @var string|null The route attribute name used for the course */
        protected ?string $courseattributename = null,
        /** @var bool Whether to autologin guest users */
        public bool $autologinguest = true,
    ) {
        if ($requirelogin && $requirecourselogin) {
            throw new \InvalidArgumentException('Cannot require login and course login at the same time');
        }
    }

    /**
     * Get the attribute name used for the course.
     *
     * A null value is returned if the course attribute name is not set.
     *
     * @return null|string
     */
    public function get_course_attribute_name(): ?string {
        return $this->courseattributename;
    }

    /**
     * Whether course login is required.
     *
     * @return bool
     */
    public function should_require_course_login(): bool {
        return $this->requirecourselogin;
    }

    /**
     * Whether login is required.
     *
     * @return bool
     */
    public function should_require_login(): bool {
        return $this->requirelogin;
    }

    /**
     * Whether automatic guest login is enabled.
     *
     * @return bool
     */
    public function should_autologin_guest(): bool {
        return $this->autologinguest;
    }
}
