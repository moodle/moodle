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

namespace qtype_ordering\output;

use templatable;
use renderable;
use question_attempt;

/**
 * The base class for the renderables that are used to output the components of the ordering question.
 *
 * @package    qtype_ordering
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class renderable_base implements templatable, renderable {

    /**
     * The class constructor.
     *
     * @param question_attempt $qa The question attempt object.
     */
    public function __construct(
        /** @var question_attempt The question attempt object. */
        protected question_attempt $qa,
    ) {
    }
}
