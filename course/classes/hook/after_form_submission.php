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

use core\hook\described_hook;
use stdClass;

/**
 * Allows plugins to extend course form submission.
 *
 * @see create_course()
 * @see update_course()
 *
 * @package    core
 * @copyright  2023 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_form_submission implements described_hook {

    /**
     * Submitted data.
     *
     * @var stdClass
     */
    protected $data;

    /**
     * Is it a new course ?
     *
     * @var bool
     */
    protected $isnewcourse = false;

    /**
     * Creates new hook.
     *
     * @param stdClass $data Submitted data.
     * @param bool $isnewcourse Is it a new course?
     */
    public function __construct(stdClass $data, bool $isnewcourse = false) {
        $this->data = $data;
        $this->isnewcourse = $isnewcourse;
    }

    /**
     * Returns submitted data.
     *
     * @return stdClass
     */
    public function get_data(): stdClass {
        return $this->data;
    }

    /**
     * Informs callbacks if a hook called for a new course.
     *
     * @return bool
     */
    public function is_new_course(): bool {
        return $this->isnewcourse;
    }

    /**
     * Describes the hook purpose.
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'Allows plugins to extend saving of the course editing form';
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['course'];
    }
}
