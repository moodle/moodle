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
use mod_quiz\structure;

/**
 * The quiz structure has been modified
 *
 * @package   mod_quiz
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[attribute\label('The quiz structure has been modified')]
#[attribute\tags('quiz', 'structure')]
#[attribute\hook\replaces_callbacks('quiz_structure_modified::callback')]
class structure_modified {
    /**
     * Create a new hook with the modified structure.
     *
     * @param structure $structure The new structure.
     */
    public function __construct(
        protected structure $structure
    ) {
    }

    /**
     * Returns the new structure of the quiz.
     *
     * @return structure The structure object.
     */
    public function get_structure(): structure {
        return $this->structure;
    }
}
