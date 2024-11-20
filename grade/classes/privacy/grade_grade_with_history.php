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

namespace core_grades\privacy;

use grade_grade;

/**
 * A grade_item which has a reference to its historical content.
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class grade_grade_with_history extends grade_grade {
    public int $historyid;

    public function __construct(?\stdClass $params = null, $fetch = true) {
        // The grade history is not a real grade_grade so we remove the ID.
        $this->historyid = $params->id;
        unset($params->id);

        parent::__construct($params, $fetch);
    }
}
