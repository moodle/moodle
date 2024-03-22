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
namespace mod_quiz\hook;

use core\attribute;

/**
 * A quiz attempt changed state.
 *
 * @package   mod_quiz
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[attribute\label('A quiz attempt changed state.')]
#[attribute\tags('quiz', 'attempt')]
#[attribute\hook\replaces_callbacks('quiz_attempt_deleted::callback')]
class attempt_state_changed {
    /**
     * Create a new hook instance.
     *
     * @param ?\stdClass $originalattempt The original database record for the attempt, null if it has just been created.
     * @param ?\stdClass $updatedattempt The updated database record of the new attempt, null if it has just been deleted.
     */
    public function __construct(
        protected ?\stdClass $originalattempt,
        protected ?\stdClass $updatedattempt,
    ) {
        if (is_null($this->originalattempt) && is_null($this->updatedattempt)) {
            throw new \InvalidArgumentException('originalattempt and updatedattempt cannot both be null.');
        }
        if (
            !is_null($this->originalattempt)
            && !is_null($this->updatedattempt)
            && $this->originalattempt->id != $this->updatedattempt->id
        ) {
            throw new \InvalidArgumentException('originalattempt and updatedattempt must have the same id.');
        }
    }

    /**
     * Get the original attempt, null if it has just been created.
     *
     * @return ?\stdClass
     */
    public function get_original_attempt(): ?\stdClass {
        return $this->originalattempt;
    }

    /**
     * Get the updated attempt, null if it has just been deleted.
     *
     * @return ?\stdClass
     */
    public function get_updated_attempt(): ?\stdClass {
        return $this->updatedattempt;
    }
}
