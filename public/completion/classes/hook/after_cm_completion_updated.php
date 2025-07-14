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

namespace core_completion\hook;

/**
 * Hook after course module creation.
 *
 * This hook will be dispatched after a course module is created and events are fired.
 *
 * @package    core_completion
 * @copyright  2024 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions after completion is updated.')]
#[\core\attribute\tags('completion', 'course')]
class after_cm_completion_updated {
    /**
     * Constructor for the hook
     *
     * @param \cm_info $cm The course module info
     * @param \stdClass $data completion data
     */
    public function __construct(
        /** @var \cm_info The course module info */
        public readonly \cm_info $cm,
        /** @var \stdClass completion data */
        public readonly \stdClass $data
    ) {
    }
}
