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

namespace core_courseformat\hook;

use core\hook\described_hook;
use cm_info;

/**
 * Hook for course-module name edited.
 *
 * @package    core_courseformat
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class after_cm_name_edited implements described_hook {
    /**
     * Constructor.
     *
     * @param cm_info $cm the course module
     * @param string $newname the new name
     */
    public function __construct(
        /** @var cm_info the course module */
        protected cm_info $cm,
        /** @var string the new name */
        protected string $newname,
    ) {
    }


    /**
     * Describes the hook purpose.
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'This hook is triggered when a course module name is edited.';
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['cm_name_edited'];
    }

    /**
     * Get course module instance.
     *
     * @return cm_info
     */
    public function get_cm(): cm_info {
        return $this->cm;
    }

    /**
     * Get new name.
     * @return string
     */
    public function get_newname(): string {
        return $this->newname;
    }
}
