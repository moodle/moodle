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

namespace mod_quiz\output;

use renderable;

/**
 * Represents the list of links to other attempts
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class links_to_other_attempts implements renderable {
    /**
     * @var array The list of links. string attempt number => one of three things:
     * - null if this is the current attempt, and so should not be linked. (Just the number is output.)
     * - moodle_url if this is a different attempt. (Output as a link to the URL with the number as link text.)
     * - a renderable, in which case the results of rendering the renderable is output.
     * (The third option is used by {@see quiz_attempt::links_to_other_redos()}.)
     */
    public $links = [];
}
