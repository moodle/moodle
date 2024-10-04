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

namespace core_course\hook;

/**
 * Allows plugins to extend course form submission.
 *
 * @see create_course()
 * @see update_course()
 *
 * @package    core_course
 * @copyright  2023 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to extend saving of the course editing form')]
#[\core\attribute\tags('course')]
class after_form_submission {
    /**
     * Creates new hook.
     *
     * @param \stdClass $data Submitted data
     * @param bool $isnewcourse Whether this is a new course
     */
    public function __construct(
        /** @var \stdClass The submitted data */
        protected \stdClass $data,
        /** @var bool Whether this is a new course */
        public readonly bool $isnewcourse = false,
    ) {
    }

    /**
     * Returns submitted data.
     *
     * @return \stdClass
     */
    public function get_data(): \stdClass {
        return $this->data;
    }
}
